<?php

/**
 * By Hassan Jahangiri (http://wpwave.com)
 * Mainly from weDevs Settings API wrapper class by Tareq Hasan <tareq@weDevs.com> (http://tareq.weDevs.com)
 */
class PP_Settings_API {

    /**
     * settings sections array
     *
     * @var array
     */
    private $settings_sections = array();

    /**
     * Settings fields array
     *
     * @var array
     */
    private $settings_fields = array();

    /**
     * Settings fields array
     *
     * @var array
     */
    private $settings_menu = array();

    /**
     * Singleton instance
     *
     * @var object
     */
    private static $_instance;

    public function __construct($fields, $sections, $menu = '') {
        //set sections and fields
        //if (!is_admin())
        //    return;

        $this->set_sections($sections);
        $this->set_fields($fields);

        if ($menu) {
            $this->set_menu($menu);

            if ($menu['multisite_only'])
                add_action('network_admin_menu', array(&$this, 'register_menu'));
            else
                add_action('admin_menu', array(&$this, 'register_menu'));
        }


        add_action('init', array(&$this, 'filter_settings'));

        //$this->admin_init();
        //$this->register_menu();

        if ($this->settings_menu['action_link'])
            add_filter('plugin_action_links_' . $this->settings_menu['plugin_file'], array(&$this, 'plugin_actions_links'), -10);

        add_action('admin_init', array(&$this, 'admin_init'));

        add_action('wp_ajax_nopriv_hmwp_get_ids_data', array($this,'hmwp_get_ids_data'));
        add_action('wp_ajax_hmwp_get_ids_data', array($this,'hmwp_get_ids_data'));

        add_action('wp_ajax_nopriv_hmwp_remove_dashboard_notice', array($this,'hmwp_remove_dashboard_notice'));
        add_action('wp_ajax_hmwp_remove_dashboard_notice', array($this,'hmwp_remove_dashboard_notice'));
    }

    /**
     * @version 6.0
     */
    public function hmwp_remove_dashboard_notice(){
        update_option('hmwp_remove_dashboard_notice_opt',1);
        echo 'success';
        exit;
    }

    /**
     * @version 6.0
     */
    public function hmwp_get_ids_data(){
        $call = $_POST['call_type'];
        $attack_type = $_POST['attack_type'];
        $sale_data = array();
        global $wpdb;
        $table_name = $wpdb->prefix . 'hmwp_ms_intrusions';
        if($attack_type == 'IPS'){
            $table_name = $wpdb->prefix . 'hmwp_blocked_ips';
        }
        if($call == 'this month'){
            $current_date = date('d');
            $all_date = date('t');
            $current_my = date(' m d');
            for($i=01; $i <= $current_date; $i++){
                $key = date('Y-m-' . $i);
                $key_1 = date( str_pad($i, 2, '0', STR_PAD_LEFT) . ' M Y');
                $data_r = $wpdb->get_row("SELECT COUNT(*) cnt FROM " . $table_name . " WHERE date(created) = '". $key ."'");
                $sale_data[ $key_1 ] = $data_r->cnt;
            }
            $main_arr = array();
            if($sale_data){
                foreach ($sale_data as $key => $value) {
                    $main_arr[] = array(
                        'x' => str_replace("-", ", ", $key),
                        'y' => $value
                    );
                }
            }
            echo json_encode($main_arr);
        } else if($call == 'last month'){
            $first = date("j", strtotime("first day of previous month"));
            $last = date("j", strtotime("last day of previous month"));
            $last_month = date("n", strtotime("last day of previous month"));
            $last_month_name = date('F', strtotime('last month'));
            $this_year = date('Y');
            for($i=$first; $i <= $last; $i++){
                $key = date('Y-'. $last_month .'-' . $i);
                $key_1 = str_pad($i, 2, '0', STR_PAD_LEFT) . ' '. $last_month_name . ' ' . $this_year;
                $data_r = $wpdb->get_row("SELECT COUNT(*) cnt FROM " . $table_name . " WHERE date(created) = '". $key ."'");
                $sale_data[ $key_1 ] = $data_r->cnt;
            }
            $main_arr = array();
            if($sale_data){
                foreach ($sale_data as $key => $value) {
                    $main_arr[] = array(
                        'x' => str_replace("-", ", ", $key),
                        'y' => $value
                    );
                }
            }
            echo json_encode($main_arr);
        } else if($call == 'this year'){
            $this_year = date('Y');
            for($i=01; $i <= 12; $i++){
                $dateObj   = DateTime::createFromFormat('!m', $i);
                $monthName = $dateObj->format('F');
                $key = $this_year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $key_1 = $monthName . ' ' . $this_year;
                $data_r = $wpdb->get_row("SELECT COUNT(*) cnt FROM " . $table_name . " WHERE created LIKE '%" . $key . "%'" );
                $sale_data[ $key_1 ] = $data_r->cnt;
            }
            $main_arr = array();
            if($sale_data){
                foreach ($sale_data as $key => $value) {
                    $main_arr[] = array(
                        'x' => str_replace("-", ", ", $key),
                        'y' => $value
                    );
                }
            }
            echo json_encode($main_arr);
        } else{
            $now = new DateTime( "6 days ago", new DateTimeZone('America/New_York'));
            $interval = new DateInterval( 'P1D'); // 1 Day interval
            $period = new DatePeriod( $now, $interval, 6); // 7 Days
            $sale_data = array();
            foreach( $period as $day) {
                $key = $day->format( 'd M Y');
                $key_1 = $day->format( 'Y-m-d');
                $data_r = $wpdb->get_row("SELECT COUNT(*) cnt FROM " . $table_name . " WHERE date(created) = '". $key_1 ."'");
                $sale_data[ $key ] = $data_r->cnt;
            }
            $main_arr = array();
            if($sale_data){
                foreach ($sale_data as $key => $value) {
                    $main_arr[] = array(
                        'x' => str_replace("-", ", ", $key),
                        'y' => $value
                    );
                }
            }
            echo json_encode($main_arr);
        }
        exit;
    }

    public function register_menu() {
        $role = ($this->settings_menu['role']) ? $this->settings_menu['role'] : 'manage_options';
        if ($this->settings_menu['multisite_only']) {
            add_submenu_page('settings.php', $this->settings_menu['title'], $this->settings_menu['title'], $role, $this->settings_menu['name'], array(&$this, 'render_option_page'));
        } else {
            $hmwp_options = get_option('hide_my_wp');
            if (!$hmwp_options['enable_ids']) {
                include_once('mute-screamer/mute-screamer.php');
            }
            $intrusion_count = (int) HMWP_MS_IDS::instance()->opt( 'new_intrusions_count' );
            $intrusions_menu_title = sprintf( __( 'Intrusions %s', 'mute-screamer' ), "<span class='update-plugins count-$intrusion_count' title='$intrusion_count'><span class='update-count'>" . number_format_i18n( $intrusion_count ) . '</span></span>' );
            add_menu_page(__('Dashboard', 'hide_my_wp'), __('Hide My WP', 'hide_my_wp'), $role, 'hide_my_wp_dashboard', array(&$this, 'render_dashboard_page'), HMW_URL . '/img/hmwp-logo-t.png', 60);
            add_submenu_page('hide_my_wp_dashboard', __('Dashboard', 'hide_my_wp'), __('Dashboard', 'hide_my_wp'), $role, 'hide_my_wp_dashboard', array(&$this, 'render_dashboard_page'));
            add_submenu_page('hide_my_wp_dashboard', __( 'IDS', 'mute-screamer' ), $intrusions_menu_title, 'activate_plugins', 'hmwp_ms_intrusions', array($this,'render_ids_page'));
            add_submenu_page('hide_my_wp_dashboard', __( 'Blocked IPs', 'hide_my_wp' ), __('Blocked IPs', 'hide_my_wp'), 'activate_plugins', 'hmwp_blocked_ips', array($this,'hmwp_render_blocked_ips_page'));
            add_submenu_page('hide_my_wp_dashboard', __('Settings', 'hide_my_wp'), __('Settings', 'hide_my_wp'), $role, $this->settings_menu['name'], array(&$this, 'render_option_page'));
            add_submenu_page('hide_my_wp_dashboard', __('<span style="color:#f56e28">Feedback</span>', 'hide_my_wp'), __('<span style="color:#f56e28">Feedback</span>', 'hide_my_wp'), $role, 'hmwp_custom_feedback_form', array(&$this, 'hmwp_custom_feedback_form'));
            add_submenu_page(NULL, __('Setup Wizard', 'hide_my_wp'), __('Setup Wizard', 'hide_my_wp'), 'activate_plugins', 'hmwp_setup_wizard', array(&$this, 'hmwp_show_setup_wizard'));
            //add_options_page($this->settings_menu['title'],  $this->settings_menu['title'], $role, $this->settings_menu['name'], array(&$this, 'render_option_page'));
        }
    }
    
