<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-4 py-3">

    <!-- Header Halaman & Breadcrumb -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 fw-bold text-dark fs-4">
                <i class="fa-solid fa-bed text-success me-2"></i> Form Tambah <?= $title ?>
            </h4>
            <p class="text-muted mb-0 fs-7">Lengkapi data jenis kamar di bawah ini dengan benar.</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0 fs-7">
                    <li class="breadcrumb-item"><a href="<?= base_url('setting/pekerjaan') ?>" class="text-decoration-none text-secondary"><?= $title ?></a></li>
                    <li class="breadcrumb-item active text-success fw-semibold" aria-current="page"><?= $sub ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Card Utama Form -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-lg-5">

                    <!-- Alert Error Validasi -->
                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger border-0 rounded-4 shadow-sm p-3 mb-4" role="alert">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                                <span class="fw-bold">Terjadi Kesalahan Validasi:</span>
                            </div>
                            <ul class="mb-0 ps-3">
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form class="needs-validation" action="<?= base_url('setting/jenis-kamar') ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field(); ?>

                        <!-- Section Judul Data Diri -->
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <h5 class="fw-bold text-dark mb-0 fs-5">
                                <i class="fa-solid fa-id-card text-success me-2"></i> Informasi Data Jenis Kamar
                            </h5>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="kamar" class="form-label fw-semibold text-secondary fs-7">Jenis Kamar <code class="text-danger">*</code></label>
                                    <input type="text" class="form-control number-only py-2 rounded-3" id="kamar" name="kamar" minlength="6"
                                        autofocus required placeholder="Jenis Kamar">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Bawah -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-sm-12 d-flex justify-content-between align-items-center">
                                <a href="<?= base_url('setting/jenis-kamar') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 waves-effect fw-medium">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Batal
                                </a>
                                <button class="btn btn-success rounded-pill px-5 py-2 shadow-sm waves-effect fw-medium" type="submit" id="submit">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jenis_kamar.js?v=') . filemtime(FCPATH . 'assets/js/jenis_kamar.js') ?>"></script>
<?= $this->endSection(); ?>