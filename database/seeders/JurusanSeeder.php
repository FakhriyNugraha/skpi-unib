<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusans = [
            [
                'nama_jurusan' => 'Informatika',
                'kode_jurusan' => 'IF',
                'deskripsi' => 'Program Studi Informatika Fakultas Teknik Universitas Bengkulu',
                'kaprodi' => 'Ir. Kurnia Anggriani, S.T., M.T., Ph.D.',
                'nip_kaprodi' => '198901182015042004',
                'status' => 'active'
            ],
            [
                'nama_jurusan' => 'Teknik Sipil',
                'kode_jurusan' => 'TS',
                'deskripsi' => 'Program Studi Teknik Sipil Fakultas Teknik Universitas Bengkulu',
                'kaprodi' => 'Dr. Ir. Rena Misliniyati, S.T., M.T.',
                'nip_kaprodi' => '198201212006042003',
                'status' => 'active'
            ],
            [
                'nama_jurusan' => 'Teknik Mesin',
                'kode_jurusan' => 'TM',
                'deskripsi' => 'Program Studi Teknik Mesin Fakultas Teknik Universitas Bengkulu',
                'kaprodi' => 'Dr. Zuliantoni, S.T., M.T.',
                'nip_kaprodi' => '197710212005011001',
                'status' => 'active'
            ],
            [
                'nama_jurusan' => 'Teknik Elektro',
                'kode_jurusan' => 'TE',
                'deskripsi' => 'Program Studi Teknik Elektro Fakultas Teknik Universitas Bengkulu',
                'kaprodi' => 'Ir. Afriyastuti Herawati, S.T., M.T.',
                'nip_kaprodi' => '198205012008122002',
                'status' => 'active'
            ],
            [
                'nama_jurusan' => 'Arsitektur',
                'kode_jurusan' => 'ARS',
                'deskripsi' => 'Program Studi Arsitektur Fakultas Teknik Universitas Bengkulu',
                'kaprodi' => 'Ar. Abdul Hamid Hakim, S.T., M.Sc.',
                'nip_kaprodi' => '198709242019031006',
                'status' => 'active'
            ],
            [
                'nama_jurusan' => 'Sistem Informasi',
                'kode_jurusan' => 'SI',
                'deskripsi' => 'Program Studi Sistem Informasi Fakultas Teknik Universitas Bengkulu',
                'kaprodi' => 'Dr. Endina Putri Purwandari, S.T., M.Kom.',
                'nip_kaprodi' => '198701272012122001',
                'status' => 'active'
            ]
        ];

        foreach ($jurusans as $jurusan) {
            Jurusan::create($jurusan);
        }
    }
}