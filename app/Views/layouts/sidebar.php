<style>
    #side_bar {
        background: #1d4ed8
    }
</style>

<!-- Sidebar -->
<!-- bg-dark -->
<aside class="app-sidebar  shadow" id="side_bar" data-bs-theme="dark"">
            <div class=" sidebar-brand border-bottom">
    <a href="#" class="brand-link text-decoration-none">

        <img src="<?= base_url('assets/images/icon_title_app.png') ?>"
            alt="Logo"
            class="brand-image opacity-75 shadow">

        <span class="brand-text fw-light">
            SistemStok
        </span>

    </a>
    </div>

    <?php
    $uri = service('uri');

    $lastSegment = $uri->getSegment(
        $uri->getTotalSegments()
    );

    helper('uri');

    ?>

    <div class="sidebar-wrapper">
        <nav class="mt-2">

            <ul class="nav sidebar-menu flex-column nav-child-indent"
                data-lte-toggle="treeview"
                role="menu">

                <?php
                $db = \Config\Database::connect();

                $uri = uri_segment(1);
                $akses_submenu = session()->get('akses_submenu');
                $session_submenu = $akses_submenu ? explode(',', $akses_submenu) : [];
                $permissions = session()->get('permissions');
                $session_permissions = $permissions ? explode(',', $permissions) : [];
                // Ambil sub menu utama (yang tampil sebagai induk menu)
                $builder = $db->table('tbl_m_sub_menu');
                $builder->select('tbl_m_sub_menu.*, tbl_m_menu.flag_route');
                $builder->join('tbl_m_menu', 'tbl_m_menu.menu_id = tbl_m_sub_menu.menu_id');
                $builder->where('tbl_m_sub_menu.display_sub', 'yes');
                $builder->where('tbl_m_menu.flag_route', $uri);
                $builder->orderBy('order_sub', 'ASC');
                $menuList = $builder->get()->getResultArray();
                ?>
                <?php foreach ($menuList as $menu): ?>
                    <?php
                    // Ambil permissions tipe 'view' untuk sub menu yang punya submenu
                    $builder2 = $db->table('tbl_m_permissions');
                    $builder2->select('tbl_m_permissions.*, tbl_m_sub_menu.sub_id, tbl_m_sub_menu.sub');
                    $builder2->join('tbl_m_sub_menu', 'tbl_m_sub_menu.sub_id = tbl_m_permissions.sub_id');
                    $builder2->join('tbl_m_menu', 'tbl_m_menu.menu_id = tbl_m_sub_menu.menu_id');
                    $builder2->where('tipe_menu', 'view');
                    $builder2->where('tbl_m_permissions.display_submenu', 'yes');
                    $builder2->where('tbl_m_sub_menu.sub_id', $menu['sub_id']);
                    $builder2->where('tbl_m_menu.flag_route', $uri);
                    $builder2->orderBy('order_submenu', 'ASC');
                    $permissionList = $builder2->get()->getResultArray();
                    ?>
                    <li class="nav-item">
                        <?php if (in_array($menu['sub_id'], $session_submenu)) : ?>
                            <?php if ($menu['sub_menu'] == 1): ?>
                                <a href="<?= !empty($menu['route_menu']) ? base_url($menu['route_menu']) : 'javascript:void(0);' ?> <?php if ($lastSegment == $menu['last_uri']) {
                                                                                                                                        echo "active";
                                                                                                                                    }  ?>"
                                    class="nav-link has-arrow">
                                    <i class="<?= $menu['icon'] ?>"></i>
                                    <span><?= $menu['sub'] ?></span>
                                </a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <?php foreach ($permissionList as $child): ?>
                                        <?php if (in_array($child['permissions_id'], $session_permissions)) : ?>
                                            <li>
                                                <a href="<?= !empty($child['route_submenu']) ? base_url($child['route_submenu']) : 'javascript:void(0);' ?>">
                                                    <?= $child['submenu'] ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <?php foreach ($permissionList as $child): ?>
                                    <?php if (in_array($menu['sub_id'], $session_submenu)) : ?>
                                        <a href="<?= !empty($child['route_submenu']) ? base_url($child['route_submenu']) : 'javascript:void(0);' ?>" class="nav-link <?php if ($lastSegment == $child['last_uri']) {
                                                                                                                                                                            echo "active";
                                                                                                                                                                        }  ?>">
                                            <i class="<?= $menu['icon'] ?>"></i>
                                            <?= $child['submenu'] ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </li>


                <?php endforeach; ?>
            </ul>

        </nav>
    </div>
</aside>