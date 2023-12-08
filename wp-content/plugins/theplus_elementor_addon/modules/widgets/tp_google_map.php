<?php 
/*
Widget Name: Advanced Google Map
Description: Style Of Google Map Location
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;
 
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
 
class ThePlus_Google_Map extends Widget_Base {
	
	public $TpDoc = THEPLUS_TPDOC;

	public function get_name() {
		return 'tp-google-map';
	}
 
    public function get_title() {
        return esc_html__('Google Map', 'theplus');
    }
 
    public function get_icon() {
        return 'fa fa-map-o theplus_backend_icon';
    }
 
    public function get_categories() {
        return array('plus-adapted');
    }
	
	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "google-maps";

		return esc_url($DocUrl);
	}

    protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'map_content_heading',
			[
				'label' => esc_html__( 'Map Locations', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$repeater->add_control(
			'latitude',
			[
				'label' => esc_html__( 'Latitude Value', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '40.730271',				
				'placeholder' => esc_html__( 'Enter Latitude Location', 'theplus' ),
				'description' => sprintf( __( 'Enter Latitude value of your location of Google map. You can find that using. <a target="_blank" class="tootip-link" href="https://www.latlong.net/">Check link</a>', 'theplus' )),
			]
		);
		$repeater->add_control(
			'longitude',
			[
				'label' => esc_html__( 'Longitude Value', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '-73.989089',				
				'placeholder' => esc_html__( 'Enter Latitude Location', 'theplus' ),
				'description' => sprintf( __( 'Enter Longitude value of your location of Google map. You can find that using. <a target="_blank" class="tootip-link" href="https://www.latlong.net/">Check link</a>', 'theplus' )),
			]
		);
		$repeater->add_control(
			'address',
			[
				'label' => esc_html__( 'Address text for Tooltip', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'New York City', 'theplus' ),
				'description' => esc_html__( 'Add text you want to show on Pin Icon as a Tooltip for this Location using this option.', 'theplus' ),
			]
		);
		$repeater->add_control(
			'pin_icon',
			[
				'label' => wp_kses_post( "Pin Icon <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-google-maps-custom-marker/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'pin_icon_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
			]
		);
		$this->add_control(
            'map_locations',
            [
				'label' => wp_kses_post( "Add Multiple Location Point <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-google-maps-multiple-locations-pin/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'address' => '',                       
                    ],
                ],                
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ address}}}',
            ]
        );
		$this->add_responsive_control(
			'min_height',
			[
				'label' => esc_html__( 'Minimum Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 400,
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-map' => 'min-height:{{SIZE}}{{UNIT}};',
				],
			]
		);
 
		$this->end_controls_section();
		/*map style creative*/
		$this->start_controls_section(
            'section_map_style_content',
            [
                'label' => esc_html__('Map Style', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'zoom',
			[
				'label' => wp_kses_post( "Map Zoom <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-google-maps-multiple-locations-pin/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 25,
					],
				],
				'description' => esc_html__('Enter values from 1 to 25 to zoom google map as per requirement..','theplus'),
			]
		);
		$this->add_control(
			'gmap_option',
			[
				'label' => wp_kses_post( "Map Options <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "google-maps-elementor-widget-settings-overview/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'options' => [
					'scroll_wheel'  => esc_html__( 'Scroll Wheel', 'theplus' ),
					'pan_control' => esc_html__( 'Pan Control', 'theplus' ),
					'draggable' => esc_html__( 'Draggable', 'theplus' ),
					'zoom_control' => esc_html__( 'Zoom Control', 'theplus' ),
					'map_type_control' => esc_html__( 'Map Type Control', 'theplus' ),
					'scale_control' => esc_html__( 'Scale Control', 'theplus' ),
					'fullscreen_control' => esc_html__( 'Full-screen Control', 'theplus' ),
					'streetview_control' => esc_html__( 'Street View Control', 'theplus' ),
					'marker_clustering' => esc_html__( 'Marker Clustering', 'theplus' ),
				],
				'default' => [ 'pan_control','draggable','zoom_control','map_type_control','scale_control','scroll_wheel','fullscreen_control','streetview_control'],
			]
		);
		$this->add_control(
			'map_type',
			[
				'label' => esc_html__( 'Google Map Variations', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ROADMAP',
				'options' => [
					'ROADMAP'  => esc_html__( 'ROADMAP (Displays a normal, default 2D map)', 'theplus' ),
					'HYBRID' => esc_html__( 'HYBRID (Displays a photographic map + roads and city names)', 'theplus' ),
					'SATELLITE' => esc_html__( 'SATELLITE (Displays a photographic map)', 'theplus' ),
					'TERRAIN' => esc_html__( 'TERRAIN (Displays a map with mountains, rivers, etc.)', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'adv_modify_json',[
				'label' => esc_html__( 'Custom Style Maps', 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'description' => esc_html__( 'You can choose our creative google map styles using this option.', 'theplus' ),
			]
		);
		$this->add_control(
			'map_style',
			[
				'label' => esc_html__( 'Creative Map Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(7),
				'condition' => [
					'adv_modify_json' => 'yes',
				],
			]
		);
		$this->add_control(
			'modify_coloring',[
				'label'   => esc_html__( 'Modify Google Maps Hue, Saturation, Lightness', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'description' => esc_html__( 'Choose one from these Modify Google Maps Hue, Saturation styles.', 'theplus' ),
				'condition' => [
					'adv_modify_json' => 'yes',
				],
			]
		);
		$this->add_control(
			'hue',
			[
				'label' => esc_html__( 'Hue', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ccc',
				'condition' => [
					'adv_modify_json' => 'yes',
					'modify_coloring' => 'yes',
				],
			]
		);
		$this->add_control(
			'saturation',
			[
				'label' => esc_html__( 'Saturation', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'description' => esc_html__('Shifts the saturation of colors by a percentage of the original value if decreasing and a percentage of the remaining value if increasing. Valid values: [-100, 100].','theplus'),
				'condition' => [
					'adv_modify_json' => 'yes',
					'modify_coloring' => 'yes',
				],
			]
		);
		$this->add_control(
			'lightness',
			[
				'label' => esc_html__( 'Lightness', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'description' => esc_html__('Shifts lightness of colors by a percentage of the original value if decreasing and a percentage of the remaining value if increasing. Valid values: [-100, 100].','theplus'),
				'condition' => [
					'adv_modify_json' => 'yes',
					'modify_coloring' => 'yes',
				],
			]
		);
 
		$this->end_controls_section();
		/*map style creative*/
		/*map overlay*/
		$this->start_controls_section(
            'section_map_overlay_content',
            [
                'label' => esc_html__('Map Overlay', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'overlay_toggle',
			[
				'label' => wp_kses_post( "Content Over the Map <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "elementor-google-maps-text-overlay/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'description' => esc_html__( 'You can Put toggle on off button with content over the map using this option.', 'theplus' ),
			]
		);
		$this->add_control(
			'title_text',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' =>  esc_html__( 'Location Here', 'theplus' ),
				'description' => esc_html__( 'You can add title of map using this option.', 'theplus' ),
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_control(
			'overlay_content',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' =>  esc_html__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s.', 'theplus' ),
				'description' => esc_html__( 'You can add description of map using this option.', 'theplus' ),
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt-plus-overlay-map-content',
				'separator' => 'after',
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Title Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-overlay-map-content .gmap-title',
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-overlay-map-content .gmap-title' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-overlay-map-content .gmap-desc',
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-overlay-map-content .gmap-desc' => 'color: {{VALUE}}',
				],
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_control(
			'toggle_btn_color',
			[
				'label' => esc_html__( 'Toggle Button Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0, 0, 0, 0.4)',
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->add_control(
			'toggle_ative_color',
			[
				'label' => esc_html__( 'Toggle Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#81d742',
				'condition' => [
					'overlay_toggle' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*map overlay*/
 
		$this->start_controls_section('extraoptions_section',
			[
				'label' => esc_html__( 'Extra Options ', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'Maplisting',
			[
				'label' => esc_html__( 'Override for WP Search Filter', 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'description' => esc_html__( 'Note : You need to use Wp Search filter widget besides this for it’s auto connection.', 'theplus' ),
			]
		);
		$this->add_control(
			'mapattrtitlehide',
			[
				'label' => esc_html__( 'Hide Title', 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
			]
		);
		$this->add_control(
			'maponhover',
			[
				'label' => esc_html__( 'Content on Hover', 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
			]
		);
		$this->end_controls_section();
 
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
 
	 protected function render() {
        $settings = $this->get_settings_for_display();
		$PostId = get_the_ID();
		$WidgetId = $this->get_id();		
		$FilterType = !empty($settings["Maplisting"]) ? 'search_list' : '';
 
		$mapattrtitlehide = !empty($settings["mapattrtitlehide"]) ? 'hidetitlemap' : '';
		$maponhover = !empty($settings["maponhover"]) ? 'onhovercontent' : '';
 
		$map_style = !empty($settings["map_style"]) ? $settings["map_style"] : 'style-1';
		$adv_modify_json = !empty($settings["adv_modify_json"]) ? $settings["adv_modify_json"] : 'no';
		$modify_coloring = !empty($settings["modify_coloring"]) ? $settings["modify_coloring"] : 'no';
		$map_type = !empty($settings["map_type"]) ? $settings["map_type"] : 'ROADMAP';
		$hue = !empty($settings["hue"]) ? $settings["hue"] : '#ccc';
 
		if( !empty($FilterType) ){
			$theplus_google_map_api="";
			$GetheplusArray = get_option( 'theplus_api_connection_data' );
			if( !empty($GetheplusArray) && is_array($GetheplusArray) ){
				if( isset($GetheplusArray['theplus_google_map_api']) && !empty($GetheplusArray['theplus_google_map_api']) ){
					$theplus_google_map_api = $GetheplusArray['theplus_google_map_api'];
				}
			}
		}
 
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
 
		$json = array();
		$json1 = array();
		$json['places']  = array();
		$json['options'] = array();
		$json['style']   = array();
		$json['onhover']   = array();
		$json['hidetitle']   = array();
		$pin_icon='';
 
		if(!empty($maponhover) && $maponhover=='onhovercontent'){
			$json['onhover'][] = array(
				"onhovervalues"   => $maponhover
			);
		}
 
		if(!empty($mapattrtitlehide) && $mapattrtitlehide=='hidetitlemap'){
			$json['hidetitle'][] = array(
				"hidetitlevalues"   => $mapattrtitlehide
			);
		}	
 
 
		foreach($settings['map_locations'] as $index => $item ) {
			$longitude = !empty($item['longitude']) ? $item['longitude'] : '';
			$latitude = !empty($item['latitude']) ? $item['latitude'] : '';
			$address = !empty($item['address']) ? $item['address'] : '';
 
			if(!empty($item['pin_icon']["url"])){
				//$pin_icon=$item['pin_icon']["url"];
				$pin_icon = $item['pin_icon']['id'];
				$img = wp_get_attachment_image_src($pin_icon,$item['pin_icon_thumbnail_size']);
				$pin_icon = isset($img[0]) ? $img[0] : Utils::get_placeholder_image_src();
			}else{
				$pin_icon='';
			}
			if(!empty($longitude) || !empty($latitude)){
				$json['places'][] = array(
					"address"   => $address,
					"latitude"  => $latitude,
					"longitude" => $longitude,		
					"pin_icon" => $pin_icon
				);
			}
 
			if( !empty($FilterType) ){
				if(!empty($latitude) && !empty($longitude)){
					$URL = wp_remote_get("https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$theplus_google_map_api}");
					$StatusCode = wp_remote_retrieve_response_code($URL);
					$GetDataOne = wp_remote_retrieve_body($URL);
					if( $StatusCode == 200 ){
						$GetArray = json_decode($GetDataOne, true);
						if( !empty($GetArray) ){
							$address_components[] = array(
								'address_components' => !empty($GetArray['results'][0]['address_components']) ? $GetArray['results'][0]['address_components'] : '',
								"address"   => $address,
								"latitude"  => $latitude,
								"longitude" => $longitude,
								"pin_icon" => $pin_icon,
							);
						}
					}
				}
			}
		}
		$gmap_option=array();
		foreach ( $settings['gmap_option'] as $value ) {
			$gmap_option[]=$value;
		}
 
		$draggable=$pan_control=$zoom_control=$scale_control=$map_type_control=$scrollwheel=$fullscreen_control=$streetview_control=$marker_clustering='false';
		foreach($gmap_option as $key => $val) {
			if($val=='draggable'){
				$draggable='true';
			}
			if($val=='scroll_wheel'){
				$scrollwheel='true';
			}
			if($val=='pan_control'){
				$pan_control ='true';
			}
			if($val=='zoom_control'){
				$zoom_control='true';
			}
			if($val=='scale_control'){
				$scale_control ='true';
			}
			if($val=='map_type_control'){
				$map_type_control='true';
			}	
			if($val=='fullscreen_control'){
				$fullscreen_control='true';
			}
			if($val=='streetview_control'){
				$streetview_control='true';
			}
			if($val=='marker_clustering'){
				$marker_clustering='true';
			}
		}
 
		$json['options'] = array(
			"zoom" => intval($settings['zoom']["size"]),
			"scrollwheel" => $scrollwheel == 'true' ? true : false,
			"draggable"	=> $draggable == 'true' ? true : false,
			"panControl" => $pan_control == 'true' ? true : false,
			"zoomControl" => $zoom_control == 'true' ? true : false,
			"scaleControl" => $scale_control == 'true' ? true : false,
			"mapTypeControl" => $map_type_control == 'true' ? true : false,
			"fullscreenControl"	=> $fullscreen_control == 'true' ? true : false,
			"streetViewControl"	=> $streetview_control == 'true' ? true : false,
			"marker_clustering"	=> $marker_clustering == 'true' ? true : false,
			"mapTypeId"	=> $map_type
		);
 
		$maps_style='';
		if($modify_coloring == 'yes') {
			$json['style'][] = array(
				"stylers" => array(
					array("hue" => $hue),
					array("saturation" => $settings['saturation']["size"]),
					array("lightness" => $settings['lightness']["size"]),
					array("featureType" => "landscape.man_made","stylers" => array(array("visibility" => "on")))
				)
			);
			$maps_style='';
		}elseif($adv_modify_json == 'yes') {
			$maps_style=$map_style;
		}
 
		$uid=uniqid("plus-gmap");
 
		$serchAttr=$tp_list='';
		if( !empty($FilterType) ){
			$tp_list = 'tp_list';
 
			$jsonn = array(
				'load' => 'googlemap',
				'MapWidgetId' => $WidgetId,
				'PostId' => $PostId,
				'listing_type' => !empty($FilterType) ? $FilterType : '',
			);
			$data = array_merge( $json, $jsonn );
			$serchAttr = 'data-searchAttr= "'.htmlspecialchars(json_encode($data), ENT_QUOTES, "UTF-8").'" ';			
			$reaction_data = get_post_meta($PostId, 'tp-gmap-address-'.$WidgetId, true);
			if( !empty($reaction_data) ){
				update_post_meta( $PostId, 'tp-gmap-address-'.$WidgetId, $address_components );
			}else{
				add_post_meta( $PostId, 'tp-gmap-address-'.$WidgetId, $address_components );
			}
		}
 
		$json = str_replace("'", "&apos;", json_encode($json));
		$gmap_content ='<div class="pt-plus-adv-gmap">';
			$gmap_content .='<div id="'.esc_attr($uid).'" class="pt-plus-adv-map js-el '.esc_attr($animated_class).' '.esc_attr($tp_list).'" data-id="'.esc_attr($uid).'" data-adv-maps="'.htmlentities($json, ENT_QUOTES, "UTF-8").'" data-map-style="'.esc_attr($maps_style).'" '.$serchAttr.' '.$animation_attr.'></div>';
			if(!empty($settings["overlay_toggle"]) && $settings["overlay_toggle"]=='yes'){
				$toggle_btn_color = $settings["toggle_btn_color"];
				$toggle_ative_color = $settings["toggle_ative_color"];
				$title_text = $settings["title_text"];
				$overlay_content = $settings["overlay_content"];
 
				$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['box_background_image']) : '';
				$gmap_content .='<div class="pt-plus-overlay-map-content selected '.esc_attr($uid).' '.esc_attr($lz1).'"  data-uid="'.esc_attr($uid).'" data-toggle-btn-color="'.esc_attr($toggle_btn_color).'" data-toggle-active-color="'.esc_attr($toggle_ative_color).'">';
					$gmap_content .='<div class="gmap-title">'.wp_kses_post($title_text).'</div>';
					$gmap_content .='<div class="gmap-desc">'.wp_kses_post($overlay_content).'</div>';
					$gmap_content .='<div class="overlay-list-item"><input id="toggle_overlay_'.esc_attr($uid).'" type="checkbox" class="pt-plus-overlay-gmap pt-plus-overlay-gmap-tgl checked-'.esc_attr($uid).'"/><label for="toggle_overlay_'.esc_attr($uid).'" class="pt-plus-overlay-gmap-btn check-label-'.esc_attr($uid).'"></label></div>';
				$gmap_content .='</div>';
			}
		$gmap_content .='</div>';

		echo $gmap_content;
	}
 
    protected function content_template() {
 
    }
}