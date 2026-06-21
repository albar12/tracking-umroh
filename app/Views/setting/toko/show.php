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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Nama Outlet</label>
                                <input type="text" class="form-control" id="nama_toko" name="nama_toko" autofocus placeholder="Nama Outlet" disabled value="<?= $toko['nama_toko'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" autofocus disabled placeholder="Email" value="<?= $toko['email'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">No. Telp</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp" autofocus disabled placeholder="No. Telp" value="<?= $toko['no_telp'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status" autofocus disabled placeholder="Status" value="<?= $toko['status'] ?>">
                                <div class="invalid-feedback" id="nik-feedback">
                                    Wajib di isi.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Logo</label>
                                <input type="file" class="form-control mb-2" name="logo_aplikasi" value="<?= $toko['logo'] ?>" accept="image/*" disabled>
                                <?php if (!empty($toko['logo'])) : ?>
                                    <div style="width: 80px; height: 80px; padding: 3px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px;">
                                        <img src="<?= base_url($toko['logo']) ?>" alt="Logo Toko" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-12">
                                <label for="validationCustom02" class="form-label">Alamat</label>
                                <textarea disabled name="alamat" class="form-control" rows="3" placeholder="Alamat"><?= $toko['alamat'] ?></textarea>
                                <div class="invalid-feedback">
                                    Data wajib diisi.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12">
                            <a href="<?= base_url('setting/toko') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/toko.js?v=') . filemtime(FCPATH . 'assets/js/toko.js') ?>"></script>
<?= $this->endSection(); ?>