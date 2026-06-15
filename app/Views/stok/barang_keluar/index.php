<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$permissions = session()->get('permissions');
$session_permissions = $permissions ? explode(',', $permissions) : [];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <?php if (in_array(32, $session_permissions)) : ?>
                        <a href="<?= base_url('stok/barang-keluar/new') ?>" type="button"
                            class="float-end btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                            <i class="mdi mdi-plus me-1"></i> Tambah <?= $title ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Dokumen</th>
                                <th>Tgl Keluar</th>
                                <th>Jam Keluar</th>
                                <th>Total Harga</th>
                                <th>Admin Input</th>
                                <th>Status</th>
                                <th>Aksi</th>
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
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/barang_keluar.js?v=') . filemtime(FCPATH . 'assets/js/barang_keluar.js') ?>"></script>
<?= $this->endSection() ?>