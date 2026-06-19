<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailBarangKeluarModel extends Model
{
    protected $table            = 'tbl_t_detail_barang_keluar';
    protected $primaryKey       = 'detail_barang_keluar_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "barcode_value",
        "qty",
        "harga_jual",
        "total_harga",
        "produk_id",
        "barang_keluar_id",
        "user_created",
        "user_updated",
        "user_deleted",
        "created_at",
        "updated_at",
        "deleted_at",

    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getDetailBarangKeluarId($id)
    {
        return $this
            ->select('tbl_t_detail_barang_keluar.*, tbl_m_produk.produk,  tbl_m_kategori.kategori, tbl_t_detail_barang_keluar.harga_jual, total_harga')
            ->join('tbl_m_produk', 'tbl_m_produk.produk_id = tbl_t_detail_barang_keluar.produk_id', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id', 'left')
            ->where('barang_keluar_id', $id)
            ->where('tbl_t_detail_barang_keluar.deleted_at', null)
            ->findAll();
    }
}
