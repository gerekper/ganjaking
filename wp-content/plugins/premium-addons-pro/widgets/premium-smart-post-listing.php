<?php
/**
 * Premium Smart Post Listing.
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use Elementor\Core\Base\Document;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Controls\Premium_Tax_Filter;
use PremiumAddons\Includes\Controls\Premium_Post_Filter;
use PremiumAddons\Includes\Premium_Template_Tags as Posts_Helper;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Smart_Post_Listing.
 */
class Premium_Smart_Post_Listing extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-smart-post-listing';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Smart Post Listing', 'premium-addons-pro' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-post-listing';
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'magazine', 'news', 'posts', 'listing', 'custom', 'grid', 'cpt', 'query', 'loop', 'blog' );
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'font-awesome-5-all',
			'pa-slick',
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'imagesloaded',
			'isotope-js',
			'pa-slick',
			'pa-magazine',
		);
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Smart Post Listing controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_content_tab_controls();
		$this->register_style_tab_controls();

	}

	/**
	 * Adds content tab controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function register_content_tab_controls() {

		$this->add_layout_section_controls();
		$this->add_general_section_controls();
		$this->add_query_section_controls();
		$this->add_header_section_controls();
		$this->add_featured_post_section_controls();
		$this->add_posts_section_controls();
		$this->add_navigation_section_controls();
	}

	/**
	 * Adds style tab controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function register_style_tab_controls() {

		$this->add_img_common_style();

		$this->add_header_style_controls();

		$this->add_fitlers_style();

		$this->add_categories_style();

		$this->add_featured_posts_style();

		$this->add_featured_readmore_style();

		$this->add_posts_style();

		$this->add_posts_readmore_style();

		$this->add_pagination_style();

		$this->add_load_more_btn_style();

		$this->add_loader_style();
	}

	/**
	 * Adds General controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_layout_section_controls() {

		$this->start_controls_section(
			'pa_spl_layout_section',
			array(
				'label' => __( 'Layout', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pa_spl_skin',
			array(
				'label'              => __( 'Grid', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'skin-1' => __( 'Grid 1', 'premium-addons-pro' ),
					'skin-2' => __( 'Grid 2', 'premium-addons-pro' ),
					'skin-3' => __( 'Grid 3', 'premium-addons-pro' ),
					'custom' => __( 'Custom Grid', 'premium-addons-pro' ),
				),
				'default'            => 'skin-1',
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		// this version should be edited to the release version.
		$add_custom_loop_temp = version_compare( PREMIUM_ADDONS_VERSION, '4.10.0', '>=' ) && Helper_Functions::is_loop_exp_enabled();

		if ( $add_custom_loop_temp ) {

			$this->add_control(
				'live_temp_content',
				array(
					'label'       => __( 'Template Title', 'premium-addons-pro' ),
					'type'        => Controls_Manager::TEXT,
					'classes'     => 'premium-live-temp-title control-hidden ',
					'label_block' => true,
					'condition'   => array(
						'pa_spl_skin' => 'custom',
					),
				)
			);

			$this->add_control(
				'temp_content_live',
				array(
					'type'        => Controls_Manager::BUTTON,
					'label_block' => true,
					'button_type' => 'default papro-btn-block grid-temp',
					'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
					'event'       => 'createLiveTemp',
					'condition'   => array(
						'pa_spl_skin' => 'custom',
					),
				)
			);

			$this->add_control(
				'pa_grid_live_temp_id',
				array(
					'label'     => __( 'Live Temp ID', 'premium-addons-pro' ),
					'type'      => Controls_Manager::HIDDEN,
					'condition' => array(
						'pa_spl_skin' => 'custom',
					),
				)
			);

			$this->add_control(
				'pa_grid_template_id',
				array(
					'label'              => __( 'OR Select Existing Template', 'premium-addons-pro' ),
					'type'               => Template_Query::CONTROL_ID,
					'classes'            => 'premium-live-temp-label',
					'label_block'        => true,
					'autocomplete'       => array(
						'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
						'query'  => array(
							'post_status' => Document::STATUS_PUBLISH,
							'meta_query'  => array(
								array(
									'key'     => Document::TYPE_META_KEY,
									'value'   => 'premium-grid',
									'compare' => 'IN',
								),
							),
						),
					),
					'actions'            => array(
						'new'  => array(
							'visible'         => true,
							'document_config' => array(
								'type' => 'premium-grid',
							),
						),
						'edit' => array(
							'visible' => true,
						),
					),
					'frontend_available' => true,
					'condition'          => array(
						'pa_spl_skin' => 'custom',
					),
				)
			);

		} else {
			$this->add_control(
				'custom_loop_temp_notice',
				array(
					'raw'             => __( 'Custom Grid option requires Elementor PRO ( version 3.8.0 or higher ) & Loop Expirement to be activated.', 'premium-addons-pro' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'pa_spl_skin' => 'custom',
					),
				)
			);
		}

		$this->add_control(
			'premium_blog_number_of_posts',
			array(
				'label'   => __( 'Posts Per Page', 'premium-addons-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 4,
			)
		);

		$this->add_control(
			'display_featured_posts',
			array(
				'label'        => __( 'Featured Posts', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-no-featured-',
				'render_type'  => 'template',
				'label_on'     => __( 'Yes', 'premium-addons-pro' ),
				'label_off'    => __( 'No', 'premium-addons-pro' ),
				'default'      => 'yes',
				'condition'    => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$devices = Helper_Functions::get_all_breakpoints( 'keys' );

		$this->add_responsive_control(
			'listing_cols',
			array(
				'label'              => __( 'Listing Columns', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'prefix_class'       => 'premium-smart-listing__',
				'options'            => array(
					'1' => __( '1', 'premium-addons-pro' ),
					'2' => __( '2', 'premium-addons-pro' ),
					'3' => __( '3', 'premium-addons-pro' ),
					'4' => __( '4', 'premium-addons-pro' ),
					'5' => __( '5', 'premium-addons-pro' ),
				),
				'devices'            => array_diff( $devices, array( 'mobile', 'mobile_extra' ) ),
				'selectors'          => array(
					'{{WRAPPER}}:not(.premium-carousel-yes) .premium-smart-listing__posts-wrapper'  => 'grid-template-columns: repeat({{VALUE}},1fr);',
				),
				'conditions'         => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'pa_spl_skin',
							'operator' => '!==',
							'value'    => 'custom',
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'pa_spl_skin',
									'operator' => '===',
									'value'    => 'custom',
								),
								array(
									'name'     => 'carousel',
									'operator' => '===',
									'value'    => 'yes',
								),
								array(
									'name'     => 'premium_blog_paging',
									'operator' => '!==',
									'value'    => 'yes',
								),
							),
						),
					),
				),
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'grid_spacing',
			array(
				'label'      => __( 'Grid Spacing (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__posts-outer-wrapper'  => 'column-gap: {{SIZE}}px; row-gap: {{SIZE}}px;',
				),
				'condition'  => array(
					'display_featured_posts' => 'yes',
					'pa_spl_skin!'           => 'custom',
				),
			)
		);

		$this->add_control(
			'posts_layout',
			array(
				'label'     => __( 'Listed Posts Grid', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'row_spacing',
			array(
				'label'      => __( 'Row Spacing (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__posts-wrapper'  => 'row-gap: {{SIZE}}px;',
					'{{WRAPPER}} .slick-slide > div:not(:last-child) .premium-smart-listing__post-wrapper'  => 'margin-bottom: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'outer_col_spacing',
			array(
				'label'      => __( 'Column Spacing (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__posts-wrapper'  => 'column-gap: {{SIZE}}px;',
				),
				'condition'  => array(
					'post_img'      => 'yes',
					'listing_cols!' => array( '', '1' ),
					'pa_spl_skin!'  => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'col_spacing',
			array(
				'label'      => __( 'Post Inner Spacing (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__posts-outer-wrapper:not(.premium-smart-listing__skin-3) .premium-smart-listing__post-wrapper'  => 'column-gap: {{SIZE}}px;',
					'{{WRAPPER}} .premium-smart-listing__posts-outer-wrapper.premium-smart-listing__skin-3 .premium-smart-listing__post-wrapper'  => 'row-gap: {{SIZE}}px;',
				),
				'condition'  => array(
					'post_img'     => 'yes',
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_general_section_controls() {

		$this->start_controls_section(
			'pa_spl_gen_section',
			array(
				'label' => __( 'General', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'img_hover_effect',
			array(
				'label'        => __( 'Hover Effect', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-smart-listing__',
				'options'      => array(
					'none'    => __( 'None', 'premium-addons-pro' ),
					'zoomin'  => __( 'Zoom In', 'premium-addons-pro' ),
					'zoomout' => __( 'Zoom Out', 'premium-addons-pro' ),
					'scale'   => __( 'Scale', 'premium-addons-pro' ),
					'gray'    => __( 'Grayscale', 'premium-addons-pro' ),
					'sepia'   => __( 'Sepia', 'premium-addons-pro' ),
					'trans'   => __( 'Translate', 'premium-addons-pro' ),
				),
				'default'      => 'zoomin',
				'condition'    => array(
					'display_featured_posts' => 'yes',
					'pa_spl_skin!'           => 'custom',
				),
			)
		);

		$this->add_control(
			'loading_animation',
			array(
				'label'        => __( 'Loading Animation', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'premium-loading-animation__slide-',
				'options'      => array(
					'none'  => __( 'None', 'premium-addons-pro' ),
					'up'    => __( 'Slide Up', 'premium-addons-pro' ),
					'down'  => __( 'Slide Down', 'premium-addons-pro' ),
					'left'  => __( 'Slide Left', 'premium-addons-pro' ),
					'right' => __( 'Slide Right', 'premium-addons-pro' ),
				),
				'default'      => 'up',
			)
		);

		$this->add_control(
			'loading_animation_dur',
			array(
				'label'     => __( 'Animation Duration (ms)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => '0',
				'condition' => array(
					'loading_animation!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__grid-item'  => 'animation-duration: {{VALUE}}ms;',
				),
			)
		);

		$this->add_control(
			'new_tab',
			array(
				'label'     => __( 'Open Post Link in New Tab', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_control(
			'article_tag_switcher',
			array(
				'label'     => __( 'Change Post HTML Tag To Article', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_control(
			'infinite_scroll',
			array(
				'label'              => __( 'Load More Posts On Scroll', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'pa_spl_skin'          => array( 'skin-2', 'skin-3', 'custom' ),
					'premium_blog_paging!' => 'yes',
					'post_type_filter!'    => 'related',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'load_more_button',
			array(
				'label'              => __( 'Load More Button', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'pa_spl_skin'          => array( 'skin-2', 'skin-3', 'custom' ),
					'premium_blog_paging!' => 'yes',
					'post_type_filter!'    => 'related',
					'infinite_scroll!'     => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'load_more_txt',
			array(
				'label'     => __( 'Load More Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Load More',
				'condition' => array(
					'pa_spl_skin'          => array( 'skin-2', 'skin-3', 'custom' ),
					'premium_blog_paging!' => 'yes',
					'post_type_filter!'    => 'related',
					'infinite_scroll!'     => 'yes',
					'load_more_button'     => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_blog_title_tag',
			array(
				'label'     => __( 'Post Title HTML Tag', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h4',
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_control(
			'meta_separator',
			array(
				'label'     => __( 'Post Meta Separator', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '⋅',
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'meta_spacing',
			array(
				'label'      => __( 'Post Meta Spacing (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-meta-container'  => 'column-gap: {{SIZE}}px;',
				),
				'condition'  => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_control(
			'scroll_to_offset',
			array(
				'label'              => __( 'Scroll After Pagination/Filter', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'description'        => __( 'Enable this option to scroll to top offset of the widget after click pagination or filter tabs.', 'premium-addons-for-ele,entor' ),
				'default'            => 'yes',
				'conditions'         => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'post_type_filter',
							'operator' => '!==',
							'value'    => 'related',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'filter_tabs',
									'value' => 'yes',
								),
								array(
									'name'  => 'premium_blog_paging',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'separator'          => 'before',
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds query controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_query_section_controls() {

		$this->start_controls_section(
			'pa_spl_query_section',
			array(
				'label' => __( 'Query', 'premium-addons-pro' ),
			)
		);

		$post_types = Posts_Helper::get_posts_types();

		$this->add_control(
			'post_type_filter',
			array(
				'label'       => __( 'Source', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array_merge(
					$post_types,
					array(
						'related' => 'Related',
					)
				),
				'default'     => 'post',
			)
		);

		foreach ( $post_types as $key => $type ) {

			// Get all the taxanomies associated with the selected post type.
			$taxonomy = Posts_Helper::get_taxnomies( $key );

			if ( ! empty( $taxonomy ) ) {

				// Get all taxonomy values under the taxonomy.
				foreach ( $taxonomy as $index => $tax ) {

					$terms = get_terms( $index, array( 'hide_empty' => false ) );

					$related_tax = array();

					if ( ! empty( $terms ) ) {

						foreach ( $terms as $t_index => $t_obj ) {

							$related_tax[ $t_obj->slug ] = $t_obj->name;
						}

						// Add filter rule for the each taxonomy.
						$this->add_control(
							$index . '_' . $key . '_filter_rule',
							array(
								/* translators: %s Taxnomy Label */
								'label'       => sprintf( __( '%s Filter Rule', 'premium-addons-pro' ), $tax->label ),
								'type'        => Controls_Manager::SELECT,
								'default'     => 'IN',
								'label_block' => true,
								'options'     => array(
									/* translators: %s: Taxnomy Label */
									'IN'     => sprintf( __( 'Match %s', 'premium-addons-pro' ), $tax->label ),
									/* translators: %s: Taxnomy Label */
									'NOT IN' => sprintf( __( 'Exclude %s', 'premium-addons-pro' ), $tax->label ),
								),
								'condition'   => array(
									'post_type_filter' => $key,
								),
							)
						);

						// Add select control for each taxonomy.
						$this->add_control(
							'tax_' . $index . '_' . $key . '_filter',
							array(
								/* translators: %s Taxnomy Label */
								'label'       => sprintf( __( '%s Filter', 'premium-addons-pro' ), $tax->label ),
								'type'        => Controls_Manager::SELECT2,
								'default'     => '',
								'multiple'    => true,
								'label_block' => true,
								'options'     => $related_tax,
								'condition'   => array(
									'post_type_filter' => $key,
								),
							)
						);

					}
				}
			}
		}

		$this->add_control(
			'author_filter_rule',
			array(
				'label'       => __( 'Filter By Author Rule', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'author__in',
				'label_block' => true,
				'options'     => array(
					'author__in'     => __( 'Match Authors', 'premium-addons-pro' ),
					'author__not_in' => __( 'Exclude Authors', 'premium-addons-pro' ),
				),
			)
		);

		$this->add_control(
			'premium_blog_users',
			array(
				'label'       => __( 'Authors', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Posts_Helper::get_authors(),
			)
		);

		$this->add_control(
			'posts_filter_rule',
			array(
				'label'       => __( 'Filter By Post Rule', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'post__not_in',
				'label_block' => true,
				'options'     => array(
					'post__in'     => __( 'Match Post', 'premium-addons-pro' ),
					'post__not_in' => __( 'Exclude Post', 'premium-addons-pro' ),
				),
				'condition'   => array(
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_control(
			'premium_blog_posts_exclude',
			array(
				'label'       => __( 'Posts', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => Posts_Helper::get_default_posts_list( 'post' ),
				'condition'   => array(
					'post_type_filter' => 'post',
				),
			)
		);

		$this->add_control(
			'custom_posts_filter',
			array(
				'label'              => __( 'Posts', 'premium-addons-pro' ),
				'type'               => Premium_Post_Filter::TYPE,
				'render_type'        => 'template',
				'label_block'        => true,
				'multiple'           => true,
				'frontend_available' => true,
				'condition'          => array(
					'post_type_filter!' => array( 'post', 'related' ),
				),

			)
		);

		$this->add_control(
			'ignore_sticky_posts',
			array(
				'label'     => __( 'Ignore Sticky Posts', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'premium-addons-pro' ),
				'label_off' => __( 'No', 'premium-addons-pro' ),
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'premium_blog_offset',
			array(
				'label'       => __( 'Offset', 'premium-addons-pro' ),
				'description' => __( 'This option is used to exclude number of initial posts from being display.', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '0',
				'min'         => '0',
			)
		);

		$this->add_control(
			'query_exclude_current',
			array(
				'label'       => __( 'Exclude Current Post', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option will remove the current post from the query.', 'premium-addons-pro' ),
				'label_on'    => __( 'Yes', 'premium-addons-pro' ),
				'label_off'   => __( 'No', 'premium-addons-pro' ),
				'condition'   => array(
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_control(
			'premium_blog_order_by',
			array(
				'label'       => __( 'Order By', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'none'          => __( 'None', 'premium-addons-pro' ),
					'ID'            => __( 'ID', 'premium-addons-pro' ),
					'author'        => __( 'Author', 'premium-addons-pro' ),
					'title'         => __( 'Title', 'premium-addons-pro' ),
					'name'          => __( 'Name', 'premium-addons-pro' ),
					'date'          => __( 'Date', 'premium-addons-pro' ),
					'modified'      => __( 'Last Modified', 'premium-addons-pro' ),
					'rand'          => __( 'Random', 'premium-addons-pro' ),
					'menu_order'    => __( 'Menu Order', 'premium-addons-for-elementor' ),
					'comment_count' => __( 'Number of Comments', 'premium-addons-pro' ),
				),
				'default'     => 'date',
			)
		);

		$this->add_control(
			'premium_blog_order',
			array(
				'label'       => __( 'Order', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'DESC' => __( 'Descending', 'premium-addons-pro' ),
					'ASC'  => __( 'Ascending', 'premium-addons-pro' ),
				),
				'default'     => 'DESC',
			)
		);

		$this->add_control(
			'empty_query_text',
			array(
				'label'       => __( 'Empty Query Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds Header controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_header_section_controls() {

		$this->start_controls_section(
			'pa_spl_header_section',
			array(
				'label' => __( 'Posts Header', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'posts_title',
			array(
				'label'       => __( 'Posts Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'posts_title_tag',
			array(
				'label'       => __( 'Title HTML Tag', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h4',
				'options'     => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'label_block' => true,
				'condition'   => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_control(
			'filter_tabs',
			array(
				'label'              => __( 'Filter Tabs', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'carousel!' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'filter_tabs_type',
			array(
				'label'     => __( 'Get Tabs From', 'premium-addons-pro' ),
				'type'      => Premium_Tax_Filter::TYPE,
				'default'   => 'category',
				'condition' => array(
					'filter_tabs'       => 'yes',
					'carousel!'         => 'yes',
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_control(
			'filter_tabs_notice',
			array(
				'raw'             => __( 'Please make sure to select the categories/tags you need to show from Query tab.', 'premium-addons-for-elemeentor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'filter_tabs'       => 'yes',
					'carousel!'         => 'yes',
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_control(
			'first_tab_label',
			array(
				'label'     => __( 'First Tab Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'All', 'premium-addons-pro' ),
				'condition' => array(
					'filter_tabs'       => 'yes',
					'carousel!'         => 'yes',
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_control(
			'wrap_tabs_sw',
			array(
				'label'              => __( 'Wrap Filter Tabs', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array(
					'filter_tabs'       => 'yes',
					'carousel!'         => 'yes',
					'post_type_filter!' => 'related',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'filter_tabs_num',
			array(
				'label'       => __( 'Number Of Visible Tabs', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'render_type' => 'template',
				'condition'   => array(
					'filter_tabs'       => 'yes',
					'carousel!'         => 'yes',
					'wrap_tabs_sw'      => 'yes',
					'post_type_filter!' => 'related',
				),
				// 'frontend_available' => true,
				'selectors'   => array(
					'{{WRAPPER}} .premium-smart-listing__header-wrapper'  => '--premium-spl-filters: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wrap_icon',
			array(
				'label'       => __( 'Wrap Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => array(
					'value'   => 'fas fa-ellipsis-h',
					'library' => 'fa-solid',
				),
				'skin'        => 'inline',
				'condition'   => array(
					'filter_tabs'       => 'yes',
					'wrap_tabs_sw'      => 'yes',
					'carousel!'         => 'yes',
					'post_type_filter!' => 'related',
				),
				'label_block' => false,
			)
		);

		$this->add_control(
			'tabs_wrap_label',
			array(
				'label'     => __( 'Wrap Label', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'filter_tabs'       => 'yes',
					'wrap_tabs_sw'      => 'yes',
					'carousel!'         => 'yes',
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_control(
			'filters_spacing',
			array(
				'label'      => __( 'Filter Tabs Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs'  => 'column-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'filter_tabs'       => 'yes',
					'carousel!'         => 'yes',
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_responsive_control(
			'header_alignment',
			array(
				'label'      => __( 'Alignment', 'premium-addons-pro' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'    => 'flex-start',
				'toggle'     => false,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'filter_tabs',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'name'     => 'posts_title',
									'operator' => '!==',
									'value'    => '',
								),
								array(
									'relation' => 'and',
									'terms'    => array(
										array(
											'name'     => 'premium_blog_paging',
											'operator' => '===',
											'value'    => 'yes',
										),
										array(
											'name'     => 'pagination_pos',
											'operator' => '===',
											'value'    => 'bottom',
										),
									),
								),
							),
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'  => 'filter_tabs',
									'value' => 'yes',
								),
								array(
									'name'  => 'posts_title',
									'value' => '',
								),
								array(
									'relation' => 'and',
									'terms'    => array(
										array(
											'name'     => 'premium_blog_paging',
											'operator' => '===',
											'value'    => 'yes',
										),
										array(
											'name'     => 'pagination_pos',
											'operator' => '===',
											'value'    => 'bottom',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__header-wrapper' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds Featured Posts controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_featured_post_section_controls() {

		$this->start_controls_section(
			'pa_spl_featured_posts_section',
			array(
				'label'     => __( 'Featured Posts', 'premium-addons-pro' ),
				'condition' => array(
					'display_featured_posts' => 'yes',
					'pa_spl_skin!'           => 'custom',
				),
			)
		);

		$this->add_control(
			'featured_post_default',
			array(
				'label'       => __( 'Posts', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => false,
				'options'     => Posts_Helper::get_default_posts_list( 'post' ),
				'condition'   => array(
					'post_type_filter' => 'post',
				),
			)
		);

		$this->add_control(
			'featured_post',
			array(
				'label'              => __( 'Post', 'premium-addons-pro' ),
				'type'               => Premium_Post_Filter::TYPE,
				'render_type'        => 'template',
				'label_block'        => true,
				'multiple'           => false,
				'frontend_available' => true,
				'condition'          => array(
					'post_type_filter!'      => 'post',
					'display_featured_posts' => 'yes',
					'pa_spl_skin'            => 'skin-1',
				),

			)
		);

		$this->add_responsive_control(
			'pa_featured_area_width',
			array(
				'label'      => __( 'Featured Area Size (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__skin-1'  => 'grid-template-columns: [line1] {{SIZE}}px [line2] auto [line3];',
					// '{{WRAPPER}} .premium-smart-listing__skin-2 .premium-smart-listing__featured-post-wrapper' => 'min-height:{{SIZE}}px'
				),
				'condition'  => array(
					'pa_spl_skin' => array( 'skin-1' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'featured_image',
				'default' => 'full',
			)
		);

		$this->add_control(
			'pa_featured_img_height',
			array(
				'label'      => __( 'Minimum Height (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__skin-1 .premium-smart-listing__featured-post-wrapper,{{WRAPPER}} .premium-smart-listing__skin-2 .premium-smart-listing__featured-post-wrapper, {{WRAPPER}} .premium-smart-listing__skin-3 .premium-smart-listing__featured-post-wrapper'  => 'min-height: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'featured_meta_controls_heading',
			array(
				'label'     => __( 'Post Options', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pa_featured_excerpt',
			array(
				'label'   => __( 'Show Post Content', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'pa_featured_content_source',
			array(
				'label'       => __( 'Get Content From', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'excerpt' => __( 'Post Excerpt', 'premium-addons-pro' ),
					'full'    => __( 'Post Full Content', 'premium-addons-pro' ),
				),
				'default'     => 'excerpt',
				'label_block' => true,
				'condition'   => array(
					'pa_featured_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_featured_excerpt_length',
			array(
				'label'     => __( 'Excerpt Length ( Words )', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'condition' => array(
					'pa_featured_excerpt'        => 'yes',
					'pa_featured_content_source' => 'excerpt',
				),
			)
		);

		$this->add_control(
			'pa_featured_excerpt_type',
			array(
				'label'       => __( 'Excerpt Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'dots' => __( 'Dots', 'premium-addons-pro' ),
					'link' => __( 'Link', 'premium-addons-pro' ),
				),
				'default'     => 'dots',
				'label_block' => true,
				'condition'   => array(
					'pa_featured_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_featured_read_more_full_width',
			array(
				'label'        => __( 'Full Width', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-smart-listing__featured-cta-full-',
				'condition'    => array(
					'pa_featured_excerpt'      => 'yes',
					'pa_featured_excerpt_type' => 'link',
				),
			)
		);

		$this->add_control(
			'pa_featured_excerpt_text',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-pro' ),
				'condition' => array(
					'pa_featured_excerpt'      => 'yes',
					'pa_featured_excerpt_type' => 'link',
				),
			)
		);

		$this->add_control(
			'pa_featured_hide_content',
			array(
				'label'       => __( 'Hide Content On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'after',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'pa_featured_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_featured_meta_above_title',
			array(
				'label'     => __( 'Place Post Meta Above Post Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'premium-addons-pro' ),
				'label_off' => __( 'No', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pa_featured_author_meta',
			array(
				'label'   => __( 'Author Meta', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'pa_featured_date_meta',
			array(
				'label'   => __( 'Date Meta', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'pa_featured_categories_meta',
			array(
				'label'   => __( 'Categories Meta', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'pa_featured_comments_meta',
			array(
				'label' => __( 'Comments Meta', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'pa_featured_tags_meta',
			array(
				'label' => __( 'Tags Meta', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds Posts controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_posts_section_controls() {

		$this->start_controls_section(
			'pa_spl_posts_section',
			array(
				'label'     => __( 'Posts Options', 'premium-addons-pro' ),
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_control(
			'post_img',
			array(
				'label'   => __( 'Show Post Thumbnail', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'img_pos',
			array(
				'label'     => __( 'Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'0' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'1' => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => '0',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__skin-1 .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper,
					{{WRAPPER}} .premium-smart-listing__skin-2 .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper' => 'order: {{VALUE}};',
				),
				'condition' => array(
					'post_img'     => 'yes',
					'pa_spl_skin!' => array( 'skin-3' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image',
				'default'   => 'full',
				'condition' => array(
					'post_img' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'img_width',
			array(
				'label'      => __( 'Width (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__posts-wrapper .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper'  => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'post_img'     => 'yes',
					'pa_spl_skin!' => array( 'skin-3' ),
				),
			)
		);

		$this->add_responsive_control(
			'img_height',
			array(
				'label'      => __( 'Height (px)', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__skin-1 .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper,
					{{WRAPPER}} .premium-smart-listing__skin-2 .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper'  => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .premium-smart-listing__skin-3 .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper' => 'min-height: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'post_img' => 'yes',
				),
			)
		);

		$start_align = is_rtl() ? 'right;' : 'left;';
		$end_align   = is_rtl() ? 'left;' : 'right;';

		$this->add_responsive_control(
			'post_content_align',
			array(
				'label'                => __( 'Content Alignment', 'premium-addons-pro' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'start'  => array(
						'title' => __( 'Start', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'end'    => array(
						'title' => __( 'End', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'              => 'start',
				'selectors_dictionary' => array(
					'start'  => 'align-items:flex-start; text-align: ' . $start_align,
					'center' => 'align-items:center; text-align: center',
					'end'    => 'align-items:flex-end; text-align: ' . $end_align,
				),
				'toggle'               => false,
				'selectors'            => array(
					'{{WRAPPER}} .premium-smart-listing__posts-wrapper .premium-smart-listing__post-content-wrapper' => '{{VALUE}}',
				),
			)
		);

		// content.
		$this->add_control(
			'post_excerpt',
			array(
				'label'     => __( 'Show Post Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'content_source',
			array(
				'label'       => __( 'Get Content From', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'excerpt' => __( 'Post Excerpt', 'premium-addons-pro' ),
					'full'    => __( 'Post Full Content', 'premium-addons-pro' ),
				),
				'default'     => 'excerpt',
				'label_block' => true,
				'condition'   => array(
					'post_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'post_excerpt_length',
			array(
				'label'     => __( 'Excerpt Length ( Words )', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'condition' => array(
					'post_excerpt'   => 'yes',
					'content_source' => 'excerpt',
				),
			)
		);

		$this->add_control(
			'post_excerpt_type',
			array(
				'label'       => __( 'Excerpt Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'dots' => __( 'Dots', 'premium-addons-pro' ),
					'link' => __( 'Link', 'premium-addons-pro' ),
				),
				'default'     => 'dots',
				'label_block' => true,
				'condition'   => array(
					'post_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_full_width',
			array(
				'label'        => __( 'Full Width', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'premium-smart-listing__cta-full-',
				'condition'    => array(
					'post_excerpt'      => 'yes',
					'post_excerpt_type' => 'link',
				),
			)
		);

		$this->add_control(
			'post_excerpt_text',
			array(
				'label'     => __( 'Read More Text', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'premium-addons-pro' ),
				'condition' => array(
					'post_excerpt'      => 'yes',
					'post_excerpt_type' => 'link',
				),
			)
		);

		$this->add_control(
			'hide_content',
			array(
				'label'       => __( 'Hide Content On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helper_Functions::get_all_breakpoints(),
				'separator'   => 'after',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
				'condition'   => array(
					'post_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_above_title',
			array(
				'label'     => __( 'Place Post Meta Above Post Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'premium-addons-pro' ),
				'label_off' => __( 'No', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'author_meta',
			array(
				'label'   => __( 'Author Meta', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'date_meta',
			array(
				'label'   => __( 'Date Meta', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'categories_meta',
			array(
				'label'   => __( 'Categories Meta', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'comments_meta',
			array(
				'label' => __( 'Comments Meta', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'tags_meta',
			array(
				'label' => __( 'Tags Meta', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->end_controls_section();
	}

	private function add_navigation_section_controls() {

		$this->start_controls_section(
			'navigation_section_tab',
			array(
				'label' => __( 'Navigation', 'premium-addons-pro' ),
			)
		);

		$this->add_carousel_section_controls();
		$this->add_pagination_section_controls();

		$this->end_controls_section();

	}

	private function add_carousel_section_controls() {

		$conds = array(
			'carousel'             => 'yes',
			'infinite_scroll!'     => 'yes',
			'load_more_button!'    => 'yes',
			'premium_blog_paging!' => 'yes',
		);

		$this->add_control(
			'carousel',
			array(
				'label'              => __( 'Enable Carousel', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'prefix_class'       => 'premium-carousel-',
				'render_type'        => 'template',
				'frontend_available' => true,
				'condition'          => array(
					'infinite_scroll!'     => 'yes',
					'load_more_button!'    => 'yes',
					'premium_blog_paging!' => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_fade',
			array(
				'label'              => __( 'Fade', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => array_merge(
					$conds,
					array(
						'listing_cols' => array( '', '1' ),
					)
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_play',
			array(
				'label'              => __( 'Auto Play', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => $conds,
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'carousel_rows',
			array(
				'label'              => __( 'Rows', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => '3',
				'condition'          => $conds,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'slides_to_scroll',
			array(
				'label'              => __( 'Slides To Scroll', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'condition'          => $conds,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed (ms)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5000,
				'condition'          => array_merge(
					$conds,
					array(
						'carousel_play' => 'yes',
					)
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_speed',
			array(
				'label'              => __( 'Transition Speed (ms)', 'premium-addons-pro' ),
				'description'        => __( 'Set the speed of the carousel animation in milliseconds (ms)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 300,
				'render_type'        => 'template',
				'selectors'          => array(
					'{{WRAPPER}} .premium-smart-listing__posts-wrapper .slick-slide' => 'transition: all {{VALUE}}ms !important',
				),
				'condition'          => $conds,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_center',
			array(
				'label'              => __( 'Center Mode', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'condition'          => $conds,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_spacing',
			array(
				'label'              => __( 'Slides\' Spacing', 'premium-addons-pro' ),
				'description'        => __( 'Set a spacing value in pixels (px)', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => '15',
				'condition'          => array_merge(
					$conds,
					array(
						'carousel_center' => 'yes',
					)
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'carousel_arrows',
			array(
				'label'              => __( 'Navigation Arrows', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'condition'          => $conds,
				'frontend_available' => true,
				'separator'          => 'after',
			)
		);
	}

	private function add_pagination_section_controls() {

		$this->add_control(
			'premium_blog_paging',
			array(
				'label'              => __( 'Enable Pagination', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'post_type_filter!' => 'related',
				),
			)
		);

		$this->add_control(
			'max_pages',
			array(
				'label'     => __( 'Page Limit', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'condition' => array(
					'premium_blog_paging' => 'yes',
					'post_type_filter!'   => 'related',
				),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'              => __( 'Type', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options'            => array(
					'default' => __( 'Arrows', 'premium-addons-pro' ),
					'num'     => __( 'Numbers', 'premium-addons-pro' ),
				),
				'default'            => 'num',
				'label_block'        => true,
				'condition'          => array(
					'premium_blog_paging' => 'yes',
					'post_type_filter!'   => 'related',
				),
			)
		);

		// change the condition to show if carousel is enabled and arrows are enabled.
		$this->add_control(
			'pagination_strings',
			array(
				'label'     => __( 'Enable Pagination Next/Prev Strings', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_blog_paging' => 'yes',
					'pagination_type'     => 'num',
					'post_type_filter!'   => 'related',
				),
			)
		);

		$this->add_control(
			'premium_blog_prev_text',
			array(
				'label'     => __( 'Previous Page String', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Previous', 'premium-addons-pro' ),
				'condition' => array(
					'premium_blog_paging' => 'yes',
					'pagination_strings'  => 'yes',
					'pagination_type'     => 'num',
					'post_type_filter!'   => 'related',
				),
			)
		);

		$this->add_control(
			'premium_blog_next_text',
			array(
				'label'     => __( 'Next Page String', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Next', 'premium-addons-pro' ),
				'condition' => array(
					'premium_blog_paging' => 'yes',
					'pagination_strings'  => 'yes',
					'pagination_type'     => 'num',
					'post_type_filter!'   => 'related',
				),
			)
		);

		$this->add_control(
			'pagination_pos',
			array(
				'label'        => __( 'Arrows Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'render_type'  => 'template',
				'separator'    => 'before',
				'prefix_class' => 'premium-smart-listing__nav-',
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'      => 'bottom',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'premium_blog_paging',
									'operator' => '===',
									'value'    => 'yes',
								),
								array(
									'name'     => 'pagination_type',
									'operator' => '===',
									'value'    => 'default',
								),
								array(
									'name'     => 'post_type_filter',
									'operator' => '!==',
									'value'    => 'related',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'carousel',
									'operator' => '===',
									'value'    => 'yes',
								),
								array(
									'name'     => 'premium_blog_paging',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'name'     => 'carousel_arrows',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_align',
			array(
				'label'      => __( 'Alignment', 'premium-addons-pro' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'    => 'right',
				'toggle'     => false,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'premium_blog_paging',
							'operator' => '===',
							'value'    => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'pagination_type',
									'operator' => '===',
									'value'    => 'num',
								),
								array(
									'relation' => 'and',
									array(
										'name'     => 'pagination_type',
										'operator' => '===',
										'value'    => 'default',
									),
									'terms'    => array(
										array(
											'name'     => 'pagination_pos',
											'operator' => '===',
											'value'    => 'bottom',
										),
									),
								),
							),
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container' => 'text-align: {{VALUE}}',
				),
			)
		);
	}

	/** Style Controls.*/

	/**
	 * Adds Image style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_img_common_style() {

		$this->start_controls_section(
			'pa_img_common_style',
			array(
				'label'     => __( 'Image', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'pa_img_css_filters',
				'selector' => '{{WRAPPER}} .premium-smart-listing__thumbnail-container',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'pa_img_css_filters_hov',
				'label'    => __( 'Hover CSS Filters', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper:hover .premium-smart-listing__thumbnail-container, {{WRAPPER}} .premium-smart-listing__post-wrapper:hover .premium-smart-listing__thumbnail-container',
			)
		);

		$this->add_control(
			'pa_featured_overlay',
			array(
				'label'     => __( 'Featured Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__thumbnail-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_img_overlay',
			array(
				'label'     => __( 'Post Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__thumbnail-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_img_border',
				'label'    => __( 'Post Border', 'premium-addons-pro' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper',
			)
		);

		$this->add_control(
			'pa_img_border_rad',
			array(
				'label'      => __( 'Post Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__thumbnail-container,
					{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__thumbnail-overlay,
					{{WRAPPER}} .premium-smart-listing__post-thumbnail-wrapper'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_img_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-thumbnail-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__thumbnail-overlay' => 'top: {{TOP}}{{UNIT}}; right: {{RIGHT}}{{UNIT}}; bottom: {{BOTTOM}}{{UNIT}}; left:{{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds header style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_header_style_controls() {

		$this->start_controls_section(
			'pa_header_style',
			array(
				'label' => __( 'Posts Header', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'pa_header_style_tabs' );

		$this->add_posts_title_style();

		$this->add_header_container_style();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Adds posts title style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_header_container_style() {

		$this->start_controls_tab(
			'pa_header_container_tab',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_header_container_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__header-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_header_container_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__header-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_header_container_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__header-wrapper',
			)
		);

		$this->add_control(
			'pa_header_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__header-wrapper'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_header_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__header-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_header_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__header-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
	}

	/**
	 * Adds posts title style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_posts_title_style() {

		$this->start_controls_tab(
			'pa_post_title_tab',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'condition' => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_control(
			'posts_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__header *' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'posts_title_typo',
				'selector'  => '{{WRAPPER}} .premium-smart-listing__header *',
				'condition' => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'posts_title_text_shadow',
				'selector'  => '{{WRAPPER}} .premium-smart-listing__header',
				'condition' => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'posts_title_bg',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .premium-smart-listing__header',
				'condition' => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'posts_title_border',
				'selector'  => '{{WRAPPER}} .premium-smart-listing__header',
				'condition' => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_control(
			'posts_tri_color',
			array(
				'label'     => __( 'Triangle Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__header::before' => 'border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_control(
			'posts_title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__header'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'posts_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'posts_title!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'posts_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'posts_title!' => '',
				),
			)
		);

		$this->end_controls_tab();
	}

	/**
	 * Adds header style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_fitlers_style() {

		$this->start_controls_section(
			'pa_filter_style',
			array(
				'label'     => __( 'Filter Tabs', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'filter_tabs' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_tabs_typo',
				'selector' => '{{WRAPPER}} .premium-smart-listing__filter-tabs li > * ',
			)
		);

		$this->start_controls_tabs( 'pa_filters_style' );

		$this->start_controls_tab(
			'pa_filters_tab',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'filter_tabs_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs li > *' => 'color: {{VALUE}}; fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_tabs_bg',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs > li > *:not(ul),
					{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper li > *' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_tabs_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__filter-tabs > li > *:not(ul)',
			)
		);

		$this->add_control(
			'filter_tabs_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs > li > *:not(ul)'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_filters_tab_hov',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'filter_tabs_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs li:hover > *' => 'color: {{VALUE}}; fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_tabs_bg_hov',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs > li:hover > *:not(ul),
					{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper li > *' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_tabs_border_hov',
				'selector' => '{{WRAPPER}} .premium-smart-listing__filter-tabs > li:hover > *:not(ul)',
			)
		);

		$this->add_control(
			'filter_tabs_border_rad_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs > li:hover > *:not(ul)'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_filters_tab_active',
			array(
				'label' => __( 'Active', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'filter_tabs_color_active',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs li a.active' => 'color: {{VALUE}}; fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'filter_tabs_bg_active',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs li a.active' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filter_tabs_border_active',
				'selector' => '{{WRAPPER}} .premium-smart-listing__filter-tabs li a.active',
			)
		);

		$this->add_control(
			'filter_tabs_border_rad_active',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs li a.active'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'filter_tabs_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__filter-tabs > li > *:not(ul)',
			)
		);

		$this->add_responsive_control(
			'filter_tabs_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs > li > *:not(ul)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tabs_wrapper_align',
			array(
				'label'     => __( 'Wrapped Menu Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper ul ' => 'text-align: {{VALUE}}',
				),
				'condition' => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'wrapped_icon_size',
			array(
				'label'      => __( 'Wrap Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper span i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper span svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
				'condition'  => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'wrapped_item_padding',
			array(
				'label'      => __( 'Wrapped Item Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tabs_wrapper_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'separator'  => 'before',
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper ul' => 'width: {{SIZE}}px;',
				),
				'condition'  => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tabs_wrapper_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper li:not(:last-child)' => 'margin-bottom: {{SIZE}}px;',
				),
				'condition'  => array(
					'filter_tabs_num' => 'yes',
				),
			)
		);

		$this->add_control(
			'tabs_wrapper_bg',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper ul' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'tabs_wrapper_border',
				'selector'  => '{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper ul',
				'condition' => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->add_control(
			'tabs_wrapper_rad',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper ul'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'tabs_wrapper_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__filter-tabs-menu-wrapper ul' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'wrap_tabs_sw' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_categories_style() {

		$this->start_controls_section(
			'post_categories_style_section',
			array(
				'label'     => __( 'Categories', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'category_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-smart-listing__category',
			)
		);

		$repeater = new REPEATER();

		$repeater->add_control(
			'category_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'category_hover_background_color',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'categories_repeater',
			array(
				'label'       => __( 'Categories', 'premium-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'category_background_color' => '',
					),
				),
				'render_type' => 'ui',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__category',
			)
		);

		$this->add_control(
			'category_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__category' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'categories_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__category' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'categories_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pa_featured_cats',
			array(
				'label'     => __( 'Featured Posts Categories', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'categories_hor_pos',
			array(
				'label'      => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__cat-container'  => 'left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'categories_ver_pos',
			array(
				'label'      => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__cat-container'  => 'top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'categories_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__cat-container ul'  => 'column-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

	}

	private function add_featured_posts_style() {

		$this->start_controls_section(
			'pa_featured_posts_style',
			array(
				'label'     => __( 'Featured Posts', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_featured_posts' => 'yes',
					'pa_spl_skin!'           => 'custom',
				),
			)
		);

		$this->add_control(
			'pa_featured_title_heading',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_featured_title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-title-wrapper, {{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-title-wrapper a',
			)
		);

		$this->add_control(
			'pa_featured_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-title-wrapper *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_featured_title_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-title-wrapper:hover *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_featured_title_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-title-wrapper'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		// meta style.
		$this->add_control(
			'pa_featured_meta_heading',
			array(
				'label'     => __( 'Post Meta', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_featured_meta_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-meta-container',
			)
		);

		$this->add_control(
			'pa_featured_meta_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-meta > *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_featured_meta_links',
			array(
				'label'     => __( 'Links Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-meta:not(.premium-smart-listing__post-time):hover > *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_featured_meta_sep_color',
			array(
				'label'     => __( 'Separator Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__meta-separator'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_featured_meta_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-meta-container'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		// content style.
		$this->add_control(
			'pa_featured_content_heading',
			array(
				'label'     => __( 'Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'post_excerpt' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pa_featured_content_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content',
				'condition' => array(
					'post_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_featured_content_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content'  => 'color: {{VALUE}};',
				),
				'condition' => array(
					'post_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'pa_featured_content_box_heading',
			array(
				'label'     => __( 'Content Box', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'pa_featured_content_box_width',
			array(
				'label'      => __( 'Width', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content-wrapper'  => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_featured_content_box_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_featured_content_box_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_featured_content_box_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content-wrapper',
			)
		);

		$this->add_responsive_control(
			'pa_featured_content_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_featured_content_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_featured_content_box_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__post-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// box / container.
		$this->add_control(
			'pa_featured_box_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_featured_box_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_featured_box_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_featured_box_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper',
			)
		);

		$this->add_control(
			'pa_featured_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_featured_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_featured_readmore_style() {

		$this->start_controls_section(
			'pa_featured_readmore_style',
			array(
				'label'     => __( 'Featured Read More', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pa_featured_excerpt'      => 'yes',
					'pa_featured_excerpt_type' => 'link',
					'pa_spl_skin!'             => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_featured_readmore_typo',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->start_controls_tabs( 'pa_featured_readmore_style_tabs' );

		$this->start_controls_tab(
			'pa_featured_readmore_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pa_featured_readmore_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_featured_readmore_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_featured_readmore_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_featured_readmore_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->add_control(
			'pa_featured_readmore_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_featured_readmore_hov',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pa_featured_readmore_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_featured_readmore_shadow_hov',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_featured_readmore_bg_hov',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_featured_readmore_border_hov',
				'selector' => '{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link:hover',
			)
		);

		$this->add_control(
			'pa_featured_readmore_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link:hover'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pa_featured_readmore_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_featured_readmore_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__featured-post-wrapper .premium-smart-listing__excerpt-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_posts_style() {

		$this->start_controls_section(
			'pa_posts_style',
			array(
				'label'     => __( 'Posts', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pa_spl_skin!' => 'custom',
				),
			)
		);

		$this->add_control(
			'pa_post_title_heading',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_post_title_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-title-wrapper, {{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-title-wrapper a',
			)
		);

		$this->add_control(
			'pa_post_title_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-title-wrapper *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_post_title_color_hov',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-title-wrapper:hover *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_post_title_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-title-wrapper'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		// meta style.
		$this->add_control(
			'pa_post_meta_heading',
			array(
				'label'     => __( 'Post Meta', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_post_meta_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-meta-container',
			)
		);

		$this->add_control(
			'pa_post_meta_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-meta > *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_post_meta_links',
			array(
				'label'     => __( 'Links Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-meta:not(.premium-smart-listing__post-time):hover > *'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_post_meta_sep_color',
			array(
				'label'     => __( 'Separator Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__meta-separator'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_post_meta_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-meta-container'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		// content style.
		$this->add_control(
			'pa_post_content_heading',
			array(
				'label'     => __( 'Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_post_content_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content',
			)
		);

		$this->add_control(
			'pa_post_content_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pa_post_content_box_heading',
			array(
				'label'     => __( 'Content Box', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_post_content_box_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_post_content_box_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_post_content_box_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content-wrapper',
			)
		);

		$this->add_control(
			'pa_post_content_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content-wrapper'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_post_content_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// $this->add_responsive_control(
		// 'pa_post_content_box_margin',
		// array(
		// 'label'      => __( 'Margin', 'premium-addons-pro' ),
		// 'type'       => Controls_Manager::DIMENSIONS,
		// 'size_units' => array( 'px', 'em', '%' ),
		// 'selectors'  => array(
		// '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__post-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// ),
		// )
		// );

		// box / container.
		$this->add_control(
			'pa_post_box_heading',
			array(
				'label'     => __( 'Container', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_post_box_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_post_box_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_post_box_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper',
			)
		);

		$this->add_control(
			'pa_post_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_post_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_posts_readmore_style() {

		$this->start_controls_section(
			'pa_posts_readmore_style',
			array(
				'label'     => __( 'Posts Read More', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'post_excerpt'      => 'yes',
					'post_excerpt_type' => 'link',
					'pa_spl_skin!'      => 'custom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pa_posts_readmore_typo',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->start_controls_tabs( 'pa_posts_readmore_style_tabs' );

		$this->start_controls_tab(
			'pa_posts_readmore_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pa_posts_readmore_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_posts_readmore_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_posts_readmore_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_posts_readmore_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link',
			)
		);

		$this->add_control(
			'pa_posts_readmore_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pa_posts_readmore_hov',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pa_posts_readmore_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'pa_posts_readmore_shadow_hov',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pa_posts_readmore_bg_hov',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pa_posts_readmore_border_hov',
				'selector' => '{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link:hover',
			)
		);

		$this->add_control(
			'pa_posts_readmore_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link:hover'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pa_posts_readmore_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator'  => 'before',
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pa_posts_readmore_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__post-wrapper .premium-smart-listing__excerpt-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Adds pagination style controls.
	 *
	 * @access private
	 * @since 4.9.37
	 */
	private function add_pagination_style() {

		$this->start_controls_section(
			'pa_pagination_style',
			array(
				'label'      => __( 'Pagination', 'premium-addons-pro' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_blog_paging',
							'value' => 'yes',
						),
						array(
							'terms' => array(
								array(
									'name'  => 'carousel',
									'value' => 'yes',
								),
								array(
									'name'     => 'premium_blog_paging',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'name'  => 'carousel_arrows',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pagination_typo',
				'selector'  => '{{WRAPPER}} .premium-smart-listing__pagination-container > .page-numbers',
				'condition' => array(
					'premium_blog_paging' => 'yes',
					'pagination_type'     => 'num',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container > .page-numbers' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'  => 'premium_blog_paging',
									'value' => 'yes',
								),
								array(
									'name'  => 'pagination_type',
									'value' => 'default',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'  => 'carousel',
									'value' => 'yes',
								),
								array(
									'name'     => 'premium_blog_paging',
									'operator' => '!==',
									'value'    => 'yes',
								),
								array(
									'name'  => 'carousel_arrows',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'pagination_colors' );

		$this->start_controls_tab(
			'pagination_nomral',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers',
			)
		);

		$this->add_control(
			'pagination_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'pagination_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'pagination_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_blog_pagination_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),

			)
		);

		$this->add_control(
			'pagination_color_hov',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers:not(:disabled):hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_bg_hov',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers:not(:disabled):hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_border_hov',
				'selector' => '{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers:not(:disabled):hover',
			)
		);

		$this->add_control(
			'pagination_border_radius_hov',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers:not(:disabled):hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'pagination_adv_radius_hov!' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_adv_radius_hov',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'pagination_adv_radius_value_hov',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers:not(:disabled):hover' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'pagination_adv_radius_hov' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_active',
			array(
				'label'     => __( 'Active', 'premium-addons-pro' ),
				'condition' => array(
					'pagination_type'     => 'num',
					'premium_blog_paging' => 'yes',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container span.current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_bg',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container span.current' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'active_navigation_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__pagination-container span.current',
			)
		);

		$this->add_control(
			'active_navigation_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container span.current' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'active_navigation_adv_radius!' => 'yes',
				),
			)
		);

		$this->add_control(
			'active_navigation_adv_radius',
			array(
				'label'       => __( 'Advanced Border Radius', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Apply custom radius values. Get the radius value from ', 'premium-addons-pro' ) . '<a href="https://9elements.github.io/fancy-border-radius/" target="_blank">here</a>',
			)
		);

		$this->add_control(
			'active_navigation_adv_radius_value',
			array(
				'label'     => __( 'Border Radius', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container span.current' => 'border-radius: {{VALUE}};',
				),
				'condition' => array(
					'active_navigation_adv_radius' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__pagination-container .page-numbers' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_loader_style() {

		$this->start_controls_section(
			'loader_style_section',
			array(
				'label'      => __( 'Loader', 'premium-addons-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'premium_blog_paging',
							'value' => 'yes',
						),
						array(
							'name'  => 'filter_tabs',
							'value' => 'yes',
						),
						array(
							'name'  => 'infinite_scroll',
							'value' => 'yes',
						),
						array(
							'name'  => 'load_more_button',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'pagination_overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .premium-loading-feed' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'spinner_color',
			array(
				'label'     => __( 'Spinner Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-loader' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'spinner_fill_color',
			array(
				'label'     => __( 'Fill Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-loader' => 'border-top-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_load_more_btn_style() {

		$btn_conds = array(
			'pa_spl_skin'          => array( 'skin-2', 'skin-3', 'custom' ),
			'premium_blog_paging!' => 'yes',
			'post_type_filter!'    => 'related',
			'infinite_scroll!'     => 'yes',
			'load_more_button'     => 'yes',
		);

		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Load More Button', 'premium-addons-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $btn_conds,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'cta_typography',
				'selector' => '{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a',
			)
		);

		$this->add_control(
			'cta_sep_bg',
			array(
				'label'     => __( 'Separators Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a::before, {{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a::after' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cta_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'cta_style_tabs' );

		$this->start_controls_tab(
			'cta_style_tab_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'cta_color',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow',
				'selector' => '{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border',
				'selector' => '{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a',
			)
		);

		$this->add_control(
			'cta_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cta_style_tab_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-for-elementor' ),
			)
		);

		$this->add_control(
			'cta_color_hover',
			array(
				'label'     => __( 'Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'cta_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'cta_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cta_border_hover',
				'selector' => '{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a:hover',
			)
		);

		$this->add_control(
			'cta_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-smart-listing__load-more-btn-wrapper a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Render smart post listing widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();

		$settings['active_cat']  = '';
		$settings['widget_id']   = $this->get_id();
		$settings['widget_type'] = 'premium-smart-listing';

		$skin              = $settings['pa_spl_skin'];
		$posts_helper      = Posts_Helper::getInstance();
		$title             = ! empty( $settings['posts_title'] ) ? $settings['posts_title'] : false;
		$title_tag         = $settings['posts_title_tag'];
		$filtr_tabs        = 'yes' !== $settings['carousel'] && 'yes' === $settings['filter_tabs'] && 'related' !== $settings['post_type_filter'] ? true : false;
		$pagination        = 'yes' === $settings['premium_blog_paging'] ? true : false;
		$carousel_arrows   = ! $pagination && 'yes' === $settings['carousel'] && 'yes' === $settings['carousel_arrows'] ? true : false;
		$pagination_arrows = $pagination && 'default' === $settings['pagination_type'] ? true : false;
		$load_more_button  = 'skin-1' !== $skin && 'yes' !== $settings['infinite_scroll'] && 'yes' === $settings['load_more_button'];
		$draw_arrows       = $carousel_arrows || $pagination_arrows;

		$posts_helper->set_widget_settings( $settings, $settings['active_cat'] );

		$query = $posts_helper->get_query_posts();

		if ( ! $query->have_posts() ) {

			$query_notice = $settings['empty_query_text'];

			$posts_helper->get_empty_query_message( $query_notice );
			return;
		}

		// Filters.
		if ( $filtr_tabs ) {

			$filter_rule = $settings['filter_tabs_type'];

			$filters = $this->get_filter_array( $filter_rule );

			if ( empty( $settings['first_tab_label'] ) ) {
				$settings['active_cat'] = $filters[0]->slug;
			}
		}

		// Pagination.
		if ( $pagination ) {

			$total_pages = $query->max_num_pages;

			if ( ! empty( $settings['max_pages'] ) ) {
				$total_pages = min( $settings['max_pages'], $total_pages );
			}
		}

		$page_id = '';

		if ( null !== Plugin::$instance->documents->get_current() ) {
			$page_id = Plugin::$instance->documents->get_current()->get_main_id();
		}

		$this->add_render_attribute(
			'posts_outer_wrapper',
			array(
				'class' => array(
					'premium-smart-listing__posts-outer-wrapper',
					'premium-smart-listing__' . $skin,
				),
			)
		);

		?>
			<div class="premium-smart-listing__wrapper premium-slide-up" data-page="<?php echo esc_attr( $page_id ); ?>">
				<div class="premium-smart-listing__header-wrapper">
					<?php if ( $title ) : ?>
					<div class="premium-smart-listing__header">
						<<?php echo wp_kses_post( $title_tag ); ?>> <?php echo esc_html( $title ); ?> </<?php echo wp_kses_post( $title_tag ); ?>>
					</div>
					<?php endif; ?>
					<div class="premium-smart-listing__navigation-outer-container">
						<?php if ( $filtr_tabs ) : ?>
							<div class="premium-smart-listing__filter-tabs-wrapper">
								<?php $this->get_filter_tabs_markup( $filters ); ?>
							</div>
						<?php endif; ?>
						<?php
						if ( 'top' === $settings['pagination_pos'] ) {
							if ( $draw_arrows ) {
								$posts_helper->add_navigation_arrows();

							} elseif ( $pagination && $total_pages > 1 ) {
								$posts_helper->render_pagination();
							}
						}
						?>
					</div>
				</div>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'posts_outer_wrapper' ) ); ?>>
					<?php
						$id = $this->get_id();
						$posts_helper->render_smart_posts();
					?>
				</div>
				<?php
				if ( 'bottom' === $settings['pagination_pos'] ) {
					if ( $draw_arrows ) {
						$posts_helper->add_navigation_arrows();

					} elseif ( $pagination && $total_pages > 1 ) {
						?>
						<div class="premium-smart-listing__footer">
							<?php $posts_helper->render_pagination(); ?>
						</div>
						<?php
					}
				}
				?>

				<?php
					// load more btn
				if ( $load_more_button ) {
					?>
							<div class="premium-smart-listing__load-more-btn-wrapper">
								<a type="button" data-role="none" role="button"> <?php echo esc_html__( $settings['load_more_txt'], 'premium-addons-pro' ); ?></a>
							</div>
						<?php
				}
				?>
			</div>
		<?php
	}

	/**
	 * Get Filter Array.
	 * Returns an array of filters
	 *
	 * @since 3.20.8
	 * @access protected
	 *
	 * @param string $filter filter rule.
	 *
	 * @return array
	 */
	public function get_filter_array( $filter ) {

		$settings = $this->get_settings();

		$current_language = apply_filters( 'wpml_current_language', '-' );

		$post_type = $settings['post_type_filter'];

		if ( 'tag' === $filter ) {
			$filter = 'post_tag';
		}

		$filter_rule = isset( $settings[ $filter . '_' . $post_type . '_filter_rule' ] ) ? $settings[ $filter . '_' . $post_type . '_filter_rule' ] : '';

		// Fix: Make sure there is a value set for the current tax control.
		if ( empty( $filter_rule ) ) {
			return;
		}

		$filters = $settings[ 'tax_' . $filter . '_' . $post_type . '_filter' ];

		// Get the categories based on filter source.
		$taxs = get_terms( $filter );

		$tabs_array = array();

		if ( is_wp_error( $taxs ) ) {
			return array();
		}

		if ( empty( $filters ) || '' === $filters ) {

			$tabs_array = $taxs;

		} else {

			foreach ( $taxs as $key => $value ) {

				$slug = str_replace( '-' . $current_language, '', $value->slug );

				if ( 'IN' === $filter_rule ) {

					if ( in_array( $slug, $filters, true ) ) {

						$tabs_array[] = $value;
					}
				} else {

					if ( ! in_array( $slug, $filters, true ) ) {

						$tabs_array[] = $value;
					}
				}
			}
		}

		return $tabs_array;
	}

	/**
	 * Get Filter Tabs Markup.
	 *
	 * @since 3.11.2
	 * @access protected
	 *
	 * @param string $filters filters' labels.
	 */
	protected function get_filter_tabs_markup( $filters ) {

		$settings = $this->get_settings();

		if ( empty( $filters ) ) {
			return;
		}

		$wrap_elements    = 'yes' === $settings['wrap_tabs_sw'] ? true : false;
		$visibleFilters   = $wrap_elements && ! empty( $settings['first_tab_label'] ) ? $settings['filter_tabs_num'] - 1 : $settings['filter_tabs_num'];
		$wrapped_elements = $wrap_elements ? array_splice( $filters, $visibleFilters ) : array();

		if ( ! empty( $filters ) ) {
			?>
			<ul class="premium-smart-listing__filter-tabs">
				<!-- Render the fisrt lablel. -->
				<?php if ( ! empty( $settings['first_tab_label'] ) ) : ?>
					<li>
						<a href="javascript:;" class="category active" data-filter="*">
							<?php echo esc_html( $settings['first_tab_label'] ); ?>
						</a>
					</li>
				<?php endif; ?>

				<?php
				// Render the filters normally.
				foreach ( $filters as $index => $filter ) {
					$key = 'post_category_' . $index;

					$this->add_render_attribute( $key, 'class', 'category' );

					if ( empty( $settings['first_tab_label'] ) && 0 === $index ) {
						$this->add_render_attribute( $key, 'class', 'active' );
					}
					?>
						<li>
							<a href="javascript:;" <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?> data-filter="<?php echo esc_attr( $filter->slug ); ?>">
								<?php echo wp_kses_post( $filter->name ); ?>
							</a>
						</li>
					<?php
				}
				?>

				<li class="premium-smart-listing__filter-tabs-menu-wrapper" style="display:none">
					<span>
						<?php
						if ( $wrap_elements ) {

							echo esc_html( $settings['tabs_wrap_label'] );

							Icons_Manager::render_icon(
								$settings['wrap_icon'],
								array(
									'class'       => array( 'premium-smart-listing__filter-tabs-wrap-icon' ),
									'aria-hidden' => 'true',
								)
							);
						}
						?>
					</span>
					<ul>
						<?php
						if ( ! empty( $wrapped_elements ) ) {
							foreach ( $wrapped_elements as $index => $filter ) {
								$key = 'post_category_wrapped_' . $index;

								$this->add_render_attribute( $key, 'class', 'category' );

								if ( empty( $settings['first_tab_label'] ) && 0 === $index && 0 === $settings['filter_tabs_num'] ) {
									$this->add_render_attribute( $key, 'class', 'active' );
								}
								?>
							<li class="premium-smart-listing__wrapped-filter">
								<a href="javascript:;" <?php echo wp_kses_post( $this->get_render_attribute_string( $key ) ); ?> data-filter="<?php echo esc_attr( $filter->slug ); ?>">
									<?php echo wp_kses_post( $filter->name ); ?>
								</a>
							</li>
								<?php
							}
						}
						?>
					</ul>
				</li>

			</ul>
			<?php
		}
	}
}
