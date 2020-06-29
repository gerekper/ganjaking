<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WAPT_License_Page is used as template to display form to active premium functionality.
 *
 * @since 2.0.7
 */
class WIS_LicensePage extends WIS_Page {

	/**
	 * {@inheritdoc}
	 */
	public $type = "page";

	/**
	 * {@inheritdoc}
	 */
	public $page_menu_dashicon = 'dashicons-admin-network';

	/**
	 * {@inheritdoc}
	 */
	public $show_right_sidebar_in_options = false;

	/**
	 * {@inheritdoc}
	 */
	public $page_menu_position = 0;

	/**
	 * {@inheritdoc}
	 */
	public $available_for_multisite = true;

	/**
	 * @var string Name of the paid plan.
	 */
	public $plan_name;

	// PREMIUM SECTION
	// ------------------------------------------------------------------
	/**
	 * @since 2.0.7
	 * @var bool
	 */
	protected $is_premium;

	/**
	 * @since 2.0.7
	 * @var \WBCR\Factory_423\Premium\Provider
	 */
	protected $premium;

	/**
	 * @since 2.0.7
	 * @var bool
	 */
	protected $is_premium_active;

	/**
	 * @since 2.0.7
	 * @var bool
	 */
	protected $premium_has_subscription;

	/**
	 * @since 2.0.7
	 * @var \WBCR\Factory_423\Premium\Interfaces\License
	 */
	protected $premium_license;

	// END PREMIUM SECTION
	// ------------------------------------------------------------------

	/**
	 * {@inheritdoc}
	 * @param WIS_Plugin $plugin
	 */
	public function __construct ( $plugin ) {
		$this->plugin = $plugin;

		parent::__construct( $plugin );

		$this->id             = 'license';
		$this->menu_title     = '<span style="color:#f18500">'.__( 'License', 'instagram-slider-widget' ).'</span>';
		$this->page_title = __( 'License of Social Slider Widget', 'instagram-slider-widget' );
		$this->template_name = "license";
		$this->menu_target= "widgets-".$plugin->getPluginName();
		$this->capabilitiy = "manage_options";

		$this->premium                  = WIS_Plugin::app()->premium;
		$this->is_premium               = $this->premium->is_activate();
		$this->is_premium_active        = $this->premium->is_active();
		$this->premium_has_subscription = $this->premium->has_paid_subscription();
		$this->premium_license          = $this->premium->get_license();
	}

	/**
	 * [MAGIC] Magic method that configures assets for a page.
	 */
	public function assets ( $scripts, $styles ) {
		parent::assets( $scripts, $styles );

		$this->styles->add( WIS_PLUGIN_URL . '/admin/assets/css/license-manager.css' );

		$this->styles->request( array(
			'bootstrap.core',
			'bootstrap.form-groups',
			'bootstrap.separator',
		), 'bootstrap' );

		$this->scripts->add( WIS_PLUGIN_URL . '/admin/assets/js/license-manager.js' );
	}

	/**
	 * Get before content.
	 *
	 * @return string Before content.
	 */
	protected function get_plan_description () {
		return '';
	}

	/**
	 * @return string
	 */
	protected function get_hidden_license_key () {
		if ( ! $this->is_premium ) {
			return '';
		}

		return $this->premium_license->get_hidden_key();
	}

	/**
	 * @return string
	 */
	protected function get_plan () {
		if ( ! $this->is_premium ) {
			return 'free';
		}

		return $this->premium->get_plan();
	}

	/**
	 * @return mixed
	 */
	protected function get_expiration_days () {
		return $this->premium_license->get_expiration_time( 'days' );
	}

	/**
	 * @return string
	 */
	protected function get_billing_cycle_readable () {
		if ( ! $this->is_premium ) {
			return '';
		}

		$billing_cycle = $this->premium->get_billing_cycle();
		$billing       = 'lifetime';

		if ( 1 == $billing_cycle ) {
			$billing = 'month';
		} else if ( 12 == $billing_cycle ) {
			$billing = 'year';
		}

		return $billing;
	}

	/**
	 * Тип лицензии, цветовое оформление для формы лицензирования
	 * free - бесплатная
	 * gift - пожизненная лицензия, лицензия на особых условиях
	 * trial - красный цвет, применяется для триалов, если лиценизия истекла или заблокирована
	 * paid - обычная оплаченная лицензия, в данный момент активна.
	 *
	 * @return string
	 */
	protected function get_license_type () {
		if ( ! $this->is_premium ) {
			return 'free';
		}

		$license = $this->premium_license;

		if ( $license->is_lifetime() ) {
			return 'gift';
		} else if ( $license->get_expiration_time( 'days' ) < 1 ) {
			return 'trial';
		}

		return 'paid';
	}
}
