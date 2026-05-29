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
                        <h4 class="card-title"><b>Data Produk</b></h4>
                        <hr>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Kategori </label>
                                <select class="form-control select select2 kategori_id" disabled name="kategori_id" required>
                                    <option value="">--Pilih Kategori--</option>
                                    <?php foreach ($kategoris as $kategori): ?>
                                        <option disabled value="<?= $kategori['kategori_id'] ?>" data-name="<?= $kategori['kategori'] ?>" <?php if ($kategori['kategori_id'] == $produk['kategori_id']) {
                                                                                                                                                echo "selected";
                                                                                                                                            } ?>><?= $kategori['kategori'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Produk</label>
                                <input type="text" class="form-control number-only" id="produk" name="produk" autofocus required placeholder="Produk" disabled value="<?= $produk['produk'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Deskripsi Produk</label>
                                <textarea required name="deskripsi_produk" class="form-control" rows="3" placeholder="Deskripsi Produk" disabled><?= $produk['deskripsi_produk'] ?></textarea>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Harga Jual</label>
                                <input type="text" class="form-control number-only" id="harga_jual" name="harga_jual" disabled placeholder="Harga Jual" value="<?= rupiah($produk['harga_jual']) ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Produk Barang</label>
                                <div class="d-flex gap-3 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="produk_barang" id="produk_barang_ya" disabled value="Ya" <?php if ($produk['produk_barang'] == 'Ya') {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?>>
                                        <label class="form-check-label" for="produk_barang_ya">Ya</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="produk_barang" id="produk_barang_tidak" disabled value="Tidak" <?php if ($produk['produk_barang'] == 'Tidak') {
                                                                                                                                                                echo "checked";
                                                                                                                                                            } ?>>
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
                                <label for="validationCustom02" class="form-label">Satuan</label>
                                <select class="form-control select select2 satuan_id" name="satuan_id" disabled>
                                    <option value="">--Pilih Satuan--</option>
                                    <?php foreach ($satuans as $satuan): ?>
                                        <option value="<?= $satuan['satuan_id'] ?>" <?php if ($produk['satuan_id'] == $satuan['satuan_id']) {
                                                                                        echo "selected";
                                                                                    } ?>><?= $satuan['satuan'] ?> - <?= $satuan['qty'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Barcode Value</label>
                                <input type="text" class="form-control number-only" id="barcode_value" name="barcode_value" disabled placeholder="Barcode Value" value="<?= $produk['barcode_value'] ?>">
                                <div class="invalid-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/produk') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/produk.js?v=') . filemtime(FCPATH . 'assets/js/produk.js') ?>"></script>
<?= $this->endSection(); ?>