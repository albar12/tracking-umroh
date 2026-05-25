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
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

    <div class="app-wrapper">

        <!-- Navbar -->
        <nav class="app-header navbar navbar-expand bg-white border-bottom">

            <div class="container-fluid">

                <!-- Left navbar links -->
                <ul class="navbar-nav">

                    <!-- Sidebar toggle -->
                    <li class="nav-item">
                        <a class="nav-link"
                            data-lte-toggle="sidebar"
                            href="#"
                            role="button">

                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </li>

                    <!-- Menu -->
                    <li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link">
                            Contact
                        </a>
                    </li>

                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ms-auto">

                    <!-- Search -->
                    <li class="nav-item">
                        <a class="nav-link"
                            data-widget="navbar-search"
                            href="#"
                            role="button">

                            <i class="fa-solid fa-magnifying-glass"></i>
                        </a>
                    </li>

                    <!-- Notifications -->
                    <li class="nav-item dropdown">

                        <a class="nav-link"
                            data-bs-toggle="dropdown"
                            href="#">

                            <i class="fa-regular fa-bell"></i>

                            <span class="navbar-badge badge text-bg-warning">
                                3
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">

                            <span class="dropdown-header">
                                3 Notifications
                            </span>

                            <div class="dropdown-divider"></div>

                            <a href="#" class="dropdown-item">
                                <i class="fa-solid fa-envelope me-2"></i>
                                1 new message
                            </a>

                            <div class="dropdown-divider"></div>

                            <a href="#" class="dropdown-item">
                                <i class="fa-solid fa-users me-2"></i>
                                2 friend requests
                            </a>

                        </div>

                    </li>

                    <!-- Fullscreen -->
                    <li class="nav-item">
                        <a class="nav-link"
                            data-lte-toggle="fullscreen"
                            href="#"
                            role="button">

                            <i class="fa-solid fa-maximize"></i>
                        </a>
                    </li>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">

                        <a class="nav-link d-flex align-items-center"
                            data-bs-toggle="dropdown"
                            href="#">

                            <img src="https://i.pravatar.cc/40"
                                class="rounded-circle shadow"
                                width="32"
                                height="32"
                                alt="User">

                            <span class="ms-2 d-none d-md-inline">
                                Admin
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">

                            <a href="#" class="dropdown-item">
                                <i class="fa-solid fa-user me-2"></i>
                                Profile
                            </a>

                            <a href="#" class="dropdown-item">
                                <i class="fa-solid fa-gear me-2"></i>
                                Settings
                            </a>

                            <div class="dropdown-divider"></div>

                            <a href="#" class="dropdown-item text-danger">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>
                                Logout
                            </a>

                        </div>

                    </li>

                </ul>

            </div>

        </nav>

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

</body>

</html>