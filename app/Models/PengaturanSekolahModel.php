<?php
namespace App\Models;

use CodeIgniter\Model;

/**
 * PengaturanSekolahModel
 * Menyimpan profil dan identitas sekolah (singleton row — hanya 1 baris).
 */
class PengaturanSekolahModel extends Model
{
    protected $table      = 'pengaturan_sekolah';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'nama_sekolah', 'singkatan', 'nss', 'npsn', 'akreditasi',
        'tahun_berdiri', 'logo', 'alamat', 'kecamatan', 'kota',
        'provinsi', 'kode_pos', 'no_telp', 'email', 'website',
        'nama_kepala_sekolah', 'nip_kepala_sekolah', 'visi', 'misi',
    ];

    protected $validationRules = [
        'nama_sekolah' => 'required|min_length[3]|max_length[255]',
        'email'        => 'permit_empty|valid_email',
        'website'      => 'permit_empty|max_length[200]',
    ];

    protected $validationMessages = [
        'nama_sekolah' => [
            'required'   => 'Nama sekolah wajib diisi.',
            'min_length' => 'Nama sekolah minimal 3 karakter.',
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid.',
        ],
    ];

    // ── Ambil satu-satunya baris pengaturan (atau buat jika belum ada) ──
    public function getPengaturan(): array
    {
        $this->ensureTableExists();
        $data = $this->first();

        if (! $data) {
            // Insert default jika tabel kosong
            $this->insert([
                'nama_sekolah' => 'SMK Perangkat Pembelajaran',
                'singkatan'    => 'SMK PP',
                'akreditasi'   => 'A',
                'kota'         => 'Kota Anda',
                'provinsi'     => 'Provinsi Anda',
            ]);
            $data = $this->first();
        }

        return $data ?? [];
    }

    // ── Update pengaturan (always update row id=1) ──────────────
    public function updatePengaturan(array $data): bool
    {
        $existing = $this->first();
        if ($existing) {
            return $this->update($existing['id'], $data);
        }
        return (bool) $this->insert($data);
    }

    // ── Pastikan tabel ada (auto-create jika belum) ─────────────
    private function ensureTableExists(): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('pengaturan_sekolah')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `pengaturan_sekolah` (
                    `id`                    INT(11)      NOT NULL AUTO_INCREMENT,
                    `nama_sekolah`          VARCHAR(255) NOT NULL DEFAULT 'SMK Perangkat Pembelajaran',
                    `singkatan`             VARCHAR(50)           DEFAULT 'SMK PP',
                    `nss`                   VARCHAR(30)           DEFAULT NULL,
                    `npsn`                  VARCHAR(20)           DEFAULT NULL,
                    `akreditasi`            VARCHAR(5)            DEFAULT 'A',
                    `tahun_berdiri`         YEAR                  DEFAULT NULL,
                    `logo`                  VARCHAR(255)          DEFAULT NULL,
                    `alamat`                TEXT                  DEFAULT NULL,
                    `kecamatan`             VARCHAR(100)          DEFAULT NULL,
                    `kota`                  VARCHAR(100)          DEFAULT NULL,
                    `provinsi`              VARCHAR(100)          DEFAULT NULL,
                    `kode_pos`              VARCHAR(10)           DEFAULT NULL,
                    `no_telp`               VARCHAR(25)           DEFAULT NULL,
                    `email`                 VARCHAR(150)          DEFAULT NULL,
                    `website`               VARCHAR(200)          DEFAULT NULL,
                    `nama_kepala_sekolah`   VARCHAR(150)          DEFAULT NULL,
                    `nip_kepala_sekolah`    VARCHAR(30)           DEFAULT NULL,
                    `visi`                  TEXT                  DEFAULT NULL,
                    `misi`                  TEXT                  DEFAULT NULL,
                    `created_at`            DATETIME              DEFAULT CURRENT_TIMESTAMP,
                    `updated_at`            DATETIME              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }
}
