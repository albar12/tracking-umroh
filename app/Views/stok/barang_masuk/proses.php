<?= $this->extend('layouts/main'); ?>
<?= $this->section('content'); ?>

<body onload="viewLoad()">
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
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationCustom02" class="form-label">No Dokumen</label>
                                        <input type="text" class="form-control" name="no_dokument" id="no_dokument"
                                            value="<?= $barang_masuk['no_dokument'] ?>" disabled>
                                        <input type="hidden" id="id" value="<?= $id ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationCustom02" class="form-label">Produk <code>*</code></label>
                                        <select class="form-control select select2 produk" id="produk" name="produk" required>
                                            <option value="">--Pilih--</option>
                                            <?php foreach ($produks as $prod): ?>
                                                <option
                                                    value="<?= $prod['produk_id'] ?>"
                                                    <?= ($prod['produk_id'] == $produk) ? 'selected' : '' ?>
                                                    data-jenis="<?= $prod['jenis_sn'] ?>">
                                                    <?= $prod['produk'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Data wajib diisi.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="divSn">
                                    <div class="mb-3">
                                        <label for="validationCustom02" class="form-label">SN</label>
                                        <input type="text" class="form-control" name="sn" id="sn" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="col-md-12">
                                    <div class="d-flex">
                                        <h5 class="font-size-18 mb-1 fw-semibold">Produk :</h5>
                                    </div>
                                    <table width="100%">
                                        <?php foreach ($produks as $produk): ?>
                                            <tr class="border-bottom">
                                                <td width="10%"><i class="bx bx-right-arrow-circle"></i></td>
                                                <td width="70%"><?= esc($produk['produk']) ?></td>
                                                <td width="20%" style="text-align: right;">
                                                    <span class="font-size-14 text-success me-2"><?= esc($produk['qty']) ?></i> /
                                                        <?php
                                                        if ($produk['qty'] == $produk['qty_input']) {
                                                            $css = 'success';
                                                        } else {
                                                            $css = 'danger';
                                                        }
                                                        ?>
                                                        <span class="font-size-14 text-<?= $css; ?> me-2"><?= esc($produk['qty_input']) ?? '0' ?></i></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <div id="dynamic-content"></div>
                            </div>
                        </div>
                        <div class="row mt-4" id="divBatal">
                            <div class="col-sm-12">
                                <a href="<?= base_url('stok/barang_masuk') ?>" class="btn btn-secondary waves-effect">Batal</a>
                                <a href="<?= base_url('stok/hasil_input/' . $id) ?>" class="btn btn-primary" type="submit" style="float: right" id="submit">Selanjutnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="staticBackdropLabel">Kelengkapan</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="sn_modal">
                <input type="hidden" id="produk_id_modal_encrip">
                <input type="hidden" id="produk_id_modal">
                <input type="hidden" id="detail_barang_masuk_id_modal">
                <input type="hidden" id="sp_id_modal">
                <input type="hidden" id="bank_id_modal">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="checkAllKelengkapan">
                            <label class="form-check-label" for="checkAllKelengkapan">
                                Pilih Semua
                            </label>
                        </div>
                        <div id="container-kelengkapan"></div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="kelengkapan" name="kelengkapan[]" value="Tidak">
                            <label class="form-check-label" for="kelengkapan">
                                Tidak ada kelengkapan
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="simpanModal">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script>
    // Saat tombol Batal diklik
    document.querySelector('#staticBackdrop .btn-light').addEventListener('click', function() {
        // Kosongkan nilai SN
        document.getElementById('sn').value = '';

        // Fokus kembali ke input SN setelah modal tertutup
        setTimeout(function() {
            document.getElementById('sn').focus();
        }, 300); // tunggu animasi modal selesai
    });

    function viewLoad() {
        const barang_masuk_id = document.getElementById('id').value;
        const selectedOption = $('#produk').find(':selected');
        let produk = selectedOption.data('jenis');
        if (produk == 'Ya') {
            produk = 'SN';
        } else if (produk == 'Tidak') {
            produk = 'NSN';
        }

        if (barang_masuk_id && produk) {
            view(barang_masuk_id, produk);

            $('#divBatal').hide();
            if (produk == 'SN') {
                $('#divSn').show();
                document.getElementById('sn').readOnly = false;
                setTimeout(function() {
                    $('#sn').focus();
                }, 100);
            } else if (produk == 'NSN') {
                $('#divSn').hide();
                document.getElementById('sn').readOnly = true;
            }
        }
    }

    $(document).ready(function() {
        $('#produk').on('change', function() {
            const barang_masuk_id = document.getElementById('id').value;
            const produk_id = $(this).val();
            const jenis = $('option:selected', this).data('jenis');

            let produk;
            if (jenis === "Ya") {
                produk = "SN";
            } else {
                produk = "NSN";
            }

            if (produk) {
                $('#divBatal').hide();
                view(barang_masuk_id, produk);
            } else {
                $('#divBatal').show();
                $('#dynamic-content').html('');
            }

            if (produk == 'SN') {
                $('#divSn').show();
                document.getElementById('sn').readOnly = false;
                setTimeout(function() {
                    $('#sn').focus();
                }, 100);
            } else if (produk == 'NSN') {
                $('#divSn').hide();
                document.getElementById('sn').readOnly = true;
            }
        });

        $("#sn").change(function() {
            let sn = $(this).val();
            let jumlahKarakter = sn.length;
            const barang_masuk_id = document.getElementById('id').value;
            const produk_id = document.getElementById('produk').value;
            document.getElementById('sn_modal').value = sn;

            $.ajax({
                url: '<?= base_url('stok/val_sn') ?>',
                method: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                    jumlahKarakter: jumlahKarakter,
                    barang_masuk_id: barang_masuk_id,
                    produk_id: produk_id,
                    sn: sn,
                },
                success: function(response) {
                    if (response.status == false) {
                        document.getElementById('sn').value = '';
                        setToast('error', response.message);
                        $('#sn').focus();
                    } else {
                        if (response.count == 0) {
                            document.getElementById('sn').value = '';
                            setToast('error', response.message);
                            $('#sn').focus();
                        } else {
                            const modalElement = document.getElementById('staticBackdrop');
                            const modal = new bootstrap.Modal(modalElement);
                            modal.show();
                            document.getElementById('produk_id_modal').value = response.produk_id;
                            document.getElementById('produk_id_modal_encrip').value = response.produkId;
                            document.getElementById('detail_barang_masuk_id_modal').value = response.detail_barang_masuk_id;
                            document.getElementById('sp_id_modal').value = response.sp_id;
                            document.getElementById('bank_id_modal').value = response.bank_id;

                            renderKelengkapan(response.kelengkapan);
                        }
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseText, 'error');
                }
            });
        });

        $('#simpanModal').on('click', function(e) {
            e.preventDefault(); // Hindari behavior default

            // Cek apakah ada minimal satu checkbox yang dicentang
            const isAnyChecked = $('input[name="kelengkapan[]"]:checked').length > 0;

            if (!isAnyChecked) {
                Swal.fire('Peringatan', 'Pilih kelengkapan produk!', 'warning');
                return; // Hentikan eksekusi jika tidak ada yang dicentang
            }

            const barang_masuk_id = document.getElementById('id').value;
            const detailBarangMasukId = document.getElementById('detail_barang_masuk_id_modal').value;
            const sp_id_modal = document.getElementById('sp_id_modal').value;
            const bank_id_modal = document.getElementById('bank_id_modal').value;
            const produk_id_modal_encrip = document.getElementById('produk_id_modal_encrip').value;
            const produkId = $("#produk_id_modal").val();
            const sn = document.getElementById('sn_modal').value;

            // Ambil semua checkbox yang dicentang
            const kelengkapan = [];
            $('input[name="kelengkapan[]"]:checked').each(function() {
                if ($(this).val() !== 'Tidak') {
                    kelengkapan.push($(this).val());
                }
            });

            // Hitung jumlah yang dicentang dan total checkbox
            const checkedCheckbox = kelengkapan.length;
            const totalCheckbox = $('input[name="kelengkapan[]"]').length;

            // Ubah array jadi string dipisahkan koma
            const kelengkapanStr = kelengkapan.join(',');

            updateSn(barang_masuk_id, detailBarangMasukId, produkId, sn, kelengkapanStr, sp_id_modal, produk_id_modal_encrip, bank_id_modal);
        });

        // Deteksi Enter saat modal aktif
        document.addEventListener('keydown', function(e) {
            const modalEl = document.getElementById('staticBackdrop');
            const modalIsVisible = modalEl.classList.contains('show');

            if (modalIsVisible && e.key === 'Enter') {
                e.preventDefault(); // Hindari submit default jika ada form
                document.getElementById('simpanModal').click(); // Trigger tombol Simpan
            }
        });

        // checklist all
        document.getElementById('checkAllKelengkapan').addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('#container-kelengkapan input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = isChecked);
        });
    });

    function renderKelengkapan(kelengkapan) {
        const container = document.getElementById('container-kelengkapan');
        container.innerHTML = ''; // kosongkan dulu kontainer sebelumnya

        kelengkapan.forEach(item => {
            const kelengkapanId = item.kelengkapan_produk_id;
            const kelengkapanNama = item.produk;

            const div = document.createElement('div');
            div.className = 'form-check mb-3';

            div.innerHTML = `
                                <input class="form-check-input" type="checkbox"
                                    id="kelengkapan_${kelengkapanId}"
                                    name="kelengkapan[]"
                                    value="${kelengkapanId}">
                                <label class="form-check-label" for="kelengkapan_${kelengkapanId}">
                                    ${kelengkapanNama}
                                </label>
                            `;

            container.appendChild(div);
        });
    }

    function updateSn(barang_masuk_id, detailBarangMasukId, produkId, sn, kelengkapanStr, sp_id_modal, produk_id_modal_encrip, bank_id_modal) {
        const selectedOption = $('#produk').find(':selected');
        const jenisValue = selectedOption.data('jenis');
        const no_dokument = document.getElementById('no_dokument').value;
        const button = $('#simpanModal');
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled').text('Menyimpan...');

        $.ajax({
            url: '<?= base_url('stok/input_sn') ?>',
            method: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                barang_masuk_id: barang_masuk_id,
                detailBarangMasukId: detailBarangMasukId,
                no_dokument: no_dokument,
                produkId: produkId,
                sn: sn,
                bankId: bank_id_modal,
                kelengkapan: kelengkapanStr,
                spId: sp_id_modal
            },
            success: function(response) {
                if (response.status) {
                    setToast('success', response.message);

                    const baseUrl = "<?= base_url('stok/input_barang_masuk/') ?>";
                    const barangMasukId = "<?= $id ?>";
                    const targetUrl = baseUrl + barang_masuk_id + "/" + produk_id_modal_encrip;

                    window.location.href = targetUrl;

                } else {
                    setToast('error', response.message);
                    button.removeClass('disabled').text('Simpan');

                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    }

    function view(barang_masuk_id, produk) {
        $.ajax({
            type: "POST",
            url: "<?= base_url('stok/load_view') ?>",
            data: {
                id: barang_masuk_id,
                produk: produk,
            },
            beforeSend: function() {
                $('#dynamic-content').html('<i class="text-info bx bx-hourglass bx-spin me-2"></i> Loading...');
            },
            success: function(response) {
                $('#dynamic-content').html(response);
            },
            error: function(err) {
                console.log(err);
                $('#dynamic-content').html('<p class="text-danger">Gagal memuat data</p>');
            }
        });
    }
</script>
<?= $this->endSection(); ?>