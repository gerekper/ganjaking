<?php
/**
 * Helper class for extending WooCommerce to support the Amazon Integration
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 2.0.0
 */

if ( ! class_exists( 'NS_FBA_WooCommerce' ) ) {

	class NS_FBA_WooCommerce {

		private $ns_fba;

		function __construct( $ns_fba ) {
			// local reference to the main ns_fba object
			$this->ns_fba = $ns_fba;

			// Custom column for the product post type to toggle NS FBA on/off
			add_filter( 'manage_product_posts_columns', array( $this, 'manage_new_product_columns' ), PHP_INT_MAX, 2 );
			add_action( 'manage_product_posts_custom_column', array( $this, 'manage_new_product_custom_column' ), 10, 2 );
			add_action( 'wp_ajax_nsfba_button_actions', array( $this, 'nsfba_ajax_button_actions' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'nsfba_ajax_button_jquery' ) );
		}

		/**
		 * Defines the custom column for FBA status on a WooCommerce product post type
		 *
		 * @param string $columns
		 */

		function manage_new_product_columns( $columns ) {
			// put our custom column first
			$new_columns = array_slice( $columns, 0, 1, true ) +
		    	array(
					'product_fba_status column-thumb' => __( 'FBA', $this->ns_fba->text_domain ),
				) +
		    	array_slice( $columns, 1, count( $columns ) -1, true );
			return $new_columns;
		}

		/**
		 * Defines the row content of the custom column for FBA status on a WooCommerce product post type
		 *
		 * @param string $columns
		 */

		function manage_new_product_custom_column( $column, $post_id ) {
			switch ( $column ) {
				case 'product_fba_status column-thumb' :
					//AJAX on/off control that can be flipped by clicking it
					$fba_status = get_post_meta( $post_id, 'ns_fba_is_fulfill', true );
					echo '<label class="switch"><input id="ns_fba_is_fulfill" name="ns_fba_is_fulfill" type="checkbox" ' . ( ( $fba_status == 'yes' ) ? 'checked value="1"' : '' ) . '"><div class="slider round"><span class="switch-on" style="min-width: 19px;">On</span><span class="switch-off" style="min-width: 19px;">Off</span></div></label>';
					break;
			}
		}

		/**
		 * Tests if the current version of WooCommerce is greater than or equal to the version passed in starting with 2.2
		 *
		 * @param string $version
		 */
		function is_woo_version( $version = '2.2.0' ) {
			if ( class_exists( 'WooCommerce' ) ) {
				global $woocommerce;
				if ( version_compare( $woocommerce->version, $version, '>=' ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Adds custom order statuses depending on WooCommerce version
		 */
		function add_custom_order_status() {
			// check for version
			if ( $this->is_woo_version( '2.2.0' ) ) {
				// WooCommerce 2.2 and later
				register_post_status( 'wc-sent-to-fba', array(
					'label'                     => _x( 'Sent to FBA', 'Order status', 'woocommerce' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Sent to FBA <span class="count">(%s)</span>', 'Sent to FBA <span class="count">(%s)</span>', 'woocommerce' ),
				) );
				register_post_status( 'wc-part-to-fba', array(
					'label'                     => _x( 'Partial to FBA', 'Order status', 'woocommerce' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Partial to FBA <span class="count">(%s)</span>', 'Partial to FBA <span class="count">(%s)</span>', 'woocommerce' ),
				) );
				register_post_status( 'wc-fail-to-fba', array(
					'label'                     => _x( 'Failed to FBA', 'Order status', 'woocommerce' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Failed to FBA <span class="count">(%s)</span>', 'Failed to FBA <span class="count">(%s)</span>', 'woocommerce' ),
				) );

				add_filter( 'wc_order_statuses', array( $this, 'custom_wc_order_statuses' ) );

			} else {
				// WooCommerce 2.1 and earlier
				if ( ! term_exists( 'sent-to-fba', 'shop_order_status' ) ) {
					add_action( 'wp_loaded', array( $this, 'ns_fba_insert_sent' ) );
				}
				if ( ! term_exists( 'part-to-fba', 'shop_order_status' ) ) {
					add_action( 'wp_loaded', array( $this, 'ns_fba_insert_part' ) );
				}
				if ( ! term_exists( 'fail-to-fba', 'shop_order_status' ) ) {
					add_action( 'wp_loaded', array( $this, 'ns_fba_insert_fail' ) );
				}
			}// End if().
		}

		/**
		 * Register custom statuses with WooCommerce
		 */

		// for WooCommerce 2.1 and earlier
		function ns_fba_insert_sent() {
			wp_insert_term( 'sent-to-fba', 'shop_order_status' );
		}

		function ns_fba_insert_part() {
			wp_insert_term( 'part-to-fba', 'shop_order_status' );
		}

		function ns_fba_insert_fail() {
			wp_insert_term( 'fail-to-fba', 'shop_order_status' );
		}

		// for WooCommerce 2.2+
		function custom_wc_order_statuses( $order_statuses ) {
			$order_statuses['wc-sent-to-fba'] = _x( 'Sent to FBA', 'Order status', 'woocommerce' );
			$order_statuses['wc-part-to-fba'] = _x( 'Partial to FBA', 'Order status', 'woocommerce' );
			$order_statuses['wc-fail-to-fba'] = _x( 'Failed to FBA', 'Order status', 'woocommerce' );
			return $order_statuses;
		}

		/**
		 * Register custom statuses with WooCommerce Reporting
		 * see line 67 /wp-content/plugins/woocommerce/includes/admin/reports/class-wc-admin-report.php
		 */
		function add_custom_status_reporting( $statuses ) {
			if ( is_array( $statuses ) && in_array( 'completed', $statuses ) ) {
				array_push( $statuses, 'sent-to-fba' );
				array_push( $statuses, 'part-to-fba' );
				array_push( $statuses, 'fail-to-fba' );
			}
			return $statuses;
		}

		function woo_fba_product_tab( $tabs ) {
			//error_log( 'in woo_fba_product_tab filter' );
			$tabs['fba'] = array(
				'label' 	=> __( 'Amazon Fulfillment', $this->ns_fba->text_domain ),
				'target' 	=> 'custom_product_fba_settings',
				'class' 	=> '',
			);
			return $tabs;
		}

		/**
		 * Add a checkbox option for FBA to the product data
		 */
		function custom_product_fba_panel() {
			global $woocommerce, $post;
			echo '<div id="custom_product_fba_settings" class="panel woocommerce_options_panel">';
			// Create a checkbox for fulfillment management
			woocommerce_wp_checkbox(
				array(
					'id'            => 'ns_fba_is_fulfill',
					'label'         => __( 'Fulfill with Amazon FBA', $this->ns_fba->text_domain ),
				)
			);
			// if the product is a variable product then provide an option to send the parent SKU to FBA
			$product = wc_get_product( $post );
			if ( $product->is_type( 'variable' ) ) {
				woocommerce_wp_checkbox(
					array(
						'id'            => 'ns_fba_send_parent_sku',
						'label'         => __( 'Send Parent SKU to FBA instead of Variation SKU', $this->ns_fba->text_domain ),
					)
				);
			}
			echo '</div>';
		}

		/**
		 * Save our custom product setting
		 */
		function save_custom_settings( $post_id ) {
			// save custom options
			$is_fulfill = isset( $_POST['ns_fba_is_fulfill'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, 'ns_fba_is_fulfill', $is_fulfill );
			$is_send_parent_sku = isset( $_POST['ns_fba_send_parent_sku'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, 'ns_fba_send_parent_sku', $is_send_parent_sku );
			// to use the field values just use get_post_meta, and you are good to go!
		}

		/**
		 * Add 'Send to Amazon FBA' link to Order Actions dropdown
		 *
		 * @param array $actions order actions array to display
		 * @return array
		 */
		public function add_order_meta_box_actions( $actions ) {
			$actions['ns_fba_send_to_fulfillment'] = __( 'Send to Amazon FBA', $this->ns_fba->text_domain );
			return $actions;
		}

		/**
		 * Process the 'Send to Amazon FBA' link in Order Actions dropdown
		 *
		 * @param object $order \WC_Order object
		 */

		// TODO: UPDATE and REMOVE class dependencies that are not passed in
		public function process_order_meta_box_actions( $order ) {
			if ( $this->ns_fba->is_debug ) {
				$order_number = $order->get_order_number();
				$message = "<h2>DEBUG on manual send to FBA order: #$order_number </h2>" .
				              '<b>Date: </b>' . date( 'c', time() ) . '<br /><br />' .
				              '<b>Step 1 of 8: </b>INSIDE process_order_meta_box_actions - hook worked<br /><br />';
				error_log( $message, 3, str_replace( '-ERROR.html', '-DEBUG.html', $this->ns_fba->err_log_path ) );
			}

			// THIS WAS CAUSING AN INFINITE LOOP BECAUSE OF DOWNSTREAM order->update_status
			// MAYBE AN ISSUE IN WC CORE WHEN A STATUS UPDATE IS TRIGGERED FROM ORDER EDIT
			// INSTEAD, WE CAN AVOID BY USING wp_set_post_terms DOWNSTREAM
			//
			// call send_fulfillment_order with the $is_manual_send = true to override the
			// "fulfill with Amazon" per product setting

			$this->ns_fba->outbound->send_fulfillment_order( $order->get_id(), true );

			if ( $this->ns_fba->is_debug ) {
				error_log( '<b>Step 7 of 8: </b>AFTER send_fulfillment_order<br /><br />', 3, $this->ns_fba->debug_log_path );
				error_log( '<b>Step 8 of 8: </b>END of process_order_meta_box_actions<br /><br />', 3, $this->ns_fba->debug_log_path );
				$order->add_order_note( '<a href="' . $this->ns_fba->debug_log_url . '" target="_blank">' . __( 'Captured new DEBUG log', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . ').' );
			}
		}

		function ns_fba_order_edit_notice() {
			// get the current order id
			if ( isset( $_GET['post'] ) ) {
				$id = $_GET['post'];

				// handle WooCommerce versions before and after 2.2
				$term = term_exists( 'fail-to-fba', 'shop_order_status' );

				if ( ( $term && has_term( 'fail-to-fba', 'shop_order_status', intval( $id ) ) ) ||
				     get_post_status( $id ) == 'wc-fail-to-fba' ) {
					_e( '<div class="error"><p>Uh-oh! The submission to Amazon Fulfillment failed.</p></div>', $this->ns_fba->text_domain );
				} elseif ( ( $term && has_term( 'sent-to-fba', 'shop_order_status', intval( $id ) ) ) ||
				           get_post_status( $id ) == 'wc-sent-to-fba' ) {
					_e( '<div class="updated"><p>Success! The submission to Amazon Fulfillment worked!</p></div>', $this->ns_fba->text_domain );
				}
			}
		}

		/**
		 * Gets WC_Product by SKU
		 *
		 * @param 	string 	$sku
		 */
		function get_product_by_sku( $sku ) {
			$product_id = wc_get_product_id_by_sku( $sku );
			if ( $product_id ) {
				return wc_get_product( $product_id );
			}
			return null;
		}

		/**
		 * Return the active shipping methods for use in the settings
		 */
		function get_active_shipping_methods() {
			$zones = array();
			$active_methods = array();

			// FIRST: Handle all configured WC shipping methods per configured zone
			// 		  We can only do this for WC 2.6+ since Zones were NOT introduced until 2.6
			//		  And to make things even more fun WC 3.0 changed things even further
			if ( $this->is_woo_version( '2.6.0' ) ) {
				// Rest of the World zone
				$zone                                                    = new WC_Shipping_Zone();
				if ( $this->is_woo_version( '3.0' ) ) {
					$zones[ $zone->get_id() ]                            = $zone->get_data();
					$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods();
				} else {
					$zones[ $zone->get_zone_id() ]                       = $zone->get_data();
					$zones[ $zone->get_zone_id() ]['shipping_methods']   = $zone->get_shipping_methods();
				}

				// Add user configured zones
				$zones = array_merge( $zones, WC_Shipping_Zones::get_zones() );

				// Go through each zone and get configured shipping methods
				foreach ( $zones as $zone ) {
					foreach ( $zone['shipping_methods'] as $method ) {
						if ( isset( $method->enabled ) && 'yes' === $method->enabled && $method->title !== '' ) {
							array_push( $active_methods, $method->title );
						}
					}
				}
			}
			// SECOND: Handle all WC < 2.6 and third party shipping methods
			$shipping_methods = WC()->shipping->get_shipping_methods();
			foreach ( $shipping_methods as $method ) {
				if ( isset( $method->enabled ) && 'yes' === $method->enabled && $method->title !== '' ) {
					array_push( $active_methods, $method->title );
				}
			}
			// Strip out any duplicates
			$active_methods = array_unique( $active_methods );
			return $active_methods;
		}

		/**
		 * Simulates a payment received for testing the hook and dependencies
		 *
		 * @param int $order_id
		 */
		function test_payment_received( $order_id ) {
			// simulate payment received for testing
			$tmp_order = new WC_Order( $order_id );
			$order->payment_complete();
		}

		// add the php handling of the ajax fba toggle

		function nsfba_ajax_button_actions() {
			//check_ajax_referer( 'nsfba_button_actions', 'security' );
			//error_log ( 'Past security Request = ' . $_REQUEST['req'] );
		 	if ( isset( $_REQUEST ) && $_REQUEST['req'] == 'toggle_fba' ) {
		 		$data = null;
		        $request = ( ! empty( $_REQUEST['req'] )) ? $_REQUEST['req'] : '';
		        $pid = ( ! empty( $_REQUEST['pid'] )) ? $_REQUEST['pid'] : '';
		        $value = ( ! empty( $_REQUEST['value'] )) ? $_REQUEST['value'] : '';
		        //error_log( 'pid = '. $pid );
		        if ( $request == 'toggle_fba' ) {
		        	if ( get_post_meta( $pid, 'ns_fba_is_fulfill', true ) == 'no' ) {
				 		//error_log('Toggle #' . $pid . ' FBA On');
						update_post_meta( $pid, 'ns_fba_is_fulfill', 'yes' );
		        	} else {
		        		//error_log('Toggle #' . $pid . ' FBA Off');
		        		update_post_meta( $pid, 'ns_fba_is_fulfill', 'no' );
		        	}
		        }
		        echo $data;
		    }
			die();
		}

		// Add the js handling for the fba_status toggle

		function nsfba_ajax_button_jquery() {
			$ajax_nonce = wp_create_nonce( 'nsfba_ajax_button_actions' );
		?>
			<script type="text/javascript">
				var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';	
				var ajax_nonce = '<?php echo $ajax_nonce ?>';
				//console.log( ajaxurl );						
				(function($) {	
					jQuery('.slider').on( 'click', function() {
						var data = {
							action: 'nsfba_button_actions',
							security: ajax_nonce,				  
							req: 'toggle_fba',
							pid: jQuery(this).closest('tr').attr('id').replace(/post-/, '')
						};
						jQuery.post( ajaxurl, data, function( response ) {
							//console.log( response );
						});							
					});
				})(jQuery);
			</script>
		<?php
		}
	}
}// End if().
