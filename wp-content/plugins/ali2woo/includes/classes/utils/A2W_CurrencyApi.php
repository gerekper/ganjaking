<?php

/**
 * Description of A2W_CurrencyApi
 *
 * @author Ali2Woo Team
 */
if (!class_exists('A2W_CurrencyApi')) {

    class A2W_CurrencyApi
    {
        public function get_conversion_rate($currency, $base_currency = 'USD'){
            $currency = strtoupper($currency);
            $req_url = 'https://api.exchangerate.host/latest?base=' . $base_currency . '&symbols=' . $currency;
            $request = a2w_remote_get($req_url);

            if (is_wp_error($request)) {
                $result = A2W_ResultBuilder::buildError($request->get_error_message());
            } else {
                $result = json_decode($request['body'], true);

                if ($result['success'] === true) {
              
                    if (isset($result['rates']) && isset($result['rates'][$currency])) {
                        $rate = round(floatval($result['rates'][$currency]), 2);
                        $result = A2W_ResultBuilder::buildOk(array('rate' => $rate));
                    } else {
                        $msg = sprintf(__('No currency exchange rate available for %s / %s, you have to synchronize it.', 'ali2woo'),
                            $base_currency,
                            $currency
                        );

                        $result = A2W_ResultBuilder::buildError($msg);
                    }           
                  
                } else {
                    $msg = sprintf(__('No currency exchange rate available for %s / %s, you have to synchronize it.', 'ali2woo'),
                        $base_currency,
                        $currency
                    );

                    $result = A2W_ResultBuilder::buildError($msg);
                }
            } 
            
            return $result;
        }
    }
}