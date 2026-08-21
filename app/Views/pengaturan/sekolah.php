<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$logo     = $pengaturan['logo'] ?? null;
$logoUrl  = $logo ? base_url('uploads/logo/' . $logo) : null;
$akrList  = ['A+', 'A', 'B', 'C', 'Belum Terakreditasi'];
$provList = ['Aceh','Sumatera Utara','Sumatera Barat','Riau','Kepulauan Riau','Jambi','Bengkulu','Sumatera Selatan','Kepulauan Bangka Belitung','Lampung','Banten','DKI Jakarta','Jawa Barat','Jawa Tengah','DI Yogyakarta','Jawa Timur','Bali','Nusa Tenggara Barat','Nusa Tenggara Timur','Kalimantan Barat','Kalimantan Tengah','Kalimantan Selatan','Kalimantan Timur','Kalimantan Utara','Sulawesi Utara','Gorontalo','Sulawesi Tengah','Sulawesi Barat','Sulawesi Selatan','Sulawesi Tenggara','Maluku','Maluku Utara','Papua Barat','Papua Barat Daya','Papua Tengah','Papua Pegunungan','Papua Selatan','Papua'];
?>

<form action="<?= base_url('pengaturan/sekolah/update') ?>" method="post" enctype="multipart/form-data" id="formPengaturan">
<?= csrf_field() ?>

