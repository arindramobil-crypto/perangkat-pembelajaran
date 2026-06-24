<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $uid = $ulangan['id']; ?>

<style>
/* ── Soal Page Styles ────────────────────────────────── */
.soal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.75rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--lms-border);
    flex-wrap: wrap;
    gap: 1rem;
}
.soal-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 992px) {
    .soal-layout { grid-template-columns: 1fr; }
}

/* Soal item card */
.soal-item {
    background: rgba(255,255,255,0.025);
    border: 1px solid var(--lms-border);
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    transition: border-color 0.2s;
    margin-bottom: 0.85rem;
    position: relative;
}
.soal-item:hover { border-color: rgba(129,140,248,0.3); }
.soal-no {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #818CF8;
    margin-bottom: 0.4rem;
}
.soal-tipe-badge {
    display: inline-block;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 100px;
    margin-left: 8px;
    vertical-align: middle;
}
.badge-pg       { background: rgba(79,70,229,0.2); color:#818CF8; border:1px solid rgba(79,70,229,0.3); }
.badge-pgk      { background: rgba(139,92,246,0.2);color:#A78BFA; border:1px solid rgba(139,92,246,0.3); }
.badge-bs       { background: rgba(34,197,94,0.15); color:#22C55E; border:1px solid rgba(34,197,94,0.3); }
.badge-jodoh    { background: rgba(245,158,11,0.15);color:#F59E0B; border:1px solid rgba(245,158,11,0.3); }
.badge-uraian   { background: rgba(99,102,241,0.15);color:#A5B4FC; border:1px solid rgba(99,102,241,0.3); }

.soal-pertanyaan { color: white; margin-bottom: 0.75rem; line-height: 1.65; }
.soal-opsi { color: var(--lms-text-muted); font-size: 0.82rem; }
.soal-opsi li { padding: 3px 0; }
.soal-kunci {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #22C55E;
    background: rgba(34,197,94,0.1);
    border: 1px solid rgba(34,197,94,0.25);
    border-radius: 6px;
    padding: 4px 12px;
}
.soal-actions {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    gap: 6px;
}
.soal-empty {
    padding: 3rem;
    border: 2px dashed rgba(255,255,255,0.1);
    border-radius: 12px;
    text-align: center;
    color: var(--lms-text-muted);
}

/* Panel Kanan */
.side-panel {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: sticky;
    top: calc(var(--lms-navbar-h) + 1.5rem);
}
.panel-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--lms-border);
    border-radius: 12px;
    padding: 1.25rem;
}
.panel-card h5 {
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--lms-text-muted);
    margin-bottom: 1rem;
    padding-bottom: 0.6rem;
    border-bottom: 1px solid var(--lms-border);
}

/* Ekspor / Impor */
.ei-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    text-decoration: none;
    color: var(--lms-text-muted);
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 6px;
    font-family: inherit;
}
.ei-btn:hover { background: rgba(255,255,255,0.08); color: white; border-color: rgba(255,255,255,0.15); }
.ei-btn .ei-icon { font-size: 1.05rem; width: 20px; flex-shrink: 0; }
.ei-btn.primary { background: rgba(79,70,229,0.15); color: #818CF8; border-color: rgba(79,70,229,0.3); }
.ei-btn.primary:hover { background: rgba(79,70,229,0.25); }
.ei-btn.success { background: rgba(34,197,94,0.12); color: #22C55E; border-color: rgba(34,197,94,0.3); }
.ei-btn.success:hover { background: rgba(34,197,94,0.2); }
.ei-btn.warning { background: rgba(245,158,11,0.12); color: #F59E0B; border-color: rgba(245,158,11,0.3); }
.ei-btn.warning:hover { background: rgba(245,158,11,0.2); }

/* Upload drop area */
.drop-zone {
    border: 2px dashed rgba(79,70,229,0.35);
    border-radius: 10px;
    padding: 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: rgba(79,70,229,0.04);
    position: relative;
}
.drop-zone:hover, .drop-zone.dragover {
    border-color: rgba(129,140,248,0.6);
    background: rgba(79,70,229,0.1);
}
.drop-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}
.drop-zone-icon { font-size: 1.75rem; color: #818CF8; margin-bottom: 6px; }
.drop-zone p { font-size: 0.75rem; color: var(--lms-text-muted); margin: 0; }
.drop-zone .file-name { font-size: 0.78rem; color: #22C55E; margin-top: 6px; font-weight: 600; display: none; }
</style>

<!-- Header -->
<div class="soal-header">
    <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <a href="<?= base_url('ulangan') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <span class="badge" style="background:rgba(79,70,229,0.2);color:#818CF8;border:1px solid rgba(79,70,229,0.3);font-size:0.72rem;">
                <?= esc($ulangan['tipe']) ?>
            </span>
        </div>
        <h2 class="lms-page-title"><?= esc($ulangan['judul']) ?></h2>
        <p style="color:var(--lms-text-muted);font-size:0.85rem;margin:0;">
            <i class="bi bi-clock me-1"></i><?= esc($ulangan['durasi']) ?> Menit &nbsp;·&nbsp;
            <i class="bi bi-award me-1"></i>KKM <?= esc($ulangan['kkm']) ?> &nbsp;·&nbsp;
            <i class="bi bi-list-ol me-1"></i><?= count($soalList) ?> Soal
        </p>
    </div>
    <button class="btn btn-primary" onclick="showModal()">
        <i class="bi bi-plus-lg me-1"></i>Tambah Soal
    </button>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i>
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-circle-fill"></i>
    <?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Layout Utama -->
<div class="soal-layout">

    <!-- ══ KIRI: Daftar Soal ══ -->
    <div>
        <?php if (empty($soalList)): ?>
        <div class="soal-empty">
            <i class="bi bi-journal-x" style="font-size:2.5rem;display:block;margin-bottom:0.75rem;opacity:0.4;"></i>
            <p style="font-weight:600;color:white;margin-bottom:6px;">Belum ada soal</p>
            <p style="font-size:0.83rem;margin-bottom:1rem;">Tambahkan soal secara manual atau impor dari file JSON / CSV.</p>
            <button class="btn btn-primary btn-sm" onclick="showModal()">
                <i class="bi bi-plus-lg me-1"></i>Tambah Soal Pertama
            </button>
        </div>
        <?php else: ?>
        <div id="daftarSoal">
            <?php $no = 1; foreach ($soalList as $s): ?>
            <?php
            $tipeBadge = match($s['tipe_soal']) {
                'PG'           => ['badge-pg',    'Pilihan Ganda'],
                'PG Kompleks'  => ['badge-pgk',   'PG Kompleks'],
                'Benar Salah'  => ['badge-bs',    'Benar/Salah'],
                'Menjodohkan'  => ['badge-jodoh', 'Menjodohkan'],
                'Uraian'       => ['badge-uraian','Uraian'],
                default        => ['badge-pg',    $s['tipe_soal']],
            };
            ?>
            <div class="soal-item">
                <div class="soal-actions">
                    <a href="<?= base_url('ulangan/delete_soal/'.$s['id'].'/'.$uid) ?>"
                       class="btn btn-sm btn-outline-danger"
                       style="font-size:0.72rem;padding:3px 10px;"
                       onclick="return confirm('Hapus soal No.<?= $no ?>?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>

                <div class="soal-no">
                    Soal <?= $no++ ?>
                    <span class="soal-tipe-badge <?= $tipeBadge[0] ?>"><?= $tipeBadge[1] ?></span>
                    <span class="soal-tipe-badge" style="background:rgba(255,255,255,0.06);color:var(--lms-text-muted);border:1px solid rgba(255,255,255,0.1);">
                        Bobot <?= esc($s['bobot']) ?>
                    </span>
                </div>

                <p class="soal-pertanyaan"><?= nl2br(esc($s['pertanyaan'])) ?></p>

                <?php if ($s['tipe_soal'] === 'PG'): ?>
                <ul class="soal-opsi" style="list-style:none;padding:0;">
                    <?php foreach (['A','B','C','D','E'] as $opt): ?>
                    <?php if (!empty($s['opsi_'.strtolower($opt)])): ?>
                    <li style="<?= $s['kunci_jawaban'] === $opt ? 'color:#22C55E;font-weight:600;' : '' ?>">
                        <span style="opacity:0.5;"><?= $opt ?>.</span> <?= esc($s['opsi_'.strtolower($opt)]) ?>
                        <?php if ($s['kunci_jawaban'] === $opt): ?>
                        <i class="bi bi-check-circle-fill" style="color:#22C55E;margin-left:4px;"></i>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <?php elseif ($s['tipe_soal'] === 'PG Kompleks'): ?>
                <ul class="soal-opsi" style="list-style:none;padding:0;">
                    <?php foreach (['A','B','C','D','E'] as $opt): ?>
                    <?php if (!empty($s['opsi_'.strtolower($opt)])): ?>
                    <?php $kunci = json_decode($s['kunci_jawaban'], true) ?? []; ?>
                    <li style="<?= in_array($opt, (array)$kunci) ? 'color:#22C55E;font-weight:600;' : '' ?>">
                        <span style="opacity:0.5;"><?= $opt ?>.</span> <?= esc($s['opsi_'.strtolower($opt)]) ?>
                        <?php if (in_array($opt, (array)$kunci)): ?>
                        <i class="bi bi-check-circle-fill" style="color:#22C55E;margin-left:4px;"></i>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <?php elseif ($s['tipe_soal'] === 'Menjodohkan'): ?>
                <?php
                $kiri   = array_filter(['A'=>$s['opsi_a'],'B'=>$s['opsi_b'],'C'=>$s['opsi_c'],'D'=>$s['opsi_d'],'E'=>$s['opsi_e']]);
                $kanan  = $s['opsi_tambahan'] ? explode('|', $s['opsi_tambahan']) : [];
                ?>
                <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:8px;font-size:0.8rem;margin-top:4px;">
                    <div>
                        <?php foreach ($kiri as $k => $v): ?>
                        <div style="padding:4px 0;color:var(--lms-text-muted);"><?= $k ?>. <?= esc($v) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div style="display:flex;flex-direction:column;justify-content:space-around;padding:4px 0;">
                        <?php foreach ($kiri as $k => $v): ?>
                        <i class="bi bi-arrow-right" style="color:#818CF8;"></i>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <?php foreach ($kanan as $k => $v): ?>
                        <div style="padding:4px 0;color:#22C55E;"><?= esc($v) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php else: ?>
                <div class="soal-kunci">
                    <i class="bi bi-key-fill"></i>
                    <?= esc(strlen($s['kunci_jawaban']) > 80 ? substr($s['kunci_jawaban'],0,80).'...' : $s['kunci_jawaban']) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ══ KANAN: Panel Samping ══ -->
    <div class="side-panel">

        <!-- Ekspor Soal -->
        <div class="panel-card">
            <h5><i class="bi bi-box-arrow-up me-1"></i>Ekspor Soal</h5>
            <p style="font-size:0.78rem;color:var(--lms-text-muted);margin-bottom:0.85rem;">
                Download semua <strong style="color:white;"><?= count($soalList) ?></strong> soal sebagai file backup atau untuk dipindahkan ke ujian lain.
            </p>
            <a href="<?= base_url('soal/ekspor/'.$uid.'/json') ?>"
               class="ei-btn primary <?= empty($soalList) ? 'disabled' : '' ?>">
                <i class="bi bi-filetype-json ei-icon"></i>
                <div>
                    <div style="color:white;">Ekspor JSON</div>
                    <small style="font-weight:400;font-size:0.7rem;">Backup lengkap (semua tipe soal)</small>
                </div>
            </a>
            <a href="<?= base_url('soal/ekspor/'.$uid.'/excel') ?>"
               class="ei-btn success <?= empty($soalList) ? 'disabled' : '' ?>">
                <i class="bi bi-file-earmark-spreadsheet ei-icon"></i>
                <div>
                    <div style="color:white;">Ekspor Excel / XLS</div>
                    <small style="font-weight:400;font-size:0.7rem;">Buka di Excel atau LibreOffice</small>
                </div>
            </a>
        </div>

        <!-- Impor Soal -->
        <div class="panel-card">
            <h5><i class="bi bi-box-arrow-in-down me-1"></i>Impor Soal</h5>

            <!-- Unduh Template -->
            <a href="<?= base_url('soal/template-csv/'.$uid) ?>" class="ei-btn warning mb-3">
                <i class="bi bi-download ei-icon"></i>
                <div>
                    <div style="color:white;">Unduh Template CSV</div>
                    <small style="font-weight:400;font-size:0.7rem;">Berisi contoh semua 5 tipe soal</small>
                </div>
            </a>

            <form action="<?= base_url('soal/impor/'.$uid) ?>" method="post" enctype="multipart/form-data" id="formImpor">
                <?= csrf_field() ?>

                <!-- Drop Zone -->
                <div class="drop-zone" id="dropZone">
                    <input type="file" name="file_soal" id="fileSoal"
                           accept=".json,.csv,.xls"
                           onchange="onFileSelect(this)">
                    <div id="dropContent">
                        <div class="drop-zone-icon"><i class="bi bi-cloud-upload"></i></div>
                        <p style="font-weight:600;color:white;margin-bottom:4px;">Klik atau seret file ke sini</p>
                        <p>JSON, CSV, atau XLS · Maks. 5MB</p>
                    </div>
                    <div class="file-name" id="selectedFileName">
                        <i class="bi bi-file-earmark-check me-1"></i>
                        <span id="fileNameText"></span>
                    </div>
                </div>

                <!-- Mode Impor -->
                <div style="margin-top:0.85rem;margin-bottom:0.85rem;">
                    <label style="font-size:0.78rem;color:var(--lms-text-muted);display:block;margin-bottom:6px;font-weight:600;">Mode Import:</label>
                    <div style="display:flex;gap:1rem;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:0.8rem;cursor:pointer;">
                            <input type="radio" name="mode_impor" value="tambah" checked> Tambah ke soal yang ada
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:0.8rem;cursor:pointer;color:#EF4444;">
                            <input type="radio" name="mode_impor" value="ganti"> Ganti semua soal
                        </label>
                    </div>
                    <div id="warnGanti" style="display:none;margin-top:6px;padding:7px 10px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);border-radius:7px;font-size:0.72rem;color:#FCA5A5;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Perhatian:</strong> Mode ini akan MENGHAPUS semua <?= count($soalList) ?> soal yang ada sebelum mengimpor!
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" id="btnImpor" disabled>
                    <i class="bi bi-upload me-1"></i>Mulai Import
                </button>
            </form>
        </div>

        <!-- Distribusi Kelas -->
        <div class="panel-card">
            <h5><i class="bi bi-diagram-3 me-1"></i>Distribusi Kelas</h5>

            <?php if (empty($assigned_classes)): ?>
            <p style="font-size:0.78rem;color:var(--lms-danger);">
                <i class="bi bi-exclamation-circle me-1"></i>Belum dibagikan ke kelas mana pun.
            </p>
            <?php else: ?>
            <ul style="list-style:none;padding:0;margin-bottom:1rem;">
                <?php foreach ($assigned_classes as $ac): ?>
                <li style="display:flex;align-items:center;gap:8px;font-size:0.8rem;padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.06);">
                    <i class="bi bi-people" style="color:#818CF8;"></i>
                    <div>
                        <div style="color:white;font-weight:600;"><?= esc($ac['nama_kelas']) ?></div>
                        <div style="color:var(--lms-text-muted);font-size:0.71rem;"><?= esc($ac['tahun']) ?></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <form action="<?= base_url('ulangan/assign_kelas') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="ulangan_id" value="<?= $uid ?>">
                <div class="mb-2">
                    <select name="tahun_pelajaran_id" class="form-select form-select-sm">
                        <?php foreach ($tahunList as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['tahun']) ?> — <?= esc($t['semester']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <select name="kelas_id" class="form-select form-select-sm">
                        <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i>Tugaskan ke Kelas
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ══════════════ MODAL TAMBAH SOAL ══════════════ -->
<div id="soalModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:2000;overflow-y:auto;backdrop-filter:blur(4px);">
    <div style="max-width:700px;margin:2rem auto;padding:0 1rem;">
        <div class="glass-panel" style="border-radius:16px;padding:2rem;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h4 style="color:white;margin:0;"><i class="bi bi-pencil-square me-2" style="color:#818CF8;"></i>Tambah Soal Baru</h4>
                <button onclick="hideModal()" style="background:none;border:none;color:var(--lms-text-muted);font-size:1.4rem;cursor:pointer;padding:0;line-height:1;">&times;</button>
            </div>

            <form action="<?= base_url('ulangan/save_soal') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="ulangan_id" value="<?= $uid ?>">

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Tipe Soal</label>
                        <select name="tipe_soal" id="tipeSoal" class="form-select" onchange="toggleForm()" required>
                            <option value="PG">📝 Pilihan Ganda Biasa (A–E)</option>
                            <option value="PG Kompleks">📋 Pilihan Ganda Kompleks (lebih dari 1 jawaban)</option>
                            <option value="Benar Salah">✅ Benar / Salah</option>
                            <option value="Menjodohkan">🔗 Menjodohkan</option>
                            <option value="Uraian">✍️ Uraian / Essay</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bobot Skor</label>
                        <input type="number" name="bobot" class="form-control" value="10" min="1" max="100" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Pertanyaan / Soal</label>
                        <textarea name="pertanyaan" class="form-control" rows="4" required placeholder="Tulis pertanyaan di sini..."></textarea>
                    </div>
                </div>

                <!-- Area PG / PG Kompleks -->
                <div id="areaPG" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--lms-border);">
                    <p style="font-size:0.8rem;color:var(--lms-text-muted);margin-bottom:0.75rem;">Isi pilihan jawaban. Untuk PG Kompleks, bisa pilih lebih dari 1 kunci.</p>
                    <?php foreach (['A','B','C','D','E'] as $opt): ?>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                        <input type="checkbox" id="kunci_<?= $opt ?>" name="kunci_jawaban[]" value="<?= $opt ?>"
                               style="width:16px;height:16px;flex-shrink:0;cursor:pointer;" title="Tandai sebagai kunci">
                        <label for="kunci_<?= $opt ?>" style="font-weight:700;color:#818CF8;width:20px;flex-shrink:0;cursor:pointer;"><?= $opt ?></label>
                        <input type="text" name="opsi_<?= strtolower($opt) ?>" class="form-control" placeholder="Opsi <?= $opt ?>">
                    </div>
                    <?php endforeach; ?>
                    <p style="font-size:0.73rem;color:var(--lms-text-muted);margin-top:4px;">
                        <i class="bi bi-info-circle me-1"></i>Centang kotak di sebelah kiri untuk menandai kunci jawaban.
                    </p>
                </div>

                <!-- Area Benar/Salah -->
                <div id="areaBernarSalah" style="display:none;margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--lms-border);">
                    <label class="form-label">Kunci Jawaban</label>
                    <div style="display:flex;gap:1rem;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.9rem;">
                            <input type="radio" name="kunci_bs" value="Benar"> <span style="color:#22C55E;font-weight:600;">✅ Benar</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.9rem;">
                            <input type="radio" name="kunci_bs" value="Salah"> <span style="color:#EF4444;font-weight:600;">❌ Salah</span>
                        </label>
                    </div>
                </div>

                <!-- Area Menjodohkan -->
                <div id="areaMenjodohkan" style="display:none;margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--lms-border);">
                    <p style="font-size:0.8rem;color:var(--lms-text-muted);margin-bottom:0.75rem;">Isi kolom kiri (Opsi A–E) dan kolom kanan yang sesuai.</p>
                    <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:8px;align-items:center;">
                        <div style="font-size:0.78rem;color:var(--lms-text-muted);font-weight:700;">Kolom Kiri</div>
                        <div></div>
                        <div style="font-size:0.78rem;color:var(--lms-text-muted);font-weight:700;">Kolom Kanan (Pasangan)</div>
                        <?php foreach (['A','B','C','D','E'] as $opt): ?>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:700;color:#818CF8;"><?= $opt ?>.</span>
                            <input type="text" name="opsi_<?= strtolower($opt) ?>" class="form-control form-control-sm" placeholder="Kolom kiri <?= $opt ?>">
                        </div>
                        <i class="bi bi-arrow-right text-center" style="color:#818CF8;"></i>
                        <input type="text" name="pasangan_<?= $opt ?>" class="form-control form-control-sm" placeholder="Pasangan <?= $opt ?>">
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Area Uraian -->
                <div id="areaUraian" style="display:none;margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--lms-border);">
                    <label class="form-label">Kunci / Pedoman Penilaian (Rubrik) <small class="text-muted">— opsional, hanya untuk guru</small></label>
                    <textarea name="kunci_uraian" class="form-control" rows="3" placeholder="Tuliskan poin-poin jawaban yang diharapkan..."></textarea>
                </div>

                <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--lms-border);">
                    <button type="button" class="btn btn-outline-secondary" onclick="hideModal()">Batal</button>
                    <button type="submit" class="btn btn-primary px-4" id="btnSimpanSoal">
                        <i class="bi bi-save2 me-1"></i>Simpan Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
/* ── Modal Tambah Soal ──────────────────────────────── */
function showModal() {
    document.getElementById('soalModal').style.display = 'block';
    toggleForm();
}
function hideModal() {
    document.getElementById('soalModal').style.display = 'none';
}
document.getElementById('soalModal').addEventListener('click', function(e) {
    if (e.target === this) hideModal();
});

/* ── Toggle area berdasarkan tipe soal ─────────────── */
function toggleForm() {
    const tipe = document.getElementById('tipeSoal').value;
    document.getElementById('areaPG').style.display          = 'none';
    document.getElementById('areaBernarSalah').style.display = 'none';
    document.getElementById('areaMenjodohkan').style.display = 'none';
    document.getElementById('areaUraian').style.display      = 'none';

    if (tipe === 'PG' || tipe === 'PG Kompleks') {
        document.getElementById('areaPG').style.display = 'block';
        // PG biasa: hanya 1 kunci (radio mode), PG kompleks: multiple
        const checkboxes = document.querySelectorAll('#areaPG input[type="checkbox"]');
        if (tipe === 'PG') {
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked) checkboxes.forEach(c => { if(c!==this) c.checked=false; });
                });
            });
        }
    } else if (tipe === 'Benar Salah') {
        document.getElementById('areaBernarSalah').style.display = 'block';
    } else if (tipe === 'Menjodohkan') {
        document.getElementById('areaMenjodohkan').style.display = 'block';
    } else if (tipe === 'Uraian') {
        document.getElementById('areaUraian').style.display = 'block';
    }
}

