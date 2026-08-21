<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0.5rem;">Jadwal Mengajar Anda</h3>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Berikut adalah daftar kelas yang ditugaskan kepada Anda. Untuk manajemen jadwal, silakan hubungi Kurikulum/Admin.</p>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>Hari & Waktu</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>T.A</th>
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
                    <td><?= esc($j['tahun']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
