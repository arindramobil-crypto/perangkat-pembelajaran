<?php
// Ambil pengaturan sekolah
$_sekolah = [];
try {
    $_sekolah = (new \App\Models\PengaturanSekolahModel())->getPengaturan();
} catch (\Throwable $e) {}
$_namaSekolah = $_sekolah['nama_sekolah'] ?? 'LMS SMK';
$_logoFile    = $_sekolah['logo']        ?? null;
$_logoUrl     = $_logoFile ? base_url('uploads/logo/' . $_logoFile) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login' ?> — <?= esc($_namaSekolah) ?></title>
    <meta name="description" content="Sistem Perangkat Pembelajaran <?= esc($_namaSekolah) ?> — Login">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <?php if ($_logoUrl): ?>
    <link rel="icon" href="<?= $_logoUrl ?>">
    <link rel="apple-touch-icon" href="<?= $_logoUrl ?>">
    <?php else: ?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">
    <?php endif; ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* ══════════════════════════════════════════════════════════
   RESET & BASE
   ══════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --primary:   #4F46E5;
    --accent:    #818CF8;
    --success:   #22C55E;
    --danger:    #EF4444;
    --bg:        #0A0F1E;
    --text:      #F8FAFC;
    --muted:     #94A3B8;
    --border:    rgba(255,255,255,0.08);
    --glass-bg:  rgba(15,23,42,0.65);
    --blur:      blur(20px);
    --radius:    16px;
}

html, body {
    height: 100%;
    font-family: 'Inter', system-ui, sans-serif;
    background: var(--bg);
    color: var(--text);
    overflow: hidden;
}

/* ══════════════════════════════════════════════════════════
   ANIMATED BACKGROUND
   ══════════════════════════════════════════════════════════ */
.login-bg {
    position: fixed;
    inset: 0;
    z-index: 0;
    background:
        radial-gradient(ellipse 80% 60% at 10% 20%,  rgba(79,70,229,0.30) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 90% 80%,  rgba(139,92,246,0.25) 0%, transparent 60%),
        radial-gradient(ellipse 50% 40% at 50% 110%, rgba(34,197,94,0.10)  0%, transparent 60%),
        linear-gradient(135deg, #060912 0%, #0A0F1E 50%, #0D1526 100%);
    animation: bgPulse 12s ease-in-out infinite alternate;
}
@keyframes bgPulse {
    0%   { filter: hue-rotate(0deg); }
    100% { filter: hue-rotate(15deg); }
}

/* Partikel mengambang */
.orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(60px);
    animation: orbFloat linear infinite;
    pointer-events: none;
    z-index: 0;
}
.orb-1 { width:350px; height:350px; background:rgba(79,70,229,0.18);  top:-80px;  left:-60px;  animation-duration:18s; }
.orb-2 { width:280px; height:280px; background:rgba(139,92,246,0.14); bottom:-60px; right:-40px; animation-duration:22s; animation-delay:-8s; }
.orb-3 { width:200px; height:200px; background:rgba(34,197,94,0.08);  top:40%;  right:15%;    animation-duration:15s; animation-delay:-4s; }

@keyframes orbFloat {
    0%,100% { transform: translate(0, 0) scale(1); }
    33%      { transform: translate(20px, -30px) scale(1.05); }
    66%      { transform: translate(-15px, 20px) scale(0.95); }
}

/* Grid overlay halus */
.grid-overlay {
    position: fixed;
    inset: 0;
    z-index: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
}

/* ══════════════════════════════════════════════════════════
   LAYOUT WRAPPER
   ══════════════════════════════════════════════════════════ */
.login-wrapper {
    position: relative;
    z-index: 1;
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 480px;
    align-items: center;
    overflow-y: auto;
}

/* ══════════════════════════════════════════════════════════
   LEFT PANEL — Info Sekolah
   ══════════════════════════════════════════════════════════ */
.info-panel {
    padding: 3rem 4rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 100vh;
    animation: slideInLeft 0.8s cubic-bezier(0.16,1,0.3,1) both;
}
@keyframes slideInLeft {
    from { opacity:0; transform: translateX(-40px); }
    to   { opacity:1; transform: translateX(0); }
}

