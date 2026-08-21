<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Helper fungsi mapping ikon & warna berdasarkan ekstensi file
function getIconInfo(string $ext, string $tipe): array {
    if ($tipe === 'link') return ['bi-play-btn-fill', '#38BDF8', 'Link'];
    return [
        'pdf'  => ['bi-file-earmark-pdf-fill', '#EF4444', 'PDF'],
        'docx' => ['bi-file-earmark-word-fill','#3B82F6', 'DOCX'],
        'doc'  => ['bi-file-earmark-word-fill','#3B82F6', 'DOC'],
        'pptx' => ['bi-file-earmark-ppt-fill', '#F97316', 'PPTX'],
        'ppt'  => ['bi-file-earmark-ppt-fill', '#F97316', 'PPT'],
        'xlsx' => ['bi-file-earmark-excel-fill','#22C55E', 'XLSX'],
        'xls'  => ['bi-file-earmark-excel-fill','#22C55E', 'XLS'],
        'mp4'  => ['bi-play-circle-fill',       '#A78BFA', 'MP4'],
        'jpg'  => ['bi-file-earmark-image-fill','#F59E0B', 'JPG'],
        'png'  => ['bi-file-earmark-image-fill','#F59E0B', 'PNG'],
    ][$ext] ?? ['bi-file-earmark-fill', '#94A3B8', strtoupper($ext) ?: '—'];
}
?>

<!-- ══ Banner Info Kelas ══ -->
<div class="glass-panel mb-4 p-3 p-md-4 d-flex flex-wrap align-items-center gap-3 justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(129,140,248,0.15);
                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-mortarboard-fill" style="font-size:1.3rem;color:#818CF8;"></i>
        </div>
        <div>
            <?php if ($kelas): ?>
            <strong style="color:white;font-size:1.05rem;">Kelas <?= esc($kelas['nama_kelas']) ?></strong><br>
            <small class="text-lms-muted">
                <?= count($materis) ?> materi ditemukan
                <?= $keyword ? ' untuk "<span style="color:#818CF8;">' . esc($keyword) . '</span>"' : '' ?>
            </small>
            <?php else: ?>
            <strong style="color:#F59E0B;">Belum Terdaftar di Kelas</strong><br>
            <small class="text-lms-muted">Hubungi admin untuk mendaftarkan Anda ke kelas.</small>
            <?php endif; ?>
        </div>
    </div>
    <!-- Kartu mini stat -->
    <?php if ($kelas): ?>
    <div class="d-flex gap-3">
        <div class="text-center" style="min-width:60px;">
            <div style="font-size:1.4rem;font-weight:700;color:#818CF8;"><?= count($materis) ?></div>
            <div style="font-size:0.7rem;color:var(--lms-text-muted);">Total</div>
        </div>
        <div class="text-center" style="min-width:60px;">
            <div style="font-size:1.4rem;font-weight:700;color:#22C55E;"><?= $totalFile ?></div>
            <div style="font-size:0.7rem;color:var(--lms-text-muted);">File</div>
        </div>
        <div class="text-center" style="min-width:60px;">
            <div style="font-size:1.4rem;font-weight:700;color:#38BDF8;"><?= $totalLink ?></div>
            <div style="font-size:0.7rem;color:var(--lms-text-muted);">Link</div>
        </div>
        <div class="text-center" style="min-width:60px;">
            <div style="font-size:1.4rem;font-weight:700;color:#F59E0B;"><?= count($mapelList) ?></div>
            <div style="font-size:0.7rem;color:var(--lms-text-muted);">Mapel</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (! $kelas): ?>
<!-- Siswa belum punya kelas -->
<div class="glass-panel card text-center py-5">
    <i class="bi bi-person-x" style="font-size:3.5rem;color:var(--lms-text-muted);"></i>
    <h5 style="color:white;margin-top:1.5rem;">Anda Belum Terdaftar di Kelas</h5>
    <p class="text-lms-muted">Minta admin sekolah untuk memasukkan Anda ke dalam kelas agar bisa mengakses materi pelajaran.</p>
</div>

<?php else: ?>

