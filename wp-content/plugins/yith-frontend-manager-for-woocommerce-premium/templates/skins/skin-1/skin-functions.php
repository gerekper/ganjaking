<?php
/**
 * Skin 1 functions
 *
 * @package YITH\FrontendManager\Templates
 */

/**
 * SKIN CUSTOM CODE GOES HERE
 */
function ywfm_skin1_specific_eunque() {
	if ( is_user_logged_in() ) {
		$skin     = get_option( 'yith_wcfm_skin', 'default' );
		$skin_url = YITH_WCFM_TEMPLATE_URL . 'skins/' . $skin . '/';
		wp_enqueue_style( 'ywfm-open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700', false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_script( 'ywfm-skin1-double-tap-to-go', $skin_url . 'assets/js/jquery.doubletaptogo.min.js', array( 'jquery' ), YITH_WCFM_VERSION, true );
		wp_enqueue_script( 'ywfm-skin1-script', $skin_url . 'assets/js/script.js', array( 'ywfm-skin1-double-tap-to-go' ), YITH_WCFM_VERSION, true );
	}
}

add_action( 'wp_enqueue_scripts', 'ywfm_skin1_specific_eunque', 15 );



add_action( 'yith_wcfm_before_account_navigation_link_list', 'ywfm_skin1_add_welcome_message' );

/**
 * Add a welcome user message before sidebar nav
 */
function ywfm_skin1_add_welcome_message() {
	$current_user    = wp_get_current_user();
	$user_first_name = $current_user->user_firstname;
	$user_last_name  = $current_user->user_lastname;
	$user_email      = $current_user->user_email;
	$display_name    = $current_user->display_name;

	/**
	 * APPLY_FILTERS: yith_wcfm_user_avatar_size
	 *
	 * Filters the size for the user avatar.
	 *
	 * @param int $avatar_size Avatar size.
	 *
	 * @return int
	 */
	$avatar_size = apply_filters( 'yith_wcfm_user_avatar_size', 88 );

	/**
	 * APPLY_FILTERS: yith_wcfm_user_avatar
	 *
	 * Filters the user avatar.
	 *
	 * @param string $user_avatar User avatar.
	 * @param int    $avatar_size Avatar size.
	 *
	 * @return string
	 */
	$user_avatar = apply_filters( 'yith_wcfm_user_avatar', get_avatar( $user_email, $avatar_size ), $avatar_size );
	$user_name   = '' === $user_first_name ? $display_name : $user_first_name . ' ' . $user_last_name;

	?>

	<div id="ywfm_user-infos">
		<div class="user-image">
			<?php echo wp_kses_post( $user_avatar ); ?>
		</div>
		<div class="user-name">
			<?php echo esc_html__( 'Hi', 'yith-frontend-manager-for-woocommerce' ) . ', ' . wp_kses_post( $user_name ); ?>
		</div>
	</div>
	<?php

}

/**
 * Enable support for mobile devices on each used theme
 */
function ywfm_add_mobile_viewport_support() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
}

add_action( 'wp_head', 'ywfm_add_mobile_viewport_support' );


/**
 * Remove the unused scripts and styles from the active theme
 */
function yfmfw_remove_scripts() {
	$wp_theme   = wp_get_theme();
	$theme_name = strtolower( $wp_theme->Name ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

	// Specific for YITH Nielsen theme.
	if ( 'nielsen' === $theme_name || 'rémy' === $theme_name || 'mindig' === $theme_name || 'desire-sexy-shop' === $theme_name || 'globe' === $theme_name || 'globe child' === $theme_name ) {

		/**
		 * Redefine the smooth scroll js function to avoid the functionality
		 */
		$remove_scroll = 'jQuery.srSmoothscroll = function() { return false; }';
		wp_add_inline_script( 'yit-common', $remove_scroll );

		wp_dequeue_style( 'bootstrap-twitter' );
		YIT_Asset()->remove( 'style', 'bootstrap-twitter' );
	}
}

add_action( 'wp_enqueue_scripts', 'yfmfw_remove_scripts', 100 );

if ( ! function_exists( 'yith_wcfm_load_skin1_template_loader' ) ) {
	/**
	 * Load Skin1 Template for header and footer
	 *
	 * @since 1.0.0
	 * @author YITH <plugins@yithemes.com>
	 * @return void
	 */
	function yith_wcfm_load_skin1_template_loader() {
		switch ( current_action() ) {
			case 'yith_wcfm_load_skin1_header':
				/**
				 * APPLY_FILTERS: yith_wcfm_remove_skin1_header
				 *
				 * Filters whether to remove the header in the skin 1.
				 *
				 * @param bool $remove_header Whether to remove the header in the skin 1 or not.
				 *
				 * @return bool
				 */
				if ( ! apply_filters( 'yith_wcfm_remove_skin1_header', false ) ) {
					yith_wcfm_get_template( 'header.php', array(), 'skins/skin-1' );
				}
				break;

			case 'yith_wcfm_load_skin1_footer':
				/**
				 * APPLY_FILTERS: yith_wcfm_remove_skin1_footer
				 *
				 * Filters whether to remove the footer in the skin 1.
				 *
				 * @param bool $remove_footer Whether to remove the footer in the skin 1 or not.
				 *
				 * @return bool
				 */
				if ( ! apply_filters( 'yith_wcfm_remove_skin1_footer', false ) ) {
					yith_wcfm_get_template( 'footer.php', array(), 'skins/skin-1' );
				}
				break;
		}
	}
}

add_action( 'yith_wcfm_load_skin1_header', 'yith_wcfm_load_skin1_template_loader' );
add_action( 'yith_wcfm_load_skin1_footer', 'yith_wcfm_load_skin1_template_loader' );
