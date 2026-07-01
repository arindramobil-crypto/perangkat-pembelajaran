<?php
// ── Ambil pengaturan sekolah (cache di PHP variable) ───────────
$_sekolah = [];
try {
    $_sekolah = (new \App\Models\PengaturanSekolahModel())->getPengaturan();
} catch (\Throwable $e) {
    $_sekolah = ['nama_sekolah' => 'LMS SMK', 'singkatan' => 'SMK', 'logo' => null];
}
$_namaSekolah = $_sekolah['nama_sekolah'] ?? 'LMS SMK';
$_singkatan   = $_sekolah['singkatan']   ?? 'SMK';
$_logoFile    = $_sekolah['logo']        ?? null;
$_logoUrl     = $_logoFile ? base_url('uploads/logo/' . $_logoFile) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <script>
        // Set theme as early as possible to prevent FOUC
        const savedTheme = localStorage.getItem('lms_theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> — <?= esc($_namaSekolah) ?></title>
    <meta name="description" content="Sistem Perangkat Pembelajaran <?= esc($_namaSekolah) ?>">
    
    <!-- Favicon -->
    <?php if ($_logoUrl): ?>
    <link rel="icon" href="<?= $_logoUrl ?>">
    <link rel="apple-touch-icon" href="<?= $_logoUrl ?>">
    <?php else: ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">
    <?php endif; ?>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Custom Theme (versi untuk cache busting) -->
    <?php $appVersion = new \Config\AppVersion(); $cssVer = urlencode($appVersion->lastUpdated); ?>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>?v=<?= $cssVer ?>">

    <!-- CSRF Token (untuk AJAX/Fetch request) -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name"  content="<?= csrf_token() ?>">
    <!-- Keamanan tambahan -->
    <meta name="robots" content="noindex, nofollow">
</head>
<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar navbar-dark lms-navbar px-3 py-2" id="topNavbar">

    <!-- Sidebar Toggle -->
    <button class="btn btn-sm lms-toggle-btn me-3" id="sidebarToggle" title="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Brand Dinamis -->
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= base_url('dashboard') ?>">
        <?php if ($_logoUrl): ?>
        <img src="<?= $_logoUrl ?>" alt="Logo" style="width:32px;height:32px;object-fit:contain;border-radius:6px;">
        <?php else: ?>
        <span style="font-size:1.3rem;">📚</span>
        <?php endif; ?>
        <span class="lms-brand-text d-none d-md-inline">
            <?= esc($_singkatan) ?>
        </span>
    </a>

    <!-- Right: Badge + User Dropdown -->
    <div class="ms-auto d-flex align-items-center gap-3">

        <!-- Notifikasi Bell -->
        <!-- Theme Toggle -->
        <button class="btn lms-notif-btn me-2" id="themeToggleBtn" title="Ganti Tema">
            <i class="bi bi-moon-stars-fill fs-5" id="themeIcon"></i>
        </button>

        <!-- Notifications -->
        <div class="dropdown" id="notifDropdown">
            <button class="btn lms-notif-btn position-relative" id="notifBell"
                    data-bs-toggle="dropdown" aria-expanded="false"
                    title="Notifikasi" onclick="loadNotif()">
                <i class="bi bi-bell-fill fs-5"></i>
                <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end lms-notif-menu" id="notifMenu">
                <div class="notif-menu-header">
                    <span style="font-weight:700;color:white;">Notifikasi</span>
                    <button class="notif-mark-all" id="markAllBtn" title="Tandai semua dibaca">
                        <i class="bi bi-check2-all"></i> Semua dibaca
                    </button>
                </div>
                <div id="notifContent">
                    <div class="notif-loading"><div class="spinner-border spinner-border-sm text-secondary"></div></div>
                </div>
                <div class="notif-menu-footer">
                    <a href="<?= base_url('notifikasi') ?>">Lihat Semua Notifikasi →</a>
                </div>
            </div>
        </div>

        <!-- Role Badge -->
        <span class="badge lms-role-badge d-none d-sm-inline-flex align-items-center gap-1">
            <?php
            $roleIcon = match(session()->get('role')) {
                'Admin' => '🛡️',
                'Guru'  => '👨‍🏫',
                'Siswa' => '🎓',
                default => '👤',
            };
            echo $roleIcon . ' ' . session()->get('role');
            ?>
        </span>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn lms-user-btn dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <div class="lms-avatar">
                    <?= strtoupper(substr(session()->get('nama_lengkap') ?? 'U', 0, 1)) ?>
                </div>
                <span class="d-none d-md-block" style="font-size:0.9rem;">
                    <?= esc(session()->get('nama_lengkap')) ?>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end lms-dropdown">
                <li>
                    <span class="dropdown-item-text small text-muted">
                        Masuk sebagai <strong class="text-white"><?= esc(session()->get('role')) ?></strong>
                    </span>
                </li>
                <li><hr class="dropdown-divider border-secondary"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2"
                       href="<?= base_url('profil') ?>">
                        <i class="bi bi-person-circle"></i> Profil Saya
                    </a>
                </li>
                <li><hr class="dropdown-divider border-secondary"></li>
                <li>
                    <a class="dropdown-item text-danger d-flex align-items-center gap-2"
                       href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

    </div>
</nav>
<!-- =================== END NAVBAR =================== -->


<!-- ============= LAYOUT WRAPPER ==================== -->
<div class="lms-wrapper" id="lmsWrapper">

    <!-- ========== SIDEBAR ========================= -->
    <aside class="lms-sidebar" id="lmsSidebar">
        <nav class="lms-sidenav">

            <!-- Grup: Umum -->
            <div class="lms-nav-group">
                <span class="lms-nav-group-label">Umum</span>
                <a href="<?= base_url('dashboard') ?>"
                   data-label="Dashboard"
                   class="lms-nav-link <?= uri_string() === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('kalender') ?>"
                   data-label="Kalender"
                   class="lms-nav-link <?= uri_string() === 'kalender' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event"></i>
                    <span>Kalender Akademik</span>
                </a>
            </div>

            <?php if (session()->get('role') === 'Admin'): ?>
            <!-- Grup: Data Master (Admin Only) -->
            <div class="lms-nav-group">
                <span class="lms-nav-group-label">Data Master</span>
                <a href="<?= base_url('master/tahun-pelajaran') ?>"
                   data-label="Tahun Pelajaran"
                   class="lms-nav-link <?= str_contains(uri_string(), 'tahun-pelajaran') ? 'active' : '' ?>">
                    <i class="bi bi-calendar3"></i>
                    <span>Tahun Pelajaran</span>
                </a>
                <a href="<?= base_url('master/mata-pelajaran') ?>"
                   data-label="Mata Pelajaran"
                   class="lms-nav-link <?= str_contains(uri_string(), 'mata-pelajaran') ? 'active' : '' ?>">
                    <i class="bi bi-book"></i>
                    <span>Mata Pelajaran</span>
                </a>
                <a href="<?= base_url('master/kelas') ?>"
                   data-label="Data Kelas"
                   class="lms-nav-link <?= (str_contains(uri_string(), 'kelas') && !str_contains(uri_string(), 'anggota')) ? 'active' : '' ?>">
                    <i class="bi bi-door-open"></i>
                    <span>Data Kelas</span>
                </a>
                <a href="<?= base_url('master/anggota-kelas') ?>"
                   data-label="Anggota Kelas"
                   class="lms-nav-link <?= str_contains(uri_string(), 'anggota-kelas') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Anggota Kelas</span>
                </a>
            </div>

            <!-- Grup: Pengguna (Admin Only) -->
            <div class="lms-nav-group">
                <span class="lms-nav-group-label">Pengguna</span>
                <a href="<?= base_url('users/guru') ?>"
                   data-label="Data Guru"
                   class="lms-nav-link <?= str_contains(uri_string(), 'users/guru') ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i>
                    <span>Data Guru</span>
                </a>
                <a href="<?= base_url('users/siswa') ?>"
                   data-label="Data Siswa"
                   class="lms-nav-link <?= str_contains(uri_string(), 'users/siswa') ? 'active' : '' ?>">
                    <i class="bi bi-person-video2"></i>
                    <span>Data Siswa</span>
                </a>
            </div>

            <!-- Grup: Pengaturan (Admin Only) -->
            <div class="lms-nav-group">
                <span class="lms-nav-group-label">Pengaturan</span>
                <a href="<?= base_url('pengaturan/sekolah') ?>"
                   data-label="Profil Sekolah"
                   class="lms-nav-link <?= str_contains(uri_string(), 'pengaturan') ? 'active' : '' ?>">
                    <i class="bi bi-gear-wide-connected"></i>
                    <span>Profil Sekolah</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- Grup: Akademik (semua role) -->
            <div class="lms-nav-group">
                <span class="lms-nav-group-label">Akademik</span>

                <a href="<?= base_url('jadwal') ?>"
                   data-label="Jadwal Pelajaran"
                   class="lms-nav-link <?= str_contains(uri_string(), 'jadwal') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-week"></i>
                    <span>Jadwal Pelajaran</span>
                </a>

                <?php if (session()->get('role') !== 'Admin'): ?>
                <a href="<?= base_url('presensi') ?>"
                   data-label="<?= session()->get('role') === 'Guru' ? 'Input Presensi' : 'Riwayat Kehadiran' ?>"
                   class="lms-nav-link <?= str_contains(uri_string(), 'presensi') ? 'active' : '' ?>">
                    <i class="bi bi-check2-square"></i>
                    <span><?= session()->get('role') === 'Guru' ? 'Input Presensi' : 'Riwayat Kehadiran' ?></span>
                </a>
                <?php if (session()->get('role') === 'Guru'): ?>
                <a href="<?= base_url('jurnal') ?>"
                   data-label="Jurnal Mengajar"
                   class="lms-nav-link <?= str_contains(uri_string(), 'jurnal') ? 'active' : '' ?>">
                    <i class="bi bi-journal-text"></i>
                    <span>Jurnal Mengajar Harian</span>
                </a>
                <a href="<?= base_url('rpp') ?>"
                   data-label="Modul Ajar"
                   class="lms-nav-link <?= str_contains(uri_string(), 'rpp') ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-richtext"></i>
                    <span>Bank RPP / Modul Ajar</span>
                </a>
                <a href="<?= base_url('prota') ?>"
                   data-label="Prota & Promes"
                   class="lms-nav-link <?= str_contains(uri_string(), 'prota') ? 'active' : '' ?>">
                    <i class="bi bi-calendar3-range"></i>
                    <span>Prota & Promes</span>
                </a>
                <a href="<?= base_url('export') ?>"
                   data-label="Cetak Dokumen"
                   class="lms-nav-link <?= str_contains(uri_string(), 'export') ? 'active' : '' ?>">
                    <i class="bi bi-printer"></i>
                    <span>Pusat Cetak Dokumen</span>
                </a>
                <?php endif; ?>
                <?php
                $materiUrl    = session()->get('role') === 'Siswa' ? base_url('materi-siswa') : base_url('materi');
                $materiActive = (str_contains(uri_string(), 'materi-siswa') || (str_contains(uri_string(), 'materi') && session()->get('role') !== 'Siswa')) ? 'active' : '';
                ?>
                <a href="<?= $materiUrl ?>"
                   data-label="Materi Pembelajaran"
                   class="lms-nav-link <?= $materiActive ?>">
                    <i class="bi bi-folder2-open"></i>
                    <span>Materi Pembelajaran</span>
                </a>
                <a href="<?= base_url('ulangan') ?>"
                   data-label="<?= session()->get('role') === 'Guru' ? 'Bank Ujian / Kuis' : 'Ujian Online' ?>"
                   class="lms-nav-link <?= str_contains(uri_string(), 'ulangan') ? 'active' : '' ?>">
                    <i class="bi bi-pencil-square"></i>
                    <span><?= session()->get('role') === 'Guru' ? 'Bank Ujian / Kuis' : 'Ujian Online' ?></span>
                </a>
                <a href="<?= base_url('buku-nilai') ?>"
                   data-label="<?= session()->get('role') === 'Guru' ? 'Rekap Nilai Kelas' : 'Buku Nilai Saya' ?>"
                   class="lms-nav-link <?= str_contains(uri_string(), 'buku-nilai') ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-steps"></i>
                    <span><?= session()->get('role') === 'Guru' ? 'Rekap Nilai Kelas' : 'Buku Nilai Saya' ?></span>
                </a>
                <?php endif; ?>
            </div>

        </nav>

        <!-- Sidebar Footer: Profil & Logout -->
        <div class="lms-sidebar-footer">
            <a href="<?= base_url('profil') ?>"
               data-label="Profil Saya"
               class="lms-nav-link <?= str_contains(uri_string(), 'profil') ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>
            <a href="<?= base_url('logout') ?>"
               data-label="Logout"
               class="lms-nav-link text-danger-lms">
                <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </a>
        </div>

        <div class="mt-auto py-3 text-center" style="font-size:0.75rem; color:#64748b; border-top: 1px solid rgba(255,255,255,0.05); margin-top: auto;">
            <?php $appVersion = new \Config\AppVersion(); ?>
            Perangkat Pembelajaran &copy; <?= date('Y') ?><br>
            Terakhir diperbarui:<br>
            <span class="text-white"><?= esc($appVersion->lastUpdated) ?></span>
        </div>
    </aside>
    <!-- ========== END SIDEBAR ===================== -->


    <!-- ========== MAIN CONTENT ==================== -->
    <main class="lms-content page-transition" id="lmsContent">

        <!-- Page Header -->
        <div class="lms-page-header">
            <div>
                <h1 class="lms-page-title"><?= esc($title ?? 'Halaman') ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb lms-breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= esc($title ?? 'Halaman') ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Flash: Success -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert lms-alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Flash: Error -->
        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert lms-alert-error alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- ======= CONTENT SECTION (diisi view child) ======= -->
        <?= $this->renderSection('content') ?>
        <!-- ==================================================== -->

    </main>
    <!-- ========== END MAIN CONTENT ================ -->

</div>
<!-- ============= END LAYOUT WRAPPER ================ -->

<!-- Sidebar Overlay (Mobile) -->
<div class="lms-overlay" id="sidebarOverlay"></div>


<!-- ==================== SCRIPTS ==================== -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- DataTables JS + Bootstrap 5 adapter -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// ── Sidebar Toggle Logic ──────────────────────────
const sidebarToggle = document.getElementById('sidebarToggle');
const lmsSidebar    = document.getElementById('lmsSidebar');
const lmsOverlay    = document.getElementById('sidebarOverlay');
const lmsWrapper    = document.getElementById('lmsWrapper');
const SIDEBAR_KEY   = 'lms_sidebar_collapsed';

function isCollapsed() {
    return localStorage.getItem(SIDEBAR_KEY) === '1';
}

function applyState() {
    const collapsed = isCollapsed();
    lmsSidebar.classList.toggle('collapsed', collapsed);
    lmsWrapper.classList.toggle('sidebar-collapsed', collapsed);
    sidebarToggle.innerHTML = collapsed
        ? '<i class="bi bi-layout-sidebar-inset fs-5"></i>'
        : '<i class="bi bi-list fs-5"></i>';
}

sidebarToggle.addEventListener('click', function () {
    if (window.innerWidth < 992) {
        // Mobile: slide in/out
        lmsSidebar.classList.toggle('show');
        lmsOverlay.classList.toggle('show');
    } else {
        // Desktop: collapse/expand
        localStorage.setItem(SIDEBAR_KEY, isCollapsed() ? '0' : '1');
        applyState();
    }
});

lmsOverlay.addEventListener('click', function () {
    lmsSidebar.classList.remove('show');
    lmsOverlay.classList.remove('show');
});

// Apply state on page load (no animation flash)
applyState();

// ── DataTables Init ───────────────────────────────
$(document).ready(function () {
    $('.datatable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search:      'Cari:',
            lengthMenu:  'Tampilkan _MENU_ data',
            info:        'Menampilkan _START_ s/d _END_ dari _TOTAL_ data',
            paginate:    { first: '«', last: '»', next: '›', previous: '‹' },
            zeroRecords: 'Tidak ada data yang cocok.',
            emptyTable:  'Belum ada data.'
        }
    });
});

