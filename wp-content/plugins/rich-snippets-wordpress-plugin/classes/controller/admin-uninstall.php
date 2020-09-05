<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Uninstall.
 *
 * Uninstalls theh plugin.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.13.1
 */
final class Admin_Uninstall_Controller {

	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.13.1
	 */
	public function __construct() {

		if ( ! wp_verify_nonce( $_GET['_wpnonce'] ?? 1, 'rich-snippets-uninstall' ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You are not allowed to do this.', 'rich-snippets-schema' ) );
		}

		$this->enqueue_scripts();
		$this->uninstall_page();
	}


	/**
	 * Enqueues plugin page styles and scripts.
	 *
	 * @since 2.13.1
	 */
	public function enqueue_scripts() {
		wp_register_style(
			'wpb-rs-admin-uninstall',
			plugins_url( 'css/admin-uninstall.css', rich_snippets()->get_plugin_file() ),
			[ 'install' ],
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-uninstall.css' )
		);

		wp_enqueue_style( 'wpb-rs-admin-uninstall' );
	}


	/**
	 * Prepare uninstall page.
	 *
	 * @since 2.13.1
	 */
	public function uninstall_page() {

		$step = absint( $_GET['step'] ?? 1 );

		if ( 2 === $step ) {
			$this->uninstall();
		}

		$this->page_header();
		?>
        <div class="wpb-rs-uninstall-window">
            <p><?php _e( 'This will delete all data that was created by SNIP including all your Global Snippets and all settings. The plugin will then be deactivated so that you can delete it from your plugin list if you like. Are you sure you want to do this?', 'rich-snippets-schema' ); ?></p>

            <p>
                <a href="<?php
				echo esc_url( add_query_arg( [
					'page'     => 'rich-snippets-uninstall',
					'_wpnonce' => wp_create_nonce( 'rich-snippets-uninstall' ),
					'step'     => 2,
				], admin_url( 'index.php' ) ) );
				?>" class="button button-link-delete button-hero"><?php _e( 'Yes, uninstall please.', 'rich-snippets-schema' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'index.php' ) ); ?>" class="button button-hero"><?php _e( 'No. Do not uninstall and go back.', 'rich-snippets-schema' ); ?></a>
            </p>
        </div>
		<?php
		$this->page_footer();

		exit;
	}


	/**
	 * Prints the page header
	 *
	 * @since 2.13.1
	 */
	private function page_header() {
		set_current_screen();
		?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php esc_html_e( 'SNIP Uninstalling', 'rich-snippets-schema' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'wpb-rs-admin-uninstall' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
        </head>
        <body class="wpb-rs-uninstall wp-core-ui">
        <h1 id="wpb-rs-logo">
            <a href="https://rich-snippets.io"><img src="<?php echo esc_url( plugin_dir_url( rich_snippets()->get_plugin_file() ) ); ?>/img/snip.svg" alt="SNIP" /></a>
        </h1>
		<?php
	}


	/**
	 * Prints the page footer.
	 *
	 * @since 2.13.1
	 */
	private function page_footer() {
		?>
        </body>
        </html>
		<?php
	}


	/**
	 * Uninstalls the plugin.
	 *
	 * @since 2.13.1
	 */
	private function uninstall() {

		/**
		 * from the old deprecated version
		 */
		call_user_func( function () {
			$file = __DIR__ . '/uninstall-deprecated.php';
			if ( ! is_file( $file ) ) {
				return;
			}

			require_once $file;
		} );


		/*
		 * From the new version
		 */
		call_user_func( function () {

			global $wpdb;

			# delete user meta that is no longer needed
			$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '%wpb-rs-global%' " );

			# delete options
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wpb_rs%' " );

			# delete transients and transient timeouts
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient%wpb_rs%' " );

			/**
			 * Delete posts and post meta
			 */
			$global_snippet_ids = $wpdb->get_results( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'wpb-rs-global'" );
			if ( $global_snippet_ids && is_array( $global_snippet_ids ) && count( $global_snippet_ids ) > 0 ) {
				$global_snippet_ids = wp_list_pluck( $global_snippet_ids, 'ID' );

				foreach ( $global_snippet_ids as $global_snippet_id ) {
					wp_delete_post( $global_snippet_id, true );
				}
			}

			# Delete rich snippets from other post meta
			$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_wpb_rs%' " );

			# delete the magic variable
			delete_option( 'd3BiX3JzL3ZlcmlmaWVk' );
			delete_option( base64_encode( 'd3BiX3JzL3ZlcmlmaWVk' ) );

			# delete all overwritten data
			$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'snippet_%' " );
		} );


		/**
		 * Deactivate the plugin
		 */
		$plugin = plugin_basename( rich_snippets()->get_plugin_file() );
		deactivate_plugins( [ $plugin ] );


		/**
		 * Redirect
		 */
		$url = add_query_arg( [
			'plugin_status' => 'inactive'
		], admin_url( 'plugins.php' ) );
		wp_redirect( $url );
		exit();
	}
}
