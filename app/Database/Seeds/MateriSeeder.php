<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $guruBudi = $this->db->table('gurus')->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.budi')->get()->getRowArray();
        $guruSiti = $this->db->table('gurus')->select('gurus.id')
            ->join('users', 'users.id = gurus.user_id')
            ->where('users.username', 'guru.siti')->get()->getRowArray();

        // Ambil jadwal (sesuai relasi: materi->jadwal->mapel)
        $jadwalPwd = $this->db->table('jadwal')
            ->select('jadwal.id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('mata_pelajaran.kode_mapel', 'C3-RPL-01')->get()->getRowArray();
        $jadwalWeb = $this->db->table('jadwal')
            ->select('jadwal.id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('mata_pelajaran.kode_mapel', 'C3-RPL-02')->get()->getRowArray();
        $jadwalDb  = $this->db->table('jadwal')
            ->select('jadwal.id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('mata_pelajaran.kode_mapel', 'C3-RPL-03')->get()->getRowArray();

        $data = [
            [
                'guru_id'       => $guruBudi['id'],
                'jadwal_id'     => $jadwalPwd['id'] ?? null,
                'judul_materi'  => 'Pengenalan Algoritma dan Pemrograman',
                'deskripsi'     => 'Materi dasar tentang algoritma, flowchart, dan pseudocode sebagai fondasi pemrograman.',
                'file_materi'   => null,
                'nama_asli_file'=> null,
                'tipe_konten'   => 'link',
                'link_eksternal'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'guru_id'       => $guruBudi['id'],
                'jadwal_id'     => $jadwalPwd['id'] ?? null,
                'judul_materi'  => 'Tipe Data, Variabel, dan Operator',
                'deskripsi'     => 'Memahami berbagai tipe data primitif, cara deklarasi variabel, dan penggunaan operator dalam pemrograman.',
                'file_materi'   => null,
                'nama_asli_file'=> null,
                'tipe_konten'   => 'file',
                'link_eksternal'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'guru_id'       => $guruBudi['id'],
                'jadwal_id'     => $jadwalWeb['id'] ?? null,
                'judul_materi'  => 'Pengenalan HTML5 dan Struktur Dokumen Web',
                'deskripsi'     => 'Memahami anatomi dokumen HTML5, elemen semantik, dan standar pengembangan web modern.',
                'file_materi'   => null,
                'nama_asli_file'=> null,
                'tipe_konten'   => 'link',
                'link_eksternal'=> 'https://developer.mozilla.org/en-US/docs/Web/HTML',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'guru_id'       => $guruBudi['id'],
                'jadwal_id'     => $jadwalWeb['id'] ?? null,
                'judul_materi'  => 'CSS3 dan Desain Responsif',
                'deskripsi'     => 'Belajar styling dengan CSS3, Flexbox, Grid, dan teknik responsive design untuk berbagai ukuran layar.',
                'file_materi'   => null,
                'nama_asli_file'=> null,
                'tipe_konten'   => 'file',
                'link_eksternal'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'guru_id'       => $guruSiti['id'],
                'jadwal_id'     => $jadwalDb['id'] ?? null,
                'judul_materi'  => 'Konsep Dasar Basis Data dan ERD',
                'deskripsi'     => 'Pengenalan konsep basis data relasional, Entity Relationship Diagram (ERD), dan normalisasi tabel.',
                'file_materi'   => null,
                'nama_asli_file'=> null,
                'tipe_konten'   => 'file',
                'link_eksternal'=> null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'guru_id'       => $guruSiti['id'],
                'jadwal_id'     => $jadwalDb['id'] ?? null,
                'judul_materi'  => 'DDL dan DML pada MySQL',
                'deskripsi'     => 'Praktek perintah SQL: CREATE, ALTER, DROP (DDL) serta SELECT, INSERT, UPDATE, DELETE (DML) menggunakan MySQL.',
                'file_materi'   => null,
                'nama_asli_file'=> null,
                'tipe_konten'   => 'link',
                'link_eksternal'=> 'https://dev.mysql.com/doc/',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('materi')->insertBatch($data);
        echo "✅ MateriSeeder: " . count($data) . " materi pembelajaran berhasil ditambahkan.\n";
    }
}
