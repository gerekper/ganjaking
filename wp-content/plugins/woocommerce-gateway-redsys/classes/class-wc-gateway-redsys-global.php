<?php
/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2021 José Conti
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Gateway class
 */
/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
 * Copyright: (C) 2013 - 2021 José Conti
 */
class WC_Gateway_Redsys_Global {

	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_option( $option, $gateway ) {

		$options = get_option( 'woocommerce_' . $gateway . '_settings' );

		if ( ! empty( $options ) ) {
			$redsys_options = maybe_unserialize( $options );
			if ( array_key_exists( $option, $redsys_options ) ) {
				$option_value = $redsys_options[ $option ];
				return $option_value;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function return_help_notice() {
		?>
		<div class="redsysnotice">
			<span class="dashicons dashicons-welcome-learn-more redsysnotice-dash"></span>
			<span class="redsysnotice__content"><?php printf( __( 'For Redsys Help: Check WooCommerce.com Plugin <a href="%1$s" target="_blank" rel="noopener">Documentation page</a> for setup, <a href="%2$s" target="_blank" rel="noopener">FAQ page</a> for working problems, or open a <a href="%3$s" target="_blank" rel="noopener">Ticket</a> for support', 'woocommerce-redsys' ), 'https://docs.woocommerce.com/document/redsys-servired-sermepa-gateway/', 'https://redsys.joseconti.com/redsys-for-woocommerce/', 'https://woocommerce.com/my-account/tickets/' ); ?><span>
		</div>
		<?php
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_token_r( $order_id ) {

		if ( ! is_a( $order_id, 'WC_Abstract_Order' ) ) {
			$order = wc_get_order( $order_id );
		}
		if ( $order ) {
			foreach ( $order->get_items() as $item_id => $item_values ) {
				$product_id = $item_values->get_product_id();
				$get        = get_post_meta( $product_id, '_redsystokenr', true );
				if ( 'yes' === $get ) {
					return true;
				}
				continue;
			}
			return false;
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_redsys_subscription_checkout( $product_id ) {

		$get = get_post_meta( $product_id, '_redsystokenr', true );
		
		if ( 'yes' === $get ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_yith_subscription_checkout( $product_id ) {

		$get = get_post_meta( $product_id, '_ywsbs_subscription', true );
		
		if ( 'yes' === $get ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_woo_subscription_checkout( $product_id ) {
		
		if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product_id ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_all_woo_subscription_checkout( $product_id ) {

		$get = get_post_meta( $product_id, '_ywsbs_subscription', true );
		
		if ( 'yes' === $get ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function clean_data( $out ) {

		$out = str_replace( 'Á', 'A', $out );
		$out = str_replace( 'À', 'A', $out );
		$out = str_replace( 'Ä', 'A', $out );
		$out = str_replace( 'É', 'E', $out );
		$out = str_replace( 'È', 'E', $out );
		$out = str_replace( 'Ë', 'E', $out );
		$out = str_replace( 'Í', 'I', $out );
		$out = str_replace( 'Ì', 'I', $out );
		$out = str_replace( 'Ï', 'I', $out );
		$out = str_replace( 'Ó', 'O', $out );
		$out = str_replace( 'Ò', 'O', $out );
		$out = str_replace( 'Ö', 'O', $out );
		$out = str_replace( 'Ú', 'U', $out );
		$out = str_replace( 'Ù', 'U', $out );
		$out = str_replace( 'Ü', 'U', $out );
		$out = str_replace( 'á', 'a', $out );
		$out = str_replace( 'à', 'a', $out );
		$out = str_replace( 'ä', 'a', $out );
		$out = str_replace( 'é', 'e', $out );
		$out = str_replace( 'è', 'e', $out );
		$out = str_replace( 'ë', 'e', $out );
		$out = str_replace( 'í', 'i', $out );
		$out = str_replace( 'ì', 'i', $out );
		$out = str_replace( 'ï', 'i', $out );
		$out = str_replace( 'ó', 'o', $out );
		$out = str_replace( 'ò', 'o', $out );
		$out = str_replace( 'ö', 'o', $out );
		$out = str_replace( 'ú', 'u', $out );
		$out = str_replace( 'ù', 'u', $out );
		$out = str_replace( 'ü', 'u', $out );
		$out = str_replace( 'Ñ', 'N', $out );
		$out = str_replace( 'ñ', 'n', $out );
		$out = str_replace( '&', '-', $out );
		$out = str_replace( '<', ' ', $out );
		$out = str_replace( '>', ' ', $out );
		$out = str_replace( '/', ' ', $out );
		$out = str_replace( '"', ' ', $out );
		$out = str_replace( "'", ' ', $out );
		$out = str_replace( '"', ' ', $out );
		$out = str_replace( '?', ' ', $out );
		$out = str_replace( '¿', ' ', $out );
		$out = str_replace( 'º', ' ', $out );
		$out = str_replace( 'ª', ' ', $out );
		$out = str_replace( '#', ' ', $out );
		$out = str_replace( '&', ' ', $out );
		$out = str_replace( '@', ' ', $out );

		return $out;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function set_txnid( $token_num, $redsys_txnid ) {
		if ( $redsys_txnid ) {
			update_option( 'txnid_' . $token_num, $redsys_txnid );
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function set_token_type( $token_num, $type ) {
		if ( $token_num && $type ) {
			update_option( 'token_type_' . $token_num, $type );
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_txnid( $token_num ) {
		if ( $token_num ) {
			$redsys_txnid = get_option( 'txnid_' . $token_num, false );
			if ( $redsys_txnid ) {
				return $redsys_txnid;
			} else {
				return '999999999999999'; // Temporal return for old tokens.
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_token_type( $token_num ) {
		if ( $token_num ) {
			$redsys_token_type = get_option( 'token_type_' . $token_num, false );
			if ( $redsys_token_type ) {
				return $redsys_token_type;
			} else {
				return 'R'; // Temporal return for old tokens.
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_ds_error() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'dserrors.php';

		$dserrors = array();
		$dserrors = redsys_return_dserrors();
		return $dserrors;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_ds_response() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'dsresponse.php';

		$dsresponse = array();
		$dsresponse = redsys_return_dsresponse();
		return $dsresponse;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_msg_error() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'insiteerrors.php';

		$msgerrors = array();
		$msgerrors = redsys_return_insiteerrors();
		return $msgerrors;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_country_codes() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'countries.php';

		$countries = array();
		$countries = redsys_get_country_code();
		return $countries;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_country_codes_3( $country_code_2 ) {

		$countries = array();
		$countries = $this->get_country_codes();

		if ( $countries ) {
			foreach ( $countries as $country => $valor ) {
				$country_2_up = strtoupper( $country_code_2 );
				if ( $country_2_up === $country ) {
					return $valor;
				} else {
					continue;
				}
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function is_ds_error( $error_code = null ) {

		$ds_errors = array();
		$ds_errors = $this->get_ds_error();

		if ( $error_code ) {
			foreach ( $ds_errors as $ds_error => $value ) {
				if ( (string) $ds_error === (string) $error_code ) {
					return true;
				} else {
					continue;
				}
			}
			return false;
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function is_ds_response( $error_code = null ) {

		$ds_response  = array();
		$ds_responses = $this->get_ds_response();

		if ( $error_code ) {
			foreach ( $ds_responses as $ds_response => $value ) {
				if ( (string) $ds_response === (string) $error_code ) {
					return true;
				}
				continue;
			}
			return false;
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function is_msg_error( $error_code = null ) {

		$msg_errors = array();
		$msg_errors = $this->get_msg_error();

		if ( $error_code ) {
			foreach ( $msg_errors as $msg_error => $value ) {
				if ( (string) $msg_error === (string) $error_code ) {
					return true;
				} else {
					continue;
				}
			}
			return false;
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_msg_error_by_code( $error_code = null ) {

		$smg_errors = array();
		$smg_errors = $this->get_msg_error();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $smg_errors ) ) {
					foreach ( $smg_errors as $msg_error => $value ) {
						if ( (string) $msg_error === (string) $error_code ) {
							return $value;
						} else {
							continue;
						}
					}
				}
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_error_by_code( $error_code = null ) {

		$ds_errors = array();
		$ds_errors = $this->get_ds_error();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $ds_errors ) ) {
					foreach ( $ds_errors as $ds_error => $value ) {
						if ( (string) $ds_error === (string) $error_code ) {
							return $value;
						} else {
							continue;
						}
					}
				}
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_response_by_code( $error_code = null ) {

		$ds_responses = array();
		$ds_responses = $this->get_ds_response();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $ds_responses ) ) {
					foreach ( $ds_responses as $ds_response => $value ) {
						if ( (string) $ds_response === (string) $error_code ) {
							return $value;
						} else {
							continue;
						}
					}
				}
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function is_redsys_error( $error_code = null ) {

		if ( $error_code ) {
			if ( $this->is_ds_error( $error_code ) ) {
				return true;
			} elseif ( $this->is_ds_response( $error_code ) ) {
				return true;
			} elseif ( $this->is_msg_error( $error_code ) ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_error( $error_code = null ) {

		if ( $error_code ) {
			if ( $this->is_ds_error( $error_code ) ) {
				return $this->get_error_by_code( $error_code );
			} elseif ( $this->is_ds_response( $error_code ) ) {
				return $this->get_response_by_code( $error_code );
			} elseif ( $this->is_msg_error( $error_code ) ) {
				return $this->get_msg_error_by_code( $error_code );
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_error_type( $error_code = null ) {

		if ( $error_code ) {
			if ( $this->is_ds_error( $error_code ) ) {
				return 'ds_error';
			} elseif ( $this->is_ds_response( $error_code ) ) {
				return 'ds_response';
			} elseif ( $this->is_msg_error( $error_code ) ) {
				return 'msg_error';
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_currencies() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'currencies.php';

		$currencies = array();
		$currencies = redsys_return_currencies();
		return $currencies;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function allowed_currencies() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'allowed-currencies.php';

		$currencies = array();
		$currencies = redsys_return_allowed_currencies();
		return $currencies;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_languages() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'languages.php';

		$languages = array();
		$languages = redsys_return_languages();
		return $languages;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_wp_languages() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'wplanguages.php';

		$languages = array();
		$languages = redsys_return_all_languages_code();
		return $languages;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_orders_type() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'redsys-types.php';

		$types = array();
		$types = redsys_return_types();
		return $types;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_lang_code( $lang = 'en' ) {

		$lang = trim( $lang );

		$languages = array();
		$languages = $this->get_redsys_wp_languages();

		if ( ! empty( $languages ) ) {
			foreach ( $languages as $language => $value ) {
				if ( (string) $language === (string) $lang ) {
					return $value;
				} else {
					continue;
				}
			}
		} else {
			return '2';
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function order_exist( $order_id ) {
		$post_status = get_post_status( $order_id );

		if ( false === $post_status ) {
			return false;
		} else {
			$port_type = get_post_type( $order_id );
			if ( 'shop_order' === $port_type ) {
				return true;
			} else {
				return false;
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function post_exist( $order_id ) {
		$post_status = get_post_status( $order_id );

		if ( false === $post_status ) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function is_redsys_order( $order_id, $type = null ) {

		$post_status = $this->order_exist( $order_id );

		if ( $post_status ) {
			$order        = new WC_Order( $order_id );
			$gateway      = $order->get_payment_method();
			$redsys_types = array();
			$redsys_types = $this->get_orders_type();
			if ( ! empty( $redsys_types ) ) {
				if ( ! $type ) {
					foreach ( $redsys_types as $redsys_type ) {
						if ( (string) $redsys_type === (string) $gateway ) {
							return true;
						}
						continue;
					}
					return false;
				} else {
					if ( $gateway === $type ) {
						return true;
					} else {
						return false;
					}
				}
			}
			return false;
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_gateway( $order_id ) {

		$post_status = $this->order_exist( $order_id );

		if ( $post_status ) {
			$order   = new WC_Order( $order_id );
			$gateway = $order->get_payment_method();
			return $gateway;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_order_date( $order_id ) {
		$date_decoded = str_replace( '%2F', '/', get_post_meta( $order_id, '_payment_date_redsys', true ) );
		if ( ! $date_decoded ) {
			return false;
		}
		return $date_decoded;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_order_hour( $order_id ) {
		$hour_decoded = str_replace( '%3A', ':', get_post_meta( $order_id, '_payment_hour_redsys', true ) );
		if ( ! $hour_decoded ) {
			return false;
		}
		return $hour_decoded;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_order_auth( $order_id ) {
		$auth = get_post_meta( $order_id, '_authorisation_code_redsys', true );
		if ( ! $auth ) {
			return false;
		}
		return $auth;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_order_mumber( $order_id ) {
		$number = get_post_meta( $order_id, '_payment_order_number_redsys', true );
		if ( ! $number ) {
			return false;
		}
		return $number;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_status_pending() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'redsys-status-paid.php';

		$status = array();
		$status = redsys_return_status_paid();
		return apply_filters( 'redsys_status_pending', $status );
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function is_paid( $order_id ) {

		if ( $this->order_exist( $order_id ) ) {

			$order       = $this->get_order( $order_id );
			$status      = $order->get_status();
			$status_paid = array();

			$status_paid = $this->get_status_pending();
			if ( $status_paid ) {
				foreach ( $status_paid as $spaid ) {
					if ( (string) $status === (string) $spaid ) {
						return false;
					}
					continue;
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function is_gateway_enabled( $gateway ) {
		$is_enabled = $this->get_redsys_option( 'enabled', $gateway );

		if ( 'yes' === $is_enabled ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_if_token_is_valid( $token_id ) {

		$token        = WC_Payment_Tokens::get( $token_id );
		$year         = $token->get_expiry_year();
		$month        = $token->get_expiry_month();
		$act_year     = date( 'Y' );
		$act_month    = date( 'm' );
		$delete_token = $this->get_redsys_option( 'deletetoken', 'redsys' );
		if ( $year >= $act_year ) {
			if ( $year > $act_year ) {
				return true;
			} elseif ( $year === $act_year && $month >= $act_month ) {
				return true;
			} else {
				if ( 'yes' === $delete_token ) {
					WC_Payment_Tokens::delete( $token_id );
				}
				return false;
			}
		} else {
			if ( 'yes' === $delete_token ) {
				WC_Payment_Tokens::delete( $token_id );
			}
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_type_exist_in_tokens( $tokens, $type ) {
		foreach ( $tokens as $token ) {
			$token_num  = $token->get_token();
			$token_type = $this->get_token_type( $token_num );
			if ( $token_type === $type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					if ( $valid_token ) {
						return true;
					}
					break;
				} else {
					continue;
				}
			}
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_redsys_users_token( $type = false ) {
		// $type puede ser R (suscripción) o C (principalmente pago con 1 clic) en estos momentos.
		$customer_token = false;
		if ( is_user_logged_in() ) {
			if ( ! $type ) {
				$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'redsys' );
				foreach ( $tokens as $token ) {
					if ( $token->get_gateway_id() === 'redsys' ) {
						$valid_token = $this->check_if_token_is_valid( $token->get_id() );
						if ( $valid_token ) {
							$customer_token = $token->get_token();
						}
						break;
					} else {
						continue;
					}
				}
			} else {
				$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'redsys' );
				foreach ( $tokens as $token ) {
					$token_id  = $token->get_token();
					$type_type = $this->get_token_type( $token_id );
					if ( $type === $type_type ) {
						if ( $token->get_gateway_id() === 'redsys' ) {
							$valid_token = $this->check_if_token_is_valid( $token->get_id() );
							if ( $valid_token ) {
								$customer_token = $token_id;
								break;
							}
						}
						continue;
					} else {
						continue;
					}
				}
			}
		}
		return $customer_token;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_users_token_bulk( $user_id, $type = false ) {
		$customer_token = false;
		$tokens         = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		if ( ! $type ) {
			foreach ( $tokens as $token ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					if ( $valid_token ) {
						return $token->get_token();
					}
				} else {
					continue;
				}
			}
		} else {
			foreach ( $tokens as $token ) {
				$token_id  = $token->get_token();
				$type_type = $this->get_token_type( $token_id );
				if ( $type === $type_type ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					if ( $valid_token ) {
						return $token->get_token();
					}
				} else {
					continue;
				}
			}
			return $customer_token;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function clean_order_number( $ordernumber ) {
		return substr( $ordernumber, 3 );
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function prepare_order_number( $order_id ) {
		$transaction_id  = str_pad( $order_id, 12, '0', STR_PAD_LEFT );
		$transaction_id1 = mt_rand( 1, 999 ); // lets to create a random number
		$transaction_id2 = substr_replace( $transaction_id, $transaction_id1, 0, -9 ); // new order number
		return $transaction_id2;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function redsys_amount_format( $total ) {
		
		if ( 0 == $total || 0.00 == $total ) {
			return 0;
		}
		
		$order_total_sign = number_format( $total, 2, '', '' );
		return $order_total_sign;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function product_description( $order, $gateway ) {
		if ( ! $this->is_redsys_order( $order->get_id() ) ) {
			return;
		}
		$product_id = '';
		$name       = '';
		$sku        = '';
		foreach ( $order->get_items() as $item ) {
			$product_id .= $item->get_product_id() . ', ';
			$name       .= $item->get_name() . ', ';
			$sku        .= get_post_meta( $item->get_product_id(), '_sku', true ) . ', ';
		}
		// Can be order, id, name or sku
		$description_type = $this->get_redsys_option( 'descripredsys', $gateway );

		if ( 'id' === $description_type ) {
			$description = $product_id;
		} elseif ( 'name' === $description_type ) {
			$description = $name;
		} elseif ( 'sku' === $description_type ) {
			$description = $sku;
		} else {
			$description = __( 'Order', 'woocommerce-redsys' ) . ' ' . $order->get_order_number();
		}
		return $description;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_psd2_arg( $order, $gateway ) {
		if ( 'yes' === $this->get_redsys_option( 'psd2', $gateway ) ) {
			return $arg;
		} else {
			return '';
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_order_has_yith_subscriptions( $oder_id ) {
		$order    = $this->get_order( $oder_id );
		$has_meta = $order->get_meta( 'subscriptions' );
		if ( $has_meta ) {
			return true;
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function create_add_payment_method_number() {

		$current_number = get_option( 'number_ad_paymnt_mehod' );

		if ( ! $current_number ) {
			update_option( 'number_ad_paymnt_mehod', '1' );
			$number_to_send = str_pad( '1', 12, '0', STR_PAD_LEFT );
			return $number_to_send;
		} else {
			$new_number    = intval( $current_number ) + 1;
			$string_number = strval( $new_number );
			update_option( 'number_ad_paymnt_mehod', $string_number );
			$number_to_send = str_pad( $string_number, 12, '0', STR_PAD_LEFT );
			return $number_to_send;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function create_checkout_insite_number() {

		$current_number = get_option( 'number_insite_checkout' );

		if ( ! $current_number ) {
			update_option( 'number_insite_checkout', '1' );
			$number_to_send = '1' . str_pad( '1', 11, '0', STR_PAD_LEFT );
			return $number_to_send;
		} else {
			$new_number    = intval( $current_number ) + 1;
			$string_number = strval( $new_number );
			update_option( 'number_insite_checkout', $string_number );
			$number_to_send = '1' . str_pad( $string_number, 11, '0', STR_PAD_LEFT );
			return $number_to_send;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_url_add_payment_method( $gateway, $user_id, $token_type ) {

		$number = $this->create_add_payment_method_number();
		set_transient( $number, $user_id, 600 );
		set_transient( $number . '_get_method', 'yes', 600 );
		set_transient( $number . '_token_type', $token_type, 600 );
		// wc_get_endpoint_url( 'add_payment_method' )
		$pay_url = wc_get_endpoint_url( 'add-redsys-method', $number, wc_get_endpoint_url( 'add-payment-method' ) );

		return add_query_arg(
			array(
				'redsys-payment-method' => $number,
				'redsys-gateway'        => $gateway,
				'redsys-token'          => $token_type,
			),
			$pay_url
		);
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_url_redsys_payment( $order_id ) {

		$pay_url = home_url() . '/redirect-redsys-pay';
		// $pay_url = WC()->api_request_url( 'redirect-redsys-pay' );

		return add_query_arg(
			array(
				'redsys-order-id' => $order_id,
			),
			$pay_url
		);
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function subscription_plugin_exist() {
		if ( function_exists( 'wcs_order_contains_subscription' ) ) {
			return true;
		} elseif ( defined( 'YITH_YWSBS_PREMIUM' ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_all_tokens( $user_id, $type ) {
		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		foreach ( $tokens as $token ) {
			$token_id  = $token->get_token();
			$type_type = $this->get_token_type( $token_id );
			if ( $type === $type_type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					// if ( $valid_token ) {
						echo $token->get_token() . "\r\n";
					// }
					continue;
				} else {
					continue;
				}
			} else {
				continue;
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_tokens_exist( $user_id, $type ) {
		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		foreach ( $tokens as $token ) {
			$token_id  = $token->get_token();
			$type_type = $this->get_token_type( $token_id );
			if ( $type === $type_type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					return true;
				} else {
					continue;
				}
			} else {
				continue;
			}
		}
		return false;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_all_tokens_checkout( $user_id, $type ) {
		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		foreach ( $tokens as $token ) {
			$token_id  = $token->get_token();
			$type_type = $this->get_token_type( $token_id );
			if ( $type === $type_type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					// if ( $valid_token ) {
						//$toke_num = $token->get_token();
						$token_id = $token->get_id();
						$brand    = $token->get_card_type();
						$last4    = $token->get_last4();
						$month    = $token->get_expiry_month();
						$year     = substr( $token->get_expiry_year(), -2 );
						echo '<input class="input-radio" type="radio" id="' . $token_id . '" name="token" value="' . $token_id . '"> ' . $brand . ' ' . __( 'ending in', 'woocommerce-redsys' ) . ' ' . $last4 . ' ' . '(' . __( 'expires ', 'woocommerce-redsys' ) .  $month . '/' . $year . ')</><br />';
					// }
					continue;
				} else {
					continue;
				}
			} else {
				continue;
			}
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function order_contains_subscription( $order_id ) {
		if ( WCRed()->check_order_has_yith_subscriptions( $order_id ) ) {
			return true;
		} elseif ( WCRed()->get_redsys_token_r( $order_id ) ) {
			return true;
		} elseif ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
			return false;
		} elseif ( wcs_order_contains_subscription( $order_id ) ) {
			return true;
		} elseif ( wcs_order_contains_resubscribe( $order_id ) ) {
			return true;
		} elseif ( wcs_order_contains_renewal( $order_id ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_order_is_paid_loop( $order_id ) {
		$x = 0;
		do {
			sleep( 5 );
			$result = $this->is_paid( $order_id );
			$x++;
		} while ( $x <= 20 && false === $result );
		if ( $result ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function collect_invoice_by_id( $order_id ) {
		$order  = new WC_Order( $order_id );
		$result = WC_Gateway_Redsys::charge_invoive_by_order( $order );
		return $result;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	public static function cart_needs_payment( $needs_payment, $cart ) {
		
		$global = new WC_Gateway_Redsys_Global();
		/*
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$token_r = $global->get_users_token_bulk( $user_id, 'R' );
			if ( $token_r ) {
				return $needs_payment;
			}
		}
		*/
		foreach ( $cart->get_cart() as $item => $values ) {
			$product_id = $values['product_id'];
			$get        = get_post_meta( $product_id, '_redsystokenr', true );
			if ( false === $needs_payment && 0 == $cart->total && 'yes' === $get ) {
				$needs_payment = true;
				return $needs_payment;
			}
		}
		return $needs_payment;
	}
	public static function order_needs_payment( $needs_payment, $order, $valid_order_statuses ) {
		// Skips checks if the order already needs payment.
		if ( $needs_payment ) {
			return $needs_payment;
		}
		
		if ( $order->get_total() > 0 ) {
			return $needs_payment;
		}
		
		$order_id  = $order->get_id();
		$get_token = get_post_meta( $order_id, '_redsystokenr', true );
		
		if ( 'yes' === $get_token ) {
			$needs_payment = true;
		}
		return $needs_payment;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_simple_product_subscription( $product_id ) {
		
		// Checking get token subscription
		if ( $this->check_redsys_subscription_checkout( $product_id ) ) {
			return 'R';
		} elseif ( $this->check_yith_subscription_checkout( $product_id ) ) {
			return 'R';
		} elseif ( $this->check_woo_subscription_checkout( $product_id ) ) {
			return 'R';
		} else {
			return 'C';
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function check_card_for_subscription( $the_card ) {
		
		foreach ( $the_card as $cart_item_key => $cart_item ) {

			$product_id = $cart_item['product_id'];

			if ( 'subscription' === get_the_terms( $product_id,'product_type' )[0]->slug ) {
				return 'R';
			} elseif ( 'variable-subscription' === get_the_terms( $product_id,'product_type' )[0]->slug ) {
				return 'R';
			} elseif ( 'simple' === get_the_terms( $product_id,'product_type' )[0]->slug ) {
				$token_type = $this->check_simple_product_subscription( $product_id );
				if ( 'R' === $token_type ) {
					return $token_type;
				}
				continue;
			}
			continue;
		}
		return 'C';
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_token_by_id( $token_id ) {
		
		$token = WC_Payment_Tokens::get( (int)$token_id );
		return $token->get_token();
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function get_card_brand( $dscardbrand = false ) {
		
		if ( ! $dscardbrand ) {
			return __( 'Unknown', 'woocommerce-redsys' );
		}
		if ( '1' === $dscardbrand ) {
			$dscardbrand = 'Visa';
		} elseif ( '2' === $dscardbrand ) {
			$dscardbrand = 'MasterCard';
		} elseif ( '8' === $dscardbrand ) {
			$dscardbrand = 'Amex';
		} elseif ( '9' === $dscardbrand ) {
			$dscardbrand = 'JCB';
		} elseif ( '6' === $dscardbrand ) {
			$dscardbrand = 'Diners';
		} elseif ( '22' === $dscardbrand ) {
			$dscardbrand = 'UPI';
		} elseif ( '7' === $dscardbrand ) {
			$dscardbrand = 'Privada';
		} else {
			$dscardbrand = __( 'Unknown', 'woocommerce-redsys' );
		}
		return $dscardbrand;
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function remove_token( $data ) {
		
		$merchant_code       = $data['merchant_code'];
		$merchant_identifier = $data['merchant_identifier'];
		$order_id            = $data['order_id'];
		$terminal            = ltrim( $data['terminal'], '0');
		$secretsha256        = $data['sha256'];
		$redsys_adr          = $data['redsys_adr'];
		$miObj               = new RedsysAPI;
		
		$miObj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $merchant_code );
		$miObj->setParameter( 'Ds_Merchant_Identifier', $merchant_identifier );
		$miObj->setParameter( "DS_MERCHANT_ORDER", $order_id );
		$miObj->setParameter( "DS_MERCHANT_TERMINAL", $terminal );
		$miObj->setParameter( "DS_MERCHANT_TRANSACTIONTYPE", '44' );
		
		$version   = 'HMAC_SHA256_V1';
		$params    = $miObj->createMerchantParameters();
		$signature = $miObj->createMerchantSignature( $secretsha256 );
		
		$response  = wp_remote_post(
		$redsys_adr,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.0',
				'user-agent'  => 'WooCommerce',
				'body'        => array(
					'Ds_SignatureVersion'   => $version,
					'Ds_MerchantParameters' => $params,
					'Ds_Signature'          => $signature,
				),
			)
		);
		
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body );
		$decodec       = $miObj->decodeMerchantParameters( $result->Ds_MerchantParameters );
		$decodec_array = json_decode( $decodec );
		
		$return = array(
			'order_id'            => $decodec_array->Ds_Order,
			'merchant_code'       => $decodec_array->Ds_MerchantCode,
			'terminal'            => ltrim( $decodec_array->Ds_Terminal, '0'),
			'ds_terminal'         => $decodec_array->Ds_Response,
			'ds_transaction_type' => $decodec_array->Ds_TransactionType,
		);

		if (
			(int)$order_id === (int)$decodec_array->Ds_Order &&
			(int)$merchant_code === (int)$decodec_array->Ds_MerchantCode &&
			(int)$terminal === (int)$decodec_array->Ds_Terminal &&
			000 === (int)$decodec_array->Ds_Response &&
			44 === (int)$decodec_array->Ds_TransactionType
			) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2021 José Conti
	 */
	function has_to_flush() {
		
		$flush_version = get_option( 'redsys_flush_version' );
		
		if ( ! $flush_version || (int)$flush_version < (int)REDSYS_FLUSH_VERSION ) {
			update_option( 'redsys_flush_version', REDSYS_FLUSH_VERSION );
			return true;
		} else {
			return false;
		}
	}
}
