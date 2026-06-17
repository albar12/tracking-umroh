<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$permissions = session()->get('permissions');
$session_permissions = $permissions ? explode(',', $permissions) : [];
?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0"><?= $title ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">

        <div class="card mb-3" style="background: whitesmoke;">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="filterTglMulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="filterTglMulai" name="filterTglMulai" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="filterTglSelesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" id="filterTglSelesai" name="filterTglSelesai" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <button id="btnCariFilter" class="btn btn-primary"><i class="bi bi-search me-1"></i> Filter</button>
                        <button id="btnResetFilter" class="btn btn-secondary ms-1">Reset</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="row mb-6">
            <div class="col-lg-4 col-md-4 col-12 mb-3 mb-md-0">
                <div class="small-box text-bg-primary" style="position: relative; display: block; border-radius: 0.375rem; overflow: hidden;">
                    <div class="inner p-3">
                        <h3 id="barang_masuk_value" style="font-size: 2.2rem; font-weight: 700; margin: 0 0 10px 0; white-space: nowrap;"></h3>
                        <p style="font-size: 1rem; margin-bottom: 0;">Barang Masuk</p>
                    </div>
                    <div class="icon" style="position: absolute; top: 15px; right: 15px; z-index: 0; font-size: 4.5rem; color: rgba(0, 0, 0, 0.15); line-height: 1;">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </div>
                    <a href="<?= base_url("stok/barang-masuk") ?>" class="small-box-footer text-center d-block py-1 text-decoration-none text-white" style="background-color: rgba(0, 0, 0, 0.1); z-index: 10; position: relative;">
                        More info <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-12 mb-3 mb-md-0">
                <div class="small-box text-bg-secondary" style="position: relative; display: block; border-radius: 0.375rem; overflow: hidden;">
                    <div class="inner p-3">
                        <h3 id="barang_keluar_value" style="font-size: 2.2rem; font-weight: 700; margin: 0 0 10px 0; white-space: nowrap;"></h3>
                        <p style="font-size: 1rem; margin-bottom: 0;">Barang Keluar</p>
                    </div>
                    <div class="icon" style="position: absolute; top: 15px; right: 15px; z-index: 0; font-size: 4.5rem; color: rgba(0, 0, 0, 0.15); line-height: 1;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </div>
                    <a href="<?= base_url("stok/barang-keluar") ?>" class="small-box-footer text-center d-block py-1 text-decoration-none text-dark" style="background-color: rgba(0, 0, 0, 0.05); z-index: 10; position: relative;">
                        More info <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-12">
                <div class="small-box text-bg-success" style="position: relative; display: block; border-radius: 0.375rem; overflow: hidden;">
                    <div class="inner p-3">
                        <h3 id="omzet_value" style="font-size: 2.2rem; font-weight: 700; margin: 0 0 10px 0; white-space: nowrap;"></h3>
                        <p style="font-size: 1rem; margin-bottom: 0;">Total Omzet</p>
                    </div>
                    <div class="icon" style="position: absolute; top: 15px; right: 15px; z-index: 0; font-size: 4.5rem; color: rgba(0, 0, 0, 0.15); line-height: 1;">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <a href="<?= base_url("stok/barang-keluar") ?>" class="small-box-footer text-center d-block py-1 text-decoration-none text-white" style="background-color: rgba(0, 0, 0, 0.1); z-index: 10; position: relative;">
                        More info <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title m-0"><i class="fa-solid fa-dollar-sign me-2"></i>
                            <span id="omzet-chart-label">Omzet</span>
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i class="data-lte-icon bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="barang-keluar-chart" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-success shadow-sm">
            <div class="card-header">
                <h5 class="card-title m-0"><i class="fa-solid fa-chart-area"></i> <span id="laris-chart-label"> Kategori Barang Terlaris</span></h5>
            </div>
            <div class="card-body">
                <div id="kategori-chart" style="min-height: 250px;"></div>
            </div>
        </div>

        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title m-0 d-flex align-items-center">
                            <i class="fa-solid fa-boxes-stacked me-2"></i>
                            <span id="laris-chart-labelx">Stok Produk</span>
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Produk Masuk</th>
                                        <th>Produk Keluar</th>
                                        <th>Stok Tersedia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dashboard.js?v=') . filemtime(FCPATH . 'assets/js/dashboard.js') ?>"></script>
<?= $this->endSection() ?>