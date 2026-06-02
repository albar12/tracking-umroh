<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableHProduk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'histori_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'kode_transaksi' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'tipe' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'qty_in' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'qty_out' => [
                'type' => 'DOUBLE',
                'null' => true,
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
            'barang_keluar_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'detail_barang_keluar_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'produk_id' => [
                'type' => 'INT',
                'constraint' => 11,
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

        $this->forge->addKey('histori_id', true);
        $this->forge->createTable('tbl_h_produk');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_h_produk');
    }
}
