<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblMProduk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_produk', [
            'produk_expired' => [
                'type' => 'ENUM',
                'constraint' => ['Ya', 'Tidak'],
                'default' => 'Ya',
                'after' => 'produk_barang'
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_produk', 'produk_expired');
    }
}
