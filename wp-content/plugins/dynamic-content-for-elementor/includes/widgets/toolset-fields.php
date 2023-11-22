<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class ToolsetFields extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['elementor-dialog'];
    }
    public function get_style_depends()
    {
        return ['dce-toolset'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => __('Toolset', 'dynamic-content-for-elementor')]);
        $this->add_control('toolset_field_list', ['label' => __('Fields list', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'empty', 'groups' => $this->get_toolset_fields()]);
        $this->add_control('toolset_field_type', ['label' => __('Field type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 0, 'options' => ['empty' => __('Select for options', 'dynamic-content-for-elementor'), 'textfield' => __('Textfield', 'dynamic-content-for-elementor'), 'url' => __('URL', 'dynamic-content-for-elementor'), 'phone' => __('Phone', 'dynamic-content-for-elementor'), 'email' => __('Email', 'dynamic-content-for-elementor'), 'textarea' => __('Textarea', 'dynamic-content-for-elementor'), 'wysiwyg' => __('WYSIWYG', 'dynamic-content-for-elementor'), 'image' => __('Image', 'dynamic-content-for-elementor'), 'date' => __('Date', 'dynamic-content-for-elementor'), 'numeric' => __('Numeric', 'dynamic-content-for-elementor'), 'video' => __('Video', 'dynamic-content-for-elementor')]]);
        $this->add_control('toolset_field_hide', ['label' => __('Hide if empty', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => __('Hide the field in front end layer', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
        $this->start_controls_section('section_settings', ['label' => __('Settings', 'dynamic-content-for-elementor'), 'condition' => ['toolset_field_type!' => 'video']]);
        $this->add_control('toolset_text_before', ['label' => __('Text before', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type!' => 'video']]);
        $this->add_control('toolset_text_after', ['label' => __('Text after', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type!' => 'video']]);
        $this->add_control('toolset_url_enable', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['toolset_field_type' => 'url']]);
        $this->add_control('toolset_url_custom_text', ['label' => __('Custom URL text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type' => 'url']]);
        $this->add_control('toolset_url_target', ['label' => __('Target type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['_self' => '_self', '_blank' => '_blank', '_parent' => '_parent', '_top' => '_top'], 'default' => '_self', 'condition' => ['toolset_field_type' => 'url']]);
        $this->add_control('toolset_date_format', ['label' => __('Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 0, 'options' => ['default' => __('Default from WordPress settings', 'dynamic-content-for-elementor'), '%Y%m%d' => __('YYYYMMDD', 'dynamic-content-for-elementor'), '%Y-%m-%d' => __('YYYY-MM-DD', 'dynamic-content-for-elementor'), '%d/%m/%Y' => __('DD/MM/YYYY', 'dynamic-content-for-elementor'), '%d-%m-%Y' => __('DD-MM-YYYY', 'dynamic-content-for-elementor'), '%Y-%m-%d %H:%M:%S' => __('YYYY-MM-DD H:M:S', 'dynamic-content-for-elementor'), '%d/%m/%Y %H:%M:%S' => __('DD/MM/YY H:M:S', 'dynamic-content-for-elementor'), '%d/%m/%y' => __('D/M/Y', 'dynamic-content-for-elementor'), '%d-%m-%y' => __('D-M-Y', 'dynamic-content-for-elementor'), '%I:%M %p' => __('H:M (12 hours)', 'dynamic-content-for-elementor'), '%A %m %B %Y' => __('Full date', 'dynamic-content-for-elementor'), '%A %m %B %Y at %H:%M' => __('Full date with hours', 'dynamic-content-for-elementor'), 'timestamp' => __('Timestamp', 'dynamic-content-for-elementor'), 'custom' => __('Custom', 'dynamic-content-for-elementor')], 'condition' => ['toolset_field_type' => 'date']]);
        $this->add_control('toolset_date_custom_format', ['label' => __('Custom date format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_date_format' => 'custom'], 'description' => __('See PHP strftime() function reference', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => __('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large', 'condition' => ['toolset_field_type' => 'image']]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'prefix_class' => 'align-dce-', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('use_bg', ['label' => __('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1' => ['title' => __('Yes', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-check'], '0' => ['title' => __('No', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-ban']], 'default' => '0', 'condition' => ['toolset_field_type' => 'image']]);
        $this->add_control('bg_position', ['label' => __('Background position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'top center', 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'top left' => __('Top Left', 'dynamic-content-for-elementor'), 'top center' => __('Top Center', 'dynamic-content-for-elementor'), 'top right' => __('Top Right', 'dynamic-content-for-elementor'), 'center left' => __('Center Left', 'dynamic-content-for-elementor'), 'center center' => __('Center Center', 'dynamic-content-for-elementor'), 'center right' => __('Center Right', 'dynamic-content-for-elementor'), 'bottom left' => __('Bottom Left', 'dynamic-content-for-elementor'), 'bottom center' => __('Bottom Center', 'dynamic-content-for-elementor'), 'bottom right' => __('Bottom Right', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset-bg' => 'background-position: {{VALUE}};'], 'condition' => ['toolset_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_control('bg_extend', ['label' => __('Extend background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'prefix_class' => 'extendbg-', 'condition' => ['toolset_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_responsive_control('height', ['label' => __('Minimum height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 200, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset-bg' => 'min-height: {{SIZE}}{{UNIT}};'], 'condition' => ['toolset_field_type' => 'image', 'use_bg' => '1']]);
        $this->add_control('toolset_phone_number_enable', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('No', 'dynamic-content-for-elementor'), 'label_on' => __('Yes', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['toolset_field_type' => 'phone']]);
        $this->add_control('toolset_phone_number_custom_text', ['label' => __('Custom phone number', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type' => 'phone']]);
        $this->add_control('toolset_email_target', ['label' => __('Link mailto', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Off', 'dynamic-content-for-elementor'), 'label_on' => __('On', 'dynamic-content-for-elementor'), 'condition' => ['toolset_field_type' => 'email']]);
        $this->add_control('toolset_numeric_currency', ['label' => __('Currency', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'condition' => ['toolset_field_type' => 'numeric']]);
        $this->add_control('toolset_currency_symbol', ['label' => __('Currency symbol', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'condition' => ['toolset_field_type' => 'numeric', 'toolset_numeric_currency' => 'yes']]);
        $this->add_control('toolset_currency_symbol_position', ['label' => __('Symbol position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['before' => ['title' => __('Before', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-arrow-left'], 'after' => ['title' => __('After', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-arrow-right']], 'default' => 'before', 'toggle' => \true, 'condition' => ['toolset_field_type' => 'numeric', 'toolset_numeric_currency' => 'yes']]);
        $this->end_controls_section();
        // ------------------------------------------------------------ [ OVERLAY Image ]
        $this->start_controls_section('section_overlay', ['label' => __('Overlay Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['toolset_field_type' => 'image']]);
        $this->add_control('overlay_heading', ['label' => __('Overlay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('use_overlay', ['label' => __('Overlay Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-overlay', 'condition' => ['use_overlay' => 'yes']]);
        $this->end_controls_section();
        // ********************** Section STYLE **********************
        $this->start_controls_section('section_style', ['label' => __('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('tx_heading', ['label' => __('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset .edc-toolset' => 'color: {{VALUE}};'], 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_control('bg_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'background-color: {{VALUE}};'], 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_responsive_control('toolset_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('toolset_space', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('toolset_shift', ['label' => __('Shift', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 180, 'min' => -180, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-toolset' => 'left: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_tx', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-toolset', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'label' => __('Text shadow', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-toolset', 'condition' => ['toolset_field_type' => ['textfield', 'url', 'image', 'phone', 'email', 'textarea', 'wysiwyg', 'date', 'numeric']]]);
        $this->end_controls_section();
    }
    protected function get_toolset_fields()
    {
        $fieldList = array();
        $fieldList[0] = __('Select the field...', 'dynamic-content-for-elementor');
        if (Helper::is_plugin_active('types')) {
            $toolset_groups = wpcf_admin_fields_get_groups();
            foreach ($toolset_groups as $group) {
                $options = array();
                $fields = wpcf_admin_fields_get_fields_by_group($group['id']);
                if (!\is_array($fields)) {
                    continue;
                }
                foreach ($fields as $field_key => $field) {
                    //
                    if (!empty($field['type'])) {
                        $a = array();
                        $a['group'] = $group['slug'];
                        $a['field'] = $field_key;
                        $a['type'] = $field['type'];
                        $index = wp_json_encode($a);
                        $options[wp_json_encode($a)] = $field['name'] . ' (' . $field['type'] . ')';
                    }
                    if (empty($options)) {
                        continue;
                    }
                }
                \array_push($fieldList, ['label' => $group['name'], 'options' => $options]);
            }
        }
        return $fieldList;
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $post_id = get_the_ID();
        $use_bg = $settings['use_bg'];
        $wrap_effect_start = '<div class="mask"><div class="wrap-filters">';
        $wrap_effect_end = '</div></div>';
        $overlay_block = '';
        if ($settings['use_overlay'] == 'yes') {
            $overlay_block = '<div class="dce-overlay"></div>';
        }
        $overlay_hover_block = '<div class="dce-overlay_hover"></div>';
        $f = \json_decode($settings['toolset_field_list']);
        $html = '';
        switch ($f->type) {
            case 'textfield':
            case 'textarea':
                $f->value = types_render_field($f->field);
                $html = '<span class="edc-toolset">' . $f->value . '</span>';
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'wysiwyg':
                $f->value = types_render_field($f->field, array('suppress_filters' => \true));
                $html = $f->value;
                break;
            case 'url':
                $f->value = types_render_field($f->field);
                if (\preg_match('/href="(.*?)" /', $f->value, $match) == 1) {
                    $url = $match[1];
                }
                if (isset($url)) {
                    $text_url = $url;
                }
                if (!empty($settings['toolset_url_custom_text'])) {
                    $text_url = wp_kses_post($settings['toolset_url_custom_text']);
                }
                if ($settings['toolset_url_enable'] && isset($url)) {
                    $html = '<a href="' . $url . '" target="' . $settings['toolset_url_target'] . '"> ' . $text_url . '</a>';
                } else {
                    $html = $text_url;
                }
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'phone':
                $f->value = types_render_field($f->field);
                $text_number = $f->value;
                if (!empty($settings['toolset_phone_number_custom_text'])) {
                    $text_number = $settings['toolset_phone_number_custom_text'];
                }
                if ($settings['toolset_phone_number_enable']) {
                    $html = '<a href="tel:' . \preg_replace('/[^0-9]/', '', $f->value) . '"> ' . $text_number . '</a>';
                } else {
                    $html = $text_number;
                }
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'email':
                $f->value = types_render_field($f->field);
                if ($settings['toolset_email_target']) {
                    $html = $f->value;
                } elseif (\preg_match('/href="mailto:(.*?)" /', $f->value, $match) == 1) {
                    $html = $match[1];
                }
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'image':
                $img_size = $settings['size_size'];
                $f->value = types_render_field($f->field);
                if (\preg_match('/src="(.*?)" /', $f->value, $match) == 1) {
                    $imgSrc = $match[1];
                }
                $img_id = Helper::get_image_id($imgSrc);
                $img_url = Group_Control_Image_Size::get_attachment_image_src($img_id, 'size', $settings);
                if (!$use_bg) {
                    $html = '<div class="toolset-image">' . $wrap_effect_start . '<img src="' . $img_url . '" />' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                } else {
                    $bg_featured_image = '<div class="toolset-image toolset-bg-image">' . $wrap_effect_start . '<figure class="dynamic-content-for-elementor-toolset-bg" style="background-image: url(\'' . $img_url . '\'); background-repeat: no-repeat; background-size: cover;"></figure>' . $wrap_effect_end . $overlay_block . $overlay_hover_block . '</div>';
                    $html = $bg_featured_image;
                }
                break;
            case 'date':
                $f->value = types_render_field($f->field);
                if ($timestamp = types_render_field($f->field, array('format' => 'U', 'style' => 'text'))) {
                    switch ($settings['toolset_date_format']) {
                        case 'default':
                            $data = $f->value;
                            break;
                        case 'timestamp':
                            $data = $timestamp;
                            break;
                        case 'custom':
                            $data = \strftime($settings['toolset_date_custom_format'], $timestamp);
                            break;
                        default:
                            $data = \strftime($settings['toolset_date_format'], $timestamp);
                            break;
                    }
                    $html = '<span class="edc-toolset">' . $data . '</span>';
                    if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                        $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                    }
                }
                break;
            case 'numeric':
                $f->value = types_render_field($f->field);
                $number = $f->value;
                if ($settings['toolset_numeric_currency'] && $settings['toolset_currency_symbol'] != '') {
                    if ($settings['toolset_currency_symbol_position'] == 'before') {
                        $number = $settings['toolset_currency_symbol'] . $number;
                    } else {
                        $number .= $settings['toolset_currency_symbol'];
                    }
                }
                $html = '<span class="edc-toolset">' . $number . '</span>';
                if ($settings['toolset_text_before'] != '' || $settings['toolset_text_after'] != '') {
                    $html = '<span class="tx-before">' . wp_kses_post($settings['toolset_text_before']) . '</span>' . $html . '<span class="tx-after">' . wp_kses_post($settings['toolset_text_after']) . '</span>';
                }
                break;
            case 'video':
                $f->value = types_render_field($f->field);
                $video = $f->value;
                echo '<code>' . \var_export($video, \true) . '</code>';
                break;
        }
        if ($settings['toolset_field_hide'] && empty($f->value) && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $html = '<style>' . $this->get_unique_selector() . '{display:none !important;} </style>';
        }
        switch ($f->type) {
            case 'code':
                $settings['html_tag'] = 'code';
                break;
            case 'image':
                $settings['html_tag'] = 'div';
                break;
            default:
                $settings['html_tag'] = 'div';
                break;
        }
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $render = \sprintf('<%1$s class="dynamic-content-for-elementor-toolset %2$s">', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']), $animation_class);
        $render .= $html;
        $render .= \sprintf('</%s>', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']));
        echo $render;
    }
}
