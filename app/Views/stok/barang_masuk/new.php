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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">No Dokumen <code>*</code></label>
                                <input type="text" class="form-control" id="no_dokument_supplier" name="no_dokument_supplier"
                                    autofocus required placeholder="No Dokumen">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Terima <code>*</code></label>
                                <input type="date" class="form-control tgl_terima"
                                    placeholder="yyyy-mm-dd" name="tgl_terima" id="tgl_terima" data-date-end-date="<?= date('Y-m-d') ?>" data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" required>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Jam Terima</label>
                                <input id="timepicker2" type="time" name="jam_terima" value="<?= date('H:i') ?>" class="form-control" data-provide="timepicker">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Diterima Oleh <code>*</code></label>
                                <select class="form-control select select2 diterima" id="diterima" name="diterima" required>
                                    <option value="">--Pilih Penerima--</option>
                                    <?php foreach ($diterima as $diterima): ?>
                                        <option value="<?= $diterima['user_id'] ?>"><?= $diterima['nama_lengkap'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Supplier <code>*</code></label>
                                <select class="form-control select select2 diserahkan" id="diserahkan" name="diserahkan" required>
                                    <option value="">--Pilih Supplier--</option>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['supplier_id'] ?>"><?= $supplier['supplier'] ?></option>
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
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                    <h5 class="card-title">List Produk</h5>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <select class="form-control select select2 kategori_id" id="kategori_id" name="kategori_id" required>
                                    <option value="">--Pilih Kategori--</option>
                                    <?php foreach ($kategoris as $kategori): ?>
                                        <option value="<?= $kategori['kategori_id'] ?>" data-name="<?= $kategori['kategori'] ?>"><?= $kategori['kategori'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <select class="form-control select select2 produk" name="produk" required>
                                    <option value="">--Pilih Produk--</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="stok" name="stok" readonly
                                    placeholder="Stok">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mb-3">
                                <input type="text" class="form-control number-only" id="qty" name="qty"
                                    placeholder="Qty">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="ket" name="ket"
                                    placeholder="Keterangan">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <input type="hidden" class="form-control" id="jenis_sn">
                                <button class="btn btn-info" type="submit" id="tambah">Tambah</button>
                            </div>
                        </div>
                    </div>
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
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <th colspan="4" class="text-end">Total Produk</th>
                                        <th><input type="text" class="form-control" id="total_produk" readonly></th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/barang-masuk') ?>" class="btn btn-secondary waves-effect">Batal</a>
                            <button class="btn btn-primary" type="submit" style="float: right" id="submit">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/barang_masuk.js?v=') . filemtime(FCPATH . 'assets/js/barang_masuk.js') ?>"></script>
<?= $this->endSection(); ?>