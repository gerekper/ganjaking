<?php
$output = $skin = $border_top_color = $border_radius = $border_top_width = $bg_top_color = $bg_bottom_color = $align = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'skin'                    => 'custom',
			'border_top_color'        => '',
			'border_radius'           => '',
			'border_top_width'        => '',
			'bg_type'                 => '',
			'bg_top_color'            => '',
			'bg_bottom_color'         => '',
			'align'                   => '',
			'show_icon'               => false,
			'icon_type'               => 'fontawesome',
			'icon'                    => '',
			'icon_simpleline'         => '',
			'icon_image'              => '',
			'box_style'               => '',
			'box_effect'              => '',
			'icon_color'              => '',
			'icon_bg_color'           => '',
			'icon_border_color'       => '',
			'icon_wrap_border_color'  => '',
			'icon_shadow_color'       => '',
			'icon_hcolor'             => '',
			'icon_hbg_color'          => '',
			'icon_hborder_color'      => '',
			'icon_wrap_hborder_color' => '',
			'icon_hshadow_color'      => '',
			'animation_type'          => '',
			'animation_duration'      => 1000,
			'animation_delay'         => 0,
			'el_class'                => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

switch ( $icon_type ) {
	case 'simpleline':
		$icon_class = $icon_simpleline;
		break;
	case 'porto':
		$icon_class = $icon_porto;
		break;
	case 'image':
		$icon_class = 'icon-image';
		break;
	default:
		$icon_class = $icon;
}
if ( ! $show_icon ) {
	$icon_class = '';
}

