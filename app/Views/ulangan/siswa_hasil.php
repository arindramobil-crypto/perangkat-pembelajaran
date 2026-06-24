<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$lulus       = $attempt['nilai_akhir'] >= $ulangan['kkm'];
$tungguKoreksi = $attempt['status_penilaian'] === 'Menunggu Koreksi';
$nilai        = (float)$attempt['nilai_akhir'];
$nilaiWarna  = $lulus ? '#22C55E' : '#EF4444';

// Durasi pengerjaan
$durMenit = '-';
if ($attempt['waktu_mulai'] && $attempt['waktu_selesai']) {
    $diff    = strtotime($attempt['waktu_selesai']) - strtotime($attempt['waktu_mulai']);
    $durMenit = floor($diff / 60) . ' mnt ' . ($diff % 60) . ' dtk';
}

// Hitung total benar & total soal
$totalSoal  = count($detailHasil);
$totalBenar = 0;
foreach ($detailHasil as $d) {
    if ($d['is_benar'] == 1) $totalBenar++;
}
?>

<!-- Tombol kembali -->
<div class="mb-4">
    <a href="<?= base_url('ulangan') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Ujian
    </a>
</div>

<!-- Header hasil -->
<div class="glass-panel card mb-4">
    <div class="row align-items-center g-4">
        <!-- Nilai besar -->
        <div class="col-auto text-center">
            <div style="width:110px;height:110px;border-radius:50%;
                        background: conic-gradient(<?= $nilaiWarna ?> <?= $nilai ?>%, rgba(255,255,255,0.08) 0);
                        display:flex;align-items:center;justify-content:center;
                        box-shadow:0 0 24px <?= $nilaiWarna ?>44;">
                <div style="width:86px;height:86px;border-radius:50%;
                             background:rgba(15,23,42,0.95);
                             display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <span style="font-size:1.6rem;font-weight:800;color:<?= $nilaiWarna ?>;">
                        <?= number_format($nilai, 0) ?>
                    </span>
                    <span style="font-size:0.65rem;color:var(--lms-text-muted);">NILAI</span>
                </div>
            </div>
        </div>
        <!-- Info ujian -->
        <div class="col">
            <h4 style="color:white;margin:0 0 0.3rem 0;"><?= esc($ulangan['judul']) ?></h4>
            <p class="text-lms-muted small mb-2">
                Diselesaikan: <?= $attempt['waktu_selesai'] ? date('d M Y, H:i', strtotime($attempt['waktu_selesai'])) : '-' ?>
                &nbsp;|&nbsp; Durasi: <?= $durMenit ?>
            </p>
            <!-- Status badge -->
            <?php if ($tungguKoreksi): ?>
            <span class="badge" style="background:rgba(245,158,11,0.2);color:#F59E0B;font-size:0.85rem;padding:0.5rem 1rem;">
                <i class="bi bi-hourglass-split me-1"></i> Menunggu Koreksi Uraian — nilai belum final
            </span>
            <?php elseif ($lulus): ?>
            <span class="badge" style="background:rgba(34,197,94,0.2);color:#22C55E;font-size:0.85rem;padding:0.5rem 1rem;">
                <i class="bi bi-trophy me-1"></i> LULUS — KKM <?= $ulangan['kkm'] ?>
            </span>
            <?php else: ?>
            <span class="badge" style="background:rgba(239,68,68,0.2);color:#EF4444;font-size:0.85rem;padding:0.5rem 1rem;">
                <i class="bi bi-x-circle me-1"></i> BELUM LULUS — KKM <?= $ulangan['kkm'] ?>
            </span>
            <?php endif; ?>
        </div>
        <!-- Mini stats -->
        <div class="col-auto">
            <div class="d-flex flex-column gap-3 text-center">
                <div class="glass-panel p-2 px-4">
                    <div style="font-size:1.3rem;font-weight:700;color:#22C55E;"><?= $totalBenar ?></div>
                    <div style="font-size:0.75rem;color:var(--lms-text-muted);">Benar</div>
                </div>
                <div class="glass-panel p-2 px-4">
                    <div style="font-size:1.3rem;font-weight:700;color:#EF4444;"><?= $totalSoal - $totalBenar ?></div>
                    <div style="font-size:0.75rem;color:var(--lms-text-muted);">Salah/Belum</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail per soal -->
