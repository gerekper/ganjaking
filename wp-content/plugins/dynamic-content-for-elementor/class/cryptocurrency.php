<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class CryptocurrencyApiError extends \Error
{
}
class Cryptocurrency
{
    private $is_sandbox;
    private $api_key;
    private $fiat_and_crypto_info = \false;
    const CACHE_MAX_AGES = ['1m' => 60, '5m' => 60 * 5, '15m' => 60 * 15, '1h' => 60 * 60];
    public function is_sandbox()
    {
        return $this->is_sandbox;
    }
    public function get_api_key()
    {
        if ($this->is_sandbox) {
            return 'b54bcf4d-1bca-4e8e-9a24-22ff2c3d462c';
        } else {
            return $this->api_key;
        }
    }
    public function get_transient_prefix()
    {
        return $this->is_sandbox ? 'dce_crypto_sandbox_' : 'dce_crypto_';
    }
    public function api_request($endpoint, $parameters = [])
    {
        if (!\function_exists('curl_init')) {
            throw new \DynamicContentForElementor\CryptocurrencyApiError(esc_html__('You need the PHP curl extension to use the cryptocurrency features.', 'dynamic-content-for-elementor'));
        }
        $subdomain = $this->is_sandbox ? 'sandbox-api' : 'pro-api';
        $url = "https://{$subdomain}.coinmarketcap.com/v1" . $endpoint;
        $headers = ['Accepts: application/json', 'X-CMC_PRO_API_KEY: ' . $this->get_api_key()];
        $qs = \http_build_query($parameters);
        $request = "{$url}?{$qs}";
        $curl = \curl_init();
        \curl_setopt_array($curl, array(\CURLOPT_URL => $request, \CURLOPT_HTTPHEADER => $headers, \CURLOPT_RETURNTRANSFER => 1));
        $response = \curl_exec($curl);
        if (\curl_error($curl)) {
            throw new \DynamicContentForElementor\CryptocurrencyApiError('Coinmarketcap API Connection Error: ' . \curl_error($curl));
        }
        \curl_close($curl);
        $data = \json_decode($response, \true);
        if (($data['status']['error_code'] ?? 9999) === 0 && \is_array($data['data'])) {
            return $data['data'];
        }
        throw new \DynamicContentForElementor\CryptocurrencyApiError($data['status']['error_message'] ?? 'Coinmarketcap API Connection Error');
    }
    public function get_coin_quote($coin_id, $convert_id, $max_age = '5m')
    {
        $transient_key = $this->get_transient_prefix() . "quote_{$max_age}_{$coin_id}_{$convert_id}";
        $transient = get_transient($transient_key);
        if ($transient !== \false) {
            return \json_decode($transient, \true);
        }
        $data = $this->api_request('/cryptocurrency/quotes/latest', ['id' => $coin_id, 'convert_id' => $convert_id]);
        $quote = $data[$coin_id]['quote'][$convert_id];
        set_transient($transient_key, wp_json_encode($quote), self::CACHE_MAX_AGES[$max_age]);
        return $quote;
    }
    public function get_coin_historical_quote($coin_id, $convert_id, $count, $interval, $max_age = '5m')
    {
        $transient_key = $this->get_transient_prefix() . "historical_quote_{$max_age}_{$count}_{$interval}_{$coin_id}_{$convert_id}";
        $transient = get_transient($transient_key);
        if ($transient !== \false) {
            return \json_decode($transient, \true);
        }
        $data = $this->api_request('/cryptocurrency/quotes/historical', ['id' => $coin_id, 'convert_id' => $convert_id, 'count' => $count, 'interval' => $interval]);
        $quotes = $data[$coin_id]['quotes'];
        set_transient($transient_key, wp_json_encode($quotes), self::CACHE_MAX_AGES[$max_age]);
        return $quotes;
    }
    public function get_cache_age_options()
    {
        return ['1m' => __('1 Minute', 'dynamic-content-for-elementor'), '5m' => __('5 Minutes', 'dynamic-content-for-elementor'), '15m' => __('15 Minutes', 'dynamic-content-for-elementor'), '1h' => __('1 Hour', 'dynamic-content-for-elementor')];
    }
    public function get_coin_logo($id)
    {
        $transient_key = $this->get_transient_prefix() . 'logo_' . $id;
        $transient = get_transient($transient_key);
        if ($transient !== \false) {
            return $transient;
        }
        $info = $this->api_request('/cryptocurrency/info', ['id' => $id]);
        $logo = $info[$id]['logo'];
        set_transient($transient_key, $logo, DAY_IN_SECONDS);
        return $logo;
    }
    public function get_convert_options()
    {
        return $this->get_fiat_options() + $this->get_coins_options();
    }
    public function get_fiat_options()
    {
        $fiat = $this->get_fiat();
        $list = [];
        foreach ($fiat as $f) {
            $list[$f['id']] = "{$f['name']} ({$f['symbol']})";
        }
        return $list;
    }
    public function get_coins_options()
    {
        $coins = $this->get_available_coins();
        $list = [];
        foreach ($coins as $c) {
            $list[$c['id']] = "{$c['name']} ({$c['symbol']})";
        }
        return $list;
    }
    public function get_crypto_info($coin_id)
    {
        return $this->get_fiat_and_crypto_info()[$coin_id] ?? \false;
    }
    public function get_fiat_and_crypto_info($coin_id = \false)
    {
        if ($this->fiat_and_crypto_info === \false) {
            $all = [];
            $fiat = $this->get_fiat();
            $coins = $this->get_available_coins();
            foreach ($fiat as $f) {
                $all[$f['id']] = $f;
            }
            foreach ($coins as $c) {
                $all[$c['id']] = $c;
            }
            $this->fiat_and_crypto_info = $all;
        }
        if ($coin_id === \false) {
            return $this->fiat_and_crypto_info;
        } else {
            return $this->fiat_and_crypto_info[$coin_id] ?? \false;
        }
    }
    public function get_sign($id)
    {
        $all = $this->get_fiat_and_crypto_info();
        if (isset($all[$id])) {
            if (isset($all[$id]['sign'])) {
                return $all[$id]['sign'];
            }
            return $all[$id]['symbol'] . ' ';
        }
        return \false;
    }
    public function get_fiat()
    {
        $transient_key = $this->get_transient_prefix() . 'fiat_map';
        $transient = get_transient($transient_key);
        if ($transient !== \false) {
            return \json_decode($transient, \true);
        }
        $fiat = $this->api_request('/fiat/map');
        set_transient($transient_key, wp_json_encode($fiat), DAY_IN_SECONDS);
        return $fiat;
    }
    public function get_available_coins()
    {
        $transient_key = $this->get_transient_prefix() . 'crypto_map';
        $transient = get_transient($transient_key);
        if ($transient !== \false) {
            return \json_decode($transient, \true);
        }
        $coins = $this->api_request('/cryptocurrency/map', []);
        set_transient($transient_key, wp_json_encode($coins), DAY_IN_SECONDS);
        return $coins;
    }
    public function __construct()
    {
        $key = get_option('dce_coinmarketcap_key');
        if (!$key) {
            $this->is_sandbox = \true;
        } else {
            $this->is_sandbox = \false;
            $this->api_key = $key;
        }
    }
}
