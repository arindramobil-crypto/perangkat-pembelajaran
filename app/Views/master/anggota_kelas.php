<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0.5rem;">Manajemen Anggota Kelas</h3>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Pilih Kelas dan Tahun Pelajaran untuk mengelola anggota (siswa).</p>
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
        <h4 style="color: white;">Daftar Siswa di Kelas Ini</h4>
        <button class="btn btn-primary" onclick="showModal()">+ Tambah Siswa ke Kelas</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
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
    <div id="addModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; overflow-y: auto;">
        <div class="glass-panel" style="background: var(--bg-dark); width: 600px; padding: 2rem; margin: auto;">
            <h3 style="color: white; margin-bottom: 1.5rem;">Pilih Siswa (Belum Punya Kelas)</h3>
            
            <form action="<?= base_url('master/anggota-kelas/save') ?>" method="post">
                <input type="hidden" name="kelas_id" value="<?= $selected_kelas ?>">
                <input type="hidden" name="tahun_pelajaran_id" value="<?= $selected_tahun ?>">
                
                <div style="max-height: 300px; overflow-y: auto; border: 1px solid var(--border-color); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <?php if(empty($siswaBebas)): ?>
                        <p style="color: var(--text-muted); text-align: center;">Semua siswa sudah masuk ke kelas di tahun ini, atau tidak ada data siswa baru.</p>
                    <?php else: ?>
                        <?php foreach($siswaBebas as $sb): ?>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <input type="checkbox" name="siswa_ids[]" value="<?= $sb['id'] ?>" id="s_<?= $sb['id'] ?>">
                            <label for="s_<?= $sb['id'] ?>" style="color: white; cursor: pointer; flex: 1;"><?= esc($sb['nis']) ?> - <?= esc($sb['nama_lengkap']) ?></label>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn" style="background: transparent; color: white; border: 1px solid var(--border-color);" onclick="hideModal()">Batal</button>
                    <?php if(!empty($siswaBebas)): ?>
                    <button type="submit" class="btn btn-primary">Masukkan ke Kelas</button>
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
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
