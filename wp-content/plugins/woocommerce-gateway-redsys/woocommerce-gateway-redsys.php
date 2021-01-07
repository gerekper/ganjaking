<?php
/**
 * Plugin Name: WooCommerce Servired/RedSys Spain Gateway
 * Plugin URI: https://woocommerce.com/products/redsys-gateway/
 * Description: Extends WooCommerce with RedSys gateway.
 * Version: 13.0.0
 * Author: José Conti
 * Author URI: https://www.joseconti.com/
 * Tested up to: 5.6
 * WC requires at least: 3.0
 * WC tested up to: 4.8
 * Woo: 187871:50392593e834002d8bee386333d1ed3c
 * Text Domain: woocommerce-redsys
 * Domain Path: /languages/
 * Copyright: (C) 2013 - 2021 José Conti
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'REDSYS_VERSION' ) ) {
	define( 'REDSYS_VERSION', '13.0.0' );
}

if ( ! defined( 'REDSYS_PLUGIN_URL' ) ) {
	define( 'REDSYS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'REDSYS_PLUGIN_PATH' ) ) {
	define( 'REDSYS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'REDSYS_CHECK_WOO_CONNECTION' ) ) {
	define( 'REDSYS_CHECK_WOO_CONNECTION', true );
}

if ( ! defined( 'REDSYS_POST_UPDATE_URL' ) ) {
	define( 'REDSYS_POST_UPDATE_URL', 'https://redsys.joseconti.com/2020/12/28/woocommerce-redsys-gateway-13-0-x/' );
}

if ( ! defined( 'REDSYS_POST_PSD2_URL' ) ) {
	define( 'REDSYS_POST_PSD2_URL', 'https://redsys.joseconti.com/2019/09/05/redsys-y-psd2-o-sca/' );
}

if ( ! defined( 'REDSYS_INSTALL_URL' ) ) {
	define( 'REDSYS_INSTALL_URL', 'https://redsys.joseconti.com/primeros-pasos-con-redsys-y-woocommerce/' );
}

if ( ! defined( 'REDSYS_TELEGRAM_SIGNUP' ) ) {
	define( 'REDSYS_TELEGRAM_SIGNUP', 'https://t.me/wooredsys' );
}

if ( ! defined( 'REDSYS_REVIEW' ) ) {
	define( 'REDSYS_REVIEW', 'https://woocommerce.com/sso?product=187871' );
}

if ( ! defined( 'REDSYS_TICKET' ) ) {
	define( 'REDSYS_TICKET', 'https://woocommerce.com/my-account/create-a-ticket/' );
}

if ( ! defined( 'REDSYS_GPL' ) ) {
	define( 'REDSYS_GPL', 'https://redsys.joseconti.com/2020/06/21/vivir-gpl-o-explotar-gpl/' );
}

require_once REDSYS_PLUGIN_PATH . 'includes/defines.php';

/**
 * Required functions
 * Copyright: (C) 2013 - 2021 José Conti
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once REDSYS_PLUGIN_PATH . 'woo-includes/woo-functions.php';
}

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function WCPSD2() {
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys-psd2.php'; // PSD2 class for Redsys
	return new WC_Gateway_Redsys_PSD2();
}

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function WCRed() {
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys-global.php'; // Global class for global functions
	return new WC_Gateway_Redsys_Global();
}

// Site Health
require_once REDSYS_PLUGIN_CLASS_PATH . 'class-redsys-site-health.php';

require_once REDSYS_PLUGIN_NOTICE_PATH . 'notices.php';
require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-settings-tab-redsys-sort-invoices.php';
require_once REDSYS_PLUGIN_CLASS_PATH . 'class-redsys-card-images.php';

if ( ! class_exists( 'RedsysAPI' ) ) {
	if ( version_compare( PHP_VERSION, '7.0.0', '<' ) ) {
		require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'apiRedsys5.php';
		define( 'REDSYS_API_LOADED', 'yes' );
	} else {
		require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'apiRedsys7.php';
		define( 'REDSYS_API_LOADED', 'yes' );
	}
}

if ( ! class_exists( 'RedsysAPIWs' ) ) {
	if ( version_compare( PHP_VERSION, '7.0.0', '<' ) ) {
		require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'apiRedsysWs5.php';
		define( 'REDSYS_API_LOADED_WS', 'yes' );
	} else {
		require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'apiRedsysWs7.php';
		define( 'REDSYS_API_LOADED_WS', 'yes' );
	}
}

require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'initRedsysApi.php';

if ( defined( 'REDSYS_WOOCOMMERCE_VERSION' ) ) {
	return;
}

add_action( 'plugins_loaded', 'woocommerce_gateway_redsys_premium_init', 0 );

/**
 * Plugin updates
 * Copyright: (C) 2013 - 2021 José Conti
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '50392593e834002d8bee386333d1ed3c', '187871' );
function woocommerce_gateway_redsys_premium_init() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	/**
	 * Localisation
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	load_plugin_textdomain( 'woocommerce-redsys', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	function redsys_users_test() {

		$selections = (array) $this->testforuserid;
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="woocommerce_redsys_testforuserid"><?php esc_html_e( 'Select Allowed Users', 'woocommerce-redsys' ); ?></label>
			</th>
			<td class="forminp">
				<select multiple="multiple" name="woocommerce_redsys_testforuserid[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose Users', 'woocommerce-redsys' ); ?>" aria-label="<?php esc_attr_e( 'User', 'woocommerce-redsys' ); ?>" class="js-woo-allowed-users-settings">
				<?php
				if ( ! empty( $selections ) ) {
					foreach ( $selections as $user_id ) {
						$user_data  = get_userdata( $user_id );
						$user_email = $user_data->user_email;
						if ( ! empty( esc_html( $user_email ) ) ) {
							?>
							<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo esc_html( $user_email ); ?></option>
							<?php
						}
					}
				}
				?>
				</select>
			</td>
		</tr>
		<?php
	}
	/*
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_add_select2_to_users_test() {
		global $current_screen;

		wp_register_script( 'redsys-select2', REDSYS_PLUGIN_URL . 'assets/js/test-users.js', array( 'jquery', 'select2' ) );
		wp_enqueue_script( 'redsys-select2' );
	}

	/*
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_get_users_settings_ajax_callback() {

		$search = $_GET['q'];
		$args   = array(
			'search'         => "*{$search}*",
			'fields'         => 'all',
			'search_columns' => array( 'user_login', 'user_email', 'user_nicename' ),
		);
		// The User Query
		$user_query = new WP_User_Query( $args );
		$users      = $user_query->get_results();

		if ( ! empty( $users ) ) {
			$return = array();
			foreach ( $users as $user ) {
				$user_info = get_userdata( $user->ID );
				$return[]  = array( $user_info->ID, $user_info->user_email );
			}
			echo wp_json_encode( $return );
			die;
		} else {
			die;
		}
	}

	add_action( 'admin_enqueue_scripts', 'redsys_add_select2_to_users_test' );
	add_action( 'wp_ajax_redsys_get_users_settings_search_users', 'redsys_get_users_settings_ajax_callback' );

	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/

	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys.php';

	add_action(
		'admin_notices',
		function() {
			WC_Gateway_redsys::admin_notice_mcrypt_encrypt();
		}
	);

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function woocommerce_add_gateway_redsys_gateway( $methods ) {
		$methods[] = 'WC_Gateway_redsys';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_redsys_gateway' );

	// inlude metaboxes
	require_once REDSYS_PLUGIN_METABOXES_PATH . 'metaboxes.php';

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_add_to_cart_redirect( $checkout_url ) {
		global $woocommerce;

		if ( ! is_checkout() && ! is_wc_endpoint_url() ) {
			$data     = new WC_Gateway_redsys();
			$redirect = $data->get_option( 'checkoutredirect' );

			if ( 'yes' === $redirect ) {
				$checkout_url = wc_get_checkout_url();
			}
		}
		return $checkout_url;
	}
	add_filter( 'woocommerce_add_to_cart_redirect', 'redsys_add_to_cart_redirect' );

	require_once REDSYS_PLUGIN_STATUS_PATH . 'status.php';

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_preauthorized_is_editable( $editable, $order ) {

		if ( 'redsys-pre' === $order->get_status() ) {
			$editable = true;
		}
		return $editable;
	}
	add_filter( 'wc_order_is_editable', 'redsys_preauthorized_is_editable', 10, 2 );

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_add_buttom_preauthorization_ok( $order ) {
		if ( 'redsys-pre' === $order->get_status() ) {
			echo '<button type="button" class="button redsys-confirm-preauthorization">' . esc_html__( 'Confirm Preauthorization', 'woocommerce-redsys' ) . '</button>';
		} else {
			return;
		}
	}
	add_action( 'woocommerce_order_item_add_action_buttons', 'redsys_add_buttom_preauthorization_ok' );

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_add_buttom_charge_deposits( $order ) {
		if ( 'partial-payment' === $order->get_status() ) {
			$amount = 0;

			foreach ( $order->get_items() as $item ) {
				if ( ! empty( $item['is_deposit'] ) ) {
					$deposit_full_amount_ex_vat = '';
					$deposit_full_amount        = '';
					$deposit_full_amount_ex_vat = (float) $item['_deposit_full_amount_ex_tax'];
					$deposit_full_amount        = (float) $item['_deposit_full_amount'];

					if ( ! empty( $deposit_full_amount ) ) {
						$amount = $deposit_full_amount + $amount;
					} else {
						$amount = $deposit_full_amount_ex_vat + $amount;
					}
				}
			}
			$total     = $order->get_total();
			$remainder = $amount - $total;

			echo '<button type="button" class="button redsys-charge-full-deposit">' . esc_html__( 'Collect the remainder With Redsys: ' . $remainder, 'woocommerce-redsys' ) . '</button>';
		} else {
			return;
		}
	}
	add_action( 'woocommerce_order_item_add_action_buttons', 'redsys_add_buttom_charge_deposits' );

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_css() {
		global $post_type;

		$current_screen = get_current_screen();

		if ( 'shop_order' === $post_type || 'woocommerce_page_wc-settings' === $current_screen->id ) {
			wp_register_style( 'redsys-css', plugins_url( 'assets/css/redsys-css.css', __FILE__ ), array(), REDSYS_VERSION );
			wp_enqueue_style( 'redsys-css' );
		}

	}
	add_action( 'admin_enqueue_scripts', 'redsys_css' );

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_add_front_css() {

		if ( is_wc_endpoint_url( 'add-payment-method' ) ) {
			wp_enqueue_style( 'redsys-style-front', REDSYS_PLUGIN_URL . 'assets/css/redsys-add-payment-method.css' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'redsys_add_front_css' );

	// public static
	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_preauthorized_js() {
		global $post;

		$screen = get_current_screen();
		if ( ! is_admin() && 'shop_order' !== $screen->id ) {
			return;
		}

		wp_enqueue_script( 'redsysajax-script', plugins_url( '/assets/js/preauthorizations.js', __FILE__ ), array( 'jquery', 'stupidtable', 'jquery-tiptip' ), '1.0.0', true );

		if ( isset( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			$post_id = '';
		}
		$params = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'postid'   => $post_id,
		);
		wp_localize_script( 'redsysajax-script', 'redsys_preauthorizations', $params );
	}
	add_action( 'admin_enqueue_scripts', 'redsys_preauthorized_js' );

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_charge_deposit_js() {
		global $post;

		$screen = get_current_screen();
		if ( ! is_admin() && 'shop_order' !== $screen->id ) {
			return;
		}

		wp_enqueue_script( 'redsysajax-script-2', plugins_url( '/assets/js/woo-deposits-charge.js', __FILE__ ), array( 'jquery', 'stupidtable', 'jquery-tiptip' ), '1.0.0', true );

		if ( isset( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			$post_id = '';
		}
		$params = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'postid'   => $post_id,
		);
		wp_localize_script( 'redsysajax-script-2', 'redsys_charge_depo', $params );
	}
	add_action( 'admin_enqueue_scripts', 'redsys_charge_deposit_js' );

	$redsys_options = get_option( 'woocommerce_redsys_settings' );

	// Adding all Redsys Gateways.

	$private_product     = WCRed()->get_redsys_option( 'privateproduct', 'redsys' );
	$sent_email_template = WCRed()->get_redsys_option( 'sentemailscustomers', 'redsys' );
	$thankyoucheck       = WCRed()->get_redsys_option( 'sendemailthankyou', 'redsys' );
	$thankyourecipe      = WCRed()->get_redsys_option( 'showthankyourecipe', 'redsys' );

	// Adding Private Products.
	if ( 'yes' === $private_product ) {
		require_once REDSYS_PLUGIN_PATH . 'includes/private-products.php';
	}

	// Adding emails Templates.
	if ( 'yes' === $sent_email_template ) {
		require_once REDSYS_PLUGIN_PATH . 'includes/emails/class-redsys-wc-email.php';
	}

	// Adding Thank you Check.
	if ( 'yes' === $thankyoucheck ) {
		require_once REDSYS_PLUGIN_PATH . 'includes/thank-you-checks.php';
	}

	// Adding Thank you Recipe.
	if ( 'yes' === $thankyourecipe ) {
		require_once REDSYS_PLUGIN_PATH . 'includes/thank-you-receipe.php';
	}

	// Adding all Redsys Gateways.

	// Adding Bizum.
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-bizum-redsys.php'; // Bizum Version 6.0.

	// Adding MasterPass.
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-masterpass-redsys.php'; // MasterPass Version 7.0.

	// Adding Preauthorizations.
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-preauthotizations-redsys.php'; // Specific Gateway for Preathorizations Version 8.0.

	// Adding Redsys Bank Transfer
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-redsys-bank-transfer.php'; // Bank Transfer Version 9.0.

	// Adding InSIte
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-insite-redsys.php'; // Insite version 10.0.

	// Adding Direct Debit stand alone.
	require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-direct-debit-redsys.php'; // Insite version 11.0.

	// Adding Google Pay
	// require_once REDSYS_PLUGIN_CLASS_PATH . 'class-wc-gateway-googlepay-redsys.php'; // Google Pay version 12.0.

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_add_actions() {
		if ( ! is_checkout() && ! is_wc_endpoint_url() ) {
			// $redsys_class = new WC_Gateway_redsys();.

			// Ajax Preautorizaciones.
			add_action( 'wp_ajax_redsys_preauth_action', array( 'WC_Gateway_Preauthorizations_Redsys', 'redsys_preauthorized_js_callback' ) );
			// Ajax carga deposits.
			add_action( 'wp_ajax_redsys_charge_depo_action', array( 'WC_Gateway_redsys', 'redsys_charge_depo_js_callback' ) );
		}
		// $redsys_class_insite = new WC_Gateway_InSite_Redsys();!
		add_action( 'wp_ajax_check_token_insite_from_action', array( 'WC_Gateway_InSite_Redsys', 'check_token_insite_from_action' ) );
		add_action( 'wp_ajax_nopriv_check_token_insite_from_action', array( 'WC_Gateway_InSite_Redsys', 'check_token_insite_from_action' ) );
	}
	add_action( 'admin_init', 'redsys_add_actions' );
	add_filter( 'bulk_actions-edit-shop_order', array( 'WC_Gateway_Preauthorizations_Redsys', 'preauthorizationsredsys_add_bulk_actions' ) );
	add_filter( 'bulk_actions-edit-shop_order', array( 'WC_Gateway_Redsys', 'redsys_add_bulk_actions' ) );
	add_filter( 'handle_bulk_actions-edit-shop_order', array( 'WC_Gateway_Preauthorizations_Redsys', 'preauthorizationsredsys_bulk_actions_handler' ), 10, 3 );
	add_filter( 'handle_bulk_actions-edit-shop_order', array( 'WC_Gateway_Redsys', 'redsys_bulk_actions_handler' ), 10, 3 );

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_woo_add_head_text() {
		echo '<!-- This site is powered by WooCommerce Redsys Gateway v.' . REDSYS_VERSION . ' - https://woocommerce.com/products/redsys-gateway/ -->';
	}
	add_action( 'wp_head', 'redsys_woo_add_head_text' );

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_chekout_button_text( $order_button_text ) {

		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( $chosen_payment_method === 'redsys' ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'redsys' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( $chosen_payment_method === 'insite' ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'insite' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( $chosen_payment_method === 'preauthorizationsredsys' ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'preauthorizationsredsys' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( $chosen_payment_method === 'masterpass' ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'masterpass' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( $chosen_payment_method === 'redsysbank' ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'redsysbank' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( $chosen_payment_method === 'bizumredsys' ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'bizumredsys' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( $chosen_payment_method === 'redsysdirectdebit' ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'redsysdirectdebit' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		}
		?>
		<script type="text/javascript">
			(function($){
				$('form.checkout').on( 'change', 'input[name^="payment_method"]', function() {
					var t = { updateTimer: !1,  dirtyInput: !1,
						reset_update_checkout_timer: function() {
							clearTimeout(t.updateTimer)
						},  trigger_update_checkout: function() {
								t.reset_update_checkout_timer(), t.dirtyInput = !1,
								$(document.body).trigger("update_checkout")
							}
						};
						t.trigger_update_checkout();
					});
				}
			)(jQuery);
		</script>
		<?php
		return $order_button_text;
	}
	add_filter( 'woocommerce_order_button_text', 'redsys_chekout_button_text' );

	// Peronalización de los colores según la pasrela en el checkout!

	/**
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_color_button_text( $html ) {

		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( $chosen_payment_method === 'redsys' ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'redsys' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'redsys' );

			if ( ! empty( $textb ) ) {
				$textb = 'background-color:' . $textb . ';';
			} else {
				$textb = '';
			}
			if ( ! empty( $text ) ) {
				$text = 'color:' . $text . ';';
			} else {
				$text = '';
			}

			if ( $textb !== '' || $text !== '' ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( $chosen_payment_method === 'insite' ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'insite' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'insite' );

			if ( ! empty( $textb ) ) {
				$textb = 'background-color:' . $textb . ';';
			} else {
				$textb = '';
			}
			if ( ! empty( $text ) ) {
				$text = 'color:' . $text . ';';
			} else {
				$text = '';
			}

			if ( $textb !== '' || $text !== '' ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( $chosen_payment_method === 'preauthorizationsredsys' ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'preauthorizationsredsys' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'preauthorizationsredsys' );

			if ( ! empty( $textb ) ) {
				$textb = 'background-color:' . $textb . ';';
			} else {
				$textb = '';
			}
			if ( ! empty( $text ) ) {
				$text = 'color:' . $text . ';';
			} else {
				$text = '';
			}

			if ( $textb !== '' || $text !== '' ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( $chosen_payment_method === 'masterpass' ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'masterpass' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'masterpass' );

			if ( ! empty( $textb ) ) {
				$textb = 'background-color:' . $textb . ';';
			} else {
				$textb = '';
			}
			if ( ! empty( $text ) ) {
				$text = 'color:' . $text . ';';
			} else {
				$text = '';
			}

			if ( $textb !== '' || $text !== '' ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( $chosen_payment_method === 'redsysbank' ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'redsysbank' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'redsysbank' );

			if ( ! empty( $textb ) ) {
				$textb = 'background-color:' . $textb . ';';
			} else {
				$textb = '';
			}
			if ( ! empty( $text ) ) {
				$text = 'color:' . $text . ';';
			} else {
				$text = '';
			}

			if ( $textb !== '' || $text !== '' ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( $chosen_payment_method === 'bizumredsys' ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'bizumredsys' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'bizumredsys' );

			if ( ! empty( $textb ) ) {
				$textb = 'background-color:' . $textb . ';';
			} else {
				$textb = '';
			}
			if ( ! empty( $text ) ) {
				$text = 'color:' . $text . ';';
			} else {
				$text = '';
			}

			if ( $textb !== '' || $text !== '' ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( $chosen_payment_method === 'redsysdirectdebit' ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'redsysdirectdebit' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'redsysdirectdebit' );

			if ( ! empty( $textb ) ) {
				$textb = 'background-color:' . $textb . ';';
			} else {
				$textb = '';
			}
			if ( ! empty( $text ) ) {
				$text = 'color:' . $text . ';';
			} else {
				$text = '';
			}
			if ( $textb !== '' || $text !== '' ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		}
		?>
		<script type="text/javascript">
			(function($){
				$('form.checkout').on( 'change', 'input[name^="payment_method"]', function() {
					var t = { updateTimer: !1,  dirtyInput: !1,
						reset_update_checkout_timer: function() {
							clearTimeout(t.updateTimer)
						},  trigger_update_checkout: function() {
								t.reset_update_checkout_timer(), t.dirtyInput = !1,
								$(document.body).trigger("update_checkout")
							}
						};
						t.trigger_update_checkout();
					});
				}
			)(jQuery);
		</script>
		<?php
		return $html;
	}
	add_filter( 'woocommerce_order_button_html', 'redsys_color_button_text' );
}
