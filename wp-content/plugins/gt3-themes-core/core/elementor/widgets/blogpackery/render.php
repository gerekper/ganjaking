<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;
use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_BlogPackery $widget */

$settings = array(
	'packery_type' => 1,
	'blog_post_listing_content_module' => 'yes',
	'meta_author' => '',
	'meta_comments' => '',
	'meta_categories' => '',
	'meta_date' => '',
	'meta_sharing' => '',
	'items_per_line' => '1',
	'spacing_beetween_items' => '30',
	'pagination_en' => '',
	'packery_en' => '',
	'blog_filter' => '',
	'static_info_block' => '',
	'title' => esc_html__('Title', 'gt3_themes_core'),
	'sub_title' => esc_html__('Subtitle', 'gt3_themes_core'),
	'content' => esc_html__('Content', 'gt3_themes_core'),
	'btn_block' => '',
	'btn_title' => esc_html__('Button Title', 'gt3_themes_core'),
	'btn_link'         => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'enable_icon' => 'yes',
	//'element_icon' => '',
	'grid_gap'      => 0,
	'post_btn_link' => '',
	'post_boxed_content' => '',
	'packery_items_per_line' => 'theme_packery',
	'post_btn_link_title' => esc_html__('Read More', 'gt3_themes_core'),
	'meta_position' => 'after_title',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$blog_masonry = $blog_masonry_item = '';


global $paged;
if(empty($paged)) {
	$paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$query_args = $settings['query']['query'];
unset($settings['query']['query']);
$query_raw = $settings['query'];
$query_args['paged'] = $paged;

if(isset($_REQUEST['category']) && !empty($_REQUEST['category'])) {
	if(isset($query_args['tax_query'])) {
		foreach($query_args['tax_query'] as $key => $value) {
			if(!is_numeric($key)) {
				continue;
			}
			if(is_array($value) && isset($value['field']) && $value['field'] == 'slug') {
				$query_args['tax_query'][$key]['terms'] = $_REQUEST[$portfolio_query_arg];
			}
		}
	}else{
		$query_args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'operator' => 'IN',
				'terms'    => array($_REQUEST['category'])
			)
		);
	}
} else {
	$_REQUEST['category'] = '';
}

$query   = new WP_Query($query_args);

$query_args['paged'] = $paged;

$exclude = array();
foreach($query->posts as $_post) {
	$exclude[] = $_post->ID;
}

$query_args['exclude']        = $exclude;

$data_wrapper                 = array(
	'pagination_en' => $settings['pagination_en'],
	'meta_author' 	=> $settings['meta_author'],
	'meta_comments' => $settings['meta_comments'],
	'meta_categories' => $settings['meta_categories'],
	'meta_date' 	=> $settings['meta_date'],
	'meta_sharing' 	=> $settings['meta_sharing'],
	'meta_position' 	=> ($settings['meta_position']),
	'packery_type'		=> $settings['packery_type'],
	'lazyload'			=> $settings['lazyload'],
	'use_filter'		=> $settings['use_filter'],
	'post_btn_link'		=> $settings['post_btn_link'],
	'post_btn_link_title' => $settings['post_btn_link_title'],
	'render_index'  => $query->query['posts_per_page'],
	'grid_gap'		=> $settings['grid_gap'],
	'query'         => $query_args,
	'exclude'		=> $exclude
);


