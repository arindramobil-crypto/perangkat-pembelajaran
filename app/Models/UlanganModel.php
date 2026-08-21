<?php
namespace App\Models;
use CodeIgniter\Model;

class UlanganModel extends Model
{
    protected $table = 'ulangan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['guru_id', 'mapel_id', 'judul', 'deskripsi', 'tipe', 'waktu_mulai', 'waktu_selesai', 'durasi', 'kkm'];
}
