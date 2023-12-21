<?php

namespace Happy_Addons\Elementor;


defined('ABSPATH') || die();

class Api_Handler
{
    public static $catwise_free_widget_map = [];
    public static $disabled_widgets = [];

    public static $featureMap = [];
    public static $disabled_features = [];
    public static $active_widgets = [];

    const FEATURES_DB_KEY = 'happyaddons_inactive_features';
    const WIDGETS_DB_KEY = 'happyaddons_inactive_widgets'; 
    const CONSENT_DB_KEY = 'happy-elementor-addons_allow_tracking'; 
    const CACHE_DB_KEY = 'happy-elementor-addons_wizard_cache_key'; 
    const WIZARD_CACHE_FIX  = "happy-elementor-x98237938759348573";

    public static function init()
    {
        // need to delete after 1/2 release
        if(get_option('happy-elementor-addons_wizard_cache')){
            delete_option('happy-elementor-addons_wizard_cache');
        }

        add_action('rest_api_init', [__CLASS__, 'ha_wizard_routes']);


        if(!get_option(self::WIZARD_CACHE_FIX)){
            delete_option(self::CACHE_DB_KEY);
            update_option(self::WIZARD_CACHE_FIX,1);
        }
    }

    public static function ha_wizard_routes()
    {
        /* Get Ends */
        register_rest_route('happy/v1', '/wizard/dummy', array(
            'methods' => 'GET',
            'callback' => [__CLASS__,'ha_wizard_get_dummy_data'],
            'permission_callback' => function () {
                return true;
            }
        ));

        register_rest_route('happy/v1', '/wizard/cache', array(
            'methods' => 'GET',
            'callback' => [__CLASS__,'ha_wizard_get_cache_data'],
            'permission_callback' => function () {
                return current_user_can( 'edit_others_posts' );
            }
        ));

        register_rest_route('happy/v1', '/wizard/preset/normal', array(
            'methods' => 'GET',
            'callback' => [__CLASS__,'ha_wizard_get_preset'],
            'permission_callback' => function () {
                return current_user_can( 'edit_others_posts' );
            }
        ));

        register_rest_route('happy/v1', '/wizard/preset/pro', array(
            'methods' => 'GET',
            'callback' => [__CLASS__,'ha_wizard_get_preset_pro'],
            'permission_callback' => function () {
                return current_user_can( 'edit_others_posts' );
            }
        ));


        /* Get Ends */
        register_rest_route('happy/v1', '/wizard/save', array(
            'methods' => 'POST',
            'callback' => [__CLASS__,'ha_wizard_save_data'],
            'permission_callback' => function () {
                return current_user_can( 'edit_others_posts' );
            }
        ));

        register_rest_route('happy/v1', '/wizard/save-cache', array(
            'methods' => 'POST',
            'callback' => [__CLASS__,'ha_wizard_save_cache_data'],
            'permission_callback' => function () {
                return current_user_can( 'edit_others_posts' );
            }
        ));

        register_rest_route('happy/v1', '/wizard/skip', array(
            'methods' => 'POST',
            'callback' => [__CLASS__,'ha_wizard_skip'],
            'permission_callback' => function () {
                return current_user_can( 'edit_others_posts' );
            }
        ));
    }

    public static function ha_wizard_get_dummy_data(){

        $widgets = Extensions_Manager::get_local_features_map();
        $widgets = array_keys($widgets);

        $skip = [
        'background-overlay',
        'floating-effects',
        'css-transform',
        'column-extended',
        'equal-height',
        'shape-divider',
        'wrapper-link'
        ];

        $response = array_diff($widgets,$skip);

        return false;
    }

    public static function ha_wizard_get_preset(){
        self::$disabled_widgets = ["image-compare","justified-gallery","carousel","skills","gradient-heading","wpform","ninjaform","calderaform","weform","dual-button","number","flip-box","calendly","pricing-table","step-flow","gravityforms","news-ticker","bar-chart","twitter-feed","post-tab","taxonomy-list","threesixty-rotation","fluent-form","data-table","social-share","image-hover-effect","event-calendar","link-hover","mailchimp","content-switcher","image-stack-group"];
        self::haGetWidgetList();

        self::$disabled_features = ["grid-layer","advanced-tooltip","text-stroke"];
        self::haGetFeatureList();

        $response = [
            'info'    => 'Preset data for regular users',
            'widgets' => [
                'all' => self::$catwise_free_widget_map,
                'disabled' => self::$disabled_widgets
            ],
            'features'=>[
                'all' => self::$featureMap,
                'disabled' => self::$disabled_features
            ]
        ];

        return $response;
    }

