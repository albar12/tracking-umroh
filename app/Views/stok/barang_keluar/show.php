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
                                <input type="text" class="form-control" id="no_dokument" name="no_dokument" disabled placeholder="No Dokumen" value="<?= $barangKeluar['no_dokument'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Keluar</label>
                                <input type="date" class="form-control tgl_keluar"
                                    placeholder="yyyy-mm-dd" name="tgl_keluar" id="tgl_keluar" disabled data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                    data-provide="datepicker" data-date-autoclose="true" required value="<?= $barangKeluar['tgl_keluar'] ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Jam Keluar</label>
                                <input id="timepicker2" type="time" name="jam_terima" disabled value="<?= $barangKeluar['jam_keluar'] ?>" class="form-control" data-provide="timepicker">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" disabled rows="3" placeholder="Keterangan"><?= $barangKeluar['keterangan'] ?></textarea>
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
                                            <th>Barcode Data</th>
                                            <th>Produk</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th width="15%">Qty</th>
                                            <th width="15%">Total Harga</th>
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
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <th colspan="5" class="text-end">Total Bayar</th>
                                        <th>
                                            <input type="text" class="form-control" id="total_bayar_show" readonly value="<?= rupiah($barangKeluar['total_harga']) ?>">
                                        </th>
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
                                <label for="validationCustom02" class="form-label">Metode Pembayaran</label>
                                <select class="form-control select select2 metode" disabled id="metode" name="metode" required>
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
                                <label class="form-label">Nominal Bayar</label>
                                <input type="text" class="form-control nominal_bayar" disabled placeholder="Nominal Bayar" name="nominal_bayar" id="nominal_bayar" value="<?= rupiah($barangKeluar['nominal_bayar'])   ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                        <div id="kembalian" class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Nominal Kembalian</label>
                                <input type="text" class="form-control nominal_kembalian" disabled placeholder="Nominal Kembalian" name="nominal_kembalian" id="nominal_kembalian" value="<?= $barangKeluar['nominal_kembalian'] ? rupiah($barangKeluar['nominal_kembalian']) : rupiah("0")  ?>">
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('stok/barang-keluar') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/barang_keluar.js?v=') . filemtime(FCPATH . 'assets/js/barang_keluar.js') ?>"></script>
<?= $this->endSection(); ?>