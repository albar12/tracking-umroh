<!-- Sidebar -->
<aside class="app-sidebar bg-dark shadow" data-bs-theme="dark"">
            <div class=" sidebar-brand border-bottom">
    <a href="#" class="brand-link text-decoration-none">

        <img src="<?= base_url('logo.png') ?>"
            alt="Logo"
            class="brand-image opacity-75 shadow">

        <span class="brand-text fw-light">
            CI4 AdminLTE
        </span>

    </a>
    </div>

    <?php
    helper('uri');
    $uri = uri_segment(1);
    ?>

    <div class="sidebar-wrapper">
        <nav class="mt-2">

            <ul class="nav sidebar-menu flex-column nav-child-indent"
                data-lte-toggle="treeview"
                role="menu">

                <li class="nav-item">
                    <a href="/" class="nav-link <?php if ($uri == '') {
                                                    echo "active";
                                                }  ?>">
                        <i class="nav-icon fa-solid fa-house"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/users" class="nav-link <?php if ($uri == 'users') {
                                                            echo "active";
                                                        }  ?> ">
                        <i class="nav-icon fa-solid fa-people"></i>
                        <p>User</p>
                    </a>
                </li>

            </ul>

        </nav>
    </div>
</aside>