<!-- ══ Toolbar: Search + Filter + Sort ══ -->
<form method="get" action="<?= base_url('materi-siswa') ?>" id="formFilter">
<div class="glass-panel card mb-4">

    <!-- Baris 1: Search -->
    <div class="d-flex gap-3 mb-3 flex-wrap">
        <div class="flex-grow-1" style="position:relative;min-width:220px;">
            <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--lms-text-muted);pointer-events:none;"></i>
            <input type="text"
                   name="q"
                   id="searchInput"
                   class="form-control"
                   style="padding-left:2.6rem;"
                   placeholder="Cari judul, deskripsi, guru, atau mata pelajaran..."
                   value="<?= esc($keyword) ?>"
                   autocomplete="off">
            <?php if ($keyword): ?>
            <a href="<?= base_url('materi-siswa?' . http_build_query(['mapel' => $mapel_id, 'tipe' => $tipe, 'sort' => $sort])) ?>"
               style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--lms-text-muted);text-decoration:none;"
               title="Hapus pencarian">
                <i class="bi bi-x-circle"></i>
            </a>
            <?php endif; ?>
        </div>

        <!-- Filter Tipe -->
        <select name="tipe" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
            <option value=""   <?= $tipe===''     ? 'selected' : '' ?>>Semua Tipe</option>
            <option value="file" <?= $tipe==='file' ? 'selected' : '' ?>>📄 File Unduhan</option>
            <option value="link" <?= $tipe==='link' ? 'selected' : '' ?>>🔗 Link Eksternal</option>
        </select>

        <!-- Urutan -->
        <select name="sort" class="form-select" style="max-width:170px;" onchange="this.form.submit()">
            <option value="terbaru" <?= $sort==='terbaru' ? 'selected' : '' ?>>🕐 Terbaru</option>
            <option value="terlama" <?= $sort==='terlama' ? 'selected' : '' ?>>🕐 Terlama</option>
            <option value="az"      <?= $sort==='az'      ? 'selected' : '' ?>>🔤 A → Z</option>
            <option value="za"      <?= $sort==='za'      ? 'selected' : '' ?>>🔤 Z → A</option>
        </select>

        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-search me-1"></i>Cari
        </button>
    </div>

    <!-- Baris 2: Tab filter Mata Pelajaran -->
    <?php if (! empty($mapelList)): ?>
    <div class="d-flex flex-wrap gap-2" style="overflow-x:auto;">
        <a href="<?= base_url('materi-siswa?' . http_build_query(['q'=>$keyword,'tipe'=>$tipe,'sort'=>$sort,'mapel'=>0])) ?>"
           class="btn btn-sm <?= $mapel_id===0 ? 'btn-primary' : 'btn-outline-secondary' ?>">
            Semua Mapel <span class="badge ms-1" style="background:rgba(255,255,255,0.15);"><?= array_sum(array_column($mapelList,'jumlah')) ?></span>
        </a>
        <?php foreach ($mapelList as $mp): ?>
        <a href="<?= base_url('materi-siswa?' . http_build_query(['q'=>$keyword,'tipe'=>$tipe,'sort'=>$sort,'mapel'=>$mp['id']])) ?>"
           class="btn btn-sm <?= $mapel_id==$mp['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>">
            <?= esc($mp['kode_mapel'] ?: $mp['nama_mapel']) ?>
            <span class="badge ms-1" style="background:rgba(255,255,255,0.15);"><?= $mp['jumlah'] ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</form>

<!-- ══ Hasil Pencarian ══ -->
<?php if (empty($materis)): ?>
<div class="glass-panel card text-center py-5">
    <i class="bi bi-search" style="font-size:3rem;color:var(--lms-text-muted);"></i>
    <h5 style="color:white;margin-top:1.2rem;">Tidak Ada Materi Ditemukan</h5>
    <p class="text-lms-muted">
        <?= $keyword ? 'Tidak ada materi yang cocok dengan kata kunci "<strong style="color:white;">' . esc($keyword) . '</strong>".' : 'Guru belum mengunggah materi untuk kelas Anda.' ?>
    </p>
    <?php if ($keyword || $mapel_id || $tipe): ?>
    <a href="<?= base_url('materi-siswa') ?>" class="btn btn-outline-secondary mt-2">
        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
    </a>
    <?php endif; ?>
</div>

<?php else: ?>

