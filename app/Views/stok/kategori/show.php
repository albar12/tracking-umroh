<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?= $title ?></a></li>
                        <li class="breadcrumb-item active"><?= $sub ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <h4 class="card-title"><b>Data Kategori</b></h4>
                        <hr>
                        <div class="col-md-6">
                            <div class="mb-6">
                                <label for="validationCustom02" class="form-label">Kategori</label>
                                <input type="text" class="form-control number-only" disabled id="kategori" name="kategori" autofocus placeholder="Kategori" value="<?= $kategori['kategori'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-6">
                                <label for="validationCustom02" class="form-label">Status</label>
                                <input type="text" class="form-control number-only" disabled id="status" name="status" autofocus placeholder="Status" value="<?= $kategori['status'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/kategori') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/kategori.js?v=') . filemtime(FCPATH . 'assets/js/kategori.js') ?>"></script>
<?= $this->endSection(); ?>