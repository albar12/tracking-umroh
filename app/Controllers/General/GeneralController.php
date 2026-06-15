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
            $items = $this->db->table("tbl_h_produk")
                ->select("tbl_h_produk.produk_id, tbl_m_produk.produk, tbl_m_kategori.kategori_id, 
                tbl_m_kategori.kategori, COALESCE(SUM(tbl_h_produk.qty_in), 0) - COALESCE(SUM(tbl_h_produk.qty_out), 0) AS stok, tbl_m_produk.harga_jual")
                ->join("tbl_m_produk", "tbl_m_produk.produk_id = tbl_h_produk.produk_id")
                ->join("tbl_m_kategori", "tbl_m_kategori.kategori_id = tbl_m_produk.kategori_id", "left")
                ->where("tbl_h_produk.barcode_value", $barcode)
                ->where("tbl_h_produk.deleted_at", null)
                ->get()
                ->getRow();


            cache()->save($cacheKey, $items, 600);
        }

        return $this->response->setJSON(['items' => $items]);
    }
}
