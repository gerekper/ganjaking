<?php

if (!function_exists('a2w_error_handler')) {

    function a2w_error_handler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        switch ($errno) {

            case E_ERROR:
                $typestr = 'E_ERROR';
                break;
            case E_WARNING:
                $typestr = 'E_WARNING';
                break;
            case E_PARSE:
                $typestr = 'E_PARSE';
                break;
            case E_NOTICE:
                $typestr = 'E_NOTICE';
                break;
            case E_CORE_ERROR:
                $typestr = 'E_CORE_ERROR';
                break;
            case E_CORE_WARNING:
                $typestr = 'E_CORE_WARNING';
                break;
            case E_COMPILE_ERROR:
                $typestr = 'E_COMPILE_ERROR';
                break;
            case E_CORE_WARNING:
                $typestr = 'E_COMPILE_WARNING';
                break;
            case E_USER_ERROR:
                $typestr = 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $typestr = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $typestr = 'E_USER_NOTICE';
                break;
            case E_STRICT:
                $typestr = 'E_STRICT';
                break;
            case E_RECOVERABLE_ERROR:
                $typestr = 'E_RECOVERABLE_ERROR';
                break;
            case E_DEPRECATED:
                $typestr = 'E_DEPRECATED';
                break;
            case E_USER_DEPRECATED:
                $typestr = 'E_USER_DEPRECATED';
                break;
            default:
                $typestr = "Unknown error[$errno]";
                break;
        }

        a2w_error_log(strip_tags("$typestr: " . $errstr . " in " . $errfile . " on line " . $errline));
        if (!in_array($typestr, array('E_WARNING', 'E_NOTICE', 'E_DEPRECATED', 'E_USER_DEPRECATED', 'E_USER_WARNING', 'E_USER_NOTICE'))) {
            throw new Exception("<b>$typestr</b>: $errstr in <b>$errfile</b> on line <b>$errline</b>");
        }
    }

}

if (!function_exists('a2w_init_error_handler')) {

    function a2w_init_error_handler()
    {
        $old_error_handler = set_error_handler('a2w_error_handler');
        return $old_error_handler;
    }

}

if (!function_exists('a2w_remote_get')) {

    function a2w_remote_get($url, $args = array())
    {
        $def_args = array(
            'headers' => array('Accept-Encoding' => ''),
            'timeout' => 30,
            'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0',
            // 'verify' => false,
            // 'sslverify' => false,
            // 'verifyname' => false
        );

        if (!is_array($args)) {
            $args = array();
        }

        foreach ($def_args as $key => $val) {
            if (!isset($args[$key])) {
                $args[$key] = $val;
            }
        }

        if (isset($args['headers'])) {
            $headers = $args['headers'];
            unset($args['headers']);
        }

        // If we've got cookies, use and convert them to Requests_Cookie.
        if (!empty($args['cookies'])) {
            $cookie_jar = new Requests_Cookie_Jar();
            $tmp_cookies = array();
            foreach ($args['cookies'] as $cookie) {
                $tmp_cookies[] = $cookie_jar->normalize_cookie($cookie);
            }
            $args['cookies'] = $tmp_cookies;
        }

        try {
            // Avoid issues where mbstring.func_overload is enabled.
            if (function_exists('mbstring_binary_safe_encoding')) {
                mbstring_binary_safe_encoding();
            } else {
                a2w_error_log('WARNING! function mbstring_binary_safe_encoding is not exist!');
            }

            $requests_response = Requests::get($url, $headers, $args);

            // Convert the response into an array
            $http_response = new A2W_Requests_Response($requests_response);
            $response = $http_response->to_array();

            // Add the original object to the array.
            //$response['http_response'] = $http_response;

            if (function_exists('reset_mbstring_encoding')) {
                reset_mbstring_encoding();
            } else {
                a2w_error_log('WARNING! function reset_mbstring_encoding is not exist!');
            }
        } catch (Requests_Exception $e) {
            $response = new WP_Error('http_request_failed', $e->getMessage());
        } catch (Throwable $e) {
            a2w_print_throwable($e);
            $response = new WP_Error('php_error', 'PHP Error');
        } catch (Exception $e) {
            a2w_print_throwable($e);
            $response = new WP_Error('php_error', 'PHP Error');
        }
        return $response;
    }

}

