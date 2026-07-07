<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblHProduk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_h_produk', [
            'so_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'detail_barang_keluar_id',
            ],
            'detail_so_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'so_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_h_produk', 'so_id');
        $this->forge->dropColumn('tbl_h_produk', 'detail_so_id');
    }
}
