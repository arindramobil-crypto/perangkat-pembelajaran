<?php
namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table      = 'notifikasi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'tipe', 'judul', 'pesan', 'url', 'is_read', 'ref_id'
    ];
    protected $useTimestamps  = false; // created_at dihandle kolom DEFAULT
    protected $dateFormat     = 'datetime';

    /**
     * Kirim notifikasi ke satu user
     */
    public function kirim(int $userId, string $tipe, string $judul, string $pesan, ?string $url = null, ?int $refId = null): bool
    {
        // Hindari duplikat dalam 1 menit (cegah spam notif)
        $exist = $this->where('user_id', $userId)
            ->where('tipe', $tipe)
            ->where('ref_id', $refId)
            ->where('created_at >', date('Y-m-d H:i:s', strtotime('-1 minute')))
            ->countAllResults();
        if ($exist) return false;

        return (bool) $this->insert([
            'user_id' => $userId,
            'tipe'    => $tipe,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'url'     => $url,
            'is_read' => 0,
            'ref_id'  => $refId,
        ]);
    }

    /**
     * Kirim notifikasi ke banyak user sekaligus (bulk)
     */
    public function kirimBulk(array $userIds, string $tipe, string $judul, string $pesan, ?string $url = null, ?int $refId = null): int
    {
        $count = 0;
        foreach (array_unique($userIds) as $uid) {
            if ($this->kirim($uid, $tipe, $judul, $pesan, $url, $refId)) $count++;
        }
        return $count;
    }

    /**
     * Ambil notifikasi milik user (limit 20 terbaru)
     */
    public function getByUser(int $userId, int $limit = 20): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Hitung notif belum dibaca
     */
    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    /**
     * Tandai semua notif user sebagai sudah dibaca
     */
    public function bacaSemua(int $userId): bool
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->set(['is_read' => 1])->update();
    }

    /**
     * Tandai 1 notif sebagai sudah dibaca
     */
    public function baca(int $id, int $userId): bool
    {
        return $this->where('id', $id)->where('user_id', $userId)->set(['is_read' => 1])->update();
    }

    /**
     * Hapus notif lama > 30 hari
     */
    public function hapusLama(): int
    {
        $this->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')))->delete();
        return $this->db->affectedRows();
    }
}
