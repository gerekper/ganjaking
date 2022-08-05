<?php
/**
 * Plugin Name: WooCommerce Ship to Multiple Addresses
 * Plugin URI: https://woocommerce.com/products/shipping-multiple-addresses/
 * Description: Allow customers to ship orders with multiple products or quantities to separate addresses instead of forcing them to place multiple orders for different delivery addresses.
 * Version: 3.7.0
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: wc_shipping_multiple_address
 * Domain Path: /languages
 * Tested up to: 6.0.1
 * WC tested up to: 6.7.0
 * WC requires at least: 3.2.3
 * Woo: 18741:aa0eb6f777846d329952d5b891d6f8cc
 *
 * Copyright 2020 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


require_once( 'class.ms_compat.php' );

/**
 * WooCommerce fallback notice.
 *
 * @since 3.6.15
 * @return void
 */
function woocommerce_shipping_multiple_addresses_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Multiple Addresses requires WooCommerce to be installed and active. You can download %s here.', 'wc_shipping_multiple_address' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Ship_Multiple' ) ) :
	define( 'WC_SHIPPING_MULTIPLE_ADDRESSES_VERSION', '3.7.0' ); // WRCS: DEFINED_VERSION.

	class WC_Ship_Multiple {

		const FILE = __FILE__;

		public $front;
		public $cart;
		public $packages;
		public $address_book;
		public $checkout;
		public $notes;
		public $gifts;

		public $meta_key_order      = '_shipping_methods';
		public $meta_key_settings   = '_shipping_settings';
		public $settings            = null;
		public $gateway_settings    = null;
		public static $lang         = array(
			'notification'  => 'You may use multiple shipping addresses on this cart',
			'btn_items'     => 'Set Multiple Addresses',
		);

		public function __construct() {
			// load the shipping options
			$this->settings = get_option( $this->meta_key_settings, array());

			// shortcode
			add_shortcode( 'woocommerce_select_multiple_addresses', array( $this, 'draw_form' ) );
			add_shortcode( 'woocommerce_account_addresses', array( $this, 'account_addresses' ) );

			// override needs shipping method and totals
			add_action( 'woocommerce_init', array( $this, 'wc_init' ) );
			add_action( 'woocommerce_init', array( $this, 'maybe_install_pages' ) );

			include_once( 'multi-shipping.php' );

			$settings   = get_option( 'woocommerce_multiple_shipping_settings', array() );
			$this->gateway_settings = $settings;

			if ( isset($settings['lang_notification']) ) {
				self::$lang['notification'] = $settings['lang_notification'];
			}

			if ( isset($settings['lang_btn_items']) ) {
				self::$lang['btn_items'] = $settings['lang_btn_items'];
			}

			include_once 'includes/wcms-post-types.php';

			include_once 'includes/functions.php';
			include_once 'includes/wcms-gifts.php';
			include_once 'includes/wcms-notes.php';
			include_once 'includes/wcms-checkout.php';
			include_once 'includes/wcms-cart.php';
			include_once 'includes/wcms-packages.php';
			include_once 'includes/wcms-address-book.php';
			include_once 'includes/wcms-front.php';
			include_once 'includes/wcms-admin.php';
			include_once 'includes/wcms-order.php';
			include_once 'includes/wcms-order-shipment.php';

			include_once 'includes/integrations/wcms-customer-order-csv-export.php';

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if (
				is_plugin_active( 'se_woocommerce/shippingeasy_order.php' ) ||
				is_plugin_active( 'woocommerce-shippingeasy/woocommerce-shippingeasy.php' )
			) {
				include_once 'includes/wcms-shipping-easy.php';
			}

			include_once 'includes/wcms-api.php';
			new WC_MS_API();

			$this->gifts        = new WC_MS_Gifts( $this );
			$this->notes        = new WC_MS_Notes( $this );
			$this->checkout     = new WC_MS_Checkout( $this );
			$this->cart         = new WC_MS_Cart( $this );
			$this->packages     = new WC_MS_Packages( $this );
			$this->address_book = new WC_MS_Address_Book( $this );
			$this->front        = new WC_MS_Front( $this );
			$this->admin        = new WC_MS_Admin( $this );
			$this->order        = new WC_MS_Order( $this );
			$this->shipments    = new WC_MS_Order_Shipment( $this );
			$this->csv_export   = new WC_MS_Customer_Order_Csv_Export( $this );

			include_once 'includes/wcms-shipworks.php';

			$this->load_compat_classes();
		}

		public function maybe_install_pages() {

			$page_id = wc_get_page_id( 'multiple_addresses' );

			if ( $page_id == -1 || get_post( $page_id ) == null) {
				// get the checkout page
				$checkout_id = wc_get_page_id( 'checkout' );

				// add page and assign
				$page = array(
					'menu_order'        => 0,
					'comment_status'    => 'closed',
					'ping_status'       => 'closed',
					'post_author'       => 1,
					'post_content'      => '[woocommerce_select_multiple_addresses]',
					'post_name'         => 'shipping-addresses',
					'post_parent'       => $checkout_id,
					'post_title'        => 'Shipping Addresses',
					'post_type'         => 'page',
					'post_status'       => 'publish',
					'post_category'     => array(1)
				);

				$page_id = wp_insert_post($page);

				update_option( 'woocommerce_multiple_addresses_page_id', $page_id);
			}

			$page_id = wc_get_page_id( 'account_addresses' );

			if ($page_id == -1 || get_post( $page_id ) == null ) {
				// get the checkout page
				$account_id = wc_get_page_id( 'myaccount' );

				// add page and assign
				$page = array(
					'menu_order'        => 0,
					'comment_status'    => 'closed',
					'ping_status'       => 'closed',
					'post_author'       => 1,
					'post_content'      => '[woocommerce_account_addresses]',
					'post_name'         => 'account-addresses',
					'post_parent'       => $account_id,
					'post_title'        => 'Shipping Addresses',
					'post_type'         => 'page',
					'post_status'       => 'publish',
					'post_category'     => array(1)
				);

				$page_id = wp_insert_post($page);

				update_option( 'woocommerce_account_addresses_page_id', $page_id);
			}
		}

		/**
		 * Load compatibility classes.
		 *
		 * @since 3.6.13
		 * @return void
		 */
		public function load_compat_classes() {
			if ( class_exists( 'WC_PIP' ) ) {
				include_once 'includes/compat/class-wc-pip-compat.php';
				new WC_Pip_Compat();
			}
		}

		public function is_multiship_enabled() {
			$enabled = true;

			// role-based shipping methods
			if ( class_exists( 'WC_Role_Methods' ) ) {
				global $current_user;

				$enabled = false;

				if ( !isset( $wp_roles ) )
					$wp_roles = new WP_Roles();

				$the_roles          = $wp_roles->roles;
				$current_user_roles = array();

				if ( is_user_logged_in() ) {
					$user = new WP_User( $current_user->ID );
					if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
						foreach ( $user->roles as $role ) {
							$current_user_roles[] = strtolower($the_roles[$role]['name']);
						}
					}
				} else {
					$current_user_roles[] = 'Guest';
				}

				$role_methods   = WC_Role_Methods::get_instance();

				foreach ( $current_user_roles as $user_role ) {
					if ( $role_methods->check_rolea_methods( $user_role, 'multiple_shipping' ) ) {
						$enabled = true;
						break;
					}
				}

			}


			return apply_filters( 'wc_ms_is_multiship_enabled', $enabled );
		}

		function wc_init() {

			include_once 'includes/wcms-privacy.php';

			add_action( 'woocommerce_before_order_total', array( $this, 'display_shipping_methods' ) );
			add_action( 'woocommerce_review_order_before_order_total', array( $this, 'display_shipping_methods' ) );
		}

		/**
		 * unused
		 */
		public function menu() {
			add_submenu_page( 'woocommerce', __( 'Multiple Shipping Settings', 'wc_shipping_multiple_address' ),  __( 'Multiple Shipping', 'wc_shipping_multiple_address' ) , 'manage_woocommerce', 'wc-ship-multiple-products', array( $this, 'settings' ) );
		}


		/**
		 * unused
		 */
		public function settings() {
			include 'settings.php';
		}



		public function product_options() {
			global $post, $thepostid;

			$settings   = $this->settings;
			$thepostid  = $post->ID;

			$ship       = WC()->shipping;

			$shipping_methods   = WC()->shipping->shipping_methods;
			$ship_methods_array = array();
			$categories_array   = array();

			foreach ($shipping_methods as $id => $object) {
				if ($object->enabled == 'yes' && $id != 'multiple_shipping' ) {
					$ship_methods_array[$id] = $object->method_title;
				}
			}

			$method     = $this->get_product_shipping_method( $thepostid );
			?>
			<p style="border-top: 1px solid #DFDFDF;">
				<strong><?php _e( 'Shipping Options', 'periship' ); ?></strong>
			</p>
			<p class="form-field method_field">
				<label for="product_method"><?php _e( 'Shipping Methods', 'wc_shipping_multiple_address' ); ?></label>
				<select name="product_method[]" id="product_method" class="chzn-select" multiple>
					<option value=""></option>
					<?php
					foreach ($ship_methods_array as $value => $label):
						$selected = (in_array($value, $method)) ? 'selected' : '';
					?>
					<option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<script type="text/javascript">jQuery("#product_method").chosen();</script>
			<?php
		}

		public function process_metabox( $post_id ) {
			$settings = $this->settings;

			$zip_origin = null;
			$method     = ( ! empty( $_POST['product_method'] ) && is_array( $_POST['product_method'] ) ) ? $_POST['product_method'] : false;

			if (! $method ) return;

			// remove all instances of this product is first
			foreach ( $settings as $idx => $setting ) {
				if ( in_array($post_id, $setting['products']) ) {
					foreach ( $setting['products'] as $pid => $id ) {
						if ( $id == $post_id ) unset($settings[$idx]['products'][$pid]);
					}
				}
			}

			// look for a matching zip code
			$matched    = false;
			$zip_match  = false;
			foreach ( $settings as $idx => $setting ) {

				if ( $setting['zip'] == $zip_origin ) {
					$zip_match = $idx;
					// methods must match
					if ( $method && count(array_diff($setting['method'], $method)) == 0 ) {
						// zip and method matched
						// add to existing setting
						$matched = true;
						$settings[$idx]['products'][] = $post_id;
						break;
					}
				}

			}

			if (! $matched ) {
				$settings[] = array(
					'zip'       => $zip_origin,
					'products'  => array($post_id),
					'categories'=> array(),
					'method'    => $method
				);
			}

			// finally, do some cleanup
			foreach ( $settings as $idx => $setting ) {
				if ( empty($setting['products']) && empty($setting['categories']) ) {
					unset($settings[$idx]);
				}
			}
			$settings = array_merge($settings, array());

			// update the settings
			update_option( $this->meta_key_settings, $settings );
		}

		public function account_addresses() {
			ob_start();

			$this->cart->load_cart_files();

			$checkout   = WC()->checkout;
			$user       = wp_get_current_user();
			$shipFields = WC()->countries->get_address_fields( WC()->countries->get_base_country(), 'shipping_' );

			if ($user->ID == 0) {
				return;
			}

			if ( isset( $_GET['edit'] ) ) {
				$updating   = true;
				$idx        = absint( $_GET['edit'] );

				$otherAddr  = get_user_meta($user->ID, 'wc_other_addresses', true);
				$address    = $otherAddr[ $idx ];
			} else {
				$updating   = false;
				$idx        = -1;
				$address    = array();
			}

				// Enqueue scripts
				wp_enqueue_script( 'wc-country-select' );
				wp_enqueue_script( 'wc-address-i18n' );


			wc_get_template(
				'account-address-form.php',
				array(
					'checkout'      => $checkout,
					'user'          => $user,
					'shipFields'    => $shipFields,
					'address'       => $address,
					'idx'           => $idx,
					'updating'      => $updating
				),
				'multi-shipping',
				dirname( __FILE__ ) .'/templates/'
			);

			return ob_get_clean();
		}

		public function draw_form() {
			if ( is_null( WC()->cart ) ) {
				return '';
			}

			ob_start();

			if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {

				$this->cart->load_cart_files();

				$user       = wp_get_current_user();
				$cart       = WC()->cart;
				$checkout   = WC()->checkout;
				$contents   = wcms_get_real_cart_items();
				$shipFields = WC()->countries->get_address_fields( WC()->countries->get_base_country(), 'shipping_' );
				$addresses  = $this->address_book->get_user_addresses( $user );
				unset( $shipFields['shipping_state']['country'] );

				if ( isset($_GET['new']) ) {
					if ( function_exists('wc_add_notice') )
						wc_add_notice(__('New address saved', 'wc_shipping_multiple_address'), 'success');
					else
						WC()->add_message( __('New address saved', 'wc_shipping_multiple_address') );
				}

				if ( function_exists('wc_print_notices') )
					wc_print_notices();
				else
					WC()->show_messages();

				if ( empty( $addresses ) || isset( $_REQUEST['address-form'] ) ) {
					wc_get_template(
						'address-form.php',
						array(
							'checkout'      => $checkout,
							'addresses'     => $addresses,
							'shipFields'    => $shipFields
						),
						'multi-shipping',
						dirname( __FILE__ ) .'/templates/'
					);
				} else {

					if (! empty($contents)) {
						$relations  = wcms_session_get('wcms_item_addresses');

						if ($addresses) foreach ($addresses as $x => $addr) {
							foreach ( $contents as $key => $value ) {
								if ( isset($relations[$x]) && !empty($relations[$x]) ):
									$qty = array_count_values($relations[$x]);

									if ( in_array($key, $relations[$x]) ) {
										if ( isset($placed[$key]) ) {
											$placed[$key] += $qty[$key];
										} else {
											$placed[$key] = $qty[$key];
										}
									}

								endif;
							}
						}

						$relations  = wcms_session_get( 'wcms_item_addresses' );

						wc_get_template(
							'shipping-address-table.php',
							array(
								'addresses'     => $addresses,
								'relations'     => $relations,
								'checkout'      => $checkout,
								'contents'      => $contents,
								'shipFields'    => $shipFields,
								'user'          => $user,
							),
							'multi-shipping',
							dirname( __FILE__ ) .'/templates/'
						);

					}
				}

			} else {
				// load order and display the addresses
				$order_id = (int)$_GET['order_id'];
				$order = wc_get_order( $order_id );

				if ($order_id == 0 || !$order) wp_die(__( 'Order could not be found', 'wc_shipping_multiple_address' ) );

				$packages = $order->get_meta( '_wcms_packages' );

				if ( !$packages ) wp_die(__( 'This order does not ship to multiple addresses', 'wc_shipping_multiple_address' ) );

				// load the address fields
				$this->cart->load_cart_files();

				$checkout   = WC()->checkout;
				$cart       = WC()->cart;
				//$shipFields = apply_filters( 'woocommerce_shipping_fields', array() );
				$shipFields = WC()->countries->get_address_fields( WC()->countries->get_base_country(), 'shipping_' );

				echo '<table class="shop_tabe"><thead><tr><th class="product-name">'. __( 'Product', 'wc_shipping_multiple_address' ) .'</th><th class="product-quantity">'. __( 'Qty', 'wc_shipping_multiple_address' ) .'</th><th class="product-address">'. __( 'Address', 'wc_shipping_multiple_address' ) .'</th></thead>';
				echo '<tbody>';

				$tr_class = '';
				foreach ( $packages as $x => $package ) {
					$products = $package['contents'];
					$item_meta = '';
					foreach ( $products as $i => $product ) {
						$tr_class = ($tr_class == '' ) ? 'alt-table-row' : '';

						if (isset($product['data']->item_meta) && !empty($product['data']->item_meta)) {
							$item_meta .= '<pre>';
							foreach ($product['data']->item_meta as $meta) {
								$item_meta .= $meta['meta_name'] .': '. $meta['meta_value'] ."\n";
							}
							$item_meta .= '</pre>';
						}

						echo '<tr class="'. $tr_class .'">';
						echo '<td class="product-name"><a href="'. get_permalink($product['data']->get_id()) .'">'. apply_filters( 'wcms_product_title', get_the_title($product['data']->get_id()), $product ) .'</a><br />'. $item_meta .'</td>';
						echo '<td class="product-quantity">'. $product['quantity'] .'</td>';
						echo '<td class="product-address"><address>'. wcms_get_formatted_address( $package['destination'] ) .'</td>';
						echo '</tr>';
					}
				}

				echo '</table>';
			}

			return ob_get_clean();
		}

		public function display_shipping_methods() {

			$packages = WC()->cart->get_shipping_packages();
			$shipping_packages = WC()->shipping->get_packages();

			foreach ( $shipping_packages as $index => $package ) {
				if ( ! isset( $packages[ $index ] ) ) {
					continue;
				}

				$packages[ $index ]['rates'] = $package['rates'];
			}

			if (! $this->cart->cart_is_eligible_for_multi_shipping() )
				return;

			$sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );
			if ( isset($sess_cart_addresses) && !empty($sess_cart_addresses) ) {
				// always allow users to select shipping
				$this->render_shipping_row($packages, 0);
			} else {
				if ( $this->packages_have_different_origins($packages) || $this->packages_have_different_methods($packages) ) {
					// show shipping methods available to each package
					$this->render_shipping_row($packages, 1);
				} else {
					if ( $this->packages_contain_methods($packages) ) {
						// methods must be combined
						$this->render_shipping_row($packages, 2);
					}
				}
			}

		}

		/**
		 * @param array $packages
		 * @param int $type 0=multi-shipping; 1=different packages; 2=same packages
		 */
		public function render_shipping_row($packages, $type = 2) {

			$page_id            = wc_get_page_id( 'multiple_addresses' );
			$rates_available    = false;

			if ( function_exists('wc_add_notice') ) {
				$available_methods  = $this->get_available_shipping_methods();
			} else {
				$available_methods  = WC()->shipping->get_available_shipping_methods();
			}

			$field_name         = 'shipping_methods';
			$post               = array();

			if ( function_exists('wc_add_notice') ) {
				$field_name = 'shipping_method';
			}

			if ( isset($_POST['post_data']) ) {
				parse_str($_POST['post_data'], $post);
			}

			if ( $type == 0 || $type == 1):

			?>
			<tr class="multi_shipping">
				<td style="vertical-align: top;" colspan="<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) echo '2'; else echo '1'; ?>">
					<?php _e( 'Shipping Methods', 'wc_shipping_multiple_address' ); ?>

					<div id="shipping_addresses">
						<?php
						foreach ($packages as $x => $package):
							$error_message = '';

							if ( $this->is_address_empty( $package['destination'] ) ) {

								$error_message = __( 'The following items do not have a shipping address assigned.', 'wc_shipping_multiple_address' );
							
							} elseif ( !isset( $package['rates'] ) || empty( $package['rates'] ) ) {

								$error_message = __( 'There are no shipping options available for the following items.', 'wc_shipping_multiple_address' );

							}

							if (! empty( $error_message ) ) {
								// we have cart items with no set address
								$products           = $package['contents'];
								?>
								<div class="ship_address no_shipping_address">
									<em><?php echo $error_message; ?></em>
									<ul>
									<?php
										foreach ($products as $i => $product):
											$attributes = html_entity_decode( WC_MS_Compatibility::get_item_data( $product ) );
											?>
											<li>
												<strong><?php echo wp_kses_post( apply_filters( 'wcms_product_title', get_the_title($product['data']->get_id()), $product ) ); ?> x <?php echo $product['quantity']; ?></strong>
												<?php
												if ( !empty( $attributes ) ) {
													echo '<small class="data">'. str_replace( "\n", "<br/>", $attributes ) .'</small>';
												}
												?>
											</li>
										<?php endforeach; ?>
									</ul>
										<?php
										$sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );
										//if ( $sess_cart_addresses && !empty($sess_cart_addresses) ) {
											echo '<p style="text-align: center"><a href="'. get_permalink($page_id) .'" class="button modify-address-button">'. __( 'Assign Shipping Address', 'wc_shipping_multiple_address' ) .'</a></p>';
										//}
								?>
								</div>
								<?php
								continue;
							}

							$shipping_methods   = array();
							$products           = $package['contents'];
							//$shipping_methods   = $package['rates'];
							$selected           = wcms_session_get('shipping_methods');
							$rates_available    = true;

							if ( $type == 0 ):
						?>
						<div class="ship_address">
							<dl>
							<?php
								foreach ($products as $i => $product):
									$attributes = html_entity_decode( WC_MS_Compatibility::get_item_data( $product, true ) );
							?>
							<dd>
								<strong><?php echo wp_kses_post( apply_filters( 'wcms_product_title', get_the_title($product['data']->get_id()), $product ) ); ?> x <?php echo $product['quantity']; ?></strong>
								<?php
									if ( !empty( $attributes ) ) {
										echo '<small class="data">'. str_replace( "\n", "<br/>", $attributes )  .'</small>';
									}
								?>
							</dd>
								<?php endforeach; ?>
							</dl>
								<?php
								$formatted_address = wcms_get_formatted_address( $package['destination'] );
								echo '<address>'. $formatted_address .'</address><br />'; ?>
								<?php

								do_action( 'wc_ms_shipping_package_block', $x, $package );

								// If at least one shipping method is available
								$ship_package['rates'] = array();

								foreach ( $package['rates'] as $rate ) {
									$ship_package['rates'][$rate->id] = $rate;
								}

								foreach ( $ship_package['rates'] as $method ) {
									if ( $method->id == 'multiple_shipping' ) continue;

									$method->label = esc_html( $method->label );

									if ( $method->cost > 0 ) {
										$shipping_tax = $method->get_shipping_tax();
										$method->label .= ' &mdash; ';

										// Append price to label using the correct tax settings
										if ( WC()->cart->display_totals_ex_tax || ! WC()->cart->prices_include_tax ) {

											if ( $shipping_tax > 0 ) {
												if ( WC()->cart->prices_include_tax ) {
													$method->label .= wc_price( $method->cost ) .' '.WC()->countries->ex_tax_or_vat();
												} else {
													$method->label .= wc_price( $method->cost );
												}
											} else {
												$method->label .= wc_price( $method->cost );
											}
										} else {
											$method->label .= wc_price( $method->cost + $shipping_tax );
											if ( $shipping_tax > 0 && ! WC()->cart->prices_include_tax ) {
												$method->label .= ' '.WC()->countries->inc_tax_or_vat();
											}
										}
									}

									$shipping_methods[] = $method;
								}

								// Print the single available shipping method as plain text
								if ( 1 === count( $shipping_methods ) ) {
									$method = $shipping_methods[0];

									echo $method->label;
									echo '<input type="hidden" class="shipping_methods shipping_method" name="'. $field_name .'['. $x .']" value="'.esc_attr( $method->id ).'">';

								// Show multiple shipping methods in a select list
								} elseif ( count( $shipping_methods ) > 1 ) {
									if ( !is_array( $selected ) || !isset( $selected[ $x ] ) ) {
										$cheapest_rate = wcms_get_cheapest_shipping_rate( $package['rates'] );

										if ( $cheapest_rate ) {
											$selected[ $x ] = $cheapest_rate;
										}
									}

									echo '<select class="shipping_methods shipping_method" name="'. $field_name .'['. $x .']">';

									foreach ( $package['rates'] as $rate ) {
										if ( $rate->id == 'multiple_shipping' ) continue;
										$sel = '';

										if ( isset( $selected[$x]['id'] ) && $selected[$x]['id'] === $rate->id ) $sel = 'selected';

										echo '<option value="'.esc_attr( $rate->id ).'" '. $sel .'>';
										echo strip_tags( $rate->label );
										echo '</option>';
									}

									echo '</select>';
								} else {
									echo '<p>'.__( '(1) Sorry, it seems that there are no available shipping methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'wc_shipping_multiple_address' ).'</p>';
								}

								$sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );
								if ( $sess_cart_addresses && !empty($sess_cart_addresses) ) {
									echo '<p><a href="'. get_permalink($page_id) .'" class="modify-address-button">'. __( 'Modify address', 'wc_shipping_multiple_address' ) .'</a></p>';
								}
						?>
						</div>
						<?php
							elseif ($type == 1):
						?>
						<div class="ship_address">
							<dl>
							<?php
								foreach ($products as $i => $product):
									$attributes = WC_MS_Compatibility::get_item_data( $product );
							?>
							<dd>
								<strong><?php echo esc_html( apply_filters( 'wcms_product_title', get_the_title($product['data']->get_id()), $product ) ); ?> x <?php echo $product['quantity']; ?></strong>
									<?php
									if ( !empty($attributes) ) {
										echo '<small class="data">'. str_replace( "\n", "<br/>", $attributes )  .'</small>';
									}
									?>
							</dd>
								<?php endforeach; ?>
							</dl>
							<?php
								// If at least one shipping method is available
								// Calculate shipping method rates
								$ship_package['rates'] = array();

								foreach ( WC()->shipping->shipping_methods as $shipping_method ) {

									if ( isset($package['method']) && !in_array($shipping_method->id, $package['method']) ) continue;

									if ( $shipping_method->is_available( $package ) ) {

										// Reset Rates
										$shipping_method->rates = array();

										// Calculate Shipping for package
										$shipping_method->calculate_shipping( $package );

										// Place rates in package array
										if ( ! empty( $shipping_method->rates ) && is_array( $shipping_method->rates ) )
											foreach ( $shipping_method->rates as $rate )
												$ship_package['rates'][$rate->id] = $rate;
									}

								}

								foreach ( $ship_package['rates'] as $method ) {
									if ( $method->id == 'multiple_shipping' ) continue;

									$method->label = esc_html( $method->label );

									if ( $method->cost > 0 ) {
										$shipping_tax = $method->get_shipping_tax();
										$method->label .= ' &mdash; ';

										// Append price to label using the correct tax settings
										if ( WC()->cart->display_totals_ex_tax || ! WC()->cart->prices_include_tax ) {

											if ( $shipping_tax > 0 ) {
												if ( WC()->cart->prices_include_tax ) {
													$method->label .= wc_price( $method->cost ) .' '.WC()->countries->ex_tax_or_vat();
												} else {
													$method->label .= wc_price( $method->cost );
												}
											} else {
												$method->label .= wc_price( $method->cost );
											}
										} else {
											$method->label .= wc_price( $method->cost + $shipping_tax );
											if ( $shipping_tax > 0 && ! WC()->cart->prices_include_tax ) {
												$method->label .= ' '.WC()->countries->inc_tax_or_vat();
											}
										}
									}

									$shipping_methods[] = $method;
								}

								// Print a single available shipping method as plain text
								if ( 1 === count( $shipping_methods ) ) {
									$method = $shipping_methods[0];

									echo $method->label;
									echo '<input type="hidden" class="shipping_methods shipping_method" name="'. $field_name .'['. $x .']" value="'.esc_attr( $method->id ).'||'. strip_tags($method->label) .'">';

								// Show multiple shipping methods in a select list
								} elseif ( count( $shipping_methods ) > 1 ) {
									echo '<select class="shipping_methods shipping_method" name="'. $field_name .'['. $x .']">';
									foreach ( $shipping_methods as $method ) {
										if ($method->id == 'multiple_shipping' ) continue;
										$current_selected = ( isset($selected[ $x ])  ) ? $selected[ $x ]['id'] : '';
										echo '<option value="'.esc_attr( $method->id ).'||'. strip_tags($method->label) .'" '.selected( $current_selected, $method->id, false).'>';

										if ( function_exists('wc_cart_totals_shipping_method_label') )
											echo wp_kses_post( wc_cart_totals_shipping_method_label( $method ));
										else
											echo strip_tags( $method->label );

										echo '</option>';
									}
									echo '</select>';
								} else {
									echo '<p>'.__( '(2) Sorry, it seems that there are no available shipping methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'wc_shipping_multiple_address' ).'</p>';
								}

								$sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );
								if ( $sess_cart_addresses && !empty($sess_cart_addresses) ) {
									echo '<p><a href="'. get_permalink($page_id) .'" class="modify-address-button">'. __( 'Modify address', 'wc_shipping_multiple_address' ) .'</a></p>';
								}
						?>
						</div>
						<?php endif;
                            
                            $all_shippings = array();
                            foreach ( $shipping_methods as $shipping_method ) {
                                if ( ! array_key_exists( $shipping_method->get_id(), $all_shippings ) ) {
                                    $all_shippings[ $shipping_method->get_id() ] = array(
                                        'id'    => $shipping_method->get_id(),
                                        'label' => $shipping_method->get_label(),
                                    );
                                }
                            }

						endforeach;?>
						<div style="clear:both;"></div>

						<?php if (! function_exists('wc_add_notice') ): ?>
						<input type="hidden" name="shipping_method" value="multiple_shipping" />
						<?php endif; ?>

                        <input type="hidden" name="all_shipping_methods" value="<?php echo esc_attr( wp_json_encode( $all_shippings ) ); ?>" />
					</div>

				</td>
				<td style="vertical-align: top;">
					<?php
					$shipping_total = WC()->cart->shipping_total;
					$shipping_tax   = WC()->cart->shipping_tax_total;
					$inc_or_exc_tax = '';

					if ( $shipping_total > 0 && wc_tax_enabled() ) {

						// Append price to label using the correct tax settings
						if ( ! WC()->cart->display_totals_ex_tax ) {
							$shipping_total += $shipping_tax;

							if ( 0 < $shipping_tax ) {
								$inc_or_exc_tax = WC()->countries->inc_tax_or_vat();
							}
						}
					}

					echo wc_price( $shipping_total ) . ' ' . $inc_or_exc_tax;
					?>
				</td>
				<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery("tr.shipping").remove();
					});
				<?php
				if ( null == wcms_session_get('shipping_methods') && $rates_available ) {
					echo 'jQuery("body").trigger("update_checkout");';
				}
				?>
				</script>
			</tr>
			<?php
			else:
			?>
			<tr class="multi_shipping">
				<td style="vertical-align: top;" colspan="<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) echo '2'; else echo '1'; ?>">
					<?php _e( 'Shipping Methods', 'wc_shipping_multiple_address' ); ?>

					<?php
					foreach ($packages as $x => $package):
						$shipping_methods   = array();
						$products           = $package['contents'];

						if ($type == 2):
							// If at least one shipping method is available
							// Calculate shipping method rates
							$ship_package['rates'] = array();

							foreach ( WC()->shipping->shipping_methods as $shipping_method ) {

								if ( isset($package['method']) && !in_array($shipping_method->id, $package['method']) ) {
									continue;
								}

								if ( $shipping_method->is_available( $package ) ) {

									// Reset Rates
									$shipping_method->rates = array();

									// Calculate Shipping for package
									$shipping_method->calculate_shipping( $package );

									// Place rates in package array
									if ( ! empty( $shipping_method->rates ) && is_array( $shipping_method->rates ) )
										foreach ( $shipping_method->rates as $rate )
											$ship_package['rates'][$rate->id] = $rate;
								}

							}

							foreach ( $ship_package['rates'] as $method ) {
								if ( $method->id == 'multiple_shipping' ) continue;

								$method->label = esc_html( $method->label );

								if ( $method->cost > 0 ) {
									$method->label .= ' &mdash; ';

									// Append price to label using the correct tax settings
									if ( WC()->cart->display_totals_ex_tax || ! WC()->cart->prices_include_tax ) {
									$method->label .= wc_price( $method->cost );
										if ( $method->get_shipping_tax() > 0 && WC()->cart->prices_include_tax ) {
											$method->label .= ' '.WC()->countries->ex_tax_or_vat();
								}
									} else {
										$method->label .= wc_price( $method->cost + $method->get_shipping_tax() );
										if ( $method->get_shipping_tax() > 0 && ! WC()->cart->prices_include_tax ) {
											$method->label .= ' '.WC()->countries->inc_tax_or_vat();
										}
									}
								}
								$shipping_methods[] = $method;
							}

							// Print a single available shipping method as plain text
							if ( 1 === count( $shipping_methods ) ) {
								$method = $shipping_methods[0];
								echo $method->label;
								echo '<input type="hidden" class="shipping_methods shipping_method" name="'. $field_name .'['. $x .']" value="'.esc_attr( $method->id ).'">';

							// Show multiple shipping methods in a select list
							} elseif ( count( $shipping_methods ) > 1 ) {
								echo '<select class="shipping_methods shipping_method" name="'. $field_name .'['. $x .']">';
								foreach ( $shipping_methods as $method ) {
									if ($method->id == 'multiple_shipping' ) continue;
									echo '<option value="'.esc_attr( $method->id ).'" '.selected( $method->id, (isset($post['shipping_method'])) ? $post['shipping_method'] : '', false).'>';
									echo strip_tags( $method->label );
									echo '</option>';
								}
								echo '</select>';
							} else {
								echo '<p>'.__( '(3) Sorry, it seems that there are no available shipping methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'wc_shipping_multiple_address' ).'</p>';
							}

							$sess_cart_addresses = wcms_session_get( 'cart_item_addresses' );
							if ( $sess_cart_addresses && !empty($sess_cart_addresses) ) {
								echo '<p><a href="'. get_permalink($page_id) .'" class="modify-address-button">'. __( 'Modify address', 'wc_shipping_multiple_address' ) .'</a></p>';
							}
						endif;
					endforeach;
					?>
				</td>
				<td style="vertical-align: top;"><?php echo wc_price( WC()->cart->shipping_total + WC()->cart->shipping_tax_total ); ?></td>
				<script type="text/javascript">
				jQuery("tr.shipping").remove();
				<?php
				if ( null == wcms_session_get('shipping_methods') && $rates_available ) {
					echo 'jQuery("body").trigger("update_checkout");';
				}
				?>
				</script>
			</tr>
			<?php
			endif;
		}

		public function get_available_shipping_methods() {

			$packages = WC()->cart->get_shipping_packages();

			// Loop packages and merge rates to get a total for each shipping method
			$available_methods = array();

			foreach ( $packages as $package ) {
				if ( !isset($package['rates']) || !$package['rates'] ) continue;

				foreach ( $package['rates'] as $id => $rate ) {

					if ( isset( $available_methods[$id] ) ) {
						// Merge cost and taxes - label and ID will be the same
						$available_methods[$id]->cost += $rate->cost;

						foreach ( array_keys( $available_methods[$id]->taxes + $rate->taxes ) as $key ) {
							$available_methods[$id]->taxes[$key] = ( isset( $rate->taxes[$key] ) ? $rate->taxes[$key] : 0 ) + ( isset( $available_methods[$id]->taxes[$key] ) ? $available_methods[$id]->taxes[$key] : 0 );
						}
					} else {
						$available_methods[$id] = $rate;
					}

				}

			}

			return apply_filters( 'wcms_available_shipping_methods', $available_methods );
		}

		/*function available_shipping_methods($shipping_methods) {

			if ( !wcms_session_isset( 'wcms_packages' ) && isset($shipping_methods['multiple_shipping']) ) {
				unset($shipping_methods['multiple_shipping']);
			}

			return $shipping_methods;
		}*/




		public function clear_session( $order_id = '' ) {
			if ( $order_id ) {
				$order = wc_get_order( $order_id );

				if ( $order && in_array( $order->get_status(), array( 'pending', 'failed' ) ) ) {
					return;
				}
			}

			$packages = wcms_session_get('wcms_packages');

			// clear packages transient
			if ( is_array($packages) ) {
				foreach ( $packages as $package ) {
					$package_hash = 'wc_ship_' . md5( wp_json_encode( $package ) );
					delete_transient( $package_hash );
				}
			}

			wcms_session_delete( 'cart_item_addresses' );
			wcms_session_delete( 'wcms_item_addresses' );
			wcms_session_delete( 'cart_address_sigs' );
			wcms_session_delete( 'address_relationships' );
			wcms_session_delete( 'shipping_methods' );
			wcms_session_delete( 'wcms_original_cart' );
			wcms_session_delete( 'wcms_packages' );
            wcms_session_delete( 'wcms_packages_after_tax_calc' );
			wcms_session_delete( 'wcms_item_delivery_dates' );

			do_action( 'wc_ms_cleared_session' );

		}

		public function get_package_shipping_rates( $package = array() ) {

			$_tax = new WC_Tax;

			// See if we have an explicitly set shipping tax class
			if ( $shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' ) ) {
				$tax_class = $shipping_tax_class == 'standard' ? '' : $shipping_tax_class;
			}

			if ( ! empty( $package['destination'] ) ) {
				$country    = $package['destination']['country'];
				$state      = $package['destination']['state'];
				$postcode   = $package['destination']['postcode'];
				$city       = $package['destination']['city'];
			} else {
				// Prices which include tax should always use the base rate if we don't know where the user is located
				// Prices excluding tax however should just not add any taxes, as they will be added during checkout
				if ( get_option( 'wc_prices_include_tax' ) == 'yes' || get_option( 'woocommerce_default_customer_address' ) == 'base' ) {
					$country    = WC()->countries->get_base_country();
					$state      = WC()->countries->get_base_state();
					$postcode   = '';
					$city       = '';
				} else {
					return array();
				}
			}

			// If we are here then shipping is taxable - work it out
			// This will be per order shipping - loop through the order and find the highest tax class rate
			$found_tax_classes = array();
			$matched_tax_rates = array();
			$rates = false;

			// Loop cart and find the highest tax band
			if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
				foreach ( WC()->cart->get_cart() as $item ) {
					$found_tax_classes[] = $item['data']->get_tax_class();
				}
			}

			$found_tax_classes = array_unique( $found_tax_classes );

			// If multiple classes are found, use highest
			if ( sizeof( $found_tax_classes ) > 1 ) {
				if ( in_array( '', $found_tax_classes ) ) {
					$rates = $_tax->find_rates( array(
						'country'   => $country,
						'state'     => $state,
						'city'      => $city,
						'postcode'  => $postcode,
					) );
				} else {
					$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );

					foreach ( $tax_classes as $tax_class ) {
						if ( in_array( $tax_class, $found_tax_classes ) ) {
							$rates = $_tax->find_rates( array(
								'country'   => $country,
								'state'     => $state,
								'postcode'  => $postcode,
								'city'      => $city,
								'tax_class' => $tax_class
							) );
							break;
						}
					}
				}

			// If a single tax class is found, use it
			} elseif ( sizeof( $found_tax_classes ) == 1 ) {

				$rates = $_tax->find_rates( array(
					'country'   => $country,
					'state'     => $state,
					'postcode'  => $postcode,
					'city'      => $city,
					'tax_class' => $found_tax_classes[0]
				) );

			}

			// If no class rate are found, use standard rates
			if ( ! $rates )
				$rates = $_tax->find_rates( array(
					'country'   => $country,
					'state'     => $state,
					'postcode'  => $postcode,
					'city'      => $city,
				) );

			if ( $rates )
				foreach ( $rates as $key => $rate )
					if ( isset( $rate['shipping'] ) && $rate['shipping'] == 'yes' )
						$matched_tax_rates[ $key ] = $rate;

			return $matched_tax_rates;

		}

		public function get_cart_item_subtotal( $cart_item ) {

			$_product   = $cart_item['data'];
			$quantity   = $cart_item['quantity'];

			$price      = $_product->get_price();
			$taxable    = $_product->is_taxable();

			if ( $taxable ) {
				$tax_display_mode = version_compare( WC_VERSION, '4.4', '<' ) ? WC()->cart->tax_display_cart : WC()->cart->get_tax_price_display_mode();

				if ( $tax_display_mode == 'excl' ) {
					// WC 3.0 compatibility check
					$row_price        = is_callable( 'wc_get_price_excluding_tax' ) ? wc_get_price_excluding_tax( $_product, array( 'qty' => $quantity ) ) : $_product->get_price_excluding_tax( $quantity );

				} else {
					// WC 3.0 compatibility check
					$row_price        = is_callable( 'wc_get_price_including_tax' ) ? wc_get_price_including_tax( $_product, array( 'qty' => $quantity ) ) : $_product->get_price_excluding_tax( $quantity );

				}

				// Non-taxable
			} else {

				$row_price        = $price * $quantity;

			}

			return $row_price;

		}


		public function get_product_shipping_method( $product_id ) {
			$method         = false;
			$settings       = $this->settings;
			$product_cats   = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

			// look for direct product matches
			$matched = false;
			foreach ( $settings as $idx => $setting ) {
				if ( in_array($product_id, $setting['products']) ) {
					return $setting['method'];
				}
			}

			if (! $matched ) {
				// look for category matches
				foreach ( $settings as $idx => $setting ) {
					foreach ( $product_cats as $product_cat_id ) {
						if ( in_array($product_cat_id, $setting['categories']) ) {
							return $setting['method'];
						}
					}
				}
			}

			return $method;
		}

		public function packages_have_different_methods($packages = array()) {
			$last_method    = false;
			$_return        = false;

			foreach ( $packages as $package ) {
				if ( isset($package['method']) ) {
					if (! $last_method ) {
						$last_method = $package['method'];
					} else {
						if ( $last_method != $package['method']) {
							$_return = true;
							break;
						}
					}
				}
			}

			return apply_filters( 'wc_ms_packages_have_different_methods', $_return, $packages );
		}

		public function packages_have_different_origins($packages = array()) {
			$last_origin    = false;
			$_return        = false;

			foreach ( $packages as $package ) {
				if ( isset($package['origin']) ) {
					if (! $last_origin ) {
						$last_origin = $package['origin'];
					} else {
						if ( $last_origin != $package['origin']) {
							$_return = true;
							break;
						}
					}
				}
			}

			return apply_filters( 'wc_ms_packages_have_different_origins', $_return, $packages );
		}

		public function packages_contain_methods( $packages = array() ) {
			$return = false;

			foreach ( $packages as $package ) {
				if ( isset($package['method'])) {
					$return = true;
					break;
				}
			}

			return apply_filters( 'wc_ms_packages_contain_methods', $return, $packages );
		}

		public function clear_packages_cache() {

			WC()->cart->calculate_totals();
			$packages = WC()->cart->get_shipping_packages();

			foreach ( $packages as $idx => $package ) {
				$package_hash = 'wc_ship_' . md5( wp_json_encode( $package ) );
				delete_transient( $package_hash );
			}
		}

		public function is_address_empty( $address_array ) {
			if ( empty( $address_array['country'] ) ) {
				return true;
			}

			$address_fields = WC()->countries->get_address_fields( $address_array['country'], 'shipping_' );

			foreach ( $address_fields as $key => $field ) {
				$key = str_replace( 'shipping_', '', $key );

				if ( isset( $field['required'] ) && $field['required'] && empty( $address_array[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}


		public function generate_address_session( $packages ) {

			$fields     = WC()->countries->get_address_fields( WC()->countries->get_base_country(), 'shipping_' );
			$data       = array();
			$rel        = array();

			foreach ( $packages as $pkg_idx => $package ) {

				if (
					! isset( $package['destination'] ) ||
					empty( $package['destination']['postcode'] ) ||
					empty( $package['destination']['country'] )
				) {
					continue;
				}

				$items = $package['contents'];

				foreach ( $items as $cart_key => $item ) {

					$qty            = $item['quantity'];

					$product_id     = $item['product_id'];
					$sig            = $cart_key .'_'. $product_id .'_';
					$address_id     = 0;

					$i = 1;
					for ( $x = 0; $x < $qty; $x++ ) {
						$rel[ $address_id ][]  = $cart_key;


						while ( isset($data['shipping_first_name_'. $sig . $i]) ) {
							$i++;
						}
						$_sig = $sig . $i;

						if ( $fields ) {
							foreach ( $fields as $key => $field ) {
								$address_key                = str_replace( 'shipping_', '', $key );
								$data[ $key . '_' . $_sig ] = $package['destination'][ $address_key ];
							}
						}
					}
				}
			}

			wcms_session_set( 'cart_item_addresses', $data );
			wcms_session_set( 'address_relationships', $rel );

		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_shipping_multiple_addresses_init' );

/**
 * Initializes the extension.
 *
 * @since 3.6.15
 * @return void
 */
function woocommerce_shipping_multiple_addresses_init() {
	load_plugin_textdomain( 'wc_shipping_multiple_address', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_shipping_multiple_addresses_missing_wc_notice' );
		return;
	}

	$GLOBALS['wcms'] = new WC_Ship_Multiple();
}
