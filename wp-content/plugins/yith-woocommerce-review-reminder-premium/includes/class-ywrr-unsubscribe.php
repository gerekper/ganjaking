<?php
/**
 * Unsubscribe class
 *
 * @package YITH\ReviewReminder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWRR_Unsubscribe' ) && ! class_exists( 'YWRAC_Unsubscribe' ) ) {

	/**
	 * Implements Unsubscribe module for YWRR plugin
	 *
	 * @class   YWRR_Unsubscribe
	 * @since   1.1.5
	 * @author  YITH <plugins@yithemes.com>
	 *
	 * @package YITH
	 */
	class YWRR_Unsubscribe {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.1.5
		 */
		public function __construct() {

			add_action( 'admin_notices', array( $this, 'protect_unsubscribe_page_notice' ) );
			add_action( 'wp_trash_post', array( $this, 'protect_unsubscribe_page' ) );
			add_action( 'before_delete_post', array( $this, 'protect_unsubscribe_page' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			add_shortcode( 'ywrr_unsubscribe', array( $this, 'unsubscribe' ) );
			add_shortcode( 'ywrac_unsubscribe', array( $this, 'unsubscribe' ) );
			add_filter( 'wp_get_nav_menu_items', array( $this, 'hide_unsubscribe_page' ) );
			add_action( 'admin_init', array( $this, 'create_unsubscribe_page' ) );

		}

		/**
		 * Creates the unsubscribe page
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function create_unsubscribe_page() {
			$page_exists = false;

			if ( get_post_status( get_option( 'ywrr_unsubscribe_page_id' ) ) ) {
				$page_exists = true;
			}

			if ( ! $page_exists && get_post_status( get_option( 'ywrac_unsubscribe_page_id' ) ) ) {
				$page_exists = true;
			}

			if ( $page_exists ) {
				return;
			}

			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => _x( 'unsubscribe', 'Page slug', 'yith-woocommerce-review-reminder' ),
				'post_title'     => _x( 'Unsubscribe', 'Page title', 'yith-woocommerce-review-reminder' ),
				'post_content'   => '<!-- wp:shortcode -->[ywrr_unsubscribe]<!-- /wp:shortcode -->',
				'post_parent'    => 0,
				'comment_status' => 'closed',
			);

			$page_id = wp_insert_post( $page_data );

			update_option( 'ywrr_unsubscribe_page_id', $page_id );

		}

		/**
		 * Notifies the inability to delete the page
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function protect_unsubscribe_page_notice() {

			global $post_type, $pagenow;

			if ( 'edit.php' === $pagenow && 'page' === $post_type && isset( $_GET['impossible'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				echo '<div id="message" class="error"><p>' . esc_html__( 'The Unsubscribe page cannot be deleted', 'yith-woocommerce-review-reminder' ) . '</p></div>';
			}

		}

		/**
		 * Prevent the deletion of unsubscribe page
		 *
		 * @param integer $post_id The post ID.
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function protect_unsubscribe_page( $post_id ) {

			if ( get_option( 'ywrr_unsubscribe_page_id' ) === $post_id || get_option( 'ywrac_unsubscribe_page_id' ) === $post_id ) {

				$query_args = array(
					'post_type'  => 'page',
					'impossible' => '1',
				);
				$error_url  = esc_url( add_query_arg( $query_args, admin_url( 'edit.php' ) ) );

				wp_safe_redirect( $error_url );
				exit();

			}

		}

		/**
		 * Hides unsubscribe page from menus
		 *
		 * @param array $items Pages array.
		 *
		 * @return  array
		 * @since   1.0.0
		 */
		public function hide_unsubscribe_page( $items ) {

			foreach ( $items as $key => $value ) {
				if ( 'unsubscribe' === basename( $value->url ) ) {
					unset( $items[ $key ] );
				}
			}

			return $items;

		}

		/**
		 * Initializes Javascript with localization
		 *
		 * @return  void
		 * @since   1.1.5
		 */
		public function frontend_scripts() {

			global $post;

			if ( $post instanceof WP_Post && ( (int) get_option( 'ywrr_unsubscribe_page_id' ) === $post->ID || (int) get_option( 'ywrac_unsubscribe_page_id' ) === $post->ID ) ) {

				wp_enqueue_script( 'ywrr-unsubscribe', yit_load_js_file( YWRR_ASSETS_URL . 'js/ywrr-unsubscribe.js' ), array( 'jquery' ), YWRR_VERSION, false );

				$params = array(
					'ajax_url' => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
				);

				wp_localize_script( 'ywrr-unsubscribe', 'ywrr_unsubscribe', $params );

			}
		}

		/**
		 * Unsubscribe page shortcode.
		 *
		 * @return  string
		 * @since   1.0.0
		 */
		public function unsubscribe() {

			ob_start();

			$email  = '';
			$getted = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$type = isset( $getted['type'] ) ? $getted['type'] : '';

			if ( '' === $type ) {
				$type = isset( $getted['action'] ) ? $getted['action'] : '';
			}

			$path = '';

			?>
			<div class="woocommerce ywrr-unsubscribe-form">
				<?php

				switch ( $type ) {
					case 'ywrr':
						$path  = function_exists( 'YITH_WRR' ) ? YWRR_TEMPLATE_PATH : '';
						$email = isset( $getted['email'] ) ? $getted['email'] : '';
						break;
					case 'ywrac':
					case '_ywrac_unsubscribe_from_mail':
						$path  = defined( 'YITH_YWRAC_PREMIUM' ) && YITH_YWRAC_PREMIUM ? YITH_YWRAC_TEMPLATE_PATH : '';
						$email = isset( $getted['customer'] ) && is_email( $getted['customer'] ) ? $getted['customer'] : '';
						break;
				}

				if ( '_ywrac_unsubscribe_from_mail' === $type ) {
					$type = 'ywrac';
				}

				if ( '' !== $path && '' !== $email ) {
					wc_get_template( $type . '-unsubscribe.php', array(), $path, $path );

				} else {
					?>
					<p class="return-to-shop"><a class="button wc-backward" href="<?php echo esc_url( get_home_url() ); ?>"><?php esc_html_e( 'Back To Home Page', 'yith-woocommerce-review-reminder' ); ?></a></p>
					<?php
				}

				?>
			</div>
			<?php

			return ob_get_clean();
		}

	}

	new YWRR_Unsubscribe();

}

