<?php

/**
 * SearchWP AdminBar.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin;

use SearchWP\Utils;
use SearchWP\Settings;

/**
 * Class AdminBar is responsible for implementing an entry in the WordPress Admin Bar.
 *
 * @since 4.0
 */
class AdminBar {

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 * @return void
	 */
	function __construct() {
		add_action( 'wp_before_admin_bar_render', [ __CLASS__, 'render' ] );
		add_filter( 'heartbeat_received',         [ __CLASS__ , 'heartbeat_received' ], 10, 2 );

		add_action( 'admin_head', function() {
			if ( ! \SearchWP\License::inactive_license_notice() ) {
				return;
			}

			?>
			<style>
				#wp-admin-bar-searchwp a {
					display: flex !important;
					align-items: center;
				}

				#wpadminbar .searchwp-admin-bar-icon {
					font-family: dashicons;
					line-height: 1;
					font-weight: 400;
					font-size: 20px;
					width: 20px;
					height: 20px;
					text-transform: none;
					color: #ca4a1f;
					margin-left: 6px;
				}
			</style>

			<?php
		} );
	}

	/**
	 * Callback to implement the Admin Bar entry.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function render() {
		global $wp_admin_bar, $pagenow, $post, $wpdb;

		if ( ! is_admin_bar_showing() || ! apply_filters( 'searchwp\admin_bar', current_user_can( Settings::get_capability() ) ) ) {
			return;
		}

		$options_page_url = add_query_arg(
			[ 'page' => Utils::$slug ],
			admin_url( 'options-general.php' )
		);

		// Add base Admin Bar menu entry.
		$wp_admin_bar->add_menu( [
			'id'    => Utils::$slug,
			'title' => \SearchWP\License::inactive_license_notice() ? '<span>SearchWP</span> <span class="dashicons dashicons-warning searchwp-admin-bar-icon"></span>' : 'SearchWP',
			'href'  => esc_url( $options_page_url ),
		] );

		if ( \SearchWP\License::inactive_license_notice() ) {
			// Add link to Settings page.
			$wp_admin_bar->add_menu( [
				'parent' => Utils::$slug,
				'id'     => Utils::$slug . '_support',
				'title'  => '<span style="color: #ca4a1f;">' . __( 'Activate License', 'searchwp' ) . '</span>',
				'href'   => esc_url( add_query_arg( [ 'tab' => 'support' ], $options_page_url ) ),
			] );
		}

		// Add link to Settings page.
		$wp_admin_bar->add_menu( [
			'parent' => Utils::$slug,
			'id'     => Utils::$slug . '_settings',
			'title'  => __( 'Settings', 'searchwp' ),
			'href'   => esc_url( $options_page_url ),
		] );

		if ( apply_filters( 'searchwp\admin_bar\statistics', true ) ) {
			// Add link to Statistics tab of Settings page.
			$wp_admin_bar->add_menu( [
				'parent' => Utils::$slug,
				'id'     => Utils::$slug . '_statistics',
				'title'  => __( 'Statistics', 'searchwp' ),
				'href'   => esc_url( add_query_arg( [ 'tab' => 'statistics' ], $options_page_url ) ),
			] );
		}

		do_action( 'searchwp\admin_bar\render', [ 'menu' => Utils::$slug ] );

		// If this is not a post edit screen, bail out.
		// TODO: This can be customized to account for any registered Source.
		if ( 'post.php' !== $pagenow || ! $post instanceof \WP_Post ) {
			return;
		}

		self::heartbeat_setup();

		// Add entry showing the index status.
		$wp_admin_bar->add_menu( [
			'parent' => Utils::$slug,
			'id'     => Utils::$slug . '_index_status',
			'title'  => Utils::get_source_entry_index_status( 'post' . SEARCHWP_SEPARATOR . $post->post_type, $post->ID ),
		] );
	}

	/**
	 * Setup updater and listener for Heartbeat API.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function heartbeat_setup() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'heartbeat' );

		add_action( 'admin_print_footer_scripts', [ __CLASS__, 'heartbeat_index_status' ], 20 );
	}

	/**
	 * Update the index status Admin Bar entry alongside Heartbeat.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function heartbeat_index_status() {
		global $post;
		?>
		<script>
			(function($){
				// Hook into the heartbeat-send.
				$(document).on('heartbeat-send', function(e, data) {
					data['searchwp_heartbeat_action'] = 'index_status';
					data['searchwp_heartbeat_object'] = {
						source: '<?php echo esc_js( 'post' . SEARCHWP_SEPARATOR . $post->post_type ); ?>',
						id: '<?php echo esc_js( $post->ID ); ?>'
					};
				});

				// listen for the custom event "heartbeat-tick" on $(document).
				$(document).on( 'heartbeat-tick', function(e, data) {
					if ( data['searchwp_index_status'] ) {
						$('#wp-admin-bar-<?php echo esc_js( SEARCHWP_PREFIX ); ?>index_status > div').text( data['searchwp_index_status'] );
					}
				});
			}(jQuery));
		</script>
		<?php
	}

	/**
	 * Append Source entry index status to Heartbeat updates.
	 *
	 * @since 4.0
	 * @param mixed $response
	 * @param mixed $data
	 * @return mixed|string[]
	 */
	public static function heartbeat_received( $response, $data ) {
		if ( isset( $data['searchwp_heartbeat_action'] ) && 'index_status' == $data['searchwp_heartbeat_action'] ) {
			$response['searchwp_index_status'] = Utils::get_source_entry_index_status(
				$data['searchwp_heartbeat_object']['source'],
				$data['searchwp_heartbeat_object']['id']
			);

		}
		return $response;
	}
}
