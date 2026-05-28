<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableHLogLogin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'log_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'login_ip' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'login_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'login_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'logout_ip' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'logout_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'logout_time' => [
                'type' => 'DATETIME',
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
        ]);

        $this->forge->addKey('log_id', true);
        $this->forge->createTable('tbl_h_log_login');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_h_log_login');
    }
}
