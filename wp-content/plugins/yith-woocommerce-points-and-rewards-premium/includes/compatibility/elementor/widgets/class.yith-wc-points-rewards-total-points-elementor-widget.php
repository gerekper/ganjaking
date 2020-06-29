<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;

class YITH_WC_Points_Rewards_Total_Points_Elementor_Widget extends \Elementor\Widget_Base {


	public function get_name() {
		return 'yith-points-and-rewards-total-points';
	}

	public function get_title() {
		return __( 'YITH Points and Rewards Total Points', 'yith-woocommerce-points-and-rewards' );
	}

	public function get_icon() {
		return 'eicon-rating';
	}

	public function get_categories() {
		return array( 'yith', 'general' );
	}

	public function get_keywords() {
		return array( 'woocommerce', 'shop', 'store', 'points', 'rewards', 'total points' );
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'YITH Points and Rewards Total Points', 'yith-woocommerce-points-and-rewards' ),
			)
		);

		$this->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'yith-woocommerce-ajax-search' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => false,
				),
				'default' => __( 'Your credit is ', 'yith-woocommerce-points-and-rewards' ),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$shortcode = do_shortcode( '[yith_ywpar_points label="' . $settings['label'] . '"]' );
		?>
		<div class="elementor-shortcode"><?php echo $shortcode; //phpcs:ignore ?></div>
		<?php

	}

}
