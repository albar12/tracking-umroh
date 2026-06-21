<?php

namespace App\Models;

use CodeIgniter\Model;

class TokoModel extends Model
{
    protected $table            = 'tbl_m_toko';
    protected $primaryKey       = 'toko_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "nama_toko",
        "email",
        "no_telp",
        "alamat",
        "logo",
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
        'nama_toko' => 'required',
        'email' => 'required',
        'no_telp' => 'required',
        'alamat' => 'required',
    ];
    protected $validationMessages   = [
        'nama_toko' => [
            'required'    => 'Nama Outlet wajib diisi.',
        ],
        'email' => [
            'required'    => 'Email Outlet wajib diisi.',
        ],
        'no_telp' => [
            'required'    => 'No Telp Outlet wajib diisi.',
        ],
        'alamat' => [
            'required'    => 'Alamat Outlet wajib diisi.',
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

    public function getTokoData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getTokoData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
            ]));

            $tokos = cache()->get($cacheKey);

            if (!$tokos) {
                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('nama_toko', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $tokos = $query->getResultArray();

                foreach ($tokos as &$toko) {
                    $toko['encrypted_id'] = stringEncryptions('encrypt', $toko['toko_id']);
                }

                cache()->save($cacheKey, $tokos, 600);
            }


            return $tokos;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredToko($searchValue)
    {
        $cacheKey = 'countFilteredToko_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('nama_toko', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllToko()
    {
        $cacheKey = 'countAllToko';

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

    public function getTokoId($id)
    {
        $cacheKey = 'getTokoId_' . $id;

        $data = cache()->get($cacheKey);
        if (!$data) {
            $data = $this->where('toko_id', $id)
                ->where("deleted_at", null)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekToko($supplier)
    {
        return $this->where('nama_toko', $supplier)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function get_all_toko()
    {
        $cacheKey = 'get_all_toko';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("toko_id, nama_toko")
                ->where("status", "Aktif")
                ->where("deleted_at", null);

            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function getTokoFirst()
    {
        $data = $this->where("deleted_at", null)
            ->first();
        return $data;
    }
}