if ( 'custom' == $skin && ( $box_style || $box_effect || $icon_class ) ) {
	$sc_class  = 'porto-content-box' . rand();
	$el_class .= ' ' . $sc_class;
	?>
	<style>
	<?php
	if ( $icon_class ) :
		if ( $icon_color || $icon_bg_color || $icon_border_color ) :
			?>
			.<?php echo '' . $sc_class; ?> .featured-box .icon-featured {
				<?php if ( $icon_color ) : ?>
					color: <?php echo esc_html( $icon_color ); ?>;
				<?php endif; ?>
				<?php if ( $icon_bg_color ) : ?>
					background-color: <?php echo esc_html( $icon_bg_color ); ?>;
				<?php endif; ?>
				<?php if ( $icon_border_color ) : ?>
					border-color: <?php echo esc_html( $icon_border_color ); ?>;
				<?php endif; ?>
			}
			<?php
		endif;
		if ( $icon_hcolor || $icon_hbg_color || $icon_hborder_color ) :
			?>
			.<?php echo '' . $sc_class; ?> .featured-box:hover .icon-featured {
				<?php if ( $icon_hcolor ) : ?>
					color: <?php echo esc_html( $icon_hcolor ); ?>;
				<?php endif; ?>
				<?php if ( $icon_hbg_color ) : ?>
					background-color: <?php echo esc_html( $icon_hbg_color ); ?>;
				<?php endif; ?>
				<?php if ( $icon_hborder_color ) : ?>
					border-color: <?php echo esc_html( $icon_hborder_color ); ?>;
				<?php endif; ?>
			}
			<?php
		endif;
		if ( 'featured-boxes-style-7' == $box_style ) :
			if ( $icon_shadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box .icon-featured:after {
				box-shadow: 3px 3px <?php echo esc_html( $icon_shadow_color ); ?>;
			}
				<?php
			endif;
			if ( $icon_hshadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box:hover .icon-featured:after {
				box-shadow: 3px 3px <?php echo esc_html( $icon_hshadow_color ); ?>;
			}
				<?php
			endif;
		endif;
		if ( 'featured-box-effect-1' == $box_effect || 'featured-box-effect-2' == $box_effect ) :
			if ( $icon_shadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box .icon-featured:after {
				box-shadow: 0 0 0 3px <?php echo esc_html( $icon_shadow_color ); ?>;
			}
				<?php
			endif;
			if ( $icon_hshadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box:hover .icon-featured:after {
				box-shadow: 0 0 0 3px <?php echo esc_html( $icon_hshadow_color ); ?>;
			}
				<?php
			endif;
		endif;
		if ( 'featured-box-effect-3' == $box_effect ) :
			if ( $icon_shadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box .icon-featured:after {
				box-shadow: 0 0 0 10px <?php echo esc_html( $icon_shadow_color ); ?>;
			}
				<?php
			endif;
			if ( $icon_hshadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box:hover .icon-featured:after {
				box-shadow: 0 0 0 10px <?php echo esc_html( $icon_hshadow_color ); ?>;
			}
				<?php
			endif;
		endif;
		if ( 'featured-box-effect-7' == $box_effect ) :
			if ( $icon_shadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box .icon-featured:after {
				box-shadow: 3px 3px <?php echo esc_html( $icon_shadow_color ); ?>;
			}
				<?php
			endif;
			if ( $icon_hshadow_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box:hover .icon-featured:after {
				box-shadow: 3px 3px <?php echo esc_html( $icon_hshadow_color ); ?>;
			}
				<?php
			endif;
		endif;
		if ( 'featured-boxes-style-6' == $box_style ) :
			if ( $icon_wrap_border_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box .icon-featured:after {
				border-color: <?php echo esc_html( $icon_wrap_border_color ); ?>;
			}
				<?php
			endif;
			if ( $icon_wrap_hborder_color ) :
				?>
			.<?php echo '' . $sc_class; ?> .featured-box:hover .icon-featured:after {
				border-color: <?php echo esc_html( $icon_wrap_hborder_color ); ?>;
			}
				<?php
			endif;
		endif;
	endif;
	?>
	</style>
	<?php
}

if ( $bg_type ) {
	$el_class .= ' ' . $bg_type;
}

if ( $box_style ) {
	$el_class .= ' ' . $box_style;
}

$output = '<div class="porto-content-box featured-boxes wpb_content_element ' . esc_attr( $el_class ) . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';

$output .= '<div class="featured-box ' . esc_attr( ( 'custom' != $skin ? 'featured-box-' . $skin . ' ' : '' ) . $box_effect . ( $align ? ' align-' . $align : '' ) ) . '"' . ( 'custom' == $skin ? ' style="' . ( ( $border_radius ) ? 'border-radius:' . esc_attr( $border_radius ) . 'px;' : '' ) .
	( ( $bg_top_color && $bg_bottom_color ) ? 'background:-webkit-linear-gradient(top, ' . esc_attr( $bg_top_color ) . ' 1%, ' . esc_attr( $bg_bottom_color ) . ' 98%) repeat scroll 0 0 transparent; background: linear-gradient(to bottom, ' . esc_attr( $bg_top_color ) . ' 1%, ' . esc_attr( $bg_bottom_color ) . ' 98%) repeat scroll 0 0 transparent; ' : '' ) . '"' : '' ) . '>';
$output .= '<div class="box-content" style="' . ( ( $border_radius ) ? 'border-radius:' . esc_attr( $border_radius ) . 'px;' : '' ) .
	( $border_top_color ? 'border-top-color:' . esc_attr( $border_top_color ) . ';' : '' ) . ( $border_top_width ? 'border-top-width:' . esc_attr( $border_top_width ) . 'px;' : '' ) . '">';
if ( $icon_class ) {
	if ( 'custom' != $skin ) {
		if ( in_array( $box_style, array( 'featured-boxes-style-3', 'featured-boxes-style-4', 'featured-boxes-style-5', 'featured-boxes-style-6', 'featured-boxes-style-8' ) ) ) {
			$icon_class .= ' text-color-' . $skin;
		}
		if ( in_array( $box_style, array( 'featured-boxes-style-3', 'featured-boxes-style-4' ) ) ) {
			$icon_class .= ' border-color-' . $skin;
		}
	} else {
		if ( in_array( $box_style, array( 'featured-boxes-style-3', 'featured-boxes-style-4', 'featured-boxes-style-5', 'featured-boxes-style-6', 'featured-boxes-style-7', 'featured-boxes-style-8' ) ) ) {
			$icon_class .= ' text-color-primary';
		}
		if ( in_array( $box_style, array( 'featured-boxes-style-3', 'featured-boxes-style-4' ) ) ) {
			$icon_class .= ' border-color-primary';
		}
	}
	$output .= '<i class="icon-featured ' . esc_attr( $icon_class ) . '">';
	if ( 'icon-image' == $icon_class && $icon_image ) {
		$icon_image = preg_replace( '/[^\d]/', '', $icon_image );
		$image_url  = wp_get_attachment_url( $icon_image );
		$alt_text   = get_post_meta( $icon_image, '_wp_attachment_image_alt', true );
		$image_url  = str_replace( array( 'http:', 'https:' ), '', $image_url );
		if ( $image_url ) {
			$output .= '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $image_url ) . '">';
		}
	}
	$output .= '</i>';
}
$output .= do_shortcode( $content );
$output .= '</div></div>';

$output .= '</div>';

echo porto_filter_output( $output );
