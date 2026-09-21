<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTKesehatan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kesehatan_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'jamaah_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'riwayat_penyakit_khusus' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'alat_bantu' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
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

        $this->forge->addKey('kesehatan_id', true);
        $this->forge->createTable('tbl_t_kesehatan');
    }

    public function down()
    {
        $this->forge->createTable('tbl_t_kesehatan');
    }
}
