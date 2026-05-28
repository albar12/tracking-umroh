<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMMenu extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'menu_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'menu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'route_menu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'flag_route' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'sub_menu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'order_menu' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'display' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('menu_id', true);
        $this->forge->createTable('tbl_m_menu');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_menu');
    }
}
