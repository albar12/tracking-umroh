<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'tbl_m_users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "username",
        "nama_lengkap",
        "email",
        "password",
        "jenis_kelamin",
        "tgl_lahir",
        "alamat",
        "tlp",
        "no_hp",
        "foto_profile",
        "role",
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
        'nama_lengkap' => 'required',
        'username' => 'required|is_unique[tbl_m_users.username]',
        'jenis_kelamin' => 'required',
        'tgl_lahir' => 'required',
        'alamat' => 'required',
        'no_hp' => 'required',
        'email' => 'required|valid_email',
        'password' => 'required|min_length[5]',
        'role' => 'required',

    ];
    protected $validationMessages   = [
        'nama_lengkap' => [
            'required'    => 'Nama Lengkap wajib diisi.',
        ],
        'username' => [
            'required'    => 'Username wajib diisi.',
            'is_unique'   => 'Username ini sudah terdaftar. Gunakan username lain.',
        ],
        'jenis_kelamin' => [
            'required'    => 'Jenis Kelamin wajib diisi.',
        ],
        'tgl_lahir' => [
            'required'    => 'Tanggal Lahir wajib diisi.',
        ],
        'alamat' => [
            'required'    => 'Alamat wajib diisi.',
        ],
        'no_hp' => [
            'required'    => 'No HP wajib diisi.',
        ],
        'email' => [
            'required'    => 'Alamat email wajib diisi.',
            'valid_email' => 'Harap masukkan alamat email yang valid.',
            'is_unique'   => 'Alamat email ini sudah terdaftar. Gunakan email lain.',
        ],
        'password' => [
            'required'   => 'Kata sandi wajib diisi.',
            'min_length' => 'Kata sandi harus memiliki minimal {param} karakter.',
        ],
        'role' => [
            'required'    => 'Role wajib diisi.',
        ],
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

    public function getUsersData($limit, $start, $searchValue, $filters = [])
    {
        try {
            $userId            = session()->get('user_id');
            $userLevel         = session()->get('level');

            $builder = $this->db->table($this->table);
            $builder->select("user_id, username, nama_lengkap, email, jenis_kelamin, tgl_lahir, no_hp, tbl_m_users.status, tbl_m_role_akses.role")
                ->join('tbl_m_role_akses', 'tbl_m_role_akses.role_id = tbl_m_users.role')
                ->where('tbl_m_users.deleted_at', null)
                ->orderBy('tbl_m_users.created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('tbl_m_users.username', $searchValue)
                    ->orLike('tbl_m_users.email', $searchValue)
                    ->orLike('tbl_m_users.nama_lengkap', $searchValue)
                    ->orLike('tbl_m_role_akses.role', $searchValue)
                    ->groupEnd();
            }

            // filter dari form
            if (!empty($filters['role_id'])) {
                $builder->where('tbl_m_users.role', $filters['role_id']);
            }


            $query = $builder->limit($limit, $start)->get();
            $users = $query->getResultArray();

            foreach ($users as &$user) {
                $user['encrypted_id'] = stringEncryptions('encrypt', $user['user_id']);
            }

            return $users;
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'query' => $this->db->getLastQuery()->getQuery(),
                'message' => $e->getMessage()
            ];
        }
    }

    public function countFilteredUsers($searchValue, $filters = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select("user_id")
            ->join('tbl_m_role_akses', 'tbl_m_role_akses.role_id = tbl_m_users.role')
            ->where('tbl_m_users.deleted_at', null)
            ->orderBy('tbl_m_users.created_at', 'DESC');

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('tbl_m_users.username', $searchValue)
                ->orLike('tbl_m_users.email', $searchValue)
                ->orLike('tbl_m_users.nama_lengkap', $searchValue)
                ->orLike('tbl_m_role_akses.role', $searchValue)
                ->groupEnd();
        }

        // filter dari form
        if (!empty($filters['role_id'])) {
            $builder->where('tbl_m_users.role', $filters['role_id']);
        }


        return $builder->countAllResults();
    }

    public function countAllUsers()
    {
        $builder = $this->db->table($this->table);
        $builder->select("user_id, username, nama_lengkap, email, jenis_kelamin, tgl_lahir, tbl_m_users.status, tbl_m_role_akses.role")
            ->join('tbl_m_role_akses', 'tbl_m_role_akses.role_id = tbl_m_users.role')
            ->where('tbl_m_users.deleted_at', null)
            ->orderBy('tbl_m_users.created_at', 'DESC');

        return $builder->countAllResults();
    }

    public function getProfilId($id)
    {
        return $this->select('tbl_m_users.*, tbl_m_role_akses.role AS role_user')
            ->join('tbl_m_role_akses', 'tbl_m_role_akses.role_id = tbl_m_role_akses.role', 'left')
            ->where('tbl_m_users.user_id', $id)
            ->first();
    }

    public function cekEmail($email)
    {
        return $this->where('email', $email)->where('deleted_at', null)->countAllResults();
    }
}
