$(document).ready(function () {
    setSelections("#filterKategori", BASE_URL + "general/get-kategori", "");

    $('#btnCariFilter').on('click', function () {
        $('#datatable').DataTable().ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $("#tgl_mulai, #tgl_selesai").val(null);
        $('#tgl_selesai').removeAttr('min'); // Bersihkan batasan min
        $('#datatable').DataTable().ajax.reload();
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
            url: "/stok/laporan-stok/getLaporanStoks",
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
            { data: 'created_at' },
            { data: 'nama_lengkap' },
            { data: 'tgl_mulai' },
            { data: 'tgl_selesai' },
            {
                "data": "encrypted_id",
                "render": function (data, type, row) {
                    return `<a class="btn btn-info btn-sm" href="/${row.filename}" download>Download</a>`;
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
                    url: BASE_URL + 'stok/produk/' + userId,
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