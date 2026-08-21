<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Kelompok Nasional
            ['kode_mapel' => 'PAI-01',   'nama_mapel' => 'Pendidikan Agama Islam & Budi Pekerti', 'kelompok' => 'Nasional'],
            ['kode_mapel' => 'PKN-01',   'nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan', 'kelompok' => 'Nasional'],
            ['kode_mapel' => 'BIN-01',   'nama_mapel' => 'Bahasa Indonesia',                       'kelompok' => 'Nasional'],
            ['kode_mapel' => 'MAT-01',   'nama_mapel' => 'Matematika',                             'kelompok' => 'Nasional'],
            ['kode_mapel' => 'SEJ-01',   'nama_mapel' => 'Sejarah Indonesia',                      'kelompok' => 'Nasional'],
            ['kode_mapel' => 'BIG-01',   'nama_mapel' => 'Bahasa Inggris',                         'kelompok' => 'Nasional'],

            // Kelompok Kewilayahan
            ['kode_mapel' => 'SENI-01',  'nama_mapel' => 'Seni Budaya',          'kelompok' => 'Kewilayahan'],
            ['kode_mapel' => 'PJOK-01',  'nama_mapel' => 'PJOK',                 'kelompok' => 'Kewilayahan'],

            // Kelompok Kejuruan (RPL)
            ['kode_mapel' => 'C3-RPL-01', 'nama_mapel' => 'Pemrograman Dasar',          'kelompok' => 'Kejuruan'],
            ['kode_mapel' => 'C3-RPL-02', 'nama_mapel' => 'Pemrograman Web',             'kelompok' => 'Kejuruan'],
            ['kode_mapel' => 'C3-RPL-03', 'nama_mapel' => 'Basis Data',                  'kelompok' => 'Kejuruan'],
            ['kode_mapel' => 'C3-RPL-04', 'nama_mapel' => 'Pemrograman Berorientasi Objek', 'kelompok' => 'Kejuruan'],
            ['kode_mapel' => 'C3-RPL-05', 'nama_mapel' => 'Rekayasa Perangkat Lunak',    'kelompok' => 'Kejuruan'],
        ];

        $this->db->table('mata_pelajaran')->insertBatch($data);
        echo "✅ MataPelajaranSeeder: " . count($data) . " mata pelajaran berhasil ditambahkan.\n";
    }
}
