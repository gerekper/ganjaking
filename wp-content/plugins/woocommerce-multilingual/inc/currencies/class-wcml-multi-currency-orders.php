<?php

use WCML\COT\Helper as COTHelper;
use WCML\Orders\Legacy\Helper as LegacyHelper;
use WCML\Orders\Helper as OrdersHelper;
use WPML\FP\Fns;
use WPML\FP\Obj;

class WCML_Multi_Currency_Orders {
	const WCML_CONVERTED_META_KEY_PREFIX = '_wcml_converted_';

	/** @var WCML_Multi_Currency */
	private $multi_currency;
	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var WP $wp */
	private $wp;

	/** @var string|null $order_currency */
	private $order_currency;

	public function __construct( WCML_Multi_Currency $multi_currency, woocommerce_wpml $woocommerce_wpml, WP $wp ) {
		$this->multi_currency   = $multi_currency;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wp               = $wp;

		if ( is_admin() ) {
			add_filter( 'init', [ $this, 'orders_init' ] );
		}
	}

	public function orders_init() {

		add_action( 'restrict_manage_posts', [ $this, 'show_orders_currencies_selector' ] );
		add_action( 'woocommerce_order_list_table_restrict_manage_orders', [ $this, 'show_orders_currencies_selector' ] );
		$this->wp->add_query_var( '_order_currency' );

		add_filter( 'woocommerce_order_list_table_prepare_items_query_args', [ $this, 'hpos_filter_orders_by_currency' ] );
		add_filter( 'posts_join', [ $this, 'filter_orders_by_currency_join' ] );
		add_filter( 'posts_where', [ $this, 'filter_orders_by_currency_where' ] );

		// new order currency/language switchers.
		add_action( 'woocommerce_process_shop_order_meta', [ $this, 'set_order_currency_on_update' ] );
		add_action( 'woocommerce_order_actions_start', [ $this, 'show_order_currency_selector' ] );

		add_filter( 'woocommerce_order_get_items', Fns::withoutRecursion( Fns::identity(), [ $this, 'set_totals_for_order_items' ] ), 10, 2 );

		add_filter( 'woocommerce_hidden_order_itemmeta', [ $this, 'add_woocommerce_hidden_order_itemmeta' ] );

		add_action( 'wp_ajax_wcml_order_set_currency', [ $this, 'set_order_currency_on_ajax_update' ] );

		// dashboard status screen.
		if ( current_user_can( 'view_woocommerce_reports' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'publish_shop_orders' ) ) {
			// filter query to get order by status.
			add_filter( 'query', [ $this, 'filter_order_status_query' ] );
		}

		add_action( 'woocommerce_email_before_order_table', [ $this, 'fix_currency_before_order_email' ] );
		add_action( 'woocommerce_email_after_order_table', [ $this, 'fix_currency_after_order_email' ] );

		if ( is_admin() ) {
			add_filter( 'woocommerce_order_get_currency', [ $this, 'get_currency_for_new_order' ], 10, 2 );
		}
	}

	public function show_orders_currencies_selector() {
		global $wp_query;

		if ( ! OrdersHelper::isOrderListAdminScreen() ) {
			return;
		}

		$currency_codes = $this->multi_currency->get_currency_codes();
		$currencies     = get_woocommerce_currencies();
		?>
		<select id="dropdown_shop_order_currency" name="_order_currency">
			<option value=""><?php esc_html_e( 'Show all currencies', 'woocommerce-multilingual' ); ?></option>
			<?php
			foreach ( $currency_codes as $currency ) {
				$selected = '';
				if ( isset( $wp_query->query['_order_currency'] ) ) {
					$selected = selected( $currency, $wp_query->query['_order_currency'], false );
				} elseif ( COTHelper::isOrderListAdminScreen() ) {
					$selected = selected( $currency, $this->get_order_currency_get(), false );
				}
				$text = sprintf( '%s (%s)', $currencies[ $currency ], get_woocommerce_currency_symbol( $currency ) );
				?>
				<option value="<?php echo esc_html( $currency ); ?>" <?php echo wp_kses_post( $selected ); ?>><?php echo esc_html( $text ); ?></option>
				<?php
			}
			?>
		</select>
		<?php
	}

	/**
	 * Check if a currency filter is applied on the current page.
	 *
	 * @return bool True if a currency filter is applied, false otherwise.
	 */
	private function is_currency_filter_applied() {
		global $wp_query;
		return ! empty( $wp_query->query['_order_currency'] );
	}

	public function filter_orders_by_currency_join( $join ) {
		global $wpdb;

		if ( LegacyHelper::isOrderListAdminScreen() && $this->is_currency_filter_applied() ) {
			$join .= " JOIN {$wpdb->postmeta} wcml_pm ON {$wpdb->posts}.ID = wcml_pm.post_id AND wcml_pm.meta_key='_order_currency'";
		}

		return $join;
	}

