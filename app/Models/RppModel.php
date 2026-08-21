<?php
namespace App\Models;

use CodeIgniter\Model;

class RppModel extends Model
{
    protected $table      = 'rpp_digital';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'judul',
        'konten',
        'file_path'
    ];
    
    protected $useTimestamps = true;
    
    public function getRppByGuru($guru_id)
    {
        return $this->select('rpp_digital.*, kelas.nama_kelas, mata_pelajaran.nama_mapel')
                    ->join('kelas', 'kelas.id = rpp_digital.kelas_id')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = rpp_digital.mapel_id')
                    ->where('rpp_digital.guru_id', $guru_id)
                    ->orderBy('rpp_digital.created_at', 'DESC')
                    ->findAll();
    }
}
