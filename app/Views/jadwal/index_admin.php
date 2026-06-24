<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0;">Atur Jadwal Pelajaran</h3>
        <button class="btn btn-primary" onclick="showModal()">+ Buat Jadwal</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>Hari & Waktu</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                    <th>T.A</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($jadwals as $j): ?>
                <tr>
                    <td>
                        <strong style="color: #818CF8;"><?= esc($j['hari']) ?></strong><br>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($j['jam_mulai']) ?> - <?= esc($j['jam_selesai']) ?></span>
                    </td>
                    <td><?= esc($j['nama_kelas']) ?></td>
                    <td><?= esc($j['nama_mapel']) ?></td>
                    <td><?= esc($j['nama_guru']) ?></td>
                    <td><?= esc($j['tahun']) ?></td>
                    <td>
                        <a href="<?= base_url('jadwal/delete/'.$j['id']) ?>" class="btn" style="background: var(--danger); color: white; padding: 0.4rem 0.8rem; font-size: 0.875rem;" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Jadwal -->
<div id="jadwalModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="glass-panel" style="background: var(--bg-dark); width: 600px; padding: 2rem; margin: auto;">
        <h3 style="color: white; margin-bottom: 1.5rem;">Tambah Jadwal</h3>
        
        <form action="<?= base_url('jadwal/save') ?>" method="post">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Tahun Pelajaran (Aktif)</label>
                    <select name="tahun_pelajaran_id" class="form-control" required>
                        <?php foreach($tahunList as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['tahun']) ?> - <?= esc($t['semester']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-control" required>
                        <?php foreach($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control" required>
                        <?php foreach($mapelList as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= esc($m['nama_mapel']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Guru Pengajar</label>
                    <select name="guru_id" class="form-control" required>
                        <?php foreach($guruList as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-control" required>
                        <option value="Senin">Senin</option><option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option><option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option><option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam (Mulai - Selesai)</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="time" name="jam_mulai" class="form-control" required>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn" style="background: transparent; color: white; border: 1px solid var(--border-color);" onclick="hideModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showModal() { document.getElementById('jadwalModal').style.display = 'flex'; }
    function hideModal() { document.getElementById('jadwalModal').style.display = 'none'; }
</script>
<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
