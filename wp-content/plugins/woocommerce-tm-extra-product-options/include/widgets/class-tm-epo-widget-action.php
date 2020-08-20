<?php
/**
 * Epo Widget
 * (Used for for echoing a custom action)
 *
 * Autoload classes on demand.
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */
defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_Widget_Action extends WP_Widget {

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$widget_ops = array( 'classname' => 'tc_epo_show_widget', 'description' => esc_html__( 'Echo a custom action', 'woocommerce-tm-extra-product-options' ) );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'tc_epo_show_widget' );

		parent::__construct( 'tc_epo_show_widget', esc_html__( 'EPO custom action', 'woocommerce-tm-extra-product-options' ), $widget_ops, $control_ops );
	}

	/**
	 * Echoes the widget content
	 *
	 * @param array $args
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		$title  = empty( $instance['title'] ) ? '' : $instance['title'];
		$action = empty( $instance['action'] ) ? 'tc_show_epo' : $instance['action'];

		echo wp_kses_post( $args['before_widget'] );
		echo esc_html( $title );
		do_action( $action );

		echo wp_kses_post( $args['after_widget'] );

	}

	/**
	 * Updates a particular instance of a widget
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = wp_strip_all_tags( $new_instance['title'] );
		$instance['action'] = wp_strip_all_tags( $new_instance['action'] );

		return $instance;
	}

	/**
	 * Outputs the settings update form
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string Default return is 'noform'.
	 */
	public function form( $instance ) {
		$title  = isset( $instance['title'] ) ? $instance['title'] : '';
		$action = isset( $instance['action'] ) ? $instance['action'] : '';

		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'woocommerce-tm-extra-product-options' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'action' ) ); ?>"><?php esc_html_e( 'Custom action:', 'woocommerce-tm-extra-product-options' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'action' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'action' ) ); ?>" type="text" value="<?php echo esc_attr( $action ); ?>"/>
        </p>

		<?php
	}
}