// ── Auto-dismiss Flash Alert ──────────────────────
setTimeout(function () {
    document.querySelectorAll('.alert.fade.show').forEach(function (el) {
        var bsAlert = bootstrap.Alert.getInstance(el) || new bootstrap.Alert(el);
        bsAlert.close();
    });
}, 4500);

// ── Notifikasi Polling & Dropdown ─────────────────
const notifBell   = document.getElementById('notifBell');
const notifBadge  = document.getElementById('notifBadge');
const notifContent= document.getElementById('notifContent');
const markAllBtn  = document.getElementById('markAllBtn');

function updateBadge(count) {
    if (count > 0) {
        notifBadge.innerText = count > 99 ? '99+' : count;
        notifBadge.style.display = 'flex';
        notifBell.classList.add('has-notif');
    } else {
        notifBadge.style.display = 'none';
        notifBell.classList.remove('has-notif');
    }
}

function pollNotifUnread() {
    fetch('<?= base_url('notifikasi/unread') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => updateBadge(data.count))
        .catch(err => console.error(err));
}

function loadNotif() {
    notifContent.innerHTML = '<div class="notif-loading"><div class="spinner-border spinner-border-sm text-secondary"></div></div>';
    fetch('<?= base_url('notifikasi/recent') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            notifContent.innerHTML = data.html;
            updateBadge(data.count);
        })
        .catch(err => {
            notifContent.innerHTML = '<div class="notif-empty text-danger"><i class="bi bi-exclamation-triangle"></i><br>Gagal memuat notifikasi</div>';
        });
}

