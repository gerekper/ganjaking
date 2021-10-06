<?php

/**
 * Description of A2W_AliexpressLocalizator
 *
 * @author Andrey
 */
if (!class_exists('A2W_AliexpressLocalizator')) {

    class A2W_AliexpressLocalizator {

        private static $_instance = null;
        public $language;
        public $currency;
        public $all_currencies = array();

        protected function __construct() {
            $this->language = strtolower(a2w_get_setting('import_language'));
            $this->currency = strtoupper(a2w_get_setting('local_currency'));

            $currencies_file = A2W()->plugin_path() . '/assets/data/currencies.json';  
            if(file_exists ($currencies_file)){
                $this->all_currencies = json_decode(file_get_contents($currencies_file), true);
            }

            if (a2w_check_defined('A2W_CUSTOM_CURRENCY')) {
                $cca = explode(";", A2W_CUSTOM_CURRENCY);
                if (is_array($cca)) {
                    foreach ($cca as $cc) {
                        if($cc) {
                            $tmp_cur=explode("#", $cc);
                            if(isset($tmp_cur[0])){
                                $this->all_currencies[] =array('code'=>$tmp_cur[0], 'name'=>isset($tmp_cur[1])?$tmp_cur[1]:$tmp_cur[0], 'custom'=>true);
                            }
                        }
                    }
                }
            }
        }

        protected function __clone() {
            
        }

        static public function getInstance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function getLocaleCurr($in_curr=false) {
            $out_curr = $in_curr?$in_curr:$this->currency;
            if ($out_curr == 'USD') {
                return '$';
            }
            return $out_curr . ' ';
        }

        public function isCustomCurrency($in_curr=false){
            $custom_currencies =  $this->getCurrencies(true);
            return isset($custom_currencies[$in_curr?strtoupper($in_curr):$this->currency]);
        }

        public function getLangCode() {
            if(a2w_check_defined('A2W_API_LANG_CODE')){
                return A2W_API_LANG_CODE;
            }
            switch ($this->language) {
                case 'en':
                    return 'en_GB';
                case 'fr':
                    return 'fr_FR';
                case 'it':
                    return 'it_IT';
                case 'ru':
                    return 'ru_RU';
                case 'de':
                    return 'de_DE';
                case 'pt':
                    return 'pt_BR';
                case 'es':
                    return 'es_ES';
                case 'nl':
                    return 'nl_NL';
                case 'tr':
                    return 'tr_TR';
                case 'ja':
                    return 'ja_JP';
                case 'ko':
                    return 'ko_KR';
                case 'th':
                    return 'th_TH';
                case 'vi':
                    return 'vi_VN';
                case 'ar':
                    return 'ar_MA';
                case 'he':
                    return 'iw_IL';
                case 'pl':
                    return 'pl_PL';
                case 'id':
                    return 'in_ID';
                default:
                    return 'en_GB';
            }
        }

        public function getLocaleCookies($as_object = true) {
            switch ($this->language) {
                case 'en':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=en_US', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'b_locale=en_US&site=glo&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'fr':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=fr_FR', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=fra&b_locale=fr_FR&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'it':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=it_IT', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=ita&b_locale=it_IT&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'ru':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=ru_RU', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=rus&b_locale=ru_RU&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'de':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=de_DE', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=deu&b_locale=de_DE&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'pt':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=pt_BR', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=bra&b_locale=pt_BR&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'es':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=es_ES', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=esp&b_locale=es_ES&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'nl':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=nl_NL', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=nld&b_locale=nl_NL&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'tr':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=tr_TR', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=tur&b_locale=tr_TR&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'ja':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=ja_JP', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=jpn&b_locale=ja_JP&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'ko':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=ko_KR', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=kor&b_locale=ko_KR&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'th':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=th_TH', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=tha&b_locale=th_TH&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'vi':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=vi_VN', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=vnm&b_locale=vi_VN&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'ar':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=ar_MA', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=ara&b_locale=ar_MA&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'he':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=iw_IL', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=isr&b_locale=iw_IL&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'pl':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=pl_PL', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=pol&b_locale=pl_PL&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                case 'id':
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=in_ID', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'site=idn&b_locale=in_ID&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
                default:
                    $cookies = array(
                        array('name' => 'xman_us_f', 'value' => 'x_l=0&x_locale=en_US', 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                        array('name' => 'aep_usuc_f', 'value' => 'b_locale=en_US&site=glo&c_tp=' . $this->currency, 'params' => array('domain' => '.aliexpress.com', 'path' => '/', 'expires' => mktime(0, 0, 0, date('m'), date('d') + 7, date('Y')))),
                    );
                    break;
            }

            $result_cookies = array();
            if ($as_object) {
                foreach ($cookies as $c) {
                    $result_cookies[] = new Requests_Cookie($c['name'], $c['value'], $c['params'], array('host-only' => false));
                }
            } else {
                $result_cookies = $cookies;
            }

            return $result_cookies;
        }

        

        public function getCurrencies($custom = false) {  
            $result = array();

            foreach($this->all_currencies as $c){
                if(!$custom && !$c['custom']){
                    $result[strtoupper($c['code'])] = $c['name'];
                } else if($custom && $c['custom']){
                    $result[strtoupper($c['code'])] = $c['name'];
                }
            }
            return $result;
        }

        public function build_params($skip_lang = false) {
            if($skip_lang){
                return '&curr=' . $this->currency;
            }else{
                $lang_code_str = "";
                if(a2w_check_defined('A2W_API_LANG_CODE')){
                    $lang_code_str = "&lang_code=".A2W_API_LANG_CODE;
                }
                return '&lang=' . $this->language . '&curr=' . $this->currency . $lang_code_str;
                
            }
        }

    }

}
