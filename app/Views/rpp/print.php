<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        /* Menggunakan font mirip Times New Roman atau serif umum untuk dokumen formal */
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 40px;
            font-size: 12pt;
            line-height: 1.5;
        }
        
        h3 {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 30px;
        }

        table.header-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        table.header-table td {
            padding: 4px;
            vertical-align: top;
        }

        table.header-table td:first-child {
            width: 180px;
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .sub-title {
            font-weight: bold;
            margin-left: 20px;
        }

        .content-box {
            margin-left: 45px;
            margin-bottom: 15px;
            text-align: justify;
        }

        ul {
            margin-top: 0;
            padding-left: 20px;
        }

        /* Hapus margin pada text area agar rapi saat dicetak dengan white-space pre-wrap */
        .pre-wrap {
            white-space: pre-wrap;
            margin: 0;
        }

        /* Tanda Tangan */
        .signature-box {
            width: 100%;
            margin-top: 60px;
        }
        .signature-table {
            width: 100%;
            text-align: center;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 80px;
        }

        /* Sembunyikan elemen saat mencetak jika ada */
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; background:#f0f0f0; padding:10px; text-align:center; border:1px solid #ccc; font-family:sans-serif;">
        <button onclick="window.print()" style="padding:8px 16px; background:#0d6efd; color:#fff; border:none; cursor:pointer;">🖨️ Cetak / Simpan PDF (Ctrl+P)</button>
        <button onclick="window.close()" style="padding:8px 16px; background:#6c757d; color:#fff; border:none; cursor:pointer; margin-left:10px;">Tutup</button>
    </div>

    <h3>RENCANA PEMBELAJARAN (RPP) KURIKULUM MERDEKA</h3>

    <table class="header-table">
        <tr>
            <td>Mata Pelajaran</td>
            <td>: <?= esc($rpp["nama_mapel"]) ?></td>
        </tr>
        <tr>
            <td>Kelas/Tingkat</td>
            <td>: <?= esc($rpp["nama_kelas"]) ?></td>
        </tr>
        <tr>
            <td>Topik</td>
            <td>: <span class="pre-wrap"><?= esc($template["topik"] ?? "-") ?></span></td>
        </tr>
        <tr>
            <td>Durasi</td>
            <td>: <?= esc($template["durasi"] ?? "-") ?></td>
        </tr>
        <tr>
            <td>Pendekatan</td>
            <td>: <?= esc($template["pendekatan"] ?? "-") ?></td>
        </tr>
    </table>

    <div class="section-title">1. IDENTIFIKASI</div>
    
    <div class="sub-title">a. Kesiapan Peserta Didik:</div>
    <div class="content-box pre-wrap"><?= esc($template["identifikasi_kesiapan"] ?? "-") ?></div>

    <div class="sub-title">b. Karakteristik Materi Pelajaran:</div>
    <div class="content-box pre-wrap"><?= esc($template["identifikasi_karakteristik"] ?? "-") ?></div>

    <div class="sub-title">c. Dimensi Profil Lulusan:</div>
    <div class="content-box pre-wrap"><?= esc($template["identifikasi_profil"] ?? "-") ?></div>


    <div class="section-title">2. DESAIN PEMBELAJARAN</div>
    
    <div class="sub-title">a. Capaian Pembelajaran (CP):</div>
    <div class="content-box pre-wrap"><?= esc($template["desain_cp"] ?? "-") ?></div>

    <div class="sub-title">b. Topik Pembelajaran Kontekstual:</div>
    <div class="content-box pre-wrap"><?= esc($template["desain_topik_kontekstual"] ?? "-") ?></div>

    <div class="sub-title">c. Integrasi Lintas Disiplin:</div>
    <div class="content-box pre-wrap"><?= esc($template["desain_integrasi"] ?? "-") ?></div>

    <div class="sub-title">d. Tujuan Pembelajaran:</div>
    <div class="content-box pre-wrap"><?= esc($template["desain_tujuan"] ?? "-") ?></div>

    <div class="sub-title">e. Kerangka Pembelajaran:</div>
    <div class="content-box pre-wrap"><?= esc($template["desain_kerangka"] ?? "-") ?></div>


    <div class="section-title">3. PENGALAMAN BELAJAR</div>

    <div class="sub-title">a. Prinsip Berkesadaran, Bermakna, Menggembirakan:</div>
    <div class="content-box pre-wrap"><?= esc($template["pengalaman_prinsip"] ?? "-") ?></div>

    <div class="sub-title">b. Tahapan Pembelajaran:</div>
    <div class="content-box pre-wrap"><?= esc($template["pengalaman_tahapan"] ?? "-") ?></div>

    <div class="sub-title">c. Deskripsi Pengalaman Belajar:</div>
    <div class="content-box pre-wrap"><?= esc($template["pengalaman_deskripsi"] ?? "-") ?></div>


    <div class="section-title">4. ASESMEN</div>

    <div class="sub-title">a. Asesmen Awal:</div>
    <div class="content-box pre-wrap"><?= esc($template["asesmen_awal"] ?? "-") ?></div>

    <div class="sub-title">b. Asesmen Proses:</div>
    <div class="content-box pre-wrap"><?= esc($template["asesmen_proses"] ?? "-") ?></div>

    <div class="sub-title">c. Asesmen Akhir:</div>
    <div class="content-box pre-wrap"><?= esc($template["asesmen_akhir"] ?? "-") ?></div>


    <div class="signature-box">
        <table class="signature-table">
            <tr>
                <td>Mengetahui,<br>Kepala Sekolah</td>
                <td><?= esc($sekolah["kota"] ?? "Kota") ?>, <?= date("d F Y") ?><br>Guru Pengajar</td>
            </tr>
            <tr>
                <td>
                    <div class="signature-name"><?= esc($sekolah["nama_kepsek"] ?? "Nama Kepala Sekolah") ?></div>
                    NIP. <?= esc($sekolah["nip_kepsek"] ?? "-") ?>
                </td>
                <td>
                    <div class="signature-name"><?= esc($rpp["nama_guru"]) ?></div>
                    NIP. <?= esc($rpp["nip"] ?? "-") ?>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
