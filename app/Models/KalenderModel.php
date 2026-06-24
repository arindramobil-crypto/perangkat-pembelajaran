<?php
namespace App\Models;

use CodeIgniter\Model;

class KalenderModel extends Model
{
    protected $table      = 'kalender_akademik';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'judul', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'tipe', 
        'deskripsi', 
        'warna'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil agenda dalam rentang bulan tertentu
     * Format param: YYYY-MM-DD
     */
    public function getEventsInRange(string $start, string $end): array
    {
        return $this->where('tanggal_mulai >=', $start)
                    ->where('tanggal_selesai <=', $end)
                    ->orderBy('tanggal_mulai', 'ASC')
                    ->findAll();
    }
}