	public function filter_orders_by_currency_where( $where ) {
		global $wp_query;

		if ( LegacyHelper::isOrderListAdminScreen() && $this->is_currency_filter_applied() ) {
			$where .= " AND wcml_pm.meta_value = '" . esc_sql( $wp_query->query['_order_currency'] ) . "'";
		}

		return $where;
	}

	/**
	 * Filter orders by currency in a WooCommerce system.
	 *
	 * This method is responsible for modifying the query arguments used for filtering orders
	 * based on the selected currency from the WooCommerce admin selector. If a currency is
	 * selected in the admin, it adds the 'currency' filter to the query arguments.
	 *
	 * @param array $query_args The original query arguments for filtering orders.
	 *
	 * @return array The modified query arguments, including the 'currency' filter if applicable.
	 */
	public function hpos_filter_orders_by_currency( $query_args ) {
		$currencyFromAdminSelector = $this->get_order_currency_get();

		if ( $currencyFromAdminSelector ) {
			$query_args['currency'] = $currencyFromAdminSelector;
		}

		return $query_args;
	}

	public function set_order_currency_on_update( $post_id ) {

		if ( isset( $_POST['wcml_shop_order_currency'] ) ) {
			OrdersHelper::setCurrency( $post_id, filter_input( INPUT_POST, 'wcml_shop_order_currency', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
		}

	}

	public function show_order_currency_selector( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order && $order->get_status() === 'auto-draft' ) { /** @see https://onthegosystems.myjetbrains.com/youtrack/issue/wcml-4500 */

			$current_order_currency = $this->get_order_currency_cookie();

			$wc_currencies = get_woocommerce_currencies();
			$currencies    = $this->multi_currency->get_currency_codes();

			?>
			<li class="wide">
				<label><?php _e( 'Order currency:', 'woocommerce-multilingual' ); ?></label>
				<select id="dropdown_shop_order_currency" name="wcml_shop_order_currency">

					<?php foreach ( $currencies as $currency ) : ?>

						<option value="<?php echo $currency; ?>" <?php echo $current_order_currency == $currency ? 'selected="selected"' : ''; ?>><?php echo $wc_currencies[ $currency ]; ?></option>

					<?php endforeach; ?>

				</select>
			</li>
			<?php
			$wcml_order_set_currency_nonce = wp_create_nonce( 'set_order_currency' );

			wc_enqueue_js(
				"
                var order_currency_current_value = jQuery('#dropdown_shop_order_currency option:selected').val();

                jQuery('#dropdown_shop_order_currency').on('change', function(){

                    if(confirm('" . esc_js( __( 'All the products will be removed from the current order in order to change the currency', 'woocommerce-multilingual' ) ) . "')){
                        jQuery.ajax({
                            url: ajaxurl,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                action: 'wcml_order_set_currency',
                                currency: jQuery('#dropdown_shop_order_currency option:selected').val(),
                                wcml_nonce: '" . $wcml_order_set_currency_nonce . "'
                                },
                            success: function( response ){
                                if(typeof response.error !== 'undefined'){
                                    alert(response.error);
                                }else{
                                   window.location = window.location.href;
                                }
                            }
                        });
                    }else{
                        jQuery(this).val( order_currency_current_value );
                        return false;
                    }

                });

            "
			);

		}

	}

