<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('ai-assistant') ?>" class="text-decoration-none text-lms-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Kembali ke Generator</a>
    <h4 style="color:white;font-weight:800;margin:0;">
        <i class="bi bi-robot text-primary me-2"></i><?= esc($title) ?>
    </h4>
    <small class="text-lms-muted">Berikut adalah draf dokumen yang berhasil dibuat oleh AI.</small>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius:15px; overflow:hidden;">
    <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: black;">Hasil Draft: <?= esc($topik) ?></h5>
        <button class="btn btn-outline-primary btn-sm" onclick="copyToClipboard()">
            <i class="bi bi-clipboard"></i> Salin Teks
        </button>
    </div>
    <div class="card-body p-4 bg-light" id="ai-result-content" style="color: black;">
        <?= $htmlResult ?>
    </div>
    <div class="card-footer bg-white py-3 px-4 d-flex justify-content-end gap-2">
        <a href="<?= base_url('ai-assistant') ?>" class="btn btn-secondary">Tutup & Kembali</a>
        <?php if(strtolower($tipe) === 'rpp'): ?>
        <a href="<?= base_url('rpp/create') ?>" class="btn btn-success"><i class="bi bi-file-earmark-plus"></i> Simpan ke PPM Digital</a>
        <?php endif; ?>
    </div>
</div>

<script>
function copyToClipboard() {
    const content = document.getElementById('ai-result-content').innerText;
    navigator.clipboard.writeText(content).then(function() {
        alert('Teks berhasil disalin!');
    }, function(err) {
        alert('Gagal menyalin teks: ', err);
    });
}
</script>

<?= $this->endSection() ?>
