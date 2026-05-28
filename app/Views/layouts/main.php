<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(getAppTitle()) ?><?= isset($title) ? ' | ' . esc($title) : '' ?></title>

    <!-- Bootstrap 5 -->
    <link href=" <?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>">

    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery.dataTables.min.css') ?>">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

    <div class="app-wrapper">

        <!-- Sidebar -->
        <?php include('navbar.php'); ?>

        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>


        <!-- Content -->
        <main class="app-main">
            <div class="app-content p-3">

                <?= $this->renderSection('content') ?>

            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer bg-white border-top">

            <!-- Default to the start -->
            <div class="float-end d-none d-sm-inline">
                Version 1.0.0
            </div>

            <!-- To the end -->
            <strong>
                Copyright &copy; <?= date('Y') ?>

                <a href="#" class="text-decoration-none">
                    My Application
                </a>.
            </strong>

            All rights reserved.

        </footer>

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

    <script src="<?= base_url('assets/js/general.js') ?>"></script>

    <script>
        const BASE_URL = "<?= base_url() ?>";

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

        function rupiah(angka) {

            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka);

        }
    </script>

</body>

</html>