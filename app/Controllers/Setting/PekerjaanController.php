<?php

namespace App\Controllers\Setting;

use App\Models\PekerjaanModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class PekerjaanController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $pekerjaanModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->pekerjaanModel = new PekerjaanModel();

        $this->title = 'Pekerjaan';
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
        if (in_array(39, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/pekerjaan/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getPekerjaans()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->pekerjaanModel->countAllResults();
        $totalFiltered = $this->pekerjaanModel->countFilteredDatas($searchValue);
        $pekerjaans = $this->pekerjaanModel->getPekerjaanData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $pekerjaans,
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
        if (in_array(39, $this->session_permissions)) {
            $pekerjaan = $this->pekerjaanModel->getPekerjaanId(stringEncryptions('decrypt', $id));

            if (!$pekerjaan) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/pekerjaan');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['pekerjaan'] = $pekerjaan;

            return view('setting/pekerjaan/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/pekerjaan');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(40, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/pekerjaan/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/pekerjaan');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(40, $this->session_permissions)) {
            if (!$this->validate($this->pekerjaanModel->validationRules, $this->pekerjaanModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->pekerjaanModel->cekPekerjaan($this->request->getPost('pekerjaan')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Pekerjaan yang Anda masukkan sudah terdaftar. Silakan gunakan nama pekerjaan lain']);
            }


            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'pekerjaan'      => $this->request->getPost('pekerjaan'),
                'status'         => 'Aktif',
                'user_created'   => $userID,
                'created_at'     => $now
            ];

            $inserted = $this->pekerjaanModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/pekerjaan');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/pekerjaan');
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
        if (in_array(41, $this->session_permissions)) {
            $pekerjaan = $this->pekerjaanModel->getPekerjaanId(stringEncryptions('decrypt', $id));

            if (!$pekerjaan) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/pekerjaan');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['pekerjaan'] = $pekerjaan;

            return view('setting/pekerjaan/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/pekerjaan');
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
        if (in_array(41, $this->session_permissions)) {

            if (!$this->validate($this->pekerjaanModel->validationRules, $this->pekerjaanModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('pekerjaan') != $this->request->getPost('pekerjaan_old')) {
                if ($this->pekerjaanModel->cekPekerjaan($this->request->getPost('pekerjaan')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Pekerjaan yang Anda masukkan sudah terdaftar. Silakan gunakan nama pekerjaan lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'pekerjaan'      => $this->request->getPost('pekerjaan'),
                'status'         => $this->request->getPost('status') ?? "Tidak Aktif",
                'user_updated'   => $userID,
                'updated_at'     => $now
            ];
            $updated = $this->pekerjaanModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/pekerjaan');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/pekerjaan');
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
        if (in_array(42, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->pekerjaanModel->update($decodeId, $data);

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
