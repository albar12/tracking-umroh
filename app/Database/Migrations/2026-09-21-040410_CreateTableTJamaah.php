<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTJamaah extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'jamaah_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'paket_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'tgl_berangkat' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tgl_pulang' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'kloter_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status_pembayaran' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'status_dokumen' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'ukuran_seragam' => [
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

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_t_jamaah');
    }

    public function down()
    {
        $this->forge->createTable('tbl_t_jamaah');
    }
}
