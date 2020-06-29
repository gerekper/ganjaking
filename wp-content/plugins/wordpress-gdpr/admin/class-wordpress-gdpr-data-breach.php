<?php

class WordPress_GDPR_Data_Breach extends WordPress_GDPR
{
    protected $plugin_name;
    protected $version;
    protected $options;

    /**
     * Store Locator Plugin Construct
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
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
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $wordpress_gdpr_options;

        $this->options = $wordpress_gdpr_options;

        if (!$this->get_option('enable')) {
            return false;
        }

        return true;
    }

    public function check_action()
    {
    	if(!isset($_GET['wordpress_gdpr']) || !is_admin()) {
    		return false;
		}

		if(!isset($_GET['wordpress_gdpr']['send-data-breach'])) {
			return false;
		}

        $from = $this->get_option('dataBreachEmail');
        $subject = $this->get_option('dataBreachSubject');

        $headers = array(
            'From: ' . $from . ' <' . $from . '>' . "\r\n",
            'Content-Type: text/html; charset=UTF-8'
        );

		$users = get_users();
		foreach ($users as $user) {
			$user_data = get_userdata($user->ID);
			
			if(empty($user_data->data->user_email) || !isset($user_data->data->user_email)) {
				continue;
			}
            $text = wpautop( sprintf( $this->get_option('dataBreachText'), $user_data->data->user_nicename) );
			wp_mail($user_data->data->user_email, $subject, $text, $headers);
		}
		
		wp_redirect( get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options' );
    }
}