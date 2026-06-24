<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <h4 style="color:white;font-weight:800;margin:0;">
        <i class="bi bi-journal-plus me-2" style="color:#818CF8;"></i><?= esc($title) ?>
    </h4>
    <small class="text-lms-muted">Catat rincian KBM Anda hari ini.</small>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="glass-panel card">
            <form action="<?= base_url('jurnal/save') ?>" method="post">
                <?= csrf_field() ?>
                <?php if(isset($jurnal)): ?>
                    <input type="hidden" name="id" value="<?= $jurnal['id'] ?>">
                <?php endif; ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white small">Pilih Jadwal / Kelas <span class="text-danger">*</span></label>
                        <select name="jadwal_id" class="form-select lms-input" required>
                            <option value="">-- Pilih Jadwal --</option>
                            <?php foreach($jadwalList as $j): ?>
                                <option value="<?= $j['id'] ?>" <?= (isset($jurnal) && $jurnal['jadwal_id'] == $j['id']) ? 'selected' : '' ?>>
                                    <?= esc($j['nama_kelas']) ?> - <?= esc($j['nama_mapel']) ?> (<?= esc($j['hari']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white small">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control lms-input" required value="<?= isset($jurnal) ? $jurnal['tanggal'] : date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white small">Jam Ke-</label>
                        <input type="text" name="jam_ke" class="form-control lms-input" placeholder="Contoh: 1-2" value="<?= isset($jurnal) ? esc($jurnal['jam_ke']) : '' ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white small">Materi Pembahasan <span class="text-danger">*</span></label>
                    <textarea name="materi_pembahasan" class="form-control lms-input" rows="4" required placeholder="Tuliskan materi yang dibahas hari ini..."><?= isset($jurnal) ? esc($jurnal['materi_pembahasan']) : '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label text-white small">Catatan Kejadian di Kelas (Opsional)</label>
                    <textarea name="catatan_kejadian" class="form-control lms-input" rows="2" placeholder="Siswa ribut, proyektor rusak, atau ada kegiatan lain..."><?= isset($jurnal) ? esc($jurnal['catatan_kejadian']) : '' ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white small">Siswa Tidak Hadir (Opsional)</label>
                    <input type="text" name="siswa_absen" class="form-control lms-input" placeholder="Contoh: Budi (Sakit), Ani (Izin)" value="<?= isset($jurnal) ? esc($jurnal['siswa_absen']) : '' ?>">
                </div>

                <div class="d-flex justify-content-between border-top border-secondary border-opacity-25 pt-3">
                    <a href="<?= base_url('jurnal') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Jurnal</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4 d-none d-lg-block">
        <div class="glass-panel card bg-opacity-10 bg-primary" style="border-color:rgba(129,140,248,0.3);">
            <h6 class="text-white mb-3"><i class="bi bi-info-circle me-2 text-accent"></i>Panduan Jurnal</h6>
            <p class="text-lms-muted small mb-2">1. Jurnal ini akan menjadi rekapitulasi buku kerja harian Anda.</p>
            <p class="text-lms-muted small mb-2">2. Pastikan memilih kelas yang tepat agar laporan sinkron.</p>
            <p class="text-lms-muted small mb-0">3. Jurnal ini akan dapat diunduh & dicetak oleh Anda kapan saja.</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
