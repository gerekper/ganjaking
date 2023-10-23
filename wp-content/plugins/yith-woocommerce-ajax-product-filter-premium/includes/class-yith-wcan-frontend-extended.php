<?php
/**
 * Frontend class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Frontend_Extended' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Frontend_Extended extends YITH_WCAN_Frontend {

		/**
		 * Constructor method
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__construct();

			// Frontend methods.
			add_filter( 'yith_wcan_body_class', array( $this, 'premium_body_class' ) );
			add_action( 'yith_wcan_after_preset_filters', array( $this, 'apply_filters_button' ), 10, 1 );
		}

		/* === FRONTEND METHODS === */

		/**
		 * Add custom meta to filtered page
		 *
		 * @return void
		 */
		public function add_meta() {
			// add meta for parent class.
			parent::add_meta();

			// add meta for session.
			$session_share_url = $this->query->get_current_session_share_url();

			if ( ! $session_share_url ) {
				return;
			}

			?>
			<meta name="yith_wcan:sharing_url" content="<?php echo esc_attr( $session_share_url ); ?>">
			<?php
		}

		/**
		 * Returns an array of parameters to use to localize shortcodes script
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array Array of parameters.
		 */
		protected function get_shortcodes_localize( $context = 'view' ) {
			$params = array_merge(
				parent::get_shortcodes_localize( 'edit' ),
				array(
					'instant_filters' => 'yes' === yith_wcan_get_option( 'yith_wcan_instant_filters', 'yes' ),
					'ajax_filters'    => 'yes' === yith_wcan_get_option( 'yith_wcan_ajax_filters', 'yes' ),
					'scroll_top'      => 'yes' === yith_wcan_get_option( 'yith_wcan_scroll_top', 'no' ),
					'session_param'   => YITH_WCAN_Session_Factory::get_session_query_param(),
				)
			);

			if ( 'view' === $context ) {
				return apply_filters( 'yith_wcan_shortcodes_script_args', $params );
			}

			return $params;
		}

		/* === TEMPLATE METHODS === */

		/**
		 * Print Apply Filters button template
		 *
		 * @param YITH_WCAN_Preset|bool $preset Current preset, when applicable; false otherwise.
		 *
		 * @return void
		 */
		public function apply_filters_button( $preset = false ) {
			$instant_filters = 'yes' === yith_wcan_get_option( 'yith_wcan_instant_filters', 'yes' );

			if ( $instant_filters ) {
				return;
			}

			yith_wcan_get_template( 'filters/global/apply-filters.php', compact( 'preset' ) );
		}

		/**
		 * Add a body class(es)
		 *
		 * @param string $classes Body classes added by the plugin.
		 *
		 * @return string Filtered list of classes added by the plugin to the body.
		 * @since  1.0
		 */
		public function premium_body_class( $classes ) {
			return 'yith-wcan-extended';
		}
	}
}
