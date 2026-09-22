<?php

namespace App\Controllers\Setting;

use App\Models\StatusPembayaranModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class StatusPembayaranController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $statusPembayaranModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->statusPembayaranModel = new StatusPembayaranModel();

        $this->title = 'Status Pembayaran';
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
        if (in_array(56, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/status_pembayaran/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getStatusPembayarans()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->statusPembayaranModel->countAllResults();
        $totalFiltered = $this->statusPembayaranModel->countFilteredStatusPembayaran($searchValue);
        $statusPembayaran = $this->statusPembayaranModel->getStatusPembayaranData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $statusPembayaran,
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
        if (in_array(56, $this->session_permissions)) {
            $statusPembayaran = $this->statusPembayaranModel->getStatusPembayaranId(stringEncryptions('decrypt', $id));

            if (!$statusPembayaran) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/status-pembayaran');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['statusPembayaran'] = $statusPembayaran;

            return view('setting/status_pembayaran/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-pembayaran');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(57, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/status_pembayaran/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-pembayaran');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(57, $this->session_permissions)) {
            if (!$this->validate($this->statusPembayaranModel->validationRules, $this->statusPembayaranModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->statusPembayaranModel->cekStatusPembayaran($this->request->getPost('nama_status')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Status Pembayaran yang Anda masukkan sudah terdaftar. Silakan gunakan nama status pembayaran lain']);
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

            $inserted = $this->statusPembayaranModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/status-pembayaran');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-pembayaran');
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
        if (in_array(58, $this->session_permissions)) {
            $statusPembayaran = $this->statusPembayaranModel->getStatusPembayaranId(stringEncryptions('decrypt', $id));

            if (!$statusPembayaran) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/status-pembayaran');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['statusPembayaran'] = $statusPembayaran;

            return view('setting/status_pembayaran/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-pembayaran');
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
        if (in_array(58, $this->session_permissions)) {

            if (!$this->validate($this->statusPembayaranModel->validationRules, $this->statusPembayaranModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('nama_status') != $this->request->getPost('nama_status_old')) {
                if ($this->statusPembayaranModel->cekStatusPembayaran($this->request->getPost('nama_status')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Status Pembayaran yang Anda masukkan sudah terdaftar. Silakan gunakan nama status pembayaran lain']);
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
            $updated = $this->statusPembayaranModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/status-pembayaran');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/status-pembayaran');
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
        if (in_array(59, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->statusPembayaranModel->update($decodeId, $data);

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
