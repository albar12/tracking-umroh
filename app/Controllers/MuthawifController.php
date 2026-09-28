<?php

namespace App\Controllers;

use App\Models\MuthawifModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class MuthawifController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $muthawifModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->muthawifModel = new MuthawifModel();

        $this->title = 'Muthawif';
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
        if (in_array(10, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('muthawif/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getMuthawifs()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->muthawifModel->countAllMuthawif();
        $totalFiltered = $this->muthawifModel->countFilteredMuthawif($searchValue);
        $muthawif = $this->muthawifModel->getMuthawifData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $muthawif,
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
        if (in_array(10, $this->session_permissions)) {
            $muthawif = $this->muthawifModel->getMuthawifId(stringEncryptions('decrypt', $id));

            if (!$muthawif) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/muthawif');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['muthawif'] = $muthawif;

            return view('muthawif/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/muthawif');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(11, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('muthawif/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/muthawif');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(11, $this->session_permissions)) {
            if (!$this->validate($this->muthawifModel->validationRules, $this->muthawifModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->muthawifModel->cekMuthawif($this->request->getPost('nama_lengkap')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Muthawif yang Anda masukkan sudah terdaftar. Silakan gunakan nama muthawif lain']);
            }

            $this->db->transBegin();

            $file = $this->request->getFile('foto_profile');
            if ($file->isValid() && !$file->hasMoved()) {
                $newFileName = $file->getRandomName();
                $file->move('file_upload/muthawif/foto/', $newFileName);
                $newFileName = 'file_upload/muthawif/foto/' . $newFileName;
            }

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'nama_lengkap'          => $this->request->getPost('nama_lengkap'),
                'nama_panggilan'        => $this->request->getPost('nama_panggilan'),
                'jenis_kelamin'         => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir'          => $this->request->getPost('tempat_lahir'),
                'tgl_lahir'             => $this->request->getPost('tgl_lahir'),
                'kewarganegaraan'       => $this->request->getPost('kewarganegaraan'),
                'no_telp'               => $this->request->getPost('no_telp'),
                'email'                 => $this->request->getPost('email'),
                'password'              => password_hash("muthawif@123", PASSWORD_BCRYPT),
                'foto_profile'          => isset($newFileName) ? $newFileName : null,
                'status'                => 'Aktif',
                'user_created'          => $userID,
                'created_at'            => $now
            ];

            $inserted = $this->muthawifModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/muthawif');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/muthawif');
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
        if (in_array(12, $this->session_permissions)) {
            $muthawif = $this->muthawifModel->getMuthawifId(stringEncryptions('decrypt', $id));

            if (!$muthawif) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/muthawif');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['muthawif'] = $muthawif;

            return view('muthawif/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/muthawif');
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
        if (in_array(12, $this->session_permissions)) {

            if (!$this->validate($this->muthawifModel->validationRules, $this->muthawifModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('nama_lengkap') != $this->request->getPost('nama_lengkap_old')) {
                if ($this->muthawifModel->cekMuthawif($this->request->getPost('nama_lengkap')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Muthawif yang Anda masukkan sudah terdaftar. Silakan gunakan nama muthawif lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $file = $this->request->getFile('foto_profile');

            if ($file->isValid() && !$file->hasMoved()) {
                $newFileName = $file->getRandomName();
                $file->move('file_upload/muthawif/foto/', $newFileName);
                $newFileName = 'file_upload/muthawif/foto/' . $newFileName;
                unlink($this->request->getPost('foto_profile_old'));
            } else {
                $newFileName = $this->request->getPost('foto_profile_old');
            }

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'nama_lengkap'          => $this->request->getPost('nama_lengkap'),
                'nama_panggilan'        => $this->request->getPost('nama_panggilan'),
                'jenis_kelamin'         => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir'          => $this->request->getPost('tempat_lahir'),
                'tgl_lahir'             => $this->request->getPost('tgl_lahir'),
                'kewarganegaraan'       => $this->request->getPost('kewarganegaraan'),
                'no_telp'               => $this->request->getPost('no_telp'),
                'email'                 => $this->request->getPost('email'),
                'foto_profile'          => isset($newFileName) ? $newFileName : null,
                'status'                => $this->request->getPost('status') ?? "Tidak Aktif",
                'user_updated'          => $userID,
                'updated_at'            => $now
            ];

            if (!empty($this->request->getPost('password'))) {

                $data['password'] = password_hash(
                    $this->request->getPost('password'),
                    PASSWORD_BCRYPT
                );
            }

            $updated = $this->muthawifModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/muthawif');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/muthawif');
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
        if (in_array(13, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->muthawifModel->update($decodeId, $data);

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
