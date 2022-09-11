<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce subscriptions.
 * Version tested: 2.2.19.
 *
 * @since 0.4
 */
class PLLWC_Subscriptions {

	/**
	 * Constructor.
	 * Setups actions and filters.
	 *
	 * @since 0.4
	 */
	public function __construct() {
		add_filter( 'pllwc_copy_post_metas', array( $this, 'copy_post_metas' ) );

		// Add languages to the subscriptions, similar to orders.
		add_filter( 'pll_get_post_types', array( $this, 'translate_types' ), 10, 2 );
		add_filter( 'pll_bulk_translate_post_types', array( $this, 'bulk_translate_post_types' ) );

		// Renewal and Resubscribe.
		add_filter( 'wcs_new_order_created', array( $this, 'new_order_created' ), 10, 2 );

		if ( PLL() instanceof PLL_Admin ) {
			add_action( 'wp_loaded', array( $this, 'custom_columns' ), 20 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 ); // FIXME or add a filter in PLLWC not to restrict to orders.
		}

		add_action( 'change_locale', array( $this, 'change_locale' ) ); // Since WP 4.7.

		// Strings translations.
		$options = array(
			'add_to_cart_button_text' => __( 'Add to Cart Button Text', 'polylang-wc' ),
			'order_button_text'       => __( 'Place Order Button Text', 'polylang-wc' ),
			'switch_button_text'      => __( 'Switch Button Text', 'polylang-wc' ),
		);

		add_filter( 'pll_sanitize_string_translation', array( $this, 'sanitize_strings' ), 10, 3 );

		foreach ( $options as $option => $name ) {
			if ( PLL() instanceof PLL_Settings && $string = get_option( 'woocommerce_subscriptions_' . $option ) ) {
				pll_register_string( $name, $string, 'WooCommerce Subscriptions' );
			} elseif ( PLL() instanceof PLL_Frontend ) {
				add_filter( 'option_woocommerce_subscriptions_' . $option, 'pll__' );
			}
		}

		if ( PLL() instanceof PLL_Frontend ) {
			add_action( 'parse_query', array( $this, 'parse_query' ), 3 ); // Before Polylang.
		}

		// Endpoints.
		add_filter( 'pll_translation_url', array( $this, 'pll_translation_url' ), 10, 2 );
		add_filter( 'pllwc_endpoints_query_vars', array( $this, 'pllwc_endpoints_query_vars' ) );

		// Check if a user has a subscription.
		add_filter( 'wcs_user_has_subscription', array( $this, 'user_has_subscription' ), 10, 4 );
		add_filter( 'woocommerce_get_subscriptions_query_args', array( $this, 'get_subscriptions_query_args' ), 10, 2 );

		// Variable subscription products.
		add_action( 'wp_trash_post', array( $this, 'delete_variation' ) );
		add_action( 'before_delete_post', array( $this, 'delete_variation' ) );

		// Work around endpoints options added in wpml-config.xml :/.
		remove_filter( 'option_woocommerce_myaccount_subscriptions_endpoint', array( PLL_WPML_Config::instance(), 'translate_strings' ) );
		remove_filter( 'option_woocommerce_myaccount_view_subscription_endpoint', array( PLL_WPML_Config::instance(), 'translate_strings' ) );

		// Add e-mails for translation.
		add_filter( 'pllwc_order_email_actions', array( $this, 'filter_order_email_actions' ) );
	}

