<?php
namespace App\Controllers;

use App\Models\RppModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

class Rpp extends BaseController
{
    private function getGuru()
    {
        $guruModel = new GuruModel();
        return $guruModel->where('user_id', session()->get('id'))->first();
    }

    public function index()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');

        $guru = $this->getGuru();
        if (!$guru) return redirect()->to('/dashboard')->with('error', 'Data guru tidak ditemukan.');

        $rppModel = new RppModel();
        $data = [
            'title' => 'Bank RPP / Modul Ajar Digital',
            'rpp'   => $rppModel->getRppByGuru($guru['id'])
        ];

        return view('rpp/index', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');

        $kelasModel = new KelasModel();
        $mapelModel = new MataPelajaranModel();
        
        $data = [
            'title' => 'Upload / Buat RPP Baru',
            'kelas' => $kelasModel->findAll(),
            'mapel' => $mapelModel->findAll()
        ];

        return view('rpp/form', $data);
    }

    public function save()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        $guru = $this->getGuru();

        $rppModel = new RppModel();
        $id = $this->request->getVar('id');

        $data = [
            'guru_id'  => $guru['id'],
            'mapel_id' => $this->request->getVar('mapel_id'),
            'kelas_id' => $this->request->getVar('kelas_id'),
            'judul'    => $this->request->getVar('judul'),
            'konten'   => null, // Not used anymore
        ];

        // Handle File Upload
        $file = $this->request->getFile('file_lampiran');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            // Pindahkan file ke writable/uploads/rpp
            $file->move(FCPATH . 'uploads/rpp', $newName);
            
            // Hapus file lama jika ada
            if ($id) {
                $oldRpp = $rppModel->find($id);
                if ($oldRpp['file_path'] && file_exists(FCPATH . 'uploads/rpp/' . $oldRpp['file_path'])) {
                    unlink(FCPATH . 'uploads/rpp/' . $oldRpp['file_path']);
                }
            }
            $data['file_path'] = $newName;
        } else if (!$id) {
            return redirect()->back()->with('error', 'File RPP wajib diunggah.');
        }

        if ($id) {
            $rppModel->update($id, $data);
            $msg = 'RPP berhasil diperbarui!';
        } else {
            $rppModel->insert($data);
            $msg = 'RPP berhasil ditambahkan!';
        }

        return redirect()->to('/rpp')->with('success', $msg);
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        
        $guru = $this->getGuru();
        $rppModel = new RppModel();
        $rpp = $rppModel->find($id);

        if (!$rpp || $rpp['guru_id'] != $guru['id']) {
            return redirect()->to('/rpp')->with('error', 'RPP tidak ditemukan.');
        }

        $kelasModel = new KelasModel();
        $mapelModel = new MataPelajaranModel();
        
        $data = [
            'title' => 'Edit RPP',
            'rpp'   => $rpp,
            'kelas' => $kelasModel->findAll(),
            'mapel' => $mapelModel->findAll()
        ];

        return view('rpp/form', $data);
    }

    public function view($id)
    {
        if (session()->get('role') !== 'Guru' && session()->get('role') !== 'Admin') return redirect()->to('/dashboard');

        $rppModel = new RppModel();
        $rpp = $rppModel->select('rpp_digital.*, kelas.nama_kelas, mata_pelajaran.nama_mapel, users.nama_lengkap as nama_guru')
                        ->join('kelas', 'kelas.id = rpp_digital.kelas_id')
                        ->join('mata_pelajaran', 'mata_pelajaran.id = rpp_digital.mapel_id')
                        ->join('gurus', 'gurus.id = rpp_digital.guru_id')
                        ->join('users', 'users.id = gurus.user_id')
                        ->find($id);

        if (!$rpp) return redirect()->to('/dashboard')->with('error', 'RPP tidak ditemukan.');

        $data = [
            'title' => 'Detail RPP',
            'rpp'   => $rpp
        ];

        return view('rpp/view', $data);
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        
        $guru = $this->getGuru();
        $rppModel = new RppModel();
        $rpp = $rppModel->find($id);

        if (!$rpp || $rpp['guru_id'] != $guru['id']) {
            return redirect()->to('/rpp')->with('error', 'RPP tidak ditemukan.');
        }

        // Hapus file fisik jika ada
        if ($rpp['file_path'] && file_exists(FCPATH . 'uploads/rpp/' . $rpp['file_path'])) {
            unlink(FCPATH . 'uploads/rpp/' . $rpp['file_path']);
        }

        $rppModel->delete($id);
        return redirect()->to('/rpp')->with('success', 'RPP berhasil dihapus.');
    }
}
