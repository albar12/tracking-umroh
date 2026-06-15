<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTableBarangKeluar extends Migration
{
    public function up()
    {
        $fields = [
            'status_process' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],


        ];
        $this->forge->modifyColumn('tbl_t_barang_keluar', $fields);
    }

    public function down()
    {
        $fields = [
            'status_process' => [
                'type' => 'INT',
                'constraint' => 11,
            ],


        ];
        $this->forge->modifyColumn('tbl_t_barang_keluar', $fields);
    }
}
