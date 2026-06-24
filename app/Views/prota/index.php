<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="color:white;font-weight:800;margin:0;">
            <i class="bi bi-calendar3-range me-2" style="color:#818CF8;"></i>Program Tahunan & Semester
        </h4>
        <small class="text-lms-muted">Tabel matriks alokasi waktu dan materi pembelajaran.</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('export') ?>" class="btn btn-outline-light shadow-sm">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen
        </a>
        <a href="<?= base_url('prota/create') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data Baru
        </a>
    </div>
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

    <div class="p-3 border-bottom border-secondary border-opacity-25 bg-dark bg-opacity-50">
        <span class="text-white small fw-bold"><i class="bi bi-info-circle me-1"></i> Info:</span>
        <span class="text-lms-muted small">Tabel di bawah menampilkan matriks semester ganjil. Untuk melihat matriks semester genap atau mencetaknya secara lengkap, silakan gunakan menu <a href="<?= base_url('export') ?>" class="text-accent">Cetak Dokumen</a>. Geser tabel ke kanan untuk melihat alokasi minggu.</span>
    </div>

    <div class="table-responsive" style="max-height: 600px;">
        <table class="table table-dark table-hover mb-0 align-middle table-bordered" style="background:transparent; min-width: 1200px;">
            <thead style="background:rgba(15,23,42,0.95); position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th rowspan="2" class="align-middle text-center" style="color:#94A3B8;width:60px;">Tipe</th>
                    <th rowspan="2" class="align-middle text-center" style="color:#94A3B8;width:150px;">Kelas & Mapel</th>
                    <th rowspan="2" class="align-middle" style="color:#94A3B8;width:250px;">Materi Pokok / TP</th>
                    <th rowspan="2" class="align-middle text-center" style="color:#94A3B8;width:80px;">JP</th>
                    <!-- Menampilkan Matriks Ganjil saja sebagai Pratinjau (Bulan 7 sd 12) -->
                    <?php 
                    $previewMonths = [7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'];
                    foreach($previewMonths as $m => $mName): 
                    ?>
                        <th colspan="5" class="text-center" style="color:#94A3B8; font-size:0.8rem;"><?= $mName ?></th>
                    <?php endforeach; ?>
                    <th rowspan="2" class="align-middle text-center" style="color:#94A3B8;width:100px;">Aksi</th>
                </tr>
                <tr>
                    <?php foreach($previewMonths as $m => $mName): ?>
                        <th class="text-center p-1" style="color:#94A3B8; font-size:0.75rem;">1</th>
                        <th class="text-center p-1" style="color:#94A3B8; font-size:0.75rem;">2</th>
                        <th class="text-center p-1" style="color:#94A3B8; font-size:0.75rem;">3</th>
                        <th class="text-center p-1" style="color:#94A3B8; font-size:0.75rem;">4</th>
                        <th class="text-center p-1" style="color:#94A3B8; font-size:0.75rem;">5</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($prota)): ?>
                    <tr><td colspan="<?= 4 + (count($previewMonths)*5) + 1 ?>" class="text-center py-4 text-lms-muted">Belum ada data Prota / Promes</td></tr>
                <?php else: ?>
                    <?php foreach ($prota as $p): 
                        $alokasiJson = json_decode($p['alokasi_mingguan'], true) ?: [];
                    ?>
                    <tr>
                        <td class="text-center">
                            <?php if($p['tipe'] === 'Prota'): ?>
                                <span class="badge" style="background:rgba(79,70,229,0.2); color:#818CF8;">PRT</span>
                            <?php else: ?>
                                <span class="badge" style="background:rgba(34,197,94,0.2); color:#4ade80;">PRM</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="color:white;font-weight:600;font-size:0.85rem;"><?= esc($p['nama_kelas']) ?></div>
                            <small class="text-lms-muted" style="font-size:0.75rem;"><?= esc($p['nama_mapel']) ?></small>
                        </td>
                        <td style="white-space:normal;">
                            <span class="text-white" style="font-size:0.85rem;"><?= esc($p['materi_pokok']) ?></span>
                        </td>
                        <td class="text-center text-accent fw-bold" style="font-size:0.85rem;">
                            <?= esc($p['alokasi_waktu']) ?>
                        </td>
                        
                        <?php foreach($previewMonths as $m => $mName): ?>
                            <?php for($w=1; $w<=5; $w++): 
                                $val = isset($alokasiJson[$m][$w]) ? $alokasiJson[$m][$w] : '';
                            ?>
                                <td class="text-center p-1" style="font-size:0.8rem; color:#F8FAFC; border-color:rgba(255,255,255,0.05);">
                                    <?= esc($val) ?>
                                </td>
                            <?php endfor; ?>
                        <?php endforeach; ?>

                        <td class="text-center">
                            <a href="<?= base_url('prota/edit/' . $p['id']) ?>" class="btn btn-sm" style="background:rgba(56,189,248,0.15);color:#38BDF8;border:1px solid rgba(56,189,248,0.3);" title="Edit / Isi Matriks">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="<?= base_url('prota/delete/' . $p['id']) ?>" class="btn btn-sm ms-1" style="background:rgba(239,68,68,0.15);color:#EF4444;border:1px solid rgba(239,68,68,0.3);" title="Hapus" onclick="return confirm('Hapus data ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
