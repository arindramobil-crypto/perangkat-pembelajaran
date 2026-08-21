<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="<?= base_url('rpp') ?>" class="text-decoration-none text-lms-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Kembali ke Bank RPP</a>
        <h4 style="color:white;font-weight:800;margin:0;">
            <i class="bi bi-file-earmark-richtext me-2" style="color:#818CF8;"></i><?= esc($rpp['judul']) ?>
        </h4>
    </div>
    <?php if(session()->get('role') === 'Guru'): ?>
    <a href="<?= base_url('rpp/edit/'.$rpp['id']) ?>" class="btn btn-outline-light"><i class="bi bi-pencil-square me-1"></i> Edit RPP</a>
    <?php endif; ?>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="glass-panel card h-100 p-0 overflow-hidden" style="min-height: 70vh;">
            <?php if($rpp['file_path']): ?>
                <?php $ext = strtolower(pathinfo($rpp['file_path'], PATHINFO_EXTENSION)); ?>
                <?php if($ext === 'pdf'): ?>
                    <iframe src="<?= base_url('uploads/rpp/'.$rpp['file_path']) ?>" width="100%" height="100%" style="border:none; min-height: 70vh;"></iframe>
                <?php else: ?>
                    <div class="text-center py-5 d-flex flex-column justify-content-center h-100">
                        <i class="bi bi-file-earmark-word" style="font-size:4rem; color:#38BDF8;"></i>
                        <h5 class="text-white mt-3">File Dokumen (Word)</h5>
                        <p class="text-lms-muted">File ini tidak dapat ditampilkan langsung di browser.</p>
                        <div>
                            <a href="<?= base_url('uploads/rpp/'.$rpp['file_path']) ?>" class="btn btn-primary mt-2"><i class="bi bi-download me-1"></i> Unduh File</a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-x" style="font-size:4rem; color:var(--lms-text-muted); opacity:0.5;"></i>
                    <p class="text-lms-muted mt-3">File tidak ditemukan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="glass-panel card mb-4">
            <h6 class="text-white border-bottom border-secondary border-opacity-25 pb-2 mb-3">Informasi Perangkat</h6>
            <ul class="list-unstyled mb-0" style="font-size:0.9rem; color:var(--lms-text-muted);">
                <li class="mb-2"><strong class="text-white">Penyusun:</strong> <br> <?= esc($rpp['nama_guru']) ?></li>
                <li class="mb-2"><strong class="text-white">Mata Pelajaran:</strong> <br> <?= esc($rpp['nama_mapel']) ?></li>
                <li class="mb-2"><strong class="text-white">Target Kelas:</strong> <br> <?= esc($rpp['nama_kelas']) ?></li>
                <li class="mb-0"><strong class="text-white">Dibuat:</strong> <br> <?= date('d M Y H:i', strtotime($rpp['created_at'])) ?></li>
            </ul>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
