<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
/* ── FullCalendar: Tema Dinamis (Light & Dark) ── */

/* Grid border */
.fc-theme-standard .fc-scrollgrid {
    border: 1px solid var(--lms-border);
    border-radius: 12px;
    overflow: hidden;
}
.fc-theme-standard td,
.fc-theme-standard th {
    border: 1px solid var(--lms-border);
}

/* Header hari (Sen, Sel, Rab, ...) */
.fc-theme-standard th {
    padding: 10px 0;
    background: var(--lms-bg);
    color: var(--lms-text-muted);
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
}

/* Nomor tanggal */
.fc-daygrid-day-number {
    color: var(--lms-text) !important;
    padding: 8px !important;
    font-weight: 500;
    text-decoration: none;
}

/* Hari ini */
.fc-day-today {
    background: rgba(37, 99, 235, 0.08) !important;
}

/* Event bar */
.fc-h-event {
    border: none;
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}
.fc-h-event:hover { transform: translateY(-1px); }

/* Judul bulan & tombol navigasi */
.fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 700;
    color: var(--lms-text) !important;
}
.fc-button-primary {
    background: var(--lms-bg-panel) !important;
    border: 1px solid var(--lms-border) !important;
    color: var(--lms-text) !important;
    box-shadow: none !important;
    text-transform: capitalize;
    transition: all 0.2s;
}
.fc-button-primary:hover {
    background: rgba(37,99,235,0.1) !important;
    color: var(--lms-primary) !important;
    border-color: var(--lms-primary) !important;
}
.fc-button-active,
.fc-button-primary:not(:disabled).fc-button-active {
    background: var(--lms-primary) !important;
    color: white !important;
    border-color: var(--lms-primary) !important;
}

/* Sel hari kosong */
.fc-daygrid-day {
    background: var(--lms-bg-panel);
}
.fc-daygrid-day.fc-day-other {
    background: var(--lms-bg);
}

/* Legend */
.legend-box {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    color: var(--lms-text-muted);
    margin-right: 16px;
}
.legend-color { width: 12px; height: 12px; border-radius: 3px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-800" style="margin:0;color:var(--lms-text);font-weight:800;">
            <i class="bi bi-calendar3 me-2 text-accent"></i>Kalender Akademik
        </h4>
        <small class="text-lms-muted">Jadwal libur, ujian, dan kegiatan sekolah</small>
    </div>
    <?php if ($role === 'Admin'): ?>
    <button class="btn btn-primary shadow-sm" onclick="openModal()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Agenda
    </button>
    <?php endif; ?>
</div>

<!-- Legends -->
<div class="mb-3 d-flex flex-wrap">
    <div class="legend-box"><div class="legend-color" style="background:#EF4444;"></div>Libur</div>
    <div class="legend-box"><div class="legend-color" style="background:#F59E0B;"></div>Ujian</div>
    <div class="legend-box"><div class="legend-color" style="background:#22C55E;"></div>Kegiatan</div>
    <div class="legend-box"><div class="legend-color" style="background:#818CF8;"></div>Lainnya</div>
</div>

<!-- Kalender Container -->
<div class="glass-panel card p-3 p-md-4 mb-4">
    <div id="calendar"></div>
</div>

<?php if ($role === 'Admin'): ?>
<!-- Modal Tambah/Edit Agenda -->
<div class="modal fade" id="agendaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content lms-modal">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title" id="modalTitle">Tambah Agenda</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="agendaForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="agendaId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white small">Judul Acara <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control lms-input" required placeholder="Contoh: Penilaian Akhir Semester">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-white small">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" id="tanggalMulai" class="form-control lms-input" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-white small">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggalSelesai" class="form-control lms-input">
                            <small class="text-lms-muted" style="font-size:0.7rem;">Kosongkan jika 1 hari</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small">Kategori Acara</label>
                        <select name="tipe" id="tipe" class="form-select lms-input" onchange="updateWarna()">
                            <option value="Kegiatan" data-color="#22C55E">Kegiatan</option>
                            <option value="Libur" data-color="#EF4444">Libur</option>
                            <option value="Ujian" data-color="#F59E0B">Ujian / Penilaian</option>
                            <option value="Lainnya" data-color="#818CF8">Lainnya</option>
                        </select>
                        <input type="hidden" name="warna" id="warna" value="#22C55E">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white small">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control lms-input" rows="3" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25 justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnHapus" style="display:none;" onclick="hapusAgenda()">Hapus</button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSimpan">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Modal View Agenda (Siswa/Guru) -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content lms-modal">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span id="viewColor" style="width:12px;height:12px;border-radius:3px;display:inline-block;"></span>
                    <span id="viewTitle">Detail Acara</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <span class="badge mb-3" id="viewTipe" style="font-size:0.8rem;padding:6px 12px;"></span>
                <h6 class="mb-2" id="viewDateRange" style="font-size:1.1rem;color:var(--lms-text);"></h6>
                <p class="text-lms-muted mb-0 mt-3" id="viewDesc" style="white-space:pre-wrap;text-align:left;background:var(--lms-bg);padding:12px;border-radius:8px;"></p>
            </div>
            <div class="modal-footer border-secondary border-opacity-25">
                <button type="button" class="btn btn-secondary btn-sm w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>

<script>
let calendar;
const isAdmin = <?= $role === 'Admin' ? 'true' : 'false' ?>;

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'id',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: '<?= base_url('kalender/events') ?>',
        height: 'auto',
        contentHeight: 600,
        selectable: isAdmin,
        selectMirror: true,
        select: function(arg) {
            if(isAdmin) {
                openModal();
                document.getElementById('tanggalMulai').value = arg.startStr;
                // FullCalendar select includes next day as end, we substract 1 day for intuitive UI
                let endObj = new Date(arg.endStr);
                endObj.setDate(endObj.getDate() - 1);
                let startObj = new Date(arg.startStr);
                if (startObj.getTime() !== endObj.getTime()) {
                    document.getElementById('tanggalSelesai').value = endObj.toISOString().split('T')[0];
                }
            }
            calendar.unselect();
        },
        eventClick: function(arg) {
            let evt = arg.event;
            let endStr = evt.extendedProps.tanggal_selesai_asli || evt.startStr;
            
            if (isAdmin) {
                openModal(evt.id, evt.title, evt.startStr, endStr, evt.extendedProps.tipe, evt.extendedProps.deskripsi, evt.backgroundColor);
            } else {
                // View Only Mode
                document.getElementById('viewColor').style.background = evt.backgroundColor;
                document.getElementById('viewTitle').innerText = evt.title;
                
                let tipeBadge = document.getElementById('viewTipe');
                tipeBadge.innerText = evt.extendedProps.tipe;
                tipeBadge.style.backgroundColor = evt.backgroundColor + '40'; // transparent bg
                tipeBadge.style.color = evt.backgroundColor;
                tipeBadge.style.border = '1px solid ' + evt.backgroundColor;
                
                let dateText = formatTanggal(evt.startStr);
                if (evt.startStr !== endStr) {
                    dateText += ' - ' + formatTanggal(endStr);
                }
                document.getElementById('viewDateRange').innerText = dateText;
                
                let desc = evt.extendedProps.deskripsi;
                let descEl = document.getElementById('viewDesc');
                if (desc) {
                    descEl.innerText = desc;
                    descEl.style.display = 'block';
                } else {
                    descEl.style.display = 'none';
                }
                
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            }
        }
    });
    calendar.render();
});

