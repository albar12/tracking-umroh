<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMSubMenu extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'sub_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'sub' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'route_sub' => [
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
            'order_sub' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'tipe_sub' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'menu_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'display_sub' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('sub_id', true);
        $this->forge->createTable('tbl_m_sub_menu');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_sub_menu');
    }
}
