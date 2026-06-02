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
                        <h4 class="card-title"><b>Data Supplier</b></h4>
                        <hr>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label for="validationCustom02" class="form-label">Supplier</label>
                                <input type="text" class="form-control" id="supplier" name="supplier" autofocus disabled placeholder="Supplier" value="<?= $supplier['supplier'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label for="validationCustom02" class="form-label">Tlp Supplier</label>
                                <input type="text" class="form-control number-only" id="tlp_supplier" name="tlp_supplier" autofocus disabled placeholder="Tlp Supplier" value="<?= $supplier['tlp_supplier'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label for="validationCustom02" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status" autofocus disabled placeholder="Status" value="<?= $supplier['status'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-12">
                                <label for="validationCustom02" class="form-label">Alamat</label>
                                <textarea disabled name="alamat" class="form-control" rows="3" placeholder="Alamat"><?= $supplier['alamat'] ?></textarea>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('setting/supplier') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/supplier.js?v=') . filemtime(FCPATH . 'assets/js/supplier.js') ?>"></script>
<?= $this->endSection(); ?>