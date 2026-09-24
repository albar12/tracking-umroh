<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusDokumenModel extends Model
{
    protected $table            = 'tbl_m_status_dokumen';
    protected $primaryKey       = 'status_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "nama_status",
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
        'nama_status' => 'required',
    ];
    protected $validationMessages   = [
        'nama_status' => [
            'required'    => 'Nama Status wajib diisi.',
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

    public function getStatusDokumenData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getStatusDokumenData_' . md5(json_encode([
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
                        ->like('nama_status', $searchValue)
                        ->orLike('status', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $datas = $query->getResultArray();

                foreach ($datas as &$data) {
                    $data['encrypted_id'] = stringEncryptions('encrypt', $data['status_id']);
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

    public function countFilteredStatusDokumen($searchValue)
    {
        $cacheKey = 'countFilteredStatusDokumen_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('nama_status', $searchValue)
                    ->orLike('status', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function countAllStatusDokumen()
    {
        $cacheKey = 'countAllStatusDokumen';

        $total = cache()->get($cacheKey);

        if ($total === null) {

            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null);
            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function getStatusDokumenId($id)
    {
        $cacheKey = 'getStatusDokumenId_' . $id;

        $data = cache()->get($cacheKey);

        if (!$data) {
            $data = $this->select('*')
                ->where('status_id', $id)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return  $data;
    }

    public function cekStatusDokumen($pekerjaan)
    {
        return $this->where('nama_status', $pekerjaan)->where('deleted_at', null)->countAllResults();
    }

    public function get_all_status_dokumen()
    {
        $cacheKey = 'get_all_status_dokumen';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("status_id, nama_status")
                ->where("status", "Aktif")
                ->where("deleted_at", null);
            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }
}
