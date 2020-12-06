<?php

/**
* Copyright: (C) 2013 - 2021 José Conti
*/
class Redsys_Card_Images extends WP_Widget {
	
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function __construct() {
		$options = array(
			'classname'   => 'redsys_credit_card_widget',
			'description' => __( 'This Widget add the credit card image', 'woocommerce-redsys' ),
		);
		
		parent::__construct(
			'redsys_credit_card_widget', 'Redsys Credit Card Image', $options
		);
	}
	
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function widget( $args, $instance ) {
		// Define the widget
		$title = $instance['title'];
		echo $args['before_widget'];
		//if title is present
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		//output
		echo '<!-- logos tarjetas crédito añadidos por el plugin de Redsys de WooCommerce.com -->';
		echo '<img src="' . REDSYS_PLUGIN_URL . 'assets/images/Visa-MasterCard.png" alt="' . __( 'Accepted Credit Cards', 'woocommerce-redsys' ) . '" height="58" width="150">';
		echo '<!-- Fin logos tarjetas crédito añadidos por el plugin de Redsys de WooCommerce.com -->';
		echo $args['after_widget'];
	}
	
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function form( $instance ) {
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = '';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'woocommerce-redsys' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}
	
	/**
	* Copyright: (C) 2013 - 2021 José Conti
	*/
	public function update( $new_instance, $old_instance ) {
		
		$instance            = array();
		$instance[ 'title' ] = ( ! empty( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
		return $instance;
	}
}
// Register the widget
/**
* Copyright: (C) 2013 - 2021 José Conti
*/
function redsys_register_card_image_widget() {
	register_widget( 'Redsys_Card_Images' );
}
add_action( 'widgets_init', 'redsys_register_card_image_widget' );
