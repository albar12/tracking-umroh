<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTDokumen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'dokumen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'dokumen' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tipe' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'barang_masuk_id' => [
                'type' => 'INT',
                'constraint' => 11,
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

        $this->forge->addKey('dokumen_id', true);
        $this->forge->createTable('tbl_t_dokumen');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_t_dokumen');
    }
}
