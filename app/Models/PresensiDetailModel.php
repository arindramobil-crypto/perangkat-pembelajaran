<?php
namespace App\Models;
use CodeIgniter\Model;

class PresensiDetailModel extends Model
{
    protected $table = 'presensi_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = ['presensi_id', 'siswa_id', 'status', 'catatan'];
}
