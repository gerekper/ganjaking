<?php
$default_atts = array(
	'selector'        => '',
	'hover_effect'    => 'plus',
	'inner_icon'      => '',
	'icon_type'       => 'fontawesome',
	'icon_simpleline' => '',
	'icon_porto'      => '',
	'cursor_w'        => '',
	'el_id'           => '',
);
extract( // @codingStandardsIgnoreLine
	shortcode_atts(
		$default_atts,
		$atts
	)
);

switch ( $icon_type ) {
	case 'simpleline':
		$inner_icon = $icon_simpleline;
		break;
	case 'porto':
		$inner_icon = $icon_porto;
		break;
}

wp_enqueue_script( 'porto-cursor-effect', PORTO_SHORTCODES_URL . 'assets/js/porto-cursor-effect.min.js', array( 'jquery-core' ), PORTO_SHORTCODES_VERSION, true );

$cursor_cls = '';
if ( ! empty( $shortcode_class ) ) {
	$cursor_cls .= trim( $shortcode_class );
} elseif ( ! empty( $el_id ) ) {
	$cursor_cls = 'cursor-element-' . $el_id;
}

if ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) :
	?>
<script>
	if ( typeof window.porto_cursor_effects == 'undefined' ) {
		window.porto_cursor_effects = [];
	}
	window.porto_cursor_effects.forEach( function( i, index ) {
		if ( i.id && '<?php echo esc_js( $cursor_cls ); ?>' == i.id ) {
			window.porto_cursor_effects.splice( index, 1 );
			return false;
		}
	} );
	window.porto_cursor_effects.push( { id: '<?php echo esc_js( $cursor_cls ); ?>', selector: '<?php echo sanitize_text_field( str_replace( '&gt;', '>', $selector ) ); ?>', hover_effect: '<?php echo esc_js( $hover_effect ); ?>', icon: '<?php echo esc_js( $inner_icon ); ?>', cursor_w: <?php echo (int) $cursor_w; ?> } );
</script>
	<?php
elseif ( ! empty( $shortcode_class ) ):
	echo '<div class="opacity-0"></div>';
	echo '<span class="shortcode-class d-none">' . esc_html( $shortcode_class ) . '</span>';
endif;
