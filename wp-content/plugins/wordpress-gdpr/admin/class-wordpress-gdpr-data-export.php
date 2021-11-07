<?php

class WordPress_GDPR_Data_Export extends WordPress_GDPR
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
     * Check if Export Action Triggered
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function check_action()
    {
    	$by_email = false;
    	if(isset($_POST['wordpress_gdpr_btn_form']) && $_POST['wordpress_gdpr_btn_form'] == "request-data") {
    		$user_id = get_current_user_id();
    		$action = 'request-data';
    	} else {
    		if(!isset($_GET['wordpress_gdpr']) || !is_admin()) {
    			return false;
			}

			if(!isset($_GET['wordpress_gdpr']['user_id']) || empty($_GET['wordpress_gdpr']['user_id'])) {
				$by_email = true;
				$user_id = get_post_meta($_GET['wordpress_gdpr']['post_id'], 'gdpr_email', true);
			} else {
				$user_id = $_GET['wordpress_gdpr']['user_id'];
			}

			if(!isset($_GET['wordpress_gdpr']['request-data']) && !isset($_GET['wordpress_gdpr']['send-data'])) {
				return false;
			}
			
			if(isset($_GET['wordpress_gdpr']['request-data'])) {
				$action = 'request-data';
			} else {
				$action = 'send-data';
			}
		}

		if(!$user_id) {
			wp_die( __('No User ID set.', 'wordpress-gdpr'));
		}

		if($by_email) {
			$export_data = $this->get_user_data_by_email($user_id);
		} else {
			$export_data = $this->get_user_data($user_id);
		}

		if ($this->is_array_empty($export_data)){
			wp_die( __('No Data for Export found.'), 'wordpress-gdpr');
		}

		// Export in Browser
		if($action == "request-data") {
			if(isset($_GET['wordpress_gdpr']['post_id'])) {
				update_post_meta($_GET['wordpress_gdpr']['post_id'], 'gdpr_status', __('Data Exported Manually', 'wordpress-gdpr') );
			}

			if($this->get_option('requestDataAsTable')) {
				$json_data = json_encode($export_data, JSON_PRETTY_PRINT);
				$exportTable = $this->jsonToDebug($json_data);				
				header('Content-disposition: attachment; filename=user_export_' . $user_id . '.html');
	    		header('Content-Type: application/html');
	    		echo $exportTable;
			} else {
	    		header('Content-disposition: attachment; filename=user_export_' . $user_id . '.html');
	    		header('Content-Type: application/html');
	    		echo json_encode($export_data, JSON_PRETTY_PRINT);
			}
    		die();
		}

		// Send Data
		if($action == "send-data") {

			$subject = $this->get_option('requestDataSubject');
			$from = $this->get_option('contactDPOEmail');

			if($by_email) {
				$text = wpautop( sprintf( $this->get_option('requestDataText'), $user_id) );
			} else {
				$text = wpautop( sprintf( $this->get_option('requestDataText'), $export_data['user_data']->data->user_nicename) );
			}
			$upload_dir = WP_CONTENT_DIR . '/gdpr-exports';

			$create_folder = wp_mkdir_p($upload_dir);
			if(!$create_folder) {
				wp_die( __('Could not create export folder: ' . $upload_dir, 'wordpress-gdpr') );
				return false;
			}

			if($this->get_option('requestDataAsTable')) {
				$file = $upload_dir . '/user_export_' . $user_id . '.html';
				$json_data = json_encode($export_data, JSON_PRETTY_PRINT);
				$exportTable = $this->jsonToDebug($json_data);		

				$file_saved = file_put_contents($file, $exportTable);
				if(!$file_saved) {
					wp_die( __('Could not create export file: ' . $file, 'wordpress-gdpr') );
					return false;
				}
			} else {
				$file = $upload_dir . '/user_export_' . $user_id . '.json';
				$file_saved = file_put_contents($file, json_encode($export_data, JSON_PRETTY_PRINT));
				if(!$file_saved) {
					wp_die( __('Could not create export file: ' . $file, 'wordpress-gdpr') );
					return false;
				}
			}
			
			$attachments = array($file);

			$headers = array(
				'From: ' . $from . ' <' . $from . '>' . "\r\n",
				'Content-Type: text/html; charset=UTF-8'
			);

			if($by_email) {
				$mail_sent = wp_mail($user_id, $subject, $text, $headers, $attachments);
			} else {
				$mail_sent = wp_mail($export_data['user_data']->data->user_email, $subject, $text, $headers, $attachments);
			}

			if($mail_sent) {
				unlink($file);
				if(isset($_GET['wordpress_gdpr']['post_id'])) {
					update_post_meta($_GET['wordpress_gdpr']['post_id'], 'gdpr_status', __('Data Sent', 'wordpress-gdpr') );
				}
			} else {
				wp_die( __('Export Data could not be mailed to user', 'wordpress-gdpr') );
				return false;
			}	
		}

		wp_redirect( $_GET['wordpress_gdpr']['redirect'] );
		exit;
    }

    /**
     * Get User Data for Export
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $user_id [description]
     * @return  [type]                                [description]
     */
	public function get_user_data($user_id)
	{
		global $wpdb;

		$export_data = array();
		$export_data['user_data'] 		= get_userdata($user_id);
		$export_data['user_meta'] 		= get_user_meta( $user_id );
		$export_data['user_comments'] 	= get_comments( array('author__in' => array($user_id) ) );

		if($this->get_option('integrationsQuform')) {
			$export_data['quform'] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}quform_entries WHERE created_by = '" . $user_id . "'", OBJECT );
		}

        if($this->get_option('integrationsFormidable')) {
			$export_data['formidable'] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}frm_items WHERE user_id = '" . $user_id . "'", OBJECT );
        }

        if($this->get_option('integrationsGravityForms')) {
			$export_data['gravityforms'] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}gf_entry WHERE created_by = '" . $user_id . "'", OBJECT );
        }

		$export_data = apply_filters('wordpress_gdpr_export_data', $export_data);

		if(class_exists('WooCommerce')) {
			$export_data['user_orders'] = get_posts( array(
									        'numberposts' => -1,
									        'meta_key'    => '_customer_user',
									        'meta_value'  => $user_id,
									        'post_type'   => wc_get_order_types(),
									        'post_status' => array_keys( wc_get_order_statuses() ),
									    ) );
		}

		return $export_data;
	}

    /**
     * Get User Data for Export
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $user_id [description]
     * @return  [type]                                [description]
     */
	public function get_user_data_by_email($email)
	{
		global $wpdb;

		$export_data = array();
		$export_data['user_comments'] 	= get_comments('author_email=' . $email);

		if($this->get_option('integrationsQuform')) {
			$export_data['quform'] = $wpdb->get_results( "SELECT entry_id FROM {$wpdb->prefix}quform_entry_data WHERE value = '" . $email . "'", OBJECT );
			if(!empty($export_data['quform'])) {
				$temp = array();
				$temp['quform_entry_data'] = array();
				$temp['quform_entries'] = array();

				foreach ($export_data['quform'] as $tempp) {
					$entry_id = $tempp->entry_id;
					if(empty($entry_id)) {
						continue;
					}
					$temp['quform_entry_data'][] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}quform_entry_data WHERE entry_id = '" . $entry_id . "'", OBJECT );
					$temp['quform_entries'][] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}quform_entries WHERE id = '" . $entry_id . "'", OBJECT );
				}
			}
			$export_data['quform'] = $temp;
		}

		if($this->get_option('integrationsFlamingoDB')) {
			$export_data['flamingodb'] = $wpdb->get_results( 	"SELECT * FROM {$wpdb->prefix}posts 
																WHERE post_content LIKE '%" . $email . "%' AND post_type IN('flamingo_inbound', 'flamingo_contact')", OBJECT );
		}

        if($this->get_option('integrationsFormidable')) {
			$export_data['formidable'] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}frm_item_metas WHERE meta_value = " . $email, OBJECT );
			if(!empty($export_data['formidable'])) {
				$temp = array();
				$temp['formidable_entry_data'] = array();
				$temp['formidable_entries'] = array();

				foreach ($export_data['formidable'] as $tempp) {
					$item_id = $tempp->item_id;
					if(empty($item_id)) {
						continue;
					}
					$temp['formidable_entry_data'][] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}frm_item_metas WHERE item_id = '" . $item_id . "'", OBJECT );
					$temp['formidable_entries'][] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}frm_items WHERE id = '" . $item_id . "'", OBJECT );
				}
			}
			$export_data['formidable'] = $temp;
			
        }

        if($this->get_option('integrationsGravityForms')) {
			$export_data['gravityforms'] = $wpdb->get_results( "SELECT entry_id FROM {$wpdb->prefix}gf_entry_meta WHERE meta_value = '" . $email . "'", OBJECT );
			if(!empty($export_data['gravityforms'])) {
				$temp = array();
				$temp['gravityforms_entry_data'] = array();
				$temp['gravityforms_entries'] = array();

				foreach ($export_data['gravityforms'] as $tempp) {
					$entry_id = $tempp->entry_id;
					if(empty($entry_id)) {
						continue;
					}
					$temp['gravityforms_entry_data'][] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id = '" . $entry_id . "'", OBJECT );
					$temp['gravityforms_entries'][] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}gf_entry WHERE id = '" . $entry_id . "'", OBJECT );
				}
			}
			$export_data['gravityforms'] = $temp;
        }

		$export_data = apply_filters('wordpress_gdpr_export_data', $export_data);

		if(class_exists('WooCommerce')) {
			$export_data['user_orders'] = $wpdb->get_results( $wpdb->prepare( 
				'SELECT post_id FROM wp_postmeta 
				WHERE meta_key = "_billing_email" AND meta_value = %s', $email)); 
		}

		return $export_data;
	}

	/**
	 * Decode JSON to Array
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://plugins.db-dzine.com
	 * @param   string                       $jsonText [description]
	 * @return  [type]                                 [description]
	 */
	public static function jsonToDebug($jsonText = '')
	{
	    $arr = json_decode($jsonText, true);
	    $html = "";
	    if ($arr && is_array($arr)) {
	        $html .= self::_arrayToHtmlTableRecursive($arr);
	    }
	    return $html;
	}

	/**
	 * Array to HTML Table
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://plugins.db-dzine.com
	 * @param   [type]                       $arr [description]
	 * @return  [type]                            [description]
	 */
	private static function _arrayToHtmlTableRecursive($arr) 
	{
	    $str = "<table border='1' valign='top'><tbody>";
	    foreach ($arr as $key => $val) {
	        $str .= "<tr>";
	        $str .= "<td>$key</td>";
	        $str .= "<td>";
	        if (is_array($val)) {
	            if (!empty($val)) {
	                $str .= self::_arrayToHtmlTableRecursive($val);
	            }
	        } else {
	            $str .= "<strong>$val</strong>";
	        }
	        $str .= "</td></tr>";
	    }
	    $str .= "</tbody></table>";

	    return $str;
	}

	/**
	 * [is_array_empty description]
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://plugins.db-dzine.com
	 * @param   [type]                       $InputVariable [description]
	 * @return  boolean                                     [description]
	 */
	private function is_array_empty($InputVariable)
	{
	   $Result = true;

	   if (is_array($InputVariable) && count($InputVariable) > 0)
	   {
	      foreach ($InputVariable as $Value)
	      {
	         $Result = $Result && $this->is_array_empty($Value);
	      }
	   }
	   else
	   {
	      $Result = empty($InputVariable);
	   }

	   return $Result;
	}
}