.school-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(79,70,229,0.15);
    border: 1px solid rgba(79,70,229,0.3);
    border-radius: 100px;
    padding: 8px 20px;
    font-size: 0.8rem;
    color: #818CF8;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 2.5rem;
    width: fit-content;
    backdrop-filter: blur(8px);
}
.school-badge::before {
    content: '';
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #22C55E;
    box-shadow: 0 0 8px #22C55E;
    animation: blink 2s ease-in-out infinite;
}
@keyframes blink {
    0%,100% { opacity:1; } 50% { opacity:0.4; }
}

.info-panel h1 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 1.25rem;
    letter-spacing: -0.02em;
}
.info-panel h1 span {
    background: linear-gradient(135deg, #818CF8 0%, #C4B5FD 50%, #818CF8 100%);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer 4s linear infinite;
}
@keyframes shimmer {
    to { background-position: 200% center; }
}

.info-panel p.subtitle {
    font-size: 1.05rem;
    color: var(--muted);
    line-height: 1.7;
    max-width: 460px;
    margin-bottom: 3rem;
}

/* Fitur cards */
.feature-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 420px;
}
.feature-item {
    display: flex;
    align-items: center;
    gap: 14px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 14px 18px;
    backdrop-filter: blur(8px);
    transition: background 0.3s, border-color 0.3s;
    animation: fadeUp 0.6s ease both;
}
.feature-item:nth-child(1) { animation-delay: 0.3s; }
.feature-item:nth-child(2) { animation-delay: 0.45s; }
.feature-item:nth-child(3) { animation-delay: 0.6s; }
.feature-item:hover { background: rgba(79,70,229,0.08); border-color: rgba(79,70,229,0.2); }

@keyframes fadeUp {
    from { opacity:0; transform: translateY(16px); }
    to   { opacity:1; transform: translateY(0); }
}

.feature-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.feature-text strong {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 2px;
}
.feature-text span {
    font-size: 0.78rem;
    color: var(--muted);
}

/* ══════════════════════════════════════════════════════════
   RIGHT PANEL — Form Login
   ══════════════════════════════════════════════════════════ */
.login-panel {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 2.5rem;
    border-left: 1px solid rgba(255,255,255,0.05);
    background: rgba(10,15,30,0.5);
    backdrop-filter: blur(24px);
    animation: slideInRight 0.8s cubic-bezier(0.16,1,0.3,1) 0.1s both;
}
@keyframes slideInRight {
    from { opacity:0; transform: translateX(40px); }
    to   { opacity:1; transform: translateX(0); }
}

.login-card {
    width: 100%;
    max-width: 400px;
}

/* Logo / Brand */
.brand {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 2.5rem;
}
.brand-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #4F46E5, #7C3AED);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    box-shadow: 0 8px 24px rgba(79,70,229,0.4);
}
.brand-text {
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: -0.01em;
}
.brand-text span { color: #818CF8; }
.brand-sub {
    font-size: 0.72rem;
    color: var(--muted);
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.login-card h2 {
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin-bottom: 0.4rem;
}
.login-card .tagline {
    font-size: 0.88rem;
    color: var(--muted);
    margin-bottom: 2rem;
}

/* Alert */
.alert-lms {
    background: rgba(239,68,68,0.12);
    border: 1px solid rgba(239,68,68,0.3);
    color: #FCA5A5;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.85rem;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 1.5rem;
    animation: shake 0.4s ease;
}
@keyframes shake {
    0%,100% { transform: translateX(0); }
    20%      { transform: translateX(-6px); }
    40%      { transform: translateX(6px); }
    60%      { transform: translateX(-4px); }
    80%      { transform: translateX(4px); }
}

/* Form */
.form-grp {
    margin-bottom: 1.25rem;
}
.form-grp label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 7px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
.input-wrap {
    position: relative;
}
.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 1rem;
    pointer-events: none;
    transition: color 0.3s;
}
.input-wrap input {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1.5px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 13px 42px;
    font-size: 0.92rem;
    color: var(--text);
    font-family: inherit;
    outline: none;
    transition: border-color 0.3s, background 0.3s, box-shadow 0.3s;
}
.input-wrap input::placeholder { color: rgba(148,163,184,0.5); }
.input-wrap input:focus {
    border-color: #4F46E5;
    background: rgba(79,70,229,0.08);
    box-shadow: 0 0 0 3px rgba(79,70,229,0.15);
}
.input-wrap input:focus + .focus-ring { opacity: 1; }
.input-wrap input:focus ~ .input-icon { color: #818CF8; }

/* Toggle password */
.btn-pass {
    position: absolute;
    right: 13px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    font-size: 1rem;
    padding: 4px;
    border-radius: 6px;
    transition: color 0.2s;
    line-height: 1;
}
.btn-pass:hover { color: var(--text); }

/* Role selector */
.role-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 1.5rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 5px;
}
.role-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    padding: 10px 6px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--muted);
    transition: all 0.25s;
    border: 1px solid transparent;
    text-align: center;
}
.role-tab .ri { font-size: 1.1rem; }
.role-tab:hover { background: rgba(255,255,255,0.05); color: var(--text); }
.role-tab.active { background: rgba(79,70,229,0.18); color: #818CF8; border-color: rgba(79,70,229,0.3); }

/* Tombol Login */
.btn-login {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #4F46E5, #6D28D9);
    color: white;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    letter-spacing: 0.02em;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    margin-top: 0.5rem;
    box-shadow: 0 4px 20px rgba(79,70,229,0.35);
}
.btn-login::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}
.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(79,70,229,0.5);
}
.btn-login:hover::before { opacity: 1; }
.btn-login:active { transform: translateY(0); }

