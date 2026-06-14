<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'tbl_m_produk';
    protected $primaryKey       = 'produk_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "kategori_id",
        "produk",
        "deskripsi_produk",
        "harga_jual",
        "produk_barang",
        "produk_expired",
        "satuan_id",
        "barcode_value",
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
        'kategori_id' => 'required',
        'deskripsi_produk' => 'required',
        'harga_jual' => 'required',
        'produk_barang' => 'required',
        'produk_expired' => 'required',
        'satuan_id' => 'required',
        // 'barcode_value' => 'required',
    ];
    protected $validationMessages   = [
        'kategori_id' => [
            'required'    => 'Kategori wajib diisi.',
        ],
        'deskripsi_produk' => [
            'required'    => 'Deskripsi Produk wajib diisi.',
        ],
        'harga_jual' => [
            'required'    => 'Harga Jual wajib diisi.',
        ],
        'produk_barang' => [
            'required'    => 'Produk Barang wajib diisi.',
        ],
        'produk_expired' => [
            'required'    => 'Produk Memiliki Tanggal Kadaluarsa wajib diisi.',
        ],
        'satuan_id' => [
            'required'    => 'Satuan wajib diisi.',
        ],
        // 'barcode_value' => [
        //     'required'    => 'Barcode value wajib diisi.',
        // ],
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

    public function getProdukData($limit, $start, $searchValue, $filters = [])
    {
        try {
            $cacheKey = 'getProdukData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
                'filters' => $filters,
            ]));

            $produks = cache()->get($cacheKey);

            if (!$produks) {
                $builder = $this->db->table($this->table)
                    ->select("tbl_m_produk.*, tbl_m_kategori.kategori, COALESCE(SUM(tbl_h_produk.qty_in), 0) - COALESCE(SUM(tbl_h_produk.qty_out), 0) AS stok")
                    ->join("tbl_m_kategori", "tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id")
                    ->join("tbl_h_produk", "tbl_h_produk.produk_id = tbl_m_produk.produk_id AND tbl_h_produk.deleted_at IS NULL", "left")
                    ->where('tbl_m_produk.deleted_at', null)
                    ->orderBy('tbl_m_produk.created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('kategori', $searchValue)
                        ->groupEnd();
                }

                // filter dari form
                if (!empty($filters['kategori_id'])) {
                    $builder->where('tbl_m_produk.kategori_id', $filters['kategori_id']);
                }

                $query = $builder->limit($limit, $start)->get();
                $produks = $query->getResultArray();

                foreach ($produks as &$produk) {
                    $produk['encrypted_id'] = stringEncryptions('encrypt', $produk['produk_id']);
                }

                cache()->save($cacheKey, $produks, 600);
            }


            return $produks;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredProduk($searchValue, $filters = [])
    {
        $cacheKey = 'countFilteredProduk_' . md5(json_encode([
            'search' => $searchValue,
            'filters' => $filters,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->select("tbl_m_produk.*, tbl_m_kategori.kategori")
                ->join("tbl_m_kategori", "tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id")
                ->where('tbl_m_produk.deleted_at', null)
                ->orderBy('tbl_m_produk.created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('kategori', $searchValue)
                    ->groupEnd();
            }

            // filter dari form
            if (!empty($filters['kategori_id'])) {
                $builder->where('tbl_m_produk.kategori_id', $filters['kategori_id']);
            }

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return  $total;
    }

    public function countAllProduk()
    {
        $cacheKey = 'countAllProduk';

        $total = cache()->get($cacheKey);

        if ($total == null) {
            $builder = $this->db->table($this->table)
                ->select("tbl_m_produk.*, tbl_m_kategori.kategori")
                ->join("tbl_m_kategori", "tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id")
                ->where('tbl_m_produk.deleted_at', null)
                ->orderBy('tbl_m_produk.created_at', 'DESC');

            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }

    public function getProdukiId($id)
    {
        $cacheKey = 'getProdukiId_' . $id;

        $data = cache()->get($cacheKey);
        if (!$data) {
            $data = $this->where('produk_id', $id)
                ->where("deleted_at", null)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekProduk($kategori_id, $produk)
    {
        return $this->where('kategori_id', $kategori_id)
            ->where("produk", $produk)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function getStokProduk($produk_id)
    {
        return $this->db->table('tbl_h_produk')
            ->select('SUM(qty_in - qty_out) AS stok')
            ->where('produk_id', $produk_id)
            ->where('deleted_at', null)
            ->get()
            ->getRow();
    }
}
