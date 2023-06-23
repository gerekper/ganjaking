<?php
/**
 * Redsys Sequential Invoice Numbers
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
/**
 * Copyright: (C) 2013 - 2023 José Conti
 */
class WC_Settings_Tab_Redsys_Sort_Invoices {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_settings_tab_redsys_invoices', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_settings_tab_redsys_invoices', __CLASS__ . '::update_settings' );
	}


	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['settings_tab_redsys_invoices'] = __( 'Sequential Invoice Numbers', 'woocommerce-redsys' );
		return $settings_tabs;
	}


	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function get_settings() {

		$settings = array(
			'title'                  => array(
				'name' => esc_html__( 'Sequential Invoice Numbers', 'woocommerce-redsys' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'wc_settings_tab_redsys_sort_invoices_title',
			),
			'invoices_is_active'     => array(
				'title'   => esc_html__( 'Activate Sequential Invoice numbers', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Activate Sequential Invoice numbers.', 'woocommerce-redsys' ),
				'default' => 'no',
				'desc'    => sprintf( esc_html__( 'Activate Sequential Invoice numbers', 'woocommerce-redsys' ) ),
				'id'      => 'wc_settings_tab_redsys_sort_invoices_is_active',
			),
			'first_invoice_number'   => array(
				'name' => esc_html__( 'First Invoice Number', 'woocommerce-redsys' ),
				'type' => 'text',
				'desc' => esc_html__( 'Add here the first invoice number. By Default is number 1. Save this number before activate it. Example 345 ', 'woocommerce-redsys' ),
				'id'   => 'wc_settings_tab_redsys_sort_invoices_first_invoice_number',
			),
			'length_invoice_number'  => array(
				'name' => esc_html__( 'Invoice Number Length', 'woocommerce-redsys' ),
				'type' => 'text',
				'desc' => esc_html__( 'The Invoice number length, this is not required. Example 10, the result will be 0000000345', 'woocommerce-redsys' ),
				'id'   => 'wc_settings_tab_redsys_sort_invoices_length_invoice_number',
			),
			'prefix_invoice_number'  => array(
				'name' => esc_html__( 'Prefix Invoice Number', 'woocommerce-redsys' ),
				'type' => 'text',
				'desc' => sprintf( esc_html__( 'Add here a prefix invoice number, this is not required. Example WC-, the result will be WC-0000000345. Pattern are allowed ex. {Y} this will add the current year. You will find all patterns %1$sshere%2$s.', 'woocommerce-redsys' ), '<a href="https://docs.woocommerce.com/document/redsys-servired-sermepa-gateway/" target="_blank">', '</a>' ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				'id'   => 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number',
			),
			'postfix_invoice_number' => array(
				'name' => __( 'Postfix Invoice Number', 'woocommerce-redsys' ),
				'type' => 'text',
				'desc' => sprintf( esc_html__( 'Add here a postfix invoice number, this is not required. Example -2015 the result will be WC-0000000345-2015. Pattern are allowed ex. {Y} this will add the current year. You will find all patterns %1$sshere%2$s.', 'woocommerce-redsys' ), '<a href="https://docs.woocommerce.com/document/redsys-servired-sermepa-gateway/" target="_blank">', '</a>' ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				'id'   => 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number',
			),
			'reset_invoice_number'   => array(
				'title'   => esc_html__( 'Reset Invoice Number', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Reset Invoice Number.', 'woocommerce-redsys' ),
				'default' => 'no',
				'desc'    => esc_html__( 'If you enable Reset Invoice Number, every January 1st the invoice number will be reset and will start again with number 1. Is very important that if you enable this option, you use a prefix or postfix year pattern {Y}.', 'woocommerce-redsys' ),
				'id'      => 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number',
			),
			'redsys_section_end'     => array(
				'type' => 'sectionend',
				'id'   => 'wc_settings_tab_redsys_sort_invoices_section_end',
			),
		);
		return apply_filters( 'wc_settings_tab_redsys_sort_invoices_settings', $settings );
	}
}

WC_Settings_Tab_Redsys_Sort_Invoices::init();

if ( 'yes' === get_option( 'wc_settings_tab_redsys_sort_invoices_is_active' ) ) {
	add_filter( 'manage_edit-shop_order_columns', 'redsys_add_invoice_number' );
	add_action( 'manage_shop_order_posts_custom_column', 'redsys_add_invoice_number_value', 2 );
	add_filter( 'manage_edit-shop_order_sortable_columns', 'redsys_add_invoice_number_sortable_colum' );
	// add_action(   'woocommerce_email_before_order_table', 'redsys_add_invoice_number_to_customer_email' );
	add_action( 'woocommerce_payment_complete', 'redsys_sort_invoice_orders' );
	add_action( 'woocommerce_order_status_processing', 'redsys_sort_invoice_orders_admin' );
	add_action( 'woocommerce_order_status_completed', 'redsys_sort_invoice_orders_admin' );
	if ( ! is_admin() ) {
		// add_filter( 'woocommerce_order_number', 'redsys_show_invoice_number', 10, 2 );
	}
}
/**
 * Add invoice number to the order list.
 *
 * @param array $columns Add Invocien Number to the order list.
 */
function redsys_add_invoice_number( $columns ) {

	$new_column = ( is_array( $columns ) ) ? $columns : array();
	unset( $new_column['wc_actions'] );

	// edit this for you column(s)
	// all of your columns will be added before the actions colums.
	$new_column['invoice_number'] = __( 'Invoice Number', 'woocommerce-redsys' );

	// stop editing.
	$new_column['wc_actions'] = $columns['wc_actions'];
	return $new_column;
}

/**
 * Add invoice number to the order list.
 *
 * @param array $column column.
 */
function redsys_add_invoice_number_value( $column ) {
	global $post;

	$invoice_number = WCRed()->get_order_meta( $post->ID, '_invoice_order_redsys', true );

	if ( 'invoice_number' === $column ) {
		echo ( ! empty( $invoice_number ) ? esc_html( $invoice_number ) : esc_html__( 'No invoice n&#176;', 'woocommerce-redsys' ) );
	}
}

/**
 * Sort by invoice number.
 *
 * @param array $columns columns.
 */
function redsys_add_invoice_number_sortable_colum( $columns ) {

	$custom = array(
		'invoice_number' => '_invoice_order_redsys',
	);
	return wp_parse_args( $custom, $columns );
}

/**
 * Add invoice number to customer email.
 *
 * @param int $order_id order.
 */
function redsys_sort_invoice_orders( $order_id ) {

	$reset_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number' );
	if ( 'yes' === $reset_invoice_number ) {
		redsys_check_current_year();
	}

	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$before_prefix_invoice_number  = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number' );
	$before_postfix_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number' );
	$length_invoice_number         = get_option( 'wc_settings_tab_redsys_sort_invoices_length_invoice_number' );
	$prefix_invoice_number         = redsys_use_patterns( $before_prefix_invoice_number );
	$postfix_invoice_number        = redsys_use_patterns( $before_postfix_invoice_number );
	$get_invoice_if_exist          = WCRed()->get_order_meta( $order_id, '_invoice_order_redsys', true );

	if ( empty( $get_invoice_if_exist ) ) {
		if ( ! empty( $last_invoice_number ) ) {
			settype( $last_invoice_number, 'integer' );
		}
		if ( empty( $last_invoice_number ) ) {
			// Check if there is a option with the first invoice number.
			$first_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number' );
			if ( empty( $first_invoice_number ) ) {
				$invoice_number = 1;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			} else {
				settype( $first_invoice_number, 'integer' );
				$invoice_number = $first_invoice_number;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			}
		} else {
			$invoice_number = ++$last_invoice_number;
			update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
		}
		if ( ! empty( $length_invoice_number ) && ( strlen( $invoice_number ) < $length_invoice_number ) ) {
			$invoice_number_long = str_pad( $invoice_number, $length_invoice_number, '0', STR_PAD_LEFT );
		} else {
			$invoice_number_long = $invoice_number;
		}
		$final_invoice_number = $prefix_invoice_number . $invoice_number_long . $postfix_invoice_number;
		WCRed()->update_order_meta( $order_id, '_invoice_order_redsys', $final_invoice_number );
	}
}

/**
 * Add invoice number to customer email.
 *
 * @param int $order_id order id.
 */
function redsys_sort_invoice_orders_admin( $order_id ) {

	$reset_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number' );
	if ( 'yes' === $reset_invoice_number ) {
		redsys_check_current_year();
	}

	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$before_prefix_invoice_number  = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number' );
	$before_postfix_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number' );
	$length_invoice_number         = get_option( 'wc_settings_tab_redsys_sort_invoices_length_invoice_number' );
	$prefix_invoice_number         = redsys_use_patterns( $before_prefix_invoice_number );
	$postfix_invoice_number        = redsys_use_patterns( $before_postfix_invoice_number );
	$get_invoice_if_exist          = WCRed()->get_order_meta( $order_id, '_invoice_order_redsys', true );

	if ( empty( $get_invoice_if_exist ) ) {
		if ( ! empty( $last_invoice_number ) ) {
			settype( $last_invoice_number, 'integer' );
		}
		if ( empty( $last_invoice_number ) ) {
			// Check if there is a option with the first invoice number.
			$first_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number' );
			if ( empty( $first_invoice_number ) ) {
				$invoice_number = 1;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			} else {
				settype( $first_invoice_number, 'integer' );
				$invoice_number = $first_invoice_number;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			}
		} else {
			$invoice_number = ++$last_invoice_number;
			update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
		}
		if ( ! empty( $length_invoice_number ) && ( strlen( $invoice_number ) < $length_invoice_number ) ) {
			$invoice_number_long = str_pad( $invoice_number, $length_invoice_number, '0', STR_PAD_LEFT );
		} else {
			$invoice_number_long = $invoice_number;
		}
		$final_invoice_number = $prefix_invoice_number . $invoice_number_long . $postfix_invoice_number;
		WCRed()->update_order_meta( $order_id, '_invoice_order_redsys', $final_invoice_number );
	}
}
// We hook to WooCommerce payment function.

/**
 * Customer_email_invoice_number.
 *
 * @param int $order Order ID.
 */
function redsys_add_invoice_number_to_customer_email( $order ) {

	$invoice_number = redsys_check_add_invoice_number( $order );
	if ( empty( $invoice_number ) ) {
		printf( esc_html__( 'Order Number: %s', 'woocommerce-redsys' ), esc_html( $order ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	} else {
		echo '<h2>';
		printf( esc_html__( 'Invoice Number: %s', 'woocommerce-redsys' ), esc_html( $invoice_number ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		echo '</h2>';
	}
}

/**
 * Customer_email_invoice_number.
 *
 * @param int $order Order ID.
 */
function redsys_check_add_invoice_number( $order ) {
	global $woocommerce, $post;

	$reset_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number' );
	if ( 'yes' === $reset_invoice_number ) {
		redsys_check_current_year();
	}
	$get_invoice_if_exist          = WCRed()->get_order_meta( $order, '_invoice_order_redsys', true );
	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$before_prefix_invoice_number  = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number' );
	$before_postfix_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number' );
	$length_invoice_number         = get_option( 'wc_settings_tab_redsys_sort_invoices_length_invoice_number' );
	$prefix_invoice_number         = redsys_use_patterns( $before_prefix_invoice_number );
	$postfix_invoice_number        = redsys_use_patterns( $before_postfix_invoice_number );

	if ( ! empty( $last_invoice_number ) ) {
		settype( $last_invoice_number, 'integer' );
	}

	if ( empty( $last_invoice_number ) ) {
		// Check if there is a option with the first invoice number.
		$first_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number' );
		if ( empty( $first_invoice_number ) ) {
			$invoice_number = 1;
		} else {
			settype( $first_invoice_number, 'integer' );
			$invoice_number = $first_invoice_number;
		}
	} else {
		$invoice_number = $last_invoice_number;
	}
	if ( ! empty( $length_invoice_number ) && ( strlen( $invoice_number ) < $length_invoice_number ) ) {
		$invoice_number_long = str_pad( $invoice_number, $length_invoice_number, '0', STR_PAD_LEFT );
	} else {
		$invoice_number_long = $invoice_number;
	}
	$final_invoice_number = $prefix_invoice_number . $invoice_number_long . $postfix_invoice_number;
	return $final_invoice_number;
}

/**
 * Customer_email_invoice_number.
 *
 * @param int $oldnumber Numer.
 * @param int $order Order ID.
 */
function redsys_show_invoice_number( $oldnumber, $order ) {
	$preorderprefix = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_order_number' );
	$preordersufix  = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_order_number' );
	$orderprefix    = redsys_use_patterns( $preorderprefix );
	$ordersufix     = redsys_use_patterns( $preordersufix );

	if ( empty( $ordersufix ) && empty( $orderprefix ) ) {
			$ordersufix = __( '-ORDER', 'woocommerce-redsys' );
	}

	$order = WCRed()->get_order_meta( $oldnumber, '_invoice_order_redsys', true );
	if ( empty( $order ) ) {
		$order = $orderprefix . $oldnumber . $ordersufix;
	}
	if ( is_checkout() ) {
		$order = $oldnumber;
	}
	return $order;
}

/**
 * Invoice Pattern.
 *
 * @param string $string String.
 */
function redsys_use_patterns( $string ) {
	$numericzero                   = preg_replace( '/(\{d\})/', date_i18n( 'd' ), $string );
	$numeric                       = preg_replace( '/(\{j\})/', date_i18n( 'j' ), $numericzero );
	$english_suffix                = preg_replace( '/(\{S\})/', date_i18n( 'S' ), $numeric );
	$full_name                     = preg_replace( '/(\{l\})/', date_i18n( 'l' ), $english_suffix );
	$three_letter                  = preg_replace( '/(\{D\})/', date_i18n( 'D' ), $full_name );
	$month_numericzero             = preg_replace( '/(\{m\})/', date_i18n( 'm' ), $three_letter );
	$month_numeric                 = preg_replace( '/(\{n\})/', date_i18n( 'n' ), $month_numericzero );
	$textual_full                  = preg_replace( '/(\{F\})/', date_i18n( 'F' ), $month_numeric );
	$textual_three                 = preg_replace( '/(\{M\})/', date_i18n( 'M' ), $textual_full );
	$year_numeric_four             = preg_replace( '/(\{Y\})/', date_i18n( 'Y' ), $textual_three );
	$year_numeric_two              = preg_replace( '/(\{y\})/', date_i18n( 'y' ), $year_numeric_four );
	$time_lowercase                = preg_replace( '/(\{a\})/', date_i18n( 'a' ), $year_numeric_two );
	$time_uppercase                = preg_replace( '/(\{A\})/', date_i18n( 'A' ), $time_lowercase );
	$hour_twelve_without_zero      = preg_replace( '/(\{g\})/', date_i18n( 'g' ), $time_uppercase );
	$hour_twelve_zero              = preg_replace( '/(\{h\})/', date_i18n( 'h' ), $hour_twelve_without_zero );
	$hour_twenty_four_without_zero = preg_replace( '/(\{G\})/', date_i18n( 'G' ), $hour_twelve_zero );
	$hour_twenty_four_zero         = preg_replace( '/(\{H\})/', date_i18n( 'H' ), $hour_twenty_four_without_zero );
	$minutes                       = preg_replace( '/(\{i\})/', date_i18n( 'i' ), $hour_twenty_four_zero );
	$final                         = preg_replace( '/(\{s\})/', date_i18n( 's' ), $minutes );

	return $final;
}

/**
 * Copyright: (C) 2013 - 2023 José Conti
 */
function redsys_check_current_year() {
		$current_year = date_i18n( 'Y' );
		$saved_year   = get_option( 'redsys_saved_year' );
		settype( $saved_year, 'integer' );

	if ( empty( $saved_year ) ) {
		add_option( 'redsys_saved_year', $current_year );
	} else {
		if ( $current_year > $saved_year ) {
			update_option( 'redsys_saved_year', $current_year );
			update_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number', '0' );
			update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', '0' );
		}
	}
}
