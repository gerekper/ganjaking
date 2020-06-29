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

class YCTPW_UpSells_Elementor_widget extends \Elementor\Widget_Base {

	/**
	 * Get Widget Section Name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'yith-custom-thank-you-page-upsells';
	}

	/**
	 * Get Widget Title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'YITH Custom ThankYou Page UpSells', 'yith-custom-thankyou-page-for-woocommerce' );
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
		return array( 'woocommerce', 'shop', 'store', 'up-sell', 'thank-you' );
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
				'label' => esc_html__( 'YITH Custom ThankYou Page UpSells', 'yith-custom-thankyou-page-for-woocommerce' ),
			)
		);

		$this->add_control(
			'wc_style_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					'%s [<a target="_blank" href="%s">%s</a>].',
					esc_html__( 'This widget will work only on the Custom Thank You page, here you can only see a preview. Moreover to use this element in the position you\'ve chosen, you have to disable the UpSells settings ', 'yith-custom-thankyou-page-for-woocommerce' ),
					get_admin_url( null, 'admin.php?page=yith_ctpw_panel&tab=upsells' ),
					esc_html__( 'here', 'yith-custom-thankyou-page-for-woocommerce' )
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'       => esc_html__( 'Products per row', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => false,
				),
				'default'     => 4,
				'placeholder' => '',
			)
		);

		$this->add_control(
			'products_per_page',
			array(
				'label'       => esc_html__( 'Products per page', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => false,
				),
				'default'     => 4,
				'placeholder' => '',
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order by', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'title'  => esc_html__( 'title', 'yith-custom-thankyou-page-for-woocommerce' ),
					'random' => esc_html__( 'random', 'yith-custom-thankyou-page-for-woocommerce' ),
					'date'   => esc_html__( 'date', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'default' => 'title',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'asc'  => esc_html__( 'ASC', 'yith-custom-thankyou-page-for-woocommerce' ),
					'desc' => esc_html__( 'DESC', 'yith-custom-thankyou-page-for-woocommerce' ),
				),
				'default' => 'asc',
			)
		);

		$this->add_control(
			'ids',
			array(
				'label'       => esc_html__( 'Products', 'yith-custom-thankyou-page-for-woocommerce' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => false,
				),
				'default'     => '',
				'placeholder' => 'write products id comma separated',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$p_per_row  = ( isset( $settings['columns'] ) && '' !== $settings['columns'] ) ? $settings['columns'] : '4';
		$p_per_page = ( isset( $settings['products_per_page'] ) && '' !== $settings['products_per_page'] ) ? $settings['products_per_page'] : - 1;
		$order_by   = ( isset( $settings['order_by'] ) && '' !== $settings['order_by'] ) ? $settings['order_by'] : 'title';
		$order      = ( isset( $settings['order'] ) && '' !== $settings['order'] ) ? $settings['order'] : 'ASC';
		$ids        = ( isset( $settings['ids'] ) && '' !== $settings['ids'] ) ? $settings['ids'] : '';
		$title      = ( isset( $settings['title'] ) && '' !== $settings['title'] ) ? $settings['title'] : '';

		add_filter( 'yit_ctpw_force_upsells', '__return_true' );


		$args = array(
			'columns'           => esc_html( $p_per_row ),
			'orderby'           => $order_by,
			'order'             => $order,
			'ids'               => esc_html( $ids ),
			'skus'              => '',
			'products_per_page' => esc_html( $p_per_page ),
		);

		if ( ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) || ( isset( $_POST['action'] ) && $_POST['action'] === 'elementor_ajax' ) || ( isset( $_GET['ctpw'] ) && isset( $_GET['order-received'] ) ) ) {//phpcs:ignore
			$yctpw_f = new YITH_Custom_Thankyou_Page_Frontend_Premium();
			echo $yctpw_f->ctpw_show_products_shortcode( $args );//phpcs:ignore XSS.
		}
	}


}