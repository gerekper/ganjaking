<?php

/**
 * Description of A2W_RequestHelper
 *
 * @author Andrey
 */
if (!class_exists('A2W_RequestHelper')) {

    class A2W_RequestHelper {
        public static function build_request($function, $params=array()){
            $request_url = a2w_get_setting('api_endpoint').$function.'.php?' . A2W_Account::getInstance()->build_params() . A2W_AliexpressLocalizator::getInstance()->build_params(isset($params['lang']))."&su=".  urlencode(site_url());
            if(!empty($params) && is_array($params)){
                foreach($params as $key=>$val){
                    $request_url .= "&".str_replace("%7E", "~", rawurlencode($key))."=".str_replace("%7E", "~", rawurlencode($val));
                }    
            }
            return $request_url;
        }
    }
}
