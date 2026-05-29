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
                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form class="needs-validation" action="<?= base_url('setting/satuan/' . $id) ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PUT">
                        <div class="row">
                            <h4 class="card-title"><b>Data Produk</b></h4>
                            <hr>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Satuan <code>*</code></label>
                                    <input type="text" class="form-control" id="satuan" name="satuan" autofocus required placeholder="Satuan" value="<?= $satuan['satuan'] ?>">
                                    <input type="hidden" name="satuanOld" id="satuanOld" value="<?= $satuan['satuan'] ?>">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Qty <code>*</code></label>
                                    <input type="number" class="form-control number-only" id="qty" name="qty" autofocus required placeholder="Qty" value="<?= $satuan['qty'] ?>">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Status <code>*</code></label>
                                    <select class="form-control select select2 status" name="status" required>
                                        <option value="">--Pilih Status--</option>
                                        <option value="Aktif" <?php if ($satuan['status'] == "Aktif") {
                                                                    echo 'selected';
                                                                } ?>>Aktif</option>
                                        <option value="Tidak Aktif" <?php if ($satuan['status'] == "Tidak Aktif") {
                                                                        echo 'selected';
                                                                    } ?>>Tidak Aktif</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <a href="<?= base_url('setting/satuan') ?>" class="btn btn-secondary waves-effect">Batal</a>
                                <button class="btn btn-primary" type="submit" style="float: right"
                                    id="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/satuan.js?v=') . filemtime(FCPATH . 'assets/js/satuan.js') ?>"></script>
<?= $this->endSection(); ?>