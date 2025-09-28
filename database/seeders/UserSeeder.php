<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin SKPI',
            'email' => 'superadmin@unib.ac.id',
            'password' => Hash::make('password123'),
            'nip' => '196701011993031001',
            'role' => 'superadmin',
            'status' => 'active',
            'phone' => '+6281234567890',
            'address' => 'Universitas Bengkulu, Kandang Limun, Bengkulu'
        ]);

        // Admin untuk setiap jurusan
        $jurusans = Jurusan::all();
        $adminCounter = 1;
        
        foreach ($jurusans as $jurusan) {
            User::create([
                'name' => 'Admin ' . $jurusan->nama_jurusan,
                'email' => 'admin.' . strtolower($jurusan->kode_jurusan) . '@unib.ac.id',
                'password' => Hash::make('password123'),
                'nip' => '19700101199403' . sprintf('%04d', $adminCounter),
                'role' => 'admin',
                'status' => 'active',
                'jurusan_id' => $jurusan->id,
                'phone' => '+6281234567' . sprintf('%03d', $adminCounter),
                'address' => 'Universitas Bengkulu, Kandang Limun, Bengkulu'
            ]);
            $adminCounter++;
        }

        // Sample mahasiswa untuk setiap jurusan
        $mahasiswaCounter = 1;
        foreach ($jurusans as $jurusan) {
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'name' => 'Mahasiswa ' . $jurusan->kode_jurusan . ' ' . $i,
                    'email' => 'mahasiswa' . $mahasiswaCounter . '@student.unib.ac.id',
                    'password' => Hash::make('password123'),
                    'npm' => 'G1A0' . sprintf('%02d', $jurusan->id) . sprintf('%03d', $i),
                    'role' => 'user',
                    'status' => 'active',
                    'jurusan_id' => $jurusan->id,
                    'phone' => '+6281234560' . sprintf('%03d', $mahasiswaCounter),
                    'address' => 'Bengkulu, Indonesia'
                ]);
                $mahasiswaCounter++;
            }
        }
    }
}