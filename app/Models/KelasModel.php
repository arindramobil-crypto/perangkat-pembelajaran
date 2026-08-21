<?php
namespace App\Models;
use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_kelas', 'jurusan', 'wali_kelas_id'];
    
    public function getKelasWithWali()
    {
        return $this->select('kelas.*, gurus.nip, users.nama_lengkap as wali_kelas')
                    ->join('gurus', 'gurus.id = kelas.wali_kelas_id', 'left')
                    ->join('users', 'users.id = gurus.user_id', 'left')
                    ->findAll();
    }
}
