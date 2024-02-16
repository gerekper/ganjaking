<?php
/**
 * Personalizaciones botones checkout
 *
 * @package WooCommerce Redsys Gateway
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkout buton text
 *
 * @param string $order_button_text Button text.
 */
function redsys_chekout_button_text( $order_button_text ) {

	if ( function_exists( 'WC' ) && isset( WC()->session ) && method_exists( WC()->session, 'get' ) ) {

		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( 'redsys' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'redsys' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( 'insite' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttontext', 'insite' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( 'masterpass' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'masterpass' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( 'redsysbank' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'redsysbank' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( 'bizumredsys' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'bizumredsys' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( 'redsysdirectdebit' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'redsysdirectdebit' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( 'paygold' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'paygold' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} elseif ( 'bizumcheckout' === $chosen_payment_method ) {
			$text = WCRed()->get_redsys_option( 'buttoncheckout', 'bizumcheckout' );
			if ( ! empty( $text ) ) {
				$order_button_text = $text;
			} else {
				$order_button_text = $order_button_text;
			}
		} else {
			$order_button_text = $order_button_text;
		}
	}
	return $order_button_text;
}
add_filter( 'woocommerce_order_button_text', 'redsys_chekout_button_text' );

/**
 * Button color text
 *
 * @param string $html Button text.
 */
function redsys_color_button_text( $html ) {

	if ( function_exists( 'WC' ) && isset( WC()->session ) && method_exists( WC()->session, 'get' ) ) {

		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( 'redsys' === $chosen_payment_method ) {
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

			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'insite' === $chosen_payment_method ) {
			$textb = WCRed()->get_redsys_option( 'colorbutton', 'insite' );
			$text  = WCRed()->get_redsys_option( 'colortextbutton', 'insite' );

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

			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . ' ' . $text . 'display:none; visibility: hidden;" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'masterpass' === $chosen_payment_method ) {
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

			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'redsysbank' === $chosen_payment_method ) {
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

			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'bizumredsys' === $chosen_payment_method ) {
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

			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'paygold' === $chosen_payment_method ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'paygold' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'paygold' );

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

			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'redsysdirectdebit' === $chosen_payment_method ) {
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
			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'bizumcheckout' === $chosen_payment_method ) {
			$textb = WCRed()->get_redsys_option( 'butonbgcolor', 'bizumcheckout' );
			$text  = WCRed()->get_redsys_option( 'butontextcolor', 'bizumcheckout' );

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
			if ( '' !== $textb || '' !== $text ) {
				$cssbutton = 'style="' . $textb . '' . $text . '" ';
				$html      = str_replace( '<button type="submit"', '<button type="submit" ' . $cssbutton, $html );
			}
		} elseif ( 'googlepayredsys' === $chosen_payment_method ) {
			$html = str_replace( '<button type="submit"', '<button type="submit" style="display:none; visibility:hidden;"', $html );
		} elseif ( 'applepayredsys' === $chosen_payment_method ) {
			$html = str_replace( '<button type="submit"', '<button type="submit" style="display:none; visibility:hidden;"', $html );
		} else {
			$html = $html;
		}
	}
	return $html;
}
add_filter( 'woocommerce_order_button_html', 'redsys_color_button_text', 0 );
