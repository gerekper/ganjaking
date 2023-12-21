<?php
/**
 * Post Grid widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Happy_Addons_Pro\Traits\Post_Grid_Markup;
use WP_Query;

defined( 'ABSPATH' ) || die();

class Post_Grid extends Base {

	use Lazy_Query_Builder;
	use Post_Grid_Markup;

    /**
	 * By setting this to false we can remove the "Default" option from
	 * skin dropdown. And the "Default" option indicates the widget itself.
	 *
	 * @var bool
	 */
    protected $_has_template_content = false;

	/**
	 * @var \WP_Query
	 */
    protected $query = null;


	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Post Grid', 'happy-addons-pro' );
	}

	public function show_in_panel() {
		return false;
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-post-grid';
	}

	public function get_keywords() {
		return ['post', 'posts', 'portfolio', 'grid', 'tiles', 'query', 'blog'];
	}

 	/**
	 * Register & Inculde Post Grid Skins
	 */
	protected function register_skins() {
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/skin-base.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/classic.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/hawai.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/standard.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/monastic.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/stylica.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/outbox.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/post-grid/skins/crossroad.php' );

		$this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Post_Grid\Classic( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Post_Grid\Hawai( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Post_Grid\Standard( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Post_Grid\Monastic( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Post_Grid\Stylica( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Post_Grid\Outbox( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Post_Grid\Crossroad( $this ) );

	}


	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {

		//Layout
		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
        );

        $this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columns', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'prefix_class' => 'ha-pg-grid%s-',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-grid-wrap' => 'grid-template-columns: repeat( {{VALUE}}, 1fr );',
				],
			]
		);


		$this->end_controls_section();

		//Query content
		$this->query_content_tab_controls();

		//Paginations content
		$this->pagination_content_tab_controls();

    }

	/**
	 * Query content controls
	 */
	protected function query_content_tab_controls( ) {

		//Query
		$this->start_controls_section(
			'_section_query',
			[
				'label' => __( 'Query', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_controls();

		$this->end_controls_section();

	}

	/**
	 * Paginations content controls
	 */
	protected function pagination_content_tab_controls( ) {

		//Pagination
		$this->start_controls_section(
			'_section_pagination',
			[
				'label' => __( 'Pagination & Load More', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label' => __( 'Pagination', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'happy-addons-pro' ),
					'numbers' => __( 'Numbers', 'happy-addons-pro' ),
					'prev_next' => __( 'Previous/Next', 'happy-addons-pro' ),
					'numbers_and_prev_next' => __( 'Numbers', 'happy-addons-pro' ) . ' + ' . __( 'Previous/Next', 'happy-addons-pro' ),
				],
			]
		);

		$this->add_control(
			'pagination_page_limit',
			[
				'label' => __( 'Page Limit', 'happy-addons-pro' ),
				'default' => '5',
				'condition' => [
					'pagination_type!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_numbers_shorten',
			[
				'label' => __( 'Shorten', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'pagination_type' => [
						'numbers',
						'numbers_and_prev_next',
					],
				],
			]
		);

		$this->add_control(
			'pagination_prev_label',
			[
				'label' => __( 'Previous Label', 'happy-addons-pro' ),
				'default' => __( '&laquo; Previous', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next',
					],
				],
			]
		);

		$this->add_control(
			'pagination_next_label',
			[
				'label' => __( 'Next Label', 'happy-addons-pro' ),
				'default' => __( 'Next &raquo;', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => [
						'prev_next',
						'numbers_and_prev_next',
					],
				],
			]
		);

		$this->add_control(
			'loadmore',
			[
				'label' => __( 'Load More Button', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'pagination_type' => '',
				],
			]
		);

		$this->add_control(
			'loadmore_text',
			[
				'label' => __( 'Load More Text', 'happy-addons-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Load More', 'happy-addons-pro' ),
				'condition' => [
					'pagination_type' => '',
					'loadmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_align',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				// 'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap' => 'text-align: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'pagination_type',
							'operator' => '!=',
							'value' => '',

						],
						[
							'name' => 'loadmore',
							'operator' => '===',
							'value' => 'yes',

						],
					],
				],
			]
		);

		$this->end_controls_section();

	}


	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {

		//Laout Style Start
		$this->layout_style_tab_controls();

		//Pagination Style Start
		$this->pagination_style_tab_controls();

	}


	/**
	 * Layout Style controls
	 */
	protected function layout_style_tab_controls() {

		$this->start_controls_section(
			'_section_layout_style',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __( 'Columns Gap', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-grid-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => __( 'Rows Gap', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-grid-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		/* $this->add_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				//'prefix_class' => 'elementor-posts--align-',
			]
		); */

		$this->end_controls_section();
    }


	/**
	 * Paginations Style controls
	 */
	protected function pagination_style_tab_controls( ) {

		$this->start_controls_section(
			'_section_pagination_style',
			[
				'label' => __( 'Pagination', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'pagination_type',
							'operator' => '!=',
							'value' => '',

						],
						[
							'name' => 'loadmore',
							'operator' => '===',
							'value' => 'yes',

						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'pagination_margin',
			[
				'label' => __( 'Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				// 'separator' => 'before',
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination_type!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'selector' => '{{WRAPPER}} .ha-pg-pagination-wrap, {{WRAPPER}} .ha-pg-loadmore-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers, {{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore',
				'exclude' => ['color'],
			]
		);

		$this->add_responsive_control(
			'pagination_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				// 'condition' => [
				// 	'navigation_show' => 'yes',
				// ]
			]
		);

		$this->start_controls_tabs( 'pagination_tabs' );

		$this->start_controls_tab(
			'pagination_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_bg_color',
			[
				'label' => __( 'Background', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_hover',
			[
				'label' => __( 'Hover & Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers.current' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_bg_hover_color',
			[
				'label' => __( 'Background', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers.current' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_border_hover_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers:not([class~=dots]):hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-pagination-wrap .page-numbers.current' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-pg-loadmore-wrap .ha-pg-loadmore:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get Query
	 *
	 * @param array $args
	 * @return void
	 */
	public function get_query( $args = array() ) {

		$default = $this->get_post_query_args();
		$args = array_merge( $default, $args );

		$this->query = new WP_Query( $args );
		return $this->query;
	}

	/**
	 * Get post query arguments
	 *
	 * @return function
	 */
	public function get_post_query_args() {

		return $this->get_query_args();
	}

	/**
	 * Get current page number
	 *
	 * @return init
	 */
	public function get_current_page() {
		if ( '' === $this->get_settings_for_display( 'pagination_type' ) ) {
			return 1;
		}

		return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	}

	/**
	 * Get page number link
	 *
	 * @param [init] $i
	 * @return string
	 */
	private function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
		global $wp_rewrite;
		$post = get_post();
		$query_args = [];
		$url = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, [ 'draft', 'pending' ] ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( 'page'.$i, 'single_paged' ); // Change Occurs For Fixing Pagination Issue.
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$query_args['preview_id'] = wp_unslash( $_GET['preview_id'] );
				$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	/**
	 * Get post navigation link
	 *
	 * @param [init] $page_limit
	 * @return string
	 */
	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			// return;
			$page_limit = $this->query->max_num_pages; // Change Occurs For Fixing Pagination Issue.
		}

		$return = [];

		// $paged = $this->get_current_page();
		$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

		$link_template = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_prev_label' ) );
		}
		// else {
		// 	$return['prev'] = sprintf( $disabled_template, 'prev', $this->get_settings_for_display( 'pagination_prev_label' ) );
		// }

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->get_settings_for_display( 'pagination_next_label' ) );
		}
		// else {
		// 	$return['next'] = sprintf( $disabled_template, 'next', $this->get_settings_for_display( 'pagination_next_label' ) );
		// }

		return $return;
	}

	/**
	 * Pagination render
	 *
	 * @param [array] $_query
	 * @return void
	 */
	public function pagination_render($_query) {

		$parent_settings = $this->get_settings_for_display();
		if ( '' === $parent_settings['pagination_type'] ) {
			return;
		}

		$page_limit = $_query->max_num_pages;
		if ( '' !== $parent_settings['pagination_page_limit'] ) {
			$page_limit = min( $parent_settings['pagination_page_limit'], $page_limit );
		}

		if ( 2 > $page_limit ) {
			return;
		}

		$has_numbers = in_array( $parent_settings['pagination_type'], [ 'numbers', 'numbers_and_prev_next' ] );
		$has_prev_next = in_array( $parent_settings['pagination_type'], [ 'prev_next', 'numbers_and_prev_next' ] );

		$links = [];

		if ( $has_numbers ) {
			$paginate_args = [
				'type' => 'array',
				'current' => $this->get_current_page(),
				'total' => $page_limit,
				'prev_next' => false,
				'show_all' => 'yes' !== $parent_settings['pagination_numbers_shorten'],
				'before_page_number' => '<span class="elementor-screen-only">' . __( 'Page', 'happy-addons-pro' ) . '</span>',
			];

			if ( is_singular() && ! is_front_page() ) {
				global $wp_rewrite;
				if ( $wp_rewrite->using_permalinks() ) {
					$paginate_args['base'] = trailingslashit( get_permalink() ) . '%_%';
					$paginate_args['format'] = user_trailingslashit( 'page%#%', 'single_paged' ); // Change Occurs For Fixing Pagination Issue.
				} else {
					$paginate_args['format'] = '?page=%#%';
				}
			}

			$links = paginate_links( $paginate_args );
		}

		if ( $has_prev_next ) {
			$prev_next = $this->get_posts_nav_link( $page_limit );
			if(isset($prev_next['prev'])) {
				array_unshift( $links, $prev_next['prev'] );
			}
			if(isset($prev_next['next'])) {
				$links[] = $prev_next['next'];
			}
		}

		?>
		<nav class="ha-pg-pagination-wrap" role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'happy-addons-pro' ); ?>">
			<?php echo implode( PHP_EOL, $links ); ?>
		</nav>
		<?php
	}

	/**
	 * Load more render
	 *
	 * @param [array] $query_settings
	 * @return void
	 */
	public function load_more_render( $query_settings ) {

		$settings = $this->get_settings_for_display();
		if ( empty($settings['loadmore']) || empty($settings['loadmore_text']) ) {
			return;
		}
		?>
		<div class="ha-pg-loadmore-wrap">
			<button class="ha-pg-loadmore" data-settings="<?php echo esc_attr($query_settings);?>">
				<?php echo esc_html($settings['loadmore_text']);?>
				<i class="eicon-loading eicon-animation-spin"></i>
			</button>
		</div>
		<?php
	}

	/**
	 * render content
	 *
	 * @return void
	 */
    protected function render() {}

}