if (!function_exists('a2w_remote_post')) {

    function a2w_remote_post($url, $data = array(), $args = array())
    {
        $def_args = array(
            'headers' => array('Accept-Encoding' => ''),
            'timeout' => 30,
            'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:60.0) Gecko/20100101 Firefox/60.0',
            // 'verify' => false,
            // 'sslverify' => false,
            // 'verifyname' => false
        );

        if (!is_array($args)) {
            $args = array();
        }

        foreach ($def_args as $key => $val) {
            if (!isset($args[$key])) {
                $args[$key] = $val;
            }
        }

        if (isset($args['headers'])) {
            $headers = $args['headers'];
            unset($args['headers']);
        }

        // If we've got cookies, use and convert them to Requests_Cookie.
        if (!empty($args['cookies'])) {
            $cookie_jar = new Requests_Cookie_Jar();
            $tmp_cookies = array();
            foreach ($args['cookies'] as $cookie) {
                $tmp_cookies[] = $cookie_jar->normalize_cookie($cookie);
            }
            $args['cookies'] = $tmp_cookies;
        }

        try {
            // Avoid issues where mbstring.func_overload is enabled.
            if (function_exists('mbstring_binary_safe_encoding')) {
                mbstring_binary_safe_encoding();
            } else {
                a2w_error_log('WARNING! function mbstring_binary_safe_encoding is not exist!');
            }

            $requests_response = Requests::post($url, $headers, $data, $args);

            // Convert the response into an array
            $http_response = new A2W_Requests_Response($requests_response);
            $response = $http_response->to_array();

            // Add the original object to the array.
            //$response['http_response'] = $http_response;

            if (function_exists('reset_mbstring_encoding')) {
                reset_mbstring_encoding();
            } else {
                a2w_error_log('WARNING! function reset_mbstring_encoding is not exist!');
            }
        } catch (Requests_Exception $e) {
            $response = new WP_Error('http_request_failed', $e->getMessage());
        } catch (Throwable $e) {
            a2w_print_throwable($e);
            $response = new WP_Error('php_error', 'PHP Error');
        } catch (Exception $e) {
            a2w_print_throwable($e);
            $response = new WP_Error('php_error', 'PHP Error');
        }
        return $response;
    }

}

