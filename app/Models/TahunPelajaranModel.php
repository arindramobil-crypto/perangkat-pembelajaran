<?php
namespace App\Models;
use CodeIgniter\Model;

class TahunPelajaranModel extends Model
{
    protected $table = 'tahun_pelajaran';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tahun', 'semester', 'status'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
