<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailBarangMasukModel extends Model
{
    protected $table            = 'tbl_t_detail_barang_masuk';
    protected $primaryKey       = 'detail_barang_masuk_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "qty",
        "qty_input",
        "keterangan",
        "produk_id",
        "barang_masuk_id",
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

    // Ambil satu berdasarkan ID
    public function getDetailBarangMasukId($id)
    {
        return $this
            ->select('barang_masuk_id, tbl_m_produk.produk, tbl_t_detail_barang_masuk.qty, tbl_m_produk.produk_id, jenis_sn, qty_input,
                        tbl_t_detail_barang_masuk.keterangan as det_ket, tbl_m_kategori.kategori, tbl_t_detail_barang_masuk.detail_barang_masuk_id,
                        GROUP_CONCAT(peripheral_id) AS cekPeripheral')
            ->join('tbl_m_produk', 'tbl_m_produk.produk_id = tbl_t_detail_barang_masuk.produk_id', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id', 'left')
            ->join('tbl_m_kelengkapan_produk', 'tbl_m_kelengkapan_produk.produk_id=tbl_m_produk.produk_id', 'left')
            ->where('barang_masuk_id', $id)
            ->where('tbl_t_detail_barang_masuk.deleted_at', null)
            ->groupBy('tbl_m_produk.produk_id')
            ->findAll();
    }

    public function getDetailBarangMasukIdProduk($id)
    {
        $builder = $this
            ->select('tbl_m_produk.produk, tbl_t_detail_barang_masuk.qty, tbl_m_produk.produk_id, jenis_sn, qty_input,
                      tbl_t_detail_barang_masuk.keterangan as det_ket, tbl_m_kategori.kategori, tbl_t_detail_barang_masuk.detail_barang_masuk_id')
            ->join('tbl_m_produk', 'tbl_m_produk.produk_id = tbl_t_detail_barang_masuk.produk_id', 'left')
            ->join('tbl_m_kategori', 'tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id', 'left')
            ->where('tbl_t_detail_barang_masuk.barang_masuk_id', $id)
            ->where('jenis_sn', 'Tidak')
            ->where('tbl_t_detail_barang_masuk.deleted_at', null);

        return $builder->findAll();
    }

    // Ambil satu berdasarkan ID
    public function getById($id)
    {
        return $this->where($this->primaryKey, $id)->where('deleted_at', null)->first();
    }

    // Insert data
    public function insertData($data)
    {
        try {
            $this->db->table($this->table)->insert($data);

            if ($this->db->affectedRows() > 0) {
                return $this->db->insertID();
            } else {
                return false;
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Delete berdasarkan ID
    public function deleteData($id, $data)
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $id)
            ->update($data) && $this->db->affectedRows() > 0;
    }

    // Update berdasarkan ID
    public function updateData($id, $data)
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $id)
            ->update($data) && $this->db->affectedRows() > 0;
    }
}
