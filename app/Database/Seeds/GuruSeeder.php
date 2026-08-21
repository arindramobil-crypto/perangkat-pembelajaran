<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run()
    {
        // Ambil ID user guru berdasarkan username
        $users = $this->db->table('users')
            ->whereIn('username', ['guru.budi', 'guru.siti', 'guru.ahmad'])
            ->get()->getResultArray();

        $userMap = [];
        foreach ($users as $u) {
            $userMap[$u['username']] = $u['id'];
        }

        $data = [
            [
                'user_id'       => $userMap['guru.budi'],
                'nip'           => '198501012010011001',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Bandung',
                'tanggal_lahir' => '1985-01-01',
                'alamat'        => 'Jl. Merdeka No. 10, Bandung',
                'no_telp'       => '081234567890',
            ],
            [
                'user_id'       => $userMap['guru.siti'],
                'nip'           => '199003152015032002',
                'jenis_kelamin' => 'P',
                'tempat_lahir'  => 'Yogyakarta',
                'tanggal_lahir' => '1990-03-15',
                'alamat'        => 'Jl. Pahlawan No. 22, Yogyakarta',
                'no_telp'       => '082345678901',
            ],
            [
                'user_id'       => $userMap['guru.ahmad'],
                'nip'           => '198812202012121003',
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'Surabaya',
                'tanggal_lahir' => '1988-12-20',
                'alamat'        => 'Jl. Diponegoro No. 5, Surabaya',
                'no_telp'       => '083456789012',
            ],
        ];

        $this->db->table('gurus')->insertBatch($data);
        echo "✅ GuruSeeder: " . count($data) . " data guru berhasil ditambahkan.\n";
    }
}
