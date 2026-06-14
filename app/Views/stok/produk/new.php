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
                    <form class="needs-validation" action="<?= base_url('stok/produk') ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field(); ?>
                        <div class="row">
                            <h4 class="card-title"><b>Data Produk</b></h4>
                            <hr>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Kategori <code>*</code></label>
                                    <select class="form-control select select2 kategori_id" name="kategori_id" required>
                                        <option value="">--Pilih Kategori--</option>
                                        <?php foreach ($kategoris as $kategori): ?>
                                            <option value="<?= $kategori['kategori_id'] ?>" data-name="<?= $kategori['kategori'] ?>"><?= $kategori['kategori'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Produk <code>*</code></label>
                                    <input type="text" class="form-control number-only" id="produk" name="produk" autofocus required placeholder="Produk">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Deskripsi Produk <code>*</code></label>
                                    <textarea required name="deskripsi_produk" class="form-control" rows="3" placeholder="Deskripsi Produk"></textarea>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Harga Jual <code>*</code></label>
                                    <input type="number" class="form-control number-only" id="harga_jual" name="harga_jual" autofocus required placeholder="Harga Jual">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Produk Barang <code>*</code></label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produk_barang" id="produk_barang_ya" value="Ya" checked>
                                            <label class="form-check-label" for="produk_barang_ya">Ya</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produk_barang" id="produk_barang_tidak" value="Tidak">
                                            <label class="form-check-label" for="produk_barang_tidak"> Tidak</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Produk Memiliki Tanggal Kadaluarsa <code>*</code></label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produk_expired" id="produk_expired_ya" value="Ya" checked>
                                            <label class="form-check-label" for="produk_expired_ya">Ya</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="produk_expired" id="produk_expired_tidak" value="Tidak">
                                            <label class="form-check-label" for="produk_expired_tidak"> Tidak</label>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Satuan <code>*</code></label>
                                    <select class="form-control select select2 satuan_id" name="satuan_id" required>
                                        <option value="">--Pilih Satuan--</option>
                                        <?php foreach ($satuans as $satuan): ?>
                                            <option value="<?= $satuan['satuan_id'] ?>"><?= $satuan['satuan'] ?> - <?= $satuan['qty'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Barcode Value <code>*</code></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control number-only" id="barcode_value" name="barcode_value" required placeholder="Barcode Value">
                                        <button type="button" class="btn btn-primary" id="generate_barcode" title="Generate"><i class="fa-solid fa-rotate"></i></button>
                                    </div>
                                    <div class="invalid-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <a href="<?= base_url('stok/produk') ?>" class="btn btn-secondary waves-effect">Batal</a>
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
<script src="<?= base_url('assets/js/produk.js?v=') . filemtime(FCPATH . 'assets/js/produk.js') ?>"></script>
<?= $this->endSection(); ?>