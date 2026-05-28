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
    }
);
