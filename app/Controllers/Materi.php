<?php
namespace App\Controllers;

use App\Models\MateriModel;
use App\Models\JadwalModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\NotifikasiModel;

/**
 * Controller Materi — CRUD Lengkap
 *
 * Routes yang dibutuhkan (tambahkan di Config/Routes.php):
 *   $routes->group('materi', ['filter' => 'authGuard'], static function ($routes) {
 *       $routes->get('/',                  'Materi::index');
 *       $routes->get('create',             'Materi::create');    // Form tambah
 *       $routes->post('store',             'Materi::store');     // Proses tambah
 *       $routes->get('edit/(:num)',        'Materi::edit/$1');   // Form edit
 *       $routes->post('update/(:num)',     'Materi::update/$1'); // Proses edit
 *       $routes->get('delete/(:num)',      'Materi::delete/$1'); // Hapus
 *       $routes->get('download/(:num)',    'Materi::download/$1');
 *   });
 */
class Materi extends BaseController
{
    // ── Konfigurasi upload ───────────────────────────────────────
    private const UPLOAD_PATH    = FCPATH . 'uploads/materi/';
    private const MAX_SIZE_KB    = 20480; // 20 MB
    private const ALLOWED_TYPES  = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'mp4', 'jpg', 'png'];
    private const ALLOWED_MIME   = 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,video/mp4,image/jpeg,image/png';

    // ── Helpers ──────────────────────────────────────────────────
    private function getGuru(): ?array
    {
        $guruModel = new GuruModel();
        return $guruModel->where('user_id', session()->get('id'))->first();
    }

    private function ensureGuru(): array
    {
        $guru = $this->getGuru();
        if (! $guru) {
            return [];
        }
        return $guru;
    }

    private function notifySiswa(int $jadwalId, string $judulMateri)
    {
        $db = \Config\Database::connect();
        $jadwal = $db->table('jadwal')
            ->select('kelas.nama_kelas, mata_pelajaran.nama_mapel, jadwal.kelas_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('jadwal.id', $jadwalId)
            ->get()->getRowArray();
        
        if ($jadwal) {
            $siswas = $db->table('anggota_kelas')
                ->select('siswas.user_id')
                ->join('siswas', 'siswas.id = anggota_kelas.siswa_id')
                ->where('anggota_kelas.kelas_id', $jadwal['kelas_id'])
                ->get()->getResultArray();
            
            $userIds = array_column($siswas, 'user_id');
            if (!empty($userIds)) {
                $notif = new NotifikasiModel();
                $pesan = "Materi baru '{$judulMateri}' untuk mata pelajaran {$jadwal['nama_mapel']} telah ditambahkan.";
                $notif->kirimBulk($userIds, 'materi', '📚 Materi Baru', $pesan, base_url('materi'));
            }
        }
    }

    // ═══════════════════════════════════════════════════════════
    // READ — Daftar semua materi
    // ═══════════════════════════════════════════════════════════
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $role = session()->get('role');

        if ($role === 'Guru') {
            $guru = $this->getGuru();
            $materiModel = new MateriModel();

            return view('materi/index_guru', [
                'title'   => 'Materi Pembelajaran',
                'materis' => $guru ? $materiModel->getMateriByGuru($guru['id']) : [],
            ]);
        }

        if ($role === 'Siswa') {
            $siswaModel = new SiswaModel();
            $siswa      = $siswaModel->where('user_id', session()->get('id'))->first();
            $db         = \Config\Database::connect();
            $enrollment = $db->table('anggota_kelas')
                             ->where('siswa_id', $siswa['id'] ?? 0)
                             ->orderBy('id', 'DESC')
                             ->get()->getRowArray();

            $materiModel = new MateriModel();
            return view('materi/index_siswa', [
                'title'   => 'Materi Pembelajaran',
                'materis' => $enrollment ? $materiModel->getMateriByKelas($enrollment['kelas_id']) : [],
            ]);
        }

        return redirect()->to('/dashboard');
    }

    // ═══════════════════════════════════════════════════════════
    // CREATE — Tampilkan form tambah materi
    // ═══════════════════════════════════════════════════════════
    public function create(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $guru = $this->getGuru();
        if (! $guru) {
            return redirect()->to('/materi')->with('error', 'Data guru tidak ditemukan.');
        }

        $jadwalModel = new JadwalModel();

        return view('materi/form', [
            'title'      => 'Tambah Materi Baru',
            'mode'       => 'create',
            'materi'     => null,
            'guru_id'    => $guru['id'],
            'jadwalList' => $jadwalModel->getJadwalByGuru($guru['id']),
            'validation' => \Config\Services::validation(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // STORE — Proses simpan materi baru (dengan validasi CI4)
    // ═══════════════════════════════════════════════════════════
    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $guru = $this->getGuru();
        if (! $guru) {
            return redirect()->to('/materi')->with('error', 'Akses ditolak.');
        }

        // ── 1. Validasi input teks ────────────────────────────
        $rules = [
            'judul_materi' => 'required|min_length[3]|max_length[255]',
            'jadwal_id'    => 'required|is_natural_no_zero',
            'deskripsi'    => 'permit_empty|max_length[1000]',
            'tipe_konten'  => 'required|in_list[file,link]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tipe = $this->request->getVar('tipe_konten');

        // ── 2a. Jika tipe = link, validasi URL ───────────────
        if ($tipe === 'link') {
            $link = $this->request->getVar('link_eksternal');
            if (empty($link) || ! filter_var($link, FILTER_VALIDATE_URL)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['link_eksternal' => 'URL eksternal tidak valid.']);
            }

            (new MateriModel())->save([
                'guru_id'       => $guru['id'],
                'jadwal_id'     => $this->request->getVar('jadwal_id'),
                'judul_materi'  => $this->request->getVar('judul_materi'),
                'deskripsi'     => $this->request->getVar('deskripsi'),
                'tipe_konten'   => 'link',
                'link_eksternal'=> $link,
            ]);

            $this->notifySiswa($this->request->getVar('jadwal_id'), $this->request->getVar('judul_materi'));

            return redirect()->to('/materi')->with('success', 'Materi (link) berhasil ditambahkan!');
        }

        // ── 2b. Jika tipe = file, validasi & simpan file ─────
        $file = $this->request->getFile('file_materi');

        if (! $file || ! $file->isValid()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['file_materi' => 'File tidak valid atau tidak ada file yang diunggah.']);
        }

        // Validasi keamanan file
        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, self::ALLOWED_TYPES)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['file_materi' => 'Tipe file tidak diizinkan. Gunakan: ' . implode(', ', self::ALLOWED_TYPES)]);
        }

        if ($file->getSizeByUnit('kb') > self::MAX_SIZE_KB) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['file_materi' => 'Ukuran file melebihi batas 20 MB.']);
        }

        // Pastikan direktori upload ada
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }

        $namaAsli = $file->getClientName();
        $namaFile = $file->getRandomName(); // Nama acak aman (mencegah path traversal)
        $file->move(self::UPLOAD_PATH, $namaFile);

        (new MateriModel())->save([
            'guru_id'       => $guru['id'],
            'jadwal_id'     => $this->request->getVar('jadwal_id'),
            'judul_materi'  => $this->request->getVar('judul_materi'),
            'deskripsi'     => $this->request->getVar('deskripsi'),
            'tipe_konten'   => 'file',
            'file_materi'   => $namaFile,
            'nama_asli_file'=> $namaAsli,
        ]);

        $this->notifySiswa($this->request->getVar('jadwal_id'), $this->request->getVar('judul_materi'));

        return redirect()->to('/materi')->with('success', 'Materi berhasil diunggah!');
    }

    // ═══════════════════════════════════════════════════════════
    // EDIT — Tampilkan form edit materi
    // ═══════════════════════════════════════════════════════════
    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $guru        = $this->getGuru();
        $materiModel = new MateriModel();
        $materi      = $materiModel->find($id);

        // Keamanan: hanya guru pemilik yang bisa edit
        if (! $materi || ! $guru || $materi['guru_id'] !== $guru['id']) {
            return redirect()->to('/materi')->with('error', 'Materi tidak ditemukan atau akses ditolak.');
        }

        $jadwalModel = new JadwalModel();

        return view('materi/form', [
            'title'      => 'Edit Materi',
            'mode'       => 'edit',
            'materi'     => $materi,
            'guru_id'    => $guru['id'],
            'jadwalList' => $jadwalModel->getJadwalByGuru($guru['id']),
            'validation' => \Config\Services::validation(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // UPDATE — Proses simpan perubahan materi
    // ═══════════════════════════════════════════════════════════
    public function update(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $guru        = $this->getGuru();
        $materiModel = new MateriModel();
        $materi      = $materiModel->find($id);

        if (! $materi || ! $guru || $materi['guru_id'] !== $guru['id']) {
            return redirect()->to('/materi')->with('error', 'Akses ditolak.');
        }

        // Validasi input teks
        $rules = [
            'judul_materi' => 'required|min_length[3]|max_length[255]',
            'jadwal_id'    => 'required|is_natural_no_zero',
            'deskripsi'    => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'jadwal_id'    => $this->request->getVar('jadwal_id'),
            'judul_materi' => $this->request->getVar('judul_materi'),
            'deskripsi'    => $this->request->getVar('deskripsi'),
        ];

        // Jika ada file baru diunggah, ganti file lama
        $file = $this->request->getFile('file_materi');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());

            if (! in_array($ext, self::ALLOWED_TYPES)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['file_materi' => 'Tipe file tidak diizinkan.']);
            }
            if ($file->getSizeByUnit('kb') > self::MAX_SIZE_KB) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['file_materi' => 'Ukuran file melebihi batas 20 MB.']);
            }

            // Hapus file lama
            if (! empty($materi['file_materi'])) {
                $oldPath = self::UPLOAD_PATH . $materi['file_materi'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $namaFile = $file->getRandomName();
            $file->move(self::UPLOAD_PATH, $namaFile);

            $updateData['file_materi']    = $namaFile;
            $updateData['nama_asli_file'] = $file->getClientName();
            $updateData['tipe_konten']    = 'file';
        }

        $materiModel->update($id, $updateData);

        return redirect()->to('/materi')->with('success', 'Materi berhasil diperbarui!');
    }

    // ═══════════════════════════════════════════════════════════
    // DELETE — Hapus materi + file fisiknya
    // ═══════════════════════════════════════════════════════════
    public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $guru        = $this->getGuru();
        $materiModel = new MateriModel();
        $materi      = $materiModel->find($id);

        if (! $materi || ! $guru || $materi['guru_id'] !== $guru['id']) {
            return redirect()->to('/materi')->with('error', 'Akses ditolak.');
        }

        // Hapus file fisik dari server
        if (! empty($materi['file_materi'])) {
            $filePath = self::UPLOAD_PATH . $materi['file_materi'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $materiModel->delete($id);

        return redirect()->to('/materi')->with('success', 'Materi berhasil dihapus.');
    }

    // ═══════════════════════════════════════════════════════════
    // DOWNLOAD — Kirim file ke browser
    // ═══════════════════════════════════════════════════════════
    public function download(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $materiModel = new MateriModel();
        $materi      = $materiModel->find($id);

        if (! $materi || empty($materi['file_materi'])) {
            return redirect()->to('/materi')->with('error', 'File tidak ditemukan.');
        }

        $filePath = self::UPLOAD_PATH . $materi['file_materi'];

        if (! file_exists($filePath)) {
            return redirect()->to('/materi')->with('error', 'File fisik tidak ditemukan di server.');
        }

        // Gunakan nama asli saat diunduh
        $namaUnduh = $materi['nama_asli_file'] ?: $materi['file_materi'];

        return $this->response->download($filePath, null)->setFileName($namaUnduh);
    }
}
