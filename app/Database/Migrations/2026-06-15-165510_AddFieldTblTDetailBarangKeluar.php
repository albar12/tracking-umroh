<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTDetailBarangKeluar extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_detail_barang_keluar', [
            'barcode_value' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'detail_barang_keluar_id'
            ],


        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_detail_barang_keluar', 'barcode_value');
    }
}
