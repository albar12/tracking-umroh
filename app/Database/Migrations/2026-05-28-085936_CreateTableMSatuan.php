<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMSatuan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'satuan_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'satuan' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'qty' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
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

        $this->forge->addKey('satuan_id', true);
        $this->forge->createTable('tbl_m_satuan');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_satuan');
    }
}
