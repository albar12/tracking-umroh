$(document).ready(function () {
    $('#filterKategori').on('change', function () {
        $('#datatable').DataTable().ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterKategori').val('').trigger('change');
        // $('#datatable').DataTable().ajax.reload();
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

    $("#barcode_value").change(function () {
        let barcode_value = $(this).val();
        let produk_id = $('#produk_process').val();
        const so_id = document.getElementById('id').value;
        const detail_so_id = $('#produk_process option:selected').data('detail-id');

        if (!produk_id) {
            Swal.fire('Oops', 'Harap lengkapi semua informasi produk yang diperlukan!', 'warning');
            return;
        }

        new bootstrap.Modal(
            document.getElementById('staticBackdrop')
        ).show();

        document.getElementById('so_id_modal').value = so_id;
        document.getElementById('detail_so_id_modal').value = detail_so_id;
        document.getElementById('produk_id_modal').value = produk_id;
        document.getElementById('barcode_value_modal').value = barcode_value;


    });

    $('#simpanModal').on('click', function (e) {
        e.preventDefault(); // Hindari behavior default

        const produk_id = $('#produk_id_modal').val();
        const so_id = $('#so_id_modal').val();
        const detail_so_id = $('#detail_so_id_modal').val();
        const qty = $('#qty').val();
        const barcode_value = $('#barcode_value_modal').val();
        const button = $('#simpanModal');

        if (!qty || qty < 1) {
            Swal.fire('Peringatan', 'Qty Masuk tidak dapat kosong!', 'warning');
            return;
        }


        // --- PROSES VALIDASI PRODUK GANDA (DUPLIKAT) ---
        // Jika detail_so_id kosong (artinya ini adalah TAMBAH DATA BARU, bukan EDIT)
        let produkSudahAda = false;

        // Loop setiap input hidden produk_list[] di dalam tabel
        $('input[name="produk_list[]"]').each(function () {
            if ($(this).val() == produk_id) {
                produkSudahAda = true;
                return false; // Berhenti dari loop .each() jika ketemu
            }
        });

        // Jika produk ditemukan, batalkan submit dan beri peringatan
        if (produkSudahAda) {
            Swal.fire('Oops', 'Produk ini sudah ada.', 'warning');
            return; // Hentikan fungsi klik, AJAX tidak akan berjalan
        }
        // ------------------------------------------------

        $.ajax({
            url: BASE_URL + 'stok/stok-opname/input-so',
            method: 'POST',
            data: {
                produk_id: produk_id,
                so_id: so_id,
                detail_so_id: detail_so_id,
                qty: qty,
                barcode_value: barcode_value
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);

                    const baseUrl = BASE_URL + 'stok/stok-opname/input-stok-opname/';
                    const so_id = document.getElementById('id').value;
                    // const targetUrl = baseUrl + barang_masuk_id + "/" + produk_id_modal_encrip;
                    const targetUrl = baseUrl;

                    window.location.href = targetUrl + so_id;

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

    $(document).on('click', '.delete-btn-so', function () {
        const detail_so_id = $(this).data('id');

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
                    url: BASE_URL + 'stok/stok-opname/batal-so',
                    type: "POST",
                    data: {
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                        "detail_so_id": detail_so_id,
                    },
                    success: function (response) {
                        if (response.status) {
                            setToast('success', response.message);

                            const baseUrl = BASE_URL + 'stok/stok-opname/input-stok-opname/';
                            const so_id = document.getElementById('id').value;
                            // const targetUrl = baseUrl + barang_masuk_id + "/" + produk_id_modal_encrip;
                            const targetUrl = baseUrl + so_id;

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

    $('#syncron_stok_opname').on('click', function (e) {
        e.preventDefault();

        let button = $(this);
        if (button.hasClass('disabled')) {
            return false;
        }
        button.addClass('disabled');

        let so_id = document.getElementById('id').value;

        Swal.fire({
            title: "Yakin ingin proses syncron stok opname?",
            text: "Data tidak bisa diubah kembali!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, selesai!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {

                // === 1. TAMPILKAN LOADING BAR / ANIMASI LOADING DI SINI ===
                Swal.fire({
                    title: 'Mohon Tunggu',
                    html: 'Sedang menyinkronkan stok...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading(); // Menampilkan spinner loading bawaan SweetAlert
                    }
                });

                $.ajax({
                    url: BASE_URL + 'stok/stok-opname/syncron-stok-opname',
                    method: 'POST',
                    data: {
                        so_id: so_id
                    },
                    success: function (response) {
                        // === 2. LOADING AKAN OTOMATIS TERTUTUP SAAT Swal.fire BARU BERJALAN ===
                        if (response.status) {
                            setToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = BASE_URL + 'stok/stok-opname';
                            }, 1000);
                        } else {
                            // Tutup loading dan tampilkan error jika status gagal
                            Swal.fire('Gagal', response.message, 'error');
                            button.removeClass('disabled'); // Aktifkan tombol lagi
                        }
                    },
                    error: function (xhr) {
                        // Tutup loading dan tampilkan error jika AJAX bermasalah
                        Swal.fire('Error', xhr.responseText, 'error');
                        button.removeClass('disabled'); // Aktifkan tombol lagi
                    }
                });
            } else {
                // Jika user memilih 'Batal', langsung kembalikan tombol jadi aktif
                button.removeClass('disabled');
            }
        });
    });

    $('#tambah').on('click', function (e) {
        e.preventDefault();

        // Ambil data input
        let kategori_id = $('select[name="produk"]').val();
        let kategori = $('#kategori_id').find(':selected').data('name');
        let produk_id = $('select[name="produk"]').val();
        let produk = $('select[name="produk"] option:selected').text();

        if (!produk_id || !kategori_id) {
            Swal.fire('Oops', 'Harap lengkapi semua informasi produk yang diperlukan!', 'warning');
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
        $('#kategori_id').val('');

    });

    $(document).on('click', '.btn-hapus', function () {
        $(this).closest('tr').remove();
    });

    $('#submit').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        let stokOpname = [];
        let produkList = [];

        let batch = $('#batch').val();
        let tgl_mulai = $('#tgl_mulai').val();
        let tgl_selesai = $('#tgl_selesai').val();
        let keterangan = $('#keterangan').val();

        if (!batch || !tgl_mulai || !tgl_selesai || !keterangan) {
            Swal.fire('Peringatan', 'Harap isi semua field yang diperlukan.', 'warning');
            button.removeClass('disabled');
            return;
        }

        stokOpname.push({
            batch,
            tgl_mulai,
            tgl_selesai,
            keterangan,
        });

        $('#produkTable tbody tr').each(function () {
            let row = $(this);
            let produk_id = row.find('input[name="produk_list[]"]').val();
            let kategori_id = row.find('input[name="kategori_list[]"]').val();

            produkList.push({
                produk_id,
                kategori_id,
            });
        });

        if (produkList.length === 0) {
            Swal.fire('Peringatan', 'Harap tambahkan setidaknya satu produk.', 'warning');
            button.removeClass('disabled');
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/stok-opname',
            method: 'POST',
            data: {
                stokOpname: stokOpname[0],
                produkList: produkList
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = BASE_URL + 'stok/stok-opname';
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
        let so_id = document.getElementById('so_id').value;
        let produk_id = $('select[name="produk"]').val();

        if (!produk_id) {
            Swal.fire('Oops', 'Pastikan semua data produk terisi!', 'warning');
            button.removeClass('disabled');
            return;
        }

        // 🔍 Validasi produk_id sudah ada
        let sudahAda = false;
        $('#produkTable tbody tr').each(function () {
            let existingId = $(this).find('input[name="produk_list[]"]').val();
            console.log(existingId);
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
            url: BASE_URL + 'stok/stok-opname/tambah-produk',
            type: "POST",
            data: {
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>",
                "so_id": so_id,
                "produk_id": produk_id,
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

    $(document).on("click", ".delete-produk-btn", function () {
        let detail_id = $(this).data("id");

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
                    url: BASE_URL + 'stok/stok-opname/delete-detail',
                    type: "POST",
                    data: {
                        "detail_id": detail_id,
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

    $('#submit_update').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        let stokOpname = [];
        let produkList = [];

        let so_id = $('#so_id').val();
        let batch = $('#batch').val();
        let batchOld = $('#batchOld').val();
        let tgl_mulai = $('#tgl_mulai').val();
        let tgl_selesai = $('#tgl_selesai').val();
        let keterangan = $('#keterangan').val();

        if (!batch || !tgl_mulai || !tgl_selesai) {
            Swal.fire('Peringatan', 'Harap isi semua field yang diperlukan.', 'warning');
            button.removeClass('disabled');
            return;
        }

        stokOpname.push({
            so_id,
            batch,
            batchOld,
            tgl_mulai,
            tgl_selesai,
            keterangan,
        });

        $('#produkTable tbody tr').each(function () {
            let row = $(this);
            let produk_id = row.find('input[name="produk_list[]"]').val();
            let kategori_id = row.find('input[name="kategori_list[]"]').val();

            produkList.push({
                produk_id,
                kategori_id,
            });
        });

        if (produkList.length === 0) {
            Swal.fire('Peringatan', 'Harap tambahkan setidaknya satu produk.', 'warning');
            button.removeClass('disabled');
            return;
        }

        $.ajax({
            url: BASE_URL + 'stok/stok-opname/' + so_id,
            method: 'PUT',
            data: {
                stokOpname: stokOpname[0],
                produkList: produkList
            },
            success: function (response) {
                if (response.status) {
                    setToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = BASE_URL + 'stok/stok-opname';
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

    $('#selesai-approval').on('click', function (e) {
        e.preventDefault();
        // Cek apakah tombol sudah disabled
        let button = $(this);
        if (button.hasClass('disabled')) {
            // Stop eksekusi jika tombol sudah diklik
            return false;
        }
        button.addClass('disabled');

        let stokOpname = [];

        let so_id = document.getElementById('id').value;
        let persetujuan = document.getElementById('persetujuan').value;
        let no_dokument = document.getElementById('no_dokument').value;
        let keterangan = document.getElementById('keterangan_approval').value;

        if (!so_id || !no_dokument || !persetujuan) {
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
            title: "Yakin ingin menyelesaikan persetujuan stok opname?",
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
                    url: BASE_URL + 'stok/stok-opname/update-so',
                    method: 'POST',
                    data: {
                        so_id: so_id,
                        no_dokument: no_dokument,
                        keterangan: keterangan,
                        status: persetujuan,
                    },
                    success: function (response) {
                        if (response.status) {
                            setToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = BASE_URL + 'stok/stok-opname';
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

    var table_produk = $("#produkTable").DataTable();

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
            url: "/stok/stok-opname/getStokOpnames",
            type: "POST",
            data: function (d) {
                d.filters = {
                    tgl_mulai: $('#tgl_mulai').val(),
                    tgl_selesai: $('#tgl_selesai').val(),
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
            { data: 'batch' },
            { data: 'tgl_mulai' },
            { data: 'tgl_selesai' },
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
                    buttons += `<a href="stok-opname/${encodeURIComponent(data)}" class="text-info" title="Lihat Data">
                                            <i class="fa-solid fa-eye font-size-18"></i>
                                        </a>`;
                    if (row.status_approval === 'Proses' || row.status_approval === 'UnApprove') {
                        buttons += `<span data-bs-toggle="tooltip" data-bs-placement="top" title="Input SO">
                                                <a href="stok-opname/input-stok-opname/${encodeURIComponent(data)}" class="text-secondary">
                                                    <i class="fa-solid fa-box-archive"></i>
                                                </a>
                                            </span>`;
                        buttons += `<a href="stok-opname/${encodeURIComponent(data)}/edit" class="text-success" title="Edit Data">
                                            <i class="fa-solid fa-pencil font-size-18"></i>
                                        </a>`;
                    }

                    if (row.status_approval === 'Need Approval') {
                        buttons += `<span data-bs-toggle="tooltip" data-bs-placement="top" title="Approval">
                                                <a href="stok-opname/approval-stok-opname/${encodeURIComponent(data)}" class="text-warning">
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
        order: [[4, "desc"]],
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
                    url: BASE_URL + 'stok/stok-opname/' + userId,
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
});