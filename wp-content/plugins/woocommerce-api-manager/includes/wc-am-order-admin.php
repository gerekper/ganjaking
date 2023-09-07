<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce API Manager Order Admin Class
 *
 * @since       1.1.1
 *
 * @author      Todd Lahman LLC
 * @copyright   Copyright (c) Todd Lahman LLC
 * @package     WooCommerce API Manager/Order Admin
 */
class WC_AM_Order_Admin {

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * @static
	 * @return \WC_AM_Order_Admin
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'wp_ajax_wc_api_manager_delete_activation', array( $this, 'delete_activation' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save' ), 10, 2 );
		/**
		 * For WooCommerce HPOS.
		 *
		 * @since 2.5
		 */
		if ( WCAM()->is_custom_order_tables_usage_enabled() ) {
			add_filter( 'manage_edit-woocommerce_page_wc-orders_columns', array( $this, 'render_contains_api_product_column' ) );
			add_action( 'manage_woocommerce_page_wc-orders_posts_custom_column', array( $this, 'render_contains_api_product_column_content' ), 10, 2 );
		} else {
			// Non HPOS.
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'render_contains_api_product_column' ) );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_contains_api_product_column_content' ), 10, 2 );
		}
	}

	/**
	 * Adds meta boxes to Order screen.
	 *
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param string                $post_type            Post type.
	 * @param WP_Post|WC_Order|null $post_or_order_object Post object.
	 */
	public function add_meta_boxes( $post_type, $post_or_order_object ) {
		// Ensure backward compatibility if $post_or_order_object is WP_Post or WC_Order.
		$order = ( $post_or_order_object instanceof WP_Post ) ? WC_AM_ORDER_DATA_STORE()->get_order_object( $post_or_order_object->ID ) : $post_or_order_object;

		// Get "Edit Order" screen ID, which differs if HPOS is enabled.
		$order_screen_id = WCAM()->get_wc_page_screen_id( 'shop_order' );
		$current_screen  = get_current_screen();

		// Only display the meta boxes if viewing an order that contains an API Resource (product).
		if ( is_object( $order ) && $current_screen && $current_screen->id === $order_screen_id && WC_AM_ORDER_DATA_STORE()->has_api_product( $order->get_id() ) ) {
			add_meta_box( 'wc_am_master_api_key', esc_html__( 'Master API Key', 'woocommerce-api-manager' ), array( $this, 'master_api_key_meta_box' ), $order_screen_id, 'normal', 'high' );
			add_meta_box( 'wc_am_api_resource', esc_html__( 'API Resources', 'woocommerce-api-manager' ), array( $this, 'api_resource_meta_box' ), $order_screen_id, 'normal', 'high' );
			add_meta_box( 'wc_am_api_resource_activations', esc_html__( 'API Resource Activations', 'woocommerce-api-manager' ), array( $this, 'api_resource_activation_meta_box' ), $order_screen_id, 'normal', 'high' );
		}
	}

	/**
	 * Master API Key Meta Box*
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param WP_Post|WC_Order Object $post_or_order_object
	 */
	public function master_api_key_meta_box( $post_or_order_object ) {
		$order = ( $post_or_order_object instanceof WP_Post ) ? WC_AM_ORDER_DATA_STORE()->get_order_object( $post_or_order_object->ID ) : $post_or_order_object;

		if ( is_object( $order ) ) {
			if ( ! WC_AM_ORDER_DATA_STORE()->has_api_product( $order->get_id() ) ) {
				?>
                <p style="padding:0 8px;"><?php esc_html_e( 'Contains no API Product.', 'woocommerce-api-manager' ) ?></p>
				<?php
			} else {
				$user_id = WC_AM_API_RESOURCE_DATA_STORE()->get_user_id_by_order_id( $order->get_id() );

				/**
				 * Every customer must have a Master API Key, and it is missing, so create it now.
				 */
				if ( empty( WC_AM_USER()->get_master_api_key( $user_id ) ) ) {
					WC_AM_USER()->set_registration_master_key_and_status( $user_id );
				}

				$mak = WC_AM_USER()->get_master_api_key( $user_id );

				if ( ! empty( $mak ) ) {
					?>
                    <div class="api_order_licence_keys wc-metaboxes-wrapper">
						<?php
						include( WCAM()->plugin_path() . '/includes/admin/meta-boxes/html-order-master-api-key.php' );
						?>
                    </div>
					<?php
				} else {
					?>
                    <p style="padding:0 8px;"><?php esc_html_e( 'No API resources for this order.', 'woocommerce-api-manager' ) ?></p><?php
				}
			}
		}
	}

	/**
	 * API Resources Meta Box*
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param WP_Post|WC_Order Object $post_or_order_object
	 */
	public function api_resource_meta_box( $post_or_order_object ) {
		$order = ( $post_or_order_object instanceof WP_Post ) ? WC_AM_ORDER_DATA_STORE()->get_order_object( $post_or_order_object->ID ) : $post_or_order_object;

		if ( is_object( $order ) ) {
			if ( ! WC_AM_ORDER_DATA_STORE()->has_api_product( $order->get_id() ) ) {
				?>
                <p style="padding:0 8px;"><?php esc_html_e( 'Contains no API Product.', 'woocommerce-api-manager' ) ?></p>
				<?php
			} else {
				$resources             = array();
				$sub_parent_id         = 0;
				$sub_parent_order_id   = 0;
				$switched_order_id     = 0;
				$sub_resources         = array();
				$order_contains_switch = false;
				$order_screen_id       = WCAM()->get_wc_page_screen_id( 'shop_order' );

				/**
				 * Subscription resources should be displayed on the Subscription parent order only.
				 */
				if ( WCAM()->get_wc_subs_exist() ) {
					$sub_parent_id         = WC_AM_SUBSCRIPTION()->get_parent_id( $order->get_id() );
					$order_contains_switch = WC_AM_SUBSCRIPTION()->is_subscription_switch_order( $order->get_id() );

					if ( ! empty( $sub_parent_id ) && (int) $sub_parent_id == (int) $order->get_id() ) {
						// Use $sub_parent_id, since $post_id would get results only for the current post, not the parent.
						$sub_resources = WC_AM_API_RESOURCE_DATA_STORE()->get_all_api_resources_for_sub_parent_id( $sub_parent_id );
					} elseif ( $order_contains_switch ) {
						$sub_parent_order_id = WC_AM_SUBSCRIPTION()->get_sub_parent_order_id_for_related_order( $order, array( 'switch' ) );
						$sub_resources       = WC_AM_API_RESOURCE_DATA_STORE()->get_all_api_resources_for_sub_parent_id( $sub_parent_order_id );
					}
				}

				if ( ! empty( $sub_resources ) ) {
					$non_sub_resources = WC_AM_API_RESOURCE_DATA_STORE()->get_all_api_non_wc_subscription_resources_for_order_id( $order->get_id() );
					$resources         = array_merge( $non_sub_resources, $sub_resources );
				} else {
					// If WC Subs exist, but WC Subs is deactvated, the Expires field will display required.
					$resources = WC_AM_API_RESOURCE_DATA_STORE()->get_all_api_resources_for_order_id( $order->get_id() );
				}

				if ( ! empty( $resources ) ) {
					?>
                    <div class="api_order_licence_keys wc-metaboxes-wrapper">
						<?php
						$i = 0;

						foreach ( $resources as $resource ) {
							// Refreshing cache here will also delete API cache for activations about to be deleted.
							WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( $resource->order_id );

							// Delete excess API Key activations by activation resource ID.
							WC_AM_API_ACTIVATION_DATA_STORE()->delete_excess_api_key_activations_by_activation_id( $resource->activation_ids, $resource->activations_purchased_total );

							// This prevents Subscription orders that were switched away from, from displaying API Resources meant for the new Switched order.
							if ( ( $order->get_id() == $resource->order_id ) || $order_contains_switch ) {
								include( WCAM()->plugin_path() . '/includes/admin/meta-boxes/html-order-api-resources.php' );

								// Update Access Expires
								if ( empty( $resource->sub_id ) && ! empty( $resource->access_expires ) ) {
									ob_start();
									?>
                                    /* Datepicker for Access Expires */
                                    jQuery( '#wc_am_access_expires_api_resources_<?php esc_attr_e( $i ); ?>' ).datepicker({
                                    showOn: "button",
                                    buttonImage: '<?php echo esc_url( WCAM()->plugin_url() . 'includes/assets/images/calendar.gif' ) ?>',
                                    buttonImageOnly: true,
                                    buttonText: "Add More Time",
                                    dateFormat: 'yy-mm-dd',
                                    numberOfMonths: 1,
                                    showButtonPanel: true,
                                    minDate: '<?php esc_attr_e( WC_AM_FORMAT()->unix_timestamp_to_calendar_date( $resource->access_expires ) ) ?>',
                                    onSelect: function(datetext) {
                                    var d = new Date(); // for now

                                    var h = d.getHours();
                                    h = (h < 10) ? ("0" + h) : h ;

                                    var m = d.getMinutes();
                                    m = (m < 10) ? ("0" + m) : m ;

                                    var s = d.getSeconds();
                                    s = (s < 10) ? ("0" + s) : s ;

                                    datetext = datetext + " " + h + ":" + m + ":" + s;

                                    jQuery( 'input[name="access_expires_<?php esc_attr_e( $i ); ?>"]' ).val(datetext);
                                    jQuery( 'input[name="new_access_expires_<?php esc_attr_e( $i ); ?>"]' ).val(datetext);
                                    }
                                    });
									<?php
									$javascript = ob_get_clean();
									WCAM()->wc_am_print_js( $javascript );
								}

								$i ++;
							}
						}
						?>
                    </div>
					<?php
					/**
					 * Javascript
					 */
					ob_start();
					?>
                    /**
                    * Expand API Key Text Input on mouseover
                    */
                    jQuery('.am_expand_text_box').mouseenter(function(){
                    var $this = jQuery(this);
                    if (!$this.data('expand')) {
                    $this.data('expand', true);
                    $this.animate({width:'+=140',left:'-=6px'}, 'linear');
                    $this.siblings('.s').animate({width:'-=140',left:'+=6px'}, 'linear')
                    }
                    $this.focus();
                    $this.select();
                    }).mouseleave(function(){
                    var $this = jQuery(this);
                    $this.data('expand', false);
                    $this.animate({width:'-=140',left:'+=6px'}, 'linear');
                    $this.siblings('.s').animate({width:'+=140',left:'-=6px'}, 'linear')
                    });

					<?php
					$javascript = ob_get_clean();
					WCAM()->wc_am_print_js( $javascript );
				} else {
					if ( WCAM()->get_wc_subs_exist() ) {
						$sub_parent_order_id = WC_AM_SUBSCRIPTION()->get_sub_parent_order_id_for_related_order( $order, array( 'renewal' ) );
						$switched_order_id   = WC_AM_SUBSCRIPTION()->get_last_swtiched_subscription_order_id( WC_AM_SUBSCRIPTION()->get_subscription_id( $sub_parent_order_id ) );
						$url_id              = ! empty( $switched_order_id ) ? $switched_order_id : $sub_parent_order_id;
					}

					if ( ! empty( $sub_parent_order_id ) ) {
						if ( $order_screen_id == 'woocommerce_page_wc-orders' ) {
							printf( esc_html__( 'See Parent Order %s%s', 'woocommerce-api-manager' ), '<a href="' . esc_url( self_admin_url() . 'admin.php?page=wc-orders&action=edit&id=' . $url_id ) . '">', ' #' . esc_attr( $url_id ) . '</a>' );
						} else {
							printf( esc_html__( 'See Parent Order %s%s', 'woocommerce-api-manager' ), '<a href="' . esc_url( self_admin_url() . 'post.php?action=edit&post=' . $url_id ) . '">', ' #' . esc_attr( $url_id ) . '</a>' );
						}
					} else {
						?>
                        <p style="padding:0 8px;"><?php esc_html_e( 'No API resources for this order.', 'woocommerce-api-manager' ) ?></p><?php
					}
				}
			}
		}
	}

	/**
	 * API Resources Meta Box*
	 *
	 * @since   2.0
	 * @updated 2.5 For WooCommerce HPOS.
	 *
	 * @param WP_Post|WC_Order Object $post_or_order_object
	 */
	public function api_resource_activation_meta_box( $post_or_order_object ) {
		$order = ( $post_or_order_object instanceof WP_Post ) ? WC_AM_ORDER_DATA_STORE()->get_order_object( $post_or_order_object->ID ) : $post_or_order_object;

		if ( is_object( $order ) ) {
			if ( ! WC_AM_ORDER_DATA_STORE()->has_api_product( $order->get_id() ) ) {
				?>
                <p style="padding:0 8px;"><?php esc_html_e( 'Contains no API Product.', 'woocommerce-api-manager' ) ?></p>
				<?php
			} else {
				$resources             = WC_AM_API_ACTIVATION_DATA_STORE()->get_activation_resources_by_order_id( $order->get_id() );
				$order_contains_switch = ! empty( $resources[ 0 ]->sub_item_id ) && WC_AM_SUBSCRIPTION()->is_subscription_switch_order( $order->get_id() );

				/**
				 * Subscription activations should be displayed on the Subscription parent, or Switched Subscription, order only.
				 */
				if ( ! empty( $resources[ 0 ]->sub_parent_id ) && ! $order_contains_switch && $resources[ 0 ]->sub_parent_id != $order->get_id() ) {
					?>
                    <p style="padding:0 8px;"><?php esc_html_e( 'No activations yet.', 'woocommerce-api-manager' ) ?></p>
					<?php
				} elseif ( ! empty( $resources ) ) {
					include( WCAM()->plugin_path() . '/includes/admin/meta-boxes/html-order-api-activations.php' );
					/**
					 * Delete Activation Javascript
					 */
					ob_start();
					?>
                    jQuery( '#activations-table' ).on( 'click', 'button.delete_api_key', function( e ){
                    e.preventDefault();

                    var answer = confirm('<?php echo esc_js( __( 'Are you sure you want to delete this activation?', 'woocommerce-api-manager' ) ); ?>');

                    if ( answer ){
                    var el              = jQuery( this ).parent().parent();
                    var instance        = jQuery( this ).attr( 'instance' );
                    var order_id        = jQuery( this ).attr( 'order_id' );
                    var sub_parent_id   = jQuery( this ).attr( 'sub_parent_id' );
                    var api_key         = jQuery( this ).attr( 'api_key' );
                    var product_id      = jQuery( this ).attr( 'product_id' );
                    var user_id         = jQuery( this ).attr( 'user_id' );

                    if ( instance ) {
                    jQuery(el).block({
                    message: null,
                    overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                    }
                    });

                    var data = {
                    action:         'wc_api_manager_delete_activation',
                    instance:       instance,
                    order_id:       order_id,
                    sub_parent_id:  sub_parent_id,
                    api_key:        api_key,
                    product_id:     product_id,
                    user_id:        user_id,
                    security:       '<?php esc_attr_e( wp_create_nonce( "am-delete-activation" ) ); ?>'
                    };

                    jQuery.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', data, function( response ) {
                    // Success
                    jQuery(el).fadeOut('300', function(){
                    jQuery(el).remove();
                    });

                    location.reload(true);
                    });

                    } else {
                    jQuery( el ).fadeOut('300', function(){
                    jQuery( el ).remove();
                    });
                    }

                    }

                    return false;
                    });

					<?php
					$javascript = ob_get_clean();
					WCAM()->wc_am_print_js( $javascript );
				} else {
					?>
                    <p style="padding:0 8px;"><?php esc_html_e( 'No activations yet.', 'woocommerce-api-manager' ) ?></p>
					<?php
				}
			}
		}
	}

	/**
	 * Delete activation using the Delete button in API Resources Activations meta box.
	 *
	 * @since 2.0
	 */
	public function delete_activation() {
		check_ajax_referer( 'am-delete-activation', 'security' );

		$this_post = wc_clean( $_POST );

		// Delete activation.
		WC_AM_API_ACTIVATION_DATA_STORE()->delete_api_key_activation_by_instance_id( $this_post[ 'instance' ] );

		/**
		 * Delete cache.
		 *
		 * @since 2.1.7
		 */
		$admin_resources = array(
			'instance'      => $this_post[ 'instance' ],
			'order_id'      => $this_post[ 'order_id' ],
			'sub_parent_id' => $this_post[ 'sub_parent_id' ],
			'api_key'       => $this_post[ 'api_key' ],
			'product_id'    => $this_post[ 'product_id' ],
			'user_id'       => $this_post[ 'user_id' ]
		);

		WC_AM_SMART_CACHE()->delete_cache( wc_clean( array( 'admin_resources' => $admin_resources ) ), true );

		WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( (int) $this_post[ 'order_id' ] );

		wp_die();
	}

	/**
	 * Save the data from the API Resources meta box
	 *
	 * @since 2.0
	 *
	 * @param int    $post_id
	 * @param object $post
	 *
	 * @throws \Exception
	 */
	public function save( $post_id, $post ) {
		global $wpdb;

		$this_post = wc_clean( $_POST );

		if ( isset( $this_post[ 'activations_purchased_total' ] ) && isset( $this_post[ 'product_id' ] ) && isset( $this_post[ 'product_order_api_key' ] ) && isset( $this_post[ 'current_activations_purchased_total' ] ) ) {
			$product_order_api_key               = $this_post[ 'product_order_api_key' ];
			$activations_purchased_total         = $this_post[ 'activations_purchased_total' ];
			$current_activations_purchased_total = $this_post[ 'current_activations_purchased_total' ];
			$max_loop                            = max( array_keys( $product_order_api_key ) );

			for ( $i = 0; $i <= $max_loop; $i ++ ) {
				if ( ! isset( $product_order_api_key[ $i ] ) ) {
					continue;
				}

				$product_id = (int) $this_post[ 'product_id' ][ $i ];

				if ( $activations_purchased_total[ $i ] > $current_activations_purchased_total[ $i ] ) {
					//$item_quanity_and_refund_quantity = WC_AM_API_RESOURCE_DATA_STORE()->get_item_quantity_and_refund_quantity_by_order_id_and_product_id( $post_id, $product_id );
					//$quanity_total                    = absint( $item_quanity_and_refund_quantity->item_qty - $item_quanity_and_refund_quantity->refund_qty );

					$data = array(
						'activations_purchased'       => ! empty( $activations_purchased_total[ $i ] ) ? $activations_purchased_total[ $i ] : apply_filters( 'wc_api_manager_custom_default_api_activations', 1, $product_id ),
						'activations_purchased_total' => ! empty( $activations_purchased_total[ $i ] ) ? $activations_purchased_total[ $i ] : apply_filters( 'wc_api_manager_custom_default_api_activations', 1, $product_id )
					);

					$where = array(
						'order_id'   => $post_id,
						'product_id' => $product_id
					);

					$data_format = array(
						'%d',
						'%d'
					);

					$where_format = array(
						'%d',
						'%d'
					);

					$wpdb->update( $wpdb->prefix . WC_AM_USER()->get_api_resource_table_name(), $data, $where, $data_format, $where_format );
				}

				/**
				 * Update access_expires
				 *
				 * @since  2.4
				 * @update 2.6.5 to calculate new access expires according to minutes and seconds that match order created time.
				 * @update 3.1 Change key names for access_expires_before_change_ and new_access_expires_ to match new hidden inputs in form.
				 */
				if ( ! empty( $this_post[ 'access_expires_before_change_' . $i ] ) && ! empty( $this_post[ 'new_access_expires_' . $i ] ) ) {
					$new_access_expires               = WC_AM_FORMAT()->date_to_unix_timestamp_with_timezone_offset( $this_post[ 'new_access_expires_' . $i ] );
					$current_access_expires_timestamp = WC_AM_FORMAT()->date_to_unix_timestamp_with_no_timezone_offset( $this_post[ 'access_expires_before_change_' . $i ] );
					$order_created_time               = WC_AM_ORDER_DATA_STORE()->get_order_time_to_epoch_time_stamp( $post_id );

					if ( $current_access_expires_timestamp != $order_created_time && $new_access_expires > $current_access_expires_timestamp ) {
						$data = array(
							'access_expires' => ( ( absint( $new_access_expires / DAY_IN_SECONDS ) - absint( $order_created_time / DAY_IN_SECONDS ) ) * DAY_IN_SECONDS ) + $order_created_time
						);

						$where = array(
							'order_id'   => $post_id,
							'product_id' => $product_id,
							'sub_id'     => 0
						);

						$data_format = array(
							'%d'
						);

						$where_format = array(
							'%d',
							'%d',
							'%d'
						);

						$wpdb->update( $wpdb->prefix . WC_AM_USER()->get_api_resource_table_name(), $data, $where, $data_format, $where_format );
					}
				}
			}

			WC_AM_SMART_CACHE()->delete_activation_api_cache_by_order_id( $post_id );
			WC_AM_SMART_CACHE()->refresh_cache_by_order_id( $post_id, false );
		}
	}

	/**
	 * Add a column to the WooCommerce Orders admin screen to indicate whether an order contains an API Product.
	 *
	 * @since 2.1.2
	 *
	 * @param array $columns The current list of columns
	 *
	 * @return array
	 */
	public function render_contains_api_product_column( $columns ) {
		$column_header = '<span class="api_product_head tips" data-tip="' . esc_attr__( 'Contains API Product', 'woocommerce-api-manager' ) . '">' . esc_attr__( 'API Product', 'woocommerce-api-manager' ) . '</span>';
		$new_columns   = WC_AM_ARRAY()->array_insert_after( 'order_status', $columns, 'api_product', $column_header );

		return $new_columns;
	}

	/**
	 * Add a column to the WooCommerce Orders admin screen to indicate whether an order contains an API Product.
	 *
	 * @since 2.1.2
	 *
	 * @param string $column The string of the current column
	 * @param int    $post_id
	 */
	public function render_contains_api_product_column_content( $column, $post_id ) {
		if ( 'api_product' == $column ) {
			if ( WC_AM_ORDER_DATA_STORE()->has_api_product( $post_id ) ) {
				$has_activations = WC_AM_API_ACTIVATION_DATA_STORE()->has_activations_for_order_id( $post_id );

				if ( $has_activations ) {
					echo '<span class="api_product_order_has_activations tips" data-tip="' . esc_attr__( 'Has activations.', 'woocommerce-api-manager' ) . '"></span>';
				} else {
					echo '<span class="api_product_order_no_activations tips" data-tip="' . esc_attr__( 'No activations.', 'woocommerce-api-manager' ) . '"></span>';
				}
			} else {
				echo '<span class="normal_order">&ndash;</span>';
			}
		}
	}

} // End of class