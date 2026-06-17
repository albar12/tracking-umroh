// Variabel global untuk menyimpan objek instance grafik ApexCharts
let barangKeluarChart = null;
let kategoriChart = null

$(document).ready(function () {

    // 1. Ketika Tanggal Mulai diubah
    $('#filterTglMulai').on('change', function () {
        const tglMulaiVal = $(this).val();

        // Atur atribut 'min' pada Tanggal Selesai agar sama dengan Tanggal Mulai
        $('#filterTglSelesai').attr('min', tglMulaiVal);

        // Validasi Tambahan: Jika Tanggal Selesai sudah terisi dan ternyata lebih kecil dari Tanggal Mulai yang baru dipilih
        const tglSelesaiVal = $('#filterTglSelesai').val();
        if (tglSelesaiVal && tglSelesaiVal < tglMulaiVal) {
            // Reset atau samakan Tanggal Selesai dengan Tanggal Mulai agar tidak konflik
            $('#filterTglSelesai').val(tglMulaiVal);
        }
    });

    // 2. Ketika Tanggal Selesai diubah (Validasi ketat jika user mengetik manual)
    $('#filterTglSelesai').on('change', function () {
        const tglMulaiVal = $('#filterTglMulai').val();
        const tglSelesaiVal = $(this).val();

        if (tglMulaiVal && tglSelesaiVal && tglSelesaiVal < tglMulaiVal) {
            alert('Tanggal Selesai tidak boleh lebih kecil dari Tanggal Mulai!');
            $(this).val(tglMulaiVal); // Kembalikan ke Tanggal Mulai
        }
    });

    // Tombol Cari Filter
    $('#btnCariFilter').on('click', function () {
        dataDashboard();
    });

    // Tombol Reset Filter
    $('#btnResetFilter').on('click', function () {
        $("#filterTglMulai, #filterTglSelesai").val(null);
        $('#filterTglSelesai').removeAttr('min'); // Bersihkan batasan min
        dataDashboard();
    });

    // Eksekusi pertama saat halaman dimuat
    dataDashboard();

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
            url: "/home/getStokData",
            type: "POST",
            data: function (d) {
                // d.filters = {
                //     kategori_id: $('#filterKategori').val(),
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
            { data: 'produk' },
            { data: 'stok_masuk' },
            { data: 'stok_keluar' },
            { data: 'stok_tersedia' },

        ],
        order: [[4, "desc"]],
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100],
        ],
        drawCallback: function (settings) {
        },
    });
});

function dataDashboard() {
    // 1. Ambil nilai dari input HTML
    let tglMulai = $('#filterTglMulai').val();
    let tglSelesai = $('#filterTglSelesai').val();
    let isDefault = false;
    let tahunFilter = ''; // <-- Siapkan variabel kosong untuk menampung tahun

    // 2. Jika input KOSONG (Kondisi pertama kali load atau setelah Reset)
    if (!tglMulai && !tglSelesai) {
        isDefault = true;
        const sekarang = new Date();
        const tahun = sekarang.getFullYear();
        const bulan = String(sekarang.getMonth() + 1).padStart(2, '0'); // Ambil bulan saat ini (01-12)

        // Cari tanggal terakhir di bulan ini
        const tglTerakhir = new Date(tahun, sekarang.getMonth() + 1, 0).getDate();

        // Set default tanggal 1 s/d akhir bulan berjalan ini
        tglMulai = `${tahun}-${bulan}-01`;
        tglSelesai = `${tahun}-${bulan}-${String(tglTerakhir).padStart(2, '0')}`;

        tahunFilter = tahun; // <-- Jika default, otomatis tahun saat ini
    } else {
        // Jika user memilih tanggal sendiri, ambil 4 angka pertama dari string YYYY-MM-DD
        if (tglMulai) {
            tahunFilter = tglMulai.split('-')[0]; // Hasilnya: "2026"
        }
    }

    // 3. Atur teks keterangan Periode di Header halaman
    let teksPeriode = `Periode: ${formatTanggalIndo(tglMulai)} s/d ${formatTanggalIndo(tglSelesai)}`;
    if (isDefault) {
        teksPeriode += " (Bulan Ini)";
    }

    $("#omzet-chart-label").text("Omzet (Periode: " + tahunFilter + ")");
    $("#laris-chart-label").text("Kategori Barang Terlaris" + teksPeriode);

    $.ajax({
        url: BASE_URL + 'home/getDataDashboard',
        type: "POST",
        data: {
            filters: {
                tgl_mulai: $('#filterTglMulai').val(),
                tgl_selesai: $('#filterTglSelesai').val(),
            }
        },
        beforeSend: function () {
            // 1. Loading Efek Spinner untuk Info Box Angka
            $("#barang_masuk_value").html('<div class="spinner-border spinner-border-sm text-light" role="status"></div>');
            $("#barang_keluar_value").html('<div class="spinner-border spinner-border-sm text-light" role="status"></div>');
            $("#omzet_value").html('<div class="spinner-border spinner-border-sm text-light" role="status"></div>');

        },
        success: function (response) {
            // Hilangkan efek overlay loading pada grafik
            $("#chart-loading").remove();

            // Suntik data angka asli ke Info Box
            $("#barang_masuk_value").text(response.total_masuk);
            $("#barang_keluar_value").text(response.total_keluar);
            $("#omzet_value").text(rupiah(response.omzet_value));

            // Render ulang grafik omzet dengan data baru dari backend
            initBarangKeluarChart(response.omzet_data);
            kategoriBarang(response.barang_data.total_keluar, response.barang_data.produk);

        },
        error: function () {
            // Hilangkan efek overlay jika server bermasalah
            $("#chart-loading").remove();
            setToast('error', 'Gagal menampilkan data. Silakan coba beberapa saat lagi.');

            $("#barang_masuk_value").text('0');
            $("#barang_keluar_value").text('0');
            $("#omzet_value").text('0');
        }
    });
}

