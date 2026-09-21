<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-4 py-3">

    <!-- Header Halaman & Breadcrumb -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 fw-bold text-dark fs-4">
                <i class="fa-solid fa-user-plus text-success me-2"></i> Form Tambah <?= $title ?>
            </h4>
            <p class="text-muted mb-0 fs-7">Lengkapi data diri dan pengaturan akun di bawah ini dengan benar.</p>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-sm-end mb-0 bg-transparent p-0 fs-7">
                    <li class="breadcrumb-item"><a href="<?= base_url('home/users') ?>" class="text-decoration-none text-secondary"><?= $title ?></a></li>
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

                    <form class="needs-validation" action="<?= base_url('home/users') ?>" method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrf_field(); ?>

                        <!-- Section Judul Data Diri -->
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <h5 class="fw-bold text-dark mb-0 fs-5">
                                <i class="fa-solid fa-id-card text-success me-2"></i> Informasi Data Diri
                            </h5>
                        </div>

                        <div class="row g-3">
                            <!-- Nama Lengkap -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label fw-semibold text-secondary fs-7">Nama Lengkap <code class="text-danger">*</code></label>
                                    <input type="text" class="form-control number-only py-2 rounded-3" id="nama_lengkap" name="nama_lengkap" minlength="6"
                                        autofocus required placeholder="Nama Lengkap">
                                    <div class="invalid-feedback" id="nik-feedback">
                                        Wajib diisi dan nama lengkap minimal 6 karakter.
                                    </div>
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-semibold text-secondary fs-7">Username <code class="text-danger">*</code></label>
                                    <input type="text" class="form-control py-2 rounded-3" id="username" name="username"
                                        required placeholder="User Name">
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label fw-semibold text-secondary fs-7">Jenis Kelamin <code class="text-danger">*</code></label>
                                    <select class="form-control select select2 jenis_kelamin" id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">--Pilih Jenis Kelamin--</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="tgl_lahir" class="form-label fw-semibold text-secondary fs-7">Tanggal Lahir <code class="text-danger">*</code></label>
                                    <div class="input-group" id="datepicker2">
                                        <input type="date" class="form-control tgl_lahir py-2"
                                            placeholder="yyyy-mm-dd" name="tgl_lahir" id="tgl_lahir" data-date-end-date="<?= date('Y-m-d') ?>"
                                            data-date-format="yyyy-mm-dd" data-date-container='#datepicker2'
                                            data-provide="datepicker" data-date-autoclose="true" required>
                                        <span class="input-group-text bg-light text-secondary border-start-0"><i class="fa-regular fa-calendar"></i></span>
                                        <div class="invalid-feedback">
                                            Data wajib diisi.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="alamat" class="form-label fw-semibold text-secondary fs-7">Alamat <code class="text-danger">*</code></label>
                                    <textarea required name="alamat" id="alamat" class="form-control rounded-3" rows="3" placeholder="Alamat lengkap..."></textarea>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>

                            <!-- Tlp Rumah -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="tlp" class="form-label fw-semibold text-secondary fs-7">Tlp Rumah</label>
                                    <input type="text" class="form-control py-2 rounded-3" id="tlp" name="tlp"
                                        placeholder="Tlp Rumah">
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>

                            <!-- No HP -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label fw-semibold text-secondary fs-7">No HP <code class="text-danger">*</code></label>
                                    <input type="text" class="form-control number-only py-2 rounded-3" id="no_hp" name="no_hp"
                                        required placeholder="No HP">
                                    <div class="invalid-feedback" id="hp-feedback">
                                        Wajib diisi dan HP minimal 10 karakter.
                                    </div>
                                </div>
                            </div>

                            <!-- Foto Profile -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="foto_profile" class="form-label fw-semibold text-secondary fs-7">Foto Profile <code class="text-danger">*</code></label>
                                    <input type="file" class="form-control py-2 rounded-3" id="foto_profile" name="foto_profile"
                                        required placeholder="Foto Profile">
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold text-secondary fs-7">Email <code class="text-danger">*</code></label>
                                    <input type="email" name="email" class="form-control py-2 rounded-3" id="email"
                                        placeholder="nama@email.com" required>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold text-secondary fs-7">Password <code class="text-danger">*</code></label>
                                    <input type="password" name="password" class="form-control py-2 rounded-3" id="password"
                                        placeholder="••••••••" required>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>

                            <!-- Role -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="role" class="form-label fw-semibold text-secondary fs-7">Role <code class="text-danger">*</code></label>
                                    <select class="form-control select select2 role" id="role" name="role" required>
                                        <option value="">--Pilih Role--</option>
                                        <?php foreach ($role_akses as $role): ?>
                                            <option value="<?= $role['role_id'] ?>"><?= $role['role'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Bawah -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-sm-12 d-flex justify-content-between align-items-center">
                                <a href="<?= base_url('home/users') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 waves-effect fw-medium">
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
<script src="<?= base_url('assets/js/user.js?v=') . filemtime(FCPATH . 'assets/js/user.js') ?>"></script>
<?= $this->endSection(); ?>