<?php
global $porto_settings, $porto_settings_optimize;

// For Favicon
if ( $porto_settings['favicon'] ) : ?>
	<link rel="shortcut icon" href="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $porto_settings['favicon']['url'] ) ); ?>" type="image/x-icon" />
	<?php
endif;

// For iPhone
if ( $porto_settings['icon-iphone'] ) :
	?>
	<link rel="apple-touch-icon" href="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $porto_settings['icon-iphone']['url'] ) ); ?>">
	<?php
endif;

// For iPhone Retina
if ( $porto_settings['icon-iphone-retina'] ) :
	?>
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $porto_settings['icon-iphone-retina']['url'] ) ); ?>">
	<?php
endif;

// For iPad
if ( $porto_settings['icon-ipad'] ) :
	?>
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $porto_settings['icon-ipad']['url'] ) ); ?>">
	<?php
endif;

// For iPad Retina
if ( $porto_settings['icon-ipad-retina'] ) :
	?>
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', $porto_settings['icon-ipad-retina']['url'] ) ); ?>">
	<?php
endif;

if ( isset( $porto_settings_optimize['preload'] ) ) {
	if ( in_array( 'porto', $porto_settings_optimize['preload'] ) ) {
		echo '<link rel="preload" href="' . PORTO_URI . '/fonts/porto-font/porto.woff2" as="font" type="font/woff2" crossorigin>';
	}
	$font_awesome_font = ! empty( $porto_settings_optimize['optimize_fontawesome'] ) ? 'fontawesome_optimized' : 'fontawesome';
	if ( in_array( 'fas', $porto_settings_optimize['preload'] ) ) {
		echo '<link rel="preload" href="' . PORTO_URI . '/fonts/' . $font_awesome_font . '/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>';
	}
	if ( in_array( 'far', $porto_settings_optimize['preload'] ) ) {
		echo '<link rel="preload" href="' . PORTO_URI . '/fonts/' . $font_awesome_font . '/fa-regular-400.woff2" as="font" type="font/woff2" crossorigin>';
	}
	if ( in_array( 'fab', $porto_settings_optimize['preload'] ) ) {
		echo '<link rel="preload" href="' . PORTO_URI . '/fonts/' . $font_awesome_font . '/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin>';
	}
	if ( in_array( 'sli', $porto_settings_optimize['preload'] ) ) {
		echo '<link rel="preload" href="' . PORTO_URI . '/fonts/Simple-Line-Icons/Simple-Line-Icons.ttf" as="font" type="font/ttf" crossorigin>';
	}
}
if ( ! empty( $porto_settings_optimize['preload_custom'] ) ) {
	$font_urls = explode( "\n", $porto_settings_optimize['preload_custom'] );
	foreach ( $font_urls as $font_url ) {
		$dot_pos = strrpos( $font_url, '.' );
		if ( false !== $dot_pos ) {
			$type = substr( $font_url, $dot_pos + 1 );
			echo '<link rel="preload" href="' . esc_url( $font_url ) . '" as="font" type="font/' . esc_attr( $type ) . '" crossorigin>';
		}
	}
}

wp_head();
