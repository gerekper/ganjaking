<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

Class Plus_Library
{
	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */	
	private static $instance = null;
	
	public $registered_widgets;
	public $get_plus_pro_widget_settings;
    /**
     *  Return array of registered elements.
     *
     * @todo filter output
     */	 
    public function get_registered_widgets()
    {
        return array_keys($this->registered_widgets);
    }
	
	public function __construct(){
		$this->get_plus_widget_settings();
		add_filter('plus_widget_setting', array( $this,'plus_pro_widget_setting'));
	}
	
	public function plus_pro_widget_setting($args){
		$args = array_merge($this->get_plus_pro_widget_settings, $args); 
		return $args;
	}
    /**
     * Return saved settings
     *
     * @since 2.0
     */
    public function get_plus_widget_settings($element = null)
    {
		$replace = [
			'tp_smooth_scroll' => 'tp-smooth-scroll',
			'tp_accordion' => 'tp-accordion',
			'tp_adv_text_block' => 'tp-adv-text-block',
			'tp_advanced_typography' => 'tp-advanced-typography',
			'tp_advanced_buttons' => 'tp-advanced-buttons',
			'tp_age_gate' => 'tp-age-gate',
			'tp_animated_service_boxes' => 'tp-animated-service-boxes',
			'tp_advertisement_banner' => 'tp_advertisement_banner',
			'tp_audio_player' => 'tp-audio-player',
			'tp_before_after' => 'tp-before-after',
			'tp_blockquote' => 'tp-blockquote',
			'tp_blog_listout' => 'tp-blog-listout',
			'tp_dynamic_smart_showcase' => 'tp-dynamic-smart-showcase',
			'tp_breadcrumbs_bar' => 'tp-breadcrumbs-bar',
			'tp_button' => 'tp-button',
			'tp_carousel_anything' => 'tp-carousel-anything',
			'tp_carousel_remote' => 'tp-carousel-remote',
			'tp_caldera_forms' => 'tp-caldera-forms',
			'tp_cascading_image' => 'tp-cascading-image',
			'tp_chart' => 'tp-chart',
			'tp_circle_menu' => 'tp-circle-menu',
			'tp_clients_listout' => 'tp-clients-listout',
			'tp_contact_form_7' => 'tp-contact-form-7',
			'tp_countdown' => 'tp-countdown',
			'tp_coupon_code' => 'tp-coupon-code',
			'tp_dark_mode' => 'tp-dark-mode',
			'tp_draw_svg' => 'tp-draw-svg',
			'tp_dynamic_device' => 'tp-dynamic-device',
			'tp_dynamic_listing' => 'tp-dynamic-listing',			
			'tp_everest_form' => 'tp-everest-form',
			'tp_flip_box' => 'tp-flip-box',
			'tp_gallery_listout' => 'tp-gallery-listout',
			'tp_google_map' => 'tp-google-map',
			'tp_gravity_form' => 'tp-gravityt-form',			
			'tp_heading_animation' => 'tp-heading-animation',
			'tp_header_extras' => 'tp-header-extras',
			'tp_heading_title' => 'tp-heading-title',
			'tp_hotspot' => 'tp-hotspot',
			'tp_hovercard' => 'tp-hovercard',
			'tp_horizontal_scroll_advance' => 'tp-horizontal-scroll-advance',
			'tp_image_factory' => 'tp-image-factory',
			'tp_info_box' => 'tp-info-box',
			'tp_instagram' => 'tp-instagram',
			'tp_mailchimp' => 'tp-mailchimp-subscribe',
			'tp_messagebox' => 'tp-messagebox',
			'tp_mobile_menu' => 'tp-mobile-menu',			
			'tp_morphing_layouts' => 'tp-morphing-layouts',
			'tp_mouse_cursor' => 'tp-mouse-cursor',
			'tp_navigation_menu_lite' => 'tp-navigation-menu-lite',
			'tp_navigation_menu' => 'tp-navigation-menu',
			'tp_ninja_form' => 'tp-ninja-form',
			'tp_number_counter' => 'tp-number-counter',
			'tp_post_title' => 'tp-post-title',
			'tp_post_content' => 'tp-post-content',
			'tp_post_featured_image' => 'tp-post-featured-image',
			'tp_post_meta' => 'tp-post-meta',
			'tp_post_author' => 'tp-post-author',
			'tp_post_comment' => 'tp-post-comment',
			'tp_post_navigation' => 'tp-post-navigation',
			'tp_off_canvas' => 'tp-off-canvas',
			'tp_page_scroll' => 'tp-page-scroll',
			'tp_pre_loader' => 'tp-pre-loader',
			'tp_pricing_list' => 'tp-pricing-list',
			'tp_pricing_table' => 'tp-pricing-table',
			'tp_product_listout' => 'tp-product-listout',
			'tp_protected_content' => 'tp-protected-content',
			'tp_post_search' => 'tp-post-search',
			'tp_progress_bar' => 'tp-progress-bar',
			'tp_process_steps' => 'tp-process-steps',
			'tp_row_background' => 'tp-row-background',
			'tp_scroll_navigation' => 'tp-scroll-navigation',
			'tp_scroll_sequence' => 'tp-scroll-sequence',
			'tp_search_filter' => 'tp-search-filter',
			'tp_search_bar' => 'tp-search-bar',
			'tp_site_logo' => 'tp-site-logo',
			'tp_shape_divider' => 'tp-shape-divider',
			'tp_social_embed' => 'tp-social-embed',
			'tp_social_feed' => 'tp-social-feed',
			'tp_social_icon' => 'tp-social-icon',
			'tp_social_reviews' => 'tp-social-reviews',
			'tp_social_sharing' => 'tp-social-sharing',
			'tp_style_list' => 'tp-style-list',
			'tp_switcher' => 'tp-switcher',
			'tp_syntax_highlighter' => 'tp-syntax-highlighter',
			'tp_table' => 'tp-table',
			'tp_table_content' => 'tp-table-content',
			'tp_tabs_tours' => 'tp-tabs-tours',
			'tp_team_member_listout' => 'tp-team-member-listout',
			'tp_testimonial_listout' => 'tp-testimonial-listout',
			'tp_timeline' => 'tp-timeline',
			'tp_video_player' => 'tp-video-player',
			'tp_unfold' => 'tp-unfold',
			'tp_dynamic_categories' => 'tp-dynamic-categories',
			'tp_wp_forms' => 'tp-wp-forms',
			'tp_woo_cart' => 'tp-woo-cart',
			'tp_woo_checkout' => 'tp-woo-checkout',
			'tp_woo_myaccount' => 'tp-woo-myaccount',
			'tp_woo_order_track' => 'tp-woo-order-track',
			'tp_woo_single_basic' => 'tp-woo-single-basic',
			'tp_woo_single_image' => 'tp-woo-single-image',
			'tp_woo_single_pricing' => 'tp-woo-single-pricing',
			'tp_woo_single_tabs' => 'tp-woo-single-tabs',
			'tp_woo_thank_you' => 'tp-woo-thank-you',
			'tp_wp_login_register' => 'tp-wp-login-register',
			'tp_custom_field' => 'tp-custom-field',
        ];
		$merge = [
			'plus-backend-editor'
		];
		
		$elements=theplus_get_option('general','check_elements');
		if(empty($elements)){
			$elements = array_keys($replace);
		}
		$plus_extras=theplus_get_option('general','extras_elements');
		$elements = array_map(function ($val) use ($replace) {
		    return (array_key_exists($val, $replace) ? $replace[$val] : $val);
        }, $elements);
		if(in_array('tp-shape-divider',$elements)){
			$merge[]= 'plus-wavify';
		}
		if(in_array('tp-dynamic-listing',$elements) || in_array('tp-product-listout',$elements)){
			$merge[]= 'tp-ajax-based-pagination';
		}
		if(in_array('tp-dynamic-listing',$elements)){
			$merge[]= 'tp-custom-field';
		}
		if(in_array('tp_advertisement_banner',$elements) || in_array('tp-cascading-image',$elements)){
			$merge[]= 'plus-hover3d';
		}
		if(in_array('tp-row-background',$elements)){
			$merge[]= 'plus-vegas-gallery';
			$merge[]= 'plus-row-animated-color';
			$merge[]= 'plus-row-segmentation';
			$merge[]= 'plus-row-scroll-color';
			$merge[]= 'plus-row-canvas-particle';
			$merge[]= 'plus-row-canvas-particleground';
			$merge[]= 'plus-row-canvas-8';
		}
		if(in_array('tp-number-counter',$elements)){
			$merge[]= 'tp-draw-svg';
		}
		if(in_array('tp-blog-listout',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';
			$merge[] = 'plus-listing-metro';
			$merge[] = 'plus-pagination';
		}
		if(in_array('tp-dynamic-smart-showcase',$elements)){
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';
		}
		if(in_array('tp-dynamic-listing',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';
			$merge[] = 'plus-listing-metro';
			$merge[] = 'plus-pagination';
		}
		if((in_array('tp-social-feed',$elements)) || (in_array('tp-social-reviews',$elements))){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';
		}
		if(in_array('tp-clients-listout',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';
			$merge[] = 'plus-pagination';
		}
		if(in_array('tp-dynamic-device',$elements)){
			$merge[] = 'plus-carousel';
		}
		if(in_array('tp-gallery-listout',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';
			$merge[] = 'plus-listing-metro';
		}
		if(in_array('tp-product-listout',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';
			$merge[] = 'plus-listing-metro';
			$merge[] = 'plus-pagination';
			$merge[] = 'plus-product-listout-yithcss';
			$merge[] = 'plus-product-listout-quickview';
		}
		if(in_array('tp-team-member-listout',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
			$merge[] = 'plus-post-filter';			
		}
		if(in_array('tp-testimonial-listout',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';
		}
		if(in_array('tp-page-scroll',$elements)){
			$merge[] = 'tp-fullpage';
			$merge[] = 'tp-pagepiling';
			$merge[] = 'tp-multiscroll';
			$merge[] = 'tp-horizontal-scroll';
		}
		if(in_array('tp-dynamic-categories',$elements)){
			$merge[] = 'plus-listing-masonry';
			$merge[] = 'plus-carousel';			
			$merge[] = 'plus-listing-metro';
		}
		
		if(!empty($plus_extras) && in_array('column_sticky',$plus_extras)){
			$merge[] ='plus-extras-column';
		}
		if(!empty($plus_extras) && in_array('column_mouse_cursor',$plus_extras)){
			$merge[] ='plus-column-cursor';
		}
		if(!empty($plus_extras) && in_array('section_scroll_animation',$plus_extras)){
			$merge[] ='plus-extras-section-skrollr';
		}
		if(!empty($plus_extras) && in_array('plus_equal_height',$plus_extras)){
			$merge[] ='plus-equal-height';
		}
		if(function_exists('tp_has_lazyload') && tp_has_lazyload()){		
			$merge[] ='plus-lazyLoad';
		}
		
		/*if(!empty($plus_extras) && in_array('plus_section_column_link',$plus_extras)){
			$merge[] ='plus-section-column-link';
		}*/	
		$result =array_unique($merge);
		$elements =array_merge($result , $elements);
		$this->get_plus_pro_widget_settings = (isset($element) ? (isset($elements[$element]) ? $elements[$element] : 0) : array_filter($elements));
        return $this->get_plus_pro_widget_settings;
    }

    /**
     * Check if elementor preview mode or not 
	 * @since 2.0
     */
    public function is_preview_mode()
    {
        if (isset($_POST['doing_wp_cron'])) {
            return true;
        }
        if (wp_doing_ajax()) {
            return true;
        }
        
		if (isset($_GET['elementor-preview']) && (int)$_GET['elementor-preview']) {
            return true;
        }
        if (isset($_POST['action']) && $_POST['action'] == 'elementor') {
            return true;
        }

        return false;
    }

	/**
	 * Returns the instance.
	 * @since  1.0.0
	 */
	public static function get_instance( $shortcodes = array() ) {
		
		if ( null == self::$instance ) {
			self::$instance = new self( $shortcodes );
		}
		return self::$instance;
	}
}
/**
 * Returns instance of Plus_Library
 */
function theplus_library() {
	return Plus_Library::get_instance();
}