<!-- Grid Materi -->
<div class="row g-4" id="materiGrid">
    <?php foreach ($materis as $m):
        $ext = strtolower(pathinfo($m['nama_asli_file'] ?? '', PATHINFO_EXTENSION));
        [$icon, $color, $typeLabel] = getIconInfo($ext, $m['tipe_konten']);
        $isLink    = $m['tipe_konten'] === 'link';
        $fileSize  = '';
        if (! $isLink && ! empty($m['file_materi'])) {
            $fp = FCPATH . 'uploads/materi/' . $m['file_materi'];
            if (file_exists($fp)) {
                $bytes = filesize($fp);
                $fileSize = $bytes < 1024*1024
                    ? round($bytes/1024, 1) . ' KB'
                    : round($bytes/1024/1024, 2) . ' MB';
            }
        }
        $tglUpload = date('d M Y', strtotime($m['created_at']));
    ?>
    <div class="col-12 col-sm-6 col-xl-4 materi-card-wrap">
        <div class="glass-panel h-100 materi-card"
             style="padding:0;overflow:hidden;display:flex;flex-direction:column;
                    transition:transform 0.25s, box-shadow 0.25s;"
             onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,0.3)'"
             onmouseout="this.style.transform='none';this.style.boxShadow=''">

            <!-- Header warna -->
            <div style="background:linear-gradient(135deg,<?= $color ?>18,<?= $color ?>06);
                         border-bottom:1px solid <?= $color ?>22;padding:1.25rem 1.25rem 1rem;">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <!-- Ikon tipe file -->
                    <div style="width:52px;height:52px;border-radius:12px;flex-shrink:0;
                                background:<?= $color ?>18;border:1px solid <?= $color ?>30;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="bi <?= $icon ?>" style="font-size:1.6rem;color:<?= $color ?>;"></i>
                    </div>

                    <!-- Badge tipe -->
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="badge" style="background:<?= $color ?>20;color:<?= $color ?>;border:1px solid <?= $color ?>30;font-size:0.7rem;font-weight:600;">
                            <?= $typeLabel ?>
                        </span>
                        <span class="badge" style="background:rgba(129,140,248,0.12);color:#818CF8;font-size:0.68rem;">
                            <?= esc($m['kode_mapel'] ?: $m['nama_mapel']) ?>
                        </span>
                    </div>
                </div>

                <!-- Judul -->
                <h6 style="color:white;margin:0.9rem 0 0.3rem;font-size:1rem;font-weight:600;
                            display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
                            overflow:hidden;line-height:1.4;">
                    <?= esc($m['judul_materi']) ?>
                </h6>
                <!-- Mapel -->
                <small style="color:<?= $color ?>;font-size:0.75rem;font-weight:500;">
                    <i class="bi bi-book me-1"></i><?= esc($m['nama_mapel']) ?>
                </small>
            </div>

            <!-- Body: Deskripsi -->
            <div style="padding:1rem 1.25rem;flex:1;">
                <?php if (! empty($m['deskripsi'])): ?>
                <p style="font-size:0.84rem;color:var(--lms-text-muted);line-height:1.6;margin:0;
                           display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                    <?= esc($m['deskripsi']) ?>
                </p>
                <?php else: ?>
                <p class="text-lms-muted" style="font-size:0.84rem;font-style:italic;margin:0;">
                    Tidak ada deskripsi.
                </p>
                <?php endif; ?>
            </div>

            <!-- Footer: Meta info + Aksi -->
            <div style="padding:0.9rem 1.25rem;border-top:1px solid rgba(255,255,255,0.07);">
                <!-- Meta -->
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-1">
                    <div style="font-size:0.73rem;color:var(--lms-text-muted);">
                        <i class="bi bi-person-circle me-1"></i>
                        <span style="color:white;font-weight:500;"><?= esc($m['nama_guru']) ?></span>
                    </div>
                    <div style="font-size:0.73rem;color:var(--lms-text-muted);">
                        <i class="bi bi-calendar3 me-1"></i><?= $tglUpload ?>
                    </div>
                </div>

                <!-- Info ukuran file -->
                <?php if ($fileSize): ?>
                <div style="font-size:0.72rem;color:var(--lms-text-muted);margin-bottom:0.75rem;">
                    <i class="bi bi-hdd me-1"></i>Ukuran: <strong style="color:white;"><?= $fileSize ?></strong>
                </div>
                <?php elseif ($isLink): ?>
                <div style="font-size:0.72rem;color:#38BDF8;margin-bottom:0.75rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <i class="bi bi-link-45deg me-1"></i>
                    <span><?= esc(parse_url($m['link_eksternal'] ?? '', PHP_URL_HOST) ?: 'Eksternal') ?></span>
                </div>
                <?php endif; ?>

                <!-- Tombol Aksi -->
                <?php if ($isLink): ?>
                <a href="<?= base_url('materi-siswa/download/' . $m['id']) ?>"
                   id="btn-materi-<?= $m['id'] ?>"
                   class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold"
                   style="background:linear-gradient(135deg,rgba(56,189,248,0.2),rgba(56,189,248,0.1));
                          border:1px solid rgba(56,189,248,0.35);color:#38BDF8;"
                   target="_blank" rel="noopener">
                    <i class="bi bi-box-arrow-up-right"></i> Buka Materi
                </a>
                <?php else: ?>
                <button type="button"
                        id="btn-materi-<?= $m['id'] ?>"
                        class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold btn-unduh"
                        data-href="<?= base_url('materi-siswa/download/' . $m['id']) ?>"
                        data-nama="<?= esc($m['nama_asli_file'] ?? $m['judul_materi']) ?>"
                        style="background:linear-gradient(135deg,rgba(129,140,248,0.2),rgba(129,140,248,0.1));
                               border:1px solid rgba(129,140,248,0.35);color:#818CF8;">
                    <i class="bi bi-cloud-download"></i> Unduh Materi
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Jumlah hasil -->
<div class="text-center mt-4">
    <small class="text-lms-muted">
        Menampilkan <strong style="color:white;"><?= count($materis) ?></strong> materi
        <?= $kelas ? 'untuk Kelas <strong style="color:#818CF8;">' . esc($kelas['nama_kelas']) . '</strong>' : '' ?>
    </small>
