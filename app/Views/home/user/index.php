<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <a href="<?= base_url('home/users/new') ?>" type="button"
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
                                    <label for="filterRole">Role</label>
                                    <select id="filterRole" class="form-control select-role select2" style="width: 100%;">
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
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Jenis Kelamin</th>
                                <th>Tanggal Lahir</th>
                                <th>No Handphone</th>
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