<div class="row g-4">

    <!-- ═══════════════════════════════════
         KOLOM KIRI — Logo + Identitas
         ═══════════════════════════════════ -->
    <div class="col-lg-4">

        <!-- Kartu Logo -->
        <div class="glass-panel card mb-4">
            <h5 class="card-title mb-4">
                <i class="bi bi-image me-2 text-accent"></i>Logo Sekolah
            </h5>

            <!-- Preview Logo -->
            <div class="text-center mb-4">
                <div id="logoPreviewWrap" style="
                    width:160px; height:160px; border-radius:16px; margin:0 auto;
                    background:rgba(255,255,255,0.04);
                    border:2px dashed rgba(255,255,255,0.15);
                    display:flex; align-items:center; justify-content:center;
                    overflow:hidden; position:relative; cursor:pointer;"
                    onclick="document.getElementById('logoInput').click()"
                    title="Klik untuk ganti logo">
                    <?php if ($logoUrl): ?>
                    <img src="<?= $logoUrl ?>" id="logoPreview" alt="Logo Sekolah"
                         style="width:100%;height:100%;object-fit:contain;">
                    <?php else: ?>
                    <div id="logoPlaceholder" style="text-align:center;">
                        <i class="bi bi-building" style="font-size:2.5rem;color:var(--lms-text-muted);"></i>
                        <p style="font-size:0.72rem;color:var(--lms-text-muted);margin-top:6px;">Klik untuk upload</p>
                    </div>
                    <img src="" id="logoPreview" alt="" style="display:none;width:100%;height:100%;object-fit:contain;">
                    <?php endif; ?>
                    <!-- Overlay hover -->
                    <div style="position:absolute;inset:0;background:rgba(79,70,229,0.5);
                                display:flex;align-items:center;justify-content:center;
                                opacity:0;transition:opacity 0.2s;border-radius:14px;"
                         onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                        <i class="bi bi-camera-fill" style="font-size:1.5rem;color:white;"></i>
                    </div>
                </div>

                <input type="file" name="logo" id="logoInput" accept=".jpg,.jpeg,.png,.gif,.webp,.svg"
                       style="display:none;" onchange="previewLogo(this)">

                <div class="mt-3 d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="document.getElementById('logoInput').click()">
                        <i class="bi bi-upload me-1"></i>Pilih Logo
                    </button>
                    <?php if ($logo): ?>
                    <a href="<?= base_url('pengaturan/sekolah/hapus-logo') ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Yakin hapus logo?')">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </a>
                    <?php endif; ?>
                </div>
                <p class="text-lms-muted mt-2" style="font-size:0.72rem;">
                    JPG, PNG, WebP, SVG · Maks. 2 MB
                </p>
            </div>
        </div>

        <!-- Identitas Singkat -->
        <div class="glass-panel card mb-4">
            <h5 class="card-title mb-3">
                <i class="bi bi-award me-2 text-accent"></i>Identitas Singkat
            </h5>
            <div class="mb-3">
                <label class="form-label">NSS <small class="text-lms-muted">(Nomor Statistik Sekolah)</small></label>
                <input type="text" name="nss" class="form-control"
                       value="<?= esc($pengaturan['nss'] ?? '') ?>" placeholder="cth: 342040101001">
            </div>
            <div class="mb-3">
                <label class="form-label">NPSN <small class="text-lms-muted">(Nomor Pokok Sekolah)</small></label>
                <input type="text" name="npsn" class="form-control"
                       value="<?= esc($pengaturan['npsn'] ?? '') ?>" placeholder="cth: 20523456">
            </div>
            <div class="mb-3">
                <label class="form-label">Akreditasi</label>
                <select name="akreditasi" class="form-select">
                    <?php foreach ($akrList as $a): ?>
                    <option value="<?= $a ?>" <?= ($pengaturan['akreditasi'] ?? '') === $a ? 'selected' : '' ?>>
                        <?= $a ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-0">
                <label class="form-label">Tahun Berdiri</label>
                <input type="number" name="tahun_berdiri" class="form-control"
                       value="<?= esc($pengaturan['tahun_berdiri'] ?? '') ?>"
                       placeholder="cth: 1995" min="1900" max="<?= date('Y') ?>">
            </div>
        </div>

        <!-- Kepala Sekolah -->
        <div class="glass-panel card">
            <h5 class="card-title mb-3">
                <i class="bi bi-person-badge me-2 text-accent"></i>Kepala Sekolah
            </h5>
            <div class="mb-3">
                <label class="form-label">Nama Kepala Sekolah</label>
                <input type="text" name="nama_kepala_sekolah" class="form-control"
                       value="<?= esc($pengaturan['nama_kepala_sekolah'] ?? '') ?>"
                       placeholder="Nama lengkap beserta gelar">
            </div>
            <div class="mb-0">
                <label class="form-label">NIP Kepala Sekolah</label>
                <input type="text" name="nip_kepala_sekolah" class="form-control"
                       value="<?= esc($pengaturan['nip_kepala_sekolah'] ?? '') ?>"
                       placeholder="cth: 197001012000121001">
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════
         KOLOM KANAN — Detail Sekolah
         ═══════════════════════════════════ -->
    <div class="col-lg-8">

        <!-- Nama & Singkatan -->
        <div class="glass-panel card mb-4">
            <h5 class="card-title mb-4">
                <i class="bi bi-buildings me-2 text-accent"></i>Nama Sekolah
            </h5>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Nama Sekolah Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_sekolah" class="form-control form-control-lg"
                           value="<?= esc($pengaturan['nama_sekolah'] ?? '') ?>" required
                           placeholder="cth: SMK Negeri 1 Kota Anda"
                           style="font-weight:600;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Singkatan / Nama Pendek</label>
                    <input type="text" name="singkatan" class="form-control"
                           value="<?= esc($pengaturan['singkatan'] ?? '') ?>"
                           placeholder="cth: SMKN 1 Kota Anda">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Website Sekolah</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);color:var(--lms-text-muted);">
                            <i class="bi bi-globe2"></i>
                        </span>
                        <input type="url" name="website" class="form-control"
                               value="<?= esc($pengaturan['website'] ?? '') ?>"
                               placeholder="https://smk.sch.id">
                    </div>
                </div>
            </div>
        </div>

        <!-- Alamat -->
        <div class="glass-panel card mb-4">
            <h5 class="card-title mb-4">
                <i class="bi bi-geo-alt me-2 text-accent"></i>Alamat & Lokasi
            </h5>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Alamat Jalan</label>
                    <textarea name="alamat" class="form-control" rows="2"
                              placeholder="Nama jalan, nomor, RT/RW"><?= esc($pengaturan['alamat'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" name="kecamatan" class="form-control"
                           value="<?= esc($pengaturan['kecamatan'] ?? '') ?>" placeholder="Kecamatan">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota / Kabupaten</label>
                    <input type="text" name="kota" class="form-control"
                           value="<?= esc($pengaturan['kota'] ?? '') ?>" placeholder="Kota / Kabupaten">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Provinsi</label>
                    <select name="provinsi" class="form-select">
                        <option value="">-- Pilih Provinsi --</option>
                        <?php foreach ($provList as $prov): ?>
                        <option value="<?= $prov ?>" <?= ($pengaturan['provinsi'] ?? '') === $prov ? 'selected' : '' ?>>
                            <?= $prov ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" name="kode_pos" class="form-control"
                           value="<?= esc($pengaturan['kode_pos'] ?? '') ?>"
                           placeholder="00000" maxlength="10">
                </div>
            </div>
        </div>

        <!-- Kontak -->
        <div class="glass-panel card mb-4">
            <h5 class="card-title mb-4">
                <i class="bi bi-telephone me-2 text-accent"></i>Kontak
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">No. Telepon / Fax</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);color:var(--lms-text-muted);">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <input type="text" name="no_telp" class="form-control"
                               value="<?= esc($pengaturan['no_telp'] ?? '') ?>"
                               placeholder="(021) 000-0000">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Sekolah</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.1);color:var(--lms-text-muted);">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control"
                               value="<?= esc($pengaturan['email'] ?? '') ?>"
                               placeholder="info@smk.sch.id">
                    </div>
                </div>
            </div>
        </div>

        <!-- Visi & Misi -->
        <div class="glass-panel card mb-4">
            <h5 class="card-title mb-4">
                <i class="bi bi-eye me-2 text-accent"></i>Visi & Misi
            </h5>
            <div class="mb-3">
                <label class="form-label">Visi Sekolah</label>
                <textarea name="visi" class="form-control" rows="3"
                          placeholder="Tuliskan visi sekolah..."><?= esc($pengaturan['visi'] ?? '') ?></textarea>
            </div>
            <div class="mb-0">
                <label class="form-label">
                    Misi Sekolah
                    <small class="text-lms-muted">— Pisahkan setiap poin misi dengan baris baru (Enter)</small>
                </label>
                <textarea name="misi" class="form-control" rows="5"
                          placeholder="Poin misi pertama&#10;Poin misi kedua&#10;dst..."><?= esc(str_replace('|', "\n", $pengaturan['misi'] ?? '')) ?></textarea>
                <small class="text-lms-muted">Setiap baris akan ditampilkan sebagai satu poin misi.</small>
            </div>
        </div>

        <!-- Preview Identitas -->
        <div class="glass-panel card mb-4" style="background:rgba(79,70,229,0.05);border-color:rgba(79,70,229,0.2);">
            <h5 class="card-title mb-3">
                <i class="bi bi-eye me-2" style="color:#818CF8;"></i>Preview Kop Sekolah
            </h5>
            <div class="d-flex align-items-center gap-4 p-3"
                 style="background:white;border-radius:10px;color:#1e293b;">
                <!-- Preview Logo -->
                <div style="width:70px;height:70px;flex-shrink:0;border:1px solid #e2e8f0;border-radius:8px;
                             overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f8fafc;">
                    <?php if ($logoUrl): ?>
                    <img id="prevKopLogo" src="<?= $logoUrl ?>" alt="Logo"
                         style="width:100%;height:100%;object-fit:contain;">
                    <?php else: ?>
                    <img id="prevKopLogo" src="" alt=""
                         style="width:100%;height:100%;object-fit:contain;display:none;">
                    <i class="bi bi-building" id="prevKopPlaceholder" style="font-size:1.8rem;color:#94a3b8;"></i>
                    <?php endif; ?>
                </div>
                <!-- Preview Teks -->
                <div style="border-left:3px solid #4F46E5;padding-left:16px;flex:1;">
                    <div style="font-size:0.75rem;font-weight:700;color:#4F46E5;letter-spacing:0.06em;">
                        PEMERINTAH DAERAH
                    </div>
                    <div id="prevNamaSekolah" style="font-size:1.05rem;font-weight:800;color:#0f172a;line-height:1.2;">
                        <?= esc($pengaturan['nama_sekolah'] ?? 'Nama Sekolah') ?>
                    </div>
                    <div id="prevAlamatKop" style="font-size:0.72rem;color:#64748b;margin-top:2px;">
                        <?= esc($pengaturan['alamat'] ?? '') ?><?= !empty($pengaturan['kota']) ? ', ' . esc($pengaturan['kota']) : '' ?>
                        <?php if (!empty($pengaturan['no_telp'])): ?>
                        &nbsp;· Telp. <?= esc($pengaturan['no_telp']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="d-flex justify-content-end gap-3">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary px-4">
                <i class="bi bi-x-lg me-1"></i>Batal
            </a>
            <button type="submit" class="btn btn-primary px-5 fw-semibold" id="btnSimpan">
                <i class="bi bi-save2 me-2"></i>Simpan Pengaturan
            </button>
        </div>
    </div>
</div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
/* ── Preview logo sebelum upload ────────────────────── */
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const file   = input.files[0];
    const reader = new FileReader();

    reader.onload = function (e) {
        const img   = document.getElementById('logoPreview');
        const ph    = document.getElementById('logoPlaceholder');
        img.src     = e.target.result;
        img.style.display = 'block';
        if (ph) ph.style.display = 'none';

        // Update preview kop
        const kopLogo = document.getElementById('prevKopLogo');
        const kopPh   = document.getElementById('prevKopPlaceholder');
        if (kopLogo) { kopLogo.src = e.target.result; kopLogo.style.display = 'block'; }
        if (kopPh)   kopPh.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

/* ── Live preview nama sekolah di kop ───────────────── */
document.querySelector('[name="nama_sekolah"]').addEventListener('input', function () {
    const el = document.getElementById('prevNamaSekolah');
    if (el) el.textContent = this.value || 'Nama Sekolah';
});

/* ── Konversi misi baris → pipe sebelum submit ──────── */
document.getElementById('formPengaturan').addEventListener('submit', function () {
    const misiEl = document.querySelector('[name="misi"]');
    if (misiEl) {
        misiEl.value = misiEl.value
            .split('\n')
            .map(l => l.trim())
            .filter(l => l !== '')
            .join('|');
    }
    // Loading state
    const btn = document.getElementById('btnSimpan');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    btn.disabled = true;
});
</script>
<?= $this->endSection() ?>
