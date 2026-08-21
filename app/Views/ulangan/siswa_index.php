<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="glass-panel card">
    <div style="margin-bottom: 1.5rem;">
        <h3 class="card-title" style="margin-bottom: 0.5rem;">Daftar Ujian & Kuis Aktif</h3>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Berikut adalah ujian yang ditugaskan ke kelas Anda.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        <?php foreach($ulanganList as $u): ?>
        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; transition: transform 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <h4 style="color: white; margin: 0;"><?= esc($u['judul']) ?></h4>
                <span style="background: rgba(129, 140, 248, 0.2); color: #818CF8; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">
                    <?= esc($u['tipe']) ?>
                </span>
            </div>
            
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem; min-height: 40px;">
                <?= esc($u['deskripsi']) ?>
            </p>
            
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 1rem;">
                <div>
                    <span style="display: block; font-size: 0.8rem; color: var(--text-muted);">Mata Pelajaran</span>
                    <strong style="color: white; font-size: 0.9rem;"><?= esc($u['nama_mapel']) ?></strong>
                </div>
                <div style="text-align: right;">
                    <span style="display: block; font-size: 0.8rem; color: var(--text-muted);">Durasi</span>
                    <strong style="color: white; font-size: 0.9rem;"><?= esc($u['durasi']) ?> Menit</strong>
                </div>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <?php if($u['status'] == 'Selesai'): ?>
                    <div style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.3);
                                padding: 0.8rem; border-radius: 8px; text-align: center; margin-bottom: 0.75rem;">
                        <span style="color: #22C55E; display: block; font-size: 0.8rem; margin-bottom: 0.2rem;">
                            <i class="bi bi-check-circle me-1"></i>Selesai Dikerjakan
                        </span>
                        <strong style="color: white; font-size: 1.3rem;">
                            <?= $u['nilai_akhir'] ?>
                        </strong>
                        <span style="color: var(--lms-text-muted); font-size: 0.75rem;"> / 100</span>
                    </div>
                    <a href="<?= base_url('ulangan/hasil/' . $u['js_id']) ?>"
                       class="btn btn-sm w-100 d-flex align-items-center justify-content-center gap-1"
                       style="background:rgba(129,140,248,0.15);border:1px solid rgba(129,140,248,0.3);color:#818CF8;">
                        <i class="bi bi-eye"></i> Lihat Hasil &amp; Pembahasan
                    </a>
                <?php elseif($u['status'] == 'Mengerjakan'): ?>
                    <a href="<?= base_url('ulangan/kerjakan/'.$u['uk_id']) ?>"
                       class="btn btn-warning text-dark w-100 fw-bold">
                        <i class="bi bi-play-fill me-1"></i>Lanjutkan Mengerjakan
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('ulangan/kerjakan/'.$u['uk_id']) ?>"
                       class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-pencil-fill me-1"></i>Mulai Kerjakan
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($ulanganList)): ?>
        <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; border: 1px dashed var(--border-color); border-radius: 12px;">
            <p style="color: var(--text-muted); font-size: 1.1rem;">Belum ada ujian yang ditugaskan ke kelas Anda saat ini.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
