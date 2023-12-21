<?php
/**
 * Dashboard manager
 *
 * Package: Happy_Addons
 * @since 2.0.0
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class Dashboard {

    const PAGE_SLUG = 'happy-addons';

    const WIZARD_PAGE_SLUG = 'happy-addons-setup-wizard';

    const LICENSE_PAGE_SLUG = 'happy-addons-license';

    const WIDGETS_NONCE = 'ha_save_dashboard';

    const WIZARD_NONCE = 'wp_rest';

    static $menu_slug = '';

    public static $catwise_widget_map = [];
    public static $catwise_free_widget_map = [];

    static $wizard_slug = '';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_menu' ], 21 );
        add_action( 'admin_menu', [ __CLASS__, 'update_menu_items' ], 99 );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_' . self::WIDGETS_NONCE, [ __CLASS__, 'save_data' ] );

        add_action( 'admin_init', [ __CLASS__, 'activation_redirect' ] );
        add_filter( 'plugin_action_links_' . plugin_basename( HAPPY_ADDONS__FILE__ ), [ __CLASS__, 'add_action_links' ] );

        add_action( 'happyaddons_save_dashboard_data', [ __CLASS__, 'save_widgets_data' ], 1);
        add_action( 'happyaddons_save_dashboard_data', [ __CLASS__, 'save_features_data' ] );
        add_action( 'happyaddons_save_dashboard_data', [ __CLASS__, 'save_credentials_data' ] );
        add_action( 'happyaddons_save_dashboard_data', [ __CLASS__, 'disable_unused_widget' ], 10);

        add_action( 'in_admin_header', [ __CLASS__, 'remove_all_notices' ], PHP_INT_MAX );

        add_action( 'admin_menu', function() {
            remove_menu_page( 'happy-addons-setup-wizard' );
        }, 100 );
    }

    public static function is_page() {
        return ( isset( $_GET['page'] ) && ( sanitize_text_field($_GET['page']) === self::PAGE_SLUG || sanitize_text_field($_GET['page']) === self::LICENSE_PAGE_SLUG ) );
    }

    public static function remove_all_notices() {
        if ( self::is_page() ) {
            remove_all_actions( 'admin_notices' );
            remove_all_actions( 'all_admin_notices' );
        }
    }

    public static function activation_redirect() {
        if ( get_option( HAPPY_ADDONS_REDIRECTION_FLAG, false ) ) {
			delete_option( HAPPY_ADDONS_REDIRECTION_FLAG );
			exit( esc_url( wp_safe_redirect( ha_get_dashboard_link() ) ) );
		}

		if ( get_option( HAPPY_ADDONS_WIZARD_REDIRECTION_FLAG, false ) == 'initiate' ) {
			update_option( HAPPY_ADDONS_WIZARD_REDIRECTION_FLAG, 'running' );
			exit( esc_url( wp_safe_redirect( ha_get_setup_wizard_link() ) ) );
		}
    }

    public static function add_action_links( $links ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return $links;
        }

        $links = array_merge( [
            sprintf( '<a href="%s">%s</a>',
                ha_get_dashboard_link(),
                esc_html__( 'Settings', 'happy-elementor-addons' )
            )
        ], $links );
        if ( ! ha_has_pro() ) {
            $links = array_merge( $links, [
                sprintf( '<a target="_blank" style="color:#e2498a; font-weight: bold;" href="%s">%s</a>',
                    'https://happyaddons.com/go/get-pro',
                    esc_html__( 'Get Pro', 'happy-elementor-addons' )
                )
            ] );
        }
        return $links;
    }

    public static function save_data() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! check_ajax_referer( self::WIDGETS_NONCE, 'nonce' ) ) {
            wp_send_json_error();
        }

        $posted_data = ! empty( $_POST['data'] ) ? ha_sanitize_array_recursively($_POST['data']) : '';
        $data = [];
        parse_str( $posted_data, $data );

        do_action( 'happyaddons_save_dashboard_data', $data );

        wp_send_json_success();
    }

    public static function save_widgets_data( $data ) {
        $widgets = ! empty( $data['widgets'] ) ? $data['widgets'] : [];
        $inactive_widgets = array_values( array_diff( array_keys( self::get_real_widgets_map() ), $widgets ) );
        Widgets_Manager::save_inactive_widgets( $inactive_widgets );
    }

    public static function save_features_data( $data ) {
        $features = ! empty( $data['features'] ) ? $data['features'] : [];

        /* Check whether Pro is available and allow to disable pro features */
        $widgets_map = self::get_real_features_map();
        if ( ha_has_pro() ) {
            $widgets_map = array_merge( $widgets_map, Extensions_Manager::get_pro_features_map() );
        }

        $inactive_features = array_values( array_diff( array_keys( $widgets_map ), $features ) );

        Extensions_Manager::save_inactive_features( $inactive_features );
    }

    public static function save_credentials_data( $data ) {
        $credentials = ! empty( $data['credentials'] ) ? $data['credentials'] : [];
        Credentials_Manager::save_credentials( $credentials );
    }

    public static function disable_unused_widget( $data ) {
        $disable_unused_widgets = ( ( ! empty( $data['disable-unused-widgets'] ) ) && ( 'true' == $data['disable-unused-widgets'] ) ) ? true : false;

		if( $disable_unused_widgets ){
			$inactive_widgets = \Happy_Addons\Elementor\Widgets_Manager::get_inactive_widgets();
			$unuse_widget = self::get_un_usage();
			$disable = array_unique(array_merge( $inactive_widgets, $unuse_widget ));
            Widgets_Manager::save_inactive_widgets( $disable );
		}
    }

    public static function enqueue_scripts( $hook ) {
        // css for dashboard widget
		$screen = \get_current_screen();
		if($screen->id == 'dashboard') {
			wp_enqueue_style(
				'happy-elementor-addons-wp-dashboard',
				HAPPY_ADDONS_ASSETS . 'admin/css/wp-dashboard.min.css',
				null,
				HAPPY_ADDONS_VERSION
			);
		}

        if ( self::$wizard_slug == $hook && current_user_can( 'manage_options' ) ) {
            wp_enqueue_style(
                'happy-elementor-addons-wizard',
                HAPPY_ADDONS_ASSETS . 'admin/css/wizard.min.css',
                null,
                HAPPY_ADDONS_VERSION
            );

            wp_enqueue_script(
                'vue-js-3',
                HAPPY_ADDONS_ASSETS . 'vendor/vue3/vue.global.prod.js',
                null,
                '3',
                true
            );

            wp_register_script(
                'happy-elementor-addons-wizard',
                HAPPY_ADDONS_ASSETS . 'admin/js/wizard.min.js',
                [ 'jquery', 'vue-js-3' ],
                HAPPY_ADDONS_VERSION,
                true
            );

            wp_localize_script(
                'happy-elementor-addons-wizard',
                'HappyWizard',
                [
                    'nonce'   => wp_create_nonce( self::WIZARD_NONCE ),
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'apiBase' => get_rest_url( null, 'happy/v1' ),
                    'haAdmin' => admin_url( 'admin.php?page=happy-addons' ),
                ]
            );

            wp_enqueue_script( 'happy-elementor-addons-wizard' );
        }

        if ( self::$menu_slug !== $hook || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        wp_enqueue_style(
            'happy-icons',
            HAPPY_ADDONS_ASSETS . 'fonts/style.min.css',
            null,
            HAPPY_ADDONS_VERSION
        );

        wp_enqueue_style(
            'google-nunito-font',
            HAPPY_ADDONS_ASSETS . 'fonts/nunito/stylesheet.css',
            null,
            HAPPY_ADDONS_VERSION
        );

        wp_enqueue_style(
            'happy-elementor-addons-dashboard',
            HAPPY_ADDONS_ASSETS . 'admin/css/dashboard.min.css',
            null,
            HAPPY_ADDONS_VERSION
        );

        /**
         * Magnific popup
         */
        wp_enqueue_style(
            'magnific-popup',
            HAPPY_ADDONS_ASSETS . 'vendor/magnific-popup/magnific-popup.css',
            null,
            HAPPY_ADDONS_VERSION
        );

        wp_enqueue_script(
            'jquery-magnific-popup',
            HAPPY_ADDONS_ASSETS . 'vendor/magnific-popup/jquery.magnific-popup.min.js',
            null,
            HAPPY_ADDONS_VERSION,
            true
        );

        wp_enqueue_script(
            'happy-elementor-addons-dashboard',
            HAPPY_ADDONS_ASSETS . 'admin/js/dashboard.min.js',
            [ 'jquery' ],
            HAPPY_ADDONS_VERSION,
            true
        );

        wp_localize_script(
            'happy-elementor-addons-dashboard',
            'HappyDashboard',
            [
                'nonce' => wp_create_nonce( self::WIDGETS_NONCE ),
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'action' => self::WIDGETS_NONCE,
                'saveChangesLabel' => esc_html__( 'Save Changes', 'happy-elementor-addons' ),
                'savedLabel' => esc_html__( 'Changes Saved', 'happy-elementor-addons' ),
            ]
        );
    }

    private static function get_real_widgets_map() {
        $widgets_map = Widgets_Manager::get_widgets_map();
        unset( $widgets_map[ Widgets_Manager::get_base_widget_key() ] );
        return $widgets_map;
    }

    public static function get_widgets() {
        $widgets_map = self::get_real_widgets_map();

        if ( ! ha_has_pro() ) {
            $widgets_map = array_merge( $widgets_map, Widgets_Manager::get_pro_widget_map() );
        }
        elseif( ha_has_pro() && version_compare( HAPPY_ADDONS_PRO_VERSION, '2.1.0', '<=' ) ) {
			$widgets_map = array_merge( $widgets_map, Widgets_Manager::get_pro_widget_map() );
		}

        uksort( $widgets_map, [ __CLASS__, 'sort_widgets' ] );
        return $widgets_map;
    }


	public static function get_widget_map_catwise() {
		$widgets = self::get_widgets();
		array_walk($widgets, function($item, $key){
			$item["cat"] = isset($item["cat"]) ? $item["cat"] : 'general'; // this code will be remove after next 2 release
		    self::$catwise_widget_map[$item["cat"]][$key] = [
		        'demo' => isset($item["demo"])? $item["demo"]: '',
		        'title' => $item["title"],
		        'icon' => $item["icon"],
		        'is_pro' => isset($item["is_pro"])? $item["is_pro"]: false,
		    	];
			}
		);

		return self::$catwise_widget_map;
	}

    public static function get_free_widget_map_catwise() {
		$widgets = self::get_real_widgets_map();
		array_walk($widgets, function($item, $key){
		    self::$catwise_free_widget_map[$item["cat"]][$key] = [
		        'demo' => isset($item["demo"])? $item["demo"]: '',
		        'title' => $item["title"],
		        'icon' => $item["icon"],
		        'is_pro' => isset($item["is_pro"])? $item["is_pro"]: false,
		    ];
		});
		return self::$catwise_free_widget_map;
	}

    private static function get_real_features_map() {
        $widgets_map = Extensions_Manager::get_features_map();
        return $widgets_map;
    }

    public static function get_features() {
        $widgets_map = self::get_real_features_map();

        $widgets_map = array_merge( $widgets_map, Extensions_Manager::get_pro_features_map() );

        uksort( $widgets_map, [ __CLASS__, 'sort_widgets' ] );
        return $widgets_map;
    }

    public static function get_credentials() {

        $credentail_map = Credentials_Manager::get_credentials_map();

        $credentail_map = array_merge( $credentail_map, Credentials_Manager::get_pro_credentials_map() );

        return $credentail_map;
    }

    public static function sort_widgets( $k1, $k2 ) {
        return strcasecmp( $k1, $k2 );
    }

    public static function add_menu() {
        self::$menu_slug = add_menu_page(
            __( 'Happy Elementor Addons Dashboard', 'happy-elementor-addons' ),
            __( 'Happy Addons', 'happy-elementor-addons' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_main' ],
            ha_get_b64_icon(),
            58.5
        );

        self::$wizard_slug =  add_menu_page(
            __( 'Setup Wizard', 'happy-elementor-addons' ),
            __( 'Setup Wizard', 'happy-elementor-addons' ),
            'manage_options',
            self::WIZARD_PAGE_SLUG,
            [ __CLASS__, 'wizard_page_wrapper'],
            '',
            null
        );

        $tabs = self::get_tabs();
        if ( is_array( $tabs ) ) {
            foreach ( $tabs as $key => $data ) {
                if ( empty( $data['renderer'] ) || ! is_callable( $data['renderer'] ) ) {
                    continue;
                }

                add_submenu_page(
                    self::PAGE_SLUG,
                    sprintf( __( '%s - Happy Elementor Addons', 'happy-elementor-addons' ), $data['title'] ),
                    $data['title'],
                    'manage_options',
                    self::PAGE_SLUG . '#' . $key,
                    [ __CLASS__, 'render_main' ]
                );
            }
        }
    }

    public static function update_menu_items() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        global $submenu;
        $menu = $submenu[ self::PAGE_SLUG ];
        array_shift( $menu );
        $submenu[ self::PAGE_SLUG ] = $menu;
    }

	public static function get_raw_usage( $format = 'raw' ) {
		/** @var Module $module */
        
		$module = \Elementor\Modules\Usage\Module::instance();
		$usage = PHP_EOL;
		$widgets_list = [];
		if( ha_has_pro() ){
			$all_widgets = self::get_widgets();
		}else{
			$all_widgets = Widgets_Manager::get_local_widgets_map();
		}
        if(is_array($module->get_formatted_usage( $format ))||is_object($module->get_formatted_usage( $format ))){
            foreach ( $module->get_formatted_usage( $format ) as $doc_type => $data ) {
                $usage .= "\t{$data['title']} : " . $data['count'] . PHP_EOL;

                if(is_array($data['elements'])||is_object($data['elements'])){
                    foreach ( $data['elements'] as $element => $count ) {
                        $usage .= "\t\t{$element} : {$count}" . PHP_EOL;
                        $is_happy_widget = strpos( $element , "ha-") !== false;
                        $widget_key = str_replace('ha-','',$element);

                        if( $is_happy_widget && array_key_exists( $widget_key, $all_widgets ) ) {

                            $widgets_list[ $widget_key ] = $count;
                        }
                    }
                }
            }
        }
		return $widgets_list;
	}

	public static function get_un_usage() {
		if( ha_has_pro() ){
			$all_widgets = self::get_widgets();
		}else{
			$all_widgets = Widgets_Manager::get_local_widgets_map();
		}
		$used_widgets = self::get_raw_usage();
		$get_diff = array_diff( array_keys( $all_widgets ), array_keys( $used_widgets ) );
		// return $get_diff;
		return array_values($get_diff);
	}

    public static function get_tabs() {
        $tabs = [
            'home' => [
                'title' => esc_html__( 'Home', 'happy-elementor-addons' ),
                'renderer' => [ __CLASS__, 'render_home' ],
            ],
            'widgets' => [
                'title' => esc_html__( 'Widgets', 'happy-elementor-addons' ),
                'renderer' => [ __CLASS__, 'render_widgets' ],
            ],
            'features' => [
                'title' => esc_html__( 'Features', 'happy-elementor-addons' ),
                'renderer' => [ __CLASS__, 'render_features' ],
            ],
            'credentials' => [
                'title' => esc_html__( 'Credentials', 'happy-elementor-addons' ),
                'renderer' => [ __CLASS__, 'render_credentials' ],
            ],
            'analytics' => [
                'title' => esc_html__( 'Analytics', 'happy-elementor-addons' ),
                'renderer' => [ __CLASS__, 'render_analytics' ],
            ],
            'pro' => [
                'title' => esc_html__( 'Get Pro', 'happy-elementor-addons' ),
                'renderer' => [ __CLASS__, 'render_pro' ],
            ],
        ];

        return apply_filters( 'happyaddons_dashboard_get_tabs', $tabs );
    }

    private static function load_template( $template ) {
        $file = HAPPY_ADDONS_DIR_PATH . 'templates/admin/dashboard-' . $template . '.php';
        if ( is_readable( $file ) ) {
            include( $file );
        }
    }

    private static function load_wizard_template( $template ) {
        $file = HAPPY_ADDONS_DIR_PATH . 'templates/wizard/wizard-' . $template . '.php';
        if ( is_readable( $file ) ) {
            include( $file );
        }
    }

    public static function render_main() {
        self::load_template( 'main' );
    }

    public static function render_home() {
        self::load_template( 'home' );
    }

    public static function render_widgets() {
        self::load_template( 'widgets' );
    }

    public static function render_features() {
        self::load_template( 'features' );
    }

    public static function render_credentials() {
        self::load_template( 'credentials' );
    }

    public static function render_analytics() {
        self::load_template( 'analytics' );
    }

    public static function render_pro() {
        self::load_template( 'pro' );
    }

    /**
	 * Set up a div for the app to render into.
	 */
	public static function wizard_page_wrapper() {
		?>
		<div class="wrap" id="ha-setup-wizard">
            <div id="loader-wrap" v-if="!loaded">
                <div class="loader">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
			<div id="wizard-root" :class="{ visible: loaded }">
                <div class="ha-setup-wizard__header">
                    <div class="ha-stepper">
                        <div class="ha-stepper__steps">
                            <ha-step
                                v-for="(step, index) in steps"
                                :active="currentPage"
                                :complete="step.isComplete"
                                :step="step.key"
                                :title="step.name"
                                :index="index+1"
                                @set-tab="setTab">
                            </ha-step>
                        </div>
                    </div>
                </div>
                <div class="ha-setup-wizard__container">
                    <div class="ha-setup-wizard__container_content">
                        <div class="ha-step-content ha-step-welcome welcome-step-bg" v-if="currentPage == 'welcome'">
                            <?php self::load_wizard_template( 'welcome' ); ?>
                        </div>
                        <div class="ha-step-content ha-step-widgets" v-if="currentPage == 'widgets'">
                            <?php self::load_wizard_template( 'widgets' ); ?>
                        </div>
                        <div class="ha-step-content ha-step-features" v-if="currentPage == 'features'">
                            <?php self::load_wizard_template( 'features' ); ?>
                        </div>
                        <div class="ha-step-content ha-step-bepro" v-if="currentPage == 'bepro'">
                            <?php self::load_wizard_template( 'bepro' ); ?>
                        </div>
                        <div class="ha-step-content ha-step-contribute" v-if="currentPage == 'contribute'">
                            <?php self::load_wizard_template( 'contribute' ); ?>
                        </div>
                        <div class="ha-step-content ha-step-congrats congrats-step-bg" v-if="currentPage == 'congrats'">
                            <?php self::load_wizard_template( 'congrats' ); ?>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<?php
	}
}

Dashboard::init();
