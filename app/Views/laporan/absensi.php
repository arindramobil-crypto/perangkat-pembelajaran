<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= esc($title) ?></title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 10pt; color: #1a1a1a; background: #f5f5f5; }

@media screen {
    .screen-only { display: block; }
    .wrap        { max-width: 1200px; margin: 20px auto; padding: 0 16px; }
    .print-area  { background: white; padding: 28px; box-shadow: 0 2px 24px rgba(0,0,0,0.12); border-radius: 8px; margin-top: 0; overflow-x: auto; }
    .filter-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 16px; box-shadow: 0 1px 6px rgba(0,0,0,0.06); }
    .btn-print   { background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 9px 20px; font-size: 0.85rem; font-weight: 700; cursor: pointer; }
    .btn-back    { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 18px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-block; }
    label { font-size: 0.78rem; font-weight: 600; color: #475569; display: block; margin-bottom: 3px; }
    select, input[type="month"] { width: 100%; padding: 7px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.82rem; background: white; }
    .row { display: flex; gap: 14px; flex-wrap: wrap; }
    .col { flex: 1; min-width: 140px; }
    .empty-state { text-align: center; padding: 48px; color: #94a3b8; }
}
@media print {
    .screen-only { display: none !important; }
    body  { background: white; font-size: 9pt; }
    .wrap { padding: 0; margin: 0; }
    .print-area { padding: 0; box-shadow: none; }
    @page { margin: 1.5cm 1cm 1.5cm 1.5cm; size: A4 landscape; }
    tr { page-break-inside: avoid; }
}

/* Kop Surat */
.kop { display: flex; align-items: center; gap: 14px; border-bottom: 3px double #1a1a1a; padding-bottom: 10px; margin-bottom: 12px; }
.kop img { width: 65px; height: 65px; object-fit: contain; flex-shrink: 0; }
.kop-text { flex: 1; text-align: center; }
.kop-text .nama  { font-size: 14pt; font-weight: 800; text-transform: uppercase; }
.kop-text .alamat { font-size: 8pt; color: #555; margin-top: 2px; }

.lap-title { text-align: center; margin-bottom: 10px; }
.lap-title h3 { font-size: 11pt; font-weight: 800; text-transform: uppercase; }
.lap-title p  { font-size: 9pt; color: #444; margin-top: 2px; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; margin-bottom: 12px; font-size: 9pt; }
.info-row  { display: flex; gap: 4px; padding: 2px 0; }
.info-row .lbl { width: 110px; font-weight: 600; flex-shrink: 0; }

/* Tabel Absensi */
table { width: 100%; border-collapse: collapse; font-size: 8pt; }
thead th { background: #1e40af; color: white; padding: 5px 3px; text-align: center; border: 1px solid #1e3a8a; font-size: 7.5pt; }
thead th.text-left { text-align: left; padding-left: 6px; }
tbody td { border: 1px solid #cbd5e1; padding: 4px 3px; text-align: center; font-size: 8pt; }
tbody td.text-left { text-align: left; padding-left: 6px; }
tbody tr:nth-child(even) td { background: #f8fafc; }

/* Status cells */
.H  { color: #15803d; font-weight: 700; }
.S  { color: #2563eb; font-weight: 700; }
.I  { color: #d97706; font-weight: 700; }
.A  { color: #dc2626; font-weight: 700; }
.dash { color: #94a3b8; }

.sum-h { background: #dcfce7 !important; color: #15803d; font-weight: 700; }
.sum-s { background: #dbeafe !important; color: #2563eb; font-weight: 700; }
.sum-i { background: #fef3c7 !important; color: #d97706; font-weight: 700; }
.sum-a { background: #fee2e2 !important; color: #dc2626; font-weight: 700; }

.legend { display: flex; gap: 20px; margin-top: 8px; font-size: 8pt; }
.legend span { display: flex; align-items: center; gap: 4px; }

.ttd-area { display: flex; justify-content: space-between; margin-top: 20px; font-size: 9pt; }
.ttd-box  { text-align: center; min-width: 150px; }
.ttd-box .garis { border-bottom: 1px solid #1a1a1a; margin: 48px 10px 4px; }
.print-date { font-size: 7.5pt; color: #666; text-align: right; margin-top: 12px; }
</style>
</head>
<body>
<div class="wrap">

<!-- Filter (layar) -->
<div class="screen-only filter-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h5 style="margin:0;font-size:0.9rem;font-weight:700;color:#475569;">⚙️ Filter Laporan Absensi</h5>
        <div style="display:flex;gap:8px;">
            <a href="<?= base_url('presensi') ?>" class="btn-back">← Kembali</a>
            <?php if ($kelasId && !empty($jadwalList)): ?>
            <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
            <?php endif; ?>
        </div>
    </div>
    <form method="get">
        <div class="row">
            <div class="col">
                <label>Pilih Kelas</label>
                <select name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelasId == $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label>Bulan</label>
                <input type="month" name="bulan" value="<?= esc($bulan) ?>">
            </div>
            <div class="col" style="max-width:110px;display:flex;align-items:flex-end;">
                <button type="submit" class="btn-print" style="width:100%;padding:8px;">Tampilkan</button>
            </div>
        </div>
    </form>
</div>

<!-- Print Area -->
<?php if ($kelasId && $kelasInfo): ?>
<div class="print-area">

    <!-- Kop -->
    <div class="kop">
        <?php if (!empty($sekolah['logo'])): ?>
        <img src="<?= base_url('uploads/logo/' . $sekolah['logo']) ?>" alt="Logo">
        <?php else: ?>
        <div style="width:65px;height:65px;background:#1e40af;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:1.4rem;font-weight:800;flex-shrink:0;">
            <?= strtoupper(substr($sekolah['nama_sekolah'] ?? 'S', 0, 1)) ?>
        </div>
        <?php endif; ?>
        <div class="kop-text">
            <div style="font-size:7.5pt;color:#555;text-transform:uppercase;">Pemerintah Provinsi Jawa Timur</div>
            <div class="nama"><?= esc($sekolah['nama_sekolah'] ?? 'Nama Sekolah') ?></div>
            <div class="alamat"><?= esc(($sekolah['alamat'] ?? '') . (!empty($sekolah['kota']) ? ', ' . $sekolah['kota'] : '')) ?></div>
        </div>
    </div>

    <!-- Judul -->
    <div class="lap-title">
        <h3>Rekapitulasi Absensi Siswa</h3>
        <p>
            Kelas <?= esc($kelasInfo['nama_kelas']) ?> —
            <?= date('F Y', strtotime($bulan . '-01')) ?>
        </p>
    </div>

    <!-- Info -->
    <div class="info-grid">
        <div>
            <div class="info-row"><span class="lbl">Kelas</span><span>: <?= esc($kelasInfo['nama_kelas']) ?></span></div>
            <div class="info-row"><span class="lbl">Wali Kelas</span><span>: <?= esc($kelasInfo['wali_kelas'] ?? '—') ?></span></div>
        </div>
        <div>
            <div class="info-row"><span class="lbl">Bulan</span><span>: <?= date('F Y', strtotime($bulan . '-01')) ?></span></div>
            <div class="info-row"><span class="lbl">Jumlah Pertemuan</span><span>: <?= count($jadwalList) ?> kali</span></div>
        </div>
    </div>

    <?php if (empty($jadwalList)): ?>
    <div class="empty-state">Belum ada data presensi untuk periode ini.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="no-col" style="width:26px;">No</th>
                <th rowspan="2" class="text-left" style="min-width:40px;width:55px;">NIS</th>
                <th rowspan="2" class="text-left" style="min-width:120px;">Nama Siswa</th>
                <?php foreach ($jadwalList as $i => $p): ?>
                <th title="<?= esc($p['nama_mapel']) ?> — <?= date('d/m', strtotime($p['tanggal'])) ?>">
                    <?= date('d/m', strtotime($p['tanggal'])) ?>
                </th>
                <?php endforeach; ?>
                <th style="background:#065f46;min-width:24px;">H</th>
                <th style="background:#1d4ed8;min-width:24px;">S</th>
                <th style="background:#92400e;min-width:24px;">I</th>
                <th style="background:#991b1b;min-width:24px;">A</th>
                <th style="background:#1e3a8a;min-width:35px;">%</th>
            </tr>
            <tr>
                <?php foreach ($jadwalList as $p): ?>
                <th style="font-size:7pt;font-weight:400;background:#1e3a8a;" title="<?= esc($p['nama_mapel']) ?>">
                    <?= esc(mb_strimwidth($p['nama_mapel'] ?? '', 0, 4, '')) ?>
                </th>
                <?php endforeach; ?>
                <th style="background:#1e3a8a;" colspan="5"></th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; foreach ($siswaData as $row):
            $total = $row['hadir'] + $row['sakit'] + $row['izin'] + $row['alfa'];
            $pct   = $total > 0 ? round($row['hadir'] / $total * 100) : 0;
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td class="text-left"><?= esc($row['nis']) ?></td>
            <td class="text-left"><?= esc($row['nama']) ?></td>
            <?php foreach ($jadwalList as $p): ?>
            <?php $st = $row['presensi'][$p['presensi_id']] ?? '-'; ?>
            <td class="<?= $st === 'Hadir' ? 'H' : ($st === 'Sakit' ? 'S' : ($st === 'Izin' ? 'I' : ($st === 'Alfa' ? 'A' : 'dash'))) ?>">
                <?= $st === 'Hadir' ? 'H' : ($st === 'Sakit' ? 'S' : ($st === 'Izin' ? 'I' : ($st === 'Alfa' ? 'A' : '·'))) ?>
            </td>
            <?php endforeach; ?>
            <td class="sum-h"><?= $row['hadir'] ?></td>
            <td class="sum-s"><?= $row['sakit'] ?></td>
            <td class="sum-i"><?= $row['izin']  ?></td>
            <td class="sum-a"><?= $row['alfa']  ?></td>
            <td style="font-weight:700;color:<?= $pct >= 75 ? '#15803d' : ($pct >= 50 ? '#d97706' : '#dc2626') ?>;"><?= $pct ?>%</td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="legend">
        <span><strong class="H">H</strong> = Hadir</span>
        <span><strong class="S">S</strong> = Sakit</span>
        <span><strong class="I">I</strong> = Izin</span>
        <span><strong class="A">A</strong> = Alfa/Tanpa Keterangan</span>
        <span>· = Belum ada data</span>
    </div>
    <?php endif; ?>

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box">
            Mengetahui,
            <div style="font-weight:700;">Kepala Sekolah</div>
            <div class="garis"></div>
            <div><?= esc($sekolah['kepala_sekolah'] ?? '.................................') ?></div>
            <?php if (!empty($sekolah['nip_kepsek'])): ?>
            <div style="font-size:7.5pt;">NIP. <?= esc($sekolah['nip_kepsek']) ?></div>
            <?php endif; ?>
        </div>
        <div class="ttd-box">
            <?= esc($kelasInfo['kota'] ?? ($sekolah['kota'] ?? '')), ', ', date('d F Y') ?>
            <div style="font-weight:700;">Wali Kelas</div>
            <div class="garis"></div>
            <div><?= esc($kelasInfo['wali_kelas'] ?? '.................................') ?></div>
        </div>
    </div>
    <div class="print-date">Dicetak: <?= date('d F Y, H:i') ?> WIB</div>
</div>

<?php else: ?>
<div class="print-area">
    <div class="empty-state">
        <div style="font-size:2rem;margin-bottom:10px;">📋</div>
        <p style="font-size:1rem;font-weight:600;margin-bottom:6px;">Pilih Kelas & Bulan</p>
        <p>Gunakan filter di atas untuk menampilkan rekap absensi.</p>
    </div>
</div>
<?php endif; ?>

</div>
</body>
</html>
