<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenModel extends Model
{
    protected $table            = 'tbl_t_dokumen';
    protected $primaryKey       = 'dokumen_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "dokumen",
        "tipe",
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

    public function dokumen_barang_masuk($id)
    {
        $data = $this->where('deleted_at', NULL)->where('barang_masuk_id', $id);

        return $data->findAll();
    }

    // Ambil satu berdasarkan ID
    public function getById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
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

    public function deleteData($id, $data)
    {
        return $this->db->table($this->table)
            ->where($this->primaryKey, $id)
            ->update($data) && $this->db->affectedRows() > 0;
    }
}
