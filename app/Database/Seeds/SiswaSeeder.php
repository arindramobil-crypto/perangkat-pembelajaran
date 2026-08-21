<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $siswaUsernames = [
            'siswa.andi', 'siswa.dewi', 'siswa.rizky',
            'siswa.maya', 'siswa.fajar', 'siswa.nila',
        ];

        $users = $this->db->table('users')
            ->whereIn('username', $siswaUsernames)
            ->get()->getResultArray();

        $userMap = [];
        foreach ($users as $u) {
            $userMap[$u['username']] = $u['id'];
        }

        $data = [
            [
                'user_id'       => $userMap['siswa.andi'],
                'nis'           => '2526001',
                'nisn'          => '0012345601',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '2007-05-10',
                'alamat'        => 'Jl. Kenanga No. 1, Jakarta',
                'no_telp'       => '089112345601',
            ],
            [
                'user_id'       => $userMap['siswa.dewi'],
                'nis'           => '2526002',
                'nisn'          => '0012345602',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Semarang',
                'tanggal_lahir' => '2007-08-22',
                'alamat'        => 'Jl. Mawar No. 2, Semarang',
                'no_telp'       => '089112345602',
            ],
            [
                'user_id'       => $userMap['siswa.rizky'],
                'nis'           => '2526003',
                'nisn'          => '0012345603',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Malang',
                'tanggal_lahir' => '2007-02-14',
                'alamat'        => 'Jl. Melati No. 3, Malang',
                'no_telp'       => '089112345603',
            ],
            [
                'user_id'       => $userMap['siswa.maya'],
                'nis'           => '2526004',
                'nisn'          => '0012345604',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Bandung',
                'tanggal_lahir' => '2007-11-30',
                'alamat'        => 'Jl. Anggrek No. 4, Bandung',
                'no_telp'       => '089112345604',
            ],
            [
                'user_id'       => $userMap['siswa.fajar'],
                'nis'           => '2526005',
                'nisn'          => '0012345605',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Surakarta',
                'tanggal_lahir' => '2008-01-07',
                'alamat'        => 'Jl. Dahlia No. 5, Surakarta',
                'no_telp'       => '089112345605',
            ],
            [
                'user_id'       => $userMap['siswa.nila'],
                'nis'           => '2526006',
                'nisn'          => '0012345606',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Medan',
                'tanggal_lahir' => '2007-09-18',
                'alamat'        => 'Jl. Tulip No. 6, Medan',
                'no_telp'       => '089112345606',
            ],
        ];

        $this->db->table('siswas')->insertBatch($data);
        echo "✅ SiswaSeeder: " . count($data) . " data siswa berhasil ditambahkan.\n";
    }
}
