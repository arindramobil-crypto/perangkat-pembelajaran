<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TahunPelajaranSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $data = [
            [
                'tahun'      => '2024/2025',
                'semester'   => 'Genap',
                'status'     => 'Tidak Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun'      => '2025/2026',
                'semester'   => 'Ganjil',
                'status'     => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('tahun_pelajaran')->insertBatch($data);
        echo "✅ TahunPelajaranSeeder: 2 tahun pelajaran berhasil ditambahkan.\n";
    }
}
