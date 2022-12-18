<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Archive Builder Posts Grid widget
 *
 * @since 2.3.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
class Porto_Elementor_Archive_Posts_Grid_Widget extends Porto_Elementor_Posts_Grid_Widget {

	public $post_types = '';

	public function get_name() {
		return 'porto_archive_posts_grid';
	}

	public function get_title() {
		return __( 'Archive Posts Grid', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-archive' );
	}

	public function get_keywords() {
		return array( 'post', 'product', 'shop', 'term', 'category', 'taxonomy', 'type', 'card', 'builder', 'custom', 'archive', 'portfolio', 'event', 'member' );
	}

	protected function register_controls() {
		parent::register_controls();

		$this->remove_control( 'source' );
		$this->remove_control( 'post_type' );
		$this->remove_control( 'post_tax' );
		$this->remove_control( 'post_terms' );
		$this->remove_control( 'tax' );
		$this->remove_control( 'filter_cat_tax' );
		$this->remove_control( 'terms' );

		$this->update_control(
			'count',
			array(
				'type'  => Controls_Manager::SLIDER,
				'label' => __( 'Count (per page)', 'porto-functionality' ),
				'range' => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 100,
					),
				),
			)
		);

		$this->update_control(
			'pagination_style',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Pagination Type', 'porto-functionality' ),
				'options'   => array(
					''          => __( 'No pagination', 'porto-functionality' ),
					'ajax'      => __( 'Ajax Pagination', 'porto-functionality' ),
					'infinite'  => __( 'Infinite Scroll', 'porto-functionality' ),
					'load_more' => __( 'Load more', 'porto-functionality' ),
				),
				'condition' => array(),
			)
		);

		$this->update_control(
			'orderby_term',
			array(
				'type'      => Controls_Manager::SELECT,
				'label'     => __( 'Order by', 'porto-functionality' ),
				'options'   => array(
					''            => __( 'Default', 'porto-functionality' ),
					'name'        => __( 'Title', 'porto-functionality' ),
					'term_id'     => __( 'ID', 'porto-functionality' ),
					'count'       => __( 'Post Count', 'porto-functionality' ),
					'none'        => __( 'None', 'porto-functionality' ),
					'parent'      => __( 'Parent', 'porto-functionality' ),
					'description' => __( 'Description', 'porto-functionality' ),
					'term_group'  => __( 'Term Group', 'porto-functionality' ),
				),
				'default'   => '',
				'condition' => array(),
			)
		);

		$this->remove_control( 'category_filter' );

		$this->start_controls_section(
			'filter_posts',
			array(
				'label' => __( 'Filter Posts', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'category_filter',
			array(
				'type'      => Controls_Manager::SWITCHER,
				'label'     => __( 'Filter By Taxonomy', 'porto-functionality' ),
				'condition' => array(),
			)
		);

		$this->add_control(
			'filter_cat_tax',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Taxonomy', 'porto-functionality' ),
				'description' => __( 'Please select a post taxonomy to be used as category filter.', 'porto-functionality' ),
				'options'     => '%archive_builder%_alltax',
				'label_block' => true,
				'condition'   => array(
					'category_filter' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'post_advanced_section',
			array(
				'label' => esc_html__( 'Advanced', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
			$this->add_control(
				'post_found_nothing',
				array(
					'type'    => Controls_Manager::TEXTAREA,
					'label'   => esc_html__( 'Nothing Found Message', 'porto-functionality' ),
					'default' => __( 'It seems we can\'t find what you\'re looking for.', 'porto-functionality' ),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'post_advanced_style',
			array(
				'label'     => esc_html__( 'Found Nothing', 'porto-functionality' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'post_found_nothing!' => '',
				),
			)
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'nothing_msg_typography',
					'label'    => esc_html__( 'Typography', 'porto-functionality' ),
					'selector' => '.elementor-element-{{ID}} .nothing-found-message',
				)
			);

			$this->add_control(
				'nothing_msg_color',
				array(
					'label'       => esc_html__( 'Color', 'porto-functionality' ),
					'description' => esc_html__( 'Controls the color of the message.', 'porto-functionality' ),
					'type'        => Controls_Manager::COLOR,
					'selectors'   => array(
						'.elementor-element-{{ID}} .nothing-found-message' => 'color: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	protected function render() {

		$atts                 = $this->get_settings_for_display();
		$atts['page_builder'] = 'elementor';
		echo PortoBuildersArchive::get_instance()->shortcode_archive_posts_grid( $atts );
	}
}
