<?php

if(!defined('ABSPATH')) {
	exit;
}

use Elementor\Utils;

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_TestimonialsLite $widget */

$settings = array(
	'nav'           => 'none',
	'items_per_line' => '1',
	'autoplay'      => true,
	'autoplay_time' => 4000,
	'space'			=> '30px',
	'author_position' => 'after',
	'avatar_slider'	=> '',
	'round_imgs'    => false,
	'image_position'=> 'aside',
	'item_align'	=> 'left',
	'image_size'    => array(
		'size' => 60
	),
);

$settings = wp_parse_args($widget->get_settings(), $settings);

$css_class = array(
	'gt3_testimonial',
	'active-carousel',
	'items_per_line-' .intval($settings['items_per_line']),
);

if(!empty($settings['type'])) {
	$css_class[] = esc_attr($settings['type']);
}
if(!empty($settings['item_align'])) {
	$css_class[] = 'text_align-'.esc_attr($settings['item_align']);
}

if(!empty($settings['author_position'])) {
	$css_class[] = 'author_position-'.esc_attr($settings['author_position']);
}

if(!empty($settings['image_position'])) {
	$css_class[] = 'image_position-'.esc_attr($settings['image_position']);
}


if ($settings['items_per_line'] == '1' && $settings['image_position'] != 'aside' && $settings['avatar_slider'] == true) {
	if (count($settings['items']) > 3) {
		$css_class[] = 'testimonials_avatar_slider';
	}else{
		$settings['avatar_slider'] = false;
	}
}

