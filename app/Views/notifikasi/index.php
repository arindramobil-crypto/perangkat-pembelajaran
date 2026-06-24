<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.notif-page-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 14px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.07);
    margin-bottom: 10px;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}
.notif-page-item.unread {
    background: rgba(129,140,248,0.06);
    border-color: rgba(129,140,248,0.2);
}
.notif-page-item.unread::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: #818CF8;
    border-radius: 0;
}
.notif-page-item:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.12);
}
.notif-icon-lg {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.notif-badge-type {
    font-size: 0.65rem; font-weight: 700;
    padding: 2px 8px; border-radius: 100px;
    display: inline-block; margin-bottom: 4px;
}
.notif-page-title {
    font-size: 0.9rem; font-weight: 700;
    color: white; margin-bottom: 4px;
}
.notif-page-msg {
    font-size: 0.82rem; color: var(--lms-text-muted);
    line-height: 1.4; margin-bottom: 6px;
}
.notif-page-time {
    font-size: 0.72rem; color: var(--lms-text-muted);
}
.notif-actions {
    display: flex; gap: 6px; margin-left: auto;
    align-items: center; flex-shrink: 0;
}
.notif-actions a, .notif-actions button {
    font-size: 0.7rem; padding: 4px 10px;
    border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.04); color: var(--lms-text-muted);
    cursor: pointer; text-decoration: none; transition: all 0.15s;
}
.notif-actions a:hover { background: rgba(129,140,248,0.15); color: #818CF8; border-color: rgba(129,140,248,0.3); }
.notif-actions button:hover { background: rgba(239,68,68,0.15); color: #EF4444; border-color: rgba(239,68,68,0.3); }
.empty-notif { text-align: center; padding: 60px 20px; }
.filter-tabs { display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap; }
.filter-tab {
    padding: 6px 16px; border-radius: 100px; font-size: 0.78rem; font-weight: 600;
    border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04);
    color: var(--lms-text-muted); cursor: pointer; transition: all 0.15s; text-decoration: none;
}
.filter-tab.active, .filter-tab:hover {
    background: rgba(129,140,248,0.15); color: #818CF8;
    border-color: rgba(129,140,248,0.3);
}
</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 style="color:white;font-weight:800;margin:0;">
            <i class="bi bi-bell-fill me-2" style="color:#818CF8;"></i>Notifikasi
        </h4>
        <small class="text-lms-muted">
            <?= $unread > 0 ? "<span style='color:#818CF8;font-weight:700;'>{$unread} belum dibaca</span>" : 'Semua sudah dibaca' ?>
        </small>
    </div>
    <?php if ($unread > 0): ?>
    <form method="post" action="<?= base_url('notifikasi/baca-semua') ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm"
                style="background:rgba(129,140,248,0.12);border:1px solid rgba(129,140,248,0.25);color:#818CF8;">
            <i class="bi bi-check2-all me-1"></i> Tandai Semua Dibaca
        </button>
    </form>
    <?php endif; ?>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs" id="filterTabs">
    <a href="#" class="filter-tab active" data-filter="all">Semua</a>
    <a href="#" class="filter-tab" data-filter="unread">Belum Dibaca</a>
    <a href="#" class="filter-tab" data-filter="materi">📚 Materi</a>
    <a href="#" class="filter-tab" data-filter="ujian">📝 Ujian</a>
    <a href="#" class="filter-tab" data-filter="nilai">📊 Nilai</a>
    <a href="#" class="filter-tab" data-filter="koreksi">✏️ Koreksi</a>
</div>

<!-- Daftar Notifikasi -->
<div id="notifList">
<?php if (empty($notifs)): ?>
<div class="glass-panel card empty-notif">
    <i class="bi bi-bell-slash" style="font-size:3rem;color:var(--lms-text-muted);display:block;margin-bottom:12px;"></i>
    <h5 style="color:white;">Tidak Ada Notifikasi</h5>
    <p style="color:var(--lms-text-muted);">Notifikasi akan muncul ketika ada materi baru, ujian baru, atau pembaruan nilai.</p>
</div>
<?php else: ?>
<?php
$tipeColors = [
    'materi'   => ['color'=>'#38BDF8', 'bg'=>'rgba(56,189,248,0.12)'],
    'ujian'    => ['color'=>'#F59E0B', 'bg'=>'rgba(245,158,11,0.12)'],
    'nilai'    => ['color'=>'#22C55E', 'bg'=>'rgba(34,197,94,0.12)'],
    'presensi' => ['color'=>'#818CF8', 'bg'=>'rgba(129,140,248,0.12)'],
    'koreksi'  => ['color'=>'#EF4444', 'bg'=>'rgba(239,68,68,0.12)'],
    'sistem'   => ['color'=>'#94A3B8', 'bg'=>'rgba(148,163,184,0.12)'],
];
$tipeIcons = [
    'materi'=>'📚','ujian'=>'📝','nilai'=>'📊','presensi'=>'✅','koreksi'=>'✏️','sistem'=>'🔔'
];
foreach ($notifs as $n):
    $tc    = $tipeColors[$n['tipe']] ?? $tipeColors['sistem'];
    $icon  = $tipeIcons[$n['tipe']] ?? '🔔';
    $unreadCls = $n['is_read'] ? '' : 'unread';
    $ago   = '';
    $diff  = time() - strtotime($n['created_at']);
    if ($diff < 60)     $ago = 'Baru saja';
    elseif ($diff < 3600)   $ago = (int)($diff/60) . ' menit lalu';
    elseif ($diff < 86400)  $ago = (int)($diff/3600) . ' jam lalu';
    elseif ($diff < 604800) $ago = (int)($diff/86400) . ' hari lalu';
    else $ago = date('d M Y, H:i', strtotime($n['created_at']));
?>
<div class="notif-page-item <?= $unreadCls ?>" data-tipe="<?= $n['tipe'] ?>" data-read="<?= $n['is_read'] ?>">
    <div class="notif-icon-lg" style="background:<?= $tc['bg'] ?>;">
        <span><?= $icon ?></span>
    </div>
    <div style="flex:1;min-width:0;">
        <div class="notif-badge-type" style="background:<?= $tc['bg'] ?>;color:<?= $tc['color'] ?>;">
            <?= ucfirst($n['tipe']) ?>
        </div>
        <div class="notif-page-title"><?= esc($n['judul']) ?></div>
        <div class="notif-page-msg"><?= esc($n['pesan']) ?></div>
        <div class="notif-page-time">
            <i class="bi bi-clock me-1"></i><?= $ago ?>
        </div>
    </div>
    <div class="notif-actions">
        <?php if ($n['url']): ?>
        <a href="<?= base_url('notifikasi/baca/' . $n['id']) ?>">
            <i class="bi bi-arrow-right me-1"></i>Lihat
        </a>
        <?php endif; ?>
        <?php if (!$n['is_read']): ?>
        <a href="<?= base_url('notifikasi/baca/' . $n['id']) ?>" onclick="event.preventDefault(); markRead(<?= $n['id'] ?>, this);">
            <i class="bi bi-check2 me-1"></i>Baca
        </a>
        <?php endif; ?>
        <form method="post" action="<?= base_url('notifikasi/hapus/' . $n['id']) ?>" style="display:inline;">
            <?= csrf_field() ?>
            <button type="submit" title="Hapus" onclick="return confirm('Hapus notifikasi ini?')">
                <i class="bi bi-trash3"></i>
            </button>
        </form>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// ── Filter tab ────────────────────────────────────────────────
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const filter = tab.dataset.filter;
        document.querySelectorAll('.notif-page-item').forEach(item => {
            const show =
                filter === 'all'    ? true :
                filter === 'unread' ? item.dataset.read === '0' :
                item.dataset.tipe   === filter;
            item.style.display = show ? 'flex' : 'none';
        });
    });
});

// ── Mark read via fetch ───────────────────────────────────────
function markRead(id, el) {
    fetch(`<?= base_url('notifikasi/baca/') ?>${id}`, { method: 'GET', redirect: 'manual' });
    const item = el.closest('.notif-page-item');
    item.classList.remove('unread');
    item.dataset.read = '1';
    el.remove();
}
</script>
<?= $this->endSection() ?>
