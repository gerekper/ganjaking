<?php
/**
 * Description of woocom_email_att_admin
 *
 * @author Guenter Schoenmann
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class WC_Email_Att_Admin 
{
	const TABID = "inoplugs_email";
	
	/**
	 *
	 * WooCommerce needed fields
	 */
	public $settings_tabs;
	public $current_tab;
	public $fields;
	
	/**
	 * Stores all actions for upgrading
	 * 
	 * @var array 	 
	 */
	public $update_actions;
	
	public function __construct() 
	{	
		$this->settings_tabs = array();
		$this->current_tab = '';
		$this->fields = array();
		
		$this->update_actions = array(
				'move_210' => 'update_woocom_email_att_210_move',
				'copy_210' => 'update_woocom_email_att_210_copy',
				'general_move_210' => 'update_woocom_email_att_210_general_move',
				'general_delete_210' => 'update_woocom_email_att_210_general_delete',
				'ignore_210' => 'update_woocom_email_att_210_ignore',
				'delete_files' => 'wc_email_att_delete_files',
				'upload_folder' => 'wc_email_att_upload_folder',
				'do_update' => 'wc_email_att_do_update'
			);
		
					//	on our option page only
		if( false !== strpos( $_SERVER['REQUEST_URI'], 'tab=inoplugs_email' ) )
		{
			add_action( 'init', array( $this, 'handler_wc_init'), 1000 );
		}
		
		add_filter( 'plugin_action_links_' . WC_Email_Att::$plugin_base_name, array( $this, 'handler_wp_plugin_action_links' ) );		
		add_action( 'admin_enqueue_scripts',  array( $this, 'handler_wp_admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'handler_wp_admin_init' ) );
		add_action( 'admin_init', array( $this, 'handler_wp_admin_message_action' ) );
		add_action( 'admin_notices', array( $this, 'handler_wp_admin_notices' ) );
		add_action( 'admin_print_styles', array( $this, 'handler_wp_admin_print_styles' ) );
			//	upload attachment files
		add_filter( 'upload_dir', array( $this, 'handler_wp_upload_dir' ), 10 , 1 );
			
		add_filter( 'woocommerce_screen_ids', array( $this, 'handler_wc_screen_ids' ), 10 , 1 );
					
		//	attach to WooCommerce settings page and order page hooks
		$this->attach_to_wc_settingspage();
		
		add_action( 'wp_ajax_wc_eatt_get_attachments', array( $this, 'handler_ajax_get_attachments' ) );
	}
	
	public function __destruct() 
	{
		unset( $this->settings_tabs );
		unset( $this->fields );
		unset( $this->update_actions );
	}
	
	/**
	 * Initialise data after textdomaine is loaded
	 */
	public function attach_to_wc_settingspage()
	{	
		$this->current_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';

		//	Add all tabs required
		$this->settings_tabs = array(
			WC_Email_Att_Admin::TABID => __( 'E-Mail Attachments', WC_Email_Att::TEXT_DOMAIN )
		);
		
		// Load in the new settings tabs and attach handler
		if( version_compare( WC()->version, '2.6.14', '<' ) )
		{
			add_action( 'woocommerce_settings_tabs', array( $this, 'handler_wc_add_settings_tab' ), 10 );
		}
		else
		{
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'handler_wc_add_settings_tab_array'), 30, 1 );
		}
		
		// Run these actions when generating the settings tabs - recognised as deprecated since 2.6.14
		foreach ( $this->settings_tabs as $name => $label ) 
		{
			if( version_compare( WC()->version, '2.6.14', '<' ) )
			{
				add_action( 'woocommerce_settings_tabs_' . $name, array( $this, 'handler_wc_get_settings_tab' ), 10 );
			}
			else
			{
	//			add_action( 'woocommerce_sections_' . $name, array( $this, 'handler_wc_get_sections' ), 10 );
				add_action( 'woocommerce_settings_' . $name, array( $this, 'handler_wc_get_settings_tab' ), 10 );
			}
			
			add_action( 'woocommerce_update_options_' . $name, array( $this, 'handler_wc_save_settings_tab' ), 10 );
		}
		
		//	add fields to tab on admin page
		add_action( 'woocommerce_email_attachment_settings', array( $this, 'handler_wc_add_settings_fields' ), 10 );
	}
	
	/**
	 * 
	 */
	public function handler_wp_admin_init()
	{
		wp_register_style( 'woocom_email_att_css', WC_Email_Att::$plugin_url . 'css/wc_email_att_admin.css' );
		
		wp_register_script( 'woocom_email_att_ips_email_checker', WC_Email_Att::$plugin_url . 'js/wc_ips_email_checker.js', array( 'jquery' ) );
		wp_register_script( 'woocom_email_att_implement_script', WC_Email_Att::$plugin_url . 'js/wc_email_attachments_admin.js', array( 'jquery', 'media-upload', 'jquery-tiptip', 'jquery-ui-sortable' ) );
	}
	
	/**
	 * Needed to load email classes in admin to adjust options
	 * 
	 * @since 3.0.11
	 */
	public function handler_wc_init()
	{
			//	fires wc_email_classes -> handler_wc_email_classes
		$wc_emails = WC_Emails::instance();
		unset( $wc_emails );
	}
	
	/**
	 * Add plugins to load tool tip plugin and style sheets
	 * 
	 * @param array $screen_ids
	 * @return array
	 */
	public function handler_wc_screen_ids( array $screen_ids )
	{
		$screen_ids[] = 'plugins';
		return $screen_ids;
	}

	/**
	 * 
	 */
	public function handler_wp_admin_enqueue_scripts()
	{
		if( isset( $_REQUEST['page'] ) && ( 'wc-settings' == $_REQUEST['page'] ) && isset( $_REQUEST['tab'] ) && ( self::TABID == $_REQUEST['tab'] ))
		{
			wp_enqueue_media();
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'woocom_email_att_ips_email_checker' );
			wp_enqueue_script( 'woocom_email_att_implement_script' );
		}
	}

	/**
	 * Add all styles to admin page
	 */
	public function handler_wp_admin_print_styles()
	{
		$urlpath = WC_Email_Att::$plugin_url;
		
		wp_enqueue_style( 'woocom_email_att_css' );
		
		$translate = array(
				'remove_file' => __( 'Do you want to remove this file from the attachment list?', WC_Email_Att::TEXT_DOMAIN ),
				'uploaded_for_attachment' => __( 'Uploaded for E-Mail attachment', WC_Email_Att::TEXT_DOMAIN ),
				'attachment_post_id' => WC_Email_Att::instance()->email_post->ID,
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				WC_Email_Att::AJAX_NONCE => wp_create_nonce( WC_Email_Att::AJAX_NONCE ),
				'alert_ajax_error' => __( 'Error occured in loading selected file info. Please try again. No files have been selected.', WC_Email_Att::TEXT_DOMAIN ),
				'ajax_loader_url' => $urlpath.'images/ajax-loader.gif',
				'alert_delete_files' => __( 'Are you sure, you want to delete all uploaded EMail attachment files permanently? This can take some time depending on the quantity of files to delete.', WC_Email_Att::TEXT_DOMAIN )
			);
		
		$translate_email_checker = array(
				'nostring' => __( 'E-Mail address is not a string.', WC_Email_Att::TEXT_DOMAIN ),
				'emptystring' => __( 'Empty E-Mail is not allowed.', WC_Email_Att::TEXT_DOMAIN ),
				'error1' => __( '"<" is not allowed in E-Mail address.', WC_Email_Att::TEXT_DOMAIN ),
				'error2' => __( 'Endtag ">" is missing in E-Mail address.', WC_Email_Att::TEXT_DOMAIN ),
				'invalid' => __( 'Invalid E-Mail address entered.', WC_Email_Att::TEXT_DOMAIN ),
				'emailonly' => __( 'Enter only the E-Mail address - not name and EM-ail address.', WC_Email_Att::TEXT_DOMAIN ),
				'oneaddressonly' => __( 'Only 1 E-Mail address is allowed to be entered.', WC_Email_Att::TEXT_DOMAIN ),
				'errorsfound' => __( 'Errors found in E-Mail addresses:', WC_Email_Att::TEXT_DOMAIN ),
				'marked' => __( 'Check for marked addresses.', WC_Email_Att::TEXT_DOMAIN )
			);
		
		wp_localize_script( 'woocom_email_att_implement_script', 'wc_email_attachments', $translate );
		wp_localize_script( 'woocom_email_att_ips_email_checker', 'wc_ips_email_checker_tx', $translate_email_checker );
	}
	
	/**
	 * Filter the directory for uploads.
	 * 
	 * @param array $pathdata
	 * @return array
	 */
	public function handler_wp_upload_dir( array $pathdata )
	{
		// Change upload dir for email attachment files
		if ( isset( $_POST['type'] ) && $_POST['type'] == 'wc_email_attachment_files' ) 
		{
						//		e.g.  "/wc_email_attachment_uploads"
			$new_path = WC_Email_Att::instance()->options['upload_folder'];
			
			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . $new_path;
				$pathdata['url']    = $pathdata['url']. $new_path;
				$pathdata['subdir'] = $new_path;
			} else {
				$new_subdir = $new_path;
				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}

		return $pathdata;
	}
	

	
	
	/**
	 * Gets the selected attachment files and returns it in <tr> for the settings page
	 */
	public function handler_ajax_get_attachments()
	{
		check_ajax_referer( WC_Email_Att::AJAX_NONCE, WC_Email_Att::AJAX_NONCE );
		
			// response output
		header( "Content-Type: application/json" );
		$response = array( WC_Email_Att::AJAX_NONCE => wp_create_nonce( WC_Email_Att::AJAX_NONCE) );
		
		$response ['alert'] = __( 'An error occured in loading attachment file info. Please try again. No files have been saved.', WC_Email_Att::TEXT_DOMAIN );
		
		$file_ids = isset( $_REQUEST['wc_eatt_att_ids'] ) ? $_REQUEST['wc_eatt_att_ids'] : array();
		$email_subject = isset( $_REQUEST['wc_eatt_subject'] ) ? $_REQUEST['wc_eatt_subject'] : '';
		$product_id = isset( $_REQUEST['wc_eatt_product_id'] ) ? $_REQUEST['wc_eatt_product_id'] : '';
		$new_attachments = isset( $_REQUEST['wc_eatt_new_att'] ) ? $_REQUEST['wc_eatt_new_att'] : false;
		
		//	fires wc_email_classes -> handler_wc_email_classes 
		$wc_emails = WC_Emails::instance();
		unset( $wc_emails );
		
		$panel = new WC_Email_Att_Panel_Admin( WC_Email_Att::$_instance->woo_addons );
		$response ['html'] = $panel->get_attachment_files_info( $file_ids, $email_subject, $product_id, $new_attachments );
		
		$response ['success'] = true;
		
		echo json_encode( $response );
		exit;
	}
	
	/**
	 * Add all tables
	 * 
	 * Recognised as deprecated since WC 2.6.14
	 */
	public function handler_wc_add_settings_tab() 
	{
		foreach ( $this->settings_tabs as $name => $label )
		{
			$class = 'nav-tab';
			if( $this->current_tab == $name ) $class .= ' nav-tab-active';
			echo '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $name ) . '" class="' . $class . '">' . $label . '</a>';
		}
	}
	
	/**
	 * Add all tabs
	 * 
	 * @param array $tabs 
	 */
	public function handler_wc_add_settings_tab_array( array $tabs ) 
	{
		foreach ( $this->settings_tabs as $name => $label )
		{
			$tabs[ $name ] = $label;
		}
		
		return $tabs;
	}
	
	/**
	 * Called when viewing our custom settings tab(s). One function for all tabs.
	 */
	public function handler_wc_get_settings_tab() 
	{
		global $woocommerce_settings;

		// Determine the current tab in effect.
		$this->current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_settings_tabs_' );

		// Hook onto this from another function to keep things clean.
		do_action( 'woocommerce_email_attachment_settings' );

		// Display settings for this tab (make sure to add the settings to the tab).
		woocommerce_admin_fields( $woocommerce_settings[ $this->current_tab ] );
	}		
	
	/**
	 * Add settings fields for each tab.
	 */
	public function handler_wc_add_settings_fields() 
	{
		global $woocommerce_settings;

		if(! WC_Email_Att::instance()->emailsubjects_init )
		{
			WC_Email_Att::instance()->handler_wc_init();
		}
		
		// Load the prepared form fields.
		$panel = new WC_Email_Att_Panel_Admin( WC_Email_Att::$_instance->woo_addons );
		$this->fields[ $this->current_tab ] = $panel->get_form_fields_settings_page();

		if ( is_array( $this->fields ) ) 
		{
			foreach ( $this->fields as $k => $v )
			{
				$woocommerce_settings[ $k ] = $v;
			}
		}
	}
	
	/**
	 * Woocommerce saves settings in a single field in the database for each option. 
	 * This does not apply for this plugin, we use our own structure and also handle
	 * initialising of form with stored values.
	 * 
	 * We ignore woocommere options handling
	 */
	public function handler_wc_save_settings_tab() 
	{				
//		global $woocommerce_settings;

		// Make sure our settings fields are recognised.
//		$this->add_settings_fields();

//		$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_update_options_' );
//		woocommerce_update_options( $woocommerce_settings[$current_tab] );
		
		//	save all data to own option
		// Load the prepared form fields.
		$panel = new WC_Email_Att_Panel_Admin( WC_Email_Att::$_instance->woo_addons );
		$panel->save_settings_page_options();
	}

	/**
	 * Get the tab current in view/processing.
	 * 
	 * @param string $current_filter
	 * @param string $filter_base
	 */
	protected function get_tab_in_view ( $current_filter, $filter_base ) 
	{
		return str_replace( $filter_base, '', $current_filter );
	}
	
	/**
	 * Show action links on the plugin screen
	 *
	 * @param mixed $links
	 * @return array
	 */
	public function handler_wp_plugin_action_links( array $links )
	{
		return array_merge( 
				array(
					'<a href="'.add_query_arg( 'tab', self::TABID, admin_url( 'admin.php?page=wc-settings' ) ) . '" class="wc_email_att_settings_page" title="' . __( 'Goto settingspage of this plugin', WC_Email_Att::TEXT_DOMAIN ) . '">' . __( 'Settings', WC_Email_Att::TEXT_DOMAIN ) . '</a>'
					),
				$links,
				array(
					'<a href="'.add_query_arg( 'wc_email_att_delete_files', 'true', admin_url( 'admin.php?page=wc-settings' ) ) . '" class="wc_email_att_delete_files" title="' . __( 'Delete all uploaded EMail attachment files permanently', WC_Email_Att::TEXT_DOMAIN ) . '">'. __( 'Delete attachment files', WC_Email_Att::TEXT_DOMAIN ) . '</a>'
					)  );
	}

	/**
	 * Shows messages on the admin page fitting standard WP-design.
	 * Scans all possible messages.
	 */
	public function handler_wp_admin_notices()
	{
		// Only show to admins
		if ( ! current_user_can('manage_options')) {  return;  }

		if( ! empty( WC_Email_Att::$admin_message ) )
		{
			$this->show_admin_notice_string( WC_Email_Att::$admin_message );
		}
		
		$notice_option = get_option( WC_Email_Att::OPTIONNAME_NOTICE, array() );
		if( ! empty( $notice_option ) )
		{
			$this->show_admin_notice_string( $notice_option['message'], $notice_option['error'] );
			delete_option( WC_Email_Att::OPTIONNAME_NOTICE );
		}
		
		$update_option = get_option( WC_Email_Att::OPTIONNAME_UPDATE, array() );
		if( ! empty( $update_option ) )
		{
			$this->show_admin_update( $update_option );
			return;
		}
		return;
	}
	
	/**
	 * 
	 * @param array $update_option
	 */
	protected function show_admin_update( array &$update_option )
	{
		if( $update_option['status'] == 'update_needed' )
		{
			$this->show_admin_update_needed( $update_option );
		}
		else if ( $update_option['status'] == 'update_done' )
		{
			$this->show_admin_notice_string( $update_option['message'], ! $update_option['success'] );
			delete_option( WC_Email_Att::OPTIONNAME_UPDATE );
		}
	}
	
	/**
	 * 
	 * @param array $update_option
	 */
	protected function show_admin_update_needed( array &$update_option )
	{
		$new_folder = WC_Email_Att_Func::get_full_upload_path( WC_Email_Att::instance()->options['upload_folder'] );
		
		$tip_move = __( 'All options and settings are copied. All the existing files are moved to be shown as E-Mail attachment files in the WP media manager and the source files and folder is deleted.', WC_Email_Att::TEXT_DOMAIN );
		$tip_copy = __( 'All options and settings are copied. All the existing files are copied to be shown as E-Mail attachment files in the WP media manager and leave the source files and folder. You can delete this folder manually.', WC_Email_Att::TEXT_DOMAIN );
		$tip_general_move = __( 'Only General settings are copied - You have to set all the the E-Mail specific settings and the file attachments manually again. All the existing files are moved to be shown as E-Mail attachment files in the WP media manager and the source files and folder is deleted.', WC_Email_Att::TEXT_DOMAIN );
		$tip_general_delete = __( 'Only General settings are copied - You have to set all the the E-Mail specific settings and the file attachments manually again. The old source files and folder are deleted and no longer available.', WC_Email_Att::TEXT_DOMAIN );
		$tip_ignore = __( 'Ignore all old settings, delete all old files and folder and start with the default settings.', WC_Email_Att::TEXT_DOMAIN );
		$tip_upload = __( 'Change to a new upload folder first and update data afterwards.', WC_Email_Att::TEXT_DOMAIN );
		$tip_execute = __( 'Start executing the selected task.', WC_Email_Att::TEXT_DOMAIN );
		
		echo '<div id="message" class="updated woocommerce-message wc-connect">';
		echo		__( '<strong>WooCommerce Email Attachment Data Update Required:</strong><br/><br/> The new version needs a new data and directory structure. The upload path will be changed:', WC_Email_Att::TEXT_DOMAIN ).'<br/><br/>';
		echo		__( '<strong>From:</strong>', WC_Email_Att::TEXT_DOMAIN ) . '  '.$update_option['old_upload_folder'].'<br/>';
		echo		__( '<strong>To:</strong>', WC_Email_Att::TEXT_DOMAIN ) . '  '.$new_folder.'<br/><br/>';
		echo		__( 'It is recommended to make a backup of your database and WordPress installation before proceeding.', WC_Email_Att::TEXT_DOMAIN ) . '<br/>';
		echo		__( 'You have the following possibilities with the existing old data and files:', WC_Email_Att::TEXT_DOMAIN );
		echo	'<div class="submit wc_email_att_submit">';
		echo		'<div class="wc_email_att_select_div">';
		echo			'<select id="update_woocom_email_att_select" name="update_woocom_email_att_select" size="1" class="wc_email_att_select_upgrade">';
		echo				'<option selected="selected" value="'.$this->update_actions['move_210'] . '">' . __( 'Move all files and option settings', WC_Email_Att::TEXT_DOMAIN ) . '</option>';
		echo				'<option value="' . $this->update_actions['copy_210'].'">' . __( 'Copy all files and option settings', WC_Email_Att::TEXT_DOMAIN ) . '</option>';
		echo				'<option value="' . $this->update_actions['general_move_210'] . '">' . __( 'Copy General Settings and move files', WC_Email_Att::TEXT_DOMAIN ) . '</option>';
		echo				'<option value="' . $this->update_actions['general_delete_210'] . '">' . __( 'Copy General Settings and delete files', WC_Email_Att::TEXT_DOMAIN ) . '</option>';
		echo				'<option value="' . $this->update_actions['ignore_210'].'">' . __( 'Ignore old settings and delete files', WC_Email_Att::TEXT_DOMAIN ) . '</option>';
		echo				'<option value="' . $this->update_actions['upload_folder'].'">' . __( 'Set new upload folder', WC_Email_Att::TEXT_DOMAIN ) . '</option>';
		echo			'</select>';
		echo		'</div>';
		echo		'<div href="'.add_query_arg( $this->update_actions['do_update'], 'true', admin_url('admin.php?page=wc-settings')).'" class="wc_email_att_update_now button-secondary tips" data-tip="' . $tip_execute . '">'; 
		echo			__( 'Execute Selection', WC_Email_Att::TEXT_DOMAIN );
		echo		'</div>';
		echo		'<div class="wc_email_att_select_div_tip">';
		echo			'<div class="wc_email_att_select_tips" tip_src="' . $this->update_actions['move_210'] . '">' . $tip_move . '</div>';
		echo			'<div class="wc_email_att_select_tips" tip_src="' . $this->update_actions['copy_210'] . '">' . $tip_copy . '</div>';
		echo			'<div class="wc_email_att_select_tips" tip_src="' . $this->update_actions['general_move_210'] . '">' . $tip_general_move . '</div>';
		echo			'<div class="wc_email_att_select_tips" tip_src="' . $this->update_actions['general_delete_210'] . '">' . $tip_general_delete . '</div>';
		echo			'<div class="wc_email_att_select_tips" tip_src="' . $this->update_actions['ignore_210'] . '">' . $tip_ignore . '</div>';
		echo			'<div class="wc_email_att_select_tips" tip_src="' . $this->update_actions['upload_folder'] . '">' . $tip_upload . '</div>';
		echo		'</div>';
		echo	'</div>';
		echo '</div>';
		
		$message = 'var answer = confirm(\'';
		$message .= __( 'It is recommended that you backup your database and files before proceeding. Are you sure you wish to run the updater now? This may take some time depending on the amount of files to copy.', WC_Email_Att::TEXT_DOMAIN );
		$message .= '\');';
				
		echo '<script type="text/javascript">';
		echo '	jQuery(".wc_email_att_update_now").on("click", function(){';
		echo		$message;
		echo '		if(!answer) return;';
		echo '		var sel = jQuery("#update_woocom_email_att_select").val();';
		echo '		var href = jQuery(".wc_email_att_update_now").attr("href");';
		echo '		window.location.href = href+"&update_woocom_email_att_select="+sel;';
		echo '		return;';
		echo '	});';
		echo '</script>';
	}
	
	/**
	 * Generic function to show a message to the user using WP's
	 * standard CSS classes to make use of the already-defined
	 * message colour scheme.
	 *
	 * @param string $message		The message you want to tell the user.
	 * @param bool $errormsg		If true, the message is an error, so use
	 *								the red message style. If false, the message is a status
	 *								message, so use the yellow information message style.
	 */
	protected function show_admin_notice_string( $message, $errormsg = true )
	{
		if ( $errormsg ) 
		{
			echo '<div id="message" class="error">';
		}
		else 
		{
			echo '<div id="message" class="updated fade">';
		}

		echo "<p><strong>$message</strong></p></div>";
	}    
	
	/**
	 * Reacts on buttons in the admin message area or plugin page by filtering query argument
	 */
	public function handler_wp_admin_message_action()
	{
		$action = '';
		foreach ( $this->update_actions as $action_key => $action_value ) 
		{
			if( isset( $_REQUEST[ $action_value ] ) )
			{
				$action = $action_key;
				break;
			}
		}
		
			//	standard routing of call
		if( empty( $action ) )  {  return;  }
		
		if( $action == 'delete_files' )
		{
			$this->delete_all_attachment_files();
			
			wp_redirect( admin_url( 'plugins.php' ) );
			exit;
		}
		
		if( $action != 'do_update' ) {  return;  }
		
		$selected_action = array_search( $_REQUEST['update_woocom_email_att_select'], $this->update_actions );
		
		if( $selected_action === false ) {  return;  }
		
		if( $selected_action == 'upload_folder' )
		{
			wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=inoplugs_email' ) );
			exit;
		}
		
		$upgrade = new WC_Email_Att_Upgrade( $this->update_actions, WC_Email_Att::$skip_files );
		$upgrade->do_upgrade( $selected_action );
		
			//	redirect to our settings page
		wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=inoplugs_email' ) );
		exit;
	}
	
	/**
	 * Deletes all the files attached to the only EMail attachment post
	 */
	protected function delete_all_attachment_files()
	{
		$args = array( 
			'post_type' => 'attachment', 
			'posts_per_page' => -1, 
			'post_status' =>'any', 
			'post_parent' => WC_Email_Att::instance()->email_post->ID ); 
		
		$attachments = get_posts( $args );
		
		$to_delete = count( $attachments );
		$deleted = 0;
		foreach ( $attachments as $key => $attachment ) 
		{
			if( false !== wp_delete_attachment( $attachment->ID, true ) )
			{
				$deleted++;
			}
		}

		if( $to_delete == $deleted )
		{
			$notice_option = array(
				'message' => sprintf( __( 'All %d attachment file(s) could be permanently deleted.', WC_Email_Att::TEXT_DOMAIN ), $to_delete ), 
				'error' => false
				);
		}
		else
		{
			$diff = $to_delete - $deleted;
			$notice_option = array(
				'message' => sprintf( __( '%d attachment file(s) could not be deleted, %d file(s) could be deleted.', WC_Email_Att::TEXT_DOMAIN ), $diff, $deleted ), 
				'error' => true
				);
		}
			
		update_option( WC_Email_Att::OPTIONNAME_NOTICE, $notice_option );
	}
}

