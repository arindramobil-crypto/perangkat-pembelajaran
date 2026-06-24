<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0;">Data Kelas</h3>
        <button class="btn btn-primary" onclick="showModal()">+ Tambah Kelas</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
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
                    <td><?= esc($k['nama_kelas']) ?></td>
                    <td><?= esc($k['jurusan']) ?></td>
                    <td><?= esc($k['wali_kelas'] ?? 'Belum ada') ?></td>
                    <td>
                        <a href="<?= base_url('master/kelas/delete/'.$k['id']) ?>" class="btn" style="background: var(--danger); color: white; padding: 0.4rem 0.8rem; font-size: 0.875rem;" onclick="return confirm('Yakin hapus data?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="kelasModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: var(--bg-dark); width: 400px; padding: 2rem;">
        <h3 style="color: white; margin-bottom: 1.5rem;">Tambah Kelas</h3>
        <form action="<?= base_url('master/kelas') ?>" method="post">
            <input type="hidden" name="id" id="kelas_id">
            <div class="form-group">
                <label class="form-label">Nama Kelas</label>
                <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jurusan</label>
                <input type="text" name="jurusan" id="jurusan" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Wali Kelas (Opsional)</label>
                <select name="wali_kelas_id" id="wali_kelas_id" class="form-control">
                    <option value="">-- Pilih Wali Kelas --</option>
                    <?php foreach($gurus as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?> (NIP: <?= esc($g['nip'] ?? '-') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn" style="background: transparent; color: white; border: 1px solid var(--border-color);" onclick="hideModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showModal() { document.getElementById('kelasModal').style.display = 'flex'; }
    function hideModal() { document.getElementById('kelasModal').style.display = 'none'; }
</script>
<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
