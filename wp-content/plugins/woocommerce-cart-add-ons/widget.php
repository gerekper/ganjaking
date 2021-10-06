<?php
defined( 'ABSPATH' ) || exit;

/**
 * Cart Add-Ons Widget
 */
class Cart_Addons_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'cart_addons_widget',
			__( 'Cart Addons', 'sfn_cart_addons' ),
			array( 'description' => __( 'Display available add-ons', 'sfn_cart_addons' ) )
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
		$title   = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$length  = ! empty( $instance['length'] ) ? $instance['length'] : 4;
		$display = ! empty( $instance['display'] ) ? $instance['display'] : 'images';
		$atc     = isset( $instance['add_to_cart'] ) ? $instance['add_to_cart'] : 0;
		$addons  = '';

		if ( function_exists( 'sfn_display_cart_addons' ) ) {
			ob_start();
			sfn_display_cart_addons( $length, $display, $atc );
			$addons = ob_get_clean();
		}

		if ( $addons ) {
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo $addons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
		$instance                = array();
		$instance['title']       = wp_strip_all_tags( $new_instance['title'] );
		$instance['length']      = wp_strip_all_tags( $new_instance['length'] );
		$instance['display']     = wp_strip_all_tags( $new_instance['display'] );
		$instance['add_to_cart'] = wp_strip_all_tags( $new_instance['add_to_cart'] );
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
		$title       = __( 'Available Add-ons', 'sfn_cart_addons' );
		$length      = 4;
		$display     = 'images';
		$add_to_cart = 0;

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}

		if ( isset( $instance['length'] ) ) {
			$length = $instance['length'];
		}

		if ( isset( $instance['display'] ) ) {
			$display = $instance['display'];
		}

		if ( isset( $instance['add_to_cart'] ) ) {
			$add_to_cart = $instance['add_to_cart'];
		}

		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'sfn_cart_addons' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'length' ) ); ?>"><?php esc_html_e( 'Max. Products to Show:', 'sfn_cart_addons' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'length' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'length' ) ); ?>" type="text" value="<?php echo esc_attr( $length ); ?>" />
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"><?php esc_html_e( 'Display Mode:', 'sfn_cart_addons' ); ?></label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>">
			<option value="images" <?php selected( $display, 'images' ); ?>><?php esc_html_e( 'Product Thumbnails', 'sfn_cart_addons' ); ?></option>
			<option value="images_name" <?php selected( $display, 'images_name' ); ?>><?php esc_html_e( 'Product Thumbnails with Title', 'sfn_cart_addons' ); ?></option>
			<option value="images_name_price" <?php selected( $display, 'images_name_price' ); ?>><?php esc_html_e( 'Product Thumbnails with Title and Price', 'sfn_cart_addons' ); ?></option>
			<option value="names" <?php selected( $display, 'names' ); ?>><?php esc_html_e( 'Product Titles', 'sfn_cart_addons' ); ?></option>
			<option value="names_price" <?php selected( $display, 'names_price' ); ?>><?php esc_html_e( 'Product Titles with Price', 'sfn_cart_addons' ); ?></option>
		</select>
		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'add_to_cart' ) ); ?>"><?php esc_html_e( 'Add to Cart button:', 'sfn_cart_addons' ); ?></label>
		<select name="<?php echo esc_attr( $this->get_field_name( 'add_to_cart' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'add_to_cart' ) ); ?>">
			<option value="0" <?php selected( $add_to_cart, '0' ); ?>><?php esc_html_e( 'No' ); ?></option>
			<option value="1" <?php selected( $add_to_cart, '1' ); ?>><?php esc_html_e( 'Yes' ); ?></option>
		</select>
		</p>
		<?php
	}

}
