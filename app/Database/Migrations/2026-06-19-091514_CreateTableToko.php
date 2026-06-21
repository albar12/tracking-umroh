<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableToko extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'toko_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_toko' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'no_telp' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'logo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Aktif', 'Tidak Aktif'],
                'default' => 'Aktif',
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

        $this->forge->addKey('toko_id', true);
        $this->forge->createTable('tbl_m_toko');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_toko');
    }
}
