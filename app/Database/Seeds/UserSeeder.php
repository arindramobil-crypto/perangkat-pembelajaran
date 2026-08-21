<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Disable FK check, lalu bersihkan semua tabel terkait secara berurutan
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('jawaban_siswa_detail')->truncate();
        $this->db->table('jawaban_siswa')->truncate();
        $this->db->table('soal')->truncate();
        $this->db->table('ulangan_kelas')->truncate();
        $this->db->table('ulangan')->truncate();
        $this->db->table('presensi_detail')->truncate();
        $this->db->table('presensi')->truncate();
        $this->db->table('materi_kelas')->truncate();
        $this->db->table('materi')->truncate();
        $this->db->table('jadwal')->truncate();
        $this->db->table('anggota_kelas')->truncate();
        $this->db->table('kelas')->truncate();
        $this->db->table('mata_pelajaran')->truncate();
        $this->db->table('nilai')->truncate();
        $this->db->table('siswas')->truncate();
        $this->db->table('gurus')->truncate();
        $this->db->table('users')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        $now = date('Y-m-d H:i:s');

        $data = [
            // ===================== ADMIN =====================
            [
                'username'     => 'admin',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Administrator Utama',
                'email'        => 'admin@smk.sch.id',
                'role'         => 'Admin',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ===================== GURU =====================
            [
                'username'     => 'guru.budi',
                'password'     => password_hash('guru123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Budi Santoso, S.Kom',
                'email'        => 'budi.santoso@smk.sch.id',
                'role'         => 'Guru',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'guru.siti',
                'password'     => password_hash('guru123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Siti Rahayu, S.Pd',
                'email'        => 'siti.rahayu@smk.sch.id',
                'role'         => 'Guru',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'guru.ahmad',
                'password'     => password_hash('guru123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Ahmad Fauzi, M.Pd',
                'email'        => 'ahmad.fauzi@smk.sch.id',
                'role'         => 'Guru',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ===================== SISWA =====================
            [
                'username'     => 'siswa.andi',
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Andi Prasetyo',
                'email'        => 'andi.prasetyo@siswa.smk.sch.id',
                'role'         => 'Siswa',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'siswa.dewi',
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Dewi Lestari',
                'email'        => 'dewi.lestari@siswa.smk.sch.id',
                'role'         => 'Siswa',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'siswa.rizky',
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Rizky Ramadhan',
                'email'        => 'rizky.ramadhan@siswa.smk.sch.id',
                'role'         => 'Siswa',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'siswa.maya',
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Maya Sari',
                'email'        => 'maya.sari@siswa.smk.sch.id',
                'role'         => 'Siswa',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'siswa.fajar',
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Fajar Nugroho',
                'email'        => 'fajar.nugroho@siswa.smk.sch.id',
                'role'         => 'Siswa',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'username'     => 'siswa.nila',
                'password'     => password_hash('siswa123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Nila Permatasari',
                'email'        => 'nila.permatasari@siswa.smk.sch.id',
                'role'         => 'Siswa',
                'foto'         => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($data);
        echo "✅ UserSeeder: " . count($data) . " user berhasil ditambahkan.\n";
        echo "   Admin  : admin / admin123\n";
        echo "   Guru   : guru.budi, guru.siti, guru.ahmad / guru123\n";
        echo "   Siswa  : siswa.andi, siswa.dewi, siswa.rizky, siswa.maya, siswa.fajar, siswa.nila / siswa123\n";
    }
}
