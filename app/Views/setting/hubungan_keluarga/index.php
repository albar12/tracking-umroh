<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4 py-3">

    <!-- Header Halaman & Tombol Tambah -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 fw-bold text-dark fs-4">
                <i class="fa-solid fa-people-roof text-success me-2"></i> Data <?= $title ?>
            </h4>
            <p class="text-muted mb-0 fs-7">Kelola informasi data <?= strtolower($title) ?> sistem.</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <a href="<?= base_url('setting/hubungan-keluarga/new') ?>"
                class="btn btn-success rounded-pill px-4 py-2 shadow-sm waves-effect waves-light fw-medium">
                <i class="fa-solid fa-plus me-1"></i> Tambah <?= $title ?>
            </a>
        </div>
    </div>

    <!-- Card Utama & Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">

                    <!-- Tabel Data Wrapper -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-premium align-middle nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">No</th>
                                            <th>Hubungan Keluarga</th>
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
        </div>
    </div>
</div>



<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/hubungan_keluarga.js?v=') . filemtime(FCPATH . 'assets/js/hubungan_keluarga.js') ?>"></script>
<?= $this->endSection() ?>