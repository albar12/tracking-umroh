<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblHProduk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_h_produk', [
            'barcode_value' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'produk_id'
            ],
            'tgl_expired' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'barcode_value'
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_h_produk', 'barcode_value');
        $this->forge->dropColumn('tbl_h_produk', 'tgl_expired');
    }
}
