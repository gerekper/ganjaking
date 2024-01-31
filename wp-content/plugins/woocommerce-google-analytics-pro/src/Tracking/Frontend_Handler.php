<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2024, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;

use SkyVerge\WooCommerce\Google_Analytics_Pro\Helpers\Product_Helper;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integration;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Tracking;

defined( 'ABSPATH' ) or exit;

/**
 * The frontend handler class.
 *
 * @since 2.0.0
 */
class Frontend_Handler {


	/** @var string Google Analytics js tracker function name **/
	private string $ga_function_name;

	/** @var array associative array of queued JavaScript tracking calls for UA  **/
	private array $queued_ua_tracking_calls = [];

	/** @var array array of queued JavaScript tracking calls  **/
	private array $queued_tracking_calls = [];

	/** @var Duplicate_Tracking_Code_Detector Duplicate Tracking Code Detector instance **/
	private Duplicate_Tracking_Code_Detector $duplicate_tracking_code_detector;


	/**
	 * Constructs the frontend handler class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->duplicate_tracking_code_detector = new Duplicate_Tracking_Code_Detector();

		add_action( 'init', [ $this, 'register_hooks'] );
	}


	/**
	 * Registers frontend hooks.
	 *
	 * Registering hooks on `init` is required to avoid loading the integration class before it's initialized.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_hooks(): void {

		// header/footer JavaScript code, only add if measurement/tracking ID is available
		if (Tracking::get_measurement_id() || Tracking::get_tracking_id()) {

			add_action( 'wp_head',    [ $this, 'print_tracking_code' ], 9 );
			add_action( 'login_head', [ $this, 'print_tracking_code' ], 9 );

			// print tracking JavaScript
			add_action( 'wp_footer', [ $this, 'print_tracking_calls' ] );
		}

		// Enhanced Ecommerce related product impressions (UA)
		add_action( 'woocommerce_before_shop_loop_item', [ $this, 'product_impression' ] );
	}


	/**
	 * Prints the tracking code JavaScript.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function print_tracking_code(): void {

		// bail if tracking is disabled
		if ( Tracking::do_not_track() ) {
			return;
		}

		// helper functions for ga pro
		$gateways = [];

		foreach ( WC()->payment_gateways->get_available_payment_gateways() as $gateway ) {
			$gateways[ $gateway->id ] = html_entity_decode( wp_strip_all_tags( $gateway->get_title() ) );
		}

		?>
		<script<?php echo $this->get_script_attributes(); ?>>
			window.wc_ga_pro = {};

			window.wc_ga_pro.ajax_url = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';

			window.wc_ga_pro.available_gateways = <?php echo json_encode( $gateways ); ?>;

			// interpolate json by replacing placeholders with variables
			window.wc_ga_pro.interpolate_json = function( object, variables ) {

				if ( ! variables ) {
					return object;
				}

				let j = JSON.stringify( object );

				for ( let k in variables ) {
					j = j.split( '{$' + k + '}' ).join( variables[ k ] );
				}

				return JSON.parse( j );
			};

			// return the title for a payment gateway
			window.wc_ga_pro.get_payment_method_title = function( payment_method ) {
				return window.wc_ga_pro.available_gateways[ payment_method ] || payment_method;
			};

			// check if an email is valid
			window.wc_ga_pro.is_valid_email = function( email ) {
				return /[^\s@]+@[^\s@]+\.[^\s@]+/.test( email );
			};
		</script>
		<?php

		/**
		 * Filters if the tracking code should be removed
		 *
		 * @since 1.5.1
		 *
		 * @param bool $remove_tracking_code
		 */
		if ( apply_filters( 'wc_google_analytics_pro_remove_tracking_code', false ) ) {
			return;
		}

