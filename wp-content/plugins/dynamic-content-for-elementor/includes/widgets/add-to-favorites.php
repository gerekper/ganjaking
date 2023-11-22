<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
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
class AddToFavorites extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return void
     */
    public function run_once()
    {
        add_shortcode('dce-favorites', function ($atts = [], $content = null) {
            $key = $atts['key'] ?? 'my_favorites';
            $get = $atts['get'] ?? '';
            if ($get === 'count') {
                $favs = get_user_meta(get_current_user_id(), $key, \true);
                if (\is_array($favs)) {
                    return \count($favs);
                }
                return '';
            }
            return '';
        });
    }
    public function get_style_depends()
    {
        return ['dce-add-to-favorites'];
    }
    public function get_script_depends()
    {
        return ['dce-cookie'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => __('Settings', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_favorite_scope', ['label' => __('Scope', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => __('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-cookie'], 'user' => ['title' => __('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user'], 'global' => ['title' => __('Global', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-globe']], 'toggle' => \false, 'default' => 'user']);
        $this->add_control('dce_favorite_key', ['label' => __('Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites', 'description' => __('The unique name that identifies the favorites in user meta, cookies or options. Pay attention when setting it because if you change it, you will lose the preferences saved until now', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_sticky' => '']]);
        $this->add_control('dce_favorite_remove', ['label' => __('Can remove', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('dce_favorite_counter', ['label' => __('Show counter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_favorite_scope' => ['user', 'cookie']]]);
        $this->add_control('dce_favorite_counter_icon', ['label' => __('Icon Counter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true, 'condition' => ['dce_favorite_scope' => ['user', 'cookie'], 'dce_favorite_counter!' => '']]);
        $this->add_control('dce_favorite_sticky', ['label' => __('Save as Sticky Posts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_favorite_scope' => 'global']]);
        $this->start_controls_tabs('repe_tabs');
        $this->start_controls_tab('add_repe_tab', ['label' => __('Add', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_favorite_title_add', ['label' => __('Title Add', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Add to my Favorites', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_favorite_icon_add', ['label' => __('Icon Add', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true]);
        $this->end_controls_tab();
        $this->start_controls_tab('remove_repe_tab', ['label' => __('Remove', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_remove!' => '']]);
        $this->add_control('dce_favorite_title_remove', ['label' => __('Title Remove', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Remove from my Favorites', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_favorite_icon_remove', ['label' => __('Icon Remove', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('dce_favorite_cookie_days', ['label' => __('Cookie expiration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 365, 'min' => 0, 'description' => __('Value is in days. Set 0 or empty for session duration.', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_scope' => 'cookie']]);
        $this->add_control('dce_favorite_cookie_cache', ['label' => __('Cache', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_favorite_scope' => 'cookie']]);
        $this->end_controls_section();
        $this->start_controls_section('section_button', ['label' => __('Button', 'dynamic-content-for-elementor')]);
        $this->add_control('button_type', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['' => __('Default', 'dynamic-content-for-elementor'), 'info' => __('Info', 'dynamic-content-for-elementor'), 'success' => __('Success', 'dynamic-content-for-elementor'), 'warning' => __('Warning', 'dynamic-content-for-elementor'), 'danger' => __('Danger', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-button-']);
        $this->add_control('size', ['label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $this->add_control('icon_align', ['label' => __('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'left', 'options' => ['left' => ['title' => __('Before', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'right' => ['title' => __('After', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'toggle' => \false]);
        $this->add_responsive_control('icon_size', ['label' => __('Icon size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 200, 'min' => 10]], 'selectors' => ['{{WRAPPER}} .elementor-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('icon_indent', ['label' => __('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('counter_align', ['label' => __('Counter Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'left', 'options' => ['left' => ['title' => __('Before', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'right' => ['title' => __('After', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'toggle' => \false, 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-button-counter' => 'float: {{VALUE}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'border-right: 4px solid;', '{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'border-left: 4px solid;'], 'render_type' => 'template', 'condition' => ['dce_favorite_counter!' => '']]);
        $this->add_control('counter_separator_width', ['label' => __('Separator width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 3], 'range' => ['px' => ['max' => 10, 'min' => 0]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'border-left-width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'border-right-width: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_favorite_counter!' => '']]);
        $this->add_control('counter_padding', ['label' => __('Counter Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'default' => ['size' => 5], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'padding-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'padding-right: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_favorite_counter!' => '']]);
        $this->add_control('counter_indent', ['label' => __('Counter Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'default' => ['size' => 5], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_favorite_counter!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_visitors', ['label' => __('Visitors', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_scope' => 'user', 'dce_favorite_key!' => 'dce_wishlist']]);
        $this->add_control('dce_favorite_visitor_hide', ['label' => __('Hide Button for non-logged-in users', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dce_favorite_visitor_login', ['label' => __('Login URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'default' => ['url' => wp_login_url(), 'is_external' => \false, 'nofollow' => \false], 'condition' => ['dce_favorite_visitor_hide' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_messages', ['label' => __('Messages', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_favorite_msg_add_enable', ['label' => __('Enable Add success message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('dce_favorite_msg_add', ['label' => __('Add success message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Post saved in your favorites', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_msg_add_enable!' => '']]);
        $this->add_control('dce_favorite_msg_remove_enable', ['label' => __('Enable Remove success message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_favorite_remove!' => '']]);
        $this->add_control('dce_favorite_msg_remove', ['label' => __('Remove success message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Post removed from your favorites', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_msg_remove_enable!' => '', 'dce_favorite_remove!' => '']]);
        $this->add_control('dce_favorite_msg_floating', ['label' => __('Floating message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'selectors' => ['{{WRAPPER}} .dce-notice' => 'position: fixed; display: block; z-index: 100;']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'prefix_class' => 'elementor%s-align-', 'default' => '']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab('tab_button_normal', ['label' => __('Add', 'dynamic-content-for-elementor')]);
        $this->add_control('button_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_remove', ['label' => __('Remove', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_remove!' => '']]);
        $this->add_control('button_text_color_remove', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.dce-add-to-favorite-remove' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('background_color_remove', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.dce-add-to-favorite-remove' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_disabled', ['label' => __('Disabled', 'dynamic-content-for-elementor'), 'condition' => ['dce_favorite_remove' => '']]);
        $this->add_control('button_text_color_disabled', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.dce-add-to-favorite-disabled' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('background_color_disabled', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.dce-add-to-favorite-disabled' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};', '{{WRAPPER}} a.elementor-button:hover svg, {{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} a.elementor-button:focus svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};']]);
        $this->add_control('button_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};']]);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'prefix_class' => 'dce-elementor-animation-']);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border', 'selector' => '{{WRAPPER}} .elementor-button', 'separator' => 'before']);
        $this->add_control('border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->add_responsive_control('text_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_messages_style', ['label' => __('Messages', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'message_typography', 'selector' => '{{WRAPPER}} .elementor-message']);
        $this->add_control('success_message_color', ['label' => __('Success Message Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-success' => 'color: {{COLOR}};']]);
        $this->add_control('error_message_color', ['label' => __('Error Message Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-danger' => 'color: {{COLOR}};']]);
        $this->add_control('message_full_width', ['label' => __('Extend to Full Window Size (100%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'selectors' => ['{{WRAPPER}} .dce-notice' => 'width: 100%; left: 0;'], 'condition' => ['dce_favorite_msg_floating!' => '']]);
        $this->add_control('message_align', ['label' => __('Horizontal Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'right', 'condition' => ['dce_favorite_msg_floating!' => '', 'message_full_width' => '']]);
        $this->add_control('message_valign', ['label' => __('Vertical alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'middle' => ['title' => __('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top']], 'default' => 'bottom', 'condition' => ['dce_favorite_msg_floating!' => '']]);
        $this->add_control('message_padding', ['label' => __('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'default' => ['top' => '15', 'right' => '40', 'bottom' => '15', 'left' => '15', 'unit' => 'px', 'isLinked' => \false], 'selectors' => ['{{WRAPPER}} .dce-notice' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('message_margin', ['label' => __('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-notice' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'message_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'placeholder' => '1px', 'selector' => '{{WRAPPER}} .dce-notice']);
        $this->add_control('message_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-notice' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['label' => __('Message shadow', 'dynamic-content-for-elementor'), 'name' => 'message_box_shadow', 'selector' => '{{WRAPPER}} .dce-notice']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (empty($settings['dce_favorite_key'])) {
            Helper::notice('', __('Please set a key for your favorites', 'dynamic-content-for-elementor'));
            return;
        }
        $user_id = get_current_user_id();
        if (!$user_id && $settings['dce_favorite_scope'] == 'user' && $settings['dce_favorite_visitor_hide']) {
            return;
        }
        $post_ID = apply_filters('wpml_object_id', get_the_ID(), get_post_type(), \true);
        // Don't show Add to Wishlist if the post isn't a product
        if ('dce_wishlist' === $settings['dce_favorite_key'] && 'product' !== get_post_type($post_ID)) {
            return;
        }
        $element_ID = $this->get_id();
        $act_add = \false;
        $act_remove = \false;
        $act = $this->update_list($element_ID, $post_ID);
        if ($act) {
            if ('add' === $act) {
                $act_add = \true;
            }
            if ('remove' === $act) {
                $act_remove = \true;
            }
        }
        $this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');
        $this->add_render_attribute('button', 'class', 'dce-button');
        $this->add_render_attribute('button', 'class', 'elementor-button');
        $this->add_render_attribute('button', 'class', 'dce-favorite-btn-' . $element_ID . '-' . $post_ID);
        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', $settings['button_css_id']);
        }
        if (!empty($settings['size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
        }
        $this->add_render_attribute('wrapper', 'class', 'btn-group');
        $this->add_render_attribute('wrapper', 'role', 'group');
        $this->add_render_attribute(['content-wrapper' => ['class' => 'elementor-button-content-wrapper'], 'icon-align' => ['class' => ['elementor-button-icon', 'elementor-align-icon-' . $settings['icon_align']]], 'text' => ['class' => 'elementor-button-text']]);
        $this->add_inline_editing_attributes('text', 'none');
        if (!empty($settings['dce_favorite_counter'])) {
            $this->add_render_attribute(['counter-align' => ['class' => ['elementor-button-counter', 'elementor-align-counter-' . $settings['counter_align']]]]);
        }
        if ($settings['dce_favorite_scope'] == 'global' && $settings['dce_favorite_sticky']) {
            $settings['dce_favorite_key'] = 'sticky_posts';
        }
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('wrapper');
        ?>>

			<?php 
        $list = ['dce_favorite_title_add' => wp_kses_post($settings['dce_favorite_title_add']), 'dce_favorite_icon_add' => $settings['dce_favorite_icon_add'], 'dce_favorite_title_remove' => wp_kses_post($settings['dce_favorite_title_remove']), 'dce_favorite_icon_remove' => $settings['dce_favorite_icon_remove'], 'dce_favorite_key' => sanitize_text_field($settings['dce_favorite_key'])];
        $favorite_value = $this->get_favorite_value($list['dce_favorite_key'], $settings['dce_favorite_scope']);
        $is_favorite = $act_add;
        if ($favorite_value && \in_array($post_ID, $favorite_value) && !$act_remove && !$settings['dce_favorite_cookie_cache']) {
            $is_favorite = \true;
        }
        $this->add_render_attribute('button', 'class', 'dce-add-to-favorite-button');
        if ($is_favorite) {
            $act = 'remove';
            $act_alt = 'add';
        } else {
            $act = 'add';
            $act_alt = 'remove';
        }
        $icon = 'dce_favorite_icon_' . $act;
        $title = 'dce_favorite_title_' . $act;
        $icon_alt = 'dce_favorite_icon_' . $act_alt;
        $title_alt = 'dce_favorite_title_' . $act_alt;
        $this->add_render_attribute('button', 'class', 'dce-add-to-favorite-' . $act);
        if ($is_favorite && !$settings['dce_favorite_remove']) {
            $this->add_render_attribute('button', 'class', 'elementor-button-disabled');
            $this->add_render_attribute('button', 'class', 'dce-add-to-favorite-disabled');
            $this->add_render_attribute('button', 'href', '#');
            $this->add_render_attribute('button', 'onclick', 'return false;');
        } else {
            if (!$user_id && $settings['dce_favorite_scope'] == 'user' && !$settings['dce_favorite_visitor_hide']) {
                $btn_url = $settings['dce_favorite_visitor_login']['url'];
                if ($settings['dce_favorite_visitor_login']['nofollow']) {
                    $this->add_render_attribute('button', 'rel', 'nofollow');
                }
                if ($settings['dce_favorite_visitor_login']['is_external']) {
                    $this->add_render_attribute('button', 'target', '_blank');
                }
            } else {
                $nonce = wp_create_nonce('dce_add_to_favorites');
                $btn_url = admin_url('admin-ajax.php?action=dce_add_to_favorites');
                $btn_url = add_query_arg('nonce', $nonce, $btn_url);
                $btn_url = add_query_arg('eid', $element_ID, $btn_url);
                $btn_url = add_query_arg('dce_list', $list['dce_favorite_key'], $btn_url);
                $btn_url = add_query_arg('dce_post_id', $post_ID, $btn_url);
                $btn_url = add_query_arg('time', \time(), $btn_url);
            }
            $this->add_render_attribute('button', 'href', $btn_url);
            $this->add_render_attribute('button', 'class', 'elementor-button-link');
        }
        $this->add_render_attribute('button', 'rel', apply_filters('dynamicooo/add-to-favorites/rel', 'nofollow'));
        if ($settings['hover_animation']) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }
        ?>
			<a <?php 
        echo $this->get_render_attribute_string('button');
        ?>>
				<span <?php 
        echo $this->get_render_attribute_string('content-wrapper');
        ?>>
					<?php 
        if (!empty($settings['dce_favorite_counter'])) {
            if ($settings['dce_favorite_scope'] == 'user') {
                $counter = self::get_user_counter($list['dce_favorite_key']);
            }
            if ($settings['dce_favorite_scope'] == 'cookie') {
                $counter = self::get_cookie_counter($list['dce_favorite_key'], $post_ID);
            }
            if ($counter || \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
							<span <?php 
                echo $this->get_render_attribute_string('counter-align');
                ?>>
							<?php 
                if (!empty($settings['dce_favorite_counter_icon']['value'])) {
                    Icons_Manager::render_icon($settings['dce_favorite_counter_icon'], ['aria-hidden' => 'true']);
                }
                ?> <?php 
                echo $counter;
                ?>
							</span>
							<?php 
            }
        }
        $icon_align = $this->get_render_attribute_string('icon-align');
        $icon_align_alt = \str_replace('class="', 'class="dce-add-to-favorite-icon-' . $act_alt . ' ', $icon_align);
        $icon_align = \str_replace('class="', 'class="dce-add-to-favorite-icon-' . $act . ' ', $icon_align);
        $text = $this->get_render_attribute_string('text');
        $text_alt = \str_replace('class="', 'class="dce-add-to-favorite-text-' . $act_alt . ' ', $text);
        $text = \str_replace('class="', 'class="dce-add-to-favorite-text-' . $act . ' ', $text);
        ?>
							<?php 
        if (!empty($list[$icon]['value'])) {
            ?>
						<span <?php 
            echo $icon_align;
            ?>>
								<?php 
            Icons_Manager::render_icon($list[$icon], ['aria-hidden' => 'true']);
            ?>
						</span>
								<?php 
        }
        ?>
					<?php 
        if (!empty($list[$icon_alt]['value'])) {
            ?>
						<span <?php 
            echo $icon_align_alt;
            ?>>
						<?php 
            Icons_Manager::render_icon($list[$icon_alt], ['aria-hidden' => 'true']);
            ?>
						</span>
						<?php 
        }
        ?>
			<?php 
        if (!empty($list[$title])) {
            ?>
						<span <?php 
            echo $text;
            ?>><?php 
            echo $list[$title];
            ?></span>
				<?php 
        }
        ?>
			<?php 
        if (!empty($list[$title_alt])) {
            ?>
						<span <?php 
            echo $text_alt;
            ?>><?php 
            echo $list[$title_alt];
            ?></span>
				<?php 
        }
        ?>
				</span>
			</a>
			<?php 
        if ($settings['hover_animation']) {
            ?>
			<script>
				jQuery(function () {
					jQuery('.dce-favorite-btn-<?php 
            echo $element_ID;
            ?>-<?php 
            echo $post_ID;
            ?>')
						.mouseover(
							function() {
								jQuery(this).addClass('elementor-animation-<?php 
            echo $settings['hover_animation'];
            ?>');
							}
						).mouseout(
							function(){
								jQuery(this).removeClass('elementor-animation-<?php 
            echo $settings['hover_animation'];
            ?>');
							}
						);
				});
			</script>
				<?php 
        }
        $btn_selector = '".elementor-element-' . $this->get_id() . ' .dce-favorite-btn-' . $element_ID . '-' . $post_ID . '"';
        if ($settings['dce_favorite_scope'] != 'user' || $settings['dce_favorite_scope'] == 'user' && get_current_user_id()) {
            ?>
			<script>
				jQuery(function () {
					// The one below are jQuery event namespaces. We do off-on to make sure handler is attached once. And we don't disturb other potential handlers.
					jQuery(<?php 
            echo $btn_selector;
            ?>).off('click.dce-favorite').on('click.dce-favorite', function () {
						if (jQuery(this).hasClass('dce-add-to-favorite-add')) {
							jQuery(this).removeClass('dce-add-to-favorite-add').addClass('dce-add-to-favorite-remove');
							jQuery(this).closest('.elementor-widget-dce-add-to-favorites').find('.dce-notice-favorite-add').show();
							if (Boolean(<?php 
            echo !empty($settings['dce_favorite_counter']);
            ?>)) {
								if (jQuery('span').hasClass('elementor-button-counter')) {
									jQuery(this).find('span.elementor-button-counter').text(' ' + parseInt(parseInt(jQuery(this).find('span.elementor-button-counter').text()) + 1));
									<?php 
            if (!empty($settings['dce_favorite_counter_icon']['value'])) {
                ?>
										jQuery(this).find('span.elementor-button-counter').prepend('<?php 
                Icons_Manager::render_icon($settings['dce_favorite_counter_icon'], ['aria-hidden' => 'true']);
                ?>');
										jQuery('span.elementor-button-counter').addClass('<?php 
                echo 'elementor-align-counter-' . $settings['counter_align'];
                ?>');
										<?php 
            }
            ?>
								} else {
									jQuery(this).find('.elementor-button-content-wrapper')
									.append('<span class="elementor-button-counter elementor-align-counter-<?php 
            echo $settings['counter_align'];
            ?>">');
									jQuery(this).find('span.elementor-button-counter').text(' ' + 1);
									<?php 
            if (!empty($settings['dce_favorite_counter_icon']['value'])) {
                ?>
										jQuery(this).find('span.elementor-button-counter').prepend('<?php 
                Icons_Manager::render_icon($settings['dce_favorite_counter_icon'], ['aria-hidden' => 'true']);
                ?>');
										jQuery('span.elementor-button-counter').addClass('<?php 
                echo 'elementor-align-counter-' . $settings['counter_align'];
                ?>');
										<?php 
            }
            ?>
								}
							}
						} else {
							jQuery(this).addClass('dce-add-to-favorite-add').removeClass('dce-add-to-favorite-remove');
							jQuery(this).closest('.elementor-widget-dce-add-to-favorites').find('.dce-notice-favorite-remove').show();
							if (parseInt(jQuery(this).find('span.elementor-button-counter').text()) == 1){
								jQuery(this).find('span.elementor-button-counter').remove();
							} else {
								jQuery(this).find('.elementor-button-counter').text(' ' + (parseInt(jQuery(this).find('.elementor-button-counter').text()) - 1));
								<?php 
            if (!empty($settings['dce_favorite_counter_icon']['value'])) {
                ?>
									jQuery(this).find('span.elementor-button-counter').prepend('<?php 
                Icons_Manager::render_icon($settings['dce_favorite_counter_icon'], ['aria-hidden' => 'true']);
                ?>');
									<?php 
            }
            ?>
							}
						}
						if (jQuery(this).hasClass('dce-add-to-favorite-remove')) {
							dce_act = 'add';
						} else {
							dce_act = 'remove';
						}
						jQuery(<?php 
            echo $btn_selector;
            ?>).css('pointer-events', 'none');
						jQuery.get(jQuery(this).attr('href') + '&dce_act=' + dce_act + '&time=' + Date.now(), function (data) {
							var my_btn = jQuery(<?php 
            echo $btn_selector;
            ?>);
				<?php 
            if ($settings['dce_favorite_cookie_cache']) {
                ?>
								var counter = parseInt(jQuery(data).find('.dce-favorite-btn-<?php 
                echo $this->get_id();
                ?>-<?php 
                echo $post_ID;
                ?> .elementor-button-counter').text());
								if (counter) {
									my_btn.find('.elementor-button-counter').text(counter);
								}
					<?php 
            }
            ?>

							my_btn.css('pointer-events', 'auto');
						});
						return false;
					});
				});
			</script>
				<?php 
        }
        if ($settings['dce_favorite_scope'] == 'cookie' && $settings['dce_favorite_cookie_cache']) {
            ?>
				<script>
					jQuery(function () {
						var cookieValue = dceGetCookie("<?php 
            echo $list['dce_favorite_key'];
            ?>");
						if (cookieValue) {
							var postIds = cookieValue.split(',');
							if (postIds.includes("<?php 
            echo $post_ID;
            ?>")) {
								jQuery(<?php 
            echo $btn_selector;
            ?>).removeClass('dce-add-to-favorite-add').addClass('dce-add-to-favorite-remove');
							}
						}
					});
				</script>
				<?php 
        }
        ?>
		</div>
		<?php 
        $modal_class = !empty($settings['dce_favorite_msg_floating']) ? ' dce-modal' : '';
        $modal_class = $settings['dce_favorite_msg_floating'] && !empty($settings['message_align']) ? $modal_class . ' modal-' . $settings['message_align'] : $modal_class;
        $modal_class = $settings['dce_favorite_msg_floating'] && !empty($settings['message_valign']) ? $modal_class . ' modal-' . $settings['message_valign'] : $modal_class;
        ?>
		<?php 
        if ($settings['dce_favorite_msg_add_enable']) {
            ?>
			<div class="elementor-message elementor-message-success dce-notice dce-notice-favorite dce-notice-favorite-add elementor-alert elementor-alert-success<?php 
            echo $modal_class;
            ?>"<?php 
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?> style="display:none;"<?php 
            }
            ?>>
				<span class="elementor-alert-description-asd"><?php 
            echo $settings['dce_favorite_msg_add'];
            ?></span>
				<button type="button" class="elementor-alert-dismiss" onClick="jQuery(this).parent().fadeOut();">
					<span aria-hidden="true">&times;</span>
					<span class="elementor-screen-only"><?php 
            echo __('Dismiss alert', 'dynamic-content-for-elementor');
            ?></span>
				</button>
			</div>
			<?php 
        }
        if ($settings['dce_favorite_msg_remove_enable']) {
            ?>
			<div class="elementor-message elementor-message-danger dce-notice dce-notice-favorite dce-notice-favorite-remove elementor-alert elementor-alert-warning<?php 
            echo $modal_class;
            ?>"<?php 
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?> style="display:none;"<?php 
            }
            ?>>
				<span class="elementor-alert-description-asd"><?php 
            echo $settings['dce_favorite_msg_remove'];
            ?></span>
				<button type="button" class="elementor-alert-dismiss" onClick="jQuery(this).parent().fadeOut();">
					<span aria-hidden="true">&times;</span>
					<span class="elementor-screen-only"><?php 
            echo __('Dismiss alert', 'dynamic-content-for-elementor');
            ?></span>
				</button>
			</div>
			<?php 
        }
    }
    public function update_list($element_ID, $post_ID, $list_key = '')
    {
        $settings = $this->get_settings_for_display();
        $act_add = $act_remove = \false;
        if (!empty($_GET['eid']) && $_GET['eid'] == $element_ID) {
            if (!empty($_GET['dce_list'])) {
                if (!empty($_GET['dce_post_id']) && $_GET['dce_post_id'] == $post_ID) {
                    $list_key = !empty($_GET['dce_list']) ? Helper::recursive_sanitize_text_field($_GET['dce_list']) : $list_key;
                    $favorite_value = $this->get_favorite_value($list_key, $settings['dce_favorite_scope']);
                    $dce_act = !empty($_GET['dce_act']) ? sanitize_text_field($_GET['dce_act']) : 'auto';
                    if (!empty($favorite_value) && \is_array($favorite_value)) {
                        $favorite_pos = \array_search($post_ID, $favorite_value);
                        switch ($dce_act) {
                            case 'add':
                                if ($favorite_pos === \false) {
                                    $favorite_value[] = $post_ID;
                                    $act_add = \true;
                                }
                                break;
                            case 'remove':
                                if ($favorite_pos !== \false) {
                                    if ($settings['dce_favorite_remove']) {
                                        unset($favorite_value[$favorite_pos]);
                                        $act_remove = \true;
                                    }
                                }
                                break;
                            default:
                                if ($favorite_pos === \false) {
                                    $favorite_value[] = $post_ID;
                                    $act_add = \true;
                                } else {
                                    if ($settings['dce_favorite_remove']) {
                                        unset($favorite_value[$favorite_pos]);
                                        $act_remove = \true;
                                    }
                                }
                        }
                    } else {
                        if ($dce_act != 'remove') {
                            $favorite_value = [$post_ID];
                            $act_add = \true;
                        }
                    }
                    switch ($settings['dce_favorite_scope']) {
                        case 'user':
                            $user_id = get_current_user_id();
                            update_user_meta($user_id, $list_key, $favorite_value);
                            break;
                        case 'global':
                            update_option($list_key, $favorite_value);
                            break;
                        case 'cookie':
                            $favorite_value = \implode(',', $favorite_value);
                            $cookie_days = $settings['dce_favorite_cookie_days'] ? \time() + 86400 * $settings['dce_favorite_cookie_days'] : 0;
                            // 86400 = 1 day
                            $http_host = $_SERVER['HTTP_HOST'] == 'localhost' ? '' : sanitize_text_field($_SERVER['HTTP_HOST']);
                            @\setcookie($list_key, $favorite_value, $cookie_days, '/', $http_host);
                            if ($settings['dce_favorite_counter']) {
                                $cookies = get_option('dce_favorite_cookies', []);
                                if (isset($cookies[$list_key][$post_ID])) {
                                    if ($act_add) {
                                        $cookies[$list_key][$post_ID]++;
                                    } else {
                                        $cookies[$list_key][$post_ID]--;
                                    }
                                } else {
                                    $cookies[$list_key][$post_ID] = 1;
                                }
                                update_option('dce_favorite_cookies', $cookies);
                            }
                            break;
                    }
                }
            }
        }
        if ($act_add) {
            return 'add';
        }
        if ($act_remove) {
            return 'remove';
        }
        return \false;
    }
    public function get_user_favorites($meta_key = '', $post_ID = 0, $user_ID = 0)
    {
        if (!$post_ID) {
            $post_ID = get_the_ID();
        }
        if (!$meta_key) {
            $settings = $this->get_settings_for_display();
            $meta_key = sanitize_text_field($settings['dce_favorite_key']);
        }
        $args = ['meta_query' => [['key' => $meta_key, 'value' => \sprintf(':"%s";', $post_ID), 'compare' => 'LIKE']]];
        $user_query = new \WP_User_Query($args);
        // User Loop
        $count = 0;
        if (!empty($user_query->get_results())) {
            foreach ($user_query->get_results() as $user) {
                $user_favorites = get_user_meta($user->ID, $meta_key, \true);
                if (\in_array($post_ID, $user_favorites)) {
                    $count++;
                }
            }
        }
        return $count;
    }
    public function get_favorite_value($list_key = '', $scope = '')
    {
        $favorite_value = [];
        switch ($scope) {
            case 'user':
                $user_id = get_current_user_id();
                $favorite_value = get_user_meta($user_id, $list_key, \true);
                break;
            case 'global':
                $favorite_value = get_option($list_key);
                break;
            case 'cookie':
                if (isset($_COOKIE[$list_key])) {
                    $favorite_value = Helper::str_to_array(',', sanitize_text_field($_COOKIE[$list_key]));
                }
                break;
        }
        if (Helper::is_wpml_active()) {
            return Helper::wpml_translate_object_id($favorite_value);
        }
        return $favorite_value;
    }
    public static function get_user_counter($list_key = '')
    {
        global $wpdb;
        $sql = $wpdb->prepare('SELECT COUNT(user_id) as ucount FROM ' . $wpdb->prefix . 'usermeta um WHERE meta_key LIKE %s AND meta_value LIKE %s', esc_sql($list_key), '%i:' . esc_sql(get_the_ID()) . ';%');
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            return \reset($results)->ucount;
        }
        return 0;
    }
    public static function get_cookie_counter($list_key = '', $post_ID = 0)
    {
        $cookies = get_option('dce_favorite_cookies', []);
        if (isset($cookies[$list_key][$post_ID])) {
            return \intval($cookies[$list_key][$post_ID]);
        }
        return 0;
    }
}
