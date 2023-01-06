<?php

namespace WCML\Compatibility\WcProductAddons;

use WC_Product_Booking;
use WCML_Product_Addons;
use woocommerce_wpml;
use WPML_Twig_Template_Loader;

class MulticurrencyHooks implements \IWPML_Action {

	const TEMPLATE_FOLDER   = '/templates/compatibility/';
	const DIALOG_TEMPLATE   = 'product-addons-prices-dialog.twig';
	const SETTINGS_TEMPLATE = 'product-addons-prices-settings.twig';
	const PRICE_OPTION_KEY  = '_product_addon_prices';

	/** @var woocommerce_wpml $woocommerce_wpml */
	private $woocommerce_wpml;

	/**
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		add_filter( 'get_product_addons_fields', [ $this, 'product_addons_price_filter' ], 10, 2 );
		add_filter( 'wcml_cart_contents_not_changed', [ $this, 'filter_booking_addon_product_in_cart_contents' ], 20 );
		add_filter( 'wcml_product_addons_global_updated', [ $this, 'onGlobalAddonsUpdated' ], 10, 2 );

		if ( is_admin() ) {
			add_action( 'woocommerce_product_addons_panel_start', [ $this, 'load_dialog_resources' ] );
			add_action( 'woocommerce_product_addons_panel_option_row', [ $this, 'dialog_button_after_option_row' ], 10, 4 );
			add_action( 'woocommerce_product_addons_panel_before_options', [ $this, 'dialog_button_before_options' ], 10, 3 );
			add_action( 'wcml_before_sync_product', [ $this, 'update_custom_prices_values' ] );
			add_action( 'save_post', [ $this, 'maybeUpdateCustomPricesValues' ], 10, 2 );
			add_action( 'woocommerce_product_addons_global_edit_objects', [ $this, 'custom_prices_settings_block' ] );
		}
	}

	public function maybeUpdateCustomPricesValues( $productId, $arg ) {
		if ( 'product' === get_post_type( $productId ) ) {
			$this->update_custom_prices_values( $productId );
		}
	}

	/**
	 * @param array $addons
	 * @param int   $postId
	 *
	 * @return array
	 */
	public function product_addons_price_filter( $addons, $postId ) {
		foreach ( $addons as $addonId => $addon ) {

			$addon_data = wpml_collect( $addon );

			if ( $addon_data->offsetExists( 'price' ) && $addon_data->get( 'price' ) ) {
				$addons[ $addonId ]['price'] = $this->converted_addon_price( $addon, $postId );
			}

			if ( $addon_data->offsetExists( 'options' ) ) {
				foreach ( $addon_data->get( 'options' ) as $key => $option ) {
					$addons[ $addonId ]['options'][ $key ]['price'] = $this->converted_addon_price( $option, $postId );
				}
			}
		}

		return $addons;
	}

	/**
	 * Special case for WC Bookings plugin - need add addon cost after re-calculating booking costs.
	 *
	 * @see https://onthegosystems.myjetbrains.com/youtrack/issue/wcml-1877
	 *
	 * @param array $cartItem
	 *
	 * @return array
	 */
	public function filter_booking_addon_product_in_cart_contents( $cartItem ) {
		$isBookingProductWithAddons = $cartItem['data'] instanceof WC_Product_Booking && isset( $cartItem['addons'] );

		if ( $isBookingProductWithAddons ) {
			$cost = $cartItem['data']->get_price();

			foreach ( $cartItem['addons'] as $addon ) {
				$cost += $addon['price'];
			}

			$cartItem['data']->set_price( $cost );
		}

		return $cartItem;
	}

	/**
	 * @param  array $addon
	 * @param  int   $postId
	 *
	 * @return string
	 */
	private function converted_addon_price( $addon, $postId ) {
		$addonData = wpml_collect( $addon );

		$isCustomPricesOn = $this->isProductCustomPricesOn( $postId );
		$field            = 'price_' . $this->woocommerce_wpml->multi_currency->get_client_currency();

		if (
			$isCustomPricesOn &&
			$addonData->get( $field )
		) {
			return $addonData->get( $field );
		}

		if ( wpml_collect( [ 'flat_fee', 'quantity_based' ] )->contains( $addonData->get( 'price_type' ) ) ) {
			return apply_filters( 'wcml_raw_price_amount', $addonData->get( 'price' ) );
		}

		return $addonData->get( 'price' );
	}

