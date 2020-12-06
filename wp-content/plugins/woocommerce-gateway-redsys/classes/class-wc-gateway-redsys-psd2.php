<?php
/*
* @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
* @since 13.0.0
* Copyright: (C) 2013 - 2021 José Conti

/**
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
* Gateway class
*/
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
class WC_Gateway_Redsys_PSD2 {
	
	function clean_data( $out ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		$out = str_replace ( "Á", "A", $out );
		$out = str_replace ( "À", "A", $out );
		$out = str_replace ( "Ä", "A", $out );
		$out = str_replace ( "É", "E", $out );
		$out = str_replace ( "È", "E", $out );
		$out = str_replace ( "Ë", "E", $out );
		$out = str_replace ( "Í", "I", $out );
		$out = str_replace ( "Ì", "I", $out );
		$out = str_replace ( "Ï", "I", $out );
		$out = str_replace ( "Ó", "O", $out );
		$out = str_replace ( "Ò", "O", $out );
		$out = str_replace ( "Ö", "O", $out );
		$out = str_replace ( "Ú", "U", $out );
		$out = str_replace ( "Ù", "U", $out );
		$out = str_replace ( "Ü", "U", $out );
		$out = str_replace ( "á", "a", $out );
		$out = str_replace ( "à", "a", $out );
		$out = str_replace ( "ä", "a", $out );
		$out = str_replace ( "é", "e", $out );
		$out = str_replace ( "è", "e", $out );
		$out = str_replace ( "ë", "e", $out );
		$out = str_replace ( "í", "i", $out );
		$out = str_replace ( "ì", "i", $out );
		$out = str_replace ( "ï", "i", $out );
		$out = str_replace ( "ó", "o", $out );
		$out = str_replace ( "ò", "o", $out );
		$out = str_replace ( "ö", "o", $out );
		$out = str_replace ( "ú", "u", $out );
		$out = str_replace ( "ù", "u", $out );
		$out = str_replace ( "ü", "u", $out );
		$out = str_replace ( "Ñ", "N", $out );
		$out = str_replace ( "ñ", "n", $out );
		$out = str_replace ( "&", "-", $out );
		$out = str_replace ( "<", " ", $out );
		$out = str_replace ( ">", " ", $out );
		$out = str_replace ( "/", " ", $out );
		$out = str_replace ( "\"", " ", $out );
		$out = str_replace ( "'", " ", $out );
		$out = str_replace ( "\"", " ", $out );
		$out = str_replace ( "?", " ", $out );
		$out = str_replace ( "¿", " ", $out );
		$out = str_replace ( "º", " ", $out );
		$out = str_replace ( "ª", " ", $out );
		$out = str_replace ( "#", " ", $out );
		$out = str_replace ( "&", " ", $out );
		$out = str_replace ( "@", " ", $out );
		
		return $out;
	}
	
