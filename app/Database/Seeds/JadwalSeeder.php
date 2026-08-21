<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run()
    {
        // Ambil referensi data
        $tahun  = $this->db->table('tahun_pelajaran')->where('status', 'Aktif')->get()->getRowArray();
        $kelas  = $this->db->table('kelas')->where('nama_kelas', 'X RPL 1')->get()->getRowArray();

        $guruBudi = $this->db->table('gurus')->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.budi')->get()->getRowArray();
        $guruSiti = $this->db->table('gurus')->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.siti')->get()->getRowArray();
        $guruAhmad = $this->db->table('gurus')->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.ahmad')->get()->getRowArray();

        $mapelPwd = $this->db->table('mata_pelajaran')->where('kode_mapel', 'C3-RPL-01')->get()->getRowArray();
        $mapelWeb = $this->db->table('mata_pelajaran')->where('kode_mapel', 'C3-RPL-02')->get()->getRowArray();
        $mapelDb  = $this->db->table('mata_pelajaran')->where('kode_mapel', 'C3-RPL-03')->get()->getRowArray();
        $mapelMat = $this->db->table('mata_pelajaran')->where('kode_mapel', 'MAT-01')->get()->getRowArray();

        $data = [
            [
                'kelas_id'          => $kelas['id'],
                'guru_id'           => $guruBudi['id'],
                'mapel_id'          => $mapelPwd['id'],
                'tahun_pelajaran_id' => $tahun['id'],
                'hari'              => 'Senin',
                'jam_mulai'         => '07:30:00',
                'jam_selesai'       => '09:30:00',
            ],
            [
                'kelas_id'          => $kelas['id'],
                'guru_id'           => $guruBudi['id'],
                'mapel_id'          => $mapelWeb['id'],
                'tahun_pelajaran_id' => $tahun['id'],
                'hari'              => 'Rabu',
                'jam_mulai'         => '07:30:00',
                'jam_selesai'       => '09:30:00',
            ],
            [
                'kelas_id'          => $kelas['id'],
                'guru_id'           => $guruSiti['id'],
                'mapel_id'          => $mapelDb['id'],
                'tahun_pelajaran_id' => $tahun['id'],
                'hari'              => 'Selasa',
                'jam_mulai'         => '09:45:00',
                'jam_selesai'       => '11:45:00',
            ],
            [
                'kelas_id'          => $kelas['id'],
                'guru_id'           => $guruAhmad['id'],
                'mapel_id'          => $mapelMat['id'],
                'tahun_pelajaran_id' => $tahun['id'],
                'hari'              => 'Kamis',
                'jam_mulai'         => '07:30:00',
                'jam_selesai'       => '09:00:00',
            ],
        ];

        $this->db->table('jadwal')->insertBatch($data);
        echo "✅ JadwalSeeder: " . count($data) . " jadwal berhasil ditambahkan.\n";
    }
}
