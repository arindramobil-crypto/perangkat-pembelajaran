<?php
namespace App\Controllers;

use App\Models\NotifikasiModel;

/**
 * Notifikasi Controller
 * Routes:
 *   GET  /notifikasi          → Halaman semua notifikasi
 *   GET  /notifikasi/unread   → JSON jumlah belum dibaca (AJAX polling)
 *   POST /notifikasi/baca-semua → Tandai semua sudah dibaca
 *   GET  /notifikasi/baca/{id}  → Tandai 1 notif & redirect ke url-nya
 *   POST /notifikasi/hapus/{id} → Hapus 1 notifikasi
 */
class Notifikasi extends BaseController
{
    private NotifikasiModel $model;

    public function __construct()
    {
        $this->model = new NotifikasiModel();
    }

    private function userId(): int
    {
        return (int) session()->get('id');
    }

    // ── Halaman semua notifikasi ──────────────────────────────────
    public function index()
    {
        $notifs = $this->model->getByUser($this->userId(), 50);
        return view('notifikasi/index', [
            'title'   => 'Notifikasi',
            'notifs'  => $notifs,
            'unread'  => $this->model->countUnread($this->userId()),
        ]);
    }

    // ── AJAX: hitung unread (polling tiap 30 detik) ───────────────
    public function unread()
    {
        return $this->response
            ->setContentType('application/json')
            ->setBody(json_encode(['count' => $this->model->countUnread($this->userId())]));
    }

    // ── AJAX: ambil 10 notif terbaru (untuk dropdown navbar) ─────
    public function recent()
    {
        $notifs = $this->model->getByUser($this->userId(), 10);
        $html   = '';
        foreach ($notifs as $n) {
            $ago   = $this->timeAgo($n['created_at']);
            $icon  = $this->tipeIcon($n['tipe']);
            $color = $this->tipeColor($n['tipe']);
            $readCls = $n['is_read'] ? '' : 'notif-unread';
            $url   = $n['url'] ? base_url('notifikasi/baca/' . $n['id']) : '#';
            $html .= "<a href='{$url}' class='notif-item {$readCls}'>
                <div class='notif-icon' style='background:{$color}22;color:{$color};'>{$icon}</div>
                <div class='notif-body'>
                    <div class='notif-title'>" . esc($n['judul']) . "</div>
                    <div class='notif-msg'>" . esc(mb_strimwidth($n['pesan'], 0, 70, '…')) . "</div>
                    <div class='notif-time'>{$ago}</div>
                </div>
            </a>";
        }
        if (empty($notifs)) {
            $html = "<div class='notif-empty'><i class='bi bi-bell-slash'></i><br>Belum ada notifikasi</div>";
        }
        return $this->response->setContentType('application/json')
            ->setBody(json_encode([
                'html'   => $html,
                'count'  => $this->model->countUnread($this->userId()),
                'total'  => count($notifs),
            ]));
    }

    // ── Tandai semua sudah dibaca (AJAX / form) ───────────────────
    public function bacaSemua()
    {
        $this->model->bacaSemua($this->userId());
        if ($this->request->isAJAX()) {
            return $this->response->setContentType('application/json')
                ->setBody(json_encode(['ok' => true]));
        }
        return redirect()->to('/notifikasi');
    }

    // ── Baca 1 notif & redirect ───────────────────────────────────
    public function baca(int $id)
    {
        $notif = $this->model->where('id', $id)->where('user_id', $this->userId())->first();
        if ($notif) {
            $this->model->baca($id, $this->userId());
            if ($notif['url']) return redirect()->to($notif['url']);
        }
        return redirect()->to('/notifikasi');
    }

    // ── Hapus notif ───────────────────────────────────────────────
    public function hapus(int $id)
    {
        $this->model->where('id', $id)->where('user_id', $this->userId())->delete();
        if ($this->request->isAJAX()) {
            return $this->response->setContentType('application/json')
                ->setBody(json_encode(['ok' => true]));
        }
        return redirect()->to('/notifikasi');
    }

    // ── Helper: relative time ─────────────────────────────────────
    private function timeAgo(string $datetime): string
    {
        $diff = time() - strtotime($datetime);
        if ($diff < 60)     return 'Baru saja';
        if ($diff < 3600)   return (int)($diff / 60) . ' menit lalu';
        if ($diff < 86400)  return (int)($diff / 3600) . ' jam lalu';
        if ($diff < 604800) return (int)($diff / 86400) . ' hari lalu';
        return date('d M Y', strtotime($datetime));
    }

    private function tipeIcon(string $tipe): string
    {
        return match($tipe) {
            'materi'   => '📚',
            'ujian'    => '📝',
            'nilai'    => '📊',
            'presensi' => '✅',
            'koreksi'  => '✏️',
            default    => '🔔',
        };
    }

    private function tipeColor(string $tipe): string
    {
        return match($tipe) {
            'materi'   => '#38BDF8',
            'ujian'    => '#F59E0B',
            'nilai'    => '#22C55E',
            'presensi' => '#818CF8',
            'koreksi'  => '#EF4444',
            default    => '#94A3B8',
        };
    }
}
