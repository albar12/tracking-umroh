<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Verifikasi OTP - Dashboard' ?></title>

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
        }

        .otp-input {
            padding-left: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 8px;
            font-family: Courier, monospace;
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
                    <h1>Verifikasi Keamanan</h1>
                    <p>
                        Kami telah mengirimkan 6 digit kode keamanan OTP ke alamat email Anda.
                        Silakan masukkan kode tersebut untuk memverifikasi kepemilikan akun.
                    </p>
                    <img src="<?= base_url("assets/images/icon_app.png") ?>"
                        alt="Inventory Illustration"
                        class="img-fluid inventory-image">
                </div>

                <div class="col-12 col-md-6 right-side">
                    <div class="mb-4">
                        <h2 class="login-title">Verifikasi OTP</h2>
                        <p class="login-subtitle">
                            Masukkan 6 digit kode yang dikirim ke email Anda
                        </p>
                    </div>

                    <form class="needs-validation" action="<?= base_url('/check-otp'); ?>" method="POST" novalidate>
                        <input type="hidden" id="email" name="email" value="<?= $email ?? '' ?>">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Kode OTP</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-shield-halved"></i>
                                <input type="text"
                                    class="form-control otp-input"
                                    name="otp"
                                    id="otp"
                                    maxlength="6"
                                    placeholder="******"
                                    required
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                <div class="invalid-feedback">
                                    Masukkan 6 digit kode OTP berupa angka.
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-reset w-100 mb-3 text-white">
                            <i class="fa-solid fa-circle-check me-2"></i> Verifikasi Kode
                        </button>

                        <div class="footer-text">
                            Tidak menerima kode?
                            <button type="button" id="resend-otp" class="btn btn-link p-0 m-0 align-baseline fw-bold text-decoration-none" style="color: #2563eb;">
                                Kirim Ulang
                            </button>
                        </div>

                        <div class="footer-text mt-2">
                            Kembali ke <a href="<?= base_url('login'); ?>" class="fw-semibold text-decoration-none">Halaman Login</a>
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

        var countdownTimer;

        // Cek sisa waktu saat halaman selesai dimuat (jika di-refresh)
        $(document).ready(function() {
            var resendOtpTargetTime = localStorage.getItem('resend_otp_target_time');
            var btn = $('#resend-otp');

            if (resendOtpTargetTime) {
                var currentTime = Math.floor(Date.now() / 1000);
                var timeRemaining = resendOtpTargetTime - currentTime;

                if (timeRemaining > 0) {
                    startCountdown(btn, timeRemaining);
                } else {
                    localStorage.removeItem('resend_otp_target_time');
                }
            }
        });

        // Trigger Ajax Kirim Ulang OTP
        $('#resend-otp').on('click', function(e) {
            e.preventDefault();

            var btn = $(this);
            var email = $("#email").val();

            $.ajax({
                url: BASE_URL + 'resend-otp',
                method: 'POST',
                data: {
                    email: email
                },
                beforeSend: function() {
                    btn.prop('disabled', true);
                    btn.html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Mengirim...');

                    Swal.fire({
                        title: 'Mohon Tunggu',
                        html: 'Sedang mengirim ulang kode OTP ke email Anda...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();

                    if (response.status) {
                        setToast('success', response.message);
                        var targetTime = Math.floor(Date.now() / 1000) + 60;
                        localStorage.setItem('resend_otp_target_time', targetTime);
                        startCountdown(btn, 60);
                    } else {
                        setToast('error', response.message);
                        btn.prop('disabled', false);
                        btn.html('Kirim Ulang');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan sistem atau server sedang down.', 'error');
                    btn.prop('disabled', false);
                    btn.html('Kirim Ulang');
                }
            });
        });

        // Fungsi Countdown Timer
        function startCountdown(buttonElement, seconds) {
            clearInterval(countdownTimer);
            var timeRemaining = seconds;

            buttonElement.prop('disabled', true);
            buttonElement.css('color', '#6b7280'); // Mengubah warna teks menjadi abu-abu saat dinonaktifkan
            buttonElement.html('<i class="fa-solid fa-clock me-1"></i> Tunggu ' + timeRemaining + 's');

            countdownTimer = setInterval(function() {
                timeRemaining--;

                if (timeRemaining <= 0) {
                    clearInterval(countdownTimer);
                    localStorage.removeItem('resend_otp_target_time');
                    buttonElement.prop('disabled', false);
                    buttonElement.css('color', '#2563eb'); // Mengembalikan warna teks ke biru
                    buttonElement.html('Kirim Ulang');
                } else {
                    buttonElement.html('<i class="fa-solid fa-clock me-1"></i> Tunggu ' + timeRemaining + 's');
                }
            }, 1000);
        }

        // Bootstrap Native Validation Trigger
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
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