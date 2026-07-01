<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::login');
$routes->match(['GET', 'POST'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'authGuard']);

// Pengaturan Sekolah (Admin only)
$routes->group('pengaturan', ['filter' => 'adminGuard'], static function ($routes) {
    $routes->get('sekolah',             'PengaturanSekolah::index');
    $routes->post('sekolah/update',     'PengaturanSekolah::update');
    $routes->get('sekolah/hapus-logo',  'PengaturanSekolah::hapus_logo');
});

$routes->group('master', ['filter' => 'adminGuard'], static function ($routes) {
    $routes->get('tahun-pelajaran', 'MasterData::tahun_pelajaran');
    $routes->post('tahun-pelajaran', 'MasterData::save_tahun_pelajaran');
    $routes->get('tahun-pelajaran/delete/(:num)', 'MasterData::delete_tahun_pelajaran/$1');
    
    $routes->get('mata-pelajaran', 'MasterData::mata_pelajaran');
    $routes->post('mata-pelajaran', 'MasterData::save_mata_pelajaran');
    $routes->get('mata-pelajaran/delete/(:num)', 'MasterData::delete_mata_pelajaran/$1');
    
    $routes->get('kelas', 'MasterData::kelas');
    $routes->post('kelas', 'MasterData::save_kelas');
    $routes->get('kelas/delete/(:num)', 'MasterData::delete_kelas/$1');

    $routes->get('anggota-kelas', 'AnggotaKelas::index');
    $routes->post('anggota-kelas/save', 'AnggotaKelas::save');
    $routes->get('anggota-kelas/delete/(:num)', 'AnggotaKelas::delete/$1');
});

$routes->group('users', ['filter' => 'adminGuard'], static function ($routes) {
    $routes->get('guru',                   'Users::guru');
    $routes->post('guru',                  'Users::save_guru');          // tambah
    $routes->post('guru/import',           'Users::import_guru');        // import masal
    $routes->post('guru/update',           'Users::save_guru');          // edit (user_id diisi)
    $routes->get('guru/delete/(:num)',     'Users::delete_guru/$1');

    $routes->get('siswa',                  'Users::siswa');
    $routes->post('siswa',                 'Users::save_siswa');         // tambah
    $routes->post('siswa/import',          'Users::import_siswa');       // import masal
    $routes->post('siswa/update',          'Users::save_siswa');         // edit
    $routes->get('siswa/delete/(:num)',    'Users::delete_siswa/$1');
});

// Profil — semua role yang sudah login
$routes->get('profil',           'Users::profil',          ['filter' => 'authGuard']);
$routes->post('profil/update',   'Users::update_profil',   ['filter' => 'authGuard']);

// Buku Nilai — Siswa lihat nilai sendiri, Guru lihat rekap kelas
$routes->get('buku-nilai',       'BukuNilai::index',       ['filter' => 'authGuard']);

// Materi Siswa — halaman materi khusus siswa dengan pencarian & download
$routes->group('materi-siswa', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',                'MateriSiswa::index');         // daftar + cari
    $routes->get('download/(:num)', 'MateriSiswa::download/$1');   // unduh file aman
});

$routes->group('jadwal', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/', 'Jadwal::index');
    $routes->post('save', 'Jadwal::save', ['filter' => 'adminGuard']);
    $routes->get('delete/(:num)', 'Jadwal::delete/$1', ['filter' => 'adminGuard']);
});

$routes->group('materi', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',               'Materi::index');
    $routes->get('create',          'Materi::create');          // Form tambah
    $routes->post('store',          'Materi::store');           // Proses tambah
    $routes->get('edit/(:num)',     'Materi::edit/$1');         // Form edit
    $routes->post('update/(:num)',  'Materi::update/$1');       // Proses update
    $routes->get('delete/(:num)',   'Materi::delete/$1');       // Hapus
    $routes->get('download/(:num)', 'Materi::download/$1');     // Unduh file
});

$routes->group('presensi', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/', 'Presensi::index');
    $routes->get('input/(:num)', 'Presensi::input/$1'); // jadwal_id
    $routes->post('save', 'Presensi::save');
});

