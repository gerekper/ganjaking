<?php

class WordPress_GDPR_Data_Delete extends WordPress_GDPR
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
    	global $wpdb;
		$by_email = false;

    	if(isset($_POST['wordpress_gdpr_btn_form']) && $_POST['wordpress_gdpr_btn_form'] == "forget-me") {
    		$user_id = get_current_user_id();
    	} else {
    		if(!isset($_GET['wordpress_gdpr']['delete-data']) || !is_admin()) {
    			return false;
			}

			if(!isset($_GET['wordpress_gdpr']['user_id']) || empty($_GET['wordpress_gdpr']['user_id'])) {
				$by_email = true;
				$user_id = get_post_meta($_GET['wordpress_gdpr']['post_id'], 'gdpr_email', true);
			} else {
				$user_id = $_GET['wordpress_gdpr']['user_id'];
			}
		}

		if(!$user_id) {
			wp_die( __('No User ID set.', 'wordpress-gdpr'));
		}

		if($by_email) {		
			$this->delete_records_by_email($user_id);
		} else {
			$user_data = get_userdata($user_id);

			// Delete all Pages / Posts except Integration ones
			if($this->get_option('forgetMeDeletePosts')) {

	    		$post_types = get_post_types();
	    		if(isset($post_types['shop_order'])) {
	    			unset($post_types['shop_order']);
	    			unset($post_types['shop_order_refund']);
	    		}

	    		if(isset($post_types['flamingo_inbound'])) {
	    			unset($post_types['flamingo_inbound']);
	    			unset($post_types['flamingo_contact']);
				}

	    		foreach ($post_types as $post_type) {
					$args = array(
						'author'        =>  $user_id,
						'posts_per_page' => -1,
						'post_type' => $post_type
					);
					$current_user_posts = get_posts( $args );
					
					if(!empty($current_user_posts)) {
						foreach ($current_user_posts as $current_user_post) {
							wp_delete_post($current_user_post->ID, true);
						}
					}
	    		}
			}

			// Delete Quform Records
			if($this->get_option('integrationsQuformForgetMe')) {
				$wpdb->query("DELETE FROM {$wpdb->prefix}quform_entries WHERE created_by = " . $user_id);
			}

			// Delete Formidable Records
	        if($this->get_option('integrationsFormidableForgetMe')) {
	            $wpdb->query("DELETE FROM {$wpdb->prefix}frm_items WHERE user_id = " . $user_id);
	        }

			// Delete Flamingo Records
	        if($this->get_option('integrationsFlamingoDBForgetMe')) {
	            $wpdb->query(	"DELETE FROM {$wpdb->prefix}posts 
	            				WHERE (post_type = 'flamingo_inbound' OR post_type = 'flamingo_contact') 
	            				AND post_author = '" . $user_id . "'");
	        }

			// Delete Gravity Form Records
	        if($this->get_option('integrationsGravityFormsForgetMe')) {
				$wpdb->query("DELETE FROM {$wpdb->prefix}gf_entry WHERE created_by = " . $user_id);
	        }

			// Logout User
			$sessions = WP_Session_Tokens::get_instance($user_id);
			$sessions->destroy_all();

			// Delete comment with that user id
			if($this->get_option('forgetMeDeleteComments')) {
			    $comments = array_merge( get_comments('author_email=' . $user_data->data->user_email), get_comments('user_id=' . $user_id) );
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
	                'meta_value'    => $user_id
	            ));

				if ( ! empty( $user_orders ) ) {
		            foreach( $user_orders as $order ) {
		                wp_delete_post($order->ID,true);
	                }
				}
			}

			// Delete the User account
			$reassign = $this->get_option('forgetMeReassignUser');

			require_once(ABSPATH.'wp-admin/includes/user.php');
			$user_deleted = wp_delete_user($user_id, $reassign);
			
			if(!$user_deleted) {
				wp_die( __('User Data Not Deleted', 'wordpress-gdpr') );
				return false;
			}
			require_once(ABSPATH . '/wp-admin/includes/ms.php');
			wpmu_delete_user( $user_id );

			$this->delete_records_by_email($user_data->data->user_email);
		}

		$subject = $this->get_option('forgetMeSubject');
		$from = $this->get_option('contactDPOEmail');
		if($by_email) {
			$text = wpautop( sprintf( $this->get_option('forgetMeText'), $user_id) );
		} else {
			$text = wpautop( sprintf( $this->get_option('forgetMeText'), $user_data->data->user_nicename) );
		}

		$headers = array(
			'From: ' . $from . ' <' . $from . '>' . "\r\n",
			'Content-Type: text/html; charset=UTF-8'
		);

		if($by_email) {
			$mail_sent = wp_mail($user_id, $subject, $text, $headers);
		} else {
			$mail_sent = wp_mail($user_data->data->user_email, $subject, $text, $headers);
		}

		if($mail_sent) {
			if(isset($_GET['wordpress_gdpr']['post_id'])) {
				update_post_meta($_GET['wordpress_gdpr']['post_id'], 'gdpr_status', __('Data Deleted', 'wordpress-gdpr') );
			}
			wp_die( __('User Data Notification sent', 'wordpress-gdpr') );
		} else {
			wp_die( __('User Data Notification not sent', 'wordpress-gdpr') );
			return false;
		}	

		wp_redirect( $_GET['wordpress_gdpr']['redirect'] );
		exit;
    }

    public function delete_records_by_email($email)
    {
    	global $wpdb;

		// Delete Quform Records
		if($this->get_option('integrationsQuformForgetMe')) {

			$export_data['quform'] = $wpdb->get_results( "SELECT entry_id FROM {$wpdb->prefix}quform_entry_data WHERE value = '" . $email . "'", OBJECT );
			if(!empty($export_data['quform'])) {
				foreach ($export_data['quform'] as $tempp) {
					$entry_id = $tempp->entry_id;
					if(empty($entry_id)) {
						continue;
					}
					$wpdb->query("DELETE FROM {$wpdb->prefix}quform_entry_data WHERE entry_id = " . $entry_id);
					$wpdb->query("DELETE FROM {$wpdb->prefix}quform_entries WHERE id = " . $entry_id);
				}
			}
		}

		// Delete Formidable Records
		if($this->get_option('integrationsFormidableForgetMe')) {

			$export_data['formidable'] = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}frm_item_metas WHERE meta_value = " . $email, OBJECT );
			if(!empty($export_data['formidable'])) {
				foreach ($export_data['formidable'] as $tempp) {
					$item_id = $tempp->item_id;
					if(empty($item_id)) {
						continue;
					}
					$wpdb->query("DELETE FROM {$wpdb->prefix}frm_item_metas WHERE item_id = " . $item_id);
					$wpdb->query("DELETE FROM {$wpdb->prefix}frm_items WHERE id = " . $item_id);
				}
			}
		}

		// Delete Flamingo Records
        if($this->get_option('integrationsFlamingoDBForgetMe')) {
            $wpdb->query(	"DELETE FROM {$wpdb->prefix}posts 
            				WHERE (post_type = 'flamingo_inbound' OR post_type = 'flamingo_contact') 
            				AND post_content 
            				LIKE '%" . $email . "%'");
        }

		// Delete Gravity Form Records
        if($this->get_option('integrationsGravityFormsForgetMe')) {
			$export_data['gravityforms'] = $wpdb->get_results( "SELECT entry_id FROM {$wpdb->prefix}gf_entry_meta WHERE meta_value = '" . $email . "'", OBJECT );
			if(!empty($export_data['gravityforms'])) {
				foreach ($export_data['gravityforms'] as $tempp) {
					$entry_id = $tempp->entry_id;
					if(empty($entry_id)) {
						continue;
					}
					$wpdb->query("DELETE FROM {$wpdb->prefix}gf_entry_meta WHERE entry_id = " . $entry_id);
					$wpdb->query("DELETE FROM {$wpdb->prefix}gf_entry WHERE id = " . $entry_id);
				}
			}
        }

        if($this->get_option('integrationsWooCommerceForgetMe')) {
        	$orders = $wpdb->get_results( $wpdb->prepare( 
				'SELECT post_id FROM wp_postmeta 
				WHERE meta_key = "_billing_email" AND meta_value = %s', $email), OBJECT); 
        	if(!empty($orders)) {
        		foreach ($orders as $order) {
        			wp_delete_post($order->post_id, true);
        		}
        	}
    	}
        
        if($this->get_option('forgetMeDeleteComments')) {
	        $comments = get_comments('author_email=' . $email);
	        foreach ($comments as $comment) {
	        	wp_delete_comment($comment->comment_ID, true);
	        }
        }

        return true;
    }
}