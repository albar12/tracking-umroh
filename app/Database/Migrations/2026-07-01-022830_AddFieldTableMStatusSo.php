<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTableMStatusSo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_status_so', [
            'warna_span' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'keterangan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_status_so', 'warna_span');
    }
}
