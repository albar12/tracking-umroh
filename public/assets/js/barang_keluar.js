$(document).ready(function () {
    setSelections("#kategori_id", BASE_URL + "general/get-kategori", "", false, "Kategori");

    $('#filterSupplier').on('change', function () {
        $('#datatable').DataTable().ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterSupplier').val('').trigger('change');
        $('#datatable').DataTable().ajax.reload();
    });

    $("#metode").change(function () {
        let metode = $(this).val();
        $("#nominal_bayar").val(null);
        $("#nominal_kembalian").val(null);
        if (metode == '1') {
            $("#kembalian").show();
        } else {
            $("#kembalian").hide();
        }
    });

    $("#nominal_bayar").change(function () {
        let nominal_bayar = $(this).val();
        let total_bayar = $("#total_bayar").val();
        let metode = $("#metode").val();

        $(".produk option").remove();
        $(".kategori_id option").remove();
        if (metode) {
            if (metode == 1) {
                if (nominal_bayar < total_bayar) {
                    Swal.fire('Error', "Nominal bayar lebih kecil dari total yang harus dibayar", 'error');
                    $(this).val(null);
                } else {
                    let kembalian = nominal_bayar - total_bayar;
                    $("#nominal_kembalian").val(kembalian);
                }
            } else {
                if (nominal_bayar != total_bayar) {
                    Swal.fire('Error', "Nominal bayar tidak sesuai dengan total yang harus dibayar", 'error');
                    $(this).val(null);
                }
            }
        } else {
            Swal.fire('Error', "Silahkan pilih metode pembayaran terlebih dahulu", 'error');
            $(this).val(null);
            $("#nominal_kembalian").val(null);
        }
    });

    $("#barcode").change(function () {
        let barcode = $(this).val();
        console.log("barcode");
        console.log(barcode);
        $(".produk option").remove();
        $(".kategori_id option").remove();
        if (barcode) {
            $.ajax({
                url: BASE_URL + "general/get-produk-by-barcode",
                method: 'POST',
                data: {
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                    barcode: barcode,
                },
                success: function (response) {
                    $('.produk').append(
                        `<option value="${response.items.produk_id}" data-harga-jual="${response.items.harga_jual}" selected>${response.items.produk}</option>`
                    )

                    $('.kategori_id').append(
                        `<option value="${response.items.kategori_id}" selected>${response.items.kategori}</option>`
                    )

                    $("#stok").val(response.items.stok);
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseText, 'error');
                }
            });
        } else {
            setSelections("#kategori_id", BASE_URL + "general/get-kategori", "", false, "Kategori");
            $('.produk').append(`<option value="">--Pilih Produk--</option>`);
        }
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
                        `<option value="${item.id}" data-harga-jual="${item.harga_jual}" data-produk-expired="${item.produk_expired}">${item.name}</option>`
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
                $('#barcode').val(response.data.barcode_value);
                $('#barcode').att
            },
            error: function (err) {
                Swal.fire('Error', err.responseText, 'error');
            },
        });
    });

    $('#tambah').on('click', function (e) {
        e.preventDefault();

        // Ambil data input
        let barcode = $('#barcode').val();
        let kategori_id = $('select[name="kategori_id"]').val();
        let kategori = $('select[name="kategori_id"] option:selected').text();
        let produk_id = $('select[name="produk"]').val();
        let produk = $('select[name="produk"] option:selected').text();
        let hargaJual = $('select[name="produk"] option:selected').data('harga-jual');
        let stok = $('#stok').val();
        let qty = $('#qty').val();
        let totalHarga = hargaJual * qty;


        if (!produk_id || !kategori_id || !qty) {
            Swal.fire('Oops', 'Harap lengkapi semua informasi produk yang diperlukan!', 'warning');
            return;
        }

        if (isNaN(qty) || qty <= 0) {
            Swal.fire('Oops', 'Jumlah yang dimasukkan harus berupa angka dan lebih besar dari nol', 'warning');
            return;
        }

        if (qty > stok) {
            Swal.fire('Oops', 'Jumlah yang dimasukkan lebih besar dari stok yang tersedia', 'warning');
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
                            <td><input type="hidden" name="barcode_list[]" value="${barcode}">${barcode}</td>
                            <td><input type="hidden" name="produk_list[]" value="${produk_id}">${produk}</td>
                            <td><input type="hidden" name="kategori_list[]" value="${kategori_id}">${kategori}</td>
                            <td><input type="hidden" name="harga_list[]" value="${hargaJual}">${rupiah(hargaJual)}</td>
                            <td><input type="hidden" name="qty_list[]" value="${qty}">${qty}</td>
                            <td><input type="hidden" name="total_harga_list[]" value="${totalHarga}">${rupiah(totalHarga)}</td>
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
        $('#stok, #qty, #barcode').val('');
        $(".kategori_id option").remove();
        $('.kategori_id').append(`<option value="">--Pilih Kategori--</option>`)


        updateTotal();
    });



    $(document).on('click', '.btn-hapus', function () {
        $(this).closest('tr').remove();
        updateTotal();
    });

    function updateTotal() {
        let total = 0;
        $('input[name="total_harga_list[]"]').each(function () {
            const val = $(this).val().replace(/[^\d]/g, '');
            total += parseInt(val) || 0;
        });
        $('#total_bayar').val(total);
        $('#total_bayar_show').val(rupiah(total));
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

        let barangKeluar = [];
        let produkList = [];

        let tgl_keluar = $('#tgl_keluar').val();
        let jam_keluar = $('#timepicker2').val();
        let keterangan = $('#keterangan').val();
        let total_bayar = $('#total_bayar').val();
        let metode_pembayaran = $('#metode').val();
        let nominal_bayar = $('#nominal_bayar').val();
        let nominal_kembalian = $('#nominal_kembalian').val();

        if (!tgl_keluar || !jam_keluar || !metode_pembayaran || !nominal_bayar) {
            Swal.fire('Peringatan', 'Harap isi semua field yang diperlukan.', 'warning');
            button.removeClass('disabled');
            return;
        }

        barangKeluar.push({
            tgl_keluar,
            jam_keluar,
            keterangan,
            total_bayar,
            metode_pembayaran,
            nominal_bayar,
            nominal_kembalian,
        });

        $('#produkTable tbody tr').each(function () {
            let row = $(this);
            let barcode_value = row.find('input[name="barcode_list[]"]').val();
            let produk_id = row.find('input[name="produk_list[]"]').val();
            let kategori_id = row.find('input[name="kategori_list[]"]').val();
            let qty = row.find('input[name="qty_list[]"]').val();
            let harga_jual = row.find('input[name="harga_list[]"]').val();
            let total_harga = row.find('input[name="total_harga_list[]"]').val();

            produkList.push({
                barcode_value,
                produk_id,
                kategori_id,
                qty,
                harga_jual,
                total_harga,
            });
        });

        if (produkList.length === 0) {
            Swal.fire('Peringatan', 'Harap tambahkan setidaknya satu produk.', 'warning');
            button.removeClass('disabled');
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-keluar',
            method: 'POST',
            data: {
                barangKeluar: barangKeluar[0],
                produkList: produkList
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = BASE_URL + 'stok/barang-keluar';
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

        let barangKeluar = [];
        let produkList = [];

        let barang_keluar_id = $('#barang_keluar_id').val();
        let tgl_keluar = $('#tgl_keluar').val();
        let jam_keluar = $('#timepicker2').val();
        let keterangan = $('#keterangan').val();
        let total_bayar = $('#total_bayar').val();
        let metode_pembayaran = $('#metode').val();
        let nominal_bayar = $('#nominal_bayar').val();
        let nominal_kembalian = $('#nominal_kembalian').val();

        if (!tgl_keluar || !jam_keluar || !metode_pembayaran || !nominal_bayar) {
            Swal.fire('Peringatan', 'Harap isi semua field yang diperlukan.', 'warning');
            button.removeClass('disabled');
            return;
        }

        if (metode_pembayaran == 1) {
            if (!nominal_kembalian) {
                Swal.fire('Peringatan', 'Harap isi semua field yang diperlukan.', 'warning');
                button.removeClass('disabled');
                return;
            } else {
                if (nominal_bayar < total_bayar) {
                    Swal.fire('Error', "Nominal bayar lebih kecil dari total yang harus dibayar", 'error');
                    button.removeClass('disabled');
                    return;
                }
            }
        } else {
            if (nominal_bayar != total_bayar) {
                Swal.fire('Error', "Nominal bayar tidak sesuai dengan total yang harus dibayar", 'error');
                button.removeClass('disabled');
                return;
            }
        }

        barangKeluar.push({
            barang_keluar_id,
            tgl_keluar,
            jam_keluar,
            keterangan,
            total_bayar,
            metode_pembayaran,
            nominal_bayar,
            nominal_kembalian,
        });

        $('#produkTable tbody tr').each(function () {
            let row = $(this);
            let produk_id = row.find('input[name="produk_list[]"]').val();

            produkList.push({
                produk_id,
            });
        });

        if (produkList.length === 0) {
            Swal.fire('Peringatan', 'Harap tambahkan setidaknya satu produk.', 'warning');
            button.removeClass('disabled');
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-keluar/' + barang_keluar_id,
            method: 'PUT',
            data: {
                barangKeluar: barangKeluar[0],
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = BASE_URL + 'stok/barang-keluar';
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
        let barang_keluar_id = document.getElementById('barang_keluar_id').value;
        let no_dokument = $('#no_dokument_value').val();
        let barcodeValue = $('#barcode').val();
        let produk_id = $('select[name="produk"]').val();
        let qty = parseInt($('#qty').val() || 0);
        let hargaJual = $('select[name="produk"] option:selected').data('harga-jual');
        let totalHarga = hargaJual * qty;

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
        $('#produkTable tbody tr').each(function () {
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
            url: BASE_URL + 'stok/barang-keluar/tambah-produk',
            type: "POST",
            data: {
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                "barang_keluar_id": barang_keluar_id,
                "no_dokument": no_dokument,
                "qty": qty,
                "harga_jual": hargaJual,
                "barcode_value": barcodeValue,
                "produk_id": produk_id,
                "total_harga": totalHarga,
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
                setToast('error', 'Terjadi kesalahan saat tambah data.');
            }
        });
    });

    let minQty = 0;
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        const qty = $(this).data('qty');
        const harga_jual = $(this).data('harga-jual');
        const total_harga = $("#total_harga").val();

        $('#edit-id').val(id);
        $('#edit-qty').val(qty);
        $('#edit-harga-jual').val(harga_jual);
        $('#edit-total-harga').val(total_harga);
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
        const harga_jual = $('#edit-harga-jual').val();
        const total_harga = $('#edit-total-harga').val();

        if (qty < minQty) {
            $('#max-warning').show();
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/barang-keluar/updateQty',
            method: 'POST',
            data: {
                id: id,
                qty: qty,
                harga_jual: harga_jual,
                total_harga: total_harga,
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
            url: "/stok/barang-keluar/getBarangKeluars",
            type: "POST",
            data: function (d) {
                d.filters = {
                    // supplier_id: $('#filterSupplier').val(),
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
            { data: 'tgl_keluar' },
            { data: 'jam_keluar' },
            {
                data: 'total_harga',
                render: function (data, type, row) {
                    return rupiah(data);
                }
            },
            { data: 'nama_lengkap' },
            {
                data: 'metode',
                render: function (data, type, row) {
                    const val = data || '-';
                    if (val === 'Tunai') {
                        flag = 'success';
                    } else if (val === 'Qris') {
                        flag = 'primary';
                    } else if (val === 'Transfer') {
                        flag = 'secondary';
                    }
                    return `<span class="badge rounded-pill bg-${flag}">${val}</span>`;
                }
            },
            {
                "data": "encrypted_id",
                "render": function (data, type, row) {
                    let buttons = `<div class="d-flex gap-3">`;
                    buttons += `<a href="barang-keluar/${encodeURIComponent(data)}" class="text-info" title="Lihat Data">
                                            <i class="fa-solid fa-eye font-size-18"></i>
                                        </a>`;

                    buttons += `<a href="barang-keluar/cetak-struk/${encodeURIComponent(data)}" class="text-secondary" title="Cetak Struk">
                                            <i class="fa-solid fa-print font-size-18"></i>
                                        </a>`;

                    buttons += `<a href="barang-keluar/${encodeURIComponent(data)}/edit" class="text-success" title="Edit Data">
                                            <i class="fa-solid fa-pencil font-size-18"></i>
                                        </a>`;

                    buttons += `<a href="javascript:void(0);" class="text-danger delete-btn" title="Delete Data" data-id="${encodeURIComponent(data)}">
                                            <i class="fa-solid fa-trash font-size-18"></i>
                                        </a>`;

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
                    url: BASE_URL + 'stok/barang-keluar/' + userId,
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
        let harga_jual = $(this).data("harga-jual");
        let total_harga = $("#total_harga").val();
        let barang_keluar_id = $('#barang_keluar_id').val();

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
                    url: BASE_URL + 'stok/barang-keluar/delete-detail',
                    type: "POST",
                    data: {
                        "detail_id": detail_id,
                        "qty": qty,
                        "harga_jual": harga_jual,
                        "total_harga": total_harga,
                        "barang_keluar_id": barang_keluar_id
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

});