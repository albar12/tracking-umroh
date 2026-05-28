$(document).ready(function () {
    setSelections("#filterKategori", BASE_URL + "general/get-kategori", "");

    $('#filterKategori').on('change', function () {
        $('#datatable').DataTable().ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterKategori').val('').trigger('change');
        // $('#datatable').DataTable().ajax.reload();
    });

    $(".kategori_id").change(function () {
        const kategori = $(this).find(':selected').data('name');
        const produk = document.getElementById("produk").value ?? '';

        $('textarea[name="deskripsi_produk"]').val(kategori + ' ' + produk);
    });

    $('#produk').on('input', function () {
        const kategori = $(".kategori_id").find(':selected').data('name');
        const produk = document.getElementById("produk").value;

        $('textarea[name="deskripsi_produk"]').val(kategori + ' ' + produk);
    });

    $("#generate_barcode").click(function () {

        let random = Math.floor(
            100000000000 + Math.random() * 900000000000
        );

        $("#barcode_value").val(random);

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
            url: "/stok/produk/getProduks",
            type: "POST",
            data: function (d) {
                d.filters = {
                    kategori_id: $('#filterKategori').val(),
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
            { data: 'kategori' },
            { data: 'produk' },
            {
                data: 'harga_jual',

                render: function (data, type, row) {

                    return rupiah(data);

                }
            },
            {
                data: 'status',
                render: function (data, type, row) {

                    if (data == 'Aktif') {
                        return `<span class="badge rounded-pill bg-success">${data}</span>`;
                    } else {
                        return `<span class="badge bg-danger">${data}</span>`;
                    }

                }
            },
            {
                "data": "encrypted_id",
                "render": function (data, type, row) {
                    let buttons = `<div class="d-flex gap-3">`;
                    buttons += `<a href="produk/${encodeURIComponent(data)}" class="text-info" title="Lihat Data">
                                            <i class="fa-solid fa-eye font-size-18"></i>
                                        </a>`;
                    buttons += `<a href="produk/${encodeURIComponent(data)}/edit" class="text-success" title="Edit Data">
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
                    url: BASE_URL + 'stok/kategori/' + userId,
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