<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="lms-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--lms-text);">
                <i class="bi bi-door-open me-2" style="color: var(--lms-primary);"></i>Data Kelas
            </h4>
            <p class="mb-0 small" style="color: var(--lms-text-muted);">Kelola data kelas dan wali kelas</p>
        </div>
        <button class="btn lms-btn-primary d-flex align-items-center gap-2" onclick="showModal()">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Tambah Kelas</span>
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" id="kelasTable" style="width: 100%;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Jurusan</th>
                    <th>Wali Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($kelas as $k): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <span class="lms-badge-kelas">
                            <i class="bi bi-building me-1"></i><?= esc($k['nama_kelas']) ?>
                        </span>
                    </td>
                    <td><?= esc($k['jurusan']) ?></td>
                    <td>
                        <?php if (!empty($k['wali_kelas'])): ?>
                            <span class="d-flex align-items-center gap-2">
                                <span class="lms-avatar-sm"><?= strtoupper(substr($k['wali_kelas'], 0, 1)) ?></span>
                                <?= esc($k['wali_kelas']) ?>
                            </span>
                        <?php else: ?>
                            <span style="color: var(--lms-text-muted); font-style: italic;">— Belum ada</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('master/kelas/delete/'.$k['id']) ?>"
                           class="lms-btn-danger-sm"
                           onclick="return confirmDelete(this, '<?= esc($k['nama_kelas']) ?>')">
                            <i class="bi bi-trash3-fill"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ══════════ MODAL TAMBAH KELAS ══════════ -->
<div id="kelasModal" class="lms-modal-overlay" onclick="handleOverlayClick(event)">
    <div class="lms-modal-box" id="kelasModalBox">

        <!-- Header -->
        <div class="lms-modal-header">
            <div class="lms-modal-icon">
                <i class="bi bi-building-add"></i>
            </div>
            <div>
                <h5 class="lms-modal-title">Tambah Kelas Baru</h5>
                <p class="lms-modal-subtitle">Isi informasi kelas yang ingin ditambahkan</p>
            </div>
            <button class="lms-modal-close" onclick="hideModal()" title="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <form action="<?= base_url('master/kelas') ?>" method="post" class="lms-modal-body">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="kelas_id">

            <div class="lms-form-group">
                <label class="lms-form-label">
                    <i class="bi bi-tag me-1"></i>Nama Kelas
                </label>
                <input type="text"
                       name="nama_kelas"
                       id="nama_kelas"
                       class="lms-form-input"
                       placeholder="Contoh: X RPL 1, XI TKJ 2"
                       required
                       autocomplete="off">
            </div>

            <div class="lms-form-group">
                <label class="lms-form-label">
                    <i class="bi bi-mortarboard me-1"></i>Jurusan
                </label>
                <input type="text"
                       name="jurusan"
                       id="jurusan"
                       class="lms-form-input"
                       placeholder="Contoh: Rekayasa Perangkat Lunak"
                       required
                       autocomplete="off">
            </div>

            <div class="lms-form-group">
                <label class="lms-form-label">
                    <i class="bi bi-person-badge me-1"></i>Wali Kelas
                    <span class="lms-label-optional">Opsional</span>
                </label>
                <select name="wali_kelas_id" id="wali_kelas_id" class="lms-form-select">
                    <option value="">— Pilih Wali Kelas —</option>
                    <?php foreach($gurus as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?> <?= $g['nip'] ? '(NIP: '.esc($g['nip']).')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Footer -->
            <div class="lms-modal-footer">
                <button type="button" class="lms-btn-cancel" onclick="hideModal()">
                    <i class="bi bi-x me-1"></i>Batal
                </button>
                <button type="submit" class="lms-btn-submit">
                    <i class="bi bi-check2-circle me-1"></i>Simpan Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════ CONFIRM DELETE DIALOG ══════════ -->
<div id="deleteModal" class="lms-modal-overlay" onclick="handleDeleteOverlay(event)">
    <div class="lms-modal-box lms-modal-sm" id="deleteModalBox">
        <div class="lms-modal-header lms-modal-danger-header">
            <div class="lms-modal-icon lms-icon-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h5 class="lms-modal-title">Konfirmasi Hapus</h5>
                <p class="lms-modal-subtitle">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        <div class="lms-modal-body">
            <p style="color: var(--lms-text); margin-bottom: 0;">
                Yakin ingin menghapus kelas <strong id="deleteTargetName" style="color: var(--lms-danger);">—</strong>?
            </p>
        </div>
        <div class="lms-modal-footer">
            <button type="button" class="lms-btn-cancel" onclick="cancelDelete()">
                <i class="bi bi-x me-1"></i>Batal
            </button>
            <a id="deleteConfirmLink" href="#" class="lms-btn-danger">
                <i class="bi bi-trash3-fill me-1"></i>Ya, Hapus
            </a>
        </div>
    </div>
</div>

<style>
/* ── Tabel badge & avatar ────────────────────────────── */
.lms-badge-kelas {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(37, 99, 235, 0.12);
    color: var(--lms-primary);
    font-weight: 600; font-size: 0.82rem;
    padding: 3px 10px; border-radius: 20px;
    border: 1px solid rgba(37, 99, 235, 0.2);
}
.lms-avatar-sm {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--lms-primary), var(--lms-accent));
    color: #fff; font-size: 0.7rem; font-weight: 700;
    display: inline-flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* ── Danger button kecil ─────────────────────────────── */