	/**
	 * Add Subscription e-mails in the translation mechanism.
	 *
	 * @since 1.6
	 *
	 * @param string[] $actions Array of actions used to send emails.
	 * @return string[]
	 */
	public function filter_order_email_actions( $actions ) {
		return array_merge(
			$actions,
			array(
				// Cancelled subscription.
				'cancelled_subscription_notification',
				// Customer completed order.
				'woocommerce_order_status_completed_renewal_notification',
				// Customer Completed Switch Order.
				'woocommerce_order_status_completed_switch_notification',
				// Customer renewal order.
				'woocommerce_order_status_pending_to_processing_renewal_notification',
				'woocommerce_order_status_pending_to_on-hold_renewal_notification',
				// Customer renewal invoice.
				'woocommerce_generated_manual_renewal_order_renewal_notification',
				'woocommerce_order_status_failed_renewal_notification',
				// Expired subscription.
				'expired_subscription_notification', // Since WCS 2.1.
				// New order (to the shop).
				'woocommerce_order_status_pending_to_processing_renewal_notification',
				'woocommerce_order_status_pending_to_completed_renewal_notification',
				'woocommerce_order_status_pending_to_on-hold_renewal_notification',
				'woocommerce_order_status_failed_to_processing_renewal_notification',
				'woocommerce_order_status_failed_to_completed_renewal_notification',
				'woocommerce_order_status_failed_to_on-hold_renewal_notification',
				// Switch order (to the shop).
				'woocommerce_order_status_pending_to_processing_switch_notification',
				'woocommerce_order_status_pending_to_completed_switch_notification',
				'woocommerce_order_status_pending_to_on-hold_switch_notification',
				'woocommerce_order_status_failed_to_processing_switch_notification',
				'woocommerce_order_status_failed_to_completed_switch_notification',
				'woocommerce_order_status_failed_to_on-hold_switch_notification',
				// Suspended Subscription.
				'on-hold_subscription_notification', // Since WCS 2.1.
			)
		);
	}

	/**
	 * Copies or synchronizes metas.
	 * Hooked to the filter 'pllwc_copy_post_metas'.
	 *
	 * @since 0.4
	 *
	 * @param array $keys List of custom fields names.
	 * @return array
	 */
	public function copy_post_metas( $keys ) {
		$wcs_keys = array(
			'_subscription_payment_sync_date',
			'_subscription_length',
			'_subscription_limit',
			'_subscription_period',
			'_subscription_period_interval',
			'_subscription_price',
			'_subscription_sign_up_fee',
			'_subscription_trial_length',
			'_subscription_trial_period',
		);
		return array_merge( $keys, $wcs_keys );
	}

	/**
	 * Language and translation management for the subscriptions post type.
	 * Hooked to the filter 'pll_get_post_types'.
	 *
	 * @since 0.4
	 *
	 * @param array $types List of post type names for which Polylang manages language and translations.
	 * @param bool  $hide  True when displaying the list in Polylang settings.
	 * @return array List of post type names for which Polylang manages language and translations.
	 */
	public function translate_types( $types, $hide ) {
		$wcs_types = array( 'shop_subscription' );
		return $hide ? array_diff( $types, $wcs_types ) : array_merge( $types, $wcs_types );
	}

	/**
	 * Removes the subscriptions post type from bulk translate.
	 * Hooked to the filter 'pll_bulk_translate_post_types'.
	 *
	 * @since 1.2
	 *
	 * @param array $types List of post type names for which Polylang manages the bulk translation.
	 * @return array
	 */
	public function bulk_translate_post_types( $types ) {
		return array_diff( $types, array( 'shop_subscription' ) );
	}

	/**
	 * Assigns the order language when it is created from a subscription.
	 * Hooked to the filter 'wcs_new_order_created'.
	 *
	 * @since 0.4.4
	 *
	 * @param object $new_order    New order.
	 * @param object $subscription Parent subscription.
	 * @return object Unmodified order
	 */
	public function new_order_created( $new_order, $subscription ) {
		if ( $lang = pll_get_post_language( $subscription->get_id() ) ) {
			$data_store = PLLWC_Data_Store::load( 'order_language' );
			$data_store->set_language( $new_order->get_id(), $lang );
		}
		return $new_order;
	}

