<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRppDigitalTable extends Migration
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
            'mapel_id' => [
                'type'       => 'INT',
            ],
            'kelas_id' => [
                'type'       => 'INT',
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'konten' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rpp_digital');
    }

    public function down()
    {
        $this->forge->dropTable('rpp_digital');
    }
}
