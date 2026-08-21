<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Tombol kembali & info ujian -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('ulangan') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div>
        <h4 style="color:white; margin:0;"><?= esc($ulangan['judul']) ?></h4>
        <small class="text-lms-muted">KKM: <strong style="color:#F59E0B;"><?= $ulangan['kkm'] ?></strong> &nbsp;|&nbsp;
            Durasi: <?= $ulangan['durasi'] ?> menit</small>
    </div>
</div>

<!-- Kartu statistik -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['label'=>'Peserta',     'val'=> $totalSiswa,  'icon'=>'bi-people',      'color'=>'#818CF8'],
        ['label'=>'Lulus',       'val'=> $jumlahLulus, 'icon'=>'bi-patch-check', 'color'=>'#22C55E'],
        ['label'=>'Tidak Lulus', 'val'=> $totalSiswa - $jumlahLulus, 'icon'=>'bi-x-circle', 'color'=>'#EF4444'],
        ['label'=>'Rata-rata',   'val'=> $rataRata,    'icon'=>'bi-bar-chart',   'color'=>'#F59E0B'],
    ];
    foreach ($cards as $c):
    ?>
    <div class="col-6 col-md-3">
        <div class="glass-panel text-center p-3">
            <i class="bi <?= $c['icon'] ?>" style="font-size:1.5rem;color:<?= $c['color'] ?>;"></i>
            <div style="font-size:1.8rem;font-weight:700;color:<?= $c['color'] ?>;margin:0.3rem 0;">
                <?= $c['val'] ?>
            </div>
            <small class="text-lms-muted"><?= $c['label'] ?></small>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Progress bar lulus vs tidak -->
<?php if ($totalSiswa > 0): ?>
<div class="glass-panel card mb-4">
    <div class="d-flex justify-content-between mb-2">
        <span style="font-size:0.875rem;color:var(--lms-text-muted);">Persentase Kelulusan</span>
        <strong style="color:white;"><?= round(($jumlahLulus / $totalSiswa) * 100) ?>%</strong>
    </div>
    <div style="background:rgba(239,68,68,0.3);border-radius:100px;height:12px;">
        <div style="background:#22C55E;width:<?= round(($jumlahLulus / $totalSiswa) * 100) ?>%;
                    height:100%;border-radius:100px;transition:width 0.8s ease;"></div>
    </div>
    <div class="d-flex justify-content-between mt-1">
        <small style="color:#22C55E;"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Lulus</small>
        <small style="color:#EF4444;"><i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>Tidak Lulus</small>
    </div>
</div>
<?php endif; ?>

<!-- Tabel rekap nilai -->
<div class="glass-panel card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0"><i class="bi bi-table me-2 text-accent"></i>Daftar Nilai Siswa</h5>
        <?php if ($adaUraian): ?>
        <span class="badge" style="background:rgba(245,158,11,0.2);color:#F59E0B;border:1px solid rgba(245,158,11,0.3);">
            <i class="bi bi-exclamation-triangle me-1"></i>Ada soal uraian — koreksi manual diperlukan
        </span>
        <?php endif; ?>
    </div>

    <?php if (empty($rekapList)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox" style="font-size:3rem;color:var(--lms-text-muted);"></i>
        <p class="text-lms-muted mt-3">Belum ada siswa yang mengerjakan ujian ini.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table datatable w-100 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>Waktu Selesai</th>
                    <th>Nilai</th>
                    <th>Status Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rekapList as $i => $r):
                    $lulus   = $r['nilai_akhir'] >= $ulangan['kkm'];
                    $pctBar  = min(100, $r['nilai_akhir']);
                    $barClr  = $lulus ? '#22C55E' : '#EF4444';
                ?>
                <tr>
                    <td class="text-lms-muted"><?= $i + 1 ?></td>
                    <td><strong style="color:white;"><?= esc($r['nama_lengkap']) ?></strong></td>
                    <td class="text-lms-muted"><?= esc($r['nis']) ?></td>
                    <td>
                        <small class="text-lms-muted">
                            <?= $r['waktu_selesai'] ? date('d/m/Y H:i', strtotime($r['waktu_selesai'])) : '-' ?>
                        </small>
                    </td>
                    <td style="min-width:130px;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="flex:1;background:rgba(255,255,255,0.08);border-radius:100px;height:8px;">
                                <div style="background:<?= $barClr ?>;width:<?= $pctBar ?>%;height:100%;border-radius:100px;"></div>
                            </div>
                            <strong style="color:<?= $barClr ?>;min-width:40px;text-align:right;">
                                <?= number_format($r['nilai_akhir'], 1) ?>
                            </strong>
                        </div>
                    </td>
                    <td>
                        <?php if ($r['status_penilaian'] === 'Menunggu Koreksi'): ?>
                        <span class="badge" style="background:rgba(245,158,11,0.2);color:#F59E0B;">
                            <i class="bi bi-hourglass-split me-1"></i>Tunggu Koreksi
                        </span>
                        <?php else: ?>
                        <span class="badge" style="background:rgba(<?= $lulus ? '34,197,94' : '239,68,68' ?>,0.15);color:<?= $lulus ? '#22C55E' : '#EF4444' ?>;">
                            <i class="bi bi-<?= $lulus ? 'check-circle' : 'x-circle' ?> me-1"></i>
                            <?= $lulus ? 'Lulus' : 'Tidak Lulus' ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($adaUraian && $r['status_penilaian'] === 'Menunggu Koreksi'): ?>
                        <a href="<?= base_url('ulangan/koreksi/' . $r['id']) ?>"
                           class="btn btn-sm btn-warning text-dark">
                            <i class="bi bi-pencil-square me-1"></i> Koreksi
                        </a>
                        <?php else: ?>
                        <span class="text-lms-muted small"><i class="bi bi-check2-all"></i> Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