/* Loading state */
.btn-login.loading {
    pointer-events: none;
    opacity: 0.8;
}
.btn-login .spinner {
    display: none;
    width: 18px; height: 18px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
.btn-login.loading .btn-text { display: none; }
.btn-login.loading .spinner { display: block; }

/* Info default password */
.default-info {
    background: rgba(129,140,248,0.06);
    border: 1px solid rgba(129,140,248,0.15);
    border-radius: 10px;
    padding: 12px 14px;
    margin-top: 1.5rem;
    font-size: 0.78rem;
    color: var(--muted);
}
.default-info strong { color: var(--text); }

/* Footer */
.login-footer {
    text-align: center;
    margin-top: 2rem;
    font-size: 0.75rem;
    color: var(--muted);
    opacity: 0.6;
}

/* ══════════════════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════════════════ */
@media (max-width: 900px) {
    html, body { overflow: auto; }
    .login-wrapper { grid-template-columns: 1fr; }
    .info-panel {
        min-height: auto;
        padding: 2.5rem 2rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        text-align: center;
        align-items: center;
    }
    .info-panel h1 { font-size: 1.8rem; }
    .info-panel p.subtitle { display: none; }
    .feature-list { flex-direction: row; flex-wrap: wrap; justify-content: center; }
    .feature-item { padding: 10px 14px; }
    .feature-text span { display: none; }
    .login-panel {
        min-height: auto;
        padding: 2rem 1.5rem 3rem;
        border-left: none;
        border-top: 1px solid rgba(255,255,255,0.05);
    }
}
@media (max-width: 480px) {
    .info-panel { padding: 1.5rem; }
    .feature-list { gap: 8px; }
    .feature-item { padding: 8px 12px; }
    .role-tabs { grid-template-columns: 1fr; }
    .role-tab { flex-direction: row; justify-content: center; }
}
</style>
</head>
<body>

<!-- Background Efek -->
<div class="login-bg"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>
<div class="grid-overlay"></div>

<div class="login-wrapper">

    <!-- ══ LEFT PANEL ══ -->
    <div class="info-panel">

        <div class="school-badge">
            <span>SMK Perangkat Pembelajaran</span>
        </div>

        <h1>
            Platform Belajar<br>
            <span>Digital Terpadu</span>
        </h1>

        <p class="subtitle">
            Sistem manajemen pembelajaran modern untuk guru dan siswa SMK.
            Kelola materi, ujian online, presensi, dan penilaian dalam satu platform terintegrasi.
        </p>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon" style="background:rgba(79,70,229,0.15);">
                    <i class="bi bi-journals" style="color:#818CF8;"></i>
                </div>
                <div class="feature-text">
                    <strong>Materi Pembelajaran</strong>
                    <span>Upload & akses materi PDF, video, dan presentasi</span>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon" style="background:rgba(34,197,94,0.12);">
                    <i class="bi bi-pencil-square" style="color:#22C55E;"></i>
                </div>
                <div class="feature-text">
                    <strong>Ujian Online (CBT)</strong>
                    <span>5 tipe soal: PG, Kompleks, Menjodohkan, B/S, Uraian</span>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon" style="background:rgba(245,158,11,0.12);">
                    <i class="bi bi-bar-chart-steps" style="color:#F59E0B;"></i>
                </div>
                <div class="feature-text">
                    <strong>Rekap Nilai & Presensi</strong>
                    <span>Pantau perkembangan akademik secara real-time</span>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon" style="background:rgba(236,72,153,0.12);">
                    <i class="bi bi-file-earmark-richtext" style="color:#EC4899;"></i>
                </div>
                <div class="feature-text">
                    <strong>Modul Ajar & Perangkat Guru</strong>
                    <span>Buat RPP digital dan kelola jurnal mengajar harian</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT PANEL — Form ══ -->
    <div class="login-panel">
        <div class="login-card">

            <!-- Brand -->
            <div class="brand">
                <div class="brand-icon">📚</div>
                <div>
                    <div class="brand-text">LMS <span>SMK</span></div>
                    <div class="brand-sub">Perangkat Pembelajaran</div>
                </div>
            </div>

            <h2>Masuk ke Akun</h2>
            <p class="tagline">Selamat datang! Masukkan kredensial Anda untuk melanjutkan.</p>

            <!-- Alert Error -->
            <?php if (session()->getFlashdata('msg')): ?>
            <div class="alert-lms" role="alert">
                <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
                <span><?= esc(session()->getFlashdata('msg')) ?></span>
            </div>
            <?php endif; ?>
            <?php if (isset($validation)): ?>
            <div class="alert-lms" role="alert">
                <i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i>
                <span><?= $validation->listErrors() ?></span>
            </div>
            <?php endif; ?>

            <!-- Tab Role removed by request -->
            <!-- Form -->
            <form action="<?= base_url('login') ?>" method="post" id="loginForm">
                <?= csrf_field() ?>

                <div class="form-grp">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text"
                               id="username"
                               name="username"
                               placeholder="Masukkan username"
                               required
                               autocomplete="username"
                               value="<?= set_value('username') ?>">
                    </div>
                </div>

                <div class="form-grp">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Masukkan password"
                               required
                               autocomplete="current-password">
                        <button type="button" class="btn-pass" id="togglePass" title="Tampilkan password">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <span class="btn-text">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </span>
                    <span class="spinner"></span>
                </button>
            </form>

            <!-- Info Akun Default -->
            <div class="default-info">
                <i class="bi bi-info-circle me-1" style="color:#818CF8;"></i>
                Password default — Guru: <strong>guru123</strong> &nbsp;|&nbsp; Siswa: <strong>siswa123</strong>
            </div>

            <div class="login-footer">
                &copy; <?= date('Y') ?> LMS SMK Perangkat Pembelajaran &mdash; Powered by <?= esc($_namaSekolah) ?>
            </div>

        </div>
    </div>
</div>

<script>
/* ── Toggle show/hide password ────────────────────────── */
const toggleBtn = document.getElementById('togglePass');
const passInput = document.getElementById('password');
const eyeIcon   = document.getElementById('eyeIcon');

toggleBtn.addEventListener('click', function () {
    const show = passInput.type === 'text';
    passInput.type    = show ? 'password' : 'text';
    eyeIcon.className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
    passInput.focus();
});

/* ── Pilih tab role (UX dekoratif) ───────────────────── */
function selectRole(role, el) {
    document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

/* ── Loading state saat submit ───────────────────────── */
document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('btnLogin');
    btn.classList.add('loading');
});

/* ── Enter di username → fokus ke password ───────────── */
document.getElementById('username').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('password').focus();
    }
});

/* ── Auto-focus username ─────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const un = document.getElementById('username');
    if (!un.value) un.focus();
});
</script>

</body>
</html>
