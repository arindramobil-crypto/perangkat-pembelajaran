<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$role   = session()->get('role');
$initials = strtoupper(substr($user['nama_lengkap'] ?? 'U', 0, 2));

// Warna avatar berdasarkan role
$roleColor = ['Admin' => '#EF4444', 'Guru' => '#818CF8', 'Siswa' => '#22C55E'][$role] ?? '#94A3B8';
?>

<div class="row g-4">
    <!-- ══ Kartu Profil ══ -->
    <div class="col-lg-4">
        <!-- Avatar + Info singkat -->
        <div class="glass-panel card text-center mb-4">
            <div class="mx-auto mb-3"
                 style="width:80px;height:80px;border-radius:50%;
                        background:<?= $roleColor ?>22;
                        border:3px solid <?= $roleColor ?>44;
                        display:flex;align-items:center;justify-content:center;
                        font-size:1.8rem;font-weight:700;color:<?= $roleColor ?>;">
                <?= $initials ?>
            </div>
            <h4 style="color:white;margin-bottom:0.3rem;"><?= esc($user['nama_lengkap']) ?></h4>
            <span class="badge mb-3" style="background:<?= $roleColor ?>22;color:<?= $roleColor ?>;border:1px solid <?= $roleColor ?>44;font-size:0.8rem;">
                <?= $role ?>
            </span>

            <?php if (!empty($profileDetail)): ?>
            <div style="border-top:1px solid rgba(255,255,255,0.08);padding-top:1rem;text-align:left;font-size:0.85rem;">
                <?php if (!empty($profileDetail['nip'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-lms-muted">NIP</span>
                    <strong style="color:white;"><?= esc($profileDetail['nip']) ?></strong>
                </div>
                <?php endif; ?>
                <?php if (!empty($profileDetail['nis'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-lms-muted">NIS</span>
                    <strong style="color:white;"><?= esc($profileDetail['nis']) ?></strong>
                </div>
                <?php endif; ?>
                <?php if (!empty($profileDetail['jenis_kelamin'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-lms-muted">Kelamin</span>
                    <strong style="color:white;"><?= $profileDetail['jenis_kelamin']==='L' ? '♂ Laki-laki' : '♀ Perempuan' ?></strong>
                </div>
                <?php endif; ?>
                <?php if (!empty($profileDetail['tanggal_lahir'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-lms-muted">Tgl. Lahir</span>
                    <strong style="color:white;"><?= date('d M Y', strtotime($profileDetail['tanggal_lahir'])) ?></strong>
                </div>
                <?php endif; ?>
                <?php if (!empty($profileDetail['no_telp'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-lms-muted">No. Telp</span>
                    <strong style="color:white;"><?= esc($profileDetail['no_telp']) ?></strong>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Keamanan akun -->
        <div class="glass-panel card">
            <h5 class="card-title mb-3"><i class="bi bi-shield-lock me-2 text-accent"></i>Keamanan Akun</h5>
            <div style="font-size:0.85rem;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-person text-lms-muted"></i>
                    <div>
                        <span class="text-lms-muted d-block" style="font-size:0.75rem;">Username</span>
                        <code style="color:#818CF8;"><?= esc($user['username']) ?></code>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-envelope text-lms-muted"></i>
                    <div>
                        <span class="text-lms-muted d-block" style="font-size:0.75rem;">Email</span>
                        <span style="color:white;"><?= esc($user['email'] ?? '—') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ Form Edit Profil ══ -->
    <div class="col-lg-8">
        <div class="glass-panel card">
            <h4 class="card-title mb-4">
                <i class="bi bi-person-gear me-2 text-accent"></i>Edit Profil Saya
            </h4>

            <!-- Alert flash -->
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert lms-alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert lms-alert-error alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form action="<?= base_url('profil/update') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Info Dasar -->
                <h6 class="text-accent mb-3" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;">
                    <i class="bi bi-person me-1"></i> Informasi Dasar
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control"
                               value="<?= esc($user['nama_lengkap']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= esc($user['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="no_telp" class="form-control"
                               value="<?= esc($profileDetail['no_telp'] ?? '') ?>"
                               placeholder="cth: 08123456789">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control"
                               value="<?= esc($profileDetail['alamat'] ?? '') ?>">
                    </div>
                </div>

                <!-- Ganti Password -->
                <div style="border-top:1px solid rgba(255,255,255,0.08);padding-top:1.5rem;margin-bottom:1.5rem;">
                    <h6 class="text-accent mb-3" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.06em;">
                        <i class="bi bi-key me-1"></i> Ganti Password
                        <small class="text-lms-muted ms-2 text-lowercase">(opsional — kosongkan jika tidak ingin mengganti)</small>
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Password Lama</label>
                            <div class="input-group">
                                <input type="password" name="password_lama" id="passLama"
                                       class="form-control" placeholder="Password saat ini" autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('passLama', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" name="password_baru" id="passBaru"
                                       class="form-control" placeholder="Min. 6 karakter" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePass('passBaru', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="passwordStrength" class="mt-1" style="display:none;">
                                <div style="height:4px;border-radius:2px;background:rgba(255,255,255,0.08);">
                                    <div id="strengthBar" style="height:100%;border-radius:2px;transition:0.3s;width:0%;"></div>
                                </div>
                                <small id="strengthLabel" class="text-lms-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="d-flex justify-content-end gap-3 pt-3" style="border-top:1px solid rgba(255,255,255,0.08);">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save2 me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Toggle show/hide password
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
}

// Password strength indicator
document.getElementById('passBaru').addEventListener('input', function() {
    const val  = this.value;
    const bar  = document.getElementById('strengthBar');
    const lbl  = document.getElementById('strengthLabel');
    const wrap = document.getElementById('passwordStrength');

    if (!val) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';

    let strength = 0;
    if (val.length >= 6)  strength++;
    if (val.length >= 10) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const map = {
        1: ['20%',  '#EF4444', 'Sangat Lemah'],
        2: ['40%',  '#F97316', 'Lemah'],
        3: ['60%',  '#F59E0B', 'Sedang'],
        4: ['80%',  '#22C55E', 'Kuat'],
        5: ['100%', '#10B981', 'Sangat Kuat'],
    };
    const [w, c, t] = map[strength] || ['20%', '#EF4444', 'Sangat Lemah'];
    bar.style.width      = w;
    bar.style.background = c;
    lbl.textContent      = t;
    lbl.style.color      = c;
});
</script>
<?= $this->endSection() ?>