    /**
     * @version 6.0
     * redirect to google form
     */
    public function hmwp_custom_feedback_form(){ ?>
        <script>
            window.location.href = "https://wpwave.public.makerkit.co/";
        </script>
    <?php    
    }

    /**
     * @version 6.0
     * Show blocked ip list
     */
    public function hmwp_render_blocked_ips_page(){
        global $wpdb;
		$Blocked_IPs_Table = new HMWP_blocked_ips_Table();
		$search_key = (isset($_REQUEST['s']) ? $_REQUEST['s'] : '');
		$Blocked_IPs_Table->prepare_items($search_key);
		$message = false;
		$whitelisted = isset( $_GET['whitelisted'] ) ? (int) $_GET['whitelisted'] : 0;
		$banned = isset( $_GET['banned'] ) ? (int) $_GET['banned'] : 0;
		$deleted = isset( $_GET['deleted'] ) ? (int) $_GET['deleted'] : 0;
		if ($whitelisted) {
			$message = sprintf(_n('IP added to the whitelist.', '%s IPs added to the whitelist.', $whitelisted, 'hide_my_wp'), number_format_i18n($whitelisted));
		}
		if ($banned) {
			$message = sprintf(_n('IP added to the block list.', '%s IPs added to the block list.', $banned, 'hide_my_wp'), number_format_i18n($banned));
		}
		if ($deleted) {
			$message = sprintf(_n('Item permanently deleted.', '%s items permanently deleted.', $deleted, 'hide_my_wp'), number_format_i18n($deleted));
		}
		if ($message) {
			?><div class="notice notice-success is-dismissible"><p><?php echo $message;?></p></div><?php
		}
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e('HMWP Blocked IP Addresses', 'hide_my_wp'); ?></h1>
			<hr class="wp-header-end">
			<?php $Blocked_IPs_Table->views(); ?>
			<form method="get" action="admin.php" class="hmwp_blocked_ips_form">
				<input type="hidden" value="hmwp_blocked_ips" name="page"/>
				<?php
				$Blocked_IPs_Table->search_box('search', 'search_id');
				$Blocked_IPs_Table->display();
				?>
			</form>
			<style type="text/css">.hmwp_blocked_ips_form table tr.whitelisted th, .hmwp_blocked_ips_form table tr.whitelisted td{background-color: #ddfcdd;color: #333;}</style>
		</div>
		<?php
	}

    /**
     * @version 1.0
     * Display Setup wizard
     */
    public function hmwp_show_setup_wizard(){
        ?>
        <div class="wrap">
            <h1><?php _e('Setup Wizard','hide_my_wp'); ?></h1>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                   jQuery('.setup-element-wrap').click(function(e){
                        e.preventDefault();
                        jQuery('.setup-element-wrap').removeClass('setup-selected');
                        jQuery('.setup-element-wrap').find('input[type="radio"]').prop("checked", false);
                        jQuery(this).addClass('setup-selected');
                        jQuery(this).find('input[type="radio"]').prop("checked", true);
                        jQuery('.setup-next-step').trigger('click');
                   });
                   jQuery('.setup-next-step').click(function(e){
                       e.preventDefault();
                       jQuery('.setup-main-wrapper').hide();
                       jQuery('.setup-purchase-code').show();
                       jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                   });
                });
            </script>
            <style type="text/css">
                .setup-element-wrap{
                    border: 2px solid #fff;
                    border-radius: 5px;
                    padding: 0 20px 0 20px;
                    display: inline-block;
                    width: 97%;
                    clear: both;
                    margin-top: 10px;
                    position: relative;
                    cursor: pointer;
                    transition: 0.2s all;
                    -o-transition: 0.2s all;
                    -webkit-transition: 0.2s all;
                    -ms-transition: 0.2s all;
                }
                .setup-purchase-code{
                    border: 2px solid #fff;
                    border-radius: 5px;
                    padding: 0 20px 20px 20px;
                    margin-top: 50px;
                }
                .setup-purchase-code input[type="text"] {
                    width: 500px;
                    max-width: 100%;
                }
                .setup-element-wrap:hover,
                .setup-selected{
                    background: #d6f9cb;
                    border-color: #d6f9cb;
                }
                .setup-element-wrap .setup-settings-wrap .enable-ele{
                    background: #0073aa;
                    color: #fff;
                    border-radius: 5px;
                    padding: 5px 10px 5px 5px;
                    font-size: 12px;
                    display: inline-block;
                    margin: 0 5px 7px 0;
                }

                .setup-element-wrap .setup-settings-wrap .disable-ele{
                    background: red;
                    color: #fff;
                    border-radius: 5px;
                    padding: 5px 10px 5px 5px;
                    font-size: 12px;
                    display: inline-block;
                    margin: 0 5px 7px 0;
                }

                .setup-element-wrap .setup-settings-wrap{
                    width: 60%;
                    float: left;
                    border-right: 3px solid #fff;
                    margin-top: 5px;
                    margin-bottom: 5px;
                    padding-right: 15px;
                    box-sizing: border-box;
                }
                .setup-element-wrap .setup-dots{
                    width: 25%;
                    float: left;
                    margin-top: 5px;
                    padding-left: 30px;
                    display: inline-block;
                    box-sizing: border-box;
                }
                .setup-element-wrap .setup-dots ul li{
                    list-style-type: circle;
                    font-size: 15px;
                    font-weight: 500;
                }
                .skip-button-div-wrap{
                    margin-top: 20px;
                    text-align: right;
                }
                .setup-element-wrap .setup-select-button{
                    position: absolute;
                    bottom: 10px;
                    right: 10px;
                }
            </style>
            <?php
			$pre_made_settings = HideMyWP::pre_made_settings();
            //Get old setting
            $hmwp_settings = get_option($this->settings_menu['name']);
            $admin_email = get_option('admin_email');
            
            if ( isset($_POST['hmwp_setup_nonce']) && wp_verify_nonce($_POST['hmwp_setup_nonce'], 'hmwp_setup_setting') ){
                $options_file = (is_multisite()) ? 'network/settings.php' : 'admin.php';
                $page_url = admin_url(add_query_arg('page', $this->settings_menu['name'], $options_file));
                if (isset($_POST['setup-purchase-code']) && $_POST['setup-purchase-code'] && (strlen($_POST['setup-purchase-code']) <= 34 || strlen($_POST['setup-purchase-code']) > 42)) {
                    $goback = add_query_arg(array('wrong-number' => 'true'), $page_url);
                    wp_redirect($goback);
                    exit;
                }


                $new_settings = stripslashes($_POST['setup-eb-setting']);
                $new_settings = json_decode($new_settings, true);
                $new_settings = str_replace('[new_line]', "\r\n", $new_settings);
                $new_settings = str_replace('[double_slashes]', "\/", $new_settings);
                $new_settings = str_replace('[quotation]', '"', $new_settings);
                $new_settings = str_replace('[o_cb]', '{', $new_settings);
                $new_settings = str_replace('[c_cb]', '}', $new_settings);
				
				if (!is_multisite()) {
					global $wp_rewrite;
					$is_permalink = HideMyWP::is_permalink();
					if (!$is_permalink && empty($new_settings['post_base'])) {
						$new_settings['post_base'] = '/%category%/%postname%/';
					}
					$wp_rewrite->set_permalink_structure(trim($new_settings['post_base'], ' '));
					$wp_rewrite->set_category_base(trim($new_settings['category_base'], '/ '));
					$wp_rewrite->set_tag_base(trim($new_settings['tag_base'], '/ '));
				}

                if (isset($new_settings['li']) && !trim($new_settings['li']) && $_POST['setup-purchase-code'])
                    $new_settings['li'] = $_POST['setup-purchase-code'];

                update_option($this->settings_menu['name'], $new_settings);
                update_option('hmwp_setup_run', 'yes');
                $goback = add_query_arg(array('settings-imported' => 'true'), $page_url);
                wp_redirect($goback);
                exit;
            }
            ?>
            <form method="POST">
                <div class="setup-main-wrapper">
                    <div class="setup-element-wrap">
                        <div class="setup-settings-wrap">
                            <h2>Light Privacy Settings</h2>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Basic Anti Detection </span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Basic Cleanup </span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Hide wp-login.php </span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Antispam </span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Hides Themes </span>
                        </div>
                        <div class="setup-dots">
                            <ul>
                                <li>Enough for simple sites</li>
                                <li>Minimal changes to the site</li>
                            </ul>
                        </div>
                        <input style="display: none;" type="radio" name="setup-eb-setting" value='<?php echo $pre_made_settings['low_privacy'];?>' />
                        <a href="#" class="setup-select-button button button-primary">Select Setting</a>
                    </div>
                    <div class="setup-element-wrap">
                        <div class="setup-settings-wrap">
                            <h2>Medium Privacy Settings</h2>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Deep Anti Detection </span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Intrusion Detection</span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Rename Plugins</span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Trust Network</span>
                            
