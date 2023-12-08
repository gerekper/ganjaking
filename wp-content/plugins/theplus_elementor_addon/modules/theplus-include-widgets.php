<?php
namespace TheplusAddons;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'Theplus_Widgets_Include' ) ) {

	/**
	 * Define Theplus_Widgets_Include class
	 */
	class Theplus_Widgets_Include {
		
		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;
		
		/**
		 * Check if processing elementor widget
		 *
		 * @var boolean
		 */
		 /**
		 * Localize data array
		 *
		 * @var array
		 */
		public $localize_data = array();
		
		/**
		 * ThePlus_Load constructor.
		 */
		private function __construct() {
			
			$this->required_fiels();
			theplus_generator()->init();
			theplus_library();
			$this->init();
			theplus_wpml_translate();
		}
		/**
		 * Initalize integration hooks
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'elementor/widgets/register', array($this, 'add_widgets' ) );
			add_filter( 'all_plugins',array($this,'tp_white_label_update') );
		}
		
		/**
		* Widget Include required files
		*
		*/
		public function required_fiels()
		{	
			require_once THEPLUS_PATH.'modules/enqueue/plus-library.php';
			require_once THEPLUS_PATH.'modules/enqueue/plus-generator.php';			
			require_once THEPLUS_PATH.'modules/enqueue/plus-wpml.php';
		}
		/**
		 * Add new controls.
		 *
		 * @param  object $widgets_manager Controls manager instance.
		 * @return void
		 */		
		public function add_widgets( $widgets_manager ) {

			$grouped = array(
				'theplus-widgets' => '\TheplusAddons\Widgets\Theplus_Elements_Widgets',
				'tp_smooth_scroll' => '\TheplusAddons\Widgets\ThePlus_Smooth_Scroll',
				'tp_accordion' => '\TheplusAddons\Widgets\ThePlus_Accordion',
				'tp_adv_text_block' => '\TheplusAddons\Widgets\ThePlus_Adv_Text_Block',
				'tp_advanced_typography' => '\TheplusAddons\Widgets\ThePlus_Advanced_Typography',
				'tp_advanced_buttons' => '\TheplusAddons\Widgets\ThePlus_Advanced_Buttons',
				'tp_advertisement_banner' => '\TheplusAddons\Widgets\ThePlus_Advertisement_Banner',
				'tp_age_gate' => '\TheplusAddons\Widgets\ThePlus_Age_Gate',
				'tp_animated_service_boxes' => '\TheplusAddons\Widgets\ThePlus_Animated_Service_Boxes',
				'tp_audio_player' => '\TheplusAddons\Widgets\ThePlus_Audio_Player',
				'tp_before_after' => '\TheplusAddons\Widgets\ThePlus_Before_After',
				'tp_blockquote' => '\TheplusAddons\Widgets\ThePlus_Block_Quote',
				'tp_blog_listout' => '\TheplusAddons\Widgets\ThePlus_Blog_ListOut',
				'tp_dynamic_smart_showcase' => '\TheplusAddons\Widgets\ThePlus_Dynamic_Smart_Showcase',
				'tp_breadcrumbs_bar' => '\TheplusAddons\Widgets\ThePlus_Breadcrumbs_Bar',
				'tp_button' => '\TheplusAddons\Widgets\ThePlus_Button',
				'tp_wp_bodymovin' => '\TheplusAddons\Widgets\ThePlus_Bodymovin_Animations',
				'tp_carousel_anything' => '\TheplusAddons\Widgets\ThePlus_Carousel_Anything',
				'tp_carousel_remote' => '\TheplusAddons\Widgets\ThePlus_Carousel_Remote',
				'tp_caldera_forms' => '\TheplusAddons\Widgets\ThePlus_Caldera_Forms',
				'tp_cascading_image' => '\TheplusAddons\Widgets\ThePlus_Cascading_Image',
				'tp_chart' => '\TheplusAddons\Widgets\ThePlus_Chart',
				'tp_circle_menu' => '\TheplusAddons\Widgets\ThePlus_Circle_Menu',
				'tp_clients_listout' => '\TheplusAddons\Widgets\ThePlus_Clients_ListOut',
				'tp_contact_form_7' => '\TheplusAddons\Widgets\ThePlus_Contact_Form_7',
				'tp_countdown' => '\TheplusAddons\Widgets\ThePlus_Countdown',
				'tp_coupon_code' => '\TheplusAddons\Widgets\ThePlus_Coupon_Code',
				'tp_design_tool' => '\TheplusAddons\Widgets\ThePlus_Design_Tool',
				'tp_dark_mode' => '\TheplusAddons\Widgets\ThePlus_Dark_Mode',
				'tp_draw_svg' => '\TheplusAddons\Widgets\ThePlus_Draw_Svg',
				'tp_dynamic_listing' => '\TheplusAddons\Widgets\ThePlus_Dynamic_Listing',
				'tp_custom_field' => '\TheplusAddons\Widgets\ThePlus_Custom_Field',
				'tp_dynamic_categories' => '\TheplusAddons\Widgets\ThePlus_Dynamic_Categories',				
				'tp_dynamic_device' => '\TheplusAddons\Widgets\ThePlus_Dynamic_Devices',				
				'tp_everest_form' => '\TheplusAddons\Widgets\ThePlus_Everest_form',
				'tp_flip_box' => '\TheplusAddons\Widgets\ThePlus_Flip_Box',
				'tp_gallery_listout' => '\TheplusAddons\Widgets\ThePlus_Gallery_ListOut',
				'tp_google_map' => '\TheplusAddons\Widgets\ThePlus_Google_Map',
				'tp_gravity_form' => '\TheplusAddons\Widgets\ThePlus_Gravity_Form',
				'tp_heading_animation' => '\TheplusAddons\Widgets\ThePlus_Heading_Animation',
				'tp_header_extras' => '\TheplusAddons\Widgets\ThePlus_Header_Extras',
				'tp_heading_title' => '\TheplusAddons\Widgets\Theplus_Ele_Heading_Title',
				'tp_hotspot' => '\TheplusAddons\Widgets\ThePlus_Hotspot',
				'tp_hovercard' => '\TheplusAddons\Widgets\ThePlus_Hovercard',
				'tp_horizontal_scroll_advance' => '\TheplusAddons\widgets\ThePlus_Horizontal_Scroll_Advance',
				'tp_image_factory' => '\TheplusAddons\Widgets\ThePlus_Image_Factory',
				'tp_info_box' => '\TheplusAddons\Widgets\ThePlus_Info_Box',
				'tp_instagram' => '\TheplusAddons\Widgets\ThePlus_Instagram',
				'tp_mailchimp' => '\TheplusAddons\Widgets\ThePlus_MailChimp_Subscribe',
				'tp_messagebox' => '\TheplusAddons\Widgets\ThePlus_MessageBox',
				'tp_meeting_scheduler' => '\TheplusAddons\Widgets\ThePlus_Meeting_Scheduler',
				'tp_mobile_menu' => '\TheplusAddons\Widgets\ThePlus_Mobile_Menu',				
				'tp_morphing_layouts' => '\TheplusAddons\Widgets\ThePlus_MorphingLayouts',
				'tp_mouse_cursor' => '\TheplusAddons\Widgets\ThePlus_Mouse_Cursor',
				'tp_navigation_menu_lite' => '\TheplusAddons\Widgets\ThePlus_Navigation_Menu_Lite',
				'tp_navigation_menu' => '\TheplusAddons\Widgets\ThePlus_Navigation_Menu',
				'tp_ninja_form' => '\TheplusAddons\Widgets\ThePlus_Ninja_form',
				'tp_number_counter' => '\TheplusAddons\Widgets\ThePlus_Number_Counter',
				'tp_post_title' => '\TheplusAddons\Widgets\ThePlus_Post_Title',				
				'tp_post_content' => '\TheplusAddons\Widgets\ThePlus_Post_Content',
				'tp_post_featured_image' => '\TheplusAddons\Widgets\ThePlus_Featured_Image',
				'tp_post_meta' => '\TheplusAddons\Widgets\ThePlus_Post_Meta',
				'tp_post_author' => '\TheplusAddons\Widgets\ThePlus_Post_Author',
				'tp_post_comment' => '\TheplusAddons\Widgets\ThePlus_Post_Comment',
				'tp_post_navigation' => '\TheplusAddons\Widgets\ThePlus_Post_Navigation',
				'tp_off_canvas' => '\TheplusAddons\Widgets\ThePlus_Off_Canvas',
				'tp_page_scroll' => '\TheplusAddons\Widgets\ThePlus_Page_Scroll',
				'tp_pre_loader' => '\TheplusAddons\Widgets\ThePlus_Pre_Loader',
				'tp_pricing_list' => '\TheplusAddons\Widgets\ThePlus_Pricing_List',
				'tp_pricing_table' => '\TheplusAddons\Widgets\ThePlus_Pricing_Table',
				'tp_product_listout' => '\TheplusAddons\Widgets\ThePlus_Product_ListOut',
				'tp_protected_content' => '\TheplusAddons\Widgets\ThePlus_Protected_Content',
				'tp_post_search' => '\TheplusAddons\Widgets\ThePlus_Post_Search',
				'tp_progress_bar' => '\TheplusAddons\Widgets\ThePlus_Progress_Bar',
				'tp_process_steps' => '\TheplusAddons\Widgets\ThePlus_Process_Steps',
				'tp_row_background' => '\TheplusAddons\Widgets\ThePlus_Row_Background',
				'tp_scroll_navigation' => '\TheplusAddons\Widgets\ThePlus_Scroll_Navigation',
				'tp_scroll_sequence' => '\TheplusAddons\Widgets\ThePlus_Scroll_Sequence',
				'tp_search_filter' => '\TheplusAddons\Widgets\ThePlus_Search_Filter',
				'tp_search_bar' => '\TheplusAddons\Widgets\ThePlus_Search_Bar',
				'tp_site_logo' => '\TheplusAddons\Widgets\ThePlus_Site_Logo',
				'tp_shape_divider' => '\TheplusAddons\Widgets\ThePlus_Tp_Shape_Divider',
				'tp_social_embed' => '\TheplusAddons\Widgets\ThePlus_Social_Embed',
				'tp_social_feed' => '\TheplusAddons\Widgets\ThePlus_Social_Feed',
				'tp_social_icon' => '\TheplusAddons\Widgets\ThePlus_Social_Icon',
				'tp_social_reviews' => '\TheplusAddons\Widgets\ThePlus_Social_Reviews',
				'tp_social_sharing' => '\TheplusAddons\Widgets\ThePlus_Social_Sharing',
				'tp_style_list' => '\TheplusAddons\Widgets\ThePlus_Style_List',
				'tp_switcher' => '\TheplusAddons\Widgets\ThePlus_Switcher',
				'tp_syntax_highlighter' => '\TheplusAddons\Widgets\ThePlus_Syntax_Highlighter',
				'tp_table' => '\TheplusAddons\Widgets\ThePlus_Data_Table',
				'tp_table_content' => '\TheplusAddons\Widgets\ThePlus_Table_Content',
				'tp_tabs_tours' => '\TheplusAddons\Widgets\ThePlus_Tabs_Tours',
				'tp_team_member_listout' => '\TheplusAddons\Widgets\ThePlus_Team_Member_ListOut',
				'tp_testimonial_listout' => '\TheplusAddons\Widgets\ThePlus_Testimonial_ListOut',
				'tp_timeline' => '\TheplusAddons\Widgets\ThePlus_TimeLine',				
				'tp_unfold' => '\TheplusAddons\Widgets\ThePlus_Unfold',				
				'tp_video_player' => '\TheplusAddons\Widgets\ThePlus_Video_Player',				
				'tp_wp_forms' => '\TheplusAddons\Widgets\ThePlus_Wp_Forms',
				'tp_woo_cart' => '\TheplusAddons\Widgets\ThePlus_Woo_Cart',
				'tp_woo_checkout' => '\TheplusAddons\Widgets\ThePlus_Woo_Checkout',		
				'tp_woo_myaccount' => '\TheplusAddons\Widgets\ThePlus_Woo_Myaccount',
				'tp_woo_order_track' => '\TheplusAddons\Widgets\ThePlus_Woo_Order_Track',
				'tp_woo_single_basic' => '\TheplusAddons\Widgets\ThePlus_Woo_Single_Basic',
				'tp_woo_single_image' => '\TheplusAddons\Widgets\ThePlus_Woo_Single_Image',
				'tp_woo_single_pricing' => '\TheplusAddons\Widgets\ThePlus_Woo_Single_Pricing',
				'tp_woo_single_tabs' => '\TheplusAddons\Widgets\ThePlus_Woo_Single_Tabs',
				'tp_woo_thank_you' => '\TheplusAddons\Widgets\ThePlus_Woo_Thank_You',
				'tp_wp_login_register' => '\TheplusAddons\Widgets\ThePlus_Wp_Login_Register',
			);
			
			$get_option=theplus_get_option('general','check_elements');
			if(!empty($get_option)){
				array_push($get_option, "theplus-widgets");
				if(!empty($get_option) && in_array("tp_dynamic_listing",$get_option)){
					array_push($get_option, "tp_custom_field");
				}
				foreach ( $grouped as $widget_id => $class_name ) {
					if(in_array($widget_id,$get_option)){
						if ( $this->include_widget( $widget_id, true ) ) {
							$widgets_manager->register( new $class_name() );
						}
					}
				}
			}
		}

