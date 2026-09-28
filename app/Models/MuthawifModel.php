<?php

namespace App\Models;

use CodeIgniter\Model;

class MuthawifModel extends Model
{
    protected $table            = 'tbl_m_muthawif';
    protected $primaryKey       = 'muthawif_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "nama_lengkap",
        "nama_panggilan",
        "jenis_kelamin",
        "tempat_lahir",
        "tgl_lahir",
        "kewarganegaraan",
        "no_telp",
        "email",
        "password",
        "foto_profile",
        "token_fcm",
        "access_token",
        "refresh_token",
        "otp",
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
        'nama_panggilan' => 'required',
        'jenis_kelamin' => 'required',
        'tempat_lahir' => 'required',
        'tgl_lahir' => 'required',
        'kewarganegaraan' => 'required',
        'no_telp' => 'required',
        'email' => 'required',
    ];
    protected $validationMessages   = [
        'nama_lengkap' => [
            'required'    => 'Nama Lengkap wajib diisi.',
        ],
        'nama_panggilan' => [
            'required'    => 'Nama Panggilan wajib diisi.',
        ],
        'jenis_kelamin' => [
            'required'    => 'Jenis Kelamin wajib diisi.',
        ],
        'tempat_lahir' => [
            'required'    => 'Tempat Lahir wajib diisi.',
        ],
        'tgl_lahir' => [
            'required'    => 'Tanggal Lahir wajib diisi.',
        ],
        'kewarganegaraan' => [
            'required'    => 'Kewarganegaraan wajib diisi.',
        ],
        'no_telp' => [
            'required'    => 'No Telp. wajib diisi.',
        ],
        'email' => [
            'required'    => 'Email wajib diisi.',
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

    public function getMuthawifData($limit, $start, $searchValue)
    {
        try {
            $cacheKey = 'getMuthawifData_' . md5(json_encode([
                'limit'  => $limit,
                'start'  => $start,
                'search' => $searchValue
            ]));

            $datas = cache()->get($cacheKey);

            if (!$datas) {
                $userId            = session()->get('user_id');
                $userLevel         = session()->get('level');

                $builder = $this->db->table($this->table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC');

                if (!empty($searchValue)) {
                    $builder->groupStart()
                        ->like('nama_lengkap', $searchValue)
                        ->orLike('nama_panggilan', $searchValue)
                        ->orLike('jenis_kelamin', $searchValue)
                        ->orLike('email', $searchValue)
                        ->orLike('status', $searchValue)
                        ->groupEnd();
                }

                $query = $builder->limit($limit, $start)->get();
                $datas = $query->getResultArray();

                foreach ($datas as &$data) {
                    $data['encrypted_id'] = stringEncryptions('encrypt', $data['muthawif_id']);
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

    public function countFilteredMuthawif($searchValue)
    {
        $cacheKey = 'countFilteredMuthawif_' . md5(json_encode([
            'search' => $searchValue,
        ]));

        $total = cache()->get($cacheKey);

        if (!$total) {
            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null)
                ->orderBy('created_at', 'DESC');

            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('nama_lengkap', $searchValue)
                    ->orLike('nama_panggilan', $searchValue)
                    ->orLike('jenis_kelamin', $searchValue)
                    ->orLike('email', $searchValue)
                    ->orLike('status', $searchValue)
                    ->groupEnd();
            }

            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function countAllMuthawif()
    {
        $cacheKey = 'countAllMuthawif';

        $total = cache()->get($cacheKey);

        if ($total === null) {

            $builder = $this->db->table($this->table);
            $builder->where('deleted_at', null);
            $total = $builder->countAllResults();

            cache()->save($cacheKey, $total, 600);
        }

        return $total;
    }

    public function getMuthawifId($id)
    {
        $cacheKey = 'getMuthawifId_' . $id;

        $data = cache()->get($cacheKey);

        if (!$data) {
            $data = $this->select('*')
                ->where('muthawif_id', $id)
                ->first();
            cache()->save($cacheKey, $data, 600);
        }
        return  $data;
    }

    public function cekMuthawif($alat_bantu)
    {
        return $this->where('nama_lengkap', $alat_bantu)->where('deleted_at', null)->countAllResults();
    }

    public function get_all_muthawif()
    {
        $cacheKey = 'get_all_muthawif';

        $data = cache()->get($cacheKey);

        if (!$data) {
            $builder = $this->select("muthawif_id, nama_lengkap")
                ->where("status", "Aktif")
                ->where("deleted_at", null);
            $data = $builder->findAll();
            cache()->save($cacheKey, $data, 600);
        }
        return $data;
    }
}
