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
                    <input type="hidden" id="barang_keluar_id" name="barang_keluar_id" value="<?= $id ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">No Dokumen</label>
                                <input type="text" class="form-control" id="no_dokument" name="no_dokument" disabled placeholder="No Dokumen" value="<?= $barangKeluar['no_dokument'] ?>">
                                <input type="hidden" id="no_dokument_value" name="no_dokument_value" value="<?= $barangKeluar['no_dokument'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Keluar <code>*</code></label>
                                <input type="date" class="form-control tgl_keluar"
                                    placeholder="yyyy-mm-dd" name="tgl_keluar" id="tgl_keluar" data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" required min="<?= date("Y-m-d") ?>" value="<?= $barangKeluar['tgl_keluar'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Jam Keluar</label>
                                <input id="timepicker2" type="time" name="jam_terima" value="<?= $barangKeluar['jam_keluar'] ?>" class="form-control" data-provide="timepicker">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Keterangan"><?= $barangKeluar['keterangan'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <h5 class="card-title">List Produk</h5>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <input type="text" autofocus class="form-control" id="barcode" name="barcode" placeholder="Barcode">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <select class="form-control select select2 kategori_id" id="kategori_id" name="kategori_id" required>
                                    <option value="">--Pilih Kategori--</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
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
                                <button class="btn btn-info" type="submit" id="tambah_produk">Tambah</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-scroll-horizontal">
                                <table id="produkTable" class="table table-bordered dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th>Barcode Data</th>
                                            <th>Produk</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th width="15%">Qty</th>
                                            <th width="15%">Total Harga</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach ($detailBarangKeluar as $key => $value) :
                                        ?>
                                            <tr>
                                                <td><?= esc($value['barcode_value']) ?></td>
                                                <td><input type="hidden" name="produk_list[]" value="<?= esc($value['produk_id']) ?>"><?= esc($value['produk']) ?></td>
                                                <td><?= esc($value['kategori']) ?></td>
                                                <td><?= esc(rupiah($value['harga_jual'])) ?></td>
                                                <td><?= esc($value['qty']) ?></td>
                                                <td><?= esc(rupiah($value['total_harga'])) ?></td>
                                                <td>
                                                    <!-- Tombol Edit -->
                                                    <a href="javascript:void(0);" class="text-success edit-btn"
                                                        data-id="<?= esc($value['detail_barang_keluar_id']) ?>"
                                                        data-harga-jual="<?= $value['harga_jual'] ?>"
                                                        data-qty="<?= esc($value['qty']) ?>">

                                                        <i class="fa-solid fa-pencil font-size-18"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" class="text-danger delete-produk-btn" data-id="<?= esc($value['detail_barang_keluar_id']) ?>" data-harga-jual="<?= $value['harga_jual'] ?>" data-qty="<?= esc($value['qty']) ?>">
                                                        <i class="fa-solid fa-trash font-size-18"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <th colspan="5" class="text-end">Total Bayar</th>
                                        <th>
                                            <input type="text" class="form-control" id="total_bayar_show" readonly value="<?= rupiah($barangKeluar['total_harga']) ?>">
                                            <input type="hidden" class="form-control" id="total_bayar" readonly value="<?= $barangKeluar['total_harga'] ?>">
                                        </th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <h5 class="card-title">Pembayaran</h5>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Metode Pembayaran <code>*</code></label>
                                <select class="form-control select select2 metode" id="metode" name="metode" required>
                                    <option value="">--Pilih Metode--</option>
                                    <?php foreach ($metodes as $metode): ?>
                                        <option value="<?= $metode['metode_id'] ?>" <?php if ($metode['metode_id'] == $barangKeluar['metode_pembayaran']) {
                                                                                        echo "selected";
                                                                                    } ?>><?= $metode['metode'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Nominal Bayar <code>*</code></label>
                                <input type="number" class="form-control nominal_bayar" placeholder="Nominal Bayar" name="nominal_bayar" id="nominal_bayar" required value="<?= $barangKeluar['nominal_bayar'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div id="kembalian" class="col-md-3" <?php if (!$barangKeluar['nominal_kembalian']) {
                                                                    echo  'style = "display: none;"';
                                                                } ?>>
                            <div class="mb-3">
                                <label class="form-label">Nominal Kembalian </label>
                                <input type="text" class="form-control nominal_kembalian" placeholder="Nominal Kembalian" name="nominal_kembalian" id="nominal_kembalian" readonly value="<?= $barangKeluar['nominal_kembalian'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/barang-masuk') ?>" class="btn btn-secondary waves-effect">Batal</a>
                            <button class="btn btn-primary" type="submit" style="float: right" id="submit_update">Simpan</button>
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
                    <input type="hidden" name="harga_jual_edit" id="edit-harga-jual">
                    <input type="hidden" name="total_harga_edit" id="edit-total-harga">
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
<script src="<?= base_url('assets/js/barang_keluar.js?v=') . filemtime(FCPATH . 'assets/js/barang_keluar.js') ?>"></script>
<?= $this->endSection(); ?>