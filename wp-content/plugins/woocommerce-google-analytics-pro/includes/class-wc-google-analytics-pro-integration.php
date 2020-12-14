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
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * The plugin integration class.
 *
 * Handles settings and provides common tracking functions needed by enhanced eCommerce tracking.
 *
 * @since 1.0.0
 */
class WC_Google_Analytics_Pro_Integration extends Framework\SV_WC_Tracking_Integration {


	/** @var string URL to Google Analytics Pro Authentication proxy */
	const PROXY_URL = 'https://wc-ga-pro-proxy.com';

	/** @var string MonsterInsights's GA tracking type, Universal or old 'ga.js'. Default is empty string, which means that MonsterInsights tracking is inactive. */
	private $_monsterinsights_tracking_type = '';

	/** @var \WC_Google_Analytics_Pro_Email_Tracking instance **/
	public $email_tracking;

	/** @var array cache for user tracking status **/
	private $user_tracking_enabled = array();

	/** @var string google analytics js tracker function name **/
	private $ga_function_name;

	/** @var array associative array of queued tracking JavaScript **/
	private $queued_js = array();

	/** @var \WC_Google_Analytics_Pro_Management_API handler */
	private $management_api;

	/** @var \WC_Google_Analytics_Pro_Measurement_Protocol_API handler */
	private $measurement_protocol_api;


	/**
	 * Constructs the class.
	 *
	 * Sets up the settings page & adds the necessary hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			'google_analytics_pro',
			__( 'Google Analytics Pro', 'woocommerce-google-analytics-pro' ),
			__( 'Supercharge your Google Analytics tracking with enhanced eCommerce tracking, and custom event tracking', 'woocommerce-google-analytics-pro' )
		);

		// header/footer JavaScript code, only add if tracking ID is available
		if ( $this->get_tracking_id() ) {

			add_action( 'wp_head',    array( $this, 'ga_tracking_code' ), 9 );
			add_action( 'login_head', array( $this, 'ga_tracking_code' ), 9 );

			// print tracking JavaScript
			add_action( 'wp_footer', array( $this, 'print_js' ) );
		}

		// Enhanced Ecommerce related product impressions
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'product_impression' ) );

		// save GA identity to each order
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'store_ga_identity' ) );

		// mark the order as placed, which prevents us from tracking completed orders that were placed before GA Pro was enabled
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'add_order_placed_meta' ] );

		// two filters catching the event of MonsterInsights doing tracking
		if ( wc_google_analytics_pro()->is_monsterinsights_active() ) {

			if ( wc_google_analytics_pro()->is_monsterinsights_lt_6() ) {
				add_filter( 'yoast-ga-push-array-ga-js',     array( $this, 'set_monsterinsights_tracking_type_ga_js' ) );
				add_filter( 'yoast-ga-push-array-universal', array( $this, 'set_monsterinsights_tracking_data' ) );
			} else {
				add_filter( 'monsterinsights_frontend_tracking_options_analytics_end', array( $this, 'set_monsterinsights_tracking_data' ) );
			}
		}

		// load email tracking class
		add_action( 'init', array( $this, 'load_email_tracking' ) );

		// handle Google Client API callbacks
		add_action( 'woocommerce_api_wc-google-analytics-pro/auth', array( $this, 'authenticate' ) );

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		add_filter( 'woocommerce_settings_api_sanitized_fields_google_analytics_pro', array( $this, 'filter_admin_options' ) );

		add_action( 'deactivated_plugin', [ $this, 'clear_duplicate_tracking_code_results' ] );
	}


	/**
	 * Loads the email tracking class.
	 *
	 * Note: Loading this class on `init` is required to support custom emails defined by external extensions/code
	 *
	 * @since 1.8.2
	 */
	public function load_email_tracking() {
		$this->email_tracking = wc_google_analytics_pro()->load_class( '/includes/class-wc-google-analytics-pro-email-tracking.php', 'WC_Google_Analytics_Pro_Email_Tracking' );
	}


