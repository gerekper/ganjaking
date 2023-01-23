<?php
/**
 * WooCommerce class for handling all the interactions with updating data in WooCommerce.
 *
 * TODO: 4.1.0 This class should mostly implement and interact only with local DB and environment.
 * TODO: Ideally, when all the inventory functionality is here and tested well, then class-ns-fba-woocommerce.php.php can be deleted.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_WooCommerce' ) ) {

	/**
	 * Inventory class. For handling all API and local operations and functions working with product inventory.
	 */
	class NS_MCF_WooCommerce extends NS_MCF_Integration {

		/**
		 * Initialize the class.
		 */
		public function init() {
			// Custom column for the product post type to toggle NS FBA on/off.
			add_filter( 'manage_product_posts_columns', array( $this, 'manage_new_product_columns' ), PHP_INT_MAX, 2 );
			add_action( 'manage_product_posts_custom_column', array( $this, 'manage_new_product_custom_column' ), 10, 2 );
			add_action( 'wp_ajax_nsfba_button_actions', array( $this, 'nsfba_ajax_button_actions' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'nsfba_ajax_button_jquery' ) );

			// Add custom statuses as 'paid' to enable correct reporting.
			add_filter( 'woocommerce_order_is_paid_statuses', array( $this, 'filter_paid_order_statuses' ) );
		}

		/**
		 * Defines the custom column for FBA status on a WooCommerce product post type.
		 *
		 * @param  array $columns  The current columns.
		 *
		 * @return array
		 */
		public function manage_new_product_columns( array $columns ): array {
			// put our custom column first.
			$new_columns = array_slice( $columns, 0, 1, true ) +
				array(
					'product_fba_status column-thumb' => __( 'FBA', $this->ns_fba->text_domain ),
				) +
				array_slice( $columns, 1, count( $columns ) - 1, true );
			return $new_columns;
		}

		/**
		 * Defines the row content of the custom column for FBA status on a WooCommerce product post type
		 *
		 * @param string $column The column name.
		 * @param int    $post_id The post id.
		 */
		public function manage_new_product_custom_column( $column, $post_id ) {
			switch ( $column ) {
				case 'product_fba_status column-thumb':
					// AJAX on/off control that can be flipped by clicking it.
					$fba_status = get_post_meta( $post_id, 'ns_fba_is_fulfill', true );
					echo '<label class="switch"><input id="ns_fba_is_fulfill" name="ns_fba_is_fulfill" type="checkbox" ' . ( ( 'yes' === $fba_status ) ? 'checked value="1"' : '' ) . '"><div class="slider round"><span class="switch-on" style="min-width: 19px;">On</span><span class="switch-off" style="min-width: 19px;">Off</span></div></label>';
					break;
			}
		}

		/**
		 * Tests if the current version of WooCommerce is greater than or equal to the version passed in starting with 2.2
		 *
		 * @param string $version The WooCommerce version. Defaults to 2.2.0.
		 *
		 * @return bool
		 */
		public function is_woo_version( $version = '2.2.0' ) {
			if ( class_exists( 'WooCommerce' ) ) {
				global $woocommerce;
				if ( version_compare( $woocommerce->version, $version, '>=' ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Adds custom order statuses depending on WooCommerce version.
		 */
		public function add_custom_order_status() {
			// check for version.
			if ( $this->is_woo_version( '2.2.0' ) ) {
				// WooCommerce 2.2 and later.
				register_post_status(
					'wc-sent-to-fba',
					array(
						'label'                     => _x( 'Sent to FBA', 'Order status', 'woocommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						// translators: The total count.
						'label_count'               => _n_noop( 'Sent to FBA <span class="count">(%s)</span>', 'Sent to FBA <span class="count">(%s)</span>', 'woocommerce' ),
					)
				);
				register_post_status(
					'wc-part-to-fba',
					array(
						'label'                     => _x( 'Partial to FBA', 'Order status', 'woocommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						// translators: The total count.
						'label_count'               => _n_noop( 'Partial to FBA <span class="count">(%s)</span>', 'Partial to FBA <span class="count">(%s)</span>', 'woocommerce' ),
					)
				);
				register_post_status(
					'wc-fail-to-fba',
					array(
						'label'                     => _x( 'Failed to FBA', 'Order status', 'woocommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						// translators: The total count.
						'label_count'               => _n_noop( 'Failed to FBA <span class="count">(%s)</span>', 'Failed to FBA <span class="count">(%s)</span>', 'woocommerce' ),
					)
				);

				add_filter( 'wc_order_statuses', array( $this, 'custom_wc_order_statuses' ) );

			} else {
				// WooCommerce 2.1 and earlier.
				if ( ! term_exists( 'sent-to-fba', 'shop_order_status' ) ) {
					add_action( 'wp_loaded', array( $this, 'insert_sent' ) );
				}
				if ( ! term_exists( 'part-to-fba', 'shop_order_status' ) ) {
					add_action( 'wp_loaded', array( $this, 'insert_part' ) );
				}
				if ( ! term_exists( 'fail-to-fba', 'shop_order_status' ) ) {
					add_action( 'wp_loaded', array( $this, 'insert_fail' ) );
				}
			}
		}

		/**
		 * Adds custom order statuses to count as 'paid' statuses for reporting.
		 *
		 * @param array $statuses Other paid order statuses (processing and completed by default).
		 *
		 * @return array
		 */
		public function filter_paid_order_statuses( $statuses ) {
			return array_merge( $statuses, array( 'sent-to-fba', 'part-to-fba' ) );
		}

		/**
		 * Register custom statuses with WooCommerce 2.1 and earlier.
		 */
		public function insert_sent() {
			wp_insert_term( 'sent-to-fba', 'shop_order_status' );
		}

		/**
		 * Register custom statuses with WooCommerce.
		 */
		public function insert_part() {
			wp_insert_term( 'part-to-fba', 'shop_order_status' );
		}

		/**
		 * Register custom statuses with WooCommerce.
		 */
		public function insert_fail() {
			wp_insert_term( 'fail-to-fba', 'shop_order_status' );
		}

		/**
		 * Create Order statuses for WooCommerce 2.2+.
		 *
		 * @param array $order_statuses Current order statuses.
		 *
		 * @return array
		 */
		public function custom_wc_order_statuses( $order_statuses ) {
			$order_statuses['wc-sent-to-fba'] = _x( 'Sent to FBA', 'Order status', 'woocommerce' );
			$order_statuses['wc-part-to-fba'] = _x( 'Partial to FBA', 'Order status', 'woocommerce' );
			$order_statuses['wc-fail-to-fba'] = _x( 'Failed to FBA', 'Order status', 'woocommerce' );
			return $order_statuses;
		}

		/**
		 * Register custom statuses with WooCommerce Reporting.
		 * see line 67 /wp-content/plugins/woocommerce/includes/admin/reports/class-wc-admin-report.php.
		 *
		 * @param array $statuses The current report statuses.
		 *
		 * @return array
		 */
		public function add_custom_status_reporting( $statuses ) {
			if ( is_array( $statuses ) && in_array( 'completed', $statuses, true ) ) {
				array_push( $statuses, 'sent-to-fba' );
				array_push( $statuses, 'part-to-fba' );
				array_push( $statuses, 'fail-to-fba' );
			}
			return $statuses;
		}

		/**
		 * Add tab in WooCommerce product area.
		 *
		 * @param array $tabs The tabs.
		 *
		 * @return array
		 */
		public function woo_fba_product_tab( $tabs ) {
			$tabs['fba'] = array(
				'label'  => __( 'Amazon Fulfillment', $this->ns_fba->text_domain ),
				'target' => 'custom_product_fba_settings',
				'class'  => '',
			);
			return $tabs;
		}

		/**
		 * Add a checkbox option for FBA to the product data.
		 */
		public function custom_product_fba_panel() {
			global $woocommerce, $post;
			echo '<div id="custom_product_fba_settings" class="panel woocommerce_options_panel">';
			// Create a checkbox for fulfillment management.
			woocommerce_wp_checkbox(
				array(
					'id'    => 'ns_fba_is_fulfill',
					'label' => __( 'Fulfill with Amazon FBA', $this->ns_fba->text_domain ),
				)
			);
			// if the product is a variable product then provide an option to send the parent SKU to FBA.
			$product = wc_get_product( $post );
			if ( $product && $product->is_type( 'variable' ) ) {
				woocommerce_wp_checkbox(
					array(
						'id'    => 'ns_fba_send_parent_sku',
						'label' => __( 'Send Parent SKU to FBA instead of Variation SKU', $this->ns_fba->text_domain ),
					)
				);
			}
			echo '</div>';
		}

		/**
		 * Save our custom product setting.
		 *
		 * @param int $post_id The post id.
		 */
		public function save_custom_settings( $post_id ) {
			// save custom options.
			// phpcs:disable WordPress.Security.NonceVerification
			$is_fulfill = isset( $_POST['ns_fba_is_fulfill'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, 'ns_fba_is_fulfill', $is_fulfill );
			$is_send_parent_sku = isset( $_POST['ns_fba_send_parent_sku'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, 'ns_fba_send_parent_sku', $is_send_parent_sku );
			// phpcs:enable WordPress.Security.NonceVerification.Missing
			// to use the field values just use get_post_meta, and you are good to go!
		}

		/**
		 * Add 'Send to Amazon FBA' link to Order Actions dropdown
		 *
		 * @param array $actions  Order actions array to display.
		 *
		 * @return array
		 */
		public function add_order_meta_box_actions( array $actions ): array {
			$actions['ns_fba_send_to_fulfillment'] = __( 'Send to Amazon FBA', $this->ns_fba->text_domain );
			$actions['ns_fba_check_tracking']      = __( 'Check Amazon Tracking Info', $this->ns_fba->text_domain );
			return $actions;
		}

		/**
		 * Process the 'Send to Amazon FBA' link in Order Actions dropdown
		 *
		 * @param   WC_Order $order  The WC_Order object.
		 *
		 * @throws Exception Because exceptions.
		 */
		public function process_order_meta_box_actions( WC_Order $order ) {
			if ( $this->ns_fba->is_debug ) {
				$order_number = $order->get_order_number();
				$message      = "<h2>DEBUG on manual send to FBA order: #$order_number </h2>" .
								'<b>Date: </b>' . gmdate( 'c', time() ) . '<br /><br />' .
								'<b>Step 1 of 8: </b>INSIDE process_order_meta_box_actions - hook worked<br /><br />';
				$this->ns_fba->logger->add_entry( $message, 'debug', str_replace( '-ERROR.html', '-DEBUG.html', $this->ns_fba->err_log_path ) );
			}

			// THIS WAS CAUSING AN INFINITE LOOP BECAUSE OF DOWNSTREAM order->update_status
			// MAYBE AN ISSUE IN WC CORE WHEN A STATUS UPDATE IS TRIGGERED FROM ORDER EDIT
			// INSTEAD, WE CAN AVOID BY USING wp_set_post_terms DOWNSTREAM
			//
			// call send_fulfillment_order with the $is_manual_send = true to override the
			// "fulfill with Amazon" per product setting.

			$mcf_fulfillment = new NS_MCF_Fulfillment( $this->ns_fba );
			$mcf_fulfillment->post_fulfillment_order( $order, true );

			if ( $this->ns_fba->is_debug ) {
				$this->ns_fba->logger->add_entry( '<b>Step 7 of 8: </b>AFTER send_fulfillment_order<br /><br />', 'debug', $this->ns_fba->debug_log_path );
				$this->ns_fba->logger->add_entry( '<b>Step 8 of 8: </b>END of process_order_meta_box_actions<br /><br />', 'debug', $this->ns_fba->debug_log_path );
				$order->add_order_note( '<a href="' . $this->ns_fba->debug_log_url . '" target="_blank">' . __( 'Captured new DEBUG log', $this->ns_fba->text_domain ) . '</a> (' . __( 'click for full log', $this->ns_fba->text_domain ) . ').' );
			}
		}

		/**
		 * Add notice to order edit page.
		 */
		public function order_edit_notice() {
			// get the current order id.
			if ( isset( $_GET['post'] ) ) {
				$id = intval( $_GET['post'] );

				// handle WooCommerce versions before and after 2.2.
				$term = term_exists( 'fail-to-fba', 'shop_order_status' );

				if ( ( $term && has_term( 'fail-to-fba', 'shop_order_status', $id ) ) ||
					get_post_status( $id ) === 'wc-fail-to-fba' ) {
					?>
						<div class="error"><p>
						<?php
						esc_html_e( 'Uh-oh! The submission to Amazon Fulfillment failed.', $this->ns_fba->text_domain );
						?>
						</p></div>
					<?php
				} elseif ( ( $term && has_term( 'sent-to-fba', 'shop_order_status', $id ) ) ||
					get_post_status( $id ) === 'wc-sent-to-fba' ) {
					?>
						<div class="updated"><p>
						<?php
						esc_html_e( 'Success! The submission to Amazon Fulfillment worked!', $this->ns_fba->text_domain );
						?>
						</p></div>
					<?php
				}
			}
		}

		/**
		 * Return the active shipping methods for use in the settings.
		 */
		public function get_active_shipping_methods() {
			$zones          = array();
			$active_methods = array();

			// FIRST: Handle all configured WC shipping methods per configured zone
			// We can only do this for WC 2.6+ since Zones were NOT introduced until 2.6
			// And to make things even more fun WC 3.0 changed things even further.
			if ( $this->is_woo_version( '2.6.0' ) ) {
				// Rest of the World zone.
				$zone = new WC_Shipping_Zone();
				if ( $this->is_woo_version( '3.0' ) ) {
					$zones[ $zone->get_id() ]                     = $zone->get_data();
					$zones[ $zone->get_id() ]['shipping_methods'] = $zone->get_shipping_methods();
				} else {
					$zones[ $zone->get_zone_id() ]                     = $zone->get_data();
					$zones[ $zone->get_zone_id() ]['shipping_methods'] = $zone->get_shipping_methods();
				}

				// Add user configured zones.
				$zones = array_merge( $zones, WC_Shipping_Zones::get_zones() );

				// Go through each zone and get configured shipping methods.
				foreach ( $zones as $zone ) {
					foreach ( $zone['shipping_methods'] as $method ) {
						if ( isset( $method->enabled ) && 'yes' === $method->enabled && '' !== $method->title ) {
							array_push( $active_methods, $method->title );
						}
					}
				}
			}
			// SECOND: Handle all WC < 2.6 and third party shipping methods.
			$shipping_methods = WC()->shipping->get_shipping_methods();
			foreach ( $shipping_methods as $method ) {
				if ( isset( $method->enabled ) && 'yes' === $method->enabled && '' !== $method->title ) {
					array_push( $active_methods, $method->title );
				}
			}
			// Strip out any duplicates.
			$active_methods = array_unique( $active_methods );
			return $active_methods;
		}

		/**
		 * Simulates a payment received for testing the hook and dependencies
		 *
		 * @param int $order_id The order id.
		 */
		public function test_payment_received( $order_id ) {
			// simulate payment received for testing.
			$tmp_order = wc_get_order( $order_id );
			$tmp_order->payment_complete();
		}

		/**
		 * FBA toggle ajax.
		 */
		public function nsfba_ajax_button_actions() {
			if ( isset( $_REQUEST['security'] )
				&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'nsfba_ajax_button_actions' ) ) {
				$data    = null;
				$request = ( ! empty( $_REQUEST['req'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['req'] ) ) : '';
				$pid     = ( ! empty( $_REQUEST['pid'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['pid'] ) ) : '';
				$value   = ( ! empty( $_REQUEST['value'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['value'] ) ) : '';

				if ( 'toggle_fba' === $request ) {
					$old_value = get_post_meta( $pid, 'ns_fba_is_fulfill', true );
					$new_value = ! $old_value || 'no' === $old_value ? 'yes' : 'no';
					update_post_meta( $pid, 'ns_fba_is_fulfill', $new_value );
				}
				echo esc_html( $data );
			}
			die();
		}

		/**
		 * Inline JS handle FBA status toggle.
		 */
		public function nsfba_ajax_button_jquery() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			// Only load on required pages to avoid script conflicts.
			$plugin_screens = array( 'woocommerce_page_wc-settings', 'edit-product', 'edit-shop_order', 'shop_order' );
			if ( ! in_array( $screen_id, $plugin_screens, true ) ) {
				return;
			}

			$ajax_nonce = wp_create_nonce( 'nsfba_ajax_button_actions' );
			?>
			<script type="text/javascript">
				var ajaxurl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';	
				var ajax_nonce = '<?php echo esc_attr( $ajax_nonce ); ?>';
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

	} // class.
}
