<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap porto-wrap">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Change Log', 'porto' ); ?></h2>
	<h2 class="porto-admin-nav">
		<?php
		printf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=porto' ) ), esc_html__( 'Theme License', 'porto' ) );
		printf( '<a href="#" class="active nolink">%s</a>', esc_html__( 'Change Log', 'porto' ) );
		if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
			printf( '<a href="%s">%s</a>', esc_url( admin_url( 'customize.php' ) ), esc_html__( 'Theme Options', 'porto' ) );
			printf( '<a href="%s">%s</a>', esc_url( admin_url( 'themes.php?page=porto_settings' ) ), esc_html__( 'Advanced', 'porto' ) );
		} else {
			printf( '<a href="%s">%s</a>', esc_url( admin_url( 'themes.php?page=porto_settings' ) ), esc_html__( 'Theme Options', 'porto' ) );
		}
		printf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=porto-setup-wizard' ) ), esc_html__( 'Setup Wizard', 'porto' ) );
		printf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) ), esc_html__( 'Speed Optimize Wizard', 'porto' ) );
		?>
	</h2>
	<div class="porto-admin-header">
		<div class="header-left">
			<h1><?php esc_html_e( 'Welcome to Porto!', 'porto' ); ?></h1>
			<h6><?php echo esc_html__( 'Porto is now installed and ready to use! Read below for additional information. We hope you enjoy it!', 'porto' ); ?></h6>
		</div>
		<div class="header-right">
			<?php /* translators: theme version */ ?>
			<div class="porto-logo"><img src="<?php echo PORTO_URI . '/images/logo/logo_white_small.png'; ?>" alt=""><span class="version"><?php printf( __( 'version %s', 'porto' ), PORTO_VERSION ); ?></span></div>
		</div>
	</div>
	<main>
		<div class="porto-section porto-changelog">
			<?php

				require_once PORTO_PLUGINS . '/importer/importer-api.php';
				$importer_api = new Porto_Importer_API();
				$result       = $importer_api->get_response( 'changelog', array(), 'text' );
			if ( ! is_wp_error( $result ) ) {
				echo porto_strip_script_tags( $result );
			}
			?>
		</div>
		<div class="porto-thanks">
			<p class="description"><?php esc_html_e( 'Thank you, we hope you to enjoy using Porto!', 'porto' ); ?></p>
		</div>
	</main>
</div>
