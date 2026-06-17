<nav class="app-header navbar navbar-expand-lg bg-white border-bottom shadow-sm">
    <div class="container-fluid d-flex align-items-center justify-content-between">

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light" data-lte-toggle="sidebar" type="button">
                <i class="fa-solid fa-bars"></i>
            </button>

            <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
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

        preg_match('/(chrome|firefox|avantgo|blackberry|android|blazer|elaine|hiptop|iphone|ipod|kindle|midp|mmp|mobile|o2|opera mini|palm|palm os|pda|plucker|pocket|psp|smartphone|symbian|treo|up.browser|up.link|vodafone|wap|windows ce; iemobile|windows ce; ppc;|windows ce; smartphone;|xiino)/i', $_SERVER['HTTP_USER_AGENT'], $version);
        ?>

        <div class="collapse navbar-collapse order-3 order-lg-2" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-2 mt-lg-0">
                <?php foreach ($menus as $menu): ?>
                    <?php if (in_array($menu['menu_id'], $session_menu)) { ?>
                        <li class="nav-item">
                            <a href="<?= base_url($menu['route_menu']) ?>" class="nav-link px-3 fw-medium text-secondary active">
                                <i class="<?= $menu['icon'] ?> me-1 text-primary"></i>
                                <?= strtoupper($menu['menu']) ?>
                            </a>
                        </li>
                    <?php } ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="order-2 order-lg-3">
            <ul class="navbar-nav flex-row align-items-center gap-3">

                <div class="dropdown d-none d-lg-inline-block ms-1">
                    <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen" id="fullscreen-btn">
                        <i class="fa-solid fa-expand"></i>
                    </button>
                </div>

                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center p-0" data-bs-toggle="dropdown" href="#">
                        <img src="<?= base_url($user_profile['foto_profile'])  ?>" class="rounded-circle border border-2 border-light shadow-sm" width="32" height="32" alt="User">
                        <span class="ms-2 d-none d-md-inline fw-semibold text-dark"><?= session()->get('nama_lengkap') ?></span>
                    </a>

                </li>

                <div>
                    <button class="btn header-item noti-icon waves-effect logout_confirm">
                        <i class="fa-solid fa-arrow-right-from-bracket text-danger"></i>
                    </button>
                </div>

            </ul>
        </div>

    </div>
</nav>

<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script>
    document.querySelector(".logout_confirm").addEventListener("click", function() {
        Swal.fire({
            title: "Anda yakin ingin logout?",
            text: "Anda akan keluar dari sistem.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Logout!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('/logout'); ?>";
            }
        });
    });

    $("#fullscreen-btn").click(function() {

        console.log("test");
        if (!document.fullscreenElement) {

            document.documentElement.requestFullscreen();

        } else {

            if (document.exitFullscreen) {
                document.exitFullscreen();
            }

        }

    });
</script>