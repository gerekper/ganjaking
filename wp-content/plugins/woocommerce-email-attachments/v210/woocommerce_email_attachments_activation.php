<?php
/**
 * Handles activation and deactivation of this plugin
 * 
 * Creates the uploadfolder on activation and deletes it on deactivation
 * 
 */
class woocommerce_email_attachments_activation
{
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
		unset ($this->options);
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
		$found = get_option(woocommerce_email_attachments::OPTIONNAME);
		
		if(($found === false) || (!is_array($found)))
		{
			$this->options = self::init_default_options();
			add_option(woocommerce_email_attachments::OPTIONNAME, $this->options);
		}
		else
		{
			if(version_compare($found[woocommerce_email_attachments::OPT_VERSION], woocommerce_email_attachments::VERSION, '!='))
			{
				$this->options = self::update_options_version($found);
				update_option(woocommerce_email_attachments::OPTIONNAME, $this->options);
			}
			else
			{
				$this->options = $found;
			}
		}
		
		//	Checks and creates Upload Folder
		self::check_upload_folder($this->options);
		
		woocommerce_email_attachments::$admin_message = 'Activation successfull';
	}

	/**
	 * Checks for OPT_DEL_ON_DEACTIVATE -> removes option and all files and the last folder, if empty
	 */
	public function on_deactivate() 
	{
		woocommerce_email_attachments::$admin_message = 'Deactivation successfull';
		
		$this->options = get_option(woocommerce_email_attachments::OPTIONNAME, array());
		
		//	fallback only
		if(empty($this->options))
		{
			return;
		}
		
		//	fallback - Delete in any case to clean up database
		if(!isset($this->options[woocommerce_email_attachments::OPT_DEL_ON_DEACTIVATE]) || 
				$this->options[woocommerce_email_attachments::OPT_DEL_ON_DEACTIVATE])
		{
			$this->delete_files();
			delete_option(woocommerce_email_attachments::OPTIONNAME);
		}
		
   } 
   
   /**
	 * Checks for OPT_DEL_ON_UNINSTALL -> removes option and all files and the last folder, if empty
	 */
   public function on_uninstall()
   {
	   woocommerce_email_attachments::$admin_message = 'Uninstallation successfull';
		
		$this->options = get_option(woocommerce_email_attachments::OPTIONNAME, array());
		
		//	already deleted on deactivation
		if(empty($this->options))
		{
			return;
		}
		
		//	fallback - Delete in any case to clean up database
		if(!isset($this->options[woocommerce_email_attachments::OPT_DEL_ON_UNINSTALL]) || 
				$this->options[woocommerce_email_attachments::OPT_DEL_ON_UNINSTALL])
		{
			$this->delete_files();
			delete_option(woocommerce_email_attachments::OPTIONNAME);
		}
   }
   
   /**
    * Try to delete all files and folder, if uploadfolder exists 
    */
   protected function delete_files()
   {
	   //	fallbacks
	   if(!isset($this->options[woocommerce_email_attachments::OPT_UPLOAD_FOLDER]))
		{
			return;
		}
		
		$folder = trailingslashit($this->options[woocommerce_email_attachments::OPT_UPLOAD_FOLDER]);
		if(empty($folder))
		{
			return;
		}
		
		$emptyfolder = true;
		//	delete all files in folder
		if (is_dir($folder)) 
		{
			$objects = scandir($folder);
			foreach ($objects as $object) 
			{
				if ($object != "." && $object != "..") 
				{
					if (filetype($folder.$object) != "dir") 
						unlink($folder.$object);
					else
						$emptyfolder = false;
				}
			}
			reset($objects);
       }
 		
		if($emptyfolder)
			rmdir($folder);
   }

      
	/**
	 * Initialises the options for this plugin
	 * 
	 * @return array
	 */
	static public function init_default_options() 
	{
		$opt = array();
		
		$opt[woocommerce_email_attachments::OPT_VERSION] = woocommerce_email_attachments::VERSION;
		$opt[woocommerce_email_attachments::OPT_DEL_ON_DEACTIVATE] = false;
		$opt[woocommerce_email_attachments::OPT_DEL_ON_UNINSTALL] = true;
		$opt[woocommerce_email_attachments::OPT_UPLOAD_FOLDER] = WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH;
		
		$opt[woocommerce_email_attachments::OPT_HEAD_NEW_ORDER] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_PROCESSING] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_COMPLETED] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_INVOICE] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_NOTE] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_LOW_STOCK] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_NO_STOCK] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_BACKORDER] = array('cc' => '', 'bcc' => '');
		$opt[woocommerce_email_attachments::OPT_HEAD_NEW_ACCOUNT] = array('cc' => '', 'bcc' => '');
		
		$opt[woocommerce_email_attachments::OPT_ATT_NEW_ORDER] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_PROCESSING] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_COMPLETED] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_INVOICE] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_NOTE] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_LOW_STOCK] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_NO_STOCK] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_BACKORDER] = array();
		$opt[woocommerce_email_attachments::OPT_ATT_NEW_ACCOUNT] = array();
		
		$opt[woocommerce_email_attachments::OPT_EMAIL_NOTIFICATION_HEADLINE] = '';
		$opt[woocommerce_email_attachments::OPT_EMAIL_NOTIFICATION_TEXT] = '';
		
		return $opt;
	}
	
	/**
	 * Upgrades the option array to the new version and returns it.
	 * 
	 * V 1.0.0.0 -> 1.1.0.1 -  copy old values and init new to empty
	 * 
	 * @param array $oldopt		Optionarray to upgrade to new version
	 * @return array			new option array for current version
	 */
	static public function update_options_version(&$oldopt = array())
	{
		$default_option = self::init_default_options();
		
			//	Copy old values from V 1.0.0.0
		$newopt = shortcode_atts($default_option, $oldopt);
		$newopt[woocommerce_email_attachments::OPT_VERSION] = woocommerce_email_attachments::VERSION;
		
//		if($oldopt[woocommerce_email_attachments::OPT_VERSION] == '1.0.0.0')
		
		
//		$newopt[woocommerce_email_attachments::OPT_VERSION] = woocommerce_email_attachments::VERSION;
//		$newopt[woocommerce_email_attachments::OPT_DEL_ON_DEACTIVATE] = $oldopt[woocommerce_email_attachments::OPT_DEL_ON_DEACTIVATE];
//		$newopt[woocommerce_email_attachments::OPT_DEL_ON_UNINSTALL] = $oldopt[woocommerce_email_attachments::OPT_DEL_ON_UNINSTALL];
//		$newopt[woocommerce_email_attachments::OPT_UPLOAD_FOLDER] = $oldopt[woocommerce_email_attachments::OPT_UPLOAD_FOLDER];
//		$newopt[woocommerce_email_attachments::OPT_ATT_PROCESSING] = $oldopt[woocommerce_email_attachments::OPT_ATT_PROCESSING];
//		$newopt[woocommerce_email_attachments::OPT_ATT_COMPLETED] = $oldopt[woocommerce_email_attachments::OPT_ATT_COMPLETED];
//		$newopt[woocommerce_email_attachments::OPT_ATT_INVOICE] = $oldopt[woocommerce_email_attachments::OPT_ATT_INVOICE];
//		$newopt[woocommerce_email_attachments::OPT_EMAIL_NOTIFICATION_HEADLINE] = $oldopt[woocommerce_email_attachments::OPT_EMAIL_NOTIFICATION_HEADLINE];
//		$newopt[woocommerce_email_attachments::OPT_EMAIL_NOTIFICATION_TEXT] = $oldopt[woocommerce_email_attachments::OPT_EMAIL_NOTIFICATION_TEXT];
		
		return $newopt;
	}
	
	/**
	 * checks if the upload folder exists and creates it, if not
	 * 
	 * @param array $options 
	 * @return bool		true, if folder exists
	 */
	static public function check_upload_folder(&$options = array())
	{
		if(!isset($options[woocommerce_email_attachments::OPT_UPLOAD_FOLDER]))
			return false;
		
		$folder = $options[woocommerce_email_attachments::OPT_UPLOAD_FOLDER];
		if(empty($folder))
			return false;
		
		return self::create_folder($folder);
	}
	
	/**
	 * Creates the folder with index.php inside
	 * 
	 * @param string $folder
	 * @param bool	$addfiles	true, if index.php should be added
	 * 
	 * @return bool		true, if folder exists
	 */
	static public function create_folder(&$folder, $addfiles = true)
	{
		if(is_dir($folder))
			return true;
		
//		$oldmask = @umask(0);
		
		$created = wp_mkdir_p( trailingslashit( $folder ) );
		@chmod( $folder, 0777 );
		
//		$newmask = @umask($oldmask);
		
		if(!$addfiles)
			return $created;
		
		$index_file = trailingslashit( $folder ) . 'index.php';
		if ( file_exists( $index_file ) )
			return $created;

		$handle = @fopen( $index_file, 'w' );
		if ($handle) 
		{
			fwrite( $handle, "<?php\r\necho 'Sorry, browsing of directory is not allowed !!!!!';\r\n?>" );
			fclose( $handle );
		}
		
		$index_file = trailingslashit( $folder ) . '.htaccess';
		if ( file_exists( $index_file ) )
			return $created;
		
		$handle = @fopen( $index_file, 'w' );
		if ($handle) 
		{
			fwrite( $handle, "deny from all\r\n" );
			fclose( $handle );
		}
		
		return $created;
	}
}
?>
