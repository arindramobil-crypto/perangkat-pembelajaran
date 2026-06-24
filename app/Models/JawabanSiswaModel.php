<?php
namespace App\Models;
use CodeIgniter\Model;

class JawabanSiswaModel extends Model
{
    protected $table = 'jawaban_siswa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ulangan_id', 'siswa_id', 'nilai_akhir', 'status', 'status_penilaian', 'waktu_mulai', 'waktu_selesai'];
}
