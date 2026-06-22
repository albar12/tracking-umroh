<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasukModel extends Model
{
    protected $table            = 'tbl_t_barang_masuk';
    protected $primaryKey       = 'barang_masuk_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "no_dokument",
        "no_dokument_supplier",
        "tgl_terima",
        "jam_terima",
        "keterangan",
        "diserahkan",
        "total_produk",
        "diterima",
        "status_approval",
        "approval_keterangan",
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

    public function getBarangMasukData($limit, $start, $searchValue, $filters = [])
    {
        try {
            $cacheKey = 'getBarangMasukData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
                'filters' => $filters,
            ]));

            $barangMasuks = cache()->get($cacheKey);

            if (!$barangMasuks) {
                $builder = $this->db->table($this->table)
                    ->select("tbl_t_barang_masuk.*, tbl_m_supplier.supplier, tbl_m_users.nama_lengkap")
                    ->join("tbl_m_users", "tbl_m_users.user_id = tbl_t_barang_masuk.diterima")
                    ->join("tbl_m_supplier", "tbl_m_supplier.supplier_id = tbl_t_barang_masuk.diserahkan")
                    ->where('tbl_t_barang_masuk.deleted_at', null)
                    ->orderBy('tbl_t_barang_masuk.created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('no_dokument', $searchValue)
                        ->orLike('tbl_m_supplier.supplier', $searchValue)
                        ->orLike('tbl_t_barang_masuk.tgl_terima', $searchValue)
                        ->orLike('tbl_t_barang_masuk.jam_terima', $searchValue)
                        ->groupEnd();
                }

                // filter dari form
                if (!empty($filters['supplier_id'])) {
                    $builder->where('tbl_t_barang_masuk.diserahkan', $filters['supplier_id']);
                }

                $query = $builder->limit($limit, $start)->get();
                $barangMasuks = $query->getResultArray();

                foreach ($barangMasuks as &$barangMasuk) {
                    $barangMasuk['encrypted_id'] = stringEncryptions('encrypt', $barangMasuk['barang_masuk_id']);
                }

                cache()->save($cacheKey, $barangMasuks, 600);
            }


            return $barangMasuks;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredBarangMasuk($searchValue, $filters = [])
    {
        $cacheKey = 'countFilteredBarangMasuk_' . md5(json_encode([
            'search' => $searchValue,
            'filters' => $filters,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->select("tbl_t_barang_masuk.*, tbl_m_supplier.supplier, tbl_m_users.nama_lengkap")
                ->join("tbl_m_users", "tbl_m_users.user_id = tbl_t_barang_masuk.diterima")
                ->join("tbl_m_supplier", "tbl_m_supplier.supplier_id = tbl_t_barang_masuk.diserahkan")
                ->where('tbl_t_barang_masuk.deleted_at', null)
                ->orderBy('tbl_t_barang_masuk.created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('no_dokument', $searchValue)
                    ->orLike('tbl_m_supplier.supplier', $searchValue)
                    ->orLike('tbl_t_barang_masuk.tgl_terima', $searchValue)
                    ->orLike('tbl_t_barang_masuk.jam_terima', $searchValue)
                    ->groupEnd();
            }

            // filter dari form
            if (!empty($filters['supplier_id'])) {
                $builder->where('tbl_t_barang_masuk.diserahkan', $filters['supplier_id']);
            }

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllBarangMasuk()
    {
        $cacheKey = 'countAllBarangMasuk';

        $total = cache()->get($cacheKey);

        if ($total == null) {
            $builder = $this->db->table($this->table)
                ->select("tbl_t_barang_masuk.*, tbl_m_supplier.supplier, tbl_m_users.nama_lengkap")
                ->join("tbl_m_users", "tbl_m_users.user_id = tbl_t_barang_masuk.diterima")
                ->join("tbl_m_supplier", "tbl_m_supplier.supplier_id = tbl_t_barang_masuk.diserahkan")
                ->where('tbl_t_barang_masuk.deleted_at', null)
                ->orderBy('tbl_t_barang_masuk.created_at', 'DESC');

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }

    public function getBarangMasukId($id)
    {
        $cacheKey = 'getBarangMasukId_' . $id;

        $data = cache()->get($cacheKey);
        if (!$data) {
            $data = $this->where('barang_masuk_id', $id)
                ->where("deleted_at", null)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekBarangMasuk($no_dok_supplier, $diserahkan)
    {
        return $this->where('no_dokument_supplier', $no_dok_supplier)
            ->where("diserahkan", $diserahkan)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function generate_no_dokumen()
    {
        $builder = $this->select('no_dokument')
            ->orderBy('barang_masuk_id', 'DESC')
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

        return 'B' . $no_dokument;
    }

    public function getLaporanBarangMasuk($filters = [])
    {
        $builder = $this->db->table("tbl_t_barang_masuk")
            ->select("tbl_t_barang_masuk.barang_masuk_id, no_dokument, tgl_terima, produk, kategori, tbl_t_detail_barang_masuk.qty_input, supplier")
            ->join("tbl_t_detail_barang_masuk", "tbl_t_detail_barang_masuk.barang_masuk_id = tbl_t_barang_masuk.barang_masuk_id")
            ->join("tbl_m_supplier", "tbl_m_supplier.supplier_id = tbl_t_barang_masuk.diserahkan", 'left')
            ->join("tbl_m_produk", "tbl_m_produk.produk_id = tbl_t_detail_barang_masuk.produk_id", 'left')
            ->join("tbl_m_kategori", "tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id", 'left')
            ->where("tgl_terima >=", $filters['tgl_mulai'])
            ->where("tgl_terima <=", $filters['tgl_selesai'])
            ->where("tbl_t_barang_masuk.deleted_at", null)
            ->where("tbl_t_detail_barang_masuk.deleted_at", null);
        $query = $builder->get();

        $data = $query->getResultArray();

        return $data;
    }
}
