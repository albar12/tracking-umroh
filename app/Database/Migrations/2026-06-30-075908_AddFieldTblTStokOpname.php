<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTStokOpname extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_stok_opname', [
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'produk_so',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_stok_opname', 'keterangan');
    }
}
