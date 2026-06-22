<?php

namespace App\Controllers\Stok;

use App\Models\LaporanStokModel;
use App\Models\HistoriProdukModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class LaporanStokController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $laporanStokModel;
    protected $historiProdukModel;
    protected $barangMasukModel;
    protected $barangKeluarModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->laporanStokModel = new LaporanStokModel();
        $this->historiProdukModel = new HistoriProdukModel();
        $this->barangMasukModel = new BarangMasukModel();
        $this->barangKeluarModel = new BarangKeluarModel();

        $this->title = 'Laporan Stok';
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
        if (in_array(43, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('stok/laporan_stok/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok');
        }
    }

    public function getLaporanStoks()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $filters = $request->getPost('filters');

        $totalRecords = $this->laporanStokModel->countAllLaporan();
        $totalFiltered = $this->laporanStokModel->countFilteredLaporan($searchValue, $filters);
        $produks = $this->laporanStokModel->getLaporanData($length, $start, $searchValue, $filters);


        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $produks,
        ]);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        if (in_array(44, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            return view('stok/laporan_stok/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/laporan-stok');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(44, $this->session_permissions)) {
            if (!$this->validate($this->laporanStokModel->validationRules, $this->laporanStokModel->validationMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');
            $tgl_mulai = $this->request->getPost('tgl_mulai');
            $tgl_selesai = $this->request->getPost('tgl_selesai');

            $data_laporan = [
                "tgl_mulai" => $tgl_mulai,
                "tgl_selesai" => $tgl_selesai,
            ];

            $filename = $this->generateLaporanStok($data_laporan);


            $data = [
                'tgl_mulai'         => $tgl_mulai,
                'tgl_selesai'       => $tgl_selesai,
                'filename'          => $filename,
                'user_created'      => $userID,
                'created_at'        => $now
            ];

            $inserted = $this->laporanStokModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/stok/laporan-stok');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/laporan-stok');
        }
    }

    public function generateLaporanStok($data = [])
    {


        // 1. Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Coloring by range
        $styleBlue = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00b0f0']
            ]
        ];

        $styleYellow = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ffff00']
            ]
        ];

        $styleOrange = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ffc000']
            ]
        ];

        $styleGreen = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92d050']
            ]
        ];

        // ==========================================
        // SHEET 1: STOK SAAT INI
        // ==========================================
        // Secara default, PhpSpreadsheet sudah membuat 1 sheet pertama (index 0)
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Stok Saat Ini'); // Mengubah nama sheet 1

        // Isi Header Sheet 1
        $headers = [
            'PRODUK',
            'KATEGORI',
            'STOK AWAL',
            'BARANG MASUK',
            'BARANG KELUAR',
            'STOK AKHIR',
        ];

        $sheet1->fromArray($headers, NULL, 'A1');



        $sheet1->getStyle('A1:B1')->applyFromArray($styleBlue);
        $sheet1->getStyle('C1:E1')->applyFromArray($styleYellow);
        $sheet1->getStyle('F1:F1')->applyFromArray($styleGreen);

        // Bold + Border all header
        $headerStyle = [
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet1->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Contoh Data Sheet 1 (Idealnya didapat dari query database $this->produkModel->findAll())
        $data_laporan_stok = $this->historiProdukModel->getLaporanStok($data);

        $row = 2;
        foreach ($data_laporan_stok as $item) {

            $sheet1->fromArray([
                $item['produk'],
                $item['kategori'],
                $item['stok_awal'],
                $item['barang_masuk'],
                $item['barang_keluar'],
                $item['stok_akhir'],
            ], NULL, 'A' . $row);

            $sheet1->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ]);
            $row++;
        }

        // Auto-size semua kolom A - F
        $lastColumn = 'F';
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 1; $col <= $lastColumnIndex; $col++) {
            // $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
            $sheet1->getColumnDimension(Coordinate::stringFromColumnIndex($col));
        }

        // ==========================================
        // SHEET 2: BARANG MASUK
        // ==========================================
        // Membuat sheet baru kedua (index 1)
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet();
        $sheet2->setTitle('Barang Masuk'); // Mengubah nama sheet 2

        // Isi Header Sheet 1
        $headers = [
            'NO DOKUMENT',
            'TANGGAL MASUK',
            'PRODUK',
            'KATEGORI',
            'QTY MASUK',
            'SUPPLIER',
        ];

        $sheet2->fromArray($headers, NULL, 'A1');



        $sheet2->getStyle('A1:B1')->applyFromArray($styleBlue);
        $sheet2->getStyle('C1:E1')->applyFromArray($styleYellow);
        $sheet2->getStyle('F1:F1')->applyFromArray($styleOrange);

        // Bold + Border all header
        $headerStyle = [
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet2->getStyle('A1:F1')->applyFromArray($headerStyle);

        $data_laporan_barang_masuk = $this->barangMasukModel->getLaporanBarangMasuk($data);

        $row = 2;
        foreach ($data_laporan_barang_masuk as $item) {

            $sheet2->fromArray([
                $item['no_dokument'],
                $item['tgl_terima'],
                $item['produk'],
                $item['kategori'],
                $item['qty_input'],
                $item['supplier'],
            ], NULL, 'A' . $row);

            $sheet2->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ]);
            $row++;
        }

        // Auto-size semua kolom A - F
        $lastColumn = 'F';
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 1; $col <= $lastColumnIndex; $col++) {
            // $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
            $sheet2->getColumnDimension(Coordinate::stringFromColumnIndex($col));
        }

        // ==========================================
        // SHEET 3: BARANG KELUAR
        // ==========================================
        // Membuat sheet baru kedua (index 1)
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet();
        $sheet3->setTitle('Barang Keluar'); // Mengubah nama sheet 2

        // Isi Header Sheet 1
        $headers = [
            'NO DOKUMENT',
            'TANGGAL KELUAR',
            'PRODUK',
            'KATEGORI',
            'QTY KELUAR',
            'HARGA JUAL',
            'TOTAL HARGA',
        ];

        $sheet3->fromArray($headers, NULL, 'A1');



        $sheet3->getStyle('A1:B1')->applyFromArray($styleBlue);
        $sheet3->getStyle('C1:D1')->applyFromArray($styleYellow);
        $sheet3->getStyle('E1:G1')->applyFromArray($styleGreen);

        // Bold + Border all header
        $headerStyle = [
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet3->getStyle('A1:G1')->applyFromArray($headerStyle);

        $data_laporan_barang_keluar = $this->barangKeluarModel->getLaporanBarangKeluar($data);

        $row = 2;
        foreach ($data_laporan_barang_keluar as $item) {

            $sheet3->fromArray([
                $item['no_dokument'],
                $item['tgl_keluar'],
                $item['produk'],
                $item['kategori'],
                $item['qty'],
                rupiah($item['harga_jual']),
                rupiah($item['total_harga']),
            ], NULL, 'A' . $row);

            $sheet3->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ]);
            $row++;
        }

        // Auto-size semua kolom A - F
        $lastColumn = 'F';
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 1; $col <= $lastColumnIndex; $col++) {
            // $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
            $sheet3->getColumnDimension(Coordinate::stringFromColumnIndex($col));
        }

        // ==========================================
        // PROSES PENYIMPANAN KE SERVER
        // ==========================================
        // Set kembalian ke sheet pertama saat user membuka file pertama kali
        $spreadsheet->setActiveSheetIndex(0);

        // 1. Tentukan nama file yang unik (ditambah timestamp/random string agar tidak bentrok)
        $filename = 'Laporan_Stok_dan_Penjualan_' . date('Ymd_His') . '.xlsx';

        // 2. Tentukan path/lokasi penyimpanan di folder writable CI4
        $path = 'file_upload/laporan_stok/';
        $savePath = FCPATH . $path . $filename;

        // 3. Simpan file ke path tersebut menggunakan Writer
        $writer = new Xlsx($spreadsheet);
        $writer->save($savePath);

        // 4. Return nama file (atau bisa juga return $savePath jika butuh full path-nya)
        return $path . $filename;
    }
}
