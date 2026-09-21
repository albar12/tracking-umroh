<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMRiwayatPenyakit extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'riwayat_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'riwayat_penyakit_khusus' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
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

        $this->forge->addKey('riwayat_id', true);
        $this->forge->createTable('tbl_m_riwayat_penyakit');
    }

    public function down()
    {
        $this->forge->createTable('tbl_m_riwayat_penyakit');
    }
}
