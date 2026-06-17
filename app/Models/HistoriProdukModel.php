<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriProdukModel extends Model
{
    protected $table            = 'tbl_h_produk';
    protected $primaryKey       = 'histori_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "kode_transaksi",
        "tipe",
        "qty_in",
        "qty_out",
        "barang_masuk_id",
        "detail_barang_masuk_id",
        "barang_keluar_id",
        "detail_barang_keluar_id",
        "user_id",
        "produk_id",
        "barcode_value",
        "tgl_expired",
        "keterangan",
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

    public function getTotalBarangMasuk($filters = [])
    {
        try {
            $cacheKey = 'getTotalBarangMasuk_' . md5(json_encode([
                'filters' => $filters,
            ]));

            $total = cache()->get($cacheKey);

            if (!$total) {
                $builder = $this->db->table($this->table)
                    ->select("COALESCE(SUM(qty_in), 0) AS total_masuk")
                    ->join("tbl_t_barang_masuk", "tbl_t_barang_masuk.barang_masuk_id = tbl_h_produk.barang_masuk_id")
                    ->where("tipe", "IN")
                    ->where('tbl_h_produk.deleted_at', null)
                    ->where('tbl_t_barang_masuk.deleted_at', null);

                // filter dari form
                if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
                    $startDate = date("Y-m-d", strtotime($filters['tgl_mulai']));
                    $endDate = date("Y-m-d", strtotime($filters['tgl_selesai']));

                    $builder->where('tbl_t_barang_masuk.tgl_terima >=', $startDate);
                    $builder->where('tbl_t_barang_masuk.tgl_terima <=', $endDate);
                } else {
                    $startDate = date("Y-m-01 00:00:00");
                    $endDate = date("Y-m-t 23:59:59");

                    $builder->where('tbl_t_barang_masuk.tgl_terima >=', $startDate);
                    $builder->where('tbl_t_barang_masuk.tgl_terima <=', $endDate);
                }

                $query = $builder->get();
                $total = $query->getRowArray();

                cache()->save($cacheKey, $total, 600);
            }

            return $total;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function getTotalBarangKeluar($filters = [])
    {
        try {
            $cacheKey = 'getTotalBarangKeluar_' . md5(json_encode([
                'filters' => $filters,
            ]));

            $total = cache()->get($cacheKey);

            if (!$total) {
                $builder = $this->db->table($this->table)
                    ->select("COALESCE(SUM(qty_out), 0) AS total_keluar")
                    ->join("tbl_t_barang_keluar", "tbl_t_barang_keluar.barang_keluar_id = tbl_h_produk.barang_keluar_id")
                    ->where("tipe", "OUT")
                    ->where('tbl_h_produk.deleted_at', null)
                    ->where('tbl_t_barang_keluar.deleted_at', null);

                // filter dari form
                if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
                    $startDate = date("Y-m-d", strtotime($filters['tgl_mulai']));
                    $endDate = date("Y-m-d", strtotime($filters['tgl_selesai']));

                    $builder->where('tbl_t_barang_keluar.tgl_keluar >=', $startDate);
                    $builder->where('tbl_t_barang_keluar.tgl_keluar <=', $endDate);
                } else {
                    $startDate = date("Y-m-01 00:00:00");
                    $endDate = date("Y-m-t 23:59:59");

                    $builder->where('tbl_t_barang_keluar.tgl_keluar >=', $startDate);
                    $builder->where('tbl_t_barang_keluar.tgl_keluar <=', $endDate);
                }

                $query = $builder->get();
                $total = $query->getRowArray();

                cache()->save($cacheKey, $total, 600);
            }

            return $total;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function getOmzet($filters = [])
    {
        try {
            $cacheKey = 'getOmzet_' . md5(json_encode([
                'filters' => $filters,
            ]));

            $total = cache()->get($cacheKey);

            if (!$total) {
                $builder = $this->db->table("tbl_t_barang_keluar")
                    ->select("SUM(total_harga) AS total_omzet")
                    ->where('deleted_at', null);

                // filter dari form
                if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
                    $startDate = date("Y-m-d", strtotime($filters['tgl_mulai']));
                    $endDate = date("Y-m-d", strtotime($filters['tgl_selesai']));

                    $builder->where('tgl_keluar >=', $startDate);
                    $builder->where('tgl_keluar <=', $endDate);
                } else {
                    $startDate = date("Y-m-01 00:00:00");
                    $endDate = date("Y-m-t 23:59:59");

                    $builder->where('tgl_keluar >=', $startDate);
                    $builder->where('tgl_keluar <=', $endDate);
                }

                $query = $builder->get();
                $total = $query->getRowArray();

                cache()->save($cacheKey, $total, 600);
            }

            return $total;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function getOmzetChart($filters = [])
    {
        try {
            $cacheKey = 'getOmzetChart_' . md5(json_encode([
                'filters' => $filters,
            ]));


            $dataBulanan = cache()->get($cacheKey);

            if (!$dataBulanan) {
                $builder = $this->db->table('tbl_t_barang_keluar')
                    ->select('MONTH(tgl_keluar) as bulan, COALESCE(SUM(total_harga), 0) as total')
                    ->where('deleted_at', null)
                    ->groupBy('MONTH(tgl_keluar)')
                    ->orderBy('MONTH(tgl_keluar)', 'ASC');

                // filter dari form
                if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
                    $tahun = date("Y", strtotime($filters['tgl_mulai']));

                    $builder->where('YEAR(tgl_keluar)', $tahun);
                } else {
                    $tahun = date("Y");
                    $builder->where('YEAR(tgl_keluar)', $tahun);
                }

                $query = $builder->get();
                $hasilQuery = $query->getResultArray();

                // Petakan hasil query ke dalam array 12 bulan (Jan - Des) penuh
                $dataBulanan = array_fill(1, 12, 0); // Membuat array [1 => 0, 2 => 0, ..., 12 => 0]

                foreach ($hasilQuery as $row) {
                    $dataBulanan[(int)$row['bulan']] = $row['total'];
                }

                cache()->save($cacheKey, $dataBulanan, 600);
            }

            return $dataBulanan;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function getBarangChart($filters = [])
    {
        try {
            $cacheKey = 'getBarangChart_' . md5(json_encode([
                'filters' => $filters,
            ]));


            $dataBarang = cache()->get($cacheKey);

            if (!$dataBarang) {
                $builder = $this->db->table('tbl_m_produk')
                    ->select('tbl_m_produk.produk, COALESCE(SUM(qty_out), 0) as total_keluar')
                    ->join("tbl_h_produk", "tbl_h_produk.produk_id = tbl_m_produk.produk_id")
                    ->join("tbl_t_barang_keluar", "tbl_h_produk.barang_keluar_id = tbl_t_barang_keluar.barang_keluar_id")
                    ->where('tbl_h_produk.tipe', "OUT")
                    ->where('tbl_m_produk.deleted_at', null)
                    ->where('tbl_h_produk.deleted_at', null)
                    ->where('tbl_t_barang_keluar.deleted_at', null)
                    ->groupBy('tbl_m_produk.produk_id');

                // filter dari form
                if (!empty($filters['tgl_mulai']) && !empty($filters['tgl_selesai'])) {
                    $startDate = date("Y-m-d", strtotime($filters['tgl_mulai']));
                    $endDate = date("Y-m-d", strtotime($filters['tgl_selesai']));

                    $builder->where('tbl_t_barang_keluar.tgl_keluar >=', $startDate);
                    $builder->where('tbl_t_barang_keluar.tgl_keluar <=', $endDate);
                } else {
                    $startDate = date("Y-m-01 00:00:00");
                    $endDate = date("Y-m-t 23:59:59");

                    $builder->where('tbl_t_barang_keluar.tgl_keluar >=', $startDate);
                    $builder->where('tbl_t_barang_keluar.tgl_keluar <=', $endDate);
                }

                $query = $builder->get();
                $dataBarang = $query->getResultArray();

                cache()->save($cacheKey, $dataBarang, 600);
            }

            return $dataBarang;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function getStokData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getStokData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
            ]));

            $datas = cache()->get($cacheKey);

            if (!$datas) {
                $builder = $this->db->table("tbl_m_produk")
                    ->select("tbl_m_produk.produk_id, produk, COALESCE(SUM(qty_in), 0) as stok_masuk, COALESCE(SUM(qty_out), 0) as stok_keluar, 
                COALESCE(SUM(tbl_h_produk.qty_in), 0) - COALESCE(SUM(tbl_h_produk.qty_out), 0) AS stok_tersedia")
                    ->join("tbl_h_produk", "tbl_h_produk.produk_id = tbl_m_produk.produk_id")
                    ->where("tbl_m_produk.deleted_at", null)
                    ->where('tbl_h_produk.deleted_at', null)
                    ->groupBy('tbl_h_produk.produk_id');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('produk', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $datas = $query->getResultArray();


                foreach ($datas as &$data) {
                    $data['encrypted_id'] = stringEncryptions('encrypt', $data['produk_id']);
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

    public function countFilteredStok($searchValue)
    {
        $cacheKey = 'countFilteredStok_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table("tbl_m_produk")
                ->select("tbl_m_produk.*")
                ->join("tbl_h_produk", "tbl_h_produk.produk_id = tbl_m_produk.produk_id")
                ->where("tbl_m_produk.deleted_at", null)
                ->where('tbl_h_produk.deleted_at', null)
                ->groupBy('tbl_h_produk.produk_id');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('produk', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->get()->getNumRows();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllStok()
    {
        $cacheKey = 'countAllStok';

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table("tbl_m_produk")
                ->select("tbl_m_produk.*")
                ->join("tbl_h_produk", "tbl_h_produk.produk_id = tbl_m_produk.produk_id")
                ->where("tbl_m_produk.deleted_at", null)
                ->where('tbl_h_produk.deleted_at', null)
                ->groupBy('tbl_h_produk.produk_id');

            $query = $builder->get();

            $total = $query->getNumRows();
        }

        return $total;
    }
}
