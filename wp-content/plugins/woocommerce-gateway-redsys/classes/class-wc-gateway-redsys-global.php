<?php
/**
 * Class WC_Gateway_Redsys_Global
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Gateway class
 */
class WC_Gateway_Redsys_Global {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_available_payment_gateways', array( $this, 'disable_gateways_preauth' ), 100 );
		add_action( 'woocommerce_available_payment_gateways', array( $this, 'disable_gateways_token_r' ), 100 );
	}
	/**
	 * Debug function
	 *
	 * @param string $log is the log message.
	 */
	public function debug( $log ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug = new WC_Logger();
			$debug->add( 'redsys-global', $log );
		}
	}

	/**
	 * Get Redsys Ownsetting
	 *
	 * @param string $gateway is the WooCommerce gateway name.
	 */
	public function get_redsys_ownsetting( $gateway ) {

		if ( is_multisite() ) {

			$options = get_option( 'woocommerce_' . $gateway . '_settings' );

			if ( ! empty( $options ) ) {
				$redsys_options = maybe_unserialize( $options );
				if ( array_key_exists( 'ownsetting', $redsys_options ) ) {
					$option_value = $redsys_options['ownsetting'];
					return $option_value;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/**
	 * Send customer email
	 *
	 * @param int    $order_id Order ID.
	 * @param string $subject Email subject.
	 * @param string $message Email message.
	 * @param string $heading Email heading.
	 */
	public function send_customer_email( $order_id, $subject, $message, $heading ) {
		$order      = wc_get_order( $order_id );
		$email_name = get_option( 'woocommerce_email_from_name' );
		$email_from = get_option( 'woocommerce_email_from_address' );
		$headers[]  = 'Content-Type: text/html; charset=UTF-8';
		$headers[]  = 'From: ' . $email_name . ' <' . $email_from . '>';
		if ( $order ) {
			$mailer          = WC()->mailer();
			$wrapped_message = $mailer->wrap_message( $heading, $message );
			$mailer->send( $order->get_billing_email(), $subject, $wrapped_message );
		}
	}
	/**
	 * Send admin email
	 *
	 * @param int    $order_id Order ID.
	 * @param string $subject Email subject.
	 * @param string $message Email message.
	 * @param string $heading Email heading.
	 */
	public function send_admin_email( $order_id, $subject, $message, $heading ) {
		$order      = wc_get_order( $order_id );
		$email_name = get_option( 'woocommerce_email_from_name' );
		$email_from = get_option( 'woocommerce_email_from_address' );
		$headers[]  = 'Content-Type: text/html; charset=UTF-8';
		$headers[]  = 'From: ' . $email_name . ' <' . $email_from . '>';
		if ( $order ) {
			$mailer          = WC()->mailer();
			$wrapped_message = $mailer->wrap_message( $heading, $message );
			$mailer->send( $email_from, $subject, $wrapped_message );
		}
	}
	/**
	 * Get option from WordPress
	 *
	 * @param string $option Option.
	 */
	public function get_wp_option( $option ) {
		return get_option( $option, false );
	}
	/**
	 * Get option from main site
	 *
	 * @param string $gateway Gateway.
	 */
	public function get_option_from_main_site( $gateway ) {
		$this->debug( 'get_option_from_main_site( $gateway )' );
		$this->debug( 'get_site_option( "redsys_cached" ): ' . get_site_option( 'redsys_cached' ) );
		if ( 'yes' !== get_site_option( 'redsys_cached' ) ) {
			delete_site_option( 'redsys_option_' . $gateway );
			switch_to_blog( 1 );
			$options = get_option( 'woocommerce_' . $gateway . '_settings' );
			restore_current_blog();
			return $options;
		}

		$cached_option = get_site_option( 'redsys_option_' . $gateway );

		if ( false !== $cached_option ) {
			return $cached_option;
		}

		switch_to_blog( 1 );
		$options = get_option( 'woocommerce_' . $gateway . '_settings' );
		restore_current_blog();

		// Considera cambiar esto por un set_transient si el objetivo es cachear.
		update_site_option( 'redsys_option_' . $gateway, $options );

		return $options;
	}
	/**
	 * Get option from main site
	 *
	 * @param string $option Option.
	 * @param string $gateway Gateway.
	 */
	public function get_redsys_option( $option, $gateway ) {

		$options = get_option( 'woocommerce_' . $gateway . '_settings' );
		if ( ! empty( $options ) ) {
			if ( 'all' === $option ) {
				return maybe_unserialize( $options );
			}
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

		if ( is_multisite() && ! is_main_site() ) {
			$this->debug( 'is_multisite() && ! is_main_site()' );
			$this->debug( '$gateway: ' . $gateway );
			$this->debug( '$option: ' . $option );
			$options        = $this->get_option_from_main_site( $gateway );
			$redsys_options = maybe_unserialize( $options );
			if ( 'ownsetting' !== $option ) {
				if ( 'hideownsetting' === $option || 'multisitesttings' === $option ) {
					if ( ! empty( $redsys_options ) ) {
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
			}

			$multisitesttings = $redsys_options['multisitesttings'];
			$ownsetting       = $redsys_options['hideownsetting'];

			if ( 'yes' !== $ownsetting && 'yes' === $multisitesttings ) {
				$this->debug( 'yes !== $ownsetting && yes === $multisitesttings' );
				if ( ! empty( $redsys_options ) ) {
					if ( 'all' === $option ) {
						return $redsys_options;
					}
					if ( array_key_exists( $option, $redsys_options ) ) {
						$option_value = $redsys_options[ $option ];
						return $option_value;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				$this->debug( 'NOT: yes !== $ownsetting && yes === $multisitesttings' );
				$options = get_option( 'woocommerce_' . $gateway . '_settings' );
				if ( ! empty( $options ) ) {
					$this->debug( '$options: ' . print_r( $options, true ) );
					$this->debug( '$gateway: ' . $gateway );
					$this->debug( '$option: ' . $option );
					if ( 'all' === $option ) {
						$this->debug( '$gateway: ' . $gateway );
						$this->debug( '$option: ' . $option );
						return $options;
					}
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
		}

		$options = get_option( 'woocommerce_' . $gateway . '_settings' );

		if ( 'all' === $option ) {
			return $options;
		}

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
	 * Return help notice
	 */
	public function return_help_notice() {
		$guias  = 'https://redsys.joseconti.com/guias/';
		$faq    = 'https://redsys.joseconti.com/redsys-for-woocommerce/';
		$ticket = 'https://woocommerce.com/my-account/tickets/';
		printf(
			wp_kses(
				'<div class="redsysnotice">
				<span class="dashicons dashicons-welcome-learn-more redsysnotice-dash"></span>
				<span class="redsysnotice__content">' .
				// translators: Links to Jose Conti Redsys website Guides, Faq and Suport tickets.
				__( 'For Redsys Help: Check the website <a href="%1$s" target="_blank" rel="noopener">Guides</a> for setup <a href="%2$s" target="_blank" rel="noopener">FAQ page</a> for working problems, or open a <a href="%3$s" target="_blank" rel="noopener"> Ticket</a> for support', 'woocommerce-redsys' ) . '<span></div>',
				array(
					'a'    => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
					'div'  => array(
						'class' => array(),
					),
					'span' => array(
						'class' => array(),
					),
				)
			),
			esc_url( $guias ),
			esc_url( $faq ),
			esc_url( $ticket )
		);
	}
	/**
	 * Get Redsys Order Number
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_redsys_order_number( $order_id ) {
		return $this->get_order_meta( $order_id, '_payment_order_number_redsys', true );
	}
	/**
	 * Get Redsys token R
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_redsys_token_r( $order_id ) {

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
	 * Check if order needs preauth
	 *
	 * @param int $order_id Order ID.
	 */
	public function order_needs_preauth( $order_id ) {

		if ( ! is_a( $order_id, 'WC_Abstract_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( $order ) {

			$global_preauth = $this->get_redsys_option( 'redsyspreauthall', 'redsys' );

			if ( 'yes' === $global_preauth ) {
				return true;
			}

			foreach ( $order->get_items() as $item_id => $item_values ) {
				$product_id = $item_values->get_product_id();
				$get        = get_post_meta( $product_id, '_redsyspreauth', true );
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
	 * Check if order needs subscription
	 *
	 * @param int $product_id Order ID.
	 */
	public function check_redsys_subscription_checkout( $product_id ) {

		$get = get_post_meta( $product_id, '_redsystokenr', true );

		if ( 'yes' === $get ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check if order needs subscription
	 *
	 * @param int $product_id Order ID.
	 */
	public function check_yith_subscription_checkout( $product_id ) {

		if ( defined( 'YITH_YWSBS_PREMIUM' ) || defined( 'YITH_YWSBS_VERSION' ) ) {
			$get = get_post_meta( $product_id, '_ywsbs_subscription', true );

			if ( 'yes' === $get ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Check if order needs subscription
	 *
	 * @param int $product_id Order ID.
	 */
	public function check_sumo_subscription_checkout( $product_id ) {

		if ( defined( 'SUMO_SUBSCRIPTIONS_PLUGIN_FILE' ) ) {
			$get = (string) get_post_meta( $product_id, 'sumo_susbcription_status', true );

			if ( '1' === $get ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Check if order needs subscription
	 *
	 * @param int $product_id Order ID.
	 */
	public function check_woo_subscription_checkout( $product_id ) {

		if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product_id ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check if order needs subscription
	 *
	 * @param int $product_id Order ID.
	 */
	public function check_all_woo_subscription_checkout( $product_id ) {

		$get = get_post_meta( $product_id, '_ywsbs_subscription', true );

		if ( 'yes' === $get ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Clean data
	 *
	 * @param string $out Data to clean.
	 *
	 * @return string
	 */
	public function clean_data( $out ) {
		$replacements = array(
			'ª' => 'a',
			'º' => 'o',
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'Å' => 'A',
			'Æ' => 'AE',
			'Ç' => 'C',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ð' => 'D',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ý' => 'Y',
			'Þ' => 'TH',
			'ß' => 's',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'æ' => 'ae',
			'ç' => 'c',
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'd',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'o',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ü' => 'u',
			'ý' => 'y',
			'þ' => 'th',
			'ÿ' => 'y',
			'Ø' => 'O',
			// Decompositions for Latin Extended-A.
			'Ā' => 'A',
			'ā' => 'a',
			'Ă' => 'A',
			'ă' => 'a',
			'Ą' => 'A',
			'ą' => 'a',
			'Ć' => 'C',
			'ć' => 'c',
			'Ĉ' => 'C',
			'ĉ' => 'c',
			'Ċ' => 'C',
			'ċ' => 'c',
			'Č' => 'C',
			'č' => 'c',
			'Ď' => 'D',
			'ď' => 'd',
			'Đ' => 'D',
			'đ' => 'd',
			'Ē' => 'E',
			'ē' => 'e',
			'Ĕ' => 'E',
			'ĕ' => 'e',
			'Ė' => 'E',
			'ė' => 'e',
			'Ę' => 'E',
			'ę' => 'e',
			'Ě' => 'E',
			'ě' => 'e',
			'Ĝ' => 'G',
			'ĝ' => 'g',
			'Ğ' => 'G',
			'ğ' => 'g',
			'Ġ' => 'G',
			'ġ' => 'g',
			'Ģ' => 'G',
			'ģ' => 'g',
			'Ĥ' => 'H',
			'ĥ' => 'h',
			'Ħ' => 'H',
			'ħ' => 'h',
			'Ĩ' => 'I',
			'ĩ' => 'i',
			'Ī' => 'I',
			'ī' => 'i',
			'Ĭ' => 'I',
			'ĭ' => 'i',
			'Į' => 'I',
			'į' => 'i',
			'İ' => 'I',
			'ı' => 'i',
			'Ĳ' => 'IJ',
			'ĳ' => 'ij',
			'Ĵ' => 'J',
			'ĵ' => 'j',
			'Ķ' => 'K',
			'ķ' => 'k',
			'ĸ' => 'k',
			'Ĺ' => 'L',
			'ĺ' => 'l',
			'Ļ' => 'L',
			'ļ' => 'l',
			'Ľ' => 'L',
			'ľ' => 'l',
			'Ŀ' => 'L',
			'ŀ' => 'l',
			'Ł' => 'L',
			'ł' => 'l',
			'Ń' => 'N',
			'ń' => 'n',
			'Ņ' => 'N',
			'ņ' => 'n',
			'Ň' => 'N',
			'ň' => 'n',
			'ŉ' => 'n',
			'Ŋ' => 'N',
			'ŋ' => 'n',
			'Ō' => 'O',
			'ō' => 'o',
			'Ŏ' => 'O',
			'ŏ' => 'o',
			'Ő' => 'O',
			'ő' => 'o',
			'Œ' => 'OE',
			'œ' => 'oe',
			'Ŕ' => 'R',
			'ŕ' => 'r',
			'Ŗ' => 'R',
			'ŗ' => 'r',
			'Ř' => 'R',
			'ř' => 'r',
			'Ś' => 'S',
			'ś' => 's',
			'Ŝ' => 'S',
			'ŝ' => 's',
			'Ş' => 'S',
			'ş' => 's',
			'Š' => 'S',
			'š' => 's',
			'Ţ' => 'T',
			'ţ' => 't',
			'Ť' => 'T',
			'ť' => 't',
			'Ŧ' => 'T',
			'ŧ' => 't',
			'Ũ' => 'U',
			'ũ' => 'u',
			'Ū' => 'U',
			'ū' => 'u',
			'Ŭ' => 'U',
			'ŭ' => 'u',
			'Ů' => 'U',
			'ů' => 'u',
			'Ű' => 'U',
			'ű' => 'u',
			'Ų' => 'U',
			'ų' => 'u',
			'Ŵ' => 'W',
			'ŵ' => 'w',
			'Ŷ' => 'Y',
			'ŷ' => 'y',
			'Ÿ' => 'Y',
			'Ź' => 'Z',
			'ź' => 'z',
			'Ż' => 'Z',
			'ż' => 'z',
			'Ž' => 'Z',
			'ž' => 'z',
			'ſ' => 's',
			// Decompositions for Latin Extended-B.
			'Ș' => 'S',
			'ș' => 's',
			'Ț' => 'T',
			'ț' => 't',
			// Euro sign.
			'€' => 'E',
			// GBP (Pound) sign.
			'£' => '',
			// Vowels with diacritic (Vietnamese).
			// Unmarked.
			'Ơ' => 'O',
			'ơ' => 'o',
			'Ư' => 'U',
			'ư' => 'u',
			// Grave accent.
			'Ầ' => 'A',
			'ầ' => 'a',
			'Ằ' => 'A',
			'ằ' => 'a',
			'Ề' => 'E',
			'ề' => 'e',
			'Ồ' => 'O',
			'ồ' => 'o',
			'Ờ' => 'O',
			'ờ' => 'o',
			'Ừ' => 'U',
			'ừ' => 'u',
			'Ỳ' => 'Y',
			'ỳ' => 'y',
			// Hook.
			'Ả' => 'A',
			'ả' => 'a',
			'Ẩ' => 'A',
			'ẩ' => 'a',
			'Ẳ' => 'A',
			'ẳ' => 'a',
			'Ẻ' => 'E',
			'ẻ' => 'e',
			'Ể' => 'E',
			'ể' => 'e',
			'Ỉ' => 'I',
			'ỉ' => 'i',
			'Ỏ' => 'O',
			'ỏ' => 'o',
			'Ổ' => 'O',
			'ổ' => 'o',
			'Ở' => 'O',
			'ở' => 'o',
			'Ủ' => 'U',
			'ủ' => 'u',
			'Ử' => 'U',
			'ử' => 'u',
			'Ỷ' => 'Y',
			'ỷ' => 'y',
			// Tilde.
			'Ẫ' => 'A',
			'ẫ' => 'a',
			'Ẵ' => 'A',
			'ẵ' => 'a',
			'Ẽ' => 'E',
			'ẽ' => 'e',
			'Ễ' => 'E',
			'ễ' => 'e',
			'Ỗ' => 'O',
			'ỗ' => 'o',
			'Ỡ' => 'O',
			'ỡ' => 'o',
			'Ữ' => 'U',
			'ữ' => 'u',
			'Ỹ' => 'Y',
			'ỹ' => 'y',
			// Acute accent.
			'Ấ' => 'A',
			'ấ' => 'a',
			'Ắ' => 'A',
			'ắ' => 'a',
			'Ế' => 'E',
			'ế' => 'e',
			'Ố' => 'O',
			'ố' => 'o',
			'Ớ' => 'O',
			'ớ' => 'o',
			'Ứ' => 'U',
			'ứ' => 'u',
			// Dot below.
			'Ạ' => 'A',
			'ạ' => 'a',
			'Ậ' => 'A',
			'ậ' => 'a',
			'Ặ' => 'A',
			'ặ' => 'a',
			'Ẹ' => 'E',
			'ẹ' => 'e',
			'Ệ' => 'E',
			'ệ' => 'e',
			'Ị' => 'I',
			'ị' => 'i',
			'Ọ' => 'O',
			'ọ' => 'o',
			'Ộ' => 'O',
			'ộ' => 'o',
			'Ợ' => 'O',
			'ợ' => 'o',
			'Ụ' => 'U',
			'ụ' => 'u',
			'Ự' => 'U',
			'ự' => 'u',
			'Ỵ' => 'Y',
			'ỵ' => 'y',
			// Vowels with diacritic (Chinese, Hanyu Pinyin).
			'ɑ' => 'a',
			// Macron.
			'Ǖ' => 'U',
			'ǖ' => 'u',
			// Acute accent.
			'Ǘ' => 'U',
			'ǘ' => 'u',
			// Caron.
			'Ǎ' => 'A',
			'ǎ' => 'a',
			'Ǐ' => 'I',
			'ǐ' => 'i',
			'Ǒ' => 'O',
			'ǒ' => 'o',
			'Ǔ' => 'U',
			'ǔ' => 'u',
			'Ǚ' => 'U',
			'ǚ' => 'u',
			// Grave accent.
			'Ǜ' => 'U',
			'ǜ' => 'u',
			'Á' => 'A',
			'À' => 'A',
			'Ä' => 'A',
			'É' => 'E',
			'È' => 'E',
			'Ë' => 'E',
			'Í' => 'I',
			'Ì' => 'I',
			'Ï' => 'I',
			'Ó' => 'O',
			'Ò' => 'O',
			'Ö' => 'O',
			'Ú' => 'U',
			'Ù' => 'U',
			'Ü' => 'U',
			'á' => 'a',
			'à' => 'a',
			'ä' => 'a',
			'é' => 'e',
			'è' => 'e',
			'ë' => 'e',
			'í' => 'i',
			'ì' => 'i',
			'ï' => 'i',
			'ó' => 'o',
			'ò' => 'o',
			'ö' => 'o',
			'ú' => 'u',
			'ù' => 'u',
			'ü' => 'u',
			'ç' => 'c',
			'Ç' => 'C',
			'Ñ' => 'N',
			'ñ' => 'n',
			'&' => '-',
			'<' => ' ',
			'>' => ' ',
			'/' => ' ',
			'"' => ' ',
			"'" => ' ',
			'?' => ' ',
			'¿' => ' ',
			'º' => ' ',
			'ª' => ' ',
			'#' => ' ',
			'@' => ' ',
			'[' => ' ',
			']' => ' ',
		);

		return strtr( $out, $replacements );
	}
	/**
	 * Get Order
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_order( $order_id ) {
		$order = new WC_Order( $order_id );
		return $order;
	}
	/**
	 * Set Txnid
	 *
	 * @param int $token_id Token ID.
	 * @param int $redsys_txnid Redsys Txnid.
	 */
	public function set_txnid( $token_id, $redsys_txnid ) {
		if ( $redsys_txnid ) {
			update_option( 'txnid_' . $token_id, $redsys_txnid );
		}
	}
	/**
	 * Set Token Type
	 *
	 * @param int $token_id Token ID.
	 * @param int $type Type.
	 */
	public function set_token_type( $token_id, $type ) {
		if ( $token_id && $type ) {
			update_option( 'token_type_' . $token_id, $type );
		}
	}
	/**
	 * Get Txnid
	 *
	 * @param int $token_id Token ID.
	 */
	public function get_txnid( $token_id ) {
		$this->debug( 'get_txnid( $token_id )' );
		$this->debug( '$token_id: ' . $token_id );
		if ( $token_id ) {
			$this->debug( 'Hay Token ID' );
			$redsys_txnid = trim( get_option( 'txnid_' . $token_id, false ) );
			$this->debug( '$redsys_txnid: ' . $redsys_txnid );
			if ( $redsys_txnid && ! empty( $redsys_txnid ) && '' !== $redsys_txnid ) {
				$this->debug( 'Hay $redsys_txnid' );
				return trim( $redsys_txnid );
			} else {
				$this->debug( 'No hay $redsys_txnid' );
				$redsys_txnid = trim( get_option( 'txnid_' . WCRed()->get_token_by_id( $token_id ), false ) );
				if ( $redsys_txnid ) {
					return $redsys_txnid;
				}
			}
			return '999999999999999'; // Temporal return for old tokens.
		}
		return false;
	}
	/**
	 * Get Token Type
	 *
	 * @param int $token_id Token ID.
	 */
	public function get_token_type( $token_id ) {
		if ( $token_id ) {
			$redsys_token_type = trim( get_option( 'token_type_' . $token_id ) );
			if ( 'R' === $redsys_token_type || 'C' === $redsys_token_type ) {
				return $redsys_token_type;
			} else {
				$redsys_token_type = get_option( 'token_type_' . $this->get_token_by_id( $token_id ), false );
				if ( 'R' === $redsys_token_type || 'C' === $redsys_token_type ) {
					return $redsys_token_type;
				}
			}
			return 'R'; // Temporal return for old tokens.
		}
		return false;
	}
	/**
	 * What to do with token
	 *
	 * @param string $token_id Token.
	 *
	 * @return string.
	 */
	public function maybe_use_token( $token_id ) {
		$error_e174 = get_transient( 'redsys_E174_' . $token_id );
		if ( 'E174' === $error_e174 ) {
			return false;
		}
		return true;
	}
	/**
	 * What to do with token
	 *
	 * @param WC_Order $order Order.
	 * @param string   $token_id Token.
	 * @param string   $error_code Error code.
	 *
	 * @return string.
	 */
	public function check_token_error( $order = false, $token_id = false, $error_code = false ) {
		if ( ! $order || ! $token_id || ! $error_code ) {
			return false;
		}
		$this->debug( 'check_token_error( $token_id, $error_code = false )' );
		$this->debug( '$token_id: ' . $token_id );
		$this->debug( '$error_code: ' . $error_code );
		$error_e174 = get_transient( 'redsys_E174_' . $token_id );
		if ( 'E174' === $error_e174 ) {
			return 'waiting';
		}
		/**
		 * 172: Denegada, no repetir.
		 * 173: Denegada, no repetir sin actualizar datos de la tarjeta.
		 * 174: Denegada, no repetir antes de 72 horas.
		 */
		if (
			'0172' === $error_code ||
			'172' === $error_code ||
			'0173' === $error_code ||
			'173' === $error_code
			) {
			// Hay que eliminar el token.
			$token = WC_Payment_Tokens::get( $token_id );
			if ( $token ) {
				$order_id = $order->get_id();
				$subject  = esc_html__( 'Your Credit Card has been Deleted', 'woocommerce-redsys' );
				$message  = esc_html__( 'Your Credit Card has been Deleted', 'woocommerce-redsys' );
				$heading  = esc_html__( 'Credit Card Deleted', 'woocommerce-redsys' );
				$this->send_customer_email( $order_id, $subject, $message, $heading );
				WC_Payment_Tokens::delete( $token_id );
				return 'delete_token';
			}
			return 'no_token';

		}
		if (
			'0174' === $error_code ||
			'174' === $error_code
			) {
			// Esperar 72 horas.
			set_transient( 'redsys_E174_' . $token_id, 'E174', 72 * HOUR_IN_SECONDS );
			return 'waiting';
		}
		return 'normal_error';
	}
	/**
	 * Get DS Error
	 */
	public function get_ds_error() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'dserrors.php';

		$dserrors = array();
		$dserrors = redsys_return_dserrors();
		return $dserrors;
	}
	/**
	 * Get DS Response
	 */
	public function get_ds_response() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'dsresponse.php';

		$dsresponse = array();
		$dsresponse = redsys_return_dsresponse();
		return $dsresponse;
	}
	/**
	 * Get msg error
	 */
	public function get_msg_error() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'insiteerrors.php';

		$msgerrors = array();
		$msgerrors = redsys_return_insiteerrors();
		return $msgerrors;
	}
	/**
	 * Get country codes
	 */
	public function get_country_codes_phone() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'countries-2.php';

		$countries = array();
		$countries = redsys_get_country_code_2();
		return $countries;
	}
	/**
	 * Get country codes 2
	 *
	 * @param string $country_code_2 Country Code 2.
	 */
	public function get_country_codes_2( $country_code_2 ) {

		$countries = array();
		$countries = $this->get_country_codes_phone();

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
	 * Get country codes
	 */
	public function get_country_codes() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'countries.php';

		$countries = array();
		$countries = redsys_get_country_code();
		return $countries;
	}
	/**
	 * Get country codes 3
	 *
	 * @param string $country_code_2 Country code 2.
	 */
	public function get_country_codes_3( $country_code_2 ) {

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
	 * Check if is DS Error
	 *
	 * @param string $error_code Error code.
	 */
	public function is_ds_error( $error_code = null ) {

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
	 * Check if is DS Response
	 *
	 * @param string $error_code Error code.
	 */
	public function is_ds_response( $error_code = null ) {

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
	 * Check if is msg error
	 *
	 * @param string $error_code Error code.
	 */
	public function is_msg_error( $error_code = null ) {

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
	 * Get msg Error by code
	 *
	 * @param string $error_code Error code.
	 */
	public function get_msg_error_by_code( $error_code = null ) {

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
	 * Get Error by code
	 *
	 * @param string $error_code Error code.
	 */
	public function get_error_by_code( $error_code = null ) {

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
	 * Get Response by code
	 *
	 * @param string $error_code Error code.
	 */
	public function get_response_by_code( $error_code = null ) {

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
	 * Check if is Redsys Error
	 *
	 * @param string $error_code Error code.
	 */
	public function is_redsys_error( $error_code = null ) {

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
	 * Get Redsys Error
	 *
	 * @param string $error_code Error code.
	 */
	public function get_error( $error_code = null ) {

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
	 * Get error type
	 *
	 * @param string $error_code Error code.
	 */
	public function get_error_type( $error_code = null ) {

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
	 * Get currencies
	 */
	public function get_currencies() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'currencies.php';

		$currencies = array();
		$currencies = redsys_return_currencies();
		return $currencies;
	}
	/**
	 * Get allowed currencies
	 */
	public function allowed_currencies() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'allowed-currencies.php';

		$currencies = array();
		$currencies = redsys_return_allowed_currencies();
		return $currencies;
	}
	/**
	 * Get Redsys languages
	 */
	public function get_redsys_languages() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'languages.php';

		$languages = array();
		$languages = redsys_return_languages();
		return $languages;
	}
	/**
	 * Get Redsys WP languages
	 */
	public function get_redsys_wp_languages() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'wplanguages.php';

		$languages = array();
		$languages = redsys_return_all_languages_code();
		return $languages;
	}
	/**
	 * Get Order types
	 */
	public function get_orders_type() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'redsys-types.php';

		$types = array();
		$types = redsys_return_types();
		return $types;
	}
	/**
	 * Get lang code
	 *
	 * @param string $lang Language.
	 */
	public function get_lang_code( $lang = 'en' ) {

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
			return '2';
		}
		return '2';
	}
	/**
	 * Get Order types
	 */
	public function get_orders_number_type() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'number-order-type.php';

		$types = array();
		$types = redsys_return_number_order_type();
		return $types;
	}
	/**
	 * Check if order exist
	 *
	 * @param string $order_id Order ID.
	 */
	public function order_exist( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( empty( $order ) ) {
			return false;
		}
		return true;
	}
	/**
	 * Check if post exist
	 *
	 * @param string $order_id Order ID.
	 */
	public function post_exist( $order_id ) {
		$post_status = get_post_status( $order_id );

		if ( false === $post_status ) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Check if order is redsys order
	 *
	 * @param string $order_id Order ID.
	 * @param string $type Order type.
	 */
	public function is_redsys_order( $order_id, $type = null ) {

		$order = wc_get_order( $order_id );

		if ( $order ) {

			$gateway      = $order->get_payment_method();
			$redsys_types = $this->get_orders_type();

			if ( empty( $redsys_types ) ) {
				return false;
			}

			if ( $type ) {
				return $gateway === $type;
			}

			foreach ( $redsys_types as $redsys_type ) {
				if ( (string) $redsys_type === (string) $gateway ) {
					return true;
				}
			}
		}
	}
	/**
	 * Get Gateway
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_gateway( $order_id ) {

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
	 * Get Order date
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_order_date( $order_id ) {
		$date_decoded = str_replace( '%2F', '/', $this->get_order_meta( $order_id, '_payment_date_redsys', true ) );
		if ( ! $date_decoded ) {
			return false;
		}
		return $date_decoded;
	}
	/**
	 * Get Order hour
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_order_hour( $order_id ) {
		$hour_decoded = str_replace( '%3A', ':', $this->get_order_meta( $order_id, '_payment_hour_redsys', true ) );
		if ( ! $hour_decoded ) {
			return false;
		}
		return $hour_decoded;
	}
	/**
	 * Get Order auth
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_order_auth( $order_id ) {
		$auth = $this->get_order_meta( $order_id, '_authorisation_code_redsys', true );
		if ( ! $auth ) {
			return false;
		}
		return $auth;
	}
	/**
	 * Get Order Number
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_order_mumber( $order_id ) {
		$number = $this->get_order_meta( $order_id, '_payment_order_number_redsys', true );
		if ( ! $number ) {
			return false;
		}
		return $number;
	}
	/**
	 * Get order auth refund.
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_order_auth_refund( $order_id ) {
		$auth = $this->get_order_meta( $order_id, '_authorisation_code_refund_redsys', true );
		if ( ! $auth ) {
			return false;
		}
		return $auth;
	}
	/**
	 * Get Order Paygold Link
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_order_pay_gold_link( $order_id ) {
		$link = $this->get_order_meta( $order_id, '_paygold_link_redsys', true );
		if ( ! $link ) {
			return false;
		}
		return $link;
	}
	/**
	 * Set Order Paygold Link
	 *
	 * @param string $order_id Order ID.
	 * @param string $link Link.
	 */
	public function set_order_paygold_link( $order_id, $link ) {
		$this->update_order_meta( $order_id, '_paygold_link_redsys', $link );
	}
	/**
	 * Update order meta.
	 *
	 * @param int    $post_id Post ID.
	 * @param array  $meta_key_array Meta keys array.
	 * @param string $meta_value Meta value.
	 *
	 * @return void
	 */
	public function update_order_meta( $post_id, $meta_key_array, $meta_value = false ) {
		if ( ! is_array( $meta_key_array ) ) {
			$meta_keys = array( $meta_key_array => $meta_value );
		} else {
			$meta_keys = $meta_key_array;
		}
		$order_id = $this->get_order_meta( $post_id, 'post_id', true );
		if ( $order_id ) {
			$post_id = $order_id;
			$order   = wc_get_order( $post_id );
		} else {
			$order = wc_get_order( $post_id );
		}
		foreach ( $meta_keys as $meta_key => $meta_value ) {
			$order->update_meta_data( $meta_key, $meta_value );
		}
		$order->save();
	}
	/**
	 * Get order meta.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $key Meta key.
	 * @param bool   $single Single.
	 * @param string $context Context.
	 *
	 * @return mixed
	 */
	public function get_order_meta( $order_id, $key, $single = true, $context = false ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			return $order->get_meta( $key, $single, $context );
		}
		return false;
	}
	/**
	 * Get transient.
	 *
	 * @param array  $data Data.
	 * @param string $order_id Order ID.
	 */
	public function set_transient( $data = false, $order_id = false ) {
		if ( ! $order_id || ! $data ) {
			return false;
		}
		if ( is_array( $data ) ) {
			$data = array( $data );
		}
		$serialized = maybe_serialize( $data );
		set_transient( 'redsys_transients_' . $order_id, $serialized, 3600 );
	}
	/**
	 * Get transient.
	 *
	 * @param string $order_id Order ID.
	 */
	public function get_transient( $order_id = false ) {
		if ( ! $order_id ) {
			return false;
		}
		$transient = get_transient( 'redsys_transients_' . $order_id );
		if ( $transient ) {
			$transient = maybe_unserialize( $transient );
			return $transient;
		}
		return false;
	}
	/**
	 * Get status pending.
	 */
	public function get_status_pending() {

		include_once REDSYS_PLUGIN_DATA_PATH_P . 'redsys-status-paid.php';

		$status = array();
		$status = redsys_return_status_paid();
		return apply_filters( 'redsys_status_pending', $status );
	}
	/**
	 * Check if order is paid.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool
	 */
	public function is_paid( $order_id ) {

		$this->debug( 'is_paid()' );
		$this->debug( '$order_id: ' . $order_id );

		if ( $this->order_exist( $order_id ) ) {

			$this->debug( 'Order exist' );

			$order       = $this->get_order( $order_id );
			$status      = $order->get_status();
			$status_paid = array();

			$status_paid = $this->get_status_pending();
			if ( $status_paid ) {
				foreach ( $status_paid as $spaid ) {
					$this->debug( '$status: ' . $status );
					$this->debug( '$spaid: ' . $spaid );
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
	 * Check if order is cancelled.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool
	 */
	public function is_cancelled( $order_id ) {

		if ( $this->order_exist( $order_id ) ) {

			$order  = $this->get_order( $order_id );
			$status = $order->get_status();

			if ( 'cancelled' === $status ) {
				return true;
			}
			return false;
		}
	}
	/**
	 * Check if Gateway is enabled.
	 *
	 * @param string $gateway Gateway.
	 */
	public function is_gateway_enabled( $gateway ) {
		$is_enabled = $this->get_redsys_option( 'enabled', $gateway );

		if ( 'yes' === $is_enabled ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check if token is valid.
	 *
	 * @param int $token_id Token ID.
	 */
	public function check_if_token_is_valid( $token_id ) {

		$token        = WC_Payment_Tokens::get( $token_id );
		$year         = $token->get_expiry_year();
		$month        = $token->get_expiry_month();
		$act_year     = date( 'Y' ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		$act_month    = date( 'm' ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
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
	 * Check Token Type exist in tokens.
	 *
	 * @param array  $tokens Tokens.
	 * @param string $type Type.
	 */
	public function check_type_exist_in_tokens( $tokens, $type ) {
		foreach ( $tokens as $token ) {
			$token_num  = $token->get_token();
			$token_id   = $token->get_id();
			$token_type = $this->get_token_type( $token_id );
			if ( $token_type === $type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token_id );
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
	 * Get Redsys Users Token.
	 *
	 * @param string $type Type.
	 * @param array  $data Data.
	 */
	public function get_redsys_users_token( $type = false, $data = false ) {
		// $type puede ser R (suscripción) o C (principalmente pago con 1 clic) en estos momentos.
		$customer_token = false;
		if ( is_user_logged_in() ) {
			if ( ! $type ) {
				$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'redsys' );
				foreach ( $tokens as $token ) {
					if ( $token->get_gateway_id() === 'redsys' ) {
						$valid_token = $this->check_if_token_is_valid( $token->get_id() );
						if ( $valid_token ) {
							return $token->get_token();
						}
						break;
					} else {
						continue;
					}
				}
			} else {
				$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'redsys' );
				foreach ( $tokens as $token ) {
					$token_num = $token->get_token();
					$token_id  = $token->get_id();
					$type_type = $this->get_token_type( $token_id );
					if ( $type === $type_type ) {
						if ( $token->get_gateway_id() === 'redsys' ) {
							$valid_token = $this->check_if_token_is_valid( $token_id );
							if ( $valid_token ) {
								if ( 'id' === $data ) {
									return $token_id;
								}
								return $token_num;
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
	 * Get Users Token Bulk.
	 *
	 * @param string $user_id User ID.
	 * @param string $type Type.
	 * @param array  $data Data.
	 */
	public function get_users_token_bulk( $user_id, $type = false, $data = false ) {
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
				$token_num = $token->get_token();
				$token_id  = $token->get_id();
				$type_type = $this->get_token_type( $token_id );
				if ( $type === $type_type || ! $type_type ) {
					$valid_token = $this->check_if_token_is_valid( $token_id );
					if ( $valid_token ) {
						if ( 'id' === $data ) {
							return $token_id;
						}
						return $token_num;
					}
				} else {
					continue;
				}
			}
			return $customer_token;
		}
	}
	/**
	 * Clean Order Number.
	 *
	 * @param string $ordernumber Order Number.
	 */
	public function clean_order_number( $ordernumber ) {
		$this->debug( 'Function clean_order_number()' );
		$this->debug( '$ordernumber: ' . $ordernumber );
		$real_order = get_transient( 'redys_order_temp_' . $ordernumber );
		$this->debug( '$ordernumber: ' . $ordernumber );
		if ( $real_order ) {
			return $real_order;
		} else {
			return ltrim( substr( $ordernumber, 3 ), '0' );
		}
	}
	/**
	 * Get Letters Up.
	 *
	 * @param string $length Length.
	 */
	public function get_letters_up( $length ) {
		$characters        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ wp_rand( 0, $characters_length - 1 ) ];
		}
		return $random_string;
	}
	/**
	 * Get Letters.
	 *
	 * @param string $length Length.
	 */
	public function get_letters( $length ) {
		$characters        = 'abcdefghijklmnopqrstuvwxyz';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ wp_rand( 0, $characters_length - 1 ) ];
		}
		return $random_string;
	}
	/**
	 * Prepare Order Number.
	 *
	 * @param string $order_id Order ID.
	 * @param string $gateway Gateway.
	 */
	public function prepare_order_number( $order_id, $gateway = false ) {

		if ( ! $gateway ) {
			$transaction_id  = str_pad( $order_id, 12, '0', STR_PAD_LEFT );
			$transaction_id1 = wp_rand( 1, 999 ); // lets to create a random number.
			$transaction_id2 = substr_replace( $transaction_id, $transaction_id1, 0, -9 ); // new order number.
		} else {
			$ordernumbertype = $this->get_redsys_option( 'redsysordertype', $gateway );
			$sufix           = $this->get_redsys_option( 'subfix', $gateway );
			if ( $sufix ) {
				$sufix = $sufix;
			} else {
				$sufix = '';
			}
			if ( ! $ordernumbertype || 'threepluszeros' === $ordernumbertype ) {
				$transaction_id  = str_pad( $order_id, 12, '0', STR_PAD_LEFT );
				$transaction_id1 = wp_rand( 1, 999 ); // lets to create a random number.
				$transaction_id2 = substr_replace( $transaction_id, $transaction_id1, 0, -9 ); // new order number.
			} elseif ( 'endoneletter' === $ordernumbertype ) {
				$letters         = $this->get_letters( 1 );
				$transaction_id2 = str_pad( $order_id . $letters . $sufix, 9, '0', STR_PAD_LEFT );
			} elseif ( 'endtwoletters' === $ordernumbertype ) {
				$letters         = $this->get_letters( 2 );
				$transaction_id2 = str_pad( $order_id . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endthreeletters' === $ordernumbertype ) {
				$letters         = $this->get_letters( 3 );
				$transaction_id2 = str_pad( $order_id . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endoneletterup' === $ordernumbertype ) {
				$letters         = $this->get_letters_up( 1 );
				$transaction_id2 = str_pad( $order_id . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endtwolettersup' === $ordernumbertype ) {
				$letters         = $this->get_letters_up( 2 );
				$transaction_id2 = str_pad( $order_id . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endthreelettersup' === $ordernumbertype ) {
				$letters         = $this->get_letters_up( 3 );
				$transaction_id2 = str_pad( $order_id . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endoneletterdash' === $ordernumbertype ) {
				$letters         = $this->get_letters( 1 );
				$transaction_id2 = str_pad( $order_id . '-' . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endtwolettersdash' === $ordernumbertype ) {
				$letters         = $this->get_letters( 2 );
				$transaction_id2 = str_pad( $order_id . '-' . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endthreelettersdash' === $ordernumbertype ) {
				$letters         = $this->get_letters( 3 );
				$transaction_id2 = str_pad( $order_id . '-' . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endoneletterupdash' === $ordernumbertype ) {
				$letters         = $this->get_letters_up( 1 );
				$transaction_id2 = str_pad( $order_id . '-' . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endtwolettersupdash' === $ordernumbertype ) {
				$letters         = $this->get_letters_up( 2 );
				$transaction_id2 = str_pad( $order_id . '-' . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'endthreelettersupdash' === $ordernumbertype ) {
				$letters         = $this->get_letters_up( 3 );
				$transaction_id2 = str_pad( $order_id . '-' . $letters . $sufix, 12, '0', STR_PAD_LEFT );
			} elseif ( 'simpleorder' === $ordernumbertype ) {
				$transaction_id2 = str_pad( $order_id . $sufix, 12, '0', STR_PAD_LEFT );
			}
		}
		set_transient( 'redys_order_temp_' . $transaction_id2, $order_id, 3600 );
		return $transaction_id2;
	}
	/**
	 * Redsys amount format
	 *
	 * @param  float $total Order total.
	 */
	public function redsys_amount_format( $total ) {

		if ( 0 === $total || 0.00 === $total ) {
			return '000';
		}

		$order_total_sign = number_format( $total, 2, '', '' );
		return $order_total_sign;
	}
	/**
	 * Obtiene una descripción del producto para utilizarla como descripción del pedido en Redsys.
	 *
	 * @param WC_Order $order Objeto que representa el pedido de WooCommerce.
	 * @param string   $gateway Nombre de la pasarela de pago.
	 * @return string|null Cadena de texto con la descripción del producto, o null si el pedido no es de Redsys.
	 */
	public function product_description( $order, $gateway ) {
		// Verificar si el pedido es de Redsys.
		if ( ! $this->is_redsys_order( $order->get_id() ) ) {
			return null;
		}

		// Inicializar variables para almacenar los IDs de producto, nombres y SKUs.
		$product_ids = array();
		$names       = array();
		$skus        = array();

		// Recorrer los elementos del pedido para obtener información de cada producto.
		foreach ( $order->get_items() as $item ) {
			// Almacenar el ID de producto y nombre en arrays separados.
			$product_ids[] = $item->get_product_id();
			$names[]       = $item->get_name();

			// Obtener el objeto de producto correspondiente y obtener su SKU.
			$product = wc_get_product( $item->get_product_id() );
			$skus[]  = $product->get_sku();
		}

		// Definir las opciones de descripción disponibles y sus valores correspondientes.
		$description_options = array(
			'id'    => implode( ', ', $product_ids ),
			'name'  => implode( ', ', $names ),
			'sku'   => implode( ', ', $skus ),
			'order' => __( 'Order', 'woocommerce-redsys' ) . ' ' . $order->get_order_number(),
		);

		// Obtener el tipo de descripción seleccionado por el usuario.
		$description_type = $this->get_redsys_option( 'descripredsys', $gateway );

		// Obtener el valor correspondiente a la opción seleccionada.
		$description = isset( $description_options[ $description_type ] ) ? $description_options[ $description_type ] : $description_options['order'];

		/**
		 * Aplicar cualquier filtro definido por otros plugins o temas.
		 */
		$description = apply_filters( 'redsys_product_description', $description, $order );

		// Devolver la descripción del producto.
		return $description;
	}
	/**
	 * Check if order has yith subscriptions
	 *
	 * @param int $order_id Order ID.
	 */
	public function check_order_has_yith_subscriptions( $order_id ) {
		if ( defined( 'YITH_YWSBS_PREMIUM' ) || defined( 'YITH_YWSBS_VERSION' ) ) {
			$order    = $this->get_order( $order_id );
			$has_meta = $order->get_meta( 'subscriptions' );
			if ( $has_meta ) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Check if order has SUMO subscriptions
	 *
	 * @param int $order_id Order ID.
	 */
	public function check_order_has_sumo_subscriptions( $order_id ) {
		$this->debug( 'Function check_order_has_sumo_subscriptions' );
		if ( defined( 'SUMO_SUBSCRIPTIONS_PLUGIN_FILE' ) ) {
			$this->debug( 'SUMO_SUBSCRIPTIONS_PLUGIN_FILE defined' );
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				$this->debug( 'Not an Order' );
				return false;
			}
			$items = $order->get_items();
			foreach ( $items as $item ) {
				$product_id  = $item->get_product_id();
				$sumo_status = (string) get_post_meta( $product_id, 'sumo_susbcription_status', true );
				$this->debug( '$sumo_status: ' . $sumo_status );
				if ( '1' === $sumo_status ) {
					$this->debug( '$sumo_status: return true' );
					return true;
				}
			}
			$this->debug( 'foreach: return false' );
			return false;
		}
		$this->debug( 'SUMO_SUBSCRIPTIONS_PLUGIN_FILE not defined' );
		return false;
	}
	/**
	 * Check if order has pre order
	 *
	 * @param int $order_id Order ID.
	 */
	public function check_order_has_pre_order( $order_id ) {

		if ( ! class_exists( 'WC_Pre_Orders_Order' ) ) {
			return false;
		} elseif ( WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Create add payment method number
	 */
	public function create_add_payment_method_number() {

		wp_cache_delete( 'number_ad_paymnt_mehod' );
		$current_number = get_option( 'number_ad_paymnt_mehod' );

		if ( ! $current_number ) {
			update_option( 'number_ad_paymnt_mehod', '1', false );
			$number_to_send = str_pad( '1', 12, '0', STR_PAD_LEFT );
			return $number_to_send;
		} else {
			$new_number    = intval( $current_number ) + 1;
			$string_number = strval( $new_number );
			update_option( 'number_ad_paymnt_mehod', $string_number, false );
			$number_to_send = str_pad( $string_number, 12, '0', STR_PAD_LEFT );
			return $number_to_send;
		}
	}
	/**
	 * Create checkout insite number
	 */
	public function create_checkout_insite_number() {

		wp_cache_delete( 'number_insite_checkout' );
		$current_number = get_option( 'number_insite_checkout' );

		if ( ! $current_number ) {
			update_option( 'number_insite_checkout', '1', false );
			$number_to_send = '1' . str_pad( '1', 11, '0', STR_PAD_LEFT );
			return $number_to_send;
		} else {
			$new_number    = intval( $current_number ) + 1;
			$string_number = strval( $new_number );
			update_option( 'number_insite_checkout', $string_number, false );
			$number_to_send = '1' . str_pad( $string_number, 11, '0', STR_PAD_LEFT );
			return $number_to_send;
		}
	}
	/**
	 * Get url add payment method
	 *
	 * @param string $gateway Gateway.
	 * @param int    $user_id User ID.
	 * @param string $token_type Token type.
	 */
	public function get_url_add_payment_method( $gateway, $user_id, $token_type ) {

		$number = $this->create_add_payment_method_number();
		set_transient( $number, $user_id, 600 );
		set_transient( $number . '_get_method', 'yes', 600 );
		set_transient( $number . '_token_type', $token_type, 600 );
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
	 * Get url redsys payment
	 *
	 * @param int    $order_id Order ID.
	 * @param string $final_notify_url Final notify url.
	 */
	public function get_url_redsys_payment( $order_id, $final_notify_url ) {

		$pay_url = $final_notify_url;
		if ( 'iframe' === $this->get_redsys_option( 'usebrowserreceipt', 'redsys' ) ) {
			set_transient( $order_id . '_iframe', 'yes' );
			return add_query_arg(
				array(
					'redsys-order-id' => $order_id,
					'redsys-iframe'   => 'yes',
				),
				$pay_url
			);
		}
		return add_query_arg(
			array(
				'redsys-order-id' => $order_id,
			),
			$pay_url
		);
	}
	/**
	 * Get url redsys payment
	 *
	 * @param int    $order_id Order ID.
	 * @param string $final_notify_url Final notify url.
	 */
	public function get_url_bizum_payment( $order_id, $final_notify_url ) {

		$pay_url = $final_notify_url;
		set_transient( $order_id . '_iframe', 'yes' );
		return add_query_arg(
			array(
				'bizum-order-id' => $order_id,
				'bizum-iframe'   => 'yes',
			),
			$pay_url
		);
	}
	/**
	 * Check Subscription plugin exist.
	 */
	public function subscription_plugin_exist() {
		if ( function_exists( 'wcs_order_contains_subscription' ) ) {
			return true;
		} elseif ( defined( 'YITH_YWSBS_PREMIUM' ) || defined( 'YITH_YWSBS_VERSION' ) ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Get all tokens by type.
	 *
	 * @param int    $user_id User ID.
	 * @param string $type Type.
	 */
	public function get_all_tokens( $user_id, $type ) {
		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		foreach ( $tokens as $token ) {
			$token_num = $token->get_token();
			$token_id  = $token->get_id();
			$type_type = $this->get_token_type( $token_id );
			if ( $type === $type_type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$brand = $token->get_card_type();
					$last4 = $token->get_last4();
					$month = $token->get_expiry_month();
					$year  = substr( $token->get_expiry_year(), -2 );
					$last4 = $token->get_last4();
					echo esc_html( $brand ) . ' - ' . esc_html( $token->get_token() ) . ' - ' . esc_html( $last4 ) . ' - ' . esc_html( $month ) . '/' . esc_html( $year ) . "\r\n";
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
	 * Check tokens exist.
	 *
	 * @param int    $user_id User ID.
	 * @param string $type Type.
	 */
	public function check_tokens_exist( $user_id, $type ) {
		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		foreach ( $tokens as $token ) {
			$token_num = $token->get_token();
			$token_id  = $token->get_id();
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
	 * Get all tokens by type.
	 *
	 * @param int    $user_id User ID.
	 * @param string $type Type.
	 */
	public function get_all_tokens_checkout( $user_id, $type ) {
		$tokens = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
		foreach ( $tokens as $token ) {
			$token_num = $token->get_token();
			$token_id  = $token->get_id();
			$type_type = $this->get_token_type( $token_id );
			if ( $type === $type_type ) {
				if ( $token->get_gateway_id() === 'redsys' ) {
					$valid_token = $this->check_if_token_is_valid( $token->get_id() );
					$token_id    = $token->get_id();
					$brand       = $token->get_card_type();
					$last4       = $token->get_last4();
					$month       = $token->get_expiry_month();
					$year        = substr( $token->get_expiry_year(), -2 );
					echo '<input id="' . esc_html( $token_id ) . '" type="radio" class="input-radio" name="token" value="' . esc_html( $token_id ) . '" />';
					echo '<label for="' . esc_html( $token_id ) . '"> ' . esc_html( $brand ) . ' ' . esc_html__( 'ending in', 'woocommerce-redsys' ) . ' ' . esc_html( $last4 ) . ' (' . esc_html__( 'expires ', 'woocommerce-redsys' ) . esc_html( $month ) . '/' . esc_html( $year ) . ')</label><br />';
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
	 * Check if order contains subscription.
	 *
	 * @param int $order_id Order ID.
	 */
	public function order_contains_subscription( $order_id ) {
		$this->debug( 'Function order_contains_subscription()' );
		if ( $this->check_order_has_yith_subscriptions( $order_id ) ) {
			$this->debug( 'check_order_has_yith_subscriptions: TRUE' );
			return true;
		} elseif ( $this->check_order_has_sumo_subscriptions( $order_id ) ) {
			$this->debug( 'check_order_has_sumo_subscriptions: TRUE' );
			return true;
		} elseif ( $this->check_order_has_pre_order( $order_id ) ) {
			$this->debug( 'check_order_has_pre_order: TRUE' );
			return true;
		} elseif ( $this->get_redsys_token_r( $order_id ) ) {
			$this->debug( 'get_redsys_token_r: TRUE' );
			return true;
		} elseif ( ! function_exists( 'wcs_order_contains_subscription' ) ) {
			$this->debug( '! function_exists( "wcs_order_contains_subscription" ): FALSE' );
			return false;
		} elseif ( wcs_order_contains_subscription( $order_id ) ) {
			$this->debug( 'wcs_order_contains_subscription: TRUE' );
			return true;
		} elseif ( wcs_order_contains_resubscribe( $order_id ) ) {
			$this->debug( 'wcs_order_contains_resubscribe: TRUE' );
			return true;
		} elseif ( wcs_order_contains_renewal( $order_id ) ) {
			$this->debug( 'wcs_order_contains_renewal: TRUE' );
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check if order is paid Loop.
	 *
	 * @param int $order_id Order ID.
	 */
	public function check_order_is_paid_loop( $order_id ) {
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
	 * Collect invoice by ID.
	 *
	 * @param int $order_id Order ID.
	 */
	public function collect_invoice_by_id( $order_id ) {
		$order  = new WC_Order( $order_id );
		$result = WC_Gateway_Redsys::charge_invoive_by_order( $order );
		return $result;
	}
	/**
	 * Check if order needs payment.
	 *
	 * @param bool    $needs_payment Whether the order needs payment.
	 * @param WC_Cart $cart Cart object.
	 */
	public static function cart_needs_payment( $needs_payment, $cart ) {

		foreach ( $cart->get_cart() as $item => $values ) {

			$product_id = $values['product_id'];
			$get        = get_post_meta( $product_id, '_redsystokenr', true );
			if ( false === $needs_payment && 0 === $cart->total && 'yes' === $get ) {
				$needs_payment = true;
				return $needs_payment;
			}
		}
		return $needs_payment;
	}
	/**
	 * Check if order needs payment.
	 *
	 * @param bool     $needs_payment Whether the order needs payment.
	 * @param WC_Order $order Order object.
	 * @param array    $valid_order_statuses Array of valid order statuses.
	 */
	public static function order_needs_payment( $needs_payment, $order, $valid_order_statuses ) {
		// $global = new WC_Gateway_Redsys_Global();
		// Skips checks if the order already needs payment.

		$order_id = $order->get_id();

		$need_token = WCRed()->check_order_needs_token_r( $order_id );

		if ( $need_token ) {
			return true;
		}

		if ( $needs_payment ) {
			return $needs_payment;
		}

		if ( $order->get_total() > 0 ) {
			return $needs_payment;
		}
	}
	/**
	 * Check simple product subscription.
	 *
	 * @param int $product_id Product ID.
	 */
	public function check_simple_product_subscription( $product_id ) {

		$this->debug( 'Function check_simple_product_subscription()' );

		$product = wc_get_product( $product_id );

		$is_variable = $product instanceof WC_Product_Variable;

		if ( $is_variable ) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				$variation_product  = wc_get_product( $variation['variation_id'] );
				$variation_id       = $variation_product->get_id();
				$subscription_check = $this->check_subscription( $variation_id );
				if ( 'R' === $subscription_check ) {
					return 'R';
				}
			}
			return 'C';
		} else {
			return $this->check_subscription( $product_id );
		}
	}
	/**
	 * Check if product is subscription.
	 *
	 * @param int $product_id Product ID.
	 */
	private function check_subscription( $product_id ) {
		$this->debug( 'Function check_subscription()' );
		if ( $this->check_redsys_subscription_checkout( $product_id ) ) {
			$this->debug( 'check_redsys_subscription_checkout: TRUE' );
			return 'R';
		} elseif ( $this->check_yith_subscription_checkout( $product_id ) ) {
			$this->debug( 'check_yith_subscription_checkout: TRUE' );
			return 'R';
		} elseif ( $this->check_woo_subscription_checkout( $product_id ) ) {
			$this->debug( 'check_woo_subscription_checkout: TRUE' );
			return 'R';
		} elseif ( $this->check_sumo_subscription_checkout( $product_id ) ) {
			$this->debug( 'check_sumo_subscription_checkout: TRUE' );
			return 'R';
		} elseif ( class_exists( 'WCS_ATT' ) ) {
			$this->debug( 'class_exists( "WCS_ATT" ): TRUE' );
			if ( get_post_meta( $product_id, '_wcsatt_schemes', true ) ) {
				$this->debug( 'get_post_meta( $product_id, "_wcsatt_schemes", true ): ' . get_post_meta( $product_id, '_wcsatt_schemes', true ) );
				// _wcsatt_schemes exists, so All Products Subscriptions is in action.
				return 'R';
			} else {
				$this->debug( 'get_post_meta( $product_id, "_wcsatt_schemes", true ): FALSE' );
			}
		} else {
			$this->debug( 'check_simple_product_subscription() return C )' );
			return 'C';
		}

	}
	/**
	 * Check if product needs preauth.
	 *
	 * @param int $product_id Product ID.
	 */
	public function need_preauth( $product_id ) {

		$global_preauth = $this->get_redsys_option( 'redsyspreauthall', 'redsys' );

		if ( 'yes' === $global_preauth ) {
			return true;
		}

		$need = get_post_meta( $product_id, '_redsyspreauth', true );
		if ( 'yes' === $need ) {
			return true;
		}
		return false;
	}
	/**
	 * Check if product needs token R.
	 *
	 * @param int $product_id Product ID.
	 */
	public function need_token_r( $product_id ) {

		$need = get_post_meta( $product_id, '_redsystokenr', true );
		if ( 'yes' === $need ) {
			return true;
		}
		return false;
	}
	/**
	 * Check if product needs Preauth
	 *
	 * @param array $the_card The Card.
	 */
	public function check_card_preauth( $the_card ) {
		foreach ( $the_card as $cart_item_key => $cart_item ) {
			$product_id = $cart_item['product_id'];
			$preauth    = $this->need_preauth( $product_id );
			if ( $preauth ) {
				return true;
			}
			continue;
		}
		return false;
	}
	/**
	 * Check Card for Subscription.
	 *
	 * @param array $the_card The Card.
	 */
	public function check_card_for_subscription( $the_card ) {

		foreach ( $the_card as $cart_item_key => $cart_item ) {

			$product_id = $cart_item['product_id'];

			if ( class_exists( 'WCS_ATT' ) ) {
				if ( ! empty( get_post_meta( $product_id, '_wcsatt_schemes', true ) ) ) {
					$this->debug( 'check_card_for_subscription All Product Subscriptions: ' . print_r( get_post_meta( $product_id, '_wcsatt_schemes', true ), true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					return 'R';
				}
			}
			$token_type = $this->check_simple_product_subscription( $product_id );
			if ( 'R' === $token_type ) {
				return $token_type;
			}
			if ( class_exists( 'WC_Subscriptions_Cart' ) ) {
				if ( WC_Subscriptions_Cart::cart_contains_subscription() ) {
					return 'R';
				}
			}

			if ( 'subscription' === get_the_terms( $product_id, 'product_type' )[0]->slug ) {
				return 'R';
			} elseif ( 'variable-subscription' === get_the_terms( $product_id, 'product_type' )[0]->slug ) {
				return 'R';
			}
			continue;
		}
		return 'C';
	}
	/**
	 * Check if product needs token R.
	 *
	 * @param int $product_id Product ID.
	 */
	public function check_product_for_subscription( $product_id ) {
		$product = wc_get_product( $product_id );

		// Verifica si el producto es una instancia de WC_Product_Variable.
		$is_variable = $product instanceof WC_Product_Variable;

		if ( $is_variable ) {
			// Si es un producto variable, verifica cada variación.
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				$variation_product = wc_get_product( $variation['variation_id'] );
				$subscription_check = $this->check_simple_product_subscription( $variation_product->get_id() );
				if ( 'R' === $subscription_check ) {
					return 'R';
				}
			}
		}
		if ( class_exists( 'WCS_ATT' ) ) {
			$schemes = $product->get_meta( '_wcsatt_schemes', true );
			if ( ! empty( $schemes ) ) {
				$this->debug( 'check_product_for_subscription All Product Subscriptions: ' . print_r( $schemes, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				return 'R';
			}
		}

		$token_type = $this->check_simple_product_subscription( $product_id );
		if ( 'R' === $token_type ) {
			return $token_type;
		}

		if ( class_exists( 'WC_Subscriptions_Product' ) ) {
			if ( WC_Subscriptions_Product::is_subscription( $product ) ) {
				return 'R';
			}
		}

		$product_type = $product->get_type();
		if ( 'subscription' === $product_type || 'variable-subscription' === $product_type ) {
			return 'R';
		}

		return 'C';
	}

	/**
	 * Get token by id.
	 *
	 * @param int $token_id Token ID.
	 */
	public function get_token_by_id( $token_id ) {

		$token = WC_Payment_Tokens::get( (int) $token_id );
		return (string) $token->get_token();
	}
	/**
	 * Get card brand.
	 *
	 * @param string $dscardbrand Card brand.
	 */
	public function get_card_brand( $dscardbrand = false ) {

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
	 * Remove token.
	 *
	 * @param array $data Data.
	 */
	public function remove_token( $data ) {

		$merchant_code       = $data['merchant_code'];
		$merchant_identifier = $data['merchant_identifier'];
		$order_id            = $data['order_id'];
		$terminal            = ltrim( $data['terminal'], '0' );
		$secretsha256        = $data['sha256'];
		$redsys_adr          = $data['redsys_adr'];
		$mi_obj              = new WooRedsysAPI();

		$mi_obj->setParameter( 'DS_MERCHANT_MERCHANTCODE', $merchant_code );
		$mi_obj->setParameter( 'Ds_Merchant_Identifier', $merchant_identifier );
		$mi_obj->setParameter( 'DS_MERCHANT_ORDER', $order_id );
		$mi_obj->setParameter( 'DS_MERCHANT_TERMINAL', $terminal );
		$mi_obj->setParameter( 'DS_MERCHANT_TRANSACTIONTYPE', '44' );

		$version   = 'HMAC_SHA256_V1';
		$params    = $mi_obj->createMerchantParameters();
		$signature = $mi_obj->createMerchantSignature( $secretsha256 );

		$response = wp_remote_post(
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
		$decodec       = $mi_obj->decodeMerchantParameters( $result->Ds_MerchantParameters ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$decodec_array = json_decode( $decodec );

		$return = array(
			'order_id'            => $decodec_array->Ds_Order, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'merchant_code'       => $decodec_array->Ds_MerchantCode, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'terminal'            => ltrim( $decodec_array->Ds_Terminal, '0' ), // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'ds_terminal'         => $decodec_array->Ds_Response, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'ds_transaction_type' => $decodec_array->Ds_TransactionType, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		);

		if (
			(int) $order_id === (int) $decodec_array->Ds_Order && // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			(int) $merchant_code === (int) $decodec_array->Ds_MerchantCode && // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			(int) $terminal === (int) $decodec_array->Ds_Terminal && // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			000 === (int) $decodec_array->Ds_Response && // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			44 === (int) $decodec_array->Ds_TransactionType // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check SOAP.
	 *
	 * @param string $terminal_state Terminal state.
	 *
	 * @return boolean
	 */
	public function check_soap( $terminal_state = 'real' ) {
		$link = ( 'real' === $terminal_state ) ? 'https://sis.redsys.es:443/sis/services/SerClsWSEntradaV2?wsdl' : 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl';

		try {
			$soap_client = new SoapClient( $link );
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}
	/**
	 * Get the IP.
	 *
	 * @return string
	 */
	public function get_the_ip() {

		if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} else {
			return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		}
	}
	/**
	 * Send paygold link.
	 *
	 * @param int    $post_id Post ID.
	 * @param array  $data Data.
	 * @param string $type Type.
	 */
	public function send_paygold_link( $post_id = false, $data = false, $type = false ) {

			$this->debug( '$status: ' . $status );
			$this->debug( 'arrive to Global send_paygold_link() function' );
			$this->debug( '$post_id: ' . $post_id );
			$this->debug( '$data: ' . print_r( $data, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

		if ( $post_id ) {

			// Capturamos todo de $post_id.
			$order_id                        = $post_id;
			$order                           = $this->get_order( $order_id );
			$type                            = $data['send_type'];
			$send_to                         = $data['send_to'];
			$subject                         = remove_accents( $this->get_redsys_option( 'subject', 'paygold' ) );
			$name                            = remove_accents( $order->get_billing_first_name() );
			$last_name                       = remove_accents( $order->get_billing_last_name() );
			$adress_ship_ship_addr_line1     = remove_accents( $order->get_billing_address_1() );
			$adress_ship_ship_addr_line2     = remove_accents( $order->get_billing_address_2() );
			$adress_ship_ship_addr_city      = remove_accents( $order->get_billing_city() );
			$adress_ship_ship_addr_state     = remove_accents( strtolower( $order->get_billing_state() ) );
			$adress_ship_ship_addr_post_code = remove_accents( $order->get_billing_postcode() );
			$adress_ship_ship_addr_country   = remove_accents( strtolower( $order->get_billing_country() ) );
			$customermail                    = $order->get_billing_email();
			$order_total_sign                = $this->redsys_amount_format( $order->get_total() );
			$orderid2                        = $this->prepare_order_number( $order_id, 'paygold' );
			$product_description             = $this->product_description( $order, 'paygold' );
			$description                     = WCRed()->product_description( $order, 'paygold' );
		} else {
			$order_id                        = $this->create_add_payment_method_number();
			$user_id                         = $data['user_id'];
			$user                            = get_user_by( 'id', $user_id );
			$type                            = $data['link_type'];
			$token_type                      = $data['token_type'];
			$send_to                         = $data['send_to'];
			$subject                         = remove_accents( $this->get_redsys_option( 'subject', 'paygold' ) );
			$name                            = remove_accents( $user->billing_first_name );
			$last_name                       = remove_accents( $user->billing_last_name );
			$adress_ship_ship_addr_line1     = remove_accents( $user->billing_address_1 );
			$adress_ship_ship_addr_line2     = remove_accents( $user->billing_address_2 );
			$adress_ship_ship_addr_city      = remove_accents( $user->billing_city );
			$adress_ship_ship_addr_state     = remove_accents( strtolower( $user->billing_state ) );
			$adress_ship_ship_addr_post_code = remove_accents( $user->billing_postcode );
			$adress_ship_ship_addr_country   = remove_accents( strtolower( $user->billing_country ) );
			$customermail                    = $data['email'];
			$order_total_sign                = '0';
			$orderid2                        = $order_id;
			$product_description             = 'Add Payment Method';
			$description                     = $data['description'];
		}
		$texto_libre1         = '';
		$sms_txt              = $this->get_redsys_option( 'sms', 'paygold' );
		$currency_codes       = $this->get_currencies();
		$currency             = $currency_codes[ get_woocommerce_currency() ];
		$paygold              = new WC_Gateway_Paygold_Redsys();
		$url_ok               = esc_attr( add_query_arg( 'utm_nooverride', '1', $paygold->get_return_url( $order ) ) );
		$transaction_type     = 'F';
		$mi_obj               = new WooRedsysAPIWS();
		$liveurlws            = 'https://sis.redsys.es:443/sis/services/SerClsWSEntradaV2?wsdl';
		$customer             = $this->get_redsys_option( 'customer', 'paygold' );
		$commercename         = $this->get_redsys_option( 'commercename', 'paygold' );
		$ds_merchant_terminal = $this->get_redsys_option( 'terminal', 'paygold' );
		$secretsha256         = $this->get_redsys_option( 'secretsha256', 'paygold' );
		$descripredsys        = $this->get_redsys_option( 'descripredsys', 'paygold' );
		$merchantgroup        = $this->get_redsys_option( 'merchantgroup', 'paygold' );
		$p2f_xmldata          = '&lt;![CDATA[&lt;nombreComprador&gt;' . $name . ' ' . $last_name . '&lt;&#47;nombreComprador&gt;&lt;direccionComprador&gt;' . $adress_ship_ship_addr_line1 . ' ' . $adress_ship_ship_addr_line2 . ', ' . $adress_ship_ship_addr_city . ', ' . $adress_ship_ship_addr_state . ', ' . $adress_ship_ship_addr_post_code . ', ' . $adress_ship_ship_addr_country . '&lt;&#47;direccionComprador&gt;&lt;textoLibre1&gt;' . $texto_libre1 . '&lt;&#47;textoLibre1&gt;&lt;subjectMailCliente&gt;' . $subject . '&lt;&#47;subjectMailCliente&gt;]]&gt;';
		$ds_signature         = '';
		$expiration           = $this->get_redsys_option( 'expiration', 'paygold' );
		$not_use_https        = $this->get_redsys_option( 'not_use_https', 'paygold' );
		$notify_url_not_https = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Gateway_paygold', home_url( '/' ) ) );
		$notify_url           = add_query_arg( 'wc-api', 'WC_Gateway_paygold', home_url( '/' ) );

		if ( ! $expiration ) {
			$expiration = $expiration;
		} else {
			$expiration = '1440';
		}

		if ( 'yes' === WCRed()->get_redsys_option( 'debug', 'paygold' ) ) {
			$this->debug( '$expiration: ' . $expiration );
		}

		if ( 'yes' === $not_use_https ) {
				$final_notify_url = $notify_url_not_https;
		} else {
			$final_notify_url = $notify_url;
		}

		if ( 'yes' === WCRed()->get_redsys_option( 'debug', 'paygold' ) ) {
			$this->debug( '$final_notify_url: ' . $final_notify_url );
		}

		if ( ! $merchantgroup ) {
			$ds_merchant_group = '';
		} else {
			$ds_merchant_group = '<DS_MERCHANT_GROUP>' . $merchantgroup . '</DS_MERCHANT_GROUP>';
		}

		if ( 'SMS' === $type ) {
			$send     = '<DS_MERCHANT_CUSTOMER_MOBILE>' . $send_to . '</DS_MERCHANT_CUSTOMER_MOBILE>';
			$sms_text = '<DS_MERCHANT_CUSTOMER_SMS_TEXT>' . $sms_txt . '</DS_MERCHANT_CUSTOMER_SMS_TEXT>';
			$xmldata  = '';
		} else {
			$send     = '<DS_MERCHANT_CUSTOMER_MAIL>' . $send_to . '</DS_MERCHANT_CUSTOMER_MAIL>';
			$sms_text = '';
			$xmldata  = '<DS_MERCHANT_P2F_XMLDATA>' . $p2f_xmldata . '</DS_MERCHANT_P2F_XMLDATA>';
		}

		$datos_entrada  = '<DATOSENTRADA>';
		$datos_entrada .= '<DS_MERCHANT_AMOUNT>' . $order_total_sign . '</DS_MERCHANT_AMOUNT>';
		$datos_entrada .= '<DS_MERCHANT_ORDER>' . $orderid2 . '</DS_MERCHANT_ORDER>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTCODE>' . $customer . '</DS_MERCHANT_MERCHANTCODE>';
		$datos_entrada .= '<DS_MERCHANT_CURRENCY>' . $currency . '</DS_MERCHANT_CURRENCY>';
		$datos_entrada .= '<DS_MERCHANT_MERCHANTURL>' . $final_notify_url . '</DS_MERCHANT_MERCHANTURL>';
		$datos_entrada .= '<DS_MERCHANT_TERMINAL>' . $ds_merchant_terminal . '</DS_MERCHANT_TERMINAL>';
		$datos_entrada .= '<DS_MERCHANT_PRODUCTDESCRIPTION>' . $description . '</DS_MERCHANT_PRODUCTDESCRIPTION>';
		$datos_entrada .= '<DS_MERCHANT_P2F_EXPIRYDATE>' . $expiration . '</DS_MERCHANT_P2F_EXPIRYDATE>';
		$datos_entrada .= '<DS_MERCHANT_URLOK>' . $url_ok . '</DS_MERCHANT_URLOK>';

		if ( 'Add Payment Method' === $product_description ) {
			if ( 'R' === $token_type ) {
				$datos_entrada .= $ds_merchant_group;
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>REQUIRED</DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>S</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>R</DS_MERCHANT_COF_TYPE>';

				$exp_sec = $expiration * 60;
				$trans   = $orderid2 . '_add_method_type_subcription';
				set_transient( $trans, 'R', $exp_sec );
				set_transient( $orderid2 . '_user_id_token', $user_id, $exp_sec );
			}
			if ( 'C' === $token_type ) {
				$datos_entrada .= $ds_merchant_group;
				$datos_entrada .= '<DS_MERCHANT_IDENTIFIER>REQUIRED<DS_MERCHANT_IDENTIFIER>';
				$datos_entrada .= '<DS_MERCHANT_COF_INI>S</DS_MERCHANT_COF_INI>';
				$datos_entrada .= '<DS_MERCHANT_COF_TYPE>C</DS_MERCHANT_COF_TYPE>';
				$exp_sec        = $expiration * 60;
				$trans          = $orderid2 . '_add_method_type_subcription';
				set_transient( $trans, 'C', $exp_sec );
				set_transient( $orderid2 . '_user_id_token', $user_id, $exp_sec );
			}
		}

		$datos_entrada .= $send;
		$datos_entrada .= $sms_text;
		$datos_entrada .= '<DS_MERCHANT_TRANSACTIONTYPE>' . $transaction_type . '</DS_MERCHANT_TRANSACTIONTYPE>';
		$datos_entrada .= $xmldata;
		$datos_entrada .= '</DATOSENTRADA>';

		$xml  = '<REQUEST>';
		$xml .= $datos_entrada;
		$xml .= '<DS_SIGNATUREVERSION>HMAC_SHA256_V1</DS_SIGNATUREVERSION>';
		$xml .= '<DS_SIGNATURE>' . $mi_obj->createMerchantSignatureHostToHost( $secretsha256, $datos_entrada ) . '</DS_SIGNATURE>';
		$xml .= '</REQUEST>';

		$this->debug( '$xml: ' . $xml );

		$cliente  = new SoapClient( $liveurlws );
		$response = $cliente->trataPeticion( array( 'datoEntrada' => $xml ) );

		$this->debug( '$response: ' . print_r( $response, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

		if ( isset( $response->trataPeticionReturn ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$xml_retorno       = new SimpleXMLElement( $response->trataPeticionReturn ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$authorisationcode = (string) $xml_retorno->OPERACION->Ds_AuthorisationCode; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$codigo            = (string) $xml_retorno->CODIGO; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$redsys_order      = (string) $xml_retorno->OPERACION->Ds_Order; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$terminal          = (string) $xml_retorno->OPERACION->Ds_Terminal; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$currency_code     = (string) $xml_retorno->OPERACION->Ds_Currency; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$response          = (string) $xml_retorno->OPERACION->Ds_Response; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$urlpago2fases     = (string) $xml_retorno->OPERACION->Ds_UrlPago2Fases; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		} else {
			$this->debug( 'There was and error' );
			return 'error';
		}

		if ( '0' === $codigo && '9998' === $response ) {
			$this->debug( '$urlpago2fases: ' . $urlpago2fases );
			return $urlpago2fases;
		} else {
			$error = $this->get_error( $codigo );
			$this->debug( 'There was and error: ' . $error );
			return $error;
		}
	}
	/**
	 * Check if we need to flush rewrite rules
	 *
	 * @return bool
	 */
	public function has_to_flush() {

		$flush_version = get_option( 'redsys_flush_version' );

		if ( ! $flush_version || (int) $flush_version < (int) REDSYS_FLUSH_VERSION ) {
			update_option( 'redsys_flush_version', REDSYS_FLUSH_VERSION );
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check if an order needs a token
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return bool
	 */
	public function check_order_needs_token_r( $order_id ) {
		$order = wc_get_order( $order_id );
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();
			$get        = get_post_meta( $product_id, '_redsystokenr', true );
			if ( 'yes' === $get ) {
				return true;
			}
			continue;
		}
		return false;
	}
	/**
	 * Do the 3D Secure method
	 *
	 * @param int $order_id Order ID.
	 */
	public function do_make_3dmethod( $order_id ) {

		$three_ds_server_trans_id = get_transient( 'threeDSServerTransID_' . $order_id );
		$three_ds_method_url      = get_transient( 'threeDSMethodURL_' . $order_id );
		$final_notify_url         = get_transient( 'final_notify_url_' . $order_id );
		$array_data               = array(
			'threeDSServerTransID'         => $three_ds_server_trans_id,
			'threeDSMethodNotificationURL' => $final_notify_url,
		);
		$json                     = wp_json_encode( $array_data );
		$this->debug( '$json: ' . $json );
		$json_datos_3dsecure_codificad = base64_encode( $json ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->debug( '$json_datos_3dsecure_codificad: ' . $json_datos_3dsecure_codificad );
		echo '<form id="tresdmethod" method="POST" action="' . esc_url( $three_ds_method_url ) . '">
				<input type="hidden" name="threeDSMethodData" value="' . esc_html( $json_datos_3dsecure_codificad ) . '" />
				<input name="submit_3ds" type="submit" class="button-alt" id="submit_redsys_3ds_method" value="' . esc_html__( 'Press here if you are not redirected', 'woocommerce-redsys' ) . '" />
			</form>';
	}
	/**
	 * Send push notifications
	 *
	 * @param string $type Type of notification.
	 */
	public function send_push( $type ) {

		if ( 'error' === $type || ! $type ) {
			return true;
		}
		return false;
	}
	/**
	 * Push notifications
	 *
	 * @param string $message Message.
	 * @param string $type Type of notification.
	 */
	public function push( $message, $type = false ) {

		$send = $this->send_push( $type );
		if ( $send ) {
			$push = new Redsys_Push_Notifications();
			$push->call( $message );
		}
	}
	/**
	 * Get Order Edit URL
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_order_edit_url( $order_id ) {
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$url = get_admin_url() . 'admin.php?page=wc-orders&id=' . esc_attr( $order_id ) . '&amp;action=edit';
		} else {
			$url = get_admin_url() . 'post.php?post=' . esc_attr( $order_id ) . '&amp;action=edit';
		}
		return $url;
	}
	/**
	 * Get Post Edit URL
	 *
	 * @param int $order_id Order ID.
	 */
	public function get_post_edit_url( $order_id ) {
		_doing_it_wrong( 'WCRed()->get_post_edit_url( $order_id )', 'Deprecated function, use WCRed()->get_order_edit_url( $order_id )', '19.0.0' );
		$url = esc_url( $this->get_order_edit_url( $order_id ) );
		return $url;
	}
	/**
	 * Get las 4 numbers of a card
	 *
	 * @param string $number Card number.
	 * @param string $number2 Card number 2.
	 *
	 * @return string
	 */
	public function get_last_four( $number = false, $number2 = false ) {

		if ( ( ! $number2 && ! $number ) || ( '' === $number && '' === $number2 ) ) {
			$dscardnumber4 = '0000';
		} else {
			if ( $number2 ) {
				$dscardnumber4 = substr( $number2, -4 );
			} else {
				$dscardnumber4 = substr( $number, -4 );
			}
		}
		return $dscardnumber4;
	}
	/**
	 * Add subscription note
	 *
	 * @param string $text Text.
	 * @param int    $order_id Order ID.
	 *
	 * @return bool
	 */
	public function add_subscription_note( $text, $order_id ) {

		$subscriptions = wcs_get_subscriptions_for_order( $order_id, array( 'order_type' => 'any' ) );
		if ( $subscriptions ) {
			foreach ( $subscriptions as $subscription_id => $subscription_obj ) {
				$subscription_obj->add_order_note( $text );
			}
			return true;
		}
		return false;
	}
	/**
	 * Check if the product key is valid
	 *
	 * @return bool
	 */
	public function check_product_key() {

		if ( ! REDSYS_CHECK_WOO_CONNECTION ) {
			return true;
		}
		if ( is_multisite() ) {
			switch_to_blog( REDSYS_LICENSE_SITE_ID );
		}
		$license = get_transient( 'redsys_license_active' );

		if ( 'yes' === $license ) {
			if ( is_multisite() ) {
				restore_current_blog();
			}
			return true;
		}

		if ( ! class_exists( 'WC_Helper' ) ) {
			if ( is_multisite() ) {
				restore_current_blog();
			}
			return;
		}
		$woocommerce_account_auth      = WC_Helper_Options::get( 'auth' );
		$woocommerce_account_connected = ! empty( $woocommerce_account_auth );

		if ( ! $woocommerce_account_connected ) {
			if ( is_multisite() ) {
				restore_current_blog();
			}
			return false;
		}

		$woocommerce_account_subscriptions = WC_Helper::get_subscriptions();
		$site_id                           = absint( $woocommerce_account_auth['site_id'] );
		$has_active_product_key            = false;

		foreach ( $woocommerce_account_subscriptions as $subscription ) {
			if ( isset( $subscription['product_id'] ) && REDSYS_PRODUCT_ID_WOO === $subscription['product_id'] ) {
				$has_active_product_key = in_array( $site_id, $subscription['connections'], true );
				if ( true === $has_active_product_key ) {
					set_transient( 'redsys_license_active', 'yes', WEEK_IN_SECONDS );
					if ( is_multisite() ) {
						restore_current_blog();
					}
					return true;
				}
			}
		}
		if ( is_multisite() ) {
			restore_current_blog();
		}
		return false;
	}
	/**
	 * Check if the card needs preauth.
	 *
	 * @return bool
	 */
	public function cart_has_preauth() {

		if ( did_action( 'wp_loaded' ) && isset( WC()->cart ) ) {
			$contents = WC()->cart->get_cart();
			if ( ! empty( $contents ) ) {

				foreach ( $contents as $item_key => $item ) {
					$product_id = $item['product_id'];
					if ( $this->need_preauth( $product_id ) ) {
						return true;
					}
					continue;
				}
				return false;
			}
		}
		return false;
	}
	/**
	 * Check if the card needs tokenization.
	 *
	 * @return bool
	 */
	public function cart_has_token_r() {

		if ( did_action( 'wp_loaded' ) && isset( WC()->cart ) ) {
			$contents = WC()->cart->get_cart();
			if ( ! empty( $contents ) ) {

				foreach ( $contents as $item_key => $item ) {
					$product_id = $item['product_id'];
					if ( $this->need_token_r( $product_id ) ) {
						return true;
					}
					continue;
				}
				return false;
			}
		}
		return false;
	}
	/**
	 * Disable gateways that don't support preauth.
	 *
	 * @param array $available_gateways Available gateways.
	 *
	 * @return bool
	 */
	public function disable_gateways_preauth( $available_gateways ) {

		if ( $this->cart_has_preauth() ) {
			foreach ( $available_gateways as $gateway_id => $gateway ) {

				$supports_preauth = $gateway->supports( 'redsys_preauth' );
				$support_filter   = apply_filters( 'redsys_allow_preauth', $gateway_id, false );

				if ( ! $supports_preauth && ! $support_filter ) {
					unset( $available_gateways[ $gateway_id ] );
				}
			}
		}
		return $available_gateways;
	}
	/**
	 * Disable gateways that don't support tokenization.
	 *
	 * @param array $available_gateways Available gateways.
	 *
	 * @return bool
	 */
	public function disable_gateways_token_r( $available_gateways ) {

		if ( $this->cart_has_token_r() ) {
			foreach ( $available_gateways as $gateway_id => $gateway ) {

				$supports_token_r = $gateway->supports( 'redsys_token_r' );
				$support_filter   = apply_filters( 'redsys_allow_token_r', $gateway_id, false );

				if ( ! $supports_token_r && ! $support_filter ) {
					unset( $available_gateways[ $gateway_id ] );
				}
			}
		}
		return $available_gateways;
	}
	/**
	 * Get order item.
	 *
	 * @param int      $item_id Item id.
	 * @param WC_Order $order Order.
	 * @return array
	 * @throws InvalidArgumentException Invalid data.
	 */
	public function get_order_item( $item_id, $order ) {

		$item = array();

		if ( ! is_a( $order, 'WC_Abstract_Order' ) ) {
			throw new InvalidArgumentException( __( 'Invalid data. No valid subscription / order was passed in.', 'woocommerce-redsys' ), 422 );
		}

		if ( ! absint( $item_id ) ) {
			throw new InvalidArgumentException( __( 'Invalid data. No valid item id was passed in.', 'woocommerce-redsys' ), 422 );
		}

		foreach ( $order->get_items() as $line_item_id => $line_item ) {
			if ( $item_id === $line_item_id ) {
				$item = $line_item;
				break;
			}
		}

		return $item;
	}
	/**
	 * Get canonical product id.
	 *
	 * @param WC_Product|WC_Order_Item $item_or_product Item or product.
	 */
	public function get_canonical_product_id( $item_or_product ) {

		if ( is_a( $item_or_product, 'WC_Product' ) ) {
			$product_id = $item_or_product->get_id();
		} elseif ( is_a( $item_or_product, 'WC_Order_Item' ) ) {
			$product_id = ( $item_or_product->get_variation_id() ) ? $item_or_product->get_variation_id() : $item_or_product->get_product_id();
		} else {
			$product_id = ( ! empty( $item_or_product['variation_id'] ) ) ? $item_or_product['variation_id'] : $item_or_product['product_id'];
		}

		return $product_id;
	}
	/**
	 * Print overlay image.
	 */
	public function print_overlay_image() {

		$image = REDSYS_PLUGIN_URL_P . 'assets/images/loader-2.gif';
		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>Untitled Document</title>
				<style type="text/css">
					body {
						background: #EAB897;
						opacity: 0.6;
					}
					section {
					  width: 100%;
					  /*border: 1px solid #2d2d2d;*/
					  display: flex;
					  justify-content: center;
					  align-items: center;
					}
					#redsysover1 {
						color: #fff;
						padding: 12px;
						display: inline-block;
					}
					h2 {
						color: #fff;
						text-align: center;
						font-size: xx-large;
					}
				</style>
			</head>
			<body>
				<section>
					<div id="redsysover1">
						<img src="' . esc_url( $image ) . '" alt="" width="250" height="250" />
						<h2>Processing payment</h2>
					</div>
				</section>
			</body>
		</html>';
	}
}
