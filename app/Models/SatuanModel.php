<?php

namespace App\Models;

use CodeIgniter\Model;

class SatuanModel extends Model
{
    protected $table            = 'tbl_m_satuan';
    protected $primaryKey       = 'satuan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "satuan",
        "qty",
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
        'satuan' => 'required',
        'qty' => 'required',
    ];
    protected $validationMessages   = [
        'satuan' => [
            'required'    => 'Satuan wajib diisi.',
        ],
        'qty' => [
            'required'    => 'Qty wajib diisi.',
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

    public function getSatuanData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getSatuanData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
            ]));

            $satuans = cache()->get($cacheKey);

            if (!$satuans) {
                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('satuan', $searchValue)
                        ->orLike('qty', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $satuans = $query->getResultArray();

                foreach ($satuans as &$satuan) {
                    $satuan['encrypted_id'] = stringEncryptions('encrypt', $satuan['satuan_id']);
                }
                cache()->save($cacheKey, $satuans, 600);
            }

            return $satuans;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredSatuan($searchValue)
    {
        $cacheKey = 'countFilteredSatuan_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('satuan', $searchValue)
                    ->orLike('qty', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }

    public function countAllSatuan()
    {
        $cacheKey = 'countAllSatuan';

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

    public function getSatuanId($id)
    {
        $cacheKey = 'getSatuanId_' . $id;

        $data = cache()->get($cacheKey);

        if (!$data) {
            $data = $this->where('satuan_id', $id)
                ->where("deleted_at", null)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekSatuan($satuan, $qty)
    {

        return $this->where('satuan', $satuan)
            ->where("qty", $qty)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function get_all_satuan()
    {
        $cacheKey = 'get_all_satuan';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("satuan_id, satuan, qty")
                ->where("status", "Aktif")
                ->where("deleted_at", null);

            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }
}
