<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblHProduk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_h_produk', [
            'status_barang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'keterangan'
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_h_produk', 'status_barang_id');
    }
}