	/**
	 * Returns the Google Analytics tracking function name.
	 *
	 * @since 1.3.0
	 * @return string Google Analytics tracking function name
	 */
	public function get_ga_function_name() {

		if ( ! isset( $this->ga_function_name ) ) {

			$ga_function_name = $this->get_option( 'function_name', 'ga' );

			if ( '__gaTracker' !== $ga_function_name && wc_google_analytics_pro()->is_monsterinsights_active() && wc_google_analytics_pro()->is_monsterinsights_gte_6() && ! monsterinsights_get_option( 'gatracker_compatibility_mode', false ) ) {
				$ga_function_name = '__gaTracker';
			}

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
	 * Loads admin styles and scripts.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix the current URL filename, i.e. edit.php, post.php, etc...
	 */
	public function load_styles_scripts( $hook_suffix ) {

		if ( wc_google_analytics_pro()->is_plugin_settings() ) {

			wp_enqueue_script( 'wc-google-analytics-pro-admin', wc_google_analytics_pro()->get_plugin_url() . '/assets/js/admin/wc-google-analytics-pro-admin.min.js', array( 'jquery' ), WC_Google_Analytics_Pro::VERSION );

			wp_localize_script( 'wc-google-analytics-pro-admin', 'wc_google_analytics_pro', array(
				'ajax_url'            => admin_url('admin-ajax.php'),
				'auth_url'            => $this->get_auth_url(),
				'revoke_access_nonce' => wp_create_nonce( 'revoke-access' ),
				'i18n' => array(
					'ays_revoke' => esc_html__( 'Are you sure you wish to revoke access to your Google Account?', 'woocommerce-google-analytics-pro' ),
				),
			) );

			wp_enqueue_style( 'wc-google-analytics-pro-admin', wc_google_analytics_pro()->get_plugin_url() . '/assets/css/admin/wc-google-analytics-pro-admin.min.css', \WC_Google_Analytics_Pro::VERSION );
		}
	}


	/**
	 * Enqueues the tracking JavaScript.
	 *
	 * Google Analytics is a bit picky about the order tacking JavaScript is output:
	 *
	 * + Impressions -> Pageview -> Events
	 *
	 * This method queues tracking JavaScript so it can be later output in the
	 * correct order.
	 *
	 * @since 1.0.3
	 * @param string $type the tracking type. One of 'impression', 'pageview', or 'event'
	 * @param string $javascript
	 */
	public function enqueue_js( $type, $javascript ) {

		if ( ! isset( $this->queued_js[ $type ] ) ) {
			$this->queued_js[ $type ] = array();
		}

		$this->queued_js[ $type ][] = $javascript;
	}


	/**
	 * Prints the tracking JavaScript.
	 *
	 * This method prints the queued tracking JavaScript in the correct order.
	 *
	 * @internal
	 *
	 * @see \WC_Google_Analytics_Pro_Integration::enqueue_js()
	 *
	 * @since 1.0.3
	 */
	public function print_js() {

		if ( $this->do_not_track() ) {
			return;
		}

		// define the correct order tracking types should be printed
		$types = [ 'impression', 'pageview', 'event' ];

		$javascript = '';

		foreach ( $types as $type ) {

			if ( isset( $this->queued_js[ $type ] ) ) {

				foreach ( $this->queued_js[ $type ] as $code ) {
					$javascript .= "\n\t\t\t\t" . $code . "\n";
				}
			}
		}

		ob_start();

		?>
		( function() {

			function trackEvents() {
				<?php echo $javascript; ?>
			}

			if ( 'undefined' !== typeof <?php echo $this->get_ga_function_name(); ?> ) {
				trackEvents();
			} else {
				// avoid using jQuery in case it's not available when this script is loaded
				document.addEventListener( 'wc_google_analytics_pro_loaded', trackEvents );
			}

		} ) ();
		<?php

		// enqueue the JavaScript
		wc_enqueue_js( ob_get_clean() );
	}


	/** Tracking methods ************************************************/


	/**
	 * Prints the tracking code JavaScript.
	 *
	 * @since 1.0.0
	 */
	public function ga_tracking_code() {

		// bail if tracking is disabled
		if ( $this->do_not_track() ) {
			return;
		}

		// helper functions for ga pro
		$gateways = array();

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

	var j = JSON.stringify( object );

	for ( var k in variables ) {
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

<?php if ( $this->should_check_for_duplicate_tracking_codes() ) : ?>
/**
 * Integrate with Google Analyitcs trackers to find out whether the configured web
 * property is being tracked multiple times.
 *
 * @since 1.8.6
 */
window.wc_ga_pro.findDuplicateTrackingCodes = function() {

	var originalSendHitTasks = {},
	    pageviewHitCount     = 0,
	    reportResultsTimeout = null;

	// return early if jQuery is not available
	if ( 'undefined' === typeof jQuery ) {
		return;
	}

	/**
	 * Update all modified trackers to use their original sendHitTask functions.
	 *
	 * @since 1.8.6
	 */
	function restoreOriginalSendHitTasks() {

		var tracker, trackerName;

		for ( trackerName in originalSendHitTasks ) {

			tracker = <?php echo $this->get_ga_function_name(); ?>.getByName( trackerName );

			if ( tracker ) {
				tracker.set( 'sendHitTask', originalSendHitTasks[ trackerName ] );
			}
		}
	}


	/**
	 * Send an AJAX request to indicate whether we found duplicate tracking codes or not.
	 *
	 * @since 1.8.6
	 */
	function reportResults( hasDuplicateTrackingCodes ) {

		clearTimeout( reportResultsTimeout );

		jQuery.post(
			window.wc_ga_pro.ajax_url,
			{
				action: 'wc_<?php echo esc_js( $this->get_plugin()->get_id() ); ?>_report_duplicate_tracking_code_results',
				nonce: '<?php echo esc_js( wp_create_nonce( 'report-duplicate-tracking-code-results' ) ); ?>',
				has_duplicate_tracking_codes: hasDuplicateTrackingCodes ? 1 : 0,
			}
		);
	}

	// update all trackers created so far to sniff every hit looking for duplicates
	jQuery.each( <?php echo $this->get_ga_function_name(); ?>.getAll(), function( i, tracker ) {

		// ignore trackers for other web properties
		if ( tracker.get( 'trackingId' ) !== '<?php echo esc_js( $this->get_tracking_id() ); ?>' ) {
			return;
		}

		originalSendHitTasks[ tracker.get( 'name' ) ] = tracker.get( 'sendHitTask' );

		tracker.set( 'sendHitTask', function( model ) {

			// call the original sendHitTask function to send information to Google Analytics servers
			originalSendHitTasks[ tracker.get( 'name' ) ]( model );

			// is this a pageview hit?
			if ( /&t=pageview&/.test( model.get( 'hitPayload' ) ) ) {
				pageviewHitCount += 1;
			}

			// multiple pageview requests suggest a property is being tracked more than once
			if ( pageviewHitCount >= 2 ) {
				restoreOriginalSendHitTasks();
				reportResults( true );
			}
		} );
	} );

	// if not duplicates are detected during the first seconds, try checking if other
	// trackers (for example named trackers from GTM) were created for the same tracking ID
	reportResultsTimeout = setTimeout( function() {

		<?php echo $this->get_ga_function_name(); ?>( function() {

			var trackers = jQuery.map( <?php echo $this->get_ga_function_name(); ?>.getAll(), function( tracker ) {
				if ( '<?php echo esc_js( $this->get_tracking_id() ); ?>' === tracker.get( 'trackingId' ) ) {
					return tracker;
				}
			} );

			reportResults( trackers.length > 1 );
		} );

	}, 3000 );
}
<?php endif; ?>
</script>
<?php

		// bail if MonsterInsights is doing the basic tracking already
		if ( $this->is_monsterinsights_tracking_active() ) {
			return;
		}

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
	})(window,document,'script','https://www.google-analytics.com/analytics.js','<?php echo $this->get_ga_function_name(); ?>');
	<?php $tracker_options = $this->get_tracker_options(); ?>
	<?php echo $this->get_ga_function_name(); ?>( 'create', '<?php echo esc_js( $this->get_tracking_id() ); ?>', <?php echo ! empty( $tracker_options ) ? wp_json_encode( $tracker_options ) : "'auto'"; ?> );
	<?php echo $this->get_ga_function_name(); ?>( 'set', 'forceSSL', true );
<?php if ( 'yes' === $this->get_option( 'track_user_id' ) && is_user_logged_in() ) : ?>
	<?php echo $this->get_ga_function_name(); ?>( 'set', 'userId', '<?php echo esc_js( get_current_user_id() ) ?>' );
<?php endif; ?>
<?php if ( 'yes' === $this->get_option( 'anonymize_ip' ) ) : ?>
	<?php echo $this->get_ga_function_name(); ?>( 'set', 'anonymizeIp', true );
<?php endif; ?>
<?php if ( 'yes' === $this->get_option( 'enable_displayfeatures' ) ) : ?>
	<?php echo $this->get_ga_function_name(); ?>( 'require', 'displayfeatures' );
<?php endif; ?>
<?php if ( 'yes' === $this->get_option( 'enable_linkid' ) ) : ?>
	<?php echo $this->get_ga_function_name(); ?>( 'require', 'linkid' );
<?php endif; ?>
<?php if ( 'yes' === $this->get_option( 'enable_google_optimize' ) && '' !== $this->get_option( 'google_optimize_code' ) ) : ?>
	<?php echo $this->get_ga_function_name(); ?>( 'require', '<?php printf( '%1$s', esc_js( $this->get_option( 'google_optimize_code' ) ) ); ?>' );
<?php endif; ?>
	<?php echo $this->get_ga_function_name(); ?>( 'require', 'ec' );
<?php if ( $this->should_check_for_duplicate_tracking_codes() ) : ?>
	<?php echo $this->get_ga_function_name(); ?>( wc_ga_pro.findDuplicateTrackingCodes );
<?php endif; ?>

	<?php
	/**
	 * Fires after the JS tracking code is setup.
	 *
	 * Allows to add custom JS calls after tracking code is setup.
	 *
	 * @since 1.3.5
	 *
	 * @param string $ga_function_name google analytics tracking function name
	 * @param string $tracking_id google analytics tracking ID
	 */
	do_action( 'wc_google_analytics_pro_after_tracking_code_setup', $this->get_ga_function_name(), $this->get_tracking_id() );
	?>

	(function() {

		// trigger an event the old-fashioned way to avoid a jQuery dependency and still support IE
		var event = document.createEvent( 'Event' );

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
	 * @since 1.8.10
	 *
	 * @return string
	 */
	private function get_script_attributes() {

		/**
		 * Filters Google Analytis Pro script attributes.
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
	 * Returns the JS tracker options.
	 *
	 * @since 1.3.5
	 *
	 * @return array|null
	 */
	private function get_tracker_options() {

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
	 * Outputs the event tracking JavaScript.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_name the name of the event to be set
	 * @param array|string $properties optional: the properties to be set with event
	 * @return bool whether the event was recorded
	 */
	private function js_record_event( $event_name, $properties = array() ) {

		$record = true;

		// skip if there are no valid properties
		if ( ! is_array( $properties ) ) {
			$record = false;
		}

		// verify tracking status
		if ( $record && $this->do_not_track() ) {
			$record = false;
		}

		// MonsterInsights is in non-universal mode: skip
		if ( $record && $this->is_monsterinsights_tracking_active() && ! $this->is_monsterinsights_tracking_universal() ) {
			$record = false;
		}

		if ( $record ) {
			$this->enqueue_js( 'event', $this->get_event_tracking_js( $event_name, $properties ) );
		}

		return $record;
	}


	/**
	 * Returns event tracking JS code.
	 *
	 * @since 1.0.0
	 * @param string $event_name the name of the vent to be set
	 * @param array/string $properties the properties to be set with event
	 * @param string|null $js_args_variable (optional) name of the JS variable to use for interpolating dynamic event properties
	 * @return string|null
	 */
	private function get_event_tracking_js( $event_name, $properties, $js_args_variable = null ) {

		if ( ! is_array( $properties ) ) {
			return;
		}

		$properties = array(
			'hitType'        => isset( $properties['hitType'] )        ? $properties['hitType']        : 'event',     // Required
			'eventCategory'  => isset( $properties['eventCategory'] )  ? $properties['eventCategory']  : 'page',      // Required
			'eventAction'    => isset( $properties['eventAction'] )    ? $properties['eventAction']    : $event_name, // Required
			'eventLabel'     => isset( $properties['eventLabel'] )     ? $properties['eventLabel']     : null,
			'eventValue'     => isset( $properties['eventValue'] )     ? $properties['eventValue']     : null,
			'nonInteraction' => isset( $properties['nonInteraction'] ) ? $properties['nonInteraction'] : false,
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
	 * Records an event via the Measurement Protocol API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_name the name of the event to be set
	 * @param string[] $properties the properties to be set with event
	 * @param string[] $ec additional enhanced ecommerce data to be sent with the event
	 * @param string[] $identities (optional) identities to use when tracking the event - if not provided, auto-detects from GA cookie and current user
	 * @param bool $admin_event whether the event is an admin one
	 * @return bool whether event was recorded
	 */
	public function api_record_event( $event_name, $properties = array(), $ec = array(), $identities = null, $admin_event = false ) {

		$record  = false;
		$user_id = is_array( $identities ) && isset( $identities['uid'] ) ? $identities['uid'] : null;

		// verify tracking status
		if ( ! $this->do_not_track( $admin_event, $user_id ) ) {

			// remove blank properties/ec properties
			unset( $properties[''], $ec[''] );

			// auto-detect identities, if not provided
			if ( ! is_array( $identities ) || empty( $identities ) || empty( $identities['cid'] ) ) {
				$identities = $this->get_identities();
			}

			// proceed if CID is not null
			if ( ! empty( $identities['cid'] ) ) {

				// remove user ID, unless user ID tracking is enabled,
				if ( 'yes' !== $this->get_option( 'track_user_id' ) && isset( $identities['uid'] ) ) {
					unset( $identities['uid'] );
				}

				// set IP and user-agent overrides, unless already provided
				if ( empty( $identities['uip'] ) ) {
					$identities['uip'] = $this->get_client_ip();
				}

				if ( empty( $identities['ua'] ) ) {
					$identities['ua'] = wc_get_user_agent();
				}

				// track the event via Measurement Protocol
				$this->get_measurement_protocol_api()->track_event( $event_name, $identities, $properties, $ec );

				$record = true;
			}
		}

		return $record;
	}


	/**
	 * Gets the code to add a product to the tracking code.
	 *
	 * @since 1.0.0
	 * @global array $woocommerce_loop The WooCommerce loop position data
	 * @param int $product_id ID of the product to add.
	 * @param int $quantity Optional. Quantity to add to the code.
	 * @return string Code to use within a tracking code.
	 */
	private function get_ec_add_product_js( $product_id, $quantity = 1 ) {
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
			$product_details['id']       = $this->get_product_identifier( $product );
			$product_details['name']     = $product->get_title();
			$product_details['category'] = $this->get_category_hierarchy( $product );
			$product_details['variant']  = $this->get_product_variation_attributes( $product );
			$product_details['price']    = $product->get_price();
		}

		/**
		 * Filters the product details data (productFieldObject).
		 *
		 * @link https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#product-data
		 *
		 * @since 1.1.1
		 *
		 * @param array $product_details_data an associative array of product product details data
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
	 * Gets a unique identity for the current user.
	 *
	 * @link http://www.stumiller.me/implementing-google-analytics-measurement-protocol-in-php-and-wordpress/
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force_generate_uuid (optional) whether to force generating a UUID if no CID can be found from cookies, defaults to false
	 * @return string the visitor's ID from Google's cookie, or user's meta, or generated
	 */
	private function get_cid( $force_generate_uuid = false ) {

		$identity = '';

		// get identity via GA cookie
		if ( isset( $_COOKIE['_ga'] ) ) {

			list( $version, $domainDepth, $cid1, $cid2 ) = preg_split( '[\.]', $_COOKIE['_ga'], 4 );

			$contents = array( 'version' => $version, 'domainDepth' => $domainDepth, 'cid' => $cid1 . '.' . $cid2 );
			$identity = $contents['cid'];
		}

		// generate UUID if identity is not set
		if ( empty( $identity ) ) {

			// neither cookie set and named identity not passed, cookies are probably disabled for visitor or GA tracking might be blocked
			if ( $this->debug_mode_on() ) {

				wc_google_analytics_pro()->log( 'No identity found. Cookies are probably disabled for visitor or GA tracking might be blocked.' );
			}

			// by default, a UUID will only be generated if we have no CID, we have a user logged in and user-id tracking is enabled
			// note: when changing this logic here, adjust the logic in WC_Google_Analytics_Pro_Email_Tracking::track_opens() as well
			$generate_uuid = $force_generate_uuid || ( ! $identity && is_user_logged_in() && 'yes' === $this->get_option( 'track_user_id' ) );

			/**
			 * Filters whether a client ID should be generated.
			 *
			 * Allows generating a UUID for to be used as the client ID, when it can't be determined from cookies or other sources, such as the order or user meta.
			 *
			 * @since 1.3.5
			 *
			 * @param bool $generate_uuid the generate UUID flag
			 */
			$generate_uuid = apply_filters( 'wc_google_analytics_pro_generate_client_id', $generate_uuid );

			if ( $generate_uuid ) {

				$identity = $this->generate_uuid();
			}
		}

		return $identity;
	}


	/**
	 * Gets an the current visitor identities.
	 *
	 * Returns 1 or 2 identities - the CID (GA client ID from cookie) and
	 * current user ID, if available.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function get_identities() {

		$identities = array();

		// get CID
		$cid = $this->get_cid();

		// set CID only if it is not null
		if ( ! empty( $cid ) ) {
			$identities['cid'] = $cid;
		}

		if ( is_user_logged_in() ) {
			$identities['uid'] = get_current_user_id();
		}

		return $identities;
	}


	/**
	 * Generates a UUID v4.
	 *
	 * Needed to generate a CID when one isn't available.
	 *
	 * @link https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/15875555#15875555
	 *
	 * @since 1.0.0
	 *
	 * @return string the generated UUID
	 */
	public function generate_uuid() {

		try {

			$bytes = random_bytes( 16 );

			$bytes[6] = chr( ord( $bytes[6] ) & 0x0f | 0x40 ); // set version to 0100
			$bytes[8] = chr( ord( $bytes[8] ) & 0x3f | 0x80 ); // set bits 6-7 to 10

			return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $bytes ), 4 ) );

		} catch( Exception $e ) {

			// fall back to mt_rand if random_bytes is unavailable
			return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,
				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
		}
	}


	/**
	 * Determines if tracking is disabled.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool $admin_event (optional) Whether or not this is an admin event that should be tracked. Defaults to false.
	 * @param  int  $user_id     (optional) User ID to check roles for
	 * @return bool
	 */
	private function do_not_track( $admin_event = false, $user_id = null ) {

		// do not track activity in the admin area, unless specified
		if ( ! $admin_event && ! is_ajax() && is_admin() ) {
			$do_not_track = true;
		} else {
			$do_not_track = ! $this->is_tracking_enabled_for_user_role( $user_id );
		}

		/**
		 * Filters whether tracking should be disabled.
		 *
		 * @since 1.5.0
		 *
		 * @param bool $do_not_track
		 * @param bool $admin_event
		 * @param int  $user_id
		 */
		return (bool) apply_filters( 'wc_google_analytics_pro_do_not_track', $do_not_track, $admin_event, $user_id );
	}


	/**
	 * Determines if tracking should be performed for the provided user, by the role.
	 *
	 * In 1.3.5 removed the $admin_event param
	 *
	 * @since 1.0.0
	 * @param int $user_id (optional) user id to check, defaults to current user id
	 * @return bool
	 */
	public function is_tracking_enabled_for_user_role( $user_id = null ) {

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ! $this->is_enabled() ) {

			$this->user_tracking_enabled[ $user_id ] = false;

		} elseif ( ! isset( $this->user_tracking_enabled[ $user_id ] ) ) {

			// enable tracking by default for all users and visitors
			$enabled = true;

			// get user's info
			$user = get_user_by( 'id', $user_id );

			if ( $user && wc_google_analytics_pro()->is_monsterinsights_active() ) {

				// if MonsterInsights is active, use their setting for disallowed roles,
				// see Yoast_GA_Universal::do_tracking(), monsterinsights_disabled_user_group()
				$ignored_roles = wc_google_analytics_pro()->get_monsterinsights_option( 'ignore_users' );

				if ( ! empty( $ignored_roles ) ) {
					$enabled = array_intersect( $user->roles, $ignored_roles ) ? false : true;
				}

			} elseif ( $user && user_can( $user_id, 'manage_woocommerce' ) ) {

				// Enable tracking of admins and shop managers only if checked in settings.
				$enabled = 'yes' === $this->get_option( 'admin_tracking_enabled' );

			}

			$this->user_tracking_enabled[ $user_id ] = $enabled;
		}

		return $this->user_tracking_enabled[ $user_id ];
	}


	/**
	 * Determines if a request was not a page reload.
	 *
	 * Prevents duplication of tracking events when user submits
	 * a form, e.g. applying a coupon on the cart page.
	 *
	 * This is not intended to prevent pageview events on a manual page refresh.
	 * Those are valid user interactions and should still be tracked.
	 *
	 * @since 1.0.0
	 *
	 * @return bool true if not a page reload, false if page reload
	 */
	private function not_page_reload() {

		// no referer..consider it's not a reload.
		if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			return true;
		}

		// compare paths
		return ( parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_PATH ) !== parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
	}


	/**
	 * Determines wehther we should try to detect duplicate tracking codes.
	 *
	 * @since 1.8.6
	 *
	 * @return bool
	 */
	private function should_check_for_duplicate_tracking_codes() {

		$should_check = false === get_transient( 'wc_' . $this->get_plugin()->get_id() . '_site_has_duplicate_tracking_codes' );

		// tracking Administrators or Shop Managers may not be enabled on other plugins
		// so is better to wait and check for duplicates when a different user is
		// exploring the website
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$should_check = false;
		}

		/**
		 * Filters whether we should try to detect duplicate tracking codes.
		 *
		 * @since 1.8.11
		 *
		 * @param bool $should_check whther we should check for duplicate tracking codes
		 * @param \WC_Google_Analytics_Pro_Integration $integration the integration instance
		 */
		return (bool) apply_filters( 'wc_google_analytics_pro_should_check_for_duplicate_tracking_codes', $should_check, $this );
	}


	/**
	 * Deletes the results from the duplicate tracking code verification when a
	 * a plugin is deactivated.
	 *
	 * If the deactivated plugin was the one sending duplicate events, checking
	 * again should show that there are no problems anymore and the notice will
	 * remain hidden.
	 *
	 * @since 1.8.6
	 */
	public function clear_duplicate_tracking_code_results() {

		// delete the transient if we previously detected a conflict only
		if ( 'yes' === get_transient( 'wc_' . $this->get_plugin()->get_id() . '_site_has_duplicate_tracking_codes' ) ) {
			delete_transient( 'wc_' . $this->get_plugin()->get_id() . '_site_has_duplicate_tracking_codes' );
		}
	}


	/**
	 * Returns the visitor's IP
	 *
	 * @since 1.3.0
	 * @return string client IP
	 */
	private function get_client_ip() {

		return \WC_Geolocation::get_ip_address();
	}


	/** MonsterInsights integration methods *************************************************/


	/**
	 * Sets MonsterInsights tracking data.
	 *
	 * Invoked by a filter at the end of MonsterInsights' tracking.
	 * If we came here then MonsterInsights is going to print the GA init script.
	 * In 1.3.0 renamed from `yoast_ga_push_array_universal` to `set_monsterinsights_tracking_data`
	 *
	 * @internal
	 *
	 * @see Yoast_GA_Universal::tracking
	 * @see MonsterInsights_Tracking_Analytics::frontend_tracking_options()
	 *
	 * @since 1.0.0
	 *
	 * @param array $data the tracking data
	 * @return array
	 */
	public function set_monsterinsights_tracking_data( $data ) {

		$this->_monsterinsights_tracking_type = 'universal';

		// require Enhanced Ecommerce
		$data[] = "'require','ec'";

		// remove pageview tracking, as we need to track it in the footer instead (because of product impressions)
		foreach ( $data as $key => $value ) {

			// check strpos() rather than strict equal to account for search archives and 404 pages
			if ( is_string( $value ) && false !== strpos( $value, "'send','pageview'" ) ) {
				unset( $data[ $key ] );
			}
		}

		return $data;
	}


	/**
	 * Sets the internal MonsterInsights' tracking type.
	 *
	 * Invoked by a filter at the end of MonsterInsights' tracking.
	 * If we came here then MonsterInsights is going to print the GA init script.
	 *
	 * In 1.3.0 renamed from `set_yoast_ga_tracking_type_ga_js` to `set_monsterinsights_tracking_type_ga_js`
	 *
	 * @internal
	 *
	 * @see Yoast_GA_JS::tracking
	 *
	 * @since 1.0.0
	 * @param mixed $ignore Ignored because we just need a trigger, not data.
	 * @return mixed
	 */
	public function set_monsterinsights_tracking_type_ga_js( $ignore ) {

		$this->_monsterinsights_tracking_type = 'ga-js';

		return $ignore;
	}


	/**
	 * Returns MonsterInsights' GA tracking type.
	 *
	 * In 1.3.0 renamed from `get_yoast_ga_tracking_type` to `get_monsterinsights_tracking_type`
	 *
	 * @since 1.0.0
	 * @return string MonsterInsights' GA tracking type
	 */
	public function get_monsterinsights_tracking_type() {

		return $this->_monsterinsights_tracking_type;
	}


	/**
	 * Determines if MonsterInsights' tracking is active.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_monsterinsights_tracking_active() {

		return $this->get_monsterinsights_tracking_type() !== '';
	}


	/**
	 * Determines if MonsterInsights' GA tracking is universal.
	 *
	 * In 1.3.0 renamed from `is_yoast_ga_tracking_universal` to `is_monsterinsights_tracking_universal`
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_monsterinsights_tracking_universal() {

		return 'universal' === $this->get_monsterinsights_tracking_type();
	}


	/** Helper methods ********************************************************/


	/**
	 * Determines if this tracking integration supports property names.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected function supports_property_names() {

		return false;
	}


	/**
	 * Gets the plugin instance.
	 *
	 * @since 1.0.0
	 * @return \WC_Google_Analytics_Pro
	 */
	protected function get_plugin() {

		return wc_google_analytics_pro();
	}


	/**
	 * Gets the configured Google Analytics tracking ID.
	 *
	 * @since 1.0.0
	 * @return string the tracking ID
	 */
	public function get_tracking_id() {

		// MonsterInsights' settings override ours
		if ( wc_google_analytics_pro()->is_monsterinsights_active() ) {
			return class_exists( 'Yoast_GA_Options' ) ? Yoast_GA_Options::instance()->get_tracking_code() : monsterinsights_get_ua_to_output();
		}

		/**
		 * Filters the tracking ID for the Google Analytics property being used.
		 *
		 * @since 1.2.0
		 *
		 * @param string $tracking_id the tracking code
		 * @param \WC_Google_Analytics_Pro_Integration $integration the integration instance
		 */
		return apply_filters( 'wc_google_analytics_pro_tracking_id', $this->get_option( 'tracking_id' ), $this );
	}


	/**
	 * Gets the Management API handler.
	 *
	 * @since 1.7.0
	 *
	 * @return \SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API
	 */
	public function get_management_api() {

		if ( $this->management_api instanceof \SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API ) {
			return $this->management_api;
		}

		// account management API wrapper
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-google-analytics-pro-management-api.php' );
		// account management API request
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-google-analytics-pro-management-api-request.php' );
		// account management API responses
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/abstract-wc-google-analytics-pro-management-api-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-google-analytics-pro-management-api-account-summaries-response.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-google-analytics-pro-management-api-profiles-response.php' );

		// the management API needs to be initialized with a token for authentication
		$token = $this->parse_access_token( $this->get_access_token() );

		// refresh token if it's expired
		if ( $this->is_access_token_expired( $token ) ) {

			try {

				$token = $this->refresh_access_token();

			} catch ( Framework\SV_WC_API_Exception $e ) {

				if ( $this->debug_mode_on() ) {
					$this->get_plugin()->log( $e->getMessage() );
				}

				$token = $this->parse_access_token();
			}
		}

		return $this->management_api = new \SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API( $token->access_token );
	}


	/**
	 * Gets the Measurement Protocol API handler.
	 *
	 * @since 1.7.0
	 *
	 * @return \WC_Google_Analytics_Pro_Measurement_Protocol_API
	 */
	public function get_measurement_protocol_api() {

		if ( $this->measurement_protocol_api instanceof \WC_Google_Analytics_Pro_Measurement_Protocol_API ) {
			return $this->measurement_protocol_api;
		}

		// measurement protocol API wrapper
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-google-analytics-pro-measurement-protocol-api.php' );
		// measurement protocol API request
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-google-analytics-pro-measurement-protocol-api-request.php' );
		// measurement protocol API response
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/api/class-wc-google-analytics-pro-measurement-protocol-api-response.php' );

		return $this->measurement_protocol_api = new \WC_Google_Analytics_Pro_Measurement_Protocol_API( $this->get_tracking_id() );
	}


	/**
	 * Gets the list type for the current screen.
	 *
	 * @since 1.0.0
	 * @return string the list type for the current screen
	 */
	public function get_list_type() {

		$list_type = '';

		if ( is_search() ) {

			$list_type = __( 'Search', 'woocommerce-google-analytics-pro' );

		} elseif ( is_product_category() ) {

			$list_type = __( 'Product category', 'woocommerce-google-analytics-pro' );

		} elseif ( is_product_tag() ) {

			$list_type = __( 'Product tag', 'woocommerce-google-analytics-pro' );

		} elseif ( is_archive() ) {

			$list_type = __( 'Archive', 'woocommerce-google-analytics-pro' );

		} elseif ( is_single() ) {

			$list_type = __( 'Related/Up sell', 'woocommerce-google-analytics-pro' );

		} elseif ( is_cart() ) {

			$list_type = __( 'Cross sell (cart)', 'woocommerce-google-analytics-pro' );
		}

		/**
		 * Filters the list type for the current screen.
		 *
		 * @since 1.0.0
		 * @param string $list_type the list type for the current screen
		 */
		return apply_filters( 'wc_google_analytics_pro_list_type', $list_type );
	}


	/**
	 * Returns the Enhanced Ecommerce action JavaScript of the provided event key if it exists.
	 *
	 * @since 1.3.0
	 * @param string $action the action, see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#action-types for available options
	 * @param array $args Optional. An array of args to be encoded as the `actionFieldObject`, see https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#action-data for availalable options
	 * @param string|null $js_args_variable (optional) name of the JS variable to use for interpolating dynamic event properties
	 * @return string the JavaScript or an empty string
	 */
	private function get_ec_action_js( $action, $args = array(), $js_args_variable = null ) {

		$args = wp_json_encode( $args );

		// interpolate dynamic event properties
		if ( $js_args_variable ) {
			$args = sprintf( 'window.wc_ga_pro.interpolate_json( %s, %s )', $args, $js_args_variable );
		}

		return sprintf( "%s( 'ec:setAction', '%s', %s );", $this->get_ga_function_name(), $action, $args );
	}


	/** Settings **************************************************************/


	/**
	 * Initializes form fields in the format required by \WC_Integration.
	 *
	 * @see \Framework\SV_WC_Tracking_Integration::init_form_fields()
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields() {

		// initialize common fields
		parent::init_form_fields();

		$form_fields = array_merge( array(

			'tracking_settings_section' => array(
				'title' => __( 'Tracking Settings', 'woocommerce-google-analytics-pro' ),
				'type'  => 'title',
			),

			'enabled' => array(
				'title'   => __( 'Enable Google Analytics tracking', 'woocommerce-google-analytics-pro' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),
		),

		$this->get_auth_fields(),

		array(

			'use_manual_tracking_id' => array(
				'label'       => __( 'Enter tracking ID manually (not recommended)', 'woocommerce-google-analytics-pro' ),
				'type'        => 'checkbox',
				'class'       => 'js-wc-google-analytics-toggle-manual-tracking-id',
				'default'     => 'no',
				'desc_tip'    => __( "We won't be able to display reports or configure your account automatically", 'woocommerce-google-analytics-pro' ),
			),

			'tracking_id' => array(
				'title'       => __( 'Google Analytics tracking ID', 'woocommerce-google-analytics-pro' ),
				'label'       => __( 'Google Analytics tracking ID', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'Go to your Google Analytics account to find your ID. e.g. <code>UA-XXXXX-X</code>', 'woocommerce-google-analytics-pro' ),
				'type'        => 'text',
				'default'     => '',
				'placeholder' => 'UA-XXXXX-X',
			),

			'admin_tracking_enabled' => array(
				'title'       => __( 'Track Administrators?', 'woocommerce-google-analytics-pro' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'description' => __( 'Check to enable tracking when logged in as Administrator or Shop Manager.', 'woocommerce-google-analytics-pro' ),
			),

			'enable_displayfeatures' => array(
				'title'         => __( 'Tracking Options', 'woocommerce-google-analytics-pro' ),
				'label'         => __( 'Use Advertising Features', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => 'start',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'Set the Google Analytics code to support Demographics and Interests Reports for Remarketing and Advertising. %1$sRead more about Advertising Features%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/2700409" target="_blank">', '</a>' ),
			),

			'enable_linkid' => array(
				'label'         => __( 'Use Enhanced Link Attribution', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'Set the Google Analytics code to support Enhanced Link Attribution. %1$sRead more about Enhanced Link Attribution%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution" target="_blank">', '</a>' ),
			),

			'anonymize_ip'          => array(
				'label'         => __( 'Anonymize IP addresses', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => '',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'Enabling this option is mandatory in certain countries due to national privacy laws. %1$sRead more about IP Anonymization%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/2763052" target="_blank">', '</a>' ),
			),

			'track_user_id'         => array(
				'label'         => __( 'Track User ID', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				'checkboxgroup' => '',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'Enable User ID tracking. %1$sRead more about the User ID feature%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/3123662" target="_blank">', '</a>' ),
			),

			'enable_google_optimize' => array(
				'title'         => __( 'Google Optimize', 'woocommerce-google-analytics-pro' ),
				'label'         => __( 'Enable Google Optimize', 'woocommerce-google-analytics-pro' ),
				'type'          => 'checkbox',
				'default'       => 'no',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( '%1$sRead more about Google Optimize%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://www.google.com/analytics/optimize" target="_blank">', '</a>' ),
			),

			'google_optimize_code' => array(
				'title'         => __( 'Google Optimize Code', 'woocommerce-google-analytics-pro' ),
				'type'          => 'text',
				'default'       => '',
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description'   => sprintf( __( 'e.g. "GTM-XXXXXX". %1$sRead more about this code%2$s', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/360suite/optimize/answer/6262084" target="_blank">', '</a>' ),
			),

			'track_product_impressions_on' => array(
				'title'       => __( 'Track product impressions on:', 'woocommerce-google-analytics-pro' ),
				'desc_tip'    => __( 'Control where product impressions are tracked.', 'woocommerce-google-analytics-pro' ),
				'description' => __( 'If you\'re running into issues, particularly if you see the "No HTTP response detected" error, try disabling product impressions on archive pages.', 'woocommerce-google-analytics-pro' ),
				'type'        => 'multiselect',
				'class'       => 'wc-enhanced-select',
				'options'     => array(
					'single_product_pages' => __( 'Single Product Pages', 'woocommerce-google-analytics-pro' ),
					'archive_pages'        => __( 'Archive Pages', 'woocommerce-google-analytics-pro' ),
				),
				'default'     => array( 'single_product_pages', 'archive_pages' ),
			),

		),

		$this->form_fields,

		array(

			'funnel_steps_section' => array(
				'title'       => __( 'Checkout Funnel', 'woocommerce-google-analytics-pro' ),
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				'description' => sprintf( __( 'Configure your Analytics account to match the checkout funnel steps below to take advantage of %1$sCheckout Behavior Analysis%2$s.', 'woocommerce-google-analytics-pro' ), '<a href="https://support.google.com/analytics/answer/6014872?hl=en#cba">', '</a>' ),
				'type'        => 'title',
			),

			'funnel_steps' => array(
				'title' => __( 'Funnel Steps', 'woocommerce-google-analytics-pro' ),
				'type'  => 'ga_pro_funnel_steps',
			),

		)

		);

		// TODO: remove this block when removing backwards compatibility with __gaTracker {IT 2016-10-12}
		if ( get_option( 'woocommerce_google_analytics_upgraded_from_gatracker' ) ) {

			$compat_fields['function_name'] = array(
				'title'     => __( 'JavaScript function name', 'woocommerce-google-analytics-pro' ),
				/* translators: %1$s - function name, %2$s - function name */
				'description' => sprintf( __( 'Set the global tracker function name. %1$s is deprecated and support for it will be removed in a future version. IMPORTANT: set the function name to %2$s only after any custom code is updated to use %2$s.', 'woocommerce-google-analytics-pro' ), '<code>__gaTracker</code>', '<code>ga</code>' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options' => array(
					'ga'          => 'ga ' . __( '(Recommended)', 'woocommerce-google-analytics-pro' ),
					'__gaTracker' => '__gaTracker',
				),
				'default' => '__gaTracker',
			);

			$form_fields = Framework\SV_WC_Helper::array_insert_after( $form_fields, 'additional_settings_section', $compat_fields );
		}

