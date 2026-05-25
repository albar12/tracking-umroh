<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMRoleAkses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'akses_menu' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'akses_submenu' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'permissions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Aktif', 'Tidak Aktif'],
                'default' => 'Aktif',
            ],
            'user_created' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'user_updated' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'user_deleted' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('role_id', true);
        $this->forge->createTable('tbl_m_role_akses');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_role_akses');
    }
}
