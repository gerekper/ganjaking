<?php
/**
 * Brand Description Widget
 *
 * When viewing a brand archive, show the current brands description + image
 *
 * @package		WooCommerce
 * @category	Widgets
 * @author		WooThemes
 */

class WC_Widget_Brand_Description extends WP_Widget {

	/** Variables to setup the widget. */
	var $woo_widget_cssclass;
	var $woo_widget_description;
	var $woo_widget_idbase;
	var $woo_widget_name;

	/** constructor */
	function __construct() {

		/* Widget variable settings. */
		$this->woo_widget_name        = __('WooCommerce Brand Description', 'woocommerce-brands' );
		$this->woo_widget_description = __( 'When viewing a brand archive, show the current brands description.', 'woocommerce-brands' );
		$this->woo_widget_idbase      = 'wc_brands_brand_description';
		$this->woo_widget_cssclass    = 'widget_brand_description';

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );

		/* Create the widget. */
		parent::__construct( $this->woo_widget_idbase, $this->woo_widget_name, $widget_ops );
	}

	/** @see WP_Widget */
	function widget( $args, $instance ) {
		extract( $args );

		if ( ! is_tax( 'product_brand' ) )
			return;

		if ( ! get_query_var( 'term' ) )
			return;

		$thumbnail = '';
		$term      = get_term_by( 'slug', get_query_var( 'term' ), 'product_brand' );

		$thumbnail = get_brand_thumbnail_url( $term->term_id, 'large' );

		echo $before_widget . $before_title . $term->name . $after_title;

		wc_get_template( 'widgets/brand-description.php', array(
			'thumbnail' => $thumbnail,
			'brand' => $term
		), 'woocommerce-brands', untrailingslashit( plugin_dir_path( dirname( dirname( __FILE__ ) ) ) ) . '/templates/' );

		echo $after_widget;
	}

	/** @see WP_Widget->update */
	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		return $instance;
	}

	/** @see WP_Widget->form */
	function form( $instance ) {
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'woocommerce-brands') ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php if ( isset ( $instance['title'] ) ) echo esc_attr( $instance['title'] ); ?>" />
			</p>
		<?php
	}

}
