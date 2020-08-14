<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Surveys_Premium' ) ){

    class YITH_WC_Surveys_Premium extends YITH_WC_Surveys{

        protected static $instance;

        /**
         * @var string cookie name
         */
        protected $survey_cookie_name = 'yith_user_surveys_cookie';


        public function __construct(){

            parent::__construct();


            //manage plugin activation license
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
            add_action( 'widgets_init', array( $this, 'register_surveys_widget' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'include_survey_scripts' ) );
            add_action( 'init', array( $this, 'initialize_user_surveys' ) );

            //save surveys voting
            add_action( 'wp_ajax_save_surveys_voting', array( $this, 'save_surveys_voting' ) );
            add_action( 'wp_ajax_nopriv_save_surveys_voting', array( $this, 'save_surveys_voting' ) );

            /*Add Surveys in YITH PLUGIN*/
            add_action( 'admin_menu', array( $this, 'add_submenu_report_in_survey' ), 5 );



        }

        /**
         * return single instance
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WC_Surveys_Premium
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        public function add_submenu_report_in_survey(){

            add_submenu_page( 'edit.php?post_type=yith_wc_surveys', __( 'Reports', 'yith-woocommerce-surveys' ), __( 'Reports', 'yith-woocommerce-surveys' ), 'edit_surveys', 'survey-report', array( YITH_Surveys_Report(), 'output' ) );
            add_submenu_page( 'edit.php?post_type=yith_wc_surveys', __( 'Tools', 'yith-woocommerce-surveys' ), __( 'Tools', 'yith-woocommerce-surveys' ), 'manage_options', 'survey-export', array( $this, 'show_survey_export_tab' ) );
        }

        /**
         * show surveys-export tab in plugin option
         * @author YIThemes
         * @since 1.0.0
         *
         */
        public function show_survey_export_tab(){

            wc_get_template( 'admin/yith-wc-surveys-export.php', array(), '', YITH_WC_SURVEYS_TEMPLATE_PATH );
        }
        /**
         * initialize users survey list
         * @author YIThemes
         * @since 1.0.0
         */
        public function initialize_user_surveys(){

            if( is_user_logged_in() ){

                $user_id = get_current_user_id();

                $cookies  =   yith_getcookie( $this->survey_cookie_name );

                foreach( $cookies as $cookie ){

                    $args = array(
                        'user_id' => $user_id,
                        'survey_id' => $cookie['survey_id'],
                        'question'  => $cookie['question'],
                        'answer'    =>  $cookie['answer']
                    );

                    YITH_WC_Surveys_Utility::add( $args );
                }
                yith_destroycookie( $this->survey_cookie_name );

            }

            // update cookie from old version to new one
            $this->_destroy_serialized_cookies();
            $this->_update_cookies();
        }

        /**
         * include script in frontend
         * @author YIThemes
         * @since 1.0.0
         */
        public function include_survey_scripts(){

            $suffix = !( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';
            wp_register_script( 'surveys_script_frontend', YITH_WC_SURVEYS_ASSETS_URL.'js/yith_surveys_frontend_premium'.$suffix.'.js', array( 'jquery' ), YITH_WC_SURVEYS_VERSION, true );

            $yith_survey_params = array(
                'ajax_url'  =>  admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
                'actions'   => array(
                    'save_surveys_voting' => 'save_surveys_voting'
                    ),
                'messages' => array(
                    'survey_thanks_voting' => get_option( 'ywcsur_thanks_message' )
                ),
                'hide_survey_after_answer' => get_option( 'ywcsur_hide_after_answer' )
            );

            wp_localize_script( 'surveys_script_frontend', 'yith_survey_frontend_params', $yith_survey_params );
        }

        /**
         * save user voting
         * @author YIThemes
         * @since 1.0.0
         */
        public function save_surveys_voting(){


            if( isset( $_REQUEST['yith_surveys_ids'] ) && isset( $_REQUEST['yith_answers_ids'] ) ){


                $user_id = is_user_logged_in() ? get_current_user_id() : -1;
                $survey_id = $_REQUEST['yith_surveys_ids'];
                $answ_id = $_REQUEST['yith_answers_ids'];
                $survey_name = get_the_title( $survey_id );
                $answ_name = get_the_title( $answ_id );
                $args = array(
                    'user_id' => $user_id,
                    'survey_id' => $survey_id,
                    'answer_id' => $answ_id,
                    'question'  => $survey_name,
                    'answer' => $answ_name
                );

                $result = YITH_WC_Surveys_Utility::add( $args );

                delete_transient( 'yith_surveys_results_transient' );

	            YITH_WC_Surveys_Utility::update_answer_info( $answ_id);

                wp_send_json( array( 'result' => $result, 'answer_name' => $answ_name ) );
            }
        }

        /**
         * Destroy serialize cookies, to prevent major vulnerability
         * @author YITHEMES
         * @return void
         * @since 1.0.0
         */
        private function _destroy_serialized_cookies(){

            if ( isset( $_COOKIE[ $this->survey_cookie_name ] ) && is_serialized( stripslashes( $_COOKIE[ $this->survey_cookie_name ] ) ) ) {
                $_COOKIE[ $this->survey_cookie_name ] = json_encode( array() );
                yith_destroycookie( $this->survey_cookie_name );
            }
        }

        /**
         * Update old savelist cookies
         * @author YITHEMES
         * @return void
         * @since 1.0.0
         */
        private function _update_cookies(){

            $cookie = yith_getcookie( $this->survey_cookie_name );
            $new_cookie = array();

            if( ! empty( $cookie ) ) {
                foreach ( $cookie as $item ) {
                    $new_cookie[] = array(
                        'survey_id'     => $item['survey_id'],
                        'question'    => $item['question'],
                        'answer'  => $item['answer']
                    );
                }

                yith_setcookie( $this->survey_cookie_name, $new_cookie );
            }
        }

        /** Register plugins for activation tab
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@YIThemes.com>
         */
        public function register_plugin_for_activation() {
            if ( !class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once YITH_WC_SURVEYS_DIR.'plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_WC_SURVEYS_DIR.'plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register( YITH_WC_SURVEYS_INIT, YITH_WC_SURVEYS_SECRET_KEY, YITH_WC_SURVEYS_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates() {
            if ( !class_exists( 'YIT_Upgrade' ) ) {
                require_once( YITH_WC_SURVEYS_DIR.'plugin-fw/lib/yit-upgrade.php' );
            }
            YIT_Upgrade()->register( YITH_WC_SURVEYS_SLUG, YITH_WC_SURVEYS_INIT );
        }

        /**register YITH WooCommerce Surveys widget
         * @author YIThemes
         * @since 1.0.0
         * @use widgets_init
         */
        public function register_surveys_widget()
        {
            register_widget( 'YITH_WC_Surveys_Widget' );
        }

	    /**
	     * plugin_row_meta
	     *
	     * add the action links to plugin admin page
	     *
	     * @param $new_row_meta_args
	     * @param $plugin_meta
	     * @param $plugin_file
	     * @param $plugin_data
	     * @param $status
	     *
	     * @return   array
	     * @since    1.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     * @use plugin_row_meta
	     */
	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WC_SURVEYS_INIT' ) {

	    	$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );
		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
			    $new_row_meta_args['is_premium'] = true;

		    }

		    return $new_row_meta_args;

	    }

    }
}