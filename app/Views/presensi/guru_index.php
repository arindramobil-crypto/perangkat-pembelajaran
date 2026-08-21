<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h3 class="card-title" style="margin-bottom: 0.5rem;">Presensi Kelas</h3>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Pilih jadwal untuk memulai absensi hari ini.</p>
        </div>
        <button class="btn btn-primary" onclick="showModal()">+ Mulai Presensi Baru</button>
    </div>

    <h4 style="color: white; margin-bottom: 1rem; margin-top: 2rem;">Riwayat Presensi Anda</h4>
    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kelas & Mapel</th>
                    <th>Pertemuan Ke-</th>
                    <th>Materi Disampaikan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($riwayat as $r): ?>
                <tr>
                    <td><strong style="color: #818CF8;"><?= date('d M Y', strtotime($r['tanggal'])) ?></strong></td>
                    <td>
                        <?= esc($r['nama_kelas']) ?><br>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($r['nama_mapel']) ?> (<?= esc($r['hari']) ?>)</span>
                    </td>
                    <td><?= esc($r['pertemuan_ke']) ?></td>
                    <td><?= esc($r['materi_disampaikan']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="pilihJadwalModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="glass-panel" style="background: var(--bg-dark); width: 500px; padding: 2rem;">
        <h3 style="color: white; margin-bottom: 1.5rem;">Pilih Jadwal Mengajar</h3>
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Pilih kelas mana yang akan Anda absen sekarang.</p>
        
        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
            <?php foreach($jadwalList as $j): ?>
            <a href="<?= base_url('presensi/input/'.$j['id']) ?>" class="btn" style="background: rgba(255,255,255,0.05); color: white; text-align: left; padding: 1rem; border: 1px solid var(--border-color); display: block;">
                <strong style="color: #818CF8; font-size: 1.1rem;"><?= esc($j['nama_kelas']) ?></strong> - <?= esc($j['nama_mapel']) ?><br>
                <span style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($j['hari']) ?>, <?= esc($j['jam_mulai']) ?> - <?= esc($j['jam_selesai']) ?></span>
            </a>
            <?php endforeach; ?>
            
            <?php if(empty($jadwalList)): ?>
                <p style="color: var(--danger);">Anda belum memiliki jadwal mengajar.</p>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 2rem; text-align: right;">
            <button class="btn" style="background: transparent; border: 1px solid var(--border-color); color: white;" onclick="hideModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    function showModal() { document.getElementById('pilihJadwalModal').style.display = 'flex'; }
    function hideModal() { document.getElementById('pilihJadwalModal').style.display = 'none'; }
</script>
<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
