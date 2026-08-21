<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="color: white; margin-bottom: 0.2rem;"><?= esc($uk['judul']) ?></h2>
        <p style="color: var(--text-muted);">Mata Pelajaran: <?= esc($uk['nama_mapel'] ?? 'N/A') ?> | Durasi: <?= esc($uk['durasi']) ?> Menit</p>
    </div>
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #EF4444; padding: 0.8rem 1.5rem; border-radius: 8px;">
        <span style="color: var(--text-muted); font-size: 0.8rem; display: block;">Sisa Waktu</span>
        <strong style="color: #EF4444; font-size: 1.5rem; font-family: monospace;" id="timer">--:--:--</strong>
    </div>
</div>

<form action="<?= base_url('ulangan/submit_jawaban') ?>" method="post" id="formUjian">
    <input type="hidden" name="attempt_id" value="<?= $attempt['id'] ?>">
    
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem;">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php $no=1; foreach($soalList as $s): ?>
            <div class="glass-panel" id="soal_<?= $no ?>" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
                    <strong style="color: white; font-size: 1.1rem;">Soal No. <?= $no ?></strong>
                    <span style="background: rgba(129, 140, 248, 0.2); color: #818CF8; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem;"><?= esc($s['tipe_soal']) ?></span>
                </div>
                
                <div style="color: white; font-size: 1.05rem; margin-bottom: 1.5rem; line-height: 1.6;">
                    <?= nl2br(esc($s['pertanyaan'])) ?>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                    <?php if($s['tipe_soal'] == 'PG'): ?>
                        <label class="opsi-label">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="A" onclick="markNav(<?= $no ?>)"> A. <?= esc($s['opsi_a']) ?>
                        </label>
                        <label class="opsi-label">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="B" onclick="markNav(<?= $no ?>)"> B. <?= esc($s['opsi_b']) ?>
                        </label>
                        <label class="opsi-label">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="C" onclick="markNav(<?= $no ?>)"> C. <?= esc($s['opsi_c']) ?>
                        </label>
                        <label class="opsi-label">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="D" onclick="markNav(<?= $no ?>)"> D. <?= esc($s['opsi_d']) ?>
                        </label>
                        <label class="opsi-label">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="E" onclick="markNav(<?= $no ?>)"> E. <?= esc($s['opsi_e']) ?>
                        </label>
                    <?php elseif($s['tipe_soal'] == 'Benar Salah'): ?>
                        <label class="opsi-label">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="Benar" onclick="markNav(<?= $no ?>)"> Benar
                        </label>
                        <label class="opsi-label">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="Salah" onclick="markNav(<?= $no ?>)"> Salah
                        </label>
                    <?php elseif($s['tipe_soal'] == 'Uraian'): ?>
                        <textarea name="jawaban[<?= $s['id'] ?>]" class="form-control" rows="5" placeholder="Ketik jawaban Anda di sini..." onchange="markNav(<?= $no ?>)" onkeyup="if(this.value.trim() !== '') markNav(<?= $no ?>);"></textarea>
                    <?php else: ?>
                        <textarea name="jawaban[<?= $s['id'] ?>]" class="form-control" rows="3" placeholder="Tuliskan jawaban..." onchange="markNav(<?= $no ?>)"></textarea>
                    <?php endif; ?>
                </div>
            </div>
            <?php $no++; endforeach; ?>
        </div>
        
        <div>
            <div class="glass-panel" style="position: sticky; top: 2rem;">
                <h4 style="color: white; margin-bottom: 1rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Navigasi Soal</h4>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 2rem;">
                    <?php for($i=1; $i<=$no-1; $i++): ?>
                    <a href="#soal_<?= $i ?>" id="nav_<?= $i ?>" style="display: flex; justify-content: center; align-items: center; height: 40px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; text-decoration: none; border-radius: 4px; font-weight: bold; transition: 0.2s;">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem;" onclick="return confirm('Apakah Anda yakin sudah selesai mengerjakan? Waktu masih tersisa.')">
                    Kumpulkan Jawaban
                </button>
            </div>
        </div>
    </div>
</form>

<style>
    .opsi-label {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: white;
        cursor: pointer;
        transition: 0.2s;
    }
    .opsi-label:hover {
        background: rgba(255,255,255,0.08);
        border-color: #818CF8;
    }
    input[type="radio"] {
        accent-color: #818CF8;
        width: 1.2rem;
        height: 1.2rem;
    }
    .nav-answered {
        background: #22C55E !important;
        border-color: #22C55E !important;
        color: white !important;
    }
</style>

<script>
    function markNav(no) {
        document.getElementById('nav_' + no).classList.add('nav-answered');
    }
    
    const durasiMenit = <?= $uk['durasi'] ?>;
    // Gunakan waktu lokal JS supaya tidak masalah zona waktu, atau parse dari PHP string (UTC/local)
    // Untuk sederhana, kita timer dari saat page load karena waktu mulai mungkin sedikit lampau.
    // Tapi karena page reload bisa me-reset timer murni JS, kita hitung dari waktu_mulai DB:
    // Hati-hati timezone. Lebih aman: JS Date.now() + (sisa waktu detik * 1000).
    // Tapi karena ini prototype, kita pakai basic logic:
    
    // Server time (waktu mulai di MySQL) dan local time bisa beda. 
    // Kita set sisa waktu dengan selisih absolut saja agar aman.
    <?php
        $mulai = strtotime($attempt['waktu_mulai']);
        $durasiDetik = $uk['durasi'] * 60;
        $sekarang = time();
        $berjalan = $sekarang - $mulai;
        $sisaDetik = $durasiDetik - $berjalan;
    ?>
    let sisaDetik = <?= $sisaDetik > 0 ? $sisaDetik : 0 ?>;
    
    const x = setInterval(function() {
        if (sisaDetik <= 0) {
            clearInterval(x);
            document.getElementById("timer").innerHTML = "HABIS";
            alert("Waktu habis! Jawaban Anda akan dikumpulkan otomatis.");
            document.getElementById("formUjian").submit();
            return;
        }
        
        sisaDetik--;
        
        const hours = Math.floor(sisaDetik / 3600);
        const minutes = Math.floor((sisaDetik % 3600) / 60);
        const seconds = Math.floor(sisaDetik % 60);
        
        document.getElementById("timer").innerHTML = 
            (hours < 10 ? "0"+hours : hours) + ":" + 
            (minutes < 10 ? "0"+minutes : minutes) + ":" + 
            (seconds < 10 ? "0"+seconds : seconds);
            
    }, 1000);
</script>
<?= $this->endSection() ?>
