<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTblMUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_users', [
            'otp' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'status',
            ],
            'otp_expired' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'otp',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_users', 'otp');
        $this->forge->dropColumn('tbl_m_users', 'otp_expired');
    }
}
