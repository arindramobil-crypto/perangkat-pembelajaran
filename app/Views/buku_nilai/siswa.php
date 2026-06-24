<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Statistik global
$nilaiWarna = $rataGlobal >= 75 ? '#22C55E' : ($rataGlobal >= 60 ? '#F59E0B' : '#EF4444');
$totalMapel = count($perMapel);
$lulusCount = 0;
foreach ($perMapel as $m) {
    if ($m['rata_rata'] >= 75) $lulusCount++;
}
?>

<!-- Tombol Cetak Raport -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 style="color:white;font-weight:700;margin:0;">Buku Nilai Saya</h4>
        <small class="text-lms-muted">Rekap seluruh hasil ujian yang pernah dikerjakan</small>
    </div>
    <a href="<?= base_url('laporan/raport-siswa/' . $siswa['id']) ?>" target="_blank"
       class="btn btn-primary" style="display:flex;align-items:center;gap:8px;">
        <i class="bi bi-printer"></i> Cetak Raport Saya
    </a>
</div>



<!-- Kartu statistik global -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:<?= $nilaiWarna ?>;"><?= $rataGlobal ?></div>
            <small class="text-lms-muted">Rata-rata Global</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#818CF8;"><?= $totalUjian ?></div>
            <small class="text-lms-muted">Total Ujian</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#22C55E;"><?= $totalMapel ?></div>
            <small class="text-lms-muted">Mata Pelajaran</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <div style="font-size:1.8rem;font-weight:800;color:#F59E0B;"><?= $lulusCount ?>/<?= $totalMapel ?></div>
            <small class="text-lms-muted">Mapel ≥75 (Lulus)</small>
        </div>
    </div>
</div>

<!-- Per Mata Pelajaran -->
<?php if (empty($perMapel)): ?>
<div class="glass-panel card text-center py-5">
    <div style="font-size:3rem;">📊</div>
    <h5 style="color:white;margin-top:1rem;">Belum Ada Nilai</h5>
    <p class="text-lms-muted">Anda belum mengerjakan ujian apapun. Nilai akan muncul di sini setelah mengerjakan ujian.</p>
</div>
<?php else: ?>

<?php foreach ($perMapel as $kode => $mapel): ?>
<?php
$rrMapel  = $mapel['rata_rata'];
$barClr   = $rrMapel >= 75 ? '#22C55E' : ($rrMapel >= 60 ? '#F59E0B' : '#EF4444');
?>
<div class="glass-panel card mb-4">
    <!-- Header mapel -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-3">
            <div style="width:40px;height:40px;border-radius:8px;background:rgba(129,140,248,0.15);
                        display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-book" style="color:#818CF8;font-size:1.1rem;"></i>
            </div>
            <div>
                <strong style="color:white;font-size:1rem;"><?= esc($mapel['nama_mapel']) ?></strong><br>
                <small class="text-lms-muted"><?= $mapel['total'] ?> ujian dikerjakan</small>
            </div>
        </div>
        <!-- Rata-rata mapel -->
        <div class="text-end">
            <div style="font-size:1.4rem;font-weight:700;color:<?= $barClr ?>;"><?= $rrMapel ?></div>
            <small class="text-lms-muted">Rata-rata</small>
        </div>
    </div>

    <!-- Mini progress bar rata-rata -->
    <div style="background:rgba(255,255,255,0.06);border-radius:4px;height:6px;margin-bottom:1.2rem;">
        <div style="background:<?= $barClr ?>;width:<?= min(100,$rrMapel) ?>%;height:100%;border-radius:4px;transition:width 0.8s;"></div>
    </div>

    <!-- Tabel ujian per mapel -->
    <div class="table-responsive">
        <table class="table mb-0" style="font-size:0.875rem;">
            <thead>
                <tr style="border-color:rgba(255,255,255,0.06);">
                    <th class="text-lms-muted fw-normal">Judul Ujian</th>
                    <th class="text-lms-muted fw-normal">Tipe</th>
                    <th class="text-lms-muted fw-normal">Tanggal</th>
                    <th class="text-lms-muted fw-normal text-center">Status</th>
                    <th class="text-lms-muted fw-normal text-end">Nilai</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mapel['ujian'] as $u): ?>
                <?php
                $lulus  = $u['nilai_akhir'] >= $u['kkm'];
                $tunggu = $u['status_penilaian'] === 'Menunggu Koreksi';
                ?>
                <tr style="border-color:rgba(255,255,255,0.04);">
                    <td style="color:white;"><?= esc($u['judul']) ?></td>
                    <td>
                        <span class="badge" style="background:rgba(129,140,248,0.15);color:#818CF8;font-size:0.7rem;">
                            <?= esc($u['tipe']) ?>
                        </span>
                    </td>
                    <td class="text-lms-muted">
                        <?= $u['waktu_selesai'] ? date('d M Y', strtotime($u['waktu_selesai'])) : '-' ?>
                    </td>
                    <td class="text-center">
                        <?php if ($tunggu): ?>
                        <span class="badge" style="background:rgba(245,158,11,0.15);color:#F59E0B;">Tunggu Koreksi</span>
                        <?php elseif ($lulus): ?>
                        <span class="badge" style="background:rgba(34,197,94,0.15);color:#22C55E;">Lulus</span>
                        <?php else: ?>
                        <span class="badge" style="background:rgba(239,68,68,0.15);color:#EF4444;">Belum Lulus</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <strong style="color:<?= $lulus ? '#22C55E' : '#EF4444' ?>;font-size:1rem;">
                            <?= number_format($u['nilai_akhir'], 1) ?>
                        </strong>
                        <span class="text-lms-muted" style="font-size:0.75rem;">/100</span>
                    </td>
                    <td>
                        <a href="<?= base_url('ulangan/hasil/'.$u['js_id']) ?>"
                           class="btn btn-sm" style="background:rgba(129,140,248,0.1);border:1px solid rgba(129,140,248,0.2);color:#818CF8;padding:0.25rem 0.6rem;">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
