<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <a href="<?= base_url('setting/supplier/new') ?>" type="button"
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
                    <table id="datatable" class="table table-bordered table-striped nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Supplier</th>
                                <th>Alamat</th>
                                <th>Tlp Supplier</th>
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
<script src="<?= base_url('assets/js/supplier.js?v=') . filemtime(FCPATH . 'assets/js/supplier.js') ?>"></script>
<?= $this->endSection() ?>