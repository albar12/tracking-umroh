<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(getAppTitle()) ?><?= isset($title) ? ' | ' . esc($title) : '' ?></title>

    <link rel="shortcut icon" href="<?= base_url('assets/images/icon_title_app.png') ?>">

    <!-- Bootstrap 5 -->
    <link href=" <?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">


    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>">

    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery.dataTables.min.css') ?>">

    <!-- select2 -->
    <link rel="stylesheet" href="<?= base_url('css/select2.min.css') ?>">

    <!-- datepicker -->
    <!-- <link rel="stylesheet" href="<?= base_url('css/bootstrap-datepicker.min.css') ?>"> -->

    <!-- timepicker -->
    <!-- <link rel="stylesheet" href="<?= base_url('css/bootstrap-timepicker.min.css') ?>"> -->

</head>

<!-- Tambahan Styling Pendukung -->
<style>
    .fs-7 {
        font-size: 0.875rem;
    }

    .fs-8 {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    /* Menyesuaikan tinggi elemen select2 agar selaras dengan input bootstrap modern */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    /* Custom DataTable Premium Styling */
    .table-premium {
        border-collapse: separate;
        border-spacing: 0;
        width: 100% !important;
    }

    .table-premium thead th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    .table-premium tbody td {
        padding: 14px 16px;
        color: #1e293b;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .table-premium tbody tr:hover td {
        background-color: #f8fafc !important;
    }

    /* Badge Status Premium */
    .badge-status {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-status .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .badge-status-active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .badge-status-active .status-dot {
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }

    .badge-status-inactive {
        background-color: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    .badge-status-inactive .status-dot {
        background-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    /* Action Button Styling */
    .btn-action-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 0.875rem;
    }

    .btn-action-view {
        background-color: #f0f9ff;
        color: #0284c7;
    }

    .btn-action-view:hover {
        background-color: #0284c7;
        color: #ffffff;
    }

    .btn-action-edit {
        background-color: #f0fdf4;
        color: #16a34a;
    }

    .btn-action-edit:hover {
        background-color: #16a34a;
        color: #ffffff;
    }

    .btn-action-delete {
        background-color: #fef2f2;
        color: #dc2626;
    }

    .btn-action-delete:hover {
        background-color: #dc2626;
        color: #ffffff;
    }
</style>

<!-- Styling Tambahan Khusus Form -->
<style>
    .fs-7 {
        font-size: 0.875rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.15);
    }

    /* Mempercantik tampilan Select2 agar selaras dengan input form */
    .select2-container .select2-selection--single {
        height: 41px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 41px !important;
        padding-left: 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 39px !important;
    }
</style>

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

    <!-- select2 -->
    <script src="<?= base_url('js/select2.min.js') ?>"></script>

    <!-- datepicker -->
    <!-- <script src="<?= base_url('js/bootstrap-datepicker.min.js') ?>"></script> -->

    <!-- timepicker -->
    <!-- <script src="<?= base_url('js/bootstrap-timepicker.min.js') ?>"></script> -->

    <!-- apexcharts -->
    <script src="<?= base_url('js/apexcharts.js') ?>"></script>


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