<div class="glass-panel card">
    <h5 class="card-title mb-4"><i class="bi bi-list-check me-2 text-accent"></i>Pembahasan Per Soal</h5>

    <?php foreach ($detailHasil as $i => $d):
        $isUraian   = ($d['tipe_soal'] === 'Uraian');
        $isPG       = in_array($d['tipe_soal'], ['PG', 'Benar Salah']);
        $isComplex  = in_array($d['tipe_soal'], ['PG Kompleks', 'Menjodohkan']);
        $benar      = $d['is_benar'] == 1;
        $tunggu     = is_null($d['is_benar']);

        // Warna status soal
        if ($tunggu)      { $bdrClr = '#F59E0B'; $bgClr = 'rgba(245,158,11,0.05)'; }
        elseif ($benar)   { $bdrClr = '#22C55E'; $bgClr = 'rgba(34,197,94,0.05)'; }
        else              { $bdrClr = '#EF4444'; $bgClr = 'rgba(239,68,68,0.05)'; }

        // Opsi untuk PG
        $opsiMap = ['A'=>$d['opsi_a'],'B'=>$d['opsi_b'],'C'=>$d['opsi_c'],'D'=>$d['opsi_d'],'E'=>$d['opsi_e']];
    ?>
    <div class="mb-4 p-3 rounded" style="background:<?= $bgClr ?>;border-left:4px solid <?= $bdrClr ?>;">
        <!-- Header soal -->
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="d-flex align-items-center gap-2">
                <!-- Icon status -->
                <?php if ($tunggu): ?>
                <span style="width:28px;height:28px;border-radius:50%;background:rgba(245,158,11,0.2);
                              display:flex;align-items:center;justify-content:center;color:#F59E0B;font-size:0.9rem;">
                    <i class="bi bi-hourglass"></i>
                </span>
                <?php elseif ($benar): ?>
                <span style="width:28px;height:28px;border-radius:50%;background:rgba(34,197,94,0.2);
                              display:flex;align-items:center;justify-content:center;color:#22C55E;font-size:0.9rem;">
                    <i class="bi bi-check-lg"></i>
                </span>
                <?php else: ?>
                <span style="width:28px;height:28px;border-radius:50%;background:rgba(239,68,68,0.2);
                              display:flex;align-items:center;justify-content:center;color:#EF4444;font-size:0.9rem;">
                    <i class="bi bi-x-lg"></i>
                </span>
                <?php endif; ?>
                <strong style="color:white;">Soal <?= $i + 1 ?></strong>
                <span class="badge" style="background:rgba(255,255,255,0.08);color:var(--lms-text-muted);font-size:0.7rem;">
                    <?= esc($d['tipe_soal']) ?>
                </span>
            </div>
            <span style="font-size:0.85rem;color:<?= $bdrClr ?>;font-weight:600;">
                <?= number_format($d['skor'], 1) ?> / <?= $d['bobot'] ?> poin
            </span>
        </div>

        <!-- Pertanyaan -->
        <p style="color:white;line-height:1.6;margin-bottom:1rem;"><?= nl2br(esc($d['pertanyaan'])) ?></p>

        <?php if ($isUraian): ?>
        <!-- Jawaban Uraian -->
        <div class="mb-2">
            <small class="text-lms-muted d-block mb-1">Jawaban Anda:</small>
            <div class="p-2 rounded" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);
                         color:white;font-size:0.875rem;line-height:1.6;">
                <?= nl2br(esc($d['jawaban'])) ?: '<em style="color:var(--lms-text-muted);">Tidak dijawab</em>' ?>
            </div>
        </div>
        <?php if ($tunggu): ?>
        <small style="color:#F59E0B;"><i class="bi bi-clock me-1"></i>Menunggu koreksi dari guru.</small>
        <?php endif; ?>

        <?php elseif ($isPG): ?>
        <!-- Opsi PG -->
        <div class="row g-2 mb-2">
            <?php foreach ($opsiMap as $huruf => $teks):
                if (empty($teks)) continue;
                $isKunci  = ($d['kunci_jawaban'] === $huruf);
                $isDipilih = ($d['jawaban'] === $huruf);
                $bgOpsi = $isKunci ? 'rgba(34,197,94,0.12)' : ($isDipilih && !$isKunci ? 'rgba(239,68,68,0.08)' : 'rgba(255,255,255,0.03)');
                $brdOpsi = $isKunci ? '#22C55E' : ($isDipilih && !$isKunci ? '#EF4444' : 'rgba(255,255,255,0.08)');
                $clrOpsi = $isKunci ? '#4ade80' : ($isDipilih && !$isKunci ? '#f87171' : 'var(--lms-text-muted)');
            ?>
            <div class="col-md-6">
                <div class="p-2 rounded d-flex align-items-center gap-2"
                     style="background:<?= $bgOpsi ?>;border:1px solid <?= $brdOpsi ?>;">
                    <span style="width:22px;height:22px;border-radius:50%;background:<?= $brdOpsi ?>;
                                  color:white;font-size:0.75rem;display:flex;align-items:center;
                                  justify-content:center;flex-shrink:0;font-weight:600;">
                        <?= $huruf ?>
                    </span>
                    <span style="font-size:0.85rem;color:<?= $clrOpsi ?>;"><?= esc($teks) ?></span>
                    <?php if ($isKunci): ?><i class="bi bi-check-circle-fill text-success ms-auto"></i><?php endif; ?>
                    <?php if ($isDipilih && !$isKunci): ?><i class="bi bi-x-circle-fill text-danger ms-auto"></i><?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (!$benar): ?>
        <small class="text-lms-muted">
            Jawaban Anda: <strong style="color:#EF4444;"><?= esc($d['jawaban'] ?: 'Tidak dijawab') ?></strong>
            &nbsp;|&nbsp; Kunci: <strong style="color:#22C55E;"><?= esc($d['kunci_jawaban']) ?></strong>
        </small>
        <?php endif; ?>

        <?php elseif ($isComplex): ?>
        <!-- Jawaban Kompleks/Menjodohkan -->
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <small class="text-lms-muted d-block mb-1">Jawaban Anda:</small>
                <div class="p-2 rounded" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);font-size:0.875rem;color:white;">
                    <?= esc($d['jawaban'] ?: 'Tidak dijawab') ?>
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-lms-muted d-block mb-1">Kunci:</small>
                <div class="p-2 rounded" style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.3);font-size:0.875rem;color:#4ade80;">
                    <?= esc($d['kunci_jawaban']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
