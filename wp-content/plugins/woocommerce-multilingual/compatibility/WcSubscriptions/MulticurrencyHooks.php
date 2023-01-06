<?php

namespace WCML\Compatibility\WcSubscriptions;

use WC_Product;
use WC_Product_Subscription_Variation;
use WC_Product_Variable_Subscription;
use WCML\Utilities\DB;
use WCML_Custom_Prices_UI;
use woocommerce_wpml;
use wpdb;
use function WPML\FP\tap as tap;

class MulticurrencyHooks implements \IWPML_Action {

	/** @var woocommerce_wpml $woocommerce_wpml */
	private $woocommerce_wpml;

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var bool $newSubscription */
	private $newSubscription = false;

	/** @var bool $proratingPrice */
	private $proratingPrice = false;

	public function __construct( woocommerce_wpml $woocommerce_wpml, wpdb $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {
		add_action( 'init', [ $this, 'init' ], 9 );

		add_filter( 'wcml_custom_prices_fields', [ $this, 'set_prices_fields' ], 10, 2 );
		add_filter( 'wcml_custom_prices_strings', [ $this, 'set_labels_for_prices_fields' ], 10, 2 );
		add_filter( 'wcml_custom_prices_fields_labels', [ $this, 'set_labels_for_prices_fields' ], 10, 2 );
		add_filter( 'wcml_update_custom_prices_values', [ $this, 'update_custom_prices_values' ], 10, 3 );
		add_action( 'wcml_after_custom_prices_block', [ $this, 'new_subscription_prices_block' ] );

		add_filter( 'woocommerce_subscriptions_product_price', [ $this, 'woocommerce_subscription_price_from' ], 10, 2 );
	}

	public function init() {
		if ( ! is_admin() ) {
			add_filter( 'woocommerce_subscriptions_product_sign_up_fee', [ $this, 'subscriptions_product_sign_up_fee_filter' ], 10, 2 );
			$this->maybe_force_client_currency_for_subscription();
		}

		add_filter( 'wcs_switch_proration_new_price_per_day', tap( [ $this, 'set_prorating_price' ] ) );
	}

	/**
	 * Set a flag when we are prorating the price (upgrades/downgrades).
	 * We do this to skip currency conversion in the sign_up_fee because
	 * when switching subscription it has already been converted.
	 */
	public function set_prorating_price() {
		$this->proratingPrice = true;
	}

	/**
	 * Filter Subscription Sign-up fee cost
	 *
	 * @param string     $subscriptionSignUpFee
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public function subscriptions_product_sign_up_fee_filter( $subscriptionSignUpFee, $product ) {
		if ( $product && ! $this->proratingPrice ) {
			$currency = $this->woocommerce_wpml->multi_currency->get_client_currency();

			if ( wcml_get_woocommerce_currency_option() !== $currency ) {
				$productId = $product->get_id();

				if ( $product instanceof WC_Product_Variable_Subscription ) {
					$productId = $product->get_meta( '_min_price_variation_id', true );
				}

				$originalProductId = $this->woocommerce_wpml->products->get_original_product_id( $productId );

				if ( get_post_meta( $originalProductId, '_wcml_custom_prices_status', true ) ) {
					$subscriptionSignUpFee = get_post_meta( $originalProductId, '_subscription_sign_up_fee_' . $currency, true );
				} else {
					$subscriptionSignUpFee = apply_filters( 'wcml_raw_price_amount', $subscriptionSignUpFee );
				}
			}
		}

		return $subscriptionSignUpFee;
	}

	/**
	 * Force client currency for resubscribe subscription
	 */
	public function maybe_force_client_currency_for_subscription() {
		$subscriptionId = false;
		$getData         = wpml_collect( $_GET );

		if ( $getData->has( 'resubscribe' ) ) {
			$subscriptionId = (int) $getData->get( 'resubscribe' );
		} elseif ( $getData->has( 'subscription_renewal_early' ) ) {
			$subscriptionId = (int) $getData->get( 'subscription_renewal_early' );
		} elseif ( is_cart() || is_checkout() ) {
			$resubscribeCartItem = wcs_cart_contains_resubscribe();
			if ( $resubscribeCartItem ) {
				$subscriptionId = $resubscribeCartItem['subscription_resubscribe']['subscription_id'];
			} else {
				$earlyRenewalCartItem = wcs_cart_contains_early_renewal();
				if ( $earlyRenewalCartItem ) {
					$subscriptionId = $earlyRenewalCartItem['subscription_renewal']['subscription_renewal_early'];
				}
			}
		}

		if ( $subscriptionId ) {
			$subscriptionCurrency = get_post_meta( $subscriptionId, '_order_currency', true );
			if ( $subscriptionCurrency && $this->woocommerce_wpml->multi_currency->get_client_currency() !== $subscriptionCurrency ) {
				$this->woocommerce_wpml->multi_currency->set_client_currency( $subscriptionCurrency );
			}
		}
	}

	/**
	 * @param array      $fields
	 * @param int|string $productId
	 *
	 * @return array
	 */
	public function set_prices_fields( $fields, $productId ) {
		if ( $this->isSubscriptionsProduct( $productId ) || $this->newSubscription ) {
			$fields[] = '_subscription_sign_up_fee';
		}

		return $fields;
	}

	/**
	 * @param array      $labels
	 * @param int|string $productId
	 *
	 * @return array
	 */
	public function set_labels_for_prices_fields( $labels, $productId ) {
		if ( $this->isSubscriptionsProduct( $productId ) || $this->newSubscription ) {
			$labels['_regular_price']            = __( 'Subscription Price', 'woocommerce-multilingual' );
			$labels['_subscription_sign_up_fee'] = __( 'Sign-up Fee', 'woocommerce-multilingual' );
		}

		return $labels;
	}

	/**
	 * @param array            $prices
	 * @param string           $code
	 * @param int|string|false $variationId
	 *
	 * @return array
	 */
	public function update_custom_prices_values( $prices, $code, $variationId = false ) {
		if ( isset( $_POST['_custom_subscription_sign_up_fee'][ $code ] ) ) {
			$prices['_subscription_sign_up_fee'] = wc_format_decimal( $_POST['_custom_subscription_sign_up_fee'][ $code ] );
		}

		if ( $variationId && isset( $_POST['_custom_variation_subscription_sign_up_fee'][ $code ][ $variationId ] ) ) {
			$prices['_subscription_sign_up_fee'] = wc_format_decimal( $_POST['_custom_variation_subscription_sign_up_fee'][ $code ][ $variationId ] );
		}

		return $prices;
	}

	/**
	 * @param int|string $productId
	 *
	 * @return void
	 */
	public function new_subscription_prices_block( $productId ) {
		if ( 'new' === $productId ) {
			$this->newSubscription = true;
			echo '<div class="wcml_prices_if_subscription" style="display: none">';
			$custom_prices_ui = new WCML_Custom_Prices_UI( $this->woocommerce_wpml, 'new' );
			$custom_prices_ui->show();
			echo '</div>';
			?>
			<script>
				jQuery(function($) {
					jQuery('.wcml_prices_if_subscription .wcml_custom_prices_input').attr('name', '_wcml_custom_prices[new_subscription]').attr( 'id', '_wcml_custom_prices[new_subscription]');
					jQuery('.wcml_prices_if_subscription .wcml_custom_prices_options_block>label').attr('for', '_wcml_custom_prices[new_subscription]');
					jQuery('.wcml_prices_if_subscription .wcml_schedule_input').each( function(){
						jQuery(this).attr('name', jQuery(this).attr('name')+'_subscription');
					});

					jQuery('.options_group>.wcml_custom_prices_block .wcml_custom_prices_input:first-child').click();
					jQuery('.options_group>.wcml_custom_prices_block .wcml_schedule_options .wcml_schedule_input:first-child').click();

					jQuery(document).on('change', 'select#product-type', function () {
						if (jQuery(this).val() == 'subscription') {
							jQuery('.wcml_prices_if_subscription').show();
							jQuery('.options_group>.wcml_custom_prices_block').hide();
						} else if (jQuery(this).val() != 'variable-subscription') {
							jQuery('.wcml_prices_if_subscription').hide();
							jQuery('.options_group>.wcml_custom_prices_block').show();
						}
					});

					jQuery(document).on('click', '#publish', function () {
						if ( jQuery('.wcml_prices_if_subscription').is( ':visible' ) ) {
							jQuery('.options_group>.wcml_custom_prices_block').remove();
							jQuery('.wcml_prices_if_subscription .wcml_custom_prices_input').attr('name', '_wcml_custom_prices[new]');
							jQuery('.wcml_prices_if_subscription .wcml_schedule_input').each( function(){
								jQuery(this).attr('name', jQuery(this).attr('name').replace('_subscription','') );
							});
						}else{
							jQuery('.wcml_prices_if_subscription').remove();
						}
					});
				});
			</script>
			<?php
		}
	}

	/**
	 * @param int|string $productId
	 *
	 * @return bool
	 */
	private function isSubscriptionsProduct( $productId ) {
		$variationTermTaxonomyIds = $this->wpdb->get_col( "SELECT tt.term_taxonomy_id FROM {$this->wpdb->terms} AS t LEFT JOIN {$this->wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id WHERE t.slug IN ( 'subscription', 'variable-subscription' ) AND tt.taxonomy = 'product_type'" );

		if ( get_post_type( $productId ) == 'product_variation' ) {
			$productId = wp_get_post_parent_id( $productId );
		}

		return (bool) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT count(object_id) FROM {$this->wpdb->term_relationships}
				WHERE object_id = %d AND term_taxonomy_id IN (" . DB::prepareIn( $variationTermTaxonomyIds, '%d' ) . ')',
				$productId
			)
		);
	}

	/**
	 * @param string                                       $price
	 * @param WC_Product|WC_Product_Subscription_Variation $product
	 *
	 * @return string
	 */
	public function woocommerce_subscription_price_from( $price, $product ) {
		if ( $product instanceof WC_Product_Subscription_Variation ) {
			$customPricesOn = get_post_meta( $product->get_id(), '_wcml_custom_prices_status', true );

			if ( $customPricesOn ) {
				$price = get_post_meta( $product->get_id(), '_price_' . $this->woocommerce_wpml->multi_currency->get_client_currency(), true );
			} else {
				$price = apply_filters( 'wcml_raw_price_amount', $price );
			}
		}

		return $price;
	}
}
