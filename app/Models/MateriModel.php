<?php
namespace App\Models;

use CodeIgniter\Model;

/**
 * MateriModel
 * Mengelola data materi pembelajaran yang diunggah oleh Guru.
 */
class MateriModel extends Model
{
    protected $table      = 'materi';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'guru_id',
        'jadwal_id',
        'judul_materi',
        'deskripsi',
        'file_materi',
        'nama_asli_file',
        'tipe_konten',
        'link_eksternal',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ── Aturan validasi bawaan CI4 ───────────────────────────────
    protected $validationRules = [
        'judul_materi' => 'required|min_length[3]|max_length[255]',
        'jadwal_id'    => 'required|is_natural_no_zero',
        'deskripsi'    => 'permit_empty|max_length[1000]',
    ];

    protected $validationMessages = [
        'judul_materi' => [
            'required'   => 'Judul materi wajib diisi.',
            'min_length' => 'Judul materi minimal 3 karakter.',
            'max_length' => 'Judul materi maksimal 255 karakter.',
        ],
        'jadwal_id' => [
            'required'            => 'Silakan pilih kelas/jadwal tujuan.',
            'is_natural_no_zero'  => 'Jadwal yang dipilih tidak valid.',
        ],
    ];

    // ── Query bantu: Guru melihat semua materinya ────────────────
    public function getMateriByGuru(int $guru_id): array
    {
        return $this
            ->select('materi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, jadwal.hari')
            ->join('jadwal',         'jadwal.id = materi.jadwal_id')
            ->join('kelas',          'kelas.id = jadwal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('materi.guru_id', $guru_id)
            ->orderBy('materi.created_at', 'DESC')
            ->findAll();
    }

    // ── Query bantu: Siswa melihat materi dari kelasnya ──────────
    public function getMateriByKelas(int $kelas_id): array
    {
        return $this
            ->select('materi.*, mata_pelajaran.nama_mapel, users.nama_lengkap AS nama_guru')
            ->join('jadwal',         'jadwal.id = materi.jadwal_id')
            ->join('kelas',          'kelas.id = jadwal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('gurus',          'gurus.id = materi.guru_id')
            ->join('users',          'users.id = gurus.user_id')
            ->where('jadwal.kelas_id', $kelas_id)
            ->orderBy('materi.created_at', 'DESC')
            ->findAll();
    }

    // ── Query bantu: Ambil 1 data dengan info lengkap ────────────
    public function getMateriDetail(int $id): ?array
    {
        return $this
            ->select('materi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, jadwal.hari')
            ->join('jadwal',         'jadwal.id = materi.jadwal_id')
            ->join('kelas',          'kelas.id = jadwal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('materi.id', $id)
            ->first();
    }

    // ── Query: Siswa — pencarian + filter mapel ──────────────────
    /**
     * Ambil materi kelas dengan dukungan pencarian kata kunci dan
     * filter per mata pelajaran. Digunakan oleh MateriSiswa controller.
     *
     * @param  int    $kelas_id    ID kelas siswa
     * @param  string $keyword     Kata kunci pencarian (judul / deskripsi / nama guru)
     * @param  int    $mapel_id    0 = semua mapel
     * @param  string $tipe        '' = semua | 'file' | 'link'
     * @param  string $sort        'terbaru' | 'terlama' | 'az' | 'za'
     * @return array
     */
    public function getMateriByKelasWithSearch(
        int    $kelas_id,
        string $keyword  = '',
        int    $mapel_id = 0,
        string $tipe     = '',
        string $sort     = 'terbaru'
    ): array {
        $builder = $this
            ->select('
                materi.id,
                materi.judul_materi,
                materi.deskripsi,
                materi.file_materi,
                materi.nama_asli_file,
                materi.tipe_konten,
                materi.link_eksternal,
                materi.created_at,
                mata_pelajaran.id      AS mapel_id,
                mata_pelajaran.nama_mapel,
                mata_pelajaran.kode_mapel,
                users.nama_lengkap     AS nama_guru,
                kelas.nama_kelas
            ')
            ->join('jadwal',         'jadwal.id = materi.jadwal_id')
            ->join('kelas',          'kelas.id = jadwal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('gurus',          'gurus.id = materi.guru_id')
            ->join('users',          'users.id = gurus.user_id')
            ->where('jadwal.kelas_id', $kelas_id);

        // Filter kata kunci
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('materi.judul_materi', $keyword)
                ->orLike('materi.deskripsi', $keyword)
                ->orLike('users.nama_lengkap', $keyword)
                ->orLike('mata_pelajaran.nama_mapel', $keyword)
            ->groupEnd();
        }

        // Filter mata pelajaran
        if ($mapel_id > 0) {
            $builder->where('mata_pelajaran.id', $mapel_id);
        }

        // Filter tipe konten
        if ($tipe === 'file' || $tipe === 'link') {
            $builder->where('materi.tipe_konten', $tipe);
        }

        // Urutan
        $orderMap = [
            'terbaru' => ['materi.created_at', 'DESC'],
            'terlama' => ['materi.created_at', 'ASC'],
            'az'      => ['materi.judul_materi', 'ASC'],
            'za'      => ['materi.judul_materi', 'DESC'],
        ];
        [$col, $dir] = $orderMap[$sort] ?? ['materi.created_at', 'DESC'];
        $builder->orderBy($col, $dir);

        return $builder->findAll();
    }

    // ── Query: Daftar mapel unik di kelas tertentu (untuk tab filter) ──
    public function getMapelByKelas(int $kelas_id): array
    {
        return $this
            ->select('mata_pelajaran.id, mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel, COUNT(materi.id) AS jumlah')
            ->join('jadwal',         'jadwal.id = materi.jadwal_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('jadwal.kelas_id', $kelas_id)
            ->groupBy('mata_pelajaran.id')
            ->orderBy('mata_pelajaran.nama_mapel', 'ASC')
            ->findAll();
    }
}
