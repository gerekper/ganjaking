<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Portfolio $widget */

$settings = array(
	'show_type'     => 'grid',
	'grid_type'     => 'square',
	'packery_type'  => 2,
	'cols'          => 4,
	'grid_gap'      => 0,
	'hover'         => 'type1',
	'lazyload'		=> true,
	'show_title'    => true,
	'show_category' => false,
	'show_description' => true,
	'use_filter'    => false,
	'filter_style'	=> 'links',
	'grid_offset'	=> false,
	'filter_align'  => 'center',
	'all_title'     => esc_html__('All', 'gt3_themes_core'),
	'show_view_all' => false,
	'load_items'    => 4,
	'button_type'   => 'default',
	'button_border' => true,
	'button_title'  => esc_html__('Load More', 'gt3_themes_core'),

	'from_elementor' => true,

	'static_info_block' => '',
	'title'             => esc_html__('Title', 'gt3_themes_core'),
	'sub_title'         => esc_html__('Subtitle', 'gt3_themes_core'),
	'content'           => esc_html__('Content', 'gt3_themes_core'),
	'btn_block'         => '',
	'btn_title'         => esc_html__('Button Title', 'gt3_themes_core'),
	'btn_link'          => array( 'url' => '#', 'is_external' => false, 'nofollow' => false, ),
	'enable_icon'       => 'yes',
	//'element_icon' => '',
	'pagination_en'     => false,
);

$settings = wp_parse_args($widget->get_settings(), $settings);

if (empty($settings['packery_type'])) {
	$settings['packery_type'] = 2;
}

if((bool) $settings['pagination_en']) {
	$settings['show_view_all'] = false;
}

if(!is_numeric($settings['load_items']) || empty($settings['load_items']) || $settings['load_items'] < 1) {
	$settings['load_items'] = 4;
}
if(!is_numeric($settings['cols']) || empty($settings['cols']) || $settings['cols'] < 1 || $settings['cols'] > 4) {
	$settings['cols'] = 4;
}
global $paged;
if(empty($paged)) {
	$paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$query_args = $settings['query']['query'];
unset($settings['query']['query']);
$query_args['paged'] = $paged;


$portfolio_query_arg = 'portfolio_category';

if (function_exists('gt3_option')) {
    $slug_option = gt3_option('portfolio_slug');
    if (!empty($slug_option)) {
    	$portfolio_query_arg = sanitize_title( $slug_option ) . '_cat';
    }
}

$query_raw = $settings['query'];
if(isset($_REQUEST[$portfolio_query_arg]) && !empty($_REQUEST[$portfolio_query_arg])) {
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
				'taxonomy' => 'portfolio_category',
				'field'    => 'slug',
				'operator' => 'IN',
				'terms'    => array($_REQUEST[$portfolio_query_arg])
			)
		);
	}
} else {
	$_REQUEST[$portfolio_query_arg] = '';
}
$query   = new WP_Query($query_args);
if (!$query->post_count) {
	$paged = 1;
	$query_args['paged'] = 1;
	$query   = new WP_Query($query_args);
}
$exclude = array();
foreach($query->posts as $_post) {
	$exclude[] = $_post->ID;
}

