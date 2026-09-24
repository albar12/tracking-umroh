<?php

namespace App\Models;

use CodeIgniter\Model;

class HubunganKeluargaModel extends Model
{
    protected $table            = 'tbl_m_hubungan';
    protected $primaryKey       = 'hubungan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "hubungan",
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
        'hubungan' => 'required',
    ];
    protected $validationMessages   = [
        'hubungan' => [
            'required'    => 'Hubungan Keluarga wajib diisi.',
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

    public function getHubunganKeluargaData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getHubunganKeluargaData_' . md5(json_encode([
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
                        ->like('hubungan', $searchValue)
                        ->orLike('status', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $datas = $query->getResultArray();

                foreach ($datas as &$data) {
                    $data['encrypted_id'] = stringEncryptions('encrypt', $data['hubungan_id']);
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

    public function countFilteredHubunganKeluarga($searchValue)
    {
        $cacheKey = 'countFilteredHubunganKeluarga_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('hubungan', $searchValue)
                    ->orLike('status', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function countAllHubunganKeluarga()
    {
        $cacheKey = 'countAllHubunganKeluarga';

        $total = cache()->get($cacheKey);

        if ($total === null) {

            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null);
            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function getHubunganKeluargaId($id)
    {
        $cacheKey = 'getHubunganKeluargaId_' . $id;

        $data = cache()->get($cacheKey);

        if (!$data) {
            $data = $this->select('*')
                ->where('hubungan_id', $id)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return  $data;
    }

    public function cekHubunganKeluarga($hubungan)
    {
        return $this->where('hubungan', $hubungan)->where('deleted_at', null)->countAllResults();
    }

    public function get_all_hubungan_keluarga()
    {
        $cacheKey = 'get_all_hubungan_keluarga';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("hubungan_id, hubungan")
                ->where("status", "Aktif")
                ->where("deleted_at", null);
            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }
}