	/**
	 * @param int|false $productId
	 *
	 * @return bool|mixed
	 */
	private function isProductCustomPricesOn( $productId ) {
		if ( $productId ) {
			return get_post_meta( $productId, '_wcml_custom_prices_status', true );
		}

		if ( SharedHooks::isGlobalAddonEditPage() ) {
			return $this->getGlobalAddonPricesStatus();
		}

		return false;
	}

	/**
	 * @return bool|mixed
	 */
	private function getGlobalAddonPricesStatus() {
		if ( isset( $_GET['edit'] ) ) {
			return get_post_meta( $_GET['edit'], '_wcml_custom_prices_status', true );
		} elseif ( isset( $_POST['_wcml_custom_prices'] ) ) {
			return $_POST['_wcml_custom_prices'];
		}

		return false;
	}

	public function load_dialog_resources() {
		wp_enqueue_script( 'wcml-dialogs', WCML_PLUGIN_URL . '/res/js/dialogs' . WCML_JS_MIN . '.js', [ 'jquery-ui-dialog', 'underscore' ], WCML_VERSION );
	}

	/**
	 * @param \WP_Post|null $product
	 * @param array         $productAddons
	 * @param int           $loop
	 * @param array         $option
	 */
	public function dialog_button_after_option_row( $product, $productAddons, $loop, $option ) {
		if ( $option ) {
			$this->renderEditPriceElement( $this->getPricesDialogModel( $productAddons, $option, $loop, $this->isProductCustomPricesOn( $product ? $product->ID : false ) ) );
		}
	}

	/**
	 * @param \WP_Post|null $product
	 * @param array         $productAddons
	 * @param int           $loop
	 */
	public function dialog_button_before_options( $product, $productAddons, $loop ) {
		$this->renderEditPriceElement( $this->getPricesDialogModel( [], $productAddons, $loop, $this->isProductCustomPricesOn( $product ? $product->ID : false ) ) );
	}

	/**
	 * @param int $metaId
	 * @param int $id
	 */
	public function onGlobalAddonsUpdated( $metaId, $id ) {
		$this->update_custom_prices_values( $id );
	}

	/**
	 * @param string|int $productId
	 */
	public function update_custom_prices_values( $productId ) {
		$this->saveGlobalAddonPricesSetting( $productId );
		$productAddons = SharedHooks::getProductAddons( $productId );

		if ( $productAddons ) {
			$activeCurrencies = $this->woocommerce_wpml->multi_currency->get_currencies();

			foreach ( $productAddons as $addonKey => $productAddon ) {

				foreach ( $activeCurrencies as $code => $currency ) {
					$priceOptionKey = self::PRICE_OPTION_KEY;

					if ( in_array( $productAddon['type'], self::getOnePriceTypes(), true ) ) {
						$productAddons = $this->updateSingleOptionPrices( $productAddons, $priceOptionKey, $addonKey, $code );
					} else {
						$productAddons = $this->updateMultipleOptionsPrices( $productAddons, $priceOptionKey, $addonKey, $code );
					}
				}
			}

			update_post_meta( $productId, WCML_Product_Addons::ADDONS_OPTION_KEY, $productAddons );
		}
	}

	/**
	 * @param array  $productAddons
	 * @param string $priceOptionKey
	 * @param string $addonKey
	 * @param string $code
	 *
	 * @return array
	 */
	private function updateSingleOptionPrices( $productAddons, $priceOptionKey, $addonKey, $code ) {
		if ( isset( $_POST[ $priceOptionKey ][ $addonKey ][ 'price_' . $code ][0] ) ) {
			$productAddons[ $addonKey ][ 'price_' . $code ] = wc_format_decimal( $_POST[ $priceOptionKey ][ $addonKey ][ 'price_' . $code ][0] );
		}

		return $productAddons;
	}

