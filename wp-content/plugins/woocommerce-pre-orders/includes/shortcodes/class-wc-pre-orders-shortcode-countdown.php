<?php
/**
 * Countdown Pre-Orders
 *
 * @package     WC_Pre_Orders/Shortcodes
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Countdown Shortcode
 *
 * Displays a JavaScript-enabled countdown timer
 *
 * @since 1.0
 */
class WC_Pre_Orders_Shortcode_Countdown {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public static function get( $atts ) {
		global $woocommerce;
		return $woocommerce->shortcode_wrapper( array( __CLASS__, 'output' ), $atts, array( 'class' => 'woocommerce-pre-orders' ) );
	}


	/**
	 * Output the countdown timer.  This defaults to the following format, where
	 * elments in [ ] are not shown if zero:
	 *
	 * [y Years] [o Months] [d Days] h Hours m Minutes s Seconds
	 *
	 * The following shortcode arguments are optional:
	 *
	 * * product_id/product_sku - id or sku of pre-order product to countdown to.
	 *     Defaults to current product, if any
	 * * until - date/time to count down to, overrides product release date
	 *     if set.  Example values: "15 March 2015", "+1 month".
	 *     More examples: http://php.net/manual/en/function.strtotime.php
	 * * before - text to show before the countdown.  Only available if 'layout' is not ''
	 * * after - text to show after the countdown.  Only available if 'layout' is not ''
	 * * layout - The countdown layout, defaults to y Years o Months d Days h Hours m Minutes s Seconds
	 *     See http://keith-wood.name/countdownRef.html#layout for all possible options
	 * * format - The format for the countdown display.  Example: 'yodhms'
	 *     to display the year, month, day and time.  See http://keith-wood.name/countdownRef.html#format for all options
	 * * compact - If 'true' displays the date/time labels in compact form, ie
	 *     'd' rather than 'days'.  Defaults to 'false'
	 *
	 * When the countdown date/time is reached the page will refresh.
	 *
	 * To test different time periods you can create shortcodes like the following samples:
	 *
	 * [woocommerce_pre_order_countdown until="+10 year"]
	 * [woocommerce_pre_order_countdown until="+10 month"]
	 * [woocommerce_pre_order_countdown until="+10 day"]
	 * [woocommerce_pre_order_countdown until="+10 second"]
	 *
	 * @param array $atts associative array of shortcode parameters
	 */
	public static function output( $atts ) {
		global $woocommerce, $product, $wpdb;

		extract( shortcode_atts( array(
			'product_id'  => '',
			'product_sku' => '',
			'until'       => '',
			'before'      => '',
			'after'       => '',
			'layout'      => '{y<}{yn} {yl}{y>} {o<}{on} {ol}{o>} {d<}{dn} {dl}{d>} {h<}{hn} {hl}{h>} {m<}{mn} {ml}{m>} {s<}{sn} {sl}{s>}',
			'format'      => 'yodHMS',
			'compact'     => 'false',
		), $atts ) );

		// product by sku?
		if ( $product_sku )
			$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_sku' AND meta_value=%s LIMIT 1", $product_sku ) );

		// product by id?
		if ( $product_id )
			$product = wc_get_product( $product_id );

		// date override (convert from string unless someone was savvy enough to provide a timestamp)
		if ( $until && ! is_numeric( $until ) )
			$until = strtotime( $until );

		// product and no date override, get the datetime from the product, if there is one
		if ( $product && ! $until ) {
			$until = get_post_meta( $product->get_id(), '_wc_pre_orders_availability_datetime', true );
		}

		// can't do anything without an 'until' date
		if ( ! $until ) return;

		// if a layout is being used, prepend/append the before/after text
		if ( $layout )
			$layout = esc_js( $before ) . $layout . esc_js( $after );

		// enqueue the required javascripts
		self::enqueue_scripts();

		// countdown javascript
		ob_start();
		?>
		$('#woocommerce-pre-orders-countdown-<?php echo $until; ?>').countdown({
		  until: new Date(<?php echo $until * 1000; ?>),
		  layout: '<?php echo $layout; ?>',
		  format: '<?php echo $format; ?>',
		  compact: <?php echo $compact; ?>,
		  expiryUrl: location.href,
		});
		<?php
		$javascript = ob_get_clean();
		if ( function_exists( 'wc_enqueue_js' ) ) {
			wc_enqueue_js( $javascript );
		} else {
			$woocommerce->add_inline_js( $javascript );
		}

		// the countdown element with a unique identifier to allow multiple countdowns on the same page, and common class for ease of styling
		echo '<div class="woocommerce-pre-orders-countdown" id="woocommerce-pre-orders-countdown-' . esc_attr( $until ) . '"></div>';
	}


	/**
	 * Enqueue required JavaScripts:
	 * * jquery.countdown.js - Main countdown script
	 * * jquery.countdown-{language}.js - Localized countdown script based on WPLANG, and if available
	 */
	private static function enqueue_scripts() {
		global $wc_pre_orders;

		// required library files
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// enqueue the main countdown script
		wp_enqueue_script( 'jquery-countdown', $wc_pre_orders->get_plugin_url() . '/assets/js/jquery.countdown/jquery.countdown' . $suffix . '.js', array( 'jquery' ), '1.6.1' );

		if ( defined('WPLANG') && WPLANG ) {
			// countdown includes some localization files, in the form: jquery.countdown-es.js and jquery.countdown-pt-BR.js
			//  convert our WPLANG constant to that format and see whether we have a localization file to include
			@list( $lang, $dialect ) = explode( '_', WPLANG );
			if ( 0 === strcasecmp( $lang, $dialect ) ) $dialect = null;
			$localization = $lang;
			if ( $dialect ) $localization .= '-' . $dialect;

			if ( ! is_readable( $wc_pre_orders->get_plugin_path() . '/assets/js/jquery.countdown/jquery.countdown-' . $localization . '.js' ) ) {
				$localization = $lang;
				if ( ! is_readable( $wc_pre_orders->get_plugin_path() . '/assets/js/jquery.countdown/jquery.countdown-' . $localization . '.js' ) )  // try falling back to base language if dialect is not found
					$localization = null;
			}

			if ( $localization )
				wp_enqueue_script( 'jquery-countdown-' . $localization, $wc_pre_orders->get_plugin_url() . '/assets/js/jquery.countdown/jquery.countdown-' . $localization . $suffix . '.js', array( 'jquery-countdown' ), '1.6.1' );
		}
	}
}
