<?php
namespace App\Models;
use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_mapel', 'nama_mapel', 'kelompok'];
}
