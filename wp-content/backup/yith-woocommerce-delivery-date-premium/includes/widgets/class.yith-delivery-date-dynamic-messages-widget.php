<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Dynamic_Messages_Widget' ) ) {

	class YITH_Delivery_Dynamic_Messages_Widget extends WP_Widget {

		public function __construct() {
			parent::__construct( 'ywcdd_dynamic_messages',
				__( 'YITH Delivery Dynamic Messages', 'yith-woocommerce-delivery-date' ),
                array('description' => __('Show dynamic messages about delivery. Please note - this widget works only on the single product sidebar','yith-woocommerce-delivery-date')));
		}

		public function widget( $args, $instance ) {

			$title    = $instance['title'];
			$title    = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$template = '';
			global $product;

			if ( is_product() &&! is_null( $product ) ) {
				ob_start();
				YITH_Delivery_Date_Product_Frontend()->get_date_info( $product );
				$template = ob_get_contents();
				ob_end_clean();
				echo $args['before_widget'];
				echo $args['before_title'];
				echo $title;
				echo $args['after_title'];
				echo $template;
				echo $args['after_widget'];
			}
		}

		public function form( $instance ) {

			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			?>
            <div id="ywcca_widget_content">
                <p class="title_shortcode">
                    <label for="<?php echo $this->get_field_id( "title" ); ?>"><?php _e( 'Title', 'yith-woocommerce-delivery-date' ); ?></label>
                    <input class="widefat" type="text" id="<?php echo $this->get_field_id( "title" ); ?>"
                           name="<?php echo $this->get_field_name( "title" ); ?>"
                           placeholder="<?php _e( 'Insert a title', 'yith-woocommerce-delivery-date' ); ?>"
                           value="<?php echo $title; ?>">
                </p>
            </div>
			<?php
		}

		public function update( $new_instance, $old_instance ) {

		    $instance = array(
		      'title' => isset( $new_instance['title'] ) ? $new_instance['title'] : ''
            );

		    return $instance;
		}
	}
}