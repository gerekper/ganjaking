<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpali.com
 * @since      1.0.0
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/admin
 * @author     ALI KHALLAD <ali@wpali.com>
 */
class Wpali_Woocommerce_Order_Builder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpali_Woocommerce_Order_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpali_Woocommerce_Order_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 
		wp_enqueue_style( 'cmb2-tabs', plugin_dir_url( __FILE__ ) . 'css/wpali-woocommerce-order-builder-admin-cmb-tabs.css', array(), $this->version, 'all' );
				
		global $post;
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( 'product' === $post->post_type ) { 
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpali-woocommerce-order-builder-admin.css', array(), $this->version, 'all' );
				wp_enqueue_style( 'metabox-tabs', plugin_dir_url( __FILE__ ) . 'css/wpali-woocommerce-order-builder-admin-metabox-tabs.css', array(), $this->version, 'all' );
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpali_Woocommerce_Order_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpali_Woocommerce_Order_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'cmb2-tabs', plugin_dir_url( __FILE__ ) . 'js/wpali-woocommerce-order-builder-admin-cmb-tabs.js', array( 'jquery' ), $this->version, false );
		
		global $post;
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( 'product' === $post->post_type ) { 
				wp_enqueue_script( 	'wwob-conditionals', 	plugin_dir_url( __FILE__ ) . 'js/wpali-woocommerce-order-builder-admin-metaboxes-condition.js', array( 'jquery', 'cmb2-scripts' ), $this->version, true );
				
				wp_enqueue_script('jquery-ui-sortable');
				
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpali-woocommerce-order-builder-admin.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, false );
			}
		}
	}

	/**
	 * Admin notice if Woocommerce is not active.
	 *
	 * @since    1.0.0
	 */
	public function wwob_admin_notice__error() {

		$class = 'notice notice-error';
		$message = __( 'WpAli: Woocommerce Order Builder: Please Activate WooCommerce First to Use the Plugin.', 'wpali-woocommerce-order-builder' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		
	}
	
	/**
	 * Display Customer Instructions in Order Edit Page
	 */
	

	public function wwob_customer_instructions_admin_order_meta($order){
		echo '<p><strong>'.__('Customer Instructions:').':</strong> <br/>' . get_post_meta( $order->id, 'wwob_customer_instructions', true ) . '</p>';
	}
	
}