</div>

<?php endif; ?>
<?php endif; ?>

<!-- ══ Modal Preview / Konfirmasi Unduh ══ -->
<div class="modal fade" id="modalUnduh" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:1px solid rgba(129,140,248,0.25);">
            <div class="modal-body text-center py-4">
                <div style="width:60px;height:60px;border-radius:50%;background:rgba(129,140,248,0.15);
                             display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="bi bi-cloud-download" style="font-size:1.6rem;color:#818CF8;"></i>
                </div>
                <h6 style="color:white;font-weight:600;">Unduh Materi</h6>
                <p id="modalNamaFile" class="text-lms-muted" style="font-size:0.85rem;"></p>
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <a id="modalBtnUnduh" href="#"
                       class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-dismiss="modal">
                        <i class="bi bi-download"></i> Unduh
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
/* ═══════════════════════════════════════════════════════
   Live Search — filter kartu secara real-time (client-side)
   Berjalan setelah data diload, sebelum submit ke server
   ═══════════════════════════════════════════════════════ */
const searchInput = document.getElementById('searchInput');
const cards       = document.querySelectorAll('.materi-card-wrap');

if (searchInput && cards.length > 0) {
    let debounceTimer;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);

        // Animasi clear sebelum filter
        const q = this.value.toLowerCase().trim();

        // Debounce 200ms — jika tidak ketik lagi, filter langsung
        debounceTimer = setTimeout(() => {
            let visibleCount = 0;

            cards.forEach(card => {
                const teks = card.querySelector('.materi-card').innerText.toLowerCase();
                const cocok = teks.includes(q);
                card.style.display     = cocok ? '' : 'none';
                card.style.opacity     = cocok ? '1' : '0';
                if (cocok) visibleCount++;
            });

            // Update counter
            const counter = document.querySelector('.text-center.mt-4 small');
            if (counter) {
                counter.innerHTML = `Menampilkan <strong style="color:white;">${visibleCount}</strong> materi`;
            }
        }, 200);
    });
}

/* ═══════════════════════════════════════════════════════
   Modal Konfirmasi Unduh
   ═══════════════════════════════════════════════════════ */
document.querySelectorAll('.btn-unduh').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const href = this.dataset.href;
        const nama = this.dataset.nama;

        document.getElementById('modalNamaFile').textContent = nama;
        document.getElementById('modalBtnUnduh').href        = href;

        new bootstrap.Modal(document.getElementById('modalUnduh')).show();
    });
});

/* ═══════════════════════════════════════════════════════
   Animasi entri kartu (staggered fade-in)
   ═══════════════════════════════════════════════════════ */
document.querySelectorAll('.materi-card-wrap').forEach(function (card, idx) {
    card.style.opacity   = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = `opacity 0.4s ease ${idx * 0.06}s, transform 0.4s ease ${idx * 0.06}s`;

    setTimeout(() => {
        card.style.opacity   = '1';
        card.style.transform = 'translateY(0)';
    }, 50 + idx * 60);
});
</script>
<?= $this->endSection() ?>
