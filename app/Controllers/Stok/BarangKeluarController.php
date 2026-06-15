<?php

namespace App\Controllers\Stok;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\DokumenModel;
use App\Models\BarcodeValueModel;
use App\Models\HistoriProdukModel;
use App\Models\DetailBarangKeluarModel;
use App\Models\BarangKeluarModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;
use CodeIgniter\Database\Exceptions\DatabaseException;

class BarangKeluarController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $kategoriModel;
    protected $produkModel;
    protected $supplierModel;
    protected $userModel;
    protected $dokumenModel;
    protected $barcodeValueModel;
    protected $historiProdukModel;
    protected $detailBarangKeluarModel;
    protected $barangKeluarModel;


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
        $this->barcodeValueModel = new BarcodeValueModel();
        $this->historiProdukModel = new HistoriProdukModel();
        $this->detailBarangKeluarModel = new DetailBarangKeluarModel();
        $this->barangKeluarModel = new BarangKeluarModel();

        $this->title = 'Barang Keluar';
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
        if (in_array(31, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('stok/barang_keluar/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok');
        }
    }

    public function getBarangKeluars()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $totalRecords = $this->barangKeluarModel->countAllBarangKeluar();
        $totalFiltered = $this->barangKeluarModel->countFilteredBarangKeluar($searchValue);
        $barangKeluars = $this->barangKeluarModel->getBarangKeluarData($length, $start, $searchValue);

        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $barangKeluars,
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
        if (in_array(31, $this->session_permissions)) {
            $barangKeluar = $this->barangKeluarModel->getBarangKeluarId(stringEncryptions('decrypt', $id));

            if (!$barangKeluar) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.x');
                return redirect()->to('/stok/barang-keluar');
            }

            $detailBarangKeluar = $this->detailBarangKeluarModel->getDetailBarangKeluarId(stringEncryptions('decrypt', $id));

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['id'] = $id;
            $data['barangKeluar'] = $barangKeluar;
            $data['detailBarangKeluar'] = $detailBarangKeluar;

            return view('stok/barang_keluar/show', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/barang-keluar');
        }
    }


    public function cetak_struk()
    {
        if (in_array(32, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Cetak Struk';
            return view('stok/barang_keluar/cetak', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/barang-keluar');
        }
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(32, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('stok/barang_keluar/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/barang-keluar');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(32, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $no_dokumen = $this->barangKeluarModel->generate_no_dokumen();
                $input = $this->request->getPost();
                $barangKeluar = $input['barangKeluar'];


                // 1. Insert barang_masuk
                $data = [
                    'no_dokument'       => $no_dokumen,
                    'tgl_keluar'        => $barangKeluar['tgl_keluar'],
                    'jam_keluar'        => $barangKeluar['jam_keluar'],
                    'keterangan'        => $barangKeluar['keterangan'],
                    'total_harga'       => $barangKeluar['total_bayar'],
                    'status_process'    => "Done",
                    'status'            => "Aktif",
                    'user_created'      => session()->get('user_id'),
                    'created_at'        => date('Y-m-d H:i:s')
                ];
                $barangKeluarId = $this->barangKeluarModel->insert($data, true);
                if (!$barangKeluarId) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal simpan barang keluar.'
                    ]);
                }

                // 2. Insert produkList (detail_barang_masuk)
                $produkList = $input['produkList'];
                foreach ($produkList as $item) {
                    $qtyKeluar = (int) $item['qty'];
                    $barcodeValue = $item['barcode_value'];

                    $detail = [
                        'barcode_value'     => $barcodeValue,
                        'qty'               => $item['qty'],
                        'produk_id'         => $item['produk_id'],
                        'barang_keluar_id'  => $barangKeluarId,
                        'user_created'      => session()->get('user_id'),
                        'created_at'        => date('Y-m-d H:i:s')
                    ];
                    $detailId = $this->detailBarangKeluarModel->insert($detail, true);
                    if (!$detailId) {
                        $this->db->transRollback();
                        return $this->response->setJSON([
                            'status' => false,
                            'message' => 'Gagal simpan detail barang keluar.'
                        ]);
                    }

                    // update stok
                    $produkRow = $this->db->query("SELECT * FROM tbl_m_produk WHERE produk_id = ? FOR UPDATE", [$item['produk_id']])->getRowArray();
                    if ($produkRow) {
                        // Catat riwayat produk
                        $historiProdukData = [
                            'kode_transaksi'          => $no_dokumen,
                            'tipe'                    => 'OUT',
                            'qty_out'                 => $qtyKeluar,
                            'barang_keluar_id'        => $barangKeluarId,
                            'detail_barang_keluar_id' => $detailId,
                            'produk_id'               => $item['produk_id'],
                            'barcode_value'           => $barcodeValue,
                            'keterangan'              => 'Barang Keluar',
                            'status_barang_id'        => '2',
                            'user_created'            => session()->get('user_id'),
                            'created_at'              => date('Y-m-d H:i:s')
                        ];
                        $this->historiProdukModel->insert($historiProdukData);
                    } else {
                        throw new \Exception("Produk dengan ID {$item['produk_id']} tidak ditemukan.");
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
            return redirect()->to('/stok/barang-keluar');
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
        if (in_array(33, $this->session_permissions)) {
            $barangKeluar = $this->barangKeluarModel->getBarangKeluarId(stringEncryptions('decrypt', $id));

            if (!$barangKeluar) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/barang-keluar');
            }

            $detailBarangKeluar = $this->detailBarangKeluarModel->getDetailBarangKeluarId(stringEncryptions('decrypt', $id));

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['barangKeluar'] = $barangKeluar;
            $data['detailBarangKeluar'] = $detailBarangKeluar;

            return view('stok/barang_keluar/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/barang-keluar');
        }
    }

    public function tambah_produk()
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();

                $barang_keluar_id = stringEncryptions('decrypt', $this->request->getPost('barang_keluar_id'));
                $detail = [
                    'barang_keluar_id' => $barang_keluar_id,
                    'barcode_value'    => $this->request->getPost('barcode_value'),
                    'qty'              => $this->request->getPost('qty'),
                    'produk_id'        => $this->request->getPost('produk_id'),
                    'user_created'     => session()->get('user_id'),
                    'created_at'       => date('Y-m-d H:i:s')
                ];
                $insertId = $this->detailBarangKeluarModel->insert($detail, true);
                if (!$insertId) {
                    throw new \Exception("Gagal insert detail barang keluar.");
                }

                $produkRow = $this->db->query("SELECT * FROM tbl_m_produk WHERE produk_id = ? FOR UPDATE", [$this->request->getPost('produk_id')])->getRowArray();
                if ($produkRow) {
                    // Catat riwayat produk
                    $historiProdukData = [
                        'kode_transaksi'          => $this->request->getPost('no_dokument'),
                        'tipe'                    => 'OUT',
                        'qty_out'                 => $this->request->getPost('qty'),
                        'barang_keluar_id'        => $barang_keluar_id,
                        'detail_barang_keluar_id' => $insertId,
                        'produk_id'               => $this->request->getPost('produk_id'),
                        'barcode_value'           => $this->request->getPost('barcode_value'),
                        'keterangan'              => 'Barang Keluar',
                        'status_barang_id'        => '2',
                        'user_created'            => session()->get('user_id'),
                        'created_at'              => date('Y-m-d H:i:s')
                    ];
                    $this->historiProdukModel->insert($historiProdukData);
                } else {
                    throw new \Exception("Produk dengan ID {$this->request->getPost('produk_id')} tidak ditemukan.");
                }

                $update = $this->barangKeluarModel->update($barang_keluar_id, [
                    'total_harga' => $this->request->getPost('total_harga'),
                    'user_updated'    => session()->get('user_id'),
                    'updated_at'      => date('Y-m-d H:i:s')
                ]);
                if (!$update) {
                    throw new \Exception("Gagal update total harga.");
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
            $harga_jual = $this->request->getPost('harga_jual');
            $total_harga = $this->request->getPost('total_harga');
            $barang_keluar_id = $this->request->getPost('barang_keluar_id');
            $decodeId = stringEncryptions('decrypt', $barang_keluar_id);

            $userId = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'total_harga' => ($total_harga - $harga_jual),
                'user_updated' => $userId,
                'updated_at' => $now
            ];

            $deleted = $this->barangKeluarModel->update($decodeId, $data);

            $data_detail = [
                'user_deleted' => $userId,
                'deleted_at' => $now
            ];
            $deletedDetail = $this->detailBarangKeluarModel->update($detail_id, $data_detail);

            // update stok
            $data_histori = [
                'user_deleted' => $userId,
                'deleted_at' => $now
            ];
            $deletedHistori = $this->historiProdukModel
                ->where("barang_keluar_id", $decodeId)
                ->where("detail_barang_keluar_id", $detail_id)
                ->set($data_histori) // Masukkan data lewat set()
                ->update();

            if ($this->db->transStatus() === false || !$deleted || !$deletedDetail || !$deletedHistori) {
                $this->db->transRollback();
                setToast('error', 'Gagal menghapus data. Silakan coba beberapa saat lagi.');
            } else {
                $this->db->transCommit();
                setToast('success', 'Data berhasil dihapus.');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->db->transRollback();
            setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut');
        }
    }

    public function updateQty()
    {
        $id = $this->request->getPost('id');
        $qty = $this->request->getPost('qty');
        $harga_jual = $this->request->getPost('harga_jual');
        $total_harga = $this->request->getPost('total_harga');

        $userId = session()->get('user_id');
        $now = date('Y-m-d H:i:s');

        if (!$id || !is_numeric($qty)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Data tidak valid']);
        }

        $this->db->transBegin();

        try {
            // Lock baris untuk update
            $sql = "SELECT qty, barang_keluar_id
                FROM tbl_t_detail_barang_keluar
                WHERE detail_barang_keluar_id = ?
                FOR UPDATE";
            $detail = $this->db->query($sql, [$id])->getRow();

            if (!$detail) {
                $this->db->transRollback();
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Data tidak ditemukan']);
            }

            // Validasi qty
            if ($qty < 1) {
                $this->db->transRollback();
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Qty tidak dapat lebih kecil dari 1']);
            }

            if ((int)$qty === (int)$detail->qty) {
                $this->db->transRollback();
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Qty tidak berubah. Tidak ada yang diperbarui.']);
            }

            $this->detailBarangKeluarModel->update($id, ['qty' => $qty]);

            $data_history = [
                "qty_out" => $qty,
                "user_updated" => $userId,
                "updated_at" => $now,
            ];
            $this->historiProdukModel
                ->where("barang_keluar_id", $detail->barang_keluar_id)
                ->where("detail_barang_keluar_id", $id)
                ->set($data_history)
                ->update();

            // Hitung ulang total qty
            $qtyLama = (int)$detail->qty;
            $qtyBaru = (int)$qty;

            $selisihQty = $qtyBaru - $qtyLama;

            $selisihHarga = $selisihQty * $harga_jual;

            $totalHarga = $total_harga + $selisihHarga;

            // Update ke barang_masuk
            $this->barangKeluarModel->update($detail->barang_keluar_id, ['total_harga' => $totalHarga]);

            $this->db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'total_harga' => $totalHarga
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
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
        if (in_array(33, $this->session_permissions)) {

            try {

                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userID = session()->get('user_id');
                $now = date('Y-m-d H:i:s');
                $input = $this->request->getRawInput();
                $barangKeluar = $input['barangKeluar'];

                // 1. Insert barang_masuk
                $data = [
                    'tgl_keluar'         => $barangKeluar['tgl_keluar'],
                    'jam_keluar'         => $barangKeluar['jam_keluar'],
                    'keterangan'         => $barangKeluar['keterangan'],
                    'total_harga'        => $barangKeluar['total_harga'],
                    'user_updated'       => $userID,
                    'updated_at'         => $now
                ];
                $barangKeluarId = $this->barangKeluarModel->update($decodeId, $data);
                if (!$barangKeluarId) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal memperbarui barang keluar.'
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
            return redirect()->to('/stok/barang-keluar');
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
        if (in_array(34, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $barangKeluarId = stringEncryptions('decrypt', $id);

                $dataSoftDelete = [
                    'user_deleted' => session()->get('user_id'),
                    'deleted_at' => date('Y-m-d H:i:s')
                ];
                $this->db->table('tbl_t_barang_keluar')
                    ->where('barang_keluar_id', $barangKeluarId)
                    ->update($dataSoftDelete);

                $this->db->table('tbl_t_detail_barang_keluar')
                    ->where('barang_keluar_id', $barangKeluarId)
                    ->update($dataSoftDelete);

                // update stok
                $this->db->table('tbl_h_produk')
                    ->where('barang_keluar_id', $barangKeluarId)
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
