<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleAksesModel extends Model
{
    protected $table            = 'tbl_m_role_akses';
    protected $primaryKey       = 'role_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "role",
        "akses_menu",
        "akses_submenu",
        "permissions",
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

    // database
    protected $db;

    public function __construct()
    {
        parent::__construct();
        // Inisialisasi database sekali di constructor
        $this->db = db_connect();
    }

    public function getRoleData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getRoleData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue,
            ]));

            $roles = cache()->get($cacheKey);

            if (!$roles) {
                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('role', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $roles = $query->getResultArray();

                foreach ($roles as &$role) {
                    $role['encrypted_id'] = stringEncryptions('encrypt', $role['role_id']);
                }
                cache()->save($cacheKey, $roles, 600);
            }

            return $roles;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredRole($searchValue)
    {
        $cacheKey = 'countFilteredRole_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('role', $searchValue)
                    ->groupEnd();
            }
            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }
        return $total;
    }

    public function countAllRole()
    {
        $cacheKey = 'countAllRole';

        $total = cache()->get($cacheKey);

        if ($total == null) {
            $builder = $this->db->table($this->table)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');
            $total = $builder->countAllResults();
            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function getRoleId($id)
    {
        $cacheKey = 'getRoleId_' . $id;

        $data = cache()->get($cacheKey);

        if (!$data) {
            $data = $this->where('role_id', $id)
                ->where("deleted_at", null)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }

    public function cekRole($role)
    {
        return $this->where('role', $role)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    public function get_all_role()
    {
        $cacheKey = 'get_all_role';

        $data = cache()->get($cacheKey);
        if (!$data) {
            $builder = $this->select("role_id, role")
                ->where("status", "Aktif")
                ->where("deleted_at", null);

            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }

        return $data;
    }
}
