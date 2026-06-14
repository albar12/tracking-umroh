<div class="table-scroll-horizontal">
    <table id="produkTable" class="table table-bordered dt-responsive">
        <thead>
            <tr>
                <th width="25%">SN</th>
                <th>Kategori</th>
                <th>Produk</th>
                <th>Kelengkapan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $kurang = 0; ?>
            <?php foreach ($detail_barang_masuk as $key => $value): ?>
                <?php
                $kelengkapan = explode(',', $value['kelengkapan']);
                if (!$value['sn']) {
                    $kurang += 1;
                }
                ?>
                <tr>
                    <td class="<?= empty($value['sn']) ? 'table-danger' : 'table-success' ?>"><?= esc($value['sn']) ?></td>
                    <td><?= esc($value['kategori']) ?></td>
                    <td><?= esc($value['produk']) ?></td>
                    <td><?= getKelengkapan($kelengkapan, $kelengkapanList) ?></td>
                    <td>
                        <?php if ($value['sn']) { ?>
                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                <a href="javascript:void(0);" class="text-danger delete-btn" data-id="<?= esc($value['sn_id']) ?>" data-produk="<?= stringEncryptions('encrypt', $value['produk_id']) ?>" data-detail="<?= esc($value['detail_barang_masuk_id']) ?>">
                                    <i class="mdi mdi-delete font-size-18"></i>
                                </a>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="row mt-4">
    <div class="col-sm-12">
        <a href="<?= base_url('stok/barang_masuk') ?>" class="btn btn-secondary waves-effect">Batal</a>
        <a href="<?= base_url('stok/hasil_input/' . $id) ?>" class="btn btn-primary" type="submit" style="float: right" id="submit">Selanjutnya</a>
    </div>
</div>
<link href="<?= base_url('assets/css/table-scroll-horizontal.css') ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>
    // TODO : tooltip dan assets/libs/bootstrap/js/bootstrap.bundle.min.js wajib ada jika load view
    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const detail = $(this).data('detail');
        const produk = $(this).data('produk');
        const barang_masuk_id = document.getElementById('id').value;

        Swal.fire({
            title: "Yakin ingin menghapus?",
            text: "Data tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('stok/barang_masuk/batal_sn/') ?>' + id,
                    type: "POST",
                    data: {
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                        "id": id,
                        "detail_id": detail,
                        "barang_masuk_id": barang_masuk_id,
                    },
                    success: function(response) {
                        if (response.status) {
                            setToast('success', response.message);

                            const baseUrl = "<?= base_url('stok/input_barang_masuk/') ?>";
                            const barangMasukId = "<?= $id ?>";
                            const targetUrl = baseUrl + barang_masuk_id + "/" + produk;

                            window.location.href = targetUrl;
                        } else {
                            setToast('error', response.message);
                        }
                    },
                    error: function() {
                        setToast('error', 'Terjadi kesalahan saat menghapus data.');
                    }
                });
            }
        });
    });
</script>