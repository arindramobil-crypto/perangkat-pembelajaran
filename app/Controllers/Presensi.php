<?php
namespace App\Controllers;

use App\Models\PresensiModel;
use App\Models\PresensiDetailModel;
use App\Models\JadwalModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\AnggotaKelasModel;

class Presensi extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        $data = ['title' => 'Sistem Presensi'];
        
        if ($role == 'Guru') {
            $guruModel = new GuruModel();
            $jadwalModel = new JadwalModel();
            
            $guru = $guruModel->where('user_id', session()->get('id'))->first();
            $data['jadwalList'] = $guru ? $jadwalModel->getJadwalByGuru($guru['id']) : [];
            
            $db = \Config\Database::connect();
            if ($guru) {
                $builder = $db->table('presensi');
                $builder->select('presensi.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, jadwal.hari, jadwal.jam_mulai');
                $builder->join('jadwal', 'jadwal.id = presensi.jadwal_id');
                $builder->join('kelas', 'kelas.id = jadwal.kelas_id');
                $builder->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id');
                $builder->where('jadwal.guru_id', $guru['id']);
                $builder->orderBy('presensi.tanggal', 'DESC');
                $data['riwayat'] = $builder->get()->getResultArray();
            } else {
                $data['riwayat'] = [];
            }
            
            return view('presensi/guru_index', $data);
            
        } elseif ($role == 'Siswa') {
            $siswaModel = new SiswaModel();
            $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
            
            $db = \Config\Database::connect();
            $builder = $db->table('presensi_detail');
            $builder->select('presensi_detail.*, presensi.tanggal, presensi.pertemuan_ke, mata_pelajaran.nama_mapel, users.nama_lengkap as nama_guru');
            $builder->join('presensi', 'presensi.id = presensi_detail.presensi_id');
            $builder->join('jadwal', 'jadwal.id = presensi.jadwal_id');
            $builder->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id');
            $builder->join('gurus', 'gurus.id = jadwal.guru_id');
            $builder->join('users', 'users.id = gurus.user_id');
            $builder->where('presensi_detail.siswa_id', $siswa ? $siswa['id'] : 0);
            $builder->orderBy('presensi.tanggal', 'DESC');
            
            $data['kehadiran'] = $builder->get()->getResultArray();
            return view('presensi/siswa_index', $data);
            
        } else {
            return redirect()->to('/dashboard'); 
        }
    }
    
    public function input($jadwal_id)
    {
        $jadwalModel = new JadwalModel();
        $anggotaModel = new AnggotaKelasModel();
        
        $jadwal = $jadwalModel->select('jadwal.*, kelas.nama_kelas, mata_pelajaran.nama_mapel')
                              ->join('kelas', 'kelas.id = jadwal.kelas_id')
                              ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                              ->where('jadwal.id', $jadwal_id)->first();
                              
        if (!$jadwal) return redirect()->to('/presensi')->with('error', 'Jadwal tidak ditemukan');
        
        $siswaList = $anggotaModel->getAnggotaByKelasTahun($jadwal['kelas_id'], $jadwal['tahun_pelajaran_id']);
        
        $data = [
            'title' => 'Input Presensi',
            'jadwal' => $jadwal,
            'siswaList' => $siswaList,
            'tanggal_hari_ini' => date('Y-m-d')
        ];
        
        return view('presensi/guru_input', $data);
    }
    
    public function save()
    {
        $presensiModel = new PresensiModel();
        $detailModel = new PresensiDetailModel();
        $db = \Config\Database::connect();
        
        $jadwal_id = $this->request->getVar('jadwal_id');
        $tanggal = $this->request->getVar('tanggal');
        
        $existing = $presensiModel->where('jadwal_id', $jadwal_id)->where('tanggal', $tanggal)->first();
        if ($existing) {
            return redirect()->to('/presensi/input/'.$jadwal_id)->with('error', 'Presensi untuk tanggal ini sudah pernah diisi.');
        }
        
        $db->transStart();
        
        $presensi_id = $presensiModel->insert([
            'jadwal_id' => $jadwal_id,
            'tanggal' => $tanggal,
            'pertemuan_ke' => $this->request->getVar('pertemuan_ke'),
            'materi_disampaikan' => $this->request->getVar('materi_disampaikan')
        ], true);
        
        $status_array = $this->request->getVar('status'); 
        $catatan_array = $this->request->getVar('catatan');
        
        if (is_array($status_array)) {
            foreach ($status_array as $siswa_id => $status) {
                $detailModel->insert([
                    'presensi_id' => $presensi_id,
                    'siswa_id' => $siswa_id,
                    'status' => $status,
                    'catatan' => $catatan_array[$siswa_id] ?? ''
                ]);
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus() === false) {
            return redirect()->to('/presensi/input/'.$jadwal_id)->with('error', 'Gagal menyimpan presensi.');
        }
        
        return redirect()->to('/presensi')->with('success', 'Presensi berhasil disimpan.');
    }
}
