<?php
namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;
use App\Models\GuruModel;
use App\Models\TahunPelajaranModel;
use App\Models\SiswaModel;

class Jadwal extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        $jadwalModel = new JadwalModel();
        
        $data = ['title' => 'Jadwal Pelajaran'];
        
        if ($role == 'Admin') {
            $kelasModel = new KelasModel();
            $mapelModel = new MataPelajaranModel();
            $guruModel = new GuruModel();
            $tahunModel = new TahunPelajaranModel();
            
            $data['jadwals'] = $jadwalModel->getFullJadwal();
            $data['kelasList'] = $kelasModel->findAll();
            $data['mapelList'] = $mapelModel->findAll();
            $data['guruList'] = $guruModel->getGurus();
            $data['tahunList'] = $tahunModel->where('status', 'Aktif')->findAll();
            
            return view('jadwal/index_admin', $data);
            
        } elseif ($role == 'Guru') {
            $guruModel = new GuruModel();
            $guru = $guruModel->where('user_id', session()->get('id'))->first();
            $data['jadwals'] = $guru ? $jadwalModel->getJadwalByGuru($guru['id']) : [];
            return view('jadwal/index_guru', $data);
            
        } elseif ($role == 'Siswa') {
            $siswaModel = new SiswaModel();
            $kelasModel = new KelasModel();
            
            $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
            
            $db = \Config\Database::connect();
            $builder = $db->table('anggota_kelas');
            $builder->select('kelas_id');
            $builder->where('siswa_id', $siswa ? $siswa['id'] : 0);
            $builder->orderBy('id', 'DESC'); 
            $enrollment = $builder->get()->getRowArray();
            
            if ($enrollment) {
                $data['jadwals'] = $jadwalModel->getJadwalByKelas($enrollment['kelas_id']);
                $data['kelas_info'] = $kelasModel->find($enrollment['kelas_id']);
            } else {
                $data['jadwals'] = [];
                $data['kelas_info'] = null;
            }
            
            return view('jadwal/index_siswa', $data);
        }
    }
    
    public function save()
    {
        $jadwalModel = new JadwalModel();
        $id = $this->request->getVar('id');
        
        $data = [
            'tahun_pelajaran_id' => $this->request->getVar('tahun_pelajaran_id'),
            'kelas_id' => $this->request->getVar('kelas_id'),
            'mapel_id' => $this->request->getVar('mapel_id'),
            'guru_id' => $this->request->getVar('guru_id'),
            'hari' => $this->request->getVar('hari'),
            'jam_mulai' => $this->request->getVar('jam_mulai'),
            'jam_selesai' => $this->request->getVar('jam_selesai')
        ];
        if (!empty($id)) $data['id'] = $id;
        
        $jadwalModel->save($data);
        
        return redirect()->to('/jadwal')->with('success', 'Jadwal berhasil disimpan.');
    }
    
    public function delete($id)
    {
        $jadwalModel = new JadwalModel();
        $jadwalModel->delete($id);
        return redirect()->to('/jadwal')->with('success', 'Jadwal berhasil dihapus.');
    }
}