		$this->print_ga4_tracking_code();
		$this->print_ua_tracking_code();
	}


	/**
	 * Prints the gtag.js tracking code JavaScript.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function print_ga4_tracking_code() : void {

		$measurement_id  = Tracking::get_measurement_id();
		$data_layer_name = $this->get_gtag_data_layer_name();

		if ( ! $measurement_id || ! $data_layer_name ) {
			return;
		}

		$gtag_url = add_query_arg( [
			'id' => $measurement_id,
			'l'  => $data_layer_name,
		], esc_url( $this->get_gtag_script_url() ) );

		$script_attributes = $this->get_script_attributes();

		$options = $this->get_gtag_options();
		$options = ! empty( $options ) ? ( wp_json_encode( $options ) ?: '{}' ) : '{}'; // json_encode may return null or false

		/**
		 * Fires before the gtag.js tracking code is added.
		 *
		 * @since 2.0.0
		 */
		do_action( 'wc_google_analytics_pro_before_gtag_tracking_code' );

		?>
		<!-- Google tag (gtag.js) -->
		<script async src='<?php echo $gtag_url; ?>'></script>
		<script <?php echo $script_attributes; ?>>

			window.<?php echo esc_js( $data_layer_name ); ?> = window.<?php echo esc_js( $data_layer_name ); ?> || [];

			function gtag() {
				<?php echo esc_js( $data_layer_name ); ?>.push(arguments);
			}

			gtag('js', new Date());

			gtag('config', '<?php echo esc_js( $measurement_id ); ?>', <?php echo $options; ?>);

			<?php
			/**
			 * Fires after the gtag.js tracking code is set up.
			 *
			 * Allows to add custom JS calls after tracking code is set up.
			 *
			 * @since 2.0.0
			 *
			 * @param string $measurement_id gtag.js measurement ID
			 * @param string $data_layer_name data layer variable name
			 */
			do_action( 'wc_google_analytics_pro_after_tracking_code_setup', $this->get_ga_function_name(), $measurement_id, $data_layer_name );
			?>

			(function() {

				const event = document.createEvent('Event');

				event.initEvent( 'wc_google_analytics_pro_gtag_loaded', true, true );

				document.dispatchEvent( event );
			})();
		</script>
		<?php

		/**
		 * Fires after the gtag.js tracking code is added.
		 *
		 * @since 2.0.0
		 */
		do_action( 'wc_google_analytics_pro_after_gtag_tracking_code' );
	}


	/**
	 * Prints the Universal Analytics tracking code JavaScript.
	 *
	 * @UA
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	protected function print_ua_tracking_code(): void {

		if ( ! Tracking::get_tracking_id() ) {
			return;
		}

		// no indentation on purpose
		?>
		<!-- Start WooCommerce Google Analytics Pro -->
		<?php
		/**
		 * Fires before the JS tracking code is added.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wc_google_analytics_pro_before_tracking_code' );
		?>
		<script<?php echo $this->get_script_attributes(); ?>>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','<?php echo esc_url( $this->get_ga_script_url() ); ?>','<?php echo $this->get_ga_function_name(); ?>');
			<?php $tracker_options = $this->get_tracker_options(); ?>
			<?php echo $this->get_ga_function_name(); ?>( 'create', '<?php echo esc_js(Tracking::get_tracking_id()); ?>', <?php echo ! empty( $tracker_options ) ? wp_json_encode( $tracker_options ) : "'auto'"; ?> );
			<?php echo $this->get_ga_function_name(); ?>( 'set', 'forceSSL', true );
			<?php if ( 'yes' === $this->get_integration()->get_option( 'track_user_id' ) && is_user_logged_in() ) : ?>
			<?php echo $this->get_ga_function_name(); ?>( 'set', 'userId', '<?php echo esc_js( get_current_user_id() ) ?>' );
			<?php endif; ?>
			<?php if ( 'yes' === $this->get_integration()->get_option( 'anonymize_ip' ) ) : ?>
			<?php echo $this->get_ga_function_name(); ?>( 'set', 'anonymizeIp', true );
			<?php endif; ?>
			<?php if ( 'yes' === $this->get_integration()->get_option( 'enable_displayfeatures' ) ) : ?>
			<?php echo $this->get_ga_function_name(); ?>( 'require', 'displayfeatures' );
			<?php endif; ?>
			<?php if ( 'yes' === $this->get_integration()->get_option( 'enable_linkid' ) ) : ?>
			<?php echo $this->get_ga_function_name(); ?>( 'require', 'linkid' );
			<?php endif; ?>
			<?php if ( 'yes' === $this->get_integration()->get_option( 'enable_google_optimize' ) && '' !== $this->get_integration()->get_option( 'google_optimize_code' ) ) : ?>
			<?php echo $this->get_ga_function_name(); ?>( 'require', '<?php printf( '%1$s', esc_js( $this->get_integration()->get_option( 'google_optimize_code' ) ) ); ?>' );
			<?php endif; ?>
			<?php echo $this->get_ga_function_name(); ?>( 'require', 'ec' );

			<?php
			/**
			 * Fires after the JS tracking code is setup.
			 *
			 * Allows to add custom JS calls after tracking code is setup.
			 *
			 * @since 1.3.5
			 *
			 * @param string $ga_function_name Google Analytics tracking function name
			 * @param string $tracking_id Google Analytics tracking ID
			 */
			do_action( 'wc_google_analytics_pro_after_tracking_code_setup', $this->get_ga_function_name(), Tracking::get_tracking_id() );
			?>

			(function() {

				// trigger an event the old-fashioned way to avoid a jQuery dependency and still support IE
				const event = document.createEvent('Event');

				event.initEvent( 'wc_google_analytics_pro_loaded', true, true );

				document.dispatchEvent( event );
			})();
		</script>
		<?php
		/**
		 * Fires after the JS tracking code is added.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wc_google_analytics_pro_after_tracking_code' );
		?>
		<!-- end WooCommerce Google Analytics Pro -->
		<?php
	}


	/**
	 * Gets script tag attributes.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_script_attributes(): string {

		/**
		 * Filters Google Analytics Pro script attributes.
		 *
		 * @since 1.8.10
		 *
		 * @param array $custom_attributes
		 */
		$custom_attributes = (array) apply_filters( 'wc_google_analytics_pro_script_attributes', [] );

		$script_attributes = '';

		foreach ( $custom_attributes as $tag => $value ) {
			$script_attributes .= ' ' . sanitize_html_class( $tag ) . '="' . esc_attr( $value ) . '"';
		}

		return $script_attributes;
	}


	/**
	 * Returns the UA JS tracker options.
	 *
	 * @UA
	 *
	 * @since 2.0.0
	 *
	 * @return array|null
	 */
	private function get_tracker_options(): ?array {

		/**
		 * Filters the JS tracker options for the create method.
		 *
		 * @since 1.3.5
		 *
		 * @param array $tracker_options an associative array of tracker options
		 */
		return apply_filters( 'wc_google_analytics_pro_tracker_options', array(
			'cookieDomain' => 'auto'
		) );
	}


	/**
	 * Returns the gtag.js tracker options.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_gtag_options(): array {

		$options = array_merge( [ 'cookie_domain' => 'auto' ], Tracking::get_debug_mode_params() );

		if ( Tracking::is_user_id_tracking_enabled() && ( $user = wp_get_current_user() ) && $user->ID ) {

			$options['user_id']         = $user->ID;
			$options['user_properties'] = [
				'role' => implode( ', ', $user->roles ),
			];
		}

		// TODO: remove this after September 30, 2023 when Google Optimize is retired {@itambek 2023-03-21}
		if ( $this->get_integration()->get_option( 'enable_google_optimize' ) && $optimize_code = $this->get_integration()->get_option( 'google_optimize_code' ) ) {
			$options[ 'optimize_id' ] = $optimize_code;
		}

		/**
		 * Filters the JS the gtag.js tracker options.
		 *
		 * @since 2.0.0
		 *
		 * @param array $options an associative array of gtag.js options
		 */
		return (array) apply_filters( 'wc_google_analytics_pro_gtag_options', $options );
	}


	/**
	 * Enqueues a tracking call in JavaScript.
	 *
	 * This method queues tracking JavaScript, so it can be later output when printing the tracking calls.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $javascript
	 */
	public function enqueue_tracking_call( string $javascript ): void {

		$this->queued_tracking_calls[] = $javascript;
	}


	/**
	 * Enqueues the tracking JavaScript.
	 *
	 * Google Analytics is a bit picky about the order tacking JavaScript is output:
	 *
	 * + Impressions -> Pageview -> Events
	 *
	 * This method queues tracking JavaScript, so it can be later output in the
	 * correct order.
	 *
	 * @UA
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $type the tracking type. One of 'impression', 'pageview', or 'event'
	 * @param string $javascript
	 */
	public function enqueue_js( string $type, string $javascript ): void {

		if ( ! isset( $this->queued_ua_tracking_calls[ $type ] ) ) {

			$this->queued_ua_tracking_calls[ $type ] = [];
		}

		$this->queued_ua_tracking_calls[ $type ][] = $javascript;
	}


	/**
	 * Prints the tracking JavaScript calls.
	 *
	 * This method prints the queued tracking JavaScript calls in the correct order.
	 *
	 * @internal
	 *
	 * @see self::enqueue_tracking_call()
	 *
	 * @since 2.0.0
	 */
	public function print_tracking_calls() : void {

		if ( Tracking::do_not_track() ) {
			return;
		}

		$tracking_calls_js = "\t" . implode( "\n\t\t\t\t", $this->queued_tracking_calls ) . "\n";

		// TODO: remove tracking types and UA tracking calls when removing UA support {@itambek 2023-03-16}
		// define the correct order tracking types should be printed
		$types = [ 'impression', 'pageview', 'event' ];

		$ua_tracking_calls_js = '';

		foreach ( $types as $type ) {

			if ( isset( $this->queued_ua_tracking_calls[ $type ] ) ) {

				foreach ( $this->queued_ua_tracking_calls[ $type ] as $code ) {
					$ua_tracking_calls_js .= "\n\t\t\t\t" . $code . "\n";
				}
			}
		}

		ob_start();

		?>
		( function() {

			function trackEvents() {
				<?php echo $tracking_calls_js; ?>
			}

			if ( 'undefined' !== typeof gtag ) {
				trackEvents();
			} else {
				// avoid using jQuery in case it's not available when this script is loaded
				document.addEventListener( 'wc_google_analytics_pro_gtag_loaded', trackEvents );
			}

			<?php /** TODO: remove UA event tracking below when removing UA support {@itambek 2023-03-16} */ ?>

			function trackUAEvents() {
			<?php echo $ua_tracking_calls_js; ?>
			}

			if ( 'undefined' !== typeof <?php echo $this->get_ga_function_name(); ?> ) {
				trackUAEvents();
			} else {
				// avoid using jQuery in case it's not available when this script is loaded
				document.addEventListener( 'wc_google_analytics_pro_loaded', trackUAEvents );
			}

		} ) ();
		<?php

		// enqueue the JavaScript
		wc_enqueue_js( ob_get_clean() );
	}


	/**
	 * Gets the Google Analytics tracking script URL.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return string Google Analytics tracking script URL
	 */
	public function get_ga_script_url(): string {

		/**
		 * Filters the analytics.js tracking URL.
		 *
		 * Third parties may use this filter to serve a different tracking script.
		 *
		 * @since 1.12.0
		 *
		 * @param string $tracking_url the analytics.js tracking URL
		 */
		return apply_filters( 'wc_google_analytics_pro_tracking_script_url', 'https://www.google-analytics.com/analytics.js' );
	}


	/**
	 * Gets the gtag.js tracking script URL.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return string gtag.js tracking script URL
	 */
	public function get_gtag_script_url(): string {

		/**
		 * Filters the gtag.js tracking URL.
		 *
		 * Third parties may use this filter to serve a different tracking script.
		 *
		 * @since 2.0.0
		 *
		 * @param string $tracking_url the gtag.js.js tracking URL
		 */
		return apply_filters( 'wc_google_analytics_pro_gtag_script_url', 'https://www.googletagmanager.com/gtag/js' );
	}


	/**
	 * Returns the Google Analytics tracking function name.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return string Google Analytics tracking function name
	 */
	public function get_ga_function_name(): string {

		if ( ! isset( $this->ga_function_name ) ) {

			$ga_function_name = wc_google_analytics_pro()->get_integration()->get_option( 'function_name', 'ga' );

			/**
			 * Filters the Google Analytics tracking function name.
			 *
			 * Since 1.3.0 the tracking function name defaults to `ga` except when:
			 * - MonsterInsights is enabled and not in compatibility mode
			 * - plugin was upgraded from a previous version and has not been configured to use the new `ga` function name
			 * in which case it will default to `__gaTracker`
			 *
			 * @since 1.0.3
			 * @param string $ga_function_name the Google Analytics tracking function name, defaults to 'ga'
			 */
			$this->ga_function_name = apply_filters( 'wc_google_analytics_pro_tracking_function_name', $ga_function_name );
		}

		return $this->ga_function_name;
	}


	/**
	 * Returns the gtag.js dataLayer variable name.
	 *
	 * @since 2.0.0
	 *
	 * @return string gtag.js dataLayer variable name.
	 */
	public function get_gtag_data_layer_name() : string {

		/**
		 * Filters the gtag.js dataLayer variable name.
		 *
		 * @link https://developers.google.com/tag-platform/devguides/datalayer#rename_the_data_layer
		 *
		 * @since 2.0.0
		 *
		 * @param string $gtag_data_layer_name the gtag.js dataLayer variable name, defaults to 'dataLayer'
		 */
		return apply_filters( 'wc_google_analytics_pro_gtag_data_layer_name', 'dataLayer' );
	}


	/**
	 * Outputs the event tracking JavaScript.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $event_name the name of the event to be set
	 * @param array|string $properties optional: the properties to be set with event
	 * @return bool whether the event was recorded
	 */
	public function js_record_event( string $event_name, $properties = [] ): bool {

		// skip if there are no valid properties
		if ( ! is_array( $properties ) ) {
			return false;
		}

		// verify tracking status
		if ( Tracking::do_not_track() ) {
			return false;
		}

		$this->enqueue_js( 'event', $this->get_event_tracking_js( $event_name, $properties ) );

		return true;
	}


	/**
	 * Returns event tracking JS code.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $event_name the name of the vent to be set
	 * @param array|string $properties the properties to be set with event
	 * @param string|null $js_args_variable (optional) name of the JS variable to use for interpolating dynamic event properties
	 * @return string|null
	 */
	public function get_event_tracking_js( string $event_name, $properties, ?string $js_args_variable = null ): ?string {

		if ( ! is_array( $properties ) ) {
			return null;
		}

		$properties = array(
				'hitType'        => $properties['hitType'] ?? 'event',         // Required
				'eventCategory'  => $properties['eventCategory'] ?? 'page',    // Required
				'eventAction'    => $properties['eventAction'] ?? $event_name, // Required
				'eventLabel'     => $properties['eventLabel'] ?? null,
				'eventValue'     => $properties['eventValue'] ?? null,
				'nonInteraction' => $properties['nonInteraction'] ?? false,
		);

		// remove blank properties
		unset( $properties[''] );

		$properties = json_encode( $properties );

		// interpolate dynamic event properties
		if ( $js_args_variable ) {
			$properties = "wc_ga_pro.interpolate_json( {$properties}, {$js_args_variable} )";
		}

		return sprintf( "%s( 'send', %s );", $this->get_ga_function_name(), $properties );
	}


	/**
	 * Gets the code to add a product to the tracking code.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @global array $woocommerce_loop The WooCommerce loop position data
	 * @param int $product_id ID of the product to add.
	 * @param int $quantity Optional. Quantity to add to the code.
	 * @return string Code to use within a tracking code.
	 */
	public function get_ec_add_product_js( int $product_id, int $quantity = 1 ): string {
		global $woocommerce_loop;

		$product         = wc_get_product( $product_id );
		$product_details = array(
				'id'       => '',
				'name'     => '',
				'brand'    => '',
				'category' => '',
				'variant'  => '',
				'price'    => '',
				'quantity' => $quantity,
				'position' => isset( $woocommerce_loop['loop'] ) ? $woocommerce_loop['loop'] : '',
		);

		// sanity check
		if ( $product instanceof \WC_Product ) {
			$product_details['id']       = Product_Helper::get_product_identifier( $product );
			$product_details['name']     = $product->get_name();
			$product_details['category'] = Product_Helper::get_category_hierarchy( $product );
			$product_details['variant']  = Product_Helper::get_product_variation_attributes( $product );
			$product_details['price']    = $product->get_price();
		}

		/**
		 * Filters the product details data (productFieldObject).
		 *
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-data
		 *
		 * @since 1.1.1
		 *
		 * @param array $product_details_data an associative array of product details data
		 * @param \WC_Product $product the product object
		 */
		$product_details_data = (array) apply_filters( 'wc_google_analytics_pro_product_details_data', $product_details, $product );

		return sprintf(
				"%s( 'ec:addProduct', %s );",
				$this->get_ga_function_name(),
				wp_json_encode( $product_details_data )
		);
	}


	/**
	 * Returns the Enhanced Ecommerce action JavaScript of the provided event key if it exists.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $action the action, see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#action-types for available options
	 * @param array $args Optional. An array of args to be encoded as the `actionFieldObject`, see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#action-data for availalable options
	 * @param string|null $js_args_variable (optional) name of the JS variable to use for interpolating dynamic event properties
	 * @return string the JavaScript or an empty string
	 */
	public function get_ec_action_js( string $action, array $args = [], ?string $js_args_variable = null ): string {

		$args = wp_json_encode( $args );

		// interpolate dynamic event properties
		if ( $js_args_variable ) {
			$args = sprintf( 'window.wc_ga_pro.interpolate_json( %s, %s )', $args, $js_args_variable );
		}

		return sprintf( "%s( 'ec:setAction', '%s', %s );", $this->get_ga_function_name(), $action, $args );
	}


	/**
	 * Tracks a product impression.
	 *
	 * @internal
	 *
	 * @UA
	 *
	 * An impression is the listing of a product anywhere on the website, e.g.
	 * search/archive/category/related/cross sell.
	 *
	 * @since 2.0.0
	 */
	public function product_impression(): void {

		if ( Tracking::do_not_track() ) {
			return;
		}

		$track_on = (array) $this->get_integration()->get_option( 'track_item_list_views_on', [] );

		// bail if product impression tracking is disabled on product pages, and we're on a product page
		// note: this doesn't account for the [product_page] shortcode unfortunately
		if ( ! in_array( 'single_product_pages', $track_on, true ) && is_product() ) {
			return;
		}

		// bail if product impression tracking is disabled on product archive pages, and we're on an archive page
		if ( ! in_array( 'archive_pages', $track_on, true ) && ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) ) {
			return;
		}

		global $product, $woocommerce_loop;

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		$attributes = [];

		if ( $product->is_type( 'variable' ) ) {
			$attributes = $product->get_default_attributes();
		}

		// set up impression data as associative array and merge attributes to be sent as custom dimensions
		$impression_data = array_merge( [
				'id'       => Product_Helper::get_product_identifier( $product ),
				'name'     => $product->get_name(),
				'list'     => Product_Helper::get_list_type(),
				'brand'    => '',
				'category' => Product_Helper::get_category_hierarchy( $product ),
				'variant'  => Product_Helper::get_product_variation_attributes( $product ),
				'position' => $woocommerce_loop['loop'] ?? 1,
				'price'    => $product->get_price(),
		], $attributes );

		/**
		 * Filters the product impression data (impressionFieldObject).
		 *
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#impression-data
		 *
		 * @since 1.1.1
		 *
		 * @param array $impression_data an associative array of product impression data
		 * @param \WC_Product $product the product object
		 */
		$impression_data = apply_filters( 'wc_google_analytics_pro_product_impression_data', $impression_data, $product );

		// unset empty values to reduce request size
		foreach ( $impression_data as $key => $value ) {

			if ( empty( $value ) ) {
				unset( $impression_data[ $key ] );
			}
		}

		$this->enqueue_js( 'impression', sprintf(
				"%s( 'ec:addImpression', %s );",
				$this->get_ga_function_name(),
				wp_json_encode( $impression_data )
		) );
	}


	/**
	 * Gets the integration instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Integration
	 */
	public function get_integration(): Integration {

		return wc_google_analytics_pro()->get_integration();
	}


}
