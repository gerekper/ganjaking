<?php

namespace DynamicContentForElementor\Widgets;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;
use DynamicContentForElementor\CryptocurrencyApiError;
use Elementor\Group_Control_Typography;
class CryptocoinBadge extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return ['dce-crypto-badge'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('coin_section', ['label' => __('Coin', 'dynamic-content-for-elementor')]);
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        try {
            $coins_options = $crypto->get_coins_options();
            $convert_options = $crypto->get_convert_options();
        } catch (\Error $e) {
            $this->add_control('notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => $e->getMessage(), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            return;
        }
        if ($crypto->is_sandbox()) {
            $this->add_control('notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You have not yet inserted a Coinmarketcap API key, the data provided are random and for testing purposes.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        }
        $this->add_control('coin_id', ['label' => __('Coin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $coins_options, 'default' => 1]);
        $this->add_control('convert_id', ['label' => __('Convert to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $convert_options, 'default' => 2781]);
        $this->add_control('cache_age', ['label' => __('Store in cache for', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $crypto->get_cache_age_options(), 'default' => '5m', 'label_block' => \true]);
        $this->end_controls_section();
    }
    private function render_template($data)
    {
        $template = <<<'EOF'
<div class="dce-cryptobadge-wrapper">
  <div class="dce-cryptobadge-main">
    <div class="dce-cryptobadge-context">
      <div class="dce-cryptobadge-logo">
        <img src="{{coin_logo}}" alt="{{coin_name}} logo">
      </div>
      <div class="dce-cryptobadge-name">{{coin_name}}</div>
      <div class="dce-cryptobadge-pair">{{pair}}</div>
    </div>
    <div class="dce-cryptobadge-latest-box">
      <div class="dce-cryptobadge-latest-price">{{latest_price}}</div>
    </div>
  </div>
  <div class="dce-cryptobadge-list-items-wrapper">
    {{#list_items}}
    <div class="dce-cryptobadge-list-item-wrapper">
      <span class="dce-cryptobadge-list-item-label">{{label}}</span>
      <span class="dce-cryptobadge-list-item-content">{{content}}</span>
    </div>
    {{/list_items}}
  </div>
</div>
EOF;
        $m = new \Mustache_Engine();
        echo $m->render($template, $data);
    }
    protected function render_badge($coin_info, $coin_logo, $convert_info, $quotes)
    {
        $fields = ['percent_change_1h' => __('Percent Change 1 Hour', 'dynamic-content-for-elementor'), 'percent_change_24h' => __('Percent Change 24 Hours', 'dynamic-content-for-elementor'), 'percent_change_7d' => __('Percent Change 7 Days', 'dynamic-content-for-elementor'), 'percent_change_30d' => __('Percent Change 30 Days', 'dynamic-content-for-elementor'), 'market_cap' => __('Market Cap', 'dynamic-content-for-elementor')];
        $pair = $coin_info['symbol'] . ' / ' . $convert_info['symbol'];
        $price = \number_format_i18n($quotes['price'], 2);
        $list_items = [];
        foreach ($fields as $key => $label) {
            $list_items[] = ['label' => $label, 'content' => \number_format_i18n($quotes[$key], 2)];
        }
        $this->render_template(['coin_name' => $coin_info['name'], 'coin_logo' => $coin_logo, 'pair' => $pair, 'latest_price' => $price, 'list_items' => $list_items]);
        $this->start_controls_section('section_badge_style', ['label' => __('Badge Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em', 'rem'], 'selectors' => ['{{WRAPPER}} .dce-cryptobadge-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em', 'rem'], 'selectors' => ['{{WRAPPER}} .dce-cryptobadge-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}}']);
        $this->add_control('bacground_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-cryptobadge-wrapper' => 'background-color: {{VALUE}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $coin_id = $this->get_settings('coin_id');
        $convert_id = $this->get_settings('convert_id');
        $cache_age = $this->get_settings('cache_age');
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        try {
            $quotes = $crypto->get_coin_quote($coin_id, $convert_id, $cache_age);
            $coin_info = $crypto->get_fiat_and_crypto_info($coin_id);
            $convert_info = $crypto->get_fiat_and_crypto_info($convert_id);
            $coin_logo = $crypto->get_coin_logo($coin_id);
        } catch (CryptocurrencyApiError $e) {
            $quotes = 'NA';
            $coin_info = 'NA';
            $convert_info = 'NA';
            $coin_logo = 'NA';
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo $e->getMessage();
            }
        }
        $this->render_badge($coin_info, $coin_logo, $convert_info, $quotes);
    }
}
