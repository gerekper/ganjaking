<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the status reports for the WooCommerce pages
 * to verify if they exist for alls languages.
 *
 * @since 1.3
 */
class PLLWC_Admin_Status_Reports {

	/**
	 * Reference to PLL_Model object.
	 *
	 * @var PLL_Model
	 */
	protected $model;

	/**
	 * List of controls on default WooCommerce pages.
	 *
	 * @var stdClass|null
	 */
	protected $woocommerce_pages_status = null;

	/**
	 * Retrieves the status of the WooCommerce pages.
	 *
	 * @since 1.3
	 *
	 * @return stdClass
	 */
	public function get_woocommerce_pages_status() {
		if ( ! empty( $this->woocommerce_pages_status ) ) {
			return $this->woocommerce_pages_status;
		}

		$this->woocommerce_pages_status = new stdClass();
		$this->woocommerce_pages_status->is_error = false;
		$this->woocommerce_pages_status->pages = array();

		$check_pages = array(
			_x( 'Shop base', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_shop_page_id',
				'shortcode' => '',
				'help'      => __( 'The status of your WooCommerce shop\'s homepage translations.', 'polylang-wc' ),
			),
			_x( 'Cart', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_cart_page_id',
				'shortcode' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']',
				'help'      => __( 'The status of your WooCommerce shop\'s cart translations.', 'polylang-wc' ),
			),
			_x( 'Checkout', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_checkout_page_id',
				'shortcode' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']',
				'help'      => __( 'The status of your WooCommerce shop\'s checkout page translations.', 'polylang-wc' ),
			),
			_x( 'My account', 'Page setting', 'polylang-wc' ) => array(
				'option'    => 'woocommerce_myaccount_page_id',
				'shortcode' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']',
				'help'      => __( 'The status of your WooCommerce shop\'s “My Account” page translations.', 'polylang-wc' ),
			),
		);

		$languages = pll_languages_list();

		$pages = array();
		foreach ( $check_pages as $page_name => $values ) {
			$page_id = get_option( $values['option'] );
			$page_id = is_numeric( $page_id ) ? (int) $page_id : 0;

			$page_properties = array();

			$page_properties['is_error'] = false;

			$page_properties['page_id'] = $page_id;
			$page_properties['page_name'] = $page_name;
			$page_properties['help'] = $values['help'];

			if ( ! $page_id ) {
				$page_properties['error_message'] = __( 'Page not set', 'polylang-wc' );
				$page_properties['is_error'] = true;
			} else {
				$translations = pll_get_post_translations( $page_id );

				$missing = array_diff( $languages, array_keys( $translations ) );

				// Do translations exist?
				if ( $missing ) {
					foreach ( $missing as $key => $slug ) {
						$missing[ $key ] = PLL()->model->get_language( $slug )->name;
					}
					$page_properties['error_message'] = sprintf(
						/* translators: %s comma separated list of native languages names */
						_n( 'Missing translation: %s', 'Missing translations: %s', count( $missing ), 'polylang-wc' ),
						implode( ', ', $missing )
					);
					$page_properties['is_error'] = true;
				}

				// Do translations have the correct shortcode?
				elseif ( $values['shortcode'] ) {
					$wrong_translations = array();
					foreach ( $translations as $lang => $translation ) {
						$_page = get_post( $translation );
						if ( ! empty( $_page ) && ! strstr( $_page->post_content, $values['shortcode'] ) ) {
							$wrong_translations[] = PLL()->model->get_language( $lang )->name;
						}
					}

					if ( $wrong_translations ) {
						$page_properties['error_message'] = sprintf(
							/* translators: %s comma separated list of native languages names */
							_n( 'The shortcode is missing for the translation in %s', 'The shortcode is missing for the translations in %s', count( $wrong_translations ), 'polylang-wc' ),
							implode( ', ', $wrong_translations )
						);
						$page_properties['is_error'] = true;
					}
				}
			}

			$pages[ $page_name ] = (object) $page_properties;
			if ( $pages[ $page_name ]->is_error ) {
				$this->woocommerce_pages_status->is_error = $pages[ $page_name ]->is_error;
			}
		}

		$this->woocommerce_pages_status->pages = $pages;

		return $this->woocommerce_pages_status;
	}

	/**
	 * Loads the status report for the translations of the default pages in the WooCommerce status page.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function status_report() {
		include __DIR__ . '/view-status-report.php';
	}

	/**
	 * Loads the status report for the translations of the default pages in our wizard.
	 *
	 * @since 1.3
	 *
	 * @return void
	 */
	public function wizard_status_report() {
		include __DIR__ . '/view-wizard-status-report.php';
	}
}
