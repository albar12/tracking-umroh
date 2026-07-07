<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTblTStokOpname extends Migration
{
    public function up()
    {
        $fields = [
            'status_approval' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],


        ];
        $this->forge->modifyColumn('tbl_t_stok_opname', $fields);
    }

    public function down()
    {
        $fields = [
            'status_approval' => [
                'type' => 'VARCHAR',
                'constraint' => 11,
                'null' => true,
            ],

        ];
        $this->forge->modifyColumn('tbl_t_stok_opname', $fields);
    }
}
