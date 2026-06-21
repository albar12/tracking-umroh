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
                    <form class="needs-validation" action="<?= base_url('setting/toko/' . $id) ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PUT">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Nama Outlet <code>*</code></label>
                                    <input type="text" class="form-control" id="nama_toko" name="nama_toko" autofocus required placeholder="Nama Outlet" value="<?= $toko['nama_toko'] ?>">
                                    <input type="hidden" name="nama_toko_old" id="nama_toko_old" value="<?= $toko['nama_toko'] ?>">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Email <code>*</code></label>
                                    <input type="text" class="form-control" id="email" name="email" autofocus required placeholder="Email" value="<?= $toko['email'] ?>">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">No. Telp <code>*</code></label>
                                    <input type="text" class="form-control" id="no_telp" name="no_telp" autofocus required placeholder="No. Telp" value="<?= $toko['no_telp'] ?>">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Status <code>*</code></label>
                                    <select class="form-control select select2 status" name="status" required>
                                        <option value="">--Pilih Status--</option>
                                        <option value="Aktif" <?php if ($toko['status'] == "Aktif") {
                                                                    echo 'selected';
                                                                } ?>>Aktif</option>
                                        <option value="Tidak Aktif" <?php if ($toko['status'] == "Tidak Aktif") {
                                                                        echo 'selected';
                                                                    } ?>>Tidak Aktif</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Logo <code>*</code></label>
                                    <input type="file" class="form-control" id="logo" name="logo" autofocus required placeholder="Logo" value="<?= $toko['logo'] ?>">
                                    <input type="hidden" id="logoOld" name="logoOld" value="<?= $toko['logo'] ?>">
                                    <?php if (!empty($toko['logo'])) : ?>
                                        <div style="width: 80px; height: 80px; padding: 3px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px;">
                                            <img src="<?= base_url($toko['logo']) ?>" alt="Logo Toko" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-text">Upload logo (jpg, png, dll)</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-12">
                                    <label for="validationCustom02" class="form-label">Alamat <code>*</code></label>
                                    <textarea required name="alamat" class="form-control" rows="3" placeholder="Alamat"><?= $toko['alamat'] ?></textarea>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <a href="<?= base_url('setting/toko') ?>" class="btn btn-secondary waves-effect">Batal</a>
                                <button class="btn btn-primary" type="submit" style="float: right" id="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/toko.js?v=') . filemtime(FCPATH . 'assets/js/toko.js') ?>"></script>
<?= $this->endSection(); ?>