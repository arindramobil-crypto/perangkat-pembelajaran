<?php
namespace App\Controllers;

use App\Models\UlanganModel;
use App\Models\SoalModel;
use App\Models\GuruModel;

/**
 * SoalExportImport — Controller untuk ekspor & impor soal bank ujian.
 *
 * FORMAT YANG DIDUKUNG:
 * ── Export ──────────────────────────────────────────────────────
 *   • JSON  : Backup lengkap semua tipe soal (lossless)
 *   • Excel : Format .xlsx sederhana via SpreadsheetML (tanpa library)
 *
 * ── Import ──────────────────────────────────────────────────────
 *   • JSON  : Restore dari file backup JSON
 *   • Excel/CSV: Dari template yang bisa diunduh
 *
 * Routes:
 *   GET  /soal/ekspor/{ulangan_id}/json
 *   GET  /soal/ekspor/{ulangan_id}/excel
 *   GET  /soal/template-csv/{ulangan_id}
 *   POST /soal/impor/{ulangan_id}
 */
class SoalExportImport extends BaseController
{
    // ════════════════════════════════════════════════════════════
    // GUARD: Pastikan hanya Guru pemilik ulangan yang bisa akses
    // ════════════════════════════════════════════════════════════
    private function getUlangan(int $ulangan_id): ?array
    {
        if (session()->get('role') !== 'Guru') return null;

        $guruModel = new GuruModel();
        $guru      = $guruModel->where('user_id', session()->get('id'))->first();
        if (! $guru) return null;

        $ulangan = (new UlanganModel())->find($ulangan_id);
        if (! $ulangan || $ulangan['guru_id'] != $guru['id']) return null;

        return $ulangan;
    }

    // ════════════════════════════════════════════════════════════
    // EKSPOR JSON — backup lengkap semua tipe soal
    // GET /soal/ekspor/{ulangan_id}/json
    // ════════════════════════════════════════════════════════════
    public function ekspor_json(int $ulangan_id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $ulangan = $this->getUlangan($ulangan_id);
        if (! $ulangan) {
            return redirect()->to('/ulangan')->with('error', 'Akses ditolak.');
        }

        $soalList = (new SoalModel())
            ->where('ulangan_id', $ulangan_id)
            ->orderBy('id', 'ASC')
            ->findAll();

        // Bersihkan field ID agar bisa diimpor ke ulangan lain
        $soalBersih = array_map(function ($s) {
            unset($s['id'], $s['ulangan_id']);
            return $s;
        }, $soalList);

        $export = [
            'meta' => [
                'format'       => 'LMS-SMK-SoalBank-v1',
                'exported_at'  => date('Y-m-d H:i:s'),
                'judul_ujian'  => $ulangan['judul'],
                'tipe_ujian'   => $ulangan['tipe'],
                'total_soal'   => count($soalBersih),
            ],
            'soal' => $soalBersih,
        ];

        $json     = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'soal_' . preg_replace('/[^a-z0-9]/i', '_', $ulangan['judul']) . '_' . date('Ymd') . '.json';

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($json);
    }

