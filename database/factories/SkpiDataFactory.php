<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SkpiData;
use App\Models\User;
use App\Models\Jurusan;
use App\Helpers\PeriodHelper;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkpiData>
 */
class SkpiDataFactory extends Factory
{
    protected $model = SkpiData::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jurusans = Jurusan::pluck('id')->toArray();
        $users = User::where('role', 'user')->pluck('id')->toArray();
        
        if (empty($jurusans) || empty($users)) {
            // If no jurusan/user exists, create default values
            $startDate = $this->faker->dateTimeBetween('-3 years', 'now');
            $endDate = clone $startDate;
            $endDate->modify('+3 years');
            
            return [
                'user_id' => null,
                'nama_lengkap' => $this->faker->name,
                'npm' => $this->faker->unique()->numerify('G1A0########'),
                'tempat_lahir' => $this->faker->city,
                'tanggal_lahir' => $this->faker->date(),
                'nomor_ijazah' => $this->faker->unique()->numerify('####/UN25.1/PP.00.1/####'),
                'tanggal_lulus' => $this->faker->date(),
                'gelar' => 'S.T',
                'program_studi' => 'Teknik Informatika',
                'jurusan_id' => null,
                'ipk' => $this->faker->randomFloat(2, 2.0, 4.0),
                'status' => 'approved',
            ];
        }

        // Randomly select a graduation date
        $tanggalLulus = $this->faker->dateTimeBetween('-2 years', 'now');
        $tanggalLulusFormatted = $tanggalLulus->format('Y-m-d');

        return [
            'user_id' => $this->faker->randomElement($users),
            'nama_lengkap' => $this->faker->name,
            'npm' => $this->faker->unique()->numerify('G1A0########'),
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $this->faker->date(),
            'nomor_ijazah' => $this->faker->unique()->numerify('####/UN25.1/PP.00.1/####'),
            'tanggal_lulus' => $tanggalLulusFormatted,
            'periode_wisuda' => PeriodHelper::getPeriodeFromDate($tanggalLulusFormatted),
            'gelar' => $this->faker->randomElement(['S.T', 'S.Kom', 'S.Si', 'S.E', 'S.IP']),
            'program_studi' => $this->faker->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil']),
            'jurusan_id' => $this->faker->randomElement($jurusans),
            'ipk' => $this->faker->randomFloat(2, 2.0, 4.0),
            'prestasi_akademik' => $this->faker->optional(0.3)->sentence,
            'prestasi_non_akademik' => $this->faker->optional(0.3)->sentence,
            'organisasi' => $this->faker->optional(0.3)->sentence,
            'pengalaman_kerja' => $this->faker->optional(0.2)->sentence,
            'sertifikat_kompetensi' => $this->faker->optional(0.3)->sentence,
            'catatan_khusus' => $this->faker->optional(0.1)->sentence,
            'drive_link' => $this->faker->url,
            'status' => $this->faker->randomElement(['draft', 'submitted', 'reviewed', 'approved', 'rejected']),
            'catatan_reviewer' => $this->faker->optional(0.2)->sentence,
            'reviewed_by' => $this->faker->optional(0.5)->randomElement(User::where('role', 'admin')->pluck('id')->toArray()),
            'reviewed_at' => $this->faker->optional(0.5)->dateTime(),
            'approved_by' => $this->faker->optional(0.5)->randomElement(User::where('role', 'admin')->pluck('id')->toArray()),
            'approved_at' => $this->faker->optional(0.5)->dateTime(),
        ];
    }
}