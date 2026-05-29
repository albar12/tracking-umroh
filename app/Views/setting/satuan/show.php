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
                        <h4 class="card-title"><b>Data Satuan</b></h4>
                        <hr>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Satuan</label>
                                <input type="text" class="form-control" id="satuan" name="satuan" autofocus disabled placeholder="Satuan" value="<?= $satuan['satuan'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Qty</label>
                                <input type="number" class="form-control number-only" id="qty" name="qty" autofocus disabled placeholder="Qty" value="<?= $satuan['qty'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status" autofocus disabled placeholder="Status" value="<?= $satuan['status'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('setting/satuan') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/satuan.js?v=') . filemtime(FCPATH . 'assets/js/satuan.js') ?>"></script>
<?= $this->endSection(); ?>