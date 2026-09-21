<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/auth/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/lupa-password', 'AuthController::lupa_password');
$routes->post('/send-forgot-password', 'AuthController::sendForgotPassword');
$routes->get('/verify-otp', 'AuthController::verify_otp');
$routes->post('/resend-otp', 'AuthController::resend_otp');
$routes->post('/check-otp', 'AuthController::check_otp');
$routes->get('/update-password', 'AuthController::update_password');
$routes->post('/update-password-action', 'AuthController::update_password_action');
$routes->get('/jamaah', 'JamaahController::index');
$routes->resource('jamaah', ['controller' => 'JamaahController']);
$routes->post('jamaah/getKategoris', 'JamaahController::getKategoris');



$routes->group(
    'general',
    ['namespace' => 'App\Controllers\General'],
    function ($routes) {
        $routes->post('get-role-akses', 'GeneralController::get_role_akses');
        $routes->post('get-kategori', 'GeneralController::get_kategori');
        $routes->post('get-supplier', 'GeneralController::get_supplier');
        $routes->post('get-produk-by-kategori', 'GeneralController::get_produk_by_kategori');
        $routes->post('get-produk-by-barcode', 'GeneralController::get_produk_by_barcode');
    }
);

$routes->group(
    'home',
    ['namespace' => 'App\Controllers\Home'],
    function ($routes) {
        $routes->get('/', 'Home::index');
        $routes->post('getDataDashboard', 'Home::getDataDashboard');
        $routes->post('getStokData', 'Home::getStokData');


        $routes->resource('users', ['controller' => 'UserController']);
        $routes->post('users/getUsers', 'UserController::getUsers');
    }
);

$routes->group(
    'stok',
    ['namespace' => 'App\Controllers\Stok'],
    function ($routes) {


        $routes->get('produk/barcode-print/(:any)/(:any)', 'ProdukController::barcode_print/$1/$2');
        $routes->resource('produk', ['controller' => 'ProdukController']);
        $routes->post('produk/getProduks', 'ProdukController::getProduks');
        $routes->post('produk/getStokProduk', 'ProdukController::getStokProduk');

        // barang masuk
        $routes->get('barang-masuk/input-barang-masuk/(:any)', 'BarangMasukController::input_barang_masuk/$1');
        $routes->get('barang-masuk/hasil-input/(:any)', 'BarangMasukController::hasil_input/$1');
        $routes->get('barang-masuk/approval-barang-masuk/(:any)', 'BarangMasukController::approval_barang_masuk/$1');
        $routes->resource('barang-masuk', ['controller' => 'BarangMasukController']);
        $routes->post('barang-masuk/getBarangMasuks', 'BarangMasukController::getBarangMasuks');
        $routes->post('barang-masuk/tambah-produk', 'BarangMasukController::tambah_produk');
        $routes->post('barang-masuk/delete-detail', 'BarangMasukController::delete_detail');
        $routes->post('barang-masuk/updateQty', 'BarangMasukController::updateQty');
        $routes->post('barang-masuk/input-barcode', 'BarangMasukController::input_barcode');
        $routes->post('barang-masuk/batal-barcode', 'BarangMasukController::batal_barcode');
        $routes->post('barang-masuk/upload-dokumen', 'BarangMasukController::upload_dokumen');
        $routes->post('barang-masuk/delete-dokumen/(:any)', 'BarangMasukController::delete_dokumen/$1');
        $routes->post('barang-masuk/update-barang-masuk', 'BarangMasukController::update_barang_masuk');
        $routes->post('barang-masuk/update-stok', 'BarangMasukController::update_stok');

        // barang keluar
        $routes->get('barang-keluar/cetak-struk/(:any)', 'BarangKeluarController::cetak_struk/$1');
        $routes->resource('barang-keluar', ['controller' => 'BarangKeluarController']);
        $routes->post('barang-keluar/getBarangKeluars', 'BarangKeluarController::getBarangKeluars');
        $routes->post('barang-keluar/tambah-produk', 'BarangKeluarController::tambah_produk');
        $routes->post('barang-keluar/delete-detail', 'BarangKeluarController::delete_detail');
        $routes->post('barang-keluar/updateQty', 'BarangKeluarController::updateQty');

        // laporan stok
        $routes->resource('laporan-stok', ['controller' => 'LaporanStokController']);
        $routes->post('laporan-stok/getLaporanStoks', 'LaporanStokController::getLaporanStoks');

        // stok opname
        $routes->get('stok-opname/input-stok-opname/(:any)', 'StokOpnameController::input_stok_opname/$1');
        $routes->get('stok-opname/hasil-input/(:any)', 'StokOpnameController::hasil_input/$1');
        $routes->get('stok-opname/approval-stok-opname/(:any)', 'StokOpnameController::approval_stok_opname/$1');
        $routes->resource('stok-opname', ['controller' => 'StokOpnameController']);
        $routes->post('stok-opname/getStokOpnames', 'StokOpnameController::getStokOpnames');
        $routes->post('stok-opname/tambah-produk', 'StokOpnameController::tambah_produk');
        $routes->post('stok-opname/delete-detail', 'StokOpnameController::delete_detail');
        $routes->post('stok-opname/input-so', 'StokOpnameController::input_so');
        $routes->post('stok-opname/batal-so', 'StokOpnameController::batal_so');
        $routes->post('stok-opname/syncron-stok-opname', 'StokOpnameController::syncron_stok_opname');
        $routes->post('stok-opname/update-so', 'StokOpnameController::update_so');

        // laporan stok opname
        $routes->resource('laporan-stok-opname', ['controller' => 'LaporanStokOpnameController']);
        $routes->post('laporan-stok-opname/getLaporanStokOpnames', 'LaporanStokOpnameController::getLaporanStokOpnames');
    }
);

$routes->group(
    'setting',
    ['namespace' => 'App\Controllers\Setting'],
    function ($routes) {
        $routes->resource('role', ['controller' => 'RoleController']);
        $routes->post('role/getRoles', 'RoleController::getRoles');
        $routes->resource('satuan', ['controller' => 'SatuanController']);
        $routes->post('satuan/getSatuans', 'SatuanController::getSatuans');
        $routes->resource('supplier', ['controller' => 'SupplierController']);
        $routes->post('supplier/getSuppliers', 'SupplierController::getSuppliers');
        $routes->resource('metode-pembayaran', ['controller' => 'MetodePembayaranController']);
        $routes->post('metode-pembayaran/getMetodes', 'MetodePembayaranController::getMetodes');
        $routes->resource('toko', ['controller' => 'TokoController']);
        $routes->post('toko/getTokos', 'TokoController::getTokos');
    }
);
