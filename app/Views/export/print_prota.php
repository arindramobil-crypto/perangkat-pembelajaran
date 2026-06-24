<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; margin: 0; padding: 20px; color: #000; }
        .kop-surat { display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .kop-teks { text-align: center; }
        .kop-teks h1 { margin: 0; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .kop-teks h2 { margin: 5px 0 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop-teks p { margin: 5px 0 0; font-size: 10pt; }
        
        .doc-title { text-align: center; font-size: 12pt; font-weight: bold; margin-bottom: 15px; text-decoration: underline; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 15px; font-size: 10pt; }
        .info-table td { padding: 2px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 9pt; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; }
        .data-table th { background-color: #f0f0f0; font-weight: bold; }
        .data-table .text-left { text-align: left; }
        
        .ttd-container { display: flex; justify-content: space-between; margin-top: 30px; page-break-inside: avoid; }
        .ttd-box { text-align: center; width: 250px; }
        .ttd-box p { margin: 0 0 60px 0; }
        .ttd-box .nama { font-weight: bold; text-decoration: underline; margin-bottom: 2px; }
        .ttd-box .nip { margin: 0; }
        
        .bg-gray { background-color: #e0e0e0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        @media print {
            @page { size: F4 landscape; margin: 1cm; }
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()" style="padding:10px 20px; font-size:14px; cursor:pointer;">🖨️ Cetak Matriks</button>
        <p style="color:red; font-family:sans-serif; font-size:12px;">*Gunakan kertas F4/A4 (Lanskap) pada pengaturan printer.</p>
    </div>

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <div class="kop-teks">
            <h1>PEMERINTAH PROVINSI <?= strtoupper(esc($sekolah['provinsi'] ?? 'JAWA TENGAH')) ?></h1>
            <h2><?= esc($sekolah['nama_sekolah'] ?? 'SMK NEGERI 1 CONTOH') ?></h2>
            <p><?= esc($sekolah['alamat'] ?? 'Jl. Pendidikan No. 1, Telp (021) 123456') ?></p>
        </div>
    </div>

    <div class="doc-title">
        PROGRAM <?= strtoupper(esc($tipe)) ?> (<?= strtoupper(esc($tipe)) ?>)
    </div>

    <table class="info-table">
        <tr>
            <td width="120"><strong>Mata Pelajaran</strong></td>
            <td width="10">:</td>
            <td><?= esc($mapel['nama_mapel']) ?></td>
            <td width="120"><strong>Kelas</strong></td>
            <td width="10">:</td>
            <td><?= esc($kelas['nama_kelas']) ?></td>
        </tr>
        <tr>
            <td><strong>Nama Guru</strong></td>
            <td>:</td>
            <td><?= esc($guru['nama_lengkap']) ?></td>
            <td><strong>Tahun Pelajaran</strong></td>
            <td>:</td>
            <td><?= date('Y') ?> / <?= date('Y')+1 ?></td>
        </tr>
    </table>

    <?php 
    // For print layout, to save space, we show 6 months per table (1 semester)
    // If it's Promes, maybe we only need to show 6 months. If Prota, we show 12 months.
    // We will render two 6-month tables for Prota, and one for Promes (assuming Ganjil for now).
    $semesters = [
        'Ganjil' => [7,8,9,10,11,12],
        'Genap' => [1,2,3,4,5,6]
    ];

    $monthNames = [
        1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun',
        7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'
    ];
    ?>

    <?php foreach($semesters as $semName => $monthsArr): ?>
        <?php 
        // Cek apakah ada data di semester ini (untuk menghemat kertas)
        // Kita anggap selalu print Ganjil dan Genap untuk Prota. Untuk Promes cetak sesuai kebutuhan.
        // Untuk demo, kita cetak semua.
        ?>
        <h4 style="margin-bottom:5px;">SEMESTER <?= strtoupper($semName) ?></h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" width="3%">No</th>
                    <th rowspan="2" width="25%">Materi Pokok / Tujuan Pembelajaran</th>
                    <th rowspan="2" width="5%">Alokasi (JP)</th>
                    <?php foreach($monthsArr as $m): ?>
                        <th colspan="5"><?= $monthNames[$m] ?></th>
                    <?php endforeach; ?>
                    <th rowspan="2" width="10%">Ket</th>
                </tr>
                <tr>
                    <?php foreach($monthsArr as $m): ?>
                        <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($docs)): ?>
                    <tr><td colspan="<?= 3 + (count($monthsArr)*5) + 1 ?>">Data tidak tersedia.</td></tr>
                <?php else: ?>
                    <?php $no=1; foreach($docs as $d): 
                        $alokasiJson = json_decode($d['alokasi_mingguan'], true) ?: [];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="text-left"><?= nl2br(esc($d['materi_pokok'])) ?></td>
                        <td><?= esc($d['alokasi_waktu']) ?></td>
                        <?php foreach($monthsArr as $m): ?>
                            <?php for($w=1; $w<=5; $w++): 
                                $val = isset($alokasiJson[$m][$w]) ? $alokasiJson[$m][$w] : '';
                                // Highlight empty or 0 if we want, but better to just show numbers
                            ?>
                                <td><?= esc($val) ?></td>
                            <?php endfor; ?>
                        <?php endforeach; ?>
                        <td class="text-left"><?= esc($d['keterangan'] ?: '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="nama"><?= esc($sekolah['kepala_sekolah'] ?? 'Nama Kepala Sekolah') ?></div>
            <div class="nip">NIP. <?= esc($sekolah['nip_kepala_sekolah'] ?? '-') ?></div>
        </div>
        <div class="ttd-box">
            <p>Guru Mata Pelajaran,<br>&nbsp;</p>
            <div class="nama"><?= esc($guru['nama_lengkap']) ?></div>
            <div class="nip">NIP. <?= esc($guru['nip'] ?? '-') ?></div>
        </div>
    </div>

</body>
</html>
