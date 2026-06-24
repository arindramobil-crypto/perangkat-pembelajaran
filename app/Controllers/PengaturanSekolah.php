<?php
namespace App\Controllers;

use App\Models\PengaturanSekolahModel;

/**
 * PengaturanSekolah — Controller khusus Admin untuk mengelola profil sekolah.
 *
 * Routes (adminGuard):
 *   GET  /pengaturan/sekolah          → Tampilkan form pengaturan
 *   POST /pengaturan/sekolah/update   → Proses simpan perubahan
 */
class PengaturanSekolah extends BaseController
{
    private const LOGO_PATH      = FCPATH . 'uploads/logo/';
    private const LOGO_BASE_URL  = 'uploads/logo/';
    private const ALLOWED_MIME   = ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml'];
    private const ALLOWED_EXT    = ['jpg','jpeg','png','gif','webp','svg'];
    private const MAX_SIZE_KB    = 2048; // 2 MB

    public function index(): string
    {
        if (session()->get('role') !== 'Admin') {
            return redirect()->to('/dashboard');
        }

        $model = new PengaturanSekolahModel();
        return view('pengaturan/sekolah', [
            'title'      => 'Pengaturan Sekolah',
            'pengaturan' => $model->getPengaturan(),
        ]);
    }

    public function update(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('role') !== 'Admin') {
            return redirect()->to('/dashboard');
        }

        $model = new PengaturanSekolahModel();

        // ── Ambil semua input teks ──────────────────────────────
        $data = [
            'nama_sekolah'        => $this->request->getPost('nama_sekolah'),
            'singkatan'           => $this->request->getPost('singkatan'),
            'nss'                 => $this->request->getPost('nss'),
            'npsn'                => $this->request->getPost('npsn'),
            'akreditasi'          => $this->request->getPost('akreditasi'),
            'tahun_berdiri'       => $this->request->getPost('tahun_berdiri') ?: null,
            'alamat'              => $this->request->getPost('alamat'),
            'kecamatan'           => $this->request->getPost('kecamatan'),
            'kota'                => $this->request->getPost('kota'),
            'provinsi'            => $this->request->getPost('provinsi'),
            'kode_pos'            => $this->request->getPost('kode_pos'),
            'no_telp'             => $this->request->getPost('no_telp'),
            'email'               => $this->request->getPost('email'),
            'website'             => $this->request->getPost('website'),
            'nama_kepala_sekolah' => $this->request->getPost('nama_kepala_sekolah'),
            'nip_kepala_sekolah'  => $this->request->getPost('nip_kepala_sekolah'),
            'visi'                => $this->request->getPost('visi'),
            'misi'                => $this->request->getPost('misi'),
        ];

        // ── Validasi field wajib ────────────────────────────────
        if (!$this->validate([
            'nama_sekolah' => 'required|min_length[3]',
            'email'        => 'permit_empty|valid_email',
        ])) {
            return redirect()->to('/pengaturan/sekolah')
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        // ── Upload Logo (opsional) ──────────────────────────────
        $logo = $this->request->getFile('logo');

        if ($logo && $logo->isValid() && ! $logo->hasMoved()) {
            // Validasi ukuran
            if ($logo->getSizeByUnit('kb') > self::MAX_SIZE_KB) {
                return redirect()->to('/pengaturan/sekolah')
                    ->with('error', 'Ukuran logo maksimal 2 MB.');
            }

            // Validasi tipe MIME & ekstensi
            if (! in_array($logo->getMimeType(), self::ALLOWED_MIME)
             || ! in_array(strtolower($logo->getExtension()), self::ALLOWED_EXT)) {
                return redirect()->to('/pengaturan/sekolah')
                    ->with('error', 'Format logo harus JPG, PNG, GIF, WebP, atau SVG.');
            }

            // Pastikan folder ada
            if (! is_dir(self::LOGO_PATH)) {
                mkdir(self::LOGO_PATH, 0755, true);
            }

            // Hapus logo lama jika ada
            $existing = $model->getPengaturan();
            if (! empty($existing['logo'])) {
                $oldFile = self::LOGO_PATH . $existing['logo'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Simpan dengan nama acak + timestamp
            $namaFile = 'logo_' . time() . '_' . $logo->getRandomName();
            $logo->move(self::LOGO_PATH, $namaFile);
            $data['logo'] = $namaFile;
        }

        // ── Simpan ke database ──────────────────────────────────
        if ($model->updatePengaturan($data)) {
            // Perbarui nama sekolah di session agar navbar langsung update
            session()->set('nama_sekolah', $data['nama_sekolah']);
            return redirect()->to('/pengaturan/sekolah')
                ->with('success', 'Pengaturan sekolah berhasil disimpan!');
        }

        return redirect()->to('/pengaturan/sekolah')
            ->with('error', 'Gagal menyimpan pengaturan. Coba lagi.');
    }

    // ── Hapus Logo ──────────────────────────────────────────────
    public function hapus_logo(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('role') !== 'Admin') {
            return redirect()->to('/dashboard');
        }

        $model    = new PengaturanSekolahModel();
        $existing = $model->getPengaturan();

        if (! empty($existing['logo'])) {
            $filePath = self::LOGO_PATH . $existing['logo'];
            if (file_exists($filePath)) unlink($filePath);
            $model->updatePengaturan(['logo' => null]);
        }

        return redirect()->to('/pengaturan/sekolah')
            ->with('success', 'Logo berhasil dihapus.');
    }
}
