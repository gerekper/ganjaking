<?php
/**
 * UAEL Business Reviews.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\BusinessReviews\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;
use UltimateElementor\Classes\UAEL_Helper;
use UltimateElementor\Modules\BusinessReviews\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Business_Reviews.
 */
class Business_Reviews extends Common_Widget {

	/**
	 * Has Template content
	 *
	 * @var _has_template_content
	 */
	protected $_has_template_content = false; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Retrieve Business Reviews Widget name.
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Business_Reviews' );
	}

	/**
	 * Retrieve Business Reviews Widget title.
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Business_Reviews' );
	}

	/**
	 * Retrieve Business Reviews Widget icon.
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Business_Reviews' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Business_Reviews' );
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'uael-business-reviews',
			'uael-slick',
		);
	}

	/**
	 * Register Skins.
	 *
	 * @since 1.29.0
	 * @access public
	 */
	public function register_skins() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->add_skin( new Skins\Skin_Default( $this ) );
		$this->add_skin( new Skins\Skin_Card( $this ) );
		$this->add_skin( new Skins\Skin_Bubble( $this ) );
	}

	/**
	 * Register Business Reviews controls.
	 *
	 * @since 1.29.2
	 * @access public
	 */
	public function register_controls() {

		$this->register_general_controls();
		$this->register_content_grid_controls();
		$this->register_filters_controls();
		$this->register_helpful_information();
	}

	/**
	 * Register Business Reviews General Controls.
	 *
	 * @since 1.13.0
	 * @access protected
	 */
	protected function register_general_controls() {
		$this->start_controls_section(
			'section_general_field',
			array(
				'label' => __( 'General', 'uael' ),
			)
		);

			$integration_options = UAEL_Helper::get_integrations_options();

			$widget_list = UAEL_Helper::get_widget_list();

			$admin_link = $widget_list['Business_Reviews']['setting_url'];
		if ( ! isset( $integration_options['google_places_api'] ) || '' === $integration_options['google_places_api'] ) {
			$this->add_control(
				'google_err_msg',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'To display Google place reviews, you need to configure Google Map API key. Please configure API key from <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'review_type!' => 'yelp',
					),
				)
			);
		}

		if ( ! isset( $integration_options['yelp_api'] ) || '' === $integration_options['yelp_api'] ) {
			$this->add_control(
				'yelp_err_msg',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'To display Yelp reviews, you need to configure the Yelp API key. Please configure API key from <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'review_type!' => 'google',
					),
				)
			);
		}

		$this->add_control(
			'review_type',
			array(
				'label'        => __( 'Review Source', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'google',
				'options'      => array(
					'google' => __( 'Google Places', 'uael' ),
					'yelp'   => __( 'Yelp', 'uael' ),
					'all'    => __( 'Google & Yelp', 'uael' ),
				),
				'render_type'  => 'template',
				'prefix_class' => 'uael-social-reviews-',
			)
		);

			$this->add_control(
				'place_id',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'       => sprintf( __( '%1$sGoogle Place ID%2$s', 'uael' ), '<b>', '</b>' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
					'default'     => __( 'ChIJBUo5LX5FXj4RqXDEdkjfdmA', 'uael' ),
					'condition'   => array(
						'review_type!' => 'yelp',
					),
				)
			);

			$this->add_control(
				'google_place_id_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click %1$s here %2$s to find your Google Place ID.', 'uael' ), '<a href="https://developers.google.com/places/place-id/" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'review_type!' => 'yelp',
					),
				)
			);

			$this->add_control(
				'all_separator',
				array(
					'type'      => Controls_Manager::DIVIDER,
					'condition' => array(
						'review_type' => 'all',
					),
				)
			);

			$this->add_control(
				'language_id',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'       => sprintf( __( '%1$sLanguage Code%2$s', 'uael' ), '<b>', '</b>' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
					'condition'   => array(
						'review_type!' => 'yelp',
					),
				)
			);

			$this->add_control(
				'language_code_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click %1$s here %2$s to check your Language code.', 'uael' ), '<a href="https://developers.google.com/admin-sdk/directory/v1/languages" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'review_type!' => 'yelp',
					),
				)
			);

			$this->add_control(
				'yelp_business_id',
				array(
					/* translators: 1: <b> 2: </b> */
					'label'       => sprintf( __( '%1$sYelp Business ID%2$s', 'uael' ), '<b>', '</b>' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
					'default'     => 'osteria-francescana-modena-2',
					'condition'   => array(
						'review_type!' => 'google',
					),
				)
			);

			$this->add_control(
				'yelp_place_id_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click %1$s here %2$s to find your Yelp Business ID.', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/find-yelp-business-id/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'review_type!' => 'google',
					),
				)
			);

			$this->add_control(
				'refresh_reviews',
				array(
					'label'   => __( 'Reload Reviews after a', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'day',
					'options' => array(
						'hour'  => __( 'Hour', 'uael' ),
						'day'   => __( 'Day', 'uael' ),
						'week'  => __( 'Week', 'uael' ),
						'month' => __( 'Month', 'uael' ),
						'year'  => __( 'Year', 'uael' ),
					),
				)
			);

		if ( '' !== $integration_options['google_places_api'] && '' !== $integration_options['yelp_api'] ) {

			$this->add_control(
				'backend_link_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Note: If you are facing issues while fetching the reviews make sure you have entered the correct API key <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'review_type' => 'all',
					),
				)
			);
		}

		if ( '' !== $integration_options['google_places_api'] ) {
			$this->add_control(
				'backend_link_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Note: If you are facing issues while fetching the reviews make sure you have entered the correct API key <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'review_type' => 'google',
					),
				)
			);
		}
		if ( '' !== $integration_options['yelp_api'] ) {
			$this->add_control(
				'backend_link_doc_3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Note: If you are facing issues while fetching the reviews make sure you have entered the correct API key <a href="%s" target="_blank" rel="noopener">here</a>.', 'uael' ), $admin_link ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array(
						'review_type' => 'yelp',
					),
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Register Business Reviews Style Controls.
	 *
	 * @since 1.13.0
	 * @access protected
	 */
	protected function register_content_grid_controls() {
		$this->start_controls_section(
			'section_content_grid',
			array(
				'label' => __( 'Layout', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'review_structure',
				array(
					'label'   => __( 'Select Layout', 'uael' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'normal',
					'options' => array(
						'normal'   => __( 'Grid', 'uael' ),
						'carousel' => __( 'Carousel', 'uael' ),
					),
				)
			);

			$this->add_control(
				'google_reviews_number',
				array(
					'label'     => __( 'Number of Reviews', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 3,
					'min'       => 1,
					'max'       => 5,
					'condition' => array(
						'review_type' => 'google',
					),
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'google_max_reviews',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Google allows maximum 5 reviews. Click <a href="%s" target="_blank" rel="noopener">here</a> to know more.', 'uael' ), UAEL_DOMAIN . 'docs/maximum-number-of-reviews-for-google-and-yelp/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'review_type' => 'google',
					),
				)
			);
		}

			$this->add_control(
				'yelp_reviews_number',
				array(
					'label'     => __( 'Number of Reviews', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 3,
					'min'       => 1,
					'max'       => 3,
					'condition' => array(
						'review_type' => 'yelp',
					),
				)
			);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'yelp_max_reviews',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Yelp allows maximum 3 reviews. Click <a href="%s" target="_blank" rel="noopener">here</a> to know more.', 'uael' ), UAEL_DOMAIN . 'docs/maximum-number-of-reviews-for-google-and-yelp/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'review_type' => 'yelp',
					),
				)
			);
		}

			$this->add_control(
				'total_reviews_number',
				array(
					'label'     => __( 'Number of Reviews', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 3,
					'min'       => 1,
					'max'       => 8,
					'condition' => array(
						'review_type' => 'all',
					),
				)
			);

			$this->add_control(
				'all_review_length_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'We can fetch only up to 5 Google and 3 Yelp Reviews. So, a maximum of 8 reviews can be displayed. Click <a href="%s" target="_blank" rel="noopener">here</a> to know more.', 'uael' ), UAEL_DOMAIN . 'docs/maximum-number-of-reviews-for-google-and-yelp/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'review_type' => 'all',
					),
				)
			);

			$this->add_responsive_control(
				'gallery_columns',
				array(
					'label'              => __( 'Columns', 'uael' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => '3',
					'tablet_default'     => '2',
					'mobile_default'     => '1',
					'options'            => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
					),
					'prefix_class'       => 'uael-reviews-grid%s__column-',
					'condition'          => array(
						'review_structure' => 'normal',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'slides_to_show',
				array(
					'label'              => __( 'Reviews to Show', 'uael' ),
					'description'        => __( 'Note: <b>Reviews to Show</b> should be less than <b>Number of Reviews</b>.', 'uael' ),
					'type'               => Controls_Manager::NUMBER,
					'default'            => 3,
					'tablet_default'     => 2,
					'mobile_default'     => 1,
					'condition'          => array(
						'review_structure' => 'carousel',
					),
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'slides_to_scroll',
				array(
					'label'              => __( 'Slides to Scroll', 'uael' ),
					'type'               => Controls_Manager::NUMBER,
					'default'            => 1,
					'tablet_default'     => 1,
					'mobile_default'     => 1,
					'min'                => 1,
					'max'                => 4,
					'condition'          => array(
						'review_structure' => 'carousel',
					),
					'frontend_available' => true,
				)
			);

			$this->add_control(
				'navigation',
				array(
					'label'     => __( 'Navigation', 'uael' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'both',
					'options'   => array(
						'both'   => __( 'Arrows and Dots', 'uael' ),
						'arrows' => __( 'Arrows', 'uael' ),
						'dots'   => __( 'Dots', 'uael' ),
						'none'   => __( 'None', 'uael' ),
					),
					'condition' => array(
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'infinite',
				array(
					'label'        => __( 'Infinite Loop', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array(
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'autoplay',
				array(
					'label'        => __( 'Autoplay', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array(
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'pause_on_hover',
				array(
					'label'        => __( 'Pause on Hover', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array(
						'review_structure' => 'carousel',
						'autoplay'         => 'yes',
					),
				)
			);

			$this->add_control(
				'autoplay_speed',
				array(
					'label'     => __( 'Autoplay Speed', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 5000,
					'condition' => array(
						'autoplay'         => 'yes',
						'review_structure' => 'carousel',
					),
					'selectors' => array(
						'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
					),
				)
			);

			$this->add_control(
				'transition_speed',
				array(
					'label'     => __( 'Transition Speed (ms)', 'uael' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 500,
					'condition' => array(
						'review_structure' => 'carousel',
					),
				)
			);

			$this->add_control(
				'equal_height',
				array(
					'label'        => __( 'Equal Height', 'uael' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
					'prefix_class' => 'uael-reviews-equal-height-',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				'help_doc_equal_height',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Note: This option sets an equal height for all the reviews content boxes. It takes the height of the longest review and applies it to the other reviews.', 'uael' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'_skin'        => 'bubble',
						'equal_height' => 'yes',
					),
				)
			);
		$this->end_controls_section();
	}


	/**
	 * Register Business Reviews filters.
	 *
	 * @since 1.13.0
	 * @access protected
	 */
	protected function register_filters_controls() {
		$this->start_controls_section(
			'section_filters_controls',
			array(
				'label' => __( 'Filters', 'uael' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'reviews_filter_by',
				array(
					'label'       => __( 'Filter By', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => false,
					'default'     => 'rating',
					'options'     => array(
						'default' => 'No Filter',
						'rating'  => 'Rating',
						'date'    => 'Review Date',
					),
				)
			);

			$this->add_control(
				'reviews_min_rating',
				array(
					'label'       => __( 'Minimum Rating', 'uael' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => false,
					'default'     => 'no',
					'options'     => array(
						'no' => 'No Minimum Rating',
						'2'  => '2 star',
						'3'  => '3 star',
						'4'  => '4 star',
						'5'  => '5 star',
					),
				)
			);

			$this->add_control(
				'reviews_min_rating_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'Choose the lowest star ratings to display.</br>For example, choosing 3 star will skip the reviews with less than 3 rating from displaying.', 'uael' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

		$this->end_controls_section();
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function register_helpful_information() {
		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

			$this->add_control(
				'help_doc_1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/business-reviews-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_6',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to get Google Places API Key? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/get-google-places-api-key/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_4',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to find Google Place ID? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/find-google-place-id/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_2',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to get Yelp API Key? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/get-yelp-api-key/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_3',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s How to find Yelp Business ID? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/find-yelp-business-id/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->add_control(
				'help_doc_5',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s doc link */
					'raw'             => sprintf( __( '%1$s Unable to display more than 5 Google/3 Yelp Reviews? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/maximum-number-of-reviews-for-google-and-yelp/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
					'content_classes' => 'uael-editor-doc',
				)
			);

			$this->end_controls_section();
		}
	}
}
