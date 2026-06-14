<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::index');
$routes->get('/login', 'AuthController::index');
$routes->post('/auth/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->group(
    'general',
    ['namespace' => 'App\Controllers\General'],
    function ($routes) {
        $routes->post('get-role-akses', 'GeneralController::get_role_akses');
        $routes->post('get-kategori', 'GeneralController::get_kategori');
        $routes->post('get-supplier', 'GeneralController::get_supplier');
        $routes->post('get-produk-by-kategori', 'GeneralController::get_produk_by_kategori');
    }
);

$routes->group(
    'home',
    ['namespace' => 'App\Controllers\Home'],
    function ($routes) {
        $routes->get('/', 'Home::index');
        $routes->resource('users', ['controller' => 'UserController']);
        $routes->post('users/getUsers', 'UserController::getUsers');
    }
);

$routes->group(
    'stok',
    ['namespace' => 'App\Controllers\Stok'],
    function ($routes) {
        $routes->get('/', 'KategoriController::index');
        $routes->resource('kategori', ['controller' => 'KategoriController']);
        $routes->post('kategori/getKategoris', 'KategoriController::getKategoris');
        $routes->resource('produk', ['controller' => 'ProdukController']);
        $routes->post('produk/getProduks', 'ProdukController::getProduks');
        $routes->post('produk/getStokProduk', 'ProdukController::getStokProduk');
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
    }
);
