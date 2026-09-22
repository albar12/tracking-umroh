<?php

namespace App\Models;

use CodeIgniter\Model;

class PekerjaanModel extends Model
{
    protected $table            = 'tbl_m_pekerjaan';
    protected $primaryKey       = 'pekerjaan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "pekerjaan",
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
        'pekerjaan' => 'required',
    ];
    protected $validationMessages   = [
        'pekerjaan' => [
            'required'    => 'Pekerjaan wajib diisi.',
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

    public function getPekerjaanData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getPekerjaanData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue
            ]));

            $datas = cache()->get($cacheKey);

            if (!$datas) {
                $userId            = session()->get('user_id');
                $userLevel         = session()->get('level');

                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('pekerjaan', $searchValue)
                        ->orLike('status', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $datas = $query->getResultArray();

                foreach ($datas as &$data) {
                    $data['encrypted_id'] = stringEncryptions('encrypt', $data['pekerjaan_id']);
                }

                cache()->save($cacheKey, $datas, 600);
            }

            return $datas;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredDatas($searchValue)
    {
        $cacheKey = 'countFilteredDatas_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('pekerjaan', $searchValue)
                    ->orLike('status', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function countAllPekerjaan()
    {
        $cacheKey = 'countAllPekerjaan';

        $total = cache()->get($cacheKey);

        if ($total === null) {

            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null);
            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function getPekerjaanId($id)
    {
        $cacheKey = 'getPekerjaanId_' . $id;

        $data = cache()->get($cacheKey);

        if (!$data) {
            $data = $this->select('*')
                ->where('pekerjaan_id', $id)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return  $data;
    }

    public function cekPekerjaan($pekerjaan)
    {
        return $this->where('pekerjaan', $pekerjaan)->where('deleted_at', null)->countAllResults();
    }

    public function get_all_pekerjaan()
    {
        $cacheKey = 'get_all_pekerjaan';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("pekerjaan_id, pekerjaan")
                ->where("status", "Aktif")
                ->where("deleted_at", null);
            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }
}