		/**
		 * Filters Google Analytics Pro Settings.
		 *
		 * @since 1.3.0
		 * @param array $settings settings fields
		 * @param \WC_Google_Analytics_Pro_Integration $ga_pro_integration instance
		 */
		$this->form_fields = apply_filters( 'wc_google_analytics_pro_settings', $form_fields, $this );
	}


	/**
	 * Outputs checkout funnel steps table.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $key
	 * @param mixed $data
	 * @return string HTML
	 */
	public function generate_ga_pro_funnel_steps_html( $key, $data ) {

		$columns = array(
			'step'    => __( 'Step', 'woocommerce-google-analytics-pro' ),
			'event'   => __( 'Event', 'woocommerce-google-analytics-pro' ),
			'name'    => __( 'Name', 'woocommerce-google-analytics-pro' ),
			'status'  => __( 'Enabled', 'woocommerce-google-analytics-pro' ),
		);

		$steps = array(
			1 => 'started_checkout',
			2 => 'provided_billing_email',
			3 => 'selected_payment_method',
			4 => 'placed_order',
		);

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php esc_html__( $data['title'] ); ?></th>
			<td class="forminp">
				<table class="wc-google-analytics-pro-funnel-steps widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								foreach ( $columns as $key => $column ) {
									echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach ( $steps as $step => $event ) {

								echo '<tr class="event-' . esc_attr( $event ) . '" data-event="' . esc_attr( $event ) . '">';

								foreach ( $columns as $key => $column ) {

									switch ( $key ) {

										case 'step' :
											echo '<td class="step">' . $step . '</td>';
											break;

										case 'event' :
											$event_title = $this->get_event_title( $event );
											echo '<td class="event"><a href="#woocommerce_google_analytics_pro_' . esc_attr( $event ) . '_event_name">' . esc_html( $event_title ) . '</a></td>';
											break;

										case 'name' :
											echo '<td class="name">' . esc_html( $this->get_event_name( $event ) ) . '</td>';
											break;

										case 'status' :
											echo '<td class="status">';
											echo '<span class="status-enabled tips" ' . ( ! $this->get_event_name( $event ) ? 'style="display:none;"' : '' ) . ' data-tip="' . __( 'Yes', 'woocommerce-google-analytics-pro' ) . '">' . __( 'Yes', 'woocommerce-google-analytics-pro' ) . '</span>';
											echo '<span class="status-disabled tips" ' . ( $this->get_event_name( $event ) ? 'style="display:none;"' : '' ) . ' data-tip="' . __( 'Currently disabled, because the event name is not set.', 'woocommerce-google-analytics-pro' ) . '">-</span>';
											echo '</td>';
											break;
									}
								}

								echo '</tr>';
							}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns the authentication fields.
	 *
	 * Only when on the plugin settings screen as this requires an API call to GA to get property data.
	 *
	 * @since 1.0.0
	 * @return array the authentication fields or an empty array
	 */
	protected function get_auth_fields() {

		if ( ! wc_google_analytics_pro()->is_plugin_settings() ) {
			return array();
		}

		$auth_fields = array();

		$ga_properties      = $this->get_access_token() ? $this->get_ga_properties() : null;
		$auth_button_text = $this->get_access_token() ? esc_html__( 'Re-authenticate with your Google account', 'woocommerce-google-analytics-pro' ) : esc_html__( 'Authenticate with your Google account', 'woocommerce-google-analytics-pro' );

		if ( ! empty( $ga_properties ) ) {

			// add empty option so clearing the field is possible
			$ga_properties = array_merge( array( '' => '' ), $ga_properties );

			$auth_fields = array(
				'property' => array(
					'title'    => __( 'Google Analytics Property', 'woocommerce-google-analytics-pro' ),
					'type'     => 'deep_select',
					'default'  => '',
					'class'    => 'wc-enhanced-select-nostd',
					'options'  => $ga_properties,
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select a property&hellip;', 'woocommerce-google-analytics-pro' ),
					),
					'desc_tip' => __( "Choose which Analytics property you want to track", 'woocommerce-google-analytics-pro' ),
				),
			);
		}

		$auth_fields['oauth_button'] = array(
			'type'     => 'button',
			'default'  => $auth_button_text,
			'class'    => 'button',
			'desc_tip' => __( 'We need view & edit access to your Analytics account so we can display reports and automatically configure Analytics settings for you.', 'woocommerce-google-analytics-pro' ),
		);

		if ( empty( $ga_properties ) ) {
			$auth_fields['oauth_button']['title'] = __( 'Google Analytics Property', 'woocommerce-google-analytics-pro' );
		}

		if ( $this->get_access_token() ) {
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			$auth_fields['oauth_button']['description'] = sprintf( __( 'or %1$srevoke authorization%2$s' ), '<a href="#" class="js-wc-google-analytics-pro-revoke-authorization">', '</a>' );
		}

		return $auth_fields;
	}


	/**
	 * Gets the Google API authentication URL.
	 *
	 * @since 1.0.0
	 * @return string the Google Client API authentication URL
	 */
	public function get_auth_url() {

		return self::PROXY_URL . '/auth?callback=' . urlencode( $this->get_callback_url() );
	}


	/**
	 * Gets the Google API refresh token.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	private function get_refresh_token() {

		return get_option( 'wc_google_analytics_pro_refresh_token', null );
	}


	/**
	 * Gets the Google API refresh access token URL, if a refresh token is available.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_access_token_refresh_url() {

		$refresh_url = null;

		if ( $refresh_token = $this->get_refresh_token() ) {
			$refresh_url = self::PROXY_URL . '/auth/refresh?token=' . base64_encode( $refresh_token );
		}

		return $refresh_url;
	}


	/**
	 * Gets the Google API revoke access token URL, if a token is available.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_access_token_revoke_url() {

		$revoke_url = null;

		if ( $token = $this->get_access_token() ) {
			$revoke_url = self::PROXY_URL . '/auth/revoke?token=' . base64_encode( $token );
		}

		return $revoke_url;
	}


	/**
	 * Gets the Google API callback URL.
	 *
	 * @since 1.0.0
	 * @return string url
	 */
	public function get_callback_url() {

		return get_home_url( null, 'wc-api/wc-google-analytics-pro/auth' );
	}


	/** Event tracking methods ******************************/


	/**
	 * Tracks a pageview.
	 *
	 * @since 1.0.0
	 */
	public function pageview() {

		if ( $this->do_not_track() ) {
			return;
		}

		// MonsterInsights is in non-universal mode, skip
		if ( $this->is_monsterinsights_tracking_active() && ! $this->is_monsterinsights_tracking_universal() ) {
			return;
		}

		$this->enqueue_js( 'pageview', $this->get_ga_function_name() . "( 'send', 'pageview' );" );
	}


	/**
	 * Tracks a homepage view.
	 *
	 * @since 1.1.3
	 */
	public function viewed_homepage() {

		// bail if tracking is disabled
		if ( $this->do_not_track() ) {
			return;
		}

		if ( is_front_page() && $this->event_name['viewed_homepage'] ) {

			$properties = array(
				'eventCategory'  => 'Homepage',
				'nonInteraction' => true,
			);

			$this->js_record_event( $this->event_name['viewed_homepage'], $properties );
		}
	}


	/**
	 * Tracks the log-in event.
	 *
	 * @since 1.0.0
	 *
	 * @param string $user_login the signed-in username
	 * @param \WP_User $user the logged-in user object
	 */
	public function signed_in( $user_login, $user ) {

		/**
		 * Filters the user roles track on the signed in event.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] array of user roles to track the event for
		 */
		if ( isset( $user->roles[0] ) && in_array( $user->roles[0], (array) apply_filters( 'wc_google_analytics_pro_signed_in_user_roles', [ 'subscriber', 'customer' ] ), true ) ) {

			// note: we send the user ID and not their login (email) to comply with Google policy for not sending personally identifiable information
			$properties = [
				'eventCategory' => 'My Account',
				'eventLabel'    => $user->ID,
			];

			$ec      = null;
			$post_id = url_to_postid( wp_unslash( $_SERVER['REQUEST_URI'] ) );

			// logged in at checkout
			if ( $post_id && $post_id === (int) get_option( 'woocommerce_checkout_page_id' ) ) {
				$ec = [ 'checkout_option' => [
					'step'   => 1,
					'option' => __( 'Registered User', 'woocommerce-google-analytics-pro' ) // can't check is_user_logged_in() as it still returns false here
				] ];
			}

			$this->api_record_event( $this->event_name['signed_in'], $properties, $ec );

			// get CID
			$cid = $this->get_cid();

			// store CID in user meta if it is not empty
			if ( ! empty( $cid ) ) {

				// store GA identity in user meta
				update_user_meta( $user->ID, '_wc_google_analytics_pro_identity', $cid );
			}
		}
	}


	/**
	 * Tracks a sign-out event.
	 *
	 * @since 1.0.0
	 */
	public function signed_out() {

		$this->api_record_event( $this->event_name['signed_out'], [
			'eventCategory' => 'My Account',
		] );
	}


	/**
	 * Tracks sign up page view (on my account page when enabled).
	 *
	 * @since 1.0.0
	 */
	public function viewed_signup() {

		if ( $this->not_page_reload() ) {

			$properties = array(
				'eventCategory'  => 'My Account',
				'nonInteraction' => true,
			);

			$this->js_record_event( $this->event_name['viewed_signup'], $properties );
		}
	}


	/**
	 * Tracks the sign up event.
	 *
	 * @since 1.0.0
	 */
	public function signed_up() {

		$properties = array(
			'eventCategory' => 'My Account',
		);

		$this->api_record_event( $this->event_name['signed_up'], $properties );
	}


	/**
	 * Track a product view.
	 *
	 * @since 1.0.0
	 */
	public function viewed_product() {

		// bail if tracking is disabled
		if ( $this->do_not_track() ) {
			return;
		}

		if ( $this->not_page_reload() ) {

			// add Enhanced Ecommerce tracking
			$product_id = get_the_ID();

			// JS add product
			$js = $this->get_ec_add_product_js( $product_id );

			// JS add action
			$js .= $this->get_ec_action_js( 'detail' );

			// enqueue JS
			$this->enqueue_js( 'event', $js );

			// set event properties - EC data will be sent with the event
			$properties = array(
				'eventCategory'  => 'Products',
				'eventLabel'     => esc_js( get_the_title() ),
				'nonInteraction' => true,
			);

			$this->js_record_event( $this->event_name['viewed_product'], $properties );
		}
	}


	/**
	 * Tracks a product click event.
	 *
	 * @since 1.0.0
	 */
	public function clicked_product() {

		if ( $this->do_not_track() ) {
			return;
		}

		// MonsterInsights is in non-universal mode, skip
		if ( $this->is_monsterinsights_tracking_active() && ! $this->is_monsterinsights_tracking_universal() ) {
			return;
		}

		global $product;

		$list       = $this->get_list_type();
		$properties = array(
			'eventCategory' => 'Products',
			'eventLabel'    => htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' ),
		);

		if ( $parent_id = $product->get_parent_id() ) {
			$product_id = $parent_id;
		} else {
			$product_id = $product->get_id();
		}

		$js =
			"$( '.products .post-" . esc_js( $product_id ) . " a' ).click( function() {
				if ( true === $(this).hasClass( 'add_to_cart_button' ) ) {
					return;
				}
				" . $this->get_ec_add_product_js( $product_id ) . $this->get_ec_action_js( 'click', array( 'list' => $list ) ) . $this->get_event_tracking_js( $this->event_name['clicked_product'], $properties ) . "
			} );";

		$this->enqueue_js( 'event', $js );
	}


	/**
	 * Tracks the (non-ajax) add-to-cart event.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $cart_item_key the unique cart item ID
	 * @param int $product_id the product ID
	 * @param int $quantity the quantity added to the cart
	 * @param int $variation_id the variation ID
	 * @param array $variation the variation data
	 * @param array $cart_item_data the cart item data
	 */
	public function added_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

		// don't track add to cart from AJAX here
		if ( is_ajax() ) {
			return;
		}

		$product    = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
		$properties = [
			'eventCategory' => 'Products',
			'eventLabel'    => htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' ),
			'eventValue'    => (int) $quantity,
		];

		if ( ! empty( $variation ) ) {

			// added a variable product to cart:
			// - set attributes as properties
			// - remove 'pa_' from keys to keep property names consistent
			$variation  = array_flip( str_replace( 'attribute_', '', array_flip( $variation ) ) );
			$properties = array_merge( $properties, $variation );
		}

		$this->api_record_event(
			$this->event_name['added_to_cart'],
			$properties,
			[
				'add_to_cart' => [
					'product'       => $product,
					'quantity'      => $quantity,
					'cart_item_key' => $cart_item_key,
				],
			]
		);
	}


	/**
	 * Tracks the (ajax) add-to-cart event.
	 *
	 * @since 1.0.0
	 * @param int $product_id the product ID
	 */
	public function ajax_added_to_cart( $product_id ) {

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return;
		}

		$this->api_record_event(
			$this->event_name['added_to_cart'],
			$properties = [
				'eventCategory' => 'Products',
				'eventLabel'    => htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' ),
				'eventValue'    => 1,
			],
			[
				'add_to_cart' => [
					'product'  => $product,
					'quantity' => 1
				],
			]
		);
	}


	/**
	 * Tracks a product cart removal event.
	 *
	 * @since 1.0.0
	 * @param string $cart_item_key the unique cart item ID
	 */
	public function removed_from_cart( $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			$item    = WC()->cart->cart_contents[ $cart_item_key ];
			$product = ! empty( $item['variation_id'] ) ? wc_get_product( $item['variation_id'] ) : wc_get_product( $item['product_id'] );

			if ( ! $product ) {
				return;
			}

			$this->api_record_event(
				$this->event_name['removed_from_cart'],
				[
					'eventCategory' => 'Cart',
					'eventLabel'    => htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' ),
				],
				[
					'remove_from_cart' => [
						'product'   => $product,
						'cart_item' => $item,
					]
				]
			);
		}
	}


	/**
	 * Tracks the cart changed quantity event.
	 *
	 * @since 1.0.0
	 * @param string $cart_item_key the unique cart item ID
	 * @param int $quantity the changed quantity
	 */
	public function changed_cart_quantity( $cart_item_key, $quantity ) {;

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			$item    = WC()->cart->cart_contents[ $cart_item_key ];
			$product = wc_get_product( $item['product_id'] );

			$properties = array(
				'eventCategory' => 'Cart',
				'eventLabel'    => htmlentities( $product->get_title(), ENT_QUOTES, 'UTF-8' ),
			);

			$this->api_record_event( $this->event_name['changed_cart_quantity'], $properties );
		}
	}


	/**
	 * Tracks a cart page view.
	 *
	 * @since 1.0.0
	 */
	public function viewed_cart() {

		if ( $this->not_page_reload() ) {

			// enhanced Ecommerce tracking
			$js = '';

			foreach ( WC()->cart->get_cart() as $item ) {

				// JS add product
				$js .= $this->get_ec_add_product_js( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $item['quantity'] );
			}

			// enqueue JS
			$this->enqueue_js( 'event', $js );

			$properties = array(
				'eventCategory'  => 'Cart',
				'nonInteraction' => true,
			);

			$this->js_record_event( $this->event_name['viewed_cart'], $properties );
		}
	}


	/**
	 * Tracks the start of checkout.
	 *
	 * @since 1.0.0
	 */
	public function started_checkout() {

		// bail if tracking is disabled
		if ( $this->do_not_track() ) {
			return;
		}

		if ( $this->not_page_reload() ) {

			// enhanced Ecommerce tracking
			$js = '';

			foreach ( WC()->cart->get_cart() as $item ) {

				// JS add product
				$js .= $this->get_ec_add_product_js( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $item['quantity'] );
			}

			// JS checkout action
			$args = array(
				'step'   => 1,
				'option' => ( is_user_logged_in() ? __( 'Registered User', 'woocommerce-google-analytics-pro' ) : __( 'Guest', 'woocommerce-google-analytics-pro' ) ),
			);

			$js .= $this->get_ec_action_js( 'checkout', $args );

			// enqueue JS
			$this->enqueue_js( 'event', $js );

			// set event properties
			$properties = array(
				'eventCategory'  => 'Checkout',
				'nonInteraction' => true,
			);

			$this->js_record_event( $this->event_name['started_checkout'], $properties );
		}
	}


	/**
	 * Tracks when a customer provides a billing email on checkout.
	 *
	 * @since 1.3.0
	 */
	public function provided_billing_email() {

		// bail if tracking is disabled
		if ( $this->do_not_track() ) {
			return;
		}

		// set event properties
		$properties = array(
			'eventCategory' => 'Checkout',
		);

		// enhanced ecommerce tracking
		$handler_js = '';

		foreach ( WC()->cart->get_cart() as $item ) {

			// JS add product
			$handler_js .= $this->get_ec_add_product_js( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $item['quantity'] );
		}

		// JS checkout action
		$args = array( 'step' => 2 );

		$handler_js .= $this->get_ec_action_js( 'checkout', $args );

		// event
		$handler_js .= $this->get_event_tracking_js( $this->event_name['provided_billing_email'], $properties );

		$user_logged_in = is_user_logged_in();
		$billing_email  = $user_logged_in ? WC()->customer->get_billing_email() : '';

		// track the billing email only once for the logged in user, if they have one
		if ( $user_logged_in && is_email( $billing_email ) && $this->not_page_reload() ) {
			$js = sprintf( "if ( ! wc_ga_pro.payment_method_tracked ) { %s };", $handler_js );
		} elseif ( ! $user_logged_in ) {
			// track billing email once it's provided & valid
			$js = sprintf( "$( 'form.checkout' ).on( 'change', 'input#billing_email', function() { if ( ! wc_ga_pro.provided_billing_email && wc_ga_pro.is_valid_email( this.value ) ) { wc_ga_pro.provided_billing_email = true; %s } });", $handler_js );
		}

		if ( ! empty( $js ) ) {
			$this->enqueue_js( 'event', $js );
		}
	}


	/**
	 * Tracks payment method selection event on checkout.
	 *
	 * @since 1.3.0
	 */
	public function selected_payment_method() {

		// bail if tracking is disabled
		if ( $this->do_not_track() ) {
			return;
		}

		// set event properties
		$properties = array(
			'eventCategory' => 'Checkout',
			'eventLabel'    => '{$payment_method}',
		);

		// enhanced ecommerce tracking
		$handler_js = '';

		foreach ( WC()->cart->get_cart() as $item ) {

			// JS add product
			$handler_js .= $this->get_ec_add_product_js( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'], $item['quantity'] );
		}

		// JS checkout action
		$args = array( 'step' => 3, 'option' => '{$payment_method}' );

		$handler_js .= $this->get_ec_action_js( 'checkout', $args, 'args' );

		// event
		$handler_js .= $this->get_event_tracking_js( $this->event_name['selected_payment_method'], $properties, 'args' );

		$js = '';

		/**
		 * Filters whether the initial payment method selection should be ignored.
		 *
		 * WooCommerce automatically selects a payment method when the checkout page is loaded.
		 * Allow the tracking of this automatic selection to be enabled or disabled.
		 *
		 * @since 1.4.1
		 *
		 * @param bool $ignore_initial_payment_method_selection
		 */
		if ( true === apply_filters( 'wc_google_analytics_pro_ignore_initial_payment_method_selection', true ) ) {
			$js .= 'wc_ga_pro.selected_payment_method = $( "input[name=\'payment_method\']:checked" ).val();';
		}

		// listen to payment method selection event
		$js .= sprintf( "$( 'form.checkout' ).on( 'click', 'input[name=\"payment_method\"]', function( e ) { if ( wc_ga_pro.selected_payment_method !== this.value ) { var args = { payment_method: wc_ga_pro.get_payment_method_title( this.value ) }; wc_ga_pro.payment_method_tracked = true; %s wc_ga_pro.selected_payment_method = this.value; } });", $handler_js );

		// fall back to sending the payment method on checkout_place_order (clicked place order)
		$js .= sprintf( "$( 'form.checkout' ).on( 'checkout_place_order', function() { if ( ! wc_ga_pro.payment_method_tracked ) { var args = { payment_method: wc_ga_pro.get_payment_method_title( $( 'input[name=\"payment_method\"]' ).val() ) }; %s } });", $handler_js );


		$this->enqueue_js( 'event', $js );
	}


	/**
	 * Tracks "Place Order" event in checkout.
	 *
	 * @since 1.3.0
	 * @param int $order_id the order ID
	 */
	public function placed_order( $order_id ) {

		$order = wc_get_order( $order_id );

		$properties = array(
			'eventCategory'  => 'Checkout',
			'eventLabel'     => $order->get_order_number(),
			'nonInteraction' => true,
		);

		$ec = array( 'checkout' => array( 'order' => $order, 'step' => 4, 'option' => $order->get_shipping_method() ) );

		$this->api_record_event( $this->event_name['placed_order'], $properties, $ec );
	}


	/**
	 * Tracks the start of payment at checkout.
	 *
	 * @since 1.0.0
	 */
	public function started_payment() {

		if ( $this->not_page_reload() ) {

			$properties = array(
				'eventCategory'  => 'Checkout',
				'nonInteraction' => true,
			);

			$this->js_record_event( $this->event_name['started_payment'], $properties );
		}
	}


	/**
	 * Tracks when someone is commenting.
	 *
	 * This can be a regular comment or an product review.
	 *
	 * @since 1.0.0
	 */

	public function wrote_review_or_commented() {

		// separate comments from review tracking
		$type = get_post_type();

		if ( 'product' === $type ) {

			$properties = array(
				'eventCategory' => 'Products',
				'eventLabel'    => get_the_title(),
			);

			if ( $this->event_name['wrote_review'] ) {
				$this->api_record_event( $this->event_name['wrote_review'], $properties );
			}

		} elseif ( 'post' === $type ) {

			$properties = array(
				'eventCategory' => 'Post',
				'eventLabel'    => get_the_title(),
			);

			if ( $this->event_name['commented'] ) {
				$this->api_record_event( $this->event_name['commented'], $properties );
			}
		}
	}


	/**
	 * Tracks a completed purchase and records revenue/sales with GA.
	 *
	 * @since 1.0.0
	 * @param int $order_id the order ID
	 */
	public function completed_purchase( $order_id ) {

		/**
		 * Filters whether the completed purchase event should be tracked or not.
		 *
		 * @since 1.1.5
		 * @param bool $do_not_track true to not track the event, false otherwise
		 * @param int $order_id the order ID
		 */
		if ( true === apply_filters( 'wc_google_analytics_pro_do_not_track_completed_purchase', false, $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		// can't track an order that doesn't exist
		if ( ! $order || ! $order instanceof \WC_Order ) {
			return;
		}

		// only track orders with a 'paid' order status
		if ( ! $order->is_paid() ) {
			return;
		}

		// bail if tracking is disabled but not if the status is being manually changed by the admin
		if ( ! $this->is_tracking_enabled_for_user_role( $order->get_customer_id() ) ) {
			return;
		}

		// don't track order when its already tracked
		if ( 'yes' === get_post_meta( $order_id, '_wc_google_analytics_pro_tracked', true ) ) {
			return;
		}

		// don't track order when we haven't tracked the 'placed' event - this prevents tracking old orders that were placed before GA Pro was active
		if ( 'yes' !== get_post_meta( $order_id, '_wc_google_analytics_pro_placed', true ) ) {
			return;
		}

		/**
		 * Toggles whether to use cents or dollars for purchase events value.
		 *
		 * @since 1.5.2
		 *
		 * @param bool $use_cents whether to use cents (default, true) or dollars (false)
		 * @param string $event_name the event name
		 * @param \WC_Order $order related order object for the event
		 */
		$use_cents = (bool) apply_filters( 'wc_google_analytics_pro_purchase_event_use_cents', true, 'completed_purchase', $order );

		// record purchase event
		$properties = array(
			'eventCategory' => 'Checkout',
			'eventLabel'    => $order->get_order_number(),
			'eventValue'    => $use_cents ? round( $order->get_total() * 100 ) : floor( $order->get_total() ),
		);

		// set to non-interaction if this is a renewal order
		if ( class_exists( 'WC_Subscriptions_Renewal_Order' ) && ( wcs_order_contains_resubscribe( $order ) || wcs_order_contains_renewal( $order ) ) ) {
			$properties['nonInteraction'] = 1;
		}

		$ec = array( 'purchase' => array( 'order' => $order ) );

		$identities = $this->get_order_identities( $order );

		if ( $this->api_record_event( $this->event_name['completed_purchase'], $properties, $ec, $identities, true ) ) {

			// mark order as tracked
			update_post_meta( $order->get_id(), '_wc_google_analytics_pro_tracked', 'yes' );
		}
	}


	/**
	 * Checks 'On Hold' orders to see if we should record a completed transaction or not.
	 *
	 * Currently, the only reason we might want to do this is if Paypal returns On Hold
	 * from the IPN. This is usually due to an email address mismatch, and the payment has
	 * technically already been captured at this point.
	 *
	 * @see https://github.com/skyverge/wc-plugins/issues/2332
	 *
	 * @since 1.4.1
	 *
	 * @param int $order_id
	 */
	public function purchase_on_hold( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( 'paypal' === $order->get_payment_method() ) {
			$this->completed_purchase( $order_id );
		}
	}


	/**
	 * Tracks an account page view.
	 *
	 * @since 1.0.0
	 */
	public function viewed_account() {

		if ( $this->not_page_reload() ) {

			$properties = array(
				'eventCategory'  => 'My Account',
				'nonInteraction' => true,
			);

			$this->js_record_event( $this->event_name['viewed_account'], $properties );
		}
	}


	/**
	 * Tracks an order view.
	 *
	 * @since 1.0.0
	 * @param int $order_id the order ID
	 */
	public function viewed_order( $order_id ) {

		if ( $this->not_page_reload() ) {

			$order = wc_get_order( $order_id );

			$properties = array(
				'eventCategory'  => 'Orders',
				'eventLabel'     => $order->get_order_number(),
				'nonInteraction' => true,
			);

			$this->api_record_event( $this->event_name['viewed_order'], $properties );
		}
	}


	/**
	 * Tracks the updated address event.
	 *
	 * @since 1.0.0
	 */
	public function updated_address() {

		if ( $this->not_page_reload() ) {

			$properties = array(
				'eventCategory' => 'My Account',
			);

			$this->api_record_event( $this->event_name['updated_address'], $properties );
		}
	}


	/**
	 * Tracks the changed password event.
	 *
	 * @since 1.0.0
	 */
	public function changed_password() {

		if ( ! empty( $_POST['password_1'] ) && $this->not_page_reload() ) {

			$properties = array(
				'eventCategory' => 'My Account',
			);

			$this->api_record_event( $this->event_name['changed_password'], $properties );
		}
	}


	/**
	 * Tracks the apply coupon event.
	 *
	 * @since 1.0.0
	 * @param string $coupon_code the coupon code that is being applied
	 */
	public function applied_coupon( $coupon_code ) {

		$properties = array(
			'eventCategory' => 'Coupons',
			'eventLabel'    => $coupon_code,
		);

		$this->api_record_event( $this->event_name['applied_coupon'], $properties );
	}


	/**
	 * Tracks the coupon removal event.
	 *
	 * @since 1.0.0
	 */
	public function removed_coupon() {

		if ( $this->not_page_reload() ) {

			$properties = array(
				'eventCategory' => 'Coupons',
				'eventLabel'    => $_GET['remove_coupon'],
			);

			$this->api_record_event( $this->event_name['removed_coupon'], $properties );
		}
	}


	/**
	 * Tracks the 'track order' event.
	 *
	 * @since 1.0.0
	 * @param int $order_id ID of the order being tracked.
	 */
	public function tracked_order( $order_id ) {

		if ( $this->not_page_reload() ) {

			$order = wc_get_order( $order_id );

			$properties = array(
				'eventCategory' => 'Orders',
				'eventLabel'    => $order->get_order_number(),
			);

			$this->api_record_event( $this->event_name['tracked_order'], $properties );
		}
	}


	/**
	 * Tracks the "calculate shipping" event.
	 *
	 * @since 1.0.0
	 */
	public function estimated_shipping() {

		$properties = array(
			'eventCategory' => 'Cart',
		);

		$this->api_record_event( $this->event_name['estimated_shipping'], $properties );
	}


	/**
	 * Tracks when an order is cancelled.
	 *
	 * @since 1.0.0
	 * @param int $order_id the order ID
	 */
	public function cancelled_order( $order_id ) {

		$order = wc_get_order( $order_id );

		$properties = array(
			'eventCategory' => 'Orders',
			'eventLabel'    => $order->get_order_number(),
		);

		$this->api_record_event( $this->event_name['cancelled_order'], $properties );
	}


	/**
	 * Tracks when an order is refunded.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id the order ID
	 * @param int $refund_id the refund ID
	 */
	public function order_refunded( $order_id, $refund_id ) {

		// don't track if the refund is already tracked
		if ( 'yes' === get_post_meta( $refund_id, '_wc_google_analytics_pro_tracked' ) ) {
			return;
		}

		$order          = wc_get_order( $order_id );
		$refund         = wc_get_order( $refund_id );
		$refunded_items = array();

		// TODO partial refunds should work, as per https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#measuring-refunds,
		// however, I could not get them to work - GA would simply not record a refund if any products (items) were set with the `refund` product action.
		// Disabled until we can figure out a solution. If you read this and can fix it, please apply for a position at info@skyverge.com {IT 2017-05-02}

		// get refunded items
		// $items = $refund->get_items();

		// if ( ! empty( $items ) ) {

		// 	foreach ( $items as $item_id => $item ) {

		// 		// any item with a quantity and line total is refunded
		// 		if ( abs( $item['qty'] ) >= 1 && abs( $refund->get_line_total( $item ) ) >= 0 ) {
		// 			$refunded_items[ $item_id ] = $item;
		// 		}
		// 	}
		// }

		/* this filter is documented in class-wc-google-analytics-pro-integration.php */
		$use_cents = (bool) apply_filters( 'wc_google_analytics_pro_purchase_event_use_cents', true, 'order_refunded', $refund );

		$refund_amount = $refund->get_amount();
		$properties    = [
			'eventCategory' => 'Orders',
			'eventLabel'    => $order->get_order_number(),
			'eventValue'    => $use_cents ? round( $refund_amount * 100 ) : floor( $refund_amount ),
		];

		// Enhanced Ecommerce can only track full refunds and refunds for specific items
		if ( doing_action( 'woocommerce_order_fully_refunded' ) || ! empty( $refunded_items ) ) {
			$ec = array( 'refund' => array( 'order' => $order, 'refunded_items' => $refunded_items ) );
		} else {
			$ec = null;
		}

		$identities = $this->get_order_identities( $order );

		if ( $this->api_record_event( $this->event_name['order_refunded'], $properties, $ec, $identities, true ) ) {

			// mark refund as tracked
			update_post_meta( $refund_id, '_wc_google_analytics_pro_tracked', 'yes' );
		}
	}


	/**
	 * Tracks when someone uses the "Order Again" button.
	 *
	 * @since 1.0.0
	 */
	public function reordered( $order_id ) {

		if ( $this->not_page_reload() ) {

			$order = wc_get_order( $order_id );

			$properties = array(
				'eventCategory' => 'Orders',
				'eventLabel'    => $order->get_order_number(),
			);

			$this->api_record_event( $this->event_name['reordered'], $properties );
		}
	}


	/** Enhanced e-commerce specific methods **********************/


	/**
	 * Tracks a product impression.
	 *
	 * An impression is the listing of a product anywhere on the website, e.g.
	 * search/archive/category/related/cross sell.
	 *
	 * @since 1.0.0
	 */
	public function product_impression() {

		if ( $this->do_not_track() ) {
			return;
		}

		// MonsterInsights is in non-universal mode, skip
		if ( $this->is_monsterinsights_tracking_active() && ! $this->is_monsterinsights_tracking_universal() ) {
			return;
		}

		$track_on = $this->get_option( 'track_product_impressions_on', array() );

		// bail if product impression tracking is disabled on product pages and we're on a prdouct page
		// note: this doesn't account for the [product_page] shortcode unfortunately
		if ( ! in_array( 'single_product_pages', $track_on, true ) && is_product() ) {
			return;
		}

		// bail if product impression tracking is disabled on product archive pages and we're on an archive page
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
			'id'       => $this->get_product_identifier( $product ),
			'name'     => $product->get_title(),
			'list'     => $this->get_list_type(),
			'brand'    => '',
			'category' => $this->get_category_hierarchy( $product ),
			'variant'  => $this->get_product_variation_attributes( $product ),
			'position' => isset( $woocommerce_loop['loop'] ) ? $woocommerce_loop['loop'] : 1,
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
	 * Tracks a custom event.
	 *
	 * Contains excess checks to account for any kind of user input.
	 *
	 * @since 1.0.0
	 * @param string $event_name the event name
	 * @param array $properties Optional. The event properties
	 */
	public function custom_event( $event_name = false, $properties = false ) {

		if ( isset( $event_name ) && $event_name != '' && strlen( $event_name ) > 0 ) {

			// sanitize property names and values
			$prop_array = false;
			$props      = false;

			if ( isset( $properties ) && is_array( $properties ) && count( $properties ) > 0 ) {

				foreach ( $properties as $k => $v ) {

					$key   = $this->sanitize_event_string( $k );
					$value = $this->sanitize_event_string( $v );

					if ( $key && $value ) {
						$prop_array[$key] = $value;
					}
				}

				$props = false;

				if ( $prop_array && is_array( $prop_array ) && count( $prop_array ) > 0 ) {
					$props = $prop_array;
				}
			}

			// sanitize event name
			$event = $this->sanitize_event_string( $event_name );

			// if everything checks out then trigger event
			if ( $event ) {
				$this->api_record_event( $event, $props );
			}
		}
	}


	/**
	 * Sanitizes a custom event string.
	 *
	 * Contains excess checks to account for any kind of user input.
	 *
	 * @since 1.0.0
	 * @param string $str
	 * @return string|bool the santitized string or false on failure
	 */
	private function sanitize_event_string( $str = false ) {

		if ( isset( $str ) ) {

			// remove excess spaces
			$str = trim( $str );

			return $str;
		}

		return false;
	}


	/**
	 * Stores the GA Identity (CID) on an order.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id the order ID
	 * @param string|null $cid optional client identity to use, otherwise it will be generated
	 * @return string|null the set client identity on success or null on failure
	 */
	public function store_ga_identity( $order_id, $cid = null ) {

		if ( null === $cid ) {

			// get CID - ensuring that order will always have some kind of client id, so that
			// the transactions are properly tracked and reported in GA
			$cid = $this->get_cid( true );
		}

		// store CID in order meta if it is not empty
		if ( ! empty( $cid ) && is_string( $cid ) ) {

			$cid = trim( $cid );

			update_post_meta( $order_id, '_wc_google_analytics_pro_identity', $cid );
		}

		return ! is_string( $cid ) || '' === $cid ? null : $cid;
	}


	/**
	 * Gets the GA Identity associated with an order.
	 *
	 * @since 1.0.0
	 * @param int $order_id the order ID
	 * @return string
	 */
	public function get_order_ga_identity( $order_id ) {

		return get_post_meta( $order_id, '_wc_google_analytics_pro_identity', true );
	}


	/**
	 * Adds a meta to mark new orders as placed.
	 *
	 * The meta `_wc_google_analytics_pro_placed` helps prevent tracking completed orders that were placed before GA Pro was enabled.
	 *
	 * @since 1.8.8
	 *
	 * @param int $order_id the order object
	 */
	public function add_order_placed_meta( $order_id ) {

		update_post_meta( $order_id, '_wc_google_analytics_pro_placed', 'yes' );
	}


	/**
	 * Gets the identities associated with a given order in the format useful for submission to Google Analytics.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Order $order
	 * @return array
	 */
	public function get_order_identities( $order ) {

		$cid = $this->get_order_ga_identity( $order->get_id() );

		return array(
			'cid' => $cid ?: $this->get_cid(),
			'uid' => $order->get_customer_id( 'edit' ),
			'uip' => $order->get_customer_ip_address(),
			'ua'  => $order->get_customer_user_agent(),
		);
	}


	/**
	 * Authenticates with Google API.
	 *
	 * @since 1.0.0
	 */
	public function authenticate() {

		// missing token
		if ( ! isset( $_REQUEST['token'] ) || ! $_REQUEST['token'] ) {
			return;
		}

		$json_token = base64_decode( $_REQUEST['token'] );
		$token      = json_decode( $json_token, true );

		// invalid token
		if ( ! $token ) {
			return;
		}

		// update access token
		update_option( 'wc_google_analytics_pro_access_token', $json_token );
		update_option( 'wc_google_analytics_pro_account_id', md5( $json_token ) );
		delete_transient( 'wc_google_analytics_pro_properties' );

		// update refresh token
		if ( isset( $token['refresh_token'] ) ) {
			update_option( 'wc_google_analytics_pro_refresh_token', $token['refresh_token'] );
		}

		echo '<script>window.opener.wc_google_analytics_pro.auth_callback(' . $json_token . ');</script>';
		exit();
	}


	/**
	 * Gets the current access token.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function get_access_token() {

		return get_option( 'wc_google_analytics_pro_access_token', null );
	}


	/**
	 * Parses access token data for internal use.
	 *
	 * @since 1.7.0
	 *
	 * @param string $json_token raw token data
	 * @return \stdClass
	 */
	private function parse_access_token( $json_token = '' ) {

		$token = [
			'access_token' => '',
			'expires_in'   => 0,
			'created'      => current_time( 'timestamp', true ),
		];

		if ( is_string( $json_token ) && '' !== $json_token ) {
			$token = wp_parse_args( (array) json_decode( $json_token ), $token );
		}

		return (object) $token;
	}


	/**
	 * Determines whether the current access token is expired or not.
	 *
	 * @since 1.7.0
	 *
	 * @param \stdClass $token access token object
	 * @return bool
	 */
	private function is_access_token_expired( $token ) {

		$expired = ! ( is_object( $token ) && $token->created && $token->expires_in );

		if ( ! $expired ) {
			$time_now     = current_time( 'timestamp', true );
			$time_expires = max( 0, (int) $token->created + (int) $token->expires_in );
			$expired      = $time_expires <= $time_now;
		}

		return $expired;
	}


	/**
	 * Refreshes the access token.
	 *
	 * @since 1.0.0
	 *
	 * @return \stdClass|null token object if successful
	 * @throws Framework\SV_WC_API_Exception
	 */
	private function refresh_access_token() {

		if ( ! $this->get_refresh_token() ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: refresh token not available.' );
		}

		$refresh_url = $this->get_access_token_refresh_url();
		$response    = wp_remote_get( $refresh_url, array( 'timeout' => MINUTE_IN_SECONDS ) );

		// bail out if the request failed
		if ( $response instanceof \WP_Error ) {
			throw new Framework\SV_WC_API_Exception( sprintf( 'Could not refresh access token: %s', json_encode( $response->errors ) ) );
		}

		// bail out if the response was empty
		if ( ! $response || empty( $response['body'] ) ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: response was empty.' );
		}

		// bail out if the Google Analytics proxy produced a 500 server error
		if ( isset( $response['response']['code'] ) && 500 === (int) $response['response']['code'] ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: a server error occurred.' );
		}

		// try to decode the token
		$json_token = base64_decode( $response['body'] );

		// bail out if the token was invalid
		if ( ! json_decode( $json_token, true ) ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: returned token was invalid.' );
		}

		// we're good: update the access token
		$updated = update_option( 'wc_google_analytics_pro_access_token', $json_token );

		// there's a rare possibility we could not store the token
		if ( ! $updated ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: a database error occurred.' );
		}

		return $this->parse_access_token( $json_token );
	}


	/**
	 * Generates the "deep select" field HTML.
	 *
	 * @since 1.0.0
	 * @param string $key the setting key
	 * @param array $data the setting data
	 * @return string the field HTML
	 */
	public function generate_deep_select_html( $key, $data ) {

		$field    = $this->get_field_key( $key );
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<select class="select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>

						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>

							<?php if ( is_array( $option_value ) ) : ?>

								<optgroup label="<?php echo esc_attr( $option_key ); ?>">

								<?php foreach ( $option_value as $option_sub_key => $option_sub_value ) : ?>

									<option value="<?php echo esc_attr( $option_sub_key ); ?>" <?php selected( $option_sub_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_attr( $option_sub_value ); ?></option>

								<?php endforeach; ?>

							<?php else : ?>

								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_attr( $option_value ); ?></option>

							<?php endif; ?>

						<?php endforeach; ?>

					</select>

					<?php echo $this->get_description_html( $data ); ?>

				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Gets a list of Google Analytics properties.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_ga_properties() {

		$ga_properties = [];

		// skip when not on the plugin settings page
		if ( $this->get_plugin()->is_plugin_settings() ) {

			$ga_properties = get_transient( 'wc_google_analytics_pro_properties' );

			if ( ! is_array( $ga_properties ) ) {

				$account_api = $this->get_management_api();

				// try to fetch analytics accounts
				try {

					// give ourselves an unlimited timeout if possible
					@set_time_limit( 0 );

					// get the account summaries in one API call
					$account_summaries = $account_api->get_account_summaries();
					$list_summaries    = $account_summaries->list_account_summaries();

					// loop over the account summaries to get available web properties
					foreach ( $list_summaries as $account_summary ) {

						// sanity checks to ensure we have the right kind of data
						if ( ! isset( $account_summary->kind, $account_summary->id, $account_summary->name, $account_summary->webProperties ) ) {
							continue;
						}
						if ( 'analytics#accountSummary' !== $account_summary->kind ) {
							continue;
						}

						// loop over the properties to create property options
						foreach ( $account_summary->webProperties as $property ) {

							// sanity checks to ensure we have the right kind of data
							if ( ! isset( $property->kind, $property->id, $property->name ) ) {
								continue;
							}
							if ( 'analytics#webPropertySummary' !== $property->kind ) {
								continue;
							}

							$optgroup = $account_summary->name;

							if ( ! isset( $ga_properties[ $optgroup ] ) ) {
								$ga_properties[ $optgroup ] = [];
							}

							$ga_properties[ $optgroup ][ $account_summary->id . '|' . $property->id ] = sprintf( '%s (%s)', $property->name, $property->id );

							// sort properties naturally
							natcasesort( $ga_properties[ $optgroup ] );
						}
					}

				// if something goes wrong we should inform the user...
				} catch ( Framework\SV_WC_API_Exception $e ) {

					// log the error
					$this->get_plugin()->log( $e->getMessage() );

					// leave an additional admin notice
					if ( is_admin() ) {

						$error_code    = (int) $e->getCode();
						$plugin_name   = '<strong>' . $this->get_plugin()->get_plugin_name() . '</strong> ';
						$notice_id     = $this->get_plugin()->get_id() . '-account-' . get_option( 'wc_google_analytics_pro_account_id', '' ) . '-no-analytics-access';
						$notice_params = [
							'dismissible'             => true,
							'always_show_on_settings' => false,
							'notice_class'            => 'error'
						];

						// authentication error (normally 401)
						if ( in_array( $error_code, [ 401, 403, 407 ], true ) ) {

							$this->get_plugin()->get_admin_notice_handler()->add_admin_notice(
								/* translators: Placeholder: %s - plugin name, in bold */
								sprintf( esc_html__( '%s: The currently authenticated Google account does not have access to any Analytics accounts. Please re-authenticate with an account that has access to Google Analytics.', 'woocommerce-google-analytics-pro' ), $plugin_name ),
								$notice_id,
								$notice_params
							);

						// possibly a timeout, or other issue
						} else {

							$this->get_plugin()->get_admin_notice_handler()->add_admin_notice(
								/* translators: Placeholder: %s - plugin name, in bold */
								sprintf( esc_html__( '%s: Something went wrong with the request to list the Google Analytics properties for the currently authenticated Google account. Please try again in a few minutes or try re-authenticating with your Google account.', 'woocommerce-google-analytics-pro' ), $plugin_name ),
								$notice_id,
								$notice_params
							);
						}
					}

					// just in case ensure the array is empty in case of errors
					$ga_properties = [];
				}

				if ( is_array( $ga_properties ) ) {
					// sort properties in the United Kingdom... just kidding, sort by keys, by comparing them naturally
					uksort( $ga_properties, 'strnatcasecmp' );
				}

				// set a 5 minute transient
				set_transient( 'wc_google_analytics_pro_properties', $ga_properties, 5 * MINUTE_IN_SECONDS );
			}
		}

		return $ga_properties;
	}


	/**
	 * Bypasses validation for the oAuth button value.
	 *
	 * @see \WC_Settings_API::get_field_value()
	 *
	 * @since 1.1.6
	 * @return string the button default value
	 */
	protected function validate_oauth_button_field() {

		$form_fields = $this->get_form_fields();

		return ! empty( $form_fields[ 'oauth_button' ]['default'] ) ? $form_fields[ 'oauth_button' ]['default'] : '';
	}


	/**
	 * Filters the admin options before saving.
	 *
	 * @since 1.0.0
	 * @param array $sanitized_fields
	 * @return array
	 */
	public function filter_admin_options( $sanitized_fields ) {

		// prevent button labels from being saved
		unset( $sanitized_fields['oauth_button'] );

		// unset web property if manual tracking is being used
		if ( isset( $sanitized_fields['use_manual_tracking_id'] ) && 'yes' === $sanitized_fields['use_manual_tracking_id'] ) {
			$sanitized_fields['property'] = '';
		}

		// get tracking ID from web property, if using oAuth, and save it to the tracking ID option
		elseif ( ! empty( $sanitized_fields['property'] ) ) {

			$parts = explode( '|', $sanitized_fields['property'] );
			$sanitized_fields['tracking_id'] = $parts[1];
		}

		// manual tracking ID not configured, and no property selected. Remove tracking ID.
		else {
			$sanitized_fields['tracking_id'] = '';
		}

		return $sanitized_fields;
	}


	/**
	 * Returns the currently selected Google Analytics Account ID.
	 *
	 * @since 1.0.0
	 * @return int|null
	 */
	public function get_ga_account_id() {

		return $this->get_ga_property_part( 0 );
	}


	/**
	 * Returns the currently selected Google Analytics property ID.
	 *
	 * @since 1.0.0
	 * @return int|null
	 */
	public function get_ga_property_id() {

		return $this->get_ga_property_part( 1 );
	}


	/**
	 * Returns the given part from the property option.
	 *
	 * In 1.3.0 renamed from get_ga_property_part() to get_ga_property_part()
	 *
	 * @since 1.0.0
	 * @param int $key the array key
	 * @return mixed|null
	 */
	private function get_ga_property_part( $key ) {

		$property = $this->get_option( 'property' );

		if ( ! $property ) {
			return;
		}

		$pieces = explode( '|', $property );

		if ( ! isset( $pieces[ $key ] ) ) {
			return;
		}

		return $pieces[ $key ];
	}


}