                        </div>
                        <div class="setup-dots">
                            <ul>
                                <li>Best for medium complexity sites</li>
                                <li style="color:green">Recommended for most sites.</li>
                            </ul>
                        </div>
                        <input style="display: none;" type="radio" name="setup-eb-setting" value='<?php echo $pre_made_settings['medium_privacy'];?>' />
                        <a href="#" class="setup-select-button button button-primary">Select Setting</a>
                    </div>
                    <div class="setup-element-wrap">
                        <div class="setup-settings-wrap">
                            <h2>High Privacy Settings</h2>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Full Hide</span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Disable Direct File Access</span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Hide Admin Bar</span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Hide Popular Plugins</span>
                            <span class="enable-ele"><span class="dashicons dashicons-yes"></span> Hide Popular Detectors</span>
                        </div>
                        <div class="setup-dots">
                            <ul>
                                <li>Hides everything!</li>
                                <li>Intrusion detection</li>
                                <li>Provides most privacy and protection</li>
                            </ul>
                        </div>
                        <input style="display: none;" type="radio" name="setup-eb-setting" value='<?php echo $pre_made_settings['high_privacy'];?>' />
                        <a href="#" class="setup-select-button button button-primary">Select Setting</a>
                    </div>
                    <div class="skip-button-div-wrap">
                        <a class="button" href="<?php echo home_url() . '/wp-admin/admin.php?page=hide_my_wp'; ?>">Skip & Go to Settings</a>
                        <a href='#' class="button button-primary setup-next-step">Next Step</a>
                    </div>
                </div>
                <div class="setup-purchase-code" style="display: none;">
                    <h2>Purchase Code</h2>
                    <input type="text" value="<?php echo isset($hmwp_settings['li']) ? $hmwp_settings['li'] : ''; ?>" name="setup-purchase-code" placeholder="Enter purchase code" />
                    <div class="skip-button-div-wrap">
                        <?php wp_nonce_field( 'hmwp_setup_setting', 'hmwp_setup_nonce' ); ?>
                        <button name="submit-setup-code" class="button button-primary">Finish Setup</button>
                    </div>
                </div>
            </form>
        </div>
    <?php
    }

    /**
     * @version 6.0
     * Display IDS Menu
     */
    public function render_ids_page(){
        $hmadmin = new HMWP_MS_Admin();
        $hmadmin->intrusions();
    }

    public function filter_settings() {
        $options_file = (is_multisite()) ? 'network/settings.php' : 'admin.php';
        $page_url = admin_url(add_query_arg('page', $this->settings_menu['name'], $options_file));

        $can_deactive = false;
        if (isset($_COOKIE['hmwp_can_deactivate']) && preg_replace("/[^a-zA-Z]/", "", substr(NONCE_SALT, 0, 8)) == preg_replace("/[^a-zA-Z]/", "", $_COOKIE['hmwp_can_deactivate']))
            $can_deactive = true;

        if ($can_deactive && is_admin() && isset($_POST['action']) && $_POST['action'] == 'update' && isset($_POST['option_page']) && $_POST['option_page'] == $this->settings_menu['name']) {

            //to fix problem with default on checkbox
            $def_keys = array_keys($this->get_defaults());
            if (is_array($def_keys))
                foreach ($def_keys as $key)
                    if (!isset($_POST[$this->settings_menu['name']][$key]))
                        $_POST[$this->settings_menu['name']][$key] = '';

            $_POST = apply_filters('pp_settings_api_filter', $_POST);
            if (isset($_POST[$this->settings_menu['name']]['li']) && $_POST[$this->settings_menu['name']]['li'] && (strlen($_POST[$this->settings_menu['name']]['li']) <= 34 || strlen($_POST[$this->settings_menu['name']]['li']) > 42)) {
                $goback = add_query_arg(array('wrong-number' => 'true'), $page_url);
                wp_redirect($goback);
                exit;
            }

            if (isset($_POST['import_field']) && $_POST['import_field'] && check_admin_referer($this->settings_menu['name'] . '-options')) {
                //delete_option( $this->settings_menu['name'] );
                $new_settings = stripslashes($_POST['import_field']);
                $new_settings = json_decode($new_settings, true);
                $new_settings = str_replace('[new_line]', "\r\n", $new_settings);
                $new_settings = str_replace('[double_slashes]', "\/", $new_settings);
                $new_settings = str_replace('[quotation]', '"', $new_settings);
                $new_settings = str_replace('[o_cb]', '{', $new_settings);
                $new_settings = str_replace('[c_cb]', '}', $new_settings);
				
				if (!is_multisite()) {
					global $wp_rewrite;
					$wp_rewrite->set_permalink_structure(trim($new_settings['post_base'], ' '));
					$wp_rewrite->set_category_base(trim($new_settings['category_base'], '/ '));
					$wp_rewrite->set_tag_base(trim($new_settings['tag_base'], '/ '));
				}

				if (isset($new_settings['li']) && !trim($new_settings['li']) && $_POST[$this->settings_menu['name']]['li'])
                    $new_settings['li'] = $_POST[$this->settings_menu['name']]['li'];

                update_option($this->settings_menu['name'], $new_settings);

                // 	if ( !count( get_settings_errors() ) )
                //	add_settings_error('general', 'settings_imported', __('Settings Imported.'), 'updated');
                //	set_transient('settings_errors', get_settings_errors(), 30);

                /**
                 * Redirect back to the settings page that was submitted
                 */
                $goback = add_query_arg(array('settings-imported' => 'true'), $page_url);
                wp_redirect($goback);
                exit;
            }



            //check to see if the options were reset
            if (isset($_POST['reset-defaults']) && check_admin_referer($this->settings_menu['name'] . '-options')) {

                // foreach ($this->settings_sections as $section)
                delete_option($this->settings_menu['name']);
                $this->update_defaults();

                do_action('pp_settings_api_reset');

                $goback = add_query_arg(array('settings-reseted' => 'true'), $page_url);
                wp_redirect($goback);
                exit;
            }


            $clean_options_page = remove_query_arg(array('settings-reseted', 'wrong-number', 'settings-imported'), $page_url);

            $_SERVER['HTTP_REFERER'] = $clean_options_page;
            $_REQUEST['_wp_original_http_referer'] = $clean_options_page;
            $_REQUEST['_wp_http_referer'] = $clean_options_page;
        }
    }

    /**
     * Display the plugin settings options page
     */
    public function render_option_page() {

        if ($this->settings_menu['template_file']) {
            include_once($this->settings_menu['template_file']);
        } else {
            echo '<div class="wrap settings_api_class_page" id="' . $this->settings_menu['name'] . '_settings" >';
            $icon = '';
            if (isset($this->settings_menu['icon_path']) && $this->settings_menu['icon_path'])
                $icon = ' style="background: url(' . $this->settings_menu['icon_path'] . ') no-repeat ;" ';

            echo '<div id="icon-options-general" class="icon32" ' . $icon . '><br /></div>';

            echo '<h2>' . $this->settings_menu['title'] . '</h2>';

            //echo '<br />';
            //settings_errors();
            if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true')
                echo '<div class="updated fade"><p><strong>' . __('Settings was updated successfully!', $this->settings_menu['name']) . '</p></strong></div>';

            if (isset($_GET['settings-reseted']) && $_GET['settings-reseted'])
                echo '<div class="updated fade"><p><strong>' . __('Settings was rested successfully!', $this->settings_menu['name']) . '</p></strong></div>';

            if (isset($_GET['settings-imported']) && $_GET['settings-imported'])
                echo '<div class="updated fade"><p><strong>' . __('Settings was imported successfully!', $this->settings_menu['name']) . '</p></strong></div>';

            if (isset($_GET['wrong-number']) && $_GET['wrong-number'])
                echo '<div class="error fade"><p><strong>' . __('Purchase code is invalid :-|<br> Read <a target="_blank" href="http://wpwave.com/envato/purchase_code_1200.png">Help</a> or check out <a href="https://codecanyon.net/item/hide-my-wp-amazing-security-plugin-for-wordpress/4177158?ref=wpwave" target="_blank">Plugin page</a>.', $this->settings_menu['name']) . '</strong></p></div>';

            do_action('pp_settings_api_header', $this->settings_menu);


            $this->show_navigation();
            $this->show_forms();

            do_action('pp_settings_api_footer', $this->settings_menu);

            echo '</div>';
        }
    }

    /**
     * Display the dashboard page
     */
    public function render_dashboard_page() {
        global $wpdb;
        $setting_name = $this->settings_menu['name'];
        $settings = get_option($setting_name);
        $count = 0;
        $htaccess = $api_msg = $tn_msg = '';        
        if(isset($settings['full_hide']) && $settings['full_hide'] !== 'on'){
            $api_msg = 'Your full hide is disable, Wapplyzer will detect you are using WordPress.';
            $count = $count + 1;
        }
        if(isset($settings['enable_ids']) && $settings['enable_ids'] !== 'on'){
            $tn_msg = 'IDS is disabled, we can not track dangerous IPS.';
            $count = $count + 1;
        }

        if(isset($settings['trust_network']) && $settings['trust_network'] !== 'on') {
            $tn_msg = 'Trust network is disabled, we can not fetch dangerous IPS.';
            $count = $count + 1;
        }
        
        $filename = ABSPATH . '.htaccess';
        if(!is_writable($filename)){
            $htaccess = 'Plugin is not able to write .htaccess file, Please make it writable.';
            $count = $count + 1;
        }
        ?>
        <style>
            .hmwp-loader{
                background-image: url('<?php echo home_url() . '/wp-admin/images/spinner-2x.gif'; ?>');
            }
            .hmwp-loader{
                position: absolute;
                background-color: rgba(255,255,255,0.5);
                top: 0px;
                left: 2%;
                right: 0;
                bottom: 0;
                width: 96%;
                background-repeat: no-repeat;
                background-position: center center;
                display: none;
            }
            .show-fixes-div p:empty{
                display: none;
            }
            .show-fixes-div p{
                padding: 0 0 20px 0;
                margin: 0;
                font-size: 16px;
                color: red;
            }
            .hndle.ui-sortable-handle{
                cursor: auto !important;
            }
            #dashboard-widgets-wrap{
                margin: 0 !important;
            }
            #dashboard_last10_instru .table.widefat{
                table-layout: fixed;
            }
        </style>
        <script type="text/javascript" src="https://www.chartjs.org/dist/2.8.0/Chart.min.js"></script>
        <div class="js">
            <div class="wrap">
                <h1><?php _e('Dashboard', 'hide_my_wp'); ?></h1>
                <?php                
                $hide_notice = get_option('hmwp_remove_dashboard_notice_opt');
                if($count !== 0 && $hide_notice != 1){ ?>
                <div id="welcome-panel" class="welcome-panel">
                    <div class="welcome-panel-content">
                        <h2 style="display: none;"><?php _e('Welcome to HMWP!', 'hide_my_wp'); ?></h2>
                        <?php if($count == 0){ ?>
                        <?php } else { ?>
                            <p class="about-description">Your website may be visible to hackers and detectors. Click on "Show Fixes" to fix <?php echo $count; ?> issues we have detected.</p>
                        <?php } ?>
                        <div class="welcome-panel-column-container">
                                <div class="welcome-panel-column" style="width: 100%; margin-bottom: 30px;">
                                    <?php if($count !== 0){ ?>
                                        <a class="button button-primary button-hero load-customize hide-if-no-customize show-fixes-button" href="#">Show Fixes</a>
                                    <?php } ?>
                                </div>
                            <div class="show-fixes-div" style="display: none;clear: both;">
                                <p><?php echo $api_msg !='' ? '- ' . $api_msg : ''; ?></p>
                                <p><?php echo $htaccess != '' ? '- ' . $htaccess : ''; ?></p>
                                <p><?php echo $tn_msg != '' ? '- ' . $tn_msg : ''; ?></p>
                            </div>
                        </div>
                        <button type="button" class="notice-dismiss hmwp-dash-notice"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>
                </div>
                <?php } ?>
                
                <?php
                //Weekly ids data
                $now = new DateTime( "6 days ago", new DateTimeZone('America/New_York'));
                $interval = new DateInterval( 'P1D'); // 1 Day interval
                $period = new DatePeriod( $now, $interval, 6); // 7 Days
                $sale_data = array();
                foreach( $period as $day) {
                    $key = $day->format( 'd M Y');
                    $key_1 = $day->format( 'Y-m-d');
                    $data_r = $wpdb->get_row("SELECT COUNT(*) cnt FROM " . $wpdb->prefix . "hmwp_ms_intrusions WHERE date(created) = '". $key_1 ."'");
                    $sale_data[ $key ] = $data_r->cnt;
                }
                $total_ids_block = 0;
                foreach ($sale_data as $key => $value) {
                    $total_ids_block = $total_ids_block + $value;
                }
                //Weekly IP data
                $sale_data_ip = array();
                foreach( $period as $day) {
                    $key = $day->format( 'd M Y');
                    $key_1 = $day->format( 'Y-m-d');
                    $data_r = $wpdb->get_row("SELECT COUNT(*) cnt FROM " . $wpdb->prefix . "hmwp_blocked_ips WHERE date(created) = '". $key_1 ."'");
                    $sale_data_ip[ $key ] = $data_r->cnt;
                }
                $total_ips_block = 0;
                foreach ($sale_data_ip as $key => $value) {
                    $total_ips_block = $total_ips_block + $value;
                }
                ?>
                <div id="dashboard-widgets-wrap">
                    <div id="dashboard-widgets" class="metabox-holder">
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="filter-div" style="height: 30px; width: 100%; display: inline-block;">
                            </div>
                            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                <div id="dashboard_right_now" class="postbox">
                                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: At a Glance</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Intrusions blocked</span></h2>
                                    <div class="inside">
                                        <div class="main">
                                            <div class="hmwp-loader"></div>
                                            <h2><?php echo $total_ids_block; ?></h2>
                                            <p id="wp-version-message" style="display: inline-block; width: 100%;">
                                                <a href="#" class="button" aria-describedby="wp-version">See More >></a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
    
                                <div id="dashboard_top10_intru" class="postbox" style="display: block;">
                                    <button type="button" class="handlediv" aria-expanded="true">
                                        <span class="screen-reader-text">Toggle panel: At a Glance</span>
                                        <span class="toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                    <h2 class="hndle ui-sortable-handle" style="font-size: 14px;padding-bottom: 9px;padding-left: 10px;">
                                        <span>Top 10 Intrusions blocked</span>
                                    </h2>
                                    <div class="inside">
                                        <div class="main">
                                            <div class="hmwp-loader"></div>
                                                <script type="text/javascript">
                                                var config = {
                                                type: 'line',
                                                data: {
                                                    labels: [
                                                        <?php
                                                        foreach ($sale_data as $key => $value) {
                                                            echo "'". $key ."',";
                                                        }
                                                        ?>
                                                    ],
                                                    datasets: [{
                                                            label: 'IDS Attack Blocked',
                                                            backgroundColor: 'rgb(54, 162, 235)',
                                                            borderColor: 'rgb(54, 162, 235)',
                                                            data: [
                                                                    <?php
                                                                    foreach ($sale_data as $key => $value) {
                                                                        echo $value . ', ';
                                                                    }
                                                                    ?>
                                                            ],
                                                            fill: false,
                                                    }]
                                                        },
                                                        options: {
                                                                responsive: true,
                                                                title: {
                                                                        display: true,
                                                                        text: 'Blocked Attack Chart'
                                                                },
                                                                tooltips: {
                                                                        mode: 'index',
                                                                        intersect: false,
                                                                },
                                                                hover: {
                                                                        mode: 'nearest',
                                                                        intersect: true
                                                                },
                                                                scales: {
                                                                        xAxes: [{
                                                                                display: true,
                                                                                scaleLabel: {
                                                                                        display: true,
                                                                                        labelString: 'Date'
                                                                                }
                                                                        }],
                                                                        yAxes: [{
                                                                                display: true,
                                                                                scaleLabel: {
                                                                                        display: true,
                                                                                        labelString: 'Attack Blocked'
                                                                                },
                                                                                ticks: {
                                                                                    beginAtZero:true
                                                                                }
                                                                        }]
                                                                }
                                                        }
                                                };
                                                </script>
                                                <canvas id="buyers" width="600" height="400"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div id="dashboard_last10_instru" class="postbox">
                                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: At a Glance</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle" style="font-size: 14px;padding-bottom: 9px;padding-left: 10px;"><span>Last 10 IDS blocked attack</span></h2>
                                    <div class="inside">
                                        <div class="main">
                                            <table class="table widefat">
                                                <?php
                                                $ids_blocked_ips = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'hmwp_ms_intrusions ORDER BY id DESC LIMIT 0, 10');
                                                ?>
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Value</th>
                                                        <th>Impact / Total</th>
                                                        <th>IP</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($ids_blocked_ips)){
                                                        foreach ($ids_blocked_ips as $key => $single_arr) {
                                                            ?>
                                                            <tr>
                                                                <td><strong><?php echo sanitize_title($single_arr->name); ?></strong></td>
                                                                <td><?php
                                                                $allowed = array(
                                                                    'a' => array(
                                                                        'href' => array(),
                                                                        'title' => array()
                                                                    ),
                                                                    'br' => array(),
                                                                    'em' => array(),
                                                                    'strong' => array()
                                                                );
                                                                $v = wp_kses(esc_attr( $single_arr->value ), $allowed);
                                                                echo $v;
                                                                ?></td>
                                                                <td><?php echo esc_html( $single_arr->impact )  .' / '. $single_arr->total_impact; ?></td>
                                                                <td><?php echo sanitize_text_field( $single_arr->ip ); ?></td>
                                                                <td><?php echo date( "M d Y", strtotime( $single_arr->created ) ); ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }else{ ?>
                                                            <tr>
                                                                <td colspan="5">No result found.</td>
                                                            </tr>
                                                    <?php }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>     
                            </div>
                        </div>
                        <div id="postbox-container-2" class="postbox-container" >
                            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                <div class="filter-div" style="width: 100%; display: inline-block;">
                                    <select id="filter_based_on_time" style="float: right;">
                                        <option value="last-7">Last Week</option>
                                        <option value="this month">This month</option>
                                        <option value="last month">Last month</option>
                                        <option value="this year">This Year</option>
                                    </select>
                                </div>
                                <div id="dashboard_right_now" class="postbox">

                                    <button type="button" class="handlediv" aria-expanded="true">
                                        <span class="screen-reader-text">Toggle panel: At a Glance</span><span class="toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                    <h2 class="hndle ui-sortable-handle"><span>Blocked by Trust Network</span></h2>
                                    <div class="inside">
                                        <div class="main">
                                            <div class="hmwp-loader"></div>
                                            <h2><?php echo $total_ips_block; ?></h2>
                                            <p id="wp-version-message" style="display: inline-block; width: 100%;">
                                                <a href="#" class="button" aria-describedby="wp-version">See More >></a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div id="dashboard_top10_attackers" class="postbox">
                                    <button type="button" class="handlediv" aria-expanded="true">
                                        <span class="screen-reader-text">Toggle panel: At a Glance</span>
                                        <span class="toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                    <h2 class="hndle ui-sortable-handle" style="font-size: 14px;padding-bottom: 9px;padding-left: 10px;">
                                        <span>Top 10 IP attacked blocked</span>
                                    </h2>
                                    <div class="inside">
                                        <div class="main">
                                            <div class="hmwp-loader"></div>
                                                <script type="text/javascript">
                                                var ips_config = {
                                                type: 'line',
                                                data: {
                                                    labels: [
                                                        <?php
                                                        foreach ($sale_data_ip as $key => $value) {
                                                            echo "'". $key ."',";
                                                        }
                                                        ?>
                                                    ],
                                                    datasets: [{
                                                            label: 'IP Attack Blocked',
                                                            backgroundColor: 'rgb(54, 162, 235)',
                                                            borderColor: 'rgb(54, 162, 235)',
                                                            data: [
                                                                    <?php
                                                                    foreach ($sale_data_ip as $key => $value) {
                                                                        echo $value . ', ';
                                                                    }
                                                                    ?>
                                                            ],
                                                            fill: false,
                                                    }]
                                                        },
                                                        options: {
                                                                responsive: true,
                                                                title: {
                                                                        display: true,
                                                                        text: 'IP Blocked Attack Chart'
                                                                },
                                                                tooltips: {
                                                                        mode: 'index',
                                                                        intersect: false,
                                                                },
                                                                hover: {
                                                                        mode: 'nearest',
                                                                        intersect: true
                                                                },
                                                                scales: {
                                                                        xAxes: [{
                                                                                display: true,
                                                                                scaleLabel: {
                                                                                        display: true,
                                                                                        labelString: 'Date'
                                                                                }
                                                                        }],
                                                                        yAxes: [{
                                                                                display: true,
                                                                                scaleLabel: {
                                                                                        display: true,
                                                                                        labelString: 'Attack Blocked'
                                                                                },
                                                                                ticks: {
                                                                                    beginAtZero:true
                                                                                }
                                                                        }]
                                                                }
                                                        }
                                                };

                                                window.onload = function () {
                                                    //Ip chart
                                                    var ips_ctx = document.getElementById('hmwp_ips_blocked').getContext('2d');
                                                    var ips_chart = new Chart(ips_ctx, ips_config);

                                                    //IDS chart
                                                    var ctx = document.getElementById('buyers').getContext('2d');
                                                    var myChart = new Chart(ctx, config);

                                                    jQuery('#filter_based_on_time').change(function(){
                                                        jQuery('.hmwp-loader').show();
                                                        jQuery.ajax({
                                                            url: ajaxurl,
                                                            data: {
                                                                "action": "hmwp_get_ids_data",
                                                                "call_type": jQuery(this).val(),
                                                                "attack_type": 'IPS'
                                                            },
                                                            type: "POST",
                                                            success: function (data) {
                                                                var obj = JSON.parse(data);
                                                                ips_chart.data.labels = [];
                                                                ips_chart.data.datasets[0].data = [];
                                                                var total_ips_cnt = 0;
                                                                jQuery.each(obj, function(key, value){
                                                                    ips_chart.data.labels.push(value['x']);
                                                                    ips_chart.data.datasets[0].data.push(value['y']);
                                                                    total_ips_cnt = total_ips_cnt + parseInt(value['y']);
                                                                });
                                                                ips_chart.update();
                                                                jQuery('#postbox-container-3').find('.inside h2').text('').text(total_ips_cnt);
                                                                jQuery('#postbox-container-3').find('.hmwp-loader').hide();
                                                                jQuery('#ips_blocked_chart').find('.hmwp-loader').hide();
                                                            }
                                                        });

                                                        jQuery.ajax({
                                                            url: ajaxurl,
                                                            data: {
                                                                "action": "hmwp_get_ids_data",
                                                                "call_type": jQuery(this).val(),
                                                            },
                                                            type: "POST",
                                                            success: function (data) {
                                                                var obj = JSON.parse(data);
                                                                myChart.data.labels = [];
                                                                myChart.data.datasets[0].data = [];
                                                                var total_ids_cnt = 0;
                                                                jQuery.each(obj, function(key, value){
                                                                    myChart.data.labels.push(value['x']);
                                                                    myChart.data.datasets[0].data.push(value['y']);
                                                                    total_ids_cnt = total_ids_cnt + parseInt(value['y']);
                                                                });
                                                                myChart.update();
                                                                jQuery('#postbox-container-2').find('.inside h2').text('').text(total_ids_cnt);
                                                                jQuery('#postbox-container-2').find('.hmwp-loader').hide();
                                                                jQuery('#ids_blocked_chart').find('.hmwp-loader').hide();
                                                                //Remove this code
                                                                jQuery('.hmwp-loader').hide();
                                                            }
                                                        });
                                                    });

                                                    jQuery('.show-fixes-button').click(function(e){
                                                        e.preventDefault();
                                                        if(jQuery('.show-fixes-div').is(":visible")){
                                                            jQuery('.show-fixes-div').slideUp();
                                                            jQuery('.show-fixes-button').text('').text('Show Fixes');
                                                        }else{
                                                            jQuery('.show-fixes-button').text('').text('Hide Fixes');
                                                            jQuery('.show-fixes-div').slideDown();
                                                        }
                                                    });

                                                    jQuery('.hmwp-dash-notice').click(function (e){
                                                        e.preventDefault();
                                                        jQuery.ajax({
                                                            url: ajaxurl,
                                                            data: {
                                                                "action": "hmwp_remove_dashboard_notice",
                                                            },
                                                            type: "POST",
                                                            success: function (data) {
                                                                jQuery('#welcome-panel').slideUp();
                                                            }
                                                        });
                                                    });
                                                }
                                                </script>
                                                <canvas id="hmwp_ips_blocked" width="600" height="400"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div id="dashboard_last10_attackers" class="postbox" >
                                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: At a Glance</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle" style="font-size: 14px;padding-bottom: 9px;padding-left: 10px;"><span>Top 10 IP addresses blocked</span></h2>
                                    <div class="inside">
                                        <div class="main">
                                            <?php
                                            $ips_arr = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'hmwp_blocked_ips ORDER BY id DESC LIMIT 0, 10');
                                            ?>
                                            <table class="table widefat">
                                                <thead>
                                                    <tr>
                                                        <th>IP</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(!empty($ips_arr)){
                                                        foreach ($ips_arr as $key => $ips_single_arr) { ?>
                                                            <tr>
                                                                <td><?php echo $ips_single_arr->ip; ?></td>
                                                                <td><?php echo date( "M d Y", strtotime( $ips_single_arr->created ) ); ?></td>
                                                            </tr>
                                                        <?php
                                                        }
                                                    } else { ?>
                                                            <tr>
                                                                <td colspan="2">No result found.</td>
                                                            </tr>
                                                    <?php }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- postbox-2 -->
                    </div>
                </div>
            </div>
                            
        <?php
    }

    public function plugin_actions_links($links) {
        if ($this->settings_menu['action_link'])
            $links[] = '<a href="' . admin_url("admin.php?page=" . $this->settings_menu['name']) . '" >' .
                    $this->settings_menu['action_link'] . '</a>';
        return $links;
    }

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new WeDevs_Settings_API();
        }

        return self::$_instance;
    }

    /**
     * Set settings sections
     *
     * @param array $sections setting sections array
     */
    function set_sections($sections) {
        $this->settings_sections = $sections;
    }

    /**
     * Set settings fields
     *
     * @param array $fields settings fields array
     */
    function set_fields($fields) {
        $this->settings_fields = $fields;
    }

    /**
     * Set settings fields
     *
     * @param array $fields settings fields array
     */
    function set_menu($menu) {
        $this->settings_menu = $menu;
    }

    /**
     * Initialize and registers the settings sections and fileds to WordPress
     *
     * Usually this should be called at `admin_init` hook.
     *
     * This function gets the initiated settings sections and fields. Then
     * registers them to WordPress and ready for use.
     */
    public function admin_init() {
        //Disable Drag
        if (isset($_GET['page']) && $_GET['page'] == $this->settings_menu['name'])
            wp_deregister_script('postbox');

        if (!get_option($this->settings_menu['name']) || !$this->get_option('db_ver') || $this->get_option('db_ver') < $this->settings_menu['version'])
            $this->update_defaults();
        //add_option( $this->settings_menu['name'] );
        //register settings sections
        foreach ($this->settings_sections as $section) {


            add_settings_section($section['id'], $section['title'], '__return_false', $this->settings_menu['name']);
        }

        //register settings fields
        foreach ($this->settings_fields as $section => $field) {
            foreach ($field as $option) {
                $args = array(
                    'id' => $option['name'],
                    'desc' => $option['desc'],
                    'name' => $option['label'],
                    'section' => $section,
                    'class' => isset($option['class']) ? $option['class'] : null,
                    'options' => isset($option['options']) ? $option['options'] : '',
                    'std' => isset($option['default']) ? $option['default'] : ''
                );
                add_settings_field($option['name'], $option['label'], array($this, 'callback_' . $option['type']), $this->settings_menu['name'], $section, $args);
            }
        }

        // creates our settings in the options table
        //foreach ($this->settings_sections as $section) {
        register_setting($this->settings_menu['name'], $this->settings_menu['name'], array(&$this, 'admin_settings_validate'));
        //}
    }

    // validate our settings

    function admin_settings_validate($input) {

        do_action('pp_settings_api_validate', $input);



//      if (empty($input['sample_text'])) {
//
//                add_settings_error(
//                    'sample_text',           // setting title
//                    'sample_text_error',            // error ID
//                    'Please enter some sample text',   // error message
//                    'error'                        // type of message
//                );
//
//       }
        return $input;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_text($args) {

        $value = esc_attr($this->get_option($args['id'], $this->settings_menu['name']));
        // $class = isset( $args['class'] ) && !is_null( $args['class'] ) ? $args['class'] : 'regular';

        $html = sprintf('<input type="text" class="regular-text %1$s" id="%4$s" name="%2$s[%4$s]" value="%5$s"/>', $args['class'], $this->settings_menu['name'], $args['section'], $args['id'], $value);
        $html .= sprintf('<br/><span class="description"> %s</span>', $args['desc']);

        echo $html;
    }

    function callback_number($args) {

        $value = esc_attr($this->get_option($args['id'], $this->settings_menu['name']));
        // $class = isset( $args['class'] ) && !is_null( $args['class'] ) ? $args['class'] : 'regular';

        $html = sprintf('<input type="text" class="regular-number %1$s" id="%4$s" name="%2$s[%4$s]" value="%5$s" style="width:50px;"/>', $args['class'], $this->settings_menu['name'], $args['section'], $args['id'], $value);
        $html .= sprintf('<span class="description"> %s</span>', $args['desc']);

        echo $html;
    }

    function callback_hidden($args) {
        echo '</td></tr><tr valign="top" style="display:none;"><td colspan="2"><span class="' . $args['class'] . '">' . $args['desc'] . '</span>';
    }

    function callback_html($args) {
        echo '</td></tr><tr valign="top"><td colspan="2"><span class="' . $args['class'] . '">' . $args['desc'] . '</span>';
    }

    function callback_custom($args) {
        echo '<div class="' . $args['class'] . '">' . $args['desc'] . '</div>';
    }

    function callback_wp_editor($args) {
        // $value = esc_attr( $this->get_option( $args['id'], $this->settings_menu['name'], $args['std'] ) );

        $value = $this->get_option($args['id'], $this->settings_menu['name']);

        echo wp_editor($value, $this->settings_menu['name'] . '_' . $args['id'], array('textarea_name' => $this->settings_menu['name'] . '[' . $args['id'] . ']', 'textarea_rows' => '5', 'wpautop' => false, 'dfw' => false, 'media_buttons' => true, 'quicktags' => true, 'tinymce' => true, 'editor_class' => $args['class'], 'teeny' => false));
        echo sprintf('<span class="description"> %s</span>', $args['desc']);
    }

    function callback_file($args) {
        $value = esc_attr($this->get_option($args['id'], $this->settings_menu['name']));

        $html = sprintf('<input type="text" class="regular-text image-upload-url %1$s" id="%3$s" name="%2$s[%3$s]" value="%4$s" />', $args['class'], $this->settings_menu['name'], $args['id'], $value);
        $html .= sprintf('<input id="st_upload_button" class="image-upload-button" type="button" name="upload_button" value="%s" />', __('Select', $this->settings_menu['name']));

        $html .= sprintf('<span class="description"> %s</span>', $args['desc']);

        echo $html;
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array $args settings field args
     */
    function callback_checkbox($args) {

        $value = esc_attr($this->get_option($args['id'], $this->settings_menu['name']));

        $html = sprintf('<input type="checkbox" class="checkbox %1$s" id="%3$s" name="%2$s[%3$s]" value="on"%4$s/>', $args['class'], $this->settings_menu['name'], $args['id'], checked('on', $value, false));
        $html .= sprintf('<label for="%2$s"> %3$s</label>', $this->settings_menu['name'], $args['id'], $args['desc']);

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array $args settings field args
     */
    function callback_multicheck($args) {

        $value = $this->get_option($args['id'], $this->settings_menu['name']);

        if (!$args['options'])
            return;
        //option name should not be 0 to work correctly with empty option
        $html = '';
        foreach ($args['options'] as $key => $label) {
            $checked = isset($value[$key]) ? $value[$key] : '0';
            $html .= sprintf('<input type="checkbox" class="checkbox %1$s" id="%3$s_%4$s" name="%2$s[%3$s][%4$s]" value="%4$s"%5$s />', $args['class'], $this->settings_menu['name'], $args['id'], $key, checked($checked, $key, false));
            $html .= sprintf('<label for="%2$s_%4$s"> %3$s</label><br>', $this->settings_menu['name'], $args['id'], $label, $key);
        }
        $html .= sprintf('<span class="description"> %s</label>', $args['desc']);

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array $args settings field args
     */
    function callback_radio($args) {

        $value = $this->get_option($args['id'], $this->settings_menu['name']);

        $html = '';
        foreach ($args['options'] as $key => $label) {
            $html .= sprintf('<input type="radio" class="radio %1$s" id="%3$s_%4$s" name="%2$s[%3$s]" value="%4$s"%5$s />', $args['class'], $this->settings_menu['name'], $args['id'], $key, checked($value, $key, false));
            $html .= sprintf('<label for="%2$s_%4$s"> %3$s</label><br>', $this->settings_menu['name'], $args['id'], $label, $key);
        }
        $html .= sprintf('<span class="description"> %s</label>', $args['desc']);

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array $args settings field args
     */
    function callback_select($args) {


        $value = esc_attr($this->get_option($args['id'], $this->settings_menu['name']));
        //$class = isset( $args['class'] ) && !is_null( $args['class'] ) ? $args['class'] : 'regular';

        $html = sprintf('<select class="regular pages_list selectbox %1$s" name="%2$s[%3$s]" id="%3$s">', $args['class'], $this->settings_menu['name'], $args['id']);
        foreach ($args['options'] as $key => $label) {
            $html .= sprintf('<option value="%s"%s>%s</option>', $key, selected($value, $key, false), $label);
        }
        $html .= sprintf('</select>');
        $html .= sprintf('<span class="description"> %s</span>', $args['desc']);

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array $args settings field args
     */
    function callback_rolelist($args) {
        global $wp_roles;

        if ($wp_roles->roles)
            foreach ($wp_roles->roles as $key => $val)
                if ($key != 'administrator')
                    $args['options'][$key] = $wp_roles->roles[$key]['name'];

        $value = $this->get_option($args['id'], $this->settings_menu['name']);
        $html = '';
        foreach ($args['options'] as $key => $label) {
            $checked = isset($value[$key]) ? $value[$key] : '0';

            $html .= sprintf('<input type="checkbox" class="checkbox user_roles_checkbox %1$s" id="%3$s_%4$s" name="%2$s[%3$s][%4$s]" value="%4$s"%5$s />', $args['class'], $this->settings_menu['name'], $args['id'], $key, checked($checked, $key, false));
            $html .= sprintf('<label for="%2$s_%4$s"> %3$s</label><br>', $this->settings_menu['name'], $args['id'], $label, $key);
        }
        $html .= sprintf('<span class="description"> %s</label>', $args['desc']);

        echo $html;
    }

    function callback_pagelist($args) {
        $value = esc_attr($this->get_option($args['id'], $this->settings_menu['name']));
        $name = sprintf('%1$s[%2$s]', $this->settings_menu['name'], $args['id']);

        $q = array(
            'depth' => 0, 'child_of' => 0,
            'selected' => $value, 'echo' => 0,
            'name' => $name, 'id' => $args['id'],
            'show_option_none' => '', 'show_option_no_change' => '',
            'option_none_value' => ''
        );

        $html = wp_dropdown_pages($q);
        $html = str_replace('<select', '<select class="' . $args['class'] . '" ', $html);

        $html .= sprintf('<span class="description"> %s</span>', $args['desc']);
        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array $args settings field args
     */
    function callback_textarea($args) {

        $value = esc_textarea($this->get_option($args['id'], $this->settings_menu['name']));
        //$class = isset( $args['class'] ) && !is_null( $args['class'] ) ? $args['class'] : 'regular';

        $html = sprintf('<textarea rows="5" cols="65" class="regular-text %1$s" id="%3$s" name="%2$s[%3$s]">%4$s</textarea>', $args['class'], $this->settings_menu['name'], $args['id'], $value);
        $html .= sprintf('<br><span class="description"> %s</span>', $args['desc']);

        echo $html;
    }

    function callback_export($args) {
		$empty_keys = array_keys(array_diff_key($this->get_defaults(), get_option($this->settings_menu['name'])));
		$empty_keys = array_fill_keys($empty_keys, '');
        if (isset($_GET['save_export_settings']) && $_GET['save_export_settings']) {
            $value = get_option($this->settings_menu['name']);
            $value = str_replace(array("\r\n", "\n", "\r"), '[new_line]', $value);
            $value = str_replace("\/", '[double_slashes]', $value);
            $value = str_replace('"', '[quotation]', $value);
            $value = str_replace('{', '[o_cb]', $value);
            $value = str_replace('}', '[c_cb]', $value);
            $value = stripslashes(json_encode(array_merge($value, $empty_keys), JSON_UNESCAPED_UNICODE));
			ob_get_clean();
			ob_start();
			$handle = fopen("hmwp_settings.txt", "w");
			header('Content-Disposition: attachment; filename="hmwp_settings.txt"');
			header('Content-Type: application/octet-stream');
			header("Content-Length: " . strlen($value));
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Expires: 0');
			header("Connection: close");
			echo $value;
			exit;
		} elseif (isset($_GET['export_settings']) && $_GET['export_settings']) {
            $value = get_option($this->settings_menu['name']);
            $value = str_replace(array("\r\n", "\n", "\r"), '[new_line]', $value);
            $value = str_replace("\/", '[double_slashes]', $value);

            $value = str_replace('"', '[quotation]', $value);
            $value = str_replace('{', '[o_cb]', $value);
            $value = str_replace('}', '[c_cb]', $value);
            //$value = str_replace('[', '[o_b]', $value);
            //  $value = str_replace(']', '[c_b]', $value);

            $value = esc_textarea(stripslashes(json_encode(array_merge($value, $empty_keys), JSON_UNESCAPED_UNICODE)));

            //$class = isset( $args['class'] ) && !is_null( $args['class'] ) ? $args['class'] : 'regular';
            $html = sprintf('<strong> %s </strong><br/>', $args['desc']);
            $html .= sprintf('<textarea readonly="readonly" onclick="this.focus();this.select()" rows="5" cols="65" class="regular-text %1$s" id="%2$s" name="%2$s" style="%4$s">%3$s</textarea>', $args['class'], 'export_field', $value, 'width:95% !important;height:400px !important');


            echo $html;
        } else {
            echo '<a href="' . add_query_arg(array('export_settings' => true)) . '" class="button">' . __('Export Current Settings', $this->settings_menu['name']) . '</a>';
			echo '&nbsp;&nbsp;&nbsp;';
            echo '<a href="' . add_query_arg(array('save_export_settings' => true)) . '" class="button">' . __('Save Current Settings as file', $this->settings_menu['name']) . '</a>';
            echo sprintf('<br><span class="description"> %s</span>', $args['desc']);
        }
    }

    function callback_import($args) {
        $html = '';

        if ($args['options']) {
            $html .= sprintf('<select class="regular selectbox %1$s" name="import_options" id="%3$s">', $args['class'], $this->settings_menu['name'], $args['id']);

            $html .= sprintf('<option value="" selected="selected">- Select Scheme -</option>');
            foreach ($args['options'] as $key => $settings_value)
                $html .= sprintf('<option value="%s">%s</option>', esc_textarea(stripslashes($settings_value)), ucfirst($key));


            $html .= sprintf('</select>');
            $html .= '  <a href="'. admin_url('admin.php?page=hmwp_setup_wizard') .'" class="button button-primary">Open Wizard</a>';
            $html .= '<br>';
        }

        $html .= sprintf('<span class="description">%s</span>', $args['desc']);
        $html .= '<br>';

        $value = '';
        //$class = isset( $args['class'] ) && !is_null( $args['class'] ) ? $args['class'] : 'regular';

        $html .= sprintf('<textarea rows="5" cols="65" class="regular-text %1$s" id="%2$s" name="%2$s">%3$s</textarea>', $args['class'], 'import_field', $value);
        // $html .= sprintf( '<br><span class="description"> %s</span>', $args['desc'] );

        echo $html;
    }

    function callback_debug_report($args) {
        global $wp_version;

        if (!isset($_GET['debug_report'])) {
            echo '<a href="' . add_query_arg(array('debug_report' => true)) . '" class="button">' . __('Generate Debug Report', $this->settings_menu['name']) . '</a>';
            echo sprintf('<br><span class="description"> %s</span>', $args['desc']);
        } else {

            /* Get from WooCommerce by WooThemes http://woothemes.com  */
            $active_plugins = (array) get_option('active_plugins', array());
            if (is_multisite())
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));

            $active_plugins = array_map('strtolower', $active_plugins);
            $pp_plugins = array();

            foreach ($active_plugins as $plugin) {
                $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                if (!empty($plugin_data['Name'])) {
                    $pp_plugins[] = $plugin_data['Name'] . ' ' . $plugin_data['Version'] . ' [' . $plugin_data['PluginURI'] . "]";
                }
            }

            if ($pp_plugins)
                $plugin_list = implode("\n", $pp_plugins);


            $wp_info = ( is_multisite() ) ? 'WPMU ' . $wp_version : 'WP ' . $wp_version;
            $wp_debug = ( defined('WP_DEBUG') && WP_DEBUG ) ? 'true' : 'false';
            $is_ssl = ( is_ssl() ) ? 'true' : 'false';
            $is_rtl = ( is_rtl() ) ? 'true' : 'false';
            $fsockopen = ( function_exists('fsockopen') ) ? 'true' : 'false';
            $curl = ( function_exists('curl_init') ) ? 'true' : 'false';
            $max_upload_size = (function_exists('size_format')) ? size_format(wp_max_upload_size()) : wp_convert_bytes_to_hr(wp_max_upload_size());

            if (function_exists('phpversion')) {
                $php_info = phpversion();
                $max_server_upload = ini_get('upload_max_filesize');
                $post_max_size = ini_get('post_max_size');
            }

            $empty_keys = array_keys(array_diff_key($this->get_defaults(), get_option($this->settings_menu['name'])));
            $empty_keys = array_fill_keys($empty_keys, '');

            $value = '
===========================================================
 WP Settings
===========================================================
WordPress version: 	' . $wp_info . '
Home URL: 	' . home_url() . '
Site URL: 	' . site_url() . '
Is SSL: 	' . $is_ssl . '
Is RTL: 	' . $is_rtl . '
Permalink: 	' . get_option('permalink_structure') . '

============================================================
 Server Environment
============================================================
PHP Version:     	' . $php_info . '
Server Software: 	' . $_SERVER['SERVER_SOFTWARE'] . '
WP Max Upload Size: ' . $max_upload_size . '
Server upload_max_filesize:     ' . $max_server_upload . '
Server post_max_size: 	' . $post_max_size . '
WP Memory Limit: 	' . WP_MEMORY_LIMIT . '
WP Debug Mode: 	    ' . $wp_debug . '
CURL:               ' . $curl . '
fsockopen:          ' . $fsockopen . '

============================================================
 Active plugins
============================================================
' . $plugin_list . '

============================================================
 Plugin Option
============================================================
' . esc_textarea(stripslashes(json_encode(array_merge(get_option($this->settings_menu['name']), $empty_keys)))) . '
    ';


            $html = sprintf('<textarea readonly="readonly" rows="5" cols="65" style="%4$s" class="%1$s" id="%2$s" name="%2$s">%3$s</textarea>', $args['class'], 'debug_report', $value, 'width:95% !important;height:400px !important');
            $html .= sprintf('<br><span class="description"> %s</span>', $args['desc']);

            echo $html;
        }
    }

    /**
     * Get the value of a settings field
     *
     * @param string $option settings field name
     * @param string $option_page the $option_page name this field belongs to
     * @param string $default default text if it's not found
     * @return string
     */
    function get_option($option, $option_page = '', $_disabled_default = '') {
        if (!$option_page)
            $option_page = $this->settings_menu['name'];

        $options = $this->get_options($option_page);

        if (isset($options[$option]))
            return $options[$option];

        return false;
    }

    public function get_defaults() {
        $defaults_val = array();
        foreach ($this->settings_fields as $tabs => $field) {
            foreach ($field as $opt) {
                if (isset($opt['name'])) {
                    if (isset($opt['default'])) {
                        $temp = str_replace('[new_line]', "\r\n", $opt['default']);
                        $temp = str_replace('[double_slashes]', "\/", $temp);
                        $temp = str_replace('[o_cb]', '{', $temp);
                        $temp = str_replace('[c_cb]', '}', $temp);

                        $defaults_val[$opt['name']] = str_replace('[quotation]', '"', $temp);
                    } else {
                        $defaults_val[$opt['name']] = '';
                    }
                }
            }
        }
        return $defaults_val;
    }

    public function update_options($main_key, $options) {

        if (is_multisite())
            update_blog_option(BLOG_ID_CURRENT_SITE, $main_key, $options);
        else
            update_option($main_key, $options);
    }

    public function get_options() {
        $main_key = $this->settings_menu['name'];

        if (is_multisite())
            $current_options = get_blog_option(BLOG_ID_CURRENT_SITE, $main_key);
        else
            $current_options = get_option($main_key);

        return $current_options;
    }

    public function update_defaults() {
        $defaults_options = $this->get_defaults();

        $main_key = $this->settings_menu['name'];

        $prev_options = $this->get_options();


        // Do previous options exist? Merge them, this way we keep existing options
        // and if an update adds new options they get added too.
        if (is_array($prev_options)) {
            $options = array_merge($defaults_options, $prev_options);
        } else {
            $options = $defaults_options;
        }


        $options['db_ver'] = $this->settings_menu['version'];

        $this->update_options($main_key, $options);
    }

    /**
     * Show navigations as tab
     *
     * Shows all the settings section labels as tab
     */
    function show_navigation() {
        $html = '<h2 class="nav-tab-wrapper">';

        foreach ($this->settings_sections as $tab) {
            $html .= sprintf('<a href="#%1$s" class="nav-tab" style="font-size:18px;" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title']);
        }

        $html .= '</h2>';

        echo $html;
    }

    function do_settings_sections_for_tab($page, $sections) {
        global $wp_settings_sections, $wp_settings_fields;

        if (!isset($wp_settings_sections) || !isset($wp_settings_sections[$page]))
            return;

        foreach ((array) $wp_settings_sections[$page] as $section) {
            if (in_array($section['id'], $sections)) {
                echo "<h3>{$section['title']}</h3>\n";
                call_user_func($section['callback'], $section);
                if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]))
                    continue;
                echo '<table class="form-table">';
                do_settings_fields($page, $section['id']);
                echo '</table>';
            }
        }
    }

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    function show_forms() {


        if ($this->settings_menu['display_metabox'])
            echo '<div class="" style="width:77%; background: white;padding: 7px 15px;margin-top: 5px; border: 1px solid #ddd;
"><div class="">';

        if (is_network_admin())
            echo '<form method="post" action="../options.php">';
        else
            echo '<form method="post" action="options.php">';

        settings_fields($this->settings_menu['name']);
        // do_settings_sections( $this->settings_menu['name']);
        foreach ($this->settings_sections as $form) {
            ?>
            <div id="<?php echo $form['id']; ?>" class="group">


                <?php
                $this->do_settings_sections_for_tab($this->settings_menu['name'], $form);
                //
                ?>
                <?php //do_settings_sections( $this->settings_menu['name']); ?>




            </div>


        <?php } ?>

        <span style="padding:0 10px;" class="alignleft">
            <?php submit_button('Save Settings'); ?>
        </span>

        <span style="padding:0 10px;" class="alignright">
            <p class="submit">
                <input name="reset-defaults" onclick="return confirm('<?php _e('Are you sure you want to restore all settings back to their default values?', $this->settings_menu['name']); ?>');" class="button-secondary" type="submit" value="<?php _e('Reset Settings to WP', $this->settings_menu['name']); ?>" />                      </p>
        </span>



        <div class="clear"></div>

        </form>
        <?php
        if ($this->settings_menu['display_metabox'])
            echo '</div></div>';

        $this->script();
    }

    /**
     * Tabbable JavaScript codes
     *
     * This code uses localstorage for displaying active tabs
     */
    function script() {
        ?>
        <style type="text/css">
            <!--
            .postbox h3{cursor: auto!important;}
            -->
        </style>

        <script>
            jQuery(document).ready(function ($) {
                // Switches option sections
                $('.group').hide();

                $('#import_options').change(function (e) {

                    if (confirm('You may lose your current settings. Is it OK?') == true)
                        $('#import_field').val($(this).val());
                    else
                        $('#import_field').val('');
                });

                $('.opener').change(function (e) {

                    var this_obj = $(this);
                    var id = this_obj.attr('id');
                    var name = this_obj.attr('name');

                    if (this_obj.attr('type') == 'checkbox') {

                        if (this_obj.is(':checked'))
                            $('.open_by_' + id).parentsUntil('tbody').slideDown('150');
                        else
                            $('.open_by_' + id).parentsUntil('tbody').slideUp('150');

                    } else if (this_obj.attr('type') == 'radio') {

                        $('.open_by_' + $('input[name="' + name + '"]:checked').attr('id')).parentsUntil('tbody').slideDown('150');
                        //hide other
                        $('.open_by_' + $('input[name="' + name + '"]:not(:checked)').attr('id')).parentsUntil('tbody').slideUp('150');
                    } else if (this_obj.hasClass('selectbox')) {

                        $('.open_by_' + id + '_' + this_obj.val()).parentsUntil('tbody').slideDown('150');
                        //hide other
                        $("[class^='open_by_" + id + "_'],[class*=' open_by_" + id + "_']").not('.open_by_' + id + '_' + this_obj.val()).parentsUntil('tbody').slideUp();

                    }

                });


                //first time load should be after change
                $('.opener').trigger('change');


                var activetab = '';
                if (typeof (localStorage) != 'undefined') {
                    activetab = localStorage.getItem("activetab");
                }
                if (activetab != '' && $(activetab).length) {
                    $(activetab).fadeIn();
                } else {
                    $('.group:first').fadeIn();
                }
                $('.group .collapsed').each(function () {
                    $(this).find('input:checked').parent().parent().parent().nextAll().each(
                            function () {
                                if ($(this).hasClass('last')) {
                                    $(this).removeClass('hidden');
                                    return false;
                                }
                                $(this).filter('.hidden').removeClass('hidden');
                            });
                });

                if (activetab != '' && $(activetab + '-tab').length) {
                    $(activetab + '-tab').addClass('nav-tab-active');
                } else {
                    $('.nav-tab-wrapper a:first').addClass('nav-tab-active');
                }
                $('.nav-tab-wrapper a').click(function (evt) {
                    $('.nav-tab-wrapper a').removeClass('nav-tab-active');
                    $(this).addClass('nav-tab-active').blur();
                    var clicked_group = $(this).attr('href');
                    if (typeof (localStorage) != 'undefined') {
                        localStorage.setItem("activetab", $(this).attr('href'));
                    }
                    $('.group').hide();
                    $(clicked_group).fadeIn();
                    evt.preventDefault();
                });
            });
        </script>
        <?php
    }

}
