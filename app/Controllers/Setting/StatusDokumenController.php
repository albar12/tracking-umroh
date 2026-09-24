<?php

namespace App\Controllers\Setting;

use App\Models\StatusDokumenModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class StatusDokumenController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $statusDokumenModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->statusDokumenModel = new StatusDokumenModel();

        $this->title = 'Status Dokumen';
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
        if (in_array(60, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/status_dokumen/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getStatusDokumens()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->statusDokumenModel->countAllResults();
        $totalFiltered = $this->statusDokumenModel->countFilteredStatusDokumen($searchValue);
        $statusDokumen = $this->statusDokumenModel->getStatusDokumenData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $statusDokumen,
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
        if (in_array(60, $this->session_permissions)) {
            $statusDokumen = $this->statusDokumenModel->getStatusDokumenId(stringEncryptions('decrypt', $id));

            if (!$statusDokumen) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/status-dokumen');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['statusDokumen'] = $statusDokumen;

            return view('setting/status_dokumen/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-dokumen');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(61, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/status_dokumen/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-dokumen');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(61, $this->session_permissions)) {
            if (!$this->validate($this->statusDokumenModel->validationRules, $this->statusDokumenModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->statusDokumenModel->cekStatusDokumen($this->request->getPost('nama_status')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Status Dokumen yang Anda masukkan sudah terdaftar. Silakan gunakan nama status dokumen lain']);
            }


            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'nama_status'    => $this->request->getPost('nama_status'),
                'status'         => 'Aktif',
                'user_created'   => $userID,
                'created_at'     => $now
            ];

            $inserted = $this->statusDokumenModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/status-dokumen');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-dokumen');
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
        if (in_array(62, $this->session_permissions)) {
            $statusDokumen = $this->statusDokumenModel->getStatusDokumenId(stringEncryptions('decrypt', $id));

            if (!$statusDokumen) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/status-dokumen');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['statusDokumen'] = $statusDokumen;

            return view('setting/status_dokumen/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-dokumen');
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
        if (in_array(62, $this->session_permissions)) {

            if (!$this->validate($this->statusDokumenModel->validationRules, $this->statusDokumenModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('nama_status') != $this->request->getPost('nama_status_old')) {
                if ($this->statusDokumenModel->cekStatusDokumen($this->request->getPost('nama_status')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Status Dokumen yang Anda masukkan sudah terdaftar. Silakan gunakan nama status dokumen lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'nama_status'    => $this->request->getPost('nama_status'),
                'status'         => $this->request->getPost('status') ?? "Tidak Aktif",
                'user_updated'   => $userID,
                'updated_at'     => $now
            ];
            $updated = $this->statusDokumenModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/status-dokumen');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-dokumen');
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
        if (in_array(63, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->statusDokumenModel->update($decodeId, $data);

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
