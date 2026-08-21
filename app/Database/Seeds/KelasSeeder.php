<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run()
    {
        // Ambil ID guru untuk wali kelas — select gurus.id secara eksplisit
        $guruBudi  = $this->db->table('gurus')
            ->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.budi')->get()->getRowArray();
        $guruSiti  = $this->db->table('gurus')
            ->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.siti')->get()->getRowArray();
        $guruAhmad = $this->db->table('gurus')
            ->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.ahmad')->get()->getRowArray();

        $data = [
            [
                'nama_kelas'    => 'X RPL 1',
                'jurusan'       => 'Rekayasa Perangkat Lunak',
                'wali_kelas_id' => $guruBudi['id'],
            ],
            [
                'nama_kelas'    => 'XI RPL 1',
                'jurusan'       => 'Rekayasa Perangkat Lunak',
                'wali_kelas_id' => $guruSiti['id'],
            ],
            [
                'nama_kelas'    => 'XII RPL 1',
                'jurusan'       => 'Rekayasa Perangkat Lunak',
                'wali_kelas_id' => $guruAhmad['id'],
            ],
        ];

        $this->db->table('kelas')->insertBatch($data);
        echo "✅ KelasSeeder: " . count($data) . " kelas berhasil ditambahkan.\n";
    }
}
