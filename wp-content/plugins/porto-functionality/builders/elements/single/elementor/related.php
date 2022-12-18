<?php
/**
 * Porto Elementor Single Builder Related Widget
 *
 * @author     P-THEMES
 * @since      2.3.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;

class Porto_Elementor_Single_Related_Widget extends Porto_Elementor_Posts_Grid_Widget {

	public function get_name() {
		return 'porto_single_related';
	}

	public function get_title() {
		return esc_html__( 'Related Posts', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-single' );
	}

	public function get_keywords() {
		return array( 'single', 'custom', 'layout', 'post', 'related', 'linked', 'grid', 'member', 'portfolio', 'event', 'fap' );
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/related-posts/';
	}

	protected function register_controls() {
		parent::register_controls();

		$this->remove_control( 'source' );
		$this->remove_control( 'post_type' );
		$this->remove_control( 'tax' );
		$this->remove_control( 'terms' );
		$this->remove_control( 'post_terms' );
		$this->remove_control( 'post_tax' );
		$this->update_control(
			'orderby',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Order by', 'porto-functionality' ),
				'options'     => array_flip( array_slice( porto_vc_woo_order_by(), 1 ) ),
				'description' => __( 'Price, Popularity and Rating values only work for product post type.', 'porto-functionality' ),
				'condition'   => array(),
			)
		);
		$this->remove_control( 'pagination_style' );
		$this->remove_control( 'category_filter' );
	}

	protected function render() {
		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		echo PortoBuildersSingle::get_instance()->shortcode_single_related( $atts );
	}
}
