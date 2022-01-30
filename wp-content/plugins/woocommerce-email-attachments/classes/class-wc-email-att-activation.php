<?php
/**
 * Handles activation and deactivation of this plugin
 * 
 * Creates the uploadfolder on activation and deletes it on deactivation
 * 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class WC_Email_Att_Activation
{
	const OPTIONNAME_210 = 'woocommerce_email_attachments';
	
	/**
	 * Holds the options for this plugin
	 * 
	 * @var array 
	 */
	var $options;

	public function __construct() 
	{
		$this->options = array();
	}
	
	public function __destruct() 
	{
		unset( $this->options );
	}
	
	/**
	 * Called, when Plugin activated.
	 * 
	 * Creates or updates the options to latest version
	 * 
	 * Creates folder for upload files and initialises options, if not present
	 */
	public function on_activate() 
	{	
		//	We need WC -> if WC is not active, do not allow to activate the plugin, because we cannot load the correct version (backward compatibility)
		if ( ! function_exists('is_plugin_active') ) 
		{
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if( ! is_plugin_active( 'woocommerce/woocommerce.php' ) )
		{
			deactivate_plugins( WC_Email_Att::$plugin_base_name );
			wp_die( __('<p>The plugin <strong>WooCommerce Email Attachments</strong> needs the plugin WooCommerce to be able to be activated. Please activate this plugin first. Plugin could not be activated.</p>', WC_Email_Att::TEXT_DOMAIN ), __( 'Plugin Activation Error', WC_Email_Att::TEXT_DOMAIN ),  array( 'response'=> 200, 'back_link' => TRUE ) );
		}
		
		//	See Documentation WP register_post_type
		WC_Email_Att::instance()->handler_wp_register_cpt_email_att();
		flush_rewrite_rules();
		
		$this->options = WC_Email_Att::get_options_default();
		
			//	test for previous versions, that need update - Optionname changed with 3.0.0
		$need_update = $this->check_for_update();
		
		//	Checks and creates new Upload Folder with fallback
		if( ! WC_Email_Att_Func::create_folder( $this->options['upload_folder'] ) )
		{
			$this->options['upload_folder'] = '/wc_email_attachment_uploads';
			update_option( WC_Email_Att::OPTIONNAME, $this->options );
			if( ! WC_Email_Att_Func::create_folder( $this->options['upload_folder'] ) )
			{
				deactivate_plugins( WC_Email_Att::$plugin_base_name );
				wp_die( __( '<p>The plugin <strong>WooCommerce Email Attachments</strong> encountered an error on creating the new upload folder. Try again or check your permissons or the permissons of Wordpress with your administrator. Plugin could not be activated.</p>', WC_Email_Att::TEXT_DOMAIN ), __( 'Plugin Activation Error', WC_Email_Att::TEXT_DOMAIN ),  array( 'response'=> 200, 'back_link' => TRUE ) );
			}
		}
				
		if( ! empty( $need_update ) )
		{
			update_option( WC_Email_Att::OPTIONNAME_UPDATE, $need_update );
		}
	}

	/**
	 * Checks for OPT_DEL_ON_DEACTIVATE -> removes option and all files and the last folder, if empty
	 */
	public function on_deactivate() 
	{
			//	allow depending plugins to react on deactivation
		do_action( 'wc_emai_att_before_deactivate' );
		
		$this->delete_message_options( WC_Email_Att::OPTIONNAME_NOTICE );
		$this->delete_message_options( WC_Email_Att::OPTIONNAME_UPDATE );
		
		$this->options = get_option( WC_Email_Att::OPTIONNAME, array() );
		
		//	fallback only
		if( empty( $this->options ) )
		{
			WC_Email_Att::instance()->delete_the_only_post();
			return;
		}
		
		//	fallback - default behaviour if not exist
		if( isset( $this->options['del_on_deactivate'] ) && $this->options['del_on_deactivate'] )
		{
			$this->delete_message_options( WC_Email_Att::OPTIONNAME_NOTICE );
			$this->delete_message_options( WC_Email_Att::OPTIONNAME_UPDATE );
			delete_option( WC_Email_Att::OPTIONNAME );
			WC_Email_Att::instance()->delete_the_only_post();
		}
		
   } 
   
   /**
	 * Checks for OPT_DEL_ON_UNINSTALL -> removes option and all files and the last folder, if empty
	 */
   public function on_uninstall()
   {
		$this->delete_message_options(WC_Email_Att::OPTIONNAME_NOTICE);
		$this->delete_message_options(WC_Email_Att::OPTIONNAME_UPDATE);
		
		$this->options = get_option( WC_Email_Att::OPTIONNAME, array() );
		
		//	already deleted on deactivation
		if( empty( $this->options ) )
		{
			WC_Email_Att::instance()->delete_the_only_post();
			
					//	fallback - delete option from old version also, but leave the files
			delete_option( 'woocommerce_email_attachments' );
			return;
		}
		
		//	fallback - default behaviour if not exist - Delete in any case to clean up database
		if( ! isset($this->options['del_on_uninstall'] ) || $this->options['del_on_uninstall'] )
		{
			delete_option( WC_Email_Att::OPTIONNAME );
			WC_Email_Att::instance()->delete_the_only_post();
			
					//	fallback - delete option from old version also, but leave the files
			delete_option( 'woocommerce_email_attachments' );
		}
   }
   
   /**
    * 
    * @param string $option_name
    */
   private function delete_message_options($option_name)
   {
	   $option = get_option( $option_name, array() );
		if( ! empty( $option ) )
		{
			delete_option( $option_name );
		}
   }

   /**
    * Checks, if an upgrade is necessary and returns the change array for upgrade display and doing the upgrade
    * 
    * Currently only upgrade from <= 2.1.0 is necessary
    * 
    * @return array
    */
   private function &check_for_update()
   {
		$change = array();
	   
		/**
		 * Optionname changed with 3.0.0.
		 */
		$option_210 = get_option( self::OPTIONNAME_210, array() );
		if( empty( $option_210 ) ) 
		{
			return $change;
		}
		
		/**
		 * Check for previous version -> Folder MUST NOT BE the same in case of upgrade to allow scan of files to make attachments
		 */
		if( isset( $option_210['upload_folder'] ) )
		{
			$new_path = str_replace( '\\', '/', WC_Email_Att_Func::get_full_upload_path( $this->options['upload_folder'] ) );
			$old_path = str_replace( '\\', '/', $option_210['upload_folder'] );

			if( strtolower( $new_path ) == strtolower( $old_path ) )
			{
				$this->options['upload_folder'] .= '_new';
				update_option( WC_Email_Att::OPTIONNAME, $this->options );
			}
			
			$change = array(
					'status' =>	'update_needed',
					'prev_version_found' => '210',
					'old_upload_folder' => $old_path
				);
		}
		return $change;
   }
	
}
