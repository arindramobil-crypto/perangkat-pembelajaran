<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="card-title mb-1">Data Siswa</h3>
            <p class="text-lms-muted small mb-0">Kelola akun dan profil seluruh siswa.</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-person-plus-fill"></i> Tambah Siswa
        </button>
    </div>
</div>

<div class="glass-panel card">
    <div class="table-responsive">
        <table class="table datatable w-100 align-middle">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Kelamin</th>
                    <th style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($siswas as $s): ?>
                <tr>
                    <td class="text-lms-muted"><?= $no++ ?></td>
                    <td><code style="color:#818CF8;"><?= esc($s['nis']) ?></code></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lms-avatar" style="width:32px;height:32px;font-size:0.8rem;background:rgba(34,197,94,0.15);color:#22C55E;">
                                <?= strtoupper(substr($s['nama_lengkap'], 0, 1)) ?>
                            </div>
                            <strong style="color:white;"><?= esc($s['nama_lengkap']) ?></strong>
                        </div>
                    </td>
                    <td class="text-lms-muted"><?= esc($s['username']) ?></td>
                    <td class="text-lms-muted"><?= esc($s['email'] ?? '-') ?></td>
                    <td>
                        <span class="badge" style="background:rgba(<?= $s['jenis_kelamin']==='L' ? '56,189,248' : '244,114,182' ?>,0.15);color:<?= $s['jenis_kelamin']==='L' ? '#38BDF8' : '#F472B6' ?>;">
                            <?= $s['jenis_kelamin']==='L' ? '♂ L' : '♀ P' ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-warning"
                                    onclick="bukaEdit(<?= htmlspecialchars(json_encode($s), ENT_QUOTES) ?>)"
                                    title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="<?= base_url('users/siswa/delete/'.$s['id']) ?>"
                               class="btn btn-sm btn-outline-danger" title="Hapus"
                               onclick="return confirm('Hapus siswa dan akun loginnya?')">
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Akun Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('users/siswa') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert lms-alert-success mb-3 py-2">
                        <i class="bi bi-key me-2"></i>Password default siswa baru: <strong>siswa123</strong>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username (Login) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Data Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('users/siswa/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="edit_siswa_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" name="nis" id="edit_nis" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" id="edit_nisn" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jk" class="form-select">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_tgl_lahir" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" id="edit_no_telp" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password Baru
                                <small class="text-lms-muted">(kosongkan jika tidak ingin mengganti)</small>
                            </label>
                            <input type="password" name="password_baru" class="form-control"
                                   placeholder="Min. 6 karakter" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function bukaEdit(data) {
    document.getElementById('edit_siswa_id').value       = data.id;
    document.getElementById('edit_username').value       = data.username;
    document.getElementById('edit_nis').value            = data.nis;
    document.getElementById('edit_nisn').value           = data.nisn || '';
    document.getElementById('edit_nama').value           = data.nama_lengkap;
    document.getElementById('edit_email').value          = data.email || '';
    document.getElementById('edit_jk').value             = data.jenis_kelamin || 'L';
    document.getElementById('edit_tempat_lahir').value   = data.tempat_lahir || '';
    document.getElementById('edit_tgl_lahir').value      = data.tanggal_lahir || '';
    document.getElementById('edit_no_telp').value        = data.no_telp || '';
    document.getElementById('edit_alamat').value         = data.alamat || '';
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
<?= $this->endSection() ?>
