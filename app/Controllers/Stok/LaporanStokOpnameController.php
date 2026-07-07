<?php

namespace App\Controllers\Stok;

use App\Models\LaporanStokOpnameModel;
use App\Models\HistoriProdukModel;
use App\Models\BarangMasukModel;
use App\Models\BarangKeluarModel;
use App\Models\StokOpnameModel;
use App\Models\DetailStokOpnameModel;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class LaporanStokOpnameController extends ResourceController
{
    protected $db;
    protected $session_permissions;
    protected $title;
    protected $laporanStokOpnameModel;
    protected $historiProdukModel;
    protected $barangMasukModel;
    protected $barangKeluarModel;
    protected $stokOpnameModel;
    protected $detailStokOpnameModel;


    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        // Inisialisasi model di constructor
        $this->laporanStokOpnameModel = new LaporanStokOpnameModel();
        $this->historiProdukModel = new HistoriProdukModel();
        $this->barangMasukModel = new BarangMasukModel();
        $this->barangKeluarModel = new BarangKeluarModel();
        $this->stokOpnameModel = new StokOpnameModel();
        $this->detailStokOpnameModel = new DetailStokOpnameModel();

        $this->title = 'Laporan Stok Opname';
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
        if (in_array(50, $this->session_permissions)) {
            $data['title'] = $this->title;
            return view('stok/laporan_stok_opname/index', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok');
        }
    }

    public function getLaporanStokOpnames()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian

        $filters = $request->getPost('filters');

        $totalRecords = $this->laporanStokOpnameModel->countAllLaporanSo();
        $totalFiltered = $this->laporanStokOpnameModel->countFilteredLaporanSo($searchValue, $filters);
        $produks = $this->laporanStokOpnameModel->getLaporanSoData($length, $start, $searchValue, $filters);


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
        if (in_array(51, $this->session_permissions)) {
            $data['title'] = $this->title;
            $data['sub'] = 'Tambah Data';
            $data['batch_so'] = $this->stokOpnameModel->get_batch_so();

            return view('stok/laporan_stok_opname/new', $data);
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/laporan-stok-opname');
        }
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        if (in_array(51, $this->session_permissions)) {
            if (!$this->request->getPost('batch_so')) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['Batch wajib diisi']);
            }

            $this->db->transBegin();

            $userID = session()->get('user_id');
            $now = date('Y-m-d H:i:s');

            $so_id = $this->request->getPost('batch_so');
            $stokOpname = $this->stokOpnameModel->getStokOpnameId($so_id);

            $batch = $stokOpname['batch'];
            $tgl_mulai = $stokOpname['tgl_mulai'];
            $tgl_selesai = $stokOpname['tgl_selesai'];

            $data_laporan = [
                "tgl_mulai" => $tgl_mulai,
                "tgl_selesai" => $tgl_selesai,
            ];

            $filename = $this->generateLaporanStokOpname($so_id, $batch);

            $data = [
                'tgl_mulai'         => $tgl_mulai,
                'tgl_selesai'       => $tgl_selesai,
                'filename'          => $filename,
                'user_created'      => $userID,
                'created_at'        => $now
            ];

            $inserted = $this->laporanStokOpnameModel->insert($data, true);

            // Cek transaksi dan hasil insert
            if ($this->db->transStatus() === false || !$inserted) {
                $this->db->transRollback();
                setToast('error', 'Gagal menambahkan data. Silakan coba lagi.');
                return redirect()->back();
            } else {
                $this->db->transCommit();
                setToast('success', 'Data telah berhasil ditambahkan.');
                return redirect()->to('/stok/laporan-stok-opname');
            }
        } else {
            setToast('error', 'Anda tidak memiliki hak akses untuk melakukan tindakan ini');
            return redirect()->to('/stok/laporan-stok-opname');
        }
    }

    public function generateLaporanStokOpname($so_id = null, $batch = null)
    {
        // 1. Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Membersihkan nama batch dari karakter terlarang sebelum diset ke Title Sheet
        $clean_batch = str_replace([':', '*', '/', '\\', '?', '[', ']'], '-', $batch);
        $sheet_title = substr('SO ' . $clean_batch, 0, 31);

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

        $styleGreen = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '92d050']
            ]
        ];

        // ==========================================
        // SHEET 1: STOK SAAT INI
        // ==========================================
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheet_title);

        // ------------------------------------------
        // MENAMBAHKAN BLOK JUDUL LAPORAN (BAGUS & RAPI)
        // ------------------------------------------
        // Baris 1: Judul Utama
        $sheet->setCellValue('A1', 'LAPORAN HASIL STOK OPNAME');
        $sheet->mergeCells('A1:G1'); // Menggabungkan cell dari kolom A sampai G
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Rata tengah
            ]
        ]);

        // Baris 2: Sub-Judul (Informasi Sesi Batch)
        $sheet->setCellValue('A2', 'Batch Opname: ' . $batch);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Baris 3: Tanggal Generate Laporan
        $sheet->setCellValue('A3', 'Tanggal Cetak: ' . date('d-m-Y H:i:s'));
        $sheet->mergeCells('A3:G3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '555555']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Baris 4 sengaja dikosongkan untuk memberi space/jarak visual

        // ------------------------------------------
        // TABEL UTAMA (Mulai dari Baris 5)
        // ------------------------------------------
        $startRowHeader = 5;

        // Isi Header Sheet 1
        $headers = [
            'PRODUK',
            'KATEGORI',
            'STOK SISTEM',
            'STOK FISIK',
            'STOK DITEMUKAN',
            'STOK HILANG',
            'STATUS',
        ];

        $sheet->fromArray($headers, NULL, 'A' . $startRowHeader);

        // Apply warna background pada header tabel (Baris 5)
        $sheet->getStyle('A' . $startRowHeader . ':B' . $startRowHeader)->applyFromArray($styleBlue);
        $sheet->getStyle('C' . $startRowHeader . ':F' . $startRowHeader)->applyFromArray($styleYellow);
        $sheet->getStyle('G' . $startRowHeader . ':G' . $startRowHeader)->applyFromArray($styleGreen);

        // Bold + Border + Rata Tengah untuk semua header tabel
        $headerStyle = [
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        $sheet->getStyle('A' . $startRowHeader . ':G' . $startRowHeader)->applyFromArray($headerStyle);
        $sheet->getRowDimension($startRowHeader)->setRowHeight(25); // Membuat baris header tabel sedikit lebih tinggi/lega

        // Ambil Data dari database
        $data_laporan_stok = $this->detailStokOpnameModel->getDetailStokOpnameId($so_id);

        // Data dimulai dari baris ke-6
        $row = 6;
        foreach ($data_laporan_stok as $item) {

            $stok_sistem = $this->historiProdukModel->getStokSistem($item['produk_id']);

            $sheet->fromArray([
                $item['produk'],
                $item['kategori'],
                $stok_sistem['stok_tersedia'],
                $item['qty'],
                $item['qty_ditemukan'],
                $item['qty_hilang'],
                $item['status_so'],
            ], NULL, 'A' . $row);

            // Border tipis untuk data baris berjalan
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            $sheet->getRowDimension($row)->setRowHeight(20); // Membuat tinggi baris data seragam dan rapi
            $row++;
        }

        // Auto-size semua kolom A - G agar teks tidak terpotong
        $lastColumn = 'G';
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        for ($col = 1; $col <= $lastColumnIndex; $col++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        // ==========================================
        // PROSES PENYIMPANAN KE SERVER
        // ==========================================
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Laporan_Stok_Opname_' . $clean_batch . '_' . date('Ymd_His') . '.xlsx';
        $path = 'file_upload/laporan_so/';
        $savePath = FCPATH . $path . $filename;

        $writer = new Xlsx($spreadsheet);
        $writer->save($savePath);

        return $path . $filename;
    }
}
