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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validationCustom02" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" name="role" value="<?= $role['role'] ?>"
                                    autofocus disabled placeholder="Group">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-check-label" for="status">Status</label>
                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                    <input type="hidden" name="status" value="Tidak Aktif">
                                    <input class="form-check-input" type="checkbox" name="status" id="SwitchCheckSizemd" value="Aktif" disabled
                                        <?= (isset($role['status']) && $role['status'] == 'Aktif') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="SwitchCheckSizemd">Aktif</label>
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
                                        <th valign="middle" colspan="2">Menu Akses</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Insert</th>
                                        <th class="text-center">Edit</th>
                                        <th class="text-center">Delete </th>
                                        <th class="text-center">Approve</th>
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
                                                    <input type="checkbox" name="akses_id[]" class="view" disabled
                                                        value="<?php echo $item->views; ?>" <?php $c = explode(',', $role['permissions']); ?>
                                                        <?php if (in_array($item->views, $c)) {
                                                            echo 'checked';
                                                        } ?>>
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($item->inserts)) { ?>
                                                    <input type="checkbox" name="akses_id[]" class="insert" disabled
                                                        value="<?php echo $item->inserts; ?>" <?php $c = explode(',', $role['permissions']); ?>
                                                        <?php if (in_array($item->inserts, $c)) {
                                                            echo 'checked';
                                                        } ?>>
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($item->edits)) { ?>
                                                    <input type="checkbox" name="akses_id[]" class="edit" disabled
                                                        value="<?php echo $item->edits; ?>" <?php $c = explode(',', $role['permissions']); ?>
                                                        <?php if (in_array($item->edits, $c)) {
                                                            echo 'checked';
                                                        } ?>>
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($item->deletes)) { ?>
                                                    <input type="checkbox" name="akses_id[]" class="delete" disabled
                                                        value="<?php echo $item->deletes; ?>" <?php $c = explode(',', $role['permissions']); ?>
                                                        <?php if (in_array($item->deletes, $c)) {
                                                            echo 'checked';
                                                        } ?>>
                                                <?php } else { ?>
                                                    <input type="checkbox" disabled>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($item->approves)) { ?>
                                                    <input type="checkbox" name="akses_id[]" class="approve" disabled
                                                        value="<?php echo $item->approves; ?>" <?php $c = explode(',', $role['permissions']); ?>
                                                        <?php if (in_array($item->approves, $c)) {
                                                            echo 'checked';
                                                        } ?>>
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
                            <a href="<?= base_url('setting/role') ?>" class="btn btn-secondary waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        for (var i = 0; i < checkboxes.length; i++) {
            if (
                checkboxes[i].type == 'checkbox' &&
                !checkboxes[i].disabled &&
                checkboxes[i].id !== 'SwitchCheckSizemd'
            ) {
                checkboxes[i].checked = ele.checked;
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