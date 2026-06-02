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
                    'name' => $s['produk']
                ];
            }
            cache()->save($cacheKey, $items, 600);
        }

        return $this->response->setJSON(['items' => $items]);
    }
}
