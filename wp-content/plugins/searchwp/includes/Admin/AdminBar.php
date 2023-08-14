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
use SearchWP\License;

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
		add_filter( 'heartbeat_received',         [ __CLASS__, 'heartbeat_received' ], 10, 2 );
	}

	/**
	 * Get notifications counter HTML.
	 *
	 * @since 4.2.8
	 */
	private static function get_notifications_counter_html() {

		$style = '
			display: inline-block;
			min-width: 18px;
			height: 18px;
			border-radius: 9px;
			margin: 7px 0 0 2px;
			vertical-align: top;
			font-size: 11px;
			line-height: 1.6;
			text-align: center;
		';

		$notifications_count = 0;

		if ( License::inactive_license_notice() ) {
			++ $notifications_count;
		}

		$notifications_count = apply_filters( 'searchwp\admin_bar\notifications_count', $notifications_count );

		if ( empty( $notifications_count ) ) {
			return '';
		}

		return '<span class="wp-ui-notification searchwp-menu-notification-counter" style="' . $style . '">' . absint( $notifications_count ) . '</span>';
	}

	/**
	 * Callback to implement the Admin Bar entry.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function render() {
		global $wp_admin_bar, $pagenow, $post;

		if ( ! is_admin_bar_showing() || ! apply_filters( 'searchwp\admin_bar', current_user_can( Settings::get_capability() ) ) ) {
			return;
		}

		$algorithm_page_url = add_query_arg(
			[ 'page' => 'searchwp-algorithm' ],
			admin_url( 'admin.php' )
		);

		// Add base Admin Bar menu entry.
		$wp_admin_bar->add_menu( [
			'id'    => Utils::$slug,
			'title' => '<span>SearchWP</span> ' . self::get_notifications_counter_html(),
			'href'  => esc_url( $algorithm_page_url ),
		] );

		if ( License::inactive_license_notice() ) {
			// Add link to Settings page.
			$wp_admin_bar->add_menu( [
				'parent' => Utils::$slug,
				'id'     => Utils::$slug . '_support',
				'title'  => '<span style="color: #ff6b6b; line-height: 1;">' . __( 'Activate License', 'searchwp' ) . '</span>',
				'href'   => esc_url( add_query_arg( [ 'page' => 'searchwp-settings' ], admin_url( 'admin.php' ) ) ),
			] );
		}

		if ( apply_filters( 'searchwp\options\settings_screen', true ) ) {
			// Add link to Algorithm page.
			$wp_admin_bar->add_menu( [
				'parent' => Utils::$slug,
				'id'     => Utils::$slug . '_settings',
				'title'  => __( 'Algorithm', 'searchwp' ),
				'href'   => esc_url( $algorithm_page_url ),
			] );
		}

		if ( apply_filters( 'searchwp\admin_bar\statistics', true ) ) {
			// Add link to Statistics page.
			$wp_admin_bar->add_menu( [
				'parent' => Utils::$slug,
				'id'     => Utils::$slug . '_statistics',
				'title'  => __( 'Statistics', 'searchwp' ),
				'href'   => esc_url( add_query_arg( [ 'page' => 'searchwp-statistics' ], admin_url( 'admin.php' ) ) ),
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
