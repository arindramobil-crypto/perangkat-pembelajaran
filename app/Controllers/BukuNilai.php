<?php
namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\UlanganModel;
use App\Models\JawabanSiswaModel;
use App\Models\MataPelajaranModel;

/**
 * BukuNilai Controller
 * Menampilkan rekapitulasi seluruh nilai ujian siswa (buku nilai digital)
 * Routes:
 *   GET /buku-nilai          → Siswa melihat nilai sendiri
 *   GET /buku-nilai/siswa/{siswa_id} → Guru melihat nilai 1 siswa
 *   GET /buku-nilai/kelas    → Guru melihat rekap per kelas (semua mapel)
 */
class BukuNilai extends BaseController
{
    // ── Siswa: Nilai semua ujian yang pernah dikerjakan ──────────
    public function index()
    {
        $role = session()->get('role');

        if ($role === 'Guru') {
            return $this->rekapKelas();
        }

        // Siswa
        $siswaModel  = new SiswaModel();
        $siswa       = $siswaModel->where('user_id', session()->get('id'))->first();
        if (!$siswa) return redirect()->to('/dashboard');

        $db = \Config\Database::connect();

        // Ambil semua nilai ujian siswa ini, digroup per mata pelajaran
        $nilaiList = $db->table('jawaban_siswa')
            ->select('jawaban_siswa.id as js_id, jawaban_siswa.nilai_akhir, jawaban_siswa.status,
                      jawaban_siswa.status_penilaian, jawaban_siswa.waktu_selesai,
                      ulangan.judul, ulangan.tipe, ulangan.kkm, ulangan.id as ulangan_id,
                      mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel')
            ->join('ulangan',         'ulangan.id = jawaban_siswa.ulangan_id')
            ->join('mata_pelajaran',  'mata_pelajaran.id = ulangan.mapel_id')
            ->where('jawaban_siswa.siswa_id', $siswa['id'])
            ->where('jawaban_siswa.status', 'Selesai')
            ->orderBy('mata_pelajaran.nama_mapel', 'ASC')
            ->orderBy('jawaban_siswa.waktu_selesai', 'DESC')
            ->get()->getResultArray();

        // Group per mapel
        $perMapel = [];
        foreach ($nilaiList as $n) {
            $mapel = $n['kode_mapel'];
            if (!isset($perMapel[$mapel])) {
                $perMapel[$mapel] = [
                    'nama_mapel'  => $n['nama_mapel'],
                    'kode_mapel'  => $n['kode_mapel'],
                    'ujian'       => [],
                    'rata_rata'   => 0,
                    'total'       => 0,
                ];
            }
            $perMapel[$mapel]['ujian'][] = $n;
        }

        // Hitung rata-rata per mapel
        foreach ($perMapel as $k => $v) {
            $sum = array_sum(array_column($v['ujian'], 'nilai_akhir'));
            $perMapel[$k]['rata_rata'] = count($v['ujian']) > 0
                ? round($sum / count($v['ujian']), 1) : 0;
            $perMapel[$k]['total'] = count($v['ujian']);
        }

        // Statistik global
        $allNilai   = array_column($nilaiList, 'nilai_akhir');
        $rataGlobal = count($allNilai) > 0 ? round(array_sum($allNilai) / count($allNilai), 1) : 0;

        return view('buku_nilai/siswa', [
            'title'      => 'Buku Nilai Saya',
            'siswa'      => $siswa,
            'perMapel'   => $perMapel,
            'totalUjian' => count($nilaiList),
            'rataGlobal' => $rataGlobal,
        ]);
    }

    // ── Guru: Rekap nilai seluruh siswa per kelas ────────────────
    private function rekapKelas()
    {
        $db = \Config\Database::connect();

        // Ambil daftar siswa per kelas beserta nilai rata-rata semua ujian
        $kelasId = $this->request->getGet('kelas_id');

        $kelasList = $db->table('kelas')->get()->getResultArray();
        $rekapData = [];

        if ($kelasId) {
            $rekapData = $db->table('anggota_kelas')
                ->select('anggota_kelas.siswa_id, users.nama_lengkap, siswas.nis,
                          COUNT(jawaban_siswa.id) as total_ujian,
                          AVG(jawaban_siswa.nilai_akhir) as rata_rata,
                          MIN(jawaban_siswa.nilai_akhir) as nilai_min,
                          MAX(jawaban_siswa.nilai_akhir) as nilai_max')
                ->join('siswas',       'siswas.id = anggota_kelas.siswa_id')
                ->join('users',        'users.id = siswas.user_id')
                ->join('jawaban_siswa','jawaban_siswa.siswa_id = anggota_kelas.siswa_id AND jawaban_siswa.status = "Selesai"', 'left')
                ->where('anggota_kelas.kelas_id', $kelasId)
                ->groupBy('anggota_kelas.siswa_id')
                ->orderBy('users.nama_lengkap', 'ASC')
                ->get()->getResultArray();
        }

        return view('buku_nilai/guru', [
            'title'      => 'Rekap Nilai Kelas',
            'kelasList'  => $kelasList,
            'kelasId'    => $kelasId,
            'rekapData'  => $rekapData,
        ]);
    }
}