.lms-btn-danger-sm {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 12px; border-radius: 6px; font-size: 0.8rem;
    background: rgba(239, 68, 68, 0.1);
    color: var(--lms-danger);
    border: 1px solid rgba(239, 68, 68, 0.25);
    text-decoration: none; font-weight: 500;
    transition: all 0.2s;
}
.lms-btn-danger-sm:hover {
    background: var(--lms-danger); color: #fff;
    border-color: var(--lms-danger);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239,68,68,0.35);
}

/* ── Modal overlay ───────────────────────────────────── */
.lms-modal-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 1050;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    justify-content: center; align-items: center;
    padding: 1rem;
}
.lms-modal-overlay.active { display: flex; }

/* ── Modal box ───────────────────────────────────────── */
.lms-modal-box {
    background: var(--lms-bg-panel);
    border: 1px solid var(--lms-border);
    border-radius: 16px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(255,255,255,0.05);
    width: 100%; max-width: 480px;
    overflow: hidden;
    animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.lms-modal-sm { max-width: 400px; }

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-24px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes modalSlideOut {
    from { opacity: 1; transform: translateY(0) scale(1); }
    to   { opacity: 0; transform: translateY(-16px) scale(0.97); }
}

/* ── Modal header ────────────────────────────────────── */
.lms-modal-header {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--lms-border);
    position: relative;
    background: linear-gradient(135deg,
        rgba(37, 99, 235, 0.06) 0%,
        transparent 60%);
}
.lms-modal-danger-header {
    background: linear-gradient(135deg,
        rgba(239, 68, 68, 0.07) 0%,
        transparent 60%);
}
.lms-modal-icon {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--lms-primary), var(--lms-accent));
    color: #fff; font-size: 1.2rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
}
.lms-icon-danger {
    background: linear-gradient(135deg, #EF4444, #F97316);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
}
.lms-modal-title {
    font-size: 1rem; font-weight: 700;
    color: var(--lms-text); margin: 0 0 2px;
}
.lms-modal-subtitle {
    font-size: 0.78rem; color: var(--lms-text-muted); margin: 0;
}
.lms-modal-close {
    position: absolute; top: 14px; right: 16px;
    background: var(--lms-hover-bg);
    border: 1px solid var(--lms-border);
    border-radius: 8px; color: var(--lms-text-muted);
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 0.85rem;
    transition: all 0.2s;
}
.lms-modal-close:hover {
    background: rgba(239,68,68,0.1); color: var(--lms-danger);
    border-color: rgba(239,68,68,0.3);
}

/* ── Modal body ──────────────────────────────────────── */
.lms-modal-body { padding: 20px 24px 0; }

/* ── Form elements ───────────────────────────────────── */
.lms-form-group { margin-bottom: 16px; }
.lms-form-label {
    display: flex; align-items: center;
    font-size: 0.82rem; font-weight: 600;
    color: var(--lms-text-muted);
    margin-bottom: 6px; text-transform: uppercase;
    letter-spacing: 0.04em;
}
.lms-label-optional {
    margin-left: auto; font-size: 0.72rem; font-weight: 500;
    color: var(--lms-text-muted);
    background: var(--lms-hover-bg);
    border: 1px solid var(--lms-border);
    padding: 1px 7px; border-radius: 20px;
    text-transform: none; letter-spacing: 0;
}
.lms-form-input,
.lms-form-select {
    width: 100%; padding: 10px 14px;
    background: var(--lms-input-bg);
    border: 1.5px solid var(--lms-border);
    border-radius: 10px;
    color: var(--lms-text);
    font-size: 0.9rem; font-family: inherit;
    transition: all 0.2s;
    outline: none; appearance: none;
}
.lms-form-input::placeholder { color: var(--lms-text-muted); }
.lms-form-input:focus,
.lms-form-select:focus {
    border-color: var(--lms-primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
    background: var(--lms-bg-panel);
}
.lms-form-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%2364748B' d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 14px;
    padding-right: 38px;
    cursor: pointer;
}

/* ── Modal footer ────────────────────────────────────── */
.lms-modal-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 16px 24px 20px;
    margin-top: 16px;
    border-top: 1px solid var(--lms-border);
}
.lms-btn-cancel {
    display: inline-flex; align-items: center;
    padding: 9px 18px; border-radius: 10px;
    background: var(--lms-hover-bg);
    border: 1.5px solid var(--lms-border);
    color: var(--lms-text-muted); font-size: 0.875rem;
    font-weight: 500; cursor: pointer;
    transition: all 0.2s; text-decoration: none;
}
.lms-btn-cancel:hover {
    background: var(--lms-border);
    color: var(--lms-text);
    border-color: var(--lms-text-muted);
}
.lms-btn-submit {
    display: inline-flex; align-items: center;
    padding: 9px 22px; border-radius: 10px;
    background: linear-gradient(135deg, var(--lms-primary), var(--lms-accent));
    border: none; color: #fff; font-size: 0.875rem;
    font-weight: 600; cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}
