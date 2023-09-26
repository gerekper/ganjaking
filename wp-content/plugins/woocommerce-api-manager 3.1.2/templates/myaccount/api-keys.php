<?php
/**
 * API Keys
 *
 * Shows API Keys on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/api-keys.php.
 *
 * HOWEVER, on occasion WooCommerce API Manager will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @since   2.0
 *
 * @author  Todd Lahman LLC
 * @package WooCommerce API Manager/Templates
 * @version 3.1
 */

defined( 'ABSPATH' ) || exit;

wc_print_notices();

if ( empty( $user_id ) ) {
	$user_id = get_current_user_id();
}

if ( ! empty( $user_id ) ) {
	// Set start time for execution.
	//$time_start = microtime( true );

	/**
	 * Every customer must have a Master API Key, and it is missing, so create it now.
	 */
	if ( empty( WC_AM_USER()->get_master_api_key( $user_id ) ) ) {
		WC_AM_USER()->set_registration_master_key_and_status( $user_id );
	}

	$master_api_key_status = WC_AM_USER()->has_api_access( $user_id );

	if ( $master_api_key_status ) {
		$resources = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resources_for_user_id_sort_by_product_title( $user_id );

		if ( $resources ) {
			$master_api_key              = WC_AM_USER()->get_master_api_key( $user_id );
			$hide_product_order_api_keys = WC_AM_USER()->hide_product_order_api_keys();
			$hide_master_api_key         = WC_AM_USER()->hide_master_api_key();

			if ( ! $hide_master_api_key ) {
				?>
                <table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_api_manager my_account_orders">
                    <thead>
                    <tr id="master-api-key-header">
                        <th class="master-api-key"><span
                                    class="nobr"><?php esc_html_e( apply_filters( 'wc_api_manager_my_account_master_api_key_heading', __( 'Master API Key - Can be used to activate any product.', 'woocommerce-api-manager' ) ) ); ?></span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="order">
                        <td class="api-manager-master-api-key">
							<?php echo esc_attr( $master_api_key ); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
				<?php
			}
			?>
            <table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_api_manager my_account_orders">
                <tbody>
                <tr id="product-order-api-key-header">
                    <hr>
					<?php if ( ! $hide_product_order_api_keys ) { ?>
                        <td>
							<?php esc_html_e( apply_filters( 'wc_api_manager_my_account_product_order_api_key_message', __( 'A Product Order API Key is used to activate a single product from a single order.', 'woocommerce-api-manager' ) ) ); ?>
                        </td>
					<?php } ?>
                </tr>
                </tbody>
            </table>
            <table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_api_manager my_account_orders">
                <thead>
                <tr>
                    <th class="<?php echo esc_attr( 'api-manager-software-product' ); ?>"><span
                                class="nobr"><?php esc_html_e( 'Product Title', 'woocommerce-api-manager' ); ?></span></th>
                    <th class="<?php echo esc_attr( 'api-manager-software-product' ); ?>"><span
                                class="nobr" style="white-space: nowrap;"><?php esc_html_e( 'Product ID', 'woocommerce-api-manager' ); ?></span></th>
					<?php
					if ( ! $hide_product_order_api_keys ) { ?>
                        <th class="<?php echo esc_attr( 'api-manager-key' ); ?>"><span
                                    class="nobr" style="white-space: nowrap;"><?php esc_html_e( apply_filters( 'wc_api_manager_my_account_product_order_api_key_heading', __( 'Product Order API Key', 'woocommerce-api-manager' ) ) ); ?></span></th>
					<?php } ?>
                    <th class="<?php echo esc_attr( 'api-manager-activation' ); ?>"><span
                                class="nobr"><?php esc_html_e( 'Activations', 'woocommerce-api-manager' ); ?></span></th>
                    <th class="<?php echo esc_attr( 'api-manager-expire' ); ?>"><span
                                class="nobr"><?php esc_html_e( 'Next Payment', 'woocommerce-api-manager' ); ?></span></th>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach ( $resources as $resource ) {
					// Refreshing cache here will also delete API cache for activations about to be deleted.
					WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( $resource->order_id );

					// Delete excess API Key activations by activation resource ID.
					WC_AM_API_ACTIVATION_DATA_STORE()->delete_excess_api_key_activations_by_activation_id( $resource->activation_ids, $resource->activations_purchased_total );

					$product_object          = WC_AM_PRODUCT_DATA_STORE()->get_product_object( $resource->product_id );
					$parent_product_id       = $resource->parent_id;
					$is_api                  = WC_AM_PRODUCT_DATA_STORE()->is_api_product( $parent_product_id );
					$order_id                = $resource->order_id;
					$order_completed_status  = WC_AM_ORDER_DATA_STORE()->has_status_completed( $order_id );
					$order_processing_status = WC_AM_ORDER_DATA_STORE()->has_status_processing( $order_id );

					if ( $is_api && ( $order_completed_status || ( WCAM()->get_grant_access_after_payment() && $order_processing_status ) ) ) {
						$product_title = $resource->product_title;
						$product_id    = $resource->product_id;
						$order         = WC_AM_ORDER_DATA_STORE()->get_order_object( $order_id );

						if ( WCAM()->get_wc_subs_exist() ) {
							$is_wc_sub = WC_AM_SUBSCRIPTION()->is_wc_subscription( $product_id );
						} else {
							$is_wc_sub = false;
						}

						if ( $hide_product_order_api_keys ) {
							/**
							 * Prevent duplicate Product IDs.
							 */
							$product_ids[]     = $product_id;
							$total_product_ids = array_count_values( $product_ids );

							if ( is_array( $product_ids ) && in_array( $product_id, $product_ids ) && $total_product_ids[ $product_id ] > 1 ) {
								continue; // Skip duplicates.
							}
						}

						/**
						 * Calculate activations per Product ID # for Master API Key.
						 */
						$master_api_key_resources    = WC_AM_API_RESOURCE_DATA_STORE()->get_active_api_resources( $master_api_key, $product_id );
						$total_activations_purchased = WC_AM_API_RESOURCE_DATA_STORE()->get_total_activations_purchased( $master_api_key_resources );
						$total_activations           = WC_AM_API_RESOURCE_DATA_STORE()->get_total_activations( $master_api_key_resources );
						$product_order_api_key       = WC_AM_API_RESOURCE_DATA_STORE()->get_api_resource_product_order_api_key( $order_id, $product_id );

						$is_expired           = WC_AM_ORDER_DATA_STORE()->is_time_expired( $resource->access_expires );
						$grace_period_expired = WC_AM_GRACE_PERIOD()->is_expired( $resource->api_resource_id );

						if ( $resource->sub_id != 0 ) {
							$end_date = WC_AM_SUBSCRIPTION()->has_end_date_by_sub( $resource->sub_id ) ? WC_AM_SUBSCRIPTION()->get_subscription_end_date_to_display( $order_id ) : '';

							$next_paymnent = ( WC_AM_SUBSCRIPTION()->has_next_payment_by_sub( $resource->sub_id ) ) ? date_i18n( wc_date_format(), WC_AM_SUBSCRIPTION()->get_subscription_time_by_sub_id( $resource->sub_id, 'next_payment', 'site' ) ) : __( 'Pending Cancellation on ', 'woocommerce-api-manager' ) . '<br>' . $end_date;
						} elseif ( $resource->access_expires > 0 ) {
							$next_paymnent = $is_expired && ! $grace_period_expired ? __( 'Renewable until: ', 'woocommerce-api-manager' ) . WC_AM_FORMAT()->unix_timestamp_to_date( WC_AM_GRACE_PERIOD()->get_expiration( $resource->api_resource_id ), true ) : WC_AM_FORMAT()->unix_timestamp_to_date( $resource->access_expires, true );
						} else {
							$next_paymnent = __( 'Lifetime Subscription', 'woocommerce-api-manager' );
						}

						if ( is_object( $order ) ) {
							/**
							 * WC Subscriptions Only API Keys
							 * Only display active subscriptions
							 */
							if ( WCAM()->get_wc_subs_exist() && ! empty( $resource->sub_id ) ) {
								$sub_id        = $resource->sub_id;
								$sub_order_key = $resource->sub_order_key;
								?>
                                <tr class="order">
                                    <td class="api-manager-product">
                                        <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"><?php esc_html_e( $product_title ) ?></a>
                                    </td>
                                    <td class="api-manager-product-id">
										<?php
										echo absint( $product_id );
										?>
                                    </td>
									<?php if ( ! $hide_product_order_api_keys ) { ?>
                                        <td class="api-manager-product-order-api-key">
											<?php echo esc_attr( $product_order_api_key ); ?>
                                        </td>
									<?php } ?>
                                    <td class="api-manager-activations">
										<?php
										if ( ! $hide_product_order_api_keys ) {
											echo esc_attr_e( $resource->activations_total ) . esc_html_e( ' out of ', 'woocommerce-api-manager' ) . absint( $resource->activations_purchased_total );
										} else {
											echo esc_attr_e( $total_activations ) . esc_html_e( ' out of ', 'woocommerce-api-manager' ) . absint( $total_activations_purchased );
										} ?>
                                    </td>
                                    <td class="api-manager-expire" style="white-space: nowrap;">
										<?php
										esc_html_e( $next_paymnent );
										?>
                                        <hr>
                                        <a href="<?php echo esc_url( wc_get_endpoint_url( 'view-subscription', $resource->sub_id, wc_get_page_permalink( 'myaccount' ) ) ) ?>"
                                           class="woocommerce-button button view"><?php echo esc_html_x( 'View', 'view a subscription', 'woocommerce-api-manager' ); ?></a>
                                    </td>
                                </tr>
								<?php
							} else {
								/**
								 * Non WC Subscriptions API Keys
								 */
								?>
                                <tr class="order">
                                    <td class="api-manager-product">
                                        <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"><?php esc_html_e( $product_title ) ?></a>
                                    </td>
                                    <td class="api-manager-product-id">
										<?php echo absint( $product_id ); ?>
                                    </td>
									<?php if ( ! $hide_product_order_api_keys ) { ?>
                                        <td class="api-manager-license-key">
											<?php
											if ( $is_expired && ! $grace_period_expired ) {
												esc_html_e( $next_paymnent );

												$discount = get_option( 'woocommerce_api_manager_manual_renewal_discount' );

												if ( ! empty( $discount ) ) {
													?>
                                                    <hr><?php
													esc_html_e( sprintf( __( 'At a %s discount.', 'woocommerce-api-manager' ), $discount . '%' ) );
												}
											} else {
												echo esc_attr( $product_order_api_key );
											}
											?>
                                        </td>
									<?php } ?>
                                    <td class="api-manager-activations">
										<?php
										if ( ! $hide_product_order_api_keys ) {
											echo esc_attr_e( $resource->activations_total ) . esc_html_e( ' out of ', 'woocommerce-api-manager' ) . absint( $resource->activations_purchased_total );
										} else {
											echo esc_attr_e( $total_activations ) . esc_html_e( ' out of ', 'woocommerce-api-manager' ) . absint( $total_activations_purchased );
										} ?>
                                    </td>
                                    <td class="api-manager-expire" style="white-space: nowrap;">
										<?php
										$item_quantity = 1;

										if ( $resource->refund_qty < $resource->item_qty ) {
											$item_quantity = $resource->item_qty - $resource->refund_qty;
										}

										if ( $is_expired && ! $grace_period_expired ) {
										esc_html_e( 'Expired', 'woocommerce-api-manager' );
										?>
                                        <hr>
                                        <p class="order-again">
                                            <a href="<?php echo esc_url( WC_AM_URL()->api_resource_renewal_url_my_account( $resource->api_resource_id, $product_id, $item_quantity ) ) ?>"
                                               class="button"><?php esc_html_e( 'Renew', 'woocommerce-api-manager' ) . '</a></p>';
												} elseif ( WC_AM_RENEW_SUBSCRIPTION()->is_manual_renweal_period( $resource->access_expires, $resource->api_resource_id ) ) {
												esc_html_e( $next_paymnent );
												?>
                                                <hr>
                                                <p class="order-again">
                                                    <a href="<?php echo esc_url( WC_AM_URL()->api_resource_renewal_url_my_account( $resource->api_resource_id, $product_id, $item_quantity ) ) ?>"
                                                       class="button"><?php esc_html_e( 'Renew', 'woocommerce-api-manager' ) . '</a></p>';
														} else {
															esc_html_e( $next_paymnent );
														} ?>
                                    </td>
                                </tr>
								<?php
							} // end if user subscription is active, or if non subscription API Key has API access

							$activation_data = WC_AM_API_ACTIVATION_DATA_STORE()->get_total_activations_resources_for_api_key_by_product_id( $master_api_key, $product_id );

							if ( ! empty( $activation_data ) ) {
								foreach ( $activation_data as $key => $activation_info ) {
									// Show Product Order API Key
									if ( ! $hide_product_order_api_keys && $activation_info->api_resource_id == $resource->api_resource_id ) {
										?>
                                        <tr class="api-manager-domains">
                                            <td colspan="3" style="border-right: 0; padding-left: 5em;">
												<?php
												echo '<a href="' . esc_url( WC_AM_URL()->delete_api_key_activation_my_account( $activation_info->instance, $activation_info->order_id, $activation_info->sub_parent_id, $activation_info->api_key, $activation_info->product_id, $user_id ) ) . '" style="float: left;" class="button ' . sanitize_html_class( 'delete' ) . '">' . esc_html( apply_filters( 'wc_api_manager_my_account_delete', __( 'Delete', 'woocommerce-api-manager' ) ) ) . '</a>';

												if ( filter_var( $activation_info->object, FILTER_VALIDATE_URL ) ) {
													// If $object is a URL, then remove the trailing slash.
													$obj_length = strlen( $activation_info->object );
													$object     = ! empty( $activation_info->object ) && substr( $activation_info->object, $obj_length - 1, $obj_length ) == '/' ? substr( $activation_info->object, 0, $obj_length - 1 ) : $activation_info->object;
													?>
                                                    <a style="text-align:left; vertical-align: middle; border-left: 0; padding-left: 1.5em;"
                                                       href="<?php echo esc_url( $activation_info->object ); ?>"
                                                       target="_blank"><?php esc_html_e( WC_AM_URL()->remove_url_prefix( $object ) ); ?></a><span
                                                            style="vertical-align: middle; padding-left: 1.5em;"><?php echo esc_html__( ' Activated on ', 'woocommerce-api-manager' ) . esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( $activation_info->activation_time ) ); ?></span>
													<?php
												} else { ?>
                                                    <span
                                                            style="vertical-align: middle;"><?php echo esc_html( $activation_info->object ) . esc_html__( ' Activated on ', 'woocommerce-api-manager' ) . esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( $activation_info->activation_time ) ); ?></span>
													<?php
												} ?>
                                            </td>
                                        </tr>
										<?php
										// Hide Product Order API Key
									} elseif ( $hide_product_order_api_keys ) {
										?>
                                        <tr class="api-manager-domains">
                                            <td colspan="4" style="border-right: 0; padding-left: 5em;">
												<?php
												echo '<a href="' . esc_url( WC_AM_URL()->delete_api_key_activation_my_account( $activation_info->instance, $activation_info->order_id, $activation_info->sub_parent_id, $activation_info->api_key, $activation_info->product_id, $user_id ) ) . '" style="float: left;" class="button ' . sanitize_html_class( 'delete' ) . '">' . esc_html( apply_filters( 'wc_api_manager_my_account_delete', __( 'Delete', 'woocommerce-api-manager' ) ) ) . '</a>';

												if ( filter_var( $activation_info->object, FILTER_VALIDATE_URL ) ) {
													// If $object is a URL, then remove the trailing slash.
													$obj_length = strlen( $activation_info->object );
													$object     = ! empty( $activation_info->object ) && substr( $activation_info->object, $obj_length - 1, $obj_length ) == '/' ? substr( $activation_info->object, 0, $obj_length - 1 ) : $activation_info->object;
													?>
                                                    <a style="text-align:left; vertical-align: middle; border-left: 0; padding-left: 1.5em;"
                                                       href="<?php echo esc_url( $activation_info->object ); ?>"
                                                       target="_blank"><?php esc_html_e( WC_AM_URL()->remove_url_prefix( $object ) ); ?></a><span
                                                            style="vertical-align: middle; padding-left: 1.5em;"><?php echo esc_html__( ' Activated on ', 'woocommerce-api-manager' ) . esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( $activation_info->activation_time ) ); ?></span>
													<?php
												} else { ?>
                                                    <span
                                                            style="vertical-align: middle;"><?php echo esc_html( $activation_info->object ) . esc_html__( ' Activated on ', 'woocommerce-api-manager' ) . esc_html( WC_AM_FORMAT()->unix_timestamp_to_date( $activation_info->activation_time ) ); ?></span>
													<?php
												} ?>
                                            </td>
                                        </tr>
										<?php
									}
									?>
									<?php
								} // end foreach $activation_data
								?>
								<?php
							} // end ! empty( $activation_data )
						} // end is_object( $order )
					} // end $is_api
				} // end foreach $resources
				?>
                </tbody>
            </table>
			<?php
		} else { ?>
            <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
                <a class="woocommerce-Button button"
                   href="<?php echo esc_url( apply_filters( 'wc_api_manager_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php esc_html_e( 'Go to shop', 'woocommerce-api-manager' ) ?>
                </a>
				<?php esc_html_e( 'No API products available yet.', 'woocommerce-api-manager' ); ?>
            </div>
			<?php
		} // end if $orders
	} else { ?>
        <div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
			<?php esc_html_e( 'This account has been disabled.', 'woocommerce-api-manager' ); ?>
        </div>
	<?php } // end if master API key not disabled
	// Amount of time for the API call to complete.
	//print( 'Executed in ' . round( ( microtime( true ) - $time_start ), 6 ) . ' seconds' );
} // end if user_id