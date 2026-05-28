<?php

if (!function_exists('setToast')) {
    function setToast($type, $message)
    {
        session()->setFlashdata('toast', [
            'type'    => $type,   // success, error, warning, info
            'message' => $message
        ]);
    }
}