	/**
	 * @param array  $productAddons
	 * @param string $priceOptionKey
	 * @param string $addonKey
	 * @param string $code
	 *
	 * @return array
	 */
	private function updateMultipleOptionsPrices( $productAddons, $priceOptionKey, $addonKey, $code ) {
		$addon_data = wpml_collect( $productAddons[ $addonKey ] );

		if ( $addon_data->offsetExists( 'options' ) ) {
			foreach ( $addon_data->get( 'options' ) as $option_key => $option ) {
				if ( isset( $_POST[ $priceOptionKey ][ $addonKey ][ 'price_' . $code ][ $option_key ] ) ) {
					$productAddons[ $addonKey ]['options'][ $option_key ][ 'price_' . $code ] = wc_format_decimal( $_POST[ $priceOptionKey ][ $addonKey ][ 'price_' . $code ][ $option_key ] );
				}
			}
		}

		return $productAddons;
	}

	public function custom_prices_settings_block() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->getTwigLoader()->get_template()->show( $this->getCustomPricesSettingsModel(), self::SETTINGS_TEMPLATE );
	}

	/**
	 * @param array $model
	 */
	private function renderEditPriceElement( $model ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->getTwigLoader()->get_template()->show( $model, self::DIALOG_TEMPLATE );
	}

	/**
	 * @return WPML_Twig_Template_Loader
	 */
	private function getTwigLoader() {
		return new WPML_Twig_Template_Loader( [ WCML_PLUGIN_PATH . SharedHooks::TEMPLATE_FOLDER ] );
	}

	/**
	 * @return array
	 */
	private function getCustomPricesSettingsModel() {
		return [
			'strings'          => [
				'label'    => __( 'Multi-currency settings', 'woocommerce-multilingual' ),
				'auto'     => __( 'Calculate prices in other currencies automatically', 'woocommerce-multilingual' ),
				'manually' => __( 'Set prices in other currencies manually', 'woocommerce-multilingual' ),
			],
			'custom_prices_on' => $this->getGlobalAddonPricesStatus(),
			'nonce'            => wp_create_nonce( 'wcml_save_custom_prices' ),
		];
	}

	/**
	 * @param array       $productAddons
	 * @param array       $option
	 * @param int         $loop
	 * @param string|bool $customPricesOn
	 *
	 * @return array
	 */
	private function getPricesDialogModel( $productAddons, $option, $loop, $customPricesOn ) {

		$label = isset( $option['label'] ) ? $option['label'] : $option['name'];

		return [
			'strings'           => [
				'dialog_title' => __( 'Multi-currency settings', 'woocommerce-multilingual' ),
				/* translators: %s is an option label */
				'description'  => sprintf( __( 'Here you can set different prices for the %s in multiple currencies:', 'woocommerce-multilingual' ), '<strong>' . $label . '</strong>' ),
				'apply'        => __( 'Apply', 'woocommerce-multilingual' ),
				'cancel'       => __( 'Cancel', 'woocommerce-multilingual' ),
			],
			'custom_prices_on'  => $customPricesOn,
			'dialog_id'         => '_product_addon_option_' . md5( uniqid( $loop . $label ) ),
			'option_id'         => isset( $productAddons[ $loop ]['options'] ) ? array_search( $option, $productAddons[ $loop ]['options'] ) : '',
			'addon_id'          => $loop,
			'option_details'    => $option,
			'default_currency'  => wcml_get_woocommerce_currency_option(),
			'active_currencies' => $this->woocommerce_wpml->multi_currency->get_currencies(),
		];
	}

	/**
	 * @param int $productId
	 */
	private function saveGlobalAddonPricesSetting( $productId ) {
		if ( SharedHooks::isGlobalAddon( $productId ) ) {
			$nonce = filter_var( isset( $_POST['_wcml_custom_prices_nonce'] ) ? $_POST['_wcml_custom_prices_nonce'] : '', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			if ( isset( $_POST['_wcml_custom_prices'] ) && isset( $nonce ) && wp_verify_nonce( $nonce, 'wcml_save_custom_prices' ) ) {
				update_post_meta( $productId, '_wcml_custom_prices_status', $_POST['_wcml_custom_prices'] );
			}
		}
	}

	/**
	 * @return array
	 */
	private static function getOnePriceTypes() {
		return [
			'custom_text',
			'custom_textarea',
			'file_upload',
			'input_multiplier',
		];
	}
}
