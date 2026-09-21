<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<style>
    .profile-avatar {
        width: 180px;
        height: 180px;
        object-fit: cover;
        object-position: center;
        border-radius: 50%;
        border: 5px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .avatar-placeholder {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        font-size: 64px;
        font-weight: 700;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .profile-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
    }

    .profile-header {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        padding: 40px 20px;
        color: white;
        text-align: center;
    }

    .info-table th {
        white-space: nowrap;
        width: 35%;
        background-color: #f8fafc;
    }

    .info-table td {
        vertical-align: middle;
    }

    @media (max-width: 768px) {

        .profile-avatar,
        .avatar-placeholder {
            width: 140px;
            height: 140px;
        }

        .avatar-placeholder {
            font-size: 48px;
        }
    }
</style>

<div class="container-fluid">

    <!-- PAGE TITLE -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                <div>
                    <h4 class="mb-1 fw-bold">
                        Data <?= $title ?>
                    </h4>

                    <p class="text-muted mb-0">
                        <?= $sub ?>
                    </p>
                </div>

                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0)">
                                <?= $title ?>
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            <?= $sub ?>
                        </li>
                    </ol>
                </nav>

            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- LEFT -->
        <div class="col-xl-4">

            <div class="card profile-card h-100">

                <!-- HEADER -->
                <div class="profile-header">

                    <?php if (!empty($user['foto_profile'])) : ?>

                        <img src="<?= base_url($user['foto_profile'])  ?>"
                            alt="Profile"
                            class="profile-avatar">

                    <?php else : ?>

                        <div class="avatar-placeholder">
                            <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                        </div>

                    <?php endif; ?>

                    <h4 class="mt-4 mb-1 fw-bold">
                        <?= $user['nama_lengkap'] ?>
                    </h4>

                    <p class="mb-3 opacity-75">
                        <?= $user['role_user'] ?>
                    </p>

                    <?php
                    $status = $user['status'];

                    if ($status === "Aktif") {
                        $flag = "success";
                    } elseif ($status === "Non Aktif") {
                        $flag = "warning";
                    } else {
                        $flag = "secondary";
                    }
                    ?>

                    <span class="badge bg-<?= $flag ?> px-3 py-2 rounded-pill">
                        <?= $status ?>
                    </span>

                </div>

                <!-- BODY -->
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">

                            <tbody>

                                <tr>
                                    <th>Email</th>
                                    <td><?= $user['email'] ?></td>
                                </tr>

                                <tr>
                                    <th>Username</th>
                                    <td><?= $user['username'] ?></td>
                                </tr>

                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td><?= $user['tgl_lahir'] ?></td>
                                </tr>

                                <tr>
                                    <th>No HP</th>
                                    <td><?= $user['no_hp'] ?></td>
                                </tr>

                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-xl-8">

            <div class="card profile-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>
                            <h4 class="card-title mb-1 fw-bold">
                                Detail Informasi
                            </h4>
                        </div>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered align-middle info-table">

                            <tbody>

                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td><?= $user['nama_lengkap'] ?></td>
                                </tr>

                                <tr>
                                    <th>Username</th>
                                    <td><?= $user['username'] ?></td>
                                </tr>

                                <tr>
                                    <th>Email</th>
                                    <td><?= $user['email'] ?></td>
                                </tr>

                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td><?= $user['jenis_kelamin'] ?></td>
                                </tr>

                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td><?= $user['tgl_lahir'] ?></td>
                                </tr>

                                <tr>
                                    <th>Telepon Rumah</th>
                                    <td><?= $user['tlp'] ?></td>
                                </tr>

                                <tr>
                                    <th>No HP</th>
                                    <td><?= $user['no_hp'] ?></td>
                                </tr>

                                <tr>
                                    <th>Alamat</th>
                                    <td><?= $user['alamat'] ?></td>
                                </tr>

                            </tbody>

                        </table>

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