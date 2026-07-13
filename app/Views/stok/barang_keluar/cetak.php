<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk Belanja</title>
    <link rel="shortcut icon" href="<?= base_url('assets/images/icon_title_app.png') ?>">

    <style>
        /* --- STYLE UNTUK TAMPILAN LAYAR --- */
        body {
            background-color: #f0f2f5;
            font-family: 'Courier New', Courier, monospace;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-family: sans-serif;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-print:hover {
            background-color: #0056b3;
        }

        /* --- KANVAS STRUK OPTIMAL BOLD (VSC 58MM) --- */
        .ticket {
            width: 42mm;
            max-width: 42mm;
            background: white;
            padding: 1mm 1.5mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
            color: #000;
            word-break: keep-all;
            font-weight: bold !important;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .logo-wrapper {
            margin-bottom: 2px;
        }

        /* --- CSS TAMBAHAN UNTUK LOGO GAMBAR --- */
        .logo-img {
            max-width: 25mm;
            /* Batasi lebar logo agar seimbang dan tidak terlalu besar */
            height: auto;
            margin-bottom: 4px;
            /* Membantu memperjelas pixel hitam-putih pada printer thermal */
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }

        .logo-text {
            font-size: 13px;
            font-weight: bold !important;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .info-toko {
            font-size: 9px;
            line-height: 1.2;
            margin-bottom: 4px;
            font-weight: bold !important;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .meta-data {
            font-size: 9px;
            line-height: 1.3;
            font-weight: bold !important;
        }

        /* --- LOGIKA TABEL BARANG --- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
            font-weight: bold !important;
        }

        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 2px 0;
            font-weight: bold !important;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
            font-weight: bold !important;
        }

        .item-name {
            font-weight: bold !important;
            display: block;
            word-wrap: break-word;
        }

        .item-detail {
            font-size: 8.5px;
            color: #000;
            font-weight: bold !important;
        }

        .total-section {
            font-size: 9px;
            line-height: 1.3;
            font-weight: bold !important;
        }

        .promo-box {
            border: 1px dashed #000;
            padding: 3px;
            margin-top: 6px;
            font-size: 8.5px;
            line-height: 1.2;
            background-color: #fff;
            font-weight: bold !important;
        }

        .coupon-code {
            font-size: 10px;
            font-weight: bold !important;
            letter-spacing: 1px;
            margin: 2px 0;
            display: block;
        }

        .footer-thanks {
            font-size: 8.5px;
            margin-top: 6px;
            line-height: 1.2;
            font-weight: bold !important;
        }

        /* ==================================================================
        --- PRINTS OVERRIDE: FINAL PERFECT FIT ANTI-POTONG VSC 58MM --- 
        ================================================================== */
        @media print {
            @page {
                margin: 0 !important;
            }

            body {
                background: none;
                padding: 0 !important;
                margin: 0 !important;
                display: block;
                width: 100% !important;
            }

            .btn-print {
                display: none;
            }

            .ticket {
                width: 42mm !important;
                max-width: 42mm !important;
                margin-left: 4.5mm !important;
                margin-right: 0 !important;
                margin-top: 0 !important;
                margin-bottom: 4mm !important;
                padding-left: 1.5mm !important;
                padding-right: 1mm !important;
                padding-top: 0 !important;
                padding-bottom: 4mm !important;
                box-shadow: none;
                font-weight: bold !important;
                -webkit-text-stroke: 0.15px #000;
            }

            .divider {
                border-top: 1px dashed #000 !important;
            }

            .promo-box {
                border: 1px dashed #000 !important;
            }
        }
    </style>
</head>

<body>

    <button class="btn-print" onclick="window.print()">Cetak Struk Sekarang</button>

    <div class="ticket">

        <div class="text-center logo-wrapper">
            <img src="<?= base_url($toko['logo']) ?>" alt="Logo" class="logo-img">
            <h1 class="logo-text"><?= $toko['nama_toko'] ?></h1>
        </div>
        <div class="text-center info-toko">
            <?= $toko['alamat'] ?><br>
            Telp: <?= $toko['no_telp'] ?><br>
        </div>

        <div class="divider"></div>

        <div class="meta-data">
            <div>No. Nota : <?= $barangKeluar['no_dokument'] ?></div>
            <div>Tanggal : <?= date("d-m-Y H:i", strtotime($barangKeluar['tgl_keluar'] . ' ' . $barangKeluar['jam_keluar']))  ?></div>
            <div>Kasir : <?= $barangKeluar['nama_lengkap'] ?></div>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Produk</th>
                    <th style="width: 20%; text-align: center;">Qty</th>
                    <th style="width: 30%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detailBarangKeluar as $row): ?>
                    <tr>
                        <td>
                            <span class="item-name"><?= $row['produk'] ?></span>
                            <span class="item-detail">@<?= rupiah($row['harga_jual'], false) ?></span>
                        </td>
                        <td class="text-center" style="vertical-align: middle;"><?= $row['qty'] ?></td>
                        <td class="text-right" style="vertical-align: middle;"><?= rupiah($row['total_harga'], false) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="total-section">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; text-align: right;">Subtotal:</td>
                    <td style="width: 40%; text-align: right;"><?= rupiah($barangKeluar['total_harga'], false) ?></td>
                </tr>

                <tr style="font-weight: bold; font-size: 11px;">
                    <td style="text-align: right; padding-top: 5px;">GRAND TOTAL:</td>
                    <td style="text-align: right; padding-top: 5px;"><?= rupiah($barangKeluar['total_harga'], false) ?></td>
                </tr>
                <tr style="font-weight: bold;">
                    <td style="text-align: right; padding-top: 5px;">Bayar (<?= $barangKeluar['metode'] ?>):</td>
                    <td style="text-align: right; padding-top: 5px;"><?= rupiah($barangKeluar['nominal_bayar'], false) ?></td>
                </tr>
                <?php if ($barangKeluar['nominal_kembalian']): ?>
                    <tr style="font-weight: bold;">
                        <td style="text-align: right;">Kembali:</td>
                        <td style="text-align: right;"><?= rupiah($barangKeluar['nominal_kembalian'], false) ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="divider"></div>

        <div class="text-center footer-thanks">
            Terima kasih telah berbelanja!<br>
            Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.<br>
            <br>
            Kritik & Saran: <?= $toko['email'] ?>
        </div>

    </div>

    <script>
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>

</html>