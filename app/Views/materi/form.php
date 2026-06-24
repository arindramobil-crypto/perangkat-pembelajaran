<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Tentukan URL action berdasarkan mode (create/edit)
$isEdit    = ($mode === 'edit');
$actionUrl = $isEdit
    ? base_url('materi/update/' . $materi['id'])
    : base_url('materi/store');
?>

<!-- Breadcrumb tambahan -->
<div class="mb-3">
    <a href="<?= base_url('materi') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Materi
    </a>
</div>

<div class="row g-4">

    <!-- ── Kolom Form Utama ── -->
    <div class="col-lg-8">
        <div class="glass-panel card">
            <h4 class="card-title mb-4">
                <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'cloud-upload' ?> me-2 text-accent"></i>
                <?= $isEdit ? 'Edit Materi' : 'Unggah Materi Baru' ?>
            </h4>

            <!-- Tampilkan error validasi (jika ada) -->
            <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert lms-alert-error alert-dismissible fade show mb-4" role="alert">
                <strong><i class="bi bi-exclamation-triangle me-2"></i>Harap perbaiki kesalahan berikut:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form action="<?= $actionUrl ?>" method="post" enctype="multipart/form-data" id="formMateri" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="guru_id" value="<?= $guru_id ?>">

                <!-- Jadwal / Kelas Tujuan -->
                <div class="mb-4">
                    <label for="jadwal_id" class="form-label fw-semibold">
                        Kelas & Jadwal Tujuan <span class="text-danger">*</span>
                    </label>
                    <select name="jadwal_id" id="jadwal_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($jadwalList as $j): ?>
                        <option value="<?= $j['id'] ?>"
                            <?= (old('jadwal_id', $materi['jadwal_id'] ?? '') == $j['id']) ? 'selected' : '' ?>>
                            Kelas <?= esc($j['nama_kelas']) ?> — <?= esc($j['nama_mapel']) ?>
                            (<?= esc($j['hari']) ?>, <?= esc(substr($j['jam_mulai'],0,5)) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Silakan pilih kelas tujuan.</div>
                </div>

                <!-- Judul Materi -->
                <div class="mb-4">
                    <label for="judul_materi" class="form-label fw-semibold">
                        Judul Materi <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="judul_materi"
                           id="judul_materi"
                           class="form-control"
                           value="<?= old('judul_materi', $materi['judul_materi'] ?? '') ?>"
                           placeholder="Cth: Pertemuan 1 — Pengenalan Algoritma"
                           minlength="3"
                           maxlength="255"
                           required>
                    <div class="form-text text-lms-muted">Minimal 3 karakter, maksimal 255 karakter.</div>
                    <div class="invalid-feedback">Judul materi wajib diisi (min. 3 karakter).</div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-4">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi / Catatan</label>
                    <textarea name="deskripsi"
                              id="deskripsi"
                              class="form-control"
                              rows="3"
                              maxlength="1000"
                              placeholder="Tuliskan ringkasan isi materi atau petunjuk belajar..."><?= old('deskripsi', $materi['deskripsi'] ?? '') ?></textarea>
                    <div class="form-text text-lms-muted">
                        <span id="charCount">0</span>/1000 karakter
                    </div>
                </div>

                <!-- Tipe Konten: File atau Link -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Tipe Konten <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_konten"
                                   id="tipeFile" value="file"
                                   onchange="toggleTipe()"
                                   <?= (old('tipe_konten', $materi['tipe_konten'] ?? 'file') === 'file') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tipeFile">
                                <i class="bi bi-file-earmark-arrow-up me-1"></i> Unggah File
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_konten"
                                   id="tipeLink" value="link"
                                   onchange="toggleTipe()"
                                   <?= (old('tipe_konten', $materi['tipe_konten'] ?? '') === 'link') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tipeLink">
                                <i class="bi bi-link-45deg me-1"></i> Link Eksternal (YouTube, GDrive, dll)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Area Upload File -->
                <div id="areaFile" class="mb-4">
                    <label for="file_materi" class="form-label fw-semibold">
                        File Materi <?= $isEdit ? '<span class="text-lms-muted fw-normal">(Kosongkan jika tidak ingin mengganti)</span>' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <!-- Drop zone visual -->
                    <div id="dropZone"
                         class="p-4 text-center rounded"
                         style="border: 2px dashed rgba(129,140,248,0.4); cursor:pointer; transition:0.2s; background:rgba(129,140,248,0.04);"
                         onclick="document.getElementById('file_materi').click()"
                         ondragover="onDragOver(event)"
                         ondragleave="onDragLeave(event)"
                         ondrop="onDrop(event)">
                        <i class="bi bi-cloud-arrow-up fs-2 text-accent mb-2 d-block"></i>
                        <p class="mb-1" style="color:white;">Klik atau <strong>seret file</strong> ke sini</p>
                        <p id="dropLabel" class="small text-lms-muted mb-0">
                            PDF, DOCX, PPTX, XLSX, MP4, JPG, PNG — Maks. 20 MB
                        </p>
                    </div>
                    <input type="file"
                           name="file_materi"
                           id="file_materi"
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.mp4,.jpg,.png"
                           class="d-none"
                           onchange="previewFile(this)"
                           <?= (! $isEdit) ? 'required' : '' ?>>

                    <?php if ($isEdit && ! empty($materi['file_materi'])): ?>
                    <div class="mt-2 p-3 rounded d-flex align-items-center gap-2"
                         style="background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2);">
                        <i class="bi bi-file-earmark-check text-success"></i>
                        <div>
                            <small class="text-lms-muted">File saat ini:</small><br>
                            <strong style="color:white;"><?= esc($materi['nama_asli_file'] ?? $materi['file_materi']) ?></strong>
                        </div>
                        <a href="<?= base_url('materi/download/'.$materi['id']) ?>"
                           class="btn btn-sm btn-outline-success ms-auto">
                            <i class="bi bi-download me-1"></i> Unduh
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Area Link Eksternal -->
                <div id="areaLink" class="mb-4" style="display:none;">
                    <label for="link_eksternal" class="form-label fw-semibold">
                        URL Materi <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(15,23,42,0.8); border-color:rgba(255,255,255,0.1); color:#94A3B8;">
                            <i class="bi bi-link"></i>
                        </span>
                        <input type="url"
                               name="link_eksternal"
                               id="link_eksternal"
                               class="form-control"
                               value="<?= old('link_eksternal', $materi['link_eksternal'] ?? '') ?>"
                               placeholder="https://youtube.com/watch?v=...">
                    </div>
                    <div class="form-text text-lms-muted">Masukkan URL lengkap dari YouTube, Google Drive, atau sumber lain.</div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-3 justify-content-end pt-3" style="border-top:1px solid rgba(255,255,255,0.08);">
                    <a href="<?= base_url('materi') ?>" class="btn btn-outline-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" id="btnSubmit">
                        <i class="bi bi-<?= $isEdit ? 'save' : 'cloud-upload' ?>"></i>
                        <?= $isEdit ? 'Simpan Perubahan' : 'Unggah Materi' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Kolom Panduan ── -->
    <div class="col-lg-4">
        <div class="glass-panel card mb-4">
            <h5 class="card-title mb-3"><i class="bi bi-info-circle me-2 text-accent"></i>Panduan Upload</h5>
            <ul class="list-unstyled" style="font-size:0.875rem; color:var(--lms-text-muted); line-height:1.8;">
                <li><i class="bi bi-check-circle text-success me-2"></i>PDF — Dokumen, Modul, Soal</li>
                <li><i class="bi bi-check-circle text-success me-2"></i>DOCX / PPTX / XLSX — Office</li>
                <li><i class="bi bi-check-circle text-success me-2"></i>MP4 — Video Pembelajaran</li>
                <li><i class="bi bi-check-circle text-success me-2"></i>JPG / PNG — Gambar / Diagram</li>
                <li class="mt-2"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Ukuran maks. <strong style="color:white;">20 MB</strong></li>
            </ul>
        </div>

        <div class="glass-panel card">
            <h5 class="card-title mb-3"><i class="bi bi-shield-check me-2 text-accent"></i>Keamanan</h5>
            <ul class="list-unstyled" style="font-size:0.875rem; color:var(--lms-text-muted); line-height:1.8;">
                <li><i class="bi bi-lock me-2"></i>File disimpan dengan nama acak</li>
                <li><i class="bi bi-person-check me-2"></i>Hanya pemilik yang bisa edit/hapus</li>
                <li><i class="bi bi-shield me-2"></i>Tipe file divalidasi di server</li>
                <li><i class="bi bi-key me-2"></i>CSRF protection aktif</li>
            </ul>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Toggle File / Link ────────────────────────────
function toggleTipe() {
    const tipe = document.querySelector('input[name="tipe_konten"]:checked').value;
    document.getElementById('areaFile').style.display = tipe === 'file' ? 'block' : 'none';
    document.getElementById('areaLink').style.display = tipe === 'link' ? 'block' : 'none';
    document.getElementById('file_materi').required = (tipe === 'file' && !<?= $isEdit ? 'true' : 'false' ?>);
}
toggleTipe(); // init

// ── Karakter counter deskripsi ────────────────────
const deskripsiEl = document.getElementById('deskripsi');
const charCountEl = document.getElementById('charCount');
function updateCount() { charCountEl.textContent = deskripsiEl.value.length; }
deskripsiEl.addEventListener('input', updateCount);
updateCount();

// ── Preview file setelah dipilih ──────────────────
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;

    const maxSize = 20 * 1024 * 1024; // 20 MB
    const allowed = ['pdf','doc','docx','ppt','pptx','xls','xlsx','mp4','jpg','png'];
    const ext = file.name.split('.').pop().toLowerCase();

    if (!allowed.includes(ext)) {
        alert('Tipe file tidak diizinkan: .' + ext);
        input.value = '';
        return;
    }
    if (file.size > maxSize) {
        alert('Ukuran file terlalu besar (maks. 20 MB).');
        input.value = '';
        return;
    }

    // Update label drop zone
    document.getElementById('dropLabel').innerHTML =
        '<i class="bi bi-file-earmark-check text-success me-1"></i>' +
        '<strong style="color:white;">' + file.name + '</strong> — ' +
        (file.size / 1024 / 1024).toFixed(2) + ' MB';
}

// ── Drag & Drop ───────────────────────────────────
function onDragOver(e) {
    e.preventDefault();
    document.getElementById('dropZone').style.borderColor = '#818CF8';
    document.getElementById('dropZone').style.background  = 'rgba(129,140,248,0.1)';
}
function onDragLeave(e) {
    document.getElementById('dropZone').style.borderColor = 'rgba(129,140,248,0.4)';
    document.getElementById('dropZone').style.background  = 'rgba(129,140,248,0.04)';
}
function onDrop(e) {
    e.preventDefault();
    onDragLeave(e);
    const fileInput = document.getElementById('file_materi');
    fileInput.files = e.dataTransfer.files;
    previewFile(fileInput);
}

// ── Feedback saat submit ──────────────────────────
document.getElementById('formMateri').addEventListener('submit', function(e) {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sedang memproses...';
});
</script>
<?= $this->endSection() ?>
