<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Perbarui Password - Dashboard' ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/images/icon_title_app.png') ?>">
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery.dataTables.min.css') ?>">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e40af);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 950px;
            border: none;
            border-radius: 28px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        }

        .left-side {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .app-badge {
            background: rgba(255, 255, 255, 0.15);
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            width: fit-content;
        }

        .left-side h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .left-side p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.7;
        }

        .inventory-image {
            width: 100%;
            max-width: 350px;
            margin-top: 30px;
        }

        .right-side {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-size: 30px;
            font-weight: 700;
            color: #111827;
        }

        .login-subtitle {
            color: #6b7280;
            margin-bottom: 35px;
        }

        .form-control {
            height: 52px;
            border-radius: 14px;
            padding-left: 45px;
            padding-right: 45px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #2563eb;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.form-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #6b7280;
            z-index: 10;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            z-index: 10;
            transition: 0.2s;
        }

        .toggle-password:hover {
            color: #2563eb;
        }

        .btn-reset {
            height: 52px;
            border-radius: 14px;
            border: none;
            font-weight: 600;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            transition: 0.3s;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            opacity: 0.95;
            color: white;
        }

        .footer-text {
            text-align: center;
            font-size: 14px;
            margin-top: 25px;
        }

        .footer-text a {
            text-decoration: none;
            font-weight: 600;
            color: #2563eb;
        }

        /* --- BREAKPOINT REFACTOR KHUSUS MOBILE (< 768px) --- */
        @media (max-width: 767.98px) {
            .login-container {
                padding: 15px !important;
            }

            .login-card {
                border-radius: 20px;
            }

            .right-side {
                padding: 40px 24px;
            }

            .login-title {
                font-size: 26px;
            }

            .login-subtitle {
                margin-bottom: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="container login-container d-flex justify-content-center align-items-center py-4">
        <div class="card login-card">
            <div class="row g-0">

                <div class="col-md-6 left-side d-none d-md-flex">
                    <div class="app-badge">
                        <i class="bi bi-box-seam"></i>
                        Stock Management System
                    </div>
                    <h1>Password Baru</h1>
                    <p>
                        Langkah terakhir untuk mengamankan akun Anda kembali.
                        Buatlah kombinasi kata sandi baru yang kuat dan mudah Anda ingat demi perlindungan data optimal.
                    </p>
                    <img src="<?= base_url("assets/images/icon_app.png") ?>"
                        alt="Inventory Illustration"
                        class="img-fluid inventory-image">
                </div>

                <div class="col-12 col-md-6 right-side">
                    <div class="mb-4">
                        <h2 class="login-title">Perbarui Password</h2>
                        <p class="login-subtitle">
                            Silakan atur ulang kata sandi baru untuk akun Anda
                        </p>
                    </div>

                    <form class="needs-validation" action="<?= base_url('/update-password-action'); ?>" method="POST" novalidate>
                        <input type="hidden" name="email" value="<?= $email ?? '' ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-lock form-icon"></i>
                                <input type="password"
                                    class="form-control"
                                    name="password"
                                    id="password"
                                    placeholder="Masukkan password baru"
                                    minlength="5"
                                    required>
                                <i class="fa-solid fa-eye toggle-password" data-target="#password"></i>
                                <div class="invalid-feedback">
                                    Password minimal harus terdiri dari 5 karakter.
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-key form-icon"></i>
                                <input type="password"
                                    class="form-control"
                                    name="confirm_password"
                                    id="confirm_password"
                                    placeholder="Ulangi password baru"
                                    required>
                                <i class="fa-solid fa-eye toggle-password" data-target="#confirm_password"></i>
                                <div class="invalid-feedback">
                                    Konfirmasi password wajib diisi.
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-reset w-100 mb-3 text-white">
                            <i class="fa-solid fa-save me-2"></i> Simpan Password Baru
                        </button>

                        <div class="footer-text mt-2">
                            Batal mereset? <a href="<?= base_url('/login'); ?>" class="fw-semibold text-decoration-none">Kembali ke Login</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('adminlte/dist/js/adminlte.min.js') ?>"></script>
    <script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/sweetalert2/sweetalert2.all.min.js') ?>"></script>

    <script>
        const BASE_URL = "<?= base_url() ?>";

        // Toast Session Handler
        <?php if (session()->getFlashdata('toast')) :
            $toast = session()->getFlashdata('toast'); ?>
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "<?= $toast['type'] ?>",
                title: "<?= $toast['message'] ?>",
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>

        // Toggle Visibility Password
        $(document).ready(function() {
            $('.toggle-password').on('click', function() {
                var targetSelector = $(this).data('target');
                var inputField = $(targetSelector);

                if (inputField.attr('type') === 'password') {
                    inputField.attr('type', 'text');
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    inputField.attr('type', 'password');
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });

        // Form Validation System
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    var password = $('#password').val();
                    var confirm = $('#confirm_password').val();

                    if (password !== confirm) {
                        event.preventDefault();
                        event.stopPropagation();

                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "error",
                            title: "Konfirmasi password tidak cocok!",
                            showConfirmButton: false,
                            timer: 3000
                        });
                        return false;
                    }

                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false)
            })
        })()
    </script>
</body>

</html>