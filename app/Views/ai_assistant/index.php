<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0"><i class="bi bi-robot text-primary"></i> AI Assistant</h3>
            <span class="badge bg-primary rounded-pill px-3 py-2">Versi Beta</span>
        </div>
        <p class="text-muted mt-2">Buat draft PPM (Perencanaan Pembelajaran Mendalam) atau Materi secara instan dengan bantuan AI.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0" style="border-radius:15px; overflow:hidden;">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold">Parameter Generator</h5>
            </div>
            <div class="card-body p-4">
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('ai-assistant/generate') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Topik / Judul Materi</label>
                        <input type="text" name="topik" class="form-control" placeholder="Contoh: Pemrograman Web Dasar, Sejarah Kemerdekaan..." required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipe Dokumen</label>
                            <select name="tipe" class="form-select" required>
                                <option value="RPP">Perencanaan Pembelajaran Mendalam (PPM)</option>
                                <option value="Materi">Materi Bahan Ajar</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Kelas / Jenjang</label>
                            <input type="text" name="kelas" class="form-control" placeholder="Contoh: X RPL, XI TKJ" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alokasi Waktu / Durasi</label>
                        <input type="text" name="durasi" class="form-control" value="90 Menit" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius:10px;">
                        <i class="bi bi-magic me-2"></i> Generate dengan AI
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