    // ════════════════════════════════════════════════════════════
    // EKSPOR EXCEL — format SpreadsheetML (.xlsx-like .xls)
    // GET /soal/ekspor/{ulangan_id}/excel
    // ════════════════════════════════════════════════════════════
    public function ekspor_excel(int $ulangan_id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $ulangan = $this->getUlangan($ulangan_id);
        if (! $ulangan) {
            return redirect()->to('/ulangan')->with('error', 'Akses ditolak.');
        }

        $soalList = (new SoalModel())
            ->where('ulangan_id', $ulangan_id)
            ->orderBy('id', 'ASC')
            ->findAll();

        $fileName = 'soal_' . preg_replace('/[^a-z0-9]/i', '_', $ulangan['judul']) . '_' . date('Ymd') . '.xls';

        // Bangun SpreadsheetML (dibuka Excel / LibreOffice)
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                   xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        $xml .= '<Styles>
          <Style ss:ID="H"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#4F46E5" ss:Pattern="Solid"/></Style>
          <Style ss:ID="C"><Font ss:Color="#059669" ss:Bold="1"/></Style>
          <Style ss:ID="W"><Alignment ss:WrapText="1"/></Style>
        </Styles>' . "\n";

        // Sheet 1: Soal
        $xml .= '<Worksheet ss:Name="Bank Soal"><Table>' . "\n";

        // Header baris info
        $xml .= $this->xlsRow([
            'Judul Ujian: ' . $ulangan['judul'],
            'Tipe: ' . $ulangan['tipe'],
            'Diekspor: ' . date('d/m/Y H:i'),
            'Total Soal: ' . count($soalList),
        ], 'H');

        // Header kolom
        $headers = [
            'No', 'Tipe Soal', 'Pertanyaan',
            'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E',
            'Opsi Tambahan (Menjodohkan/Kompleks)',
            'Kunci Jawaban', 'Bobot',
        ];
        $xml .= $this->xlsRow($headers, 'H');

        // Data soal
        foreach ($soalList as $i => $s) {
            $xml .= $this->xlsRow([
                $i + 1,
                $s['tipe_soal'],
                $s['pertanyaan'],
                $s['opsi_a']        ?? '',
                $s['opsi_b']        ?? '',
                $s['opsi_c']        ?? '',
                $s['opsi_d']        ?? '',
                $s['opsi_e']        ?? '',
                $s['opsi_tambahan'] ?? '',
                $s['kunci_jawaban'] ?? '',
                $s['bobot'],
            ], $s['tipe_soal'] === 'Uraian' ? 'W' : '');
        }

        $xml .= '</Table></Worksheet>' . "\n";

        // Sheet 2: Panduan
        $xml .= '<Worksheet ss:Name="Panduan Import"><Table>' . "\n";
        $xml .= $this->xlsRow(['PANDUAN IMPORT SOAL'], 'H');
        $xml .= $this->xlsRow(['']);
        $panduan = [
            ['Tipe Soal', 'Penjelasan', 'Kunci Jawaban'],
            ['PG', 'Pilihan Ganda biasa. Isi Opsi A-E.', 'Huruf kapital: A / B / C / D / E'],
            ['PG Kompleks', 'Pilihan Ganda lebih dari 1 jawaban benar.', 'JSON array: ["A","C"] atau pisah koma: A,C'],
            ['Menjodohkan', 'Pasangkan kolom kiri-kanan. Opsi A-E = kolom kiri. Opsi Tambahan = kolom kanan (pisah |).', 'JSON: {"A":"X","B":"Y"} atau A-X,B-Y'],
            ['Benar Salah', 'Hanya dua pilihan.', 'Benar atau Salah'],
            ['Uraian', 'Jawaban essay bebas. Dinilai manual oleh guru.', 'Kosongkan atau isi petunjuk kunci'],
        ];
        foreach ($panduan as $row) {
            $xml .= $this->xlsRow($row, count($row) === 3 && $row[0] === 'Tipe Soal' ? 'H' : '');
        }
        $xml .= '</Table></Worksheet>' . "\n";
        $xml .= '</Workbook>';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($xml);
    }

    /** Helper: buat baris XML SpreadsheetML */
    private function xlsRow(array $cells, string $style = ''): string
    {
        $row = '<Row>';
        foreach ($cells as $val) {
            $type  = is_numeric($val) ? 'Number' : 'String';
            $sAttr = $style ? " ss:StyleID=\"$style\"" : '';
            $row  .= "<Cell$sAttr><Data ss:Type=\"$type\">" . htmlspecialchars((string)$val, ENT_XML1) . '</Data></Cell>';
        }
        return $row . '</Row>' . "\n";
    }

