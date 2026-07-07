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
                                <label for="validationCustom02" class="form-label">Batch <code>*</code></label>
                                <input type="text" class="form-control" id="batch" name="batch"
                                    autofocus required placeholder="Batch">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai <code>*</code></label>
                                <input type="date" class="form-control tgl_mulai"
                                    placeholder="yyyy-mm-dd" name="tgl_mulai" id="tgl_mulai" min="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" required>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Selesai <code>*</code></label>
                                <input type="date" class="form-control tgl_selesai"
                                    placeholder="yyyy-mm-dd" name="tgl_selesai" id="tgl_selesai" data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" required>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-12">
                                <label for="validationCustom02" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                    <br>
                    <h5 class="card-title">List Produk SO</h5>
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
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/stok-opname') ?>" class="btn btn-secondary waves-effect">Batal</a>
                            <button class="btn btn-primary" type="submit" style="float: right" id="submit">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/stok_opname.js?v=') . filemtime(FCPATH . 'assets/js/stok_opname.js') ?>"></script>
<?= $this->endSection(); ?>