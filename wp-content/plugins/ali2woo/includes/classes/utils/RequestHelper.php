<?php

/**
 * Description of RequestHelper
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class RequestHelper {
    public static function build_request($function, $params=[]){
        $aliexpressToken = AliexpressToken::getInstance()->defaultToken();
        $request_url = get_setting('api_endpoint').$function.'.php?' .
            Account::getInstance()->build_params() .
            AliexpressLocalizator::getInstance()->build_params(isset($params['lang'])) .
            "&su=" . urlencode(site_url()) .
            "&ae_token=" . ($aliexpressToken ? $aliexpressToken['access_token'] : '');

        if(!empty($params) && is_array($params)){
            foreach($params as $key=>$val){
                $request_url .= "&".str_replace("%7E", "~", rawurlencode($key))."=".str_replace("%7E", "~", rawurlencode($val));
            }    
        }
        return $request_url;
    }
}
