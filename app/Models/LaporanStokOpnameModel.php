<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanStokOpnameModel extends Model
{
    protected $table            = 'tbl_h_laporan_stok_opname';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "tgl_mulai",
        "tgl_selesai",
        "filename",
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

    public function getLaporanSoData($limit, $start, $searchValue, $filters = [])
    {
        try {
            $cacheKey = 'getLaporanSoData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
                'filters' => $filters,
            ]));

            $laporans = cache()->get($cacheKey);

            if (!$laporans) {
                $builder = $this->db->table($this->table)
                    ->select("tbl_h_laporan_stok_opname.*, tbl_m_users.nama_lengkap")
                    ->join("tbl_m_users", "tbl_m_users.user_id = tbl_h_laporan_stok_opname.user_created")
                    ->where('tbl_h_laporan_stok_opname.deleted_at', null)
                    ->orderBy('tbl_h_laporan_stok_opname.created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('filename', $searchValue)
                        ->groupEnd();
                }

                // filter dari form
                if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
                    $builder->where('tbl_h_laporan_stok_opname.created_at >= ', date("Y-m-d 00:00:00", strtotime($filters['tgl_mulai'])));
                    $builder->where('tbl_h_laporan_stok_opname.created_at <= ', date("Y-m-d 23:59:59", strtotime($filters['tgl_selesai'])));
                } else {
                    $builder->where('tbl_h_laporan_stok_opname.created_at >= ', date("Y-m-01 00:00:00"));
                    $builder->where('tbl_h_laporan_stok_opname.created_at <= ', date("Y-m-t 23:59:59"));
                }

                $query = $builder->limit($limit, $start)->get();

                $laporans = $query->getResultArray();

                foreach ($laporans as &$laporan) {
                    $laporan['encrypted_id'] = stringEncryptions('encrypt', $laporan['id']);
                }

                cache()->save($cacheKey, $laporan, 600);
            }

            return $laporans;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredLaporanSo($searchValue, $filters = [])
    {
        $cacheKey = 'countFilteredLaporanSo_' . md5(json_encode([
            'search' => $searchValue,
            'filters' => $filters,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->join("tbl_m_users", "tbl_m_users.user_id = tbl_h_laporan_stok_opname.user_created")
                ->where('tbl_h_laporan_stok_opname.deleted_at', null)
                ->orderBy('tbl_h_laporan_stok_opname.created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('filename', $searchValue)
                    ->groupEnd();
            }

            // filter dari form
            if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
                $builder->where('tbl_h_laporan_stok_opname.created_at >= ', date("Y-m-d 00:00:00", strtotime($filters['tgl_mulai'])));
                $builder->where('tbl_h_laporan_stok_opname.created_at <= ', date("Y-m-d 23:59:59", strtotime($filters['tgl_selesai'])));
            } else {
                $builder->where('tbl_h_laporan_stok_opname.created_at >= ', date("Y-m-01 00:00:00"));
                $builder->where('tbl_h_laporan_stok_opname.created_at <= ', date("Y-m-t 23:59:59"));
            }

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllLaporanSo()
    {
        $cacheKey = 'countAllLaporanSo';

        $total = cache()->get($cacheKey);

        if ($total == null) {
            $builder = $this->db->table($this->table)
                ->join("tbl_m_users", "tbl_m_users.user_id = tbl_h_laporan_stok_opname.user_created")
                ->where('tbl_h_laporan_stok_opname.deleted_at', null)
                ->orderBy('tbl_h_laporan_stok_opname.created_at', 'DESC');

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }
}
