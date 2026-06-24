<?php
namespace App\Models;

use CodeIgniter\Model;

class JurnalModel extends Model
{
    protected $table      = 'jurnal_mengajar';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'guru_id',
        'jadwal_id',
        'tanggal',
        'jam_ke',
        'materi_pembahasan',
        'catatan_kejadian',
        'siswa_absen'
    ];
    
    protected $useTimestamps = true;
    
    public function getJurnalByGuru($guru_id)
    {
        return $this->select('jurnal_mengajar.*, jadwal.hari, kelas.nama_kelas, mata_pelajaran.nama_mapel')
                    ->join('jadwal', 'jadwal.id = jurnal_mengajar.jadwal_id')
                    ->join('kelas', 'kelas.id = jadwal.kelas_id')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                    ->where('jurnal_mengajar.guru_id', $guru_id)
                    ->orderBy('jurnal_mengajar.tanggal', 'DESC')
                    ->orderBy('jurnal_mengajar.id', 'DESC')
                    ->findAll();
    }
}
