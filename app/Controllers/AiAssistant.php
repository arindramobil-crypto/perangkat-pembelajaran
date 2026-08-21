<?php

namespace App\Controllers;

class AiAssistant extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        if (!in_array($role, ['Admin', 'Guru'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'AI Assistant - RPP & Materi',
            'role'  => $role
        ];

        return view('ai_assistant/index', $data);
    }

    public function generate()
    {
        $role = session()->get('role');
        if (!in_array($role, ['Admin', 'Guru'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $topik = $this->request->getPost('topik');
        $tipe = $this->request->getPost('tipe'); // 'rpp' atau 'materi'
        $kelas = $this->request->getPost('kelas');
        $durasi = $this->request->getPost('durasi') ?? '90 Menit';

        if (empty($topik) || empty($tipe)) {
            return redirect()->back()->with('error', 'Topik dan Tipe dokumen wajib diisi.');
        }

        $apiKey = env('GEMINI_API_KEY');
        
        $prompt = "Buatkan dokumen pendidikan berjenis {$tipe} untuk kelas {$kelas} dengan durasi {$durasi}. Topik: {$topik}. Tolong format dalam HTML yang rapi (menggunakan tag <h2>, <h3>, <ul>, <p> dll) agar bisa langsung ditampilkan.";

        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            // Mode Simulasi
            $htmlResult = $this->_generateSimulatedResponse($tipe, $topik, $kelas, $durasi);
        } else {
            // Panggil API (Gemini contoh)
            $htmlResult = $this->_callGeminiAPI($apiKey, $prompt);
            if (!$htmlResult) {
                $htmlResult = "<div class='alert alert-danger'>Gagal menghubungi AI API. Pastikan API key valid.</div>";
            }
        }

        $data = [
            'title' => 'Hasil Generasi AI - ' . strtoupper($tipe),
            'role' => $role,
            'topik' => $topik,
            'tipe' => $tipe,
            'htmlResult' => $htmlResult
        ];

        return view('ai_assistant/result', $data);
    }

    private function _generateSimulatedResponse($tipe, $topik, $kelas, $durasi)
    {
        sleep(2); // simulasi delay
        if (strtolower($tipe) === 'rpp') {
            return "
            <div class='mb-4'>
                <span class='badge bg-warning text-dark mb-2'>Mode Simulasi AI</span>
                <h2>Rencana Pelaksanaan Pembelajaran (RPP)</h2>
                <p><strong>Mata Pelajaran:</strong> Sesuai Topik<br>
                <strong>Kelas:</strong> {$kelas}<br>
                <strong>Alokasi Waktu:</strong> {$durasi}<br>
                <strong>Materi Pokok:</strong> {$topik}</p>
                <hr>
                <h4>A. Tujuan Pembelajaran</h4>
                <ul>
                    <li>Siswa dapat memahami konsep dasar dari {$topik}.</li>
                    <li>Siswa mampu mengaplikasikan prinsip {$topik} dalam kehidupan sehari-hari.</li>
                </ul>
                <h4>B. Kegiatan Pembelajaran</h4>
                <ol>
                    <li><strong>Pendahuluan (15 menit):</strong> Apersepsi, menyampaikan tujuan, dan motivasi.</li>
                    <li><strong>Kegiatan Inti (" . ((int)$durasi - 30) . " menit):</strong> Eksplorasi materi {$topik}, diskusi kelompok, dan presentasi.</li>
                    <li><strong>Penutup (15 menit):</strong> Kesimpulan, refleksi, dan tugas.</li>
                </ol>
                <h4>C. Penilaian</h4>
                <p>Penilaian dilakukan melalui observasi sikap, tes tertulis, dan presentasi kelompok terkait materi {$topik}.</p>
            </div>
            ";
        } else {
            return "
            <div class='mb-4'>
                <span class='badge bg-warning text-dark mb-2'>Mode Simulasi AI</span>
                <h2>Materi Pembelajaran: {$topik}</h2>
                <p><strong>Target Kelas:</strong> {$kelas}</p>
                <hr>
                <h4>1. Pengantar Singkat</h4>
                <p>Materi <strong>{$topik}</strong> sangat penting untuk dipahami oleh siswa. Topik ini mencakup konsep dasar yang akan menjadi pondasi untuk pembelajaran selanjutnya.</p>
                <h4>2. Poin-poin Utama</h4>
                <ul>
                    <li>Definisi dan pengertian {$topik}.</li>
                    <li>Karakteristik utama dan fungsi dari {$topik}.</li>
                    <li>Contoh penerapan nyata di masyarakat.</li>
                </ul>
                <h4>3. Rangkuman</h4>
                <p>Pemahaman mengenai {$topik} memberikan wawasan luas bagi siswa. Sangat disarankan bagi siswa untuk memperbanyak latihan studi kasus.</p>
            </div>
            ";
        }
    }

    private function _callGeminiAPI($apiKey, $prompt)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return false;
        }

        $resObj = json_decode($response, true);
        if (isset($resObj['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $resObj['candidates'][0]['content']['parts'][0]['text'];
            // Buang markdown ```html jika AI menambahkannya
            $text = str_replace(['```html', '```'], '', $text);
            return $text;
        }

        return false;
    }
}
