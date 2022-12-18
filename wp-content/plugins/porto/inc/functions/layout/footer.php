<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'wp_footer', 'porto_action_footer', 20 );
if ( ! function_exists( 'porto_action_footer' ) ) :
	function porto_action_footer() {
		global $porto_settings;
		// js code (Theme Settings/General)
		if ( isset( $porto_settings['js-code'] ) && $porto_settings['js-code'] && ! porto_is_amp_endpoint() ) {
			echo '<script>' . porto_strip_script_tags( $porto_settings['js-code'] ) . '</script>';
		}
		if ( isset( $porto_settings['page-share-pos'] ) && $porto_settings['page-share-pos'] ) :
			?>
			<div class="page-share position-<?php echo esc_attr( $porto_settings['page-share-pos'] ); ?>">
				<?php get_template_part( 'share' ); ?>
			</div>
			<?php
		endif;
	}
endif;
