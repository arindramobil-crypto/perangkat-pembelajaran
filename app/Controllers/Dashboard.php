<?php
namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\JadwalModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        $db   = \Config\Database::connect();
        $data = [
            'title'        => 'Dashboard',
            'role'         => $role,
            'nama_lengkap' => session()->get('nama_lengkap'),
            'stats'        => [],
        ];

        if ($role === 'Admin') {
            $data = array_merge($data, $this->statsAdmin($db));
        } elseif ($role === 'Guru') {
            $data = array_merge($data, $this->statsGuru($db));
        } elseif ($role === 'Siswa') {
            $data = array_merge($data, $this->statsSiswa($db));
        }

        return view('dashboard/index', $data);
    }

    // ════════════════════════════════════════
    // ADMIN
    // ════════════════════════════════════════
    private function statsAdmin($db): array
    {
        $stats = [
            'total_guru'    => $db->table('gurus')->countAllResults(),
            'total_siswa'   => $db->table('siswas')->countAllResults(),
            'total_kelas'   => $db->table('kelas')->countAllResults(),
            'total_mapel'   => $db->table('mata_pelajaran')->countAllResults(),
            'total_materi'  => $db->table('materi')->countAllResults(),
            'total_ulangan' => $db->table('ulangan')->countAllResults(),
            'total_jadwal'  => $db->table('jadwal')->countAllResults(),
            'total_ujian_selesai' => $db->table('jawaban_siswa')
                ->where('status', 'Selesai')->countAllResults(),
        ];

        // Siswa per kelas (bar chart)
        $siswaPerKelas = $db->query("
            SELECT k.nama_kelas, COUNT(ak.siswa_id) as total
            FROM kelas k
            LEFT JOIN anggota_kelas ak ON ak.kelas_id = k.id
            GROUP BY k.id, k.nama_kelas
            ORDER BY k.nama_kelas
        ")->getResultArray();

        // Materi per mapel — join via jadwal
        // (tabel materi tidak punya mapel_id langsung, relasi via jadwal_id)
        $materiPerMapel = $db->query("
            SELECT mp.nama_mapel, COUNT(m.id) as total
            FROM mata_pelajaran mp
            LEFT JOIN jadwal j   ON j.mapel_id  = mp.id
            LEFT JOIN materi m   ON m.jadwal_id = j.id
            GROUP BY mp.id, mp.nama_mapel
            HAVING total > 0
            ORDER BY total DESC
            LIMIT 8
        ")->getResultArray();

        // Jika tidak ada relasi via jadwal, tampilkan mapel saja
        if (empty($materiPerMapel)) {
            $materiPerMapel = $db->query("
                SELECT nama_mapel, 0 as total FROM mata_pelajaran LIMIT 8
            ")->getResultArray();
        }

        // Aktivitas terbaru (materi + ulangan)
        $aktivitas = $db->query("
            SELECT 'Materi' as tipe, judul_materi as judul, created_at FROM materi
            UNION ALL
            SELECT 'Ujian', judul, created_at FROM ulangan
            ORDER BY created_at DESC LIMIT 8
        ")->getResultArray();

        // User terbaru
        $userTerbaru = $db->query("
            SELECT nama_lengkap, role, created_at
            FROM users ORDER BY created_at DESC LIMIT 5
        ")->getResultArray();

        return [
            'stats'          => $stats,
            'siswaPerKelas'  => $siswaPerKelas,
            'materiPerMapel' => $materiPerMapel,
            'aktivitas'      => $aktivitas,
            'userTerbaru'    => $userTerbaru,
        ];
    }

    // ════════════════════════════════════════
    // GURU
    // ════════════════════════════════════════
    private function statsGuru($db): array
    {
        $guruModel = new GuruModel();
        $guru      = $guruModel->where('user_id', session()->get('id'))->first();
        if (! $guru) return ['stats' => []];

        $guruId = (int)$guru['id'];

        $stats = [
            'total_jadwal'  => $db->table('jadwal')
                ->where('guru_id', $guruId)->countAllResults(),
            'total_materi'  => $db->table('materi')
                ->where('guru_id', $guruId)->countAllResults(),
            'total_ulangan' => $db->table('ulangan')
                ->where('guru_id', $guruId)->countAllResults(),
            'total_soal'    => (int)($db->query("
                SELECT COUNT(s.id) as c FROM soal s
                JOIN ulangan u ON u.id = s.ulangan_id
                WHERE u.guru_id = {$guruId}
            ")->getRowArray()['c'] ?? 0),
            'total_dinilai' => $db->table('jawaban_siswa')
                ->join('ulangan', 'ulangan.id = jawaban_siswa.ulangan_id')
                ->where('ulangan.guru_id', $guruId)
                ->where('jawaban_siswa.status', 'Selesai')
                ->countAllResults(),
            'perlu_koreksi' => $db->table('jawaban_siswa')
                ->join('ulangan', 'ulangan.id = jawaban_siswa.ulangan_id')
                ->where('ulangan.guru_id', $guruId)
                ->where('jawaban_siswa.status', 'Perlu Koreksi')
                ->countAllResults(),
        ];

        // Jadwal semua hari guru ini
        $jadwalList = (new JadwalModel())->getJadwalByGuru($guruId);

        // Rata-rata nilai per ulangan (6 terbaru)
        $nilaiPerUlangan = $db->query("
            SELECT u.judul, ROUND(AVG(js.nilai_akhir), 1) as rata, u.kkm
            FROM jawaban_siswa js
            JOIN ulangan u ON u.id = js.ulangan_id
            WHERE u.guru_id = {$guruId}
              AND js.nilai_akhir IS NOT NULL
            GROUP BY u.id, u.judul, u.kkm
            ORDER BY u.id DESC LIMIT 6
        ")->getResultArray();

        // Distribusi tipe soal
        $tipesSoal = $db->query("
            SELECT s.tipe_soal, COUNT(*) as total
            FROM soal s
            JOIN ulangan u ON u.id = s.ulangan_id
            WHERE u.guru_id = {$guruId}
            GROUP BY s.tipe_soal
        ")->getResultArray();

        // Materi terbaru yang diupload guru ini
        // Materi join jadwal join mata_pelajaran untuk dapat nama mapel
        $materiTerbaru = $db->query("
            SELECT m.judul_materi, m.created_at,
                   COALESCE(mp.nama_mapel, '-') as nama_mapel
            FROM materi m
            LEFT JOIN jadwal j   ON j.id = m.jadwal_id
            LEFT JOIN mata_pelajaran mp ON mp.id = j.mapel_id
            WHERE m.guru_id = {$guruId}
            ORDER BY m.created_at DESC LIMIT 5
        ")->getResultArray();

        // Status ujian yang dibuat guru ini
        $ujianAktif = $db->query("
            SELECT u.judul, u.tipe,
                   COUNT(uk.id) as total_kelas,
                   (SELECT COUNT(*) FROM jawaban_siswa js
                    WHERE js.ulangan_id = u.id AND js.status = 'Selesai') as sudah_kerjakan
            FROM ulangan u
            LEFT JOIN ulangan_kelas uk ON uk.ulangan_id = u.id
            WHERE u.guru_id = {$guruId}
            GROUP BY u.id, u.judul, u.tipe
            ORDER BY u.id DESC LIMIT 5
        ")->getResultArray();

        return [
            'stats'           => $stats,
            'jadwalList'      => $jadwalList,
            'nilaiPerUlangan' => $nilaiPerUlangan,
            'tipesSoal'       => $tipesSoal,
            'materiTerbaru'   => $materiTerbaru,
            'ujianAktif'      => $ujianAktif,
            'guru'            => $guru,
        ];
    }

    // ════════════════════════════════════════
    // SISWA
    // ════════════════════════════════════════
    private function statsSiswa($db): array
    {
        $siswaModel = new SiswaModel();
        $siswa      = $siswaModel->where('user_id', session()->get('id'))->first();
        if (! $siswa) return ['stats' => ['siswa' => null, 'kelas_info' => null]];

        $siswaId  = (int)$siswa['id'];
        $enrollment = $db->table('anggota_kelas')
            ->where('siswa_id', $siswaId)
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();

        if (! $enrollment) {
            return ['stats' => ['siswa' => $siswa, 'kelas_info' => null]];
        }

        $kelasId   = (int)$enrollment['kelas_id'];
        $kelasInfo = $db->table('kelas')->where('id', $kelasId)->get()->getRowArray();

        // Kehadiran rinci
        $hadir = $db->table('presensi_detail')
            ->where('siswa_id', $siswaId)->where('status', 'Hadir')->countAllResults();
        $sakit = $db->table('presensi_detail')
            ->where('siswa_id', $siswaId)->where('status', 'Sakit')->countAllResults();
        $izin  = $db->table('presensi_detail')
            ->where('siswa_id', $siswaId)->where('status', 'Izin')->countAllResults();
        $alfa  = $db->table('presensi_detail')
            ->where('siswa_id', $siswaId)->where('status', 'Alfa')->countAllResults();
        $totalPd  = $hadir + $sakit + $izin + $alfa;
        $pctHadir = $totalPd > 0 ? round(($hadir / $totalPd) * 100) : 0;

        // Total materi untuk kelas ini (via jadwal)
        $totalMateri = (int)($db->query("
            SELECT COUNT(m.id) as c FROM materi m
            JOIN jadwal j ON j.id = m.jadwal_id
            WHERE j.kelas_id = {$kelasId}
        ")->getRowArray()['c'] ?? 0);

        // Ujian belum dikerjakan
        $ujianPending = (int)($db->query("
            SELECT COUNT(uk.id) as c FROM ulangan_kelas uk
            WHERE uk.kelas_id = {$kelasId}
              AND uk.ulangan_id NOT IN (
                  SELECT ulangan_id FROM jawaban_siswa WHERE siswa_id = {$siswaId}
              )
        ")->getRowArray()['c'] ?? 0);

        $stats = [
            'siswa'         => $siswa,
            'kelas_info'    => $kelasInfo,
            'total_jadwal'  => $db->table('jadwal')
                ->where('kelas_id', $kelasId)->countAllResults(),
            'total_materi'  => $totalMateri,
            'total_hadir'   => $hadir,
            'total_absen'   => $sakit + $izin + $alfa,
            'total_sakit'   => $sakit,
            'total_izin'    => $izin,
            'total_alfa'    => $alfa,
            'pct_hadir'     => $pctHadir,
            'total_ujian'   => $db->table('jawaban_siswa')
                ->where('siswa_id', $siswaId)->where('status', 'Selesai')->countAllResults(),
            'ujian_pending' => $ujianPending,
        ];

        // Nilai per ujian (bar chart)
        $nilaiSiswa = $db->query("
            SELECT u.judul, js.nilai_akhir, u.kkm
            FROM jawaban_siswa js
            JOIN ulangan u ON u.id = js.ulangan_id
            WHERE js.siswa_id = {$siswaId}
              AND js.nilai_akhir IS NOT NULL
            ORDER BY js.id DESC LIMIT 8
        ")->getResultArray();

        // Ujian yang belum dikerjakan
        $ujianBelum = $db->query("
            SELECT u.judul, u.tipe, u.durasi
            FROM ulangan_kelas uk
            JOIN ulangan u ON u.id = uk.ulangan_id
            WHERE uk.kelas_id = {$kelasId}
              AND uk.ulangan_id NOT IN (
                  SELECT ulangan_id FROM jawaban_siswa WHERE siswa_id = {$siswaId}
              )
            ORDER BY u.id DESC LIMIT 5
        ")->getResultArray();

        // Materi terbaru untuk kelas ini
        $materiTerbaru = $db->query("
            SELECT m.judul_materi, m.created_at,
                   COALESCE(mp.nama_mapel, '-') as nama_mapel, m.id
            FROM materi m
            JOIN jadwal j ON j.id = m.jadwal_id
            LEFT JOIN mata_pelajaran mp ON mp.id = j.mapel_id
            WHERE j.kelas_id = {$kelasId}
            ORDER BY m.created_at DESC LIMIT 5
        ")->getResultArray();

        return [
            'stats'         => $stats,
            'nilaiSiswa'    => $nilaiSiswa,
            'ujianBelum'    => $ujianBelum,
            'materiTerbaru' => $materiTerbaru,
        ];
    }
}
