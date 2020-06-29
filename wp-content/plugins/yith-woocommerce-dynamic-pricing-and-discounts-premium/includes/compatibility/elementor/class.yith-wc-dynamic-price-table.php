<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Ajax Search Premium
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
	exit; // Exit if accessed directly.
}


use Elementor\Controls_Manager;

/**
 * Class YITH_WC_Dynamic_Price_Table_Widget
 */
class YITH_WC_Dynamic_Price_Table_Widget extends \Elementor\Widget_Base {


	/**
	 * Return the name of widget.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'yith-wc-dynamic-price-table';
	}

	/**
	 * Return the title.
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'YITH WooCommerce Dynamic Price Table', 'ywdpd' );
	}

	/**
	 * Return the icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * Return the categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'yith', 'general' );
	}

	/**
	 * Return the keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'woocommerce', 'price', 'store', 'discount', 'product', 'yith' );
	}

	/**
	 * Register controls
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'YITH Dynamic Price Table', 'ywdpd' ),
			)
		);


		$this->add_control(
			'product',
			array(
				'label' => __( 'Product ID', 'ywdpd' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => defined('ELEMENTOR_PRO_VERSION'),
				),
				'default' => __( '', 'ywdpd' ),
				'placeholder' => __( 'Leave empty for single product page', 'ywdpd' ),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render the form
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['product'] ) || $settings['product'] == '' ) {
			global $product;
		} else {
			$product = wc_get_product( $settings['product'] );
		}

		if( $product ){
			$shortcode = do_shortcode( '[yith_ywdpd_quantity_table product='.$product->get_id().']' );
		}else{
			$shortcode = do_shortcode( '[yith_ywdpd_quantity_table]' );
		}

		?>
		<div class="elementor-shortcode"><?php echo $shortcode; //phpcs:ignore ?></div>
		<?php

	}

}
