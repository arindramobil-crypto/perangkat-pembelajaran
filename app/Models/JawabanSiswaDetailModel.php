<?php
namespace App\Models;
use CodeIgniter\Model;

class JawabanSiswaDetailModel extends Model
{
    protected $table = 'jawaban_siswa_detail';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jawaban_siswa_id', 'soal_id', 'jawaban', 'is_benar', 'skor'];
}
