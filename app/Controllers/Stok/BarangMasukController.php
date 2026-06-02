<?php

namespace App\Controllers\Stok;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\UserModel;
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
        if (in_array(10, $this->session_permissions)) {
            $produk = $this->produkModel->getProdukiId(stringEncryptions('decrypt', $id));

            if (!$produk) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/produk');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Lihat Data';
            $data['produk'] = $produk;
            $data['suppliers'] = $this->supplierModel->get_all_supplier();
            $data['diterima'] = $this->userModel->get_all_admin_stok();

            return view('stok/produk/show', $data);
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
                $detailList = [];
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
                    $detailList[] = $detail;
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
        if (in_array(12, $this->session_permissions)) {
            $produk = $this->produkModel->getProdukiId(stringEncryptions('decrypt', $id));

            if (!$produk) {
                setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');
                return redirect()->to('/stok/produk');
            }

            $data['title'] = $this->title;
            $data['sub'] = 'Rubah Data';
            $data['id'] = $id;
            $data['kategoris'] = $this->kategoriModel->get_all_ketgori();
            $data['satuans'] = $this->satuanModel->get_all_satuan();
            $data['produk'] = $produk;

            return view('stok/produk/edit', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/produk');
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
        if (in_array(12, $this->session_permissions)) {

            if (!$this->validate($this->produkModel->validationRules, $this->produkModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            if ($this->request->getPost('produk') != $this->request->getPost('produkOld')) {
                if ($this->produkModel->cekProduk($this->request->getPost('kategori_id'), $this->request->getPost('produk')) > 0) {
                    return redirect()->back()
                        ->withInput()
                        ->with('errors', ['Produk yang Anda masukkan sudah terdaftar. Silakan gunakan produk lain']);
                }
            }

            $this->db->transBegin();

            $decodeId = stringEncryptions('decrypt', $id);

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $data = [
                'kategori_id'       => $this->request->getPost('kategori_id'),
                'produk'            => $this->request->getPost('produk'),
                'deskripsi_produk'  => $this->request->getPost('deskripsi_produk'),
                'harga_jual'        => $this->request->getPost('harga_jual'),
                'produk_barang'     => $this->request->getPost('produk_barang'),
                'satuan_id'         => $this->request->getPost('satuan_id'),
                'barcode_value'     => $this->request->getPost('barcode_value'),
                'status'            => $this->request->getPost('status'),
                'user_updated'      => $userID,
                'updated_at'        => $now
            ];

            $updated = $this->produkModel->update($decodeId, $data);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$updated) {
                $this->db->transRollback();
                setToast('error', 'Gagal memperbarui data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil diperbarui.');
                return redirect()->to('/stok/produk');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/produk');
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
        if (in_array(13, $this->session_permissions)) {
            try {
                $this->db->transBegin();

                $decodeId = stringEncryptions('decrypt', $id);

                $userId = session()->get('user_id');
                $now = date('Y-m-d H:i:s');

                $data = [
                    'user_deleted' => $userId,
                    'deleted_at' => $now
                ];

                $deleted = $this->produkModel->update($decodeId, $data);

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
