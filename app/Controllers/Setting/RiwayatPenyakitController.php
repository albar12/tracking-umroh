<?php

namespace App\Controllers\Setting;

use App\Models\RiwayatPenyakitModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class RiwayatPenyakitController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $riwayatPenyakitModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->riwayatPenyakitModel = new RiwayatPenyakitModel();

        $this->title = 'Riwayat Penyakit';
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
        if (in_array(72, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('setting/riwayat_penyakit/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/');
        }
    }

    public function getRiwayatPenyakits()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->riwayatPenyakitModel->countAllRiwayatPenyakit();
        $totalFiltered = $this->riwayatPenyakitModel->countFilteredRiwayatPenyakit($searchValue);
        $riwayatPenyakit = $this->riwayatPenyakitModel->getRiwayatPenyakitData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $riwayatPenyakit,
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
        if (in_array(72, $this->session_permissions)) {
            $riwayatPenyakit = $this->riwayatPenyakitModel->getRiwayatPenyakitId(stringEncryptions('decrypt', $id));

            if (!$riwayatPenyakit) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/riwayat-penyakit');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['riwayatPenyakit'] = $riwayatPenyakit;

            return view('setting/riwayat_penyakit/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/riwayat-penyakit');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(73, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('setting/riwayat_penyakit/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/riwayat-penyakit');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(73, $this->session_permissions)) {
            if (!$this->validate($this->riwayatPenyakitModel->validationRules, $this->riwayatPenyakitModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->riwayatPenyakitModel->cekRiwayatPenyakit($this->request->getPost('riwayat_penyakit_khusus')) > 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Nama Riwayat Penyakit Khusus yang Anda masukkan sudah terdaftar. Silakan gunakan nama riwayat penyakit khusus lain']);
            }


            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'riwayat_penyakit_khusus'       => $this->request->getPost('riwayat_penyakit_khusus'),
                'status'                        => 'Aktif',
                'user_created'                  => $userID,
                'created_at'                    => $now
            ];

            $inserted = $this->riwayatPenyakitModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/setting/riwayat-penyakit');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/riwayat-penyakit');
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
        if (in_array(74, $this->session_permissions)) {
            $riwayatPenyakit = $this->riwayatPenyakitModel->getRiwayatPenyakitId(stringEncryptions('decrypt', $id));

            if (!$riwayatPenyakit) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/setting/riwayat-penyakit');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['riwayatPenyakit'] = $riwayatPenyakit;

            return view('setting/riwayat_penyakit/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/riwayat-penyakit');
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
        if (in_array(74, $this->session_permissions)) {

            if (!$this->validate($this->riwayatPenyakitModel->validationRules, $this->riwayatPenyakitModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('riwayat_penyakit_khusus') != $this->request->getPost('riwayat_penyakit_khusus_old')) {
                if ($this->riwayatPenyakitModel->cekRiwayatPenyakit($this->request->getPost('riwayat_penyakit_khusus')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Nama Riwayat Penyakit Khusus yang Anda masukkan sudah terdaftar. Silakan gunakan nama riwayat penyakit khusus lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'riwayat_penyakit_khusus'       => $this->request->getPost('riwayat_penyakit_khusus'),
                'status'                        => $this->request->getPost('status') ?? "Tidak Aktif",
                'user_updated'                  => $userID,
                'updated_at'                    => $now
            ];
            $updated = $this->riwayatPenyakitModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/setting/riwayat-penyakit');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/setting/riwayat-penyakit');
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
        if (in_array(75, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->riwayatPenyakitModel->update($decodeId, $data);

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
