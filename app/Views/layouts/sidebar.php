<style>
    #side_bar {
        background: #1d4ed8;
    }

    /* Memaksa elemen teks dan ikon di dalam <p> memisah ke kiri & kanan */
    .sidebar-menu .nav-link p {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 0;
    }

    /* Animasi putaran panah Font Awesome saat menu terbuka */
    .menu-open>.nav-link .nav-arrow {
        transform: rotate(90deg);
        /* Berputar ke bawah saat menu terbuka */
        transition: transform 0.3s ease;
    }

    .nav-arrow {
        transition: transform 0.3s ease;
    }
</style>

<aside class="app-sidebar shadow" id="side_bar" data-bs-theme="dark">
    <div class="sidebar-brand border-bottom">
        <a href="#" class="brand-link text-decoration-none">
            <img src="<?= base_url('assets/images/icon_title_app.png') ?>"
                alt="Logo"
                class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light">SistemStok</span>
        </a>
    </div>

    <?php
    $uri = service('uri');
    $lastSegment = $uri->getSegment($uri->getTotalSegments());
    helper('uri');
    ?>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column nav-child-indent" data-lte-toggle="treeview" role="menu">

                <?php
                $db = \Config\Database::connect();
                $uri_seg = uri_segment(1);
                $akses_submenu = session()->get('akses_submenu');
                $session_submenu = $akses_submenu ? explode(',', $akses_submenu) : [];
                $permissions = session()->get('permissions');
                $session_permissions = $permissions ? explode(',', $permissions) : [];

                // Ambil induk menu utama
                $builder = $db->table('tbl_m_sub_menu');
                $builder->select('tbl_m_sub_menu.*, tbl_m_menu.flag_route');
                $builder->join('tbl_m_menu', 'tbl_m_menu.menu_id = tbl_m_sub_menu.menu_id');
                $builder->where('tbl_m_sub_menu.display_sub', 'yes');
                $builder->where('tbl_m_menu.flag_route', $uri_seg);
                $builder->orderBy('order_sub', 'ASC');
                $menuList = $builder->get()->getResultArray();
                ?>

                <?php foreach ($menuList as $menu): ?>
                    <?php
                    // Ambil sub menu anak
                    $builder2 = $db->table('tbl_m_permissions');
                    $builder2->select('tbl_m_permissions.*, tbl_m_sub_menu.sub_id, tbl_m_sub_menu.sub');
                    $builder2->join('tbl_m_sub_menu', 'tbl_m_sub_menu.sub_id = tbl_m_permissions.sub_id');
                    $builder2->join('tbl_m_menu', 'tbl_m_menu.menu_id = tbl_m_sub_menu.menu_id');
                    $builder2->where('tipe_menu', 'view');
                    $builder2->where('tbl_m_permissions.display_submenu', 'yes');
                    $builder2->where('tbl_m_sub_menu.sub_id', $menu['sub_id']);
                    $builder2->where('tbl_m_menu.flag_route', $uri_seg);
                    $builder2->orderBy('order_submenu', 'ASC');
                    $permissionList = $builder2->get()->getResultArray();

                    // LOGIKA BARU: Cek apakah induk menu aktif atau ada salah satu anaknya yang aktif
                    $is_parent_active = ($lastSegment == $menu['last_uri']);

                    if (!$is_parent_active && $menu['sub_menu'] == 1) {
                        foreach ($permissionList as $child) {
                            if ($lastSegment == $child['last_uri'] && in_array($child['permissions_id'], $session_permissions)) {
                                $is_parent_active = true;
                                break;
                            }
                        }
                    }
                    ?>

                    <?php if (in_array($menu['sub_id'], $session_submenu)) : ?>

                        <?php if ($menu['sub_menu'] == 1): ?>
                            <li class="nav-item <?= $is_parent_active ? 'menu-open' : '' ?>">
                                <a href="javascript:void(0);" class="nav-link <?= $is_parent_active ? 'active' : '' ?>">
                                    <i class="nav-icon <?= $menu['icon'] ?>"></i>
                                    <p>
                                        <?= $menu['sub'] ?>
                                        <i class="nav-arrow fas fa-angle-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php foreach ($permissionList as $child): ?>
                                        <?php if (in_array($child['permissions_id'], $session_permissions)) : ?>
                                            <?php $is_child_active = ($lastSegment == $child['last_uri']); ?>
                                            <li class="nav-item">
                                                <a href="<?= !empty($child['route_submenu']) ? base_url($child['route_submenu']) : 'javascript:void(0);' ?>"
                                                    class="nav-link <?= $is_child_active ? 'active' : '' ?>">
                                                    <i class="nav-icon bi bi-circle"></i>
                                                    <p><?= $child['submenu'] ?></p>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                        <?php else: ?>
                            <?php foreach ($permissionList as $child): ?>
                                <?php if (in_array($menu['sub_id'], $session_submenu)) : ?>
                                    <?php $is_single_active = ($lastSegment == $child['last_uri']); ?>
                                    <li class="nav-item">
                                        <a href="<?= !empty($child['route_submenu']) ? base_url($child['route_submenu']) : 'javascript:void(0);' ?>"
                                            class="nav-link <?= $is_single_active ? 'active' : '' ?>">
                                            <i class="nav-icon <?= $menu['icon'] ?>"></i>
                                            <p><?= $child['submenu'] ?></p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php endif; ?>
                <?php endforeach; ?>

            </ul>
        </nav>
    </div>
</aside>