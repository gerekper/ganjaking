<?php
/**
 * Handles upgrading of versions
 * 
 * Currently only to < 3.0.0
 *
 * @author Guenter Schoenmann
 * @since 3.0.0.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class WC_Email_Att_Upgrade 
{
	/**
	 * All possible upgade keys
	 * 
	 * @var array 
	 */
	public $update_actions;
	
	/**
	 * Requested upgrade
	 * 
	 * @var string 
	 */
	public $action;
	
	
	/**
	 *
	 * @var array
	 */
	public $options_old;
	
	/**
	 *
	 * @var array
	 */
	public $options_new;
	
	/**
	 * [complete filepath to old directory] => attachment ID
	 * 
	 * @var array
	 */
	public $attachment_files;
	
	/**
	 * Error - Files could not be moved to new destination and not inserted as attachment
	 * 
	 * @var array
	 */
	public $attachments_not_moved;
	
	/**
	 * Filenames to skip on displaying files for attachment
	 * 
	 * @var array
	 */
	public $skip_files;
	
	/**
	 * 
	 * @param array $update_actions
	 */
	public function __construct(array $update_actions, array $skip_files)
	{
		$this->update_actions = $update_actions;
		$this->skip_files = $skip_files;
		
		$this->options_old = array();
		$this->options_new = array();
		$this->attachment_files = array();
		$this->attachments_not_moved = array();
	}
	
	public function __destruct() 
	{
		unset ($this->update_actions);
		unset ($this->skip_files);
		
		unset ($this->options_old);
		unset ($this->options_new);
		unset ($this->attachment_files);
		unset ($this->attachments_not_moved);
	}
	
	/**
	 * Performs the requested upgrade.
	 * 
	 * @param string $action
	 */
	public function do_upgrade($action)
	{
		$this->action = $action;
		
		$success = true;
		$msg = __( 'Upgrading of data and files successfull:', WC_Email_Att::TEXT_DOMAIN ).'  ';
		switch ($action)
		{
			case 'move_210':
				$this->init_data_210();
				$this->copy_general_settings_210();
				$this->generate_attachment_files_210();
				$this->copy_email_header_210();
				$this->copy_email_attachment_files_210();
				$this->delete_old_files_210();
				$this->delete_option_210();
				$msg .= __( 'Move all files and copy option settings', WC_Email_Att::TEXT_DOMAIN );
				break;
			case 'copy_210':
				$this->init_data_210();
				$this->copy_general_settings_210();
				$this->generate_attachment_files_210();
				$this->copy_email_header_210();
				$this->copy_email_attachment_files_210();
				$this->delete_option_210();
				$msg .= __( 'Copy all files and copy option settings', WC_Email_Att::TEXT_DOMAIN );
				break;
			case 'general_move_210':
				$this->init_data_210();
				$this->copy_general_settings_210();
				$this->generate_attachment_files_210();
				$this->delete_old_files_210();
				$this->delete_option_210();
				$msg .= __( 'Copy General Settings and move files', WC_Email_Att::TEXT_DOMAIN );
				break;
			case 'general_delete_210':
				$this->init_data_210();
				$this->copy_general_settings_210();
				$this->delete_old_files_210();
				$this->delete_option_210();
				$msg .= __( 'Copy General Settings and delete files', WC_Email_Att::TEXT_DOMAIN );
				break;
			case 'ignore_210':
				$this->init_data_210();
				$this->delete_old_files_210();
				$this->delete_option_210();
				$msg .= __( 'Ignore all old settings and delete files', WC_Email_Att::TEXT_DOMAIN );
				break;
			default:
				$msg = __( 'Illegal parameter for upgrading. No changes are made and default settings are kept. Please report this internal error to plugin author: action = ', WC_Email_Att::TEXT_DOMAIN ).$action;
				$success = false;
				break;	
		}
		
		$upgrade = array(
					'status' => 'update_done',
					'prev_version_found' => '210',
					'message' => $msg,
					'success' => $success,
					'move_error' => $this->attachments_not_moved
				);
		
		update_option(WC_Email_Att::OPTIONNAME, $this->options_new);
		update_option(WC_Email_Att::OPTIONNAME_UPDATE, $upgrade);
	}
	
	/**
	 * 
	 */
	protected function init_data_210()
	{
		$this->options_new = WC_Email_Att::get_options_default();
		$this->options_old = $this->get_options_default_210 ();
	}
	
	/**
	 * 
	 */
	protected function copy_general_settings_210()
	{
		$this->options_new['version'] = WC_Email_Att::VERSION;
		$this->options_new['del_on_deactivate'] = $this->options_old['delete_on_deactivate'];
		$this->options_new['del_on_uninstall'] = $this->options_old['delete_on_uninstall'];
//		$this->options_new['upload_folder'] = $this->options_old['upload_folder'];		is kept from new options !!!!!!
		$this->options_new['notification_headline'] = $this->options_old['notification_headline'];
		$this->options_new['notification_text'] = $this->options_old['notification_text'];
	}
	
	/**
	 * 
	 */
	protected function generate_attachment_files_210()
	{
		$filenames = WC_Email_Att_Func::get_all_files($this->options_old['upload_folder'], $this->skip_files);
		
		foreach ($filenames as $filename) 
		{
			$file = trailingslashit($this->options_old['upload_folder']).$filename;
			$attachment_id = $this->add_attachment($file, $filename);
			if($attachment_id > 0)
			{
				$this->attachment_files[$filename] = $attachment_id;
			}
			else
			{
				$this->attachments_not_moved[] = $file;
			}
		}		
	}	
	
	/**
	 * 
	 */
	protected function copy_email_header_210()
	{
		$this->options_new['head_new_order'] = $this->options_old['headlines_new_order'];
		$this->options_new['head_customer_processing_order'] = $this->options_old['headlines_processing'];
		$this->options_new['head_customer_completed_order'] = $this->options_old['headlines_completed'];
		$this->options_new['head_customer_invoice'] = $this->options_old['headlines_invoice'];
		$this->options_new['head_customer_note'] = $this->options_old['headlines_note'];
		$this->options_new['head_customer_new_account'] = $this->options_old['headlines_new_account'];
//		$this->options_new['head_customer_reset_password'] = $this->options_old['xxxxxx'];			was not supported
		$this->options_new['head_low_stock'] = $this->options_old['headlines_low_stock'];
		$this->options_new['head_no_stock'] = $this->options_old['headlines_no_stock'];
		$this->options_new['head_backorder'] = $this->options_old['headlines_backorder'];
	}
	
	/**
	 * 
	 */
	protected function copy_email_attachment_files_210()
	{		
		foreach (WC_Email_Att::instance()->emailsubjects as $email_key => $value) 
		{
			$this->options_new['att_'.$email_key] = array();
			
			switch ($email_key)
			{
				case 'new_order':
					$opt_name = 'attachment_new_order';
					break;
				case 'customer_processing_order':
					$opt_name = 'attachment_processing';
					break;
				case 'customer_completed_order':
					$opt_name = 'attachment_completed';
					break;
				case 'customer_invoice':
					$opt_name = 'attachment_invoice';
					break;
				case 'customer_note':
					$opt_name = 'attachment_note';
					break;
				case 'customer_new_account':
					$opt_name = 'attachment_new_account';
					break;
				case 'low_stock':
					$opt_name = 'attachment_low_stock';
					break;
				case 'no_stock':
					$opt_name = 'attachment_no_stock';
					break;
				case 'backorder':
					$opt_name = 'attachment_backorder';
					break;
				default:
					continue;
			}
		
			foreach ( $this->options_old[$opt_name] as $file) 
			{
					//	in case, file has been deleted !!!
				if(isset($this->attachment_files[$file]))
				{
					$this->options_new['att_'.$email_key][] = $this->attachment_files[$file];
				}
			}
		}
	}
	
	/**
	 * 
	 */
	protected function delete_old_files_210()
	{
		WC_Email_Att_Func::remove_folder($this->options_old['upload_folder']);	
	}
	
	/**
	 * 
	 */
	protected function delete_option_210()
	{
		delete_option(WC_Email_Att_Activation::OPTIONNAME_210);
	}

	/**
	 * Returns an options array valid up to version 2.1.0 to avoid isset(...) when copying option values to new option array
	 * 
	 * @return array
	 */
	protected function &get_options_default_210()
	{
		$default = array();
	
		$default['version'] = '2.1.0';
		$default['delete_on_deactivate'] = false;
		$default['delete_on_uninstall'] = true;
		$default['upload_folder'] = WC_Email_Att_Func::get_full_upload_path('woocommerce_email_attachments');
		
		$default['headlines_new_order'] = array('cc' => '', 'bcc' => '');
		$default['headlines_processing'] = array('cc' => '', 'bcc' => '');
		$default['headlines_completed'] = array('cc' => '', 'bcc' => '');
		$default['headlines_invoice'] = array('cc' => '', 'bcc' => '');
		$default['headlines_note'] = array('cc' => '', 'bcc' => '');
		$default['headlines_low_stock'] = array('cc' => '', 'bcc' => '');
		$default['headlines_no_stock'] = array('cc' => '', 'bcc' => '');
		$default['headlines_backorder'] = array('cc' => '', 'bcc' => '');
		$default['headlines_new_account'] = array('cc' => '', 'bcc' => '');
		
		$default['attachment_new_order'] = array();
		$default['attachment_processing'] = array();
		$default['attachment_completed'] = array();
		$default['attachment_invoice'] = array();
		$default['attachment_note'] = array();
		$default['attachment_low_stock'] = array();
		$default['attachment_no_stock'] = array();
		$default['attachment_backorder'] = array();
		$default['attachment_new_account'] = array();
		
		$default['notification_headline'] = '';
		$default['notification_text'] = '';
		
		$options = get_option(WC_Email_Att_Activation::OPTIONNAME_210, array());
		$new_options = wp_parse_args($options, $default);
		
		return $new_options;
	}
	
	/**
	 * 
	 * @param string $file
	 * @param string $filename
	 * @return int
	 */
	protected function add_attachment($file, $filename)
	{
		$dest_file = trailingslashit(WC_Email_Att_Func::get_full_upload_path($this->options_new['upload_folder'])).$filename;
		
		if(file_exists($dest_file))
		{
			$unique = uniqid('_');
			$path_parts = pathinfo($filename);
			$filename = $path_parts['filename'].$unique.'.'.$path_parts['extension'];
			$dest_file = trailingslashit(WC_Email_Att_Func::get_full_upload_path($this->options_new['upload_folder'])).$filename;
		}
		
		if(!copy($file, $dest_file))
		{
			return 0;
		}
		
		$parent_post_id = WC_Email_Att::instance()->email_post->ID;

		// Check the type of tile. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $dest_file ), null );
		
		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();
		
		// Prepare an array of post data for the attachment.
		$attachment = array(
				'guid'           => $wp_upload_dir['baseurl'].trailingslashit($this->options_new['upload_folder']).$filename, 
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $dest_file ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
		
		// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $dest_file, $parent_post_id );

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $dest_file );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		
		return $attach_id;
	}
	
}