.lms-btn-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37,99,235,0.4);
    background: linear-gradient(135deg, var(--lms-primary-hover), var(--lms-primary));
}
.lms-btn-danger {
    display: inline-flex; align-items: center;
    padding: 9px 22px; border-radius: 10px;
    background: linear-gradient(135deg, #EF4444, #F97316);
    border: none; color: #fff; font-size: 0.875rem;
    font-weight: 600; text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(239,68,68,0.3);
}
.lms-btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(239,68,68,0.4); color: #fff;
}

/* ── Primary button ─────────────────────────────────── */
.lms-btn-primary {
    background: linear-gradient(135deg, var(--lms-primary), var(--lms-accent));
    border: none; color: #fff; font-weight: 600;
    padding: 9px 18px; border-radius: 10px;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    transition: all 0.2s; cursor: pointer;
}
.lms-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37,99,235,0.4);
    color: #fff;
}
</style>

<script>
/* ── Modal Show/Hide dengan animasi ──────────────── */
function showModal() {
    const overlay = document.getElementById('kelasModal');
    overlay.classList.add('active');
    setTimeout(() => document.getElementById('nama_kelas').focus(), 100);
    document.body.style.overflow = 'hidden';
}

function hideModal() {
    const box = document.getElementById('kelasModalBox');
    box.style.animation = 'modalSlideOut 0.2s ease forwards';
    setTimeout(() => {
        document.getElementById('kelasModal').classList.remove('active');
        box.style.animation = '';
        document.body.style.overflow = '';
    }, 190);
}

function handleOverlayClick(e) {
    if (e.target.id === 'kelasModal') hideModal();
}

/* ── Delete Confirm Dialog ───────────────────────── */
let _pendingDeleteHref = null;
function confirmDelete(anchor, name) {
    _pendingDeleteHref = anchor.href;
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteConfirmLink').href = _pendingDeleteHref;
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    return false;
}
function cancelDelete() {
    const box = document.getElementById('deleteModalBox');
    box.style.animation = 'modalSlideOut 0.2s ease forwards';
    setTimeout(() => {
        document.getElementById('deleteModal').classList.remove('active');
        box.style.animation = '';
        document.body.style.overflow = '';
    }, 190);
}
function handleDeleteOverlay(e) {
    if (e.target.id === 'deleteModal') cancelDelete();
}

/* ── Tutup modal dengan tombol Escape ─────────────── */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('kelasModal').classList.contains('active')) hideModal();
        if (document.getElementById('deleteModal').classList.contains('active')) cancelDelete();
    }
});
</script>

<?= $this->endSection() ?>
