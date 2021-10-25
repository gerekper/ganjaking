<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_Testimonials $widget */

$settings = array(
	'nav'           => 'none',
	'type'			=> 'style4',
	'items_per_line' => '1',
	'autoplay'      => true,
	'autoplay_time' => 4000,
	'round_imgs'    => false,
	'image_align'   => '',
	'image_size'    => array(
		'width'  => 64,
		'height' => 64,
	),
	'text_align'    => '',
	'author_align'  => '',
	'quote_marker' => 'yes'
);

//$item['icons']
$settings = wp_parse_args($widget->get_settings(), $settings);

$css_class = array(
	'module_testimonial',
	'active-carousel',
	'items_per_line-' .intval($settings['items_per_line']),
);

if(!empty($settings['type'])) {
	$css_class[] = esc_attr($settings['type']);
}
if(!empty($settings['text_align'])) {
	$css_class[] = 'text_align-'.esc_attr($settings['text_align']);
}
if(!empty($settings['author_align'])) {
	$css_class[] = 'author_align-'.esc_attr($settings['author_align']);
}
if(!empty($settings['image_align'])) {
	$css_class[] = 'image_align-'.esc_attr($settings['image_align']);
}
if(!empty($settings['nav'])) {
	$css_class[] = 'nav-'.esc_attr($settings['nav']);
}

if ($settings['quote_marker'] == '' && ($settings['type'] == 'style2' || $settings['type'] == 'style4')) {
	$css_class[] = 'hidden_quote_marker';
}

$widget->add_render_attribute('wrapper', 'class', $css_class);
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
if(is_array($settings['items']) && count($settings['items'])) {
	?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<span class='testimonials-photo-wrapper'></span>
		<div class="module_content testimonials_list">
			<?php
			if ( $settings['type'] == 'style4' && $settings['quote_marker'] == 'yes' ) {
				$svg = '<svg width="42px" height="42px" viewBox="0 -160 300 300" xmlns="http://www.w3.org/2000/svg">
	  <path d="M49.5 92q-16.5 -16 -16.5 -38q0 -20 14 -36t32 -17q-3 -26 -17.5 -44.5t-41.5 -38.5l21 -38q42 22 72 69.5t30 95.5q0 28 -16.5 45.5t-38.5 17.5t-38.5 -16zM206 91q-16 -16 -16 -37q0 -20 14 -36t32 -17q-4 -27 -18.5 -46t-41.5 -38l21 -37q42 22 71.5 69.5t29.5 95.5 q0 28 -16 45t-38 17t-38 -16z" /></svg>';
				echo '<div class="svg_icon">'.apply_filters('gt3/widgets/render/testimonials/svg', $svg).'</div>';
			}
			?>
			<div class="testimonials_rotator">
				<?php
				foreach($settings['items'] as $index => $item) {
					$hidden = (bool) $index ? 'display: none;' : '';
					?>
                    <div class="testimonials_item" style="<?php echo esc_attr( $hidden ); ?>">
						<div class="testimonial_item_wrapper">
							<div class="testimonials_content">
								<?php
								if (empty($svg_wrap)) {
									$svg_wrap = '';
								}
								$text  = ( ! empty( $item['content'] ) ? '<div class="testimonials-text">'.( $settings['type'] == 'style2' && $settings['quote_marker'] == 'yes' ? $svg_wrap : '' ).$item['content'].'</div>' : '' );
								$icons = ( ! empty( $item['icons'] ) ) ? '<div class="icons">'.$item['icons'].'</div>' : '';
								$photo = '';
								$title = ( ! empty( $item['tstm_author'] ) ? '<div class="testimonials_title">'.esc_html( $item['tstm_author'] ).'</div>' : '' );
								$title .= ( ! empty( $item['sub_name'] ) ? '<div class="testimonials-sub_name">'.esc_html( $item['sub_name'] ).'</div>' : '' );

								if(!empty($item['image']['id'])) {
									$repeater_key = $widget->get_repeater_key('image', 'items', $index);
									if($settings['round_imgs']) {
										$widget->add_render_attribute($repeater_key, 'class', 'rounded');
									}
									$src = Utils::get_placeholder_image_src();
									if(isset($item['image']['id']) && (bool) $item['image']['id']) {
										$image = wp_get_attachment_image_src($item['image']['id'], 'single-post-thumbnail');
										if($image) {
											$src = aq_resize($image[0], (int)$settings['image_size']['width']*2, (int)$settings['image_size']['height']*2, true, true, true);
										}
									}

									$image = wp_prepare_attachment_for_js($item['image']['id']);

									$widget->add_render_attribute($repeater_key, 'src', esc_url($src));
									$photo = '<div class="testimonials_photo"><img '.$widget->get_render_attribute_string($repeater_key).' alt="'.esc_attr($image['alt']).'" /></div>';
								}
								switch($settings['type']) {
									case 'style1':
										echo ''.$photo.$text.$title.$icons;
										break;
									case 'style2':
										echo ''.$text.$photo.$title.$icons;
										break;
									case 'style3':
										echo ''.$photo.$text.$title.$icons;
										break;
									case 'style4':
										echo ''.$text.$photo.$title.$icons;
										break;
								}
								?>
							</div>
						</div>
					</div>
				<?php
				} // end foreach
				?>
			</div>
			<div class="clear"></div>
		</div>
	</div>

<?php
	$widget->print_data_settings($data);
} // end if



