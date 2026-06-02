<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTBarangMasuk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'barang_masuk_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'no_dokument' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'no_dokument_supplier' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'tgl_terima' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jam_terima' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'diserahkan' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'total_produk' => [
                'type' => 'DOUBLE',
                'null' => true,
            ],
            'diterima' => [
                'type' => 'INT',
                'constraint' => 64,
                'null' => true,
            ],
            'status_approval' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'approval_keterangan' => [
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

        $this->forge->addKey('barang_masuk_id', true);
        $this->forge->createTable('tbl_t_barang_masuk');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_t_barang_masuk');
    }
}
