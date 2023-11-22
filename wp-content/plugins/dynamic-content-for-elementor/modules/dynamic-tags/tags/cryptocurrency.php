<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\CryptocurrencyApiError;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Cryptocurrency extends Data_Tag
{
    public function get_name()
    {
        return 'dce-cryptocurrency';
    }
    public function get_title()
    {
        return __('Cryptocurrency', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return ['base', 'text', 'number'];
    }
    public function get_single_quote()
    {
        $coin_id = $this->get_settings('coin_id');
        $convert_id = $this->get_settings('convert_id');
        $cache_age = $this->get_settings('cache_age');
        $precision = $this->get_settings('precision');
        $should_format = $this->get_settings('should_format');
        $sign = $this->get_settings('sign');
        $data = $this->get_settings('data');
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        try {
            $response = $crypto->get_coin_quote($coin_id, $convert_id, $cache_age);
        } catch (CryptocurrencyApiError $e) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return $e->getMessage();
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions
            \error_log($e->getMessage());
            return 'NA';
        }
        $req_data = $response[$data] ?? 'NA';
        if ($should_format === 'yes') {
            $formatted = \number_format_i18n($req_data, $precision);
            if ('yes' === $sign) {
                if ($data === 'price' || $data === 'market_cap') {
                    return $crypto->get_sign($convert_id) . $formatted;
                } else {
                    return $formatted . '%';
                }
            }
        } else {
            $formatted = \round($req_data, $precision);
        }
        return $formatted;
    }
    public function get_price_history()
    {
        $coin_id = $this->get_settings('coin_id');
        $convert_id = $this->get_settings('convert_id');
        $cache_age = $this->get_settings('cache_age');
        $count = $this->get_settings('history_count');
        $interval = $this->get_settings('history_interval');
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        try {
            $response = $crypto->get_coin_historical_quote($coin_id, $convert_id, $count, $interval, $cache_age);
        } catch (CryptocurrencyApiError $e) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return $e->getMessage();
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions
            \error_log($e->getMessage());
            return 'NA';
        }
        $csv = '';
        foreach ($response as $quote) {
            $quote = $quote['quote'][$convert_id];
            $timestamp = \strtotime($quote['timestamp']);
            $price = $quote['price'];
            $csv .= "{$timestamp},{$price}\n";
        }
        return $csv;
    }
    public function get_value(array $options = [])
    {
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        $data = $this->get_settings('data');
        if ($data === 'price_history') {
            return $this->get_price_history();
        }
        return $this->get_single_quote();
    }
    protected function register_controls()
    {
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        try {
            $coins_options = $crypto->get_coins_options();
            $convert_options = $crypto->get_convert_options();
        } catch (CryptocurrencyApiError $e) {
            $this->add_control('notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => $e->getMessage(), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            return;
        }
        if ($crypto->is_sandbox()) {
            $this->add_control('notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You have not yet inserted a Coinmarketcap API key, the data provided are random and for testing purposes.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        }
        $this->add_control('coin_id', ['label' => __('Coin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $coins_options, 'default' => 1]);
        $this->add_control('convert_id', ['label' => __('Convert to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $convert_options, 'default' => 2781]);
        $this->add_control('data', ['label' => __('Get', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['price' => __('Latest Price', 'dynamic-content-for-elementor'), 'percent_change_1h' => __('Percent Change 1 Hour', 'dynamic-content-for-elementor'), 'percent_change_24h' => __('Percent Change 24 Hour', 'dynamic-content-for-elementor'), 'percent_change_7d' => __('Percent Change 7 Days', 'dynamic-content-for-elementor'), 'percent_change_30d' => __('Percent Change 30 Days', 'dynamic-content-for-elementor'), 'market_cap' => __('Market Cap', 'dynamic-content-for-elementor'), 'price_history' => __('Price History', 'dynamic-content-for-elementor')], 'default' => 'price']);
        $this->add_control('cache_age', ['label' => __('Store in cache for', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $crypto->get_cache_age_options(), 'default' => '5m', 'label_block' => \true]);
        $this->add_control('precision', ['label' => __('Decimal Precision', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '2', 'condition' => ['data!' => 'price_history']]);
        $this->add_control('should_format', ['label' => __('Format Number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['data!' => 'price_history']]);
        $this->add_control('sign', ['label' => __('Include Sign', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['should_format' => 'yes', 'data!' => 'price_history']]);
        $options = ['yearly' => __('yearly', 'dynamic-content-for-elementor'), 'monthly' => __('monthly', 'dynamic-content-for-elementor'), 'weekly' => __('weekly', 'dynamic-content-for-elementor'), 'daily' => __('daily', 'dynamic-content-for-elementor'), 'hourly' => __('hourly', 'dynamic-content-for-elementor'), '5m' => __('5 minutes', 'dynamic-content-for-elementor'), '10m' => __('10 minutes', 'dynamic-content-for-elementor'), '15m' => __('15 minutes', 'dynamic-content-for-elementor'), '30m' => __('30 minutes', 'dynamic-content-for-elementor'), '45m' => __('45 minutes', 'dynamic-content-for-elementor'), '1h' => __('1 hour', 'dynamic-content-for-elementor'), '2h' => __('2 hours', 'dynamic-content-for-elementor'), '3h' => __('3 hours', 'dynamic-content-for-elementor'), '4h' => __('4 hours', 'dynamic-content-for-elementor'), '6h' => __('6 hours', 'dynamic-content-for-elementor'), '12h' => __('12 hours', 'dynamic-content-for-elementor'), '24h' => __('24 hours', 'dynamic-content-for-elementor'), '1d' => __('1 day', 'dynamic-content-for-elementor'), '2d' => __('2 days', 'dynamic-content-for-elementor'), '3d' => __('3 days', 'dynamic-content-for-elementor'), '7d' => __('7 days', 'dynamic-content-for-elementor'), '14d' => __('14 days', 'dynamic-content-for-elementor'), '15d' => __('15 days', 'dynamic-content-for-elementor'), '30d' => __('30 days', 'dynamic-content-for-elementor'), '60d' => __('60 days', 'dynamic-content-for-elementor'), '90d' => __('90 days', 'dynamic-content-for-elementor'), '365d' => __('365 days', 'dynamic-content-for-elementor')];
        $this->add_control('history_interval', ['label' => __('History Interval', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'hourly', 'options' => $options, 'condition' => ['data' => 'price_history']]);
        $this->add_control('history_count', ['label' => __('History Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 10, 'min' => 1, 'max' => 10000, 'condition' => ['data' => 'price_history']]);
    }
}
