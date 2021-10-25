<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_GalleryPackery $widget */

$settings = array(
	'select_source' => 'module',
	'slides'        => array(),
	'type'          => 2,
	'grid_gap'      => 0,
	'hover'         => 'type1',
	'lightbox'      => false,
	'show_title'    => true,
	'show_category' => false,
	'use_filter'    => false,
	'filter_align'  => 'center',
	'post_per_load' => 12,
	'all_title'     => esc_html__('All', 'gt3_themes_core'),
	'show_view_all' => false,
	'load_items'    => 4,
	'button_type'   => 'default',
	'button_border' => true,
	'button_title'  => esc_html__('Load More', 'gt3_themes_core'),

	'from_elementor' => true,
	'_background_background' => '',
	'_border_border'         => '',
);
$settings = wp_parse_args($widget->get_settings(), $settings);
$settings['filter_array'] = array();

$widget->serializeImages($settings);

if(!is_numeric($settings['post_per_load']) || empty($settings['post_per_load']) || $settings['post_per_load'] < 1) {
	$settings['post_per_load'] = 12;
}
if(!is_numeric($settings['load_items']) || empty($settings['load_items']) || $settings['load_items'] < 1) {
	$settings['load_items'] = 4;
}

if(isset($settings['slides']) && is_array($settings['slides']) && count($settings['slides']) && !empty($settings['type'])) {
	$uid  = mt_rand(300, 1000);
	$type = isset($widget->packery_grids[$settings['type']]) ? $widget->packery_grids[$settings['type']] : array( 'lap' => 4, 'grid' => 4, 'elem' => array() );

	$lightbox = (bool)($settings['lightbox']) ? true : false;
	$widget->add_render_attribute('wrapper', 'class', array(
		'packery_wrapper',
		'hover_'.esc_attr($settings['hover']),
		'source_'.esc_attr($settings['select_source']),
		$settings['from_elementor'] ? 'elementor' : 'not_elementor',
	));
	$widget->add_render_attribute('wrapper', 'data-images', wp_json_encode(array_slice($settings['slides'], $settings['post_per_load'])));
	$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode(array(
		'packery'       => $type,
		'lightbox'      => $lightbox,
		'title'         => (bool)($settings['show_title']),
		'show_category' => (bool)($settings['show_category']),
		'source'        => esc_attr($settings['select_source']),
		'filter'        => (bool)($settings['use_filter']),
		'load_items'    => esc_attr($settings['load_items']),
		'uid'           => $uid,
		'gap_value'     => esc_attr(intval($settings['grid_gap'])),
		'gap_unit'      => esc_attr(substr($settings['grid_gap'], -1) == '%' ? '%' : 'px'),
	)));
	if($lightbox) {
		$gallery_items = array();
	}
	if(!$settings['from_elementor']) {
		echo '<style>
			.packery_wrapper .isotope_wrapper {
				 margin-right:-'.$settings['grid_gap'].';
				 margin-bottom:-'.$settings['grid_gap'].';
			}

			.packery_wrapper .isotope_item {
				padding-right: '.$settings['grid_gap'].';
				padding-bottom: '.$settings['grid_gap'].';
			}
		 </style>';
	}
	?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php if((bool)($settings['use_filter']) && count($settings['filter_array']) > 1) {
			?>
			<div class="isotope-filter">
				<?php
				echo '<a href="#" class="active" data-filter="*">'.esc_html($settings['all_title']).'</a>';
				ksort($settings['filter_array']);
				foreach($settings['filter_array'] as $cat_slug) {
					echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'">'.esc_html($cat_slug['name']).'</a>';
				}
				?>
			</div>
		<?php } ?>
		<div class="isotope_wrapper items_list gt3_clear">
			<?php
			$settings['slides'] = array_slice($settings['slides'], 0, $settings['post_per_load']);
			foreach($settings['slides'] as $slide) {
				if($lightbox) {
					$image           = wp_prepare_attachment_for_js($slide['id']);
					$gallery_items[] = array(
						'href'        => $image['url'],
						'title'       => $image['title'],
						'thumbnail'   => $image['sizes']['thumbnail']['url'],
						'description' => $image['caption'],
						'is_video'    => 0,
						'image_id'    => $image['id'],
					);
				}
				echo  $widget->renderItem($slide, $settings['select_source'], $settings['lightbox'], $settings['show_title'], $settings['show_category']);
			}
			?>
		</div>
		<?php
		if((bool)($settings['show_view_all'])) {
			if(empty($settings['view_all_link_text'])) {
				$settings['view_all_link_text'] = esc_html__('More', 'gt3_themes_core');
			}
			if((bool)$settings['button_border']) {
				$widget->add_render_attribute('view_more_button', 'class', 'bordered');
			}
			$widget->add_render_attribute('view_more_button', 'href', 'javascript:void(0)');
			$widget->add_render_attribute('view_more_button', 'class', 'view_more_link');
			$widget->add_render_attribute('view_more_button', 'class', 'button_type_'.esc_attr($settings['button_type']));
			if($settings['button_type'] == 'icon') {
				$widget->add_render_attribute('button_icon', 'class', esc_attr($settings['button_icon']));
			}

			$widget->add_render_attribute('button_icon', 'class', 'widget-button-icon');
			if(!empty($settings['button_title'])) {
				$widget->add_render_attribute('view_more_button', 'title', esc_attr($settings['button_title']));
			}
			echo '<a '.$widget->get_render_attribute_string('view_more_button').'>'.esc_html($settings['button_title']).'<div '.$widget->get_render_attribute_string('button_icon').'></div></a>';
		} // End button
		?>
	</div>
	<?php
	if($lightbox) {
		$wrapper = '<script>var images'.$uid.' = '.wp_json_encode($gallery_items).'</script>';

		if(!isset($GLOBALS['gt3_core_elementor__footer'])) {
			$GLOBALS['gt3_core_elementor__footer'] = '';
		}
		$GLOBALS['gt3_core_elementor__footer'] .= $wrapper;

	}

}




