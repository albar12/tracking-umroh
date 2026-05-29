<?php

namespace App\Controllers\General;

use App\Models\UserModel;
use App\Models\RoleAksesModel;
use App\Models\KategoriModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class GeneralController extends ResourceController
{
    protected $db;
    protected $userModel;
    protected $roleAksesModel;
    protected $kategoriModel;

    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        $this->userModel = new UserModel();
        $this->roleAksesModel = new RoleAksesModel();
        $this->kategoriModel = new KategoriModel();
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
}
