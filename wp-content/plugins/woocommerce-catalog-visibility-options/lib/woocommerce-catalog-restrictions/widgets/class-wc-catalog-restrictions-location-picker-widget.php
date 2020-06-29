<?php

class WC_Catalog_Restrictions_Location_Picker_Widget extends WP_Widget {

	private static $instance;

	public static function register() {
		add_action( 'widgets_init', array(__CLASS__, 'add_widget') );
	}

	public static function add_widget() {
		register_widget( 'WC_Catalog_Restrictions_Location_Picker_Widget' );
	}

	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'widget_location_picker';
		$this->woo_widget_description = __( 'Display a location picker and displays the users current location in the sidebar.', 'wc_catalog_restrictions' );
		$this->woo_widget_idbase = 'woocommerce_location_picker';
		$this->woo_widget_name = __( 'WooCommerce Location Picker', 'wc_catalog_restrictions' );

		/* Widget settings. */
		$widget_ops = array('classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description);

		/* Create the widget. */
		parent::__construct( 'widget_location_picker', $this->woo_widget_name, $widget_ops );
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		global $woocommerce, $wc_catalog_restrictions;

		extract( $args );

		// Don't show if on the account page since that has a login
		if ( is_account_page() && !is_user_logged_in() )
			return;

		$location_set_title = (!empty( $instance['location_set_title'] )) ? $instance['location_set_title'] : __( 'Current location is %s', 'wc_catalog_restrictions' );
		$location_not_set_title = (!empty( $instance['location_not_set_title'] )) ? $instance['location_not_set_title'] : __( 'Choose your location', 'wc_catalog_restrictions' );

		echo $before_widget;

		// Get redirect URL
		$redirect_to = apply_filters( 'woocommerce_catalog_restrictions_choose_location_widget_redirect', get_permalink( wc_get_page_id( 'shop' ) ) );
		?>
		<form method="post">
			<p><?php woocommerce_catalog_restrictions_country_input( $wc_catalog_restrictions->get_location_for_current_user(), array('label' => $location_not_set_title) ); ?></p>
			<p><input type="submit" class="submitbutton" name="wp-submit" id="wp-submit" value="<?php _e( 'Go &rarr;', 'wc_catalog_restrictions' ); ?>" /> </p>
			<div>
				<input type="hidden" name="redirect_to" class="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
				<input type="hidden" name="testcookie" value="1" />
				<input type="hidden" name="woocommerce_catalog_restrictions_location_picker" value="sidebar" />
				<input type="hidden" name="rememberme" value="forever" />
			</div>
		</form>
		<?php
		echo $after_widget;
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['location_set_title'] = strip_tags( stripslashes( $new_instance['location_set_title'] ) );
		$instance['location_not_set_title'] = strip_tags( stripslashes( $new_instance['location_not_set_title'] ) );
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		?>

		<p><label for="<?php echo $this->get_field_id( 'location_not_set_title' ); ?>"><?php _e( 'Location not set title:', 'woocommerce' ) ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location_not_set_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location_not_set_title' ) ); ?>" value="<?php
			if ( isset( $instance['location_not_set_title'] ) )
				echo esc_attr( $instance['location_not_set_title'] );
			else
				echo __( 'Choose your location', 'wc_catalog_restrictions' );
			?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'location_set_title' ); ?>"><?php _e( 'Logged set title:', 'woocommerce' ) ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location_set_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location_set_title' ) ); ?>" value="<?php
			       if ( isset( $instance['location_set_title'] ) )
				       echo esc_attr( $instance['location_set_title'] );
			       else
				       echo __( 'Current location is %s', 'wc_catalog_restrictions' );
			       ?>" /></p>

		<?php
	}

}

/**
 * Process the location selection.
 *
 * @access public
 * @package 	WooCommerce_Catalog_Restrictions/Widgets
 * @return void
 */
function woocommerce_catalog_restrictions_sidebar_location_picker_process() {
	global $woocommerce;

	if ( isset( $_POST['woocommerce_catalog_restrictions_location_picker'] ) && $_POST['woocommerce_catalog_restrictions_location_picker'] == 'sidebar' ) {
		// Get redirect URL
		$redirect_to = apply_filters( 'woocommerce_catalog_restrictions_choose_location_widget_redirect', get_permalink( wc_get_page_id( 'shop' ) ) );
		$woocommerce->session->wc_location = $_POST['location'];

		wp_safe_redirect( $redirect_to );
		exit;
	}
}

add_action( 'init', 'woocommerce_catalog_restrictions_sidebar_location_picker_process', 0 );
