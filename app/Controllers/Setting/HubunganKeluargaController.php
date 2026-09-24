<?php

namespace App\Controllers\Setting;

use App\Models\HubunganKeluargaModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class HubunganKeluargaController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $hubunganKeluargaModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->hubunganKeluargaModel = new HubunganKeluargaModel();

        $this->title = 'Hubungan Keluarga';
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
        if (in_array(68, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/hubungan_keluarga/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getHubunganKeluargas()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->hubunganKeluargaModel->countAllHubunganKeluarga();
        $totalFiltered = $this->hubunganKeluargaModel->countFilteredHubunganKeluarga($searchValue);
        $hubunganKeluarga = $this->hubunganKeluargaModel->getHubunganKeluargaData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $hubunganKeluarga,
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
        if (in_array(68, $this->session_permissions)) {
            $hubunganKeluarga = $this->hubunganKeluargaModel->getHubunganKeluargaId(stringEncryptions('decrypt', $id));

            if (!$hubunganKeluarga) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/hubungan-keluarga');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['hubunganKeluarga'] = $hubunganKeluarga;

            return view('setting/hubungan_keluarga/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/hubungan-keluarga');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(69, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/hubungan_keluarga/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/hubungan-keluarga');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(69, $this->session_permissions)) {
            if (!$this->validate($this->hubunganKeluargaModel->validationRules, $this->hubunganKeluargaModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->hubunganKeluargaModel->cekHubunganKeluarga($this->request->getPost('hubungan')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Hubungan Keluarga yang Anda masukkan sudah terdaftar. Silakan gunakan nama hubungan keluarga lain']);
            }


            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'hubungan'       => $this->request->getPost('hubungan'),
                'status'         => 'Aktif',
                'user_created'   => $userID,
                'created_at'     => $now
            ];

            $inserted = $this->hubunganKeluargaModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/hubungan-keluarga');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/hubungan-keluarga');
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
        if (in_array(70, $this->session_permissions)) {
            $hubunganKeluarga = $this->hubunganKeluargaModel->getHubunganKeluargaId(stringEncryptions('decrypt', $id));

            if (!$hubunganKeluarga) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/hubungan-keluarga');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['hubunganKeluarga'] = $hubunganKeluarga;

            return view('setting/hubungan_keluarga/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/hubungan-keluarga');
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
        if (in_array(70, $this->session_permissions)) {

            if (!$this->validate($this->hubunganKeluargaModel->validationRules, $this->hubunganKeluargaModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('hubungan') != $this->request->getPost('hubungan_old')) {
                if ($this->hubunganKeluargaModel->cekHubunganKeluarga($this->request->getPost('hubungan')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Hubungan Keluarga yang Anda masukkan sudah terdaftar. Silakan gunakan nama hubungan keluarga lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'hubungan'       => $this->request->getPost('hubungan'),
                'status'         => $this->request->getPost('status') ?? "Tidak Aktif",
                'user_updated'   => $userID,
                'updated_at'     => $now
            ];
            $updated = $this->hubunganKeluargaModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/hubungan-keluarga');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/hubungan-keluarga');
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
        if (in_array(71, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->hubunganKeluargaModel->update($decodeId, $data);

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
