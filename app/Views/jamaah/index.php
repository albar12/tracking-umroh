<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4 py-3">

    <!-- Header Halaman & Tombol Tambah -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 fw-bold text-dark fs-4">
                <i class="fa-solid fa-users text-success me-2"></i> Data <?= $title ?>
            </h4>
            <p class="text-muted mb-0 fs-7">Kelola informasi data <?= strtolower($title) ?> sistem.</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <a href="<?= base_url('home/users/new') ?>"
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

                    <!-- Kotak Filter Modern -->
                    <div class="card border border-light-subtle bg-light-subtle rounded-3 mb-4 shadow-none">
                        <div class="card-body p-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="filterRole" class="form-label fw-semibold text-secondary fs-7 mb-1">Filter Berdasarkan Role</label>
                                    <select id="filterRole" class="form-control select-role select2" style="width: 100%;">
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button id="btnResetFilter" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm fw-medium">
                                        <i class="fa-solid fa-rotate-right me-1"></i> Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover align-middle nowrap w-100 border-bottom border-light">
                            <thead class="table-light text-uppercase fs-8 text-secondary">
                                <tr>
                                    <th class="py-3">No</th>
                                    <th class="py-3">Nama Lengkap</th>
                                    <th class="py-3">Username</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Jenis Kelamin</th>
                                    <th class="py-3">Tanggal Lahir</th>
                                    <th class="py-3">No Handphone</th>
                                    <th class="py-3">Role</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables akan memuat data di sini -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/user.js?v=') . filemtime(FCPATH . 'assets/js/user.js') ?>"></script>
<?= $this->endSection() ?>