<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="color:white;font-weight:800;margin:0;">
            <i class="bi bi-journal-text me-2" style="color:#818CF8;"></i>Jurnal Mengajar Harian
        </h4>
        <small class="text-lms-muted">Catatan harian kegiatan belajar mengajar Anda.</small>
    </div>
    <a href="<?= base_url('jurnal/create') ?>" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Isi Jurnal Baru
    </a>
</div>

<div class="glass-panel card p-0 overflow-hidden">
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

    <div class="table-responsive">
        <table class="table table-dark table-hover mb-0 datatable align-middle" style="background:transparent;">
            <thead style="background:rgba(255,255,255,0.05);">
                <tr>
                    <th style="color:#94A3B8;font-weight:600;font-size:0.85rem;">Tanggal</th>
                    <th style="color:#94A3B8;font-weight:600;font-size:0.85rem;">Kelas & Mapel</th>
                    <th style="color:#94A3B8;font-weight:600;font-size:0.85rem;">Materi Pembahasan</th>
                    <th style="color:#94A3B8;font-weight:600;font-size:0.85rem;">Kejadian/Absen</th>
                    <th style="color:#94A3B8;font-weight:600;font-size:0.85rem;text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jurnal as $j): ?>
                <tr>
                    <td>
                        <div style="font-weight:600;color:white;"><?= date('d M Y', strtotime($j['tanggal'])) ?></div>
                        <small class="text-lms-muted">Jam ke-<?= esc($j['jam_ke'] ?? '-') ?></small>
                    </td>
                    <td>
                        <div style="color:white;font-weight:600;"><?= esc($j['nama_kelas']) ?></div>
                        <small class="text-lms-muted"><?= esc($j['nama_mapel']) ?></small>
                    </td>
                    <td style="max-width:300px; white-space:normal;">
                        <span class="text-white" style="font-size:0.85rem;"><?= nl2br(esc($j['materi_pembahasan'])) ?></span>
                    </td>
                    <td style="max-width:250px; white-space:normal; font-size:0.85rem;">
                        <?php if(!empty($j['catatan_kejadian'])): ?>
                            <div style="color:#F59E0B; margin-bottom:4px;"><strong>Catatan:</strong> <?= esc($j['catatan_kejadian']) ?></div>
                        <?php endif; ?>
                        <?php if(!empty($j['siswa_absen'])): ?>
                            <div style="color:#EF4444;"><strong>Absen:</strong> <?= esc($j['siswa_absen']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?= base_url('jurnal/edit/' . $j['id']) ?>" class="btn btn-sm" style="background:rgba(56,189,248,0.15);color:#38BDF8;border:1px solid rgba(56,189,248,0.3);" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="<?= base_url('jurnal/delete/' . $j['id']) ?>" class="btn btn-sm ms-1" style="background:rgba(239,68,68,0.15);color:#EF4444;border:1px solid rgba(239,68,68,0.3);" title="Hapus" onclick="return confirm('Hapus jurnal ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
