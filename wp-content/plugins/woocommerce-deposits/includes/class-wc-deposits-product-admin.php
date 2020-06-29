<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Plans_Product_Admin class.
 */
class WC_Deposits_Plans_Product_Admin {

	/** @var object Class Instance */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self ) : self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
		add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ), 20 );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'deposit_panels' ) );
	}

	/**
	 * Scripts.
	 */
	public function styles_and_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'woocommerce-deposits-admin', WC_DEPOSITS_PLUGIN_URL . '/assets/js/admin' . $suffix . '.js', array( 'jquery' ), WC_DEPOSITS_VERSION, true );
	}

	/**
	 * Show the deposits tab.
	 */
	public function add_tab() {
		include( 'views/html-deposits-tab.php' );
	}

	/**
	 * Show the deposits panel.
	 */
	public function deposit_panels() {
		wp_enqueue_script( 'woocommerce-deposits-admin' );
		include( 'views/html-deposit-data.php' );
	}

	/**
	 * Save data.
	 *
	 * @param int $post_id
	 */
	public function save_product_data( $post_id ) {
		$meta_to_save = array(
			'_wc_deposit_enabled'                          => '',
			'_wc_deposit_type'                             => '',
			'_wc_deposit_amount'                           => 'float',
			'_wc_deposit_payment_plans'                    => 'int',
			'_wc_deposit_selected_type'                    => '',
			'_wc_deposit_multiple_cost_by_booking_persons' => 'issetyesno',
		);
		foreach ( $meta_to_save as $meta_key => $sanitize ) {
			$value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
			switch ( $sanitize ) {
				case 'int' :
					$value = $value ? ( is_array( $value ) ? array_map( 'absint', $value ) : absint( $value ) ) : '';
					break;
				case 'float' :
					$value = $value ? ( is_array( $value ) ? array_map( 'floatval', $value ) : floatval( $value ) ) : '';
					break;
				case 'yesno' :
					$value = $value == 'yes' ? 'yes' : 'no';
					break;
				case 'issetyesno' :
					$value = $value ? 'yes' : 'no';
					break;
				default :
					$value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
			}
			WC_Deposits_Product_Meta::update_meta( $post_id, $meta_key, $value );
		}
	}
}

WC_Deposits_Plans_Product_Admin::get_instance();
