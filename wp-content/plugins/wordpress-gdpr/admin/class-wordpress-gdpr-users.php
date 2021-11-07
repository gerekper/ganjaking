<?php

class WordPress_GDPR_Users extends WordPress_GDPR
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
     * Init Reports Page in Admin
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
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
	    					$tmp .= str_replace(array('wordpress_gdpr_', '_'), array('', ' '), $key) . ': ' . $value . '<br>';
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
	    	?>
		</table>
    	<?php
    }
}