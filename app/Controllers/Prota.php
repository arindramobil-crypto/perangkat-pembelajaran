<?php
namespace App\Controllers;

use App\Models\ProtaModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

class Prota extends BaseController
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

        $protaModel = new ProtaModel();
        $data = [
            'title' => 'Program Tahunan & Semester (Prota/Promes)',
            'prota' => $protaModel->getProtaByGuru($guru['id'])
        ];

        return view('prota/index', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');

        $kelasModel = new KelasModel();
        $mapelModel = new MataPelajaranModel();
        
        $db = \Config\Database::connect();
        $kalender = $db->table('kalender_akademik')
                       ->where('YEAR(tanggal_mulai) >=', date('Y') - 1)
                       ->orderBy('tanggal_mulai', 'ASC')
                       ->get()->getResultArray();
        
        $data = [
            'title' => 'Tambah Data Prota/Promes',
            'kelas' => $kelasModel->findAll(),
            'mapel' => $mapelModel->findAll(),
            'kalender' => $kalender
        ];

        return view('prota/form', $data);
    }

    public function save()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        $guru = $this->getGuru();

        $protaModel = new ProtaModel();
        $id = $this->request->getVar('id');

        $alokasiArr = $this->request->getVar('alokasi'); // array format: alokasi[bulan][minggu]
        $alokasiJson = $alokasiArr ? json_encode($alokasiArr) : null;

        $data = [
            'guru_id'          => $guru['id'],
            'mapel_id'         => $this->request->getVar('mapel_id'),
            'kelas_id'         => $this->request->getVar('kelas_id'),
            'tipe'             => $this->request->getVar('tipe'),
            'materi_pokok'     => $this->request->getVar('materi_pokok'),
            'alokasi_waktu'    => $this->request->getVar('alokasi_waktu'),
            'alokasi_mingguan' => $alokasiJson,
            'keterangan'       => $this->request->getVar('keterangan'),
        ];

        if ($id) {
            $protaModel->update($id, $data);
            $msg = 'Data berhasil diperbarui!';
        } else {
            $protaModel->insert($data);
            $msg = 'Data berhasil ditambahkan!';
        }

        return redirect()->to('/prota')->with('success', $msg);
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        
        $guru = $this->getGuru();
        $protaModel = new ProtaModel();
        $prota = $protaModel->find($id);

        if (!$prota || $prota['guru_id'] != $guru['id']) {
            return redirect()->to('/prota')->with('error', 'Data tidak ditemukan.');
        }

        $kelasModel = new KelasModel();
        $mapelModel = new MataPelajaranModel();
        
        $db = \Config\Database::connect();
        $kalender = $db->table('kalender_akademik')
                       ->where('YEAR(tanggal_mulai) >=', date('Y') - 1)
                       ->orderBy('tanggal_mulai', 'ASC')
                       ->get()->getResultArray();
        
        $data = [
            'title' => 'Edit Data Prota/Promes',
            'prota' => $prota,
            'kelas' => $kelasModel->findAll(),
            'mapel' => $mapelModel->findAll(),
            'kalender' => $kalender
        ];

        return view('prota/form', $data);
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        
        $guru = $this->getGuru();
        $protaModel = new ProtaModel();
        $prota = $protaModel->find($id);

        if (!$prota || $prota['guru_id'] != $guru['id']) {
            return redirect()->to('/prota')->with('error', 'Data tidak ditemukan.');
        }

        $protaModel->delete($id);
        return redirect()->to('/prota')->with('success', 'Data berhasil dihapus.');
    }
}
