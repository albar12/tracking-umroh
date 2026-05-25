<?php

if (!function_exists('uri_segment')) {
    function uri_segment($index)
    {
        return service('uri')->getSegment($index);
    }
}

if (!function_exists('uri_path')) {
    function uri_path()
    {
        return service('uri')->getPath();
    }
}
