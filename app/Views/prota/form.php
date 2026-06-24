<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('prota') ?>" class="text-decoration-none text-lms-muted mb-2 d-inline-block"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Prota/Promes</a>
    <h4 style="color:white;font-weight:800;margin:0;">
        <i class="bi bi-journal-plus me-2" style="color:#818CF8;"></i><?= esc($title) ?>
    </h4>
</div>

<div class="glass-panel card p-4">
    <form action="<?= base_url('prota/save') ?>" method="post">
        <?= csrf_field() ?>
        <?php if(isset($prota)): ?>
            <input type="hidden" name="id" value="<?= $prota['id'] ?>">
        <?php endif; ?>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label text-white small">Tipe Dokumen <span class="text-danger">*</span></label>
                <select name="tipe" id="tipeSelect" class="form-select lms-input" required onchange="toggleMatrix()">
                    <option value="Prota" <?= (isset($prota) && $prota['tipe'] === 'Prota') ? 'selected' : '' ?>>Program Tahunan (Prota)</option>
                    <option value="Promes" <?= (isset($prota) && $prota['tipe'] === 'Promes') ? 'selected' : '' ?>>Program Semester (Promes)</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small">Mata Pelajaran <span class="text-danger">*</span></label>
                <select name="mapel_id" class="form-select lms-input" required>
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= (isset($prota) && $prota['mapel_id'] == $m['id']) ? 'selected' : '' ?>><?= esc($m['nama_mapel']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-white small">Target Kelas <span class="text-danger">*</span></label>
                <select name="kelas_id" class="form-select lms-input" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (isset($prota) && $prota['kelas_id'] == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label text-white small">Materi Pokok / Tujuan Pembelajaran <span class="text-danger">*</span></label>
            <textarea name="materi_pokok" class="form-control lms-input" rows="2" required placeholder="Contoh: 3.1 Memahami konsep dasar jaringan..."><?= isset($prota) ? esc($prota['materi_pokok']) : '' ?></textarea>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label text-white small">Total Alokasi Waktu (Contoh: 12 JP)</label>
                <input type="text" name="alokasi_waktu" class="form-control lms-input" placeholder="Otomatis dihitung dari matriks, tapi bisa diisi manual" value="<?= isset($prota) ? esc($prota['alokasi_waktu']) : '' ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label text-white small">Keterangan Tambahan</label>
                <input type="text" name="keterangan" class="form-control lms-input" placeholder="Opsional" value="<?= isset($prota) ? esc($prota['keterangan']) : '' ?>">
            </div>
        </div>

        <div id="matrixContainer" class="mb-4" style="display:none;">
            <label class="form-label text-white small d-block mb-3 border-bottom border-secondary pb-2">
                <i class="bi bi-grid-3x3 me-2"></i>Matriks Sebaran Alokasi Waktu (JP per Minggu)
                <span class="ms-2 badge" style="background:rgba(239,68,68,0.2);color:#EF4444;">Warna Merah = Minggu Libur/Ujian (Berdasarkan Kalender)</span>
            </label>
            <div class="table-responsive" style="max-height:400px;">
                <table class="table table-bordered table-sm align-middle text-center" style="min-width:1000px; font-size:0.8rem; background:rgba(255,255,255,0.02);">
                    <thead style="background:rgba(255,255,255,0.1); color:white; position:sticky; top:0; z-index:10;">
                        <tr>
                            <th rowspan="2" class="align-middle" style="width:100px;">Semester</th>
                            <th rowspan="2" class="align-middle" style="width:120px;">Bulan</th>
                            <th colspan="5">Minggu Ke-</th>
                        </tr>
                        <tr>
                            <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th>
                        </tr>
                    </thead>
                    <tbody style="color:#CBD5E1;">
                        <?php 
                        $months = [
                            ['sem'=>'Ganjil', 'm'=>7, 'name'=>'Juli'],
                            ['sem'=>'Ganjil', 'm'=>8, 'name'=>'Agustus'],
                            ['sem'=>'Ganjil', 'm'=>9, 'name'=>'September'],
                            ['sem'=>'Ganjil', 'm'=>10, 'name'=>'Oktober'],
                            ['sem'=>'Ganjil', 'm'=>11, 'name'=>'November'],
                            ['sem'=>'Ganjil', 'm'=>12, 'name'=>'Desember'],
                            ['sem'=>'Genap', 'm'=>1, 'name'=>'Januari'],
                            ['sem'=>'Genap', 'm'=>2, 'name'=>'Februari'],
                            ['sem'=>'Genap', 'm'=>3, 'name'=>'Maret'],
                            ['sem'=>'Genap', 'm'=>4, 'name'=>'April'],
                            ['sem'=>'Genap', 'm'=>5, 'name'=>'Mei'],
                            ['sem'=>'Genap', 'm'=>6, 'name'=>'Juni'],
                        ];
                        
                        // Parse existing data if edit
                        $savedAlokasi = [];
                        if(isset($prota) && $prota['alokasi_mingguan']) {
                            $savedAlokasi = json_decode($prota['alokasi_mingguan'], true) ?: [];
                        }

                        // Determine holiday weeks from kalender (Rough estimation based on month)
                        // A proper week calculation requires full calendar math, here we simulate based on month overlap
                        $liburMonths = [];
                        foreach($kalender as $kal) {
                            $m = (int)date('n', strtotime($kal['tanggal_mulai']));
                            $liburMonths[$m] = true;
                        }

                        foreach($months as $idx => $m_info): 
                            $m_num = $m_info['m'];
                            $isHolidayMonth = isset($liburMonths[$m_num]);
                        ?>
                        <tr class="month-row sem-<?= strtolower($m_info['sem']) ?>">
                            <?php if($idx == 0): ?>
                                <td rowspan="6" class="bg-primary bg-opacity-10 text-white fw-bold" style="writing-mode: vertical-rl; transform: rotate(180deg);">SEMESTER GANJIL</td>
                            <?php elseif($idx == 6): ?>
                                <td rowspan="6" class="bg-success bg-opacity-10 text-white fw-bold" style="writing-mode: vertical-rl; transform: rotate(180deg);">SEMESTER GENAP</td>
                            <?php endif; ?>
                            
                            <td class="text-start fw-bold"><?= $m_info['name'] ?></td>
                            <?php for($w=1; $w<=5; $w++): 
                                $val = isset($savedAlokasi[$m_num][$w]) ? $savedAlokasi[$m_num][$w] : '';
                                // Highlight week 4/5 as potential holidays if it's a holiday month
                                $isHoliday = ($isHolidayMonth && $w >= 3) ? true : false;
                                $bgClass = $isHoliday ? 'bg-danger bg-opacity-25' : '';
                            ?>
                                <td class="<?= $bgClass ?>">
                                    <input type="text" name="alokasi[<?= $m_num ?>][<?= $w ?>]" class="form-control text-center p-1" style="width:40px; margin:0 auto; background:rgba(0,0,0,0.3); color:white; border:1px solid rgba(255,255,255,0.2);" value="<?= esc($val) ?>">
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="form-text text-lms-muted mt-2"><i class="bi bi-info-circle"></i> Isikan angka jumlah JP (contoh: 4) pada kotak minggu yang sesuai.</div>
        </div>

        <div class="d-flex justify-content-between border-top border-secondary border-opacity-25 pt-4 mt-2">
            <a href="<?= base_url('prota') ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Data</button>
        </div>
    </form>
</div>

<script>
function toggleMatrix() {
    const tipe = document.getElementById('tipeSelect').value;
    const matrix = document.getElementById('matrixContainer');
    // Selalu tampilkan matriks, tapi untuk prota bisa disembunyikan atau ditampilkan full
    // Karena Promes sangat bergantung pada matriks, kita tampilkan saja untuk keduanya.
    matrix.style.display = 'block';
    
    // Highlight based on semester if Promes
    if(tipe === 'Promes') {
        // Tampilkan prompt untuk memilih semester? Tidak perlu, tampilkan 12 bulan saja.
    }
}
// Init
toggleMatrix();
</script>

<?= $this->endSection() ?>
