<?php
namespace App\Controllers;

use App\Models\UlanganModel;
use App\Models\UlanganKelasModel;
use App\Models\SoalModel;
use App\Models\JawabanSiswaModel;
use App\Models\JawabanSiswaDetailModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\TahunPelajaranModel;
use App\Models\NotifikasiModel;

class Ulangan extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        $data = ['title' => 'Ujian Online / Ulangan'];
        
        $ulanganModel = new UlanganModel();
        
        if ($role == 'Guru') {
            $guruModel = new GuruModel();
            $mapelModel = new MataPelajaranModel();
            $guru = $guruModel->where('user_id', session()->get('id'))->first();
            
            $db = \Config\Database::connect();
            if ($guru) {
                $builder = $db->table('ulangan');
                $builder->select('ulangan.*, mata_pelajaran.nama_mapel');
                $builder->join('mata_pelajaran', 'mata_pelajaran.id = ulangan.mapel_id');
                $builder->where('ulangan.guru_id', $guru['id']);
                $builder->orderBy('ulangan.id', 'DESC');
                $data['ulanganList'] = $builder->get()->getResultArray();
            } else {
                $data['ulanganList'] = [];
            }
            
            $data['mapelList'] = $mapelModel->findAll();
            $data['guru_id'] = $guru ? $guru['id'] : 0;
            
            return view('ulangan/guru_index', $data);
            
        } elseif ($role == 'Siswa') {
            $siswaModel = new SiswaModel();
            $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
            
            $db = \Config\Database::connect();
            $enrollment = $db->table('anggota_kelas')->where('siswa_id', $siswa ? $siswa['id'] : 0)->orderBy('id', 'DESC')->get()->getRowArray();
            
            if ($enrollment) {
                $builder = $db->table('ulangan_kelas');
                $builder->select('ulangan_kelas.id as uk_id, ulangan.*, mata_pelajaran.nama_mapel,
                                  jawaban_siswa.id as js_id, jawaban_siswa.status, jawaban_siswa.nilai_akhir');
                $builder->join('ulangan', 'ulangan.id = ulangan_kelas.ulangan_id');
                $builder->join('mata_pelajaran', 'mata_pelajaran.id = ulangan.mapel_id');
                $builder->join('jawaban_siswa', 'jawaban_siswa.ulangan_id = ulangan.id AND jawaban_siswa.siswa_id = ' . $siswa['id'], 'left');
                $builder->where('ulangan_kelas.kelas_id', $enrollment['kelas_id']);
                $builder->orderBy('ulangan.id', 'DESC');
                
                $data['ulanganList'] = $builder->get()->getResultArray();
            } else {
                $data['ulanganList'] = [];
            }
            
            return view('ulangan/siswa_index', $data);
        }
        
        return redirect()->to('/dashboard');
    }

    // ── hasil_by_ulangan: Helper redirect ke hasil siswa via ulangan_id ──
    public function hasil_by_ulangan($ulangan_id)
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
        if (!$siswa) return redirect()->to('/ulangan');

        $jawabanSiswaModel = new JawabanSiswaModel();
        $attempt = $jawabanSiswaModel
            ->where('ulangan_id', $ulangan_id)
            ->where('siswa_id', $siswa['id'])
            ->first();

        if (!$attempt) {
            return redirect()->to('/ulangan')->with('error', 'Data jawaban tidak ditemukan.');
        }

        return redirect()->to('/ulangan/hasil/' . $attempt['id']);
    }
    
    public function save()
    {
        $ulanganModel = new UlanganModel();
        $id = $this->request->getVar('id');
        
        $data = [
            'guru_id' => $this->request->getVar('guru_id'),
            'mapel_id' => $this->request->getVar('mapel_id'),
            'judul' => $this->request->getVar('judul'),
            'deskripsi' => $this->request->getVar('deskripsi'),
            'tipe' => $this->request->getVar('tipe'),
            'durasi' => $this->request->getVar('durasi'),
            'kkm' => $this->request->getVar('kkm')
        ];
        if (!empty($id)) $data['id'] = $id;
        
        $ulanganModel->save($data);
        return redirect()->to('/ulangan')->with('success', 'Data ulangan berhasil disimpan.');
    }
    
    public function soal($ulangan_id)
    {
        $ulanganModel = new UlanganModel();
        $soalModel = new SoalModel();
        $kelasModel = new KelasModel();
        $tahunModel = new TahunPelajaranModel();
        $ukModel = new UlanganKelasModel();
        
        $ulangan = $ulanganModel->find($ulangan_id);
        if (!$ulangan) return redirect()->to('/ulangan');
        
        $data = [
            'title' => 'Kelola Soal: ' . $ulangan['judul'],
            'ulangan' => $ulangan,
            'soalList' => $soalModel->where('ulangan_id', $ulangan_id)->findAll(),
            'kelasList' => $kelasModel->findAll(),
            'tahunList' => $tahunModel->findAll()
        ];
        
        $db = \Config\Database::connect();
        $data['assigned_classes'] = $db->table('ulangan_kelas')
            ->select('ulangan_kelas.*, kelas.nama_kelas, tahun_pelajaran.tahun')
            ->join('kelas', 'kelas.id = ulangan_kelas.kelas_id')
            ->join('tahun_pelajaran', 'tahun_pelajaran.id = ulangan_kelas.tahun_pelajaran_id')
            ->where('ulangan_id', $ulangan_id)->get()->getResultArray();
            
        return view('ulangan/guru_soal', $data);
    }
    
    public function save_soal()
    {
        $soalModel = new SoalModel();
        $ulangan_id = $this->request->getVar('ulangan_id');
        $id = $this->request->getVar('id');
        
        $tipe = $this->request->getVar('tipe_soal');
        $kunci = $this->request->getVar('kunci_jawaban');
        if (is_array($kunci)) { $kunci = json_encode($kunci); }
        
        $data = [
            'ulangan_id' => $ulangan_id,
            'pertanyaan' => $this->request->getVar('pertanyaan'),
            'tipe_soal' => $tipe,
            'opsi_a' => $this->request->getVar('opsi_a'),
            'opsi_b' => $this->request->getVar('opsi_b'),
            'opsi_c' => $this->request->getVar('opsi_c'),
            'opsi_d' => $this->request->getVar('opsi_d'),
            'opsi_e' => $this->request->getVar('opsi_e'),
            'opsi_tambahan' => $this->request->getVar('opsi_tambahan'),
            'kunci_jawaban' => $kunci,
            'bobot' => $this->request->getVar('bobot') ?: 1
        ];
        if (!empty($id)) $data['id'] = $id;
        
        $soalModel->save($data);
        return redirect()->to('/ulangan/soal/'.$ulangan_id)->with('success', 'Soal berhasil disimpan.');
    }
    
    public function delete_soal($id, $ulangan_id)
    {
        $soalModel = new SoalModel();
        $soalModel->delete($id);
        return redirect()->to('/ulangan/soal/'.$ulangan_id)->with('success', 'Soal dihapus.');
    }
    
    public function assign_kelas()
    {
        $ukModel = new UlanganKelasModel();
        $ulangan_id = $this->request->getVar('ulangan_id');
        $kelas_id = $this->request->getVar('kelas_id');
        
        $ukModel->save([
            'ulangan_id' => $ulangan_id,
            'kelas_id' => $kelas_id,
            'tahun_pelajaran_id' => $this->request->getVar('tahun_pelajaran_id')
        ]);
        
        // Kirim Notifikasi ke Siswa
        $db = \Config\Database::connect();
        $ulangan = $db->table('ulangan')->where('id', $ulangan_id)->get()->getRowArray();
        $siswas = $db->table('anggota_kelas')
            ->select('siswas.user_id')
            ->join('siswas', 'siswas.id = anggota_kelas.siswa_id')
            ->where('anggota_kelas.kelas_id', $kelas_id)
            ->get()->getResultArray();
            
        if (!empty($siswas) && $ulangan) {
            $userIds = array_column($siswas, 'user_id');
            $notif = new NotifikasiModel();
            $pesan = "Ujian baru '{$ulangan['judul']}' telah ditugaskan ke kelas Anda.";
            $notif->kirimBulk($userIds, 'ujian', '📝 Ujian Baru', $pesan, base_url('ulangan'));
        }
        
        return redirect()->to('/ulangan/soal/'.$ulangan_id)->with('success', 'Ulangan ditugaskan ke kelas.');
    }
    
    public function kerjakan($uk_id)
    {
        $db = \Config\Database::connect();
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
        if (!$siswa) return redirect()->to('/ulangan');
        
        $uk = $db->table('ulangan_kelas')
                 ->select('ulangan_kelas.*, ulangan.judul, ulangan.durasi, ulangan.id as ul_id')
                 ->join('ulangan', 'ulangan.id = ulangan_kelas.ulangan_id')
                 ->where('ulangan_kelas.id', $uk_id)->get()->getRowArray();
                 
        if (!$uk) return redirect()->to('/ulangan');
        
        $jawabanSiswaModel = new JawabanSiswaModel();
        $attempt = $jawabanSiswaModel->where('ulangan_id', $uk['ul_id'])->where('siswa_id', $siswa['id'])->first();
        
        if ($attempt && $attempt['status'] == 'Selesai') {
            return redirect()->to('/ulangan')->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }
        
        if (!$attempt) {
            $jawabanSiswaModel->insert([
                'ulangan_id' => $uk['ul_id'],
                'siswa_id' => $siswa['id'],
                'waktu_mulai' => date('Y-m-d H:i:s'),
                'status' => 'Mengerjakan'
            ]);
            $attempt_id = $jawabanSiswaModel->insertID();
            $attempt = $jawabanSiswaModel->find($attempt_id);
        }
        
        $soalModel = new SoalModel();
        $soalList = $soalModel->where('ulangan_id', $uk['ul_id'])->findAll();
        
        $data = [
            'title' => 'Mengerjakan: ' . $uk['judul'],
            'uk' => $uk,
            'attempt' => $attempt,
            'soalList' => $soalList
        ];
        
        return view('ulangan/siswa_kerjakan', $data);
    }
    
    public function submit_jawaban()
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
        $jawabanSiswaModel = new JawabanSiswaModel();
        $detailModel = new JawabanSiswaDetailModel();
        $soalModel = new SoalModel();
        
        $attempt_id = $this->request->getVar('attempt_id');
        $attempt = $jawabanSiswaModel->find($attempt_id);
        
        if (!$attempt || $attempt['siswa_id'] != $siswa['id']) return redirect()->to('/ulangan');
        
        $jawaban_input = $this->request->getVar('jawaban') ?? []; 
        
        $total_skor = 0;
        $max_skor = 0;
        $has_uraian = false;
        
        // Bersihkan detail jawaban lama jika ada
        $detailModel->where('jawaban_siswa_id', $attempt_id)->delete();
        
        $soalList = $soalModel->where('ulangan_id', $attempt['ulangan_id'])->findAll();
        
        foreach ($soalList as $soal) {
            $soal_id = $soal['id'];
            $max_skor += $soal['bobot'];
            $is_benar = 0;
            $skor = 0;
            
            $jawaban = isset($jawaban_input[$soal_id]) ? $jawaban_input[$soal_id] : '';
            
            if ($soal['tipe_soal'] == 'PG' || $soal['tipe_soal'] == 'Benar Salah') {
                if ($jawaban == $soal['kunci_jawaban']) {
                    $is_benar = 1;
                    $skor = $soal['bobot'];
                }
            } elseif ($soal['tipe_soal'] == 'PG Kompleks' || $soal['tipe_soal'] == 'Menjodohkan') {
                if (is_array($jawaban)) {
                    $jawaban_str = json_encode($jawaban);
                    if ($jawaban_str == $soal['kunci_jawaban']) {
                        $is_benar = 1;
                        $skor = $soal['bobot'];
                    }
                    $jawaban = $jawaban_str;
                }
            } elseif ($soal['tipe_soal'] == 'Uraian') {
                $has_uraian = true;
                $is_benar = null; 
                $skor = 0;
            }
            
            $total_skor += $skor;
            
            $detailModel->insert([
                'jawaban_siswa_id' => $attempt_id,
                'soal_id' => $soal_id,
                'jawaban' => is_array($jawaban) ? json_encode($jawaban) : $jawaban,
                'is_benar' => $is_benar,
                'skor' => $skor
            ]);
        }
        
        $nilai_akhir = $max_skor > 0 ? ($total_skor / $max_skor) * 100 : 0;
        $status_penilaian = $has_uraian ? 'Menunggu Koreksi' : 'Selesai';
        
        $jawabanSiswaModel->update($attempt_id, [
            'status' => 'Selesai',
            'waktu_selesai' => date('Y-m-d H:i:s'),
            'nilai_akhir' => $status_penilaian == 'Selesai' ? $nilai_akhir : 0, 
            'status_penilaian' => $status_penilaian
        ]);
        
        // Kirim Notifikasi ke Guru
        $db = \Config\Database::connect();
        $ulanganInfo = $db->table('ulangan')
            ->select('ulangan.judul, gurus.user_id')
            ->join('gurus', 'gurus.id = ulangan.guru_id')
            ->where('ulangan.id', $attempt['ulangan_id'])
            ->get()->getRowArray();
            
        if ($ulanganInfo) {
            $notif = new NotifikasiModel();
            $namaSiswa = session()->get('nama_lengkap');
            $pesan = "Siswa {$namaSiswa} telah menyelesaikan ujian '{$ulanganInfo['judul']}'" . ($has_uraian ? " dan menunggu koreksi uraian." : ".");
            $notif->kirim($ulanganInfo['user_id'], 'ujian', '✅ Ujian Selesai', $pesan, base_url('ulangan/rekap/' . $attempt['ulangan_id']));
        }
        
        return redirect()->to('/ulangan')->with('success', 'Ujian selesai dikerjakan!');
    }

    // ═══════════════════════════════════════════════════════════
    // REKAP — Guru melihat nilai semua siswa untuk 1 ujian
    // Route: GET /ulangan/rekap/{ulangan_id}
    // ═══════════════════════════════════════════════════════════
    public function rekap($ulangan_id)
    {
        $ulanganModel      = new UlanganModel();
        $soalModel         = new SoalModel();
        $jawabanSiswaModel = new JawabanSiswaModel();
        $guruModel         = new GuruModel();
        $db                = \Config\Database::connect();

        $ulangan = $ulanganModel->find($ulangan_id);
        if (!$ulangan) return redirect()->to('/ulangan');

        // Pastikan yang mengakses adalah guru pemilik
        $guru = $guruModel->where('user_id', session()->get('id'))->first();
        if (!$guru || $ulangan['guru_id'] != $guru['id']) {
            return redirect()->to('/ulangan')->with('error', 'Akses ditolak.');
        }

        // Ambil semua siswa yang sudah mengerjakan ujian ini
        $rekapList = $db->table('jawaban_siswa')
            ->select('jawaban_siswa.*, users.nama_lengkap, siswas.nis')
            ->join('siswas', 'siswas.id = jawaban_siswa.siswa_id')
            ->join('users',  'users.id  = siswas.user_id')
            ->where('jawaban_siswa.ulangan_id', $ulangan_id)
            ->orderBy('users.nama_lengkap', 'ASC')
            ->get()->getResultArray();

        // Hitung statistik kelas
        $totalSiswa = count($rekapList);
        $lulus      = 0;
        $totalNilai = 0;
        foreach ($rekapList as $r) {
            if ($r['nilai_akhir'] >= $ulangan['kkm']) $lulus++;
            $totalNilai += $r['nilai_akhir'];
        }

        // Cek apakah ada soal uraian
        $adaUraian = $soalModel->where('ulangan_id', $ulangan_id)
                               ->where('tipe_soal', 'Uraian')
                               ->countAllResults() > 0;

        return view('ulangan/guru_rekap', [
            'title'       => 'Rekap Nilai: ' . $ulangan['judul'],
            'ulangan'     => $ulangan,
            'rekapList'   => $rekapList,
            'adaUraian'   => $adaUraian,
            'totalSiswa'  => $totalSiswa,
            'jumlahLulus' => $lulus,
            'rataRata'    => $totalSiswa > 0 ? round($totalNilai / $totalSiswa, 1) : 0,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // KOREKSI — Form guru mengoreksi jawaban uraian 1 siswa
    // Route: GET /ulangan/koreksi/{jawaban_siswa_id}
    // ═══════════════════════════════════════════════════════════
    public function koreksi($jawaban_siswa_id)
    {
        $jawabanSiswaModel = new JawabanSiswaModel();
        $detailModel       = new JawabanSiswaDetailModel();
        $soalModel         = new SoalModel();
        $ulanganModel      = new UlanganModel();
        $db                = \Config\Database::connect();

        $attempt = $jawabanSiswaModel->find($jawaban_siswa_id);
        if (!$attempt) return redirect()->to('/ulangan');

        $ulangan = $ulanganModel->find($attempt['ulangan_id']);

        // Verifikasi guru pemilik
        $guruModel = new GuruModel();
        $guru = $guruModel->where('user_id', session()->get('id'))->first();
        if (!$guru || $ulangan['guru_id'] != $guru['id']) {
            return redirect()->to('/ulangan')->with('error', 'Akses ditolak.');
        }

        // Ambil info siswa
        $siswaInfo = $db->table('siswas')
            ->select('siswas.nis, users.nama_lengkap')
            ->join('users', 'users.id = siswas.user_id')
            ->where('siswas.id', $attempt['siswa_id'])
            ->get()->getRowArray();

        // Ambil HANYA jawaban soal Uraian beserta soalnya
        $jawabanUraian = $db->table('jawaban_siswa_detail')
            ->select('jawaban_siswa_detail.*, soal.pertanyaan, soal.bobot, soal.kunci_jawaban')
            ->join('soal', 'soal.id = jawaban_siswa_detail.soal_id')
            ->where('jawaban_siswa_detail.jawaban_siswa_id', $jawaban_siswa_id)
            ->where('soal.tipe_soal', 'Uraian')
            ->get()->getResultArray();

        return view('ulangan/guru_koreksi', [
            'title'          => 'Koreksi Uraian — ' . $siswaInfo['nama_lengkap'],
            'attempt'        => $attempt,
            'ulangan'        => $ulangan,
            'siswaInfo'      => $siswaInfo,
            'jawabanUraian'  => $jawabanUraian,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // PROSES KOREKSI — Simpan skor uraian, hitung nilai akhir
    // Route: POST /ulangan/proses_koreksi
    // ═══════════════════════════════════════════════════════════
    public function proses_koreksi()
    {
        $jawabanSiswaModel = new JawabanSiswaModel();
        $detailModel       = new JawabanSiswaDetailModel();
        $soalModel         = new SoalModel();

        $jawaban_siswa_id = $this->request->getVar('jawaban_siswa_id');
        $skor_uraian      = $this->request->getVar('skor') ?? [];

        $attempt = $jawabanSiswaModel->find($jawaban_siswa_id);
        if (!$attempt) return redirect()->to('/ulangan');

        // Simpan skor per jawaban uraian
        foreach ($skor_uraian as $detail_id => $skor) {
            $detail = $detailModel->find($detail_id);
            $soal   = $soalModel->find($detail['soal_id']);

            // Clamp skor agar tidak melebihi bobot soal
            $skor = max(0, min((float)$skor, (float)$soal['bobot']));

            $detailModel->update($detail_id, [
                'skor'     => $skor,
                'is_benar' => $skor > 0 ? 1 : 0,
            ]);
        }

        // Hitung ulang nilai akhir dari seluruh detail jawaban
        $semuaDetail = $detailModel->where('jawaban_siswa_id', $jawaban_siswa_id)->findAll();
        $totalSkor   = 0;
        foreach ($semuaDetail as $d) {
            $totalSkor += (float)$d['skor'];
        }

        // Hitung max skor dari semua soal ujian
        $soalList = $soalModel->where('ulangan_id', $attempt['ulangan_id'])->findAll();
        $maxSkor  = array_sum(array_column($soalList, 'bobot'));

        $nilaiAkhir = $maxSkor > 0 ? round(($totalSkor / $maxSkor) * 100, 2) : 0;

        $jawabanSiswaModel->update($jawaban_siswa_id, [
            'nilai_akhir'      => $nilaiAkhir,
            'status_penilaian' => 'Selesai',
        ]);
        
        // Kirim Notifikasi ke Siswa
        $db = \Config\Database::connect();
        $siswaInfo = $db->table('siswas')->select('user_id')->where('id', $attempt['siswa_id'])->get()->getRowArray();
        $ulangan = $db->table('ulangan')->where('id', $attempt['ulangan_id'])->get()->getRowArray();
        
        if ($siswaInfo && $ulangan) {
            $notif = new NotifikasiModel();
            $pesan = "Koreksi selesai untuk ujian '{$ulangan['judul']}'. Nilai akhir Anda: {$nilaiAkhir}";
            $notif->kirim($siswaInfo['user_id'], 'koreksi', '✏️ Hasil Koreksi', $pesan, base_url('ulangan/hasil/' . $jawaban_siswa_id));
        }

        return redirect()->to('/ulangan/rekap/' . $attempt['ulangan_id'])
                         ->with('success', 'Koreksi berhasil disimpan. Nilai akhir: ' . $nilaiAkhir);
    }

    // ═══════════════════════════════════════════════════════════
    // HASIL — Siswa melihat detail hasil ujiannya sendiri
    // Route: GET /ulangan/hasil/{jawaban_siswa_id}
    // ═══════════════════════════════════════════════════════════
    public function hasil($jawaban_siswa_id)
    {
        $jawabanSiswaModel = new JawabanSiswaModel();
        $detailModel       = new JawabanSiswaDetailModel();
        $soalModel         = new SoalModel();
        $ulanganModel      = new UlanganModel();
        $siswaModel        = new SiswaModel();
        $db                = \Config\Database::connect();

        $attempt = $jawabanSiswaModel->find($jawaban_siswa_id);
        if (!$attempt) return redirect()->to('/ulangan');

        // Keamanan: hanya pemilik jawaban yang bisa melihat
        $siswa = $siswaModel->where('user_id', session()->get('id'))->first();
        if (!$siswa || $attempt['siswa_id'] != $siswa['id']) {
            return redirect()->to('/ulangan')->with('error', 'Akses ditolak.');
        }

        $ulangan = $ulanganModel->find($attempt['ulangan_id']);

        // Ambil jawaban siswa beserta data soal
        $detailHasil = $db->table('jawaban_siswa_detail')
            ->select('jawaban_siswa_detail.*, soal.pertanyaan, soal.tipe_soal, soal.bobot,
                      soal.kunci_jawaban, soal.opsi_a, soal.opsi_b, soal.opsi_c,
                      soal.opsi_d, soal.opsi_e')
            ->join('soal', 'soal.id = jawaban_siswa_detail.soal_id')
            ->where('jawaban_siswa_detail.jawaban_siswa_id', $jawaban_siswa_id)
            ->orderBy('soal.id', 'ASC')
            ->get()->getResultArray();

        return view('ulangan/siswa_hasil', [
            'title'       => 'Hasil Ujian: ' . $ulangan['judul'],
            'ulangan'     => $ulangan,
            'attempt'     => $attempt,
            'detailHasil' => $detailHasil,
        ]);
    }
}
