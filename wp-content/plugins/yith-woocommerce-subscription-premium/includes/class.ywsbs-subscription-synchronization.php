<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Synchronization Object.
 *
 * @class   YWSBS_Subscription_Synchronization
 * @package YITH WooCommerce Subscription
 * @since   2.1.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'YWSBS_Subscription_Synchronization' ) ) {

	/**
	 * Class YWSBS_Subscription_Synchronization
	 */
	class YWSBS_Subscription_Synchronization {


		/**
		 * Single instance of the class
		 *
		 * @var YWSBS_Subscription_Synchronization
		 */
		protected static $instance;


		/**
		 * Time of the day when the synchronization should be scheduled.
		 * Usually when the site has lower traffic.
		 *
		 * @var int
		 */
		protected $time_of_day = 0;


		/**
		 * Returns single instance of the class
		 *
		 * @return YWSBS_Subscription_Synchronization
		 * @since  2.1.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Check if the product can be synchronizable.
		 *
		 * @param WC_Product $product Product.
		 * @param bool       $deep Check if can bd sync today.
		 *
		 * @return bool
		 */
		public function is_synchronizable( $product, $deep = false ) {
			$enabled_sync = get_option( 'ywsbs_enable_sync', 'no' );
			$result       = false;
			switch ( $enabled_sync ) {
				case 'no':
					$result = false;
					break;
				case 'all_products':
					$has_exclusion = get_option( 'ywsbs_sync_exclude_category_and_product', 'yes' );
					if ( 'yes' === $has_exclusion ) {
						$excluded_products = (array) get_option( 'ywsbs_sync_exclude_products_all_products', array() );
						$result            = ! in_array( $product->get_id(), $excluded_products ); //phpcs:ignore

						if ( $result ) {
							$excluded_categories = (array) get_option( 'ywsbs_sync_exclude_categories_all_products', array() );
							$result              = ! ywsbs_check_categories( $product, $excluded_categories );
						}
					} else {
						$result = true;
					}

					break;
				case 'virtual':
					$result = $product->is_virtual();

					if ( $result && 'yes' === get_option( 'ywsbs_sync_exclude_category_and_product_virtual', 'yes' ) ) {
						$excluded_products = (array) get_option( 'ywsbs_sync_exclude_products_virtual', array() );
						$result            = ! in_array( $product->get_id(), $excluded_products ); //phpcs:ignore

						if ( $result ) {
							$excluded_categories = (array) get_option( 'ywsbs_sync_exclude_categories_virtual', array() );
							$result              = ! ywsbs_check_categories( $product, $excluded_categories );
						}
					}

					break;
				case 'products':
					$included = (array) get_option( 'ywsbs_sync_include_product', array() );
					$result   = in_array( $product->get_id(), $included ); //phpcs:ignore
					break;
				case 'categories':
					$categories       = (array) get_option( 'ywsbs_sync_include_categories', array() );
					$result           = ywsbs_check_categories( $product, $categories );
					$exclude_products = get_option( 'ywsbs_sync_include_categories_enable_exclude_products', 'no' );
					if ( $result && 'yes' === $exclude_products ) {
						$excluded_products = (array) get_option( 'ywsbs_sync_exclude_products_from_categories', array() );
						$result            = ! in_array( $product->get_id(), $excluded_products ); //phpcs:ignore
					}
			}

			if ( $deep && $result ) {
				$next_payment_due_date = YWSBS_Subscription_Helper::get_billing_payment_due_date( $product );
				$next_payment_due_date = $this->get_next_payment_due_date_sync( $next_payment_due_date, $product );
				$today                 = new DateTime();
				if ( $today->format( 'Y-m-d' ) === date( 'Y-m-d', $next_payment_due_date ) ) { //phpcs:ignore
					$result = false;
				}
			}

			return apply_filters( 'ywsbs_is_synchronizable', $result, $product, $deep );
		}


		/**
		 * Constructor
		 *
		 * Initialize the YWSBS_Subscription_Synchronization Object
		 *
		 * @since 2.1.0
		 */
		public function __construct() {

			$this->time_of_day = apply_filters( 'ywsbs_synchronization_time_of_day', 2 );

			add_filter( 'ywsbs_subscription_meta_on_cart', array( $this, 'synchronize_next_payment_due_date' ), 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'set_synch_changes_on_cart' ), 100 );

			add_filter( 'yith_wcstripe_plan_trial_period', array( $this, 'yith_wcstripe_plan_trial_period' ), 10, 4 );
		}

		/**
		 * Return the number of trial days.
		 *
		 * @param int                $trial Num of trial days.
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return int
		 */
		public function yith_wcstripe_plan_trial_period( int $trial, YWSBS_Subscription $subscription ): int {
			if ( $trial && 'backend' === $subscription->get( 'created_via' ) ) {
				$trial = ( (int) $subscription->get( 'payment_due_date' ) - time() ) / DAY_IN_SECONDS;
			}

			return (int) $trial;
		}

		/**
		 * Filter the subscription cart meta information.
		 *
		 * @param array      $subscription_cart_meta Cart item subscription info.
		 * @param WC_Product $product Product.
		 *
		 * @return array
		 */
		public function synchronize_next_payment_due_date( $subscription_cart_meta, $product ) {
			if ( ! $this->is_synchronizable( $product, true ) ) {
				return $subscription_cart_meta;
			}

			$subscription_cart_meta['next_payment_due_date'] = $this->get_next_payment_due_date_sync( $subscription_cart_meta['next_payment_due_date'], $product );

			return $subscription_cart_meta;
		}

		/**
		 * Change the product price at first payment due to synchronization.
		 *
		 * @param float      $price Recurring price.
		 * @param WC_Product $product Product.
		 * @param int        $next_payment_due_date Next payment due date.
		 */
		public function get_new_price_sync( $price, $product, $next_payment_due_date ) {

			$prorate_option = get_option( 'ywsbs_sync_first_payment', 'no' );
			$is_trial       = (int) ywsbs_get_product_trial( $product );

			if ( 'no' === $prorate_option || $is_trial > 0 ) {
				return 0;
			}

			if ( 'full' === $prorate_option ) {
				return $price;
			}

			$prorate_disabled = apply_filters( 'ywsbs_sync_prorate_disabled_days', get_option( 'ywsbs_sync_prorate_disabled', array( 'number_of_days' => 30 ) ), $product, $price );

			$daily_price  = ywsbs_get_daily_amount_of_a_product( $product );
			$diff_in_days = ceil( ( (int) $next_payment_due_date - time() ) / DAY_IN_SECONDS );

			$price = ( $diff_in_days < $prorate_disabled['number_of_days'] ) ? 0 : ( $diff_in_days * $daily_price );

			return $price;

		}

		/**
		 * Get next payment due date synchronized.
		 *
		 * @param int        $next_payment_due_date Next payment due date timestamp.
		 * @param WC_Product $product Product.
		 *
		 * @return int
		 */
		public function get_next_payment_due_date_sync( $next_payment_due_date, $product ) {

			$period = $product->get_meta( '_ywsbs_price_time_option' );

			if ( ! in_array( $period, array( 'weeks', 'months', 'years' ), true ) ) {
				return $next_payment_due_date;
			}

			$sync_info_meta = $product->get_meta( '_ywsbs_synchronize_info' );

			$sync_info = isset( $sync_info_meta[ $period ] ) ? $sync_info_meta[ $period ] : $this->get_default_sync_info( $period );

			if ( false !== $sync_info ) {
				$caller                = 'get_next_payment_date_for_' . $period;
				$next_payment_due_date = $this->$caller( $sync_info, $product );
			}

			return $next_payment_due_date;
		}

		/**
		 * Return the next payment due date calculated to synchronize weekly periods.
		 *
		 * @param mixed      $sync_info Synchronization info.
		 * @param WC_Product $product Product.
		 *
		 * @return int
		 */
		public function get_next_payment_date_for_weeks( $sync_info, $product ) {
			$sync_info = (int) $sync_info;

			$new_date = $this->get_start_calculation_date( $product );
			$today    = new DateTime();
			if ( ywsbs_get_week_day_string( $sync_info ) === strtolower( $today->format( 'l' ) ) ) {
				return $new_date->getTimestamp();
			}
			$new_date->modify( 'next ' . ywsbs_get_week_day_string( $sync_info ) );
			$new_date->setTime( $this->time_of_day, 0, 0 );

			return $new_date->getTimestamp();
		}


		/**
		 * Return the next payment due date calculated to synchronize monthly periods.
		 *
		 * @param mixed      $sync_info Synchronization info can be a number from 1 - 28 or 'end'.
		 * @param WC_Product $product Product.
		 *
		 * @return int
		 */
		public function get_next_payment_date_for_months( $sync_info, $product ) {
			$new_date = $this->get_start_calculation_date( $product );

			if ( 'end' === $sync_info ) {
				$new_date->modify( 'last day of this month' );
			} else {
				$sync_info = (int) $sync_info;
				if ( $new_date->format( 'd' ) <= (int) $sync_info ) {
					$diff = $sync_info - $new_date->format( 'd' );
					$new_date->modify( '+ ' . $diff . ' days' );
				} else {
					$new_date->modify( 'first day of next month' );
					$new_date->add( new DateInterval( 'P' . ( $sync_info - 1 ) . 'D' ) );
				}
			}

			return $new_date->getTimestamp();
		}

		/**
		 * Return the next payment due date calculated to synchronize yearly periods.
		 *
		 * @param array      $sync_info Synchronization info.
		 * @param WC_Product $product Product.
		 *
		 * @return int
		 */
		public function get_next_payment_date_for_years( $sync_info, $product ) {

			$new_date = $this->get_start_calculation_date( $product );
			$day      = ( 'end' === $sync_info['day'] ) ? 1 : $sync_info['day'];
			$today    = new DateTime();
			// Move the date at the end of the month.
			if ( 'end' === $sync_info['day'] ) {
				$new_date->modify( 'last day of this month' );
				$day_of_month = $new_date->format( 'd' );
			} else {
				$day_of_month = $sync_info['day'];
			}

			if ( $today->format( 'n' ) === $sync_info['month'] && $today->format( 'd' ) === $day_of_month ) {
				$new_date = $today;
			} elseif ( $new_date->format( 'n' ) < $sync_info['month'] || ( $new_date->format( 'n' ) === $sync_info['month'] ) && ( $new_date->format( 'd' ) < $day_of_month ) ) {
				$new_date = $new_date->modify( $new_date->format( 'y' ) . '-' . $sync_info['month'] . '-' . $day );
			} else {
				$new_date = $new_date->modify( ( (int) $new_date->format( 'y' ) + 1 ) . '-' . $sync_info['month'] . '-' . $day );
			}

			$new_date->setTime( $this->time_of_day, 0, 0 );

			return $new_date->getTimestamp();
		}

		/**
		 * Set the default sync info by period.
		 *
		 * @param string $period Weeks, months or years.
		 *
		 * @return mixed
		 */
		public function get_default_sync_info( $period ) {
			$default_sync_info = array(
				'weeks'  => get_option( 'start_of_week' ),
				'months' => 1,
				'years'  => array(
					'month' => 1,
					'day'   => 1,
				),
			);

			return isset( $default_sync_info[ $period ] ) ? $default_sync_info[ $period ] : false;
		}

		/**
		 * Set the new price inside the cart when a subscription can be synchronized and the price is prorated.
		 *
		 * @param array $cart_item Cart item.
		 *
		 * @return array
		 */
		public function set_synch_changes_on_cart( $cart_item ) {
			if ( isset( $cart_item['ywsbs-subscription-info'] ) ) {
				$subscription_info = $cart_item['ywsbs-subscription-info'];
				$product           = $cart_item['data'];

				if ( $this->is_synchronizable( $product, true ) ) {
					// check the next payment due date.
					$next_payment_due_date = YWSBS_Subscription_Helper::get_billing_payment_due_date( $product );
					$next_payment_due_date = $this->get_next_payment_due_date_sync( $next_payment_due_date, $product );

					$today                           = new DateTime();
					$next_payment_due_date_date_time = new DateTime( '@' . $next_payment_due_date );

					if ( $today->format( 'Y-m-d' ) === $next_payment_due_date_date_time->format( 'Y-m-d' ) ) {
						return $cart_item;
					}

					$pay_now = $this->get_new_price_sync( $product->get_price('edit'), $product, $subscription_info['next_payment_due_date'] );

					if ( (float) $subscription_info['recurring_price'] !== $pay_now ) {
						$cart_item['data']->set_price( $pay_now );
						$cart_item['ywsbs-subscription-info']['sync'] = true;
					}
				}
			}

			return $cart_item;
		}


		/**
		 * Return a message for a product that is synchronizable.
		 *
		 * @param WC_Product $product Product.
		 *
		 * @return string
		 */
		public function get_product_sync_message( $product ) {
			$message       = '';
			$first_payment = get_option( 'ywsbs_sync_first_payment', 'no' );
			$show_message  = get_option( 'ywsbs_sync_show_product_info', 'yes' );

			if ( 'yes' !== $show_message || ! in_array( $first_payment, array( 'no', 'prorate' ), true ) || ! $this->is_synchronizable( $product, true ) ) {
				return $message;
			}

			// check the next payment due date.
			$next_payment_due_date = YWSBS_Subscription_Helper::get_billing_payment_due_date( $product );
			$next_payment_due_date = $this->get_next_payment_due_date_sync( $next_payment_due_date, $product );
			$price                 = (float) $this->get_new_price_sync( $product->get_price(), $product, $next_payment_due_date );
			$fee                   = (float) ywsbs_get_product_fee( $product );

			if ( ! empty( $fee ) && $fee > 0 ) {
				$price += $fee;
			}

			if ( $product->get_price() !== $price ) {
				$new_price  = wc_get_price_to_display( $product, array( 'price' => $price ) );
				$next_price = wc_get_price_to_display( $product, array( 'price' => $product->get_price() ) );

				if ( empty( $price ) ) {
					$message = sprintf(
					/* translators: Prorate message on single product page. 1. Amount to pay now, 2. Recurring amount, 3. Next renewal date */
						_x( 'Nothing to pay now! Your next payment will be scheduled on %3$s', 'Prorate message on single product page. 1. Amount to pay now, 2. Recurring amount, 3. Next renewal date ', 'yith-woocommerce-subscription' ),
						wc_price( $new_price ),
						wc_price( $next_price ),
						date_i18n( wc_date_format(), $next_payment_due_date )
					);
				} else {

					$message = sprintf(
					/* translators: Prorate message on single product page. 1. Amount to pay now, 2. Recurring amount, 3. Next renewal date */
						_x( 'Pay %1$s now and %2$s on %3$s', 'Prorate message on single product page. 1. Amount to pay now, 2. Recurring amount, 3. Next renewal date ', 'yith-woocommerce-subscription' ),
						wc_price( $new_price ),
						wc_price( $next_price ),
						date_i18n( wc_date_format(), $next_payment_due_date )
					);
				}
			}

			return $message;

		}

		/**
		 * Return the now Date Time translated of the trial period.
		 *
		 * @param WC_Product $product Product.
		 *
		 * @return DateTime
		 */
		protected function get_start_calculation_date( $product ) {
			$now = new DateTime();
			try {
				// add trial period to translate the calculation.
				$period = ywsbs_get_trial_period( $product );

				if ( $period ) {
					$now->add( new DateInterval( $period ) );
				}
				$date = $now;
			} catch ( Exception $e ) {
				$date = $now;
			}

			return apply_filters( 'ywsbs_sync_start_calculation_date', $now, $product );
		}
	}
}


/**
 * Unique access to instance of YWSBS_Subscription_Synchronization class
 *
 * @return YWSBS_Subscription_Synchronization
 */
function YWSBS_Subscription_Synchronization() { //phpcs:ignore
	return YWSBS_Subscription_Synchronization::get_instance();
}
