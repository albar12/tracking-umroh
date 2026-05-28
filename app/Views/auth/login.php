<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?></title>

    <!-- Bootstrap 5 -->
    <link href=" <?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>">

    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"> -->
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
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #2563eb;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #6b7280;
            z-index: 10;
        }

        .btn-login {
            height: 52px;
            border-radius: 14px;
            border: none;
            font-weight: 600;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            opacity: 0.95;
        }

        .footer-text {
            text-align: center;
            font-size: 14px;
            margin-top: 25px;
        }

        .footer-text a {
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 768px) {

            .left-side {
                text-align: center;
                padding: 40px 25px;
            }

            .left-side h1 {
                font-size: 32px;
            }

            .right-side {
                padding: 35px 25px;
            }

            .inventory-image {
                max-width: 250px;
                margin-left: auto;
                margin-right: auto;
            }

            .app-badge {
                margin-left: auto;
                margin-right: auto;
            }
        }
    </style>
</head>

<body>

    <div class="container login-container d-flex justify-content-center align-items-center py-4">

        <div class="card login-card">

            <div class="row g-0">

                <!-- Left Side -->
                <div class="col-md-6 left-side">

                    <div class="app-badge">
                        <i class="bi bi-box-seam"></i>
                        Stock Management System
                    </div>

                    <h1>Kelola Stok Lebih Mudah</h1>

                    <p>
                        Pantau stok barang, transaksi masuk & keluar,
                        serta laporan inventory secara real-time dalam satu sistem.
                    </p>

                    <img src="<?= base_url("assets/images/img_login_ilustrator.png") ?>"
                        alt="Inventory Illustration"
                        class="img-fluid inventory-image">

                </div>

                <!-- Right Side -->
                <div class="col-md-6 right-side">

                    <div class="mb-4">
                        <h2 class="login-title">Login</h2>
                        <p class="login-subtitle">
                            Silakan masuk ke akun anda
                        </p>
                    </div>

                    <form class="needs-validation" action="<?= base_url('/auth/login'); ?>" method="POST">

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Email
                            </label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan email" required>
                                <div class="invalid-feedback">
                                    Data wajib diisi dengan format Email.
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Password
                            </label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-key"></i>
                                <input type="password" required class="form-control" name="password" id="password" placeholder="Masukkan password">

                            </div>

                        </div>

                        <!-- Remember -->
                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <div class="form-check">
                                <input class="form-check-input"
                                    type="checkbox"
                                    id="remember">

                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <a href="#">
                                Lupa Password?
                            </a>

                        </div>

                        <!-- Button -->
                        <button type="submit" class="btn btn-primary btn-login w-100">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Login
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="footer-text">
                        Belum punya akun?
                        <a href="#">Daftar Sekarang</a>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap -->
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

    <!-- AdminLTE -->
    <script src="<?= base_url('adminlte/dist/js/adminlte.min.js') ?>"></script>

    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
    <script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>

    <!-- DataTables -->
    <!-- <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script> -->
    <script src="<?= base_url('assets/datatables/jquery.dataTables.min.js') ?>"></script>

    <!-- SweetAlert2 -->
    <script src="<?= base_url('assets/sweetalert2/sweetalert2.all.min.js') ?>"></script>

    <script>
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