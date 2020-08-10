<?php
/**
* Copyright: (C) 2013 - 2020 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
* Gateway class
*/
/**
* Copyright: (C) 2013 - 2020 José Conti
*/
class WC_Gateway_Redsys_Global {
	
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_ds_error() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'dserrors.php';

		$dserrors = array();
		$dserrors = redsys_return_dserrors();
		return $dserrors;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_ds_response() {
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'dsresponse.php';
		
		$dsresponse = array();
		$dsresponse = redsys_return_dsresponse();
		return $dsresponse;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_msg_error() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'insiteerrors.php';

		$msgerrors = array();
		$msgerrors = redsys_return_insiteerrors();
		return $msgerrors;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function is_ds_error( $error_code = null ) {
		
		$ds_errors = array();
		$ds_errors = $this->get_ds_error();
		
		if ( $error_code ) {
			foreach ( $ds_errors as $ds_error => $value ) {
				if ( ( string ) $ds_error === ( string ) $error_code ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function is_ds_response( $error_code = null ) {
		
		$ds_response  = array();
		$ds_responses = $this->get_ds_response();
		
		if ( $error_code ) {
			foreach ( $ds_responses as $ds_response => $value ) {
				if ( ( string ) $ds_response === ( string ) $error_code ) {
					return true;
				} 
				continue;
			}
			return false;
		}
		return false;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function is_msg_error( $error_code = null ) {
		
		$msg_errors = array();
		$msg_errors = $this->get_msg_error();
		
		if ( $error_code ) {
			foreach ( $msg_errors as $msg_error => $value ) {
				if ( ( string ) $msg_error === ( string ) $error_code ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_msg_error_by_code( $error_code = null ) {

		$smg_errors = array();
		$smg_errors = $this->get_msg_error();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $smg_errors ) ) {
					foreach ( $smg_errors as $msg_error => $value ) {
						if ( ( string ) $msg_error === ( string ) $error_code ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_error_by_code( $error_code = null ) {

		$ds_errors = array();
		$ds_errors = $this->get_ds_error();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $ds_errors ) ) {
					foreach ( $ds_errors as $ds_error => $value ) {
						if ( ( string ) $ds_error === ( string ) $error_code ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_response_by_code( $error_code = null ) {

		$ds_responses = array();
		$ds_responses = $this->get_ds_response();

		if ( $error_code ) {
			if ( ! empty( $error_code ) ) {
				if ( ! empty( $ds_responses ) ) {
					foreach ( $ds_responses as $ds_response => $value ) {
						if ( ( string ) $ds_response === ( string ) $error_code ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
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
	* Copyright: (C) 2013 - 2020 José Conti
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
	* Copyright: (C) 2013 - 2020 José Conti
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_currencies() {

		include_once REDSYS_PLUGIN_DATA_PATH . 'currencies.php';

		$currencies = array();
		$currencies = redsys_return_currencies();
		return $currencies;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function allowed_currencies() {
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'allowed-currencies.php';
		
		$currencies = array();
		$currencies = redsys_return_allowed_currencies();
		return $currencies;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_redsys_languages() {
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'languages.php';
		
		$languages = array();
		$languages = redsys_return_languages();
		return $languages;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_redsys_wp_languages() {
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'wplanguages.php';
		
		$languages = array();
		$languages = redsys_return_all_languages_code();
		return $languages;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_orders_type() {
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'redsys-types.php';
		
		$types = array();
		$types = redsys_return_types();
		return $types;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_lang_code( $lang = 'en' ) {
		 
		 $languages = array();
		 $languages = $this->get_redsys_wp_languages();
		 
		if ( ! empty( $languages ) ) {
			foreach ( $languages as $language => $value ) {
				if ( ( string ) $language === ( string ) $lang ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
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
	* Copyright: (C) 2013 - 2020 José Conti
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
	* Copyright: (C) 2013 - 2020 José Conti
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
						if ( ( string ) $redsys_type === ( string ) $gateway ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_order_date( $order_id ) {
		$date_decoded = str_replace( '%2F', '/', get_post_meta( $order_id, '_payment_date_redsys', true ) );
		if ( ! $date_decoded ) {
			return false;
		}
		return $date_decoded;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_order_hour( $order_id ) {
		$hour_decoded = str_replace( '%3A', ':', get_post_meta( $order_id, '_payment_hour_redsys', true ) );
		if ( ! $hour_decoded ) {
			return false;
		}
		return $hour_decoded;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_order_auth( $order_id ) {
		$auth = get_post_meta( $order_id, '_authorisation_code_redsys', true );
		if ( ! $auth ) {
			return false;
		}
		return $auth;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_status_pending() {
		
		include_once REDSYS_PLUGIN_DATA_PATH . 'redsys-status-paid.php';
		
		$status = array();
		$status = redsys_return_status_paid();
		return apply_filters( 'redsys_status_pending', $status );
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function is_paid( $order_id ) {
		if ( $this->order_exist( $order_id ) ) {
			$status_paid = array();
			$order       = $this->get_order( $order_id );
			$status      = $order->get_status();
			$status_paid = $this->get_status_pending();
			if ( $status_paid ) {
				foreach ( $status_paid as $spaid ) {
					if ( ( string ) $status === ( string ) $spaid ) {
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_redsys_option( $option, $gateway ) {
		
		$options = get_option( 'woocommerce_' . $gateway . '_settings' );
		
		if ( ! empty( $options ) ) {
			$redsys_options = maybe_unserialize( $options );
			if ( array_key_exists( $option, $redsys_options ) ) {
				$option_value = $redsys_options[$option];
				return $option_value;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function check_if_token_is_valid( $token_id ) {

		$token     = WC_Payment_Tokens::get( $token_id );
		$year      = $token->get_expiry_year();
		$month     = $token->get_expiry_month();
		$act_year  = date('Y');
		$act_month = date('m');
		if ( $year >= $act_year ) {
			if ( $year > $act_year ) {
				return true;
			}
			if ( $month >= $act_month ) {
				return true;
			} else {
				WC_Payment_Tokens::delete( $token_id );
				return false;
			}
		} else {
			WC_Payment_Tokens::delete( $token_id );
			return false;
		}
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_redsys_users_token() {
		$customer_token = null;
		if ( is_user_logged_in() ) {
			$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'redsys' );
			foreach ( $tokens as $token ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					if ( $valid_token ) {
						$customer_token = $token->get_token();
					}
					break;
				}
			}
		}
		return $customer_token;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_users_token_bulk( $user_id ) {
		$customer_token = null;
		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		foreach ( $tokens as $token ) {
			if ( $token->get_gateway_id() === 'redsys' ) {
				$valid_token = $this->check_if_token_is_valid( $token->get_id() );
				if ( $valid_token ) {
					$customer_token = $token->get_token();
				}
				break;
			}
		}
		return $customer_token;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function clean_order_number( $ordernumber ) {
		return substr( $ordernumber, 3 );
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function prepare_order_number( $order_id ) {
		$transaction_id   = str_pad( $order_id , 12 , '0' , STR_PAD_LEFT );
		$transaction_id1  = mt_rand( 1, 999 ); // lets to create a random number
		$transaction_id2  = substr_replace( $transaction_id, $transaction_id1, 0,-9 ); // new order number
		return $transaction_id2;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function redsys_amount_format( $total ) {
		$order_total_sign = number_format( $total, 2, '', '' );
		return $order_total_sign;
	}
	/**
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function product_description( $order, $gateway ) {
		if ( ! $this->is_redsys_order( $order->get_id() ) ) {
			return;
		}
		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id .= $item->get_product_id() . ', ';
			$name       .= $item->get_name() . ', ';
			$sku        .= get_post_meta( $item->get_product_id(), '_sku', true) . ', ';
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
	* Copyright: (C) 2013 - 2020 José Conti
	*/
	function get_psd2_arg( $order, $gateway ) {
		if ( 'yes' === $this->get_redsys_option( 'psd2', $gateway ) ) {
			return $arg;
		} else {
			return '';
		}
	}
}
