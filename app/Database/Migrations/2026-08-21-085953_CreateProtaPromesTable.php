<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProtaPromesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'guru_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'mapel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'kelas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['PROTA', 'PROMES'],
                'default'    => 'PROTA',
            ],
            'materi_pokok' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'alokasi_waktu' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'alokasi_mingguan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('guru_id', 'gurus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('prota_promes');
    }

    public function down()
    {
        $this->forge->dropTable('prota_promes');
    }
}