	/**
	 * Removes the standard languages columns for subscriptions
	 * and replace them with one unique column as done for the orders.
	 * Hooked to the action 'wp_loaded'.
	 *
	 * @since 0.4
	 *
	 * @return void
	 */
	public function custom_columns() {
		remove_filter( 'manage_edit-shop_subscription_columns', array( PLL()->filters_columns, 'add_post_column' ), 100 );
		remove_action( 'manage_shop_subscription_posts_custom_column', array( PLL()->filters_columns, 'post_column' ), 10, 2 );

		add_filter( 'manage_edit-shop_subscription_columns', array( PLLWC()->admin_orders, 'add_order_column' ), 100 );
		add_action( 'manage_shop_subscription_posts_custom_column', array( PLLWC()->admin_orders, 'order_column' ), 10, 2 );
	}

	/**
	 * Removes the language metabox for the subscriptions
	 * and replaces it by the metabox used for the orders.
	 * Hooked to the action 'add_meta_boxes'.
	 *
	 * @since 0.4
	 *
	 * @param string $post_type Post type.
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {
		if ( 'shop_subscription' === $post_type ) {
			remove_meta_box( 'ml_box', $post_type, 'side' ); // Removes the Polylang metabox.
			add_meta_box( 'pllwc_box', __( 'Language', 'polylang-wc' ), array( PLLWC()->admin_orders, 'order_language' ), $post_type, 'side', 'high' );
		}
	}

	/**
	 * Reloads Subscription translations in emails.
	 * Hooked to the action 'change_locale'.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function change_locale() {
		if ( class_exists( 'WC_Subscriptions_Core_Plugin' ) ) {
			WC_Subscriptions_Core_Plugin::instance()->load_plugin_textdomain();
		} else {
			// Backward compatibility with Subscriptions < 4.0.0.
			WC_Subscriptions::load_plugin_textdomain();
		}
	}

	/**
	 * Translated strings must be sanitized the same way WooCommerce Subscriptions does before they are saved.
	 * Hooked to the filter 'pll_sanitize_string_translation'.
	 *
	 * @since 0.4
	 *
	 * @param string $translation A string translation.
	 * @param string $name        The string name.
	 * @param string $context     The group the string belongs to.
	 * @return string Sanitized translation
	 */
	public function sanitize_strings( $translation, $name, $context ) {
		if ( 'WooCommerce Subscriptions' === $context ) {
			$translation = wp_kses_post( trim( $translation ) );
		}
		return $translation;
	}

	/**
	 * Disables the language filter for a customer to see all his/her subscriptions whatever the languages.
	 * Hooked to the action 'parse_query'.
	 *
	 * @since 0.4
	 *
	 * @param WP_Query $query WP_Query object.
	 * @return void
	 */
	public function parse_query( $query ) {
		$qvars = $query->query_vars;

		// Customers should see all their subscriptions whatever the language.
		if ( isset( $qvars['post_type'] ) && ( 'shop_subscription' === $qvars['post_type'] || ( is_array( $qvars['post_type'] ) && in_array( 'shop_subscription', $qvars['post_type'] ) ) ) ) {
			$query->set( 'lang', 0 );
		}
	}

	/**
	 * Returns the translation of the current url.
	 * Handles the translations of the Subscriptions endpoints slugs.
	 * Hooked to the filter 'pll_translation_url'.
	 *
	 * @since 0.4
	 *
	 * @param string $url  URL of the translation, to modify.
	 * @param string $lang Language slug.
	 * @return string
	 */
	public function pll_translation_url( $url, $lang ) {
		if ( $url && defined( 'POLYLANG_PRO' ) && POLYLANG_PRO && get_option( 'permalink_structure' ) ) {
			$wcs_query = pll_get_anonymous_object_from_filter( 'init', array( 'WCS_Query', 'add_endpoints' ) );

			if ( is_object( $wcs_query ) ) {
				$endpoint = $wcs_query->get_current_endpoint();

				if ( $endpoint && isset( $wcs_query->query_vars[ $endpoint ] ) ) {
					$language = PLL()->model->get_language( $lang );
					$url      = PLL()->translate_slugs->slugs_model->switch_translated_slug( $url, $language, 'wc_' . $wcs_query->query_vars[ $endpoint ] );
				}
			}
		}

		return $url;
	}

