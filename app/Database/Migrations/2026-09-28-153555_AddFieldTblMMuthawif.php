<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblMMuthawif extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_muthawif', [
            'foto_profile' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'password'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_muthawif', 'foto_profile');
    }
}
