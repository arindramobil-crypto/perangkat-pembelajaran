<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJurnalMengajarTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'guru_id' => [
                'type'       => 'INT',
            ],
            'jadwal_id' => [
                'type'       => 'INT',
            ],
            'tanggal' => [
                'type'       => 'DATE',
            ],
            'jam_ke' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'materi_pembahasan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'catatan_kejadian' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'siswa_absen' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('guru_id', 'gurus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jadwal_id', 'jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jurnal_mengajar');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_mengajar');
    }
}
