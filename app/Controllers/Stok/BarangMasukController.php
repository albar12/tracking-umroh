<?php

namespace App\Controllers\Stok;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\DokumenModel;
use App\Models\DetailBarangMasukModel;
use App\Models\BarangMasukModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;
use CodeIgniter\Database\Exceptions\DatabaseException;

class BarangMasukController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $kategoriModel;
    protected $produkModel;
    protected $supplierModel;
    protected $userModel;
    protected $dokumenModel;
    protected $detailBarangMasukModel;
    protected $barangMasukModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->kategoriModel = new KategoriModel();
        $this->produkModel = new ProdukModel();
        $this->supplierModel = new SupplierModel();
        $this->userModel = new UserModel();
        $this->dokumenModel = new DokumenModel();
        $this->detailBarangMasukModel = new DetailBarangMasukModel();
        $this->barangMasukModel = new BarangMasukModel();

        $this->title = 'Barang Masuk';
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
        if (in_array(26, $this->session_permissions)) {
            $data['suppliers'] = $this->supplierModel->get_all_supplier();
            $data['title'] = $this->title;
            return view('stok/barang_masuk/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok');
        }
    }

    public function getBarangMasuks()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $filters = $request->getPost('filters');

        $totalRecords = $this->barangMasukModel->countAllBarangMasuk();
        $totalFiltered = $this->barangMasukModel->countFilteredBarangMasuk($searchValue, $filters);
        $barangMasuks = $this->barangMasukModel->getBarangMasukData($length, $start, $searchValue, $filters);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $barangMasuks,
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
        if (in_array(26, $this->session_permissions)) {
            $barangMasuk = $this->barangMasukModel->getBarangMasukId(stringEncryptions('decrypt', $id));

            if (!$barangMasuk) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/barang-masuk');
            }

            $detailBarangMasuk = $this->detailBarangMasukModel->getDetailBarangMasukId(stringEncryptions('decrypt', $id));

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['id'] = $id;
            $data['suppliers'] = $this->supplierModel->get_all_supplier();
            $data['diterima'] = $this->userModel->get_all_admin_stok();
            $data['kategoris'] = $this->kategoriModel->get_all_ketgori();
            $data['dokumen'] = $this->dokumenModel->dokumen_barang_masuk(stringEncryptions('decrypt', $id));
            $data['barangMasuk'] = $barangMasuk;
            $data['detailBarangMasuk'] = $detailBarangMasuk;

            return view('stok/barang_masuk/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/produk');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(27, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            $data['suppliers'] = $this->supplierModel->get_all_supplier();
            $data['diterima'] = $this->userModel->get_all_admin_stok();
            $data['kategoris'] = $this->kategoriModel->get_all_ketgori();
            return view('stok/barang_masuk/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/barang-masuk');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(27, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $no_dokumen = $this->barangMasukModel->generate_no_dokumen();
                $input = $this->request->getPost();
                $barangMasuk = $input['barangMasuk'];

                // 1. Insert barang_masuk
                $data = [
                    'no_dokument'        => $no_dokumen,
                    'no_dokument_supplier' => $barangMasuk['no_dokument_supplier'],
                    'tgl_terima'         => $barangMasuk['tgl_terima'],
                    'jam_terima'         => $barangMasuk['jam_terima'],
                    'diterima'           => $barangMasuk['diterima'],
                    'diserahkan'         => $barangMasuk['diserahkan'],
                    'keterangan'         => $barangMasuk['keterangan'],
                    'total_produk'       => $barangMasuk['total_produk'],
                    'status_approval'    => "Proses",
                    'status'             => "Aktif",
                    'user_created'       => session()->get('user_id'),
                    'created_at'         => date('Y-m-d H:i:s')
                ];
                $barangMasukId = $this->barangMasukModel->insert($data, true);
                if (!$barangMasukId) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal simpan barang masuk.'
                    ]);
                }

                // 2. Insert produkList (detail_barang_masuk)
                $produkList = $input['produkList'];
                foreach ($produkList as $item) {
                    $detail = [
                        'qty'             => $item['qty'],
                        'keterangan'      => $item['ket'],
                        'produk_id'       => $item['produk_id'],
                        'barang_masuk_id' => $barangMasukId,
                        'user_created'    => session()->get('user_id'),
                        'created_at'      => date('Y-m-d H:i:s')
                    ];
                    $detailId = $this->detailBarangMasukModel->insert($detail, true);
                    if (!$detailId) {
                        $this->db->transRollback();
                        return $this->response->setJSON([
                            'status' => false,
                            'message' => 'Gagal simpan detail barang masuk.'
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
            return redirect()->to('/stok/barang-masuk');
        }
    }

    public function input_barang_masuk($id = null, $produk = null)
    {
        try {
            // Decrypt jika kamu pakai sistem enkripsi
            $brang_masuk_id = stringEncryptions('decrypt', $id);
            $produk = stringEncryptions('decrypt', $produk);

            $barang_masuk = $this->barangMasukModel->getBarangMasukId($brang_masuk_id);
            $produks = $this->detailBarangMasukModel->getDetailBarangMasukId($brang_masuk_id);

            // Data yang akan dikirim ke view
            $data = [
                'title' => $this->title,
                'sub' => 'Input Stok',
                'id' => $id,
                'produk' => $produk,
                'produks' => $produks,
                'barang_masuk' => $barang_masuk,
            ];
            return view('stok/transaksi/barang_masuk/proses', $data);
        } catch (\Exception $e) {
            log_message('error', 'Gagal input barang masuk: ' . $e->getMessage());
            setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut.');
            return redirect()->to('/stok/barang_masuk');
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
        if (in_array(28, $this->session_permissions)) {
            $barangMasuk = $this->barangMasukModel->getBarangMasukId(stringEncryptions('decrypt', $id));

            if (!$barangMasuk) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/barang-masuk');
            }

            $detailBarangMasuk = $this->detailBarangMasukModel->getDetailBarangMasukId(stringEncryptions('decrypt', $id));

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['suppliers'] = $this->supplierModel->get_all_supplier();
            $data['diterima'] = $this->userModel->get_all_admin_stok();
            $data['kategoris'] = $this->kategoriModel->get_all_ketgori();
            $data['barangMasuk'] = $barangMasuk;
            $data['detailBarangMasuk'] = $detailBarangMasuk;

            return view('stok/barang_masuk/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/barang-masuk');
        }
    }

    public function tambah_produk()
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();

                $barang_masuk_id = stringEncryptions('decrypt', $this->request->getPost('barang_masuk_id'));
                $detail = [
                    'barang_masuk_id' => $barang_masuk_id,
                    'qty'             => $this->request->getPost('qty'),
                    'keterangan'      => $this->request->getPost('keterangan'),
                    'produk_id'       => $this->request->getPost('produk_id'),
                    'user_created'    => session()->get('user_id'),
                    'created_at'      => date('Y-m-d H:i:s')
                ];
                $insertId = $this->detailBarangMasukModel->insert($detail, true);
                if (!$insertId) {
                    throw new \Exception("Gagal insert detail barang masuk.");
                }

                $update = $this->barangMasukModel->update($barang_masuk_id, [
                    'total_produk' => $this->request->getPost('total_produk'),
                    'user_updated'    => session()->get('user_id'),
                    'updated_at'      => date('Y-m-d H:i:s')
                ]);
                if (!$update) {
                    throw new \Exception("Gagal update total produk.");
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
            $qty = $this->request->getPost('qty');
            $total_produk = $this->request->getPost('total_produk');
            $barang_masuk_id = $this->request->getPost('barang_masuk_id');
            $decodeId = stringEncryptions('decrypt', $barang_masuk_id);

            $userId = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'total_produk' => ($total_produk - $qty),
                'user_updated' => $userId,
                'updated_at' => $now
            ];
            $deleted = $this->barangMasukModel->update($decodeId, $data);

            $data_detail = [
                'user_deleted' => $userId,
                'deleted_at' => $now
            ];
            $deletedDetail = $this->detailBarangMasukModel->update($detail_id, $data_detail);

            if ($this->db->transStatus() === false || !$deleted || !$deletedDetail) {
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

    public function updateQty()
    {
        $id = $this->request->getPost('id');
        $qty = $this->request->getPost('qty');

        if (!$id || !is_numeric($qty)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Data tidak valid']);
        }

        $this->db->transBegin();

        try {
            // Lock baris untuk update
            $sql = "SELECT qty_input, qty, barang_masuk_id
                FROM tbl_t_detail_barang_masuk
                WHERE detail_barang_masuk_id = ?
                FOR UPDATE";
            $detail = $this->db->query($sql, [$id])->getRow();

            if (!$detail) {
                $this->db->transRollback();
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
            }

            // Validasi qty
            $qtyInput = isset($detail->qty_input) ? $detail->qty_input : null;
            if ($qtyInput !== null && $qty < $qtyInput) {
                $this->db->transRollback();
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Qty lebih kecil dari qty_input']);
            }

            if ((int)$qty === (int)$detail->qty) {
                $this->db->transRollback();
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Qty tidak berubah. Tidak ada yang diperbarui.']);
            }

            $this->detailBarangMasukModel->update($id, ['qty' => $qty]);

            // Hitung ulang total qty
            $totalQty = $this->db->table('tbl_t_detail_barang_masuk')
                ->selectSum('qty')
                ->where('barang_masuk_id', $detail->barang_masuk_id)
                ->where('deleted_at', null)
                ->get()
                ->getRow()
                ->qty;

            // Update ke barang_masuk
            $this->barangMasukModel->update($detail->barang_masuk_id, ['total_produk' => $totalQty]);

            $this->db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'total_qty' => $totalQty
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Gagal update qty. Exception: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
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
        if (in_array(28, $this->session_permissions)) {

            try {

                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userID = session()->get('user_id');
                $now = date('Y-m-d H:i:s');
                $input = $this->request->getRawInput();
                $barangMasuk = $input['barangMasuk'];

                // 1. Insert barang_masuk
                $data = [
                    'no_dokument_supplier' => $barangMasuk['no_dokument_supplier'],
                    'tgl_terima'         => $barangMasuk['tgl_terima'],
                    'jam_terima'         => $barangMasuk['jam_terima'],
                    'diterima'           => $barangMasuk['diterima'],
                    'diserahkan'         => $barangMasuk['diserahkan'],
                    'keterangan'         => $barangMasuk['keterangan'],
                    'total_produk'       => $barangMasuk['total_produk'],
                    'user_updated'       => $userID,
                    'updated_at'         => $now
                ];
                $barangMasukId = $this->barangMasukModel->update($decodeId, $data);
                if (!$barangMasukId) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal memperbarui barang masuk.'
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
            return redirect()->to('/stok/barang-masuk');
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
        if (in_array(29, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $barangMasukId = stringEncryptions('decrypt', $id);

                $sumQtyInput = $this->db->table('tbl_t_detail_barang_masuk')
                    ->selectSum('qty_input')
                    ->where('barang_masuk_id', $barangMasukId)
                    ->where('deleted_at', null)
                    ->get()
                    ->getRow();
                $totalQtyInput = $sumQtyInput->qty_input ?? 0;
                if ($totalQtyInput > 0) {
                    throw new \Exception("Barang Masuk dalam proses input.");
                }

                $dataSoftDelete = [
                    'user_deleted' => session()->get('user_id'),
                    'deleted_at' => date('Y-m-d H:i:s')
                ];
                $this->db->table('tbl_t_barang_masuk')
                    ->where('barang_masuk_id', $barangMasukId)
                    ->update($dataSoftDelete);

                $this->db->table('tbl_t_detail_barang_masuk')
                    ->where('barang_masuk_id', $barangMasukId)
                    ->update($dataSoftDelete);

                if ($this->db->transStatus() === false) {
                    throw new \Exception("Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut.");
                }

                $this->db->transCommit();
                setToast('success', 'Data berhasil dihapus.');
            } catch (\Exception $e) {
                $this->db->transRollback();
                setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
        }
    }
}
