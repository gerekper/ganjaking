<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class AdvancedFilteringSearchAndFilterPro extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_searchfilter', ['label' => $this->get_title()]);
        $this->add_control('search_filter_id', ['label' => __('Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'placeholder' => __('Select the filter', 'dynamic-content-for-elementor'), 'query_type' => 'posts', 'object_type' => 'search-filter-widget', 'dynamic' => ['active' => \true]]);
        $this->add_responsive_control('style_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .searchandfilter > ul > li' => 'text-align: {{VALUE}};'], 'default' => '']);
        $this->add_control('ul_padding', ['label' => __('ul Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => '0', 'selectors' => ['{{WRAPPER}} .searchandfilter > ul' => 'padding: {{VALUE}}; margin: 0']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (is_admin()) {
            require_once plugin_dir_path(SEARCH_FILTER_PRO_BASE_PATH) . 'public/class-search-filter.php';
            // @phpstan-ignore-line
            \Search_Filter::get_instance();
            // @phpstan-ignore-line
        }
        $search_filter_id = $this->get_settings_for_display('search_filter_id');
        $shortcode = '[searchandfilter id="' . $search_filter_id . '"]';
        echo do_shortcode(shortcode_unautop($shortcode));
    }
}
