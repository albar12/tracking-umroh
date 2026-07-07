<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <a href="<?= base_url('stok/stok-opname/new') ?>" type="button"
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
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="tgl_mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="tgl_mulai" name="tgl_mulai" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label for="tgl_selesai" class="form-label">Tanggal Selesai</label>
                                    <input type="date" id="tgl_selesai" name="tgl_selesai" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <button id="btnCariFilter" class="btn btn-primary"><i class="bi bi-search me-1"></i> Filter</button>
                                    <button id="btnResetFilter" class="btn btn-secondary ms-1">Reset</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered table-striped nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Dokumen</th>
                                <th>Batch</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
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
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/stok_opname.js?v=') . filemtime(FCPATH . 'assets/js/stok_opname.js') ?>"></script>
<?= $this->endSection() ?>