<?php
namespace App\Models;
use CodeIgniter\Model;

class SoalModel extends Model
{
    protected $table = 'soal';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ulangan_id', 'pertanyaan', 'tipe_soal', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'opsi_tambahan', 'kunci_jawaban', 'bobot'];
}
