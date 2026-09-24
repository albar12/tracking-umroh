<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-4 py-3">

    <!-- Header Halaman & Breadcrumb -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 fw-bold text-dark fs-4">
                <i class="fa-solid fa-stethoscope text-success me-2"></i> Data Detail <?= $title ?>
            </h4>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0 fs-7">
                    <li class="breadcrumb-item"><a href="<?= base_url('setting/riwayat-penyakit') ?>" class="text-decoration-none text-secondary"><?= $title ?></a></li>
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
                    <!-- Section Judul Data Diri -->
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark mb-0 fs-5">
                            <i class="fa-solid fa-id-card text-success me-2"></i> Informasi Data Riwayat Penyakit
                        </h5>
                    </div>

                    <div class="row g-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="riwayat_penyakit_khusus" class="form-label fw-semibold text-secondary fs-7">Riwayat Penyakit Khusus</label>
                                    <input type="text" class="form-control number-only py-2 rounded-3" id="riwayat_penyakit_khusus" name="riwayat_penyakit_khusus" minlength="6"
                                        autofocus disabled placeholder="Riwayat Penyakit Khusus" value="<?= $riwayatPenyakit['riwayat_penyakit_khusus'] ?>">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Data wajib diisi.
                                    </div>
                                    <input type="hidden" id="riwayat_penyakit_khusus_old" name="riwayat_penyakit_khusus_old" value="<?= $riwayatPenyakit['riwayat_penyakit_khusus'] ?>">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary fs-7 d-block">
                                        Status
                                    </label>
                                    <div class="form-check form-switch pt-1">
                                        <input class="form-check-input" disabled type="checkbox" role="switch" id="status" name="status" value="Aktif" style="width: 2.8em; height: 1.4em; cursor: pointer;" <?= (isset($riwayatPenyakit['status']) && $riwayatPenyakit['status'] === 'Aktif') ? 'checked' : '' ?>>
                                        <label class="form-check-input-label fw-semibold ms-2" for="status" id="statusLabel" style="cursor: pointer; line-height: 1.8;">
                                            <?= (isset($riwayatPenyakit['status']) && $riwayatPenyakit['status'] === 'Aktif') ? 'Aktif' : 'Tidak Aktif' ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Bawah -->
                    <div class="row mt-4 pt-3 border-top">
                        <div class="col-sm-12 d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('setting/riwayat-penyakit') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 waves-effect fw-medium">
                                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/riwayat_penyakit.js?v=') . filemtime(FCPATH . 'assets/js/riwayat_penyakit.js') ?>"></script>
<?= $this->endSection(); ?>