<?php
namespace App\Controllers;

class Validasi extends BaseController
{
    public function dokumen($hash)
    {
        // Secara ideal kita akan query ke database tabel "dokumen_tercetak"
        // Namun sebagai simulasi yang cukup aman (berdasarkan plan): 
        // kita verifikasi format string jika diperlukan, atau sekadar 
        // memberitahu bahwa kode dokumen tersebut tercatat (simulasi validasi sederhana).
        
        $isValid = false;
        
        // Contoh hash validasi sederhana: MD5 berisi "JURNAL-guru_id-bulan-tahun"
        // Di sini kita anggap format MD5 (32 karakter alfanumerik) itu valid.
        // Untuk sistem production nyata, hash ini harus dicocokkan dengan nilai di tabel log cetak.
        if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            $isValid = true;
        }

        return view('validasi/dokumen', [
            'title' => 'Validasi Orisinalitas Dokumen',
            'hash'  => $hash,
            'isValid' => $isValid
        ]);
    }
}
