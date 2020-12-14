<?php

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class WC_MS_Order {

	private $wcms;

	public function __construct( WC_Ship_Multiple $wcms ) {
		$this->wcms = $wcms;

		// Update package status
		add_action( 'wp_ajax_wcms_update_package_status', array( $this, 'update_package_status' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'update_package_on_completed_order' ) );

		// Order page shipping address override
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'override_order_shipping_address' ) );

		// Compatibility action for displaying order shipping packages
		add_action( 'wcms_order_shipping_packages_table', array( $this, 'display_order_shipping_addresses' ), 10, 2 );

		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'show_multiple_addresses_line' ), 1 );

		// meta box
		add_action( 'add_meta_boxes', array( $this, 'order_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'update_order_addresses' ), 10, 2 );
		add_action( 'woocommerce_saved_order_items', array( $this, 'update_order_taxes' ), 1, 2 );

		add_filter( 'woocommerce_order_get_items', array( $this, 'order_item_taxes' ), 30, 2 );

		// Hide metadata in order line items.
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_item_meta' ) );

		// WC PIP
		add_filter( 'woocommerce_pip_template_body', array( $this, 'pip_template_body' ), 10, 3 );
	}

	public function update_package_status() {
		$pkg_idx  = $_POST['package'];
		$order    = $_POST['order'];
		$packages = get_post_meta( $order, '_wcms_packages', true );
		$email    = $_POST['email'];

		foreach ( $packages as $x => $package ) {
			if ( $x == $pkg_idx ) {
				$packages[ $x ]['status'] = $_POST['status'];

				if ( $_POST['status'] == 'Completed' && $email ) {
					self::send_package_email( $order, $pkg_idx );
				}

				break;
			}
		}

		update_post_meta( $order, '_wcms_packages', $packages );

		die( $_POST['status'] );

	}

	public function update_package_on_completed_order( $order_id ) {
		$packages = get_post_meta( $order_id, '_wcms_packages', true );

		if ( $packages ) {
			foreach ( $packages as $x => $package ) {
				$packages[ $x ]['status'] = 'Completed';
			}

			update_post_meta( $order_id, '_wcms_packages', $packages );
		}
	}

	public function override_order_shipping_address( $order ) {

		$order_id = WC_MS_Compatibility::get_order_prop( $order, 'id' );
		$packages  = get_post_meta( $order_id, '_wcms_packages', true );
		$multiship = get_post_meta( $order_id, '_multiple_shipping', true );

		if ( ( ! $order->get_formatted_shipping_address() && ( is_array( $packages ) && count( $packages ) > 1 ) ) || 'yes' === $multiship ) :
		?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					var $order_data = $( 'div.order_data_column' ).eq( 2 );
					$order_data.find( 'a.edit_address' ).remove();
					$order_data.find( 'div.address' ).html( '<a href="#wc_multiple_shipping"><?php _e( 'Ships to multiple addresses', 'wc_shipping_multiple_address' ); ?></a>' );
				} );
			</script>
		<?php
		endif;
	}

	public function display_order_shipping_addresses( $order, $email = false ) {

		if ( $order instanceof WC_Order ) {
			$order_id   = WC_MS_Compatibility::get_order_prop( $order, 'id' );
		} else {
			$order_id = $order;
			$order = wc_get_order( $order );
		}

		if ( false == apply_filters( 'wcms_list_order_item_addresses', true, $order_id ) )
			return;

		$package_items     = get_post_meta( $order_id, '_packages_item_ids', true );
		$methods           = get_post_meta( $order_id, '_shipping_methods', true );
		$packages          = get_post_meta( $order_id, '_wcms_packages', true );
		$available_methods = $order->get_shipping_methods();

		if ( ! $packages || count( $packages ) == 1 ) {
			return;
		}

		// Get all the order items (and generate a unique key for each)
		$order_items = $order->get_items();
		$cart_item_keys = array();
		foreach ( $order_items as $item_id => $item ) {
			if ( ! empty( $item['wcms_cart_key'] ) ) {
				$cart_item_keys[ $item['wcms_cart_key'] ] = $item_id;
			}
		}

		if ( $email ) {
			$table_style = ' cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee"';
			$th_style    = ' scope="col" style="text-align:left; border: 1px solid #eee;"';
			$td_style    = ' style="text-align:left; vertical-align:middle; border: 1px solid #eee;"';
		} else {
			$table_style = '';
			$th_style    = '';
			$td_style    = '';
		}
	?>

		<p><strong><?php _e( 'This order ships to multiple addresses.', 'wc_shipping_multiple_address' ); ?></strong></p>
		<table class="shop_table shipping_packages"<?php echo $table_style; ?>>
			<thead>
				<tr>
					<th<?php echo $th_style; ?>><?php _e( 'Products', 'wc_shipping_multiple_address' ); ?></th>
					<th<?php echo $th_style; ?>><?php _e( 'Address', 'wc_shipping_multiple_address' ); ?></th>
					<?php do_action( 'wc_ms_shop_table_head' ); ?>
					<th<?php echo $th_style; ?>><?php _e( 'Notes', 'wc_shipping_multiple_address' ); ?></th>
				</tr>
			</thead>
			<tbody>

	<?php

		foreach ( $packages as $x => $package ) {
			$products = $package['contents'];
			$method   = $methods[ $x ]['label'];

			foreach ( $available_methods as $ship_method ) {
				if ( $ship_method->get_method_id() . ':' . $ship_method->get_instance_id() === $methods[ $x ]['id']
					|| $ship_method->get_method_id() === $methods[ $x ]['id']
				) {
					$method = $ship_method->get_name();
					break;
				}
			}

			$address = empty( $package['destination'] ) ? '' : WC()->countries->get_formatted_address( $package['destination'] );

			// Products
			echo '<tr><td' . $td_style . '><ul>';

			foreach ( $products as $i => $product ) {

				// Get a matching order item
				$item = false;
				if ( ! empty( $product['cart_key'] ) && ! empty( $cart_item_keys[ $product['cart_key'] ] ) && isset( $order_items[ $cart_item_keys[ $product['cart_key'] ] ] ) ) {
					$item = $order_items[ $cart_item_keys[ $product['cart_key'] ] ];
				} elseif ( ! empty( $package_items[ $i ] ) && isset( $order_items[ $package_items[ $i ] ] ) ) {
					// Fallback for items stored before WC 3.0
					$item = $order_items[ $package_items[ $i ] ];
				}

				// Get item name and meta
				if ( empty( $item ) ) {
					$id = empty( $product['variation_id'] ) ? $product['product_id'] : $product['variation_id'];
					$name = apply_filters( 'wcms_product_title', get_the_title( $id ), $product );
					$meta = apply_filters( 'wcms_package_item_meta', self::get_item_meta( $product ), $product );
				} else {
					$name = is_callable( array( $item, 'get_name' ) ) ? $item->get_name() : get_the_title( empty( $product['variation_id'] ) ? $product['product_id'] : $product['variation_id'] );
					$name = apply_filters( 'wcms_product_title', $name, $product, $item );
					$meta = function_exists( 'wc_display_item_meta' ) ? wc_display_item_meta( $item, array( 'echo' => false ) ) : self::get_item_meta( $product );
					$meta = apply_filters( 'wcms_package_item_meta', $meta, $product, $item );
				}

				echo '<li>' . $name . ' &times; ' . $product['quantity'];
				if ( ! empty( $meta ) ) {
					echo '<br />' . $meta;
				}
				echo '</li>';
			}

			echo '</ul></td>';

			// Address
			echo '<td' . $td_style . '>' . $address . '<br/><em>(' . $method . ')</em></td>';

			do_action( 'wc_ms_shop_table_row', $package, $order_id );

			// Notes
			echo '<td' . $td_style . '>';
			if ( ! empty( $package['note'] ) ) {
				echo $package['note'];
			} else {
				echo '&ndash;';
			}

			if ( ! empty( $package['date'] ) ) {
				echo '<p>' . sprintf( __( 'Delivery date: %s', 'wc_shipping_multiple_address' ), $package['date'] ) . '</p>';
			}
			echo '</td>';

			echo '</tr>';
		}
		echo '</table>';
	}

	public function show_multiple_addresses_line( $column ) {
		global $post, $the_order;

		if ( empty( $the_order ) || WC_MS_Compatibility::get_order_prop( $the_order, 'id' ) !== $post->ID ) {
			$the_order = wc_get_order( $post->ID );
		}

		if ( $column == 'shipping_address' ) {
			$packages = get_post_meta( $post->ID, '_wcms_packages', true );

			if ( ! $the_order->get_formatted_shipping_address() && is_array( $packages ) && count( $packages ) > 1 ) {
				_e( 'Ships to multiple addresses ', 'wc_shipping_multiple_address' );
			}

		}
	}

	public function order_meta_box( $type ) {
		global $post;

		$methods   = get_post_meta( $post->ID, '_shipping_methods', true );
		$multiship = get_post_meta( $post->ID, '_multiple_shipping', true );

		if ( $multiship == 'yes' || ( is_array( $methods ) && count( $methods ) > 1 ) ) {
			add_meta_box(
				'wc_multiple_shipping',
				__( 'Order Shipping Addresses', 'wc_shipping_multiple_address' ),
				array( $this, 'packages_meta_box' ),
				'shop_order',
				'normal',
				'core'
			);
		}

	}

	public function admin_css() {
		$screen = get_current_screen();
		if ( 'shop_order' == $screen->id || 'woocommerce_page_wc-settings' == $screen->id ) {
			wp_enqueue_style( 'wc-ms-admin-css', plugins_url( 'css/admin.css', WC_Ship_Multiple::FILE ) );
		}
	}

	public function packages_meta_box( $post ) {
		$order                  = wc_get_order( $post->ID );
		$packages               = get_post_meta( $post->ID, '_wcms_packages', true );

		if ( ! $order || ! $packages ) {
			return;
		}

		$package_items          = get_post_meta( $post->ID, '_packages_item_ids', true );
		$methods                = get_post_meta( $post->ID, '_shipping_methods', true );
		$settings               = get_option( 'woocommerce_multiple_shipping_settings', array() );
		$partial_orders         = isset( $settings['partial_orders'] ) && 'yes' === $settings['partial_orders'];
		$send_email             = isset( $settings['partial_orders_email'] ) && 'yes' === $settings['partial_orders_email'];
		$order_shipping_methods = $order->get_shipping_methods();

		// Get all the order items (and match the cart keys)
		$order_items = $order->get_items();
		$cart_item_keys = array();
		foreach ( $order_items as $item_id => $item ) {
			if ( ! empty( $item['wcms_cart_key'] ) ) {
				$cart_item_keys[ $item['wcms_cart_key'] ] = $item_id;
			}
		}

		echo '<div class="item-addresses-holder">';

		foreach ( $packages as $x => $package ) {
			$products = $package['contents'];
			echo '<div class="item-address-box package-' . $x . '-box">';

			if ( $partial_orders && isset( $package['status'] ) && $package['status'] == 'Completed' ) {
				echo '<span class="complete">&nbsp;</span>';
			}

			foreach ( $products as $i => $product ) {

				// Get a matching order item
				$item = false;
				if ( ! empty( $product['cart_key'] ) && ! empty( $cart_item_keys[ $product['cart_key'] ] ) && isset( $order_items[ $cart_item_keys[ $product['cart_key'] ] ] ) ) {
					$item = $order_items[ $cart_item_keys[ $product['cart_key'] ] ];
				} elseif ( ! empty( $package_items[ $i ] ) && isset( $order_items[ $package_items[ $i ] ] ) ) {
					// Fallback for items stored before WC 3.0
					$item = $order_items[ $package_items[ $i ] ];
				}


				// Get item name and meta
				if ( empty( $item ) ) {
					$id = empty( $product['variation_id'] ) ? $product['product_id'] : $product['variation_id'];
					$name = apply_filters( 'wcms_product_title', get_the_title( $id ), $product );
					$meta = apply_filters( 'wcms_package_item_meta', self::get_item_meta( $product ), $product );
				} else {
					$name = is_callable( array( $item, 'get_name' ) ) ? $item->get_name() : get_the_title( empty( $product['variation_id'] ) ? $product['product_id'] : $product['variation_id'] );
					$name = apply_filters( 'wcms_product_title', $name, $product, $item );
					$meta = function_exists( 'wc_display_item_meta' ) ? wc_display_item_meta( $item, array( 'echo' => false ) ) : self::get_item_meta( $product );
					$meta = apply_filters( 'wcms_package_item_meta', $meta, $product, $item );
				}

				// Display product info
				echo '<h4>' . esc_html( $name ) . ' &times; ' . $product['quantity'] . '</h4>';
				if ( ! empty( $meta ) ) {
					echo $meta;
				}
			}

			self::display_shipping_package_address( $order, $package, $x, true );

			// Get Shipping method for this package
			$method = '';
			if ( isset( $methods[ $x ]['id'] ) ) {
				foreach ( $order_shipping_methods as $ship_method ) {
					if ( $ship_method->get_method_id() . ':' . $ship_method->get_instance_id() === $methods[ $x ]['id']
						|| $ship_method->get_method_id() === $methods[ $x ]['id']
					) {
						$method = $ship_method->get_name();
						break;
					}
				}
			}
			if ( empty( $method ) ) {
				$order_method = current( $order_shipping_methods );
				$method = $order_method['name'];
			}
			echo '<em>' . $method . '</em>';

			// If partial orders are enabled then show package status
			if ( $partial_orders ) {
				$current_status = isset( $package['status'] ) ? $package['status'] : 'Pending';

				if ( $current_status == 'Completed' ) {
					$select_css = 'display: none;';
					$status_css = '';
				} else {
					$select_css = '';
					$status_css = 'display: none;';
				}

				echo '<p id="package_' . $x . '_select_p" style="' . $select_css . '">
							<select id="package_' . $x . '_status">
								<option value="Pending" ' . selected( $current_status, 'Pending', false ) . '>' . __( 'Pending', 'wc_shipping_multiple_address' ) . '</option>
								<option value="Completed" ' . selected( $current_status, 'Completed', false ) . '>' . __( 'Completed', 'wc_shipping_multiple_address' ) . '</option>
							</select>
							<a class="button save-package-status" data-order="' . $post->ID . '" data-package="' . $x . '" href="#" title="Apply">' . __( 'GO', 'wc_shipping_multiple_address' ) . '</a>
						</p>';

				echo '<p id="package_' . $x . '_status_p" style="' . $status_css . '"><strong>' . __( 'Completed', 'wc_shipping_multiple_address' ) . '</strong> (<a href="#" class="edit_package" data-package="' . $x . '">' . __('Change', 'wc_shipping_multiple_address') . '</a>)</p>';
			}

			do_action( 'wc_ms_order_package_block', $order, $package, $x );

			echo '</div>';
		}
		echo '</div>';
		echo '<div class="clear"></div>';


		$email_enabled = ($send_email) ? 'true' : 'false';
		$inline_js = '
			var email_enabled = '. $email_enabled .';
			jQuery(".shipping_data a.edit_shipping_address").click(function(e) {
				e.preventDefault();
				jQuery(this).closest(".shipping_data").find("div.edit_shipping_address").show();
			});

			jQuery(".save-package-status").click(function(e) {
				e.preventDefault();
				var pkg_id      = jQuery(this).data("package");
				var order_id    = jQuery(this).data("order");
				var status      = jQuery("#package_"+ pkg_id +"_status").val();
				var email       = false;

				if ( status == "Completed" && email_enabled ) {
					if ( confirm("' . __( 'Do you want to send an email to the customer?', 'wc_shipping_multiple_address' ) . '") ) {
						email = true;
					}
				}

				jQuery(".package-"+ pkg_id +"-box").block({ message: null, overlayCSS: { background: "#fff url(' . WC()->plugin_url() . '/assets/images/ajax-loader.gif) no-repeat center", opacity: 0.6 } });

				jQuery.post(ajaxurl, {action: "wcms_update_package_status", "status": status, package: pkg_id, order: order_id, email: email}, function(resp) {
					if ( resp == "Completed" ) {
						jQuery(".package-"+ pkg_id +"-box").prepend("<span class=\'complete\'>&nbsp;</span>");
						jQuery("#package_"+ pkg_id +"_status_p").show();
						jQuery("#package_"+ pkg_id +"_select_p").hide();
					} else {
						jQuery(".package-"+ pkg_id +"-box").find("span.complete").remove();
					}

					jQuery(".package-"+ pkg_id +"-box").unblock();
				});

			});

			jQuery(".edit_package").click(function(e) {
				e.preventDefault();

				var pkg_id = jQuery(this).data("package");

				jQuery("#package_"+ pkg_id +"_status_p").hide();
				jQuery("#package_"+ pkg_id +"_select_p").show();
			});
		';

		wc_enqueue_js( $inline_js );
	}

	public static function display_shipping_package_address( $order, $package, $index, $edit = false ) {
		if ( empty( $package['destination'] ) )
			return;

	?>
		<div class="shipping_data">

			<?php do_action( 'wc_ms_order_package_block_before_address', $order, $package, $index ); ?>

			<div class="address">
				<p><?php echo WC()->countries->get_formatted_address( $package['destination'] ); ?></p>
			</div>

			<?php do_action( 'wc_ms_order_package_block_after_address', $order, $package, $index ); ?>

			<?php
				if ( $edit ) {

					// Get local shipping fields
					$shipping_fields = WC()->countries->get_address_fields( $package['destination']['country'], 'shipping_' );

					if ( ! empty( $shipping_fields ) ) {
						echo '<a class="edit_shipping_address" href="#">( ' . __( 'Edit', 'wc_shipping_multiple_address' ) . ' )</a><br />';

						// Display form
						echo '<div class="edit_shipping_address" style="display:none;">';

						foreach ( $shipping_fields as $key => $field ) {
							$key      = str_replace( 'shipping_', '', $key );
							$addr_key = $key;
							$key      = 'pkg_' . $key . '_' . $index;

							if ( ! isset( $field['type'] ) ) {
								$field['type'] = 'text';
							}
							if ( ! isset( $field['label'] ) ) {
								$field['label'] = '';
							}
							switch ( $field['type'] ) {
								case "select" :
									woocommerce_wp_select( array( 'id' => $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $package['destination'][ $addr_key ] ) );
								break;
								default :
									woocommerce_wp_text_input( array( 'id' => $key, 'label' => $field['label'], 'value' => $package['destination'][ $addr_key ] ) );
								break;
							}
						}

						echo '<input type="hidden" name="edit_address[]" value="' . $index . '" />';
						echo '</div>';

					}
				}
			?>

		</div>
		<?php
	}



	public function update_order_addresses( $post_id, $post ) {

		$packages = get_post_meta( $post_id, '_wcms_packages', true );

		if ( $packages && isset( $_POST['edit_address'] ) && count( $_POST['edit_address'] ) > 0 ) {
			foreach ( $_POST['edit_address'] as $idx ) {
				if ( ! isset( $packages[ $idx ] ) ) {
					continue;
				}

				$address = array(
					'first_name' => isset( $_POST['pkg_first_name_' . $idx] ) ? $_POST['pkg_first_name_' . $idx] : '',
					'last_name'  => isset( $_POST['pkg_last_name_' . $idx] ) ? $_POST['pkg_last_name_' . $idx] : '',
					'company'    => isset( $_POST['pkg_company_' . $idx] ) ? $_POST['pkg_company_' . $idx] : '',
					'address_1'  => isset( $_POST['pkg_address_1_' . $idx] ) ? $_POST['pkg_address_1_' . $idx] : '',
					'address_2'  => isset( $_POST['pkg_address_2_' . $idx] ) ? $_POST['pkg_address_2_' . $idx] : '',
					'city'       => isset( $_POST['pkg_city_' . $idx] ) ? $_POST['pkg_city_' . $idx] : '',
					'state'      => isset( $_POST['pkg_state_' . $idx] ) ? $_POST['pkg_state_' . $idx] : '',
					'postcode'   => isset( $_POST['pkg_postcode_' . $idx] ) ? $_POST['pkg_postcode_' . $idx] : '',
					'country'    => isset( $_POST['pkg_country_' . $idx] ) ? $_POST['pkg_country_' . $idx] : '',
				);

				$packages[ $idx ]['destination'] = $address;
			}
			update_post_meta( $post_id, '_wcms_packages', $packages );
		}
	}

	public function update_order_taxes( $order_id, $items ) {
		$order_taxes = isset( $items['order_taxes'] ) ? $items['order_taxes'] : array();
		$tax_total = array();
		$packages = get_post_meta( $order_id, '_wcms_packages', true );

		if ( ! is_array( $packages ) ) {
			return;
		}

		foreach ( $order_taxes as $item_id => $rate_id ) {
			foreach ( $packages as $package ) {
				foreach ( $package['contents'] as $item ) {
					if ( isset( $item['line_tax_data']['total'][ $rate_id ] ) ) {
						if ( ! isset( $tax_total[ $item_id ] ) ) {
							$tax_total[ $item_id ] = 0;
						}
						$tax_total[ $item_id ] += $item['line_tax_data']['total'][ $rate_id ];
					}
				}
			}
		}

		$total_tax = 0;
		foreach ( $tax_total as $item_id => $total ) {
			$total_tax += $total;
			wc_update_order_item_meta( $item_id, 'tax_amount', $tax_total[ $item_id ] );
		}

		$old_total_tax = get_post_meta( $order_id, '_order_tax', true );

		if ( $total_tax > $old_total_tax ) {
			$order_total = get_post_meta( $order_id, '_order_total', true );
			$order_total -= $old_total_tax;
			$order_total += $total_tax;

			update_post_meta( $order_id, '_order_total', $order_total );
		}

		update_post_meta( $order_id, '_order_tax', $total_tax );

	}

	public function order_item_taxes( $items, $order ) {
		if ( get_post_meta( WC_MS_Compatibility::get_order_prop( $order, 'id' ), '_multiple_shipping', true ) != 'yes' ) {
			return $items;
		}

		$packages = get_post_meta( WC_MS_Compatibility::get_order_prop( $order, 'id' ), '_wcms_packages', true );

		if ( ! $packages ) {
			return $items;
		}

		foreach ( $items as $item_id => $item ) {
			if ( $item['type'] != 'line_item' ) {
				continue;
			}

			if ( $item['qty'] == 1 ) {
				continue;
			}

			$item_tax_subtotal  = 0;
			$item_tax_total     = 0;
			$item_tax_data      = array();
			$modified           = false;

			$item_line_tax_data = ! is_array( $item['line_tax_data'] ) ? unserialize( $item['line_tax_data'] ) : $item['line_tax_data'];
			$tax_rate_ids       = array_keys( $item_line_tax_data['total'] );

			foreach ( $packages as $package ) {
				foreach ( $package['contents'] as $package_item ) {

					if ( (int) $item['product_id'] == (int) $package_item['product_id'] && (int) $item['variation_id'] == (int) $package_item['variation_id'] ) {
						$modified = true;

						$item_tax_subtotal  += $package_item['line_subtotal_tax'];
						$item_tax_total     += $package_item['line_tax'];

						if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
							$item_rate_ids = array_keys( $package_item['line_tax_data']['total'] );
							$tax_rate_ids = array_unique( array_merge( $tax_rate_ids, $item_rate_ids ) );
						}
					}

				}
			}

			if ( $modified && is_array( $tax_rate_ids ) ) {
				foreach ( $tax_rate_ids as $rate_id ) {
					if ( ! isset( $item_tax_data['total'][ $rate_id ] ) ) {
						$item_tax_data['total'][ $rate_id ] = 0;
					}

					$item_tax_data['total'][ $rate_id ] += $item_tax_total;

					if ( !isset( $item_tax_data['subtotal'][ $rate_id ] ) ) {
						$item_tax_data['subtotal'][ $rate_id ] = 0;
					}

					$item_tax_data['subtotal'][ $rate_id ] += $item_tax_subtotal;
				}

				if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
					$items[ $item_id ]->set_taxes( $item_tax_data );
				} else {
					$items[ $item_id ]['line_tax'] = $item_tax_total;
					$items[ $item_id ]['line_subtotal_tax'] = $item_tax_subtotal;
					$items[ $item_id ]['line_tax_data'] = serialize( $item_tax_data );
				}
			}

		}

		return $items;
	}

	/**
	* Load a custom template body for orders with multishipping
	* @param string    $template
	* @param WC_Order  $order
	* @param int       $order_loop
	* @return string $template
	*/
	public function pip_template_body( $template, $order, $order_loop ) {
		$packages = get_post_meta( WC_MS_Compatibility::get_order_prop( $order, 'id' ), '_shipping_packages', true );

		if ( $packages && count( $packages ) > 1 ) {
			$template = dirname( WC_Ship_Multiple::FILE ) . '/templates/pip-template-body.php';
		}

		return $template;
	}

	public static function send_package_email( $order_id, $package_index ) {
		$settings = get_option( 'woocommerce_multiple_shipping_settings', array() );
		$order    = wc_get_order( $order_id );

		$subject  = empty( $settings['email_subject'] ) ? __( 'Part of your order has been shipped', 'wc_shipping_multiple_address' ) : $settings['email_subject'];
		$message  = empty( $settings['email_message'] ) ? self::get_default_email_body() : $settings['email_message'];

		$mailer   = WC()->mailer();
		$message  = $mailer->wrap_message( $subject, $message );

		$ts         = strtotime( WC_MS_Compatibility::get_order_prop( $order, 'order_date' ) );
		$order_date = date( get_option( 'date_format' ), $ts );
		$order_time = date( get_option( 'time_format' ), $ts );

		$search       = array( '{order_id}', '{order_date}', '{order_time}', '{customer_first_name}', '{customer_last_name}', '{products_table}', '{addresses_table}' );
		$replacements = array(
			$order->get_order_number(),
			$order_date,
			$order_time,
			WC_MS_Compatibility::get_order_prop( $order, 'billing_first_name' ),
			WC_MS_Compatibility::get_order_prop( $order, 'billing_last_name' ),
			self::render_products_table( $order, $package_index ),
			self::render_addresses_table( $order, $package_index )
		);
		$message = str_replace( $search, $replacements, $message );

		$mailer->send( WC_MS_Compatibility::get_order_prop( $order, 'billing_email' ), $subject, $message );
	}

	public static function get_default_email_body() {
		ob_start();
	?>
		<p><?php printf( __( 'Hi there. Part of your recent order on %s has been completed. Your order details are shown below for your reference:', 'wc_shipping_multiple_address' ), get_option( 'blogname' ) ); ?></p>

		<h2><?php echo __( 'Order:', 'wc_shipping_multiple_address' ) . ' {order_id}'; ?></h2>

		{products_table}

		{addresses_table}

	<?php
		return ob_get_clean();
	}

	public static function render_products_table( $order, $idx ) {
		$packages = get_post_meta( WC_MS_Compatibility::get_order_prop( $order, 'id' ), '_wcms_packages', true );
		$package  = $packages[ $idx ];
		$products = $package['contents'];

		ob_start();
	?>
		<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
			<thead>
			<tr>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'wc_shipping_multiple_address' ); ?></th>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'wc_shipping_multiple_address' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
				foreach ( $products as $item ):
					$_product = wc_get_product( $item['product_id'] );
					$attachment_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $_product->get_id() ), 'thumbnail' );
					$image = $attachment_image_src ? '<img src="' . current( $attachment_image_src ) . '" alt="Product Image" height="32" width="32" style="vertical-align:middle; margin-right: 10px;" />' : '';
			?>
				<tr>
					<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php

						// Show title/image etc
						echo apply_filters( 'woocommerce_order_product_image', $image, $_product, true );

						// Product name
						echo apply_filters( 'woocommerce_order_product_title', $_product->get_title(), $_product );

						// SKU
						echo ( $_product->get_sku() ? ' (#' . $_product->get_sku() . ')' : '' );

						// File URLs
						if ( $_product->exists() && $_product->is_downloadable() ) {

							$download_file_urls = $order->get_downloadable_file_urls( $item['product_id'], $item['variation_id'], $item );

							$i = 0;

							foreach ( $download_file_urls as $file_url => $download_file_url ) {
								echo '<br/><small>';

								$filename = wc_get_filename_from_url( $file_url );

								if ( count( $download_file_urls ) > 1 ) {
									echo sprintf( __('Download %d:', 'wc_shipping_multiple_address' ), $i + 1 );
								} elseif ( $i == 0 )
									echo __( 'Download:', 'wc_shipping_multiple_address' );

									echo ' <a href="' . $download_file_url . '" target="_blank">' . $filename . '</a></small>';

									$i++;
								}
							}
					?></td>
					<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $item['quantity'] ;?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php

		return ob_get_clean();
	}

	public static function render_addresses_table( $order, $index ) {
		$packages = get_post_meta( WC_MS_Compatibility::get_order_prop( $order, 'id' ), '_wcms_packages', true );
		$package  = $packages[ $index ];

		ob_start();
	?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
			<tr>
				<td valign="top" width="50%">
					<h3><?php _e( 'Billing address', 'wc_shipping_multiple_address' ); ?></h3>
					<p><?php echo $order->get_formatted_billing_address(); ?></p>
				</td>
				<td valign="top" width="50%">
					<h3><?php _e( 'Shipping address', 'wc_shipping_multiple_address' ); ?></h3>
					<?php self::display_shipping_package_address( $order, $package, $index ); ?>
				</td>
			</tr>
		</table>
	<?php

		return ob_get_clean();
	}

	/**
	* Gets and formats a list of item meta for display (fallback function for when we can't find order item)
	*
	* @param array $item
	* @return string
	*/
	public static function get_item_meta( $item ) {

		$item_data = array();

		// Variation data
		if ( ! empty( $item['data']->variation_id ) && is_array( $item['variation'] ) ) {

			foreach ( $item['variation'] as $name => $value ) {

				if ( empty( $value ) )
					continue;

				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

				// If this is a term slug, get the term's nice name
				if ( taxonomy_exists( $taxonomy ) ) {
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
					$label = wc_attribute_label( $taxonomy );

				// If this is a custom option slug, get the options name.
				} else {
					$value = apply_filters( 'woocommerce_variation_option_name', $value );
					$label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $item['data'] );
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => $value,
				);
			}
		}

		$output = '';
		if ( ! empty( $item_data ) ) {
			$output .= '<ul>';
			foreach ( $item_data as $data ) {
				$output .= '<li>' . esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . '</li>';
			}
			$output .= '</ul>';
		}

		return $output;
	}

	/**
	 * Hides metadata.
	 *
	 * @param  array    $hidden     hidden meta strings
	 * @return array                modified hidden meta strings
	 */
	public function hidden_order_item_meta( $hidden ) {
		return array_merge( $hidden, array( '_wcms_cart_key' ) );
	}

}
