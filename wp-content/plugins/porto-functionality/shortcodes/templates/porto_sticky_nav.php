<?php
$result = $container = $min_width = $bg_color = $skin = $link_color = $link_bg_color = $link_acolor = $link_abg_color = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'container'            => false,
			'min_width'            => 991,
			'bg_color'             => '',
			'skin'                 => 'custom',
			'link_color'           => '',
			'link_bg_color'        => '',
			'link_acolor'          => '',
			'link_abg_color'       => '',
			'animation_type'       => '',
			'animation_duration'   => 1000,
			'animation_delay'      => 0,
			'sticky_nav_item_list' => '',
			'el_class'             => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$style = '';
if ( $bg_color ) {
	$style = 'background-color:' . esc_attr( $bg_color ) . ';';
}

if ( 'custom' == $skin && ( $link_color || $link_bg_color || $link_acolor || $link_abg_color ) ) {
	$sc_class_escaped = 'porto-sticky-nav' . rand();
	$el_class        .= ' ' . $sc_class_escaped;
	?>
	<style>
	<?php
	if ( $link_color ) :
		?>
		.<?php echo $sc_class_escaped; ?> .nav-pills > li > a { color: <?php echo esc_html( $link_color ); ?> !important; }<?php endif; ?>
	<?php
	if ( $link_bg_color ) :
		?>
		.<?php echo $sc_class_escaped; ?> .nav-pills > li > a { background-color: <?php echo esc_html( $link_bg_color ); ?> !important; }<?php endif; ?>
	<?php
	if ( $link_acolor ) :
		?>
		.<?php echo $sc_class_escaped; ?> .nav-pills > li.active > a { color: <?php echo esc_html( $link_acolor ); ?> !important; }<?php endif; ?>
	<?php
	if ( $link_abg_color ) :
		?>
		.<?php echo $sc_class_escaped; ?> .nav-pills > li.active > a { background-color: <?php echo esc_html( $link_abg_color ); ?> !important; }<?php endif; ?>
	</style>
	<?php
}

$options             = array();
$options['minWidth'] = (int) $min_width;
$options             = json_encode( $options );

$result .= '<div class="sticky-nav-wrapper"><div class="porto-sticky-nav nav-secondary ' . esc_attr( $el_class ) . '" data-plugin-options="' . esc_attr( $options ) . '"';
if ( $style ) {
	$result .= ' style="' . $style . '"';
}
if ( $animation_type ) {
	$result .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$result .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$result .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$result .= '>';

if ( $container ) {
	$result .= '<div class="container">';
}

$result .= '<ul class="nav nav-pills' . ( 'custom' == $skin ? '' : ' nav-pills-' . esc_attr( $skin ) ) . '">';

if ( ! empty( $sticky_nav_item_list ) ) {
	ob_start();
	foreach ( $sticky_nav_item_list as $key => $atts ) {
		if ( $template = porto_shortcode_template( 'porto_sticky_nav_link' ) ) {
			if ( is_array( $atts['icon_image'] ) && ! empty( $atts['icon_image']['id'] ) ) {
				$atts['icon_image'] = (int) $atts['icon_image']['id'];
			}
			if ( isset( $atts['icon_cl'] ) && isset( $atts['icon_cl']['value'] ) ) {
				if ( isset( $atts['icon_cl']['library'] ) && isset( $atts['icon_cl']['value']['id'] ) ) {
					$atts['icon_type'] = $atts['icon_cl']['library'];
					$atts['icon']      = $atts['icon_cl']['value']['id'];
				} else {
					$atts['icon'] = $atts['icon_cl']['value'];
				}
			}
			if ( isset( $atts['link'] ) && isset( $atts['link']['url'] ) ) {
				$atts['link'] = $atts['link']['url'];
			}
			include $template;
		}
	}
	$result .= ob_get_clean();
} elseif ( ! empty( $content ) ) {
	$result .= do_shortcode( $content );
}


$result .= '</ul>';

if ( $container ) {
	$result .= '</div>';
}

$result .= '</div></div>';

echo porto_filter_output( $result );
