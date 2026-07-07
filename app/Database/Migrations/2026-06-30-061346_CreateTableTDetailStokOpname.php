<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTDetailStokOpname extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'detail_so_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'so_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'produk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status_so_id' => [
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

        $this->forge->addKey('detail_so_id', true);
        $this->forge->createTable('tbl_t_detail_stok_opname');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_t_detail_stok_opname');
    }
}
