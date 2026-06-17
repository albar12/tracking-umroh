<?php

namespace App\Controllers\Home;

use App\Models\HistoriProdukModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Database;

class Home extends ResourceController
{
    protected $db;
    protected $historiProdukModel;
    protected $title;
    protected $session_permissions;

    public function __construct()
    {
        // Inisialisasi database
        $this->db = Database::connect();

        $this->historiProdukModel = new HistoriProdukModel();

        $this->title = 'Dashboard';
        $permissions = session()->get('permissions');
        $this->session_permissions = $permissions ? explode(',', $permissions) : [];
    }

    public function index(): string
    {
        $data['title'] = $this->title;

        return view('dashboard/dashboard', $data);
    }

    public function getDataDashboard()
    {
        $request = service('request');

        $filters = $request->getPost('filters');

        $barang_masuk_value = $this->historiProdukModel->getTotalBarangMasuk($filters);
        $barang_keluar_value = $this->historiProdukModel->getTotalBarangKeluar($filters);
        $omzet_value = $this->historiProdukModel->getOmzet($filters);
        $getOmzetChart = $this->historiProdukModel->getOmzetChart($filters);
        $getBarangChart = $this->historiProdukModel->getBarangChart($filters);

        return $this->response->setJSON([
            "total_masuk" => $barang_masuk_value['total_masuk'],
            "total_keluar" => $barang_keluar_value['total_keluar'],
            "omzet_value" => $omzet_value['total_omzet'],
            "omzet_data" => array_values($getOmzetChart),
            "barang_data" => [
                'produk' => array_column($getBarangChart, 'produk'),
                'total_keluar' => array_column($getBarangChart, 'total_keluar'),
            ],
        ]);
    }

    public function getStokData()
    {
        $request = service('request');

        $draw = (int) $request->getPost('draw'); // Untuk tracking request
        $start = (int) $request->getPost('start'); // Mulai dari record ke-
        $length = (int) $request->getPost('length'); // Jumlah record per halaman
        $searchValue = $request->getPost('search')['value']; // Nilai pencarian


        $totalRecords = $this->historiProdukModel->countAllStok();
        $totalFiltered = $this->historiProdukModel->countFilteredStok($searchValue);
        $stoks = $this->historiProdukModel->getStokData($length, $start, $searchValue);


        return $this->response->setJSON([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $stoks,
        ]);
    }
}
