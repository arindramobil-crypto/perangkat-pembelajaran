<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .validasi-card { background: white; padding: 2.5rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); max-width: 450px; width: 100%; text-align: center; }
        .valid-icon { font-size: 5rem; color: #22C55E; margin-bottom: 1rem; }
        .invalid-icon { font-size: 5rem; color: #EF4444; margin-bottom: 1rem; }
        h3 { font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
        p { color: #64748b; font-size: 0.95rem; margin-bottom: 1.5rem; }
        .hash-box { background: #f1f5f9; padding: 0.75rem; border-radius: 0.5rem; font-family: monospace; font-size: 0.85rem; color: #475569; word-break: break-all; margin-bottom: 1.5rem; }
    </style>
</head>
<body>

    <div class="validasi-card">
        <?php if ($isValid): ?>
            <i class="bi bi-patch-check-fill valid-icon"></i>
            <h3>Dokumen Valid</h3>
            <p>Dokumen cetak ini terverifikasi asli dan tercatat pada sistem akademik sekolah kami.</p>
        <?php else: ?>
            <i class="bi bi-x-octagon-fill invalid-icon"></i>
            <h3>Dokumen Tidak Valid</h3>
            <p>Maaf, kode validasi dokumen ini tidak dapat ditemukan atau formatnya tidak sesuai.</p>
        <?php endif; ?>

        <div class="hash-box">
            <strong>Kode Validasi:</strong><br>
            <?= esc($hash) ?>
        </div>
        
        <a href="<?= base_url() ?>" class="btn btn-primary w-100" style="padding: 0.6rem; border-radius: 0.5rem; font-weight: 600;">Kembali ke Beranda</a>
    </div>

</body>
</html>
