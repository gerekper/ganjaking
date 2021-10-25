<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_CustomMeta $widget */

$settings = array(
	'items' => array(
		array(
			'custom_meta_label'   => '',
			'custom_meta_type' => 'type_custom',
			'custom_meta_value' => '',
			'custom_meta_icon' => '',
		),
	),
	'select_layout' => 'vertical',
	'select_alignment' => 'align_left',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$widget->add_render_attribute('wrapper', 'class', array(
	'gt3_meta_values_wrapper',
	$settings['select_layout'],
	$settings['select_alignment'],
));

if(isset($settings['items']) && is_array($settings['items'])) {
	echo '<div '. $widget->get_render_attribute_string('wrapper') .'>';
	foreach($settings['items'] as $item) {

		$item_value = $icon = $item_meta_label = '';

		$post_type = get_post_type();

		$comments_num = get_comments_number(get_the_ID());

		if ($comments_num == 1) {
			$comments_text = esc_html__('comment', 'gt3_themes_core');
		} else {
			$comments_text = esc_html__('comments', 'gt3_themes_core');
		}

		if ($item['custom_meta_type'] == 'type_name') {
			// Name
			$item_value = esc_html(get_the_title());
		} else if ($item['custom_meta_type'] == 'type_category') {
			// Category
			if ($post_type =='post') {
				if (get_the_category()) $categories = get_the_category();
				if (!empty($categories)) {
					$post_categ = '';
					$post_category_compile = '';
					foreach ($categories as $category) {
						$post_categ = $post_categ . '<a href="' . get_category_link($category->term_id) . '">' . $category->cat_name . '</a>' . ', ';
					}
					$post_category_compile .= trim($post_categ, ', ') . '';
				}else{
					$post_category_compile = '';
				}
				$item_value = $post_category_compile;

			} else if ($post_type =='portfolio') {
				$port_cats = wp_get_post_terms(get_the_id(), 'portfolio_category');

				$echo_cats = array();
				if(is_array($port_cats) && !empty($port_cats)) {
					foreach($port_cats as $cat) {
						$echo_cats[] = '<a href="'.esc_url(get_category_link($cat)).'">'.esc_html($cat->name).'</a>';
					}
				}

				$item_value .= implode(', ', $echo_cats);
			} else if ($post_type =='project') {
				$proj_cats = wp_get_post_terms(get_the_id(), 'project_category');

				$echo_cats = array();
				if(is_array($proj_cats) && !empty($proj_cats)) {
					foreach($proj_cats as $cat) {
						$echo_cats[] = '<a href="'.esc_url(get_category_link($cat)).'">'.esc_html($cat->name).'</a>';
					}
				}

				$item_value .= implode(', ', $echo_cats);
			}
		} else if ($item['custom_meta_type'] == 'type_tags') {
			// Tags
			if ($post_type =='post') {
				if (get_the_tags()) $tags = get_the_tags();
				if (!empty($tags)) {
					ob_start();
					the_tags("", '', '');
					$post_tags = ob_get_clean();
					$item_value = $post_tags;
				}
			} else if ($post_type =='portfolio') {
				$port_tags = wp_get_post_terms(get_the_id(), 'portfolio_tag');

				$echo_tags = array();

				if(is_array($port_tags) && !empty($port_tags)) {
					foreach($port_tags as $tag) {
						$echo_tags[] = '<a href="'.esc_url(get_term_link($tag)).'">'.esc_html($tag->name).'</a>';
					}
				}

				$item_value .= implode('', $echo_tags);
			}
		} else if ($item['custom_meta_type'] == 'type_date') {
			// Date
			$item_value = esc_html(get_the_time(get_option( 'date_format' )));
		} else if ($item['custom_meta_type'] == 'type_author') {
			// Author
			$item_value = '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author_meta('display_name')) . '</a>';
		} else if ($item['custom_meta_type'] == 'type_comment' && (int)get_comments_number(get_the_ID()) != 0) {
			// Comment
			$item_value = '<a href="' . esc_url(get_comments_link()) . '">' . esc_html(get_comments_number(get_the_ID())) . ' ' . $comments_text . '</a>';
		} else if ($item['custom_meta_type'] == 'type_custom') {
			$item_value = $item['custom_meta_value'];
		}

		if(!empty($item['custom_meta_icon'])) {
			$icon = '<div class="custom_meta_icon '.esc_attr($item['custom_meta_icon']).'"></div>';
		}

		if (!empty($item['custom_meta_label'])) {
			$item_meta_label = '<span class="gt3_meta_label_title">' . esc_html($item['custom_meta_label']) . '</span>';
		}

		echo '<div class="gt3_meta_values_item elementor-repeater-item-'.$item['_id'].'">' . $item_meta_label . $icon . '<div class="gt3_meta_value ' . esc_attr($item['custom_meta_type']) . '">'. $item_value . '</div></div>';

	}
	echo '</div>';
}