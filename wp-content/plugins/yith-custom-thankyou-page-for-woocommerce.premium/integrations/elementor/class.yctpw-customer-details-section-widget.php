<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 **/

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

use Elementor\Controls_Manager;
use Elementor\Widget_Button;
use ElementorPro\Modules\QueryControl\Module;

class YCTPW_Order_Customer_Details_Elementor_widget extends \Elementor\Widget_Base {

	/**
	 * Get Widget Section Name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'yith-order-customer-details-section';
	}

	/**
	 * Get Widget Title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'YITH Custom ThankYou Page Customer Details Section', 'yith-custom-thankyou-page-for-woocommerce' );
	}

	/**
	 * Get Widget Icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-user-circle-o';
	}

	/**
	 * Get Widget Category
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'yith' );
	}

	/**
	 * Get Widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'woocommerce', 'shop', 'store', 'order', 'thank-you' );
	}

	/**
	 * Register Widget Controls
	 *
	 * @return void
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_order_header',
			array(
				'label' => esc_html__( 'YITH Custom ThankYou Page Customer Details Section', 'yith-custom-thankyou-page-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					'%s [<a target="_blank" href="%s">%s</a>].',
					__( 'This widget will work only on the Custom Thank You page, here you can only see a preview. Moreover to use this element in the position you\'ve chosen, you have to disable the related section ', 'yith-custom-thankyou-page-for-woocommerce' ),
					get_admin_url( null, 'admin.php?page=yith_ctpw_panel&tab=settings' ),
					esc_html__( 'here', 'yith-custom-thankyou-page-for-woocommerce' )
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);


		$this->add_control(
			'wc_style_warning_preview',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_attr__( 'Please enter a valid order ID to preview this widget.', 'yith-custom-thankyou-page-for-woocommerce' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'order_id',
			array(
				'label'       => esc_html__( 'Order Id', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => '',
			)
		);


		$this->end_controls_section();

	}

	/**
	 * Register Widget
	 *
	 * @return void
	 */
	protected function render() {

		if ( isset($_GET['ctpw']) && isset($_GET['order-received']) ) { //phpcs:ignore
			$yctpw_f = new YITH_Custom_Thankyou_Page_Frontend_Premium();
			echo $yctpw_f->yith_ctpw_customer_details_shortcode(); //phpcs:ignore XSS.
		} else {
			if ( isset($_GET['action']) && $_GET['action'] === 'elementor' || isset( $_POST['action']) && $_POST['action'] === 'elementor_ajax' ) { //phpcs:ignore
				$settings = $this->get_settings_for_display();
				$order_id = ( isset( $settings['order_id'] ) && '' !== $settings['order_id'] ) ? $settings['order_id'] : 0;

				$order = wc_get_order( $order_id );

				if ( $order ) {
					ob_start();
					wc_get_template( 'yith_ctpw_customer_details.php', array( 'order' => $order ), '', YITH_CTPW_TEMPLATE_PATH . 'woocommerce/' );
					$value = ob_get_contents();
					ob_end_clean();
					echo $value; //phpcs:ignore XSS.
				} else {
					echo esc_html__( 'Please enter a valid order ID to preview this widget.', 'yith-custom-thankyou-page-for-woocommerce' );
				}
			}
		}

	}


}