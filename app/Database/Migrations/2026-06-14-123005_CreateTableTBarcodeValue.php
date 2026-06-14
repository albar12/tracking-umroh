<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTBarcodeValue extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'barcode_value_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'barang_masuk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'detail_barang_masuk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'produk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'barcode_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tgl_expired' => [
                'type' => 'DATE',
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

        $this->forge->addKey('barcode_value_id', true);
        $this->forge->createTable('tbl_t_barcode_value');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_t_barcode_value');
    }
}
