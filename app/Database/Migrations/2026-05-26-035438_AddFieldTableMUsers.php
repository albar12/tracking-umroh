<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldTableMUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_users', [
            'tgl_lahir' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'jenis_kelamin'
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'tgl_lahir'
            ],
            'tlp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'alamat'
            ],
            'no_hp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'tlp'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_users', 'tgl_lahir');
        $this->forge->dropColumn('tbl_m_users', 'alamat');
        $this->forge->dropColumn('tbl_m_users', 'tlp');
        $this->forge->dropColumn('tbl_m_users', 'no_hp');
    }
}
