<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class JamaahController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $kategoriModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->kategoriModel = new KategoriModel();

        $this->title = "Jama'ah";
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
        if (in_array(6, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('jamaah/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/home');
        }
    }

    public function getKategoris()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian


        $totalRecords = $this->kategoriModel->countAllKategori();
        $totalFiltered = $this->kategoriModel->countFilteredKategori($searchValue);
        $kategoris = $this->kategoriModel->getKategoriData($length, $start, $searchValue);


        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $kategoris,
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
        if (in_array(6, $this->session_permissions)) {
            $kategori = $this->kategoriModel->getKategoriId(stringEncryptions('decrypt', $id));

            if (!$kategori) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/kategori');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['kategori'] = $kategori;

            return view('stok/kategori/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/kategori');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(7, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('stok/kategori/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/kategori');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(7, $this->session_permissions)) {
            if (!$this->validate($this->kategoriModel->validationRules, $this->kategoriModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->kategoriModel->cekKategori($this->request->getPost('kategori')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Kategori yang Anda masukkan sudah terdaftar. Silakan gunakan kategori lain']);
            }

            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'kategori'      => $this->request->getPost('kategori'),
                'status'        => 'Aktif',
                'user_created'  => $userID,
                'created_at'    => $now
            ];

            $inserted = $this->kategoriModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/stok/kategori');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/kategori');
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
        if (in_array(8, $this->session_permissions)) {
            $kategori = $this->kategoriModel->getKategoriId(stringEncryptions('decrypt', $id));

            if (!$kategori) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/kategori');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['kategori'] = $kategori;

            return view('stok/kategori/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/kategori');
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
        if (in_array(8, $this->session_permissions)) {

            if (
                $this->request->getPost('kategori') != $this->request->getPost('kategoriOld') &&
                $this->kategoriModel->cekKategori($this->request->getPost('kategori')) > 0
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Kategori yang Anda masukkan sudah terdaftar. Silakan gunakan kategori lain']);
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'kategori'      => $this->request->getPost('kategori'),
                'status'        => $this->request->getPost('status'),
                'user_updated'  => $userID,
                'updated_at'    => $now
            ];

            $updated = $this->kategoriModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/stok/kategori');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/kategori');
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
        if (in_array(9, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->kategoriModel->update($decodeId, $data);

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
