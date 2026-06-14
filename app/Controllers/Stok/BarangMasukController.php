<?php

namespace App\Controllers\Stok;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
use App\Models\DokumenModel;
use App\Models\BarcodeValueModel;
use App\Models\HistoriProdukModel;
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
    protected $barcodeValueModel;
    protected $historiProdukModel;
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
        $this->barcodeValueModel = new BarcodeValueModel();
        $this->historiProdukModel = new HistoriProdukModel();
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
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.x');
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
            $produks = $this->detailBarangMasukModel->getDetailBarangMasukIdProduk($brang_masuk_id);
            $barcodes = $this->barcodeValueModel->getBarcodeBarangMasuk($brang_masuk_id);

            // Data yang akan dikirim ke view
            $data = [
                'title' => $this->title,
                'sub' => 'Input Stok',
                'id' => $id,
                'produk' => $produk,
                'produks' => $produks,
                'barcodes' => $barcodes,
                'barang_masuk' => $barang_masuk,
            ];
            return view('stok/barang_masuk/proses', $data);
        } catch (\Exception $e) {
            dd($e->getMessage());
            setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut.');
            return redirect()->to('/stok/barang-masuk');
        }
    }

    public function input_barcode()
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();

                $barcode_value = $this->request->getPost('barcode_value');
                $produk_id = $this->request->getPost('produk_id');
                $barang_masuk_id = stringEncryptions('decrypt', $this->request->getPost('barang_masuk_id'));
                $detail_barang_masuk_id = $this->request->getPost('detail_barang_masuk_id');
                $qty_input = $this->request->getPost('qty_input');
                $tgl_expired = $this->request->getPost('tgl_expired');

                if (empty($barang_masuk_id) || empty($barcode_value) || empty($produk_id) || empty($qty_input)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Data tidak lengkap.'
                    ]);
                }

                $cek_data = $this->db->table("tbl_t_barcode_value")
                    ->where("barang_masuk_id", $barang_masuk_id)
                    ->where("produk_id", $produk_id)
                    ->get()
                    ->getNumRows();

                if ($cek_data > 0) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Data sudah ada, silahkan periksa kembali data yang di input'
                    ]);
                }

                // Lock baris untuk mencegah race condition
                $detail = $this->db->query("
                                            SELECT qty_input, qty 
                                            FROM tbl_t_detail_barang_masuk 
                                            WHERE detail_barang_masuk_id = ? 
                                            FOR UPDATE
                                            ", [$detail_barang_masuk_id])->getRow();

                if (!$detail || $detail->qty_input >= $detail->qty) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Jumlah Data sudah mencapai maksimal.'
                    ]);
                }

                // Input SN
                $data = [
                    'barang_masuk_id' => $barang_masuk_id,
                    'detail_barang_masuk_id' => $detail_barang_masuk_id,
                    'produk_id' => $produk_id,
                    'barcode_value' => $barcode_value,
                    'tgl_expired' => $tgl_expired,
                    'user_created' => session()->get('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $insertBarcode = $this->barcodeValueModel->insert($data, true);

                if (!$insertBarcode) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal menyimpan Data.'
                    ]);
                }

                // Update qty_input
                $updateQty = $this->db->table('tbl_t_detail_barang_masuk')
                    ->set('qty_input', $qty_input, false)
                    ->where('detail_barang_masuk_id', $detail_barang_masuk_id)
                    ->update();
                if (!$updateQty) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal mengupdate qty_input.'
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

    public function batal_barcode()
    {
        if ($this->request->isAJAX()) {
            $this->db->transBegin(); // Mulai transaksi di sini
            try {
                $barcode_value_id = $this->request->getPost('barcode_value_id');
                $detail_id = $this->request->getPost('detail_id');
                $qty_input = $this->request->getPost('qty_input');

                if (empty($barcode_value_id)) {
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan. Silakan coba beberapa saat lagi.',
                    ]);
                }

                $data = [
                    "user_deleted" => session()->get('user_id'),
                    "deleted_at" => date("Y-m-d H:i:s"),
                ];

                $deleted = $this->barcodeValueModel->update($barcode_value_id, $data);
                if (!$deleted) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal menghapus data. Silakan coba beberapa saat lagi.'
                    ]);
                }


                // Update total_produk
                $updateStokDetail = $this->db->table('tbl_t_detail_barang_masuk')
                    ->set('qty_input', 'COALESCE(qty_input, 0) - ' . $qty_input, false)
                    ->where('detail_barang_masuk_id', $detail_id)
                    ->update();

                if (!$updateStokDetail) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal mengupdate stok.'
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
        $brang_masuk_id = stringEncryptions('decrypt', $id);

        $barangMasuk = $this->barangMasukModel->getBarangMasukId($brang_masuk_id);

        if (!$barangMasuk) {
            setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.x');
            return redirect()->to('/stok/barang-masuk');
        }

        $detailBarangMasuk = $this->detailBarangMasukModel->getDetailBarangMasukId($brang_masuk_id);
        $dokumen = $this->dokumenModel->dokumen_barang_masuk($brang_masuk_id);

        $data = [
            'title' => 'Barang Masuk',
            'sub' => 'Hasil Input',
            'brang_masuk_id' => $id,
            'barangMasuk' => $barangMasuk,
            'detailBarangMasuk' => $detailBarangMasuk,
            'dokumen' => $dokumen,
            'diterima' => $this->userModel->get_all_admin_stok(),
            'suppliers' => $this->supplierModel->get_all_supplier(),
        ];
        return view('stok/barang_masuk/hasil_input', $data);
    }

    public function upload_dokumen()
    {
        try {
            $file = $this->request->getFile('gambar');
            $brang_masuk_id = stringEncryptions('decrypt', $this->request->getPost('barang_masuk_id'));

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newFileName = $file->getRandomName();

                // Simpan file jika insert berhasil
                $file->move('file_upload/barang_masuk/', $newFileName);

                $filePath = FCPATH . 'file_upload/barang_masuk/' . $newFileName;

                $fileName = 'file_upload/barang_masuk/' . $newFileName;

                // Data untuk disimpan
                $data = [
                    'dokumen'           => $fileName,
                    'tipe'              => 'Barang Masuk',
                    'barang_masuk_id'   => $brang_masuk_id,
                    'user_created'      => session()->get('user_id'),
                    'created_at'        => date('Y-m-d H:i:s'),
                ];

                // Mulai transaksi
                $this->db->transBegin();

                // Simpan ke database lewat model
                $insert = $this->dokumenModel->insert($data, true);
                if (!$insert) {
                    $this->db->transRollback();
                    setToast('error', 'Gagal menyimpan ke database.');
                    return redirect()->back();
                }

                if ($this->db->transStatus() === false) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Terjadi kesalahan dalam transaksi.'
                    ]);
                }
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
            } else {
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
            }
        } catch (\Throwable $e) {
            // Rollback jika error
            $this->db->transRollback();
            setToast('error', 'Maaf, terjadi kesalahan. Silakan hubungi admin untuk penanganan lebih lanjut');
        }
        return redirect()->to(base_url('stok/barang-masuk/hasil-input/' . $this->request->getPost('barang_masuk_id')));
    }

    public function delete_dokumen($id = null)
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();
                $file = $this->request->getPost('file');

                // Hapus detail dokumen
                $data = [
                    'user_deleted' => session()->get('user_id'),
                    'deleted_at' => date('Y-m-d H:i:s')
                ];
                $deleted = $this->dokumenModel->update($id, $data);
                if (!$deleted) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal menghapus detail dokumen.'
                    ]);
                }

                // Tentukan path file dokumen yang akan dihapus
                $filePath = FCPATH  .  $file;

                // Hapus file dokumen dari server jika file ada
                if (file_exists($filePath)) {
                    unlink($filePath); // Menghapus file dari server
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
            } catch (\Throwable $e) {
                $this->db->transRollback(); // Rollback jika exception
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Terjadi kesalahan server.'
                ]);
            }
        }
    }


    public function update_barang_masuk()
    {
        if ($this->request->isAJAX()) {
            try {
                $this->db->transBegin();

                $barangMasukId = stringEncryptions('decrypt', $this->request->getPost('barang_masuk_id'));

                // Update data barang masuk
                $data = ['status_approval' => 'Need Approval'];
                $update = $this->barangMasukModel->update($barangMasukId, $data);
                if (!$update) {
                    $this->db->transRollback();
                    return $this->response->setJSON([
                        'status' => false,
                        'message' => 'Gagal memperbarui data barang masuk.'
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

    public function approval_barang_masuk($id = null)
    {
        if (in_array(30, $this->session_permissions)) {
            $brang_masuk_id = stringEncryptions('decrypt', $id);

            $barangMasuk = $this->barangMasukModel->getBarangMasukId($brang_masuk_id);

            if (!$barangMasuk) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.x');
                return redirect()->to('/stok/barang-masuk');
            }

            if ($barangMasuk['status_approval'] == 'Need Approval') {
                $detailBarangMasuk = $this->detailBarangMasukModel->getDetailBarangMasukId($brang_masuk_id);
                $dokumen = $this->dokumenModel->dokumen_barang_masuk($brang_masuk_id);

                $data = [
                    'title' => 'Barang Masuk',
                    'sub' => 'Hasil Input',
                    'brang_masuk_id' => $id,
                    'barangMasuk' => $barangMasuk,
                    'detailBarangMasuk' => $detailBarangMasuk,
                    'dokumen' => $dokumen,
                    'diterima' => $this->userModel->get_all_admin_stok(),
                    'suppliers' => $this->supplierModel->get_all_supplier(),
                ];
                return view('stok/barang_masuk/approval', $data);
            } else {
                setToast('error', 'Status Barang Masuk sudah di Approve');
                return redirect()->to('stok/barang-masuk');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
        }
        return redirect()->to('stok/barang-masuk');
    }

    public function update_stok()
    {
        if (in_array(30, $this->session_permissions)) {
            $barangMasukId = stringEncryptions('decrypt', $this->request->getPost('barang_masuk_id'));
            if (!$barangMasukId) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data Barang Masuk tidak ditemukan.'
                ]);
            }

            $userId = session()->get('user_id');
            $now = date('Y-m-d H:i:s');
            $noDokumen = $this->request->getPost('no_dokument');
            $status = $this->request->getPost('status');

            try {
                $this->db->transBegin();

                if ($status == 'Approve') {
                    $details = $this->detailBarangMasukModel
                        ->select("tbl_t_detail_barang_masuk.*, tbl_t_barcode_value.barcode_value, tbl_t_barcode_value.tgl_expired")
                        ->join("tbl_t_barcode_value", "tbl_t_barcode_value.detail_barang_masuk_id = tbl_t_detail_barang_masuk.detail_barang_masuk_id")
                        ->where('tbl_t_detail_barang_masuk.barang_masuk_id', $barangMasukId)
                        ->where('tbl_t_detail_barang_masuk.deleted_at', null)
                        ->findAll();


                    foreach ($details as $item) {
                        $produkId = $item['produk_id'];
                        $qtyMasuk = (int) $item['qty_input'];
                        $barcodeValue = $item['barcode_value'];
                        $tglExpired = $item['tgl_expired'];

                        $produkRow = $this->db->query("SELECT * FROM tbl_m_produk WHERE produk_id = ? FOR UPDATE", [$produkId])->getRowArray();
                        if ($produkRow) {
                            // Catat riwayat produk
                            $historiProdukData = [
                                'kode_transaksi' => $noDokumen,
                                'tipe' => 'IN',
                                'qty_in' => $qtyMasuk,
                                'barang_masuk_id' => $barangMasukId,
                                'detail_barang_masuk_id' => $item['detail_barang_masuk_id'],
                                'produk_id' => $produkId,
                                'barcode_value' => $barcodeValue,
                                'tgl_expired' => $tglExpired,
                                'keterangan' => 'Barang Masuk',
                                'user_created' => $userId,
                                'created_at' => $now
                            ];
                            $this->historiProdukModel->insert($historiProdukData);
                        } else {
                            throw new \Exception("Produk dengan ID {$produkId} tidak ditemukan.");
                        }
                    }

                    // Perbarui status barang masuk menggunakan Query Builder untuk konsistensi
                    $this->db->table('tbl_t_barang_masuk')
                        ->where('barang_masuk_id', $barangMasukId)
                        ->update(['status_approval' => $status]);
                } else {
                    // Jika status selain Approve, perbarui status dan simpan keterangan menggunakan Query Builder
                    $data = [
                        'status_approval' => $status,
                        'approval_keterangan' => $this->request->getPost('keterangan'),
                    ];
                    $this->db->table('tbl_t_barang_masuk')
                        ->where('barang_masuk_id', $barangMasukId)
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