	public function set_totals_for_order_items( $items, $order ) {

		if ( isset( $_POST['action'] ) && in_array(
			$_POST['action'],
			[
				'woocommerce_add_order_item',
				'woocommerce_save_order_items',
			],
			true
		) ) {

			foreach ( $items as $item ) {
				$this->set_converted_totals_for_item( $item, $this->get_order_coupons_objects( $order ) );
			}
		}

		return $items;
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	private function get_order_coupons_objects( $order ) {
		$order_coupons   = $order->get_items( 'coupon' );
		$coupons_objects = [];

		if ( $order_coupons ) {
			foreach ( $order_coupons as $coupon ) {
				$coupon_data       = $coupon->get_data();
				$coupons_objects[] = new WC_Coupon( $coupon_data['code'] );
			}
		}

		return $coupons_objects;
	}

	public function add_woocommerce_hidden_order_itemmeta( $itemmeta ) {
		$itemmeta[] = $this->get_converted_meta_key( 'subtotal' );
		$itemmeta[] = $this->get_converted_meta_key( 'total' );
		$itemmeta[] = '_wcml_total_qty';

		return $itemmeta;
	}

	/**
	 * @param WC_Order_Item_Product $item
	 * @param array $coupons
	 * @param int|bool $order_id
	 * @param string|bool $order_currency
	 */
	public function set_converted_totals_for_item( $item, $coupons, $order_id = false, $order_currency = false ) {

		if ( 'line_item' === $item->get_type() ) {

			if ( ! $order_currency ) {

				$order_id = $order_id ?: intval( $_POST['order_id'] );

				$order_currency = OrdersHelper::getCurrency( $order_id );

				if ( ! $order_currency ) {
					$order_currency = $this->get_order_currency_cookie();

					if ( in_array(
						$_POST['action'],
						[
							'woocommerce_add_order_item',
							'woocommerce_save_order_items',
						],
						true
					) ) {
						OrdersHelper::setCurrency( $order_id, $order_currency );
					}
				}
			}

			if ( ! isset( $this->multi_currency->prices ) ) {
				$this->multi_currency->prices = new WCML_Multi_Currency_Prices( $this->multi_currency, $this->woocommerce_wpml->get_setting( 'currency_options' ) );
				$this->multi_currency->prices->add_hooks();
			}

			$product_id          = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			$original_product_id = $this->woocommerce_wpml->products->get_original_product_id( $product_id );

			$converted_price  = get_post_meta( $original_product_id, '_price_' . $order_currency, true );
			$converted_totals = [
				'total'    => 0,
				'subtotal' => 0,
			];

			foreach ( array_keys( $converted_totals ) as $key ) {

				if ( ! $item->get_total() ) {
					continue;
				}
				
				if ( 'total' === $key
					&& ( $item->get_total() !== $item->get_subtotal() || $this->total_is_changed( $item ) )
				) {
					
					$converted_totals[ $key ] = $item->get_total();
				} else {
					if ( ! $converted_price ) {
						$converted_meta_key = $this->get_converted_meta_key( $key );
						if (
							! $item->meta_exists( $converted_meta_key ) ||
							( $item->meta_exists( '_wcml_total_qty' ) && $item->get_quantity() !== (int) $item->get_meta( '_wcml_total_qty' ) )
						) {
							$item_price               = $this->multi_currency->prices->raw_price_filter( $item->get_product()->get_price(), $order_currency );
							$converted_totals[ $key ] = $this->get_converted_item_meta( $key, $item_price, false, $item, $order_currency, $coupons );
							$item->update_meta_data( $converted_meta_key, $converted_totals[ $key ] );
						} else {
							if ( 'total' === $key ) {
								$converted_totals[ $key ] = $item->get_meta( $converted_meta_key );
							} else {
								$converted_totals[ $key ] = $this->get_item_meta( $item, $key );
							}
						}
					} else {
						$converted_totals[ $key ] = $this->get_converted_item_meta( $key, $converted_price, true, $item, $order_currency, $coupons );
					}
				}

				call_user_func_array( [ $item, 'set_' . $key ], [ $converted_totals[ $key ] ] );
			}

			$item->update_meta_data( '_wcml_total_qty', $item->get_quantity() );
			$item->save();
		}
	}
	
	/**
	 * @param WC_Order_Item_Product $item
	 *
	 * @return bool
	 */
	private function total_is_changed( $item ) {
		return (int) $item->get_product()->get_price() * (int) $item->get_quantity() !== (int) $item->get_total();
	}
	
	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private function get_converted_meta_key( $key ) {
		return self::WCML_CONVERTED_META_KEY_PREFIX . $key;
	}

	/**
	 * @param WC_Order_Item_Product $item
	 * @param string                $key
	 *
	 * @return bool
	 */
	private function is_value_changed( $item, $key ) {
		$converted_meta_key = $this->get_converted_meta_key( $key );

		if ( ! $item->meta_exists( $converted_meta_key ) ) {
			return true;
		}

		$get_key = 'get_' . $key;

		return $item->$get_key() !== $item->get_meta( $converted_meta_key );
	}

	/**
	 * @param WC_Order_Item_Product $item
	 * @param string                $key
	 *
	 * @return mixed
	 */
	private function get_item_meta( $item, $key ) {
		$converted_meta_key = $this->get_converted_meta_key( $key );

		if ( $this->is_value_changed( $item, $key ) ) {
			$get_key                  = 'get_' . $key;
			return $item->$get_key();
		} else {
			return $item->get_meta( $converted_meta_key );
		}
	}

	/**
	 * @param string                $meta
	 * @param float                 $item_price
	 * @param bool                  $is_custom_price
	 * @param WC_Order_Item_Product $item
	 * @param string                $order_currency
	 * @param array                 $coupons
	 *
	 * @return float
	 */
	private function get_converted_item_meta( $meta, $item_price, $is_custom_price, $item, $order_currency, $coupons ) {

		if ( 'total' === $meta && $coupons ) {

			$discount_amount = 0;
			foreach ( $coupons as $coupon ) {
				if ( $coupon->is_type( 'percent' ) ) {
					$discount_amount += $coupon->get_discount_amount( $item_price );
				} elseif ( $coupon->is_type( 'fixed_product' ) ) {
					$coupon_discount = $coupon->get_discount_amount( $item_price, [], true );

					if ( $is_custom_price && $coupon_discount != $item_price ) {
						$coupon_discount = $this->multi_currency->prices->raw_price_filter( $coupon_discount, $order_currency );
					}
					$discount_amount += $coupon_discount;
				}
			}
			$item_price = $item_price - $discount_amount;
		}

		return $item->get_quantity() * wc_get_price_excluding_tax( $item->get_product(), [ 'price' => $item_price ] );
	}

	public function get_order_currency_cookie() {

		if ( isset( $_COOKIE['_wcml_order_currency'] ) ) {
			return $_COOKIE['_wcml_order_currency'];
		} else {
			return wcml_get_woocommerce_currency_option();
		}

	}

	/**
	 * This function extracts and sanitizes the order currency filter value from the QUERY_STRING.
	 *
	 * @return string|null The sanitized value, or null if it is not present.
	 */

	private function get_order_currency_get() {
		return isset( $_GET['_order_currency'] ) ? sanitize_text_field( wp_unslash( $_GET['_order_currency'] ) ) : null;
	}

	public function set_order_currency_on_ajax_update() {
		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'set_order_currency' ) ) {
			echo json_encode( [ 'error' => __( 'Invalid nonce', 'woocommerce-multilingual' ) ] );
			die();
		}
		$currency = filter_input( INPUT_POST, 'currency', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$cookie_name = '_wcml_order_currency';
		// @todo uncomment or delete when #wpmlcore-5796 is resolved.
		// do_action( 'wpsc_add_cookie', $cookie_name );
		setcookie( $cookie_name, $currency, time() + 86400, COOKIEPATH, COOKIE_DOMAIN );

		$return['currency'] = $currency;

		echo json_encode( $return );

		die();
	}

