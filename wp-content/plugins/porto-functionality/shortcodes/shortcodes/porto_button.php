<?php
// Porto Button
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-button',
		array(
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_button',
		)
	);
}

function porto_shortcode_button( $settings, $content = null ) {
	ob_start();
	$template = porto_shortcode_template( 'porto_button' );
	if ( $template ) {
		include $template;
	}
	return ob_get_clean();
}
