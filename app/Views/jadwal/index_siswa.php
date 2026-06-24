<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0.5rem;">Jadwal Pelajaran Anda</h3>
        <?php if($kelas_info): ?>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Anda saat ini terdaftar di kelas: <strong style="color: white;"><?= esc($kelas_info['nama_kelas']) ?></strong>.</p>
        <?php else: ?>
        <p style="color: #EF4444; font-size: 0.875rem;">Anda belum dimasukkan ke dalam kelas apa pun. Silakan hubungi Admin.</p>
        <?php endif; ?>
    </div>

    <?php if($kelas_info): ?>
    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; color: white; text-align: left;">
            <thead>
                <tr>
                    <th>Hari & Waktu</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengajar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($jadwals as $j): ?>
                <tr>
                    <td>
                        <strong style="color: #818CF8;"><?= esc($j['hari']) ?></strong><br>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= esc($j['jam_mulai']) ?> - <?= esc($j['jam_selesai']) ?></span>
                    </td>
                    <td><?= esc($j['nama_mapel']) ?></td>
                    <td><?= esc($j['nama_guru']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<style>
    .dataTables_wrapper, .dataTables_info, .dataTables_length, .dataTables_filter { color: var(--text-muted) !important; }
    table.dataTable tbody tr { background-color: transparent !important; }
    table.dataTable tbody tr:hover { background-color: rgba(255,255,255,0.05) !important; }
    table.dataTable.no-footer { border-bottom: 1px solid var(--border-color) !important; }
</style>
<?= $this->endSection() ?>
