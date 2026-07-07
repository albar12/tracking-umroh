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
                        <input type="hidden" id="id" name="id" value="<?= $so_id ?>">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">No Dokument</label>
                                <input type="text" class="form-control" disabled
                                    autofocus required placeholder="No Dokument" value="<?= $stokOpname['no_dokument'] ?>">
                                <input type="hidden" id="no_dokument" name="no_dokument" value="<?= $stokOpname['no_dokument'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Batch</label>
                                <input type="text" class="form-control" disabled id="batch" name="batch"
                                    autofocus required placeholder="Batch" value="<?= $stokOpname['batch'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control tgl_mulai"
                                    placeholder="yyyy-mm-dd" name="tgl_mulai" disabled id="tgl_mulai" min="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" value="<?= $stokOpname['tgl_mulai'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control tgl_selesai"
                                    placeholder="yyyy-mm-dd" name="tgl_selesai" disabled id="tgl_selesai" data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" value="<?= $stokOpname['tgl_selesai'] ?>">
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
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" disabled placeholder="Keterangan"><?= $stokOpname['keterangan'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <br>
                    <h5 class="card-title">List Produk SO</h5>
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
                                            <th>Qty</th>
                                            <th>Status SO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($detailStokOpname as $key => $value) :
                                            if ($value['status_so']) {
                                                $statusSo = '<span class="badge rounded-pill bg-' . $value['warna_span'] . '">' . $value['status_so'] . '</span>';
                                            } else {
                                                $statusSo = '<span class="badge rounded-pill bg-secondary">Belum Syncron</span>';
                                            }
                                        ?>
                                            <tr>
                                                <td><input type="hidden" name="produk_list[]" value="<?= esc($value['produk_id']) ?>"><?= esc($value['produk']) ?></td>
                                                <td><?= esc($value['kategori']) ?></td>
                                                <td><?= esc($value['qty']) ?></td>
                                                <td><?= $statusSo ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Persetujuan <code>*</code></label>
                                <select class="form-control select select2" name="persetujuan" id="persetujuan" required>
                                    <option value="">--Pilih Persetujuan--</option>
                                    <option value="Approve" <?= $stokOpname['status_approval'] == 'Approve' ? 'selected' : '' ?>>Approve</option>
                                    <option value="UnApprove" <?= $stokOpname['status_approval'] == 'UnApprove' ? 'selected' : '' ?>>UnApprove</option>
                                </select>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-none" id="approval_keteragan">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Keterangan <code>*</code></label>
                                <textarea name="keterangan" id="keterangan_approval" class="form-control" rows="2"><?= $stokOpname['keterangan_approval'] ?></textarea>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if ($stokOpname['keterangan_approval']) { ?>
                                Note sebelumnya : <code><?= $stokOpname['keterangan_approval'] ?></code>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/stok-opname') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                            <button class="btn btn-primary" type="button" style="float: right" id="selesai-approval">Simpan</button>
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