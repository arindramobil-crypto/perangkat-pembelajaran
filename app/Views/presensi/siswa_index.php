<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0.5rem;">Riwayat Kehadiran Anda</h3>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Berikut adalah catatan absensi yang telah diinput oleh guru-guru Anda.</p>
    </div>

    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Mata Pelajaran</th>
                    <th>Pertemuan Ke-</th>
                    <th>Guru</th>
                    <th>Status Kehadiran</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($kehadiran as $k): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($k['tanggal'])) ?></td>
                    <td><strong style="color: #818CF8;"><?= esc($k['nama_mapel']) ?></strong></td>
                    <td><?= esc($k['pertemuan_ke']) ?></td>
                    <td><?= esc($k['nama_guru']) ?></td>
                    <td>
                        <?php 
                        $color = '#22C55E'; // Green for Hadir
                        if($k['status'] == 'Sakit' || $k['status'] == 'Izin') $color = '#EAB308'; // Yellow
                        if($k['status'] == 'Alfa') $color = '#EF4444'; // Red
                        ?>
                        <span style="background: <?= $color ?>; color: white; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                            <?= esc($k['status']) ?>
                        </span>
                    </td>
                    <td><span style="font-size: 0.85rem; color: var(--text-muted);"><?= esc($k['catatan']) ?></span></td>
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
