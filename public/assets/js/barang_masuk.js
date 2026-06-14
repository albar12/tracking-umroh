$(document).ready(function () {
    setSelections("#filterSupplier", BASE_URL + "general/get-supplier", "");

    $('#filterSupplier').on('change', function () {
        $('#datatable').DataTable().ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterSupplier').val('').trigger('change');
        $('#datatable').DataTable().ajax.reload();
    });

    $(document).on('change', '.kategori_id', function () {
        const kategori_id = $(this).val();
        $(".produk option").remove();
        $('.produk').append(`<option value="">--Pilih Produk--</option>`);
        $('.qty').val('');
        $.ajax({
            url: BASE_URL + "general/get-produk-by-kategori",
            method: 'POST',
            data: {
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                kategori_id: kategori_id,
            },
            success: function (response) {
                $.each(response.items, function (i, item) {
                    $('.produk').append(
                        `<option value="${item.id}" data-produk-expired="${item.produk_expired}">${item.name}</option>`
                    )
                })
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });

    $(document).on('change', '.produk', function () {
        let produk_id = $('select[name="produk"]').val();
        $('.qty').val('');
        $.ajax({
            type: "POST",
            url: BASE_URL + "stok/produk/getStokProduk",
            data: {
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                "produk_id": produk_id,
            },
            success: response => {
                $('#stok').val(response.data.stok);
            },
            error: function (err) {
                Swal.fire('Error', err.responseText, 'error');
            },
        });
    });

    $('#tambah').on('click', function (e) {
        e.preventDefault();

        // Ambil data input
        let kategori_id = $('select[name="produk"]').val();
        let kategori = $('#kategori_id').find(':selected').data('name');
        let produk_id = $('select[name="produk"]').val();
        let produk = $('select[name="produk"] option:selected').text();
        let stok = $('#stok').val();
        let qty = $('#qty').val();
        let ket = $('#ket').val();

        if (!produk_id || !kategori_id || !qty) {
            Swal.fire('Oops', 'Harap lengkapi semua informasi produk yang diperlukan!', 'warning');
            return;
        }

        if (isNaN(qty) || qty <= 0) {
            Swal.fire('Oops', 'Jumlah yang dimasukkan harus berupa angka dan lebih besar dari nol', 'warning');
            return;
        }

        // 🔍 Cek produk sudah ada
        let sudahAda = false;
        $('#produkTable tbody tr').each(function () {
            let existingId = $(this).find('input[name="produk_list[]"]').val();
            if (existingId == produk_id) {
                sudahAda = true;
                return false; // break
            }
        });
        if (sudahAda) {
            Swal.fire('Oops', 'Produk ini sudah ada.', 'warning');
            return;
        }

        // Tambahkan ke tabel
        let row = `
                        <tr>
                            <td><input type="hidden" name="produk_list[]" value="${produk_id}">${produk}</td>
                            <td><input type="hidden" name="kategori_list[]" value="${kategori_id}">${kategori}</td>
                            <td><input type="hidden" name="ket_list[]" value="${ket}">${ket}</td>
                            <td><input type="hidden" name="qty_list[]" value="${qty}">${qty}</td>
                            <td>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus">
                                    <a href="javascript:void(0);" class="text-danger btn-hapus">
                                        <i class="fa-solid fa-trash font-size-18"></i>
                                    </a>
                                </span>
                            </td>
                        </tr>
                    `;
        $('#produkTable tbody').append(row);

        // Kosongkan form setelah tambah
        $('select[name="produk"]').val('').trigger('change');
        $(".produk option").remove();
        $('.produk').append(`<option value="">--Pilih Produk--</option>`)
        $('#stok, #qty, #keterangan').val('');
        $('#kategori_id').val('');

        updateTotal();
    });

    $(document).on('click', '.btn-hapus', function () {
        $(this).closest('tr').remove();
        updateTotal();
    });

    function updateTotal() {
        let total = 0;
        $('input[name="qty_list[]"]').each(function () {
            const val = $(this).val().replace(/[^\d]/g, '');
            total += parseInt(val) || 0;
        });
        $('#total_produk').val(total);
    }

    $('#submit').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        let barangMasuk = [];
        let produkList = [];

        let no_dokument_supplier = $('#no_dokument_supplier').val();
        let tgl_terima = $('#tgl_terima').val();
        let jam_terima = $('#timepicker2').val();
        let diterima = $('#diterima').val();
        let diserahkan = $('#diserahkan').val();
        let keterangan = $('#keterangan').val();
        let total_produk = $('#total_produk').val();

        if (!diterima || !diserahkan || !no_dokument_supplier || !tgl_terima || !jam_terima) {
            Swal.fire('Peringatan', 'Harap isi semua field yang diperlukan.', 'warning');
            button.removeClass('disabled');
            return;
        }

        barangMasuk.push({
            no_dokument_supplier,
            tgl_terima,
            jam_terima,
            diterima,
            diserahkan,
            keterangan,
            total_produk
        });

        $('#produkTable tbody tr').each(function () {
            let row = $(this);
            let produk_id = row.find('input[name="produk_list[]"]').val();
            let kategori_id = row.find('input[name="kategori_list[]"]').val();
            let qty = row.find('input[name="qty_list[]"]').val();
            let ket = row.find('input[name="ket_list[]"]').val();

            produkList.push({
                produk_id,
                kategori_id,
                qty,
                ket,
            });
        });

        if (produkList.length === 0) {
            Swal.fire('Peringatan', 'Harap tambahkan setidaknya satu produk.', 'warning');
            button.removeClass('disabled');
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-masuk',
            method: 'POST',
            data: {
                barangMasuk: barangMasuk[0],
                produkList: produkList
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = BASE_URL + 'stok/barang-masuk';
                    }, 1000);
                } else {
                    setToast('error', response.message);
                    button.removeClass('disabled');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });

    $('#submit_update').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        let barangMasuk = [];
        let produkList = [];

        let barang_masuk_id = $('#barang_masuk_id').val();
        let no_dokument_supplier = $('#no_dokument_supplier').val();
        let tgl_terima = $('#tgl_terima').val();
        let jam_terima = $('#timepicker2').val();
        let diterima = $('#diterima').val();
        let diserahkan = $('#diserahkan').val();
        let keterangan = $('#keterangan').val();
        let total_produk = $('#total_produk').val();

        if (!diterima || !diserahkan || !no_dokument_supplier || !tgl_terima || !jam_terima) {
            Swal.fire('Peringatan', 'Harap isi semua field yang diperlukan.', 'warning');
            button.removeClass('disabled');
            return;
        }

        barangMasuk.push({
            barang_masuk_id,
            no_dokument_supplier,
            tgl_terima,
            jam_terima,
            diterima,
            diserahkan,
            keterangan,
            total_produk
        });

        $('#produkTable tbody tr').each(function () {
            let row = $(this);
            let produk_id = row.find('input[name="produk_list[]"]').val();
            let kategori_id = row.find('input[name="kategori_list[]"]').val();
            let qty = row.find('input[name="qty_list[]"]').val();
            let ket = row.find('input[name="ket_list[]"]').val();

            produkList.push({
                produk_id,
                kategori_id,
                qty,
                ket,
            });
        });

        if (produkList.length === 0) {
            Swal.fire('Peringatan', 'Harap tambahkan setidaknya satu produk.', 'warning');
            button.removeClass('disabled');
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-masuk/' + barang_masuk_id,
            method: 'PUT',
            data: {
                barangMasuk: barangMasuk[0],
                produkList: produkList
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = BASE_URL + 'stok/barang-masuk';
                    }, 1000);
                } else {
                    setToast('error', response.message);
                    button.removeClass('disabled');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });

    $('#tambah_produk').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        // Ambil data input
        let barang_masuk_id = document.getElementById('barang_masuk_id').value;
        let keterangan = $('#ket').val();
        let produk_id = $('select[name="produk"]').val();
        let total = parseInt($('#total_produk').val() || 0);
        let qty = parseInt($('#qty').val() || 0);
        let total_produk = total + qty;

        if (!produk_id || !qty) {
            Swal.fire('Oops', 'Pastikan semua data produk terisi!', 'warning');
            button.removeClass('disabled');
            return;
        }

        if (isNaN(qty) || qty <= 0) {
            Swal.fire('Oops', 'Qty harus angka dan lebih dari 0', 'warning');
            button.removeClass('disabled');
            return;
        }

        // 🔍 Validasi produk_id sudah ada
        let sudahAda = false;
        $('#datatable tbody tr').each(function () {
            let existingId = $(this).find('input[name="produk_list[]"]').val();
            if (existingId == produk_id) {
                sudahAda = true;
                return false; // break loop
            }
        });
        if (sudahAda) {
            Swal.fire('Oops', 'Produk ini sudah ada.', 'warning');
            button.removeClass('disabled');
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-masuk/tambah-produk',
            type: "POST",
            data: {
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                "barang_masuk_id": barang_masuk_id,
                "qty": qty,
                "keterangan": keterangan,
                "produk_id": produk_id,
                "total_produk": total_produk,
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    setToast('error', response.message);
                    button.removeClass('disabled');
                }
            },
            error: function () {
                setToast('error', 'Terjadi kesalahan saat menghapus data.');
            }
        });
    });

    let minQty = 0;
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const qty = $(this).data('qty');
        minQty = $(this).data('qty_input');

        $('#edit-id').val(id);
        $('#edit-qty').val(qty);
        $('#max-warning').hide();
        $('#editQtyModal').modal('show');
    });

    // Validasi saat input
    $('#edit-qty').on('input', function () {
        const val = parseInt($(this).val());
        if (val < minQty) {
            $('#max-warning').show();
        } else {
            $('#max-warning').hide();
        }
    });

    // Submit form
    $('#formEditQty').on('submit', function (e) {
        e.preventDefault();
        const id = $('#edit-id').val();
        const qty = parseInt($('#edit-qty').val());

        if (qty < minQty) {
            $('#max-warning').show();
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-masuk/updateQty',
            method: 'POST',
            data: {
                id: id,
                qty: qty
            },
            success: function (res) {
                if (res.success) {
                    $('#editQtyModal').modal('hide');
                    setToast('success', 'Berhasil menyimpan perubahan');
                    location.reload(); // Atau bisa ganti dengan partial reload
                } else {
                    setToast('error', res.error ?? 'Terjadi kesalahan saat menyimpan.');
                }
            },
            error: function (err) {
                setToast('error', 'Gagal menyimpan perubahan.');
            }
        });
    });

    var table = $("#datatable").DataTable({
        processing: true,
        serverSide: true,
        responsive: {
            details: {
                type: "column",
                target: 0,
            },
        },
        responsive: false,
        scrollX: true,
        columnDefs: [
            {
                targets: 0,
                className: "control",
                orderable: false,
            },
            {
                targets: 2,
                orderable: false,
                searchable: false,
            },
        ],
        ajax: {
            url: "/stok/barang-masuk/getBarangMasuks",
            type: "POST",
            data: function (d) {
                d.filters = {
                    supplier_id: $('#filterSupplier').val(),
                }
            },
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'no_dokument' },
            { data: 'supplier' },
            { data: 'tgl_terima' },
            { data: 'jam_terima' },
            {
                data: 'status_approval',
                render: function (data, type, row) {
                    // Jika type bukan untuk tampilan (seperti sort atau type), langsung kembalikan data mentahnya
                    if (type !== "display" && type !== "filter") {
                        return data;
                    }

                    // Jalankan logika tampilan jika type adalah display atau filter
                    const val = data || '-';
                    let flag = 'success'; // deklarasikan variabel dengan let

                    if (val === 'Proses') {
                        flag = 'info';
                    } else if (val === 'Need Approval') {
                        flag = 'warning';
                    } else if (val === 'UnApprove') {
                        flag = 'danger';
                    }

                    if (row.approval_keterangan) {
                        return `<span data-bs-toggle="tooltip" data-bs-placement="top" title="${row.approval_keterangan}">
                        <span class="badge rounded-pill bg-${flag}">${val}</span>
                    </span>`;
                    } else {
                        return `<span class="badge rounded-pill bg-${flag}">${val}</span>`;
                    }
                }
            },
            {
                "data": "encrypted_id",
                "render": function (data, type, row) {
                    let buttons = `<div class="d-flex gap-3">`;
                    buttons += `<a href="barang-masuk/${encodeURIComponent(data)}" class="text-info" title="Lihat Data">
                                            <i class="fa-solid fa-eye font-size-18"></i>
                                        </a>`;
                    if (row.status_approval === 'Proses' || row.status_approval === 'UnApprove') {
                        buttons += `<span data-bs-toggle="tooltip" data-bs-placement="top" title="Input Produk">
                                                <a href="`+ BASE_URL + `stok/barang-masuk/input-barang-masuk/${encodeURIComponent(data)}" class="text-secondary">
                                                    <i class="fa-solid fa-box-archive"></i>
                                                </a>
                                            </span>`;
                        buttons += `<a href="barang-masuk/${encodeURIComponent(data)}/edit" class="text-success" title="Edit Data">
                                            <i class="fa-solid fa-pencil font-size-18"></i>
                                        </a>`;
                    }

                    if (row.status_approval === 'Need Approval') {
                        buttons += `<span data-bs-toggle="tooltip" data-bs-placement="top" title="Approval">
                                                <a href="`+ BASE_URL + `stok/barang-masuk/approval-barang-masuk/${encodeURIComponent(data)}" class="text-warning">
                                                    <i class="fa-solid fa-check-double font-size-18"></i>
                                                </a>
                                            </span>`;
                    }

                    if (row.status_approval === 'Proses') {
                        buttons += `<a href="javascript:void(0);" class="text-danger delete-btn" title="Delete Data" data-id="${encodeURIComponent(data)}">
                                            <i class="fa-solid fa-trash font-size-18"></i>
                                        </a>`;
                    }

                    buttons += `</div>`;
                    return buttons;
                }
            }

        ],
        order: [[5, "desc"]],
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100],
        ],
        drawCallback: function (settings) {
        },
    });

    $(document).on("click", ".delete-btn", function () {
        let userId = $(this).data("id");

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
                    url: BASE_URL + 'stok/barang-masuk/' + userId,
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function () {
                        setToast('error', 'Gagal menghapus data. Silakan coba beberapa saat lagi.');
                    }
                });
            }
        });
    });

    $(document).on("click", ".delete-produk-btn", function () {
        let detail_id = $(this).data("id");
        let qty = $(this).data("qty");
        let total_produk = $("#total_produk").val();
        let barang_masuk_id = $('#barang_masuk_id').val();

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
                    url: BASE_URL + 'stok/barang-masuk/delete-detail',
                    type: "POST",
                    data: {
                        "detail_id": detail_id,
                        "qty": qty,
                        "total_produk": total_produk,
                        "barang_masuk_id": barang_masuk_id
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function () {
                        setToast('error', 'Gagal menghapus data. Silakan coba beberapa saat lagi.');
                    }
                });
            }
        });
    });


    $("#produk_process").change(function () {
        $('#barcode_value').focus();

    });

    $("#barcode_value").change(function () {
        let barcode_value = $(this).val();
        const barang_masuk_id = document.getElementById('id').value;
        const produk_id = document.getElementById('produk_process').value;
        const detail_barang_masuk_id = $('#produk_process option:selected').data('detail-id');
        const produk_expired = $('#produk_process option:selected').data('produk-expired');

        new bootstrap.Modal(
            document.getElementById('staticBackdrop')
        ).show();

        document.getElementById('barcode_value_modal').value = barcode_value;
        document.getElementById('barang_masuk_id_modal').value = barang_masuk_id;
        document.getElementById('produk_id_modal').value = produk_id;
        document.getElementById('detail_barang_masuk_id_modal').value = detail_barang_masuk_id;
        document.getElementById('produk_expired_modal').value = produk_expired;

    });

    $('#simpanModal').on('click', function (e) {
        e.preventDefault(); // Hindari behavior default

        const produk_expired = $('#produk_expired_modal').val();
        const barcode_value = $('#barcode_value_modal').val();
        const produk_id = $('#produk_id_modal').val();
        const barang_masuk_id = $('#barang_masuk_id_modal').val();
        const detail_barang_masuk_id = $('#detail_barang_masuk_id_modal').val();
        const qty_input = $('#qty_input').val();
        const tgl_expired = $('#tgl_expired').val();
        const button = $('#simpanModal');

        if (!qty_input || qty_input < 1) {
            Swal.fire('Peringatan', 'Qty Masuk tidak dapat kosong!', 'warning');
            return;
        }

        if (produk_expired == "Ya") {
            if (!tgl_expired) {
                Swal.fire('Peringatan', 'Tanggal Expired tidak dapat kosong!', 'warning');
                return;
            }
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-masuk/input-barcode',
            method: 'POST',
            data: {
                barcode_value: barcode_value,
                produk_id: produk_id,
                barang_masuk_id: barang_masuk_id,
                detail_barang_masuk_id: detail_barang_masuk_id,
                qty_input: qty_input,
                tgl_expired: tgl_expired,
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);

                    const baseUrl = BASE_URL + 'stok/barang-masuk/input-barang-masuk/';
                    const barangMasukId = document.getElementById('id').value;
                    // const targetUrl = baseUrl + barang_masuk_id + "/" + produk_id_modal_encrip;
                    const targetUrl = baseUrl;

                    window.location.href = targetUrl + barangMasukId;

                } else {
                    setToast('error', response.message);
                    button.removeClass('disabled').text('Simpan');

                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
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
                setTimeout(function () {
                    $('#sn').focus();
                }, 100);
            } else if (produk == 'NSN') {
                $('#divSn').hide();
                document.getElementById('sn').readOnly = true;
            }
        }
    }

    $(document).on('click', '.delete-btn-barcode', function () {
        const barcode_value_id = $(this).data('id');
        const detail_id = $(this).data('detail-id');
        const qty_input = $(this).data('qty');

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
                    url: BASE_URL + 'stok/barang-masuk/batal-barcode',
                    type: "POST",
                    data: {
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                        "barcode_value_id": barcode_value_id,
                        "detail_id": detail_id,
                        "qty_input": qty_input,
                    },
                    success: function (response) {
                        if (response.status) {
                            setToast('success', response.message);

                            const baseUrl = BASE_URL + 'stok/barang-masuk/input-barang-masuk/';
                            const barangMasukId = document.getElementById('id').value;
                            // const targetUrl = baseUrl + barang_masuk_id + "/" + produk_id_modal_encrip;
                            const targetUrl = baseUrl + barangMasukId;

                            window.location.href = targetUrl;
                        } else {
                            setToast('error', response.message);
                        }
                    },
                    error: function () {
                        setToast('error', 'Terjadi kesalahan saat menghapus data.');
                    }
                });
            }
        });
    });

    // Validasi file gambar saat diubah
    $('#gambar').bind('change', function () {
        var file = document.querySelector("#gambar");
        if (/\.(jpe?g|png|jpg)$/i.test(file.files[0].name) === false) {
            Swal.fire(
                'Gagal',
                'Tipe dokumen yang diperbolehkan jpeg, png, jpg',
                'error'
            ).then(function () { })
            document.getElementById('gambar').value = null;
        } else {
            var size = this.files[0].size / 1000;
            if (size > 2000) {
                Swal.fire(
                    'Gagal',
                    'Maksimal ukuran 2 MB',
                    'error'
                ).then(function () { })
                document.getElementById('gambar').value = null;
            }
        }
    });

    $(document).on('click', '.delete-btn-dokumen', function () {
        const id = $(this).data('id');
        const file = $(this).data('file');
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
                    url: BASE_URL + 'stok/barang-masuk/delete-dokumen/' + id,
                    type: "POST",
                    data: {
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                        "file": file,
                    },
                    success: function (response) {
                        if (response.status) {
                            setToast('success', response.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            setToast('error', response.message);
                        }
                    },
                    error: function () {
                        setToast('error', 'Terjadi kesalahan saat menghapus data.');
                    }
                });
            }
        });
    });

    // Ketika tombol submit diklik
    $('#selesai_barang_masuk').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        let barang_masuk_id = document.getElementById('barang_masuk_id').value;
        Swal.fire({
            title: "Yakin ingin menyelesaikan barang masuk?",
            text: "Data tidak bisa diedit kembali!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, selesai!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + 'stok/barang-masuk/update-barang-masuk',
                    method: 'POST',
                    data: {
                        barang_masuk_id: barang_masuk_id
                    },
                    success: function (response) {
                        if (response.status) {
                            setToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = BASE_URL + 'stok/barang-masuk';
                            }, 1000);
                        } else {
                            setToast('error', response.message);
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseText, 'error');
                    }
                });
            }
            button.removeClass('disabled');
        });
    });


    $('#persetujuan').on('change', function () {
        let persetujuan = $(this).val();
        let approval_keteragan = $('#approval_keteragan');
        let keteranganField = $('#keterangan_approval');

        if (persetujuan === 'Approve') {
            approval_keteragan.addClass('d-none');
            keteranganField.val('');
            keteranganField.prop('required', false);
        } else {
            approval_keteragan.removeClass('d-none');
            keteranganField.prop('required', true);
        }
    });

    const $statusSelect = $('select[name="persetujuan"]');
    const $keteranganField = $('textarea[name="keterangan_approval"]');
    const $keteranganWrapper = $keteranganField.closest('.col-md-3');

    function toggleKeterangan() {
        const selected = $statusSelect.val();
        if (selected === 'UnApprove') {
            $keteranganWrapper.show();
            $keteranganField.prop('required', true);
        } else {
            $keteranganWrapper.hide();
            $keteranganField.prop('required', false).val('');
        }
    }

    // Initial check
    toggleKeterangan();

    // On change
    $statusSelect.on('change', toggleKeterangan);

    $('#selesai-approval').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        let barangMasuk = [];

        let barang_masuk_id = document.getElementById('barang_masuk_id').value;
        let persetujuan = document.getElementById('persetujuan').value;
        let no_dokument = document.getElementById('no_dokument').value;
        let keterangan = document.getElementById('keterangan_approval').value;

        if (!barang_masuk_id || !no_dokument || !persetujuan) {
            Swal.fire('Peringatan', 'Pastikan semua field bertanda * sudah diisi!', 'warning');
            button.removeClass('disabled');
            return;
        }

        if (persetujuan === 'UnApprove' && keterangan === '') {
            Swal.fire('Peringatan', 'Pastikan semua field bertanda * sudah diisi!', 'warning');
            button.removeClass('disabled');
            return;
        }

        Swal.fire({
            title: "Yakin ingin menyelesaikan persetujuan barang masuk?",
            text: "Data tidak bisa diedit kembali!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, simpan!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + 'stok/barang-masuk/update-stok',
                    method: 'POST',
                    data: {
                        barang_masuk_id: barang_masuk_id,
                        no_dokument: no_dokument,
                        keterangan: keterangan,
                        status: persetujuan,
                    },
                    success: function (response) {
                        if (response.status) {
                            setToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = BASE_URL + 'stok/barang-masuk';
                            }, 1000);
                        } else {
                            setToast('error', response.message);
                            button.removeClass('disabled');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseText, 'error');
                    }
                });
            }
            button.removeClass('disabled');
        });
    });

});