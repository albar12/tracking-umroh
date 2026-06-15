<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrateTableBarangKeluar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'barang_keluar_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'no_dokument' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tgl_keluar' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jam_keluar' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_process' => [
                'type' => 'INT',
                'constraint' => 11,
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

        $this->forge->addKey('barang_keluar_id', true);
        $this->forge->createTable('tbl_t_barang_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_t_barang_keluar');
    }
}
