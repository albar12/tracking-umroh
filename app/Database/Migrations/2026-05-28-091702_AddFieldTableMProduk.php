<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTableMProduk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_produk', [
            'barcode_value' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'satuan_id'
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_produk', 'barcode_value');
    }
}
