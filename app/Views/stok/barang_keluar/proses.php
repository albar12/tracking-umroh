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
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">No Dokumen</label>
                                    <input type="text" class="form-control" name="no_dokument" id="no_dokument"
                                        value="<?= $barang_masuk['no_dokument'] ?>" disabled>
                                    <input type="hidden" id="id" value="<?= $id ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Produk <code>*</code></label>
                                    <select class="form-control select select2 produk_process" id="produk_process" name="produk" required>
                                        <option value="">--Pilih--</option>
                                        <?php foreach ($produks as $prod): ?>
                                            <option
                                                value="<?= $prod['produk_id'] ?>"
                                                data-detail-id="<?= $prod['detail_barang_masuk_id'] ?>"
                                                data-produk-expired="<?= $prod['produk_expired'] ?>"
                                                <?= ($prod['produk_id'] == $produk) ? 'selected' : '' ?>>
                                                <?= $prod['produk'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="divSn">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Data Barcode</label>
                                    <input type="text" class="form-control" autofocus name="barcode_value" id="barcode_value">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="col-md-12">
                                <div class="d-flex">
                                    <h5 class="font-size-18 mb-1 fw-semibold">Produk :</h5>
                                </div>
                                <table width="100%">
                                    <?php foreach ($produks as $produk): ?>
                                        <tr class="border-bottom">
                                            <td width="10%"><i class="bx bx-right-arrow-circle"></i></td>
                                            <td width="70%"><?= esc($produk['produk']) ?></td>
                                            <td width="20%" style="text-align: right;">
                                                <span class="font-size-14 text-success me-2"><?= esc($produk['qty']) ?></i> /
                                                    <?php
                                                    if ($produk['qty'] == $produk['qty_input']) {
                                                        $css = 'success';
                                                    } else {
                                                        $css = 'danger';
                                                    }
                                                    ?>
                                                    <span class="font-size-14 text-<?= $css; ?> me-2"><?= esc($produk['qty_input']) ?? '0' ?></i></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <div id="dynamic-content">
                                <?php if ($barcodes): ?>
                                    <div class="table-scroll-horizontal">
                                        <table id="produkTable" class="table table-bordered dt-responsive">
                                            <thead>
                                                <tr>
                                                    <th width="25%">Data Barcode</th>
                                                    <th>Kategori</th>
                                                    <th>Produk</th>
                                                    <th>Qty</th>
                                                    <th>Tanggal Expired</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($barcodes as $key => $value): ?>
                                                    <tr>
                                                        <td class="<?= empty($value['barcode_value']) ? 'table-danger' : 'table-success' ?>"><?= esc($value['barcode_value']) ?></td>
                                                        <td><?= esc($value['kategori']) ?></td>
                                                        <td><?= esc($value['produk']) ?></td>
                                                        <td><?= esc($value['qty_input']) ?></td>
                                                        <td><?= esc($value['tgl_expired']) ?></td>
                                                        <td>
                                                            <?php if ($value['barcode_value']) { ?>
                                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                                                    <a href="javascript:void(0);" class="text-danger delete-btn-barcode"
                                                                        data-id="<?= esc($value['barcode_value_id']) ?>"
                                                                        data-detail-id="<?= esc($value['detail_barang_masuk_id']) ?>"
                                                                        data-qty="<?= esc($value['qty_input']) ?>">
                                                                        <i class="fa-solid fa-trash font-size-18"></i>
                                                                    </a>
                                                                </span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4" id="divBatal">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/barang-masuk') ?>" class="btn btn-secondary waves-effect">Batal</a>
                            <a href="<?= base_url('stok/barang-masuk/hasil-input/' . $id) ?>" class="btn btn-primary" type="button" style="float: right">Selanjutnya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="staticBackdropLabel">Process Input</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="barcode_value_modal">
                <input type="hidden" id="barang_masuk_id_modal">
                <input type="hidden" id="detail_barang_masuk_id_modal">
                <input type="hidden" id="produk_id_modal">
                <input type="hidden" id="produk_expired_modal">
                <div class="col-md-12">
                    <div class="mb-6">
                        <div class="form-check mb-3">
                            <label for="validationCustom02" class="form-label">Qty Masuk</label>
                            <input type="number" class="form-control" name="qty_input" id="qty_input">
                        </div>
                        <div class="form-check mb-3">
                            <label for="validationCustom02" class="form-label">Tanggal Expired</label>
                            <input type="date" class="form-control" name="tgl_expired" min="<?= date("Y-m-d") ?>" id="tgl_expired">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="simpanModal">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/barang_masuk.js?v=') . filemtime(FCPATH . 'assets/js/barang_masuk.js') ?>"></script>
<?= $this->endSection(); ?>