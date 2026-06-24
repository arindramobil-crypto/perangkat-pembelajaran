<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0;">Data Mata Pelajaran</h3>
        <button class="btn btn-primary" onclick="showModal()">+ Tambah Mapel</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Mapel</th>
                    <th>Nama Mapel</th>
                    <th>Kelompok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($mapels as $m): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($m['kode_mapel']) ?></td>
                    <td><?= esc($m['nama_mapel']) ?></td>
                    <td><?= esc($m['kelompok']) ?></td>
                    <td>
                        <a href="<?= base_url('master/mata-pelajaran/delete/'.$m['id']) ?>" class="btn" style="background: var(--danger); color: white; padding: 0.4rem 0.8rem; font-size: 0.875rem;" onclick="return confirm('Yakin hapus data?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Background & Dialog -->
<div id="mapelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: var(--bg-dark); width: 400px; padding: 2rem;">
        <h3 style="color: white; margin-bottom: 1.5rem;">Tambah Mapel</h3>
        <form action="<?= base_url('master/mata-pelajaran') ?>" method="post">
            <input type="hidden" name="id" id="mapel_id">
            <div class="form-group">
                <label class="form-label">Kode Mapel</label>
                <input type="text" name="kode_mapel" id="kode_mapel" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Mapel</label>
                <input type="text" name="nama_mapel" id="nama_mapel" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kelompok</label>
                <select name="kelompok" id="kelompok" class="form-control" required>
                    <option value="Nasional">Nasional</option>
                    <option value="Kewilayahan">Kewilayahan</option>
                    <option value="Kejuruan">Kejuruan</option>
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
    function showModal() {
        document.getElementById('mapelModal').style.display = 'flex';
    }
    function hideModal() {
        document.getElementById('mapelModal').style.display = 'none';
    }
</script>
<style>
    /* datatable override */
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
