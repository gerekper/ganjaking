<?php
/**
 * WooCommerce Product Documents
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product documents widget.
 *
 * @since 1.0
 */
class WC_Product_Documents_Widget_Documents extends \WP_Widget {


	/**
	 * Sets up the widget options.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// set widget options
		$options = array(
			'classname'   => 'widget_wc_product_documents',
			'description' => __( 'Display a set of product documents for the current product if on a product page.', 'woocommerce-product-documents' ),
		);

		// instantiate the widget
		parent::__construct( 'wc_product_documents_widget_documents', __( 'WooCommerce Product Documents', 'woocommerce-product-documents' ), $options );
	}


	/**
	 * Renders the product documents widget.
	 *
	 * @since 1.0
	 *
	 * @see \WP_Widget::widget()
	 *
	 * @param array $args widget arguments
	 * @param array $instance saved values from database
	 */
	public function widget( $args, $instance ) {

		// technically this widget will render anytime there's a global $product available, not just on the product page, which may cause issues, but is kind of handy as well
		global $product;

		// bail if no product available
		if ( ! $product ) {
			return;
		}

		$documents_collection = new WC_Product_Documents_Collection( $product->get_id() );
		if ( ! $documents_collection->has_sections() ) {
			return;
		}

		// get the widget configuration
		$title = $instance['title'];

		// default to product documents title from product configuration.  Granted this makes it more difficult to have no title in the widget area, but is that an issue?
		if ( ! $title ) {
			$title = wc_product_documents()->get_documents_title_text( $product->get_id() );
		}

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		woocommerce_product_documents_template( $product, false );  // false to force the template to not display a title since the widget takes care of it

		echo $args['after_widget'];
	}


	/**
	 * Updates the widget title & selected product.
	 *
	 * @since 1.0
	 *
	 * @see \WP_Widget::update()
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
	 * @since 1.0
	 *
	 * @param array $instance the widget settings
	 * @return string|void
	 */
	public function form( $instance ) {

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'woocommerce-product-documents' ) ?>:</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( isset( $instance['title'] ) ? $instance['title'] : '' ); ?>" />
		</p>
		<?php
	}


}
