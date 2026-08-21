<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0;">Atur Jadwal Pelajaran</h3>
        <button class="btn btn-primary" onclick="showModal()">+ Buat Jadwal</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: var(--lms-text); text-align: left;">
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
                        <strong style="color: var(--lms-primary);"><?= esc($j['hari']) ?></strong><br>
                        <span style="font-size: 0.8rem; color: var(--lms-text-muted);"><?= esc($j['jam_mulai']) ?> - <?= esc($j['jam_selesai']) ?></span>
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
<div id="jadwalModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); z-index: 1050; justify-content: center; align-items: center; padding: 1rem; overflow-y: auto;">
    <div style="background: var(--lms-bg-panel); width: 100%; max-width: 600px; border-radius: 16px; border: 1px solid var(--lms-border); box-shadow: var(--lms-shadow); display: flex; flex-direction: column; overflow: hidden; animation: modalFadeIn 0.3s ease forwards; margin: auto;">
        
        <!-- Modal Header -->
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--lms-border); display: flex; justify-content: space-between; align-items: flex-start; background: var(--lms-hover-bg);">
            <div>
                <h3 style="color: var(--lms-text); margin: 0; font-size: 1.25rem; font-weight: 600;">Tambah Jadwal</h3>
                <p style="color: var(--lms-text-muted); margin: 0.5rem 0 0 0; font-size: 0.875rem;">Buat jadwal pelajaran baru untuk kelas.</p>
            </div>
            <button type="button" onclick="hideModal()" style="background: transparent; border: none; color: var(--lms-text-muted); cursor: pointer; padding: 0.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.color='var(--lms-text)'; this.style.background='var(--lms-hover-bg)';" onmouseout="this.style.color='var(--lms-text-muted)'; this.style.background='transparent';">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <form action="<?= base_url('jadwal/save') ?>" method="post" style="display: flex; flex-direction: column;">
            <div style="padding: 1.5rem; overflow-y: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" style="font-size: 0.875rem; margin-bottom: 0.5rem; display: block; color: var(--lms-text-muted);">Tahun Pelajaran (Aktif)</label>
                    <select name="tahun_pelajaran_id" class="form-control" style="padding: 0.75rem;" required>
                        <?php foreach($tahunList as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['tahun']) ?> - <?= esc($t['semester']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.875rem; margin-bottom: 0.5rem; display: block; color: var(--lms-text-muted);">Kelas</label>
                    <select name="kelas_id" class="form-control" style="padding: 0.75rem;" required>
                        <?php foreach($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.875rem; margin-bottom: 0.5rem; display: block; color: var(--lms-text-muted);">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control" style="padding: 0.75rem;" required>
                        <?php foreach($mapelList as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= esc($m['nama_mapel']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" style="font-size: 0.875rem; margin-bottom: 0.5rem; display: block; color: var(--lms-text-muted);">Guru Pengajar</label>
                    <select name="guru_id" class="form-control" style="padding: 0.75rem;" required>
                        <?php foreach($guruList as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.875rem; margin-bottom: 0.5rem; display: block; color: var(--lms-text-muted);">Hari</label>
                    <select name="hari" class="form-control" style="padding: 0.75rem;" required>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.875rem; margin-bottom: 0.5rem; display: block; color: var(--lms-text-muted);">Jam (Mulai - Selesai)</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="time" name="jam_mulai" class="form-control" style="padding: 0.75rem;" required>
                        <span style="color: var(--lms-text-muted); align-self: center;">-</span>
                        <input type="time" name="jam_selesai" class="form-control" style="padding: 0.75rem;" required>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--lms-border); display: flex; gap: 1rem; justify-content: flex-end; background: var(--lms-hover-bg);">
                <button type="button" class="btn" style="background: var(--lms-bg); color: var(--lms-text); border: 1px solid var(--lms-border);" onclick="hideModal()">Batal</button>
                <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem; padding-left: 1.25rem; padding-right: 1.25rem;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showModal() { document.getElementById('jadwalModal').style.display = 'flex'; }
    function hideModal() { document.getElementById('jadwalModal').style.display = 'none'; }
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
</style>
<?= $this->endSection() ?>
