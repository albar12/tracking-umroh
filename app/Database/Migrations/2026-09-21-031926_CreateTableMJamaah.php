<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMJamaah extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'jamaah_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_lengkap' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nama_panggilan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nik_ktp' => [
                'type' => 'VARCHAR',
                'constraint' => 16,
                'null' => true,
            ],
            'jenis_kelamin' => [
                'type' => 'ENUM',
                'constraint' => ['Laki-laki', 'Perempuan'],
                'default' => null,
            ],
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'tgl_lahir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'password' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'no_tlp' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'pekerjaan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'token_fcm' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'access_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'refresh_token' => [
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

        $this->forge->addKey('jamaah_id', true);
        $this->forge->createTable('tbl_m_jamaah');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_jamaah');
    }
}
