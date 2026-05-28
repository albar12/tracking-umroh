<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleAksesModel extends Model
{
    protected $table            = 'tbl_m_role_akses';
    protected $primaryKey       = 'roole_id';
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

    public function get_all_role()
    {
        $builder = $this->select("role_id, role")
            ->where("status", "Aktif")
            ->where("deleted_at", null);

        return $builder->findAll();
    }
}
