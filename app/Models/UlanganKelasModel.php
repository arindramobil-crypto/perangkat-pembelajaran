<?php
namespace App\Models;
use CodeIgniter\Model;

class UlanganKelasModel extends Model
{
    protected $table = 'ulangan_kelas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ulangan_id', 'kelas_id', 'tahun_pelajaran_id'];
}
