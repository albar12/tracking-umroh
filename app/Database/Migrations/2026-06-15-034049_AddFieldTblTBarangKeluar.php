<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTBarangKeluar extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_barang_keluar', [
            'total_harga' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'keterangan'
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_barang_keluar', 'total_harga');
    }
}
