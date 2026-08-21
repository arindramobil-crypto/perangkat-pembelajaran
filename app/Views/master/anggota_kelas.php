<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0.5rem;">Manajemen Anggota Kelas</h3>
        <p style="color: var(--lms-text-muted); font-size: 0.875rem;">Pilih Kelas dan Tahun Pelajaran untuk mengelola anggota (siswa).</p>
    </div>

    <!-- Filter Form -->
    <form action="" method="get" style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <div style="flex: 1;">
            <label class="form-label">Tahun Pelajaran</label>
            <select name="tahun_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Pilih Tahun Pelajaran --</option>
                <?php foreach($tahunList as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $selected_tahun == $t['id'] ? 'selected' : '' ?>><?= esc($t['tahun']) ?> - <?= esc($t['semester']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1;">
            <label class="form-label">Kelas</label>
            <select name="kelas_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $selected_kelas == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if($selected_kelas && $selected_tahun): ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h4 style="color: var(--lms-text);">Daftar Siswa di Kelas Ini</h4>
        <button class="btn btn-primary" onclick="showModal()">+ Tambah Siswa ke Kelas</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: var(--lms-text); text-align: left;">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($anggota as $a): ?>
                <tr>
                    <td><?= esc($a['nis']) ?></td>
                    <td><?= esc($a['nama_lengkap']) ?></td>
                    <td><?= esc($a['username']) ?></td>
                    <td>
                        <a href="<?= base_url('master/anggota-kelas/delete/'.$a['id']) ?>" class="btn" style="background: var(--danger); color: white; padding: 0.3rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Keluarkan siswa ini dari kelas?')">Keluarkan</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Siswa -->
    <div id="addModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); z-index: 1050; justify-content: center; align-items: center; padding: 1rem;">
        <div style="background: var(--lms-bg-panel); width: 100%; max-width: 550px; border-radius: 16px; border: 1px solid var(--lms-border); box-shadow: var(--lms-shadow); display: flex; flex-direction: column; overflow: hidden; animation: modalFadeIn 0.3s ease forwards;">
            
            <!-- Modal Header -->
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--lms-border); display: flex; justify-content: space-between; align-items: flex-start; background: var(--lms-hover-bg);">
                <div>
                    <h3 style="color: var(--lms-text); margin: 0; font-size: 1.25rem; font-weight: 600;">Tambah Anggota Kelas</h3>
                    <p style="color: var(--lms-text-muted); margin: 0.5rem 0 0 0; font-size: 0.875rem;">Pilih siswa yang belum masuk ke kelas ini.</p>
                </div>
                <button type="button" onclick="hideModal()" style="background: transparent; border: none; color: var(--lms-text-muted); cursor: pointer; padding: 0.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.color='var(--lms-text)'; this.style.background='var(--lms-hover-bg)';" onmouseout="this.style.color='var(--lms-text-muted)'; this.style.background='transparent';">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form action="<?= base_url('master/anggota-kelas/save') ?>" method="post" style="display: flex; flex-direction: column; max-height: calc(100vh - 120px);">
                <input type="hidden" name="kelas_id" value="<?= $selected_kelas ?>">
                <input type="hidden" name="tahun_pelajaran_id" value="<?= $selected_tahun ?>">
                
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    <?php if(empty($siswaBebas)): ?>
                        <div style="text-align: center; padding: 3rem 1rem; background: var(--lms-hover-bg); border-radius: 12px; border: 1px dashed var(--lms-border);">
                            <svg width="48" height="48" fill="none" stroke="var(--lms-text-muted)" viewBox="0 0 24 24" style="margin-bottom: 1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p style="color: var(--lms-text); font-weight: 500; margin: 0 0 0.5rem 0;">Semua siswa sudah memiliki kelas</p>
                            <p style="color: var(--lms-text-muted); margin: 0; font-size: 0.875rem;">Tidak ada data siswa baru yang belum dimasukkan ke kelas di tahun ini.</p>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php foreach($siswaBebas as $sb): ?>
                            <label class="siswa-checkbox-card">
                                <input type="checkbox" name="siswa_ids[]" value="<?= $sb['id'] ?>" class="siswa-checkbox-input">
                                <div class="siswa-info">
                                    <div class="siswa-name"><?= esc($sb['nama_lengkap']) ?></div>
                                    <div class="siswa-nis">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                        NIS: <?= esc($sb['nis']) ?>
                                    </div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Modal Footer -->
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--lms-border); display: flex; gap: 1rem; justify-content: flex-end; background: var(--lms-hover-bg);">
                    <button type="button" class="btn" style="background: var(--lms-bg); color: var(--lms-text); border: 1px solid var(--lms-border);" onclick="hideModal()">Batal</button>
                    <?php if(!empty($siswaBebas)): ?>
                    <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; padding-left: 1.25rem; padding-right: 1.25rem;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambahkan ke Kelas
                    </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<script>
    function showModal() { document.getElementById('addModal').style.display = 'flex'; }
    function hideModal() { document.getElementById('addModal').style.display = 'none'; }
</script>
<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--lms-text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
    
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .siswa-checkbox-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        background: var(--lms-hover-bg);
        border: 1px solid var(--lms-border);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .siswa-checkbox-card:hover {
        background: var(--lms-input-bg);
        border-color: var(--lms-primary);
    }
    
    /* Simulate :has selector for checked state styling */
    .siswa-checkbox-card:has(.siswa-checkbox-input:checked) {
        background: rgba(37, 99, 235, 0.1); /* Primary color with opacity */
        border-color: var(--lms-primary);
    }
    
    .siswa-checkbox-input {
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
        accent-color: var(--lms-primary);
    }
    
    .siswa-info {
        flex: 1;
    }
    
    .siswa-name {
        color: var(--lms-text);
        font-weight: 500;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }
    
    .siswa-nis {
        color: var(--lms-text-muted);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
</style>
<?= $this->endSection() ?>