if (markAllBtn) {
    markAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fetch('<?= base_url('notifikasi/baca-semua') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(res => res.json()).then(data => {
            if(data.ok) loadNotif();
        });
    });
}

// Cek unread saat load dan tiap 60 detik
<?php if (session()->get('id')): ?>
pollNotifUnread();
setInterval(pollNotifUnread, 60000);
<?php endif; ?>
</script>

<!-- Theme Toggle Script (terpisah agar tidak konflik) -->
<script>
(function() {
    const themeBtn  = document.getElementById('themeToggleBtn');
    const themeIcon = document.getElementById('themeIcon');

    // Set ikon sesuai tema saat ini
    function syncIcon() {
        if (!themeIcon) return;
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        themeIcon.className = isDark
            ? 'bi bi-sun-fill fs-5'
            : 'bi bi-moon-stars-fill fs-5';
    }

    // Terapkan ikon saat pertama load
    syncIcon();

    // Event klik tombol sakelar
    if (themeBtn) {
        themeBtn.addEventListener('click', function() {
            const current  = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = current === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme',    newTheme);
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('lms_theme', newTheme);
            syncIcon();
        });
    }
})();
</script>

<!-- Extra scripts dari child view -->
<?= $this->renderSection('scripts') ?>

<!-- ===================== BOTTOM NAVIGATION (MOBILE) ===================== -->
<?php if (session()->get('id')): ?>
<nav class="lms-bottom-nav">
    <a href="<?= base_url('dashboard') ?>" class="bottom-nav-item <?= uri_string() === 'dashboard' ? 'active' : '' ?>">
        <i class="bi <?= uri_string() === 'dashboard' ? 'bi-house-door-fill' : 'bi-house-door' ?>"></i>
        <span>Beranda</span>
    </a>
    
    <?php if (session()->get('role') === 'Guru'): ?>
        <a href="<?= base_url('presensi') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'presensi') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'presensi') ? 'bi-check2-square' : 'bi-check2-circle' ?>"></i>
            <span>Presensi</span>
        </a>
        <a href="<?= base_url('jurnal') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'jurnal') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'jurnal') ? 'bi-journal-check' : 'bi-journal-text' ?>"></i>
            <span>Jurnal</span>
        </a>
        <a href="<?= base_url('rpp') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'rpp') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'rpp') ? 'bi-folder-fill' : 'bi-folder' ?>"></i>
            <span>Modul Ajar</span>
        </a>
    <?php elseif (session()->get('role') === 'Siswa'): ?>
        <a href="<?= base_url('jadwal') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'jadwal') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'jadwal') ? 'bi-calendar-event-fill' : 'bi-calendar-event' ?>"></i>
            <span>Jadwal</span>
        </a>
        <a href="<?= base_url('materi-siswa') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'materi-siswa') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'materi-siswa') ? 'bi-book-fill' : 'bi-book' ?>"></i>
            <span>Materi</span>
        </a>
        <a href="<?= base_url('ulangan') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'ulangan') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'ulangan') ? 'bi-pen-fill' : 'bi-pen' ?>"></i>
            <span>Ujian</span>
        </a>
    <?php else: // Admin ?>
        <a href="<?= base_url('master/kelas') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'master/kelas') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'master/kelas') ? 'bi-door-open-fill' : 'bi-door-open' ?>"></i>
            <span>Kelas</span>
        </a>
        <a href="<?= base_url('users/guru') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'users/guru') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'users/guru') ? 'bi-person-badge-fill' : 'bi-person-badge' ?>"></i>
            <span>Guru</span>
        </a>
        <a href="<?= base_url('users/siswa') ?>" class="bottom-nav-item <?= str_contains(uri_string(), 'users/siswa') ? 'active' : '' ?>">
            <i class="bi <?= str_contains(uri_string(), 'users/siswa') ? 'bi-person-video2' : 'bi-person-video' ?>"></i>
            <span>Siswa</span>
        </a>
    <?php endif; ?>

    <a href="#" class="bottom-nav-item" id="bottomNavMoreBtn">
        <i class="bi bi-grid"></i>
        <span>Lainnya</span>
    </a>
</nav>

<script>
    // Handler untuk tombol "Lainnya" di Bottom Nav
    document.addEventListener('DOMContentLoaded', function() {
        const bottomNavMoreBtn = document.getElementById('bottomNavMoreBtn');
        if (bottomNavMoreBtn) {
            bottomNavMoreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const sidebarBtn = document.getElementById('sidebarToggle');
                if (sidebarBtn) sidebarBtn.click(); // Trigger sidebar yang sudah ada
            });
        }
    });
</script>
<?php endif; ?>

</body>
</html>
