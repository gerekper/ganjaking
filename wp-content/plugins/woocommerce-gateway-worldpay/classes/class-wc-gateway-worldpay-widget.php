<?php 
class WorldPayHosted_Logo_Widget extends WP_Widget {

	public function __construct() {

		$widget_options = array( 
		      'classname' 	=> 'worldpay_logo_widget',
		      'description' => __('WorldPay Form', 'woocommerce_worlday'),
		);

		parent::__construct( 'worldpay_logo_widget', __('WorldPay Form', 'woocommerce_worlday'), $widget_options );

	}

	public function widget( $args, $instance ) {

		$woocommerce_worldpay_settings  = get_option('woocommerce_worldpay_settings');

		$title 			= apply_filters( 'worldpay_logo_widget_title', $instance[ 'title' ] );
		$worldpaylogo 	= $this->get_wplogo( $instance[ 'worldpaylogo' ] );
		$cardlogo	 	= $this->get_cardlogos( $instance[ 'cardlogo' ], $woocommerce_worldpay_settings['cardtypes'] );

		echo $args['before_widget'];

		echo $args['before_title'] . $title . $args['after_title']; 

		?>

		<p class="worldpay_logo_widget_wplogos"><?php echo $worldpaylogo; ?></p>
		<p class="worldpay_logo_widget_cardlogos"><?php echo $cardlogo; ?></p>

		<?php echo $args['after_widget'];

	}

	public function form( $instance ) {

		$woocommerce_worldpay_settings  = get_option('woocommerce_worldpay_settings');

		$title 			= ! empty( $instance['title'] ) ? $instance['title'] : __('Payments Powered By WorldPay', 'woocommerce_worlday');
		$worldpaylogo 	= ! empty( $instance['worldpaylogo'] ) ? $instance['worldpaylogo'] : '';
		$cardlogo 		= ! empty( $instance['cardlogo'] ) ? $instance['cardlogo'] : ''; 

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'worldpaylogo' ); ?>">WorldPay Logo:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'worldpaylogo' ); ?>" name="<?php echo $this->get_field_name( 'worldpaylogo' ); ?>" value="<?php echo esc_attr( $worldpaylogo ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cardlogo' ); ?>">Card Logos:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'cardlogo' ); ?>" name="<?php echo $this->get_field_name( 'cardlogo' ); ?>" value="<?php echo esc_attr( $cardlogo ); ?>" />
		</p>
		<?php 

		$woocommerce_worldpay_settings  = get_option('woocommerce_worldpay_settings');

		$title 			= apply_filters( 'worldpay_logo_widget_title', $title );
		$worldpaylogo 	= $this->get_wplogo( $worldpaylogo );
		$cardlogo	 	= $this->get_cardlogos( $cardlogo, $woocommerce_worldpay_settings['cardtypes'] );

		echo '<h3>' . __('Current Settings', 'woocommerce_worlday') . '</h3>';

		echo $title; 

		?>

		<p class="worldpay_logo_widget_wplogos"><?php echo $worldpaylogo ?></p>
		<p class="worldpay_logo_widget_cardlogos"><?php echo $cardlogo; ?></p>

		<p><?php _e('If you have chosen any accepted cards in the WorldPay settings then these are shown here, alternatively you can load your own logos.', 'woocommerce_worlday'); ?></p>

		<?php

	}

	public function update( $new_instance, $old_instance ) {

		$instance 					= $old_instance;
		$instance[ 'title' ] 		= ( $new_instance[ 'title' ] );
		$instance[ 'worldpaylogo' ] = ( $new_instance[ 'worldpaylogo' ] );
		$instance[ 'cardlogo' ] 	= ( $new_instance[ 'cardlogo' ] );

		return $instance;

	}

	/**
	 * Return card logos for widget
	 * @param  [type] $cardlogo  [logo image in widget settings]
	 * @param  [type] $cardtypes [card types from WorldPay settings]
	 * @return [type]            [return card logos]
	 */
	public function get_cardlogos( $cardlogo, $cardtypes ) {

			if( !empty( $cardlogo ) ) {

				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( $cardlogo ) . '" alt="' . esc_attr( __('WorldPay Accepted Card Logos', 'woocommerce_worlday') ) . '" />';			
				} else {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( $cardlogo ) ) . '" alt="' . esc_attr( __('WorldPay Accepted Card Logos', 'woocommerce_worlday') ) . '" />';		
				}

			} elseif ( !empty( $cardtypes ) ) {

				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {

					// display icons for the selected card types
					foreach ( $cardtypes as $card_type ) {

						$icon .= '<img src="' . 
									esc_url( WC_Gateway_Worldpay_Form::get_plugin_url() . '/images/card-' . 
									strtolower( str_replace(' ','-',$card_type) ) . '.png' ) . '" alt="' . 
									esc_attr( strtolower( $card_type ) ) . '" />';
					}

				} else {

					// display icons for the selected card types
					foreach ( $cardtypes as $card_type ) {

						$icon .= '<img src="' . 
									esc_url( WC_HTTPS::force_https_url( WC_Gateway_Worldpay_Form::get_plugin_url() ) . '/images/card-' . 
									strtolower( str_replace(' ','-',$card_type) ) . '.png' ) . '" alt="' . 
									esc_attr( strtolower( $card_type ) ) . '" />';
					}

				}

			} else {
		
				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_Gateway_Worldpay_Form::get_plugin_url() . '/images/cards.png' ) . '" alt="' . esc_attr( __('WorldPay Accepted Card Logos', 'woocommerce_worlday') ) . '" />';			
				} else {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( WC_Gateway_Worldpay_Form::get_plugin_url() . '/images/cards.png' ) ) . '" alt="' . esc_attr( __('WorldPay Accepted Card Logos', 'woocommerce_worlday') ) . '" />';		
				}

			} 

			return $icon;

	}

	/**
	 * Return card logos for widget
	 * @param  [type] $cardlogo  [logo image in widget settings]
	 * @param  [type] $cardtypes [card types from WorldPay settings]
	 * @return [type]            [return card logos]
	 */
	public function get_wplogo( $wplogo ) {

			if( !empty( $wplogo ) ) {

				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( $wplogo ) . '" alt="' . esc_attr( __('WorldPay Logo', 'woocommerce_worlday') ) . '" />';			
				} else {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( $wplogo ) ) . '" alt="' . esc_attr( __('WorldPay Logo', 'woocommerce_worlday') ) . '" />';		
				}

			} else {
		
				if ( get_option('woocommerce_force_ssl_checkout')=='no' ) {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_Gateway_Worldpay_Form::get_plugin_url() . '/images/poweredByWorldPay.png' ) . '" alt="' . esc_attr( __('WorldPay Logo', 'woocommerce_worlday') ) . '" />';			
				} else {
					// use icon provided by filter
					$icon = '<img src="' . esc_url( WC_HTTPS::force_https_url( WC_Gateway_Worldpay_Form::get_plugin_url() . '/images/poweredByWorldPay.png' ) ) . '" alt="' . esc_attr( __('WorldPay Logo', 'woocommerce_worlday') ) . '" />';		
				}

			} 

			return $icon;

	}


}

add_action( 'widgets_init', 'worldpayhosted_logo_register_widget' );

function worldpayhosted_logo_register_widget() { 
	register_widget( 'WorldPayHosted_Logo_Widget' );
}