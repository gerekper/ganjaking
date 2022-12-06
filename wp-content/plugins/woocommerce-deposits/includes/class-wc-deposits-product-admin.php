<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Deposits plan product admin
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Plans_Product_Admin class.
 */
class WC_Deposits_Plans_Product_Admin {

	/**
	 * Class instance
	 *
	 * @var WC_Deposits_Plans_Product_Admin
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_data' ), 20 );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'deposit_panels' ) );

		// Import/Export support for _wc_deposit_payment_plans meta field.
		add_filter( 'woocommerce_product_export_meta_value', array( $this, 'format_deposit_payment_plans_export' ), 10, 4 );
		add_filter( 'woocommerce_product_import_process_item_data', array( $this, 'format_deposit_payment_plans_import' ) );
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
		include 'views/html-deposits-tab.php';
	}

	/**
	 * Show the deposits panel.
	 */
	public function deposit_panels() {
		wp_enqueue_script( 'woocommerce-deposits-admin' );
		include 'views/html-deposit-data.php';
	}

	/**
	 * Save data.
	 *
	 * @param int $post_id Post ID.
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
			$value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : ''; // phpcs:ignore WordPress.Security -- Conditional sanitize, see below
			switch ( $sanitize ) {
				case 'int':
					$value = $value ? ( is_array( $value ) ? array_map( 'absint', $value ) : absint( $value ) ) : '';
					break;
				case 'float':
					$value = $value ? ( is_array( $value ) ? array_map( 'floatval', $value ) : floatval( $value ) ) : '';
					break;
				case 'yesno':
					$value = 'yes' === $value ? 'yes' : 'no';
					break;
				case 'issetyesno':
					$value = $value ? 'yes' : 'no';
					break;
				default:
					$value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
			}
			WC_Deposits_Product_Meta::update_meta( $post_id, $meta_key, $value );
		}
	}

	/**
	 * Unserialize _wc_deposit_payment_plans value for export
	 *
	 * @since 1.5.9
	 * @param string     $value   Meta Value.
	 * @param mixed      $meta    Meta Object.
	 * @param WC_Product $product Product being exported.
	 * @param array      $row     Row data.
	 * @return string $value
	 */
	public function format_deposit_payment_plans_export( $value, $meta, $product, $row ) {
		if ( '_wc_deposit_payment_plans' === $meta->key ) {
			$plans = maybe_unserialize( $value );
			if ( is_array( $plans ) ) {
				return implode( ',', $plans );
			}
		}
		return $value;
	}

	/**
	 * Serialize _wc_deposit_payment_plans value for import
	 *
	 * @since 1.5.9
	 * @param  array $data Raw CSV data.
	 * @return array $data
	 */
	public function format_deposit_payment_plans_import( $data ) {
		if ( ! empty( $data['meta_data'] ) ) {
			foreach ( $data['meta_data'] as $index => $meta ) {
				if ( '_wc_deposit_payment_plans' === $meta['key'] && ! empty( $meta['value'] ) ) {
					$value = explode( ',', $meta['value'] );
					if ( ! empty( $value ) ) {
						$data['meta_data'][ $index ]['value'] = array_map( 'absint', $value );
					}
				}
			}
		}
		return $data;
	}
}

WC_Deposits_Plans_Product_Admin::get_instance();
