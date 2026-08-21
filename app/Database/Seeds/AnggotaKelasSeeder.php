<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnggotaKelasSeeder extends Seeder
{
    public function run()
    {
        // Ambil tahun pelajaran aktif
        $tahun = $this->db->table('tahun_pelajaran')->where('status', 'Aktif')->get()->getRowArray();

        // Ambil kelas X RPL 1
        $kelas = $this->db->table('kelas')->where('nama_kelas', 'X RPL 1')->get()->getRowArray();

        // Ambil semua siswa
        $siswas = $this->db->table('siswas')->get()->getResultArray();

        $data = [];
        foreach ($siswas as $siswa) {
            $data[] = [
                'kelas_id'          => $kelas['id'],
                'siswa_id'          => $siswa['id'],
                'tahun_pelajaran_id' => $tahun['id'],
            ];
        }

        $this->db->table('anggota_kelas')->insertBatch($data);
        echo "✅ AnggotaKelasSeeder: " . count($data) . " anggota kelas berhasil ditambahkan.\n";
    }
}
