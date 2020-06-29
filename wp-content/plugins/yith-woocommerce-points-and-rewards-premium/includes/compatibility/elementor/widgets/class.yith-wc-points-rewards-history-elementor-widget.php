<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;

class YITH_WC_Points_Rewards_History_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-points-and-rewards-history';
	}

	public function get_title() {
		return __( 'YITH Points and Rewards History', 'yith-woocommerce-points-and-rewards' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return array( 'yith', 'general' );
	}

	public function get_keywords() {
		return array( 'woocommerce', 'shop', 'store', 'points', 'rewards', 'history' );
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'YITH Points and Rewards History', 'yith-woocommerce-points-and-rewards' ),
			)
		);
		$this->add_control(
			'wc_style_warning',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This widget shows point history to logged-in users.', 'yith-woocommerce-points-and-rewards' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$shortcode = do_shortcode( '[ywpar_my_account_points]' );
		?>
		<div class="elementor-shortcode"><?php echo $shortcode; //phpcs:ignore ?></div>
		<?php

	}

}
