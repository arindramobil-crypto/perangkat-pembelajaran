<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Filter Kelas -->
<div class="glass-panel card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="card-title mb-1">Rekap Nilai per Kelas</h4>
            <p class="text-lms-muted small mb-0">Pilih kelas untuk melihat rata-rata nilai seluruh siswa.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <a href="<?= base_url('laporan/nilai-kelas') ?>" target="_blank"
               class="btn btn-sm" style="background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.25);color:#22C55E;">
                <i class="bi bi-printer me-1"></i> Cetak Rekap Nilai
            </a>
            <a href="<?= base_url('laporan/absensi') ?>" target="_blank"
               class="btn btn-sm" style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.25);color:#38BDF8;">
                <i class="bi bi-printer me-1"></i> Cetak Absensi
            </a>
            <form method="get" action="<?= base_url('buku-nilai') ?>" class="d-flex gap-2">
                <select name="kelas_id" class="form-select" style="min-width:180px;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelasId == $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>
</div>

<?php if (!$kelasId): ?>
<!-- Belum pilih kelas -->
<div class="glass-panel card text-center py-5">
    <i class="bi bi-bar-chart-line" style="font-size:3rem;color:var(--lms-text-muted);"></i>
    <p class="text-lms-muted mt-3 mb-0">Pilih kelas untuk menampilkan rekap nilai siswa.</p>
</div>

<?php elseif (empty($rekapData)): ?>
<div class="glass-panel card text-center py-5">
    <i class="bi bi-inbox" style="font-size:3rem;color:var(--lms-text-muted);"></i>
    <p class="text-lms-muted mt-3 mb-0">Tidak ada siswa terdaftar di kelas ini atau belum ada ujian.</p>
</div>

<?php else: ?>
<!-- Statistik kelas -->
<?php
$allRata = array_filter(array_column($rekapData, 'rata_rata'), fn($v) => $v !== null);
$rataKelas = count($allRata) > 0 ? round(array_sum($allRata) / count($allRata), 1) : 0;
$siswaAktif = count(array_filter($rekapData, fn($r) => $r['total_ujian'] > 0));
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#818CF8;"><?= count($rekapData) ?></div>
            <small class="text-lms-muted">Total Siswa</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#22C55E;"><?= $siswaAktif ?></div>
            <small class="text-lms-muted">Sudah Ujian</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <?php $warna = $rataKelas >= 75 ? '#22C55E' : ($rataKelas >= 60 ? '#F59E0B' : '#EF4444'); ?>
            <div style="font-size:1.8rem;font-weight:800;color:<?= $warna ?>;"><?= $rataKelas ?></div>
            <small class="text-lms-muted">Rata-rata Kelas</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <?php $lulusK = count(array_filter($rekapData, fn($r) => ($r['rata_rata'] ?? 0) >= 75)); ?>
            <div style="font-size:1.8rem;font-weight:800;color:#F59E0B;"><?= $lulusK ?></div>
            <small class="text-lms-muted">Rata ≥75</small>
        </div>
    </div>
</div>

<!-- Tabel rekap -->
<div class="glass-panel card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0"><i class="bi bi-people me-2 text-accent"></i>Daftar Nilai Siswa</h5>
        <small class="text-lms-muted">Klik <i class="bi bi-file-earmark-person" style="color:#818CF8;"></i> untuk cetak raport siswa</small>
    </div>
    <div class="table-responsive">
        <table class="table datatable w-100 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th class="text-center">Total Ujian</th>
                    <th style="min-width:160px;">Rata-rata Nilai</th>
                    <th class="text-center">Min</th>
                    <th class="text-center">Max</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Raport</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekapData as $i => $r):
                    $rataRata  = round($r['rata_rata'] ?? 0, 1);
                    $barClr    = $rataRata >= 75 ? '#22C55E' : ($rataRata >= 60 ? '#F59E0B' : '#EF4444');
                    $adaUjian  = $r['total_ujian'] > 0;
                ?>
                <tr>
                    <td class="text-lms-muted"><?= $i + 1 ?></td>
                    <td><code style="color:#818CF8;"><?= esc($r['nis']) ?></code></td>
                    <td><strong style="color:white;"><?= esc($r['nama_lengkap']) ?></strong></td>
                    <td class="text-center text-lms-muted"><?= $r['total_ujian'] ?></td>
                    <td>
                        <?php if ($adaUjian): ?>
                        <div class="d-flex align-items-center gap-2">
                            <div style="flex:1;background:rgba(255,255,255,0.08);border-radius:100px;height:8px;">
                                <div style="background:<?= $barClr ?>;width:<?= min(100,$rataRata) ?>%;height:100%;border-radius:100px;transition:width 0.6s;"></div>
                            </div>
                            <strong style="color:<?= $barClr ?>;min-width:38px;text-align:right;"><?= $rataRata ?></strong>
                        </div>
                        <?php else: ?>
                        <span class="text-lms-muted small">— Belum ujian</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center" style="color:<?= $adaUjian ? '#EF4444' : 'var(--lms-text-muted)' ?>;">
                        <strong><?= $adaUjian ? number_format($r['nilai_min'],1) : '—' ?></strong>
                    </td>
                    <td class="text-center" style="color:<?= $adaUjian ? '#22C55E' : 'var(--lms-text-muted)' ?>;">
                        <strong><?= $adaUjian ? number_format($r['nilai_max'],1) : '—' ?></strong>
                    </td>
                    <td class="text-center">
                        <a href="<?= base_url('laporan/raport-siswa/'.$r['siswa_id']) ?>" target="_blank"
                           title="Cetak Raport <?= esc($r['nama_lengkap']) ?>"
                           class="btn btn-sm" style="background:rgba(129,140,248,0.1);border:1px solid rgba(129,140,248,0.2);color:#818CF8;padding:3px 10px;">
                            <i class="bi bi-file-earmark-person"></i>
                        </a>
                    </td>
                    <td class="text-center">
                        <?php if (!$adaUjian): ?>
                        <span class="badge" style="background:rgba(255,255,255,0.05);color:var(--lms-text-muted);">Belum Ujian</span>
                        <?php elseif ($rataRata >= 75): ?>
                        <span class="badge" style="background:rgba(34,197,94,0.15);color:#22C55E;"><i class="bi bi-check-circle me-1"></i>Baik</span>
                        <?php elseif ($rataRata >= 60): ?>
                        <span class="badge" style="background:rgba(245,158,11,0.15);color:#F59E0B;"><i class="bi bi-dash-circle me-1"></i>Cukup</span>
                        <?php else: ?>
                        <span class="badge" style="background:rgba(239,68,68,0.15);color:#EF4444;"><i class="bi bi-x-circle me-1"></i>Perlu Bimbingan</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
