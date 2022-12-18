<?php
/**
 * Porto Content Switcher Render Template
 *
 * @since 2.6.0
 */
extract(
	shortcode_atts(
		array(
			'first_label'    => esc_html__( 'First', 'porto-functionality' ),
			'second_label'   => esc_html__( 'Second', 'porto-functionality' ),
			'first_content'  => esc_html__( 'Pellentesque pellentesque tempor tellus eget hendrerit. Morbi id aliquam ligula.', 'porto-functionality' ),
			'second_content' => esc_html__( 'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.', 'porto-functionality' ),
			'el_class'       => '',
			'page_builder'   => '',
		),
		$atts
	)
);
wp_enqueue_script( 'porto-content-switch' );
$output = '';

// WPBakery
if ( ! empty( $shortcode_class ) ) {
	$el_class .= $shortcode_class;
}

if ( empty( $page_builder ) ) {
	if ( ! empty( $atts['first_content'] ) ) {
		$first_content = rawurldecode( base64_decode( $first_content ) );
	}
	if ( ! empty( $atts['second_content'] ) ) {
		$second_content = rawurldecode( base64_decode( $second_content ) );
	}
}
ob_start();
?>
<div class="tabs content-switcher-wrapper <?php echo esc_attr( $el_class ); ?>">
	<div class="nav content-switch">
		<div class="text-first switcher-label active" data-switch-id="nav-first">
			<?php echo ( ! empty( $first_label ) ? esc_attr( $first_label ) : 'First' ); ?>
		</div>
		<label class="switch-input">
			<input class="switch-toggle" aria-label="Content Switcher" type="checkbox" data-content-switcher>
			<span class="toggle-button"></span>
		</label>
		<div class="text-second switcher-label" data-switch-id="nav-second">
			<?php echo ( ! empty( $second_label ) ? esc_attr( $second_label ) : 'Second' ); ?>
		</div>
	</div>
	<div class="tab-content">
		<div data-content-id="nav-first" class="switch-content content-first active">
			<?php echo do_shortcode( $first_content ); ?>
		</div>
		<div data-content-id="nav-second" class="switch-content content-second">
			<?php echo do_shortcode( $second_content ); ?>
		</div>
	</div>
</div>
<?php
echo ob_get_clean();
