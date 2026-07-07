<?php

namespace App\Models;

use CodeIgniter\Model;

class StokOpnameModel extends Model
{
    protected $table            = 'tbl_t_stok_opname';
    protected $primaryKey       = 'so_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "no_dokument",
        "batch",
        "tgl_mulai",
        "tgl_selesai",
        "produk_so",
        "keterangan",
        "status_approval",
        "keterangan_approval",
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
        'batch' => 'required',
        'tgl_mulai' => 'required',
        'tgl_selesai' => 'required',
    ];
    protected $validationMessages   = [
        'batch' => [
            'required'    => 'Batch wajib diisi.',
        ],
        'tgl_mulai' => [
            'required'    => 'Tanggal Mulai wajib diisi.',
        ],
        'tgl_selesai' => [
            'required'    => 'Tanggal Selesai wajib diisi.',
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

    public function getStokOpnameData($limit, $start, $searchValue, $filters = [])
    {
        try {
            $cacheKey = 'getStokOpnameData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
                'filters' => $filters,
            ]));

            $stokOpnames = cache()->get($cacheKey);

            if (!$stokOpnames) {
                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null);

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('no_dokument', $searchValue)
                        ->orLike('batch', $searchValue)
                        ->orLike('status_approval', $searchValue)
                        ->groupEnd();
                }

                // filter dari form
                if ($filters['tgl_mulai'] && $filters['tgl_selesai']) {
                    $builder->where('tgl_mulai >=', $filters['tgl_mulai']);
                    $builder->where('tgl_selesai <=', $filters['tgl_selesai']);
                }

                $query = $builder->limit($limit, $start)->get();

                $stokOpnames = $query->getResultArray();

                foreach ($stokOpnames as &$stokOpname) {
                    $stokOpname['encrypted_id'] = stringEncryptions('encrypt', $stokOpname['so_id']);
                }

                cache()->save($cacheKey, $stokOpnames, 600);
            }

            return $stokOpnames;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredStokOpname($searchValue, $filters = [])
    {
        $cacheKey = 'countFilteredStokOpname_' . md5(json_encode([
            'search' => $searchValue,
            'filters' => $filters,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null);

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('no_dokument', $searchValue)
                    ->orLike('batch', $searchValue)
                    ->orLike('status_approval', $searchValue)
                    ->groupEnd();
            }

            // filter dari form
            if ($filters['tgl_mulai'] && $filters['tgl_selesai']) {
                $builder->where('tgl_mulai >=', $filters['tgl_mulai']);
                $builder->where('tgl_selesai <=', $filters['tgl_selesai']);
            } else {
                $tgl_mulai = date("Y-m-01");
                $tgl_selesai = date("Y-m-t");
                $builder->where('tgl_mulai >=', $tgl_mulai);
                $builder->where('tgl_selesai <=', $tgl_selesai);
            }

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllStokOpname()
    {
        $cacheKey = 'countAllStokOpname';

        $total = cache()->get($cacheKey);

        if ($total == null) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null);

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }

    public function getStokOpnameId($id)
    {
        $cacheKey = 'getStokOpnameId_' . $id;

        $data = cache()->get($cacheKey);
        if (!$data) {
            $data = $this->where('so_id', $id)
                ->where("deleted_at", null)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekStokOpname($batch)
    {
        return $this->where('batch', $batch)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function generate_no_dokumen()
    {
        $builder = $this->select('no_dokument')
            ->orderBy('so_id', 'DESC')
            ->limit(1);

        $result = $builder->get()->getRow();

        $now = new \DateTime();
        $thnSekarang = $now->format('y');
        $ymd = $now->format('ymd');

        if ($result) {
            $thnKode = substr($result->no_dokument, 1, 2);
            if ($thnKode == $thnSekarang) {
                $date = $thnKode . $now->format('md');
                $nomor = (int) substr($result->no_dokument, 7, 7) + 1;
                $no_dokument = $date . str_pad($nomor, 7, '0', STR_PAD_LEFT);
            } else {
                $no_dokument = $ymd . '0000001';
            }
        } else {
            $no_dokument = $ymd . '0000001';
        }

        return 'SO' . $no_dokument;
    }

    public function get_batch_so()
    {
        $builder = $this->db->table($this->table)
            ->select("so_id, no_dokument, batch")
            ->where('deleted_at', null);

        $query = $builder->get()->getResultArray();

        return $query;
    }
}
