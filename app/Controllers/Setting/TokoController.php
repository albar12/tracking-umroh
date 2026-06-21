<?php

namespace App\Controllers\Setting;

use App\Models\TokoModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class TokoController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $tokoModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->tokoModel = new TokoModel();

        $this->title = 'Toko';
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
            return view('setting/toko/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting');
        }
    }

    public function getTokos()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->tokoModel->countAllResults();
        $totalFiltered = $this->tokoModel->countFilteredToko($searchValue);
        $suppliers = $this->tokoModel->getTokoData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $suppliers,
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
            $toko = $this->tokoModel->getTokoId(stringEncryptions('decrypt', $id));

            if (!$toko) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/toko');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['toko'] = $toko;

            return view('setting/toko/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/toko');
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
            return view('setting/toko/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/toko');
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
            if (!$this->validate($this->tokoModel->validationRules, $this->tokoModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->tokoModel->cekToko($this->request->getPost('nama_toko')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Outlet yang Anda masukkan sudah terdaftar. Silakan gunakan nama outlet lain']);
            }

            if (!$this->request->getFile('logo')) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Logo Outlet wajib diisi.']);
            }

            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $file = $this->request->getFile('logo');
            if ($file->isValid() && !$file->hasMoved()) {
                $newFileName = $file->getRandomName();
                $file->move('file_upload/toko/', $newFileName);
                $newFileName = 'file_upload/toko/' . $newFileName;
            }

            $data = [
                'nama_toko'      => $this->request->getPost('nama_toko'),
                'email'          => $this->request->getPost('email'),
                'no_telp'        => $this->request->getPost('no_telp'),
                'alamat'         => $this->request->getPost('alamat'),
                'logo'           => isset($newFileName) ? $newFileName : null,
                'status'         => 'Aktif',
                'user_created'   => $userID,
                'created_at'     => $now
            ];

            $inserted = $this->tokoModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/toko');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/toko');
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
            $toko = $this->tokoModel->getTokoId(stringEncryptions('decrypt', $id));

            if (!$toko) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/toko');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['toko'] = $toko;

            return view('setting/toko/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/toko');
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

            if (!$this->validate($this->tokoModel->validationRules, $this->tokoModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('nama_toko') != $this->request->getPost('nama_toko_old')) {
                if ($this->tokoModel->cekToko($this->request->getPost('nama_toko')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Outlet yang Anda masukkan sudah terdaftar. Silakan gunakan nama outlet lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $file = $this->request->getFile('logo');
            if ($file->isValid() && !$file->hasMoved()) {
                $newFileName = $file->getRandomName();
                $file->move('file_upload/toko/', $newFileName);
                $newFileName = 'file_upload/toko/' . $newFileName;
            }

            $data = [
                'nama_toko'      => $this->request->getPost('nama_toko'),
                'email'          => $this->request->getPost('email'),
                'no_telp'        => $this->request->getPost('no_telp'),
                'alamat'         => $this->request->getPost('alamat'),
                'logo'           => isset($newFileName) ? $newFileName : $this->request->getPost('logoOld'),
                'status'         => $this->request->getPost('status'),
                'user_updated'   => $userID,
                'updated_at'     => $now
            ];
            $updated = $this->tokoModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                if ($this->request->getPost('logoOld')) {
                    unlink($this->request->getPost('logoOld'));
                }
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/toko');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/toko');
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

                $deleted = $this->tokoModel->update($decodeId, $data);

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
