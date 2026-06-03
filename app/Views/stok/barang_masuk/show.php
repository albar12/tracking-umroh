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
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="barang_masuk_id" name="barang_masuk_id" value="<?= $id ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">No Dokumen</label>
                                <input type="text" class="form-control" id="no_dokument" name="no_dokument" disabled placeholder="No Dokumen" value="<?= $barangMasuk['no_dokument'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">No Dokumen Supplier</label>
                                <input type="text" class="form-control" id="no_dokument_supplier" name="no_dokument_supplier"
                                    autofocus required placeholder="No Dokumen" disabled value="<?= $barangMasuk['no_dokument_supplier'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Terima</label>
                                <input type="date" class="form-control tgl_terima"
                                    placeholder="yyyy-mm-dd" name="tgl_terima" id="tgl_terima" data-date-end-date="<?= date('Y-m-d') ?>" data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" disabled required value="<?= $barangMasuk['tgl_terima'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Jam Terima</label>
                                <input id="timepicker2" type="time" name="jam_terima" class="form-control" data-provide="timepicker" disabled value="<?= $barangMasuk['jam_terima'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Diterima Oleh</label>
                                <select class="form-control select select2 diterima" disabled id="diterima" name="diterima" required>
                                    <option value="">--Pilih Penerima--</option>
                                    <?php foreach ($diterima as $diterima): ?>
                                        <option value="<?= $diterima['user_id'] ?>" <?php if ($diterima['user_id'] == $barangMasuk['diterima']) {
                                                                                        echo 'selected';
                                                                                    } ?>><?= $diterima['nama_lengkap'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Supplier</label>
                                <select class="form-control select select2 diserahkan" disabled id="diserahkan" name="diserahkan" required>
                                    <option value="">--Pilih Supplier--</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['supplier_id'] ?>" <?php if ($supplier['supplier_id'] == $barangMasuk['diserahkan']) {
                                                                                            echo 'selected';
                                                                                        } ?>><?= $supplier['supplier'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" disabled rows="3" placeholder="Keterangan"><?= $barangMasuk['keterangan'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <h5 class="card-title">List Produk</h5>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-scroll-horizontal">
                                <table id="produkTable" class="table table-bordered dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Kategori</th>
                                            <th>Keterangan</th>
                                            <th width="15%">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach ($detailBarangMasuk as $key => $value) :
                                        ?>
                                            <tr>
                                                <td><input type="hidden" name="produk_list[]" value="<?= esc($value['produk_id']) ?>"><?= esc($value['produk']) ?></td>
                                                <td><?= esc($value['kategori']) ?></td>
                                                <td><?= esc($value['keterangan']) ?></td>
                                                <td><?= esc($value['qty']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <th colspan="3" class="text-end">Total Produk</th>
                                        <th><input type="text" class="form-control" id="total_produk" readonly value="<?= $barangMasuk['total_produk'] ?>"></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php if (count($dokumen)) { ?>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Dokumen
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="popup-gallery d-flex flex-wrap">
                                            <?php foreach ($dokumen as $row): ?>
                                                <?php if (str_contains($row['dokumen'], "https")): ?>
                                                    <a href="<?= $row['dokumen'] ?>" title="<?= $row['tipe'] ?>">
                                                        <div class="img-fluid m-1">
                                                            <img src="<?= $row['dokumen'] ?>" alt="<?= $row['tipe'] ?>" width="120">
                                                        </div>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('file_upload/barang_masuk/' . $row['dokumen']) ?>" title="<?= $row['tipe'] ?>">
                                                        <div class="img-fluid m-1">
                                                            <img src="<?= base_url('file_upload/barang_masuk/' . $row['dokumen']) ?>" alt="<?= $row['tipe'] ?>" width="120">
                                                        </div>
                                                    </a>
                                                <?php endif; ?>

                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Persetujuan </label>
                                <input type="text" class="form-control"
                                    value="<?= $barangMasuk['status_approval'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2" disabled><?= $barangMasuk['approval_keterangan'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/barang-masuk') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editQtyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEditQty">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Qty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label for="qty" class="form-label">Qty</label>
                        <input type="number" class="form-control" name="qty" id="edit-qty" required>
                        <div class="form-text text-danger" id="max-warning" style="display: none;">Qty melebihi batas minimal yang sedang diinput.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/barang_masuk.js?v=') . filemtime(FCPATH . 'assets/js/barang_masuk.js') ?>"></script>
<?= $this->endSection(); ?>