<?php
namespace App\Models;
use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table = 'siswas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'nis', 'nisn', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telp'];
    
    public function getSiswas()
    {
        return $this->select('siswas.*, users.username, users.nama_lengkap, users.email')
                    ->join('users', 'users.id = siswas.user_id')
                    ->findAll();
    }
}
