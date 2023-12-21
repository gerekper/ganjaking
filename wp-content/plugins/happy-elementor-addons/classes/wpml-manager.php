<?php
/**
 * WPML integration and compatibility manager
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class WPML_Manager {

	public static function init() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ __CLASS__, 'add_widgets_to_translate' ] );
		add_action( 'wpml_translation_job_saved', [ __CLASS__, 'on_translation_job_saved' ], 10, 3 );
	}

	/**
	 * Recreate HappyAddons widgets usage on transtion save
	 *
	 * @param int $new_post_id
	 * @param array $fields
	 * @param object $job
	 *
	 * @return void
	 */
	public static function on_translation_job_saved( $new_post_id, $fields, $job ) {
		$elements_data = get_post_meta( $job->original_doc_id, Widgets_Cache::META_KEY, true );

		if ( ! empty( $elements_data ) ) {
			update_post_meta( $new_post_id, Widgets_Cache::META_KEY, $elements_data );

			$assets_cache = new Assets_Cache( $new_post_id );
			$assets_cache->delete();
		}
	}

	public static function load_integration_files() {
		// Load repeatable module class
		include_once( HAPPY_ADDONS_DIR_PATH . 'classes/wpml-module-with-items.php' );

		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/bar-chart.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/carousel.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/image-grid.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/justified-gallery.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/logo-grid.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/pricing-table.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/skills.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/slider.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/social-icons.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/data-table.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/horizontal-timeline.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/image-accordion.php' );
		include_once( HAPPY_ADDONS_DIR_PATH . 'wpml/content-switcher.php' );
	}

	public static function add_widgets_to_translate( $widgets ) {
		self::load_integration_files();

		$widgets_map = [
			/**
			 * Bar Chart
			 */
			'bar-chart' => [
				'fields' => [
					[
						'field'       => 'labels',
						'type'        => __( 'Bar Chart: Labels', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'chart_title',
						'type'        => __( 'Bar Chart: Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Bar_Chart',
				]
			],

			/**
			 * Card
			 */
			'card' => [
				'fields' => [
					[
						'field'       => 'badge_text',
						'type'        => __( 'Card: Badge Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'title',
						'type'        => __( 'Card: Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'description',
						'type'        => __( 'Card: Description', 'happy-elementor-addons' ),
						'editor_type' => 'AREA'
					],
					[
						'field'       => 'button_text',
						'type'        => __( 'Card: Button Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					'button_link' => [
						'field'       => 'url',
						'type'        => __( 'Card: Button Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
				],
			],

			/**
			 * Carousel
			 */
			'carousel' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Carousel',
				]
			],

			/**
			 * Dual Button
			 */
			'dual-button' => [
				'fields' => [
					[
						'field'       => 'left_button_text',
						'type'        => __( 'Dual Button: Primary Button Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					'left_button_link' => [
						'field'       => 'url',
						'type'        => __( 'Dual Button: Primary Button Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
					[
						'field'       => 'button_connector_text',
						'type'        => __( 'Dual Button: Connector Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'right_button_text',
						'type'        => __( 'Dual Button: Secondary Button Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					'right_button_link' => [
						'field'       => 'url',
						'type'        => __( 'Dual Button: Secondary Button Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
				],
			],

			/**
			 * Flip Box
			 */
			'flip-box' => [
				'fields' => [
					[
						'field'       => 'front_title',
						'type'        => __( 'Flip Box: Front Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'front_description',
						'type'        => __( 'Flip Box: Front Description', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'back_title',
						'type'        => __( 'Flip Box: Back Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'back_description',
						'type'        => __( 'Flip Box: Back Description', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
				],
			],

			/**
			 * Fun Factor
			 */
			'fun-factor' => [
				'fields' => [
					[
						'field'       => 'fun_factor_title',
						'type'        => __( 'Fun Factor: Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Gradient Heading
			 */
			'gradient-heading' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Gradient_Heading: Title', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
					'link' => [
						'field'       => 'url',
						'type'        => __( 'Gradient_Heading: Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
				],
			],

			/**
			 * Icon Box
			 */
			'icon-box' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Icon Box: Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'badge_text',
						'type'        => __( 'Icon Box: Badge Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					'link' => [
						'field'       => 'url',
						'type'        => __( 'Icon Box: Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
				],
			],

			/**
			 * Image Compare
			 */
			'image-compare' => [
				'fields' => [
					[
						'field'       => 'before_label',
						'type'        => __( 'Image Compare: Before Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'after_label',
						'type'        => __( 'Image Compare: After Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Image Grid
			 */
			'image-grid' => [
				'fields' => [
					[
						'field'       => 'all_filter_label',
						'type'        => __( 'Image Grid: All Filter Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Image_Grid',
				]
			],

			/**
			 * Info Box
			 */
			'infobox' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Info Box: Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'description',
						'type'        => __( 'Info Box: Description', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'button_text',
						'type'        => __( 'Info Box: Button Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					'button_link' => [
						'field'       => 'url',
						'type'        => __( 'Info Box: Button Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
				],
			],

			/**
			 * Justified Gallery
			 */
			'justified-gallery' => [
				'fields' => [
					[
						'field'       => 'all_filter_label',
						'type'        => __( 'Justified Grid: All Filter Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Justified_Gallery',
				]
			],

			/**
			 * Logo Grid
			 */
			'logo-grid' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Logo_Grid',
				]
			],

			/**
			 * Team Member
			 */
			'member' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Team Member: Name', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'job_title',
						'type'        => __( 'Team Member: Job Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'bio',
						'type'        => __( 'Team Member: Short Bio', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
				],
			],

			/**
			 * News Ticker
			 */
			'news-ticker' => [
				'fields' => [
					[
						'field'       => 'sticky_title',
						'type'        => __( 'News Ticker: Sticky Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Number
			 */
			'number' => [
				'fields' => [
					[
						'field'       => 'number_text',
						'type'        => __( 'Number: Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Pricing Table
			 */
			'pricing-table' => [
				'fields' => [
					[
						'field'       => 'title',
						'type'        => __( 'Pricing Table: Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'price',
						'type'        => __( 'Pricing Table: Price', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'period',
						'type'        => __( 'Pricing Table: Period', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'features_title',
						'type'        => __( 'Pricing Table: Features Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'button_text',
						'type'        => __( 'Pricing Table: Button Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					'button_link' => [
						'field'       => 'url',
						'type'        => __( 'Pricing Table: Button Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
					[
						'field'       => 'badge_text',
						'type'        => __( 'Pricing Table: Badge Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Pricing_Table',
				]
			],

			/**
			 * Review
			 */
			'review' => [
				'fields' => [
					[
						'field'       => 'review',
						'type'        => __( 'Review: Review Text', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'title',
						'type'        => __( 'Review: Reviewer Name', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'job_title',
						'type'        => __( 'Review: Job Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Skills
			 */
			'skills' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Skills',
				]
			],

			/**
			 * Slider
			 */
			'slider' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Slider',
				]
			],

			/**
			 * Social Icons
			 */
			'social-icons' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Social_Icons',
				]
			],

			/**
			 * Step Flow
			 */
			'step-flow' => [
				'fields' => [
					[
						'field'       => 'badge',
						'type'        => __( 'Step Flow: Badge Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'title',
						'type'        => __( 'Step Flow: Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'description',
						'type'        => __( 'Step Flow: Description', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
					'link' => [
						'field'       => 'url',
						'type'        => __( 'Step Flow: Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
				],
			],

			/**
			 * Testimonial
			 */
			'testimonial' => [
				'fields' => [
					[
						'field'       => 'testimonial',
						'type'        => __( 'Testimonial: Testimonial Text', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'name',
						'type'        => __( 'Testimonial: Reviewer Name', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'title',
						'type'        => __( 'Testimonial: Job Title', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Twitter Feed
			 */
			'twitter-feed' => [
				'fields' => [
					[
						'field'       => 'read_more_text',
						'type'        => __( 'Twitter Feed: Read More Text', 'happy-elementor-addons' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'load_more_text',
						'type'        => __( 'Twitter Feed: Load More Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Data table
			 */
			'data-table' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Data_Table_Column_Data',
					__NAMESPACE__ . '\\WPML_Data_Table_Row_Data',
				]
			],

			/**
			 * Horizontal Timeline
			 */
			'horizontal-timeline' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Horizontal_Timeline',
				]
			],


			/**
			 * Mailchimp
			 */
			'mailchimp' => [
				'fields' => [
					[
						'field'       => 'fname_label',
						'type'        => __( 'MailChimp: First Name Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'fname_placeholder',
						'type'        => __( 'MailChimp: First Name Place Holder', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'lname_label',
						'type'        => __( 'MailChimp: Last Name Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'lname_placeholder',
						'type'        => __( 'MailChimp: Last Name Place Holder', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'phone_label',
						'type'        => __( 'MailChimp: Phone Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'phone_placeholder',
						'type'        => __( 'MailChimp: Phone Place Holder', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'email_label',
						'type'        => __( 'MailChimp: Email Label', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'email_placeholder',
						'type'        => __( 'MailChimp: Email Place Holder', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'button_text',
						'type'        => __( 'MailChimp: Button Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'mailchimp_success_message',
						'type'        => __( 'MailChimp: Success Message', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
				],
			],

			/**
			 * Image Accordion
			 */
			'image-accordion' => [
				'fields' => [],
				'integration-class' => [
					__NAMESPACE__ . '\\WPML_Image_Accordion',
				]
			],

			/*
			 * Content Switcher
			 */
			'content-switcher' => [
				'fields' => [],
				'integration-class' => __NAMESPACE__ . '\\WPML_Content_Switcher',
			],

			/**
			 * Creative Button
			 */
			'creative-button' => [
				'fields' => [
					[
						'field'       => 'button_text',
						'type'        => __( 'Creative Button: Text', 'happy-elementor-addons' ),
						'editor_type' => 'LINE',
					],
					'button_link' => [
						'field'       => 'button_link',
						'type'        => __( 'Creative Button: Link', 'happy-elementor-addons' ),
						'editor_type' => 'LINK',
					],
				],
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
