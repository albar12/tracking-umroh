<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatPenyakitModel extends Model
{
    protected $table            = 'tbl_m_riwayat_penyakit';
    protected $primaryKey       = 'riwayat_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "riwayat_penyakit_khusus",
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
        'riwayat_penyakit_khusus' => 'required',
    ];
    protected $validationMessages   = [
        'riwayat_penyakit_khusus' => [
            'required'    => 'Riwayat Penyakit Khusus wajib diisi.',
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

    public function getRiwayatPenyakitData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getRiwayatPenyakitData_' . md5(json_encode([
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
                        ->like('riwayat_penyakit_khusus', $searchValue)
                        ->orLike('status', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $datas = $query->getResultArray();

                foreach ($datas as &$data) {
                    $data['encrypted_id'] = stringEncryptions('encrypt', $data['riwayat_id']);
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

    public function countFilteredRiwayatPenyakit($searchValue)
    {
        $cacheKey = 'countFilteredRiwayatPenyakit_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('riwayat_penyakit_khusus', $searchValue)
                    ->orLike('status', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function countAllRiwayatPenyakit()
    {
        $cacheKey = 'countAllRiwayatPenyakit';

        $total = cache()->get($cacheKey);

        if ($total === null) {

            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null);
            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function getRiwayatPenyakitId($id)
    {
        $cacheKey = 'getRiwayatPenyakitId_' . $id;

        $data = cache()->get($cacheKey);

        if (!$data) {
            $data = $this->select('*')
                ->where('riwayat_id', $id)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return  $data;
    }

    public function cekRiwayatPenyakit($riwayat_penyakit_khusus)
    {
        return $this->where('riwayat_penyakit_khusus', $riwayat_penyakit_khusus)->where('deleted_at', null)->countAllResults();
    }

    public function get_all_riwayat_penyakit()
    {
        $cacheKey = 'get_all_riwayat_penyakit';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("riwayat_id, riwayat_penyakit_khusus")
                ->where("status", "Aktif")
                ->where("deleted_at", null);
            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }
}
