<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h3 class="card-title" style="margin-bottom: 0.5rem;">Input Presensi: <?= esc($jadwal['nama_kelas']) ?></h3>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Mapel: <?= esc($jadwal['nama_mapel']) ?> | Hari: <?= esc($jadwal['hari']) ?></p>
        </div>
        <a href="<?= base_url('presensi') ?>" class="btn" style="background: rgba(255,255,255,0.1); color: white;">&larr; Kembali</a>
    </div>

    <form action="<?= base_url('presensi/save') ?>" method="post">
        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 1rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" class="form-control" value="<?= $tanggal_hari_ini ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Pertemuan Ke-</label>
                <input type="number" name="pertemuan_ke" class="form-control" placeholder="Contoh: 1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Materi / Topik Disampaikan</label>
                <input type="text" name="materi_disampaikan" class="form-control" placeholder="Topik bahasan hari ini..." required>
            </div>
        </div>

        <h4 style="color: white; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">Daftar Siswa</h4>
        
        <div style="overflow-x: auto; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <th style="padding: 1rem;">No</th>
                        <th style="padding: 1rem;">NIS</th>
                        <th style="padding: 1rem;">Nama Lengkap</th>
                        <th style="padding: 1rem;">Kehadiran</th>
                        <th style="padding: 1rem;">Catatan Khusus (Opsional)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($siswaList)): ?>
                    <tr>
                        <td colspan="5" style="padding: 1rem; text-align: center; color: var(--text-muted);">Belum ada siswa yang dimasukkan ke kelas ini.</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php $no=1; foreach($siswaList as $s): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 1rem;"><?= $no++ ?></td>
                        <td style="padding: 1rem;"><?= esc($s['nis']) ?></td>
                        <td style="padding: 1rem;"><?= esc($s['nama_lengkap']) ?></td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 1rem;">
                                <label style="display: flex; align-items: center; gap: 0.3rem; cursor: pointer;">
                                    <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="Hadir" checked> Hadir
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.3rem; cursor: pointer;">
                                    <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="Sakit"> Sakit
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.3rem; cursor: pointer;">
                                    <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="Izin"> Izin
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.3rem; cursor: pointer;">
                                    <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="Alfa"> Alfa
                                </label>
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            <input type="text" name="catatan[<?= $s['siswa_id'] ?>]" class="form-control" placeholder="Ket..." style="padding: 0.3rem 0.6rem;">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(!empty($siswaList)): ?>
        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; font-size: 1.1rem;">Simpan Presensi</button>
        </div>
        <?php endif; ?>
    </form>
</div>

<style>
    input[type="radio"] { accent-color: #818CF8; width: 1.2rem; height: 1.2rem; }
</style>
<?= $this->endSection() ?>
