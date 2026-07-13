<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode - <?= $product['produk'] ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/images/icon_title_app.png') ?>">

    <style>
        /* Pengaturan global/layar normal */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }

        /* Container utama pembungkus semua label */
        .grid-container {
            display: grid;
            /* Membuat kolom otomatis selebar 40mm (3x4cm -> lebar 40mm, tinggi 30mm) */
            grid-template-columns: repeat(auto-fill, 40mm);
            gap: 5mm;
            /* Jarak antar label saat dilihat di layar (bisa disesuaikan) */
            justify-content: center;
        }

        /* Desain per-kotak label produk */
        .label-container {
            background: #fff;
            padding: 5px;
            border: 1px dashed #ccc;
            width: 40mm;
            /* Lebar 4 cm */
            height: 30mm;
            /* Tinggi 3 cm */
            text-align: center;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .product-name {
            font-size: 8px;
            /* Diperkecil sedikit agar muat di ukuran 40mm */
            font-weight: bold;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .barcode-img {
            max-width: 100%;
            height: 12mm;
            /* Mengatur tinggi barcode agar proporsional */
            object-fit: contain;
        }

        .product-code {
            font-size: 8px;
            letter-spacing: 1px;
            margin-top: 1px;
        }

        .product-price {
            font-size: 8px;
            font-weight: bold;
            margin-top: 1px;
        }

        .btn-print {
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* Pengaturan KHUSUS saat dicetak/print */
        @media print {
            body {
                margin: 0;
                background: none;
                /* Mengaktifkan sistem display table untuk penataan vertikal tengah */
                display: table;
                width: 100%;
                height: 100vh;
            }

            .btn-print {
                display: none;
                /* Sembunyikan tombol saat cetak */
            }

            .grid-container {
                display: grid;
                /* Tetap membatasi kolom otomatis selebar 40mm */
                grid-template-columns: repeat(auto-fill, 40mm);
                gap: 0;
                /* Mengubah posisi dari start menjadi center agar rata tengah secara horizontal */
                justify-content: center;
                align-content: center;
                width: 100%;
                margin: auto;
            }

            .label-container {
                border: none;
                /* Hilangkan garis putus-putus saat dicetak asli */
                width: 40mm;
                height: 30mm;
                page-break-inside: avoid;
                /* Mencegah label terpotong antar halaman */
            }

            /* Pengaturan halaman cetak (A4 atau Roll thermal tergantung jenis printer) */
            @page {
                size: auto;
                /* Jika menggunakan kertas A4 lembaran stiker */
                /* Jika menggunakan printer thermal roll ukuran pas, ganti menjadi: size: 40mm 30mm; */
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <button class="btn-print" onclick="window.print()">Cetak Label</button>

    <div class="grid-container">

        <?php
        // Default ke 12 jika variabel $jumlah_cetak tidak dikirim dari controller
        $total = isset($jumlah_cetak) ? $jumlah_cetak : 12;

        for ($i = 0; $i < $total; $i++):
        ?>
            <div class="label-container">
                <div class="product-name"><?= $product['produk'] ?></div>
                <img class="barcode-img" src="<?= $barcode ?>" alt="Barcode">
                <div class="product-code"><?= $product['barcode_value'] ?></div>
                <div class="product-price"><?= rupiah($product['harga_jual']) ?></div>
            </div>
        <?php endfor; ?>

    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>