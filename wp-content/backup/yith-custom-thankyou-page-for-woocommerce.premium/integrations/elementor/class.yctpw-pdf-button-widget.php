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

class YCTPW_Pdf_Button_Elementor_widget extends \Elementor\Widget_Base {

	/**
	 * Get Widget Section Name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'yith-thankyou-page-pdf_button';
	}

	/**
	 * Get Widget Title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'YITH Custom ThankYou Page Save As PDF button', 'yith-custom-thankyou-page-for-woocommerce' );
	}

	/**
	 * Get Widget Icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-button';
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
				'label' => esc_html__( 'YITH Custom ThankYou Page Save As PDF button', 'yith-custom-thankyou-page-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					'%s [<a target="_blank" href="%s">%s</a>].',
					esc_html__( 'This widget will work only on the Custom Thank You page, here you can only see a preview. Moreover to use this element in the position you\'ve chosen, you have to disable the automatic Pdf button and activate it as Shortcode ', 'yith-custom-thankyou-page-for-woocommerce' ),
					get_admin_url( null, 'admin.php?page=yith_ctpw_panel&tab=pdf' ),
					esc_html__( 'here', 'yith-custom-thankyou-page-for-woocommerce' )
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
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

		if ( isset($_GET['ctpw']) && isset($_GET['order-received']) ) {//phpcs:ignore
			echo is_callable( 'apply_shortcodes' ) ? apply_shortcodes( '[yith_ctpw_pdf_button]' ) : do_shortcode( '[yith_ctpw_pdf_button]' ); //phpcs:ignore XSS.
		} else {
			if ( isset($_GET['action']) && $_GET['action'] === 'elementor' || isset( $_POST['action']) && $_POST['action'] === 'elementor_ajax' ) { //phpcs:ignore
				if ( ! class_exists( 'YITH_Custom_Thankyou_Page_PDF' ) ) {
					require_once YITH_CTPW_PATH . 'includes/class.yith-custom-thankyou-page-pdf.php';
				}
				$p     = YITH_Custom_Thankyou_Page_PDF::instance();
				$style = $p->yith_ctpw_get_button_styles();

				$button_label = get_option( 'yith_ctpw_pdf_button_label', esc_html__( 'Save as PDF', 'yith-custom-thankyou-page-for-woocommerce' ) );
				echo $style . '<button id="yith_ctwp_pdf_button">' . apply_filters( 'yith_ctpw_pdf_button_label', $button_label ) . '</button>';
			}
		}

	}


}