<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTableMPermissions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_permissions', [
            'last_uri' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_permissions', 'last_uri');
    }
}
