<?php

/**
 * Description of A2W_AliexpressError
 *
 * @author Andrey
 */
if (!class_exists('A2W_AliexpressError')) {

    class A2W_AliexpressError
    {
        public static function message($error)
        {
            $error_code = 'unknown';
            if (is_array($error) && !empty($error['error_code'])) {
                $error_code = $error['error_code'];
            } else if (is_scalar($error) && !empty($error)) {
                $error_code = $error;
            }

            switch ($error_code) {
                case 'B_DROPSHIPPER_DELIVERY_ADDRESS_VALIDATE_FAIL':
                    return __('Invalid shipping address', 'ali2woo');
                case 'DELIVERY_METHOD_NOT_EXIST':
                    return __('Invalid shipping method', 'ali2woo');
                default:
                    return $error_code;
            }
        }
    }
}
