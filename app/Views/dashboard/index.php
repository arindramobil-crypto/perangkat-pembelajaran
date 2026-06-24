<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$role = session()->get('role');
$now  = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$hour = (int)$now->format('H');
$greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
?>

<style>
/* ── Dashboard Styles ─────────────────────────── */
.stat-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.4rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s, border-color 0.2s;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 60%, rgba(255,255,255,0.02));
    pointer-events: none;
}
.stat-card:hover { 
    transform: translateY(-4px); 
    border-color: rgba(129, 140, 248, 0.4); 
    box-shadow: 0 10px 25px rgba(0,0,0,0.3), 0 0 15px rgba(129, 140, 248, 0.15); 
    color: inherit; 
    text-decoration: none; 
}
.stat-icon {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.stat-val {
    font-size: 2rem; font-weight: 800; line-height: 1;
    letter-spacing: -0.03em; margin-bottom: 3px;
}
.stat-label { font-size: 0.78rem; color: var(--lms-text-muted); font-weight: 500; }
.stat-trend { font-size: 0.72rem; margin-top: 2px; }

.dash-section { margin-bottom: 1.75rem; }
.dash-section-title {
    font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: var(--lms-text-muted);
    margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;
}
.dash-section-title::after {
    content: ''; flex: 1; height: 1px; background: var(--lms-border);
}

.chart-card {
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--lms-border);
    border-radius: 16px;
    padding: 1.5rem;
}
.chart-card h5 {
    font-size: 0.9rem; font-weight: 700; color: white; margin-bottom: 1.25rem;
    display: flex; align-items: center; gap: 8px;
}

.activity-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05);
}
.activity-item:last-child { border-bottom: none; }
.activity-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}

.quick-btn {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 16px; border-radius: 12px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    color: var(--lms-text-muted); font-size: 0.85rem;
    font-weight: 500; text-decoration: none;
    transition: all 0.2s; margin-bottom: 8px;
}
.quick-btn:hover { background: rgba(255,255,255,0.07); color: white; text-decoration: none; border-color: rgba(255,255,255,0.12); }
.quick-btn i { font-size: 1rem; width: 20px; text-align: center; }

.badge-tipe {
    font-size: 0.65rem; font-weight: 700; padding: 2px 8px;
    border-radius: 100px; display: inline-block;
}
.pending-badge {
    background: rgba(239,68,68,0.15); color: #FCA5A5;
    border: 1px solid rgba(239,68,68,0.3);
    border-radius: 100px; font-size: 0.7rem; font-weight: 700;
    padding: 3px 10px;
}

/* Welcome Banner */
.welcome-banner {
    background: linear-gradient(135deg, rgba(79,70,229,0.2) 0%, rgba(139,92,246,0.12) 60%, rgba(34,197,94,0.06) 100%);
    border: 1px solid rgba(129,140,248,0.25);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}
