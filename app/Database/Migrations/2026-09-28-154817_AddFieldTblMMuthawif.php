<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblMMuthawif extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_muthawif', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Aktif', 'Tidak Aktif'],
                'after' => 'otp'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_muthawif', 'status');
    }
}
