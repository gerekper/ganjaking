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

class YCTPW_SocialBox_Elementor_widget extends \Elementor\Widget_Base {

	/**
	 * Get Widget Section Name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'yith-custom-thank-you-page-social-box';
	}

	/**
	 * Get Widget Title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'YITH Custom ThankYou Page SocialBox', 'yith-custom-thankyou-page-for-woocommerce' );
	}

	/**
	 * Get Widget Icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-carousel';
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
		return array( 'woocommerce', 'shop', 'store', 'social', 'thank-you' );
	}

	/**
	 * Register Widget Controls
	 *
	 * @return void
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_upsells',
			array(
				'label' => esc_html__( 'YITH Custom ThankYou Page SocialBox', 'yith-custom-thankyou-page-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					'%s [<a target="_blank" href="%s">%s</a>].',
					esc_html__( 'This widget will work only on the Custom Thank You page, here you can only see a preview. Moreover to use this element in the position you\'ve chosen, you have to disable the Social Box settings ', 'yith-custom-thankyou-page-for-woocommerce' ),
					get_admin_url( null, 'admin.php?page=yith_ctpw_panel&tab=socialbox' ),
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

		$this->add_control(
			'show_facebook',
			array(
				'label'        => esc_html__( 'Enable Facebook', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Enable', 'your-plugin' ),
				'label_off'    => __( 'Disable', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_twitter',
			array(
				'label'        => esc_html__( 'Enable Twitter', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Enable', 'your-plugin' ),
				'label_off'    => __( 'Disable', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_pinterest',
			array(
				'label'        => esc_html__( 'Enable Pinterest', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Enable', 'your-plugin' ),
				'label_off'    => __( 'Disable', 'your-plugin' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
		$settings = $this->get_settings_for_display();

		if ( isset($_GET['ctpw']) && isset($_GET['order-received']) ) {//phpcs:ignore
			$args = array(
				'facebook'  => $settings['show_facebook'],
				'twitter'   => $settings['show_twitter'],
				'pinterest' => $settings['show_pinterest'],
				'title'     => '',
			);

			$yctpw_f = new YITH_Custom_Thankyou_Page_Frontend_Premium();
			echo $yctpw_f->yith_ctpw_social_shortcode( $args );//phpcs:ignore XSS.
		} else {

			if ( isset($_GET['action']) && $_GET['action'] === 'elementor' || isset( $_POST['action']) && $_POST['action'] === 'elementor_ajax' ) {//phpcs:ignore
				$order_id = ( isset( $settings['order_id'] ) && '' !== $settings['order_id'] ) ? $settings['order_id'] : 0;

				if ( $order_id ) {
					add_filter( 'yit_ctpw_force_social_box', '__return_true' );

					$args = array(
						'facebook'  => $settings['show_facebook'],
						'twitter'   => $settings['show_twitter'],
						'pinterest' => $settings['show_pinterest'],
						'order'     => $order_id,
						'title'     => 'test',
					);

					$yctpw_f = new YITH_Custom_Thankyou_Page_Frontend_Premium();
					echo $yctpw_f->yith_ctpw_social_shortcode( $args );//phpcs:ignore XSS.

				} else {
					echo esc_html__( 'Please enter a valid order ID to preview this widget.', 'yith-custom-thankyou-page-for-woocommerce' );
				}
			}
		}


	}


}