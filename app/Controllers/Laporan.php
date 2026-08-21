<?php
namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\PengaturanSekolahModel;

/**
 * Laporan Controller
 * Menghasilkan halaman cetak (print-to-PDF via browser) untuk:
 *  1. GET /laporan/nilai-kelas?kelas_id=&tahun_id= → Rekap nilai per kelas (Guru/Admin)
 *  2. GET /laporan/absensi?kelas_id=&jadwal_id=   → Rekap absensi (Guru/Admin)
 *  3. GET /laporan/raport-siswa/{siswa_id}         → Raport mini 1 siswa (Guru/Admin/Siswa-sendiri)
 */
class Laporan extends BaseController
{
    private function sekolah(): array
    {
        return (new PengaturanSekolahModel())->getOrCreate();
    }

    // ════════════════════════════════════════════════════
    // 1. REKAP NILAI KELAS
    // ════════════════════════════════════════════════════
    public function nilaiKelas()
    {
        if (!in_array(session()->get('role'), ['Guru', 'Admin'])) {
            return redirect()->to('/dashboard');
        }

        $db       = \Config\Database::connect();
        $kelasId  = $this->request->getGet('kelas_id');
        $tahunId  = $this->request->getGet('tahun_id');

        $kelasList = $db->table('kelas')->orderBy('nama_kelas')->get()->getResultArray();
        $tahunList = $db->table('tahun_pelajaran')->orderBy('id', 'DESC')->get()->getResultArray();
        $mapelList = [];
        $siswaData = [];
        $kelasInfo = null;
        $tahunInfo = null;

        if ($kelasId && $tahunId) {
            $kelasInfo = $db->table('kelas')->where('id', $kelasId)->get()->getRowArray();
            $tahunInfo = $db->table('tahun_pelajaran')->where('id', $tahunId)->get()->getRowArray();

            // Daftar siswa di kelas ini
            $siswas = $db->query("
                SELECT s.id as siswa_id, s.nis, u.nama_lengkap
                FROM anggota_kelas ak
                JOIN siswas s  ON s.id  = ak.siswa_id
                JOIN users  u  ON u.id  = s.user_id
                WHERE ak.kelas_id = {$kelasId}
                ORDER BY u.nama_lengkap ASC
            ")->getResultArray();

            // Daftar mapel yang ada ulangannya untuk kelas ini
            $mapelList = $db->query("
                SELECT DISTINCT mp.id, mp.kode_mapel, mp.nama_mapel
                FROM ulangan_kelas uk
                JOIN ulangan      ul ON ul.id = uk.ulangan_id
                JOIN mata_pelajaran mp ON mp.id = ul.mapel_id
                WHERE uk.kelas_id = {$kelasId}
                ORDER BY mp.nama_mapel ASC
            ")->getResultArray();

            // Nilai per siswa per mapel
            foreach ($siswas as $s) {
                $siswaId = $s['siswa_id'];
                $row = [
                    'siswa_id'    => $siswaId,
                    'nis'         => $s['nis'],
                    'nama'        => $s['nama_lengkap'],
                    'nilai_mapel' => [],
                    'rata_global' => 0,
                ];

                $sumAll = 0; $cntAll = 0;
                foreach ($mapelList as $mp) {
                    $hasil = $db->query("
                        SELECT ROUND(AVG(js.nilai_akhir), 1) as rata, COUNT(js.id) as cnt
                        FROM jawaban_siswa js
                        JOIN ulangan ul       ON ul.id  = js.ulangan_id
                        JOIN ulangan_kelas uk ON uk.ulangan_id = ul.id AND uk.kelas_id = {$kelasId}
                        WHERE js.siswa_id    = {$siswaId}
                          AND ul.mapel_id   = {$mp['id']}
                          AND js.status     = 'Selesai'
                          AND js.nilai_akhir IS NOT NULL
                    ")->getRowArray();

                    $rata = $hasil['rata'] ?? null;
                    $row['nilai_mapel'][$mp['id']] = [
                        'rata' => $rata,
                        'cnt'  => $hasil['cnt'] ?? 0,
                    ];
                    if ($rata !== null) { $sumAll += $rata; $cntAll++; }
                }
                $row['rata_global'] = $cntAll > 0 ? round($sumAll / $cntAll, 1) : null;
                $siswaData[] = $row;
            }
        }

        return view('laporan/nilai_kelas', [
            'title'      => 'Cetak Rekap Nilai Kelas',
            'sekolah'    => $this->sekolah(),
            'kelasList'  => $kelasList,
            'tahunList'  => $tahunList,
            'kelasInfo'  => $kelasInfo,
            'tahunInfo'  => $tahunInfo,
            'mapelList'  => $mapelList,
            'siswaData'  => $siswaData,
            'kelasId'    => $kelasId,
            'tahunId'    => $tahunId,
        ]);
    }

    // ════════════════════════════════════════════════════
    // 2. REKAP ABSENSI
    // ════════════════════════════════════════════════════
    public function absensi()
    {
        if (!in_array(session()->get('role'), ['Guru', 'Admin'])) {
            return redirect()->to('/dashboard');
        }

        $db      = \Config\Database::connect();
        $kelasId = $this->request->getGet('kelas_id');
        $bulan   = $this->request->getGet('bulan') ?? date('Y-m');

        $kelasList = $db->table('kelas')->orderBy('nama_kelas')->get()->getResultArray();
        $kelasInfo = null;
        $siswaData = [];
        $jadwalList = [];

        if ($kelasId) {
            $kelasInfo  = $db->table('kelas')->where('id', $kelasId)->get()->getRowArray();
            $bulanStart = $bulan . '-01';
            $bulanEnd   = date('Y-m-t', strtotime($bulanStart));

            // Semua pertemuan (presensi) kelas ini di bulan tsb
            $jadwalList = $db->query("
                SELECT p.id as presensi_id, p.tanggal, p.created_at,
                       mp.nama_mapel, j.hari, j.jam_mulai, u.nama_lengkap as nama_guru
                FROM presensi p
                JOIN jadwal j ON j.id = p.jadwal_id
                JOIN mata_pelajaran mp ON mp.id = j.mapel_id
                JOIN gurus g ON g.id = j.guru_id
                JOIN users  u ON u.id = g.user_id
                WHERE j.kelas_id = {$kelasId}
                  AND p.tanggal BETWEEN '{$bulanStart}' AND '{$bulanEnd}'
                ORDER BY p.tanggal ASC, j.jam_mulai ASC
            ")->getResultArray();

            // Daftar siswa + status per pertemuan
            $siswas = $db->query("
                SELECT s.id as siswa_id, s.nis, u.nama_lengkap
                FROM anggota_kelas ak
                JOIN siswas s ON s.id = ak.siswa_id
                JOIN users  u ON u.id = s.user_id
                WHERE ak.kelas_id = {$kelasId}
                ORDER BY u.nama_lengkap ASC
            ")->getResultArray();

            foreach ($siswas as $s) {
                $row = [
                    'nis'       => $s['nis'],
                    'nama'      => $s['nama_lengkap'],
                    'presensi'  => [],
                    'hadir'     => 0, 'sakit' => 0, 'izin' => 0, 'alfa' => 0,
                ];
                foreach ($jadwalList as $p) {
                    $detail = $db->table('presensi_detail')
                        ->where('presensi_id', $p['presensi_id'])
                        ->where('siswa_id', $s['siswa_id'])
                        ->get()->getRowArray();
                    $status = $detail['status'] ?? '-';
                    $row['presensi'][$p['presensi_id']] = $status;
                    if ($status === 'Hadir') $row['hadir']++;
                    elseif ($status === 'Sakit') $row['sakit']++;
                    elseif ($status === 'Izin')  $row['izin']++;
                    elseif ($status === 'Alfa')  $row['alfa']++;
                }
                $siswaData[] = $row;
            }
        }

        return view('laporan/absensi', [
            'title'      => 'Cetak Rekap Absensi',
            'sekolah'    => $this->sekolah(),
            'kelasList'  => $kelasList,
            'kelasInfo'  => $kelasInfo,
            'kelasId'    => $kelasId,
            'bulan'      => $bulan,
            'jadwalList' => $jadwalList,
            'siswaData'  => $siswaData,
        ]);
    }

    // ════════════════════════════════════════════════════
    // 3. RAPORT MINI SISWA
    // ════════════════════════════════════════════════════
    public function raportSiswa(int $siswaId = 0)
    {
        $role = session()->get('role');
        $db   = \Config\Database::connect();

        // Siswa hanya bisa cetak raport sendiri
        if ($role === 'Siswa') {
            $siswaModel = new SiswaModel();
            $mySiswa    = $siswaModel->where('user_id', session()->get('id'))->first();
            if (!$mySiswa || $mySiswa['id'] != $siswaId) {
                return redirect()->to('/buku-nilai')
                    ->with('error', 'Anda hanya bisa mencetak raport Anda sendiri.');
            }
        } elseif (!in_array($role, ['Guru', 'Admin'])) {
            return redirect()->to('/dashboard');
        }

        $siswa = $db->query("
            SELECT s.*, u.nama_lengkap, u.username
            FROM siswas s JOIN users u ON u.id = s.user_id
            WHERE s.id = {$siswaId}
        ")->getRowArray();

        if (!$siswa) return redirect()->to('/buku-nilai');

        // Kelas aktif siswa ini
        $enrollment = $db->table('anggota_kelas')
            ->where('siswa_id', $siswaId)->orderBy('id','DESC')->get()->getRowArray();
        $kelasInfo = $enrollment
            ? $db->table('kelas')->where('id', $enrollment['kelas_id'])->get()->getRowArray()
            : null;

        // Nilai per mapel (semua ujian yang sudah diselesaikan)
        $nilaiPerMapel = $db->query("
            SELECT mp.kode_mapel, mp.nama_mapel,
                   COUNT(js.id)                  as total_ujian,
                   ROUND(AVG(js.nilai_akhir), 1) as rata_rata,
                   MAX(js.nilai_akhir)            as nilai_max,
                   MIN(js.nilai_akhir)            as nilai_min,
                   MAX(ul.kkm)                   as kkm
            FROM jawaban_siswa js
            JOIN ulangan ul       ON ul.id  = js.ulangan_id
            JOIN mata_pelajaran mp ON mp.id = ul.mapel_id
            WHERE js.siswa_id  = {$siswaId}
              AND js.status    = 'Selesai'
              AND js.nilai_akhir IS NOT NULL
            GROUP BY mp.id, mp.kode_mapel, mp.nama_mapel
            ORDER BY mp.nama_mapel ASC
        ")->getResultArray();

        // Kehadiran
        $kehadiran = $db->query("
            SELECT
                SUM(CASE WHEN status='Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status='Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status='Izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status='Alfa'  THEN 1 ELSE 0 END) as alfa,
                COUNT(*) as total
            FROM presensi_detail WHERE siswa_id = {$siswaId}
        ")->getRowArray();

        $totalHadir = (int)($kehadiran['hadir'] ?? 0);
        $totalAll   = (int)($kehadiran['total'] ?? 0);
        $pctHadir   = $totalAll > 0 ? round(($totalHadir / $totalAll) * 100, 1) : 0;

        // Semua nilai untuk hitung rata global
        $allNilai   = array_column($nilaiPerMapel, 'rata_rata');
        $rataGlobal = count($allNilai) > 0
            ? round(array_sum($allNilai) / count($allNilai), 1) : 0;

        return view('laporan/raport_siswa', [
            'title'        => 'Raport Mini — ' . $siswa['nama_lengkap'],
            'sekolah'      => $this->sekolah(),
            'siswa'        => $siswa,
            'kelasInfo'    => $kelasInfo,
            'nilaiPerMapel'=> $nilaiPerMapel,
            'kehadiran'    => $kehadiran,
            'pctHadir'     => $pctHadir,
            'rataGlobal'   => $rataGlobal,
        ]);
    }
}
