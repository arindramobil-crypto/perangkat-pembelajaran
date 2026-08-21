<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Banner info kelas -->
<?php if (! empty($materis) && isset($materis[0])): ?>
<div class="glass-panel mb-4 p-3 d-flex align-items-center gap-3">
    <span style="font-size:1.8rem;">📚</span>
    <div>
        <span class="text-lms-muted small">Materi tersedia untuk kelas Anda</span>
        <strong style="color:white; display:block;"><?= count($materis) ?> materi telah diunggah guru</strong>
    </div>
</div>
<?php endif; ?>

<?php if (empty($materis)): ?>
<!-- Kosong -->
<div class="glass-panel card text-center py-5">
    <div style="font-size:3.5rem; margin-bottom:1rem;">📂</div>
    <h4 style="color:white;">Belum Ada Materi</h4>
    <p class="text-lms-muted">Guru belum mengunggah materi untuk kelas Anda saat ini.</p>
</div>

<?php else: ?>
<!-- Grid materi -->
<div class="row g-4">
    <?php foreach ($materis as $m): ?>
    <?php
    // Tentukan ikon berdasarkan ekstensi file
    $ext     = strtolower(pathinfo($m['nama_asli_file'] ?? '', PATHINFO_EXTENSION));
    $iconMap = [
        'pdf'  => ['bi-file-earmark-pdf',    '#EF4444'],
        'docx' => ['bi-file-earmark-word',   '#3B82F6'],
        'doc'  => ['bi-file-earmark-word',   '#3B82F6'],
        'pptx' => ['bi-file-earmark-slides', '#F97316'],
        'ppt'  => ['bi-file-earmark-slides', '#F97316'],
        'xlsx' => ['bi-file-earmark-excel',  '#22C55E'],
        'xls'  => ['bi-file-earmark-excel',  '#22C55E'],
        'mp4'  => ['bi-play-circle',          '#A78BFA'],
        'jpg'  => ['bi-file-earmark-image',  '#F59E0B'],
        'png'  => ['bi-file-earmark-image',  '#F59E0B'],
    ];
    [$icon, $color] = $iconMap[$ext] ?? ['bi-file-earmark', '#94A3B8'];
    if ($m['tipe_konten'] === 'link') { $icon = 'bi-play-btn'; $color = '#38BDF8'; }
    ?>
    <div class="col-md-6 col-xl-4">
        <div class="glass-panel h-100 d-flex flex-column"
             style="padding:1.5rem; transition:transform 0.2s;"
             onmouseover="this.style.transform='translateY(-3px)'"
             onmouseout="this.style.transform='none'">

            <!-- Header kartu -->
            <div class="d-flex align-items-start gap-3 mb-3">
                <div style="width:48px;height:48px;border-radius:10px;background:<?= $color ?>22;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi <?= $icon ?>" style="font-size:1.4rem;color:<?= $color ?>;"></i>
                </div>
                <div style="min-width:0;">
                    <strong style="color:white;display:block;line-height:1.3;
                                   overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?= esc($m['judul_materi']) ?>
                    </strong>
                    <span class="text-lms-muted" style="font-size:0.78rem;">
                        <?= esc($m['nama_mapel']) ?>
                    </span>
                </div>
            </div>

            <!-- Deskripsi -->
            <?php if (! empty($m['deskripsi'])): ?>
            <p style="font-size:0.85rem;color:var(--lms-text-muted);line-height:1.5;flex:1;
                      display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                <?= esc($m['deskripsi']) ?>
            </p>
            <?php endif; ?>

            <!-- Footer kartu -->
            <div style="border-top:1px solid rgba(255,255,255,0.08);margin-top:auto;padding-top:1rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <span style="font-size:0.75rem;color:var(--lms-text-muted);">
                        <i class="bi bi-clock me-1"></i>
                        <?= date('d M Y', strtotime($m['created_at'])) ?>
                    </span>
                    <span style="font-size:0.75rem;color:var(--lms-text-muted);">
                        Oleh: <strong style="color:white;"><?= esc($m['nama_guru'] ?? 'Guru') ?></strong>
                    </span>
                </div>
                <div class="mt-2">
                    <?php if ($m['tipe_konten'] === 'link'): ?>
                    <a href="<?= esc($m['link_eksternal']) ?>" target="_blank" rel="noopener"
                       class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                       style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.3);color:#38BDF8;">
                        <i class="bi bi-play-circle"></i> Buka Materi
                    </a>
                    <?php else: ?>
                    <a href="<?= base_url('materi/download/' . $m['id']) ?>"
                       class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2"
                       style="background:rgba(129,140,248,0.15);border:1px solid rgba(129,140,248,0.3);color:#818CF8;">
                        <i class="bi bi-download"></i> Unduh Materi
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
