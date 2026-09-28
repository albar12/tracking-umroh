<?php

namespace App\Controllers\Setting;

use App\Models\AlatBantuModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class AlatBantuController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $alatBantuModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->alatBantuModel = new AlatBantuModel();

        $this->title = 'Alat Bantu';
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
        if (in_array(76, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/alat_bantu/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getAlatBantus()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->alatBantuModel->countAllAlatBantu();
        $totalFiltered = $this->alatBantuModel->countFilteredAlatBantu($searchValue);
        $alatBantu = $this->alatBantuModel->getAlatBantuData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $alatBantu,
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
        if (in_array(76, $this->session_permissions)) {
            $alatBantu = $this->alatBantuModel->getAlatBantuId(stringEncryptions('decrypt', $id));

            if (!$alatBantu) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/alat-bantu');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['alatBantu'] = $alatBantu;

            return view('setting/alat_bantu/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/alat-bantu');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(77, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/alat_bantu/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/alat-bantu');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(77, $this->session_permissions)) {
            if (!$this->validate($this->alatBantuModel->validationRules, $this->alatBantuModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->alatBantuModel->cekAlatBantu($this->request->getPost('alat_bantu')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Alat Bantu yang Anda masukkan sudah terdaftar. Silakan gunakan nama alat bantu lain']);
            }

            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'alat_bantu'       => $this->request->getPost('alat_bantu'),
                'status'           => 'Aktif',
                'user_created'     => $userID,
                'created_at'       => $now
            ];

            $inserted = $this->alatBantuModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/alat-bantu');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/alat-bantu');
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
        if (in_array(78, $this->session_permissions)) {
            $alatBantu = $this->alatBantuModel->getAlatBantuId(stringEncryptions('decrypt', $id));

            if (!$alatBantu) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/alat-bantu');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['alatBantu'] = $alatBantu;

            return view('setting/alat_bantu/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/alat-bantu');
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
        if (in_array(78, $this->session_permissions)) {

            if (!$this->validate($this->alatBantuModel->validationRules, $this->alatBantuModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('alat_bantu') != $this->request->getPost('alat_bantu_old')) {
                if ($this->alatBantuModel->cekAlatBantu($this->request->getPost('alat_bantu')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Alat Bantu yang Anda masukkan sudah terdaftar. Silakan gunakan nama alat bantu lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'alat_bantu'       => $this->request->getPost('alat_bantu'),
                'status'           => $this->request->getPost('status') ?? "Tidak Aktif",
                'user_updated'     => $userID,
                'updated_at'       => $now
            ];
            $updated = $this->alatBantuModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/alat-bantu');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/alat-bantu');
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
        if (in_array(79, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->alatBantuModel->update($decodeId, $data);

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
