<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; margin: 0; padding: 20px; color: #000; }
        .kop-surat { display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .kop-teks { text-align: center; }
        .kop-teks h1 { margin: 0; font-size: 16pt; font-weight: bold; text-transform: uppercase; }
        .kop-teks h2 { margin: 5px 0 0; font-size: 18pt; font-weight: bold; text-transform: uppercase; }
        .kop-teks p { margin: 5px 0 0; font-size: 11pt; }
        
        .doc-title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; text-decoration: underline; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .ttd-container { display: flex; justify-content: space-between; margin-top: 50px; }
        .ttd-box { text-align: center; width: 300px; }
        .ttd-box p { margin: 0 0 70px 0; }
        .ttd-box .nama { font-weight: bold; text-decoration: underline; margin-bottom: 2px; }
        .ttd-box .nip { margin: 0; }
        
        @media print {
            @page { size: A4 landscape; margin: 1.5cm; }
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()" style="padding:10px 20px; font-size:14px; cursor:pointer;">🖨️ Cetak Dokumen</button>
        <p style="color:red; font-family:sans-serif; font-size:12px;">*Gunakan format Lanskap (Landscape) dan kertas A4 pada pengaturan printer.</p>
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
        JURNAL MENGAJAR (AGENDA HARIAN GURU)
    </div>

    <table class="info-table">
        <tr>
            <td width="150"><strong>Nama Guru</strong></td>
            <td width="10">:</td>
            <td><?= esc($guru['nama_lengkap']) ?></td>
            <td width="150"><strong>Bulan / Tahun</strong></td>
            <td width="10">:</td>
            <td><?= date('F', mktime(0,0,0,$bulan,10)) ?> <?= esc($tahun) ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Hari/Tanggal</th>
                <th width="8%">Jam Ke</th>
                <th width="12%">Kelas/Mapel</th>
                <th width="35%">Materi Pokok / Pembahasan</th>
                <th width="15%">Catatan Kejadian</th>
                <th width="15%">Siswa Absen</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($jurnal)): ?>
                <tr><td colspan="7" style="text-align:center;">Tidak ada data jurnal pada bulan ini.</td></tr>
            <?php else: ?>
                <?php $no=1; foreach($jurnal as $j): ?>
                <tr>
                    <td style="text-align:center;"><?= $no++ ?></td>
                    <td><?= esc($j['hari']) ?>, <?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                    <td style="text-align:center;"><?= esc($j['jam_ke'] ?: '-') ?></td>
                    <td><strong><?= esc($j['nama_kelas']) ?></strong><br><?= esc($j['nama_mapel']) ?></td>
                    <td><?= nl2br(esc($j['materi_pembahasan'])) ?></td>
                    <td><?= esc($j['catatan_kejadian'] ?: '-') ?></td>
                    <td><?= esc($j['siswa_absen'] ?: '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

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
