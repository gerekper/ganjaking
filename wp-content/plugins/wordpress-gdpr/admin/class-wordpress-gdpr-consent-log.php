<?php

class WordPress_GDPR_Consent_Log extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    protected $options;

    /**
     * Store Locator Plugin Construct
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://www.welaunch.io
     * @param   string                         $plugin_name
     * @param   string                         $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    /**
     * Init the Public
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://www.welaunch.io
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_gdpr_options, $wp_version;

        $this->options = $wordpress_gdpr_options;

        if (!$this->get_option('enable')) {
            return false;
        }

        if($this->get_option('useWPCoreFunctions') && version_compare( $wp_version, '4.9.6', '>=' )) {
            return false;
        }

        add_action('admin_menu', array($this, 'add_users_menu'));

        return true;
    }

    /**
     * Update Cookies Consent
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @param   [type]                       $login [description]
     * @param   [type]                       $user  [description]
     * @return  [type]                              [description]
     */
    public function update_consent_log($cookies) 
    {
        $user_id = get_current_user_id();
        if($user_id) {

            $current_cookies = get_user_meta( $user_id, 'wordpress_gdpr_consents', true );
            if(!$current_cookies || empty($current_cookies) || !is_array($current_cookies)) {
                update_user_meta( $user_id, 'wordpress_gdpr_consents', $cookies );
            } else {

                $cookies = $cookies + $current_cookies;
                unset($cookies[0]);
                unset($cookies[1]);
                unset($cookies[2]);
                unset($cookies[3]);
                unset($cookies[4]);
                unset($cookies[5]);

                update_user_meta( $user_id, 'wordpress_gdpr_consents', $cookies );
            }  
        } else {

            if(!$this->get_option('consentLogLoggedOut')) {
                return false;
            }

            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
            if(!$ip) {
                return;
            }

            $ip = preg_replace(['/\.\d*$/', '/[\da-f]*:[\da-f]*$/'], ['.XXX', 'XXXX:XXXX'], $ip);

            global $wpdb;

            $table_name = $wpdb->prefix . "gdpr_consent_log";

            $checkExists = 
            $wpdb->get_results( 
                $wpdb->prepare( "SELECT * FROM $table_name WHERE ip = '%s'",  esc_sql( $ip) ) 
            );

            if(!empty($checkExists)) {
                $checkExists = $checkExists[0];

                $current_cookies = maybe_unserialize( $checkExists->consents );

                $cookies = $cookies + $current_cookies;
                $data = array(
                    'modified' => current_time('mysql', 1),
                    'consents' => maybe_serialize( $cookies ),
                );
                $format = array('%s');
                $wpdb->update($table_name, $data, array('ip' => $ip) );

            } else {

                $data = array(
                    'ip' => $ip,
                    'modified' => current_time('mysql', 1),
                    'consents' => maybe_serialize( $cookies ),
                );

                $wpdb->insert($table_name, $data);
            }
        }
    }

   /**
     * Init Reports Page in Admin
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://www.welaunch.io
     * @return  [type]                       [description]
     */
    public function add_users_menu()
    {        
        add_submenu_page(
            'wordpress_gdpr_options_options',
            __('Consent Log', 'wordpress-gdpr'),
            __('Consent Log', 'wordpress-gdpr'),
            'manage_options',
            'consent-log',
            array($this, 'get_consent_log_page'),
            1
        );
    }

    public function get_consent_log_page()
    {
        ?>

        <style>
        #gdpr_users {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin-top:20px;
        }

        #gdpr_users td, #gdpr_users th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #gdpr_users tr:nth-child(even){background-color: #f2f2f2;}

        #gdpr_users tr:hover {background-color: #ddd;}

        #gdpr_users th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #072894;
            color: #FFFFFF;
        }
        </style>

        <table id="gdpr_users">
            <thead>
                <tr>
                     <th><?php echo __('Username', 'wordpress-gdpr') ?></th>
                     <th><?php echo __('Name', 'wordpress-gdpr') ?></th>
                     <th><?php echo __('Email', 'wordpress-gdpr') ?></th>
                     <th><?php echo __('Last Login', 'wordpress-gdpr') ?></th>
                     <th><?php echo __('Consents', 'wordpress-gdpr') ?></th>
                     <th><?php echo __('Actions', 'wordpress-gdpr') ?></th>
                </tr>
            </thead>

            <?php

            $args = array(
                'post_type' => 'gdpr_service',
                'posts_per_page' => -1,
            );
            $services = get_posts($args);
            $tmp = array();
            foreach ($services as $service) {
                $tmp[$service->ID] = array(
                    'id' => $service->ID,
                    'name' => $service->post_title,
                    // 'cookies' => get_post_meta($service->ID, 'cookies' , true),
                    // 'deactivatable' => get_post_meta($service->ID, 'deactivatable' , true),
                    // 'head_script' => get_post_meta($service->ID, 'head_script' , true),
                    // 'body_script' => get_post_meta($service->ID, 'body_script' , true),
                    // 'adsense' => get_post_meta($service->ID, 'adsense' , true),
                    // 'defaultEnabled' => get_post_meta($service->ID, 'defaultEnabled' , true),
                );
            }
            $services = $tmp;

            $users = get_users();

            if(empty($users)) {
                echo __('No users found', 'wordpress-gdpr');
            } else {
                foreach ($users as $user) {
                    $user_id = $user->ID;
                    $user_data = get_userdata($user->ID);
                    $user_meta = get_user_meta( $user->ID );
                    $last_login = "";

                    if(empty($user_id)) {
                        continue;
                    }

                    if(isset($user_meta['wordpress_gdpr_last_login'])) {
                        $last_login = date_i18n( get_option( 'date_format' ), $user_meta['wordpress_gdpr_last_login'][0]);
                    }

                    $consents = get_user_meta( $user_id, 'wordpress_gdpr_consents', true );
                    if(!$consents || empty($consents) || !is_array($consents)) {
                        $consents = __('None logged', 'wordpress-gdpr');
                    } else {
                        $tmp = '';
                        foreach ($consents as $key => $value) {
                            $serviceKey = str_replace(array('wordpress_gdpr_', '_'), array('', ' '), $key);

                            if(isset($services[$serviceKey])) {
                                $tmp .= $services[$serviceKey]['name'] . ': ' . $value . '<br>';
                            } else {
                                $tmp .= $serviceKey . ': ' . $value . '<br>';
                            }
                        }
                        $consents = $tmp;
                    }

                    echo 
                    '<tr>
                        <td>' . $user_meta['nickname'][0] . '</td>
                        <td>' . $user_meta['first_name'][0] . ' ' . $user_meta['last_name'][0] . '</td>
                        <td>' . $user_data->data->user_email . '</td>
                        <td>' . $last_login . '</td>
                        <td>' . $consents . '</td>
                        <td>
                            <form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="get">
                                <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                                <input type="hidden" name="wordpress_gdpr[user_id]" value="' . $user_id . '">
                                <input type="submit" name="wordpress_gdpr[delete-data]" class="button" value="' . __('Delete Data', 'wordpress-gdpr') . '">
                            </form>
                            <form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="get">
                                <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                                <input type="hidden" name="wordpress_gdpr[user_id]" value="' . $user_id . '">
                                <input type="submit" name="wordpress_gdpr[request-data]" class="button" value="' . __('Export Data', 'wordpress-gdpr') . '">
                            </form>
                            <form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="get">
                                <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                                <input type="hidden" name="wordpress_gdpr[user_id]" value="' . $user_id . '">
                                <input type="submit" name="wordpress_gdpr[send-data]" class="button" value="' . __('Send Data', 'wordpress-gdpr') . '">
                            </form>
                        </td>
                    </tr>';
                }
            }

            if($this->get_option('consentLogLoggedOut')) {

                global $wpdb;
                $table_name = $wpdb->prefix . "gdpr_consent_log";

                $ipConsents = 
                $wpdb->get_results( 
                    $wpdb->prepare( "SELECT * FROM $table_name" ) 
                );

                if(!empty($ipConsents)) {
                    foreach($ipConsents as $ipConsent) {

                        $consents = maybe_unserialize( $ipConsent->consents );
                        if(!$consents || empty($consents) || !is_array($consents)) {
                            $consents = __('None logged', 'wordpress-gdpr');
                        } else {
                            $tmp = '';
                            foreach ($consents as $key => $value) {
                                $serviceKey = str_replace(array('wordpress_gdpr_', '_'), array('', ' '), $key);

                                if(isset($services[$serviceKey])) {
                                    $tmp .= $services[$serviceKey]['name'] . ': ' . $value . '<br>';
                                } else {
                                    $tmp .= $serviceKey . ': ' . $value . '<br>';
                                }
                            }
                            $consents = $tmp;
                        }

                        echo 
                        '<tr>
                            <td>' . $ipConsent->ip . '</td>
                            <td></td>
                            <td></td>
                            <td>N/A</td>
                            <td>' . $consents . '</td>
                            <td>
                                <form style="display: inline;" action="' . esc_url($_SERVER['REQUEST_URI']) . '" method="get">
                                    <input type="hidden" name="wordpress_gdpr[redirect]" value="' . esc_url($_SERVER['REQUEST_URI']) . '">
                                    <input type="hidden" name="wordpress_gdpr[user_id]" value="' . $ipConsent->ip . '">
                                    <input type="submit" name="wordpress_gdpr[request-data]" class="button" value="' . __('Export Data', 'wordpress-gdpr') . '">
                                </form>
                            </td>
                        </tr>';
                    }
                }
            }
            ?>
        </table>
        <?php
    }
}