$(document).ready(function () {

    setSelections("#filterRole", BASE_URL + "general/get-role-akses", "");

    $('#filterRole').on('change', function () {
        $('#datatable').DataTable().ajax.reload();
    });

    $('#btnResetFilter').on('click', function () {
        $('#filterRole').val('').trigger('change');
        // $('#datatable').DataTable().ajax.reload();
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
            url: "/setting/status-dokumen/getStatusDokumens",
            type: "POST",
            data: function (d) {
                // d.filters = {
                //     role_id: $('#filterRole').val(),
                // }
            },
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'nama_status' },
            {
                data: 'status',
                render: function (data, type, row) {

                    if (data === 'Aktif') {
                        return `<span class="badge-status badge-status-active">
                                <span class="status-dot"></span> Aktif
                            </span>`;
                    } else {
                        return `<span class="badge-status badge-status-inactive">
                                <span class="status-dot"></span> Non Aktif
                            </span>`;
                    }

                }
            },
            {
                "data": "encrypted_id",
                "render": function (data, type, row) {
                    let buttons = `<div class="d-flex gap-3">`;
                    buttons += `<a href="status-dokumen/${encodeURIComponent(data)}" class="text-info" title="Lihat Data">
                                            <i class="fa-solid fa-eye font-size-18"></i>
                                        </a>`;
                    buttons += `<a href="status-dokumen/${encodeURIComponent(data)}/edit" class="text-success" title="Edit Data">
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
        order: [[2, "desc"]],
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100],
        ],
        drawCallback: function (settings) {
            // var api = this.api();
            // api
            //     .column(3, { search: "applied", order: "applied" })
            //     .nodes()
            //     .each(function (cell, i) {
            //         cell.innerHTML = i + 1 + settings._iDisplayStart;
            //     });
        },
    });

    document.getElementById('status')?.addEventListener('change', function () {
        const label = document.getElementById('statusLabel');
        if (this.checked) {
            label.textContent = 'Aktif';
            label.classList.remove('text-muted');
            label.classList.add('text-success');
        } else {
            label.textContent = 'Tidak Aktif';
            label.classList.remove('text-success');
            label.classList.add('text-muted');
        }
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
            cancelButtonText: "Batal",
            customClass: {
                popup: 'rounded-4 shadow'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + 'setting/status-dokumen/' + userId,
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