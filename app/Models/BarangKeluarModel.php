<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangKeluarModel extends Model
{
    protected $table            = 'tbl_t_barang_keluar';
    protected $primaryKey       = 'barang_keluar_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "barang_keluar_id",
        "no_dokument",
        "tgl_keluar",
        "jam_keluar",
        "keterangan",
        "metode_pembayaran",
        "total_harga",
        "nominal_bayar",
        "nominal_kembalian",
        "status_process",
        "status",
        "user_created",
        "user_updated",
        "user_deleted",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

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

    public function getBarangKeluarData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getBarangKeluarData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
            ]));

            $barangKeluars = cache()->get($cacheKey);

            if (!$barangKeluars) {
                $builder = $this->db->table($this->table)
                    ->select("tbl_t_barang_keluar.*, tbl_m_users.nama_lengkap, tbl_m_metode_pembayaran.metode")
                    ->join("tbl_m_users", "tbl_m_users.user_id = tbl_t_barang_keluar.user_created")
                    ->join("tbl_m_metode_pembayaran", "tbl_m_metode_pembayaran.metode_id = tbl_t_barang_keluar.metode_pembayaran")
                    ->where('tbl_t_barang_keluar.deleted_at', null)
                    ->orderBy('tbl_t_barang_keluar.created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('no_dokument', $searchValue)
                        ->orLike('tbl_t_barang_keluar.tgl_keluar', $searchValue)
                        ->orLike('tbl_t_barang_keluar.jam_keluar', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $barangKeluars = $query->getResultArray();

                foreach ($barangKeluars as &$barangKeluar) {
                    $barangKeluar['encrypted_id'] = stringEncryptions('encrypt', $barangKeluar['barang_keluar_id']);
                }

                cache()->save($cacheKey, $barangKeluars, 600);
            }


            return $barangKeluars;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredBarangKeluar($searchValue)
    {
        $cacheKey = 'countFilteredBarangKeluar_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->select("tbl_t_barang_keluar.*,  tbl_m_users.nama_lengkap")
                ->join("tbl_m_users", "tbl_m_users.user_id = tbl_t_barang_keluar.user_created")
                ->where('tbl_t_barang_keluar.deleted_at', null)
                ->orderBy('tbl_t_barang_keluar.created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('no_dokument', $searchValue)
                    ->orLike('tbl_t_barang_keluar.tgl_keluar', $searchValue)
                    ->orLike('tbl_t_barang_keluar.jam_keluar', $searchValue)
                    ->groupEnd();
            }


            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllBarangKeluar()
    {
        $cacheKey = 'countAllBarangKeluar';

        $total = cache()->get($cacheKey);

        if ($total == null) {
            $builder = $this->db->table($this->table)
                ->select("tbl_t_barang_keluar.*, tbl_m_users.nama_lengkap")
                ->join("tbl_m_users", "tbl_m_users.user_id = tbl_t_barang_keluar.user_created")
                ->where('tbl_t_barang_keluar.deleted_at', null)
                ->orderBy('tbl_t_barang_keluar.created_at', 'DESC');

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }

    public function getBarangKeluarId($id)
    {
        $cacheKey = 'getBarangKeluarId_' . $id;

        $data = cache()->get($cacheKey);
        if (!$data) {
            $data = $this->db->table($this->table)
                ->select("tbl_t_barang_keluar.*, tbl_m_users.nama_lengkap, tbl_m_metode_pembayaran.metode")
                ->join("tbl_m_users", "tbl_m_users.user_id = tbl_t_barang_keluar.user_created")
                ->join("tbl_m_metode_pembayaran", "tbl_m_metode_pembayaran.metode_id = tbl_t_barang_keluar.metode_pembayaran")
                ->where('barang_keluar_id', $id)
                ->where("tbl_t_barang_keluar.deleted_at", null)
                ->get()
                ->getRowArray();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function generate_no_dokumen()
    {
        $builder = $this->select('no_dokument')
            ->orderBy('barang_keluar_id', 'DESC')
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

        return 'K' . $no_dokument;
    }

    public function getLaporanBarangKeluar($filters = [])
    {
        $builder = $this->db->table("tbl_t_barang_keluar")
            ->select("tbl_t_barang_keluar.barang_keluar_id, no_dokument, tgl_keluar, produk, kategori, tbl_t_detail_barang_keluar.qty, tbl_t_detail_barang_keluar.harga_jual, tbl_t_detail_barang_keluar.total_harga")
            ->join("tbl_t_detail_barang_keluar", "tbl_t_detail_barang_keluar.barang_keluar_id = tbl_t_barang_keluar.barang_keluar_id")
            ->join("tbl_m_produk", "tbl_m_produk.produk_id = tbl_t_detail_barang_keluar.produk_id", 'left')
            ->join("tbl_m_kategori", "tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id", 'left')
            ->where("tgl_keluar >=", $filters['tgl_mulai'])
            ->where("tgl_keluar <=", $filters['tgl_selesai'])
            ->where("tbl_t_barang_keluar.deleted_at", null)
            ->where("tbl_t_detail_barang_keluar.deleted_at", null);
        $query = $builder->get();

        $data = $query->getResultArray();

        return $data;
    }
}
