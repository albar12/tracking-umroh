<?php

namespace App\Controllers\Setting;

use App\Models\JenisKamarModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class JenisKamarController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $jenisKamarModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->jenisKamarModel = new JenisKamarModel();

        $this->title = 'Jenis Kamar';
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
        if (in_array(52, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/jenis_kamar/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getJenisKamars()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->jenisKamarModel->countAllResults();
        $totalFiltered = $this->jenisKamarModel->countFilteredJenisKamar($searchValue);
        $jenisKamars = $this->jenisKamarModel->getJenisKamarData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $jenisKamars,
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
        if (in_array(52, $this->session_permissions)) {
            $jenisKamar = $this->jenisKamarModel->getJenisKamarId(stringEncryptions('decrypt', $id));

            if (!$jenisKamar) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/jenis-kamar');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['jenisKamar'] = $jenisKamar;

            return view('setting/jenis_kamar/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/jenis_kamar');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(53, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/jenis_kamar/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/jenis-kamar');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(53, $this->session_permissions)) {
            if (!$this->validate($this->jenisKamarModel->validationRules, $this->jenisKamarModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->jenisKamarModel->cekJenisKamar($this->request->getPost('kamar')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Jenis Kamar yang Anda masukkan sudah terdaftar. Silakan gunakan nama jenis kamar lain']);
            }


            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'kamar'      => $this->request->getPost('kamar'),
                'status'         => 'Aktif',
                'user_created'   => $userID,
                'created_at'     => $now
            ];

            $inserted = $this->jenisKamarModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/jenis-kamar');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/jenis-kamar');
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
        if (in_array(54, $this->session_permissions)) {
            $jenisKamar = $this->jenisKamarModel->getJenisKamarId(stringEncryptions('decrypt', $id));

            if (!$jenisKamar) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/jenis-kamar');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['jenisKamar'] = $jenisKamar;

            return view('setting/jenis_kamar/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/jenis-kamar');
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
        if (in_array(54, $this->session_permissions)) {

            if (!$this->validate($this->jenisKamarModel->validationRules, $this->jenisKamarModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('kamar') != $this->request->getPost('kamar_old')) {
                if ($this->jenisKamarModel->cekPekerjaan($this->request->getPost('kamar')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Jenis Kamar yang Anda masukkan sudah terdaftar. Silakan gunakan nama jenis kamar lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'kamar'      => $this->request->getPost('kamar'),
                'status'         => $this->request->getPost('status') ?? "Tidak Aktif",
                'user_updated'   => $userID,
                'updated_at'     => $now
            ];
            $updated = $this->jenisKamarModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/jenis-kamar');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/jenis-kamar');
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
        if (in_array(55, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->jenisKamarModel->update($decodeId, $data);

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
