<?php

namespace App\Models;

use CodeIgniter\Model;

class BarcodeValueModel extends Model
{
    protected $table            = 'tbl_t_barcode_value';
    protected $primaryKey       = 'barcode_value_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "barang_masuk_id",
        "detail_barang_masuk_id",
        "produk_id",
        "barcode_value",
        "tgl_expired",
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

    public function getBarcodeBarangMasuk($barang_masuk_id)
    {
        return $this->db->table('tbl_t_barcode_value')
            ->select('tbl_t_barcode_value.*, tbl_m_produk.produk, tbl_m_kategori.kategori, tbl_t_detail_barang_masuk.qty_input')
            ->join("tbl_m_produk", "tbl_m_produk.produk_id = tbl_t_barcode_value.produk_id", "left")
            ->join("tbl_m_kategori", "tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id", "left")
            ->join("tbl_t_detail_barang_masuk", "tbl_t_detail_barang_masuk.detail_barang_masuk_id = tbl_t_barcode_value.detail_barang_masuk_id", "left")
            ->where('tbl_t_barcode_value.barang_masuk_id', $barang_masuk_id)
            ->where('tbl_t_barcode_value.deleted_at', null)
            ->get()
            ->getResultArray();
    }
}
