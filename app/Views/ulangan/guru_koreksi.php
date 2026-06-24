<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('ulangan/rekap/' . $ulangan['id']) ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekap
    </a>
    <div>
        <h4 style="color:white; margin:0;">Koreksi Jawaban Uraian</h4>
        <small class="text-lms-muted">
            Ujian: <strong style="color:white;"><?= esc($ulangan['judul']) ?></strong>
            &nbsp;|&nbsp;
            Siswa: <strong style="color:#818CF8;"><?= esc($siswaInfo['nama_lengkap']) ?></strong>
            (NIS: <?= esc($siswaInfo['nis']) ?>)
        </small>
    </div>
</div>

<!-- Panduan -->
<div class="glass-panel mb-4 p-3 d-flex align-items-start gap-3"
     style="border-left:4px solid #F59E0B;">
    <i class="bi bi-lightbulb text-warning fs-5 mt-1"></i>
    <div>
        <strong style="color:white;">Petunjuk Koreksi</strong>
        <p class="text-lms-muted small mb-0 mt-1">
            Baca jawaban siswa dengan seksama, lalu berikan skor antara <strong style="color:white;">0</strong>
            hingga <strong style="color:white;">maks. bobot soal</strong>. Nilai akhir akan dihitung ulang
            secara otomatis setelah Anda menyimpan koreksi ini.
        </p>
    </div>
</div>

<form action="<?= base_url('ulangan/proses_koreksi') ?>" method="post" id="formKoreksi">
    <?= csrf_field() ?>
    <input type="hidden" name="jawaban_siswa_id" value="<?= $attempt['id'] ?>">

    <?php if (empty($jawabanUraian)): ?>
    <div class="glass-panel card text-center py-4">
        <i class="bi bi-check2-all fs-3 text-success mb-2"></i>
        <p class="text-lms-muted mb-0">Tidak ada soal uraian yang perlu dikoreksi.</p>
    </div>
    <?php else: ?>

    <?php foreach ($jawabanUraian as $i => $j): ?>
    <div class="glass-panel card mb-4">
        <!-- Header soal -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <span class="badge mb-2" style="background:rgba(129,140,248,0.2);color:#818CF8;">
                    Soal <?= $i + 1 ?> — Uraian
                </span>
                <p style="color:white; font-size:1rem; font-weight:500; margin:0; line-height:1.6;">
                    <?= nl2br(esc($j['pertanyaan'])) ?>
                </p>
            </div>
            <span class="badge ms-3" style="background:rgba(245,158,11,0.15);color:#F59E0B;white-space:nowrap;">
                Bobot: <?= $j['bobot'] ?> poin
            </span>
        </div>

        <!-- Jawaban siswa -->
        <div class="mb-4">
            <label class="form-label" style="color:var(--lms-text-muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;">
                Jawaban Siswa
            </label>
            <div class="p-3 rounded" style="background:rgba(129,140,248,0.06);border:1px solid rgba(129,140,248,0.2);
                         color:white;line-height:1.7;font-size:0.9rem;min-height:80px;">
                <?= nl2br(esc($j['jawaban'])) ?: '<em style="color:var(--lms-text-muted);">Siswa tidak memberikan jawaban.</em>' ?>
            </div>
        </div>

        <!-- Kunci / Panduan Jawaban (jika diisi guru) -->
        <?php if (!empty($j['kunci_jawaban'])): ?>
        <div class="mb-4">
            <label class="form-label" style="color:var(--lms-text-muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;">
                Panduan Jawaban / Kunci
            </label>
            <div class="p-3 rounded" style="background:rgba(34,197,94,0.06);border:1px solid rgba(34,197,94,0.2);
                         color:#4ade80;line-height:1.7;font-size:0.9rem;">
                <?= nl2br(esc($j['kunci_jawaban'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Input Skor -->
        <div class="row align-items-center g-3">
            <div class="col-sm-6">
                <label class="form-label fw-semibold">
                    Skor untuk jawaban ini <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="number"
                           name="skor[<?= $j['id'] ?>]"
                           class="form-control skor-input"
                           value="<?= $j['skor'] ?? 0 ?>"
                           min="0"
                           max="<?= $j['bobot'] ?>"
                           step="0.5"
                           data-bobot="<?= $j['bobot'] ?>"
                           required>
                    <span class="input-group-text" style="background:rgba(15,23,42,0.8);border-color:rgba(255,255,255,0.1);color:#94A3B8;">
                        / <?= $j['bobot'] ?>
                    </span>
                </div>
                <div class="form-text text-lms-muted">Masukkan angka 0 hingga <?= $j['bobot'] ?></div>
            </div>
            <div class="col-sm-6">
                <!-- Preview persentase skor -->
                <label class="form-label" style="color:transparent;">.</label>
                <div class="p-3 rounded text-center" id="skorPreview_<?= $j['id'] ?>"
                     style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
                    <span style="font-size:1.4rem;font-weight:700;color:white;" id="skorPct_<?= $j['id'] ?>">
                        <?= $j['bobot'] > 0 ? round(($j['skor'] / $j['bobot']) * 100) : 0 ?>%
                    </span>
                    <div class="small text-lms-muted">dari bobot soal</div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Tombol simpan -->
    <div class="d-flex justify-content-end gap-3">
        <a href="<?= base_url('ulangan/rekap/' . $ulangan['id']) ?>" class="btn btn-outline-secondary">
            Batal
        </a>
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2" id="btnSimpan">
            <i class="bi bi-save2"></i> Simpan Koreksi & Hitung Nilai Akhir
        </button>
    </div>

    <?php endif; ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Update preview % skor secara real-time
document.querySelectorAll('.skor-input').forEach(function(input) {
    const detailId = input.name.match(/\[(\d+)\]/)[1];
    const bobot    = parseFloat(input.dataset.bobot) || 1;

    input.addEventListener('input', function() {
        const val  = Math.min(Math.max(parseFloat(this.value) || 0, 0), bobot);
        const pct  = Math.round((val / bobot) * 100);
        const el   = document.getElementById('skorPct_' + detailId);
        if (el) {
            el.textContent = pct + '%';
            el.style.color = pct >= 50 ? '#22C55E' : (pct > 0 ? '#F59E0B' : '#EF4444');
        }
    });
    // Trigger on load
    input.dispatchEvent(new Event('input'));
});

// Loading state saat submit
document.getElementById('formKoreksi').addEventListener('submit', function() {
    const btn = document.getElementById('btnSimpan');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>
<?= $this->endSection() ?>