$widget->add_render_attribute('wrapper', 'class', array(
	'isotope_wrapper',
	'packery_type_'.$settings['packery_type'],
));
//$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data_wrapper));
$widget->add_render_attribute('wrapper', 'data-post-index', $query->query['posts_per_page']);

	if((bool) ($settings['use_filter'])) {
		?>
		<div class="isotope-filter<?php echo !empty( $settings['filter_style']) ? ' isotope-filter--'.$settings['filter_style'] : ''; ?>">
			<?php
			if($settings['filter_style'] == 'links') {
				echo '<a href="'.get_permalink().'" data-filter="*" '.((empty($_REQUEST['category']) || (!empty($_REQUEST['category']) && $_REQUEST['category'] == '')) ? ' class="active"' : '').'>'.esc_html($settings['all_title']).'</a>';
			} else {
				echo '<a href="#" class="active" data-filter="*">'.esc_html($settings['all_title']).'</a>';
			}

			if (count($query_raw['taxonomy']) > 1) {
				foreach($widget->get_taxonomy($query_raw['taxonomy']) as $cat_slug) {
					if($settings['filter_style'] == 'links') {
						$url = add_query_arg(array(
							'category' => $cat_slug['slug'],
						));
						echo '<a href="'.$url.'" data-filter=".'.esc_attr($cat_slug['slug']).'_filter" '.($_REQUEST['category'] == $cat_slug['slug'] ? ' class="active"' : '').'>'.esc_html($cat_slug['name']).'</a>';
					} else {
						echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'_filter">'.esc_html($cat_slug['name']).'</a>';
					}
				}
			}else{
				$cats = get_categories(array(
	                'taxonomy' => 'category',
	                'hide_empty' => true
	            ));
	            foreach ($cats as $cat) {
	            	$permalink = get_permalink();
		            $permalink = add_query_arg('category', $cat->slug, $permalink);
		            echo '<a href="'.esc_url($permalink).'" data-filter=".'.esc_attr($cat->slug).'_filter"'.(!empty($_REQUEST['category']) && $_REQUEST['category'] == $cat->slug ? ' class="active"' : '').'>';
		            echo esc_html($cat->name);
		            echo '</a>';
		        }
		        wp_reset_postdata();
			}
			?>
		</div>
	<?php }

?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php
		if($query->have_posts()) {
			$render_index = 1;

			$settings['post_btn_link_title'] = (!empty($settings['post_btn_link_title']) ? esc_html__($settings['post_btn_link_title']) : esc_html__('Read More', 'gt3_themes_core'));


			while($query->have_posts()) {

				$query->the_post();
				echo $widget->renderItem($settings,$render_index);

				$render_index++;
			} //endwhile

			wp_reset_postdata();
		}
		?>
	</div>
	<?php
	if((bool) ($settings['show_view_all']) && $query->max_num_pages > 1) {
		if(empty($settings['view_all_link_text'])) {
			$settings['view_all_link_text'] = esc_html__('More', 'gt3_themes_core');
		}
		$widget->add_render_attribute('view_more_button', 'href', 'javascript:void(0)');
		$widget->add_render_attribute('view_more_button', 'class', 'blogpackery_view_more_link');
		$widget->add_render_attribute('view_more_button', 'class', 'button_size_elementor_normal alignment_center border_icon_none hover_none btn_icon_position_right');

		$widget->add_render_attribute('view_more_button', 'class', 'button_type_'.esc_attr($settings['button_type']));
		if($settings['button_type'] == 'icon') {
			$widget->add_render_attribute('button_icon', 'class', esc_attr($settings['button_icon']));
		}

		$widget->add_render_attribute('button_icon', 'class', 'elementor_gt3_btn_icon');
		if(!empty($settings['button_title'])) {
			$widget->add_render_attribute('view_more_button', 'title', esc_attr($settings['button_title']));
		}
		echo '
		<div class="elementor-element  elementor-widget elementor-widget-gt3-core-button gt3_blogpackery_view_more_link_wrapper">
			<div class="elementor-widget-container">
				<div class="gt3_module_button_elementor size_normal alignment_center button_icon_'.$settings['button_type'].' hover_none">
					<a '.$widget->get_render_attribute_string('view_more_button').'>
						<span class="gt3_module_button__container">
							<span class="gt3_module_button__cover front">
								<span class="elementor_gt3_btn_text">'.esc_html($settings['button_title']).'</span>
								'.($settings['button_type'] == 'icon' ? '<span class="elementor_btn_icon_container"><span '.$widget->get_render_attribute_string('button_icon').'></span></span>' : '').'
							</span>
						</span>
					</a>
				</div>
			</div>
		</div>';
	} // End button
	if((bool) $settings['pagination_en']) {
		echo gt3_get_theme_pagination(5, "", $query->max_num_pages, $paged);
	}

	$widget->print_data_settings($data_wrapper);
