<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableMDokumentPaspor extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'paspor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'jamaah_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'nomor_paspor' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nama_di_paspor' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tgl_dikeluarkan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tgl_berakhir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'kantor_imigrasi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_vaksinasi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'foto_ktp' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'foto_paspor' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'foto_buku_nikah' => [
                'type' => 'TEXT',
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

        $this->forge->addKey('paspor_id', true);
        $this->forge->createTable('tbl_t_dokumen_paspor');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_t_dokumen_paspor');
    }
}
