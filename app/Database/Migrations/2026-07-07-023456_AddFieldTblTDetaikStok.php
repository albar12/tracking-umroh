<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblTDetaikStok extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_t_detail_stok_opname', [
            'qty_ditemukan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'qty',
            ],
            'qty_hilang' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'qty_ditemukan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_t_detail_stok_opname', 'qty_ditemukan');
        $this->forge->dropColumn('tbl_t_detail_stok_opname', 'qty_hilang');
    }
}
