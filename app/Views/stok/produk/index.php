<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <a href="<?= base_url('stok/produk/new') ?>" type="button"
                        class="float-end btn btn-success rounded-5 waves-effect waves-light mb-2 me-2">
                        <i class="fa-solid fa-plus"></i> Tambah <?= $title ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card mb-3" style="background: whitesmoke;">
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label for="filterKategori">Kategori</label>
                                    <select id="filterKategori" class="form-control select-role select2" style="width: 100%;">
                                    </select>
                                </div>
                                <div class="col-md-3" style="margin-top: 35px;">
                                    <button id="btnResetFilter" class="btn btn-secondary">Reset Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered table-striped nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Produk</th>
                                <th>Harga Jual</th>
                                <th>Qty Stok</th>
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
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="staticBackdropLabel">Qty Print</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="produk_id_modal">
                <div class="col-md-12">
                    <div class="mb-6">
                        <div class="form-check mb-3">
                            <label for="validationCustom02" class="form-label">Qty Print</label>
                            <input type="number" class="form-control" name="qty_print" id="qty_print">
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
<script src="<?= base_url('assets/js/produk.js?v=') . filemtime(FCPATH . 'assets/js/produk.js') ?>"></script>
<?= $this->endSection() ?>