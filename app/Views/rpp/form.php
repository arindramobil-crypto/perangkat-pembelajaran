<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('rpp') ?>" class="text-decoration-none text-lms-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Kembali ke Bank RPP</a>
    <h4 style="color:white;font-weight:800;margin:0;">
        <i class="bi bi-cloud-arrow-up me-2" style="color:#818CF8;"></i><?= esc($title) ?>
    </h4>
</div>

<div class="glass-panel card">
    <form action="<?= base_url('rpp/save') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php if(isset($rpp)): ?>
            <input type="hidden" name="id" value="<?= $rpp['id'] ?>">
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-white small">Mata Pelajaran <span class="text-danger">*</span></label>
                <select name="mapel_id" class="form-select lms-input" required>
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= (isset($rpp) && $rpp['mapel_id'] == $m['id']) ? 'selected' : '' ?>><?= esc($m['nama_mapel']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white small">Target Kelas <span class="text-danger">*</span></label>
                <select name="kelas_id" class="form-select lms-input" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (isset($rpp) && $rpp['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-white small">Judul RPP / Modul Ajar <span class="text-danger">*</span></label>
            <input type="text" name="judul" class="form-control lms-input" required placeholder="Contoh: Modul Ajar Bab 1: Sistem Persamaan Linear" value="<?= isset($rpp) ? esc($rpp['judul']) : '' ?>">
        </div>

        <div class="mb-4">
            <label class="form-label text-white small">Lampiran File RPP (PDF/Word)</label>
            <?php if(isset($rpp) && $rpp['file_path']): ?>
                <div class="mb-2 p-2 rounded" style="background:rgba(255,255,255,0.05); border:1px solid var(--lms-border);">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> File saat ini: <a href="<?= base_url('uploads/rpp/' . $rpp['file_path']) ?>" target="_blank" class="text-accent"><?= esc($rpp['file_path']) ?></a>
                </div>
            <?php endif; ?>
            <input type="file" name="file_lampiran" class="form-control lms-input" accept=".pdf,.doc,.docx" <?= isset($rpp) && $rpp['file_path'] ? '' : 'required' ?>>
            <div class="form-text text-lms-muted">Pilih file berformat PDF atau Word. <?= isset($rpp) && $rpp['file_path'] ? 'Biarkan kosong jika tidak ingin mengubah file.' : 'Wajib diisi.' ?></div>
        </div>

        <div class="d-flex justify-content-end border-top border-secondary border-opacity-25 pt-4">
            <a href="<?= base_url('rpp') ?>" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan RPP</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
