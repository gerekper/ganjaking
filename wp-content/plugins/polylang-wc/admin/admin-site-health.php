<?php
/**
 * @package Polylang-WC
 */

/**
 * Class PLLWC_Admin_Site_Health
 *
 * @since 1.5
 */
class PLLWC_Admin_Site_Health {
	/**
	 * PLLWC_Admin_Site_Health constructor.
	 *
	 * @since 1.5
	 */
	public function __construct() {
		add_filter( 'debug_information', array( $this, 'info' ), 20 );
	}

	/**
	 * Add Polylang for WooCommerce informations to the Site Health Informations tab.
	 *
	 * @since 1.5
	 *
	 * @param array $debug_info The debug information to be added to the core information page.
	 * @return array
	 */
	public function info( $debug_info ) {
		$pages_status = Polylang_Woocommerce::instance()->admin_status_reports->get_woocommerce_pages_status();

		$fields = array();
		if ( false === $pages_status->is_error ) {
			$fields['pllwc']['label'] = __( 'WooCommerce pages translations', 'polylang-wc' );
			$fields['pllwc']['value'] = __( 'All WooCommerce pages are translated.', 'polylang-wc' );
		} else {
			foreach ( $pages_status->pages as $page => $value ) {
				if ( true === $value->is_error ) {
					$fields[ $page ]['label'] = $value->page_name;
					$fields[ $page ]['value'] = $value->page_id . ' - ' . $value->error_message;
				}
			}
		}

		$debug_info['polylang-wc'] = array(
			'label'  => 'Polylang for WooCommerce',
			'fields' => $fields,
		);

		return $debug_info;
	}
}



