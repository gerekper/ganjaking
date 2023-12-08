<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

Class Plus_Generator
{
	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;
	
	public $transient_widgets;
	public $registered_widgets;
    
	/**
     * Find Widgets in a page or post
     *
     * @since 2.0
     */
    public function collect_transient_widgets($widget)
    {
        if($widget->get_name() === 'global') {
            $global_widget = new \ReflectionClass(get_class($widget));
			
            $template_data = $global_widget->getProperty('template_data');
			
            $template_data->setAccessible(true);

            if($data_global = $template_data->getValue($widget)) {
				$widget_name=$this->get_global_widgets_use($data_global['content']);
				$widget_options=in_array($widget_name[0],array_keys($this->registered_widgets));
				if(!empty($widget_options) && $widget_options=='1'){
					$options=$widget->get_settings();					
					$this->plus_widgets_options($options,$widget_name[0]);
				}
                $this->transient_widgets = array_merge($this->transient_widgets, $widget_name);				
            }
        } else {
            $this->transient_widgets[] = $widget->get_name();
			$widget_options=in_array($widget->get_name(),array_keys($this->registered_widgets));
			
			if(!empty($widget->get_name()) && $widget->get_name()=='column'){
				$options=$widget->get_settings();
				if(!empty($options["plus_column_sticky"]) && $options["plus_column_sticky"]=='true'){					
					$this->transient_widgets[] = 'plus-extras-column';
				}
				if(!empty($options["plus_column_cursor_point"]) && $options["plus_column_cursor_point"]=='yes'){					
					$this->transient_widgets[] = 'plus-column-cursor';
				}
			}
			
			$options=$widget->get_settings();
			if(!empty($options["seh_switch"]) && $options["seh_switch"]=='yes'){					
				$this->transient_widgets[] = 'plus-equal-height';
			}
			
			if((!empty($options["sc_link_switch"]) && $options["sc_link_switch"]=='yes') && !empty($options['sc_link']['url'])){					
				$this->transient_widgets[] = 'plus-section-column-link';
			}

			if(((!empty($options["plus_eto_fb"]) && $options["plus_eto_fb"]=='yes')) || ((!empty($options["plus_eto_gtag"]) && $options["plus_eto_gtag"]=='yes')) ){				
				$this->transient_widgets[] = 'plus-event-tracker';
			}

			if(function_exists('tp_has_lazyload') && tp_has_lazyload()){	
				$this->transient_widgets[] = 'plus-lazyLoad';
			}
			
			if(!empty($widget->get_name()) && $widget->get_name()=='section' || !empty($widget->get_name()) && $widget->get_name()=='container'){
				$options=$widget->get_settings();
				if((!empty($options["plus_section_scroll_animation_in"]) && $options["plus_section_scroll_animation_in"]!='none') || (!empty($options["plus_section_scroll_animation_out"]) && $options["plus_section_scroll_animation_out"]!='none')){
					$this->transient_widgets[] = 'plus-extras-section-skrollr';
				}
			}
			
			if(!empty($widget_options) && $widget_options=='1'){
				$options=$widget->get_settings();
				$this->plus_widgets_options($options,$widget->get_name());
			}
		}
    }
	
	/**
     * Find global widgets
     * @since 2.0.2
     */
    public function get_global_widgets_use($widgets) {
        $get_widget = [];

        array_walk_recursive($widgets, function($val, $key) use (&$get_widget) {
            if($key == 'widgetType') {
                $get_widget[] = $val;
            }
        });

        return $get_widget;
    }
	
	public function tp_pro_transient_widget($args) {	 
		$args = array_merge($this->transient_widgets, $args);
		return $args;
	}
	
	/**
	* Check Widgets Options
	* @since 2.0.2
	*/
	public function plus_widgets_options($options='',$widget_name=''){
		
		if(!empty($options["animation_effects"]) && $options["animation_effects"]!='no-animation'){
			$this->transient_widgets[] = 'plus-velocity';
		}
		if((!empty($options["magic_scroll"]) && $options["magic_scroll"]=='yes') || (!empty($widget_name) && $widget_name=='tp-button' && !empty($options["btn_magic_scroll"]) && $options["btn_magic_scroll"]=='yes')){
			$this->transient_widgets[] = 'plus-magic-scroll';
		}
		if(!empty($options["plus_tooltip"]) && $options["plus_tooltip"]=='yes'){
			$this->transient_widgets[] = 'plus-tooltip';
		}
		if(!empty($options["plus_mouse_move_parallax"]) && $options["plus_mouse_move_parallax"]=='yes'){
			$this->transient_widgets[] = 'plus-mousemove-parallax';
		}
		if(!empty($options["plus_tilt_parallax"]) && $options["plus_tilt_parallax"]=='yes'){
			$this->transient_widgets[] = 'plus-tilt-parallax';
		}
		if((!empty($options["plus_overlay_effect"]) && $options["plus_overlay_effect"]=='yes') || (!empty($widget_name) && $widget_name=='tp-button' && !empty($options["btn_special_effect"]) && $options["btn_special_effect"]=='yes')){
			$this->transient_widgets[] = 'plus-reveal-animation';
		}
		if(!empty($options["loop_display_button"]) && $options["loop_display_button"]=='yes'){
			$this->transient_widgets[] = 'plus-button-extra';
		}
		if(!empty($widget_name) && $widget_name=='tp-advanced-typography' && !empty($options["typography_listing"]) && $options["typography_listing"]=='listing'){
			$this->transient_widgets[] = 'plus-magic-scroll';
			$this->transient_widgets[] = 'plus-mousemove-parallax';
		}
		if(!empty($widget_name) && $widget_name=='tp_advertisement_banner' && !empty($options["display_button"]) && $options["display_button"]=='yes'){
			$this->transient_widgets[] = 'plus-button';
		}
		if(!empty($widget_name) && $widget_name=='tp_advertisement_banner'){
			$this->transient_widgets[] = 'plus-content-hover-effect';
		}
		if(!empty($widget_name) && $widget_name=='tp-button' && !empty($options["btn_hover_effects"])){
			$this->transient_widgets[] = 'plus-content-hover-effect';
		}
		
		if(!empty($widget_name) && $widget_name=='tp-blog-listout' || !empty($widget_name) && $widget_name=='tp-product-listout' || !empty($widget_name) && $widget_name=='tp-dynamic-listing'){			
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
			}
			if(!empty($options["layout"]) && $options["layout"]=='metro'){
				$this->transient_widgets[] = 'plus-listing-metro';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}
			if(!empty($options["filter_category"]) && $options["filter_category"]=='yes'){
				$this->transient_widgets[] = 'plus-post-filter';
			}
			if(!empty($options["post_extra_option"]) && $options["post_extra_option"]=='pagination'){
				$this->transient_widgets[] = 'plus-pagination';
			}
			
			if($widget_name=='tp-dynamic-listing' && !empty($options["display_theplus_quickview"]) && $options["display_theplus_quickview"]=='yes' && !empty($options["tpqc"]) ){
				$this->transient_widgets[] = 'tp-dynamic-listout-qview';
			}
		}
		
		if((!empty($widget_name) && $widget_name=='tp-syntax-highlighter')){			
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-default'){
				$this->transient_widgets[] = 'prism_default';
			}
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-coy'){
				$this->transient_widgets[] = 'prism_coy';
			}
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-dark'){
				$this->transient_widgets[] = 'prism_dark';
			}
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-funky'){
				$this->transient_widgets[] = 'prism_funky';
			}
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-okaidia'){
				$this->transient_widgets[] = 'prism_okaidia';
			}
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-solarizedlight'){
				$this->transient_widgets[] = 'prism_solarizedlight';
			}
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-tomorrownight'){
				$this->transient_widgets[] = 'prism_tomorrownight';
			}
			if(!empty($options["themeType"]) && $options["themeType"]=='prism-twilight'){
				$this->transient_widgets[] = 'prism_twilight';
			}
			if(!empty($options["themeType"]) && ($options["cpybtnicon"]["value"] || $options["copiedbtnicon"]["value"] || $options["dwnldBtnIcon"]["value"])){				
				$this->transient_widgets[] = 'tp-syntax-highlighter-icons';
			}
		}
		
		if(!empty($widget_name)){
			if($widget_name=='tp-info-box' && ((!empty($options["loop_select_icon"]) && $options["loop_select_icon"]=='lottie') || (!empty($options["image_icon"]) && $options["image_icon"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		if(!empty($widget_name)){
            if($widget_name=='tp-animated-service-boxes' && ((!empty($options["loop_content"][0]["loop_image_icon"]) && $options["loop_content"][0]["loop_image_icon"]=='lottie'))){
                $this->transient_widgets[] = 'plus-lottie-player';
            }
        }
		if(!empty($widget_name)){
			if($widget_name=='tp-cascading-image' && ((!empty($options["image_cascading"][0]["select_option"]) && $options["image_cascading"][0]["select_option"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		if(!empty($widget_name)){
			if($widget_name=='tp-hotspot' && ((!empty($options["pin_hotspot"][0]["select_option"]) && $options["pin_hotspot"][0]["select_option"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
				
			}
		}
		if(!empty($widget_name)){
			if($widget_name=='tp-number-counter' && ((!empty($options["icon_type"]) && $options["icon_type"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		if(!empty($widget_name)){
			if($widget_name=='tp-off-canvas' && ((!empty($options["select_toggle_canvas"]) && $options["select_toggle_canvas"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		if(!empty($widget_name)){
			if($widget_name=='tp-pricing-list' && ((!empty($options["icon_type"]) && $options["icon_type"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		if(!empty($widget_name)){
			if($widget_name=='tp-pricing-table' && ((!empty($options["button_icon_type"]) && $options["button_icon_type"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		if(!empty($widget_name)){
            if($widget_name=='tp-process-steps' && ((!empty($options["loop_content"][0]["loop_image_icon"]) && $options["loop_content"][0]["loop_image_icon"]=='lottie'))){
                $this->transient_widgets[] = 'plus-lottie-player';
            }
        }
		if(!empty($widget_name)){
			if($widget_name=='tp-progress-bar' && ((!empty($options["image_icon"]) && $options["image_icon"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		if(!empty($widget_name)){
			if($widget_name=='tp-unfold' && ((!empty($options["icon_type"]) && $options["icon_type"]=='lottie'))){
				$this->transient_widgets[] = 'plus-lottie-player';
			}
		}
		
		if((!empty($widget_name) && $widget_name=='tp-social-feed') || (!empty($widget_name) && $widget_name=='tp-social-reviews')){			
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}
			
			if(!empty($options["AllReapeter"])){
				$instafeedBus = false;
				foreach ($options["AllReapeter"] as $value) {
					if((!empty($value['selectFeed']) && $value['selectFeed']=='Instagram') && (!empty($value['InstagramType']) && $value['InstagramType']=='Instagram_Graph')){
						$instafeedBus = true;
						break;
					}
				}
				if($instafeedBus){					
					$this->transient_widgets[] = 'plus-carousel';
				}
			}
			
			if(!empty($options["filter_category"]) && $options["filter_category"]=='yes'){
				$this->transient_widgets[] = 'plus-post-filter';
			}
		}
		
		if(!empty($widget_name) && $widget_name=='tp-dynamic-listing'){
			if(!empty($options["blogs_post_listing"]) && $options["blogs_post_listing"]=='custom_query'){
				if(!empty($options["cqid_pagination"]) && $options["cqid_pagination"]=='yes'){
					$this->transient_widgets[] = 'plus-pagination';
				}
			}			
		}
		
		if(!empty($widget_name) && $widget_name=='tp-woo-single-pricing' && !empty($options["swatchesloop"]) && $options["swatchesloop"]=='yes'){			
			$this->transient_widgets[] = 'tp-product-listout-swatches';
		}
		
		if(!empty($widget_name) && $widget_name=='tp-product-listout'){
			if(!empty($options["display_yith_list"]) && $options["display_yith_list"]=='yes'){					
					$this->transient_widgets[] = 'plus-product-listout-yithcss';					
				if(!empty($options["display_yith_quickview"]) && $options["display_yith_quickview"]=='yes'){
					$this->transient_widgets[] = 'plus-product-listout-quickview';
				}
			}
			
			if(!empty($options["display_theplus_quickview"]) && $options["display_theplus_quickview"]=='yes' && !empty($options["tpqc"]) ){
				$this->transient_widgets[] = 'tp-product-listout-qcw';
				$this->transient_widgets[] = 'tp-product-listout-swatches';
			}
		}		
		
		if (version_compare( ELEMENTOR_VERSION, '3.1.0', '>=' ) ) {
			if(!empty($widget_name) && ($widget_name=='tp-tabs-tours' || $widget_name=='tp-navigation-menu' || $widget_name=='tp-mobile-menu')){	
				if((!empty($options["tabs_swiper"]) && $options["tabs_swiper"]=='yes') || (!empty($options["show_mobile_menu"]) && $options["show_mobile_menu"]=='yes' && $options["mobile_menu_type"]=='swiper') || (!empty($options["mm_extra_display_mode"]) && $options["mm_extra_display_mode"]=='swiper')){
					$this->transient_widgets[] = 'plus-swiper';
				}
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-wp-login-register'){
			if(((!empty($options['tp_dis_pass_pattern']) && (!empty($options['tp_dis_pass_hint']) && $options['tp_dis_pass_hint']=="yes") && !empty($options['dis_pass_hint_on'])) || (!empty($options['tp_dis_show_pass_icon']) && $options['tp_dis_show_pass_icon']=="yes"))){
				$this->transient_widgets[] = 'tp-wp-login-register-ex';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-dynamic-smart-showcase'){			
			if(!empty($options["style"]) && ($options["style"] =='magazine' || $options["style"] =='none')){
				$this->transient_widgets[] = 'plus-carousel';
			}
			if(!empty($options["style"]) && $options["style"] =='news' && !empty($options["filter_category"]) && $options["filter_category"]=='yes'){
				$this->transient_widgets[] = 'plus-post-filter';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-clients-listout'){			
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}
			if(!empty($options["filter_category"]) && $options["filter_category"]=='yes'){
				$this->transient_widgets[] = 'plus-post-filter';
			}
			if(!empty($options["post_extra_option"]) && $options["post_extra_option"]=='pagination'){
				$this->transient_widgets[] = 'plus-pagination';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-dynamic-device'){
			if(!empty($options["device_mode"]) && $options["device_mode"]=='carousal'){
				$this->transient_widgets[] = 'plus-carousel';
			}
		}
		
		if(!empty($widget_name) && $widget_name=='tp-woo-single-image' && $options["select"]=='product_gallery' && $options["select_pg_style"]=='style_3'){			
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
			}
			if(!empty($options["layout"]) && $options["layout"]=='metro'){
				$this->transient_widgets[] = 'plus-listing-metro';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}
		}
			
		if(!empty($widget_name) && $widget_name=='tp-gallery-listout'){
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
				}
			if(!empty($options["layout"]) && $options["layout"]=='metro'){
				$this->transient_widgets[] = 'plus-listing-metro';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}
			if(!empty($options["filter_category"]) && $options["filter_category"]=='yes'){
				$this->transient_widgets[] = 'plus-post-filter';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-team-member-listout'){			
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}
			if(!empty($options["filter_category"]) && $options["filter_category"]=='yes'){
				$this->transient_widgets[] = 'plus-post-filter';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-testimonial-listout'){			
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}
		}
		
		if((!empty($widget_name) && $widget_name=='tp-info-box')){
			if(!empty($options["info_box_layout"]) && $options["info_box_layout"]=='carousel_layout' && !empty($options["connection_switch"]) && $options["connection_switch"]=='yes' && !empty($options["connection_unique_id"])){
				$this->transient_widgets[] = 'tp-info-box-js';
			}
		}
	
		if((!empty($widget_name) && $widget_name=='tp-advanced-buttons')){
			if((!empty($options["ab_button_type"]) && (($options["ab_button_type"]=='cta')) || (($options["ab_button_type"]=='download')))){
				if(!empty($options["download_button_style"]) || (!empty($options["cta_button_style"]) && $options["cta_button_style"] =='tp_cta_st_14')){
					$this->transient_widgets[] = 'tp-advanced-buttons-js';
				}
			}			
		}
		
		if(!empty($widget_name) && $widget_name=='tp-messagebox' && !empty($options['dismiss']) && $options['dismiss']=='yes'){
			$this->transient_widgets[] = 'tp-messagebox-js';
		}
		
		if(!empty($widget_name) && $widget_name=='tp-post-featured-image' && !empty($options['pfi_type']) && $options['pfi_type']=='pfi-background'){
			$this->transient_widgets[] = 'tp-post-featured-image-js';
		}

		if(!empty($widget_name) && $widget_name=='tp-process-steps' && ( (!empty($options['ps_style']) && $options['ps_style']=='style_2') || (!empty($options['connection_switch']) && $options['connection_switch']=='yes' && !empty($options['connection_unique_id'])) )){
			$this->transient_widgets[] = 'tp-process-steps-js';
        }

		if((!empty($widget_name) && $widget_name=='tp-flip-box') || (!empty($widget_name) && $widget_name=='tp-info-box')){
			if(!empty($options["display_button"]) && $options["display_button"]=='yes'){
				$this->transient_widgets[] = 'plus-button-extra';
			}
			if(!empty($options["info_box_layout"]) && $options["info_box_layout"]=='carousel_layout'){
				$this->transient_widgets[] = 'plus-carousel';
			}
			if(!empty($options["box_hover_effects"])){
				$this->transient_widgets[] = 'plus-content-hover-effect';
			}
			if(!empty($options["tilt_parallax"]) && $options["tilt_parallax"]=='yes'){
				$this->transient_widgets[] = 'plus-tilt-parallax';
			}
			if((!empty($options["image_icon"]) && $options["image_icon"]=='svg') || (!empty($options["loop_select_icon"]) && $options["loop_select_icon"]=='svg')){
				$this->transient_widgets[] = 'tp-draw-svg';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-image-factory'){
			if(!empty($options["bg_image_parallax"]) && $options["bg_image_parallax"]=='yes'){
				$this->transient_widgets[] = 'plus-magic-scroll';
			}
			if(!empty($options["animated_style"]) && $options["animated_style"]=='animate-image'){
				$this->transient_widgets[] = 'plus-velocity';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-instagram'){
			if(!empty($options["theplus_instafeed_masonry"]) && $options["theplus_instafeed_masonry"]=='yes'){
				$this->transient_widgets[] = 'plus-imagesloaded';
				$this->transient_widgets[] = 'plus-isotope';
			}
			if(!empty($options["theplus_instafeed_carousels"]) && $options["theplus_instafeed_carousels"]=='yes'){
				$this->transient_widgets[] = 'plus-imagesloaded';
				$this->transient_widgets[] = 'plus-carousel';
			}
		}
		if(!empty($options["box_hover_effects"]) && !empty($widget_name) && $widget_name=='tp-number-counter'){
			$this->transient_widgets[] = 'plus-content-hover-effect';
		}
		if(!empty($options["icon_type"]) && $options["icon_type"]=='svg' && !empty($widget_name) && $widget_name=='tp-number-counter'){
			$this->transient_widgets[] = 'tp-draw-svg';
		}
		if(!empty($widget_name) && $widget_name=='tp-social-icon'){
			if(!empty($options["pt_plus_social_networks"])){
				$magic_scroll= array_search('yes', array_column($options["pt_plus_social_networks"], 'loop_magic_scroll'));
				if(!empty($magic_scroll) || $magic_scroll===0){							
					$this->transient_widgets[] = 'plus-magic-scroll';
				}
				$plus_tooltip= array_search('yes', array_column($options["pt_plus_social_networks"], 'plus_tooltip'));
				if(!empty($plus_tooltip) || $plus_tooltip===0){						
					$this->transient_widgets[] = 'plus-tooltip';
				}
				$move_parallax= array_search('yes', array_column($options["pt_plus_social_networks"], 'plus_mouse_move_parallax'));
				if(!empty($move_parallax) || $move_parallax===0){						
					$this->transient_widgets[] = 'plus-mousemove-parallax';
				}
			}
		}

		if(!empty($widget_name) && $widget_name == 'tp-table'){
			$table_headings = array_search('yes', array_column($options["table_headings"], 'heading_show_tooltips'));
			$table_content = array_search('yes', array_column($options["table_content"], 'body_show_tooltips'));			
			
			if(!empty($table_headings) || !empty($table_content)){
				$this->transient_widgets[] = 'plus-tooltip';
			}
		}

		if(!empty($widget_name) && $widget_name=='tp-cascading-image'){
			if(!empty($options["image_cascading"])){
				$magic_scroll= array_search('yes', array_column($options["image_cascading"], 'loop_magic_scroll'));
				if(!empty($magic_scroll) || $magic_scroll===0){							
					$this->transient_widgets[] = 'plus-magic-scroll';
				}
				$plus_tooltip= array_search('yes', array_column($options["image_cascading"], 'plus_tooltip'));
				if(!empty($plus_tooltip) || $plus_tooltip===0){						
					$this->transient_widgets[] = 'plus-tooltip';
				}
				$special_effect= array_search('yes', array_column($options["image_cascading"], 'special_effect'));
				if(!empty($special_effect) || $special_effect===0){						
					$this->transient_widgets[] = 'plus-reveal-animation';
				}
				$move_parallax= array_search('yes', array_column($options["image_cascading"], 'cascading_move_parallax'));
				if(!empty($move_parallax) || $move_parallax===0){						
					$this->transient_widgets[] = 'plus-mousemove-parallax';
				}
				$hover_parallax= array_search('yes', array_column($options["image_cascading"], 'hover_parallax'));
				if(!empty($hover_parallax) || $hover_parallax===0){						
					$this->transient_widgets[] = 'plus-hover3d';
				}
				$link_option= array_search('popup_link', array_column($options["image_cascading"], 'link_option'));
				if(!empty($link_option) || $link_option===0){						
					$this->transient_widgets[] = 'plus-lity-popup';
				}
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-style-list'){
			if(!empty($options["icon_list"])){
				$show_tooltips= array_search('yes', array_column($options["icon_list"], 'show_tooltips'));
				if(!empty($show_tooltips) || $show_tooltips===0){						
					$this->transient_widgets[] = 'plus-tooltip';
				}
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-shape-divider'){
			if(!empty($options["shape_divider"]) && $options["shape_divider"]=='wave'){
				$this->transient_widgets[] = 'plus-wavify';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp_advertisement_banner'){
			if(!empty($options["hov_styles"]) && $options["hov_styles"]=='hover-tilt'){
				$this->transient_widgets[] = 'plus-hover3d';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-pricing-table'){
			if(!empty($options["image_icon"]) && $options["image_icon"]=='svg'){
				$this->transient_widgets[] = 'tp-draw-svg';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-row-background'){
			if(!empty($options["select_anim"]) && $options["select_anim"]=='bg_gallery'){
				$this->transient_widgets[] = 'plus-vegas-gallery';
			}
			if(!empty($options["select_anim"]) && $options["select_anim"]=='bg_color'){
				$this->transient_widgets[] = 'plus-row-animated-color';
			}
			if(!empty($options["select_anim"]) && $options["select_anim"]=='bg_Image_pieces'){
				$this->transient_widgets[] = 'plus-row-segmentation';
			}
			if(!empty($options["bg_img_parallax"]) && $options["bg_img_parallax"]=='yes'){
				$this->transient_widgets[] = 'plus-magic-scroll';
			}
			if(!empty($options["select_anim"]) && $options["select_anim"]=='scroll_animate_color'){
				$this->transient_widgets[] = 'plus-row-scroll-color';
			}
			if(!empty($options["middle_style"]) && $options["middle_style"]=='canvas'){
				if(!empty($options["canvas_style"]) && $options["canvas_style"]=='style_8'){
					$this->transient_widgets[] = 'plus-row-canvas-8';
				}
				if(!empty($options["canvas_style"]) && ($options["canvas_style"]=='style_2' || $options["canvas_style"]=='style_3' || $options["canvas_style"]=='style_4' || $options["canvas_style"]=='style_5' || $options["canvas_style"]=='style_7' || $options["canvas_style"]=='custom')){
					$this->transient_widgets[] = 'plus-row-canvas-particle';
				}
				if(!empty($options["canvas_style"]) && $options["canvas_style"]=='style_6'){
					$this->transient_widgets[] = 'plus-row-canvas-particleground';
				}
			}
			if(!empty($options["middle_style"]) && ($options["middle_style"]=='mordern_parallax' || $options["middle_style"]=='mordern_image_effect' || $options["middle_style"]=='multi_layered_parallax')){
				$this->transient_widgets[] = 'plus-magic-scroll';
			}
		}
		if(!empty($widget_name) && $widget_name=='tp-page-scroll'){
			if(!empty($options["page_scroll_opt"]) && $options["page_scroll_opt"]=='tp_full_page'){
				$this->transient_widgets[] = 'tp-fullpage';
			}
			if(!empty($options["page_scroll_opt"]) && $options["page_scroll_opt"]=='tp_page_pilling'){
				$this->transient_widgets[] = 'tp-pagepiling';
			}
			if(!empty($options["page_scroll_opt"]) && $options["page_scroll_opt"]=='tp_multi_scroll'){
				$this->transient_widgets[] = 'tp-multiscroll';
			}
			if(!empty($options["page_scroll_opt"]) && $options["page_scroll_opt"]=='tp_horizontal_scroll'){
				$this->transient_widgets[] = 'tp-horizontal-scroll';
			}
		}

		if(!empty($widget_name) && $widget_name=='tp-heading-title'){
			if(!empty($options["heading_style"]) && $options["heading_style"]=='style_10'){
				$this->transient_widgets[] = 'tp-heading-title-splite-animation';
			}
		}

		if(!empty($widget_name) && $widget_name=='tp-dynamic-categories'){
			if(!empty($options["layout"]) && $options["layout"]=='grid' || $options["layout"]=='masonry' ){
				$this->transient_widgets[] = 'plus-listing-masonry';
			}
			if(!empty($options["layout"]) && $options["layout"]=='metro'){
				$this->transient_widgets[] = 'plus-listing-metro';
			}
			if(!empty($options["layout"]) && $options["layout"]=='carousel'){
				$this->transient_widgets[] = 'plus-carousel';
			}

			if(!empty($options["style"]) && $options["style"]=='style_3'){
				$this->transient_widgets[] = 'tp-dynamic-categories-st3';
			}
		}		

		if(!empty($widget_name) && $widget_name=='tp-advanced-typography'){
			if(!empty($options["on_hover_img_reveal_switch"]) && $options["on_hover_img_reveal_switch"] =='yes'){
				$this->transient_widgets[] = 'plus-adv-typo-extra-js-css';
			}
			if(!empty($options["typography_listing"]) && $options["typography_listing"]=='listing'){
				if(!empty($options["listing_content"])){
					$hover_image = false;
					foreach ($options["listing_content"] as $value) {
						if(!empty($value['on_hover_img_reveal_switch']) && $value['on_hover_img_reveal_switch']=='yes'){
							$hover_image = true;
							break;
						}
					}
					if($hover_image){
						$this->transient_widgets[] = 'plus-adv-typo-extra-js-css';
					}
				}
			}
			
		}

		if( !empty($widget_name) && $widget_name == "tp-scroll-sequence" ){
			if( !empty($options['stickySec']) && $options['stickySec'] == 'yes' ){
				$this->transient_widgets[] = 'plus-key-animations';
			}
		}

		if( !empty($widget_name) && ( 'tp-product-listout' === $widget_name || 'tp-dynamic-listing' === $widget_name ) ){
			$paginationtype = !empty($options['paginationType']) ? $options['paginationType'] : '';

			if( 'ajaxbased' === $paginationtype ){
				$this->transient_widgets[] = 'tp-ajax-based-pagination';
			}
		}
	} 
   
	/**
	 * Enqueue editor scripts
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 */
	public function enqueue_editor_scripts() {
		// Register scripts
		wp_enqueue_script( 'plus-editor-js', $this->pathurl_security(THEPLUS_URL . DIRECTORY_SEPARATOR .  'assets/js/admin/plus-editor.min.js'), [], THEPLUS_VERSION, true );
		
		wp_localize_script( 'plus-editor-js', 'PlusEditor_localize', array(
			'plugin' => THEPLUS_URL,
			'ajax' => admin_url( 'admin-ajax.php' ),
			'delete_transient_nonce' => wp_create_nonce( 'delete_transient_nonce' ),
			'SocialReview_nonce' => wp_create_nonce('SocialReview_nonce'),
			'live_editor' => wp_create_nonce('live_editor'),
			'THEPLUS_ASSETS_URL' => THEPLUS_ASSETS_URL,
		));
		
	}
	
	//Plus Addons Scripts
	public function plus_enqueue_scripts()
	{
	
		if (theplus_library()->is_preview_mode()) {
			
			//Load Icons Mind
			$options = get_option( 'theplus_api_connection_data' );
			$load_font_id=array();
			if(isset($options["load_icons_mind_ids"]) && !empty($options["load_icons_mind_ids"])){
				$load_font_id = explode(",", $options["load_icons_mind_ids"]);
			}
			$paged_id = get_queried_object_id();
			if(!isset($options["load_icons_mind"]) || (isset($options["load_icons_mind"]) && !empty($options["load_icons_mind"]) && $options["load_icons_mind"]=='enable') || ( isset($options["load_icons_mind"]) && $options["load_icons_mind"]=='disable' && in_array($paged_id,$load_font_id) )){
				wp_enqueue_style(
					'plus-icons-mind-css',
					$this->pathurl_security(THEPLUS_URL . '/assets/css/extra/iconsmind.min.css'),
					false,
					THEPLUS_VERSION
				);
			}
			
			//Load pre loader
			$load_pre_loader_id=array();
			if(isset($options["load_pre_loader_func_ids"]) && !empty($options["load_pre_loader_func_ids"])){
				$load_pre_loader_id = explode(",", $options["load_pre_loader_func_ids"]);
			}
			$pre_load_paged_id = get_queried_object_id();
			if(!isset($options["load_pre_loader_func"]) || (isset($options["load_pre_loader_func"]) && !empty($options["load_pre_loader_func"]) && $options["load_pre_loader_func"]=='enable') || ( isset($options["load_pre_loader_func"]) && $options["load_pre_loader_func"]=='disable' && in_array($pre_load_paged_id,$load_pre_loader_id) )){
				
				wp_enqueue_style(
					'plus-pre-loader-css',
					$this->pathurl_security(THEPLUS_URL . '/assets/css/main/pre-loader/plus-pre-loader.min.css'),
					false,
					THEPLUS_VERSION
				);
				wp_enqueue_script(
					'plus-pre-loader-js2',
					$this->pathurl_security(THEPLUS_URL . '/assets/js/main/pre-loader/plus-pre-loader-extra-transition.min.js'),
					false,
					THEPLUS_VERSION
				);
				wp_enqueue_script(
					'plus-pre-loader-js',
					$this->pathurl_security(THEPLUS_URL . '/assets/js/main/pre-loader/plus-pre-loader.min.js'),
					false,
					THEPLUS_VERSION
				);
				
				if(!empty($options["load_pre_loader_lottie_js"]) && $options["load_pre_loader_lottie_js"]=='on'){				
					wp_enqueue_script('plus-pre-loader-lotties',$this->pathurl_security(THEPLUS_URL . '/assets/js/extra/lottie-player.js'),
						false,THEPLUS_VERSION);
				}
			}
			
			//Google Map Api			
			$check_elements=theplus_get_option('general','check_elements');
			$switch_api = (!empty($options['gmap_api_switch'])) ? $options['gmap_api_switch'] : '';
			if((empty($theplus_options) || (isset($check_elements) && !empty($check_elements) && in_array('tp_google_map',$check_elements))) && (empty($switch_api) || $switch_api=='enable' || $switch_api!='none') ){
				if(!empty($options['theplus_google_map_api'])){
					$theplus_google_map_api=$options['theplus_google_map_api'];
				}else{
					$theplus_google_map_api='';
				}
				wp_enqueue_script( 'gmaps-js','https://maps.googleapis.com/maps/api/js?key='.$theplus_google_map_api.'&libraries=places&sensor=false', array('jquery'), null, false, true);
			}
			
			if((isset($check_elements) && !empty($check_elements) && in_array('tp_wp_bodymovin',$check_elements)) && !empty($options['bodymovin_load_js_check'])){
				wp_enqueue_script( 'lottieplayer' , $this->pathurl_security(THEPLUS_URL . DIRECTORY_SEPARATOR .  'assets/js/extra/lottie-player.js'), array()); //Lottie Player
				wp_enqueue_script( 'lottie' , $this->pathurl_security(THEPLUS_URL . DIRECTORY_SEPARATOR .  'assets/js/extra/lottie.min.js'), array(), '5.5.2' ); //Bodymovin Animation
				wp_enqueue_script( 'theplus-bodymovin' , $this->pathurl_security(THEPLUS_URL . DIRECTORY_SEPARATOR .  'assets/js/main/bodymovin/plus-bodymovin.js'), array( 'jquery', 'lottie' ), THEPLUS_VERSION, true );
			}
			
			wp_enqueue_script( 'jquery-ui-slider' );//Audio Player	
			
			wp_enqueue_script( 'jquery-ui-draggable' );//dragable
			wp_enqueue_script( 'jquery-touch-punch' );//touch
			

		} else {
			global $wp_query;
			if (is_home() || is_singular() || is_archive() || is_search() || (isset( $wp_query ) && (bool) $wp_query->is_posts_page) || is_404()) {
				
				$queried_obj = get_queried_object_id();
				if(is_search()){
					$queried_obj = 'search';
				}
				if(is_404()){
					$queried_obj = '404';
				}
				$post_type = (is_singular() ? 'post' : 'term');
				$elements = (array) get_metadata($post_type, $queried_obj, 'theplus_transient_widgets', true);
				
				$this->enqueue_frontend_pre_loader_load();

				if (empty($elements)) {
					return;
				}

				if(in_array('tp-google-map',$elements)){					
					$this->enqueue_frontend_google_map_load();
				}				

				$this->enqueue_frontend_load();
			}
		}
	}

	protected function enqueue_frontend_google_map_load(){
		//Google Map Api		
		$check_elements=theplus_get_option('general','check_elements');
		$options = get_option( 'theplus_api_connection_data' );
		$switch_api = (!empty($options['gmap_api_switch'])) ? $options['gmap_api_switch'] : '';	
		if((empty($theplus_options) || (isset($check_elements) && !empty($check_elements) && in_array('tp_google_map',$check_elements))) && (empty($switch_api) || $switch_api=='enable')){
			if(!empty($options['theplus_google_map_api'])){
				$theplus_google_map_api=$options['theplus_google_map_api'];
			}else{
				$theplus_google_map_api='';
			}
			wp_enqueue_script( 'gmaps-js','https://maps.googleapis.com/maps/api/js?key='.$theplus_google_map_api.'&libraries=places&sensor=false', array('jquery'), null, false, true);
		}
	}

	/**
	 * Extra Option pre-loader js load
	 * 
	 * @since 5.2.2
	 */
	protected function enqueue_frontend_pre_loader_load() {
		$options = get_option( 'theplus_api_connection_data' );
		$pre_load_paged_id = get_queried_object_id();
		$load_pre_loader_id = array();

		$PreLoader_Pageids = !empty($options["load_pre_loader_func_ids"]) ? $options["load_pre_loader_func_ids"] : '';

		if( isset($PreLoader_Pageids) ){
			$load_pre_loader_id = explode(",", $PreLoader_Pageids);
		}

		$Ex_PreLoader = !empty($options["load_pre_loader_func"]) ? $options["load_pre_loader_func"] : '';
		if( (!empty($Ex_PreLoader) && $Ex_PreLoader == "enable") || ($Ex_PreLoader == "disable" && in_array($pre_load_paged_id, $load_pre_loader_id) ) ){
			wp_enqueue_style('plus-pre-loader-css',
				$this->pathurl_security( THEPLUS_URL .'/assets/css/main/pre-loader/plus-pre-loader.min.css' ),
				false, THEPLUS_VERSION
			);

			wp_enqueue_script('plus-pre-loader-js2',
				$this->pathurl_security( THEPLUS_URL . '/assets/js/main/pre-loader/plus-pre-loader-extra-transition.min.js' ), 
				array('jquery'), THEPLUS_VERSION
			);

			wp_enqueue_script('plus-pre-loader-js',
				$this->pathurl_security( THEPLUS_URL . '/assets/js/main/pre-loader/plus-pre-loader.min.js' ),
				array('jquery'), THEPLUS_VERSION
			);

			$Ex_PreLoader_lottieJS = !empty($options["load_pre_loader_lottie_js"]) ? $options["load_pre_loader_lottie_js"] : '';
			if( !empty($Ex_PreLoader_lottieJS) && $Ex_PreLoader_lottieJS == 'on' ){
				wp_enqueue_script('plus-pre-loader-lotties',
					$this->pathurl_security( THEPLUS_URL . '/assets/js/extra/lottie-player.js' ),
					false, THEPLUS_VERSION
				);
			}
		}
	}
	
	// rules how css will be enqueued on front-end
	protected function enqueue_frontend_load(){
		
		wp_register_script( 'lottie' , $this->pathurl_security(THEPLUS_URL . DIRECTORY_SEPARATOR .  'assets/js/extra/lottie.min.js'), array(), '5.5.2' ); //Bodymovin Animation
		wp_register_script( 'theplus-bodymovin' , $this->pathurl_security(THEPLUS_URL . DIRECTORY_SEPARATOR .  'assets/js/main/bodymovin/plus-bodymovin.js'), array( 'jquery', 'lottie' ), THEPLUS_VERSION, true );
		
		//Load Icons Mind
		$options = get_option( 'theplus_api_connection_data' );
		$load_font_id=array();
		if(isset($options["load_icons_mind_ids"]) && !empty($options["load_icons_mind_ids"])){
			$load_font_id = explode(",", $options["load_icons_mind_ids"]);
		}
		
		$paged_id = get_queried_object_id();
		if(!isset($options["load_icons_mind"]) || (isset($options["load_icons_mind"]) && !empty($options["load_icons_mind"]) && $options["load_icons_mind"]=='enable') || ( isset($options["load_icons_mind"]) && $options["load_icons_mind"]=='disable' && in_array($paged_id,$load_font_id) )){
			wp_enqueue_style('plus-icons-mind-css',$this->pathurl_security(THEPLUS_URL . '/assets/css/extra/iconsmind.min.css'),false,THEPLUS_VERSION);
		}

		/*sociel login google*/
		$options = get_option( 'theplus_api_connection_data' );		
		if((empty($theplus_options) || (isset($check_elements) && !empty($check_elements) && in_array('tp_wp_login_register',$check_elements))) && !empty($options['theplus_google_client_id'])){
			wp_enqueue_script( 'google_clientid_js', 'https://apis.google.com/js/api:client.js', array('jquery'), null, false, true);
			wp_enqueue_script( 'google_platform_js', 'https://apis.google.com/js/platform.js', array('jquery'), null, false, true);
		}
		/*sociel login google*/
		
		wp_enqueue_script( 'jquery-ui-slider' );//Audio Player
		
		wp_enqueue_script( 'jquery-ui-draggable' );//dragable
		wp_enqueue_script( 'jquery-touch-punch' );//touch
		
	}
	
	/**
	 * Generate secure path url
	 *
	 * @since v2.0
	 */
	public function pathurl_security($url) {
        return preg_replace(['/^http:/', '/^https:/', '/(?!^)\/\//'], ['', '', '/'], $url);
    }
	public function tp_pro_registered_widgets(){
		return $this->registered_widgets;
	}
	
	public function init(){
		$this->registered_widgets = registered_widgets();
		
		add_filter('theplus_pro_registered_widgets',array($this,'tp_pro_registered_widgets'));

		$this->transient_widgets = [];
		$this->transient_extensions = [];
		add_action('elementor/frontend/before_render', array($this, 'collect_transient_widgets'));
		
		add_filter('tp_pro_transient_widgets', array($this,'tp_pro_transient_widget'));
		
		add_action( 'elementor/editor/before_enqueue_scripts', array($this, 'enqueue_editor_scripts') );
		
		
		add_action('wp_enqueue_scripts', array($this, 'plus_enqueue_scripts'));
				
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
 * Returns instance of Plus_Generator
 */
function theplus_generator() {
	return Plus_Generator::get_instance();
}
