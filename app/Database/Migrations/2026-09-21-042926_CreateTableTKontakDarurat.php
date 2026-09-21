<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTKontakDarurat extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kontak_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'jamaah_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'hubungan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'nama_kontak' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'no_tlp' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'user_created' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'user_updated' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'user_deleted' => [
                'type' => 'INT',
                'constraint' => 11,
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('kontak_id', true);
        $this->forge->createTable('tbl_t_kontak_darurat');
    }

    public function down()
    {
        $this->forge->createTable('tbl_t_kontak_darurat');
    }
}
