<?php
namespace App\Models;

use CodeIgniter\Model;

class ProtaModel extends Model
{
    protected $table      = 'prota_promes';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'tipe',
        'materi_pokok',
        'alokasi_waktu',
        'alokasi_mingguan',
        'keterangan'
    ];
    
    protected $useTimestamps = true;
    
    public function getProtaByGuru($guru_id)
    {
        return $this->select('prota_promes.*, kelas.nama_kelas, mata_pelajaran.nama_mapel')
                    ->join('kelas', 'kelas.id = prota_promes.kelas_id')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = prota_promes.mapel_id')
                    ->where('prota_promes.guru_id', $guru_id)
                    ->orderBy('prota_promes.kelas_id', 'ASC')
                    ->orderBy('prota_promes.tipe', 'ASC')
                    ->orderBy('prota_promes.id', 'ASC')
                    ->findAll();
    }
}