	/**
	 * Adds the Subscriptions endpoints to the list of endpoints to translate.
	 * Hooked to the filter 'pllwc_endpoints_query_vars'.
	 *
	 * @since 0.4
	 *
	 * @param array $slugs Endpoints slugs.
	 * @return array
	 */
	public function pllwc_endpoints_query_vars( $slugs ) {
		$wcs_query = pll_get_anonymous_object_from_filter( 'init', array( 'WCS_Query', 'add_endpoints' ) );
		return empty( $wcs_query ) ? $slugs : array_merge( $slugs, $wcs_query->get_query_vars() );
	}

	/**
	 * Checks if a user has a subscription to a translated product.
	 * Hooked to the filter 'wcs_user_has_subscription'.
	 *
	 * @since 0.9.2
	 *
	 * @param bool  $has_subscription Whether WooCommerce Subscriptions found a subscription.
	 * @param int   $user_id          The ID of a user in the store.
	 * @param int   $product_id       The ID of a product in the store.
	 * @param mixed $status           Subscription status.
	 * @return bool
	 */
	public function user_has_subscription( $has_subscription, $user_id, $product_id, $status ) {
		if ( false === $has_subscription && ! empty( $product_id ) ) {
			$data_store = PLLWC_Data_Store::load( 'product_language' );
			foreach ( wcs_get_users_subscriptions( $user_id ) as $subscription ) {
				if ( empty( $status ) || 'any' === $status || $subscription->has_status( $status ) ) {
					foreach ( $data_store->get_translations( $product_id ) as $tr_id ) {
						if ( $subscription->has_product( $tr_id ) ) {
							$has_subscription = true;
							break 2;
						}
					}
				}
			}
		}
		return $has_subscription;
	}

	/**
	 * When querying subscriptions and no subscriptions have been found for the current product,
	 * checks if there are subscriptions for the translated products.
	 * Hooked to the filter 'woocommerce_get_subscriptions_query_args'.
	 *
	 * @since 1.2
	 *
	 * @param array $query_args WP_Query() arguments.
	 * @param array $args       Arguments of wcs_get_subscriptions().
	 * @return array
	 */
	public function get_subscriptions_query_args( $query_args, $args ) {
		if ( isset( $query_args['post__in'] ) && array( 0 ) === $query_args['post__in'] ) {
			$data_store = PLLWC_Data_Store::load( 'product_language' );
			$query_args['post__in'] = wcs_get_subscriptions_for_product(
				array_merge(
					$data_store->get_translations( $args['product_id'] ),
					$data_store->get_translations( $args['variation_id'] )
				)
			);
		}
		return $query_args;
	}

	/**
	 * Synchronizes the subscription variations deletion.
	 * The case is handled specifically in WC Subscriptions because
	 * subscription variations are trashed and not deleted permanently.
	 * Hooked to the actions 'wp_trash_post' and 'before_delete_post'.
	 *
	 * @since 1.3.3
	 *
	 * @param int $variation_id Subscription variation id.
	 * @return void
	 */
	public function delete_variation( $variation_id ) {
		static $avoid_delete = array();

		$post_type = get_post_type( $variation_id );

		if ( 'product_variation' === $post_type && ! in_array( $variation_id, $avoid_delete ) ) {
			$variation_product = wc_get_product( $variation_id );

			if ( $variation_product && $variation_product->is_type( 'subscription_variation' ) ) {
				$data_store = PLLWC_Data_Store::load( 'product_language' );
				$tr_ids = $data_store->get_translations( $variation_id );
				$avoid_delete = array_merge( $avoid_delete, array_values( $tr_ids ) ); // To avoid deleting a variation two times.
				foreach ( $tr_ids as $tr_id ) {
					wp_trash_post( $tr_id );
				}
			}
		}
	}
}