    // ════════════════════════════════════════════════════════════
    // UNDUH TEMPLATE CSV
    // GET /soal/template-csv/{ulangan_id}
    // ════════════════════════════════════════════════════════════
    public function template_csv(int $ulangan_id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $ulangan = $this->getUlangan($ulangan_id);
        if (! $ulangan) {
            return redirect()->to('/ulangan')->with('error', 'Akses ditolak.');
        }

        $rows = [
            // Header
            ['tipe_soal','pertanyaan','opsi_a','opsi_b','opsi_c','opsi_d','opsi_e','opsi_tambahan','kunci_jawaban','bobot'],
            // Contoh PG
            ['PG','Ibu kota Indonesia adalah...','Jakarta','Bandung','Surabaya','Medan','Yogyakarta','','A','1'],
            // Contoh PG Kompleks
            ['PG Kompleks','Pilih yang termasuk bilangan prima:','2','3','4','5','6','','["A","B","D"]','2'],
            // Contoh Menjodohkan
            ['Menjodohkan','Jodohkan negara dengan ibu kotanya!','Indonesia','Malaysia','Thailand','Vietnam','Filipina','Jakarta|Kuala Lumpur|Bangkok|Hanoi|Manila','{"A":"Jakarta","B":"Kuala Lumpur","C":"Bangkok","D":"Hanoi","E":"Manila"}','3'],
            // Contoh Benar Salah
            ['Benar Salah','Bumi mengelilingi Matahari.','','','','','','','Benar','1'],
            // Contoh Uraian
            ['Uraian','Jelaskan pengertian fotosintesis!','','','','','','','','5'],
        ];

        $csv = '';
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\r\n";
        }

