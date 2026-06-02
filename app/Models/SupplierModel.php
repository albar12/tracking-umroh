<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'tbl_m_supplier';
    protected $primaryKey       = 'supplier_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "supplier",
        "alamat",
        "tlp_supplier",
        "status",
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
    protected $validationRules      = [
        'supplier' => 'required',
        'alamat' => 'required',
        'tlp_supplier' => 'required',
    ];
    protected $validationMessages   = [
        'supplier' => [
            'required'    => 'Supplier wajib diisi.',
        ],
        'alamat' => [
            'required'    => 'Alamat wajib diisi.',
        ],
        'tlp_supplier' => [
            'required'    => 'Tlp Supplier wajib diisi.',
        ],
    ];
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

    public function getSupplierData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getSupplierData' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
            ]));

            $suppliers = cache()->get($cacheKey);

            if (!$suppliers) {
                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('kategori', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $suppliers = $query->getResultArray();

                foreach ($suppliers as &$supplier) {
                    $supplier['encrypted_id'] = stringEncryptions('encrypt', $supplier['supplier_id']);
                }

                cache()->save($cacheKey, $suppliers, 600);
            }


            return $suppliers;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredSupplier($searchValue)
    {
        $cacheKey = 'countFilteredSupplier_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('kategori', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllSupplier()
    {
        $cacheKey = 'countAllSupplier';

        $total = cache()->get($cacheKey);

        if ($total == null) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }

    public function getSupplierId($id)
    {
        $cacheKey = 'getSupplierId_' . $id;

        $data = cache()->get($cacheKey);
        if (!$data) {
            $data = $this->where('supplier_id', $id)
                ->where("deleted_at", null)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekSupplier($supplier)
    {
        return $this->where('supplier', $supplier)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function get_all_supplier()
    {
        $cacheKey = 'get_all_supplier';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("supplier_id, supplier")
                ->where("status", "Aktif")
                ->where("deleted_at", null);

            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }
}
