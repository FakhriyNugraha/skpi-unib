<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Hash;
use App\Helpers\PeriodHelper;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get current academic period
        $currentPeriod = PeriodHelper::getCurrentPeriod();

        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@unib.ac.id'],
            [
                'name' => 'Super Admin SKPI',
                'password' => Hash::make('password123'),
                'nip' => '196701011993031001',
                'role' => 'superadmin',
                'status' => 'active',
                'phone' => '+6281234567890',
                'address' => 'Universitas Bengkulu, Kandang Limun, Bengkulu'
            ]
        );

        // Create or update admins for each jurusan
        $jurusans = Jurusan::all();
        $adminCounter = 1;

        foreach ($jurusans as $jurusan) {
            User::updateOrCreate(
                ['email' => 'admin.' . strtolower($jurusan->kode_jurusan) . '@unib.ac.id'],
                [
                    'name' => 'Admin ' . $jurusan->nama_jurusan,
                    'password' => Hash::make('password123'),
                    'nip' => '19700101199403' . sprintf('%04d', $adminCounter),
                    'role' => 'admin',
                    'status' => 'active',
                    'jurusan_id' => $jurusan->id,
                    'phone' => '+6281234567' . sprintf('%03d', $adminCounter),
                    'address' => 'Universitas Bengkulu, Kandang Limun, Bengkulu'
                ]
            );
            $adminCounter++;
        }

        // Create 2 students per study program for current academic period
        foreach ($jurusans as $index => $jurusan) {
            for ($i = 1; $i <= 2; $i++) {
                // Generate realistic student data
                $baseNames = ["Andika", "Budi", "Citra", "Dian", "Eka", "Fajar", "Gina", "Hendra", "Indra", "Julia"];
                $lastNames = ["Pratama", "Santoso", "Dewi", "Kurnia", "Putra", "Nugraha", "Lestari", "Wijaya", "Permana", "Amalia"];
                $titles = ["S.Kom.", "S.T.", "S.IP.", "S.E.", "S.Pd.", "S.Si.", "S.H.", "S.Psi."];

                $firstName = $baseNames[($index * 2 + $i + 1) % count($baseNames)];
                $lastName = $lastNames[($index * 2 + $i + 3) % count($lastNames)];
                $title = $titles[($index * 2 + $i + 5) % count($titles)];

                $studentName = $firstName . " " . $lastName . " " . $title;
                $npm = $this->generateNpmForCurrentPeriod($jurusan->id, $i, $currentPeriod);

                User::updateOrCreate(
                    ['npm' => $npm],
                    [
                        'name' => $studentName,
                        'email' => strtolower(str_replace([' ', '.'], ['', '.'], $firstName . $lastName . $i . $jurusan->kode_jurusan)) . '@student.unib.ac.id',
                        'password' => Hash::make('password123'),
                        'npm' => $npm,
                        'role' => 'user',
                        'status' => 'active',
                        'jurusan_id' => $jurusan->id,
                        'phone' => '+62812345678' . sprintf('%02d', ($index * 2 + $i)),
                        'address' => ['Bengkulu', 'Jakarta', 'Bandung', 'Surabaya', 'Medan'][$i % 5] . ', Indonesia'
                    ]
                );
            }
        }
    }

    /**
     * Generate NPM based on the current academic period
     */
    private function generateNpmForCurrentPeriod($jurusanId, $sequence, $currentPeriod)
    {
        // Get the year from the current period
        $periodRange = PeriodHelper::getPeriodRange($currentPeriod);
        $year = $periodRange['start']->year;

        // Use 2-digit year (last 2 digits)
        $yearSuffix = substr($year, -2);

        // Format: G + year + A + 2-digit jurusan_id + sequence
        return 'G' . $yearSuffix . 'A' . sprintf('%02d', $jurusanId) . sprintf('%03d', $sequence);
    }
}