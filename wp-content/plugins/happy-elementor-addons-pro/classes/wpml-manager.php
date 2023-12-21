<?php
/**
 * WPML integration and compatibility manager
 */
namespace Happy_Addons_Pro;

use Happy_Addons\Elementor\Widgets_Cache;
use Happy_Addons\Elementor\Assets_Cache;

defined( 'ABSPATH' ) || die();

class WPML_Manager {

	public static function init() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ __CLASS__, 'add_widgets_to_translate' ] );
	}

	public static function load_integration_files() {
		// Load repeatable module class
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'classes/wpml-module-with-items.php' );

		// Load widget integration
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/accordion.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/advanced-tabs.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/animated-text.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/business-hour.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/feature-list.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/hotspots.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/list-group.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/line-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/pie-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/polar-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/price-menu.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/pricing-table.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/radar-chart.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/scrolling-image.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/testimonial-carousel.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/team-carousel.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/timeline.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/toggle.php' );
		include_once( HAPPY_ADDONS_PRO_DIR_PATH . 'wpml/advanced-slider.php' );
	}

	public static function add_widgets_to_translate( $widgets ) {
		self::load_integration_files();

		$widgets_map = [
			'accordion' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Accordion',
			],
			'advanced-heading' => [
				'fields' => [
					[
						'field' => 'heading_before',
						'type'        => __( 'Advanced Heading: Before Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'heading_center',
						'type'        => __( 'Advanced Heading: Center Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'heading_after',
						'type'        => __( 'Advanced Heading: After Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'background_text',
						'type'        => __( 'Advanced Heading: Background Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'link' => [
						'field'       => 'url',
						'type'        => __( 'Advanced Heading: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				]
			],
			'list-group' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_List_Group',
			],
			'feature-list' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Feature_List',
			],
			'flip-box' => [
				'fields' => [
					[
						'field' => 'front_title',
						'type'        => __( 'Flip Box: Before Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'front_description',
						'type'        => __( 'Flip Box: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA'
					],
					[
						'field' => 'back_title',
						'type'        => __( 'Flip Box: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'back_description',
						'editor_type' => 'AREA',
						'type'        => __( 'Flip Box: Description', 'happy-addons-pro' ),
					],
					[
						'field' => 'button_text',
						'type'        => __( 'Flip Box: Button', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'link' => [
						'field'       => 'url',
						'type'        => __( 'Flip Box: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
				]
			],
			'hotspots' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Hotspots',
			],
			'hover-box' => [
				'fields' => [
					[
						'field' => 'title',
						'type'        => __( 'Hover Box: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'sub_title',
						'type'        => __( 'Hover Box: Sub Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'detail',
						'type'        => __( 'Hover Box: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA'
					],
				]
			],
			'line-chart' => [
				'fields' => [
					[
						'field' => 'labels',
						'type'        => __( 'Line Chart: Labels', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Line_Chart',
			],
			'pie-chart' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Pie_Chart',
			],
			'polar-chart' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Polar_Chart',
			],
			'promo-box' => [
				'fields' => [
					[
						'field' => 'before_title',
						'type'        => __( 'Promo Box: Before Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'title',
						'type'        => __( 'Promo Box: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'promo_link' => [
						'field'       => 'url',
						'type'        => __( 'Promo Box: Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
					[
						'field' => 'after_title',
						'type'        => __( 'Promo Box: After Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'description',
						'type'        => __( 'Promo Box: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA'
					],
					[
						'field' => 'button_text',
						'type'        => __( 'Promo Box: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					'button_link' => [
						'field'       => 'url',
						'type'        => __( 'Promo Box: Button Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
					[
						'field' => 'badge_text_offer',
						'type'        => __( 'Promo Box: Badge Offer', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'badge_text_detail',
						'type'        => __( 'Promo Box: Badge Description', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				]
			],
			'radar-chart' => [
				'fields' => [
					[
						'field' => 'labels',
						'type'        => __( 'Radar Chart: Labels', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Radar_Chart',
			],
			'team-carousel' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Team_Carousel',
			],
			'testimonial-carousel' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Testimonial_Carousel',
			],
			/**
			 * Countdown
			 */
			'countdown' => [
				'fields' => [
					[
						'field' => 'label_days',
						'type' => __( 'Countdown: Label Days', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'label_hours',
						'type' => __( 'Countdown: Label Hours', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'label_minutes',
						'type' => __( 'Countdown: Label Minutes', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'label_seconds',
						'type' => __( 'Countdown: Label Seconds', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'separator',
						'type' => __( 'Countdown: Separator', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
					[
						'field' => 'end_message',
						'type' => __( 'Countdown: Countdown End Message', 'happy-addons-pro' ),
						'editor_type' => 'VISUAL'
					],
					[
						'field' => 'end_redirect_link',
						'type' => __( 'Countdown: Redirection Link', 'happy-addons-pro' ),
						'editor_type' => 'LINE'
					],
				]
			],

			/**
			 * Animated Text
			 */
			'animated-text' => [
				'fields' => [
					[
						'field'       => 'before_text',
						'type'        => __( 'Animated Text: Before Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'after_text',
						'type'        => __( 'Animated Text: After Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Animated_Text',
			],

			/**
			 * Business Hour
			 */
			'business-hour' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Business Hour: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Business_Hour',
			],

			/**
			 * Instagram Feed
			 */
			'instagram-feed' => [
				'fields' => [
					[
						'field'       => 'title_btn_text',
						'type'        => __( 'Instagram Feed: Title Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'load_more_text',
						'type'        => __( 'Instagram Feed: Load More Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				]
			],

			/**
			 * Price Menu
			 */
			'price-menu' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Price_Menu',
			],

			/**
			 * Pricing Table
			 */
			'pricing-table' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Pricing Table: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'currency_custom',
						'type'        => __( 'Pricing Table: Custom Symbol', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'price',
						'type'        => __( 'Pricing Table: Price', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'original_price',
						'type'        => __( 'Pricing Table: Original Price', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'period',
						'type'        => __( 'Pricing Table: Period', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'features_title',
						'type'        => __( 'Pricing Table: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'description',
						'type'        => __( 'Pricing Table: Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'button_text',
						'type'        => __( 'Pricing Table: Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					'button_link' => [
						'field'       => 'url',
						'type'        => __( 'Pricing Table: Button Link', 'happy-addons-pro' ),
						'editor_type' => 'LINK',
					],
					[
						'field'       => 'button_attributes',
						'type'        => __( 'Pricing Table: Attributes', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'footer_description',
						'type'        => __( 'Pricing Table: Footer Description', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'badge_text',
						'type'        => __( 'Pricing Table: Badge Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => __NAMESPACE__ . '\\WPML_Pricing_Table',
			],

			/**
			 * Scrolling Image
			 */
			'scrolling-image' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Scrolling_Image',
			],

			/**
			 * Source Code
			 */
			'source-code' => [
				'fields' => [
					[
						'field'       => 'copy_btn_text',
						'type'        => __( 'Source Code: Copy Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'after_copy_btn_text',
						'type'        => __( 'Source Code: After Copy Button Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				]
			],

			/**
			 * Unfold
			 */
			'unfold' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Unfold: Title', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'editor',
						'type'        => __( 'Unfold: Content Editor', 'happy-addons-pro' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'unfold_text',
						'type'        => __( 'Unfold: Unfold Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'fold_text',
						'type'        => __( 'Unfold: Fold Text', 'happy-addons-pro' ),
						'editor_type' => 'LINE',
					],
				]
			],

			/**
			 * Timeline
			 */
			'timeline' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Timeline',
			],

			/**
			 * Advanced Tabs
			 */
			'advanced-tabs' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Advanced_Tabs',
			],

			/**
			 * Advanced Toggle
			 */
			'toggle' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Advanced_Toggle',
			],
			/**
			 * Advanced Slider
			 */
			'advanced-slider' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Advanced_Slider',
			],
		];

		foreach ( $widgets_map as $key => $data ) {
			$widget_name = 'ha-'.$key;

			$entry = [
				'conditions' => [
					'widgetType' => $widget_name,
				],
				'fields' => $data['fields'],
			];

			if ( isset( $data['integration-class'] ) ) {
				$entry['integration-class'] = $data['integration-class'];
			}

			$widgets[ $widget_name ] = $entry;
		}

		return $widgets;
	}
}

WPML_Manager::init();
