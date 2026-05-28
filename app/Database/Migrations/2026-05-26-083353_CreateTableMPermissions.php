<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMPermissions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'permissions_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'submenu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'order_submenu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'route_submenu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'tipe_menu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'menu_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'sub_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'display_submenu' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'default' => 'yes',
            ],
        ]);

        $this->forge->addKey('permissions_id', true);
        $this->forge->createTable('tbl_m_permissions');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_permissions');
    }
}
