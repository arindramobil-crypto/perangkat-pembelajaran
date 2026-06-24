<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title) ?></title>
<style>
/* ── Reset & Font ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Arial', sans-serif; font-size: 11pt; color: #1a1a1a; background: #f5f5f5; }

/* ── Screen Only: Filter Form ── */
@media screen {
    .screen-only { display: block; }
    .print-area  { max-width: 1100px; margin: 0 auto; background: white; padding: 32px; box-shadow: 0 2px 24px rgba(0,0,0,0.12); border-radius: 8px; }
    .filter-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 6px rgba(0,0,0,0.06); }
    .filter-card h5 { font-size: 0.9rem; color: #475569; margin-bottom: 12px; font-weight: 700; }
    .btn-print { background: #4F46E5; color: white; border: none; border-radius: 8px; padding: 10px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-print:hover { background: #4338CA; }
    .btn-back  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 20px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .wrap { max-width: 1100px; margin: 20px auto; padding: 0 16px; }
    label { font-size: 0.8rem; font-weight: 600; color: #475569; display: block; margin-bottom: 4px; }
    select, input { width: 100%; padding: 8px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.85rem; background: white; }
    .row { display: flex; gap: 16px; flex-wrap: wrap; }
    .col { flex: 1; min-width: 160px; }
    .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
}

/* ── Print Only ── */
@media print {
    .screen-only { display: none !important; }
    body  { background: white; font-size: 10pt; }
    .print-area { padding: 0; box-shadow: none; max-width: 100%; }
    .wrap { padding: 0; margin: 0; }
    @page { margin: 1.5cm 1.5cm 1.5cm 2cm; size: A4 landscape; }
    tr { page-break-inside: avoid; }
}

/* ── Kop Surat ── */
.kop { display: flex; align-items: center; gap: 16px; border-bottom: 3px double #1a1a1a; padding-bottom: 12px; margin-bottom: 14px; }
.kop img { width: 70px; height: 70px; object-fit: contain; }
.kop-text { flex: 1; text-align: center; }
.kop-text .nama-sekolah { font-size: 15pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.02em; }
.kop-text .alamat { font-size: 8.5pt; color: #555; margin-top: 2px; }
.kop-text .nss { font-size: 8pt; color: #666; }

/* ── Judul Laporan ── */
.lap-title { text-align: center; margin-bottom: 12px; }
.lap-title h3 { font-size: 12pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
.lap-title p  { font-size: 9.5pt; color: #444; margin-top: 3px; }

/* ── Info Box ── */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 24px; margin-bottom: 14px; font-size: 9.5pt; }
.info-row  { display: flex; gap: 6px; padding: 2px 0; }
.info-row .label { width: 130px; font-weight: 600; color: #333; flex-shrink: 0; }
.info-row .val   { flex: 1; }

/* ── Tabel Nilai ── */
table { width: 100%; border-collapse: collapse; font-size: 9pt; }
thead tr th { background: #1e40af; color: white; padding: 6px 4px; text-align: center; font-weight: 700; font-size: 8.5pt; border: 1px solid #1e3a8a; }
thead tr th.text-left { text-align: left; padding-left: 8px; }
tbody tr td { border: 1px solid #cbd5e1; padding: 5px 4px; text-align: center; font-size: 8.5pt; }
tbody tr td.text-left { text-align: left; padding-left: 8px; }
tbody tr:nth-child(even) td { background: #f8fafc; }
tbody tr:hover td { background: #eff6ff; }
.no-col  { width: 30px; }
.nis-col { width: 80px; }
.num-cell { font-weight: 600; }
.lulus    { color: #15803d; font-weight: 700; }
.gagal    { color: #dc2626; font-weight: 700; }
.rata-row td { background: #dbeafe !important; font-weight: 700; font-size: 9pt; }

/* ── Footer ── */
.ttd-area { display: flex; justify-content: space-between; margin-top: 24px; font-size: 9.5pt; }
.ttd-box  { text-align: center; min-width: 160px; }
.ttd-box .garis { border-bottom: 1px solid #1a1a1a; margin: 50px 10px 4px; }
.ttd-box .jabatan { font-weight: 700; }
.print-date { font-size: 8pt; color: #666; margin-top: 16px; text-align: right; }
</style>
</head>
<body>

<!-- ═══ FILTER FORM (layar saja) ═══ -->
<div class="wrap">
<div class="screen-only filter-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h5 style="margin:0;">⚙️ Filter Laporan Nilai Kelas</h5>
        <div style="display:flex;gap:8px;">
            <a href="<?= base_url('buku-nilai') ?>" class="btn-back">← Kembali</a>
            <?php if ($kelasId && $tahunId): ?>
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
                        <?= esc($k['nama_kelas']) ?> — <?= esc($k['jurusan'] ?? '') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label>Tahun Pelajaran</label>
                <select name="tahun_id" required>
                    <option value="">-- Pilih Tahun --</option>
                    <?php foreach ($tahunList as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $tahunId == $t['id'] ? 'selected' : '' ?>>
                        <?= esc($t['tahun']) ?> — <?= esc($t['semester']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col" style="max-width:120px;display:flex;align-items:flex-end;">
                <button type="submit" class="btn-print" style="width:100%;padding:8px;">Tampilkan</button>
            </div>
        </div>
    </form>
</div>

<!-- ═══ PRINT AREA ═══ -->
<?php if ($kelasId && $tahunId && $kelasInfo && $tahunInfo): ?>
<div class="print-area">

    <!-- Kop -->
    <div class="kop">
        <?php if (!empty($sekolah['logo'])): ?>
        <img src="<?= base_url('uploads/logo/' . $sekolah['logo']) ?>" alt="Logo">
        <?php else: ?>
        <div style="width:70px;height:70px;background:#1e40af;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:1.5rem;font-weight:800;flex-shrink:0;">
            <?= strtoupper(substr($sekolah['nama_sekolah'] ?? 'S', 0, 1)) ?>
        </div>
        <?php endif; ?>
        <div class="kop-text">
            <div style="font-size:8pt;color:#555;text-transform:uppercase;letter-spacing:0.06em;">Pemerintah Provinsi Jawa Timur</div>
            <div class="nama-sekolah"><?= esc($sekolah['nama_sekolah'] ?? 'Nama Sekolah') ?></div>
            <div class="alamat"><?= esc($sekolah['alamat'] ?? '') ?><?= !empty($sekolah['kota']) ? ', ' . esc($sekolah['kota']) : '' ?></div>
            <div class="nss">
                <?php if (!empty($sekolah['nss'])): ?>NSS: <?= esc($sekolah['nss']) ?><?php endif; ?>
                <?php if (!empty($sekolah['npsn'])): ?> · NPSN: <?= esc($sekolah['npsn']) ?><?php endif; ?>
                <?php if (!empty($sekolah['email'])): ?> · <?= esc($sekolah['email']) ?><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Judul -->
    <div class="lap-title">
        <h3>Rekapitulasi Nilai Ujian / Penilaian</h3>
        <p>Kelas <?= esc($kelasInfo['nama_kelas']) ?> — Tahun Pelajaran <?= esc($tahunInfo['tahun']) ?> Semester <?= esc($tahunInfo['semester']) ?></p>
    </div>

    <!-- Info -->
    <div class="info-grid">
        <div>
            <div class="info-row"><span class="label">Kelas</span><span>: <?= esc($kelasInfo['nama_kelas']) ?> (<?= esc($kelasInfo['jurusan'] ?? '') ?>)</span></div>
            <div class="info-row"><span class="label">Wali Kelas</span><span>: <?= esc($kelasInfo['wali_kelas'] ?? '—') ?></span></div>
            <div class="info-row"><span class="label">Tahun Pelajaran</span><span>: <?= esc($tahunInfo['tahun']) ?></span></div>
        </div>
        <div>
            <div class="info-row"><span class="label">Semester</span><span>: <?= esc($tahunInfo['semester']) ?></span></div>
            <div class="info-row"><span class="label">Jumlah Siswa</span><span>: <?= count($siswaData) ?> orang</span></div>
            <div class="info-row"><span class="label">Jumlah Mapel</span><span>: <?= count($mapelList) ?> mata pelajaran</span></div>
        </div>
    </div>

    <!-- Tabel Nilai -->
    <?php if (empty($siswaData)): ?>
    <div class="empty-state">Belum ada data nilai untuk kelas ini.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th class="nis-col text-left">NIS</th>
                <th class="text-left" style="min-width:150px;">Nama Siswa</th>
                <?php foreach ($mapelList as $mp): ?>
                <th title="<?= esc($mp['nama_mapel']) ?>" style="min-width:60px;">
                    <?= esc($mp['kode_mapel']) ?>
                </th>
                <?php endforeach; ?>
                <th style="background:#1e3a8a;min-width:55px;">Rata<br>Global</th>
                <th style="background:#1e3a8a;min-width:55px;">Ket.</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; $sumGlobal = 0; $cntGlobal = 0;
        foreach ($siswaData as $row):
            $rata = $row['rata_global'];
            if ($rata !== null) { $sumGlobal += $rata; $cntGlobal++; }
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td class="text-left"><?= esc($row['nis']) ?></td>
            <td class="text-left"><?= esc($row['nama']) ?></td>
            <?php foreach ($mapelList as $mp): ?>
            <?php $nm = $row['nilai_mapel'][$mp['id']] ?? ['rata'=>null,'cnt'=>0]; ?>
            <td class="num-cell <?= $nm['rata'] !== null ? ($nm['rata'] >= 70 ? 'lulus' : 'gagal') : '' ?>">
                <?= $nm['rata'] !== null ? $nm['rata'] : '—' ?>
            </td>
            <?php endforeach; ?>
            <td class="num-cell <?= $rata !== null ? ($rata >= 70 ? 'lulus' : 'gagal') : '' ?>">
                <?= $rata !== null ? $rata : '—' ?>
            </td>
            <td class="<?= $rata !== null ? ($rata >= 70 ? 'lulus' : 'gagal') : '' ?>">
                <?= $rata !== null ? ($rata >= 70 ? 'Lulus' : 'Belum') : '—' ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <!-- Baris rata-rata kelas -->
        <?php if ($cntGlobal > 0): ?>
        <tr class="rata-row">
            <td colspan="3" class="text-left" style="padding-left:8px;font-weight:700;">Rata-rata Kelas</td>
            <?php foreach ($mapelList as $mp): ?>
            <?php
            $vals = array_filter(array_map(fn($r) => $r['nilai_mapel'][$mp['id']]['rata'] ?? null, $siswaData), fn($v) => $v !== null);
            $rataMapel = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : null;
            ?>
            <td class="num-cell"><?= $rataMapel ?? '—' ?></td>
            <?php endforeach; ?>
            <td class="num-cell"><?= round($sumGlobal / $cntGlobal, 1) ?></td>
            <td></td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Keterangan kode mapel -->
    <div style="margin-top:10px;font-size:8pt;color:#555;">
        <strong>Keterangan kode:</strong>
        <?= implode(' · ', array_map(fn($m) => $m['kode_mapel'].' = '.esc($m['nama_mapel']), $mapelList)) ?>
    </div>
    <?php endif; ?>

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box">
            <div>Mengetahui,</div>
            <div class="jabatan">Kepala Sekolah</div>
            <div class="garis"></div>
            <div><?= esc($sekolah['kepala_sekolah'] ?? '................................') ?></div>
            <?php if (!empty($sekolah['nip_kepsek'])): ?>
            <div style="font-size:8pt;">NIP. <?= esc($sekolah['nip_kepsek']) ?></div>
            <?php endif; ?>
        </div>
        <div class="ttd-box">
            <div>&nbsp;</div>
            <div class="jabatan">Wali Kelas</div>
            <div class="garis"></div>
            <div><?= esc($kelasInfo['wali_kelas'] ?? '................................') ?></div>
        </div>
    </div>

    <div class="print-date">
        Dicetak: <?= date('d F Y, H:i') ?> WIB
    </div>
</div>

<?php else: ?>
<div class="print-area">
    <div class="empty-state">
        <div style="font-size:2rem;margin-bottom:12px;">📊</div>
        <p style="font-size:1rem;font-weight:600;margin-bottom:6px;">Pilih Kelas & Tahun Pelajaran</p>
        <p>Gunakan filter di atas untuk menampilkan rekap nilai.</p>
    </div>
</div>
<?php endif; ?>
</div>
</body>
</html>
