<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login Sistem' ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/images/icon_title_app.png') ?>">

    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery.dataTables.min.css') ?>">

    <style>
        body {
            min-height: 100vh;
            /* Gradasi Hijau Zamrud Premium untuk Background Login */
            background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #047857 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 980px;
            border: none;
            border-radius: 24px;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 20px 50px rgba(2, 44, 34, 0.25);
        }

        .left-side {
            background: linear-gradient(180deg, #064e3b 0%, #022c22 100%);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .app-badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
            width: fit-content;
            color: #a7f3d0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .left-side h1 {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .left-side p {
            font-size: 15px;
            opacity: 0.85;
            line-height: 1.7;
            color: #ecfdf5;
        }

        .inventory-image {
            width: 100%;
            max-width: 300px;
            margin-top: 25px;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.2));
        }

        .right-side {
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }

        .login-subtitle {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            padding-left: 45px;
            border: 1px solid #dee2e6;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.15);
            border-color: #10b981;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper>i {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 10;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            z-index: 10;
            background: transparent;
            border: none;
        }

        .toggle-password:hover {
            color: #10b981;
        }

        .btn-login {
            height: 48px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            background: #10b981;
            color: white;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-login:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
        }

        .forgot-pass {
            color: #059669;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .forgot-pass:hover {
            text-decoration: underline;
        }

        /* Responsive Mobile */
        @media (max-width: 767.98px) {
            .login-container {
                padding: 15px !important;
            }

            .login-card {
                border-radius: 18px;
            }

            .right-side {
                padding: 35px 24px;
            }
        }
    </style>
</head>

<body>

    <div class="container login-container d-flex justify-content-center align-items-center py-4">
        <div class="card login-card shadow-lg">
            <div class="row g-0">

                <!-- Sisi Kiri (Branding / Ilustrasi) -->
                <div class="col-md-6 left-side d-none d-md-flex">
                    <div class="app-badge">
                        <i class="fa-solid fa-kaaba"></i>
                        Umroh Tracking System
                    </div>
                    <h1>Kelola Perjalanan & Jamaah Lebih Mudah</h1>
                    <p>
                        Pantau data manifest, jadwal keberangkatan, dokumen paspor,
                        serta layanan jamaah secara real-time dalam satu platform terintegrasi.
                    </p>
                    <img src="<?= base_url("assets/images/icon_app.png") ?>"
                        alt="Umroh Illustration"
                        class="img-fluid inventory-image">
                </div>

                <!-- Sisi Kanan (Form Login) -->
                <div class="col-12 col-md-6 right-side">
                    <div class="mb-4">
                        <h2 class="login-title">Selamat Datang 👋</h2>
                        <p class="login-subtitle">
                            Silakan masuk menggunakan akun administrator anda
                        </p>
                    </div>

                    <form class="needs-validation" action="<?= base_url('/auth/login'); ?>" method="POST" novalidate>
                        <?= csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary fs-7">Email</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" class="form-control" name="email" id="email" placeholder="nama@email.com" required>
                                <div class="invalid-feedback">
                                    Data wajib diisi dengan format Email yang valid.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary fs-7">Password</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" required class="form-control" name="password" id="password" placeholder="••••••••">
                                <button type="button" class="toggle-password" id="togglePasswordBtn" title="Lihat Password">
                                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                                </button>
                                <div class="invalid-feedback">
                                    Password wajib diisi.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center mb-4">
                            <a href="<?= base_url('lupa-password') ?>" class="forgot-pass">
                                Lupa Password?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-login w-100">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk ke Sistem
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Pendukung -->
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('adminlte/dist/js/adminlte.min.js') ?>"></script>
    <script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/sweetalert2/sweetalert2.all.min.js') ?>"></script>

    <script>
        // Fitur Toggle Show/Hide Password
        document.getElementById('togglePasswordBtn').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // Notifikasi SweetAlert Toast Flashdata
        <?php if (session()->getFlashdata('toast')) : ?>
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "<?= session()->getFlashdata('toast')['type'] ?>",
                title: "<?= session()->getFlashdata('toast')['message'] ?>",
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>

        function setToast(type, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000
            });
        }
    </script>
</body>

</html>