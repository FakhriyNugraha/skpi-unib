<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SkpiData;
use App\Models\User;
use App\Models\Jurusan;
use App\Helpers\PeriodHelper;
use Carbon\Carbon;

class SkpiDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all student users
        $students = User::where('role', 'user')->get();
        
        if ($students->count() === 0) {
            // If no students exist, exit gracefully
            return;
        }

        foreach ($students as $student) {
            // Randomly assign period 112, 113, or 114
            $selectedPeriod = [112, 113, 114][array_rand([112, 113, 114])];
            
            // Determine the date range for the selected period
            $periodRange = PeriodHelper::getPeriodRange($selectedPeriod);

            // Generate a random graduation date within the period range
            $start = $periodRange['start'];
            $end = $periodRange['end'];
            $randomTimestamp = mt_rand($start->timestamp, $end->timestamp);
            $tanggalLulus = Carbon::createFromTimestamp($randomTimestamp);
            
            // Get the student's jurusan, or use a random one if not set
            $jurusan = $student->jurusan ?? Jurusan::first();
            
            // Create SKPI data for the student
            SkpiData::create([
                'user_id' => $student->id,
                'nama_lengkap' => $student->name,
                'npm' => $student->npm ?? 'G1A0' . rand(1000, 9999),
                'tempat_lahir' => 'Bengkulu',
                'tanggal_lahir' => $student->created_at->subYears(22)->format('Y-m-d'),
                'nomor_ijazah' => '00' . rand(1000, 9999) . '/UN25.1/PP.00.1/' . date('Y'),
                'tanggal_lulus' => $tanggalLulus,
                'periode_wisuda' => $selectedPeriod, // Explicitly set the period
                'gelar' => $this->getGelarForJurusan($jurusan),
                'program_studi' => $jurusan ? $jurusan->nama_jurusan : 'Teknik Informatika',
                'jurusan_id' => $jurusan ? $jurusan->id : null,
                'ipk' => number_format(rand(280, 400) / 100, 2),
                'prestasi_akademik' => rand(1, 3) > 2 ? 'Juara Lomba Programming' : null,
                'prestasi_non_akademik' => rand(1, 3) > 2 ? 'Juara Lomba Olahraga' : null,
                'organisasi' => rand(1, 3) > 1 ? 'Himpunan Mahasiswa' : null,
                'pengalaman_kerja' => rand(1, 3) > 2 ? 'Magang di PT. ABC' : null,
                'sertifikat_kompetensi' => rand(1, 3) > 1 ? 'Sertifikasi Jaringan, Sertifikasi Database' : null,
                'catatan_khusus' => null,
                'drive_link' => 'https://drive.google.com/file/d/' . str_repeat('a', 33) . '/view?usp=sharing',
                'status' => $this->getRandomStatus(),
                'catatan_reviewer' => rand(1, 4) > 3 ? 'Perlu revisi pada bagian prestasi' : null,
                'reviewed_by' => $this->getRandomAdminId(),
                'reviewed_at' => rand(1, 3) > 1 ? now()->subDays(rand(1, 30)) : null,
                'approved_by' => $this->getRandomAdminId(),
                'approved_at' => rand(1, 3) > 1 ? now()->subDays(rand(1, 15)) : null,
            ]);
        }
    }
    
    /**
     * Get appropriate degree title based on jurusan
     */
    private function getGelarForJurusan($jurusan)
    {
        if (!$jurusan) {
            return 'S.T';
        }
        
        $jurusanNama = strtolower($jurusan->nama_jurusan);
        
        if (str_contains($jurusanNama, 'informatika') || str_contains($jurusanNama, 'sistem') && str_contains($jurusanNama, 'informasi')) {
            return 'S.Kom';
        } elseif (str_contains($jurusanNama, 'sipil') || str_contains($jurusanNama, 'mesin') || str_contains($jurusanNama, 'elektro')) {
            return 'S.T';
        } elseif (str_contains($jurusanNama, 'arsitek')) {
            return 'S.Ars';
        }
        
        return 'S.T'; // default
    }
    
    /**
     * Get random status - from all 4 valid statuses: draft, submitted, rejected, approved
     */
    private function getRandomStatus()
    {
        $statuses = ['draft', 'submitted', 'rejected', 'approved'];
        return $statuses[array_rand($statuses)];
    }
    
    /**
     * Get random admin ID
     */
    private function getRandomAdminId()
    {
        $adminIds = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->pluck('id')->toArray();
        return !empty($adminIds) ? $adminIds[array_rand($adminIds)] : null;
    }
}