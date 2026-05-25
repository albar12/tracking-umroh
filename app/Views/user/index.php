<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <a href="<?= base_url('dashboard/users/new') ?>" type="button"
                        class="float-end btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                        <i class="mdi mdi-plus me-1"></i> Tambah <?= $title ?>
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
                                    <label for="filterVendor">Vendor</label>
                                    <select id="filterVendor" class="form-control select-vendor select2" style="width: 100%;">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filterBank">Project</label>
                                    <select id="filterBank" class="form-control select-bank select2" style="width: 100%;">
                                        <option value="">-- Pilih Project--</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filterSP">Service Point</label>
                                    <select id="filterSP" class="form-control select-vendor select2" style="width: 100%;">
                                        <option value="">-- Pilih Service Point--</option>
                                    </select>
                                </div>
                                <div class="col-md-3" style="margin-top: 35px;">
                                    <button id="btnResetFilter" class="btn btn-secondary">Reset Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN/NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
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
<script src="<?= base_url('assets/js/user.js?v=') . filemtime(FCPATH . 'assets/js/user.js') ?>"></script>
<?= $this->endSection() ?>