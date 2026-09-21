<nav class="app-header navbar navbar-expand-lg bg-white border-bottom shadow-sm px-3 py-2">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        <!-- Kiri: Tombol Toggle Sidebar & Mobile -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light rounded-pill px-3 shadow-none border text-secondary hover-scale" data-lte-toggle="sidebar" type="button" title="Toggle Sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <button class="btn btn-light rounded-circle d-lg-none shadow-none border text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation" style="width: 38px; height: 38px;">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
        </div>

        <?php
        // Panggil model langsung
        $menuModel = new \App\Models\MenuModel();
        $menus = $menuModel->findAll();
        $akses_menu = session()->get('akses_menu');
        $session_menu = $akses_menu ? explode(',', $akses_menu) : [];

        $db = db_connect();
        $user_profile = $db->table("tbl_m_users")
            ->select("foto_profile")
            ->where("user_id", session()->get('user_id'))
            ->limit("1")
            ->get()
            ->getRowArray();

        // Ambil segmen pertama URL untuk deteksi menu aktif navbar
        helper('uri');
        $current_navbar_seg = uri_segment(1);

        preg_match('/(chrome|firefox|avantgo|blackberry|android|blazer|elaine|hiptop|iphone|ipod|kindle|midp|mmp|mobile|o2|opera mini|palm|palm os|pda|plucker|pocket|psp|smartphone|symbian|treo|up.browser|up.link|vodafone|wap|windows ce; iemobile|windows ce; ppc;|windows ce; smartphone;|xiino)/i', $_SERVER['HTTP_USER_AGENT'], $version);
        ?>

        <!-- Tengah: Menu Navigasi Utama -->
        <!-- <div class="collapse navbar-collapse order-3 order-lg-2 justify-content-center" id="navbarContent">
            <ul class="navbar-nav mb-2 mb-lg-0 mt-2 mt-lg-0 gap-1">
                <?php foreach ($menus as $menu): ?>
                    <?php if (in_array($menu['menu_id'], $session_menu)) { ?>
                        <?php
                        // Bandingkan segmen URL saat ini dengan route_menu database
                        $is_nav_active = ($current_navbar_seg == $menu['aktif_menu']);
                        ?>
                        <li class="nav-item">
                            <a href="<?= base_url($menu['route_menu']) ?>"
                                class="nav-link px-3 py-2 rounded-pill fw-medium transition-all <?= $is_nav_active ? 'active bg-primary text-white shadow-sm' : 'text-secondary hover-bg-light' ?>">
                                <i class="<?= $menu['icon'] ?> me-2 <?= $is_nav_active ? 'text-white' : 'text-primary' ?>"></i>
                                <?= strtoupper($menu['menu']) ?>
                            </a>
                        </li>
                    <?php } ?>
                <?php endforeach; ?>
            </ul>
        </div> -->

        <!-- Kanan: Utility & Profil User -->
        <div class="order-2 order-lg-3">
            <ul class="navbar-nav flex-row align-items-center gap-2">

                <!-- Tombol Fullscreen -->
                <li class="nav-item d-none d-lg-inline-block">
                    <button type="button" class="btn btn-light rounded-circle text-secondary border-0 d-flex align-items-center justify-content-center shadow-none hover-bg-light" id="fullscreen-btn" style="width: 40px; height: 40px;" title="Layar Penuh">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                </li>

                <!-- Dropdown Profil -->
                <li class="nav-item dropdown ms-1">
                    <a class="nav-link d-flex align-items-center gap-2 p-1 pe-2 rounded-pill border bg-light-subtle text-decoration-none dropdown-toggle-custom" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                        <img src="<?= base_url($user_profile['foto_profile']) ?>" class="rounded-circle border border-2 border-white shadow-sm object-fit-cover" width="36" height="36" alt="User">
                        <span class="d-none d-md-inline fw-semibold text-dark fs-7 pe-1"><?= session()->get('nama_lengkap') ?></span>
                    </a>
                </li>

                <!-- Tombol Logout -->
                <li class="nav-item">
                    <button class="btn btn-light rounded-circle text-danger border-0 d-flex align-items-center justify-content-center shadow-none hover-bg-danger-subtle logout_confirm" style="width: 40px; height: 40px;" title="Keluar Sistem">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </li>

            </ul>
        </div>

    </div>
</nav>

<!-- Tambahan Styling CSS agar transisi & hover jauh lebih premium -->
<style>
    .transition-all {
        transition: all 0.2s ease-in-out;
    }

    .hover-bg-light:hover {
        background-color: #f8f9fa !important;
        color: #0d6efd !important;
    }

    .hover-bg-danger-subtle:hover {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }

    .fs-7 {
        font-size: 0.875rem;
    }

    /* Sembunyikan panah bawaan dropdown bawaan bootstrap jika mengganggu */
    .dropdown-toggle-custom::after {
        display: none;
    }
</style>

<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script>
    document.querySelector(".logout_confirm").addEventListener("click", function() {
        Swal.fire({
            title: "Anda yakin ingin logout?",
            text: "Anda akan keluar dari sistem.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Ya, Logout!",
            cancelButtonText: "Batal",
            customClass: {
                popup: 'rounded-4 shadow'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('/logout'); ?>";
            }
        });
    });

    $("#fullscreen-btn").click(function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    });
</script>