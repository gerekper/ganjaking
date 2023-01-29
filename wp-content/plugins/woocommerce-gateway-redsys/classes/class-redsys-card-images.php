<?php
/**
 * Class Redsys Card Images
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2013 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Redsys Card Images
 */
class Redsys_Card_Images extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$options = array(
			'classname'   => 'redsys_credit_card_widget',
			'description' => __( 'This Widget add the credit card image', 'woocommerce-redsys' ),
		);

		parent::__construct(
			'redsys_credit_card_widget',
			'Redsys Credit Card Image',
			$options
		);
	}
	/**
	 * Render the widget
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		// Define the widget.
		$title         = $instance['title'];
		$accepted_html = array(
			'div' => array(
				'id'    => array(),
				'class' => array(),
			),
		);
		echo wp_kses( $args['before_widget'], $accepted_html );
		// if title is present.
		if ( ! empty( $title ) ) {
			echo wp_kses( $args['before_title'] . $title . $args['after_title'], $accepted_html );
		}
		// output.
		echo '<!-- logos tarjetas crédito añadidos por el plugin de Redsys de WooCommerce.com -->';
		echo '<img src="' . esc_url( REDSYS_PLUGIN_URL_P ) . 'assets/images/Visa-MasterCard.png" alt="' . esc_html__( 'Accepted Credit Cards', 'woocommerce-redsys' ) . '" height="58" width="150">';
		echo '<!-- Fin logos tarjetas crédito añadidos por el plugin de Redsys de WooCommerce.com -->';
		echo wp_kses( $args['after_widget'], $accepted_html );
	}
	/**
	 * Form for setting the widget
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '';
		}
		?>
		<p>
			<label for="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'woocommerce-redsys' ); ?></label>
			<input class="widefat" id="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}
	/**
	 * Update the widget
	 *
	 * @param array $new_instance New instance.
	 * @param array $old_instance Old instance.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}
/**
 * Register the widget
 */
function redsys_register_card_image_widget() {
	register_widget( 'Redsys_Card_Images' );
}
add_action( 'widgets_init', 'redsys_register_card_image_widget' );
