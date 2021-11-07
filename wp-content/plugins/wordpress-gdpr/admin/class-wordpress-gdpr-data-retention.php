<?php

class WordPress_GDPR_Data_Retention extends WordPress_GDPR
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

    /**
     * Update Last Logged in User Meta on Login
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $login [description]
     * @param   [type]                       $user  [description]
     * @return  [type]                              [description]
     */
	public function update_last_logged_in($login) 
	{
	    $user = get_user_by('login', $login);
	    $time = current_time( 'timestamp' );

	    $last_login = get_user_meta( $user->ID, 'wordpress_gdpr_last_login', 'true' );
	    update_user_meta( $user->ID, 'wordpress_gdpr_last_login', $time );
	}

    /**
     * Get Last Logged in User Meta
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $login [description]
     * @param   [type]                       $user  [description]
     * @return  [type]                              [description]
     */
	public function get_last_logged_in($user_id,$prev=null)
	{
		$last_login = get_user_meta( $user_id, 'wordpress_gdpr_last_login', true );
		return $last_login;
	}

    /**
     * [maybe_delete_old_users description]
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
	public function maybe_delete_old_users()
	{
        if (!$this->get_option('dataRetentionEnable')) {
            return false;
        }
        
        if(!is_user_logged_in() || !is_admin()){
            return false;
        }

        $wordpress_gdpr_check_retention = get_transient( 'wordpress_gdpr_check_retention' );
        if ( $wordpress_gdpr_check_retention == "true" ) {
             return false;
        }

        require_once(ABSPATH.'wp-admin/includes/user.php');
        require_once(ABSPATH . '/wp-admin/includes/ms.php');

		$now = time();
        $daysUntilRetention = $this->get_option('dataRetentionDays');

        $users = get_users();
        foreach ($users as $user) {
            $last_login = get_user_meta( $user->ID, 'wordpress_gdpr_last_login', true );
            
            if(!$last_login || empty($last_login)) {
                continue;
            }

            $timeUntilRetention = $last_login + ($daysUntilRetention * 60*60*24);
            if($now > $timeUntilRetention) {


                // Delete comment with that user id
                if($this->get_option('forgetMeDeleteComments')) {
                    $comments = get_comments('user_id=' . $user->ID);
                    foreach($comments as $comment) {
                        wp_delete_comment($comment->comment_ID, true);
                    }
                }

                // Delete Orders
                if($this->get_option('integrationsWooCommerceForgetMe')) {

                    $user_orders = get_posts( array(
                        'post_type'     => wc_get_order_types(),
                        'post_status'   => 'any',
                        'numberposts'   => -1,
                        'meta_key'      => '_customer_user',
                        'meta_value'    => $user->ID
                    ));

                    if ( ! empty( $user_orders ) ) {
                        foreach( $user_orders as $order ) {
                            wp_delete_post($order->ID,true);
                        }
                    }
                }

                // Delete User
                wp_delete_user($user->ID, $reassign);
                wpmu_delete_user( $user->ID );
            }
        }
	}

    /**
     * [check_action description]
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function check_action()
    {
    	if(!isset($_GET['wordpress_gdpr']) || !is_admin()) {
    		return false;
		}

		if(!isset($_GET['wordpress_gdpr']['update-last-logged-in'])) {
			return false;
		}

        if (!$this->get_option('dataRetentionEnable')) {
            wp_die( __('Data Retention disabled', 'wordpress-gdpr') );
        }

		$users = get_users();
		$time = current_time( 'timestamp' );
		foreach ($users as $user) {
			$last_login = get_user_meta( $user->ID, 'wordpress_gdpr_last_login', 'true' );

			if(!$last_login || empty($last_login)) {
			    update_user_meta( $user->ID, 'wordpress_gdpr_last_login', $time );
			}
		}
		
		wp_redirect( get_admin_url() . 'admin.php?page=wordpress_gdpr_options_options' );
	}
}