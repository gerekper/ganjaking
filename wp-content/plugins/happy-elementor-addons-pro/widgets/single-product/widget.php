<?php
/**
 * Single Product widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Happy_Addons_Pro\Controls\Lazy_Select;
use Happy_Addons_Pro\Lazy_Query_Manager;
use WP_Query;

defined( 'ABSPATH' ) || die();

class Single_Product extends Base {

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
		return __( 'Single Product', 'happy-addons-pro' );
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
		return 'hm hm-product-list-single';
	}

	public function get_keywords() {
		return ['single-product', 'single', 'product', 'woocommerce', 'single-shop', 'shop'];
	}

 	/**
	 * Register & Inculde Single Product Skins
	 */
	protected function register_skins() {
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/single-product/skins/skin-base.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/single-product/skins/classic.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/single-product/skins/standard.php' );
        include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'widgets/single-product/skins/landscape.php' );

		$this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Single_Product\Classic( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Single_Product\Standard( $this ) );
        $this->add_skin( new \Happy_Addons_Pro\Widget\Skins\Single_Product\Landscape( $this ) );

	}


	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_single_product',
			[
				'label' => __( 'Single Product', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
        );

		$this->add_control(
			'posts_post_type',
			[
				'label' => __( 'Hidden Style', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default'=> 'product',
			]
		);

		$this->add_control(
			'posts_selected_ids',
			[
				'label' => __( 'Search & Select Product', 'happy-addons-pro' ),
				'type' => Lazy_Select::TYPE,
				'multiple' => false,
				'placeholder' => 'Type & Search Product',
				'mininput' => 0,
				'label_block' => true,
				'lazy_args' => [
					'query' => Lazy_Query_Manager::QUERY_POSTS,
					'widget_props' => [
						'post_type' => 'posts_post_type'
					]
				],
			]
		);

		$this->end_controls_section();

		//Featured Image Control
		$this->featured_image_content_controls();

    }


	/**
	 * Featured Image Control
	 */
	protected function featured_image_content_controls() {

		$this->start_controls_section(
			'_section_feature_image',
			[
				'label' => __( 'Feature Image', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'normal_image',
			[
				'label' => __('Normal Image', 'happy-addons-pro'),
				'show_label' => true,
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'should_include_svg_inline_option' => false,
			]
		);

		$this->add_control(
			'hover_image',
			[
				'label' => __('Hover Image', 'happy-addons-pro'),
				'show_label' => true,
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'should_include_svg_inline_option' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'exclude' => [ 'custom' ],
				'default' => 'woocommerce_thumbnail',
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();

	}


	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {}



	public function get_products_query_args() {
		$settings = $this->get_settings_for_display();
		$args = [];
		$args['post_type'] = 'product';
		$args['posts_per_page'] = 1;
		$args['post_status'] = 'publish';
		$args['p'] = $settings[ 'posts_selected_ids' ];

		if( empty($settings[ 'posts_selected_ids' ]) ){
			$args['order'] = 'ASC';
			$args['orderby'] = 'title';
		}

		return $args;
	}

	public function get_query() {
		return get_posts( $this->get_products_query_args() );
	}

	public static function show_wc_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'WooCommerce is missing! Please install and activate WooCommerce.', 'happy-addons-pro' )
				);
		}
	}

	public static function show_alert_to_add_product() {
		printf(
			'<div %s>%s</div>',
			'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
			__( 'Please add some product to view.', 'happy-addons-pro' )
			);
	}

	// public function __alert_() {
	// 	if ( ! function_exists( 'WC' ) ) {
	// 		self::show_wc_missing_alert();
	// 		return;
	// 	}elseif ( empty( $this->get_query() ) ) {
	// 		self::show_alert_to_add_product();
	// 		return;
	// 	}
	// }

}
