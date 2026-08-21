<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // Truncate tahun_pelajaran (relasi lainnya sudah di-handle UserSeeder)
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('tahun_pelajaran')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        $this->call('TahunPelajaranSeeder');
        $this->call('UserSeeder');
        $this->call('GuruSeeder');
        $this->call('SiswaSeeder');
        $this->call('KelasSeeder');
        $this->call('AnggotaKelasSeeder');
        $this->call('MataPelajaranSeeder');
        $this->call('JadwalSeeder');
        $this->call('MateriSeeder');
    }
}
