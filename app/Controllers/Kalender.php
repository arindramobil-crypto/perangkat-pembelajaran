<?php
namespace App\Controllers;

use App\Models\KalenderModel;

/**
 * Controller Kalender Akademik
 * 
 * Routes:
 * GET  /kalender               => Halaman kalender (semua role)
 * GET  /kalender/events        => API Endpoint untuk FullCalendar ambil data
 * POST /kalender/save          => Admin simpan/update acara
 * POST /kalender/delete/(:num) => Admin hapus acara
 */
class Kalender extends BaseController
{
    private KalenderModel $kalenderModel;

    public function __construct()
    {
        $this->kalenderModel = new KalenderModel();
    }

    public function index()
    {
        return view('kalender/index', [
            'title' => 'Kalender Akademik',
            'role'  => session()->get('role')
        ]);
    }

    // ── API untuk FullCalendar ─────────────────────────────────────
    public function events()
    {
        // Parameter ?start=YYYY-MM-DD & end=YYYY-MM-DD dikirim oleh FullCalendar
        $start = $this->request->getGet('start');
        $end   = $this->request->getGet('end');

        if (!$start || !$end) {
            $events = $this->kalenderModel->findAll();
        } else {
            // Fullcalendar butuh rentang start dan end
            $events = $this->kalenderModel->where('tanggal_mulai >=', $start)
                                          ->where('tanggal_mulai <=', $end)
                                          ->findAll();
        }

        // Format data sesuai kebutuhan FullCalendar
        $formatted = [];
        foreach ($events as $e) {
            // FullCalendar exclusive end date logic (tambah 1 hari jika acara > 1 hari)
            $end_date = $e['tanggal_selesai'];
            if ($e['tanggal_mulai'] !== $e['tanggal_selesai']) {
                $end_date = date('Y-m-d', strtotime($e['tanggal_selesai'] . ' +1 day'));
            }

            $formatted[] = [
                'id'          => $e['id'],
                'title'       => $e['judul'],
                'start'       => $e['tanggal_mulai'],
                'end'         => $end_date,
                'color'       => $e['warna'],
                'extendedProps' => [
                    'tipe'      => $e['tipe'],
                    'deskripsi' => $e['deskripsi'],
                    'tanggal_selesai_asli' => $e['tanggal_selesai']
                ]
            ];
        }

        return $this->response->setContentType('application/json')->setBody(json_encode($formatted));
    }

    // ── Admin Only: Simpan/Edit Acara ──────────────────────────────
    public function save()
    {
        if (session()->get('role') !== 'Admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $id = $this->request->getPost('id');
        $data = [
            'judul'           => $this->request->getPost('judul'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: $this->request->getPost('tanggal_mulai'),
            'tipe'            => $this->request->getPost('tipe'),
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'warna'           => $this->request->getPost('warna')
        ];

        // Validasi input
        if (empty($data['judul']) || empty($data['tanggal_mulai'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Judul dan Tanggal Mulai wajib diisi!']);
        }

        if ($id) {
            $this->kalenderModel->update($id, $data);
            $msg = 'Acara berhasil diperbarui!';
        } else {
            $this->kalenderModel->insert($data);
            $msg = 'Acara baru berhasil ditambahkan!';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    // ── Admin Only: Hapus Acara ────────────────────────────────────
    public function delete($id)
    {
        if (session()->get('role') !== 'Admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $this->kalenderModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Acara berhasil dihapus!']);
    }
}
