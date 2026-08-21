<?php
namespace App\Controllers;

use App\Models\JurnalModel;
use App\Models\GuruModel;
use App\Models\JadwalModel;
use App\Models\AnggotaKelasModel;

class Jurnal extends BaseController
{
    private function getGuru()
    {
        $guruModel = new GuruModel();
        return $guruModel->where('user_id', session()->get('id'))->first();
    }

    public function index()
    {
        $role = session()->get('role');
        if ($role !== 'Guru') return redirect()->to('/dashboard');

        $guru = $this->getGuru();
        if (!$guru) return redirect()->to('/dashboard')->with('error', 'Data guru tidak ditemukan.');

        $jurnalModel = new JurnalModel();
        $data = [
            'title'  => 'Jurnal Mengajar Harian',
            'jurnal' => $jurnalModel->getJurnalByGuru($guru['id'])
        ];

        return view('jurnal/index_guru', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');

        $guru = $this->getGuru();
        $jadwalModel = new JadwalModel();
        
        $data = [
            'title'      => 'Isi Jurnal Mengajar',
            'jadwalList' => $jadwalModel->getJadwalByGuru($guru['id'])
        ];

        return view('jurnal/form', $data);
    }

    public function save()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        $guru = $this->getGuru();

        $jurnalModel = new JurnalModel();
        $id = $this->request->getVar('id');

        $siswaAbsen = $this->request->getVar('siswa_absen');
        if (is_array($siswaAbsen)) {
            $siswaAbsen = implode(', ', $siswaAbsen);
        }

        $data = [
            'guru_id'           => $guru['id'],
            'jadwal_id'         => $this->request->getVar('jadwal_id'),
            'tanggal'           => $this->request->getVar('tanggal'),
            'jam_ke'            => $this->request->getVar('jam_ke'),
            'materi_pembahasan' => $this->request->getVar('materi_pembahasan'),
            'catatan_kejadian'  => $this->request->getVar('catatan_kejadian'),
            'siswa_absen'       => $siswaAbsen,
        ];

        if ($id) {
            $jurnalModel->update($id, $data);
            $msg = 'Jurnal berhasil diperbarui!';
        } else {
            $jurnalModel->insert($data);
            $msg = 'Jurnal berhasil disimpan!';
        }

        return redirect()->to('/jurnal')->with('success', $msg);
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        
        $guru = $this->getGuru();
        $jurnalModel = new JurnalModel();
        $jurnal = $jurnalModel->find($id);

        if (!$jurnal || $jurnal['guru_id'] != $guru['id']) {
            return redirect()->to('/jurnal')->with('error', 'Data tidak ditemukan.');
        }

        $jadwalModel = new JadwalModel();
        
        $data = [
            'title'      => 'Edit Jurnal Mengajar',
            'jurnal'     => $jurnal,
            'jadwalList' => $jadwalModel->getJadwalByGuru($guru['id'])
        ];

        return view('jurnal/form', $data);
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');
        
        $guru = $this->getGuru();
        $jurnalModel = new JurnalModel();
        $jurnal = $jurnalModel->find($id);

        if (!$jurnal || $jurnal['guru_id'] != $guru['id']) {
            return redirect()->to('/jurnal')->with('error', 'Data tidak ditemukan.');
        }

        $jurnalModel->delete($id);
        return redirect()->to('/jurnal')->with('success', 'Jurnal berhasil dihapus.');
    }

    /**
     * Mengambil daftar siswa berdasarkan jadwal_id via AJAX
     */
    public function getSiswaByJadwal($jadwal_id)
    {
        if (session()->get('role') !== 'Guru') return $this->response->setJSON([]);

        $jadwalModel = new JadwalModel();
        $jadwal = $jadwalModel->find($jadwal_id);

        if (!$jadwal) {
            return $this->response->setJSON([]);
        }

        $anggotaModel = new AnggotaKelasModel();
        $siswaList = $anggotaModel->getAnggotaByKelasTahun($jadwal['kelas_id'], $jadwal['tahun_pelajaran_id']);

        return $this->response->setJSON($siswaList);
    }
}
