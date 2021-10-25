<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;
use Elementor\GT3_Core_Elementor_Control_Query;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_PortfolioCarousel $widget */

$settings = array(
	'nav'                      => 'arrows',
	'items_per_line'           => '1',
	'autoplay'                 => false,
	'autoplay_time'            => 4000,
	'center_mode'              => true,
	'space'                    => '60px',
	'show_title'               => true,
	'show_category'            => true,
	'portfolio_btn_link'       => '',
	'portfolio_btn_link_title' => esc_html__( 'Read More', 'gt3_themes_core' ),
	'from_elementor'           => true,
);

$settings = wp_parse_args($widget->get_settings(), $settings);


$query_args = $settings['query']['query'];
unset($settings['query']['query']);
$query_raw = $settings['query'];
$query     = new WP_Query($query_args);

$exclude = array();
foreach($query->posts as $_post) {
	$exclude[] = $_post->ID;
}

if($query->have_posts()) {
	$settings['portfolio_btn_link_title'] = (!empty($settings['portfolio_btn_link_title']) ? esc_html__($settings['portfolio_btn_link_title']) : esc_html__('Read More', 'gt3_themes_core'));

	$query_args['exclude']        = $exclude;
	$data_wrapper  = array(
		'autoplay'		=> (bool) ($settings['autoplay']),
		'autoplaySpeed'	=> (int)$settings['autoplay_time'],
		'centerMode'	=> (bool) ($settings['center_mode']),
		'fade'          => false,
		'items_per_line' => intval($settings['items_per_line']),
		'dots'          => ($settings['nav'] === 'dots') ? true : false,
		'arrows'        => ($settings['nav'] === 'arrows') ? true : false,
		'l10n'          => array(
			'prev' => esc_html__('Prev', 'gt3_themes_core'),
			'next' => esc_html__('Next', 'gt3_themes_core'),
		),
		'show_title'    => (bool) ($settings['show_title']),
		'show_category' => (bool) ($settings['show_category']),
		'show_text' 	=> (bool) ($settings['show_text']),
		'query'         => $query_args,
		'random'        => (isset($query_args['orderby']) && $query_args['orderby'] == 'rand'),
		'render_index'	=> $query->query['posts_per_page'],
	);

	$class_wrapper                = array(
		'portfolio_carousel_wrapper',
		'portfolio_items_per_line_'.$settings['items_per_line'],
		(bool) ($settings['center_mode']) ? 'portfolio_items--center_mode' : '',
		$settings['from_elementor'] ? 'elementor' : 'not_elementor',
	);

	if(!empty($settings['item_align'])) {
		$class_wrapper[] = 'text_align-'.esc_attr($settings['item_align']);
	}

	$widget->add_render_attribute('wrapper', 'class', $class_wrapper);
//	$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data_wrapper));

	?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div class="items_list gt3_clear space_<?php echo esc_attr($settings['space']) ?>">
			<?php

			$render_index = 1;
			$image_ration = 0;
            foreach ($query->posts as $post) {
            	$image_id = get_post_thumbnail_id($post->ID);
            	$image_data = wp_get_attachment_image_src( $image_id, 'full' );
            	if (!empty($image_data[1]) && !empty($image_data[2])) {
            		$image_ration += ($image_data[2] / $image_data[1]);
            	}else{
            		$image_ration += 1200/800;
            	}
            }

            if ($query_args['posts_per_page'] < $query->found_posts) {
            	$post_count = $query_args['posts_per_page'];
            }else{
            	$post_count = $query->found_posts;
            }

            $image_ration = $image_ration/$post_count;
            $settings['image_ration'] = $image_ration;

			while($query->have_posts()) {
				$query->the_post();
				echo ''.$widget->renderItem('', $settings['show_title'], $settings['show_category'], $render_index,$settings);

				$render_index++;
			}
			?>
		</div>
		<?php
		?>
	</div>
	<?php

	$widget->print_data_settings($data_wrapper);

}

wp_reset_postdata();

