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
                    <form class="needs-validation" action="<?= base_url('stok/laporan-stok') ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field(); ?>
                        <div class="row d-flex align-items-end">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Tanggal Mulai <code>*</code></label>
                                    <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" autofocus required placeholder="Tanggal Mulai">
                                    <div class="invalid-feedback" id="nik-feedback">Wajib di isi.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Tanggal Selesai <code>*</code></label>
                                    <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai" autofocus required placeholder="Tanggal Selesai">
                                    <div class="invalid-feedback" id="nik-feedback">Wajib di isi.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit" id="submit">Request Laporan</button>
                                    <a href="<?= base_url('stok/laporan-stok') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/laporan_stok.js?v=') . filemtime(FCPATH . 'assets/js/laporan_stok.js') ?>"></script>
<?= $this->endSection(); ?>