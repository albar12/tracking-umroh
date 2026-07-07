<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTStokOpname extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_stok_opname', [
            'no_dokument' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'so_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_stok_opname', 'no_dokument');
    }
}