.welcome-banner::after {
    content: '';
    position: absolute;
    right: -30px; top: -30px;
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(129,140,248,0.12) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.welcome-time {
    font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.08em; color: #818CF8; margin-bottom: 4px;
    display: flex; align-items: center; gap: 6px;
}
.welcome-name {
    font-size: 1.7rem; font-weight: 800; color: white; letter-spacing: -0.02em;
    margin-bottom: 4px;
}
.welcome-name span { color: #818CF8; }
.welcome-sub { font-size: 0.875rem; color: var(--lms-text-muted); }
.role-avatar {
    width: 64px; height: 64px; border-radius: 18px;
    background: linear-gradient(135deg, rgba(79,70,229,0.35), rgba(139,92,246,0.25));
    border: 1px solid rgba(129,140,248,0.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; flex-shrink: 0;
}

/* Koreksi badge */
.koreksi-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
    color: #FCA5A5; border-radius: 100px; font-size: 0.7rem;
    font-weight: 700; padding: 3px 10px;
}
.koreksi-badge.blink { animation: blink 1.5s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.5} }
</style>

<!-- ═══════ WELCOME BANNER ═══════ -->
<div class="welcome-banner">
    <div>
        <div class="welcome-time">
            <i class="bi bi-clock"></i>
            <?= $greeting ?> · <?= $now->format('l, d F Y') ?>
        </div>
        <div class="welcome-name">
            <?= $greeting ?>, <span><?= esc(explode(' ', $nama_lengkap)[0]) ?>!</span> 👋
        </div>
        <div class="welcome-sub">
            <?php if ($role==='Admin'): ?>
                Panel Administrasi — Sistem Perangkat Pembelajaran SMK
            <?php elseif ($role==='Guru'): ?>
                Kelola materi, ujian, dan presensi kelas Anda hari ini.
                <?php if (($stats['perlu_koreksi'] ?? 0) > 0): ?>
                <span class="koreksi-badge blink ms-2">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= $stats['perlu_koreksi'] ?> jawaban perlu dikoreksi
                </span>
                <?php endif; ?>
            <?php else: ?>
                Pantau jadwal, materi, dan perkembangan akademik Anda.
                <?php if (($stats['ujian_pending'] ?? 0) > 0): ?>
                <span class="pending-badge ms-2">
                    <?= $stats['ujian_pending'] ?> ujian menunggu
                </span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="role-avatar">
        <?= $role==='Admin' ? '🛡️' : ($role==='Guru' ? '👨‍🏫' : '🎓') ?>
    </div>
</div>

<!-- ══════════════════════════════════════════════
     ADMIN DASHBOARD
══════════════════════════════════════════════ -->
<?php if ($role === 'Admin'): ?>

<div class="dash-section">
    <div class="dash-section-title"><i class="bi bi-grid-1x2"></i> Ringkasan Sistem</div>
    <div class="row g-3">
        <?php
        $adminCards = [
            ['label'=>'Total Guru',       'val'=>$stats['total_guru']         ?? 0, 'icon'=>'bi-person-badge',       'color'=>'#818CF8', 'bg'=>'rgba(79,70,229,0.12)',   'href'=>'users/guru'],
            ['label'=>'Total Siswa',      'val'=>$stats['total_siswa']        ?? 0, 'icon'=>'bi-people-fill',        'color'=>'#22C55E', 'bg'=>'rgba(34,197,94,0.12)',   'href'=>'users/siswa'],
            ['label'=>'Total Kelas',      'val'=>$stats['total_kelas']        ?? 0, 'icon'=>'bi-building',           'color'=>'#F59E0B', 'bg'=>'rgba(245,158,11,0.12)',  'href'=>'master/kelas'],
            ['label'=>'Mata Pelajaran',   'val'=>$stats['total_mapel']        ?? 0, 'icon'=>'bi-book-fill',          'color'=>'#A78BFA', 'bg'=>'rgba(139,92,246,0.12)',  'href'=>'master/mata-pelajaran'],
            ['label'=>'Upload Materi',    'val'=>$stats['total_materi']       ?? 0, 'icon'=>'bi-folder2-open',       'color'=>'#38BDF8', 'bg'=>'rgba(56,189,248,0.12)',  'href'=>'materi'],
            ['label'=>'Bank Ujian',       'val'=>$stats['total_ulangan']      ?? 0, 'icon'=>'bi-pencil-square',      'color'=>'#FB923C', 'bg'=>'rgba(251,146,60,0.12)',  'href'=>'ulangan'],
            ['label'=>'Jadwal Pelajaran', 'val'=>$stats['total_jadwal']       ?? 0, 'icon'=>'bi-calendar-week',      'color'=>'#34D399', 'bg'=>'rgba(52,211,153,0.12)',  'href'=>'jadwal'],
            ['label'=>'Ujian Selesai',    'val'=>$stats['total_ujian_selesai']?? 0, 'icon'=>'bi-patch-check-fill',   'color'=>'#22C55E', 'bg'=>'rgba(34,197,94,0.12)',   'href'=>'ulangan'],
        ];
        foreach ($adminCards as $i => $c): ?>
        <div class="col-6 col-md-4 col-xl-3">
            <a href="<?= base_url($c['href']) ?>" class="stat-card" style="animation: fadeUp 0.4s ease <?= $i*0.07 ?>s both;">
                <div class="stat-icon" style="background:<?= $c['bg'] ?>;">
                    <i class="bi <?= $c['icon'] ?>" style="color:<?= $c['color'] ?>;font-size:1.3rem;"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:<?= $c['color'] ?>;"><?= number_format($c['val']) ?></div>
                    <div class="stat-label"><?= $c['label'] ?></div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="row g-4 dash-section">
    <!-- Grafik Siswa per Kelas -->
    <div class="col-lg-8">
        <div class="chart-card">
            <h5><i class="bi bi-bar-chart-fill" style="color:#818CF8;"></i> Siswa per Kelas</h5>
            <canvas id="chartSiswaKelas" height="200"></canvas>
        </div>
    </div>
    <!-- Materi per Mapel (Doughnut) -->
    <div class="col-lg-4">
        <div class="chart-card">
            <h5><i class="bi bi-pie-chart-fill" style="color:#F59E0B;"></i> Materi per Mapel</h5>
            <canvas id="chartMateriMapel" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 dash-section">
    <!-- Aktivitas Terbaru -->
    <div class="col-lg-7">
        <div class="chart-card">
            <h5><i class="bi bi-activity" style="color:#22C55E;"></i> Aktivitas Terbaru</h5>
            <?php foreach ($aktivitas as $a): ?>
            <div class="activity-item">
                <div class="activity-dot" style="background:<?= $a['tipe']==='Materi'?'#818CF8':'#F59E0B'; ?>"></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.85rem;color:white;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?= esc($a['judul']) ?>
                    </div>
                    <div style="font-size:0.72rem;color:var(--lms-text-muted);">
                        <span class="badge-tipe" style="background:<?= $a['tipe']==='Materi'?'rgba(79,70,229,0.2)':'rgba(245,158,11,0.2)'; ?>;color:<?= $a['tipe']==='Materi'?'#818CF8':'#F59E0B'; ?>;"><?= $a['tipe'] ?></span>
                        · <?= date('d M Y', strtotime($a['created_at'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($aktivitas)): ?>
            <div style="text-align:center; padding: 40px 20px;">
                <i class="bi bi-inbox-fill" style="font-size:3rem; color:var(--lms-text-muted); opacity:0.5; margin-bottom:10px; display:block;"></i>
                <h6 style="color:white; margin-bottom:5px;">Belum Ada Aktivitas</h6>
                <p style="color:var(--lms-text-muted); font-size:0.85rem; margin-bottom:0;">Data materi atau ujian terbaru akan muncul di sini.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- User Terbaru & Aksi Cepat -->
    <div class="col-lg-5">
        <div class="chart-card mb-4">
            <h5><i class="bi bi-person-plus-fill" style="color:#38BDF8;"></i> Pengguna Terbaru</h5>
            <?php foreach ($userTerbaru as $u): ?>
            <div class="activity-item">
                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#4F46E5,#7C3AED);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;color:white;flex-shrink:0;">
                    <?= strtoupper(substr($u['nama_lengkap'],0,1)) ?>
                </div>
                <div>
                    <div style="font-size:0.83rem;color:white;font-weight:600;"><?= esc($u['nama_lengkap']) ?></div>
                    <div style="font-size:0.72rem;color:var(--lms-text-muted);"><?= $u['role'] ?> · <?= date('d M', strtotime($u['created_at'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="chart-card">
            <h5><i class="bi bi-lightning-charge-fill" style="color:#F59E0B;"></i> Aksi Cepat</h5>
            <a href="<?= base_url('users/guru') ?>" class="quick-btn"><i class="bi bi-person-badge" style="color:#818CF8;"></i> Tambah Guru</a>
            <a href="<?= base_url('users/siswa') ?>" class="quick-btn"><i class="bi bi-person-video2" style="color:#22C55E;"></i> Tambah Siswa</a>
            <a href="<?= base_url('master/kelas') ?>" class="quick-btn"><i class="bi bi-building" style="color:#F59E0B;"></i> Kelola Kelas</a>
            <a href="<?= base_url('pengaturan/sekolah') ?>" class="quick-btn"><i class="bi bi-gear-wide-connected" style="color:#38BDF8;"></i> Pengaturan Sekolah</a>
        </div>
    </div>
</div>

<?php
// Data chart
$kelasLabels = json_encode(array_column($siswaPerKelas ?? [], 'nama_kelas'));
$kelasData   = json_encode(array_column($siswaPerKelas ?? [], 'total'));
$mapelLabels = json_encode(array_column($materiPerMapel ?? [], 'nama_mapel'));
$mapelData   = json_encode(array_column($materiPerMapel ?? [], 'total'));
?>

<!-- ══════════════════════════════════════════════
     GURU DASHBOARD
══════════════════════════════════════════════ -->
<?php elseif ($role === 'Guru'): ?>

<div class="dash-section">
    <div class="dash-section-title"><i class="bi bi-grid-1x2"></i> Statistik Mengajar Anda</div>
    <div class="row g-3">
        <?php
        $guruCards = [
            ['label'=>'Jadwal Mengajar', 'val'=>$stats['total_jadwal']   ?? 0, 'icon'=>'bi-calendar-week',   'color'=>'#818CF8', 'bg'=>'rgba(79,70,229,0.12)'],
            ['label'=>'Upload Materi',   'val'=>$stats['total_materi']   ?? 0, 'icon'=>'bi-folder2-open',    'color'=>'#38BDF8', 'bg'=>'rgba(56,189,248,0.12)'],
            ['label'=>'Bank Ujian',      'val'=>$stats['total_ulangan']  ?? 0, 'icon'=>'bi-pencil-square',   'color'=>'#F59E0B', 'bg'=>'rgba(245,158,11,0.12)'],
            ['label'=>'Total Soal',      'val'=>$stats['total_soal']     ?? 0, 'icon'=>'bi-list-ol',         'color'=>'#A78BFA', 'bg'=>'rgba(139,92,246,0.12)'],
            ['label'=>'Ujian Selesai',   'val'=>$stats['total_dinilai']  ?? 0, 'icon'=>'bi-patch-check-fill','color'=>'#22C55E', 'bg'=>'rgba(34,197,94,0.12)'],
            ['label'=>'Perlu Koreksi',   'val'=>$stats['perlu_koreksi']  ?? 0, 'icon'=>'bi-pen-fill',        'color'=>'#EF4444', 'bg'=>'rgba(239,68,68,0.12)'],
        ];
        foreach ($guruCards as $i => $c): ?>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card" style="animation: fadeUp 0.4s ease <?= $i*0.08 ?>s both;">
                <div class="stat-icon" style="background:<?= $c['bg'] ?>;">
                    <i class="bi <?= $c['icon'] ?>" style="color:<?= $c['color'] ?>;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:<?= $c['color'] ?>;font-size:1.5rem;"><?= $c['val'] ?></div>
                    <div class="stat-label"><?= $c['label'] ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="row g-4 dash-section">
    <!-- Grafik Nilai per Ujian -->
    <div class="col-lg-7">
        <div class="chart-card">
            <h5><i class="bi bi-bar-chart-fill" style="color:#818CF8;"></i> Rata-rata Nilai per Ujian</h5>
            <?php if (empty($nilaiPerUlangan)): ?>
            <p style="color:var(--lms-text-muted);font-size:0.85rem;margin:0;">Belum ada data nilai ujian.</p>
            <?php else: ?>
            <canvas id="chartNilaiGuru" height="200"></canvas>
            <?php endif; ?>
        </div>
    </div>
    <!-- Distribusi Tipe Soal -->
    <div class="col-lg-5">
        <div class="chart-card">
            <h5><i class="bi bi-pie-chart-fill" style="color:#F59E0B;"></i> Distribusi Tipe Soal</h5>
            <?php if (empty($tipesSoal)): ?>
            <p style="color:var(--lms-text-muted);font-size:0.85rem;margin:0;">Belum ada soal dibuat.</p>
            <?php else: ?>
            <canvas id="chartTipesSoal" height="200"></canvas>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4 dash-section">
    <!-- Jadwal Mengajar -->
    <div class="col-lg-5">
        <div class="chart-card">
            <h5><i class="bi bi-calendar-week" style="color:#818CF8;"></i> Jadwal Mengajar</h5>
            <?php if (empty($jadwalList)): ?>
            <p style="color:var(--lms-text-muted);font-size:0.85rem;">Belum ada jadwal mengajar.</p>
            <?php else: ?>
            <?php foreach ($jadwalList as $j): ?>
            <div class="activity-item">
                <div style="width:48px;text-align:center;flex-shrink:0;">
                    <div style="font-size:0.72rem;font-weight:700;color:#818CF8;"><?= esc($j['hari']) ?></div>
                    <div style="font-size:0.7rem;color:var(--lms-text-muted);"><?= substr($j['jam_mulai'],0,5) ?></div>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.85rem;color:white;font-weight:600;"><?= esc($j['nama_mapel']) ?></div>
                    <div style="font-size:0.75rem;color:var(--lms-text-muted);">Kelas <?= esc($j['nama_kelas']) ?></div>
                </div>
                <a href="<?= base_url('presensi/input/'.$j['id']) ?>"
                   class="btn btn-sm btn-outline-primary" style="font-size:0.72rem;padding:4px 10px;flex-shrink:0;">
                    <i class="bi bi-check2-square me-1"></i>Absen
                </a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Ujian Aktif -->
    <div class="col-lg-7">
        <div class="chart-card">
            <h5><i class="bi bi-pencil-square" style="color:#F59E0B;"></i> Status Ujian</h5>
            <?php if (empty($ujianAktif)): ?>
            <p style="color:var(--lms-text-muted);font-size:0.85rem;">Belum ada ujian dibuat.</p>
            <?php else: ?>
            <div style="overflow-x:auto;">
            <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
                <thead>
                    <tr style="color:var(--lms-text-muted);">
                        <th style="text-align:left;padding:6px 8px;font-weight:600;">Judul Ujian</th>
                        <th style="text-align:center;padding:6px 8px;font-weight:600;">Kelas</th>
                        <th style="text-align:center;padding:6px 8px;font-weight:600;">Selesai</th>
                        <th style="text-align:center;padding:6px 8px;font-weight:600;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ujianAktif as $u): ?>
                <tr style="border-top:1px solid rgba(255,255,255,0.05);">
                    <td style="padding:8px;color:white;font-weight:500;"><?= esc(mb_strimwidth($u['judul'],0,30,'…')) ?></td>
                    <td style="padding:8px;text-align:center;color:var(--lms-text-muted);"><?= $u['total_kelas'] ?></td>
                    <td style="padding:8px;text-align:center;">
                        <span style="color:#22C55E;font-weight:700;"><?= $u['sudah_kerjakan'] ?></span>
                    </td>
                    <td style="padding:8px;text-align:center;">
                        <a href="<?= base_url('ulangan') ?>" class="btn btn-sm" style="font-size:0.7rem;padding:3px 10px;background:rgba(255,255,255,0.06);color:white;border:1px solid rgba(255,255,255,0.1);">
                            Kelola
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$nilaiLabels = json_encode(array_map(fn($n) => mb_strimwidth($n['judul'],0,20,'…'), $nilaiPerUlangan ?? []));
$nilaiData   = json_encode(array_column($nilaiPerUlangan ?? [], 'rata'));
$nilaiKkm    = json_encode(array_column($nilaiPerUlangan ?? [], 'kkm'));
$tipeLabels  = json_encode(array_column($tipesSoal ?? [], 'tipe_soal'));
$tipeData    = json_encode(array_column($tipesSoal ?? [], 'total'));
?>

<!-- ══════════════════════════════════════════════
     SISWA DASHBOARD
══════════════════════════════════════════════ -->
<?php elseif ($role === 'Siswa'): ?>

<?php if (empty($stats['kelas_info'])): ?>
<div class="chart-card text-center py-5">
    <div style="font-size:3rem;margin-bottom:1rem;">🏫</div>
    <h4 style="color:white;">Anda Belum Terdaftar di Kelas</h4>
    <p style="color:var(--lms-text-muted);">Hubungi Admin untuk mendaftarkan Anda ke kelas yang sesuai.</p>
</div>
<?php else: ?>

<!-- Info Kelas -->
<div style="display:flex;align-items:center;gap:12px;background:rgba(79,70,229,0.1);border:1px solid rgba(79,70,229,0.2);border-radius:14px;padding:1rem 1.5rem;margin-bottom:1.75rem;">
    <div style="width:46px;height:46px;border-radius:12px;background:rgba(79,70,229,0.25);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">🏫</div>
    <div>
        <div style="font-size:0.72rem;color:var(--lms-text-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Kelas Anda</div>
        <div style="font-size:1.05rem;color:white;font-weight:700;"><?= esc($stats['kelas_info']['nama_kelas']) ?> &mdash; <?= esc($stats['kelas_info']['jurusan'] ?? '') ?></div>
    </div>
    <?php if (($stats['ujian_pending'] ?? 0) > 0): ?>
    <a href="<?= base_url('ulangan') ?>" class="pending-badge ms-auto" style="text-decoration:none;">
        <i class="bi bi-exclamation-circle me-1"></i><?= $stats['ujian_pending'] ?> ujian menunggu
    </a>
    <?php endif; ?>
</div>

<div class="dash-section">
    <div class="dash-section-title"><i class="bi bi-grid-1x2"></i> Ringkasan Akademik</div>
    <div class="row g-3">
        <?php
        $pct   = $stats['pct_hadir'] ?? 0;
        $pctColor = $pct >= 75 ? '#22C55E' : ($pct >= 50 ? '#F59E0B' : '#EF4444');
        $siswaCards = [
            ['label'=>'Jadwal Pelajaran',  'val'=>$stats['total_jadwal']  ?? 0, 'icon'=>'bi-calendar-week',   'color'=>'#818CF8', 'bg'=>'rgba(79,70,229,0.12)'],
            ['label'=>'Materi Tersedia',   'val'=>$stats['total_materi']  ?? 0, 'icon'=>'bi-folder2-open',    'color'=>'#38BDF8', 'bg'=>'rgba(56,189,248,0.12)'],
            ['label'=>'Total Hadir',       'val'=>$stats['total_hadir']   ?? 0, 'icon'=>'bi-check-circle-fill','color'=>'#22C55E', 'bg'=>'rgba(34,197,94,0.12)'],
            ['label'=>'Tidak Hadir',       'val'=>$stats['total_absen']   ?? 0, 'icon'=>'bi-x-circle-fill',   'color'=>'#EF4444', 'bg'=>'rgba(239,68,68,0.12)'],
            ['label'=>'Ujian Selesai',     'val'=>$stats['total_ujian']   ?? 0, 'icon'=>'bi-patch-check-fill','color'=>'#F59E0B', 'bg'=>'rgba(245,158,11,0.12)'],
            ['label'=>'Ujian Menunggu',    'val'=>$stats['ujian_pending'] ?? 0, 'icon'=>'bi-hourglass-split', 'color'=>'#FB923C', 'bg'=>'rgba(251,146,60,0.12)'],
        ];
        foreach ($siswaCards as $i => $c): ?>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card" style="animation: fadeUp 0.4s ease <?= $i*0.08 ?>s both;">
                <div class="stat-icon" style="background:<?= $c['bg'] ?>;">
                    <i class="bi <?= $c['icon'] ?>" style="color:<?= $c['color'] ?>;font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="stat-val" style="color:<?= $c['color'] ?>;font-size:1.5rem;"><?= $c['val'] ?></div>
                    <div class="stat-label"><?= $c['label'] ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="row g-4 dash-section">
    <!-- Grafik Nilai -->
    <div class="col-lg-7">
        <div class="chart-card">
            <h5><i class="bi bi-bar-chart-fill" style="color:#818CF8;"></i> Nilai Ujian Saya</h5>
            <?php if (empty($nilaiSiswa)): ?>
            <p style="color:var(--lms-text-muted);font-size:0.85rem;">Belum ada ujian yang diselesaikan.</p>
            <?php else: ?>
            <canvas id="chartNilaiSiswa" height="200"></canvas>
            <?php endif; ?>
        </div>
    </div>
    <!-- Kehadiran Doughnut -->
    <div class="col-lg-5">
        <div class="chart-card">
            <h5><i class="bi bi-pie-chart-fill" style="color:#22C55E;"></i> Rekap Kehadiran</h5>
            <div style="display:flex;align-items:center;justify-content:center;gap:1.5rem;flex-wrap:wrap;">
                <div style="position:relative;width:160px;height:160px;">
                    <canvas id="chartKehadiran"></canvas>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <div style="font-size:1.6rem;font-weight:800;color:<?= $pctColor ?>;"><?= $pct ?>%</div>
                        <div style="font-size:0.7rem;color:var(--lms-text-muted);">Kehadiran</div>
                    </div>
                </div>
                <div>
                    <?php
                    $khdItems = [
                        ['Hadir',  $stats['total_hadir'] ?? 0, '#22C55E'],
                        ['Sakit',  $stats['total_sakit'] ?? 0, '#38BDF8'],
                        ['Izin',   $stats['total_izin']  ?? 0, '#F59E0B'],
                        ['Alfa',   $stats['total_alfa']  ?? 0, '#EF4444'],
                    ];
                    foreach ($khdItems as $ki): ?>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <div style="width:10px;height:10px;border-radius:3px;background:<?= $ki[2] ?>;flex-shrink:0;"></div>
                        <span style="font-size:0.8rem;color:var(--lms-text-muted);"><?= $ki[0] ?></span>
                        <span style="font-size:0.85rem;font-weight:700;color:white;margin-left:auto;"><?= $ki[1] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 dash-section">
    <!-- Ujian yang Belum Dikerjakan -->
    <div class="col-lg-6">
        <div class="chart-card">
            <h5><i class="bi bi-hourglass-split" style="color:#FB923C;"></i> Ujian Menunggu</h5>
            <?php if (empty($ujianBelum)): ?>
            <div style="text-align:center;padding:1.5rem 0;">
                <i class="bi bi-check2-all" style="font-size:2rem;color:#22C55E;display:block;margin-bottom:8px;"></i>
                <p style="color:var(--lms-text-muted);font-size:0.85rem;margin:0;">Semua ujian sudah dikerjakan!</p>
            </div>
            <?php else: ?>
            <?php foreach ($ujianBelum as $u): ?>
            <div class="activity-item">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(251,146,60,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-pencil-square" style="color:#FB923C;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.85rem;color:white;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= esc($u['judul']) ?></div>
                    <div style="font-size:0.72rem;color:var(--lms-text-muted);"><?= $u['tipe'] ?> · <?= $u['durasi'] ?> menit</div>
                </div>
                <a href="<?= base_url('ulangan') ?>" class="btn btn-sm btn-primary" style="font-size:0.72rem;padding:4px 12px;flex-shrink:0;">Kerjakan</a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- Materi Terbaru + Aksi Cepat -->
    <div class="col-lg-6">
        <div class="chart-card mb-3">
            <h5><i class="bi bi-folder2-open" style="color:#38BDF8;"></i> Materi Terbaru</h5>
            <?php foreach ($materiTerbaru as $m): ?>
            <div class="activity-item">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(56,189,248,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-file-earmark-text" style="color:#38BDF8;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.85rem;color:white;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= esc($m['judul_materi']) ?></div>
                    <div style="font-size:0.72rem;color:var(--lms-text-muted);"><?= esc($m['nama_mapel']) ?> · <?= date('d M', strtotime($m['created_at'])) ?></div>
                </div>
                <a href="<?= base_url('materi-siswa') ?>" class="btn btn-sm" style="font-size:0.7rem;padding:3px 10px;background:rgba(255,255,255,0.06);color:white;border:1px solid rgba(255,255,255,0.1);flex-shrink:0;">Lihat</a>
            </div>
            <?php endforeach; ?>
            <?php if (empty($materiTerbaru)): ?>
            <p style="color:var(--lms-text-muted);font-size:0.85rem;">Belum ada materi.</p>
            <?php endif; ?>
        </div>
        <div class="chart-card">
            <h5><i class="bi bi-lightning-charge-fill" style="color:#F59E0B;"></i> Aksi Cepat</h5>
            <a href="<?= base_url('jadwal') ?>" class="quick-btn"><i class="bi bi-calendar-week" style="color:#818CF8;"></i> Lihat Jadwal Pelajaran</a>
            <a href="<?= base_url('materi-siswa') ?>" class="quick-btn"><i class="bi bi-download" style="color:#38BDF8;"></i> Unduh Materi</a>
            <a href="<?= base_url('ulangan') ?>" class="quick-btn"><i class="bi bi-pencil-square" style="color:#F59E0B;"></i> Ujian Online</a>
            <a href="<?= base_url('buku-nilai') ?>" class="quick-btn"><i class="bi bi-bar-chart-steps" style="color:#22C55E;"></i> Buku Nilai Saya</a>
        </div>
    </div>
</div>

<?php endif; // kelas_info ?>
<?php endif; // role Siswa ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#94A3B8';
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 11;

const gridColor  = 'rgba(255,255,255,0.06)';
const palette    = ['#818CF8','#22C55E','#F59E0B','#38BDF8','#A78BFA','#FB923C','#34D399','#F472B6'];

<?php if ($role === 'Admin'): ?>
/* ── Siswa per Kelas ─────────────── */
new Chart(document.getElementById('chartSiswaKelas'), {
    type: 'bar',
    data: {
        labels: <?= $kelasLabels ?? '[]' ?>,
        datasets: [{
            label: 'Jumlah Siswa',
            data: <?= $kelasData ?? '[]' ?>,
            backgroundColor: 'rgba(129,140,248,0.7)',
            borderColor: '#818CF8',
            borderWidth: 1.5,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: gridColor }, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});

/* ── Materi per Mapel ─────────────── */
new Chart(document.getElementById('chartMateriMapel'), {
    type: 'doughnut',
    data: {
        labels: <?= $mapelLabels ?? '[]' ?>,
        datasets: [{ data: <?= $mapelData ?? '[]' ?>, backgroundColor: palette, borderWidth: 2, borderColor: '#0A0F1E' }]
    },
    options: {
        responsive: true, maintainAspectRatio: true, cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { padding: 12, boxWidth: 12, boxHeight: 12 } } }
    }
});

<?php elseif ($role === 'Guru'): ?>
/* ── Nilai per Ujian ─────────────── */
<?php if (!empty($nilaiPerUlangan)): ?>
new Chart(document.getElementById('chartNilaiGuru'), {
    type: 'bar',
    data: {
        labels: <?= $nilaiLabels ?>,
        datasets: [
            { label: 'Rata-rata Nilai', data: <?= $nilaiData ?>, backgroundColor: 'rgba(129,140,248,0.75)', borderColor: '#818CF8', borderWidth: 1.5, borderRadius: 6 },
            { label: 'KKM',            data: <?= $nilaiKkm ?>,  type: 'line', borderColor: '#EF4444', borderDash: [5,4], borderWidth: 2, pointRadius: 0, fill: false }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { labels: { boxWidth: 14 } } },
        scales: {
            y: { min: 0, max: 100, grid: { color: gridColor } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

/* ── Tipe Soal Doughnut ──────────── */
<?php if (!empty($tipesSoal)): ?>
new Chart(document.getElementById('chartTipesSoal'), {
    type: 'doughnut',
    data: {
        labels: <?= $tipeLabels ?>,
        datasets: [{ data: <?= $tipeData ?>, backgroundColor: palette, borderWidth: 2, borderColor: '#0A0F1E' }]
    },
    options: {
        responsive: true, maintainAspectRatio: true, cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { padding: 10, boxWidth: 12, boxHeight: 12 } } }
    }
});
<?php endif; ?>

<?php elseif ($role === 'Siswa' && !empty($stats['kelas_info'])): ?>
/* ── Nilai Siswa ─────────────────── */
<?php if (!empty($nilaiSiswa)):
    $siswaLabels = json_encode(array_map(fn($n) => mb_strimwidth($n['judul'],0,20,'…'), $nilaiSiswa));
    $siswaNilai  = json_encode(array_column($nilaiSiswa,'nilai_akhir'));
    $siswaKkm    = json_encode(array_column($nilaiSiswa,'kkm'));
?>
new Chart(document.getElementById('chartNilaiSiswa'), {
    type: 'bar',
    data: {
        labels: <?= $siswaLabels ?>,
        datasets: [
            { label: 'Nilai Saya', data: <?= $siswaNilai ?>, backgroundColor: 'rgba(129,140,248,0.75)', borderColor: '#818CF8', borderWidth: 1.5, borderRadius: 6 },
            { label: 'KKM',       data: <?= $siswaKkm ?>,   type: 'line', borderColor: '#EF4444', borderDash: [5,4], borderWidth: 2, pointRadius: 0, fill: false }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { labels: { boxWidth: 14 } } },
        scales: {
            y: { min: 0, max: 100, grid: { color: gridColor } },
            x: { grid: { display: false } }
        }
    }
});
<?php endif; ?>

/* ── Kehadiran Doughnut ──────────── */
new Chart(document.getElementById('chartKehadiran'), {
    type: 'doughnut',
    data: {
        labels: ['Hadir','Sakit','Izin','Alfa'],
        datasets: [{
            data: [<?= $stats['total_hadir']??0 ?>,<?= $stats['total_sakit']??0 ?>,<?= $stats['total_izin']??0 ?>,<?= $stats['total_alfa']??0 ?>],
            backgroundColor: ['#22C55E','#38BDF8','#F59E0B','#EF4444'],
            borderWidth: 2, borderColor: '#0A0F1E'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true, cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
<?php endif; ?>

/* ── Animasi fadeUp ──────────────── */
const style = document.createElement('style');
style.textContent = `
@keyframes fadeUp {
  from { opacity:0; transform:translateY(16px); }
  to   { opacity:1; transform:translateY(0); }
}`;
document.head.appendChild(style);
</script>
<?= $this->endSection() ?>
