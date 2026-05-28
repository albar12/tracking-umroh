<?php
if (!function_exists('getAppTitle')) {
    function getAppTitle()
    {
        return getenv('APP_TITLE') ?: 'Default Title';
    }
}
