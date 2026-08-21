<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('rpp') ?>" class="text-decoration-none text-lms-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Kembali ke Bank RPP</a>
    <h4 style="color:var(--lms-text);font-weight:800;margin:0;">
        <i class="bi bi-magic me-2 text-accent"></i><?= esc($title) ?>
    </h4>
    <small class="text-lms-muted">Isi form di bawah ini untuk membuat RPP Kurikulum Merdeka secara otomatis.</small>
</div>

<form action="<?= base_url('rpp/save') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="is_template" value="1">
    <?php if(isset($rpp)): ?>
        <input type="hidden" name="id" value="<?= $rpp['id'] ?>">
    <?php endif; ?>

    <!-- INFO UMUM -->
    <div class="glass-panel card mb-4">
        <h5 class="text-white border-bottom border-secondary border-opacity-25 pb-2 mb-3"><i class="bi bi-info-circle me-2"></i>Info Umum</h5>
        <div class="row g-3 mb-3">
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
                <label class="form-label text-white small">Kelas/Tingkat <span class="text-danger">*</span></label>
                <select name="kelas_id" class="form-select lms-input" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (isset($rpp) && $rpp['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label text-white small">Judul Dokumen RPP <span class="text-danger">*</span></label>
            <input type="text" name="judul" class="form-control lms-input" required placeholder="Contoh: RPP Validitas dan Pengolahan Data" value="<?= isset($rpp) ? esc($rpp['judul']) : '' ?>">
        </div>

        <div class="mb-3">
            <label class="form-label text-white small">Topik</label>
            <textarea name="template[topik]" class="form-control lms-input" rows="2" placeholder="Contoh: Mengidentifikasi Berbagai Sumber Data..."><?= isset($template) ? esc($template['topik']) : '' ?></textarea>
        </div>

        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <label class="form-label text-white small">Durasi</label>
                <input type="text" name="template[durasi]" class="form-control lms-input" placeholder="Contoh: 4 Jtm x 1 Pertemuan" value="<?= isset($template) ? esc($template['durasi']) : '' ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label text-white small">Pendekatan</label>
                <input type="text" name="template[pendekatan]" class="form-control lms-input" placeholder="Contoh: Berbasis PBL (Project-Based Learning) dan Kolaboratif" value="<?= isset($template) ? esc($template['pendekatan']) : '' ?>">
            </div>
        </div>
    </div>

    <!-- 1. IDENTIFIKASI -->
    <div class="glass-panel card mb-4">
        <h5 class="text-white border-bottom border-secondary border-opacity-25 pb-2 mb-3"><i class="bi bi-1-circle me-2"></i>IDENTIFIKASI</h5>
        
        <div class="mb-3">
            <label class="form-label text-white small">a. Kesiapan Peserta Didik</label>
            <textarea name="template[identifikasi_kesiapan]" class="form-control lms-input" rows="3" placeholder="Deskripsikan kesiapan awal siswa..."><?= isset($template) ? esc($template['identifikasi_kesiapan']) : '' ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label text-white small">b. Karakteristik Materi Pelajaran</label>
            <textarea name="template[identifikasi_karakteristik]" class="form-control lms-input" rows="3"><?= isset($template) ? esc($template['identifikasi_karakteristik']) : '' ?></textarea>
        </div>
        <div class="mb-2">
            <label class="form-label text-white small">c. Dimensi Profil Lulusan (Gunakan list / bullet points)</label>
            <textarea name="template[identifikasi_profil]" class="form-control lms-input" rows="4" placeholder="- Penalaran Kritis: ...&#10;- Kreativitas: ...&#10;- Kolaborasi: ..."><?= isset($template) ? esc($template['identifikasi_profil']) : '' ?></textarea>
        </div>
    </div>

    <!-- 2. DESAIN PEMBELAJARAN -->
    <div class="glass-panel card mb-4">
        <h5 class="text-white border-bottom border-secondary border-opacity-25 pb-2 mb-3"><i class="bi bi-2-circle me-2"></i>DESAIN PEMBELAJARAN</h5>
        
        <div class="mb-3">
            <label class="form-label text-white small">a. Capaian Pembelajaran (CP)</label>
            <textarea name="template[desain_cp]" class="form-control lms-input" rows="4"><?= isset($template) ? esc($template['desain_cp']) : '' ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label text-white small">b. Topik Pembelajaran Kontekstual</label>
            <textarea name="template[desain_topik_kontekstual]" class="form-control lms-input" rows="3"><?= isset($template) ? esc($template['desain_topik_kontekstual']) : '' ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label text-white small">c. Integrasi Lintas Disiplin</label>
            <textarea name="template[desain_integrasi]" class="form-control lms-input" rows="3" placeholder="- Bahasa Indonesia: ...&#10;- PKn: ..."><?= isset($template) ? esc($template['desain_integrasi']) : '' ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label text-white small">d. Tujuan Pembelajaran</label>
            <textarea name="template[desain_tujuan]" class="form-control lms-input" rows="3" placeholder="- Mengidentifikasi ...&#10;- Menjelaskan ..."><?= isset($template) ? esc($template['desain_tujuan']) : '' ?></textarea>
        </div>
        <div class="mb-2">
            <label class="form-label text-white small">e. Kerangka Pembelajaran</label>
            <textarea name="template[desain_kerangka]" class="form-control lms-input" rows="4" placeholder="- Praktik Pedagogis: ...&#10;- Kemitraan Pembelajaran: ...&#10;- Lingkungan Pembelajaran: ..."><?= isset($template) ? esc($template['desain_kerangka']) : '' ?></textarea>
        </div>
    </div>

    <!-- 3. PENGALAMAN BELAJAR -->
    <div class="glass-panel card mb-4">
        <h5 class="text-white border-bottom border-secondary border-opacity-25 pb-2 mb-3"><i class="bi bi-3-circle me-2"></i>PENGALAMAN BELAJAR</h5>
        
        <div class="mb-3">
            <label class="form-label text-white small">a. Prinsip Berkesadaran, Bermakna, Menggembirakan</label>
            <textarea name="template[pengalaman_prinsip]" class="form-control lms-input" rows="3"><?= isset($template) ? esc($template['pengalaman_prinsip']) : '' ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label text-white small">b. Tahapan Pembelajaran</label>
            <textarea name="template[pengalaman_tahapan]" class="form-control lms-input" rows="5" placeholder="- Pendahuluan: ...&#10;- Inti: ...&#10;- Penutup: ..."><?= isset($template) ? esc($template['pengalaman_tahapan']) : '' ?></textarea>
        </div>
        <div class="mb-2">
            <label class="form-label text-white small">c. Deskripsi Pengalaman Belajar</label>
            <textarea name="template[pengalaman_deskripsi]" class="form-control lms-input" rows="4" placeholder="- Memahami: ...&#10;- Mengaplikasi: ...&#10;- Merefleksi: ..."><?= isset($template) ? esc($template['pengalaman_deskripsi']) : '' ?></textarea>
        </div>
    </div>

    <!-- 4. ASESMEN -->
    <div class="glass-panel card mb-4">
        <h5 class="text-white border-bottom border-secondary border-opacity-25 pb-2 mb-3"><i class="bi bi-4-circle me-2"></i>ASESMEN</h5>
        
        <div class="mb-3">
            <label class="form-label text-white small">a. Asesmen Awal</label>
            <textarea name="template[asesmen_awal]" class="form-control lms-input" rows="3"><?= isset($template) ? esc($template['asesmen_awal']) : '' ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label text-white small">b. Asesmen Proses</label>
            <textarea name="template[asesmen_proses]" class="form-control lms-input" rows="3"><?= isset($template) ? esc($template['asesmen_proses']) : '' ?></textarea>
        </div>
        <div class="mb-2">
            <label class="form-label text-white small">c. Asesmen Akhir</label>
            <textarea name="template[asesmen_akhir]" class="form-control lms-input" rows="3"><?= isset($template) ? esc($template['asesmen_akhir']) : '' ?></textarea>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="glass-panel card">
        <div class="d-flex justify-content-end align-items-center">
            <a href="<?= base_url('rpp') ?>" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-save me-1"></i> Simpan Modul Ajar</button>
        </div>
    </div>

</form>

<?= $this->endSection() ?>