		/*
		 * White label
		 * @since 3.0
		 */
		public function tp_white_label_update( $all_plugins ){
				$plugin_name =theplus_white_label_option('tp_plugin_name');
				$tp_plugin_desc =theplus_white_label_option('tp_plugin_desc');
				$tp_author_name =theplus_white_label_option('tp_author_name');
				$tp_author_uri =theplus_white_label_option('tp_author_uri');
			if(!empty($all_plugins[THEPLUS_PBNAME]) && is_array($all_plugins[THEPLUS_PBNAME])){
			$all_plugins[THEPLUS_PBNAME]['Name']           = ! empty( $plugin_name )     ? $plugin_name      : $all_plugins[THEPLUS_PBNAME]['Name'];
			$all_plugins[THEPLUS_PBNAME]['PluginURI']      = ! empty( $tp_author_uri )      ? $tp_author_uri       : $all_plugins[THEPLUS_PBNAME]['PluginURI'];
			$all_plugins[THEPLUS_PBNAME]['Description']    = ! empty( $tp_plugin_desc )     ? $tp_plugin_desc      : $all_plugins[THEPLUS_PBNAME]['Description'];
			$all_plugins[THEPLUS_PBNAME]['Author']         = ! empty( $tp_author_name )   ? $tp_author_name    : $all_plugins[THEPLUS_PBNAME]['Author'];
			$all_plugins[THEPLUS_PBNAME]['AuthorURI']      = ! empty( $tp_author_uri )      ? $tp_author_uri       : $all_plugins[THEPLUS_PBNAME]['AuthorURI'];
			$all_plugins[THEPLUS_PBNAME]['Title']          = ! empty( $plugin_name )     ? $plugin_name      : $all_plugins[THEPLUS_PBNAME]['Title'];
			$all_plugins[THEPLUS_PBNAME]['AuthorName']     = ! empty( $tp_author_name )   ? $tp_author_name    : $all_plugins[THEPLUS_PBNAME]['AuthorName'];

			return $all_plugins;
			}
		}
		/**
		 * Include control file by class name.
		 *
		 * @param  [type] $class_name [description]
		 * @return [type]             [description]
		 */
		public function include_widget( $widget_id, $grouped = false ) {

			$filename = sprintf('modules/widgets/'.$widget_id.'.php');

			if ( ! file_exists( THEPLUS_PATH.$filename ) ) {
				return false;
			}

			require THEPLUS_PATH.$filename;

			return true;
		}
		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $shortcodes = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Theplus_Widgets_Include
 *
 * @return object
 */
function theplus_widgets_include() {
	return Theplus_Widgets_Include::get_instance();
}