<?php
namespace App\Controllers;

use App\Models\TahunPelajaranModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;
use App\Models\GuruModel;

class MasterData extends BaseController
{
    // === TAHUN PELAJARAN ===
    public function tahun_pelajaran()
    {
        $model = new TahunPelajaranModel();
        $data = [
            'title' => 'Data Tahun Pelajaran',
            'tahuns' => $model->findAll()
        ];
        return view('master/tahun_pelajaran', $data);
    }
    
    public function save_tahun_pelajaran()
    {
        $model = new TahunPelajaranModel();
        $id = $this->request->getVar('id');
        $data = [
            'tahun' => $this->request->getVar('tahun'),
            'semester' => $this->request->getVar('semester'),
            'status' => $this->request->getVar('status')
        ];
        if (!empty($id)) $data['id'] = $id;
        
        $model->save($data);
        return redirect()->to('/master/tahun-pelajaran')->with('success', 'Data berhasil disimpan');
    }
    
    public function delete_tahun_pelajaran($id)
    {
        $model = new TahunPelajaranModel();
        $model->delete($id);
        return redirect()->to('/master/tahun-pelajaran')->with('success', 'Data berhasil dihapus');
    }

    // === MATA PELAJARAN ===
    public function mata_pelajaran()
    {
        $model = new MataPelajaranModel();
        $data = [
            'title' => 'Data Mata Pelajaran',
            'mapels' => $model->findAll()
        ];
        return view('master/mata_pelajaran', $data);
    }
    
    public function save_mata_pelajaran()
    {
        $model = new MataPelajaranModel();
        $id = $this->request->getVar('id');
        
        $rule_kode = empty($id) 
            ? 'required|is_unique[mata_pelajaran.kode_mapel]' 
            : "required|is_unique[mata_pelajaran.kode_mapel,id,{$id}]";
            
        $rules = [
            'kode_mapel' => $rule_kode,
            'nama_mapel' => 'required',
            'kelompok' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->to('/master/mata-pelajaran')->with('error', 'Kode Mapel sudah digunakan atau input tidak valid.');
        }
        
        $data = [
            'kode_mapel' => $this->request->getVar('kode_mapel'),
            'nama_mapel' => $this->request->getVar('nama_mapel'),
            'kelompok' => $this->request->getVar('kelompok')
        ];
        if (!empty($id)) $data['id'] = $id;
        
        $model->save($data);
        return redirect()->to('/master/mata-pelajaran')->with('success', 'Data berhasil disimpan');
    }
    
    public function delete_mata_pelajaran($id)
    {
        $model = new MataPelajaranModel();
        $model->delete($id);
        return redirect()->to('/master/mata-pelajaran')->with('success', 'Data berhasil dihapus');
    }
    
    // === KELAS ===
    public function kelas()
    {
        $model = new KelasModel();
        $guruModel = new GuruModel();
        
        $data = [
            'title' => 'Data Kelas',
            'kelas' => $model->getKelasWithWali(),
            'gurus' => $guruModel->getGurus()
        ];
        return view('master/kelas', $data);
    }
    
    public function save_kelas()
    {
        $model = new KelasModel();
        $id = $this->request->getVar('id');
        $wali_kelas_id = $this->request->getVar('wali_kelas_id');
        
        $data = [
            'nama_kelas' => $this->request->getVar('nama_kelas'),
            'jurusan' => $this->request->getVar('jurusan'),
            'wali_kelas_id' => empty($wali_kelas_id) ? null : $wali_kelas_id
        ];
        if (!empty($id)) $data['id'] = $id;
        
        $model->save($data);
        return redirect()->to('/master/kelas')->with('success', 'Data berhasil disimpan');
    }
    
    public function delete_kelas($id)
    {
        $model = new KelasModel();
        $model->delete($id);
        return redirect()->to('/master/kelas')->with('success', 'Data berhasil dihapus');
    }
}
