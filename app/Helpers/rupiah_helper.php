<?php

if (!function_exists('rupiah')) {

    /**
     * Format angka ke Rupiah
     *
     * @param int|float|string $angka
     * @param bool $prefix
     * @return string
     */
    function rupiah($angka, $prefix = true)
    {
        if ($angka === null || $angka === '') {
            return $prefix ? 'Rp 0' : '0';
        }

        $hasil = number_format((float)$angka, 0, ',', '.');

        return $prefix ? 'Rp ' . $hasil : $hasil;
    }
}