/* ── Konversi data form sebelum submit ─────────────── */
document.querySelector('#soalModal form').addEventListener('submit', function(e) {
    const tipe = document.getElementById('tipeSoal').value;

    if (tipe === 'Benar Salah') {
        // ambil nilai radio benar/salah, masukkan ke hidden input
        const val = document.querySelector('input[name="kunci_bs"]:checked');
        if (!val) { e.preventDefault(); alert('Pilih kunci Benar atau Salah!'); return; }
        addHidden(this, 'kunci_jawaban', val.value);

    } else if (tipe === 'PG Kompleks') {
        // Kunci jawaban array → JSON
        const checked = [...document.querySelectorAll('#areaPG input[type="checkbox"]:checked')].map(c => c.value);
        if (checked.length === 0) { e.preventDefault(); alert('Pilih minimal 1 kunci jawaban!'); return; }
        addHidden(this, 'kunci_jawaban', JSON.stringify(checked));

    } else if (tipe === 'PG') {
        // Ambil checkbox yang dicentang sebagai kunci tunggal
        const checked = document.querySelector('#areaPG input[type="checkbox"]:checked');
        if (!checked) { e.preventDefault(); alert('Pilih 1 kunci jawaban!'); return; }
        addHidden(this, 'kunci_jawaban', checked.value);

    } else if (tipe === 'Menjodohkan') {
        // Gabungkan pasangan kanan sebagai opsi_tambahan (pisah |)
        const pasangan = [];
        ['A','B','C','D','E'].forEach(o => {
            const el = document.querySelector('[name="pasangan_' + o + '"]');
            if (el && el.value.trim()) pasangan.push(el.value.trim());
        });
        addHidden(this, 'opsi_tambahan', pasangan.join('|'));

        // Bangun kunci menjodohkan
        const kiri  = {};
        const kanan = [];
        ['A','B','C','D','E'].forEach((o, i) => {
            const kirEl = document.querySelector('[name="opsi_' + o.toLowerCase() + '"]');
            const kanEl = document.querySelector('[name="pasangan_' + o + '"]');
            if (kirEl && kirEl.value.trim()) {
                kiri[o] = pasangan[i] || '';
            }
        });
        addHidden(this, 'kunci_jawaban', JSON.stringify(kiri));

    } else if (tipe === 'Uraian') {
        const val = document.querySelector('[name="kunci_uraian"]').value;
        addHidden(this, 'kunci_jawaban', val);
    }
});

