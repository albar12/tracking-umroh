<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTableMProduk extends Migration
{
    public function up()
    {
        $fields = [

            'ketegori_id' => [
                'name' => 'kategori_id',
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],

        ];

        $this->forge->modifyColumn(
            'tbl_m_produk',
            $fields
        );
    }

    public function down()
    {
        $fields = [
            'kategori_id' => [
                'name' => 'ketegori_id',
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],

        ];

        $this->forge->modifyColumn(
            'tbl_m_produk',
            $fields
        );
    }
}
