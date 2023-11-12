<?php
/**
 * UAEL WPML compatibility.
 *
 * @package UAEL
 */

namespace UltimateElementor\Compatibility;

/**
 * Class UAEL_Wpml.
 */
class UAEL_Wpml {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// WPML String Translation plugin exist check.
		if ( is_wpml_string_translation_active() ) {
			$wpml_version = defined( 'ICL_SITEPRESS_VERSION' ) ? true : false;

			if ( $wpml_version ) {
				$this->includes();
				add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'translatable_widgets' ) );
			}
		}
	}

	/**
	 * Integrations class for complex widgets.
	 *
	 * @since 1.2.0
	 */
	public function includes() {

		include_once 'modules/social-share.php';
		include_once 'modules/buttons.php';
		include_once 'modules/table.php';
		include_once 'modules/google-map.php';
		include_once 'modules/price-list.php';
		include_once 'modules/business-hours.php';
		include_once 'modules/price-table.php';
		include_once 'modules/video-gallery.php';
		include_once 'modules/timeline.php';
		include_once 'modules/hotspot.php';
		include_once 'modules/nav-menu.php';
		include_once 'modules/faq.php';
		include_once 'modules/registration-form.php';
		include_once 'modules/how-to.php';
	}

	/**
	 * Widgets to translate.
	 *
	 * @since 1.2.0
	 * @param array $widgets Widget array.
	 * @return array
	 */
	public function translatable_widgets( $widgets ) {
		$widgets['uael-advanced-heading']   = array(
			'conditions' => array( 'widgetType' => 'uael-advanced-heading' ),
			'fields'     => array(
				array(
					'field'       => 'heading_title',
					'type'        => __( ' Advanced Heading : Heading', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'sub_heading',
					'type'        => __( ' Advanced Heading : Sub Heading', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'heading_description',
					'type'        => __( 'Advanced Heading : Description', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'heading_line_text',
					'type'        => __( 'Advanced Heading : Separator Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				'heading_link'       => array(
					'field'       => 'url',
					'type'        => __( 'Advanced Heading : Text Link', 'uael' ),
					'editor_type' => 'LINK',
				),
				'heading_image_link' => array(
					'field'       => 'url',
					'type'        => __( 'Advanced Heading : Photo URL', 'uael' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'bg_text',
					'type'        => __( 'Advanced Heading : Background Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);
		$widgets['uael-marketing-button']   = array(
			'conditions' => array( 'widgetType' => 'uael-marketing-button' ),
			'fields'     => array(
				array(
					'field'       => 'text',
					'type'        => __( 'Marketing Button : Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'desc_text',
					'type'        => __( 'Marketing Button : Description', 'uael' ),
					'editor_type' => 'AREA',
				),
				'link' => array(
					'field'       => 'url',
					'type'        => __( 'Marketing Button : Link', 'uael' ),
					'editor_type' => 'LINK',
				),
			),
		);
		$widgets['uael-dual-color-heading'] = array(
			'conditions' => array( 'widgetType' => 'uael-dual-color-heading' ),
			'fields'     => array(
				array(
					'field'       => 'before_heading_text',
					'type'        => __( 'Dual Color Heading : Before Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'second_heading_text',
					'type'        => __( 'Dual Color Heading : Highlighted Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'after_heading_text',
					'type'        => __( 'Dual Color Heading : After Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				'heading_link' => array(
					'field'       => 'url',
					'type'        => __( 'Dual Color Heading : Link', 'uael' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'bg_text',
					'type'        => __( 'Dual Color Heading : Background Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-fancy-heading'] = array(
			'conditions' => array( 'widgetType' => 'uael-fancy-heading' ),
			'fields'     => array(
				array(
					'field'       => 'fancytext_prefix',
					'type'        => __( 'Fancy Heading : Before Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'fancytext',
					'type'        => __( 'Fancy Heading : Fancy Text', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'fancytext_suffix',
					'type'        => __( 'Fancy Heading : After Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-ba-slider'] = array(
			'conditions' => array( 'widgetType' => 'uael-ba-slider' ),
			'fields'     => array(
				array(
					'field'       => 'before_text',
					'type'        => __( 'Before After Slider : Before Label', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'after_text',
					'type'        => __( 'Before After Slider : After Label', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-content-toggle'] = array(
			'conditions' => array( 'widgetType' => 'uael-content-toggle' ),
			'fields'     => array(
				array(
					'field'       => 'rbs_section_heading_1',
					'type'        => __( 'Content Toggle : Heading 1', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'section_content_1',
					'type'        => __( 'Content Toggle : Description 1', 'uael' ),
					'editor_type' => 'VISUAL',
				),
				array(
					'field'       => 'rbs_section_heading_2',
					'type'        => __( 'Content Toggle : Heading 2', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'section_content_2',
					'type'        => __( 'Content Toggle : Description 2', 'uael' ),
					'editor_type' => 'VISUAL',
				),
			),
		);

		$widgets['uael-modal-popup'] = array(
			'conditions' => array( 'widgetType' => 'uael-modal-popup' ),
			'fields'     => array(
				array(
					'field'       => 'title',
					'type'        => __( 'Modal Popup : Modal Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'ct_content',
					'type'        => __( 'Modal Popup : Modal Description', 'uael' ),
					'editor_type' => 'VISUAL',
				),
				array(
					'field'       => 'modal_text',
					'type'        => __( 'Modal Popup : Display on Text - Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'btn_text',
					'type'        => __( 'Modal Popup : Display on Button - Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-infobox'] = array(
			'conditions' => array( 'widgetType' => 'uael-infobox' ),
			'fields'     => array(
				array(
					'field'       => 'infobox_title_prefix',
					'type'        => __( 'Info Box : Title Prefix', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'infobox_title',
					'type'        => __( 'Info Box : Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'infobox_description',
					'type'        => __( 'Info Box : Description', 'uael' ),
					'editor_type' => 'VISUAL',
				),
				array(
					'field'       => 'infobox_link_text',
					'type'        => __( 'Info Box : CTA Link Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'infobox_button_text',
					'type'        => __( 'Info Box : CTA Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				'infobox_text_link'  => array(
					'field'       => 'url',
					'type'        => __( 'Info Box : CTA Link', 'uael' ),
					'editor_type' => 'LINK',
				),
				'infobox_image_link' => array(
					'field'       => 'url',
					'type'        => __( 'Info Box : Photo URL', 'uael' ),
					'editor_type' => 'LINK',
				),
			),
		);

		$widgets['uael-buttons'] = array(
			'conditions'        => array( 'widgetType' => 'uael-buttons' ),
			'fields'            => array(),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\Buttons',
		);

		$widgets['uael-table'] = array(
			'conditions'        => array( 'widgetType' => 'uael-table' ),
			'fields'            => array(
				array(
					'field'       => 'search_text',
					'type'        => __( 'Table : Search Label', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\Table',
		);

		$widgets['uael-google-map'] = array(
			'conditions'        => array( 'widgetType' => 'uael-google-map' ),
			'fields'            => array(),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\GoogleMap',
		);

		$widgets['uael-price-list'] = array(
			'conditions'        => array( 'widgetType' => 'uael-price-list' ),
			'fields'            => array(),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\PriceList',
		);

		$widgets['uael-video-gallery'] = array(
			'conditions'        => array( 'widgetType' => 'uael-video-gallery' ),
			'fields'            => array(
				array(
					'field'       => 'filters_heading_text',
					'type'        => __( ' Video Gallery : Title Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'filters_all_text',
					'type'        => __( ' Video Gallery : "All" Tab Label', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\VideoGallery',
		);

		$widgets['uael-video'] = array(
			'conditions' => array( 'widgetType' => 'uael-video' ),
			'fields'     => array(
				array(
					'field'       => 'sticky_info_bar_text',
					'type'        => __( ' Video : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-price-table'] = array(
			'conditions'        => array( 'widgetType' => 'uael-price-table' ),
			'fields'            => array(
				array(
					'field'       => 'heading',
					'type'        => __( 'Price Table : Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'sub_heading',
					'type'        => __( 'Price Table : Description', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'price',
					'type'        => __( 'Price Table : Price', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'original_price',
					'type'        => __( 'Price Table : Original Price', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'duration',
					'type'        => __( 'Price Table : Duration', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'sub_heading_style2',
					'type'        => __( 'Price Table : Description', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'cta_text',
					'type'        => __( 'Price Table : CTA Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'link',
					'type'        => __( 'Price Table : CTA Link', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'footer_additional_info',
					'type'        => __( 'Price Table : Disclaimer Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'ribbon_title',
					'type'        => __( 'Price Table : Ribbon Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				'link' => array(
					'field'       => 'url',
					'type'        => __( 'Price Table : Link', 'uael' ),
					'editor_type' => 'LINK',
				),
			),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\PriceTable',
		);

		$widgets['uael-timeline']       = array(
			'conditions'        => array( 'widgetType' => 'uael-timeline' ),
			'fields'            => array(),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\Timeline',
		);
		$widgets['uael-faq']            = array(
			'conditions'        => array( 'widgetType' => 'uael-faq' ),
			'fields'            => array(),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\FAQ',
		);
		$widgets['uael-business-hours'] = array(
			'conditions'        => array( 'widgetType' => 'uael-business-hours' ),
			'fields'            => array(),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\BusinessHours',
		);

		$widgets['uael-image-gallery'] = array(
			'conditions' => array( 'widgetType' => 'uael-image-gallery' ),
			'fields'     => array(
				array(
					'field'       => 'filters_all_text',
					'type'        => __( 'Image Galley : "All" Tab Label', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-woo-add-to-cart'] = array(
			'conditions' => array( 'widgetType' => 'uael-woo-add-to-cart' ),
			'fields'     => array(
				array(
					'field'       => 'btn_text',
					'type'        => __( 'Woo - Add To Cart : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-posts'] = array(
			'conditions' => array( 'widgetType' => 'uael-posts' ),
			'fields'     => array(
				array(
					'field'       => 'no_results_text',
					'type'        => __( ' Display Message : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'classic_cta_text',
					'type'        => __( 'CTA Text - Classic : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'card_cta_text',
					'type'        => __( 'CTA Text - Card : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'event_cta_text',
					'type'        => __( 'CTA Text - Event : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'feed_cta_text',
					'type'        => __( 'CTA Text - Creative Feeds : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'news_cta_text',
					'type'        => __( 'CTA Text - News : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'business_cta_text',
					'type'        => __( 'CTA Text - Business Card : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'classic_load_more_text',
					'type'        => __( '"Load More" Label - Classic : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'card_load_more_text',
					'type'        => __( '"Load More" Label - Card : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'event_load_more_text',
					'type'        => __( '"Load More" Label - Event : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'feed_load_more_text',
					'type'        => __( '"Load More" Label - Creative Feeds : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'business_load_more_text',
					'type'        => __( '"Load More" Label - Business Card : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'business_writtenby_text',
					'type'        => __( 'Author Info Text - Business Card : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-hotspot'] = array(
			'conditions'        => array( 'widgetType' => 'uael-hotspot' ),
			'fields'            => array(
				array(
					'field'       => 'overlay_button_text',
					'type'        => __( 'Hotspot : Overlay Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\Hotspot',
		);

		$widgets['uael-how-to'] = array(
			'conditions'        => array( 'widgetType' => 'uael-how-to' ),
			'fields'            => array(
				array(
					'field'       => 'title',
					'type'        => __( 'How-to Schema: Heading', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'description',
					'type'        => __( 'How-to Schema: Description', 'uael' ),
					'editor_type' => 'VISUAL',
				),
				array(
					'field'       => 'time_text',
					'type'        => __( 'How-to Schema: Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'cost_text',
					'type'        => __( 'How-to Schema: Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'tools_text',
					'type'        => __( 'How-to Schema: Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'supply_text',
					'type'        => __( 'How-to Schema: Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'steps_text',
					'type'        => __( 'How-to Schema: Title', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\HowTo',
		);

		$widgets['uael-offcanvas'] = array(
			'conditions' => array( 'widgetType' => 'uael-offcanvas' ),
			'fields'     => array(
				array(
					'field'       => 'ct_content',
					'type'        => __( 'Off-Canvas : Content', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'btn_text',
					'type'        => __( 'Off-Canvas : Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-business-reviews'] = array(
			'conditions' => array( 'widgetType' => 'uael-business-reviews' ),
			'fields'     => array(
				array(
					'field'       => 'default_read_more',
					'type'        => __( 'Business Reviews - Default : Read More Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'card_read_more',
					'type'        => __( 'Business Reviews - Card : Read More Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'bubble_read_more',
					'type'        => __( 'Business Reviews - Bubble : Read More Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-countdown'] = array(
			'conditions' => array( 'widgetType' => 'uael-countdown' ),
			'fields'     => array(
				array(
					'field'       => 'message_after_expire',
					'type'        => __( 'Countdown : Message', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'custom_days',
					'type'        => __( 'Countdown : Label for Days', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'custom_hours',
					'type'        => __( 'Countdown : Label for Hours', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'custom_minutes',
					'type'        => __( 'Countdown : Label for Minutes', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'custom_seconds',
					'type'        => __( 'Countdown : Label for Seconds', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'expire_redirect_url',
					'type'        => __( 'Countdown : Redirect URL', 'uael' ),
					'editor_type' => 'LINK',
				),
			),
		);

		$widgets['uael-wpf-styler'] = array(
			'conditions' => array( 'widgetType' => 'uael-wpf-styler' ),
			'fields'     => array(
				array(
					'field'       => 'form_title',
					'type'        => __( 'WPForms Styler : Form Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'form_desc',
					'type'        => __( 'WPForms Styler : Form Description', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-team-member'] = array(
			'conditions' => array( 'widgetType' => 'uael-team-member' ),
			'fields'     => array(
				array(
					'field'       => 'team_member_name',
					'type'        => __( 'Team Member : Name', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'team_member_desig',
					'type'        => __( 'Team Member : Designation', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'team_member_desc',
					'type'        => __( 'Team Member : Description', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-login-form'] = array(
			'conditions' => array( 'widgetType' => 'uael-login-form' ),
			'fields'     => array(
				array(
					'field'       => 'user_label',
					'type'        => __( 'Login Form : Username Label', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'user_placeholder',
					'type'        => __( 'Login Form : Username Placeholder', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'password_label',
					'type'        => __( 'Login Form : Password Label', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'password_placeholder',
					'type'        => __( 'Login Form : Password Placeholder', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'separator_line_text',
					'type'        => __( 'Login Form : Separator Line Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'button_text',
					'type'        => __( 'Login Form : Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'show_register_text',
					'type'        => __( 'Login Form : Register Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'show_lost_password_text',
					'type'        => __( 'Login Form : Lost your password Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-registration-form'] = array(
			'conditions'        => array( 'widgetType' => 'uael-registration-form' ),
			'fields'            => array(
				'redirect_url'      => array(
					'field'       => 'url',
					'type'        => __( 'Registration Form : Redirect URL', 'uael' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'login_text',
					'type'        => __( 'Registration Form : Login Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				'login_url'         => array(
					'field'       => 'url',
					'type'        => __( 'Registration Form : Login URL', 'uael' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'lost_password_text',
					'type'        => __( 'Registration Form : Lost Password Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				'lost_password_url' => array(
					'field'       => 'url',
					'type'        => __( 'Registration Form : Lost Password URL', 'uael' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'logged_in_text',
					'type'        => __( 'Registration Form : Message For Logged In Users', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'button_text',
					'type'        => __( 'Registration Form : Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'email_subject',
					'type'        => __( 'Registration Form : Email Subject', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'email_content',
					'type'        => __( 'Registration Form : Email Content', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'validation_success_message',
					'type'        => __( 'Registration Form :Success Message', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'validation_error_message',
					'type'        => __( 'Registration Form : Error Message', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\RegistrationForm',
		);

		$widgets['uael-social-share'] = array(
			'conditions'        => array( 'widgetType' => 'uael-social-share' ),
			'fields'            => array(
				array(
					'field'       => 'share_url',
					'type'        => __( 'Social Share : Link', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'custom_share_text',
					'type'        => __( 'Social Share : Custom Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\SocialShare',
		);

		$widgets['uael-gf-styler'] = array(
			'conditions' => array( 'widgetType' => 'uael-gf-styler' ),
			'fields'     => array(
				array(
					'field'       => 'form_title',
					'type'        => __( 'Gravity Form Styler : Form Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'form_desc',
					'type'        => __( 'Gravity Form Styler : Form Description', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-nav-menu'] = array(
			'conditions'        => array( 'widgetType' => 'uael-nav-menu' ),
			'fields'            => array(),
			'integration-class' => '\UltimateElementor\Compatibility\WPML\Nav_Menu',
		);

		$widgets['uael-ff-styler'] = array(
			'conditions' => array( 'widgetType' => 'uael-ff-styler' ),
			'fields'     => array(
				array(
					'field'       => 'form_title',
					'type'        => __( 'WP Fluent Forms Styler : Form Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'form_desc',
					'type'        => __( 'WP Fluent Forms Styler : Form Description', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-mini-cart'] = array(
			'conditions' => array( 'widgetType' => 'uael-mini-cart' ),
			'fields'     => array(
				array(
					'field'       => 'cart_title',
					'type'        => __( 'Mini Cart : Cart Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'cart_message',
					'type'        => __( 'Mini Cart : Cart Message', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'cart_button_text',
					'type'        => __( 'Mini Cart : Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);

		$widgets['uael-woo-checkout'] = array(
			'conditions' => array( 'widgetType' => 'uael-woo-checkout' ),
			'fields'     => array(
				array(
					'field'       => 'labels_billing_section',
					'type'        => __( 'Woo - Checkout: Enter Billing Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'labels_shipping_section',
					'type'        => __( 'Woo - Checkout: Enter Shipping Text', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'labels_order_section',
					'type'        => __( 'Woo - Checkout: Enter Order Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'labels_payment_section',
					'type'        => __( 'Woo - Checkout: Enter Payment Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'labels_back_to_cart',
					'type'        => __( 'Woo - Checkout: Enter Cart Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'labels_previous_btn',
					'type'        => __( 'Woo - Checkout: Enter Previous Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'labels_next_btn',
					'type'        => __( 'Woo - Checkout: Enter Next Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'login_title',
					'type'        => __( 'Woo - Checkout: Enter Login Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'login_toggle_text',
					'type'        => __( 'Woo - Checkout: Enter Login Link Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'login_form_text',
					'type'        => __( 'Woo - Checkout: Enter Login Form Text', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'coupon_title',
					'type'        => __( 'Woo - Checkout: Enter Coupon Title', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'coupon_toggle_text',
					'type'        => __( 'Woo - Checkout: Enter Coupon Link Text', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'coupon_form_text',
					'type'        => __( 'Woo - Checkout: Enter Coupon Form Text', 'uael' ),
					'editor_type' => 'AREA',
				),
				array(
					'field'       => 'coupon_field_placeholder',
					'type'        => __( 'Woo - Checkout: Enter Coupon Form Placeholder', 'uael' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'coupon_button_text',
					'type'        => __( 'Woo - Checkout: Enter Coupon Button Text', 'uael' ),
					'editor_type' => 'LINE',
				),
			),
		);
		return $widgets;
	}
}

/**
 *  Prepare if class 'UAEL_Wpml' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAEL_Wpml::get_instance();
