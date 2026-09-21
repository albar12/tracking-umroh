<style>
    /* Gradient Sidebar modern yang mendalam & elegan (Nuansa Hijau Islami Premium) */
    #side_bar {
        background: linear-gradient(180deg, #072E1B 0%, #0D5C32 50%, #124E2E 100%) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* Brand Header Styling */
    .sidebar-brand {
        background-color: rgba(7, 46, 27, 0.85);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(197, 160, 89, 0.2) !important;
        /* Aksen garis emas tipis */
        padding: 1rem 1.25rem;
    }

    .sidebar-brand .brand-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .sidebar-brand .brand-image {
        max-height: 34px;
        width: auto;
        border-radius: 8px;
    }

    .sidebar-brand .brand-text {
        font-weight: 600 !important;
        letter-spacing: 0.5px;
        font-size: 1.1rem;
        color: #f8fafc;
    }

    /* Penataan Menu & Spacing */
    .sidebar-menu {
        padding: 0.75rem 0.6rem;
    }

    .sidebar-menu .nav-item {
        margin-bottom: 0.35rem;
    }

    .sidebar-menu .nav-link {
        border-radius: 10px !important;
        padding: 0.65rem 0.9rem !important;
        color: #cbd5e1 !important;
        transition: all 0.25s ease-in-out;
        font-weight: 500;
    }

    /* Efek Hover Menu Utama & Submenu */
    .sidebar-menu .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
        transform: translateX(3px);
    }

    /* Kondisi Aktif Menu Utama (Parent) & Single Menu (Nuansa Hijau Zamrud & Emas) */
    .sidebar-menu .nav-item>.nav-link.active,
    .sidebar-menu .menu-open>.nav-link {
        background: rgba(197, 160, 89, 0.15) !important;
        /* Sentuhan aksen emas lembut */
        color: #ffffff !important;
        box-shadow: inset 0 0 0 1px rgba(197, 160, 89, 0.4);
    }

    .sidebar-menu .nav-item>.nav-link.active i,
    .sidebar-menu .menu-open>.nav-link i {
        color: #E6C57A !important;
        /* Warna ikon emas elegan */
    }

    /* Styling Anak Menu (Sub-menu treeview) */
    .sidebar-menu .nav-treeview {
        padding-left: 0.5rem;
        margin-top: 0.25rem;
        border-left: 1px dashed rgba(197, 160, 89, 0.25);
        margin-left: 1.2rem;
    }

    .sidebar-menu .nav-treeview .nav-link {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.9rem;
        color: #cbd5e1 !important;
    }

    /* Kondisi Aktif untuk Sub-menu (Child) - Solid Hijau Zamrud ke Emas Tipis */
    .sidebar-menu .nav-treeview .nav-link.active {
        background: linear-gradient(135deg, #0F6B3C 0%, #198754 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 107, 60, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sidebar-menu .nav-treeview .nav-link.active i {
        color: #ffffff !important;
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
        transition: transform 0.3s ease;
    }

    .nav-arrow {
        transition: transform 0.3s ease;
        font-size: 0.75rem;
        opacity: 0.7;
    }
</style>

<aside class="app-sidebar shadow-lg" id="side_bar" data-bs-theme="dark">
    <div class="sidebar-brand border-bottom">
        <a href="#" class="brand-link text-decoration-none">
            <img src="<?= base_url('assets/images/icon_title_app.png') ?>"
                alt="Logo"
                class="brand-image shadow-sm">
            <span class="brand-text">Tracking Umroh</span>
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
                $builder->orderBy('CAST(order_sub AS UNSIGNED)', 'ASC');

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
                    $builder2->orderBy('CAST(order_sub AS UNSIGNED)', 'ASC');
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
                                    <i class="nav-icon <?= $menu['icon'] ?> me-2"></i>
                                    <p>
                                        <span><?= $menu['sub'] ?></span>
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
                                                    <i class="nav-icon bi bi-circle fs-8 me-2"></i>
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
                                            <i class="nav-icon <?= $menu['icon'] ?> me-2"></i>
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