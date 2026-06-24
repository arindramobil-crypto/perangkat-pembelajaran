<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= esc($title) ?></title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 11pt; color: #1a1a1a; background: #f0f4f8; }

@media screen {
    .wrap { max-width: 820px; margin: 24px auto; padding: 0 16px 40px; }
    .raport-card { background: white; border-radius: 10px; box-shadow: 0 2px 24px rgba(0,0,0,0.12); overflow: hidden; }
    .screen-only { display: block; }
    .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .btn-print { background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 10px 22px; font-size: 0.88rem; font-weight: 700; cursor: pointer; }
    .btn-back  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 18px; font-size: 0.88rem; font-weight: 600; text-decoration: none; }
}
@media print {
    .screen-only { display: none !important; }
    body  { background: white; }
    .wrap { padding: 0; margin: 0; }
    .raport-card { box-shadow: none; border-radius: 0; }
    @page { margin: 1.5cm 1.5cm; size: A4 portrait; }
}

/* Raport Header */
.raport-header { padding: 24px 28px 16px; border-bottom: 2px solid #1e40af; }
.kop { display: flex; align-items: center; gap: 16px; margin-bottom: 14px; }
.kop img { width: 68px; height: 68px; object-fit: contain; }
.kop-logo-placeholder { width: 68px; height: 68px; background: #1e40af; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: 800; flex-shrink: 0; }
.kop-text { flex: 1; text-align: center; }
.kop-text .nama  { font-size: 14pt; font-weight: 800; text-transform: uppercase; }
.kop-text .det   { font-size: 9pt; color: #555; margin-top: 2px; }

.raport-title { text-align: center; background: #1e40af; color: white; padding: 8px; margin: 0 -28px; }
.raport-title h2 { font-size: 13pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; }

/* Identitas Siswa */
.siswa-section { padding: 16px 28px; display: grid; grid-template-columns: 1fr 1fr; gap: 0 32px; border-bottom: 1px solid #e2e8f0; }
.id-row  { display: flex; gap: 6px; padding: 3px 0; font-size: 10pt; }
.id-row .lbl { width: 120px; font-weight: 600; color: #333; flex-shrink: 0; }
.id-row .val { flex: 1; border-bottom: 1px dotted #ccc; padding-bottom: 1px; }

/* Nilai Section */
.nilai-section { padding: 16px 28px; }
.section-title { font-size: 10pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #1e40af; margin-bottom: 10px; padding-bottom: 4px; border-bottom: 2px solid #dbeafe; }

table.nilai-tabel { width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 12px; }
.nilai-tabel thead th { background: #1e40af; color: white; padding: 7px 10px; text-align: center; font-weight: 700; }
.nilai-tabel thead th.text-left { text-align: left; }
.nilai-tabel tbody td { border: 1px solid #e2e8f0; padding: 7px 10px; }
.nilai-tabel tbody td.center { text-align: center; }
.nilai-tabel tbody tr:nth-child(even) td { background: #f8fafc; }
.lulus  { color: #15803d; font-weight: 700; }
.belum  { color: #dc2626; font-weight: 700; }
.no-data{ color: #94a3b8; text-align: center; font-style: italic; }

/* Kehadiran */
.kehadiran-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 14px; }
.kh-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; text-align: center; }
.kh-box .num { font-size: 1.5rem; font-weight: 800; }
.kh-box .lbl { font-size: 8pt; color: #666; margin-top: 2px; }

/* Progress bar */
.progress-wrap { margin-bottom: 6px; }
.progress-label { display: flex; justify-content: space-between; font-size: 9pt; margin-bottom: 4px; }
.progress-bar { height: 12px; background: #e2e8f0; border-radius: 100px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 100px; transition: width 0.5s; }

/* Predikat */
.predikat-box { padding: 12px 20px; border-radius: 8px; text-align: center; margin-bottom: 14px; border: 2px solid; }
.predikat-box .grade { font-size: 2.5rem; font-weight: 900; }
.predikat-box .desc  { font-size: 10pt; font-weight: 600; margin-top: 4px; }

/* TTD */
.ttd-area { display: grid; grid-template-columns: 1fr 1fr; gap: 0 40px; padding: 16px 28px; border-top: 1px solid #e2e8f0; font-size: 9.5pt; }
.ttd-box { text-align: center; }
.ttd-box .garis { border-bottom: 1px solid #1a1a1a; margin: 46px 12px 4px; }
.ttd-box .jabatan { font-weight: 700; }
.print-date { text-align: right; font-size: 8pt; color: #666; padding: 8px 28px 12px; }
</style>
</head>
<body>
<div class="wrap">

<!-- Action Bar (layar saja) -->
<div class="action-bar screen-only">
    <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
</div>

<div class="raport-card">

    <!-- Header / Kop -->
    <div class="raport-header">
        <div class="kop">
            <?php if (!empty($sekolah['logo'])): ?>
            <img src="<?= base_url('uploads/logo/' . $sekolah['logo']) ?>" alt="Logo">
            <?php else: ?>
            <div class="kop-logo-placeholder"><?= strtoupper(substr($sekolah['nama_sekolah'] ?? 'S', 0, 1)) ?></div>
            <?php endif; ?>
            <div class="kop-text">
                <div style="font-size:8pt;color:#888;text-transform:uppercase;">Pemerintah Provinsi Jawa Timur</div>
                <div class="nama"><?= esc($sekolah['nama_sekolah'] ?? 'Nama Sekolah') ?></div>
                <div class="det"><?= esc(($sekolah['alamat'] ?? '') . (!empty($sekolah['kota']) ? ', ' . $sekolah['kota'] : '')) ?></div>
                <?php if (!empty($sekolah['telp'])): ?>
                <div class="det">Telp. <?= esc($sekolah['telp']) ?> <?= !empty($sekolah['email']) ? '· ' . esc($sekolah['email']) : '' ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="raport-title">
            <h2>Laporan Hasil Belajar Siswa</h2>
        </div>
    </div>

    <!-- Identitas Siswa -->
    <div class="siswa-section">
        <div>
            <div class="id-row"><span class="lbl">Nama Siswa</span><span class="val"><?= esc($siswa['nama_lengkap']) ?></span></div>
            <div class="id-row"><span class="lbl">NIS</span><span class="val"><?= esc($siswa['nis'] ?? '—') ?></span></div>
            <div class="id-row"><span class="lbl">Kelas</span><span class="val"><?= esc($kelasInfo['nama_kelas'] ?? '—') ?><?= !empty($kelasInfo['jurusan']) ? ' — ' . esc($kelasInfo['jurusan']) : '' ?></span></div>
        </div>
        <div>
            <div class="id-row"><span class="lbl">Wali Kelas</span><span class="val"><?= esc($kelasInfo['wali_kelas'] ?? '—') ?></span></div>
            <div class="id-row"><span class="lbl">Tahun Pelajaran</span><span class="val"><?= date('Y') . '/' . (date('Y') + 1) ?></span></div>
            <div class="id-row"><span class="lbl">Tanggal Cetak</span><span class="val"><?= date('d F Y') ?></span></div>
        </div>
    </div>

    <!-- Nilai per Mapel -->
    <div class="nilai-section">
        <div class="section-title">📊 Rekapitulasi Nilai Ujian</div>

        <?php if (empty($nilaiPerMapel)): ?>
        <p class="no-data" style="padding:20px 0;">Belum ada nilai ujian yang tercatat.</p>
        <?php else: ?>
        <table class="nilai-tabel">
            <thead>
                <tr>
                    <th class="text-left" style="width:30px;">No</th>
                    <th class="text-left">Mata Pelajaran</th>
                    <th style="width:60px;">Jml Ujian</th>
                    <th style="width:65px;">Nilai Min</th>
                    <th style="width:65px;">Nilai Max</th>
                    <th style="width:75px;">Rata-rata</th>
                    <th style="width:50px;">KKM</th>
                    <th style="width:60px;">Ket.</th>
                </tr>
            </thead>
            <tbody>
            <?php $sumNilai = 0; $cntNilai = 0;
            foreach ($nilaiPerMapel as $i => $n):
                $lulus = $n['rata_rata'] >= $n['kkm'];
                $sumNilai += $n['rata_rata']; $cntNilai++;
            ?>
            <tr>
                <td class="center"><?= $i + 1 ?></td>
                <td><?= esc($n['nama_mapel']) ?></td>
                <td class="center"><?= $n['total_ujian'] ?></td>
                <td class="center"><?= $n['nilai_min'] ?? '—' ?></td>
                <td class="center"><?= $n['nilai_max'] ?? '—' ?></td>
                <td class="center" style="font-weight:700;font-size:11pt;color:<?= $lulus ? '#15803d' : '#dc2626' ?>;"><?= $n['rata_rata'] ?></td>
                <td class="center"><?= $n['kkm'] ?></td>
                <td class="center <?= $lulus ? 'lulus' : 'belum' ?>"><?= $lulus ? 'Lulus' : 'Belum' ?></td>
            </tr>
            <?php endforeach; ?>
            <!-- Rata global -->
            <?php if ($cntNilai > 0): ?>
            <tr style="background:#dbeafe;">
                <td colspan="5" style="font-weight:700;padding:7px 10px;">Rata-rata Keseluruhan</td>
                <td class="center" style="font-weight:800;font-size:12pt;color:<?= $rataGlobal >= 70 ? '#15803d' : '#dc2626' ?>;"><?= $rataGlobal ?></td>
                <td></td>
                <td class="center <?= $rataGlobal >= 70 ? 'lulus' : 'belum' ?>"><?= $rataGlobal >= 70 ? 'Lulus' : 'Belum' ?></td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Predikat -->
        <?php
        $predikat = '';
        $pColor   = '';
        $pBg      = '';
        if ($rataGlobal >= 90)      { $predikat = 'A — Sangat Baik';  $pColor = '#15803d'; $pBg = '#dcfce7'; $grade = 'A'; }
        elseif ($rataGlobal >= 80)  { $predikat = 'B — Baik';         $pColor = '#1d4ed8'; $pBg = '#dbeafe'; $grade = 'B'; }
        elseif ($rataGlobal >= 70)  { $predikat = 'C — Cukup';        $pColor = '#d97706'; $pBg = '#fef3c7'; $grade = 'C'; }
        elseif ($rataGlobal > 0)    { $predikat = 'D — Perlu Bimbingan'; $pColor = '#dc2626'; $pBg = '#fee2e2'; $grade = 'D'; }
        else { $grade = '—'; $predikat = 'Belum ada data'; $pColor = '#94a3b8'; $pBg = '#f1f5f9'; }
        ?>
        <div class="predikat-box" style="border-color:<?= $pColor ?>;background:<?= $pBg ?>;">
            <div class="grade" style="color:<?= $pColor ?>;"><?= $grade ?></div>
            <div class="desc" style="color:<?= $pColor ?>;"><?= $predikat ?></div>
            <div style="font-size:8.5pt;color:#666;margin-top:4px;">Rata-rata nilai ujian: <strong><?= $rataGlobal ?></strong></div>
        </div>
        <?php endif; ?>

        <!-- Kehadiran -->
        <div class="section-title" style="margin-top:14px;">📅 Rekap Kehadiran</div>
        <?php
        $h = (int)($kehadiran['hadir'] ?? 0);
        $s = (int)($kehadiran['sakit'] ?? 0);
        $iz = (int)($kehadiran['izin'] ?? 0);
        $a = (int)($kehadiran['alfa'] ?? 0);
        $total = $h + $s + $iz + $a;
        $pct = $pctHadir;
        $pctColor = $pct >= 75 ? '#15803d' : ($pct >= 50 ? '#d97706' : '#dc2626');
        ?>
        <div class="kehadiran-grid">
            <div class="kh-box" style="border-color:#86efac;background:#f0fdf4;">
                <div class="num" style="color:#15803d;"><?= $h ?></div>
                <div class="lbl">Hadir</div>
            </div>
            <div class="kh-box" style="border-color:#93c5fd;background:#eff6ff;">
                <div class="num" style="color:#1d4ed8;"><?= $s ?></div>
                <div class="lbl">Sakit</div>
            </div>
            <div class="kh-box" style="border-color:#fcd34d;background:#fffbeb;">
                <div class="num" style="color:#d97706;"><?= $iz ?></div>
                <div class="lbl">Izin</div>
            </div>
            <div class="kh-box" style="border-color:#fca5a5;background:#fef2f2;">
                <div class="num" style="color:#dc2626;"><?= $a ?></div>
                <div class="lbl">Alfa</div>
            </div>
        </div>
        <div class="progress-wrap">
            <div class="progress-label">
                <span style="font-weight:600;">Persentase Kehadiran</span>
                <strong style="color:<?= $pctColor ?>;"><?= $pct ?>%</strong>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $pctColor ?>;"></div>
            </div>
            <div style="font-size:8pt;color:#666;margin-top:4px;">Total <?= $total ?> pertemuan tercatat · Hadir <?= $h ?> kali</div>
        </div>
        <div style="font-size:9pt;color:<?= $pct >= 75 ? '#15803d' : '#dc2626' ?>;font-weight:700;text-align:center;padding:8px;background:<?= $pct >= 75 ? '#f0fdf4' : '#fef2f2' ?>;border-radius:6px;">
            <?= $pct >= 75 ? '✅ Kehadiran memenuhi syarat minimal (≥ 75%)' : '⚠️ Kehadiran di bawah syarat minimal (75%)' ?>
        </div>
    </div>

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box">
            Mengetahui,<br>
            <span class="jabatan">Kepala Sekolah</span>
            <div class="garis"></div>
            <strong><?= esc($sekolah['kepala_sekolah'] ?? '..............................') ?></strong>
            <?php if (!empty($sekolah['nip_kepsek'])): ?>
            <div style="font-size:8pt;">NIP. <?= esc($sekolah['nip_kepsek']) ?></div>
            <?php endif; ?>
        </div>
        <div class="ttd-box">
            <?= esc($sekolah['kota'] ?? '') ?>, <?= date('d F Y') ?><br>
            <span class="jabatan">Wali Kelas</span>
            <div class="garis"></div>
            <strong><?= esc($kelasInfo['wali_kelas'] ?? '..............................') ?></strong>
        </div>
    </div>

    <div class="print-date">Dicetak: <?= date('d F Y, H:i') ?> WIB</div>
</div><!-- end raport-card -->
</div><!-- end wrap -->
</body>
</html>
