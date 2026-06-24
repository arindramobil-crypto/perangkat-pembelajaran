<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0;">Data Tahun Pelajaran</h3>
        <button class="btn btn-primary" onclick="showModal()">+ Tambah Tahun</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tahun</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($tahuns as $t): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($t['tahun']) ?></td>
                    <td><?= esc($t['semester']) ?></td>
                    <td>
                        <span class="user-role" style="<?= $t['status'] == 'Aktif' ? 'color: #34d399; background: rgba(52, 211, 153, 0.2);' : '' ?>"><?= esc($t['status']) ?></span>
                    </td>
                    <td>
                        <a href="<?= base_url('master/tahun-pelajaran/delete/'.$t['id']) ?>" class="btn" style="background: var(--danger); color: white; padding: 0.4rem 0.8rem; font-size: 0.875rem;" onclick="return confirm('Yakin hapus data?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="tahunModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: var(--bg-dark); width: 400px; padding: 2rem;">
        <h3 style="color: white; margin-bottom: 1.5rem;">Tambah Tahun Pelajaran</h3>
        <form action="<?= base_url('master/tahun-pelajaran') ?>" method="post">
            <input type="hidden" name="id" id="tahun_id">
            <div class="form-group">
                <label class="form-label">Tahun (Contoh: 2025/2026)</label>
                <input type="text" name="tahun" id="tahun" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-control" required>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                    <option value="Aktif">Aktif</option>
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
    function showModal() { document.getElementById('tahunModal').style.display = 'flex'; }
    function hideModal() { document.getElementById('tahunModal').style.display = 'none'; }
</script>
<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
