<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <h4 style="color:white;font-weight:800;margin:0;">
        <i class="bi bi-printer me-2" style="color:#818CF8;"></i><?= esc($title) ?>
    </h4>
    <small class="text-lms-muted">Pusat pencetakan berkas administrasi guru (Format A4/F4 dengan Kop Sekolah).</small>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert lms-alert-error m-3 alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Jurnal Mengajar -->
    <div class="col-md-6 col-lg-4">
        <div class="glass-panel card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(79,70,229,0.2);display:flex;align-items:center;justify-content:center;color:#818CF8;font-size:1.5rem;margin-right:15px;">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h5 class="text-white mb-0" style="font-weight:700;">Jurnal Mengajar</h5>
                </div>
                <p class="text-lms-muted small mb-4">Cetak rekapitulasi agenda harian Anda berdasarkan bulan/tahun.</p>
                <form action="<?= base_url('export/jurnal') ?>" method="get" target="_blank">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <select name="bulan" class="form-select lms-input" required>
                                <option value="">-- Bulan --</option>
                                <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= $i ?>"><?= date('F', mktime(0,0,0,$i,10)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="tahun" class="form-select lms-input" required>
                                <option value="">-- Tahun --</option>
                                <?php $y = date('Y'); for($i=$y-2; $i<=$y+1; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-printer me-1"></i> Cetak Jurnal</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Prota & Promes -->
    <div class="col-md-6 col-lg-4">
        <div class="glass-panel card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(34,197,94,0.2);display:flex;align-items:center;justify-content:center;color:#4ade80;font-size:1.5rem;margin-right:15px;">
                        <i class="bi bi-calendar3-range"></i>
                    </div>
                    <h5 class="text-white mb-0" style="font-weight:700;">Prota & Promes</h5>
                </div>
                <p class="text-lms-muted small mb-4">Cetak tabel Program Tahunan atau Semester per kelas.</p>
                <form action="<?= base_url('export/prota_promes') ?>" method="get" target="_blank">
                    <div class="mb-2">
                        <select name="tipe" class="form-select lms-input" required>
                            <option value="">-- Tipe Dokumen --</option>
                            <option value="Prota">Program Tahunan (Prota)</option>
                            <option value="Promes">Program Semester (Promes)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <select name="mapel_id" class="form-select lms-input" required>
                            <option value="">-- Mata Pelajaran --</option>
                            <?php foreach($mapel as $m): ?><option value="<?= $m['id'] ?>"><?= esc($m['nama_mapel']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="kelas_id" class="form-select lms-input" required>
                            <option value="">-- Kelas --</option>
                            <?php foreach($kelas as $k): ?><option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-printer me-1"></i> Cetak Prota/Promes</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lainnya (Shortcut ke Nilai & Presensi) -->
    <div class="col-md-6 col-lg-4">
        <div class="glass-panel card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(245,158,11,0.2);display:flex;align-items:center;justify-content:center;color:#F59E0B;font-size:1.5rem;margin-right:15px;">
                        <i class="bi bi-files"></i>
                    </div>
                    <h5 class="text-white mb-0" style="font-weight:700;">Cetak Lainnya</h5>
                </div>
                <p class="text-lms-muted small mb-4">Pintasan menuju laporan rekapitulasi nilai dan presensi siswa yang sudah ada.</p>
                <div class="mt-auto">
                    <a href="<?= base_url('buku-nilai') ?>" class="btn btn-outline-warning w-100 mb-2"><i class="bi bi-bar-chart-steps me-1"></i> Buka Buku Nilai</a>
                    <a href="<?= base_url('presensi') ?>" class="btn btn-outline-info w-100"><i class="bi bi-check2-square me-1"></i> Buka Rekap Presensi</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