        $fileName = 'template_soal_' . preg_replace('/[^a-z0-9]/i', '_', $ulangan['judul']) . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody("\xEF\xBB\xBF" . $csv); // BOM untuk Excel agar baca UTF-8
    }

    // ════════════════════════════════════════════════════════════
    // IMPOR — dari JSON atau CSV/Excel
    // POST /soal/impor/{ulangan_id}
    // ════════════════════════════════════════════════════════════
    public function impor(int $ulangan_id): \CodeIgniter\HTTP\RedirectResponse
    {
        $ulangan = $this->getUlangan($ulangan_id);
        if (! $ulangan) {
            return redirect()->to('/ulangan')->with('error', 'Akses ditolak.');
        }

        $file = $this->request->getFile('file_soal');
        if (! $file || ! $file->isValid()) {
            return redirect()->to('/ulangan/soal/' . $ulangan_id)
                ->with('error', 'File tidak valid atau kosong.');
        }

        $ext = strtolower($file->getExtension());
        if (! in_array($ext, ['json', 'csv', 'xls', 'xlsx'])) {
            return redirect()->to('/ulangan/soal/' . $ulangan_id)
                ->with('error', 'Format file harus JSON atau CSV/XLS.');
        }

        $mode    = $this->request->getPost('mode_impor') ?? 'tambah'; // 'tambah' atau 'ganti'
        $soalModel = new SoalModel();

        // Hapus soal lama jika mode ganti
        if ($mode === 'ganti') {
            $soalModel->where('ulangan_id', $ulangan_id)->delete();
        }

        $content = file_get_contents($file->getTempName());
        $berhasil = 0;
        $gagal    = 0;
        $errors   = [];

        if ($ext === 'json') {
            [$berhasil, $gagal, $errors] = $this->prosesImporJson($content, $ulangan_id, $soalModel);
        } else {
            [$berhasil, $gagal, $errors] = $this->prosesImporCsv($content, $ulangan_id, $soalModel);
        }

        $msg = "$berhasil soal berhasil diimpor.";
        if ($gagal > 0) {
            $msg .= " $gagal soal gagal: " . implode('; ', array_slice($errors, 0, 3));
            return redirect()->to('/ulangan/soal/' . $ulangan_id)->with('error', $msg);
        }

        return redirect()->to('/ulangan/soal/' . $ulangan_id)
            ->with('success', $msg);
    }

    // ── Proses JSON ─────────────────────────────────────────────
    private function prosesImporJson(string $content, int $ulangan_id, SoalModel $model): array
    {
        $berhasil = 0; $gagal = 0; $errors = [];

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data['soal'])) {
            return [0, 1, ['Format JSON tidak valid atau soal kosong.']];
        }

        foreach ($data['soal'] as $i => $s) {
            try {
                $kunci = $s['kunci_jawaban'] ?? '';
                if (is_array($kunci)) $kunci = json_encode($kunci, JSON_UNESCAPED_UNICODE);

                $model->insert([
                    'ulangan_id'    => $ulangan_id,
                    'pertanyaan'    => $s['pertanyaan']    ?? '',
                    'tipe_soal'     => $s['tipe_soal']     ?? 'PG',
                    'opsi_a'        => $s['opsi_a']        ?? null,
                    'opsi_b'        => $s['opsi_b']        ?? null,
                    'opsi_c'        => $s['opsi_c']        ?? null,
                    'opsi_d'        => $s['opsi_d']        ?? null,
                    'opsi_e'        => $s['opsi_e']        ?? null,
                    'opsi_tambahan' => $s['opsi_tambahan'] ?? null,
                    'kunci_jawaban' => $kunci,
                    'bobot'         => (int)($s['bobot'] ?? 1),
                ]);
                $berhasil++;
            } catch (\Throwable $e) {
                $gagal++;
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        return [$berhasil, $gagal, $errors];
    }

    // ── Proses CSV ──────────────────────────────────────────────
    private function prosesImporCsv(string $content, int $ulangan_id, SoalModel $model): array
    {
        $berhasil = 0; $gagal = 0; $errors = [];

        // Hapus BOM UTF-8 jika ada
        $content = ltrim($content, "\xEF\xBB\xBF");
        $lines   = str_getcsv($content, "\n");

        // Ambil header dari baris pertama
        $header = str_getcsv(array_shift($lines));
        $header = array_map('trim', $header);

        $tipeValid = ['PG', 'PG Kompleks', 'Menjodohkan', 'Benar Salah', 'Uraian'];

        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '') continue;

            $cols = str_getcsv($line);

            // Map kolom ke nama field
            $row = [];
            foreach ($header as $j => $h) {
                $row[$h] = $cols[$j] ?? '';
            }

            $tipe = trim($row['tipe_soal'] ?? 'PG');
            if (! in_array($tipe, $tipeValid)) {
                $gagal++;
                $errors[] = "Baris " . ($i + 2) . ": Tipe soal '$tipe' tidak dikenali.";
                continue;
            }

            if (empty(trim($row['pertanyaan'] ?? ''))) {
                $gagal++;
                $errors[] = "Baris " . ($i + 2) . ": Pertanyaan kosong.";
                continue;
            }

            try {
                $model->insert([
                    'ulangan_id'    => $ulangan_id,
                    'pertanyaan'    => trim($row['pertanyaan']),
                    'tipe_soal'     => $tipe,
                    'opsi_a'        => trim($row['opsi_a']        ?? '') ?: null,
                    'opsi_b'        => trim($row['opsi_b']        ?? '') ?: null,
                    'opsi_c'        => trim($row['opsi_c']        ?? '') ?: null,
                    'opsi_d'        => trim($row['opsi_d']        ?? '') ?: null,
                    'opsi_e'        => trim($row['opsi_e']        ?? '') ?: null,
                    'opsi_tambahan' => trim($row['opsi_tambahan'] ?? '') ?: null,
                    'kunci_jawaban' => trim($row['kunci_jawaban'] ?? ''),
                    'bobot'         => (int)(trim($row['bobot'] ?? '1') ?: 1),
                ]);
                $berhasil++;
            } catch (\Throwable $e) {
                $gagal++;
                $errors[] = "Baris " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        return [$berhasil, $gagal, $errors];
    }
}
