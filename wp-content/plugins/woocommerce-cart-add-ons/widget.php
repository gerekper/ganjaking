<?php
/**
 * Cart Add-Ons Widget
 */
class Cart_Addons_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'cart_addons_widget', // Base ID
			'Cart Addons', // Name
			array( 'description' => __( 'Display available add-ons', 'sfn_cart_addons' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'] );
        $length = $instance['length'];
        $display= $instance['display'];
        $atc    = (isset($instance['add_to_cart'])) ? $instance['add_to_cart'] : 0;
        $addons = '';
        
        if (function_exists('sfn_display_cart_addons')) {
            ob_start();
            sfn_display_cart_addons( $length, $display, $atc );
            $addons = ob_get_clean();
        }
        
        if ($addons) {
            echo $before_widget;
            if ( ! empty( $title ) )
                echo $before_title . $title . $after_title;
                
            echo $addons;
            
            echo $after_widget;
        }
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']      = strip_tags( $new_instance['title'] );
        $instance['length']     = strip_tags( $new_instance['length'] );
        $instance['display']    = strip_tags( $new_instance['display'] );
        $instance['add_to_cart']= strip_tags( $new_instance['add_to_cart'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
        $title          = __( 'Available Add-ons', 'sfn_cart_addons' );
        $length         = 4;
		$display        = 'images';
        $add_to_cart    = 0;
        
        if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
        
        if ( isset( $instance[ 'length' ] ) ) {
			$length = $instance[ 'length' ];
		}
        
        if ( isset( $instance['display'] ) ) {
            $display = $instance['display'];
        }

        if ( isset( $instance['add_to_cart'] ) ) {
            $add_to_cart = $instance['add_to_cart'];
        }
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'length' ); ?>"><?php _e( 'Max. Products to Show:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'length' ); ?>" name="<?php echo $this->get_field_name( 'length' ); ?>" type="text" value="<?php echo esc_attr( $length ); ?>" />
		</p>
        <p>
		<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Display Mode:' ); ?></label> 
        <select name="<?php echo $this->get_field_name('display'); ?>" id="<?php echo $this->get_field_id('display'); ?>">
            <option value="images" <?php if ($display == 'images') echo 'selected'; ?>><?php _e('Product Thumbnails'); ?></option>
            <option value="images_name" <?php if ($display == 'images_name') echo 'selected'; ?>><?php _e('Product Thumbnails with Title'); ?></option>
            <option value="images_name_price" <?php if ($display == 'images_name_price') echo 'selected'; ?>><?php _e('Product Thumbnails with Title and Price'); ?></option>
            <option value="names" <?php if ($display == 'names') echo 'selected'; ?>><?php _e('Product Titles'); ?></option>
            <option value="names_price" <?php if ($display == 'names_price') echo 'selected'; ?>><?php _e('Product Titles with Price'); ?></option>
        </select>
		</p>

        <p>
        <label for="<?php echo $this->get_field_id( 'add_to_cart' ); ?>"><?php _e( 'Add to Cart button:' ); ?></label> 
        <select name="<?php echo $this->get_field_name('add_to_cart'); ?>" id="<?php echo $this->get_field_id('add_to_cart'); ?>">
            <option value="0" <?php if ($add_to_cart == '0') echo 'selected'; ?>><?php _e('No'); ?></option>
            <option value="1" <?php if ($add_to_cart == '1') echo 'selected'; ?>><?php _e('Yes'); ?></option>
        </select>
        </p>
		<?php 
	}

}