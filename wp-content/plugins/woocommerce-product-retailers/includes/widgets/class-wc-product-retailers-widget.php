<?php
/**
 * WooCommerce Product Retailers
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_6 as Framework;

/**
 * Product Retailers Widgets
 *
 * @since 1.4.0
 */
class WC_Product_Retailers_Widget extends \WP_Widget {


	/**
	 * Sets up the widget options.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		// set widget options
		$options = array(
			'classname'   => 'widget_wc_product_retailers',
			'description' => __( 'Display a set of product retailers for the current product if on a product page.', 'woocommerce-product-retailers' ),
		);

		// instantiate the widget
		parent::__construct( 'wc_product_retailers_widget', __( 'WooCommerce Product Retailers', 'woocommerce-product-retailers' ), $options );

	}


	/**
	 * Renders the product retailers widget.
	 *
	 * @see \WP_Widget::widget()
	 *
	 * @since 1.4.0
	 *
	 * @param array $args widget arguments
	 * @param array $instance saved values from database
	 */
	public function widget( $args, $instance ) {

		// technically this widget will render anytime there's a global $product available, not just on the product page, which may cause issues, but is kind of handy as well
		global $product;

		// bail if no product available or if retailers are hidden if product is in stock
		if ( ! $product || \WC_Product_Retailers_Product::product_retailers_hidden_if_in_stock( $product ) ) {
			return;
		}

		$retailers = \WC_Product_Retailers_Product::get_product_retailers( $product );

		if ( empty( $retailers ) ) {
			return;
		}

		// get the widget configuration
		$title = $instance['title'];

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title'];
		}

		woocommerce_single_product_product_retailers( $product );

		echo $args['after_widget'];
	}


	/**
	 * Updates the widget title & selected product.
	 *
	 * @see \WP_Widget::update()
	 *
	 * @since 1.4.0
	 *
	 * @param array $new_instance new widget settings
	 * @param array $old_instance old widget settings
	 * @return array updated widget settings
	 */
	public function update( $new_instance, $old_instance ) {

		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}


	/**
	 * Renders the admin form for the widget.
	 *
	 * @see WP_Widget::form()
	 *
	 * @since 1.4.0
	 *
	 * @param array $instance the widget settings
	 * @return string|void
	 */
	public function form( $instance ) {

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'woocommerce-product-retailers' ) ?>:</label>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'woocommerce-product-retailers' ) ?>:</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( isset( $instance['title'] ) ? $instance['title'] : '' ); ?>" />
		</p>
		<?php
	}


}
