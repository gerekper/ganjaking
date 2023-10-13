<?php

class WCML_Exchange_Rates_UI extends WCML_Templates_Factory {

	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;
	/**
	 * @var \WCML_Exchange_Rates $exchange_rates_services
	 */
	private $exchange_rates_services;

	/**
	 * WCML_Exchange_Rates_UI constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	public function __construct( $woocommerce_wpml ) {
		// @todo Cover by tests, required for wcml-3037.
		parent::__construct();

		$this->woocommerce_wpml        = $woocommerce_wpml;
		$this->exchange_rates_services = $this->woocommerce_wpml->multi_currency->exchange_rate_services;
	}

	public function get_model() {
		$settings = $this->exchange_rates_services->get_settings();

		$last_updated = empty( $settings['last_updated'] ) ?
							'<i>' . __( 'never', 'woocommerce-multilingual' ) . '</i>' :
							date_i18n( 'F j, Y g:i a', $settings['last_updated'] );

		$model = [
			'strings'              => [

				'header'           => __( 'Automatic Exchange Rates', 'woocommerce-multilingual' ),
				'no_currencies'    => __( "You haven't added any secondary currencies.", 'woocommerce-multilingual' ),
				'enable_automatic' => __( 'Enable automatic exchange rates', 'woocommerce-multilingual' ),
				'services_label'   => __( 'Exchange rates source', 'woocommerce-multilingual' ),
				'lifting_label'    => __( 'Lifting charge', 'woocommerce-multilingual' ),
				'lifting_details1' => __( 'The lifting charge adjusts the exchange rate provided by the selected service before it is saved. The exchange rates displayed in the table above include the lifting charge.', 'woocommerce-multilingual' ),
				/* translators: %s is an exchange rates service */
				'lifting_details2' => __( 'Exchange rate = %s exchange rate x (1 + lifting charge / 100)', 'woocommerce-multilingual' ),
				'services_api'     => __( 'API key (required)', 'woocommerce-multilingual' ),
				'frequency'        => __( 'Update frequency', 'woocommerce-multilingual' ),
				'update'           => __( 'Update manually now', 'woocommerce-multilingual' ),
				'update_tip'       => __( 'You have to save all settings before updating exchange rates', 'woocommerce-multilingual' ),
				'manually'         => __( 'Manually', 'woocommerce-multilingual' ),
				'hourly'           => __( 'Hourly', 'woocommerce-multilingual' ),
				'daily'            => __( 'Daily', 'woocommerce-multilingual' ),
				'weekly'           => __( 'Weekly on', 'woocommerce-multilingual' ),
				'monthly'          => __( 'Monthly on the', 'woocommerce-multilingual' ),
				'key_placeholder'  => __( 'Enter API key', 'woocommerce-multilingual' ),
				'key_required'     => __( 'API key (required)', 'woocommerce-multilingual' ),
				'fixerio_warning'  => __( 'WARNING! Minor limitations include 1000 requests/month limit and EUR being the only available base currency for customers using a free account. If you need more than 1000 requests per month or want to use all 170 available base currencies, you’ll need to choose one of the paid plans starting at only $10 per month.', 'woocommerce-multilingual' ),
				'daily_warning'    => __( 'Updating the exchange rates on an hourly basis generates around 744 API calls a month. Please check that your exchange rates source can accommodate this higher usage.', 'woocommerce-multilingual' ),
				'nonce'            => wp_create_nonce( 'update-exchange-rates' ),
				'updated_time'     => sprintf(
					/* translators: %s is a date and time */
					__( 'Last updated: %s', 'woocommerce-multilingual' ),
					'<span class="time">' . $last_updated . '</span>'
				),
				'updated_success'  => __( 'Exchange rates updated successfully', 'woocommerce-multilingual' ),
				'visit_website'    => __( 'Visit website', 'woocommerce-multilingual' ),

			],

			'services'             => $this->get_services_model(),
			'settings'             => $settings,

			'secondary_currencies' => $this->woocommerce_wpml->multi_currency->get_currencies(),

			'exchange_rates_manually_updated' => (bool) wp_cache_get( WCML_Exchange_Rates::KEY_RATES_UPDATED_FLAG ),
			'has_actionable_service'          => $this->exchange_rates_services->is_current_service_actionable(),
		];

		return $model;
	}

	/**
	 * @return array
	 */
	private function get_services_model() {
		$services = [];

		foreach ( $this->exchange_rates_services->get_services() as $id => $service ) {
			$services[ $id ] = [
				'name'         => $service->getName(),
				'url'          => $service->getUrl(),
				'requires_key' => $service->isKeyRequired(),
				'api_key'      => $service->getSetting( 'api-key' ),
				'last_error'   => $service->getLastError(),
			];
		}

		return $services;
	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/multi-currency/',
		];
	}

	public function get_template() {
		return 'exchange-rates.twig';
	}

}
