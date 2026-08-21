<?php
namespace App\Models;
use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kelas_id', 'mapel_id', 'guru_id', 'hari', 'jam_mulai', 'jam_selesai', 'tahun_pelajaran_id'];
    
    // Admin View: All
    public function getFullJadwal()
    {
        return $this->select('jadwal.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, users.nama_lengkap as nama_guru, tahun_pelajaran.tahun')
                    ->join('kelas', 'kelas.id = jadwal.kelas_id')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                    ->join('gurus', 'gurus.id = jadwal.guru_id')
                    ->join('users', 'users.id = gurus.user_id')
                    ->join('tahun_pelajaran', 'tahun_pelajaran.id = jadwal.tahun_pelajaran_id')
                    ->findAll();
    }
    
    // Guru View: Specific to Guru
    public function getJadwalByGuru($guru_id)
    {
        return $this->select('jadwal.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, tahun_pelajaran.tahun')
                    ->join('kelas', 'kelas.id = jadwal.kelas_id')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                    ->join('tahun_pelajaran', 'tahun_pelajaran.id = jadwal.tahun_pelajaran_id')
                    ->where('jadwal.guru_id', $guru_id)
                    ->orderBy('FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"), jam_mulai')
                    ->findAll();
    }
    
    // Siswa View: Specific to Siswa's class
    public function getJadwalByKelas($kelas_id)
    {
        return $this->select('jadwal.*, mata_pelajaran.nama_mapel, users.nama_lengkap as nama_guru')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                    ->join('gurus', 'gurus.id = jadwal.guru_id')
                    ->join('users', 'users.id = gurus.user_id')
                    ->where('jadwal.kelas_id', $kelas_id)
                    ->orderBy('FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"), jam_mulai')
                    ->findAll();
    }
}
