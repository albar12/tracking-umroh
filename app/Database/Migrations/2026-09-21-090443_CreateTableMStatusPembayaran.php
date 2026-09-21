<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMStatusPembayaran extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'status_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_status' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
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

        $this->forge->addKey('status_id', true);
        $this->forge->createTable('tbl_m_status_pembayaran');
    }

    public function down()
    {
        $this->forge->createTable('tbl_m_status_pembayaran');
    }
}
