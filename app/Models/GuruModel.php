<?php
namespace App\Models;
use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table = 'gurus';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'nip', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telp'];
    
    public function getGurus()
    {
        return $this->select('gurus.*, users.username, users.nama_lengkap, users.email')
                    ->join('users', 'users.id = gurus.user_id')
                    ->findAll();
    }
}
