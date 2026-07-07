<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTableTStokOpname extends Migration
{
    public function up()
    {
        $fields = [
            'batch' => [
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
            'batch' => [
                'type' => 'VARCHAR',
                'constraint' => 11,
                'null' => true,
            ],


        ];
        $this->forge->modifyColumn('tbl_t_stok_opname', $fields);
    }
}
