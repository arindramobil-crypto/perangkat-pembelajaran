<?php
namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\JurnalModel;
use App\Models\ProtaModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

class Export extends BaseController
{
    private function getGuru()
    {
        $guruModel = new GuruModel();
        return $guruModel->where('user_id', session()->get('id'))->first();
    }

    private function getProfilSekolah()
    {
        $db = \Config\Database::connect();
        $query = $db->table('pengaturan_sekolah')->where('id', 1)->get();
        return $query->getRowArray();
    }

    public function index()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');

        $guru = $this->getGuru();
        if (!$guru) return redirect()->to('/dashboard')->with('error', 'Data guru tidak ditemukan.');

        $kelasModel = new KelasModel();
        $mapelModel = new MataPelajaranModel();

        $data = [
            'title' => 'Pusat Cetak Dokumen',
            'kelas' => $kelasModel->findAll(),
            'mapel' => $mapelModel->findAll()
        ];

        return view('export/index', $data);
    }

    public function jurnal()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');

        $guru = $this->getGuru();
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');

        $jurnalModel = new JurnalModel();
        
        // Base Query for Guru
        $builder = $jurnalModel->select('jurnal_mengajar.*, jadwal.hari, kelas.nama_kelas, mata_pelajaran.nama_mapel')
                               ->join('jadwal', 'jadwal.id = jurnal_mengajar.jadwal_id')
                               ->join('kelas', 'kelas.id = jadwal.kelas_id')
                               ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                               ->where('jurnal_mengajar.guru_id', $guru['id']);

        if ($bulan && $tahun) {
            $builder->where('MONTH(jurnal_mengajar.tanggal)', $bulan)
                    ->where('YEAR(jurnal_mengajar.tanggal)', $tahun);
        }

        $jurnal = $builder->orderBy('jurnal_mengajar.tanggal', 'ASC')->findAll();

        $data = [
            'title'   => 'Cetak Jurnal Mengajar',
            'jurnal'  => $jurnal,
            'guru'    => $guru,
            'sekolah' => $this->getProfilSekolah(),
            'bulan'   => $bulan,
            'tahun'   => $tahun
        ];

        return view('export/print_jurnal', $data);
    }

    public function prota_promes()
    {
        if (session()->get('role') !== 'Guru') return redirect()->to('/dashboard');

        $guru = $this->getGuru();
        $tipe = $this->request->getGet('tipe'); // Prota atau Promes
        $kelas_id = $this->request->getGet('kelas_id');
        $mapel_id = $this->request->getGet('mapel_id');

        if (!$tipe || !$kelas_id || !$mapel_id) {
            return redirect()->back()->with('error', 'Pilih tipe dokumen, kelas, dan mapel terlebih dahulu.');
        }

        $protaModel = new ProtaModel();
        $dataDocs = $protaModel->select('prota_promes.*, kelas.nama_kelas, mata_pelajaran.nama_mapel')
                               ->join('kelas', 'kelas.id = prota_promes.kelas_id')
                               ->join('mata_pelajaran', 'mata_pelajaran.id = prota_promes.mapel_id')
                               ->where('prota_promes.guru_id', $guru['id'])
                               ->where('prota_promes.tipe', $tipe)
                               ->where('prota_promes.kelas_id', $kelas_id)
                               ->where('prota_promes.mapel_id', $mapel_id)
                               ->orderBy('prota_promes.id', 'ASC')
                               ->findAll();

        $kelasModel = new KelasModel();
        $mapelModel = new MataPelajaranModel();

        $data = [
            'title'   => 'Cetak ' . $tipe,
            'tipe'    => $tipe,
            'docs'    => $dataDocs,
            'guru'    => $guru,
            'sekolah' => $this->getProfilSekolah(),
            'kelas'   => $kelasModel->find($kelas_id),
            'mapel'   => $mapelModel->find($mapel_id)
        ];

        return view('export/print_prota', $data);
    }
}
