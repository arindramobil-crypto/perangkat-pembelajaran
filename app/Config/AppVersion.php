<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AppVersion extends BaseConfig
{
    /**
     * Waktu terakhir aplikasi diperbarui.
     * Harus diperbarui oleh Agent setiap kali ada perubahan kode.
     */
    public $lastUpdated = '01 Juli 2026, 19:01 WIB';
}
