<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTBarangKeluar extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_barang_keluar', [
            'nominal_bayar' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'total_harga'
            ],
            'nominal_kembalian' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'nominal_bayar'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_barang_keluar', 'nominal_bayar');
        $this->forge->dropColumn('tbl_t_barang_keluar', 'nominal_kembalian');
    }
}