	/*
	* Filter status query
	*
	* @param string $query
	*
	* @return string
	*
	*/
	public function filter_order_status_query( $query ) {
		global $pagenow, $wpdb;

		if ( $pagenow == 'index.php' ) {
			$sql = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = 'shop_order' GROUP BY post_status";

			if ( $query == $sql ) {

				$currency = $this->multi_currency->admin_currency_selector->get_cookie_dashboard_currency();

				if ( COTHelper::isUsageEnabled() ) {
					$query = $wpdb->prepare( "SELECT `status` as `post_status`, COUNT( * ) AS `num_posts` FROM " . COTHelper::getTableName() . "
						WHERE `type` = 'shop_order' AND `currency` = %s GROUP BY status"
						, $currency );
				} else {
					$query = $wpdb->prepare ("SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts}
						WHERE post_type = 'shop_order' AND ID IN
							( SELECT order_currency.post_id FROM {$wpdb->postmeta} AS order_currency
							WHERE order_currency.meta_key = '_order_currency'
							AND order_currency.meta_value = %s )
						GROUP BY post_status"
						, $currency );
				}

			}
		}

		return $query;
	}

	// handle currency in order emails before handled in woocommerce.
	public function fix_currency_before_order_email( $order ) {

		$order_currency = $order->get_currency();

		if ( ! $order_currency ) {
			return;
		}

		$this->order_currency = $order_currency;
		add_filter( 'woocommerce_currency', [ $this, '_override_woocommerce_order_currency_temporarily' ] );
	}

	public function fix_currency_after_order_email( $order ) {
		unset( $this->order_currency );
		remove_filter( 'woocommerce_currency', [ $this, '_override_woocommerce_order_currency_temporarily' ] );
	}

	public function _override_woocommerce_order_currency_temporarily( $currency ) {
		if ( isset( $this->order_currency ) ) {
			$currency = $this->order_currency;
		}

		return $currency;
	}

	/**
	 * @param string             $currency
	 * @param \WC_Abstract_Order $order
	 *
	 * @return string
	 */
	public function get_currency_for_new_order( $currency, $order ) {

		/** @var callable():bool $isEditingNewOrderItems */
		$isEditingNewOrderItems = function() {
			return (
				isset( $_POST['action'] ) &&
				in_array(
					$_POST['action'],
					[
						'woocommerce_add_order_item',
						'woocommerce_remove_order_item',
						'woocommerce_calc_line_taxes',
						'woocommerce_save_order_items',
					]
				) )
			|| (
				isset( $_GET['action'] ) &&
				$_GET['action'] == 'woocommerce_json_search_products_and_variations'
			);
		};


		if ( OrdersHelper::isOrderCreateAdminScreen() || $isEditingNewOrderItems() ) {
			$orderId       = method_exists( $order, 'get_id' ) ? $order->get_id() : Obj::prop( 'id', $order );
			$orderCurrency = OrdersHelper::getCurrency( $orderId, true );

			return $orderCurrency ?: $this->get_order_currency_cookie();
		}

		return $currency;
	}
}
