<?php

namespace App\Controllers\Home;

use App\Models\RoleAksesModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class UserController extends ResourceController
{
    protected $db;
    protected $client;
    protected $session_permissions;
    protected $roleAksesModel;
    protected $userModel;
    protected $title;

    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        $this->client = \Config\Services::curlrequest();

        // Inisialisasi model di constructor
        $this->roleAksesModel = new RoleAksesModel();
        $this->userModel = new UserModel();

        $this->title = 'Users';
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
        if (in_array(2, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('home/user/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/home');
        }
    }

    public function getUsers()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $filters = $request->getPost('filters');

        $totalRecords = $this->userModel->countAllUsers();
        $totalFiltered = $this->userModel->countFilteredUsers($searchValue, $filters);
        $users = $this->userModel->getUsersData($length, $start, $searchValue, $filters);


        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $users,
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
        if (in_array(2, $this->session_permissions)) {
            $user = $this->userModel->getProfilId(stringEncryptions('decrypt', $id));

            if (!$user) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/home/users');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['user'] = $user;

            return view('home/user/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/home/users');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(3, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            $data['role_akses'] = $this->roleAksesModel->get_all_role();
            return view('home/user/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/home/users');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(3, $this->session_permissions)) {
            if (!$this->validate($this->userModel->validationRules, $this->userModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->userModel->cekEmail($this->request->getPost('email')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Email yang Anda masukkan sudah terdaftar. Silakan gunakan email lain']);
            }

            if ($this->userModel->cekEmail($this->request->getPost('username')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Username yang Anda masukkan sudah terdaftar. Silakan gunakan username lain']);
            }

            $this->db->transBegin();

            $file = $this->request->getFile('foto_profile');
            if ($file->isValid() && !$file->hasMoved()) {
                $newFileName = $file->getRandomName();
                $file->move('file_upload/user/foto/', $newFileName);
                $newFileName = 'file_upload/user/foto/' . $newFileName;
            }

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'foto_profile'  => isset($newFileName) ? $newFileName : null,
                'username'      => $this->request->getPost('username'),
                'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
                'email'         => $this->request->getPost('email'),
                'password'      => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tgl_lahir'     => $this->request->getPost('tgl_lahir'),
                'alamat'        => $this->request->getPost('alamat'),
                'tlp'           => $this->request->getPost('tlp'),
                'no_hp'         => $this->request->getPost('no_hp'),
                'role'          => $this->request->getPost('role'),
                'status'        => 'Aktif',
                'user_created'  => $userID,
                'created_at'    => $now
            ];

            $inserted = $this->userModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                // Simpan log jika perlu
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/home/users');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/home/users');
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
        if (in_array(4, $this->session_permissions)) {
            $user = $this->userModel->getProfilId(stringEncryptions('decrypt', $id));

            if (!$user) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/home/users');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['user'] = $user;
            $data['role_akses'] = $this->roleAksesModel->get_all_role();

            return view('home/user/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/home/users');
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
        if (in_array(4, $this->session_permissions)) {

            if (
                $this->request->getPost('email') != $this->request->getPost('emailOld') &&
                $this->userModel->cekEmail($this->request->getPost('email')) > 0
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Email yang Anda masukkan sudah terdaftar. Silakan gunakan email lain']);
            }

            if (
                $this->request->getPost('username') != $this->request->getPost('usernameOld') &&
                $this->userModel->cekEmail($this->request->getPost('username')) > 0
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Username yang Anda masukkan sudah terdaftar. Silakan gunakan username lain']);
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $file = $this->request->getFile('foto_profile');
            if ($file->isValid() && !$file->hasMoved()) {
                $newFileName = $file->getRandomName();
                $file->move('file_upload/user/foto/', $newFileName);
                $newFileName = 'file_upload/user/foto/' . $newFileName;
            }

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'foto_profile'  => isset($newFileName) ? $newFileName : $this->request->getPost('foto_old'),
                'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
                'email'         => $this->request->getPost('email'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tgl_lahir'     => $this->request->getPost('tgl_lahir'),
                'alamat'        => $this->request->getPost('alamat'),
                'tlp'           => $this->request->getPost('tlp'),
                'no_hp'         => $this->request->getPost('no_hp'),
                'role'          => $this->request->getPost('role'),
                'status'        => $this->request->getPost('status'),
                'user_updated'  => $userID,
                'updated_at'    => $now
            ];

            if (!empty($this->request->getPost('password'))) {

                $data['password'] = password_hash(
                    $this->request->getPost('password'),
                    PASSWORD_BCRYPT
                );
            }

            $updated = $this->userModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {

                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/home/users');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/home/users');
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
        if (in_array(5, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->userModel->update($decodeId, $data);

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