	function get_redsys_option( $option, $gateway ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
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

	function get_cardholdername( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		//cardholderName
	}

	function get_email( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return $order->get_billing_email();
	}

	function get_homephone( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return $order->get_billing_phone();
	}
	
	function get_mobile_phone( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		//mobilePhone
	}
	
	function get_work( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return $order->get_billing_phone();
	}

	function get_adress_ship( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$adress_ship                     = array();
		$adress_ship['shipAddrLine1']    = $order->get_billing_address_1();
		$adress_ship['shipAddrLine2']    = $order->get_billing_address_2();
		$adress_ship['shipAddrCity']     = $order->get_billing_city();
		$adress_ship['shipAddrState']    = strtolower( $order->get_billing_state() );
		$adress_ship['shipAddrPostCode'] = $order->get_billing_postcode();
		$adress_ship['shipAddrCountry']  = strtolower( $order->get_billing_country() );
		return $adress_ship;
	}

	function addr_match( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		$adress_ship_shipAddrLine1    = $order->get_billing_address_1();
		$adress_ship_shipAddrLine2    = $order->get_billing_address_2();
		$adress_ship_shipAddrCity     = $order->get_billing_city();
		$adress_ship_shipAddrState    = strtolower( $order->get_billing_state() );
		$adress_ship_shipAddrPostCode = $order->get_billing_postcode();
		$adress_ship_shipAddrCountry  = strtolower( $order->get_billing_country() );

		if ( $order->has_shipping_address() ) {
			$adress_bill_billAddrLine1    = $order->get_shipping_address_1();
			$adress_bill_billAddrLine2    = $order->get_shipping_address_2();
			$adress_bill_billAddrCity     = $order->get_shipping_city();
			$adress_bill_billAddrPostCode = $order->get_shipping_postcode();
			$adress_bill_billAddrState    = strtolower( $order->get_shipping_state() );
			$adress_bill_billAddrCountr   = strtolower( $order->get_shipping_country() );
		} else {
			return 'Y';
		}

		if (
			$adress_ship_shipAddrLine1    === $adress_bill_billAddrLine1 &&
			$adress_ship_shipAddrLine2    === $adress_bill_billAddrLine2 &&
			$adress_ship_shipAddrCity     === $adress_bill_billAddrCity &&
			$adress_ship_shipAddrState    === $adress_bill_billAddrState &&
			$adress_ship_shipAddrPostCode === $adress_bill_billAddrPostCode &&
			$adress_ship_shipAddrCountry  === $adress_bill_billAddrCountr
		) {
			return 'Y';
		} else {
			return 'N';
		}
	}

	function get_challenge_wwndow_size( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		/**
			01 = 250x 400
			02 = 390x 400
			03 = 500x 600
			04 = 600x 400
			05 = Pantalla completa (valor por defecto).
		**/
		
		$redsys = $this->get_redsys_option( 'windowssize', 'redsys' );
		
		if ( ! empty( $redsys ) ) {
			$windows_size = $redsys;
		} else {
			$windows_size = '05';
		}
		return $windows_size;
	}

	function get_acctid( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		//acctID
	}

	function days( $start_time ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/

		$current_time    = time();
		$unix_start_time = date( 'U', strtotime( $start_time ) );
		$diff            = (int) abs( $current_time - $unix_start_time );
		
		//Now, we change seconds for days
		if ( $diff >= DAY_IN_SECONDS ) {
			$days = round( $diff / DAY_IN_SECONDS );
		}
		return $days;
	}

	function get_post_num( $post_status = array(),  $date_query = array() ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		
		$num = get_posts( array(
			'numberposts' => -1,
			'meta_key'    => '_customer_user',
			'meta_value'  => get_current_user_id(),
			'post_type'   => wc_get_order_types(),
			'post_status' => $post_status,
			'date_query'  => array(
				$date_query
			)
		));
		return count( $num );
	}

	function get_accept_headers( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return get_post_meta( $order_id, '_accept_haders', true );
	}

	function get_agente_navegador( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_post_meta( $order_id, '_billing_agente_navegador_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	function get_idioma_navegador( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_post_meta( $order_id, '_billing_idioma_navegador_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	function get_altura_pantalla( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_post_meta( $order_id, '_billing_altura_pantalla_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	function get_anchura_pantalla( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_post_meta( $order_id, '_billing_anchura_pantalla_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	function get_profundidad_color( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_post_meta( $order_id, '_billing_profundidad_color_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '1';
		}
	}

	function get_diferencia_horaria( $order_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_post_meta( $order_id, '_billing_diferencia_horaria_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}
	
	function get_browserjavaenabled( $order_id ) {
		$data = $this->get_idioma_navegador( $order_id );
		if ( '' !== $data ) {
			return '1';
		} else {
			return 'false';
		}
	}

	function get_accept_headers_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		return get_user_meta( $user_id, '_accept_haders', true );
	}
	
	function get_agente_navegador_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_agente_navegador_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	function get_idioma_navegador_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_idioma_navegador_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '';
		}
	}

	function get_altura_pantalla_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_altura_pantalla_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	function get_anchura_pantalla_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_anchura_pantalla_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}

	function get_profundidad_color_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_profundidad_color_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '1';
		}
	}

	function get_diferencia_horaria_user( $user_id ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		$data = get_user_meta( $user_id, '_billing_diferencia_horaria_field', true );
		
		if ( $data ) {
			return $data;
		} else {
			return '0';
		}
	}
	
	function get_browserjavaenabled_user( $user_id ) {
		$data = $this->get_idioma_navegador_user( $user_id );
		if ( '' !== $data ) {
			return '1';
		} else {
			return 'false';
		}
	}

	function shipnameindicator( $order ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		
		if ( $order->has_shipping_address() ) {
			$billing_first_name  = $order->get_billing_first_name();
			$billing_last_name   = $order->get_billing_last_name();			
			$shipping_first_name = $order->get_shipping_first_name();
			$shipping_last_name  = $order->get_shipping_last_name();
			
			if (
				$billing_first_name === $shipping_first_name &&
				$billing_last_name === $shipping_last_name
			) {
				$shipnameindicator = '01';
			} else {
				$shipnameindicator = '02';
			}
		} else {
			$shipnameindicator = '01';
		}
		return $shipnameindicator;
	}

	function get_acctinfo( $order, $user_data_3ds = false, $user_id = false ) {
		/**
		* Copyright: (C) 2013 - 2021 José Conti
		*/
		
		if ( $this->get_redsys_option( 'psd2', 'redsys' ) === 'yes' ) {
		
			/*
			1569057946
				01 = Sin cuenta (invitado)
				02 = Recién creada
				03 = Menos de 30 días
				04 = Entre 30 y 60días
				05 = Más de 60 días
			*/
			if ( is_user_logged_in() ||  $user_id ) {
				
				if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
				} else {
					$user_id = $user_id;
				}
				$usr_data        = get_userdata( $user_id );
				$usr_registered  = $usr_data->user_registered;
				$dt              = new DateTime( $usr_registered );
				$usr_registered  = $dt->format('Ymd');
				$last_update     = get_user_meta( $user_id, 'last_update', true );
				$minu_registered = intval( ( strtotime( 'now' ) - strtotime( $usr_registered ) ) / 60 );
				$days_registered = intval( $minu_registered / 1440 );
				$accountModified = intval( ( ( strtotime( 'now' ) - $last_update ) ) / DAY_IN_SECONDS );
				
				if ( $minu_registered < 20 ) {
					$chAccAgeInd = '02';
				} elseif ( $days_registered < 30 ) {
					$chAccAgeInd = '03';
				} elseif ( $days_registered >= 30 && $days_registered <= 60 ) {
					$chAccAgeInd = '04';
				} else {
					$chAccAgeInd = '05';
				}
				
				$customer        = new WC_Customer( $user_id );
				$dt              = new DateTime( $customer->data['date_modified'] );
				$chAccChange     = $dt->format( 'Ymd' );
				$accountModified = intval( ( strtotime( 'now' ) - strtotime( $customer->data['date_modified'] ) )/60 );
				$nDays           = intval( $accountModified/1440 );
				
				if ( $accountModified < 20)  {
					$chAccChangeInd = '01';
				} elseif ( $nDays < 30 ) {
					$chAccChangeInd = '02';
				} elseif ( $nDays >= 30 && $nDays <= 60 ) {
					$chAccChangeInd = '03';
				} else {
					$chAccChangeInd = '04';
				}
	
				$nbPurchaseAccount = $this->get_post_num( array( 'wc-completed' ), array( 'after' => '6 month ago' ) );
				$txnActivityDay    = $this->get_post_num( array( 'wc-completed', 'wc-pending' ), array( 'after' => '1 day ago' ) );
				$txnActivityYear   = $this->get_post_num( array( 'wc-completed', 'wc-pending' ), array( 'after' => '1 year ago' ) );
				
				if ( $order->has_shipping_address() ) {
					$query = get_posts( array(
						'post_type'    => wc_get_order_types(),
						'post_status'  => array_keys( wc_get_order_statuses() ),
						'meta_query'   => array(
							array(
								'key'   => '_shipping_address_1',
								'value' => $order->get_shipping_address_1()
							),
							array(
								'key'   => '_shipping_address_2',
								'value' => $order->get_shipping_address_2()
							),
							array(
								'key'   => '_shipping_city',
								'value' => $order->get_shipping_city()
							),
							array(
								'key'   => '_shipping_postcode',
								'value' => $order->get_shipping_postcode()
							),
							array(
								'key'   => '_shipping_country',
								'value' => $order->get_shipping_country()
							)
						),
						'order' => 'ASC',
					));
					if ( function_exists( 'DateTime' ) ) {
						if ( count( $query ) > 0 ) {
							$date             = new DateTime( $query[0]->post_date );
							$shipAddressUsage = $date->format( 'Ymd' );
							$days             = intval( ( ( strtotime( 'now' ) - strtotime( $query[0]->post_date ) ) / MINUTE_IN_SECONDS ) / HOUR_IN_SECONDS );
							if ( $days < 30 ) {
								$shipAddressUsageInd = '02';
							}
							elseif ( $Days >= 30 && $Days <= 60 ) {
								$shipAddressUsageInd = '03';
							}
							else{
								$shipAddressUsageInd = '04';
							}
						} else {
							$todaynow            = '';
							$date                = '';
							$shipAddressUsage    = date( 'Ymd' );
							$shipAddressUsageInd = '01';
						}
					} else {
						$todaynow            = '';
						$date                = '';
						$shipAddressUsage    = date( 'Ymd' );
						$shipAddressUsageInd = '01';
					}
				}
			} else {
				$chAccAgeInd = '01';
			}
			
			$acctInfo = array(
				'chAccAgeInd' => $chAccAgeInd,
			);
			if ( $order->has_shipping_address() ) {
				$acctInfo['shipAddressUsage']    = $shipAddressUsage;
				$acctInfo['shipAddressUsageInd'] = $shipAddressUsageInd;
			}
			if ( is_user_logged_in() ) {
				$acctInfo['chAccDate']         = $usr_registered;
				$acctInfo['chAccChange']       = $chAccChange;
				$acctInfo['chAccChangeInd']    = $chAccChangeInd;
				$acctInfo['nbPurchaseAccount'] = (string)$nbPurchaseAccount;
				$acctInfo['txnActivityDay']    = (string)$txnActivityDay;
				$acctInfo['txnActivityYear']   = (string)$txnActivityYear;
			}

			$Ds_Merchant_EMV3DS = array();
			if ( $user_data_3ds ) {
				foreach ( $user_data_3ds as $data => $valor ) {
					$Ds_Merchant_EMV3DS[$data] = $valor;
				}
			}
			$Ds_Merchant_EMV3DS['addrMatch']        = $this->addr_match( $order );
			$Ds_Merchant_EMV3DS['billAddrCity']     = $this->clean_data( $order->get_billing_city() );
			$Ds_Merchant_EMV3DS['billAddrLine1']    = $this->clean_data( $order->get_billing_address_1() );
			$Ds_Merchant_EMV3DS['billAddrPostCode'] = $this->clean_data( $order->get_billing_postcode() );
			$Ds_Merchant_EMV3DS['billAddrState']    = strtolower( $this->clean_data( $order->get_billing_state() ) );
			$Ds_Merchant_EMV3DS['billAddrCountry']  = strtolower( $this->clean_data( $order->get_billing_country() ) );
			$Ds_Merchant_EMV3DS['Email']            = $this->get_email( $order );
			$Ds_Merchant_EMV3DS['acctInfo']         = $acctInfo;
			$Ds_Merchant_EMV3DS['homePhone']        = array( 'subscriber' => $this->get_homephone( $order ) );
			
			/*
				TO-DO: suspiciousAccActivity, en una futura versión añadiré un meta a los usuarios para que el admistrador pueda marcar alguna cuenta fraudulenta o que ha habido algún problema.
			*/
			
			if ( $order->get_shipping_address_2() !== '' ) {
				$Ds_Merchant_EMV3DS['billAddrLine2'] = $this->clean_data( $order->get_shipping_address_2() );
			}
			if ( $order->has_shipping_address() ) {
				$Ds_Merchant_EMV3DS['shipAddrCity']     = $this->clean_data( $order->get_shipping_city() );
				$Ds_Merchant_EMV3DS['shipAddrLine1']    = $this->clean_data( $order->get_shipping_address_1() );
				$Ds_Merchant_EMV3DS['shipAddrPostCode'] = $this->clean_data( $order->get_shipping_postcode() );
				$Ds_Merchant_EMV3DS['shipAddrState']    = strtolower( $this->clean_data( $order->get_shipping_state() ) );
				$Ds_Merchant_EMV3DS['shipAddrCountry']  = strtolower( $this->clean_data( $order->get_shipping_country() ) );
				if ( $order->get_shipping_address_2() !== '' ) {
					$Ds_Merchant_EMV3DS['shipAddrLine2'] = $this->clean_data( $order->get_shipping_address_2() );
				}
			}
			$Ds_Merchant_EMV3DS  = wp_json_encode( $Ds_Merchant_EMV3DS );
			return $Ds_Merchant_EMV3DS;
		} else {
			return false;
		}
	}
/**
	cardholderName
	email
	homePhone
	mobilePhone
	workPhone
	shipAddrLine1
	shipAddrLine2
	shipAddrLine3
	shipAddrCity
	shipAddrPostCode
	shipAddrState
	shipAddrCountry
	billAddrLine1
	billAddrLine2
	billAddrLine3
	billAddrCity
	billAddrPostCode
	billAddrState
	billAddrCountry
	addrMatch
	challengeWindowSize
	acctID
	threeDSRequestorAuthenticationInfo
	acctInfo {
		chAccAgeInd
		chAccDate
		chAccChange
		chAccChangeInd
		chAccPwChange
		chAccPwChangeInd
		nbPurchaseAccount
		provisionAttemptsDay
		txnActivityDay
		txnActivityYear
		paymentAccAge
		paymentAccInd
		shipAddressUsage
		shipAddressUsageInd
		shipNameIndicator
		suspiciousAccActivity
	}
	merchantRiskIndicator {
		deliveryEmailAddress
		deliveryTimeframe
		giftCardAmount
		giftCardCount
		giftCardCurr
		preOrderDate
		preOrderPurchaseInd
		reorderItemsInd
		shipIndicator
	}
**/
}
