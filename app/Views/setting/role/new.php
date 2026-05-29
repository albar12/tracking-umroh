<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Data <?= $title ?></h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?= $title ?></a></li>
                        <li class="breadcrumb-item active"><?= $sub ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form class="needs-validation" action="<?= base_url('setting/role') ?>" method="POST" novalidate>
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="validationCustom02" class="form-label">Role <code>*</code></label>
                                    <input type="text" class="form-control" id="role" name="role"
                                        autofocus required placeholder="Role">
                                    <div class="invalid-feedback">
                                        Data wajib diisi.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th valign="middle" class="text-center">#</th>
                                            <th valign="middle" colspan="2">Menu Akses <input type="checkbox"
                                                    onchange="checkAll(this)" name="chk[]"></th>
                                            <th class="text-center">View <br>
                                                <input type="checkbox" onchange="viewAll(this)" name="chk[]">
                                            </th>
                                            <th class="text-center">Insert <br>
                                                <input type="checkbox" onchange="insertAll(this)" name="chk[]">
                                            </th>
                                            <th class="text-center">Edit <br>
                                                <input type="checkbox" onchange="editAll(this)" name="chk[]">
                                            </th>
                                            <th class="text-center">Delete <br>
                                                <input type="checkbox" onchange="deleteAll(this)" name="chk[]">
                                            </th>
                                            <th class="text-center">Approve <br>
                                                <input type="checkbox" onchange="approveAll(this)" name="chk[]">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($permissions as $item) { ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no++; ?></td>
                                                <td class="text-left"><?php echo $item->menu; ?></td>
                                                <td class="text-left"><?php echo $item->submenu; ?></td>
                                                <td class="text-center">
                                                    <?php if (isset($item->views)) { ?>
                                                        <input type="checkbox" name="akses_id[]" class="view"
                                                            value="<?php echo $item->views; ?>">
                                                    <?php } else { ?>
                                                        <input type="checkbox" disabled>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (isset($item->inserts)) { ?>
                                                        <input type="checkbox" name="akses_id[]" class="insert"
                                                            value="<?php echo $item->inserts; ?>">
                                                    <?php } else { ?>
                                                        <input type="checkbox" disabled>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (isset($item->edits)) { ?>
                                                        <input type="checkbox" name="akses_id[]" class="edit"
                                                            value="<?php echo $item->edits; ?>">
                                                    <?php } else { ?>
                                                        <input type="checkbox" disabled>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (isset($item->deletes)) { ?>
                                                        <input type="checkbox" name="akses_id[]" class="delete"
                                                            value="<?php echo $item->deletes; ?>">
                                                    <?php } else { ?>
                                                        <input type="checkbox" disabled>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (isset($item->approves)) { ?>
                                                        <input type="checkbox" name="akses_id[]" class="approve"
                                                            value="<?php echo $item->approves; ?>">
                                                    <?php } else { ?>
                                                        <input type="checkbox" disabled>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <a href="<?= base_url('setting/role') ?>" class="btn btn-secondary waves-effect">Batal</a>
                                <button class="btn btn-primary" type="submit" style="float: right"
                                    id="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/jquery/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/role.js?v=') . filemtime(FCPATH . 'assets/js/role.js') ?>"></script>
<script type="text/javascript">
    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox' && !(checkboxes[i].disabled)) {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    function viewAll(ele) {
        var checkboxes = document.getElementsByClassName('view');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    function insertAll(ele) {
        var checkboxes = document.getElementsByClassName('insert');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    function editAll(ele) {
        var checkboxes = document.getElementsByClassName('edit');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    function deleteAll(ele) {
        var checkboxes = document.getElementsByClassName('delete');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    function approveAll(ele) {
        var checkboxes = document.getElementsByClassName('approve');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }
</script>
<?= $this->endSection(); ?>