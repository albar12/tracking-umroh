<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTDetailBarangMasuk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_detail_barang_masuk', [
            'barcode_value' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'barang_masuk_id'
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
        $this->forge->dropColumn('tbl_t_detail_barang_masuk', 'barcode_value');
        $this->forge->dropColumn('tbl_t_detail_barang_masuk', 'tgl_expired');
    }
}
