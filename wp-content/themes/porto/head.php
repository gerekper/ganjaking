<?php
global $porto_settings;

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

wp_head();