//for PHP < 5.5.0
if (!function_exists('array_column')) {

    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $array = array();
        foreach ($input as $value) {
            if (!array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

// FIX for PHP >= 7
// https://stackoverflow.com/questions/35701730/utf8-endecode-removed-from-php7
// php-xml package is missing in your php installation.
if (!function_exists('utf8_decode')) {
    function utf8_decode($string)
    {
        // utf8_decode() unavailable, use getID3()'s iconv_fallback() conversions (possibly PHP is compiled without XML support)
        $newcharstring = '';
        $offset = 0;
        $stringlength = strlen($string);
        while ($offset < $stringlength) {
            if ((ord($string[$offset]) | 0x07) == 0xF7) {
                // 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
                $charval = ((ord($string[($offset + 0)]) & 0x07) << 18) &
                ((ord($string[($offset + 1)]) & 0x3F) << 12) &
                ((ord($string[($offset + 2)]) & 0x3F) << 6) &
                    (ord($string[($offset + 3)]) & 0x3F);
                $offset += 4;
            } elseif ((ord($string[$offset]) | 0x0F) == 0xEF) {
                // 1110bbbb 10bbbbbb 10bbbbbb
                $charval = ((ord($string[($offset + 0)]) & 0x0F) << 12) &
                ((ord($string[($offset + 1)]) & 0x3F) << 6) &
                    (ord($string[($offset + 2)]) & 0x3F);
                $offset += 3;
            } elseif ((ord($string[$offset]) | 0x1F) == 0xDF) {
                // 110bbbbb 10bbbbbb
                $charval = ((ord($string[($offset + 0)]) & 0x1F) << 6) &
                    (ord($string[($offset + 1)]) & 0x3F);
                $offset += 2;
            } elseif ((ord($string[$offset]) | 0x7F) == 0x7F) {
                // 0bbbbbbb
                $charval = ord($string[$offset]);
                $offset += 1;
            } else {
                // error? throw some kind of warning here?
                $charval = false;
                $offset += 1;
            }
            if ($charval !== false) {
                $newcharstring .= (($charval < 256) ? chr($charval) : '?');
            }
        }
        return $newcharstring;
    }
}

if (!function_exists('a2w_set_transient')) {

    function a2w_set_transient($transient, $value, $expiration = 0, $use_cache = false, $autoload = 'no')
    {
        if (a2w_check_defined('A2W_SAVE_TRANSIENT_AS_OPTION')) {
            wp_cache_delete($transient, 'options');
            if (false === get_option($transient)) {
                $result = add_option($transient, $value, '', $autoload);
            } else {
                $result = update_option($transient, $value, $autoload);
            }

            return $result;
        }

        $expiration = (int) $expiration;

        /**
         * Filters a specific transient before its value is set.
         *
         * The dynamic portion of the hook name, `$transient`, refers to the transient name.
         *
         * @since 3.0.0
         * @since 4.2.0 The `$expiration` parameter was added.
         * @since 4.4.0 The `$transient` parameter was added.
         *
         * @param mixed  $value      New value of transient.
         * @param int    $expiration Time until expiration in seconds.
         * @param string $transient  Transient name.
         */
        $value = apply_filters("pre_set_transient_{$transient}", $value, $expiration, $transient);

        /**
         * Filters the expiration for a transient before its value is set.
         *
         * The dynamic portion of the hook name, `$transient`, refers to the transient name.
         *
         * @since 4.4.0
         *
         * @param int    $expiration Time until expiration in seconds. Use 0 for no expiration.
         * @param mixed  $value      New value of transient.
         * @param string $transient  Transient name.
         */
        $expiration = apply_filters("expiration_of_transient_{$transient}", $expiration, $value, $transient);

        if ($use_cache && wp_using_ext_object_cache()) {
            $result = wp_cache_set($transient, $value, 'transient', $expiration);
        } else {
            $transient_timeout = '_transient_timeout_' . $transient;
            $transient_option = '_transient_' . $transient;
            if (false === get_option($transient_option)) {
                if ($expiration) {
                    $autoload = 'no';
                    add_option($transient_timeout, time() + $expiration, '', 'no');
                }
                $result = add_option($transient_option, $value, '', $autoload);
            } else {
                // If expiration is requested, but the transient has no timeout option,
                // delete, then re-create transient rather than update.
                $update = true;
                if ($expiration) {
                    if (false === get_option($transient_timeout)) {
                        delete_option($transient_option);
                        add_option($transient_timeout, time() + $expiration, '', 'no');
                        $result = add_option($transient_option, $value, '', 'no');
                        $update = false;
                    } else {
                        update_option($transient_timeout, time() + $expiration);
                    }
                }
                if ($update) {
                    $result = update_option($transient_option, $value);
                }
            }
        }

        if ($result) {

            /**
             * Fires after the value for a specific transient has been set.
             *
             * The dynamic portion of the hook name, `$transient`, refers to the transient name.
             *
             * @since 3.0.0
             * @since 3.6.0 The `$value` and `$expiration` parameters were added.
             * @since 4.4.0 The `$transient` parameter was added.
             *
             * @param mixed  $value      Transient value.
             * @param int    $expiration Time until expiration in seconds.
             * @param string $transient  The name of the transient.
             */
            do_action("set_transient_{$transient}", $value, $expiration, $transient);

            /**
             * Fires after the value for a transient has been set.
             *
             * @since 3.0.0
             * @since 3.6.0 The `$value` and `$expiration` parameters were added.
             *
             * @param string $transient  The name of the transient.
             * @param mixed  $value      Transient value.
             * @param int    $expiration Time until expiration in seconds.
             */
            do_action('setted_transient', $transient, $value, $expiration);
        }
        return $result;
    }

}

if (!function_exists('a2w_get_transient')) {

    function a2w_get_transient($transient, $use_cache = false)
    {
        if (a2w_check_defined('A2W_SAVE_TRANSIENT_AS_OPTION')) {
            return get_option($transient);
        }

        /**
         * Filters the value of an existing transient.
         *
         * The dynamic portion of the hook name, `$transient`, refers to the transient name.
         *
         * Passing a truthy value to the filter will effectively short-circuit retrieval
         * of the transient, returning the passed value instead.
         *
         * @since 2.8.0
         * @since 4.4.0 The `$transient` parameter was added
         *
         * @param mixed  $pre_transient The default value to return if the transient does not exist.
         *                              Any value other than false will short-circuit the retrieval
         *                              of the transient, and return the returned value.
         * @param string $transient     Transient name.
         */
        $pre = apply_filters("pre_transient_{$transient}", false, $transient);
        if (false !== $pre) {
            return $pre;
        }

        if ($use_cache && wp_using_ext_object_cache()) {
            $value = wp_cache_get($transient, 'transient');
        } else {
            $transient_option = '_transient_' . $transient;
            if (!wp_installing()) {
                // If option is not in alloptions, it is not autoloaded and thus has a timeout
                $alloptions = wp_load_alloptions();
                if (!isset($alloptions[$transient_option])) {
                    $transient_timeout = '_transient_timeout_' . $transient;
                    $timeout = get_option($transient_timeout);
                    if (false !== $timeout && $timeout < time()) {
                        delete_option($transient_option);
                        delete_option($transient_timeout);
                        $value = false;
                    }
                }
            }

            if (!isset($value)) {
                $value = get_option($transient_option);
            }

        }

        /**
         * Filters an existing transient's value.
         *
         * The dynamic portion of the hook name, `$transient`, refers to the transient name.
         *
         * @since 2.8.0
         * @since 4.4.0 The `$transient` parameter was added
         *
         * @param mixed  $value     Value of transient.
         * @param string $transient Transient name.
         */
        return apply_filters("transient_{$transient}", $value, $transient);
    }

}

if (!function_exists('a2w_delete_transient')) {

    function a2w_delete_transient($transient, $use_cache = false)
    {
        if (a2w_check_defined('A2W_SAVE_TRANSIENT_AS_OPTION')) {
            return delete_option($transient);
        }
        /**
         * Fires immediately before a specific transient is deleted.
         *
         * The dynamic portion of the hook name, `$transient`, refers to the transient name.
         *
         * @since 3.0.0
         *
         * @param string $transient Transient name.
         */
        do_action("delete_transient_{$transient}", $transient);

        if ($use_cache && wp_using_ext_object_cache()) {
            $result = wp_cache_delete($transient, 'transient');
        } else {
            $option_timeout = '_transient_timeout_' . $transient;
            $option = '_transient_' . $transient;
            $result = delete_option($option);
            if ($result) {
                delete_option($option_timeout);
            }

        }

        if ($result) {

            /**
             * Fires after a transient is deleted.
             *
             * @since 3.0.0
             *
             * @param string $transient Deleted transient name.
             */
            do_action('deleted_transient', $transient);
        }

        return $result;
    }

}

if (!function_exists('a2w_generate_call_trace')) {

    function a2w_generate_call_trace()
    {
        $e = new Exception();
        $trace = array_reverse(explode("\n", $e->getTraceAsString()));
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();
        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }
        return "\t" . implode("\n\t", $result);
    }
}

if (!function_exists('a2w_check_defined')) {
    function a2w_check_defined($name)
    {
        return apply_filters('a2w_check_defined_filter', (defined($name) && constant($name)), $name);
    }
}

if (!function_exists('a2w_image_url')) {
    function a2w_image_url($image_url)
    {
        if (a2w_check_defined('A2W_USE_CDN')) {
            $image_url = base64_encode($image_url);
            return A2W()->plugin_url() . '/includes/cdn.php?url=' . $image_url . '&_sign=' . a2w_sign_request(array('url' => $image_url));
        } else {
            return $image_url;
        }
    }
}

if (!function_exists('a2w_error_log')) {
    function a2w_error_log($message)
    {
        A2W_Logs::getInstance()->write($message);
        error_log($message);
    }
}

if (!function_exists('a2w_info_log')) {
    function a2w_info_log($message)
    {
        A2W_Logs::getInstance()->write($message);
    }
}

if (!function_exists('a2w_print_throwable')) {
    function a2w_print_throwable($throwable)
    {
        a2w_error_log('PHP Error:  ' . $throwable->getMessage() . ' in ' . $throwable->getFile() . ':' . $throwable->getLine() . PHP_EOL . 'Stack trace:' . PHP_EOL . $throwable->getTraceAsString());
    }
}

if (!function_exists('a2w_gen_pk')) {
    function a2w_gen_pk($length = 32)
    {
        try {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $len = strlen($chars);
            $val = '';
            for ($i = 0; $i < $length; $i++) {
                $val .= $chars[rand(0, $len - 1)];
            }

            if (!file_exists(WP_CONTENT_DIR . '/uploads/ali2woo/')) {
                mkdir(WP_CONTENT_DIR . '/uploads/ali2woo/');
            }

            file_put_contents(WP_CONTENT_DIR . '/uploads/ali2woo/pk.php', '<?php function a2w_plugin_key(){return "' . $val . '";}');
        } catch (Exception $e) {
            a2w_print_throwable($e);
        }
    }
}

if (!function_exists('a2w_get_pk')) {
    function a2w_get_pk()
    {
        if (file_exists(WP_CONTENT_DIR . '/uploads/ali2woo/pk.php')) {
            require_once WP_CONTENT_DIR . '/uploads/ali2woo/pk.php';
            return a2w_plugin_key();
        }
        return "";
    }
}

if (!function_exists('a2w_sign_request')) {
    function a2w_sign_request($data, $pk = false)
    {
        $pk = $pk ? $pk : a2w_get_pk();
        unset($data['_sign']);
        ksort($data);
        $val = "";
        foreach ($data as $k => $v) {
            $val .= $k . $v;
        }
        return md5($_SERVER['HTTP_HOST'] . $val . $pk);
    }
}

if (!function_exists('a2w_verify_request')) {
    function a2w_verify_request($sign, $data, $pk = false)
    {
        return $sign === a2w_sign_request($data, $pk);
    }
}

if (!function_exists('a2w_json_decode')) {
    function a2w_json_decode($json, $assoc = true, $depth = 512, $options = 2)
    {
        if (function_exists('mb_convert_encoding')) {
            $json = mb_convert_encoding($json, 'UTF-8', 'UTF-8');
        }

        return json_decode($json, $assoc, $depth, $options);
    }
}
