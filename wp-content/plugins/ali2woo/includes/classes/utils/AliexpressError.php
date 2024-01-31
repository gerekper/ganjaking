<?php

/**
 * Description of AliexpressError
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class AliexpressError
{
    const B_DROPSHIPPER_DELIVERY_ADDRESS_VALIDATE_FAIL = 'B_DROPSHIPPER_DELIVERY_ADDRESS_VALIDATE_FAIL';
    const ERROR_WHEN_BUILD_FOR_PLACE_ORDER = 'ERROR_WHEN_BUILD_FOR_PLACE_ORDER';
    const DELIVERY_METHOD_NOT_EXIST = 'DELIVERY_METHOD_NOT_EXIST';
    const INVALID_SESSION = 'Invalid session';

    public static function message($error)
    {
        $error_code = __('Aliexpress error', 'ali2woo');
        if (is_array($error) && !empty($error['error_code'])) {
            $error_code = $error['error_code'];
        } else if (is_scalar($error) && !empty($error)) {
            $error_code = $error;
        } else if (is_array($error) && !empty($error['error_response']['msg'])) {
            $error_code = $error['error_response']['msg'];
        } else if (is_array($error) && !empty($error['msg'])) {
            $error_code = $error['msg'];
        }

        switch ($error_code) {
            case self::B_DROPSHIPPER_DELIVERY_ADDRESS_VALIDATE_FAIL:
                return __('Invalid shipping address', 'ali2woo');
            case self::ERROR_WHEN_BUILD_FOR_PLACE_ORDER:
                return __('Check if each product (or its variant) in your order is still available and in-stock on AliExpress', 'ali2woo');    
            case self::DELIVERY_METHOD_NOT_EXIST:
                return __('Invalid shipping method', 'ali2woo');
            case self::INVALID_SESSION:
                    return __('Invalid session', 'ali2woo');    
            default:
                return $error_code;
        }
    }
}
