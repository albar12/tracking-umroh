<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTDetailStokOpname extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_detail_stok_opname', [
            'barcode_value' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'so_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_detail_stok_opname', 'barcode_value');
    }
}
