<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h3 class="card-title" style="margin-bottom: 0.5rem;">Bank Ujian / Kuis</h3>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Kelola ulangan harian, UTS, UAS, dan kuis Anda.</p>
        </div>
        <button class="btn btn-primary" onclick="showModal()">+ Buat Ujian Baru</button>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>Judul Ujian</th>
                    <th>Mata Pelajaran</th>
                    <th>Tipe / KKM</th>
                    <th>Waktu & Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($ulanganList as $u): ?>
                <tr>
                    <td>
                        <strong style="color: #818CF8;"><?= esc($u['judul']) ?></strong><br>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= esc(substr($u['deskripsi'], 0, 50)) ?>...</span>
                    </td>
                    <td><?= esc($u['nama_mapel']) ?></td>
                    <td><?= esc($u['tipe']) ?> <br> <small>KKM: <?= esc($u['kkm']) ?></small></td>
                    <td><?= esc($u['durasi']) ?> Menit</td>
                    <td>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= base_url('ulangan/soal/'.$u['id']) ?>"
                               class="btn btn-sm btn-outline-secondary" title="Kelola Soal & Kelas">
                                <i class="bi bi-list-check me-1"></i>Soal
                            </a>
                            <a href="<?= base_url('ulangan/rekap/'.$u['id']) ?>"
                               class="btn btn-sm" style="background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);color:#22C55E;"
                               title="Rekap Nilai Siswa">
                                <i class="bi bi-bar-chart-line me-1"></i>Rekap Nilai
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="buatModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; overflow-y: auto;">
    <div class="glass-panel" style="background: var(--bg-dark); width: 600px; padding: 2rem; margin: auto;">
        <h3 style="color: white; margin-bottom: 1.5rem;">Buat Ujian Baru</h3>
        
        <form action="<?= base_url('ulangan/save') ?>" method="post">
            <input type="hidden" name="guru_id" value="<?= $guru_id ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Mata Pelajaran</label>
                    <select name="mapel_id" class="form-control" required>
                        <?php foreach($mapelList as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= esc($m['kode_mapel']) ?> - <?= esc($m['nama_mapel']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Judul Ujian (Misal: UH 1 Bab 1)</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Deskripsi / Petunjuk Mengerjakan</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Ujian</label>
                    <select name="tipe" class="form-control" required>
                        <option value="UH">Ulangan Harian (UH)</option>
                        <option value="UTS">UTS</option>
                        <option value="UAS">UAS</option>
                        <option value="Kuis">Kuis</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Durasi (Menit)</label>
                    <input type="number" name="durasi" class="form-control" required value="60">
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai KKM</label>
                    <input type="number" name="kkm" class="form-control" required value="75">
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn" style="background: transparent; border: 1px solid var(--border-color); color: white;" onclick="hideModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showModal() { document.getElementById('buatModal').style.display = 'flex'; }
    function hideModal() { document.getElementById('buatModal').style.display = 'none'; }
</script>
<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
