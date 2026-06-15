<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk Belanja</title>
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
            font-size: 16px;
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

        /* --- KANVAS STRUK (Ukuran standar 80mm) --- */
        .ticket {
            width: 75mm;
            max-width: 75mm;
            background: white;
            padding: 4mm;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .logo-wrapper {
            margin-bottom: 5px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0;
        }

        .info-toko {
            font-size: 12px;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .meta-data {
            font-size: 11px;
            line-height: 1.4;
        }

        /* --- LOGIKA TABEL BARANG --- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
        }

        td {
            padding: 4px 0;
            vertical-align: top;
        }

        .item-name {
            font-weight: bold;
            display: block;
        }

        .item-detail {
            font-size: 11px;
            color: #333;
        }

        .total-section {
            font-size: 12px;
            line-height: 1.5;
        }

        /* --- MARKETING/PROMO SECTION --- */
        .promo-box {
            border: 1px dashed #000;
            padding: 8px;
            margin-top: 15px;
            font-size: 11px;
            line-height: 1.4;
            background-color: #fff;
        }

        .coupon-code {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 5px 0;
            display: block;
        }

        .footer-thanks {
            font-size: 11px;
            margin-top: 15px;
            line-height: 1.4;
        }

        /* --- STYLE KHUSUS SAAT DICETAK (PRINT) --- */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
                display: block;
            }

            .btn-print {
                display: none;
                /* Sembunyikan tombol cetak */
            }

            .ticket {
                width: 100%;
                max-width: 100%;
                box-shadow: none;
                padding: 0;
            }

            @page {
                margin: 0;
                /* Menghilangkan header/footer bawaan browser Chrome */
            }
        }
    </style>
</head>

<body>

    <button class="btn-print" onclick="window.print()">Cetak Struk Sekarang</button>

    <div class="ticket">

        <div class="text-center logo-wrapper">
            <h1 class="logo-text">TOKOKU MART</h1>
        </div>
        <div class="text-center info-toko">
            Jl. Malioboro No. 45, Yogyakarta<br>
            Telp: 0812-3456-7890<br>
            Instagram: @tokoku.mart
        </div>

        <div class="divider"></div>

        <div class="meta-data">
            <div>No. Nota : #K2606150000001</div>
            <div>Tanggal : 15-06-2026 18:21</div>
            <div>Kasir : Admin (Budi)</div>
            <div>Pelanggan: Umum</div>
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
                <tr>
                    <td>
                        <span class="item-name">Kopi Susu Gula Aren</span>
                        <span class="item-detail">@18.000</span>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">2</td>
                    <td class="text-right" style="vertical-align: middle;">36.000</td>
                </tr>
                <tr>
                    <td>
                        <span class="item-name">Roti Bakar Cokelat</span>
                        <span class="item-detail">@15.000</span>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">1</td>
                    <td class="text-right" style="vertical-align: middle;">15.000</td>
                </tr>
                <tr>
                    <td>
                        <span class="item-name">Snack Keripik Singkong</span>
                        <span class="item-detail">@8.000</span>
                    </td>
                    <td class="text-center" style="vertical-align: middle;">1</td>
                    <td class="text-right" style="vertical-align: middle;">8.000</td>
                </tr>
            </tbody>
        </table>

        <div class="divider"></div>

        <div class="total-section">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%; text-align: right;">Subtotal:</td>
                    <td style="width: 40%; text-align: right;">59.000</td>
                </tr>
                <tr>
                    <td style="text-align: right;">Diskon:</td>
                    <td style="text-align: right;">(5.000)</td>
                </tr>
                <tr style="font-weight: bold; font-size: 14px;">
                    <td style="text-align: right; padding-top: 5px;">GRAND TOTAL:</td>
                    <td style="text-align: right; padding-top: 5px;">54.000</td>
                </tr>
                <tr style="color: #444;">
                    <td style="text-align: right; padding-top: 5px;">Bayar (Tunai):</td>
                    <td style="text-align: right; padding-top: 5px;">100.000</td>
                </tr>
                <tr style="color: #444;">
                    <td style="text-align: right;">Kembali:</td>
                    <td style="text-align: right;">46.000</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <div class="text-center promo-box">
            <strong>UNTUK KAMU PELANGGAN SETIA!</strong><br>
            Dapatkan Diskon 10% di pembelian berikutnya.<br>
            Gunakan Kode Voucher berikut di Kasir/Web:
            <span class="coupon-code">UNTUKMU10</span>
            <small>*Berlaku s/d 30 Juni 2026</small>
        </div>

        <div class="text-center footer-thanks">
            Terima kasih telah berbelanja!<br>
            Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.<br>
            <br>
            Kritik & Saran: CS@tokokumart.com
        </div>

    </div>

    <script>
        // Lepas tanda komentar di bawah jika ingin halaman langsung memicu print saat dibuka
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>