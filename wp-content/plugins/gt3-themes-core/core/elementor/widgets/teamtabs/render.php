<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Team_Tabs $widget */

$settings = array(
	'nav'           => 'none',
	'items_per_line' => '1',
	'autoplay'      => true,
	'autoplay_time' => 4000,
	'space'			=> '30px',
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$data = array(
	'fade'          => false,
	'autoplay'      => (bool) $settings['autoplay'],
	'items_per_line' => intval($settings['items_per_line']),
	'autoplaySpeed' => intval($settings['autoplay_time']),
	'dots'          => ($settings['nav'] === 'dots') ? true : false,
	'arrows'        => ($settings['nav'] === 'arrows') ? true : false,
	'l10n'          => array(
		'prev' => esc_html__('Prev', 'gt3_themes_core'),
		'next' => esc_html__('Next', 'gt3_themes_core'),
	),
);

$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data));

$team_short_class = '';
if (isset($settings['grid_gap']) && $settings['grid_gap'] != '0') {
	$team_short_class = 'team_has_grid_gap';
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

$widget->add_render_attribute('wrapper', 'class', 'module_team');
//$widget->add_render_attribute('wrapper', 'class', esc_attr($settings['type']));
?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div class="gt3_team_tabs <?php echo esc_attr($team_short_class); ?>">
			<div class="items1">
				<?php

				$widget->add_render_attribute('item_list', 'class', 'item_list gt3_clear');

				if($query->have_posts()) {
					$widget->render_index = 1;
					while($query->have_posts()) {
						$query->the_post();
						$compile .= $widget->render_team_item($settings);
					}
					wp_reset_postdata();
				}

				$avatar_slider = $widget->image_list;

				if (!empty($avatar_slider) && is_array($avatar_slider)) {
					?><div class="gt3_team_avatar_slider"><?php 
					echo implode('',$avatar_slider);
					?></div><?php
				}

				?>
				<div <?php $widget->print_render_attribute_string('item_list') ?>>
					<?php					
					echo $compile;
					?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>