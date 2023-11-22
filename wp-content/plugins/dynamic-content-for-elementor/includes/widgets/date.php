<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Date extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-date'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_content', ['label' => __('Date', 'dynamic-content-for-elementor')]);
        $this->add_control('date_type', ['label' => __('Date Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['publish' => __('Publish Date', 'dynamic-content-for-elementor'), 'modified' => __('Last Modified Date', 'dynamic-content-for-elementor')], 'default' => 'publish']);
        $this->add_control('html_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'div']);
        $this->add_control('format', ['label' => __('Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'l, j F, Y']);
        $this->add_control('format2', ['label' => __('2 - Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('format3', ['label' => __('3 - Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['format2!' => '']]);
        $this->add_control('date_separator', ['label' => __('Date text separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '', 'condition' => ['format2!' => '']]);
        $this->add_control('date_text_before', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_responsive_control('date_text_before_block', ['label' => __('Before - Inline or Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Inline', 'dynamic-content-for-elementor'), 'label_on' => __('Block', 'dynamic-content-for-elementor'), 'return_value' => 'block', 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-date span.tx-before' => 'display: {{VALUE}};'], 'condition' => ['date_text_before!' => '']]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'post' => 'Post URL', 'custom' => __('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('float', ['label' => __('Float left', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN]);
        $this->add_control('block', ['label' => __('Use Block', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('block', 'dynamic-content-for-elementor'), 'label_off' => __('span', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Date', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('date_sec_1', ['label' => __('Date', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-date' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-date a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-date > *']);
        $this->add_control('date_sec_2', ['label' => __('Date 2', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['format2!' => '']]);
        $this->add_control('color2', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-date .d2' => 'color: {{VALUE}};'], 'condition' => ['format2!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography2', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-date .d2', 'condition' => ['format2!' => '']]);
        $this->add_control('date_sec_3', ['label' => __('Date 3', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['format3!' => '']]);
        $this->add_control('color3', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-date .d3' => 'color: {{VALUE}};'], 'condition' => ['format3!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography3', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-date .d3', 'condition' => ['format3!' => '']]);
        $this->add_control('txbefore_heading', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['date_text_before!' => '']]);
        $this->add_control('tx_before_color', ['label' => __('Text Before Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-date span.tx-before' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-date a span.tx-before' => 'color: {{VALUE}};'], 'condition' => ['date_text_before!' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx_before', 'label' => __('Font Before', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-date span.tx-before', 'condition' => ['date_text_before!' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        // Backwards compatibility check
        if ($settings['date_type']) {
            $date_type = $settings['date_type'];
        } else {
            $date_type = 'publish';
        }
        $date2 = '';
        $date3 = '';
        switch ($date_type) {
            case 'modified':
                $date = get_the_modified_date($settings['format'], $id_page);
                if ($settings['format2'] != '') {
                    $date2 = get_the_modified_date($settings['format2'], $id_page);
                }
                if ($settings['format3'] != '') {
                    $date3 = get_the_modified_date($settings['format3'], $id_page);
                }
                break;
            case 'publish':
            default:
                $date = get_the_date($settings['format'], $id_page);
                if ($settings['format2'] != '') {
                    $date2 = get_the_date($settings['format2'], $id_page);
                }
                if ($settings['format3'] != '') {
                    $date3 = get_the_date($settings['format3'], $id_page);
                }
                break;
        }
        if (empty($date)) {
            return;
        }
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'post':
                $link = esc_url(get_the_permalink($id_page));
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        $target = !empty($settings['link']['is_external']) ? 'target="_blank"' : '';
        $animation_class = '';
        $floatDate = '';
        if ('yes' == $settings['float']) {
            $floatDate = ' in_linea';
        }
        $floatDate = '';
        if ('yes' == $settings['block']) {
            $subtag = 'div';
        } else {
            $subtag = 'span';
        }
        $date_separator1 = '';
        $date_separator2 = '';
        if ($date2 != '') {
            $date_separator1 = '<span class="d-separator">' . wp_kses_post($settings['date_separator']) . '</span>';
        }
        if ($date3 != '') {
            $date_separator2 = '<span class="d-separator">' . wp_kses_post($settings['date_separator']) . '</span>';
        }
        $textBefore = '';
        if ($settings['date_text_before']) {
            $textBefore = '<span class="tx-before">' . wp_kses_post($settings['date_text_before']) . '</span>';
        }
        $allDate = $textBefore . '<' . $subtag . ' class="d1">' . $date . '</' . $subtag . '>' . $date_separator1 . '<' . $subtag . ' class="d2">' . $date2 . '</' . $subtag . '>' . $date_separator2 . '<' . $subtag . ' class="d3">' . $date3 . '</' . $subtag . '>';
        $html = \sprintf('<%1$s class="dynamic-content-for-elementor-date %2$s%3$s">', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']), $animation_class, $floatDate);
        if ($link) {
            $html .= \sprintf('<a href="%1$s" %2$s>%3$s</a>', $link, $target, $allDate);
        } else {
            $html .= $allDate;
        }
        $html .= \sprintf('</%s>', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']));
        echo $html;
    }
}
