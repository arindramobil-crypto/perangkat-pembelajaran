<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Header -->
<div class="glass-panel card mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="card-title mb-1">Data Guru</h3>
            <p class="text-lms-muted small mb-0">Kelola akun dan profil seluruh guru.</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2" onclick="bukaModal()">
            <i class="bi bi-person-plus-fill"></i> Tambah Guru
        </button>
    </div>
</div>

<!-- Tabel -->
<div class="glass-panel card">
    <div class="table-responsive">
        <table class="table datatable w-100 align-middle">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>NIP</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>No. Telp</th>
                    <th style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($gurus as $g): ?>
                <tr>
                    <td class="text-lms-muted"><?= $no++ ?></td>
                    <td class="text-lms-muted"><?= esc($g['nip'] ?? '-') ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="lms-avatar" style="width:32px;height:32px;font-size:0.8rem;">
                                <?= strtoupper(substr($g['nama_lengkap'], 0, 1)) ?>
                            </div>
                            <strong style="color:white;"><?= esc($g['nama_lengkap']) ?></strong>
                        </div>
                    </td>
                    <td><code style="color:#818CF8;"><?= esc($g['username']) ?></code></td>
                    <td class="text-lms-muted"><?= esc($g['email'] ?? '-') ?></td>
                    <td class="text-lms-muted"><?= esc($g['no_telp'] ?? '-') ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-warning"
                                    onclick="bukaEdit(<?= htmlspecialchars(json_encode($g), ENT_QUOTES) ?>)"
                                    title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="<?= base_url('users/guru/delete/'.$g['id']) ?>"
                               class="btn btn-sm btn-outline-danger" title="Hapus"
                               onclick="return confirm('Hapus guru dan akun loginnya?')">
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

<!-- ══ Modal Tambah Guru ══ -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Akun Guru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('users/guru') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert lms-alert-success mb-3 py-2">
                        <i class="bi bi-key me-2"></i>Password default guru baru: <strong>guru123</strong>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username (Login) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required placeholder="cth: budi.santoso">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" placeholder="NIP Guru">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control">
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

<!-- ══ Modal Edit Guru ══ -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Data Guru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('users/guru/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="edit_guru_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" id="edit_nip" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" id="edit_no_telp" class="form-control">
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
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                Password Baru
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
function bukaModal() {
    new bootstrap.Modal(document.getElementById('modalTambah')).show();
}

function bukaEdit(data) {
    document.getElementById('edit_guru_id').value       = data.id;
    document.getElementById('edit_username').value      = data.username;
    document.getElementById('edit_nip').value           = data.nip || '';
    document.getElementById('edit_nama').value          = data.nama_lengkap;
    document.getElementById('edit_email').value         = data.email || '';
    document.getElementById('edit_no_telp').value       = data.no_telp || '';
    document.getElementById('edit_jk').value            = data.jenis_kelamin || 'L';
    document.getElementById('edit_tempat_lahir').value  = data.tempat_lahir || '';
    document.getElementById('edit_tgl_lahir').value     = data.tanggal_lahir || '';
    document.getElementById('edit_alamat').value        = data.alamat || '';
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
<?= $this->endSection() ?>
