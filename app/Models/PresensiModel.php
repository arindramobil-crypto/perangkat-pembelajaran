<?php
namespace App\Models;
use CodeIgniter\Model;

class PresensiModel extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jadwal_id', 'tanggal', 'pertemuan_ke', 'materi_disampaikan'];
}