function formatTanggalIndo(stringTanggal) {
    if (!stringTanggal) return '';
    const bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    const dateParts = stringTanggal.split('-'); // Pecah komponen [YYYY, MM, DD]
    const tahun = dateParts[0];
    const bulan = bulanIndo[parseInt(dateParts[1]) - 1];
    const tanggal = dateParts[2];

    return `${tanggal} ${bulan} ${tahun}`;
}

function initBarangKeluarChart(omzet_data) {
    // Pastikan elemen grafik ada di halaman sebelum inisialisasi
    if ($('#barang-keluar-chart').length === 0) return;

    // Hancurkan (destroy) chart lama jika sudah pernah di-render agar data tidak menumpuk menutupi satu sama lain
    if (barangKeluarChart !== null) {
        barangKeluarChart.destroy();
    }

    const options = {
        series: [{
            name: 'Total Pendapatan (IDR)',
            data: omzet_data
        }],
        chart: {
            height: 280,
            type: 'area',
            toolbar: {
                show: false
            },
            fontFamily: 'Source Sans Pro, sans-serif',
            zoom: {
                enabled: false // Menonaktifkan fitur zoom/scroll area menggunakan mouse drag
            },
            scroller: {
                enabled: false // Mematikan scrollbar internal jika data terlalu padat
            }
        },
        colors: ['#0d6efd'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            labels: {
                style: {
                    colors: '#6c757d'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#6c757d'
                }
            }
        },
        grid: {
            borderColor: '#e9ecef',
            strokeDashArray: 4,
        },
        tooltip: {
            theme: 'light',
            x: {
                show: true
            }
        }
    };

    // Inisialisasi ulang instansi objek chart baru
    barangKeluarChart = new ApexCharts(document.querySelector("#barang-keluar-chart"), options);
    barangKeluarChart.render();
}

function kategoriBarang(barang_data, produk) {
    if ($('#kategori-chart').length === 0) return;

    if (kategoriChart !== null) {
        kategoriChart.destroy();
    }

    // --- TRIK DINAMIS: Generate warna sebanyak jumlah data ---
    const baseColors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d', '#6610f2', '#fd7e14', '#20c997', '#0dcaf0', '#d63384'];
    let dynamicColors = [];

    for (let i = 0; i < barang_data.length; i++) {
        if (i < baseColors.length) {
            // Gunakan warna dasar yang sudah kita siapkan jika index masih cukup
            dynamicColors.push(baseColors[i]);
        } else {
            // Jika data melebihi 10, buat warna HEX acak baru secara otomatis
            const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
            dynamicColors.push(randomColor);
        }
    }

    const kategoriOptions = {
        series: [{
            name: 'Terjual',
            data: barang_data,
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '50%',
                distributed: true // WAJIB TRUE agar warna bisa disebar per batang
            }
        },
        colors: dynamicColors, // PANDUAN: Masukkan array warna dinamis hasil loop di atas
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#fff'],
                fontSize: '12px'
            },
            formatter: function (val) {
                return val + " Pcs";
            },
            offsetY: 10
        },
        xaxis: {
            categories: produk,
            labels: {
                style: {
                    colors: '#6c757d',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah (Pcs)',
                style: { color: '#6c757d' }
            },
            labels: {
                style: { colors: '#6c757d' }
            }
        },
        grid: {
            borderColor: '#e9ecef',
            strokeDashArray: 4
        },
        legend: {
            show: false
        }
    };

    kategoriChart = new ApexCharts(document.querySelector("#kategori-chart"), kategoriOptions);
    kategoriChart.render();
}