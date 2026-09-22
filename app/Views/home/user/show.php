<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>


<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        --surface-glass: rgba(255, 255, 255, 0.85);
        --card-radius: 20px;
    }

    /* Page Header */
    .page-title-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    /* Card Styling */
    .card-premium {
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: var(--card-radius);
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    /* Profile Header & Banner */
    .profile-banner {
        height: 120px;
        background: var(--primary-gradient);
        position: relative;
    }

    .profile-avatar-wrapper {
        margin-top: -60px;
        position: relative;
        display: inline-block;
    }

    .profile-avatar {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        background-color: #fff;
    }

    .avatar-placeholder {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        font-size: 48px;
        font-weight: 700;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.25);
    }

    /* Status Indicator */
    .status-badge {
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.825rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-success {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
    }

    .status-success .status-dot {
        background: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .status-warning {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
    }

    .status-warning .status-dot {
        background: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }

    .status-secondary {
        background: rgba(100, 116, 139, 0.12);
        color: #475569;
    }

    .status-secondary .status-dot {
        background: #64748b;
    }

    /* Info Grid Item */
    .info-card-item {
        padding: 16px;
        border-radius: 14px;
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        transition: transform 0.2s ease, background-color 0.2s ease;
        height: 100%;
    }

    .info-card-item:hover {
        background-color: #f1f5f9;
        transform: translateY(-2px);
    }

    .info-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #ffffff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 12px;
    }

    .info-label {
        font-size: 0.775rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 0.95rem;
        color: #1e293b;
        font-weight: 600;
        word-break: break-word;
    }

    /* Quick List Item */
    .quick-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .quick-info-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .quick-info-item:last-child {
        border-bottom: none;
    }

    .quick-info-item i {
        font-size: 1rem;
        color: #94a3b8;
        width: 28px;
    }
</style>

<div class="container-fluid py-3">

    <!-- PAGE TITLE & BREADCRUMB -->
    <div class="page-title-box mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Data <?= $title ?></h4>
                <p class="text-muted mb-0 small"><?= $sub ?></p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0)" class="text-decoration-none text-primary"><?= $title ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $sub ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">

        <!-- KARTU RINGKASAN PROFIL (KIRI) -->
        <div class="col-xl-4 col-lg-5">
            <div class="card card-premium h-100">

                <!-- Banner Background -->
                <div class="profile-banner"></div>

                <div class="card-body text-center pt-0 pb-4 px-4">
                    <!-- Avatar / Foto Profile -->
                    <div class="profile-avatar-wrapper mb-3">
                        <?php if (!empty($user['foto_profile'])) : ?>
                            <img src="<?= base_url($user['foto_profile']) ?>" alt="Profile" class="profile-avatar">
                        <?php else : ?>
                            <div class="avatar-placeholder">
                                <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nama & Role -->
                    <h5 class="fw-bold text-dark mb-1"><?= $user['nama_lengkap'] ?></h5>
                    <p class="text-muted small mb-3">
                        <span class="badge bg-light text-secondary border px-2 py-1"><?= $user['role_user'] ?></span>
                    </p>

                    <!-- Status Indicator -->
                    <?php
                    $status = $user['status'];
                    if ($status === "Aktif") {
                        $statusClass = "status-success";
                    } elseif ($status === "Non Aktif") {
                        $statusClass = "status-warning";
                    } else {
                        $statusClass = "status-secondary";
                    }
                    ?>
                    <div class="mb-4">
                        <span class="status-badge <?= $statusClass ?>">
                            <span class="status-dot"></span>
                            <?= $status ?>
                        </span>
                    </div>

                    <hr class="my-3 text-muted opacity-25">

                    <!-- Quick Info List (FontAwesome) -->
                    <div class="text-start px-2">
                        <ul class="quick-info-list">
                            <li class="quick-info-item">
                                <i class="fas fa-envelope me-2"></i>
                                <div class="overflow-hidden">
                                    <div class="text-muted small" style="font-size: 0.75rem;">Email</div>
                                    <div class="text-truncate text-dark fw-medium small"><?= $user['email'] ?></div>
                                </div>
                            </li>
                            <li class="quick-info-item">
                                <i class="fas fa-user me-2"></i>
                                <div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">Username</div>
                                    <div class="text-dark fw-medium small"><?= $user['username'] ?></div>
                                </div>
                            </li>
                            <li class="quick-info-item">
                                <i class="fas fa-phone-alt me-2"></i>
                                <div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">No. WhatsApp / HP</div>
                                    <div class="text-dark fw-medium small"><?= $user['no_hp'] ?></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <!-- DETAIL INFORMASI LENGKAP (KANAN) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card card-premium h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Detail Informasi</h5>
                            <p class="text-muted small mb-0">Informasi detail akun pengguna terdaftar</p>
                        </div>
                        <div>
                            <a href="<?= base_url('home/users') ?>" class="btn btn-sm btn-light border me-1">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Info Grid (FontAwesome) -->
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value"><?= $user['nama_lengkap'] ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-at"></i>
                                </div>
                                <div class="info-label">Username</div>
                                <div class="info-value"><?= $user['username'] ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                <div class="info-label">Alamat Email</div>
                                <div class="info-value"><?= $user['email'] ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-venus-mars"></i>
                                </div>
                                <div class="info-label">Jenis Kelamin</div>
                                <div class="info-value"><?= $user['jenis_kelamin'] ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="info-label">Tanggal Lahir</div>
                                <div class="info-value"><?= $user['tgl_lahir'] ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="info-label">Nomor HP / WhatsApp</div>
                                <div class="info-value"><?= $user['no_hp'] ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="info-label">Telepon Rumah</div>
                                <div class="info-value"><?= !empty($user['tlp']) ? $user['tlp'] : '-' ?></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="info-label">Role Akses</div>
                                <div class="info-value"><?= $user['role_user'] ?></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-card-item">
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-label">Alamat Lengkap</div>
                                <div class="info-value"><?= !empty($user['alamat']) ? $user['alamat'] : '-' ?></div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<?= $this->endSection(); ?>