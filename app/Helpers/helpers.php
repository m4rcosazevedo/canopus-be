<?php

if (!function_exists('remove_extra_spaces')) {
    function remove_extra_spaces(?string $string): string
    {
        if (!$string) return '';

        $string = trim($string);
        return preg_replace('/\s+/', ' ', $string);
    }
}

