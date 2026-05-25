$(document).ready(function () {

    var table = $("#datatable").DataTable({
        processing: true,
        // serverSide: true,
        // responsive: {
        //   details: {
        //     type: "column",
        //     target: 0,
        //   },
        // },
        responsive: false,
        scrollX: true,
        // columnDefs: [
        //     {
        //         targets: 0,
        //         className: "control",
        //         orderable: false,
        //     },
        //     {
        //         targets: 2,
        //         orderable: false,
        //         searchable: false,
        //     },
        // ],
        // ajax: {
        //     url: "/job/listkirimjo",
        //     type: "POST",
        //     data: function (d) {
        //         d.id_bank = $("#filterBank").val();
        //         d.vendor = $("#filterVendor").val();
        //         d.nama_sp = $("#filterSP").val();
        //         d.nama_job = $("#filterJobID").val();
        //         d.tid = $("#filterTID").val();
        //         d.mid = $("#filterMID").val();
        //         d.merchant = $("#filterMerchant").val();
        //         d.alamat = $("#filterAlamat").val();
        //         d.kota = $("#filterKota").val();
        //     },
        // },
        // columns: [
        //     { data: null },
        //     { data: null },
        //     { data: null },
        //     { data: null },
        //     { data: null },
        //     { data: null },
        //     { data: null },

        // ],
        // order: [[6, "desc"]],
        // lengthMenu: [
        //     [10, 25, 50, 100],
        //     [10, 25, 50, 100],
        // ],
        // drawCallback: function (settings) {
        //     var api = this.api();
        //     api
        //         .column(3, { search: "applied", order: "applied" })
        //         .nodes()
        //         .each(function (cell, i) {
        //             cell.innerHTML = i + 1 + settings._iDisplayStart;
        //         });
        // },
    });
});