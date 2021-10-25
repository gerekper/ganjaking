<?php

if(!defined('ABSPATH')) {
	exit;
}

/** @var \ElementorModal\Widgets\GT3_Core_Elementor_Widget_VideoPopup $widget */

$settings = array();

$settings = wp_parse_args($widget->get_settings(), $settings);

$video_title = ! empty( $settings['video_title'] ) ? '<h2 class="video-popup__title">' . esc_html( $settings['video_title'] ) . '</h2>' : '';
$anim_divs   = '';

$anim_style = array();

if ( $settings['button_animation'] !== 'none' ) {
	$settings['count_lines'] = 1;
	if ( $settings['button_animation'] == 'type2' ) {
		$settings['lines_width'] = 0;
		$settings['shadow_lines_width'] = 0;
	}
	$duration     = (int) $settings['count_lines']*(int) $settings['lines_delay'];
	$anim_style[] = esc_attr( '-webkit-animation-duration: ' . (int) $duration . 'ms;' );
	$anim_style[] = esc_attr( '-moz-animation-duration: ' . (int) $duration . 'ms;' );
	$anim_style[] = esc_attr( '-o-animation-duration: ' . (int) $duration . 'ms;' );
	$anim_style[] = esc_attr( 'animation-duration: ' . (int) $duration . 'ms;' );
	$anim_style[] = $settings['button_animation'] == 'type1' ? esc_attr( 'box-shadow: 0 0 ' . (int) $settings['shadow_lines_width'] . 'px ' . (int) $settings['lines_width'] . 'px ' . esc_attr( $settings['color_lines'] ) . ';' ) : 'color: ' . esc_attr( $settings['color_lines'] ) . ';';

	$anim_divs .= '<div class="video-popup-animation">';

	$x = 0;
	while ( $x < (int) $settings['count_lines'] ) {
		$delay           = $settings['lines_delay']*$x;
		$animation_style = array();

		$animation_style[] = esc_attr( '-webkit-animation-delay: ' . (int) $delay . 'ms;' );
		$animation_style[] = esc_attr( '-moz-animation-delay: ' . (int) $delay . 'ms;' );
		$animation_style[] = esc_attr( '-o-animation-delay: ' . (int) $delay . 'ms;' );
		$animation_style[] = esc_attr( 'animation-delay: ' . (int) $delay . 'ms;' );

		$widget->add_render_attribute( 'anim_line_' . $x, 'style', ( $anim_style ) );
		$widget->add_render_attribute( 'anim_line_' . $x, 'style', ( $animation_style ) );

		$anim_divs .= '<div ' . $widget->get_render_attribute_string( 'anim_line_' . $x ) . '></div>';
		$x ++;
	}

	$anim_divs .= '</div>';
}
$swipebox = array(
	'autoplay' => ! ! $settings['autoplay'],
);
$widget->add_render_attribute( 'video', 'class', 'swipebox-video' );
$widget->add_render_attribute( 'video', 'href', esc_url( $settings['video_link'] ) );
$widget->add_render_attribute( 'video', 'data-settings', wp_json_encode( $swipebox ) );


$widget->add_render_attribute( 'video', 'class', 'video-popup__link' );

$widget->add_render_attribute( 'wrapper', 'class', 'video-popup-wrapper' );
if ( ! empty( $settings['align'] ) ) {
	$widget->add_render_attribute( 'wrapper', 'class', ' video-popup-wrapper__' . esc_attr( $settings['align'] ) );
}
if ( $settings['button_animation'] != 'none' ) {
	$widget->add_render_attribute( 'wrapper', 'class', ' video-popup-animation-' . esc_attr( $settings['button_animation'] ) );
}
?>
    <div <?php $widget->print_render_attribute_string( 'wrapper' ) ?>>
		<?php
		if ( (! empty( $settings['align'] ) && $settings['align'] != 'left') ||  empty( $settings['align'])) {
			echo ''.($video_title);
		}
		?>
        <a <?php $widget->print_render_attribute_string( 'video' ) ?>>
			<?php echo '' . $anim_divs; ?>
            <svg width="13" height="18">
                <polygon points="1,1 1,16 11,9" stroke-width="2" />
            </svg>
        </a>
		<?php
		if ( ! empty( $settings['align'] ) && $settings['align'] == 'left' ) {
			echo ''.($video_title);
		}
		?>
    </div>
<?php