function addHidden(form, name, value) {
    const inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = name;
    inp.value = value;
    form.appendChild(inp);
}

/* ── File Upload Drop Zone ──────────────────────────── */
function onFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('fileNameText').textContent = file.name;
    document.getElementById('selectedFileName').style.display = 'block';
    document.getElementById('btnImpor').disabled = false;
}

const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const dt = e.dataTransfer;
    if (dt.files.length) {
        document.getElementById('fileSoal').files = dt.files;
        onFileSelect(document.getElementById('fileSoal'));
    }
});

/* ── Mode Impor Warning ─────────────────────────────── */
document.querySelectorAll('[name="mode_impor"]').forEach(r => {
    r.addEventListener('change', function() {
        document.getElementById('warnGanti').style.display = this.value === 'ganti' ? 'block' : 'none';
    });
});

/* ── Konfirmasi mode ganti sebelum submit impor ─────── */
document.getElementById('formImpor').addEventListener('submit', function(e) {
    const mode = document.querySelector('[name="mode_impor"]:checked').value;
    if (mode === 'ganti' && <?= count($soalList) ?> > 0) {
        if (!confirm('⚠️ Mode GANTI akan menghapus <?= count($soalList) ?> soal yang ada!\n\nYakin ingin melanjutkan?')) {
            e.preventDefault();
        }
    }
});
</script>
<?= $this->endSection() ?>
