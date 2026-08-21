<?php
namespace App\Models;
use CodeIgniter\Model;

class AnggotaKelasModel extends Model
{
    protected $table = 'anggota_kelas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['siswa_id', 'kelas_id', 'tahun_pelajaran_id'];
    
    public function getAnggotaByKelasTahun($kelas_id, $tahun_id)
    {
        return $this->select('anggota_kelas.*, siswas.nis, users.nama_lengkap, users.username')
                    ->join('siswas', 'siswas.id = anggota_kelas.siswa_id')
                    ->join('users', 'users.id = siswas.user_id')
                    ->where('anggota_kelas.kelas_id', $kelas_id)
                    ->where('anggota_kelas.tahun_pelajaran_id', $tahun_id)
                    ->findAll();
    }
}
