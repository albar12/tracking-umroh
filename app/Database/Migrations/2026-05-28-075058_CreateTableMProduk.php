<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMProduk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'produk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'ketegori_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'produk' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'deskripsi_produk' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'harga_jual' => [
                'type' => 'VARCHAR',
                'constraint' => 11,
                'null' => true,
            ],
            'produk_barang' => [
                'type' => 'ENUM',
                'constraint' => ['Ya', 'Tidak'],
                'default' => 'Ya',
            ],
            'satuan_id' => [
                'type' => 'INT',
                'constraint' => 11,
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

        $this->forge->addKey('produk_id', true);
        $this->forge->createTable('tbl_m_produk');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_produk');
    }
}
