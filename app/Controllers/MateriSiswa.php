<?php
namespace App\Controllers;

use App\Models\MateriModel;
use App\Models\SiswaModel;

/**
 * MateriSiswa — Controller khusus untuk halaman materi dari sisi Siswa.
 *
 * Fitur:
 *  - Menampilkan daftar materi sesuai kelas siswa yang sedang login
 *  - Pencarian kata kunci (server-side via query param ?q=)
 *  - Filter tab per Mata Pelajaran
 *  - Filter tipe konten (File / Link)
 *  - Pengurutan (Terbaru / Terlama / A-Z / Z-A)
 *  - Download file yang diamankan (hanya siswa terdaftar di kelas tersebut)
 *
 * Routes (tambahkan di Config/Routes.php):
 *   $routes->group('materi-siswa', ['filter' => 'authGuard'], static function ($routes) {
 *       $routes->get('/',                'MateriSiswa::index');
 *       $routes->get('download/(:num)', 'MateriSiswa::download/$1');
 *   });
 */
class MateriSiswa extends BaseController
{
    private const UPLOAD_PATH = FCPATH . 'uploads/materi/';

    // ── Helper: cari data siswa & kelas yang sedang aktif ───────
    private function getSiswaKelas(): array
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
        if (! $siswa) return [null, null];

        $db = \Config\Database::connect();
        $enrollment = $db->table('anggota_kelas')
            ->select('anggota_kelas.kelas_id, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = anggota_kelas.kelas_id')
            ->where('anggota_kelas.siswa_id', $siswa['id'])
            ->orderBy('anggota_kelas.id', 'DESC')
            ->get()->getRowArray();

        return [$siswa, $enrollment];
    }

    // ════════════════════════════════════════════════════════════
    // INDEX — Daftar Materi Kelas Siswa
    // GET /materi-siswa?q=&mapel=&tipe=&sort=
    // ════════════════════════════════════════════════════════════
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Pastikan role Siswa
        if (session()->get('role') !== 'Siswa') {
            return redirect()->to('/materi');
        }

        [$siswa, $enrollment] = $this->getSiswaKelas();

        if (! $siswa) {
            return redirect()->to('/dashboard')
                ->with('error', 'Data siswa tidak ditemukan.');
        }

        // ── Ambil parameter pencarian & filter dari URL ──────────
        $keyword  = trim($this->request->getGet('q')    ?? '');
        $mapel_id = (int)($this->request->getGet('mapel') ?? 0);
        $tipe     = $this->request->getGet('tipe')  ?? '';
        $sort     = $this->request->getGet('sort')  ?? 'terbaru';

        // Validasi nilai tipe & sort
        $tipe = in_array($tipe, ['file', 'link', '']) ? $tipe : '';
        $sort = in_array($sort, ['terbaru', 'terlama', 'az', 'za']) ? $sort : 'terbaru';

        $materiModel = new MateriModel();

        // Jika siswa belum punya kelas, materi = kosong
        if (! $enrollment) {
            $materis   = [];
            $mapelList = [];
        } else {
            $kelasId = $enrollment['kelas_id'];
            $materis = $materiModel->getMateriByKelasWithSearch(
                $kelasId, $keyword, $mapel_id, $tipe, $sort
            );
            $mapelList = $materiModel->getMapelByKelas($kelasId);
        }

        // ── Statistik ringkas ────────────────────────────────────
        $totalFile = count(array_filter($materis, fn($m) => $m['tipe_konten'] === 'file'));
        $totalLink = count(array_filter($materis, fn($m) => $m['tipe_konten'] === 'link'));

        return view('materi/siswa_index', [
            'title'      => 'Materi Pembelajaran',
            'siswa'      => $siswa,
            'kelas'      => $enrollment,
            'materis'    => $materis,
            'mapelList'  => $mapelList,
            'keyword'    => $keyword,
            'mapel_id'   => $mapel_id,
            'tipe'       => $tipe,
            'sort'       => $sort,
            'totalFile'  => $totalFile,
            'totalLink'  => $totalLink,
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // DOWNLOAD — Unduh file materi (validasi kelas siswa)
    // GET /materi-siswa/download/{materi_id}
    // ════════════════════════════════════════════════════════════
    public function download(int $materi_id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('role') !== 'Siswa') {
            return redirect()->to('/materi');
        }

        [$siswa, $enrollment] = $this->getSiswaKelas();

        if (! $siswa || ! $enrollment) {
            return redirect()->to('/materi-siswa')
                ->with('error', 'Anda tidak terdaftar di kelas manapun.');
        }

        $materiModel = new MateriModel();

        // Ambil materi dan pastikan milik kelas siswa ini (keamanan)
        $materi = $materiModel
            ->select('materi.*, jadwal.kelas_id')
            ->join('jadwal', 'jadwal.id = materi.jadwal_id')
            ->where('materi.id', $materi_id)
            ->first();

        if (! $materi) {
            return redirect()->to('/materi-siswa')
                ->with('error', 'Materi tidak ditemukan.');
        }

        // Pastikan materi ini memang untuk kelas siswa
        if ($materi['kelas_id'] != $enrollment['kelas_id']) {
            return redirect()->to('/materi-siswa')
                ->with('error', 'Anda tidak berhak mengakses materi ini.');
        }

        // Tipe link → redirect ke URL eksternal
        if ($materi['tipe_konten'] === 'link') {
            return redirect()->to($materi['link_eksternal']);
        }

        // Tipe file → kirim file unduhan
        if (empty($materi['file_materi'])) {
            return redirect()->to('/materi-siswa')
                ->with('error', 'File tidak tersedia.');
        }

        $filePath = self::UPLOAD_PATH . $materi['file_materi'];

        if (! file_exists($filePath)) {
            return redirect()->to('/materi-siswa')
                ->with('error', 'File tidak ditemukan di server. Hubungi guru Anda.');
        }

        // Nama file unduhan = nama asli yang diupload guru
        $namaUnduh = $materi['nama_asli_file'] ?: $materi['file_materi'];

        return $this->response
            ->download($filePath, null)
            ->setFileName($namaUnduh);
    }
}
