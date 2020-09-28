<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class UserPro
{
    /**
     * The single instance of the class.
     *
     * @var UserPro
     * @since 4.9.31
     */
    protected static $_instance = null;

    public $up_admin = null;

    public static $version = '4.9.38';

    /**
     * Social addon object
     *
     * @since 4.9.33
     * @var null
     */
    public $up_social = null;

    public function __construct()
    {
        $this->defineConstants();
        $this->includes();
        $this->initHooks();

        //Check plugin version and remove old options
        if(empty(get_option('userpro_old_options_deleted'))){
            $this->deleteOldData();
        }
        // Cross site scripting dir issue.
        $dir = userpro_path . 'lib/instagram/vendor/cosenary/instagram/example/';
        if(file_exists($dir)){
            array_map('unlink', glob("$dir/*.*"));
            rmdir($dir);
        }

        do_action('userpro_loaded');
    }

    /**
     * Main UserPro instance
     *
     *
     * @since 4.9.31
     * @static
     * @return UserPro - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Hooks into actions
     * @since 4.9.31
     */
    private function initHooks()
    {
        register_activation_hook(__FILE__, [$this, 'userpro_add_userin_meta']);
        add_action('init', [$this, 'init']);
        add_action('init', [$this, 'loadLanguages']);

        if($this->is_request('frontend')){
            add_action('wp_footer', [$this, 'enqueueStyles']);
        }
    }
    /**
     * Include main UserPro files
     * @since 4.9.31
     */
    public function includes()
    {

        require_once userpro_path . 'vendor/autoload.php';

        require_once userpro_path . "functions/ajax.php";

        require_once userpro_path . "functions/defaults.php";
        require_once userpro_path . "functions/badge-functions.php";
        require_once userpro_path . "functions/common-functions.php";
        require_once userpro_path . "functions/custom-alerts.php";
        require_once userpro_path . "functions/user-functions.php";

        require_once userpro_path . "includes/user/class-user-invitation.php";
        require_once userpro_path . "includes/class-userpro-delete-options.php";

//        require_once userpro_path . "functions/_trial.php";

        require_once userpro_path . "functions/fields-filters.php";
        require_once userpro_path . "functions/fields-functions.php";
        require_once userpro_path . "functions/fields-hooks.php";
        require_once userpro_path . "functions/fields-setup.php";
        require_once userpro_path . "functions/global-actions.php";
        require_once userpro_path . "functions/buddypress.php";
        require_once userpro_path . "functions/hooks-actions.php";
        require_once userpro_path . "functions/hooks-filters.php";
        require_once userpro_path . "functions/icons-functions.php";
        require_once userpro_path . "functions/initial-setup.php";
        require_once userpro_path . "functions/mail-functions.php";
        require_once userpro_path . "functions/msg-functions.php";
        require_once userpro_path . "functions/security.php";
        require_once userpro_path . "functions/shortcode-extras.php";
        require_once userpro_path . "functions/invite_users_widgets.php";
        require_once userpro_path . "functions/shortcode-functions.php";
        require_once userpro_path . "functions/shortcode-main.php";
        require_once userpro_path . "functions/shortcode-private-content.php";
        require_once userpro_path . "functions/shortcode-social-connect.php";
        require_once userpro_path . "functions/social-connect.php";
        require_once userpro_path . "functions/template-redirects.php";
        require_once userpro_path . "functions/terms-agreement.php";


        /* load addons */
        require_once userpro_path . 'addons/emd/index.php';
        require_once userpro_path . 'addons/multiforms/index.php';
        require_once userpro_path . 'addons/badges/index.php';
        require_once userpro_path . 'addons/social/index.php';
        require_once userpro_path . 'addons/redirects/index.php';
        require_once userpro_path . 'addons/requests/index.php';
        require_once userpro_path . 'addons/userpro-google-map/userpro-google-map.php';
        require_once userpro_path . 'addons/timeline/timeline.php';

        if ( $this->is_request( 'admin' ) ) {
            require_once userpro_path . 'admin/admin.php';
            require_once userpro_path . 'admin/admin-functions.php';
            require_once userpro_path . 'admin/admin-users.php';
            require_once userpro_path . 'admin/admin-functions-woo.php';
            require_once userpro_path . 'admin/admin-metabox.php';
            require_once userpro_path . 'admin/admin-notices.php';
            require_once userpro_path . 'admin/class-up-updates-plugin.php';

            // Register admin ajax functions
            UP_UserAdminAjax::instance();
        }

        // Front end functions
        if( $this->is_request('frontend' )){
            require_once userpro_path . "includes/up-core-functions.php";
            require_once userpro_path . "includes/up-template-functions.php";
            UP_UserAjax::instance();
            $this->up_social = new UP_Social();

        }
    }

    /**
     * Require addons file
     *
     * @since 4.9.34
     */
    public function includeAddons(){

    }

    /**
     * Request type
     *
     * @since 4.9.31
     * @param $type
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'cron':
                return defined( 'DOING_CRON' );
            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     * Define main UserPro constants
     *
     * @since 4.9.31
     */
    private function defineConstants()
    {
        $this->define('userpro_url', plugin_dir_url(UP_PLUGIN_FILE));
        $this->define('userpro_path', plugin_dir_path(UP_PLUGIN_FILE));
        $this->define('UP_PREFIX', 'userpro');
    }

    /**
     * Set constant
     *
     * @since 4.9.31
     * @param $name
     * @param $value
     */
    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * UserPro init action for old UserPro class.
     */
    public function init()
    {
        do_action('userpro_before_init');

        if (!session_id()) {
            session_start();
        }

        $userpro = new userpro_api();

        if ($userpro == null) {
            $userpro = new userpro_api();
        }
        $result = get_option("userpro_invite_check");
        if (empty($result)) {

            $user_invite_template = userpro_get_option('userpro_invite_emails_template');
            $userpro_options = get_option('userpro');
            $userpro_options['userpro_invite_emails_template'] = str_replace("inivitelink", "invitelink",
                userpro_get_option('userpro_invite_emails_template'));
            update_option('userpro', $userpro_options);
            update_option("userpro_invite_check", "1");
        }

        $userpro->do_uploads_dir();

        /* include libs */
        if (!class_exists('UserProMailChimp')) {
            require_once userpro_path . '/lib/mailchimp/MailChimp.php';
        }
        do_action('userpro_after_init');
    }

    /**
     * Register default UserPro meta data.
     */
    public function userpro_add_userin_meta()
    {
        // Activation code here...
        global $wpdb;
        $usermetatable = $wpdb->prefix . 'usermeta';
        $usertable = $wpdb->prefix . 'users';
        $query = "UPDATE $usermetatable INNER JOIN $usertable ON $usermetatable.user_id = $usertable.ID
        SET $usermetatable.meta_value = $usertable.display_name
        WHERE  $usermetatable.user_id = $usertable.ID AND $usermetatable.meta_key = 'display_name'";
        $wpdb->query($query);
    }

    /**
     * Delete Old Plugin meta data for avoid invitation meta data issues.
     * @since 4.9.31
     */
    private function deleteOldData()
    {
        $userpro_data = new UP_DeleteOptions();

        $userpro_data->options_names = array(
            'userpro_invited_users',
        );

        $userpro_data->deleteOptions();
        update_option('userpro_old_options_deleted', true);
    }

    /**
     * Register new userpro styles.
     * @since 4.9.31
     */
    public function enqueueStyles(){
        if($this->is_request('admin')){
            wp_register_style('userpro-fa-icons', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css');
            wp_enqueue_style('userpro-fa-icons');
        }

        wp_register_style('userpro-fa-icons-local', userpro_url . 'assets/css/fontawesome/css/all.min.css');
        wp_enqueue_style('userpro-fa-icons-local');
        if($this->is_request('frontend')){
            wp_register_style('userpro_latest_css', userpro_url . 'assets/css/main.css');
            wp_enqueue_style('userpro_latest_css');
        }
    }

    public function loadLanguages()
    {
        load_plugin_textdomain('userpro', false, 'userpro/languages');
    }
}
add_action('wp_loaded', 'close_my_session', 30);
function close_my_session() {
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_write_close();
    }
}