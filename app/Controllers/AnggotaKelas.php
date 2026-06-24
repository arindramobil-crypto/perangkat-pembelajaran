<?php
namespace App\Controllers;

use App\Models\AnggotaKelasModel;
use App\Models\KelasModel;
use App\Models\TahunPelajaranModel;
use App\Models\SiswaModel;

class AnggotaKelas extends BaseController
{
    public function index()
    {
        $kelasModel = new KelasModel();
        $tahunModel = new TahunPelajaranModel();
        $anggotaModel = new AnggotaKelasModel();
        
        $kelas_id = $this->request->getGet('kelas_id');
        $tahun_id = $this->request->getGet('tahun_id');
        
        $data = [
            'title' => 'Anggota Kelas',
            'kelasList' => $kelasModel->findAll(),
            'tahunList' => $tahunModel->findAll(),
            'selected_kelas' => $kelas_id,
            'selected_tahun' => $tahun_id,
            'anggota' => [],
            'siswaBebas' => [] // Siswa yang belum masuk kelas di tahun ini
        ];
        
        if ($kelas_id && $tahun_id) {
            $data['anggota'] = $anggotaModel->getAnggotaByKelasTahun($kelas_id, $tahun_id);
            
            // Get all siswas that are NOT in ANY class in this tahun_pelajaran
            $db = \Config\Database::connect();
            // Subquery: cari semua siswa_id yang ADA di tahun_pelajaran ini
            $subquery = $db->table('anggota_kelas')->select('siswa_id')->where('tahun_pelajaran_id', $tahun_id)->getCompiledSelect();
            
            $builder = $db->table('siswas');
            $builder->select('siswas.id, siswas.nis, users.nama_lengkap');
            $builder->join('users', 'users.id = siswas.user_id');
            $builder->where("siswas.id NOT IN ($subquery)", null, false);
            
            $data['siswaBebas'] = $builder->get()->getResultArray();
        }
        
        return view('master/anggota_kelas', $data);
    }
    
    public function save()
    {
        $anggotaModel = new AnggotaKelasModel();
        $kelas_id = $this->request->getVar('kelas_id');
        $tahun_id = $this->request->getVar('tahun_pelajaran_id');
        $siswa_ids = $this->request->getVar('siswa_ids');
        
        if (!empty($siswa_ids) && is_array($siswa_ids)) {
            foreach ($siswa_ids as $sid) {
                // Skip jika sudah ada (duplicate protection)
                $existing = $anggotaModel->where('siswa_id', $sid)->where('tahun_pelajaran_id', $tahun_id)->first();
                if (!$existing) {
                    $anggotaModel->insert([
                        'kelas_id' => $kelas_id,
                        'tahun_pelajaran_id' => $tahun_id,
                        'siswa_id' => $sid
                    ]);
                }
            }
        }
        
        return redirect()->to(site_url('master/anggota-kelas') . '?kelas_id=' . $kelas_id . '&tahun_id=' . $tahun_id)->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }
    
    public function delete($id)
    {
        $anggotaModel = new AnggotaKelasModel();
        $anggota = $anggotaModel->find($id);
        
        if ($anggota) {
            $kelas_id  = $anggota['kelas_id'];
            $tahun_id  = $anggota['tahun_pelajaran_id'];
            $anggotaModel->delete($id);
            return redirect()->to(site_url('master/anggota-kelas') . '?kelas_id=' . $kelas_id . '&tahun_id=' . $tahun_id)->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
        }
        return redirect()->to('/master/anggota-kelas')->with('error', 'Data tidak ditemukan.');
    }
}
