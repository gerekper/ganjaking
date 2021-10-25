<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team_Carousel $widget */

$settings = array(
	'nav'           => 'none',
	'items_per_line' => '1',
	'autoplay'      => true,
	'autoplay_time' => 4000,
	'space'			=> '30px',
);
$settings = wp_parse_args($widget->get_settings(), $settings);


$team_short_class = '';
if (isset($settings['arrows_bg_color']) && !empty($settings['arrows_bg_color']) && $settings['arrows_bg_color'] != 'transparent') {
	$widget->add_render_attribute('wrapper', 'class','team_has_arrows_bg_color');
}
if (isset($settings['grid_gap']) && $settings['grid_gap'] != '0') {
	$team_short_class .= 'team_has_grid_gap';
}
if (isset($settings['show_social']) && $settings['show_social'] != '') {
	$widget->add_render_attribute('wrapper', 'class',' team_show_social');
}

$query_args = $settings['query']['query'];
unset($settings['query']['query']);
$query_raw = $settings['query'];

$query = new WP_Query($query_args);

$exclude = array();
if(isset($query_args['orderby']) && $query_args['orderby'] == 'rand') {
	foreach($query->posts as $_post) {
		$exclude[] = $_post->ID;
	}
}

$compile = '';

$data = array(
	'fade'          => false,
	'autoplay'      => (bool) $settings['autoplay'],
	'items_per_line' => intval($settings['posts_per_line']),
	'autoplaySpeed' => intval($settings['autoplay_time']),
	'dots'          => ($settings['nav'] === 'dots') ? true : false,
	'arrows'        => ($settings['nav'] === 'arrows') ? true : false,
	'centerMode'        => $settings['center_mode'] === 'yes' ? true : false,
	'l10n'          => array(
		'prev' => esc_html__('Prev', 'gt3_themes_core'),
		'next' => esc_html__('Next', 'gt3_themes_core'),
	),
);

$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data));

$widget->add_render_attribute('wrapper', 'class', 'module_team');
$widget->add_render_attribute('wrapper', 'class', esc_attr($settings['type']));
?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div class="shortcode_team <?php echo esc_attr($team_short_class); ?>">
			<div class="items<?php echo (int) $settings['posts_per_line']; ?>">
				<?php
				$widget->add_render_attribute('item_list', 'class', 'item_list gt3_clear');
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
	</div>
<?php