if($query->have_posts()) {
	if(!$settings['from_elementor']) {
		echo '<style>
			.portfolio_wrapper .isotope_wrapper {
				 margin-right:-'.$settings['grid_gap'].';
				 margin-bottom:-'.$settings['grid_gap'].';
			}

			.portfolio_wrapper .isotope_item {
				padding-right: '.$settings['grid_gap'].';
				padding-bottom: '.$settings['grid_gap'].';
			}
		 </style>';
	}

	$query_args['exclude']        = $exclude;
	$query_args['posts_per_page'] = $settings['load_items'];
	$data_wrapper                 = array(
		'pagination_en' => (bool) ($settings['pagination_en']),
		'show_title'    => (bool) ($settings['show_title']),
		'show_category' => (bool) ($settings['show_category']),
		'use_filter'    => ((bool) ($settings['use_filter'])),
		'lazyload'		=> $settings['lazyload'],
		'load_items'    => $settings['load_items'],
		'grid_gap'     => intval($settings['grid_gap']),
		'gap_value'     => intval($settings['grid_gap']),
		'gap_unit'      => substr($settings['grid_gap'], -1) == '%' ? '%' : 'px',
		'query'         => $query_args,
		'type'          => $settings['show_type'],
		'random'        => (isset($query_args['orderby']) && $query_args['orderby'] == 'rand'),
		'render_index'  => $query->query['posts_per_page'],
		'settings'      => array(
			'grid_type'    => $settings['grid_type'],
			'cols'         => $settings['cols'],
			'show_type'    => $settings['show_type'],
			'packery_type' => $settings['packery_type'],
			'lazyload'		=> $settings['lazyload'],
			'grid_gap'     => intval($settings['grid_gap']),
		)
	);

	$class_wrapper = array(
		'portfolio_wrapper',
		'show_type_'.$settings['show_type'],
		'hover_'.$settings['hover'],
		'packery_type_'.$settings['packery_type'],
		$settings['from_elementor'] ? 'elementor' : 'not_elementor',
	);

	switch($settings['show_type']) {
		case 'packery':
			if(!key_exists($settings['packery_type'], $widget->packery_grids)) {
				$settings['packery_type'] = 2;
			}
			$data_wrapper['packery'] = $widget->packery_grids[$settings['packery_type']];
			break;
		case 'masonry':
			$data_wrapper['cols'] = $settings['cols'];
			$class_wrapper[]      = 'items'.$settings['cols'];
			break;
		case 'grid':
			$class_wrapper[]           = 'items'.$settings['cols'];
			$class_wrapper[]           = 'grid_type_'.$settings['grid_type'];
			$data_wrapper['cols']      = $settings['cols'];
			$data_wrapper['grid_type'] = $settings['grid_type'];
			break;
	}

	if (isset($settings['grid_gap']) && $settings['grid_gap'] != '0') {
		$class_wrapper[] = 'testimonials_has_grid_gap';
	}

	ob_start();
	if(has_excerpt(get_the_ID()) && trim(get_the_excerpt(get_the_ID()))) {
		the_excerpt(get_the_ID());
	} else {
		the_content(get_the_ID());
	}
	$post_excerpt = ob_get_clean();
	if ((bool)$settings['show_description'] && !empty($post_excerpt)) {
		$class_wrapper[] = 'testimonials_show_desc';
	}

	$widget->add_render_attribute('wrapper', 'class', $class_wrapper);
	$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data_wrapper));
	$widget->add_render_attribute('wrapper', 'data-post-index', $query->query['posts_per_page']);

	if(empty($settings['btn_link']['url'])) {
		$settings['btn_link']['url'] = '#';
	}
	$widget->add_render_attribute('btn_link', 'class', 'static_info_link');
	$widget->add_render_attribute('btn_link', 'href', esc_url($settings['btn_link']['url']));

	if($settings['btn_link']['is_external']) {
		$widget->add_render_attribute('btn_link', 'target', '_blank');
	}

	if(!empty($settings['btn_link']['nofollow'])) {
		$widget->add_render_attribute('btn_link', 'rel', 'nofollow');
	}

	?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<?php
		if((bool) ($settings['use_filter'])) {
			?>
			<div class="isotope-filter<?php echo !empty( $settings['filter_style']) ? ' isotope-filter--'.$settings['filter_style'] : ''; ?>">
				<?php
				if($settings['filter_style'] == 'links') {
					echo '<a href="'.get_permalink().'" data-filter="*" '.($_REQUEST[$portfolio_query_arg] == '' ? ' class="active"' : '').'>'.esc_html($settings['all_title']).'</a>';
				} else {
					echo '<a href="#" class="active" data-filter="*">'.esc_html($settings['all_title']).'</a>';
				}

				if (count($query_raw['taxonomy']) > 1) {
					foreach($widget->get_taxonomy($query_raw['taxonomy']) as $cat_slug) {
						if($settings['filter_style'] == 'links') {
							$url = add_query_arg(array(
								$portfolio_query_arg => $cat_slug['slug'],
							));
							echo '<a href="'.$url.'" data-filter=".'.esc_attr($cat_slug['slug']).'" '.($_REQUEST[$portfolio_query_arg] == $cat_slug['slug'] ? ' class="active"' : '').'>'.esc_html($cat_slug['name']).'</a>';
						} else {
							echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'">'.esc_html($cat_slug['name']).'</a>';
						}
					}
				}else{
					$cats = get_terms(array(
		                'taxonomy' => 'portfolio_category',
		                'hide_empty' => true
		            ));
		            foreach ($cats as $cat) {
		            	$permalink = get_permalink();
			            $permalink = add_query_arg($portfolio_query_arg, $cat->slug, $permalink);
			            echo '<a href="'.esc_url($permalink).'" data-filter=".'.esc_attr($cat->slug).'"'.($_REQUEST[$portfolio_query_arg] == $cat->slug ? ' class="active"' : '').'>';
			            echo esc_html($cat->name);
			            echo '</a>';
			        }
			        wp_reset_postdata();
				}

				?>
			</div>
		<?php }
		$portfolio_classes = '';
		if ((bool)($settings['grid_offset']) && ($settings['show_type'] == 'masonry' || $settings['show_type'] == 'grid')) {
			$portfolio_classes .= ' portfolio_offset_mode';
		}
		?>
		<div class="isotope_wrapper gt3_isotope_parent items_list gt3_clear<?php echo esc_attr($portfolio_classes); ?>">
			<?php

			$render_index = 1;

			while($query->have_posts()) {

				if((bool) $settings['static_info_block'] && $render_index === 1) {
					$packery_grids = $widget->packery_grids[$settings['packery_type']];
					$item_class = $widget->get_isotope_item_size($render_index,$packery_grids);
					$render_index++;
					echo '<div class="static_info_text_block isotope_item loading blog_post_preview element'.(!empty($item_class) ? $item_class : '').'">
							<div class="gt3_portfolio_list__image-placeholder"></div>
						    <div class="item_wrapper">
								<div class="item">
									<div class="title">'.esc_html($settings['title']).'</div>
									<div class="sub_title">'.esc_html($settings['sub_title']).'</div>
									<div class="content">'.$settings['content'].'</div>';
					if((bool) $settings['btn_block']) {
						if((bool) $settings['enable_icon']) {
							$btn_icon = '<span class="static_info_icon"><i class="fa fa-angle-right"></i></span>';
						} else {
							$btn_icon = '';
						}
						echo '<a '.$widget->get_render_attribute_string('btn_link').'>'.esc_html($settings['btn_title']).$btn_icon.'</a>';
					}
					echo '</div>
							</div>
						  </div>';
				}

				$query->the_post();
				echo ''.$widget->renderItem(((bool) ($settings['use_filter'])), $settings['show_title'], $settings['show_category'], $render_index, $settings);

				$render_index++;
			}
			?>
		</div>
		<?php
		if((bool) ($settings['show_view_all']) && $query->max_num_pages > 1) {
			if(empty($settings['view_all_link_text'])) {
				$settings['view_all_link_text'] = esc_html__('More', 'gt3_themes_core');
			}
			if((bool) $settings['button_border']) {
				$widget->add_render_attribute('view_more_button', 'class', 'bordered');
			}
			$widget->add_render_attribute('view_more_button', 'href', 'javascript:void(0)');
			$widget->add_render_attribute('view_more_button', 'class', 'portfolio_view_more_link');
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
			<div class="elementor-element  elementor-widget elementor-widget-gt3-core-button gt3_portfolio_view_more_link_wrapper">
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
		?>
	</div>
	<?php

}

wp_reset_postdata();

$widget->print_data_settings($data_wrapper);

