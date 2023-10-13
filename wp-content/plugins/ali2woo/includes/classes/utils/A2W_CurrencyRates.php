<?php

/**
 * Description of A2W_CurrencyRates
 *
 * @author Ali2Woo Team
 */
if (!class_exists('A2W_CurrencyRates')) {

    class A2W_CurrencyRates
    {
        public static function load_currency_rate($currency, $base_currency = 'USD'){
            $currencyApi = new A2W_CurrencyApi();
            $currency_api_result = $currencyApi->get_conversion_rate($currency, $base_currency);

            return $currency_api_result;
        }

        public static function sync(){
            $rates = array();

            $current_currency = strtoupper(A2W_AliexpressLocalizator::getInstance()->currency);
            
            if ($current_currency === 'USD'){
                $rates['USD_' . $current_currency] = 1;
                $result = A2W_ResultBuilder::buildOk(array('rates' => $rates));
            }
            else {
                $currency_api_result = self::load_currency_rate($current_currency);

                if ($currency_api_result['state'] !== 'error'){
                    $rates['USD_' . $current_currency] = $currency_api_result['rate'];
                    $result = A2W_ResultBuilder::buildOk(array('rates' => $rates));
                      
                } else {
                    $result = A2W_ResultBuilder::buildError($currency_api_result['message']); 
                }
            }

            if ($result['state'] == 'ok'){
                 //also get rate for CNY
                $currency_api_result = self::load_currency_rate($current_currency, 'CNY');

                if ($currency_api_result['state'] !== 'error'){
                    $result['rates']['CNY_' . $current_currency] = $currency_api_result['rate'];
                    
                } else {
                    $result = A2W_ResultBuilder::buildError($currency_api_result['message']); 
                }
            }
           
            a2w_set_transient('a2w_currency_exchange_rate', $result, 60 * 60 * 6);
        }

        public static function get(){
            $result = a2w_get_transient('a2w_currency_exchange_rate');
            if (!is_array($result)) {
                $result = A2W_ResultBuilder::buildError(__('No currency exchange rate available, you have to synchronize it.', 'ali2woo'));
            }

            return $result;     
        }

    }
}