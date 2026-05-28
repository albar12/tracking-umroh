<?php

if (!function_exists('stringEncryptions')) {
    function stringEncryptions($action, $string)
    {
        $output = false;
        $encrypt_method = 'AES-256-CBC';

        // Gunakan ENV atau nilai default
        $secret_key = getenv('SECRET_KEY') ?: 'f8fdcb2aa276f844ae82ac75a7029e61c5da3a946c3a4c7bfcfd3d0c96e12e37';
        $secret_iv  = getenv('SECRET_IV') ?: '!IVk@_$2';

        // Hash key & IV
        $key = hash('sha256', $secret_key);
        $iv  = substr(hash('sha256', $secret_iv), 0, 16);

        try {
            if ($action == 'encrypt') {
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
            } else if ($action == 'decrypt') {
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }
        } catch (\Throwable $e) {
            // Log error atau return null
            error_log("Encryption error: " . $e->getMessage());
            $output = null;
        }

        return $output;
    }
}
