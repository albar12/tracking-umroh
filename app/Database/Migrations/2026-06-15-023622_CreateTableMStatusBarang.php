<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMStatusBarang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'status_barang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
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

        $this->forge->addKey('status_barang_id', true);
        $this->forge->createTable('tbl_m_status_barang');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_status_barang');
    }
}