function formatTanggal(dateStr) {
    let d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

<?php if ($role === 'Admin'): ?>
let myModal;
function openModal(id='', judul='', tMulai='', tSelesai='', tipe='Kegiatan', desc='', warna='#22C55E') {
    document.getElementById('agendaForm').reset();
    document.getElementById('agendaId').value = id;
    document.getElementById('judul').value = judul;
    document.getElementById('tanggalMulai').value = tMulai.split('T')[0];
    document.getElementById('tanggalSelesai').value = tSelesai.split('T')[0];
    document.getElementById('tipe').value = tipe;
    document.getElementById('deskripsi').value = desc;
    document.getElementById('warna').value = warna;
    
    document.getElementById('modalTitle').innerText = id ? 'Edit Agenda' : 'Tambah Agenda';
    document.getElementById('btnHapus').style.display = id ? 'block' : 'none';
    
    if(!myModal) myModal = new bootstrap.Modal(document.getElementById('agendaModal'));
    myModal.show();
}

function updateWarna() {
    let sel = document.getElementById('tipe');
    let color = sel.options[sel.selectedIndex].getAttribute('data-color');
    document.getElementById('warna').value = color;
}

document.getElementById('agendaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let btn = document.getElementById('btnSimpan');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
    btn.disabled = true;

    let formData = new FormData(this);
    fetch('<?= base_url('kalender/save') ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            myModal.hide();
            calendar.refetchEvents();
        } else {
            alert(data.message);
        }
    })
    .finally(() => {
        btn.innerHTML = 'Simpan';
        btn.disabled = false;
    });
});

function hapusAgenda() {
    if(!confirm('Hapus acara ini dari kalender?')) return;
    
    let id = document.getElementById('agendaId').value;
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    let fd = new FormData();
    fd.append('csrf_test_name', csrfToken);

    fetch('<?= base_url('kalender/delete/') ?>' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            myModal.hide();
            calendar.refetchEvents();
        }
    });
}
<?php endif; ?>
</script>
<?= $this->endSection() ?>