$routes->group('ulangan', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',                          'Ulangan::index');
    $routes->post('save',                      'Ulangan::save');
    $routes->get('soal/(:num)',                'Ulangan::soal/$1');
    $routes->post('save_soal',                 'Ulangan::save_soal');
    $routes->get('delete_soal/(:num)/(:num)',  'Ulangan::delete_soal/$1/$2');
    $routes->post('assign_kelas',              'Ulangan::assign_kelas');

    $routes->get('kerjakan/(:num)',            'Ulangan::kerjakan/$1');
    $routes->post('submit_jawaban',            'Ulangan::submit_jawaban');

    // Rekap & Penilaian (Guru)
    $routes->get('rekap/(:num)',               'Ulangan::rekap/$1');
    $routes->get('koreksi/(:num)',             'Ulangan::koreksi/$1');
    $routes->post('proses_koreksi',            'Ulangan::proses_koreksi');

    // Hasil Ujian (Siswa)
    $routes->get('hasil/(:num)',               'Ulangan::hasil/$1');
    $routes->get('hasil_by_ulangan/(:num)',    'Ulangan::hasil_by_ulangan/$1');
});

// ── Ekspor & Impor Soal Bank Ujian (Guru) ────────────────────────
$routes->group('soal', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('ekspor/(:num)/json',  'SoalExportImport::ekspor_json/$1');
    $routes->get('ekspor/(:num)/excel', 'SoalExportImport::ekspor_excel/$1');
    $routes->get('template-csv/(:num)', 'SoalExportImport::template_csv/$1');
    $routes->post('impor/(:num)',       'SoalExportImport::impor/$1');
});

// ── Laporan & Cetak ────────────────────────────────────────────────
$routes->group('laporan', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('nilai-kelas',         'Laporan::nilaiKelas');
    $routes->get('absensi',             'Laporan::absensi');
    $routes->get('raport-siswa/(:num)', 'Laporan::raportSiswa/$1');
});

// ── Notifikasi ─────────────────────────────────────────────────────
$routes->group('notifikasi', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',              'Notifikasi::index');
    $routes->get('unread',         'Notifikasi::unread');
    $routes->get('recent',         'Notifikasi::recent');
    $routes->post('baca-semua',    'Notifikasi::bacaSemua');
    $routes->get('baca/(:num)',    'Notifikasi::baca/$1');
    $routes->post('hapus/(:num)',  'Notifikasi::hapus/$1');
});

// ── Kalender Akademik ──────────────────────────────────────────────
$routes->group('kalender', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',               'Kalender::index');
    $routes->get('events',          'Kalender::events');
    $routes->post('save',           'Kalender::save');
    $routes->post('delete/(:num)',  'Kalender::delete/$1');
});

// ── Perangkat Mengajar Digital ─────────────────────────────────────
$routes->group('jurnal', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',               'Jurnal::index');
    $routes->get('create',          'Jurnal::create');
    $routes->post('save',           'Jurnal::save');
    $routes->get('edit/(:num)',     'Jurnal::edit/$1');
    $routes->get('delete/(:num)',   'Jurnal::delete/$1');
});

$routes->group('rpp', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',               'Rpp::index');
    $routes->get('create',          'Rpp::create');
    $routes->get('create_template', 'Rpp::create_template');
    $routes->post('save',           'Rpp::save');
    $routes->get('edit/(:num)',     'Rpp::edit/$1');
    $routes->get('view/(:num)',     'Rpp::view/$1');
    $routes->get('print/(:num)',    'Rpp::print/$1');
    $routes->get('delete/(:num)',   'Rpp::delete/$1');
});

$routes->group('prota', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',               'Prota::index');
    $routes->get('create',          'Prota::create');
    $routes->post('save',           'Prota::save');
    $routes->get('edit/(:num)',     'Prota::edit/$1');
    $routes->get('delete/(:num)',   'Prota::delete/$1');
});

$routes->group('export', ['filter' => 'authGuard'], static function ($routes) {
    $routes->get('/',               'Export::index');
    $routes->get('jurnal',          'Export::jurnal');
    $routes->get('prota_promes',    'Export::prota_promes');
});
