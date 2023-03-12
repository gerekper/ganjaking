<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Subscription Downloads Products.
 *
 * @package  WC_Subscription_Downloads_Products
 * @category Products
 * @author   WooThemes
 */
class WC_Subscription_Downloads_Products {

	/**
	 * Products actions.
	 */
	public function __construct() {
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'simple_write_panel_options' ), 10 );
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'variable_write_panel_options' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_simple_product_data' ), 10 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_product_data' ), 10, 2 );
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			add_action( 'woocommerce_duplicate_product', array( $this, 'save_subscriptions_when_duplicating_product' ), 10, 2 );
		} else {
			add_action( 'woocommerce_product_duplicate', array( $this, 'save_subscriptions_when_duplicating_product' ), 10, 2 );
		}
	}

	/**
	 * Product screen scripts.
	 *
	 * @return void
	 */
	public function scripts() {
		$screen = get_current_screen();

		if ( 'product' == $screen->id && version_compare( WC_VERSION, '2.3.0', '<' ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'wc_subscription_downloads_writepanel', plugins_url( 'assets/js/admin/writepanel' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'ajax-chosen', 'chosen' ), WC_SUBSCRIPTION_DOWNLOADS_VERSION, true );

			wp_localize_script(
				'wc_subscription_downloads_writepanel',
				'wc_subscription_downloads_product',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'security' => wp_create_nonce( 'search-products' ),
				)
			);
		}
	}

	/**
	 * Simple product write panel options.
	 *
	 * @return string
	 */
	public function simple_write_panel_options() {
		global $post, $woocommerce;

		?>

		<div class="options_group subscription_downloads show_if_downloadable">

			<p class="form-field _subscription_downloads_field">
				<label for="subscription-downloads-ids"><?php _e( 'Subscriptions', 'woocommerce-subscription-downloads' ); ?></label>

				<?php if ( version_compare( WC_VERSION, '3.0', '>=' ) ) : ?>
					<select id="subscription-downloads-ids" multiple="multiple" data-action="wc_subscription_downloads_search" data-placeholder="<?php _e( 'Select subscriptions', 'woocommerce-subscription-downloads' ); ?>" class="subscription-downloads-ids wc-product-search" name="_subscription_downloads_ids[]" style="width: 50%;">
						<?php
						$subscriptions_ids = WC_Subscription_Downloads::get_subscriptions( $post->ID );
						if ( empty( $subscriptions_ids ) ) {
							$subscriptions_ids = get_post_meta( $post->ID, '_subscription_downloads_ids', true );
						}
						if ( $subscriptions_ids ) {
							foreach ( $subscriptions_ids as $subscription_id ) {
								$_subscription = wc_get_product( $subscription_id );

								if ( $_subscription ) {
									echo '<option value="' . esc_attr( $subscription_id ) . '" selected="selected">' . sanitize_text_field( $_subscription->get_formatted_name() ) . '</option>';
								}
							}
						}
						?>
					</select>
				<?php else : ?>

					<?php
					$subscriptions_ids = WC_Subscription_Downloads::get_subscriptions( $post->ID );
					$subscriptions_selected = array();
					$subscriptions_value    = array();

					if ( $subscriptions_ids ) {
						foreach ( $subscriptions_ids as $subscription_id ) {
							$_subscription = wc_get_product( $subscription_id );

							if ( $_subscription ) {
								$subscriptions_selected[ $subscription_id ] = sanitize_text_field( $_subscription->get_formatted_name() );
								$subscriptions_value[] = $subscription_id;
							}
						}
					}
					?>
					<input type="hidden" id="subscription-downloads-ids" class="wc-product-search subscription-downloads-ids" name="_subscription_downloads_ids" data-placeholder="<?php _e( 'Select subscriptions', 'woocommerce-subscription-downloads' ); ?>" data-selected='<?php echo _wp_specialchars( wp_json_encode( $subscriptions_selected ), ENT_QUOTES, 'UTF-8', true ); // Same as wc_esc_json but it's only in WC 3.5.5. ?>' value="<?php echo esc_attr( implode( ',', $subscriptions_value ) ); ?>" data-allow_clear="true" style="width: 50%;" data-action="wc_subscription_downloads_search" data-multiple="true" />
				<?php endif; ?> <img class="help_tip" data-tip='<?php echo wc_sanitize_tooltip( __( 'Select subscriptions that this product is part.', 'woocommerce-subscription-downloads' ) ); ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
			</p>

		</div>

		<?php
	}

	/**
	 * Variable product write panel options.
	 *
	 * @return string
	 */
	public function variable_write_panel_options( $loop, $variation_data, $variation ) {
		?>

		<?php if ( version_compare( WC_VERSION, '3.0', '>=' ) ) : ?>
			<tr class="show_if_variation_downloadable">
				<td colspan="2">
					<div>
						<label><?php _e( 'Subscriptions', 'woocommerce-subscription-downloads' ); ?>: <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Select subscriptions that this product is part.', 'woocommerce-subscription-downloads' ) ); ?>" href="#">[?]</a></label>

						<select multiple="multiple" data-placeholder="<?php _e( 'Select subscriptions', 'woocommerce-subscription-downloads' ); ?>" class="subscription-downloads-ids wc-product-search" name="_variable_subscription_downloads_ids[<?php echo $loop; ?>][]">
							<?php
							$subscriptions_ids = WC_Subscription_Downloads::get_subscriptions( $variation->ID );
							if ( $subscriptions_ids ) {
								foreach ( $subscriptions_ids as $subscription_id ) {
									$_subscription = wc_get_product( $subscription_id );

									if ( $_subscription ) {
										echo '<option value="' . esc_attr( $subscription_id ) . '" selected="selected">' . sanitize_text_field( $_subscription->get_formatted_name() ) . '</option>';
									}
								}
							}
							?>
						</select>
					</div>
				</td>
			</tr>
		<?php else : ?>
			<div class="show_if_variation_downloadable">
				<p class="form-row form-row-wide">
					<label><?php _e( 'Subscriptions', 'woocommerce-subscription-downloads' ); ?>: <a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Select subscriptions that this product is part.', 'woocommerce-subscription-downloads' ) ); ?>" href="#">[?]</a></label>
					<?php
					$subscriptions_ids = WC_Subscription_Downloads::get_subscriptions( $variation->ID );
					$subscriptions_selected = array();
					$subscriptions_value    = array();

					if ( $subscriptions_ids ) {
						foreach ( $subscriptions_ids as $subscription_id ) {
							$_subscription = wc_get_product( $subscription_id );

							if ( $_subscription ) {
								$subscriptions_selected[ $subscription_id ] = sanitize_text_field( $_subscription->get_formatted_name() );
								$subscriptions_value[] = $subscription_id;
							}
						}
					}
					?>
					<input type="hidden" class="wc-product-search subscription-downloads-ids" name="_variable_subscription_downloads_ids[<?php echo $loop; ?>]" data-placeholder="<?php _e( 'Select subscriptions', 'woocommerce-subscription-downloads' ); ?>" data-selected='<?php echo _wp_specialchars( wp_json_encode( $subscriptions_selected ),  ENT_QUOTES, 'UTF-8', true ); // Same as wc_esc_json but it's only in WC 3.5.5. ?>' value="<?php echo esc_attr( implode( ',', $subscriptions_value ) ); ?>" data-allow_clear="true" data-action="wc_subscription_downloads_search" data-multiple="true" />
				</p>
			</div>
		<?php endif; ?>

		<?php
	}

	/**
	 * Search orders from subscription product ID.
	 *
	 * @param  int   $subscription_product_id
	 *
	 * @return array
	 */
	protected function get_orders( $subscription_product_id ) {
		global $wpdb;

		$orders   = array();
		$meta_key = '_product_id';

		// Check if subscription product has parent (i.e. is a variable subscription product).
		$parent_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_parent AS parent_id
				FROM {$wpdb->prefix}posts
				WHERE ID = %d;
				",
				$subscription_product_id
			)
		);

		// If the subscription product is a variation, use variation meta key to find related orders.
		if ( ! empty( $parent_id ) ) {
			$meta_key = '_variation_id';
		}

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT order_items.order_id AS id
				FROM {$wpdb->prefix}woocommerce_order_items as order_items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
				WHERE itemmeta.meta_key = %s
				AND itemmeta.meta_value = %d;
				",
				$meta_key,
				$subscription_product_id
			)
		);

		foreach ( $results as $order ) {
			$orders[] = $order->id;
		}

		$orders = apply_filters( 'woocommerce_subscription_downloads_get_orders', $orders, $subscription_product_id );

		return $orders;
	}

	/**
	 * Revoke access to download.
	 *
	 * @param  bool $download_id
	 * @param  bool $product_id
	 * @param  bool $order_id
	 *
	 * @return void
	 */
	protected function revoke_access_to_download( $download_id, $product_id, $order_id ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "
			DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
			WHERE order_id = %d AND product_id = %d AND download_id = %s;
		", $order_id, $product_id, $download_id  ) );

		do_action( 'woocommerce_ajax_revoke_access_to_product_download', $download_id, $product_id, $order_id );
	}

	/**
	 * Update subscription downloads table and orders.
	 *
	 * @param  int $product_id
	 * @param  array $subscriptions
	 *
	 * @return void
	 */
	protected function update_subscription_downloads( $product_id, $subscriptions ) {
		global $wpdb;

		if ( version_compare( WC_VERSION, '3.0', '<' ) && ! empty( $subscriptions ) ) {
			$subscriptions = explode( ',', $subscriptions );
		}

		$current = WC_Subscription_Downloads::get_subscriptions( $product_id );

		// Delete items.
		$delete_ids = array_diff( $current, $subscriptions );
		if ( $delete_ids ) {
			foreach ( $delete_ids as $delete ) {
				$wpdb->delete(
					$wpdb->prefix . 'woocommerce_subscription_downloads',
					array(
						'product_id'      => $product_id,
						'subscription_id' => $delete,
					),
					array(
						'%d',
						'%d',
					)
				);

				$_orders = $this->get_orders( $delete );
				foreach ( $_orders as $order_id ) {
					$_product  = wc_get_product( $product_id );
					$downloads = version_compare( WC_VERSION, '3.0', '<' ) ? $_product->get_files() : $_product->get_downloads();

					// Adds the downloadable files to the order/subscription.
					foreach ( array_keys( $downloads ) as $download_id ) {
						$this->revoke_access_to_download( $download_id, $product_id, $order_id );
					}
				}
			}
		}

		// Add items.
		$add_ids = array_diff( $subscriptions, $current );
		if ( $add_ids ) {
			foreach ( $add_ids as $add ) {
				$wpdb->insert(
					$wpdb->prefix . 'woocommerce_subscription_downloads',
					array(
						'product_id'      => $product_id,
						'subscription_id' => $add,
					),
					array(
						'%d',
						'%d',
					)
				);

				$_orders = $this->get_orders( $add );
				foreach ( $_orders as $order_id ) {
					$order     = wc_get_order( $order_id );

					if ( ! is_a( $order, 'WC_Subscription' ) ) {
						// avoid adding permissions to orders and it's
						// subscription for the same user, causing duplicates
						// to show up
						continue;
					}

					$_product  = wc_get_product( $product_id );
					$downloads = version_compare( WC_VERSION, '3.0', '<' ) ? $_product->get_files() : $_product->get_downloads();

					// Adds the downloadable files to the order/subscription.
					foreach ( array_keys( $downloads ) as $download_id ) {
						wc_downloadable_file_permission( $download_id, $product_id, $order );
					}
				}
			}
		}
	}

	/**
	 * Save simple product data.
	 *
	 * @param  int $product_id
	 *
	 * @return void
	 */
	public function save_simple_product_data( $product_id ) {
		$subscription_ids = ! empty( $_POST['_subscription_downloads_ids'] ) ? $_POST['_subscription_downloads_ids'] : '';

		if ( ! isset( $_POST['_downloadable'] ) || 'publish' !== get_post_status( $product_id ) ) {
			update_post_meta( $product_id, '_subscription_downloads_ids', $subscription_ids );
			return;
		}

		delete_post_meta( $product_id, '_subscription_downloads_ids', $subscription_ids );
		$subscriptions = $subscription_ids ?: array();

		$this->update_subscription_downloads( $product_id, $subscriptions );
	}

	/**
	 * Save variable product data.
	 *
	 * @param  int $variation_id
	 * @param  int $index
	 *
	 * @return void
	 */
	public function save_variation_product_data( $variation_id, $index ) {
		if ( ! isset( $_POST['variable_is_downloadable'][ $index ] ) ) {
			return;
		}

		$subscriptions = isset( $_POST['_variable_subscription_downloads_ids'][ $index ] ) ? $_POST['_variable_subscription_downloads_ids'][ $index ] : array();

		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$subscriptions = explode( ',', $subscriptions );
		}

		$subscriptions = array_filter( $subscriptions );

		$this->update_subscription_downloads( $variation_id, $subscriptions );
	}

	/**
	 * Save subscriptions information when duplicating a product.
	 *
	 * @param int|WC_Product     $new_id Duplicated product ID
	 * @param WP_Post|WC_Product $post   Product being duplicated
	 */
	public function save_subscriptions_when_duplicating_product( $id_or_product, $post ) {
		$post_id = is_a( $post, 'WC_Product' ) ? $post->get_parent_id() : $post->ID;
		$new_id  = is_a( $id_or_product, 'WC_Product' ) ? $id_or_product->get_id() : $id_or_product;

		$subscriptions = WC_Subscription_Downloads::get_subscriptions( $post_id );
		if ( ! empty( $subscriptions ) ) {
			$this->update_subscription_downloads( $new_id, $subscriptions );
		}

		$children_products = get_children( 'post_parent=' . $post_id . '&post_type=product_variation' );
		if ( empty( $children_products ) ) {
			return;
		}

		// Create assoc array where keys are flatten variation attributes and values
		// are original product variations.
		$children_ids_by_variation_attributes = array();
		foreach ( $children_products as $child ) {
			$str_attributes = $this->get_str_variation_attributes( $child );
			if ( ! empty( $str_attributes ) ) {
				$children_ids_by_variation_attributes[ $str_attributes ] = $child;
			}
		}

		// Copy variations' subscriptions.
		$exclude               = apply_filters( 'woocommerce_duplicate_product_exclude_children', false );
		$new_children_products = get_children( 'post_parent=' . $new_id . '&post_type=product_variation' );
		if ( ! $exclude && ! empty( $new_children_products ) ) {
			foreach ( $new_children_products as $child ) {
				$str_attributes = $this->get_str_variation_attributes( $child );
				if ( ! empty( $children_ids_by_variation_attributes[ $str_attributes ] ) ) {
					$this->save_subscriptions_when_duplicating_product(
						$child->ID,
						$children_ids_by_variation_attributes[ $str_attributes ]
					);
				}
			}
		}
	}

	/**
	 * Get string representation of variation attributes from a given product variation.
	 *
	 * @param mixed $product_variation Product variation
	 *
	 * @return string Variation attributes
	 */
	protected function get_str_variation_attributes( $product_variation ) {
		$product_variation = wc_get_product( $product_variation );
		if ( ! is_callable( array( $product_variation, 'get_formatted_variation_attributes' ) ) ) {
			return false;
		}

		return (string) wc_get_formatted_variation( $product_variation, true );
	}
}

new WC_Subscription_Downloads_Products;
