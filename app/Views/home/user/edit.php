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
                    <form class="needs-validation" action="<?= base_url('home/users/' . $id) ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="_method" value="PUT">
                        <div class="row">
                            <h4 class="card-title"><b>Data Diri</b></h4>
                            <hr>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Nama Lengkap <code>*</code></label>
                                    <input type="text" class="form-control number-only" id="nama_lengkap" name="nama_lengkap" minlength="6"
                                        autofocus required placeholder="Nama Lengkap" value="<?= $user['nama_lengkap'] ?>">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib di isi dan nama lengkap minimal 6 karakter.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Username <code>*</code></label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        required placeholder="User Name" disabled value="<?= $user['username'] ?>">
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Jenis Kelamin <code>*</code></label>
                                    <select class="form-control select select2 jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">--Pilih Jenis Kelamin--</option>
                                        <option value="Laki-laki" <?php if ($user['jenis_kelamin'] == "Laki-laki") {
                                                                        echo 'selected';
                                                                    } ?>>Laki-laki</option>
                                        <option value="Perempuan" <?php if ($user['jenis_kelamin'] == "Perempuan") {
                                                                        echo 'selected';
                                                                    } ?>>Perempuan</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Lahir <code>*</code></label>
                                    <div class="input-group" id="datepicker2">
                                        <input type="date" class="form-control tgl_lahir"
                                            placeholder="yyyy-mm-dd" name="tgl_lahir" id="tgl_lahir" data-date-end-date="<?= date('Y-m-d') ?>"
                                            data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                            data-provide="datepicker" data-date-autoclose="true" required value="<?= $user['tgl_lahir'] ?>">
                                        <span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
                                        <div class="invalid-feedback">
                                            Data wajib diisi.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Alamat <code>*</code></label>
                                    <textarea required name="alamat" class="form-control" rows="3" placeholder="Alamat"><?= $user['alamat'] ?></textarea>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Tlp Rumah</label>
                                    <input type="text" class="form-control" id="tlp" name="tlp" placeholder="Tlp Rumah" value="<?= $user['tlp'] ?>">
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">No HP <code>*</code></label>
                                    <input type="text" class="form-control number-only" id="no_hp" name="no_hp" required placeholder="No HP" value="<?= $user['no_hp'] ?>">
                                    <div class="invalid-feedback" id="hp-feedback">
                                        Wajib di isi dan HP minimal 10 karakter.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Foto Profile<code>*</code></label>
                                    <input type="file" class="form-control" id="foto_profile" name="foto_profile" placeholder="Foto Profile">
                                    <input type="hidden" name="foto_old" value="<?= $user['foto_profile'] ?>">
                                    <?php if (isset($user['foto_profile']) && $user['foto_profile']): ?>
                                        <div class="popup-gallery">
                                            <a href="<?= $user['foto_profile'] ?>" title="Foto">
                                                Lihat Data
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada foto</span>
                                    <?php endif; ?>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Email <code>*</code></label>
                                    <input type="email" name="email" class="form-control" id="validationCustom02" placeholder="Email" required value="<?= $user['email'] ?>">
                                    <input type="hidden" name="emailOld" value="<?= $user['email'] ?>">
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Password <code>*</code></label>
                                    <input type="password" name="password" class="form-control" id="validationCustom02" placeholder="Password">
                                    <input type="hidden" name="passwordOld" value="<?= $user['password'] ?>">
                                    <code>Isi jika ingin dirubah</code>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Role <code>*</code></label>
                                    <select class="form-control select select2 role" name="role" required>
                                        <option value="">--Pilih Role--</option>
                                        <?php foreach ($role_akses as $role): ?>
                                            <option value="<?= $role['role_id'] ?>" <?php if ($role['role_id'] == $user['role']) {
                                                                                        echo "selected";
                                                                                    } ?>><?= $role['role'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Status <code>*</code></label>
                                    <select class="form-control select select2 status" name="status" required>
                                        <option value="">--Pilih Status--</option>
                                        <option value="Aktif" <?php if ($user['status'] == "Aktif") {
                                                                    echo 'selected';
                                                                } ?>>Aktif</option>
                                        <option value="Tidak Aktif" <?php if ($user['status'] == "Tidak Aktif") {
                                                                        echo 'selected';
                                                                    } ?>>Tidak Aktif</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <a href="<?= base_url('home/users') ?>" class="btn btn-secondary waves-effect">Batal</a>
                                <button class="btn btn-primary" type="submit" style="float: right"
                                    id="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="<?= base_url('css/magnific-popup.min.css') ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('js/jquery.magnific-popup.min.js') ?>"></script>
<script src="<?= base_url('assets/js/user.js?v=') . filemtime(FCPATH . 'assets/js/user.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('.image-popup').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            mainClass: 'mfp-img-mobile',
            image: {
                verticalFit: true
            }
        });

        $('.popup-gallery').magnificPopup({
            delegate: 'a',
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    });
</script>
<?= $this->endSection(); ?>