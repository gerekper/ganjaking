<?php

namespace DynamicOOOS;

if (!\function_exists('DynamicOOOS\\dd')) {
    function dd(...$args)
    {
        if (\function_exists('DynamicOOOS\\dump')) {
            dump(...$args);
        } else {
            \var_dump(...$args);
        }
        die;
    }
}
