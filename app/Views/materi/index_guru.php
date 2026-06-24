<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Kartu aksi atas -->
<div class="glass-panel card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="card-title mb-1">Materi Pembelajaran Saya</h3>
            <p class="text-lms-muted small mb-0">Kelola bahan ajar yang telah Anda unggah untuk setiap kelas.</p>
        </div>
        <a href="<?= base_url('materi/create') ?>" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Tambah Materi
        </a>
    </div>
</div>

<!-- Tabel daftar materi -->
<div class="glass-panel card">
    <div class="table-responsive">
        <table class="table datatable w-100 align-middle">
            <thead>
                <tr>
                    <th style="width:130px;">Tgl Upload</th>
                    <th>Kelas & Mapel</th>
                    <th>Judul Materi</th>
                    <th style="width:90px;">Tipe</th>
                    <th style="width:160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materis as $m): ?>
                <tr>
                    <td>
                        <span class="text-lms-muted" style="font-size:0.82rem;">
                            <?= date('d M Y', strtotime($m['created_at'])) ?>
                        </span>
                    </td>
                    <td>
                        <strong class="text-accent"><?= esc($m['nama_kelas']) ?></strong><br>
                        <small class="text-lms-muted"><?= esc($m['nama_mapel']) ?> — <?= esc($m['hari']) ?></small>
                    </td>
                    <td>
                        <strong><?= esc($m['judul_materi']) ?></strong><br>
                        <small class="text-lms-muted"><?= esc(mb_substr($m['deskripsi'] ?? '', 0, 60)) ?>...</small>
                    </td>
                    <td>
                        <?php if ($m['tipe_konten'] === 'link'): ?>
                        <span class="badge bg-info text-dark">
                            <i class="bi bi-link-45deg"></i> Link
                        </span>
                        <?php else: ?>
                        <span class="badge" style="background:rgba(129,140,248,0.2);color:#818CF8;">
                            <i class="bi bi-file-earmark"></i> File
                        </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <?php if ($m['tipe_konten'] === 'link'): ?>
                            <a href="<?= esc($m['link_eksternal']) ?>" target="_blank"
                               class="btn btn-sm btn-outline-info" title="Buka Link">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <?php else: ?>
                            <a href="<?= base_url('materi/download/' . $m['id']) ?>"
                               class="btn btn-sm btn-outline-secondary" title="Unduh">
                                <i class="bi bi-download"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?= base_url('materi/edit/' . $m['id']) ?>"
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= base_url('materi/delete/' . $m['id']) ?>"
                               class="btn btn-sm btn-outline-danger" title="Hapus"
                               onclick="return confirm('Yakin hapus materi ini? File juga akan ikut terhapus.')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
