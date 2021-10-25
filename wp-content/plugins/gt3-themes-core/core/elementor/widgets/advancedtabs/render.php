<?php

use Elementor\Plugin as Elementor_Plugin;

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_AdvancedTabs $widget */

$settings = array();
$data = array();

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_advanced_tabs',
	($settings['gt3_tabs_type'] === 'vertical') ? 'tabs-type_vertical' : '',
));

$elementor = Elementor_Plugin::instance();

if(isset($settings['items']) && is_array($settings['items'])) {
	$uniqid = mt_rand(0, 9999);

	echo '<div data-active-tab="' . esc_attr($settings['gt3_tabs_active']) . '" '. $widget->get_render_attribute_string('wrapper') .'>';
	echo '<div class="gt3_advanced_tabs_nav_wrapper"><ul class="gt3_advanced_tabs_nav">';
	$base_count = 1;
	foreach($settings['items'] as $key=>$item) {
		$tab_nav_content = '';

		if (isset($item['type'])) {

			if ($item['type'] == 'icon') {
				if (!empty($item['icon'])) {
					$tab_nav_content .= '<span class="gt3_tabs_nav__icon '. $item['icon'] . ($item['gt3_tabs_icon_size'] ? ' gt3_tabs_nav__icon--'.$item['gt3_tabs_icon_size'] : '').' "></span>';
				}
			}

			if ($item['type'] == 'image') {
				$tab_nav_content .= '<span class="gt3_tabs_nav__image_container">';
				if (!empty($item['image'])) {
					$tab_nav_content .= '<span class="gt3_tabs_nav__image gt3_tabs_nav__image--front' . ($item['gt3_tabs_icon_size'] ? ' gt3_tabs_nav__icon--' . $item['gt3_tabs_icon_size'] : '') . '"><img src='.$item['image']['url'] . ' ></span>';
				}
				if (!empty($item['image_hover'])) {
					$tab_nav_content .= '<span class="gt3_tabs_nav__image gt3_tabs_nav__image--back' . ($item['gt3_tabs_icon_size'] ? ' gt3_tabs_nav__icon--' . $item['gt3_tabs_icon_size'] : '') . '"><img src=' . $item['image_hover']['url'] . ' ></span>';
				}
				$tab_nav_content .= '</span>';
			}

		}

		if (!empty($item['tab_title'])) {
			$tab_nav_content .= '<span class="gt3_tabs_nav__title">'. esc_html($item['tab_title']). '</span>';
		}

		echo '<li class="elementor-repeater-item-' . $item['_id'].'"><a href="#tab-' . esc_attr($uniqid) . '-' . esc_attr($base_count) . '">' . $tab_nav_content . '</a></li>';
		$base_count++;
	}
	echo '</ul></div>';
	$base_count = 1;
	foreach($settings['items'] as $item) {
		$content = $link = '';

		if (!empty($item['template_id'])) {
			$content = $elementor->frontend->get_builder_content_for_display($item['template_id']);
			/*if ($widget->is_editor && current_user_can('edit_post',$item['template_id'])) {
				$doc = $elementor->documents->get($item['template_id']);
				$link = '<a href="'.$doc->get_edit_url().'" target="_blank" class="edit_template_link">Edit</a>';
			}*/
		}
		echo '<div id="tab-' . esc_attr($uniqid) . '-' . esc_attr($base_count) . '">'.$link.$content.'</div>';
		$base_count++;
	}
	echo '</div>';
}

$widget->print_data_settings($data);