if(!empty($settings['nav'])) {
	$css_class[] = 'nav-'.esc_attr($settings['nav']);
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

$quote_src = plugins_url( '/core/elementor/assets/image/quote.png', GT3_THEMES_CORE_PLUGIN_FILE );
$quote_src = apply_filters( 'gt3_testimonial_quote_src', $quote_src );

$widget->add_render_attribute('wrapper', 'data-quote-src', $quote_src);
$widget->add_render_attribute('wrapper', 'data-settings', wp_json_encode($data));
$widget->add_render_attribute('_wrapper', 'data-settings', wp_json_encode($data));

if(is_array($settings['items']) && count($settings['items'])) {
	?>
	<div <?php $widget->print_render_attribute_string('wrapper') ?>>
		<div class="module_content testimonials_list">
			<?php ob_start(); ?>
			<div class="testimonials_rotator">
				<?php
				foreach($settings['items'] as $index => $item) {
					$hidden = (intval($index) + 1) > intval($settings['items_per_line']) ? 'display: none;' : '';
					$link_id = 'linkid_'.$index;
					?>
                    <div class="testimonials_item" style="<?php echo esc_attr( $hidden ); ?>">
						<div class="testimonial_item_wrapper" style="position: relative">
							<div class="testimonials_content">
								<?php
								$item_link = '';
								if(!empty($item['link']['url'])) {

									$widget->add_render_attribute($link_id, 'href', esc_url($item['link']['url']));

									if($item['link']['is_external']) {
										$widget->add_render_attribute($link_id, 'target', '_blank');
									}

									if(!empty($item['link']['nofollow'])) {
										$widget->add_render_attribute($link_id, 'rel', 'nofollow');
									}

									$item_link = '<a '.$widget->get_render_attribute_string($link_id).' style="position: absolute; left: 0; top: 0; width: 100%; height: 100%"></a>';
								}

								$text  = ( ! empty( $item['content'] ) ? '<div class="testimonials-text"><div class="testimonials-text-wrapper">'.$item['content'].'</div></div>' : '' );
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
											if (!empty($settings['image_size']) && is_array($settings['image_size']) && !empty($settings['image_size']['size']) ) {
												$src = aq_resize($image[0], (int)$settings['image_size']['size']*2, (int)$settings['image_size']['size']*2, true, true, true);
											}else{
												$src = $image[0];
											}
										}
									}

									$widget->add_render_attribute($repeater_key, 'src', esc_url($src));
									$widget->add_render_attribute($repeater_key, 'style', 'width:'.esc_attr((int)$settings['image_size']['size']).'px;height:'.esc_attr((int)$settings['image_size']['size']).'px;');

									$image = wp_prepare_attachment_for_js($item['image']['id']);
									$widget->add_render_attribute($repeater_key, 'alt', $image['alt']);



									$photo = '<div class="testimonials_photo"><img '.$widget->get_render_attribute_string($repeater_key).'/></div>';
								}

								if(!empty($settings['author_position']) && $settings['author_position'] == 'around' && $settings['avatar_slider'] != true) {
									echo '<div class="testimonials_author_wrapper">'.$photo. $item_link.'</div>';
									$photo = '';
								}

								if(!empty($settings['author_position']) && ($settings['author_position'] == 'after' || $settings['author_position'] == 'around')) {
									echo $text;
								}

								if (!($settings['items_per_line'] == '1' && $settings['image_position'] != 'aside' && $settings['avatar_slider'] == true)) {
									if (
										(!empty($settings['image_position']) && $settings['image_position'] == 'bottom') ||
										(!empty($settings['image_position']) && $settings['image_position'] == 'aside' && !empty($settings['item_align']) && $settings['item_align'] == 'right')
									) {
										echo '<div class="testimonials_author_wrapper">'. wp_kses_post( apply_filters( 'gt3/core/render/TestimonialsLite/block_wrap_start', '' ) ) . $title . wp_kses_post( apply_filters( 'gt3/core/render/TestimonialsLite/block_wrap_end', '' ) ) . $photo . $item_link .'</div>';
									}else{
										echo '<div class="testimonials_author_wrapper">' . $photo . wp_kses_post( apply_filters( 'gt3/core/render/TestimonialsLite/block_wrap_start', '' ) ) . $title . wp_kses_post( apply_filters( 'gt3/core/render/TestimonialsLite/block_wrap_end', '' ) ) . $item_link . '</div>';
									}
								}

								if($settings['items_per_line'] == '1' && !empty($settings['author_position']) && $settings['author_position'] == 'around' && $settings['avatar_slider'] == true) {
									echo '<div class="testimonials_author_wrapper">'.$title.$item_link.'</div>';
									$photo = '';
								}

								if(!empty($settings['author_position']) && $settings['author_position'] == 'before') {
									echo $text;
								}

								?>
							</div>
						</div>
					</div>
				<?php
				} // end foreach
				?>
			</div><?php
			$testimonials_rotator = ob_get_clean();
			ob_start();
			if ($settings['items_per_line'] == '1' && $settings['image_position'] != 'aside' && $settings['avatar_slider'] == true) {
				?><div class="testimonials_author_rotator"><?php
				foreach($settings['items'] as $index => $item) {
					?>
                    <div class="testimonials_avatar_item">
						<div class="testimonials_avatar_content">
							<?php
							$photo = '';
							$title = '<div class="testimonials_title_wrapp">';
							$title .= ( ! empty( $item['tstm_author'] ) ? '<div class="testimonials_title">'.esc_html( $item['tstm_author'] ).'</div>' : '' );
							$title .= ( ! empty( $item['sub_name'] ) ? '<div class="testimonials-sub_name">'.esc_html( $item['sub_name'] ).'</div>' : '' );
							$title .= '</div>';

							if(!empty($item['image']['id'])) {
								$repeater_key = $widget->get_repeater_key('image', 'items', $index);
								$image_obj = wp_prepare_attachment_for_js($item['image']['id']);


								$photo = '<div class="testimonials_photo"><img '.$widget->get_render_attribute_string($repeater_key).' alt="'.$image_obj['alt'].'" /></div>';
							}

							if(!empty($settings['author_position']) && $settings['author_position'] == 'around') {
								$title = '';
							}

							echo '<div class="testimonials_author_wrapper">'.$photo.$title.'</div>';

							?>
						</div>
					</div>
				<?php
				}
				?></div><?php
			}
			$testimonials_author_rotator = ob_get_clean();
			if ($settings['items_per_line'] == '1' && $settings['image_position'] != 'aside' && $settings['avatar_slider'] == true) {
				if(!empty($settings['author_position']) && ($settings['author_position'] == 'before' || $settings['author_position'] == 'around')) {
					echo $testimonials_author_rotator;
				}
				echo $testimonials_rotator;
				if(!empty($settings['author_position']) && $settings['author_position'] == 'after') {
					echo $testimonials_author_rotator;
				}
			}else{
				echo $testimonials_rotator;
			}
			?>
			<div class="clear"></div>
		</div>
	</div>

<?php
	$widget->print_data_settings($data);
} // end if