    public static function ha_wizard_get_preset_pro(){
        self::$disabled_widgets = ["image-compare","carousel","skills","gradient-heading","wpform","ninjaform","calderaform","weform","number","flip-box","calendly","pricing-table","gravityforms","news-ticker","bar-chart","twitter-feed","post-tab","taxonomy-list","threesixty-rotation","fluent-form","data-table","social-share","event-calendar","link-hover","image-stack-group"];
        self::haGetWidgetList();

        self::$disabled_features = ["grid-layer","advanced-tooltip","text-stroke"];
        self::haGetFeatureList();

        $response = [
            'info'    => 'Preset data for advanced users',
            'widgets' => [
                'all' => self::$catwise_free_widget_map,
                'disabled' => self::$disabled_widgets
            ],
            'features'=>[
                'all' => self::$featureMap,
                'disabled' => self::$disabled_features
            ]
        ];

        return $response;
    }


    public static function ha_wizard_save_data(\WP_REST_Request $request){
        $data = json_decode( $request->get_body() );

        if($data){
            if(isset($data->widget)){
                Widgets_Manager::save_inactive_widgets($data->widget);
            }
            if(isset($data->features)){
                Extensions_Manager::save_inactive_features($data->features);
            }

            if(isset($data->consent)){
                update_option(self::CONSENT_DB_KEY,'yes');
            }

            update_option(HAPPY_ADDONS_WIZARD_REDIRECTION_FLAG,1);
            delete_option(self::CACHE_DB_KEY);
        }

        $response = [
            'status'  => 200,
            'info'    => 'Save wizard settings',
            'dump'    => $data
        ];

        return $response;
    }

    public static function ha_wizard_skip(\WP_REST_Request $request){
        
        $stat = update_option(HAPPY_ADDONS_WIZARD_REDIRECTION_FLAG,1);

        $response = [
            'status'  => 200,
            'info'    => 'Skipped the setup',
            'update'  => $stat
        ];

        return $response;
    }

    public static function ha_wizard_save_cache_data(\WP_REST_Request $request){
        $data = $request->get_body();

        if($data){
            update_option(self::CACHE_DB_KEY,$data);
        }

        $response = [
            'status'  => 200,
            'info'    => 'Preset data for advanced users',
            'dump'    => json_decode( $data )
        ];

        return $response;
    }

    public static function ha_wizard_get_cache_data(){
        $data = get_option(self::CACHE_DB_KEY,'');
        $data = json_decode($data);
        $response = [
            'status'  => 200,
            'info'    => 'Cache data',
            'data'    => $data
        ];
        return $response;
    }

    public static function ha_wizard_get_widgets(){
        self::$disabled_widgets = Widgets_Manager::get_inactive_widgets();
        self::haGetWidgetList();

        $response = [
            'all' => self::$catwise_free_widget_map,
            'disabled' => self::$disabled_widgets
        ];

        return $response;
    }

    protected static function haGetWidgetList(){
		$default_active = Widgets_Manager::get_default_active_widget();
		self::$active_widgets = array_intersect($default_active,self::$disabled_widgets);
        $widgets = Widgets_Manager::get_widgets_map();
        unset( $widgets[ Widgets_Manager::get_base_widget_key() ] );

        array_walk($widgets, function($item, $key){
		    self::$catwise_free_widget_map[$item["cat"]][$key] = [
		        'slug' => $key,
		        'demo' => isset($item["demo"])? $item["demo"]: '',
		        'title' => $item["title"],
		        'icon' => $item["icon"],
		        'is_pro' => isset($item["is_pro"])? $item["is_pro"]: false,
                // 'is_active' => (!in_array($key, self::$disabled_widgets))?true:false
                'is_active' => (!in_array($key, self::$active_widgets))?true:false
		    ];

            sort(self::$catwise_free_widget_map[$item["cat"]]);
		});
    }

    protected static function haGetFeatureList(){
        $faetures = Extensions_Manager::get_local_features_map();
        
        array_walk($faetures, function($item, $key){
		    self::$featureMap[$key] = [
		        'slug' => $key,
		        'demo' => isset($item["demo"])? $item["demo"]: '',
		        'title' => $item["title"],
		        'icon' => $item["icon"],
		        'is_pro' => isset($item["is_pro"])? $item["is_pro"]: false,
                'is_active' => (!in_array($key, self::$disabled_features))?true:false
		    ];
		});
    }
}

Api_Handler::init();
