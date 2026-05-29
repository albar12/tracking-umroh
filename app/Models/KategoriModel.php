<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'tbl_m_kategori';
    protected $primaryKey       = 'kategori_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "kategori_id",
        "kategori",
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
        'kategori' => 'required',
    ];
    protected $validationMessages   = [
        'kategori' => [
            'required'    => 'Kategori wajib diisi.',
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

    public function getKategoriData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getKategoriData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
            ]));

            $kategoris = cache()->get($cacheKey);

            if (!$kategoris) {
                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('kategori', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $kategoris = $query->getResultArray();

                foreach ($kategoris as &$kategori) {
                    $kategori['encrypted_id'] = stringEncryptions('encrypt', $kategori['kategori_id']);
                }

                cache()->save($cacheKey, $kategoris, 600);
            }

            return $kategoris;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredKategori($searchValue)
    {
        $cacheKey = 'countFilteredKategori_' . md5(json_encode([
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

            $total =  $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }


        return $total;
    }

    public function countAllKategori()
    {
        $cacheKey = 'countAllKategori';

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

    public function getKategoriId($id)
    {
        $cacheKey = 'getKategoriId_' . $id;

        $data = cache()->get($cacheKey);
        if (!$data) {
            $data = $this->where('kategori_id', $id)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekKategori($kategori)
    {
        return $this->where('kategori', $kategori)->where('deleted_at', null)->countAllResults();
    }

    public function get_all_ketgori()
    {
        $cacheKey = 'get_all_ketgori';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("kategori_id, kategori")
                ->where("status", "Aktif")
                ->where("deleted_at", null);
            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }

        return $data;
    }
}
