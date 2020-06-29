<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WooCommerce_Order_Barcodes_Settings {

	/**
	 * The single instance of WooCommerce_Order_Barcodes_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 * @static
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	/**
	 * Constructor
	 * @access public
	 * @since  1.0.0
	 * @param  object $parent Main plugin object
	 */
	public function __construct ( $parent ) {

		// Set main plugin class as parent
		$this->parent = $parent;

		// Initialise settings
		$this->init_settings();

		// Set up settings fields
		add_filter( 'woocommerce_general_settings', array( $this, 'add_settings' ), 10, 1 );
		add_action( 'woocommerce_admin_field_barcode_colors', array( $this, 'colour_settings' ) );
		add_action( 'woocommerce_settings_save_general', array( $this, 'colour_settings_save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );

		// Add settings link to plugins list table
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );

	} // End __construct ()

	/**
	 * Initialise settings
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	} // End init_settings ()

	/**
	 * Build settings fields
	 * @access private
	 * @since  1.0.0
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		// Set up available barcode types.
		$type_options = array(
			'code39'		=> __( 'Code 39', 'woocommerce-order-barcodes' ),
			'code93'		=> __( 'Code 93', 'woocommerce-order-barcodes' ),
			'code128' 		=> __( 'Code 128', 'woocommerce-order-barcodes' ),
			'datamatrix'	=> __( 'Data Matrix', 'woocommerce-order-barcodes' ),
			'qr' 			=> __( 'QR Code', 'woocommerce-order-barcodes' ),
		);

		// Register settings fields.
		$settings = array(
			array( 'title'	=> __( 'Order Barcodes', 'woocommerce-order-barcodes' ), 'type' => 'title', 'desc' => '', 'id' => 'order_barcodes' ),

			array(
				'title' 	=> __( 'Enable Barcodes', 'woocommerce-order-barcodes' ),
				'desc' 		=> __( 'This will enable unique barcode generation for each order.', 'woocommerce-order-barcodes' ),
				'id' 		=> 'wc_order_barcodes_enable',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'class' 	=> 'checkbox',
				'desc_tip'	=> true,
			),

			array(
				'title' 	=> __( 'Barcode Type', 'woocommerce-order-barcodes' ),
				'desc' 		=> __( 'This is the type of barcode that will be generated for your orders - changing this will only affect future orders.', 'woocommerce-order-barcodes' ),
				'id' 		=> 'wc_order_barcodes_type',
				'css' 		=> 'min-width:350px;',
				'default'	=> 'code128',
				'type' 		=> 'select',
				'class' 	=> 'wc-enhanced-select',
				'desc_tip'	=> true,
				'options'	=> $type_options,
			),

			array( 'type'	=> 'barcode_colors' ),

			array( 'type'	=> 'sectionend', 'id' => 'order_barcodes' ),
		);

		// Allow settings to be filtered
		$settings = apply_filters( 'wc_order_barcodes_settings_fields', $settings );

		return $settings;

	} // End settings_fields ()

	/**
	 * Markup for colour settings
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function colour_settings () {
		?><tr valign="top" class="wc_order_barcodes_colours">
			<th scope="row" class="titledesc">
				<?php _e( 'Barcode Colors', 'woocommerce' ); ?>
			</th>
		    <td class="forminp"><?php

				// Get settings
				$colours = array_map( 'esc_attr', (array) get_option( 'wc_order_barcodes_colours' ) );

				// Set defaults
				if ( empty( $colours['foreground'] ) ) $colours['foreground'] = '#000000';
				if ( empty( $colours['background'] ) ) $colours['background'] = '#FFFFFF';

				$wc_settings_general = new WC_Settings_General();


				// Show colour selection inputs
	    		$wc_settings_general->color_picker( __( 'Foreground', 'woocommerce-order-barcodes' ), 'wc_order_barcodes_colours_foreground', $colours['foreground'], __( 'Barcode image and text color', 'woocommerce-order-barcodes' ) );
	    		$wc_settings_general->color_picker( __( 'Background', 'woocommerce-order-barcodes' ), 'wc_order_barcodes_colours_background', $colours['background'], __( 'Barcode background color', 'woocommerce-order-barcodes' ) );

		    ?></td>
		</tr><?php
	} // End colour_settings ()

	/**
	 * Save colour settings
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function colour_settings_save () {

		if ( isset( $_POST['wc_order_barcodes_colours_foreground'] ) || isset( $_POST['wc_order_barcodes_colours_background'] ) ) {

			// Get posted settings
			$foreground = ( isset( $_POST['wc_order_barcodes_colours_foreground'] ) && ! empty( $_POST['wc_order_barcodes_colours_foreground'] ) ) ? wc_format_hex( $_POST['wc_order_barcodes_colours_foreground'] ) : '';
			$background = ( isset( $_POST['wc_order_barcodes_colours_background'] ) && ! empty( $_POST['wc_order_barcodes_colours_background'] ) ) ? wc_format_hex( $_POST['wc_order_barcodes_colours_background'] ) : '';

			// Set settings array
			$colours = array(
				'foreground' => $foreground,
				'background' => $background,
			);

			// Save settings
			update_option( 'wc_order_barcodes_colours', $colours );
		}
	} // End colour_settings_save ()

	/**
	 * Add settings to WooCommerce General settings
	 * @access public
	 * @since  1.0.0
	 * @param  array $settings Default settings
	 * @return array           Modified settings
	 */
	public function add_settings ( $settings = array() ) {
		$settings = array_merge( $settings, $this->settings );
		return $settings;
	} // End add_settings ()

	/**
	 * Load assets
	 * @access public
	 * @since  1.0.0
	 * @param  string $hook_suffix Current hook
	 * @return void
	 */
	public function load_assets ( $hook_suffix = '' ) {
		if( 'woocommerce_page_wc-settings' != $hook_suffix ) return;
		wp_enqueue_style( $this->parent->_token . '-admin' );
	} // End load_assets ()

	/**
	 * Add settings link to plugin list table
	 * @access public
	 * @since  1.0.0
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=general' ) ) . '">' . __( 'Settings', 'woocommerce-order-barcodes' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	} // End add_settings_link ()

	/**
	 * Main class instance - ensures only one instance of the class is loaded or can be loaded
	 * @access public
	 * @since  1.0.0
	 * @static
	 * @see    WC_Order_Barcodes()
	 * @return WooCommerce_Order_Barcodes_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden
	 * @access public
	 * @since  1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden
	 * @access public
	 * @since  1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup ()

}
