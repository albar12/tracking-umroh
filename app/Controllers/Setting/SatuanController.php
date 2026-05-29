<?php

namespace App\Controllers\Setting;

use App\Models\SatuanModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class SatuanController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $satuanModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->satuanModel = new SatuanModel();

        $this->title = 'Satuan';
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
        if (in_array(18, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/satuan/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting');
        }
    }

    public function getSatuans()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->satuanModel->countAllSatuan();
        $totalFiltered = $this->satuanModel->countFilteredSatuan($searchValue);
        $satuans = $this->satuanModel->getSatuanData($length, $start, $searchValue);


        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $satuans,
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
        if (in_array(18, $this->session_permissions)) {
            $satuan = $this->satuanModel->getSatuanId(stringEncryptions('decrypt', $id));

            if (!$satuan) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/satuan');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['satuan'] = $satuan;

            return view('setting/satuan/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/satuan');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(19, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/satuan/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/satuan');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(19, $this->session_permissions)) {
            if (!$this->validate($this->satuanModel->validationRules, $this->satuanModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->satuanModel->cekSatuan($this->request->getPost('satuan'), $this->request->getPost('qty')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Satuan yang Anda masukkan sudah terdaftar. Silakan gunakan satuan lain']);
            }

            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'satuan'         => $this->request->getPost('satuan'),
                'qty'            => $this->request->getPost('qty'),
                'status'         => 'Aktif',
                'user_created'   => $userID,
                'created_at'     => $now
            ];

            $inserted = $this->satuanModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/satuan');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/satuan');
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
        if (in_array(20, $this->session_permissions)) {
            $satuan = $this->satuanModel->getSatuanId(stringEncryptions('decrypt', $id));

            if (!$satuan) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/satuan');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['satuan'] = $satuan;

            return view('setting/satuan/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/satuan');
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
        if (in_array(20, $this->session_permissions)) {

            if (!$this->validate($this->satuanModel->validationRules, $this->satuanModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('satuan') != $this->request->getPost('satuanOld')) {
                if ($this->satuanModel->cekSatuan($this->request->getPost('satuan'), $this->request->getPost('qty')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Satuan yang Anda masukkan sudah terdaftar. Silakan gunakan satuan lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'satuan'       => $this->request->getPost('satuan'),
                'qty'          => $this->request->getPost('qty'),
                'status'       => $this->request->getPost('status'),
                'user_updated' => $userID,
                'updated_at'   => $now
            ];
            $updated = $this->satuanModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/satuan');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/satuan');
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
        if (in_array(21, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->satuanModel->update($decodeId, $data);

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
