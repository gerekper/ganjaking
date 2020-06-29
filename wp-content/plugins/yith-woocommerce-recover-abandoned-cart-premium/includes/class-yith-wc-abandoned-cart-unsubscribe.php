<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWRR_Unsubscribe' ) && ! class_exists( 'YWRAC_Unsubscribe' ) ) {

	/**
	 * Implements AJAX for YWRR plugin
	 *
	 * @class   YWRAC_Unsubscribe
	 * @package YITH
	 * @since   1.1.5
	 * @author  Your Inspiration Themes
	 */
	class YWRAC_Unsubscribe {

		/**
		 * Constructor
		 *
		 * @since   1.1.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'admin_notices', array( $this, 'protect_unsubscribe_page_notice' ) );
			add_action( 'wp_trash_post', array( $this, 'protect_unsubscribe_page' ), 10, 1 );
			add_action( 'before_delete_post', array( $this, 'protect_unsubscribe_page' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
			add_shortcode( 'ywrac_unsubscribe', array( $this, 'unsubscribe' ) );
			add_shortcode( 'ywrr_unsubscribe', array( $this, 'unsubscribe' ) );
			add_filter( 'wp_get_nav_menu_items', array( $this, 'hide_unsubscribe_page' ), 10, 3 );
			add_action( 'init', array( $this, 'create_unsubscribe_page' ) );

		}

		/**
		 * Creates the unsubscribe page
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function create_unsubscribe_page() {

			if ( get_option( 'ywrr_unsubscribe_page_id' ) || get_option( 'ywrac_unsubscribe_page_id' ) ) {
				return;
			}

			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => _x( 'unsubscribe', 'Page slug', 'yith-woocommerce-recover-abandoned-cart' ),
				'post_title'     => _x( 'Unsubscribe', 'Page title', 'yith-woocommerce-recover-abandoned-cart' ),
				'post_content'   => '[ywrac_unsubscribe]',
				'post_parent'    => 0,
				'comment_status' => 'closed',
			);
			$page_id   = wp_insert_post( $page_data );

			update_option( 'ywrac_unsubscribe_page_id', $page_id );

		}

		/**
		 * Notifies the inability to delete the page
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function protect_unsubscribe_page_notice() {

			global $post_type, $pagenow;

			if ( $pagenow == 'edit.php' && $post_type == 'page' && isset( $_GET['impossible'] ) ) {
				echo '<div id="message" class="error"><p>' . __( 'The unsubscribe page cannot be deleted', 'yith-woocommerce-recover-abandoned-cart' ) . '</p></div>';
			}

		}

		/**
		 * Prevent the deletion of unsubscribe page
		 *
		 * @since   1.0.0
		 *
		 * @param   $post_id
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function protect_unsubscribe_page( $post_id ) {

			if ( $post_id == get_option( 'ywrr_unsubscribe_page_id' ) || $post_id == get_option( 'ywrac_unsubscribe_page_id' ) ) {

				$query_args = array(
					'post_type'  => 'page',
					'impossible' => '1',
				);
				$error_url  = esc_url( add_query_arg( $query_args, admin_url( 'edit.php' ) ) );

				wp_redirect( $error_url );
				exit();
			}

		}

		/**
		 * Hides unsubscribe page from menus
		 *
		 * @since   1.0.0
		 *
		 * @param   $items
		 * @param   $menu
		 * @param   $args
		 *
		 * @return  array
		 * @author  Andrea Grillo
		 */
		public function hide_unsubscribe_page( $items, $menu, $args ) {

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
		 * @since   1.1.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function frontend_scripts() {

			global $post;

			if ( $post && ( $post->ID == get_option( 'ywrr_unsubscribe_page_id' ) || $post->ID == get_option( 'ywrac_unsubscribe_page_id' ) ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 'ywrac-unsubscribe', YITH_YWRAC_ASSETS_URL . '/js/ywrac-unsubscribe' . $suffix . '.js' );

				$params = array(
					'ajax_url' => str_replace( array( 'https:', 'http:' ), '', admin_url( 'admin-ajax.php' ) ),
				);

				wp_localize_script( 'ywrac-unsubscribe', 'ywrac_unsubscribe', $params );

			}
		}

		/**
		 * Unsubscribe page shortcode.
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function unsubscribe() {

			$type = isset( $_GET['type'] ) ? $_GET['type'] : '';

			if ( $type == '' ) {
				$type = isset( $_GET['action'] ) ? $_GET['action'] : '';
			}

			$path = '';

			echo '<div class ="woocommerce ywrac-unsubscribe-form">';

			switch ( $type ) {

				case 'ywrr':
					$path  = function_exists( 'YITH_WRR' ) ? YWRR_TEMPLATE_PATH : '';
					$email = isset( $_GET['email'] ) ? $_GET['email'] : '';
					break;

				case 'ywrac':
				case '_ywrac_unsubscribe_from_mail':
					$path  = defined( 'YITH_YWRAC_PREMIUM' ) && YITH_YWRAC_PREMIUM ? YITH_YWRAC_TEMPLATE_PATH : '';
					$email = isset( $_GET['customer'] ) && is_email( $_GET['customer'] ) ? $_GET['customer'] : '';
					break;

			}

			if ( $type == '_ywrac_unsubscribe_from_mail' ) {
				$type = 'ywrac';
			}

			if ( $path != '' && $email != '' ) {
				wc_get_template( $type . '-unsubscribe.php', array(), $path, $path );

			} else {
				?>
				<p class="return-to-shop"><a class="button wc-backward" href="<?php echo get_home_url(); ?>"><?php _e( 'Return To Home Page', 'yith-woocommerce-recover-abandoned-cart' ); ?></a></p>
				<?php
			}

			echo '</div>';
		}

	}

	new YWRAC_Unsubscribe();

}

