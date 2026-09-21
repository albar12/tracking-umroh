<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMPaketUmroh extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'paket_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'jamaah_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'nama_paket' => [
                'type' => 'TEXT',
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
            'muthawif_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'jenis_kamar' => [
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

        $this->forge->addKey('paket_id', true);
        $this->forge->createTable('tbl_t_paket_umroh');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_t_paket_umroh');
    }
}
