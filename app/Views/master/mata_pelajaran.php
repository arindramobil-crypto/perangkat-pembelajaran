<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="lms-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--lms-text);">
                <i class="bi bi-book me-2" style="color: var(--lms-primary);"></i>Data Mata Pelajaran
            </h4>
            <p class="mb-0 small" style="color: var(--lms-text-muted);">Kelola mata pelajaran dan kurikulum</p>
        </div>
        <button class="lms-btn-primary d-flex align-items-center gap-2" onclick="showModal()">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Tambah Mapel</span>
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" id="mapelTable" style="width: 100%;">
            <thead>
                <tr>
                    <th>No</th><th>Kode Mapel</th><th>Nama Mapel</th><th>Kelompok</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($mapels as $m): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><code class="lms-code-badge"><?= esc($m['kode_mapel']) ?></code></td>
                    <td><strong><?= esc($m['nama_mapel']) ?></strong></td>
                    <td>
                        <?php
                        $kmap = [
                            'Nasional'    => ['rgba(16,185,129,0.12)', '#10B981', 'bi-globe'],
                            'Kewilayahan' => ['rgba(245,158,11,0.12)', '#F59E0B', 'bi-map'],
                            'Kejuruan'    => ['rgba(37,99,235,0.12)',  '#2563EB', 'bi-gear'],
                        ];
                        [$bg,$col,$ic] = $kmap[$m['kelompok']] ?? ['rgba(100,116,139,0.1)','#64748B','bi-tag'];
                        ?>
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.78rem;font-weight:600;background:<?= $bg ?>;color:<?= $col ?>;">
                            <i class="bi <?= $ic ?>"></i><?= esc($m['kelompok']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= base_url('master/mata-pelajaran/delete/'.$m['id']) ?>"
                           class="lms-btn-danger-sm"
                           onclick="return confirmDelete(this, '<?= esc($m['nama_mapel']) ?>')">
                            <i class="bi bi-trash3-fill"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH MAPEL -->
<div id="mapelModal" class="lms-modal-overlay" onclick="if(event.target===this)hideModal()">
    <div class="lms-modal-box" id="mapelModalBox">
        <div class="lms-modal-header">
            <div class="lms-modal-icon"><i class="bi bi-book-half"></i></div>
            <div>
                <h5 class="lms-modal-title">Tambah Mata Pelajaran</h5>
                <p class="lms-modal-subtitle">Isi informasi mata pelajaran baru</p>
            </div>
            <button class="lms-modal-close" onclick="hideModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        <form action="<?= base_url('master/mata-pelajaran') ?>" method="post" class="lms-modal-body">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="mapel_id">
            <div class="lms-form-group">
                <label class="lms-form-label"><i class="bi bi-hash me-1"></i>Kode Mapel</label>
                <input type="text" name="kode_mapel" id="kode_mapel" class="lms-form-input"
                       placeholder="Contoh: C3-RPL-01, MAT-01" required
                       style="font-family: monospace; letter-spacing: 0.05em;">
            </div>
            <div class="lms-form-group">
                <label class="lms-form-label"><i class="bi bi-book me-1"></i>Nama Mata Pelajaran</label>
                <input type="text" name="nama_mapel" id="nama_mapel" class="lms-form-input"
                       placeholder="Contoh: Pemrograman Web" required>
            </div>
            <div class="lms-form-group">
                <label class="lms-form-label"><i class="bi bi-layers me-1"></i>Kelompok Mapel</label>
                <select name="kelompok" id="kelompok" class="lms-form-select" required>
                    <option value="Nasional">🌐 Nasional</option>
                    <option value="Kewilayahan">🗺️ Kewilayahan</option>
                    <option value="Kejuruan">⚙️ Kejuruan</option>
                </select>
            </div>
            <div class="lms-modal-footer">
                <button type="button" class="lms-btn-cancel" onclick="hideModal()"><i class="bi bi-x me-1"></i>Batal</button>
                <button type="submit" class="lms-btn-submit"><i class="bi bi-check2-circle me-1"></i>Simpan Mapel</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE CONFIRM -->
<div id="deleteModal" class="lms-modal-overlay" onclick="if(event.target===this)cancelDelete()">
    <div class="lms-modal-box lms-modal-sm" id="deleteModalBox">
        <div class="lms-modal-header lms-modal-danger-header">
            <div class="lms-modal-icon lms-icon-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <h5 class="lms-modal-title">Konfirmasi Hapus</h5>
                <p class="lms-modal-subtitle">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        <div class="lms-modal-body">
            <p style="color:var(--lms-text);margin-bottom:0;">
                Yakin hapus mapel <strong id="deleteTargetName" style="color:var(--lms-danger);">—</strong>?
            </p>
        </div>
        <div class="lms-modal-footer">
            <button type="button" class="lms-btn-cancel" onclick="cancelDelete()"><i class="bi bi-x me-1"></i>Batal</button>
            <a id="deleteConfirmLink" href="#" class="lms-btn-danger"><i class="bi bi-trash3-fill me-1"></i>Ya, Hapus</a>
        </div>
    </div>
</div>

<style>
.lms-code-badge { background: var(--lms-hover-bg); color: var(--lms-primary); border: 1px solid var(--lms-border); padding: 2px 8px; border-radius: 6px; font-size: 0.78rem; }
/* Shared modal & form styles — juga ada di kelas.php */
.lms-btn-primary { background: linear-gradient(135deg,var(--lms-primary),var(--lms-accent)); border:none; color:#fff; font-weight:600; padding:9px 18px; border-radius:10px; box-shadow:0 4px 12px rgba(37,99,235,.3); transition:all .2s; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.lms-btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(37,99,235,.4); color:#fff; }
.lms-btn-danger-sm { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:6px; font-size:.8rem; background:rgba(239,68,68,.1); color:var(--lms-danger); border:1px solid rgba(239,68,68,.25); text-decoration:none; font-weight:500; transition:all .2s; }
.lms-btn-danger-sm:hover { background:var(--lms-danger); color:#fff; border-color:var(--lms-danger); transform:translateY(-1px); box-shadow:0 4px 12px rgba(239,68,68,.35); }
.lms-modal-overlay { display:none; position:fixed; inset:0; z-index:1050; background:rgba(0,0,0,.55); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); justify-content:center; align-items:center; padding:1rem; }
.lms-modal-overlay.active { display:flex; }
.lms-modal-box { background:var(--lms-bg-panel); border:1px solid var(--lms-border); border-radius:16px; box-shadow:0 25px 60px rgba(0,0,0,.25); width:100%; max-width:480px; overflow:hidden; animation:modalSlideIn .3s cubic-bezier(.34,1.56,.64,1); }
.lms-modal-sm { max-width:400px; }
@keyframes modalSlideIn { from{opacity:0;transform:translateY(-24px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes modalSlideOut { from{opacity:1;transform:translateY(0) scale(1)} to{opacity:0;transform:translateY(-16px) scale(.97)} }
.lms-modal-header { display:flex; align-items:flex-start; gap:14px; padding:20px 24px 16px; border-bottom:1px solid var(--lms-border); position:relative; background:linear-gradient(135deg,rgba(37,99,235,.06) 0%,transparent 60%); }
.lms-modal-danger-header { background:linear-gradient(135deg,rgba(239,68,68,.07) 0%,transparent 60%); }
.lms-modal-icon { width:44px; height:44px; border-radius:12px; flex-shrink:0; background:linear-gradient(135deg,var(--lms-primary),var(--lms-accent)); color:#fff; font-size:1.2rem; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(37,99,235,.35); }
.lms-icon-danger { background:linear-gradient(135deg,#EF4444,#F97316); box-shadow:0 4px 12px rgba(239,68,68,.35); }
.lms-modal-title { font-size:1rem; font-weight:700; color:var(--lms-text); margin:0 0 2px; }
.lms-modal-subtitle { font-size:.78rem; color:var(--lms-text-muted); margin:0; }
.lms-modal-close { position:absolute; top:14px; right:16px; background:var(--lms-hover-bg); border:1px solid var(--lms-border); border-radius:8px; color:var(--lms-text-muted); width:32px; height:32px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.85rem; transition:all .2s; }
.lms-modal-close:hover { background:rgba(239,68,68,.1); color:var(--lms-danger); border-color:rgba(239,68,68,.3); }
.lms-modal-body { padding:20px 24px 0; }
.lms-form-group { margin-bottom:16px; }
.lms-form-label { display:flex; align-items:center; font-size:.82rem; font-weight:600; color:var(--lms-text-muted); margin-bottom:6px; text-transform:uppercase; letter-spacing:.04em; }
.lms-form-input,.lms-form-select { width:100%; padding:10px 14px; background:var(--lms-input-bg); border:1.5px solid var(--lms-border); border-radius:10px; color:var(--lms-text); font-size:.9rem; font-family:inherit; transition:all .2s; outline:none; appearance:none; }
.lms-form-input::placeholder { color:var(--lms-text-muted); }
.lms-form-input:focus,.lms-form-select:focus { border-color:var(--lms-primary); box-shadow:0 0 0 3px rgba(37,99,235,.15); background:var(--lms-bg-panel); }
.lms-form-select { background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%2364748B' d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; background-size:14px; padding-right:38px; cursor:pointer; }
.lms-modal-footer { display:flex; justify-content:flex-end; gap:10px; padding:16px 24px 20px; margin-top:16px; border-top:1px solid var(--lms-border); }
.lms-btn-cancel { display:inline-flex; align-items:center; padding:9px 18px; border-radius:10px; background:var(--lms-hover-bg); border:1.5px solid var(--lms-border); color:var(--lms-text-muted); font-size:.875rem; font-weight:500; cursor:pointer; transition:all .2s; text-decoration:none; }
.lms-btn-cancel:hover { background:var(--lms-border); color:var(--lms-text); }
.lms-btn-submit { display:inline-flex; align-items:center; padding:9px 22px; border-radius:10px; background:linear-gradient(135deg,var(--lms-primary),var(--lms-accent)); border:none; color:#fff; font-size:.875rem; font-weight:600; cursor:pointer; transition:all .2s; box-shadow:0 4px 12px rgba(37,99,235,.3); }
.lms-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(37,99,235,.4); }
.lms-btn-danger { display:inline-flex; align-items:center; padding:9px 22px; border-radius:10px; background:linear-gradient(135deg,#EF4444,#F97316); border:none; color:#fff; font-size:.875rem; font-weight:600; text-decoration:none; transition:all .2s; box-shadow:0 4px 12px rgba(239,68,68,.3); }
.lms-btn-danger:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(239,68,68,.4); color:#fff; }
</style>

<script>
function showModal() {
    document.getElementById('mapelModal').classList.add('active');
    setTimeout(() => document.getElementById('kode_mapel').focus(), 100);
    document.body.style.overflow = 'hidden';
}
function hideModal() {
    const box = document.getElementById('mapelModalBox');
    box.style.animation = 'modalSlideOut .2s ease forwards';
    setTimeout(() => { document.getElementById('mapelModal').classList.remove('active'); box.style.animation = ''; document.body.style.overflow = ''; }, 190);
}
function confirmDelete(anchor, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteConfirmLink').href = anchor.href;
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden'; return false;
}
function cancelDelete() {
    const box = document.getElementById('deleteModalBox');
    box.style.animation = 'modalSlideOut .2s ease forwards';
    setTimeout(() => { document.getElementById('deleteModal').classList.remove('active'); box.style.animation = ''; document.body.style.overflow = ''; }, 190);
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        if (document.getElementById('mapelModal').classList.contains('active')) hideModal();
        if (document.getElementById('deleteModal').classList.contains('active')) cancelDelete();
    }
});
</script>

<?= $this->endSection() ?>
