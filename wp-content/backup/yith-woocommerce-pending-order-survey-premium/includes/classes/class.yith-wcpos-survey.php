<?php
if (!defined('ABSPATH'))
    exit;

if (!class_exists('YITH_WCOP_Survey')) {

    class YITH_WCOP_Survey
    {

        /**
         * @var YITH_WCOP_Survey static instance
         */
        protected static $instance;
        /**
         * @var YITH_Surveys_Post_Type Post Type
         */
        protected $survey;

        /**
         * @var YIT_Plugin_Panel_Woocommerce instance
         */
        protected $_panel;

        /**
         * @var YIT_Plugin_Panel_Woocommerce instance
         */
        protected $_panel_page = 'yith_wc_pending_order_survey_panel';

        /**
         * @var YITH_WCOP_Survey_Post_Type
         */
        public $pending_order_survey;

        public $suffix;

        public function __construct()
        {


            /* Plugin Informations */
            add_action('plugins_loaded', array($this, 'plugin_fw_loader'), 15);
            add_action('plugins_loaded', array($this, 'load_privacy'), 20);
            add_filter('plugin_action_links_' . plugin_basename(YITH_WCPO_SURVEY_DIR . '/' . basename(YITH_WCPO_SURVEY_FILE)), array($this, 'action_links'));
            add_filter('yith_show_plugin_row_meta', array($this, 'plugin_row_meta'), 10, 5 );

            //manage plugin activation license
            add_action('wp_loaded', array($this, 'register_plugin_for_activation'), 99);
            add_action('admin_init', array($this, 'register_plugin_for_updates'));

            add_action('admin_menu', array($this, 'add_pending_order_survey_menu'), 5);

            add_action('yith_ywcpos_pending_survey', array($this, 'show_pending_survey_tab'));
            add_action('yith_ywcpos_pending_survey_email', array($this, 'show_pending_survey_email_tab'));
            add_action('yith_ywcpos_pending_survey_order', array($this, 'show_pending_order_tab'));
            add_action('yith_ywcpos_pending_survey_order_recovered', array($this, 'show_pending_order_recovered_tab'));
            add_action('yith_ywcpos_pending_survey_report', array($this, 'show_pending_report_tab'));

            add_action('admin_enqueue_scripts', array($this, 'add_admin_style_script'));
            add_action('wp_enqueue_scripts', array($this, 'add_frontend_style_script'));

            add_action('wp_ajax_add_new_question', array($this, 'add_new_question'));
            add_action('wp_ajax_nopriv_add_new_question', array($this, 'add_new_question'));
            add_action('wp_ajax_validate_survey', array($this, 'validate_survey'));
            add_action('wp_ajax_nopriv_validate_survey', array($this, 'validate_survey'));

            //Add context menu to TinyMCE editor
            add_action('admin_init', array($this, 'add_shortcodes_button'));

            add_filter('woocommerce_email_classes', array($this, 'add_woocommerce_emails'));
            add_action('woocommerce_init', array($this, 'load_wcpos_mailer'));

            //Add custom meta for order in pending
            add_action('woocommerce_order_status_changed', array($this, 'remove_order_pending_meta'), 20, 3);

            add_action('update_option_ywcpos_pending_from_cancelled_unit', array($this, 'update_woocommerce_hold_stock_minutes_option'));
            add_action('update_option_ywcpos_pending_from_cancelled_value', array($this, 'update_woocommerce_hold_stock_minutes_option'));
            add_action( 'update_option_ywcpos_include_pending_from', array( $this,'reinitialize_check_pending_order' ) );
            $this->pending_order_survey = YITH_Pending_Order_Survey_Type();
            $this->pending_order_survey_email = YITH_Pending_Email_Type();
            $this->suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

            $show_checkbox = get_option( 'ywcpos_user_privacy', 'no' );

            if( 'yes' == $show_checkbox ){

                add_action( 'woocommerce_checkout_terms_and_conditions', array( $this, 'show_checkbox' ), 15 );
                add_action( 'woocommerce_checkout_create_order', array( $this, 'register_customer_choose' ), 20 );
            }


        }

        /**
         * return single instance
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WCOP_Survey
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * load plugin framework 2.0
         * @author YIThemes
         * @since 1.0.0
         */
        public function plugin_fw_loader()
        {
            if (!defined('YIT_CORE_PLUGIN')) {
                global $plugin_fw_data;
                if (!empty($plugin_fw_data)) {
                    $plugin_fw_file = array_shift($plugin_fw_data);
                    require_once($plugin_fw_file);
                }
            }
        }

        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed
         * @use plugin_action_links_{$plugin_file_name}
         */
        public function action_links($links)
        {
            $links = yith_add_action_links( $links, $this->_panel_page, true );
            return $links;
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
	     * @param $init_file
	     *
	     * @return   array
	     * @since    1.0.0
	     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	     * @use $new_row_meta_args
	     */
	    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCPO_SURVEY_INIT' ) {

		    if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
			    $new_row_meta_args['slug'] = YITH_WCPO_SURVEY_SLUG;
			    $new_row_meta_args['is_premium'] = true;

		    }

		    return $new_row_meta_args;
	    }

        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri()
        {
            return defined('YITH_REFER_ID') ? $this->_premium_landing_url . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing_url . '?refer_id=1030585';
        }

        /** Register plugins for activation tab
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@YIThemes.com>
         */
        public function register_plugin_for_activation()
        {
            if (!class_exists('YIT_Plugin_Licence')) {
                require_once(YITH_WCPO_SURVEY_DIR . 'plugin-fw/licence/lib/yit-licence.php');
                require_once(YITH_WCPO_SURVEY_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php');
            }
            YIT_Plugin_Licence()->register(YITH_WCPO_SURVEY_INIT, YITH_WCPO_SURVEY_SECRET_KEY, YITH_WCPO_SURVEY_SLUG);
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if (!class_exists('YIT_Upgrade')) {
                require_once(YITH_WCPO_SURVEY_DIR . 'plugin-fw/lib/yit-upgrade.php');
            }
            YIT_Upgrade()->register(YITH_WCPO_SURVEY_SLUG, YITH_WCPO_SURVEY_INIT);
        }

        /**
         * add Pending Order Survey in YIT Pluglin and add tabs
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_pending_order_survey_menu()
        {

            if (!empty($this->_panel))
                return;

            $admin_tabs = array(
                'general-settings' => __('General Settings', 'yith-woocommerce-pending-order-survey'),
                'pending-survey' => __('Survey', 'yith-woocommerce-pending-order-survey'),
                'pending-email' => __('Email', 'yith-woocommerce-pending-order-survey'),
                'pending-survey-order' => __('Pending Order', 'yith-woocommerce-pending-order-survey'),
                'pending-survey-order-recovered' => __('Recover Order', 'yith-woocommerce-pending-order-survey'),
                'pending-reports' => __('Report', 'yith-woocommerce-pending-order-survey'),
                'privacy-settings' => __( 'Account & Privacy', 'yith-woocommerce-pending-order-survey' )
            );

            $args = array(
                'create_menu_page' => true,
                'parent_slug' => '',
                'plugin_slug' => YITH_WCPO_SURVEY_SLUG,
                'class' => yith_set_wrapper_class(),
                'page_title' => __('Pending Order Survey', 'yith-woocommerce-pending-order-survey'),
                'menu_title' => 'Pending Order Survey',
                'capability' => 'manage_options',
                'parent' => 'yith-woocommerce-pending-order-survey',
                'parent_page' => 'yith_plugin_panel',
                'page' => $this->_panel_page,
                'admin-tabs' => $admin_tabs,
                'options-path' => YITH_WCPO_SURVEY_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if (!class_exists('YIT_Plugin_Panel_WooCommerce')) {
                require_once(YITH_WCPO_SURVEY_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php');
            }

            $this->_panel = new YIT_Plugin_Panel_WooCommerce($args);
        }

        /**
         * show all Pending Order Survey
         * @author YIThemes
         * @since 1.0.0
         */
        public function show_pending_survey_tab()
        {

            wc_get_template('admin/pending-survey-tab.php', array(), '', YITH_WCPO_SURVEY_TEMPLATE_PATH);

        }

        public function  show_pending_survey_email_tab()
        {
            wc_get_template('admin/pending-survey-email-tab.php', array(), '', YITH_WCPO_SURVEY_TEMPLATE_PATH);
        }

        public function  show_pending_order_tab()
        {

            wc_get_template('admin/pending-survey-order-tab.php', array(), '', YITH_WCPO_SURVEY_TEMPLATE_PATH);
        }

        public function show_pending_order_recovered_tab()
        {
            wc_get_template('admin/pending-survey-order-recovered-tab.php', array(), '', YITH_WCPO_SURVEY_TEMPLATE_PATH);
        }

        public function show_pending_report_tab()
        {

            wc_get_template('admin/pending-reports-tab.php', array(), '', YITH_WCPO_SURVEY_TEMPLATE_PATH);
        }

        /**
         * add script and style in admin
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_admin_style_script()
        {

            wp_enqueue_script('ywcpos_admin_script', YITH_WCPO_SURVEY_ASSETS_URL . 'js/ywcpos_admin' . $this->suffix . '.js', array('jquery'), YITH_WCPO_SURVEY_VERSION, true );

            $ywcpos_params = array(
                'admin_url' => admin_url('admin-ajax.php', is_ssl() ? 'https' : 'http'),
                'actions' => array(
                    'add_new_question' => 'add_new_question',
                    'send_pending_email' => 'ajax_send_pending_email'
                ),
                'tinymce' => $this->get_pending_survey_json(),
                'ajax_loader' => YITH_WCPO_SURVEY_ASSETS_URL . 'images/ajax-loader.gif'
            );

            wp_localize_script('ywcpos_admin_script', 'ywcpos_params', $ywcpos_params);

            wp_enqueue_style('ywcpos_admin_style', YITH_WCPO_SURVEY_ASSETS_URL . 'css/ywcpos_admin.css', array(), YITH_WCPO_SURVEY_VERSION);
            wp_enqueue_style('ywcpos_font_awesome', YITH_WCPO_SURVEY_ASSETS_URL . 'css/font-awesome.min.css', array());
        }

        /**
         * add style and script in frontend
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_frontend_style_script()
        {

            global $post;

            if (  !is_null( $post ) && $post instanceof WP_Post && $post->post_type === 'ywcpos_survey') {

                wp_enqueue_script('ywcpos_script', YITH_WCPO_SURVEY_ASSETS_URL . 'js/ywcpos_frontend' . $this->suffix . '.js', array('jquery'), YITH_WCPO_SURVEY_VERSION, true );

                $ywcpos_params = array(
                    'admin_url' => admin_url('admin-ajax.php', is_ssl() ? 'https' : 'http'),
                    'actions' => array(
                        'validate_survey' => 'validate_survey',
                    ),
                    'close_dialog_txt' => __('Close', 'yith-woocommerce-pending-order-survey')
                );

                wp_localize_script('ywcpos_script', 'ywcpos_params', $ywcpos_params);

                wp_enqueue_style('ywcpos_style', YITH_WCPO_SURVEY_ASSETS_URL . 'css/ywcpos_frontend.css', array(), YITH_WCPO_SURVEY_VERSION);
            }
        }

        public function  add_new_question()
        {

            if (isset($_REQUEST['ywcpos_loop'])) {

                $params = array(
                    'loop' => $_REQUEST['ywcpos_loop'],
                    'single_survey' => array()
                );

                $params['params'] = $params;
                ob_start();
                wc_get_template('metaboxes/types/single_pending_survey_question.php', $params, '', YITH_WCPO_SURVEY_TEMPLATE_PATH);
                $template = ob_get_contents();
                ob_end_clean();

                wp_send_json(array('result' => $template));
            }
        }

        /**
         * Add shortcode button
         *
         * Add shortcode button to TinyMCE editor, adding filter on mce_external_plugins
         *
         * @return void
         * @since 1.0.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.it>
         */
        public function add_shortcodes_button()
        {
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
                return;

            if (isset($_GET['post'])) {
                $post_type = get_post_type($_GET['post']);
                if ($post_type != 'ywcpos_survey_email') {
                    return;
                }
            }

            if (isset($_GET['post_type'])) {

                if ($_GET['post_type'] != 'ywcpos_survey_email') {
                    return;
                }
            }

            if (get_user_option('rich_editing') == 'true') {
                add_filter('mce_external_plugins', array($this, 'add_shortcodes_tinymce_plugin'));
                add_filter('mce_buttons', array($this, 'register_shortcodes_button'));
                add_filter('mce_external_languages', array($this, 'add_button_lang'));
            }
        }

        /**
         * Add shortcode plugin
         *
         * Add a script to TinyMCE script list
         *
         * @param $plugin_array array() Array containing TinyMCE script list
         *
         * @return array() The edited array containing TinyMCE script list
         * @since 1.0.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.it>
         */
        public function add_shortcodes_tinymce_plugin($plugin_array)
        {
            $plugin_array['ywcpos_shortcode'] = YITH_WCPO_SURVEY_ASSETS_URL . 'js/tinymce'.$this->suffix.'.js';
            return $plugin_array;
        }

        /**
         * Register shortcode button
         *
         * Make TinyMCE know a new button was included in its toolbar
         *
         * @param $buttons array() Array containing buttons list for TinyMCE toolbar
         *
         * @return array() The edited array containing buttons list for TinyMCE toolbar
         * @since 1.0.0
         * @author Antonio La Rocca <antonio.larocca@yithemes.it>
         */
        public function register_shortcodes_button($buttons)
        {
            array_push($buttons, "|", "ywcpos_shortcode");
            return $buttons;
        }

        /**
         * Add multilingual to mce button from filter mce_external_languages
         *
         * @return   array
         * @since    1.0
         * @author   Emanuela Castorina
         */
        function add_button_lang($locales)
        {
            $locales ['ywcpos_shortcode'] = YITH_WCPO_SURVEY_INC . 'admin/tinymce/tinymce-plugin-langs.php';
            return $locales;
        }


        /**
         * The markup of shortcode
         *
         * @since   1.0.0
         *
         * @param   $context
         *
         * @return  mixed
         * @author  Alberto Ruggiero
         */
        public function ywcpos_media_buttons_context($context)
        {

            global $post_ID, $temp_ID;


            $post_id = 0 == $post_ID ? $temp_ID : $post_ID;
            $post_type = get_post_type($post_id);

            if ($post_type != $this->pending_order_survey_email->post_type_name)
                return $context;

            $query_args = array(
                'post_id' => $post_id,
                'action' => 'print_shortcode_popup',
                'KeepThis' => true,
                'TB_iframe' => true,

            );
            $lightbox_url = esc_url(add_query_arg($query_args, admin_url('admin.php')));

            $out = sprintf('<a id="ywcpos_shortcode" style="display:none" href="%s" class="hide-if-no-js thickbox" title="%s"></a>', $lightbox_url,
                __('Add new field', 'yith-woocommerce-pending-order-survey'));

            return $context . $out;

        }

        public function print_shortcode_popup()
        {

            require_once(YITH_WCPO_SURVEY_DIR . '/templates/admin/lightbox.php');
        }

        /**
         * Add quicktags to visual editor
         *
         * @since   1.0.0
         * @return  void
         * @author  Alberto Ruggiero
         */
        public function ywcpos_add_quicktags()
        {

            global $post;
            if (isset($post) && $post->post_type == $this->pending_order_survey_email->post_type_name) {
                ?>
                <script type="text/javascript">

                    if (window.QTags !== undefined) {
                        QTags.addButton('ywcpos_shortcode', 'add field in email', function () {
                            jQuery('#ywcpos_shortcode').click()
                        });
                    }
                </script>
                <?php
            }
        }

        public function get_pending_survey_json()
        {

            $args = array(
                'post_type' => 'ywcpos_survey',
                'post_status' => 'publish'
            );
            $all_survey = get_posts($args);

            $menu = array();

            foreach ($all_survey as $survey) {

                $title = $survey->post_title;
                $item = array('text' => $title, 'value' => '{{ywcpos_pending_survey=' . $survey->ID . '}}');

                $menu[] = $item;
            }

            return $menu;
        }



        /**
         * update meta for yith pending orders
         * @author YIThemes
         * @since 1.0.0
         * @param $order_id
         * @param $old_status
         * @param $new_status
         */
        public function remove_order_pending_meta($order_id, $old_status, $new_status)
        {

        	$is_pending_survey_order = get_post_meta( $order_id, '_ywcpos_is_pending', true );

        	if( 'yes' === $is_pending_survey_order ){

	            if ('pending' === $old_status && isset($_REQUEST['_ywcpos_order']) && wp_verify_nonce($_REQUEST['_ywcpos_order'], 'ywcpos_pay_for_order')) {

	                $email_id = $_REQUEST['email_id'];

	                update_post_meta($order_id, '_ywcpos_user_email_click_in_link_order', $email_id);


	            }
	            if( 'completed'=== $new_status ){

	               ywcpos_update_counter('ywcpos_count_order_rec');
	               $email_id = get_post_meta($order_id, '_ywcpos_user_email_click_in_link_order', true);

	              	if( !empty( $email_id ) )
	                     ywcpos_update_counter_meta($email_id, '_ywcpos_email_order_rec');

	                }
	            }
        }

        /**
         * Filters woocommerce available emails
         *
         * @param $emails array
         *
         * @return array
         * @since 1.0
         */
        public function add_woocommerce_emails($emails)
        {

            $emails['YITH_WC_Send_Pending_Order_Email'] = include(YITH_WCPO_SURVEY_INC . 'emails/class.yith-wcpos-pending-order-email.php');
            $emails['YITH_WC_Send_Pending_Order_Thanks_Email'] = include(YITH_WCPO_SURVEY_INC . 'emails/class.yith-wcpos-pending-order-thanks-email.php');

            return $emails;
        }

        /**
         * Loads WC Mailer when needed
         *
         * @return void
         * @since 1.0
         */
        public function load_wcpos_mailer()
        {
            add_action('send_wcpos_mail', array('WC_Emails', 'send_transactional_email'), 10);
            add_action('send_thanks_email', array('WC_Emails', 'send_transactional_email'), 10);
        }

        public function update_woocommerce_hold_stock_minutes_option()
        {

            $old_wc_value = get_option('_ywcps_old_wc_value', '');

            if ($old_wc_value == '') {
                $old_wc_value = get_option('woocommerce_hold_stock_minutes');

                update_option('_ywcps_old_wc_value', $old_wc_value);
            }

            $unit = get_option('ywcpos_pending_from_cancelled_unit');
            $value = get_option('ywcpos_pending_from_cancelled_value');

            if ('' !== $value) {
                switch ($unit) {

                    case 'hours':

                        $value = 60 * $value;
                        break;
                    case 'days':
                        $value = 24 * 60 * $value;
                        break;
                }
            }

            $value = apply_filters('woocommerce_admin_settings_sanitize_option_woocommerce_hold_stock_minutes', '', '', $value);
            update_option('woocommerce_hold_stock_minutes', $value);
        }

        public function reinitialize_check_pending_order(){

        	$held_duration = get_option( 'ywcpos_include_pending_from' );
        	wp_clear_scheduled_hook( 'ywpos_check_pending_order' );

        	if( ''!== $held_duration ){
        	  	wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 ), 'ywpos_check_pending_order' );
        	}

        }
        /**
         *
         * check if survey is valid and save the users answer
         * @author YIThemes
         * @since 1.0.0
         */
        public function validate_survey()
        {

            if (isset($_REQUEST['answers']) && !empty($_REQUEST['answers'])) {

                if((isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])) && (isset($_REQUEST['survey_id']) && !empty($_REQUEST['survey_id']))) {

                    $order_id = $_REQUEST['order_id'];
                    $answers = $_REQUEST['answers'];
                    $survey_id = $_REQUEST['survey_id'];
                    $survey_title = get_the_title($survey_id);
                    $survey = array('title' => $survey_title, 'questions' => array());
                    $single_question = array();

                    foreach ($answers as $key => $answer) {

                        $single_question[$answer['survey_name']] = $answer['survey_answer'];
                    }

                    //update survey meta

                    $all_survey_answers = get_post_meta($survey_id, '_ywcpos_all_answers', true);

                    if( empty( $all_survey_answers) ){
                        $all_survey_answers = array();
                    }

                    foreach ($single_question as $key => $answ) {

                        if (!empty($answ)) {

                            $current_question = isset($all_survey_answers[$key]) ? $all_survey_answers[$key] : array();
                            $current_question[] = $answ;
                            $all_survey_answers[$key] = $current_question;
                        }
                    }

                    update_post_meta($survey_id, '_ywcpos_all_answers', $all_survey_answers);

                    //update counter of answers
                    ywcpos_update_counter_meta($survey_id, '_ywcpos_tot_answ');

                    //register for this surveys the associate orders
                    $order_ids = get_post_meta($survey_id, '_ywcpos_orders', true);
                    if( empty( $order_ids)){
                        $order_ids = array();
                    }
                    $order_ids[] = $order_id;
                    update_post_meta($survey_id, '_ywcpos_orders', $order_ids);

                    $email_params = array(
                        'survey' => $survey['title'],
                        'order_id' => $order_id
                    );
                    do_action('send_thanks_email', $email_params);

                }
            }
            wp_send_json(array('result' => 'ok'));
        }

        public function show_checkbox(){

            wc_get_template( 'ywcpos-term.php', array(), '', YITH_WCPO_SURVEY_TEMPLATE_PATH.'checkout/' );
        }

	    /**
	     * @param WC_Order $order
	     */
        public function register_customer_choose( $order ){

            if( !isset( $_REQUEST['ywcpos_term'] ) ){

                yit_set_prop( $order, '_ywcpos_not_send', 'yes' );
            }
        }

        public function load_privacy(){
	        require_once( YITH_WCPO_SURVEY_INC.'classes/class.yith-wcpos-privacy-policy.php' );
        }

    }
}