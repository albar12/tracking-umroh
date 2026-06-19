<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTDetailBarangKeluar extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_detail_barang_keluar', [
            'harga_jual' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'qty'
            ],
            'total_harga' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'harga_jual'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_detail_barang_keluar', 'harga_jual');
        $this->forge->dropColumn('tbl_t_detail_barang_keluar', 'total_harga');
    }
}
