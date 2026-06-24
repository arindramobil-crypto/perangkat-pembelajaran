<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="color:white;font-weight:800;margin:0;">
            <i class="bi bi-file-earmark-richtext me-2" style="color:#818CF8;"></i>Bank RPP / Modul Ajar
        </h4>
        <small class="text-lms-muted">Koleksi perangkat ajar digital Anda.</small>
    </div>
    <a href="<?= base_url('rpp/create') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-cloud-arrow-up me-1"></i> Upload / Buat Baru
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert lms-alert-success m-3 alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert lms-alert-error m-3 alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-3">
    <?php if (empty($rpp)): ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-folder-x" style="font-size:4rem; color:var(--lms-text-muted); opacity:0.5;"></i>
            <h6 class="text-white mt-3">Belum ada RPP/Modul Ajar</h6>
            <p class="text-lms-muted small">Silakan buat atau unggah modul ajar Anda yang pertama.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($rpp as $r): ?>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="glass-panel card h-100 hoverable">
            <div class="card-body d-flex flex-column p-0">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge" style="background:rgba(129,140,248,0.2); color:#818CF8; border:1px solid rgba(129,140,248,0.3);">
                        <?= esc($r['nama_kelas']) ?>
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-link text-lms-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" style="background:rgba(15,23,42,0.95);backdrop-filter:blur(10px);">
                            <li><a class="dropdown-item" href="<?= base_url('rpp/edit/'.$r['id']) ?>"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('rpp/delete/'.$r['id']) ?>" onclick="return confirm('Hapus RPP ini?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                        </ul>
                    </div>
                </div>
                <h5 class="text-white mb-1" style="font-size:1.05rem; font-weight:700;"><?= esc($r['judul']) ?></h5>
                <p class="text-lms-muted small mb-3 flex-grow-1"><i class="bi bi-book me-1"></i> <?= esc($r['nama_mapel']) ?></p>
                
                <div class="d-flex justify-content-between align-items-center mt-auto border-top border-secondary border-opacity-25 pt-3">
                    <?php if ($r['file_path']): ?>
                        <?php $ext = strtolower(pathinfo($r['file_path'], PATHINFO_EXTENSION)); ?>
                        <?php if($ext === 'pdf'): ?>
                            <span class="badge" style="background:rgba(239,68,68,0.15); color:#FCA5A5;"><i class="bi bi-file-pdf"></i> PDF File</span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(56,189,248,0.15); color:#7DD3FC;"><i class="bi bi-file-word"></i> Word File</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="badge" style="background:rgba(245,158,11,0.15); color:#FCD34D;"><i class="bi bi-exclamation-triangle"></i> Tanpa File</span>
                    <?php endif; ?>
                    <a href="<?= base_url('rpp/view/'.$r['id']) ?>" class="btn btn-sm btn-outline-light rounded-pill" style="font-size:0.75rem;">Buka <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
