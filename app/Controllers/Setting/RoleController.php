<?php

namespace App\Controllers\Setting;

use App\Models\RoleAksesModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class RoleController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $roleAksesModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->roleAksesModel = new RoleAksesModel();

        $this->title = 'Role';
        $permissions = session()->get('permissions');
        $this->session_permissions = $permissions ? explode(',', $permissions) : [];
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        if (in_array(14, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/role/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getRoles()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian


        $totalRecords = $this->roleAksesModel->countAllRole();
        $totalFiltered = $this->roleAksesModel->countFilteredRole($searchValue);
        $roles = $this->roleAksesModel->getRoleData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $roles,
        ]);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        if (in_array(14, $this->session_permissions)) {
            $role = $this->roleAksesModel->getRoleId(stringEncryptions('decrypt', $id));

            if (!$role) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/role');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['role'] = $role;
            $builder = $this->db->table('tbl_m_permissions');
            $builder->select([
                'tbl_m_menu.menu',
                'submenu',
                'MAX(CASE WHEN tipe_menu LIKE "%insert%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS inserts',
                'MAX(CASE WHEN tipe_menu LIKE "%edit%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS edits',
                'MAX(CASE WHEN tipe_menu LIKE "%view%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS views',
                'MAX(CASE WHEN tipe_menu LIKE "%delete%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS deletes',
                'MAX(CASE WHEN tipe_menu LIKE "%approve%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS approves'
            ]);
            $builder->join('tbl_m_menu', 'tbl_m_menu.menu_id = tbl_m_permissions.menu_id');
            $builder->join('tbl_m_sub_menu', 'tbl_m_sub_menu.sub_id = tbl_m_permissions.sub_id');
            $builder->where('display_submenu', 'yes');
            $builder->groupBy('tbl_m_menu.menu_id');
            $builder->groupBy('submenu');
            $builder->orderBy('tbl_m_menu.order_menu', 'ASC');
            $builder->orderBy('order_sub', 'ASC');
            $builder->orderBy('order_submenu', 'ASC');
            $result = $builder->get()->getResult();
            $data['permissions'] = $result;

            return view('setting/role/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/role');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(15, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';

            $builder = $this->db->table('tbl_m_permissions');
            $builder->select([
                'tbl_m_menu.menu',
                'submenu',
                'MAX(CASE WHEN tipe_menu LIKE "%insert%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS inserts',
                'MAX(CASE WHEN tipe_menu LIKE "%edit%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS edits',
                'MAX(CASE WHEN tipe_menu LIKE "%view%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS views',
                'MAX(CASE WHEN tipe_menu LIKE "%delete%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS deletes',
                'MAX(CASE WHEN tipe_menu LIKE "%approve%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS approves'
            ]);
            $builder->join('tbl_m_menu', 'tbl_m_menu.menu_id = tbl_m_permissions.menu_id');
            $builder->join('tbl_m_sub_menu', 'tbl_m_sub_menu.sub_id = tbl_m_permissions.sub_id');
            $builder->where('display_submenu', 'yes');
            $builder->groupBy('tbl_m_menu.menu_id');
            $builder->groupBy('submenu');
            $builder->orderBy('tbl_m_menu.order_menu', 'ASC');
            $builder->orderBy('order_sub', 'ASC');
            $builder->orderBy('order_submenu', 'ASC');
            $result = $builder->get()->getResult();
            $data['permissions'] = $result;

            return view('setting/role/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/produk');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(15, $this->session_permissions)) {
            try {
                $akses_id = $this->request->getPost('akses_id');

                if (empty($akses_id) || !is_array($akses_id)) {
                    setToast('error', 'Tidak ada data akses yang dipilih.');
                    return redirect()->to('/setting/users');
                }

                $akses_id_string = implode(',', $akses_id);
                $arr_menu = [];
                $arr_sub = [];

                foreach ($akses_id as $item) {
                    $result = $this->db->table('tbl_m_permissions')
                        ->select('sub_id, menu_id')
                        ->where('permissions_id', $item)
                        ->get()
                        ->getRowArray();
                    if (!empty($result)) {
                        if (isset($result['menu_id'])) {
                            $arr_menu[] = $result['menu_id'];
                        }
                        if (isset($result['sub_id'])) {
                            $arr_sub[] = $result['sub_id'];
                        }
                    }
                }


                $this->db->transBegin();

                $role = $this->request->getPost('role');
                $user_id = session()->get('user_id');
                $date = date('Y-m-d H:i:s');
                $data = [
                    'role'         => $role,
                    'akses_menu'    => implode(',', array_unique($arr_menu)),
                    'akses_submenu' => implode(',', array_unique($arr_sub)),
                    'permissions'   => $akses_id_string,
                    'status'        => 'Aktif',
                    'user_created'  => $user_id,
                    'created_at'    => $date
                ];

                $inserted = $this->roleAksesModel->insert($data, true);

                if ($this->db->transStatus() === false || !$inserted) {
                    $this->db->transRollback();
                    setToast('error', 'Gagal menyimpan data permission.');
                    return redirect()->back();
                } else {
                    $this->db->transCommit();
                    setToast('success', 'Data berhasil disimpan.');
                    return redirect()->to('/setting/role');
                }
            } catch (\Throwable $e) {
                $this->db->transRollback();
                setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut');
                return redirect()->back();
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/role');
        }
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        if (in_array(16, $this->session_permissions)) {
            $role = $this->roleAksesModel->getRoleId(stringEncryptions('decrypt', $id));

            if (!$role) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/role');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['role'] = $role;
            $builder = $this->db->table('tbl_m_permissions');
            $builder->select([
                'tbl_m_menu.menu',
                'submenu',
                'MAX(CASE WHEN tipe_menu LIKE "%insert%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS inserts',
                'MAX(CASE WHEN tipe_menu LIKE "%edit%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS edits',
                'MAX(CASE WHEN tipe_menu LIKE "%view%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS views',
                'MAX(CASE WHEN tipe_menu LIKE "%delete%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS deletes',
                'MAX(CASE WHEN tipe_menu LIKE "%approve%" THEN tbl_m_permissions.permissions_id ELSE NULL END) AS approves'
            ]);
            $builder->join('tbl_m_menu', 'tbl_m_menu.menu_id = tbl_m_permissions.menu_id');
            $builder->join('tbl_m_sub_menu', 'tbl_m_sub_menu.sub_id = tbl_m_permissions.sub_id');
            $builder->where('display_submenu', 'yes');
            $builder->groupBy('tbl_m_menu.menu_id');
            $builder->groupBy('submenu');
            $builder->orderBy('tbl_m_menu.order_menu', 'ASC');
            $builder->orderBy('order_sub', 'ASC');
            $builder->orderBy('order_submenu', 'ASC');
            $result = $builder->get()->getResult();
            $data['permissions'] = $result;

            return view('setting/role/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/role');
        }
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        if (in_array(16, $this->session_permissions)) {

            try {
                $akses_id = $this->request->getPost('akses_id');

                if (empty($akses_id) || !is_array($akses_id)) {
                    setToast('error', 'Tidak ada data akses yang dipilih.');
                    return redirect()->to('/setting/users');
                }

                $akses_id_string = implode(',', $akses_id);
                $arr_menu = [];
                $arr_sub = [];

                foreach ($akses_id as $item) {
                    $result = $this->db->table('tbl_m_permissions')
                        ->select('sub_id, menu_id')
                        ->where('permissions_id', $item)
                        ->get()
                        ->getRowArray();
                    if (!empty($result)) {
                        if (isset($result['menu_id'])) {
                            $arr_menu[] = $result['menu_id'];
                        }
                        if (isset($result['sub_id'])) {
                            $arr_sub[] = $result['sub_id'];
                        }
                    }
                }

                $role_id = stringEncryptions('decrypt', $id);
                $role = $this->request->getPost('role');
                $user_id = session()->get('user_id');
                $date = date('Y-m-d H:i:s');

                $this->db->transBegin();

                $updateData = [
                    'role'          => $role,
                    'akses_menu'    => implode(',', array_unique($arr_menu)),
                    'akses_submenu' => implode(',', array_unique($arr_sub)),
                    'permissions'   => $akses_id_string,
                    'status'        => $this->request->getPost('status') == 'Aktif' ? 'Aktif' : 'Tidak Aktif',
                    'user_updated'  => $user_id,
                    'updated_at'    => $date
                ];
                $updated = $this->roleAksesModel->update($role_id, $updateData);

                if ($this->db->transStatus() === false || !$updated) {
                    $this->db->transRollback();
                    setToast('error', 'Gagal menyimpan data permission.');
                    return redirect()->back();
                } else {
                    $this->db->transCommit();
                    setToast('success', 'Data berhasil diperbarui.');
                    return redirect()->to('/setting/role');
                }
            } catch (\Throwable $e) {
                $this->db->transRollback();
                setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut');
                return redirect()->back();
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/role');
        }
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        if (in_array(17, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->roleAksesModel->update($decodeId, $data);

                if ($this->db->transStatus() === false || !$deleted) {
                    $this->db->transRollback();
                    setToast('error', 'Gagal menghapus data. Silakan coba beberapa saat lagi.');
                } else {
                    $this->db->transCommit();
                    setToast('success', 'Data berhasil dihapus.');
                }
            } catch (\Exception $e) {
                $this->db->transRollback();
                setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
        }
    }
}
