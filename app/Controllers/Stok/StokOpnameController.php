<?php

namespace App\Controllers\Stok;

use App\Models\StokOpnameModel;
use App\Models\KategoriModel;
use App\Models\HistoriProdukModel;
use App\Models\DetailStokOpnameModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;
use CodeIgniter\Database\Exceptions\DatabaseException;

class StokOpnameController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $stokOpnameModel;
    protected $kategoriModel;
    protected $historiProdukModel;
    protected $detailStokOpnameModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->stokOpnameModel = new StokOpnameModel();
        $this->kategoriModel = new KategoriModel();
        $this->historiProdukModel = new HistoriProdukModel();
        $this->detailStokOpnameModel = new DetailStokOpnameModel();

        $this->title = 'Stok Opname';
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
        if (in_array(45, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('stok/stok_opname/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok');
        }
    }

    public function getStokOpnames()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $filters = $request->getPost('filters');

        $totalRecords = $this->stokOpnameModel->countAllStokOpname();
        $totalFiltered = $this->stokOpnameModel->countFilteredStokOpname($searchValue, $filters);
        $stokOpnames = $this->stokOpnameModel->getStokOpnameData($length, $start, $searchValue, $filters);


        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $stokOpnames,
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
        if (in_array(45, $this->session_permissions)) {
            $stokOpname = $this->stokOpnameModel->getStokOpnameId(stringEncryptions('decrypt', $id));

            if (!$stokOpname) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/stok-opname');
            }

            $detailStokOpname = $this->detailStokOpnameModel->getDetailStokOpnameId(stringEncryptions('decrypt', $id));

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['stokOpname'] = $stokOpname;
            $data['detailStokOpname'] = $detailStokOpname;
            return view('stok/stok_opname/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/stok-opname');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(46, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            $data['kategoris'] = $this->kategoriModel->get_all_ketgori();
            return view('stok/stok_opname/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/stok-opname');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(46, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $no_dokumen = $this->stokOpnameModel->generate_no_dokumen();
                $input = $this->request->getPost();
                $stokOpname = $input['stokOpname'];

                // 1. Insert barang_masuk
                $data = [
                    'no_dokument'        => $no_dokumen,
                    'batch'              => $stokOpname['batch'],
                    'tgl_mulai'          => $stokOpname['tgl_mulai'],
                    'tgl_selesai'        => $stokOpname['tgl_selesai'],
                    'keterangan'         => $stokOpname['keterangan'],
                    'status_approval'    => "Proses",
                    'status'             => "Aktif",
                    'user_created'       => session()->get('user_id'),
                    'created_at'         => date('Y-m-d H:i:s')
                ];
                $soId = $this->stokOpnameModel->insert($data, true);
                if (!$soId) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal simpan stok opname.'
                    ]);
                }

                // 2. Insert produkList (detail_barang_masuk)
                $produkList = $input['produkList'];
                foreach ($produkList as $item) {
                    $detail = [
                        'produk_id'       => $item['produk_id'],
                        'so_id'           => $soId,
                        'user_created'    => session()->get('user_id'),
                        'created_at'      => date('Y-m-d H:i:s')
                    ];
                    $detailId = $this->detailStokOpnameModel->insert($detail, true);
                    if (!$detailId) {
                        $this->db->transRollback();
                        return $this->response->setJSON([
                            'status' => false,
                            'message' => 'Gagal simpan detail stok opname.'
                        ]);
                    }
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan dalam transaksi.'
                    ]);
                }
                $this->db->transCommit();
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data berhasil disimpan.'
                ]);
            } catch (DatabaseException $e) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan database.',
                    'error' => $e->getMessage()
                ]);
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut.'
                ]);
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/stok-opname');
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
        if (in_array(47, $this->session_permissions)) {
            $stokOpname = $this->stokOpnameModel->getStokOpnameId(stringEncryptions('decrypt', $id));

            if (!$stokOpname) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/stok-opname');
            }

            $detailStokOpname = $this->detailStokOpnameModel->getDetailStokOpnameId(stringEncryptions('decrypt', $id));

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['kategoris'] = $this->kategoriModel->get_all_ketgori();
            $data['stokOpname'] = $stokOpname;
            $data['detailStokOpname'] = $detailStokOpname;

            return view('stok/stok_opname/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/stok-opname');
        }
    }

    public function tambah_produk()
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();

                $so_id = stringEncryptions('decrypt', $this->request->getPost('so_id'));
                $detail = [
                    'so_id'           => $so_id,
                    'produk_id'       => $this->request->getPost('produk_id'),
                    'user_created'    => session()->get('user_id'),
                    'created_at'      => date('Y-m-d H:i:s')
                ];
                $insertId = $this->detailStokOpnameModel->insert($detail, true);
                if (!$insertId) {
                    throw new \Exception("Gagal insert detail stok opname.");
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan dalam transaksi.'
                    ]);
                }
                $this->db->transCommit();
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Produk berhasil ditambahkan.'
                ]);
            } catch (\Throwable $e) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat menambah produk.'
                ]);
            }
        }
    }

    public function delete_detail()
    {

        try {
            $this->db->transBegin();

            $detail_id = $this->request->getPost('detail_id');

            $userId = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data_detail = [
                'user_deleted' => $userId,
                'deleted_at' => $now
            ];
            $deletedDetail = $this->detailStokOpnameModel->update($detail_id, $data_detail);

            if ($this->db->transStatus() === false || !$deletedDetail) {
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
        if (in_array(47, $this->session_permissions)) {
            try {

                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userID = session()->get('user_id');
                $now = date('Y-m-d H:i:s');
                $input = $this->request->getRawInput();
                $stokOpname = $input['stokOpname'];

                if ($stokOpname['batch'] != $stokOpname['batchOld']) {
                    if ($this->stokOpnameModel->cekStokOpname($stokOpname['batch']) > 0) {
                        return $this->response->setJSON([
                            'status' => false,
                            'message' => 'Batch yang Anda masukkan sudah terdaftar. Silakan gunakan batch lain'
                        ]);
                    }
                }

                $data = [
                    'batch'              => $stokOpname['batch'],
                    'tgl_mulai'          => $stokOpname['tgl_mulai'],
                    'tgl_selesai'        => $stokOpname['tgl_selesai'],
                    'keterangan'         => $stokOpname['keterangan'],
                    'user_updated'       => $userID,
                    'updated_at'         => $now
                ];
                $soId = $this->stokOpnameModel->update($decodeId, $data);
                if (!$soId) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal memperbarui stok opname.'
                    ]);
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan dalam transaksi.'
                    ]);
                }
                $this->db->transCommit();
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data berhasil diperbaharui.'
                ]);
            } catch (\Throwable $th) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut',
                    'error' => $th->getMessage(),

                ]);
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/stok-opname');
        }
    }


    public function input_stok_opname($id = null, $produk = null)
    {

        try {
            // Decrypt jika kamu pakai sistem enkripsi
            $so_id = stringEncryptions('decrypt', $id);
            $produk = stringEncryptions('decrypt', $produk);

            $stokOpname = $this->stokOpnameModel->getStokOpnameId($so_id);
            $produks = $this->detailStokOpnameModel->getDetailStokOpnameIdProduk($so_id);
            $detailStokOpname = $this->detailStokOpnameModel->getDetailStokOpnameInputId($so_id);

            // Data yang akan dikirim ke view
            $data = [
                'title' => $this->title,
                'sub' => 'Input Stok Opname',
                'id' => $id,
                'produk' => $produk,
                'produks' => $produks,
                'stokOpname' => $stokOpname,
                'detailStokOpname' => $detailStokOpname,
            ];
            return view('stok/stok_opname/proses', $data);
        } catch (\Exception $e) {
            dd($e->getMessage());
            setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut.');
            return redirect()->to('/stok/stok-opname');
        }
    }

    public function input_so()
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();

                $produk_id = $this->request->getPost('produk_id');
                $so_id = stringEncryptions('decrypt', $this->request->getPost('so_id'));
                $detail_so_id = $this->request->getPost('detail_so_id');
                $qty = $this->request->getPost('qty');
                $barcode_value = $this->request->getPost('barcode_value');


                if (empty($so_id) || empty($detail_so_id) || empty($produk_id) || empty($qty)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Data tidak lengkap.'
                    ]);
                }

                // Update qty_input
                $updateQty = $this->db->table('tbl_t_detail_stok_opname')
                    ->set('qty', $qty)
                    ->set('barcode_value', $barcode_value)
                    ->where('detail_so_id', $detail_so_id)
                    ->update();

                if (!$updateQty) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal mengupdate qty.'
                    ]);
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan dalam transaksi.'
                    ]);
                }
                $this->db->transCommit();
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data berhasil diupdate.'
                ]);
            } catch (\Throwable $th) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $th->getMessage()
                ]);
            }
        }
    }

    public function batal_so()
    {
        if ($this->request->isAJAX()) {
            $this->db->transBegin(); // Mulai transaksi di sini
            try {
                $detail_so_id = $this->request->getPost('detail_so_id');

                if (empty($detail_so_id)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan. Silakan coba beberapa saat lagi.',
                    ]);
                }

                $data = [
                    "qty"          => null, // Di sini null akan terbaca dengan benar
                    "user_updated" => session()->get('user_id'),
                    "updated_at"   => date("Y-m-d H:i:s"),
                ];

                $deleted = $this->detailStokOpnameModel
                    ->where('detail_so_id', $detail_so_id) // Sesuaikan 'id' dengan primary key tabel Anda
                    ->set($data)
                    ->update();

                if (!$deleted) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal menghapus data. Silakan coba beberapa saat lagi.'
                    ]);
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();

                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan dalam transaksi.'
                    ]);
                }
                $this->db->transCommit();
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data berhasil dihapus.'
                ]);
            } catch (\Throwable $th) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $th->getMessage()
                ]);
            }
        }
    }


    public function hasil_input($id)
    {
        $so_id = stringEncryptions('decrypt', $id);

        $stokOpname = $this->stokOpnameModel->getStokOpnameId($so_id);

        if (!$stokOpname) {
            setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.x');
            return redirect()->to('/stok/stok-opname');
        }

        $detailStokOpname = $this->detailStokOpnameModel->getDetailStokOpnameId($so_id);

        $data = [
            'title' => 'Stok Opname',
            'sub' => 'Hasil Input',
            'so_id' => $id,
            'stokOpname' => $stokOpname,
            'detailStokOpname' => $detailStokOpname,
        ];
        return view('stok/stok_opname/hasil_input', $data);
    }

    public function syncron_stok_opname()
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();

                $so_id = stringEncryptions('decrypt', $this->request->getPost('so_id'));

                $detailStokOpname = $this->detailStokOpnameModel->getDetailStokOpnameId($so_id);

                foreach ($detailStokOpname as $r) {
                    $detail_so_id = $r['detail_so_id'];
                    $produk_id = $r['produk_id'];
                    $qty = $r['qty'];

                    $get_stok_gudang = $this->db->table("tbl_m_produk")
                        ->select("tbl_m_produk.produk_id, produk, COALESCE(SUM(tbl_h_produk.qty_in), 0) - COALESCE(SUM(tbl_h_produk.qty_out), 0) AS stok_tersedia")
                        ->join("tbl_h_produk", "tbl_h_produk.produk_id = tbl_m_produk.produk_id")
                        ->where("tbl_m_produk.produk_id", $produk_id)
                        ->where("tbl_m_produk.deleted_at", null)
                        ->where('tbl_h_produk.deleted_at', null)
                        ->get()
                        ->getRowArray();

                    $cek_stok = (int) $get_stok_gudang['stok_tersedia'] - (int) $qty;

                    if ($cek_stok > 0) {
                        $status_so = '2';
                        $qty_ditemukan = abs($cek_stok);
                        $qty_hilang = null;
                    } elseif ($cek_stok < 0) {
                        $status_so = '3';
                        $qty_ditemukan = null;
                        $qty_hilang = abs($cek_stok);
                    } else {
                        $status_so = '1';
                        $qty_ditemukan = null;
                        $qty_hilang = null;
                    }

                    $data = [
                        'status_so_id' => $status_so,
                        'qty_ditemukan' => $qty_ditemukan,
                        'qty_hilang' => $qty_hilang,
                        "user_updated" => session()->get('user_id'),
                        "updated_at"   => date("Y-m-d H:i:s"),
                    ];

                    $update = $this->detailStokOpnameModel->update($detail_so_id, $data);
                    if (!$update) {
                        $this->db->transRollback();
                        return $this->response->setJSON([
                            'status' => false,
                            'message' => 'Gagal memperbarui data detail stok opname.'
                        ]);
                    }
                }

                // Update data barang masuk
                $data = ['status_approval' => 'Need Approval'];
                $update = $this->stokOpnameModel->update($so_id, $data);
                if (!$update) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal memperbarui data stok opname.'
                    ]);
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan dalam transaksi.'
                    ]);
                }
                $this->db->transCommit();
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data berhasil diperbarui.'
                ]);
            } catch (\Throwable $e) {
                $this->db->transRollback(); // Jangan lupa rollback di catch juga
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui data.'
                ]);
            }
        }
    }

    public function approval_stok_opname($id)
    {
        if (in_array(49, $this->session_permissions)) {
            $so_id = stringEncryptions('decrypt', $id);

            $stokOpname = $this->stokOpnameModel->getStokOpnameId($so_id);

            if (!$stokOpname) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.x');
                return redirect()->to('/stok/stok-opname');
            }

            $detailStokOpname = $this->detailStokOpnameModel->getDetailStokOpnameId($so_id);

            $data = [
                'title' => 'Stok Opname',
                'sub' => 'Approval',
                'so_id' => $id,
                'stokOpname' => $stokOpname,
                'detailStokOpname' => $detailStokOpname,
            ];
            return view('stok/stok_opname/approval', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
        }
        return redirect()->to('stok/stok-opname');
    }

    public function update_so()
    {
        if (in_array(49, $this->session_permissions)) {
            $so_id = stringEncryptions('decrypt', $this->request->getPost('so_id'));
            if (!$so_id) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data stok opname tidak ditemukan.'
                ]);
            }

            $userId = session()->get('user_id');
            $now = date('Y-m-d H:i:s');
            $noDokumen = $this->request->getPost('no_dokument');
            $status = $this->request->getPost('status');

            try {
                $this->db->transBegin();

                if ($status == 'Approve') {
                    $detailStokOpname = $this->detailStokOpnameModel->getDetailStokOpnameId($so_id);

                    foreach ($detailStokOpname as $item) {
                        $detail_so_id = $item['detail_so_id'];
                        $barcodeValue = $item['barcode_value'];
                        $produkId = $item['produk_id'];
                        $qty_ditemukan = (int) $item['qty_ditemukan'];
                        $qty_hilang = $item['qty_hilang'];
                        if ($qty_ditemukan) {
                            $produkRow = $this->db->query("SELECT * FROM tbl_m_produk WHERE produk_id = ? FOR UPDATE", [$produkId])->getRowArray();
                            if ($produkRow) {
                                // Catat riwayat produk
                                $historiProdukData = [
                                    'kode_transaksi' => $noDokumen,
                                    'tipe' => 'IN',
                                    'qty_in' => $qty_ditemukan,
                                    'so_id' => $so_id,
                                    'detail_so_id' => $detail_so_id,
                                    'produk_id' => $produkId,
                                    'barcode_value' => $barcodeValue,
                                    'keterangan' => 'Stok Opname',
                                    'status_barang_id' => '3',
                                    'user_created' => $userId,
                                    'created_at' => $now
                                ];
                                $this->historiProdukModel->insert($historiProdukData);
                            } else {
                                throw new \Exception("Produk dengan ID {$produkId} tidak ditemukan.");
                            }
                        }

                        if ($qty_hilang) {
                            $produkRow = $this->db->query("SELECT * FROM tbl_m_produk WHERE produk_id = ? FOR UPDATE", [$produkId])->getRowArray();
                            if ($produkRow) {
                                // Catat riwayat produk
                                $historiProdukData = [
                                    'kode_transaksi' => $noDokumen,
                                    'tipe' => 'OUT',
                                    'qty_out' => $qty_hilang,
                                    'so_id' => $so_id,
                                    'detail_so_id' => $detail_so_id,
                                    'produk_id' => $produkId,
                                    'barcode_value' => $barcodeValue,
                                    'keterangan' => 'Stok Opname',
                                    'status_barang_id' => '4',
                                    'user_created' => $userId,
                                    'created_at' => $now
                                ];
                                $this->historiProdukModel->insert($historiProdukData);
                            } else {
                                throw new \Exception("Produk dengan ID {$produkId} tidak ditemukan.");
                            }
                        }
                    }

                    // Perbarui status barang masuk menggunakan Query Builder untuk konsistensi
                    $this->db->table('tbl_t_stok_opname')
                        ->where('so_id', $so_id)
                        ->update(['status_approval' => $status]);
                } else {
                    // Jika status selain Approve, perbarui status dan simpan keterangan menggunakan Query Builder
                    $data = [
                        'status_approval' => $status,
                        'keterangan_approval' => $this->request->getPost('keterangan'),
                    ];
                    $this->db->table('tbl_t_stok_opname')
                        ->where('so_id', $so_id)
                        ->update($data);
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    throw new \Exception("Gagal menyimpan transaksi.");
                }
                $this->db->transCommit();
                return $this->response->setJSON([
                    'status' => true,
                    'message' => 'Data berhasil disimpan.'
                ]);
            } catch (\Throwable $e) {
                dd($e->getMessage());
                $this->db->transRollback();
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Anda tidak memiliki hak akses untuk melakukan tindakan ini.'
            ]);
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
        if (in_array(48, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->stokOpnameModel->update($decodeId, $data);

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
