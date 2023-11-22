<?php
/**
 * Premium Addons PRO WPML.
 */

namespace PremiumAddonsPro\Includes\Compatibility;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No access of directly access.
}

if ( ! class_exists( 'Premium_Pro_Wpml' ) ) {

	/**
	 * Class Premium_Pro_Wpml.
	 */
	class Premium_Pro_Wpml {

		/**
		 * Instance of the class
		 *
		 * @access private
		 * @since 3.1.9
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->includes();

			add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'translatable_widgets' ) );
		}

		/**
		 *
		 * Includes
		 *
		 * Integrations class for widgets with complex controls.
		 *
		 * @since 3.1.9
		 */
		public function includes() {

			include_once 'widgets/charts.php';
			include_once 'widgets/hotspots.php';
			include_once 'widgets/multiscroll.php';
			include_once 'widgets/horizontalscroll.php';
			include_once 'widgets/tabs.php';
			include_once 'widgets/table.php';
			include_once 'widgets/accordion.php';
			include_once 'widgets/image-layers.php';

		}

		/**
		 * Widgets to translate.
		 *
		 * @since 3.1.9
		 * @access public
		 *
		 * @param array $widgets Widget array.
		 *
		 * @return array
		 */
		public function translatable_widgets( $widgets ) {

			$widgets['premium-behance-feed'] = array(
				'conditions' => array( 'widgetType' => 'premium-behance-feed' ),
				'fields'     => array(
					array(
						'field'       => 'username',
						'type'        => __( 'Behance Feed: Username', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-chart'] = array(
				'conditions'        => array( 'widgetType' => 'premium-chart' ),
				'fields'            => array(
					array(
						'field'       => 'x_axis_label',
						'type'        => __( 'Charts: X-axis Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'x_axis_labels',
						'type'        => __( 'Charts: Data Labels', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'y_axis_label',
						'type'        => __( 'Charts: Y-axis Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'title',
						'type'        => __( 'Charts: Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\Charts',
			);

			$widgets['premium-addon-content-toggle'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-content-toggle' ),
				'fields'     => array(
					array(
						'field'       => 'premium_content_toggle_heading_one',
						'type'        => __( 'Content Switcher: First Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_content_toggle_heading_two',
						'type'        => __( 'Content Switcher: Second Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_content_toggle_first_content_templates',
						'type'        => __( 'Content Switcher: First Template ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_content_toggle_second_content_templates',
						'type'        => __( 'Content Switcher: Second Template ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_content_toggle_first_content_text',
						'type'        => __( 'Content Switcher: First Content', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_content_toggle_second_content_text',
						'type'        => __( 'Content Switcher: Second Content', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
				),
			);

			$widgets['premium-divider'] = array(
				'conditions' => array( 'widgetType' => 'premium-divider' ),
				'fields'     => array(
					array(
						'field'       => 'content_text',
						'type'        => __( 'Divider: Separator Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'content_link_title',
						'type'        => __( 'Divider: Link Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					'content_url' => array(
						'field'       => 'url',
						'type'        => __( 'Divider: Separator Link', 'premium-addons-pro' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-facebook-feed'] = array(
				'conditions' => array( 'widgetType' => 'premium-facebook-feed' ),
				'fields'     => array(
					array(
						'field'       => 'access_token',
						'type'        => __( 'Facebook Feed: Access Token', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'account_id',
						'type'        => __( 'Facebook Feed: User ID/Page Slug', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'read_text',
						'type'        => __( 'Facebook Feed: Show more Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-facebook-reviews'] = array(
				'conditions' => array( 'widgetType' => 'premium-facebook-reviews' ),
				'fields'     => array(
					array(
						'field'       => 'page_name',
						'type'        => __( 'Facebook Reviews: Page Name', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-fb-chat'] = array(
				'conditions' => array( 'widgetType' => 'premium-fb-chat' ),
				'fields'     => array(
					array(
						'field'       => 'premium_fbchat_login_msg',
						'type'        => __( 'Messanger Chat: Login Message', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_fbchat_logout_msg',
						'type'        => __( 'Messanger Chat: Logout Message', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-addon-flip-box'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-flip-box' ),
				'fields'     => array(
					array(
						'field'       => 'premium_flip_paragraph_header',
						'type'        => __( 'Hover Box: Front Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_flip_back_paragraph_header',
						'type'        => __( 'Hover Box: Back Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_flip_paragraph_text',
						'type'        => __( 'Hover Box: Front Description', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_flip_back_paragraph_text',
						'type'        => __( 'Hover Box: Back Description', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_flip_back_link_text',
						'type'        => __( 'Hover Box: Link Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					'premium_flip_back_link' => array(
						'field'       => 'url',
						'type'        => __( 'Hover Box: URL', 'premium-addons-pro' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-google-reviews'] = array(
				'conditions' => array( 'widgetType' => 'premium-google-reviews' ),
				'fields'     => array(
					array(
						'field'       => 'api_key',
						'type'        => __( 'Google Reviews: API Key', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'place_id',
						'type'        => __( 'Google Reviews: Place ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'language_prefix',
						'type'        => __( 'Google Reviews: Language Prefix', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-addon-icon-box'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-icon-box' ),
				'fields'     => array(
					array(
						'field'       => 'premium_icon_box_title',
						'type'        => __( 'Icon Box: Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_icon_box_label',
						'type'        => __( 'Icon Box: Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_icon_box_content',
						'type'        => __( 'Icon Box: Description', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_icon_box_more_text',
						'type'        => __( 'Icon Box: Read More Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					'premium_icon_box_link' => array(
						'field'       => 'url',
						'type'        => __( 'Icon Box: URL', 'premium-addons-pro' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-ihover'] = array(
				'conditions' => array( 'widgetType' => 'premium-ihover' ),
				'fields'     => array(
					array(
						'field'       => 'premium_ihover_thumbnail_link_text',
						'type'        => __( 'iHover: Link Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_ihover_thumbnail_back_title',
						'type'        => __( 'iHover: Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_ihover_thumbnail_back_description',
						'type'        => __( 'iHover: Description Text', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					'premium_ihover_thumbnail_url' => array(
						'field'       => 'url',
						'type'        => __( 'iHover: URL', 'premium-addons-pro' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-image-accordion'] = array(
				'conditions'        => array( 'widgetType' => 'premium-image-accordion' ),
				'fields'            => array(),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\Accordion',
			);

			$widgets['premium-addon-image-comparison'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-image-comparison' ),
				'fields'     => array(
					array(
						'field'       => 'premium_img_compare_original_img_label',
						'type'        => __( 'Image Comparison: First Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_image_comparison_modified_image_label',
						'type'        => __( 'Image Comparison: Second Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-addon-image-hotspots'] = array(
				'conditions'        => array( 'widgetType' => 'premium-addon-image-hotspots' ),
				'fields'            => array(),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\Hotspots',
			);

			$widgets['premium-img-layers-addon'] = array(
				'conditions'        => array( 'widgetType' => 'premium-img-layers-addon' ),
				'fields'            => array(),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\Image_Layers',
			);

			$widgets['premium-addon-magic-section'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-magic-section' ),
				'fields'     => array(
					array(
						'field'       => 'premium_magic_section_content',
						'type'        => __( 'Magic Section: Content', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_magic_section_content_temp',
						'type'        => __( 'Magic Section: Content Template ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_magic_section_button_text',
						'type'        => __( 'Magic Section: Button Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-multi-scroll'] = array(
				'conditions'        => array( 'widgetType' => 'premium-multi-scroll' ),
				'fields'            => array(
					array(
						'field'       => 'dots_tooltips',
						'type'        => __( 'Multi Scroll: Dots Tooltips Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\MultiScroll',
			);

			$widgets['premium-hscroll'] = array(
				'conditions'        => array( 'widgetType' => 'premium-hscroll' ),
				'fields'            => array(
					array(
						'field'       => 'dots_tooltips',
						'type'        => __( 'Horizontal Scroll: Dots Tooltips Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\HorizontalScroll',
			);

			$widgets['premium-notbar'] = array(
				'conditions' => array( 'widgetType' => 'premium-notbar' ),
				'fields'     => array(
					array(
						'field'       => 'premium_notbar_content_temp',
						'type'        => __( 'Alert Box: Content Template ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_notbar_text',
						'type'        => __( 'Alert Box: Content Text', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'premium_notbar_close_text',
						'type'        => __( 'Alert Box: Close Button Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					'premium_notbar_link' => array(
						'field'       => 'url',
						'type'        => __( 'Alert Box: URL', 'premium-addons-pro' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-addon-preview-image'] = array(
				'conditions' => array( 'widgetType' => 'premium-addon-preview-image' ),
				'fields'     => array(
					array(
						'field'       => 'trigger_text',
						'type'        => __( 'Preview Window: Trigger Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_preview_image_content_temp',
						'type'        => __( 'Preview Window: Content Template ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_preview_image_caption',
						'type'        => __( 'Preview Window: Caption', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_preview_image_title',
						'type'        => __( 'Preview Window: Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_preview_image_desc',
						'type'        => __( 'Preview Window: Description', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					'premium_preview_image_link' => array(
						'field'       => 'url',
						'type'        => __( 'Preview Window: URL', 'premium-addons-pro' ),
						'editor_type' => 'LINK',
					),
				),
			);

			$widgets['premium-addon-tabs'] = array(
				'conditions'        => array( 'widgetType' => 'premium-addon-tabs' ),
				'fields'            => array(),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\Tabs',
			);

			$widgets['premium-twitter-feed'] = array(
				'conditions' => array( 'widgetType' => 'premium-twitter-feed' ),
				'fields'     => array(
					array(
						'field'       => 'read_text',
						'type'        => __( 'Twitter Feed: Read More Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-yelp-reviews'] = array(
				'conditions' => array( 'widgetType' => 'premium-yelp-reviews' ),
				'fields'     => array(
					array(
						'field'       => 'readmore',
						'type'        => __( 'Yelp Reviews: Read More Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-unfold-addon'] = array(
				'conditions' => array( 'widgetType' => 'premium-unfold-addon' ),
				'fields'     => array(
					array(
						'field'       => 'premium_unfold_title',
						'type'        => __( 'Unfold: Title', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_unfold_content',
						'type'        => __( 'Unfold: Content', 'premium-addons-pro' ),
						'editor_type' => 'AREA',
					),
					array(
						'field'       => 'content_temp',
						'type'        => __( 'Unfold: Content Template ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_unfold_button_fold_text',
						'type'        => __( 'Unfold: Unfold Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_unfold_button_unfold_text',
						'type'        => __( 'Unfold: Fold Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-whatsapp-chat'] = array(
				'conditions' => array( 'widgetType' => 'premium-whatsapp-chat' ),
				'fields'     => array(
					array(
						'field'       => 'number',
						'type'        => __( 'Whatsapp: Phone Number', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'group_id',
						'type'        => __( 'Whatsapp: Group ID', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'button_text',
						'type'        => __( 'Whatsapp: Button Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'tooltips_msg',
						'type'        => __( 'Whatsapp: Tooltip Text', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$widgets['premium-tables-addon'] = array(
				'conditions'        => array( 'widgetType' => 'premium-tables-addon' ),
				'fields'            => array(
					array(
						'field'       => 'premium_table_search_placeholder',
						'type'        => __( 'Table: Search Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'premium_table_records_label',
						'type'        => __( 'Table: Records Label', 'premium-addons-pro' ),
						'editor_type' => 'LINE',
					),
				),
				'integration-class' => 'PremiumAddonsPro\Compatibility\WPML\Widgets\Table',
			);

			return $widgets;
		}

		/**
		 * Creates and returns an instance of the class
		 *
		 * @since 1.4.8
		 * @access public
		 *
		 * @return object
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

	}

}
