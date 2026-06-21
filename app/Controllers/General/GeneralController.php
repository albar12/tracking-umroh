<?php

namespace App\Controllers\General;

use App\Models\UserModel;
use App\Models\RoleAksesModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\ProdukModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class GeneralController extends ResourceController
{
    protected $db;
    protected $userModel;
    protected $roleAksesModel;
    protected $kategoriModel;
    protected $supplierModel;
    protected $produkModel;

    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        $this->userModel = new UserModel();
        $this->roleAksesModel = new RoleAksesModel();
        $this->kategoriModel = new KategoriModel();
        $this->supplierModel = new SupplierModel();
        $this->produkModel = new ProdukModel();
    }

    public function get_role_akses()
    {
        $cacheKey = 'get_role_akses';

        $items = cache()->get($cacheKey);

        if ($items == null) {
            $roles = $this->roleAksesModel
                ->where("status", "Aktif")
                ->where("deleted_at", null)
                ->findAll();

            $items = [];

            // Format hasil
            foreach ($roles as $r) {
                $items[] = [
                    'id'   => $r['role_id'],
                    'name' => $r['role']
                ];
            }

            cache()->save($cacheKey, $items, 600);
        }


        return $this->response->setJSON(['items' => $items]);
    }

    public function get_kategori()
    {
        $cacheKey = 'get_kategori';

        $items = cache()->get($cacheKey);

        if ($items == null) {
            $kategoris = $this->kategoriModel
                ->where("status", "Aktif")
                ->where("deleted_at", null)
                ->findAll();

            $items = [];

            // Format hasil
            foreach ($kategoris as $k) {
                $items[] = [
                    'id'   => $k['kategori_id'],
                    'name' => $k['kategori']
                ];
            }
            cache()->save($cacheKey, $items, 600);
        }

        return $this->response->setJSON(['items' => $items]);
    }

    public function get_supplier()
    {
        $cacheKey = 'get_supplier';

        $items = cache()->get($cacheKey);

        if ($items == null) {
            $suppliers = $this->supplierModel
                ->where("status", "Aktif")
                ->where("deleted_at", null)
                ->findAll();

            $items = [];

            // Format hasil
            foreach ($suppliers as $s) {
                $items[] = [
                    'id'   => $s['supplier_id'],
                    'name' => $s['supplier']
                ];
            }
            cache()->save($cacheKey, $items, 600);
        }

        return $this->response->setJSON(['items' => $items]);
    }

    public function get_produk_by_kategori()
    {
        $kategori_id = $this->request->getPost('kategori_id');
        $cacheKey = 'get_produk_by_kategori_' . $kategori_id;

        $items = cache()->get($cacheKey);

        if ($items == null) {
            $suppliers = $this->produkModel
                ->where("kategori_id", $kategori_id)
                ->where("status", "Aktif")
                ->where("deleted_at", null)
                ->findAll();

            $items = [];

            // Format hasil
            foreach ($suppliers as $s) {
                $items[] = [
                    'id'   => $s['produk_id'],
                    'name' => $s['produk'],
                    'produk_expired' => $s['produk_expired'],
                    'harga_jual' => $s['harga_jual'],
                ];
            }
            cache()->save($cacheKey, $items, 600);
        }

        return $this->response->setJSON(['items' => $items]);
    }

    public function get_produk_by_barcode()
    {
        $barcode = $this->request->getPost('barcode');
        $cacheKey = 'get_produk_by_barcode_' . $barcode;

        $items = cache()->get($cacheKey);

        if ($items == null) {
            $subQuery = $this->db->table('tbl_h_produk')
                ->select('produk_id')
                ->where('barcode_value', $barcode)
                ->limit(1);

            $items = $this->db->table('tbl_h_produk hp')
                ->select("
                    hp.produk_id,
                    mp.produk,
                    mk.kategori_id,
                    mk.kategori,
                    COALESCE(SUM(hp.qty_in),0) - COALESCE(SUM(hp.qty_out),0) AS stok,
                    mp.harga_jual
                ")
                ->join('tbl_m_produk mp', 'mp.produk_id = hp.produk_id')
                ->join('tbl_m_kategori mk', 'mk.kategori_id = mp.kategori_id', 'left')
                ->where("hp.produk_id = ({$subQuery->getCompiledSelect()})")
                ->where('hp.deleted_at', null)
                ->groupBy('hp.produk_id')
                ->get()
                ->getRow();


            cache()->save($cacheKey, $items, 600);
        }

        return $this->response->setJSON(['items' => $items]);
    }
}
