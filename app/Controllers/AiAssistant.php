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
            'title' => 'AI Assistant - PPM & Materi',
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
        
        $jenisDokumen = (strtolower($tipe) === 'rpp') ? 'Perencanaan Pembelajaran Mendalam (PPM)' : 'Materi Bahan Ajar';
        $prompt = "Buatkan dokumen pendidikan berjenis {$jenisDokumen} untuk kelas {$kelas} dengan durasi {$durasi}. Topik: {$topik}. Khusus untuk PPM, sesuaikan dengan struktur Kurikulum Mendalam (misal: Pemahaman Bermakna, Pertanyaan Pemantik, Skenario Pembelajaran Mendalam, dan Asesmen Komprehensif). Tolong format dalam HTML yang rapi (menggunakan tag <h2>, <h3>, <ul>, <p> dll) tanpa tag <html> atau <body> agar bisa langsung ditampilkan.";

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
                <h2>Perencanaan Pembelajaran Mendalam (PPM)</h2>
                <p><strong>Mata Pelajaran:</strong> Sesuai Topik<br>
                <strong>Kelas:</strong> {$kelas}<br>
                <strong>Alokasi Waktu:</strong> {$durasi}<br>
                <strong>Materi Pokok:</strong> {$topik}</p>
                <hr>
                <h4>A. Pemahaman Bermakna & Pertanyaan Pemantik</h4>
                <ul>
                    <li><strong>Pemahaman Bermakna:</strong> Memahami bahwa {$topik} sangat relevan dan dapat diterapkan dalam penyelesaian masalah sehari-hari.</li>
                    <li><strong>Pertanyaan Pemantik:</strong> Mengapa {$topik} penting untuk dipelajari? Bagaimana kaitannya dengan lingkungan kita?</li>
                </ul>
                <h4>B. Skenario Pembelajaran Mendalam</h4>
                <ol>
                    <li><strong>Eksplorasi Konsep (15 menit):</strong> Diskusi pemantik untuk menggali pengetahuan awal siswa mengenai {$topik}.</li>
                    <li><strong>Investigasi & Analisis (" . ((int)$durasi - 45) . " menit):</strong> Pembelajaran berbasis proyek/masalah (PBL) dimana siswa menganalisis studi kasus {$topik} secara mendalam.</li>
                    <li><strong>Refleksi & Konfirmasi (30 menit):</strong> Siswa mempresentasikan temuan, berdiskusi, dan guru memberikan penguatan bermakna.</li>
                </ol>
                <h4>C. Asesmen Komprehensif</h4>
                <p>Penilaian formatif melalui observasi keaktifan diskusi dan rubrik kemampuan berpikir kritis. Penilaian sumatif melalui hasil proyek/analisis terkait {$topik}.</p>
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
