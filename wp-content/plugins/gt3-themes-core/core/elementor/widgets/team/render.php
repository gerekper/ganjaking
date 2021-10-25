<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team $widget */

$settings = $widget->get_settings();

$team_short_class = '';
if (isset($settings['grid_gap']) && $settings['grid_gap'] != '0') {
	$team_short_class = 'team_has_grid_gap';
}

global $paged;
if(empty($paged)) {
	$paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$query_args = $settings['query']['query'];
unset($settings['query']['query']);
$query_args['paged'] = $paged;
$query_raw = $settings['query'];

$query = new WP_Query($query_args);

if (!$query->post_count) {
	$paged = 1;
	$query_args['paged'] = 1;
	$query = new WP_Query($query_args);
}

$exclude = array();
if(isset($query_args['orderby']) && $query_args['orderby'] == 'rand') {
	foreach($query->posts as $_post) {
		$exclude[] = $_post->ID;
	}
}

$compile = '';

$widget->add_render_attribute('wrapper', 'class', 'module_team');
$widget->add_render_attribute('wrapper', 'class', esc_attr($settings['type']));

$data_wrapper = array(
	'type'				=> $settings['type'],
	'pagination_en' 	=> (bool) ($settings['pagination_en']),
	'posts_per_line' 	=> (int)$settings['posts_per_line'],
	'grid_gap'			=> $settings['grid_gap'],
	'query'         	=> $query_args,
	'link_post'			=> $settings['link_post'],
	'custom_item_height'=> $settings['custom_item_height'],
	'show_social' => $settings['show_social'],
	'show_position' => $settings['show_position'],
	'show_title' => $settings['show_title'],
	'show_description'	=> $settings['show_description'],

);



//$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data_wrapper));
$widget->add_render_attribute('wrapper', 'data-post-index', $query->query['posts_per_page']);

if((bool) $settings['pagination_en']) {
	$settings['show_view_all'] = false;
}

if ($query->found_posts) {
?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div class="shortcode_team <?php echo esc_attr($team_short_class); ?>">
			<div class="items<?php echo (int) $settings['posts_per_line']; ?>">
				<?php if($settings['use_filter'] && !empty($query_raw['taxonomy'])) { ?>
					<div class="isotope-filter">
						<?php
						echo '<a href="#" class="active" data-filter="*">'.esc_html__('All', 'gt3_themes_core').'</a>';
						foreach($widget->get_taxonomy($query_raw['taxonomy']) as $cat_slug) {
							echo '<a href="#" data-filter=".'.esc_attr($cat_slug['slug']).'">'.esc_html($cat_slug['name']).'</a>';
						}
						?>
					</div>
				<?php }
				$widget->add_render_attribute('item_list', 'class', 'item_list ');
				if($settings['use_filter'] && !empty($query_raw['taxonomy'])) {
					$widget->add_render_attribute('item_list', 'class', 'isotope gt3_isotope_parent');
				}
				?>
				<ul <?php $widget->print_render_attribute_string('item_list') ?>>
					<?php
					if($query->have_posts()) {
						$widget->render_index = 1;
						while($query->have_posts()) {
							$query->the_post();
							$compile .= $widget->render_team_item($query_args['posts_per_page'], false, $settings['grid_gap'], $settings['link_post'], $settings['custom_item_height'],$settings);
						}
						wp_reset_postdata();
					}
					echo ''.$compile;
					?>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
		<?php
		if(!empty($settings['show_view_all']) && (bool)($settings['show_view_all']) && $query->max_num_pages > 1) {
			if(!empty($settings['view_all_link_text']) && empty($settings['view_all_link_text'])) {
				$settings['view_all_link_text'] = esc_html__('More', 'gt3_themes_core');
			}
			if(!empty($settings['button_border']) && (bool)$settings['button_border']) {
				$widget->add_render_attribute('view_more_button', 'class', 'bordered');
			}
			$widget->add_render_attribute('view_more_button', 'href', 'javascript:void(0)');
			$widget->add_render_attribute('view_more_button', 'class', 'team_view_more_link');
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
			<div class="elementor-element  elementor-widget elementor-widget-gt3-core-button gt3_team_view_more_link_wrapper">
				<div class="elementor-widget-container">
					<div class="gt3_module_button_elementor size_normal alignment_center button_icon_'.$settings['button_type'].' hover_none">
						<a '.$widget->get_render_attribute_string('view_more_button').'>
							<span class="gt3_module_button__container">
								<span class="gt3_module_button__cover front">
									<span class="elementor_gt3_btn_text">'.esc_html($settings['button_title']).'</span>
									'.(!empty($settings['button_type']) && $settings['button_type'] == 'icon' ? '<span class="elementor_btn_icon_container"><span '.$widget->get_render_attribute_string('button_icon').'></span></span>' : '').'
								</span>
							</span>
						</a>
					</div>
				</div>
			</div>';
		} // End button
		if((bool)$settings['pagination_en']) {
			echo gt3_get_theme_pagination(5, "", $query->max_num_pages, $paged);
		}
		?>
	</div>
<?php
	$widget->print_data_settings($data_wrapper);
}
