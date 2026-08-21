<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusPenilaianToJawabanSiswa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('jawaban_siswa', [
            'status_penilaian' => [
                'type'       => 'ENUM',
                'constraint' => ['Selesai', 'Menunggu Koreksi'],
                'default'    => 'Selesai',
                'after'      => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('jawaban_siswa', 'status_penilaian');
    }
}
