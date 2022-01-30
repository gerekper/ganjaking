<?php

/**
 * Class handles all attachments - hooks into 'woocommerce-admin-settings.php'
 * 
 * Uploads the files to a selectable Folder or a given default (see WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH)
 * 
 * This Folder is scanned and all files present are possible attachments to the E-Mails sent by
 * woocommerce automatically during shopping process.
 * 
 * On Deactivation all the files and options are automatically deleted, when checkbox is not disabled.
 * 
 * Optionstructure: "woocommerce_email_attachments" 
 *		= array(
 *			"version"	=>				string (#.#.#.#)
 *			"delete_on_deactivate" =>   boolean,
 *			"delete_on_uninstall" =>	boolean,
 *			"upload_folder" =>			string (absolute serverpath including \)
 *			"attachment_processing" =>	array ( []    => string (filename only)  ),
 *			"attachment_completed"  =>	array ( []    => string (filename only)  ),
 *			"attachment_invoice"    =>	array ( []    => string (filename only)  ),
 *			"notification_headline" =>	string,
 *			"notification_text"		=>	string
 * Added in V 1.1.0.0
 *			"attachment_new_order"	=>	array ( []    => string (filename only)  ),
 *			"attachment_note"		=>	array ( []    => string (filename only)  ),
 *			"attachment_low_stock"	=>	array ( []    => string (filename only)  ),
 *			"attachment_no_stock"	=>	array ( []    => string (filename only)  ),
 *			"attachment_backorder"	=>	array ( []    => string (filename only)  ),
 *			"attachment_new_account" =>	array ( []    => string (filename only)  ),
 *			"headlines_new_order"	=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_processing"	=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_completed"	=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_invoice"		=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_note"		=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_low_stock"	=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_no_stock"	=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_backorder"	=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *			"headlines_new_account"	=>	array ( ['cc', 'bcc']    => string (, seperated email adresses)  ),
 *	            )
 * @version 2.0.0.0
 * 
 */
class woocommerce_email_attachments
{
	const VERSION = '2.0.0.0';
	const OPTIONNAME = "woocommerce_email_attachments";
	const OPT_VERSION = 'version';
	const OPT_DEL_ON_DEACTIVATE = 'delete_on_deactivate';
	const OPT_DEL_ON_UNINSTALL = 'delete_on_uninstall';
	const OPT_UPLOAD_FOLDER = 'upload_folder';
	
	const OPT_HEAD_NEW_ORDER = 'headlines_new_order';
	const OPT_HEAD_PROCESSING = 'headlines_processing';
	const OPT_HEAD_COMPLETED = 'headlines_completed';
	const OPT_HEAD_INVOICE = 'headlines_invoice';
	const OPT_HEAD_NOTE = 'headlines_note';
	const OPT_HEAD_LOW_STOCK = 'headlines_low_stock';
	const OPT_HEAD_NO_STOCK = 'headlines_no_stock';
	const OPT_HEAD_BACKORDER = 'headlines_backorder';
	const OPT_HEAD_NEW_ACCOUNT = 'headlines_new_account';
	
	const OPT_ATT_NEW_ORDER = 'attachment_new_order';
	const OPT_ATT_PROCESSING = 'attachment_processing';
	const OPT_ATT_COMPLETED = 'attachment_completed';
	const OPT_ATT_INVOICE = 'attachment_invoice';
	const OPT_ATT_NOTE = 'attachment_note';
	const OPT_ATT_LOW_STOCK = 'attachment_low_stock';
	const OPT_ATT_NO_STOCK = 'attachment_no_stock';
	const OPT_ATT_BACKORDER = 'attachment_backorder';
	const OPT_ATT_NEW_ACCOUNT = 'attachment_new_account';
	
	const OPT_EMAIL_NOTIFICATION_HEADLINE = 'notification_headline';
	const OPT_EMAIL_NOTIFICATION_TEXT = 'notification_text';
	
	const AJAX_NONCE = 'wc_ip_attachment_nonce';
	const TABID = "inoplugs_email";
	
	const FORM_UPLOAD = "wc_ip_fileupload";
	const FORM_CHECKBOX = "wc_ip_checkbox";
	const FORM_AJAXLOAD = "wc_ip_ajaxload";
	const FORM_TOGGLESTART = 'wc_ip_toggle_start';
	const FORM_TOGGLETITLE = 'wc_ip_toggle_title';
	const FORM_TOGGLEAREA = 'wc_ip_toggle_area';
	const FORM_TOGGLEEND = 'wc_ip_toggle_end';
	const FORM_EMAIL = 'wc_ip_email';
	
	const CLASS_TOGGLE = 'wc_ip_toggle_section';
	
	const ID_PLUPLOAD_UNIQUE = 'wc_upload_files';
	const VAL_PLUPLOAD_CALLBACK = 'wc_plupload_upload';
	const VAL_PLUPLOAD_HANDLER_SCRIPT = 'wc_plupload_handlers_script';
	const VAL_JAVA_FILES_ADDED = 'wc_plupload_handler_files_added';
	const VAL_JAVA_ERROR = 'wc_plupload_handler_error';
	const VAL_JAVA_FILES_UPLOADED = 'wc_plupload_handler_files_uploaded';

	
	public $settings_tabs;
	public $current_tab;
	public $fields = array();
	
	/**
	 * Holds the options for this plugin
	 * 
	 * @var array 
	 */
	public $options;

	/**
	 * Messagestring to show on admin page
	 * 
	 * @var string
	 */
	static public $admin_message;
	
	/**
	 * @var string
	 */
	static public $plugin_url;

	/**
	 * Filenames to skip on displaying files for attachment
	 * 
	 * @var array
	 */
	static public $skip_files;
	
	/**
	 * All characters to be additionally removed from filenames and are not removed by WP
	 * Set in constructor.
	 * 
	 * @var array
	 */
	static public $chars_to_remove;
	
	/**
	 * All characters to be additionally replaced from filenames.
	 * Set in constructor. Arrays must be of equal length to $chars_to_be_replaced_by.
	 * 
	 * @var array
	 */
	static public $chars_to_be_replaced;
	
	/**
	 * All characters to be additionally replaced from filenames.
	 * Set in constructor. Arrays must be of equal length to $chars_to_be_replaced.
	 * 
	 * @var array
	 */
	static public $chars_to_be_replaced_by;
	
	/**
	 * true, if attachments have been added
	 * 
	 * @var bool
	 */
	protected $attachments_sent;
	
	/**
	 * variable inputfield parameters filled io constructor
	 * 
	 * @var array 
	 */
	static public $inputfields_param;
		
	/**
	 * fixed inputfield constants filled io constructor
	 * @var array 
	 */
	static public $inputfields_const;

	/**
	 * 
	 */
	public function __construct() 
	{
		if(!isset(self::$admin_message))
		{
			self::$admin_message = '';
		}
		
		if(!isset(self::$plugin_url))
		{
			self::$plugin_url = '';
		}
		if(!isset(self::$skip_files))
		{
			self::$skip_files = array();
		}
		
		if(!isset(self::$chars_to_remove))
		{
			self::$chars_to_remove = array('§');
		}
		
		if(!isset(self::$chars_to_be_replaced))
		{
			self::$chars_to_be_replaced = array('ä', 'ö', 'ü');
		}
		
		if(!isset(self::$chars_to_be_replaced_by))
		{
			self::$chars_to_be_replaced_by = array('ae', 'oe', 'ue');
		}
		
		self::init_inputfield_values();
		
		$this->attachments_sent = false;
		$this->options = get_option(self::OPTIONNAME, array());

		
		// fallback to standard options settings
		if(empty ($this->options))
		{
			$this->options = woocommerce_email_attachments_activation::init_default_options();
		}
		else if(version_compare($this->options[self::OPT_VERSION], self::VERSION, '!='))
		{
			$this->options = woocommerce_email_attachments_activation::update_options_version($this->options);
			update_option(woocommerce_email_attachments::OPTIONNAME, $this->options);
		}

					//	load scripts only on our option page
		if(false !== strpos($_SERVER['REQUEST_URI'], 'tab=inoplugs_email'))
		{
			inoplugs_plupload::activate();
			
					//	attach to plupload filters for output
			add_filter('inoplugs_plupload_translate_messages', array(&$this, 'handler_translate_messages'), 10, 2);
			add_filter('inoplugs_plupload_set_hidden_field_data', array(&$this, 'handler_set_hidden_field_data'), 10, 2);

			add_action('admin_init', array(&$this, 'handler_wp_admin_init'));
			add_action('admin_print_styles', array(&$this, 'handler_wp_admin_print_styles'));
		}
	
		
		
		$this->current_tab = ( isset($_GET['tab'] ) ) ? $_GET['tab'] : 'general';

		//	Add all tabs required
		$this->settings_tabs = array(
			woocommerce_email_attachments::TABID => __( 'E-Mail Attachments', 'woocommerce_email_attachments' )
		);

		// Load in the new settings tabs and attach handler.
		add_action( 'woocommerce_settings_tabs', array( &$this, 'add_tab' ), 10 );

		// Run these actions when generating the settings tabs.
		foreach ( $this->settings_tabs as $name => $label ) {
			add_action( 'woocommerce_settings_tabs_' . $name, array( &$this, 'set_tabs' ), 10 );
			add_action( 'woocommerce_update_options_' . $name, array( &$this, 'save_settings' ), 10 );
		}
		
		// Add the settings fields to each tab.
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_UPLOAD, array( &$this, 'set_upload_file_field' ), 10 );
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_CHECKBOX, array( &$this, 'set_checkbox_field' ), 10 );
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_AJAXLOAD, array( &$this, 'set_ajaxload_field' ), 10 );
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_TOGGLESTART, array( &$this, 'set_toggle_start_field' ), 10 );
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_TOGGLETITLE, array( &$this, 'set_toggle_title_field' ), 10 );
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_TOGGLEAREA, array( &$this, 'set_toggle_area_field' ), 10 );
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_TOGGLEEND, array( &$this, 'set_toggle_end_field' ), 10 );
		add_action( 'woocommerce_admin_field_'.woocommerce_email_attachments::FORM_EMAIL, array( &$this, 'set_email_field' ), 10 );
		
		//	add fields to tab on admin page
		add_action( 'woocommerce_email_attachment_settings', array( &$this, 'add_settings_fields' ), 10 );
		
		// add attachment notification text to email content
		add_action( 'woocommerce_email_footer', array( &$this, 'handler_email_attachment_footer' ), 10 );

		//	Arrach to subject hooks to know, which type of mail is generated to attach a notification message in footer section
		add_filter('woocommerce_email_subject_new_order', array(&$this, 'handler_subject_new_order'), 10, 2);
		add_filter('woocommerce_email_subject_customer_processing_order', array(&$this, 'handler_subject_customer_processing_order'), 10, 2);
		add_filter('woocommerce_email_subject_customer_completed_order', array(&$this, 'handler_subject_customer_completed_order'), 10, 2);
		add_filter('woocommerce_email_subject_customer_invoice', array(&$this, 'handler_subject_customer_invoice'), 10, 2);
		add_filter('woocommerce_email_subject_customer_note', array(&$this, 'handler_subject_customer_note'), 10, 2);
		add_filter('woocommerce_email_subject_low_stock', array(&$this, 'handler_subject_low_stock'), 10, 2);
		add_filter('woocommerce_email_subject_no_stock', array(&$this, 'handler_subject_no_stock'), 10, 2);
		add_filter('woocommerce_email_subject_backorder', array(&$this, 'handler_subject_backorder'), 10, 2);
		add_filter('woocommerce_email_subject_customer_new_account', array(&$this, 'handler_subject_customer_new_account'), 10, 2);
		
		//	Arrach to E-Mail Handlers for CC, BCC, additional headers
		add_filter('woocommerce_email_headers', array(&$this, 'handler_email_headers'), 10, 2);
		
		//	Arrach to E-Mail Handlers for attachments
		add_filter('woocommerce_email_attachments', array(&$this, 'handler_email_attachments'), 20, 2);	
		
		//	Attach to WP filename filters
		add_filter('sanitize_file_name_chars', array(&$this, 'handler_wp_sanitize_file_name_chars'), 10, 2);
		add_filter('sanitize_file_name', array(&$this, 'handler_wp_sanitize_file_name'), 10, 2);
			
			//	Ajax Callbacks for file upload and delete
		if(false !== strpos($_SERVER['REQUEST_URI'], 'admin-ajax.php'))
		{
			inoplugs_plupload::activate();
			
			add_action( 'wp_ajax_nopriv_delete-file', array(&$this,'handler_ajax_delete_file') );
			add_action( 'wp_ajax_delete-file', array(&$this,'handler_ajax_delete_file') );
			
			//	inoplugs_plupload callback
			add_action(self::VAL_PLUPLOAD_CALLBACK, array(&$this,'handler_ajax_wc_upload_file'), 10, 1);
			
		}
	}
	/**
	 * 
	 */
	public function handler_wp_admin_init()
	{
		$urlpath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__));
		
		wp_register_script(self::VAL_PLUPLOAD_HANDLER_SCRIPT, $urlpath.'js/wc_plupload_handlers.js', array('jquery'));

				//	attach to filters and actions of plupload
		add_filter('inoplugs_plupload_dependent_js', array(&$this, 'handler_plupload_dependent_js'), 10, 1);

	}
	/**
	 * Add all styles to admin page
	 */
	public function handler_wp_admin_print_styles()
	{
		$urlpath = self::$plugin_url.'v210/';

		wp_register_style('woocommerce_email_attachments_css', $urlpath . 'css/email_attachments.css');
		
		wp_register_script('woocommerce_email_attachments_implement_script', $urlpath.'js/wc_email_attachments.js', array('jquery'));
		wp_register_script('woocommerce_email_attachments_ips_email_checker', $urlpath.'js/wc_ips_email_checker.js', array('jquery'));
		
		wp_enqueue_style('woocommerce_email_attachments_css');
		
		wp_enqueue_script(self::VAL_PLUPLOAD_HANDLER_SCRIPT);
		wp_localize_script( 'woocommerce_email_attachments_implement_script', 'MyAjax', 
						array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
							   self::AJAX_NONCE => wp_create_nonce( self::AJAX_NONCE ),) );
		wp_enqueue_script('woocommerce_email_attachments_implement_script');
		wp_enqueue_script('woocommerce_email_attachments_ips_email_checker');
		
		$translate = array(
				'uploadinfo_ok' => __('successfully uploaded - stored as: ', 'woocommerce_email_attachments'),
				'uploadinfo_error' => __('error on uploading: ', 'woocommerce_email_attachments'),
				'delfile_yesno' => __('Do you really want to delete the following file permanenty:', 'woocommerce_email_attachments'),
				'delete_file' => __('Deleting File...', 'woocommerce_email_attachments'),
				'load_file' => __('Loading File...', 'woocommerce_email_attachments'),
				'reset_message' => __('Processing...', 'woocommerce_email_attachments'),
				);
		
		$translate_email_checker = array(
				'nostring' => __('E-Mail address is not a string.', 'woocommerce_email_attachments'),
				'emptystring' => __('Empty E-Mail is not allowed.', 'woocommerce_email_attachments'),
				'error1' => __('"<" is not allowed in E-Mail address.', 'woocommerce_email_attachments'),
				'error2' => __('Endtag ">" is missing in E-Mail address.', 'woocommerce_email_attachments'),
				'invalid' => __('Invalid E-Mail address entered.', 'woocommerce_email_attachments'),
				'emailonly' => __('Enter only the E-Mail address - not name and EM-ail address.', 'woocommerce_email_attachments'),
				'oneaddressonly' => __('Only 1 E-Mail address is allowed to be entered.', 'woocommerce_email_attachments'),
				'errorsfound' => __('Errors found in E-Mail addresses:', 'woocommerce_email_attachments'),
				'marked' => __('Check for marked addresses.', 'woocommerce_email_attachments')
			);
		
		wp_localize_script( 'woocommerce_email_attachments_implement_script', 'wc_email_attachments', $translate);
		wp_localize_script( 'woocommerce_email_attachments_ips_email_checker', 'wc_ips_email_checker_tx', $translate_email_checker);
		
		
	}
	
	/**
	 * Adds all scripts, pluplaod depends on
	 * 
	 * @param array $dependent_js 
	 * @return array
	 */
	public function handler_plupload_dependent_js($dependent_js)
	{
		if(!is_array($dependent_js))
			return $dependent_js;
		
		$dependent_js[] = self::VAL_PLUPLOAD_HANDLER_SCRIPT;
		
		return $dependent_js;
	}
	
	/**
	 * Initializes static fields for input
	 */
	static public function init_inputfield_values()
	{
		if(!isset(self::$inputfields_const))
		{
			self::$inputfields_const = array(
				'cc_text'	=> __( 'CC:', 'woocommerce_email_attachments' ),
				'cc_desc'	=> __( '<br/>Enter the email address(es) you want, seperate multiple addresses with ",", leave blank if not needed. Follow these rules for a single address: <br/>recipient_name@hisdomain.ext -- or -- hisfirstname hislastname &lt;recipient_name@hisdomain.ext&gt;', 'woocommerce_email_attachments' ),
				'bcc_text'	=> __( 'BCC:', 'woocommerce_email_attachments' ),
			);
		}
		
		if(isset(self::$inputfields_param))
			return;
		
		self::$inputfields_param = array();
		self::$inputfields_param[] =			
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for New Order', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_NEW_ORDER,
						'cc_id'		=>	self::OPT_HEAD_NEW_ORDER.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_NEW_ORDER.'[bcc]',
						'att_id'	=>	self::OPT_ATT_NEW_ORDER,
						);
		
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for Processing Order', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_PROCESSING,
						'cc_id'		=>	self::OPT_HEAD_PROCESSING.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_PROCESSING.'[bcc]',
						'att_id'	=>	self::OPT_ATT_PROCESSING,
						);
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for Completed Order', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_COMPLETED,
						'cc_id'		=>	self::OPT_HEAD_COMPLETED.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_COMPLETED.'[bcc]',
						'att_id'	=>	self::OPT_ATT_COMPLETED,
						);
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for Invoice', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_INVOICE,
						'cc_id'		=>	self::OPT_HEAD_INVOICE.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_INVOICE.'[bcc]',
						'att_id'	=>	self::OPT_ATT_INVOICE,
						);
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for Customer Note', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_NOTE,
						'cc_id'		=>	self::OPT_HEAD_NOTE.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_NOTE.'[bcc]',
						'att_id'	=>	self::OPT_ATT_NOTE,
						);
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for Low Stock Information', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_LOW_STOCK,
						'cc_id'		=>	self::OPT_HEAD_LOW_STOCK.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_LOW_STOCK.'[bcc]',
						'att_id'	=>	self::OPT_ATT_LOW_STOCK,
						);
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for No Stock Information', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_NO_STOCK,
						'cc_id'		=>	self::OPT_HEAD_NO_STOCK.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_NO_STOCK.'[bcc]',
						'att_id'	=>	self::OPT_ATT_NO_STOCK,
						);
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for Backorder Information', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_BACKORDER,
						'cc_id'		=>	self::OPT_HEAD_BACKORDER.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_BACKORDER.'[bcc]',
						'att_id'	=>	self::OPT_ATT_BACKORDER,
						);
		self::$inputfields_param[] =
				array ( 'toggletitle' => __( 'Select CC, BCC and Attachment Files for New Customer Account', 'woocommerce_email_attachments' ),
						'id'		=>	self::OPT_HEAD_NEW_ACCOUNT,
						'cc_id'		=>	self::OPT_HEAD_NEW_ACCOUNT.'[cc]',
						'bcc_id'	=>	self::OPT_HEAD_NEW_ACCOUNT.'[bcc]',
						'att_id'	=>	self::OPT_ATT_NEW_ACCOUNT,
						);
	}

	/**==================================================================================
	 * ==================================================================================
	 *          HANDLER - SECTION
	 * ==================================================================================
	 *===================================================================================*/
	
	/************************************************************************************************
	 * Subject handler section
	 ************************************************************************************************/

	public function handler_subject_new_order($subject = '', $order = '')
	{
		if(isset($this->options[self::OPT_ATT_NEW_ORDER]) && !empty($this->options[self::OPT_ATT_NEW_ORDER]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $order 
	 * @return string
	 */
	public function handler_subject_customer_processing_order($subject = '', $order = '')
	{
		if(isset($this->options[self::OPT_ATT_PROCESSING]) && !empty($this->options[self::OPT_ATT_PROCESSING]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $order 
	 * @return string
	 */
	public function handler_subject_customer_completed_order($subject = '', $order = '')
	{
		if(isset($this->options[self::OPT_ATT_COMPLETED]) && !empty($this->options[self::OPT_ATT_COMPLETED]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $order 
	 * @return string
	 */
	public function handler_subject_customer_invoice($subject = '', $order = '')
	{
		if(isset($this->options[self::OPT_ATT_INVOICE]) && !empty($this->options[self::OPT_ATT_INVOICE]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $order 
	 * @return string
	 */
	public function handler_subject_customer_note($subject = '', $order = '')
	{
		if(isset($this->options[self::OPT_ATT_NOTE]) && !empty($this->options[self::OPT_ATT_NOTE]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $product 
	 * @return string
	 */
	public function handler_subject_low_stock($subject = '', $product = '')
	{
		if(isset($this->options[self::OPT_ATT_LOW_STOCK]) && !empty($this->options[self::OPT_ATT_LOW_STOCK]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $product 
	 * @return string
	 */
	public function handler_subject_no_stock($subject = '', $product = '')
	{
		if(isset($this->options[self::OPT_ATT_NO_STOCK]) && !empty($this->options[self::OPT_ATT_NO_STOCK]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $product 
	 * @return string
	 */
	public function handler_subject_backorder($subject = '', $product = '')
	{
		if(isset($this->options[self::OPT_ATT_BACKORDER]) && !empty($this->options[self::OPT_ATT_BACKORDER]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
	
	/**
	 * Tricky - finds out, what type of email, checks for attachments and sets flag
	 * 
	 * @param string $subject
	 * @param object $user 
	 * @return string
	 */
	public function handler_subject_customer_new_account($subject = '', $user = '')
	{
		if(isset($this->options[self::OPT_ATT_NEW_ACCOUNT]) && !empty($this->options[self::OPT_ATT_NEW_ACCOUNT]))
		{
			$this->attachments_sent = true;
		}
		return $subject;
	}
		
	/************************************************************************************************
	 * Headers handler section
	 ************************************************************************************************/
	/**
	 * WooCommerce main handler for CC and BCC and other EMail headings. Routes to the approtiate handl3er 
	 * to set the headers.
	 * 
	 * @param string|array $attachment
	 * @param string $emailtype 
	 * @return string|array		(see documentation of wp_mail) 
	 */
	public function handler_email_headers($headers = '', $emailtype = '')
	{
		if(empty($emailtype) || !is_string($emailtype))
			return $headers;
		
		switch(strtolower($emailtype))
		{
			case 'new_order':
				return $this->new_order_headers($headers);
				break;
			case 'customer_processing_order':
				return $this->customer_processing_order_headers($headers);
				break;
			case 'customer_completed_order':
				return $this->customer_completed_order_headers($headers);
				break;
			case 'customer_invoice':
				return $this->customer_invoice_headers($headers);
				break;
			case 'customer_note':
				return $this->customer_note_headers($headers);
				break;
			case 'low_stock':
				return $this->low_stock_headers($headers);
				break;
			case 'no_stock':
				return $this->no_stock_headers($headers);
				break;
			case 'backorder':
				return $this->backorder_headers($headers);
				break;
			case 'customer_new_account':	
				return $this->customer_new_account_headers($headers);
				break;
			default:
				return $headers;
		}
		return $headers;
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function new_order_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_NEW_ORDER);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_processing_order_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_PROCESSING);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_completed_order_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_COMPLETED);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_invoice_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_INVOICE);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_note_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_NOTE);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function low_stock_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_LOW_STOCK);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function no_stock_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_NO_STOCK);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function backorder_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_BACKORDER);
	}
	
	/**
	 * Adds the CC and BCC stored in the options to the headers.
	 * 
	 * @param string|array $headers
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_new_account_headers($headers = array())
	{
		return $this->add_headers($headers, self::OPT_HEAD_NEW_ACCOUNT);
	}
		
	
	/************************************************************************************************
	 * Attachments handler section
	 ************************************************************************************************/

	/**
	 * WooCommerce main handler for attachments. Routes to the approtiate handl3er 
	 * to get the attachments.
	 * 
	 * @param string|array $attachment
	 * @param string $emailtype 
	 * @return string|array		(see documentation of wp_mail) 
	 */
	public function handler_email_attachments($attachment = "", $emailtype = '')
	{
		if(empty($emailtype) || !is_string($emailtype))
			return $attachment;
		
		switch(strtolower($emailtype))
		{
			case 'new_order':
				return $this->new_order_attachments($attachment);
				break;
			case 'customer_processing_order':
				return $this->customer_processing_order_attachments($attachment);
				break;
			case 'customer_completed_order':
				return $this->customer_completed_order_attachments($attachment);
				break;
			case 'customer_invoice':
				return $this->customer_invoice_attachments($attachment);
				break;
			case 'customer_note':
				return $this->customer_note_attachments($attachment);
				break;
			case 'low_stock':
				return $this->low_stock_attachments($attachment);
				break;
			case 'no_stock':
				return $this->no_stock_attachments($attachment);
				break;
			case 'backorder':
				return $this->backorder_attachments($attachment);
				break;
			case 'customer_new_account':	
				return $this->customer_new_account_attachments($attachment);
				break;
			default:
				return $attachment;
		}
		return $attachment;
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function new_order_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_NEW_ORDER);
	}
		
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_processing_order_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_PROCESSING);
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_completed_order_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_COMPLETED);
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_invoice_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_INVOICE);
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_note_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_NOTE);
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function low_stock_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_LOW_STOCK);
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function no_stock_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_NO_STOCK);
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function backorder_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_BACKORDER);
	}
	
	/**
	 * Adds the Filenames stored in the options to the attachmentlist, if the file(s) esist.
	 * 
	 * @param string|array $attachment
	 * @return string|array (see documentation of wp_mail) 
	 */
	public function customer_new_account_attachments($attachment = "")
	{
		return $this->add_attachments($attachment, woocommerce_email_attachments::OPT_ATT_NEW_ACCOUNT);
	}
	
	/************************************************************************************************
	 * WP handler section
	 ************************************************************************************************/

	/**
	 * Adds the characters we want to be removed from filename
	 * 
	 * @param array $special_chars		all characters to be replaced with ''
	 * @param strin $filename_raw		original filename before any modifications
	 * @return array					
	 */
	public function handler_wp_sanitize_file_name_chars($special_chars, $filename_raw)
	{	
		if(empty(self::$chars_to_remove))
				return $special_chars;
		
		if(!is_array($special_chars))
			$special_chars = array();
		
		return array_merge($special_chars, self::$chars_to_remove);
	}

	/**
	 * Called, after WP is ready with removing and preparing filename. 
	 * Replace the characters you do not want to have in filenames.
	 * Ensures, that the filename is in lowercase.
	 * 
	 * @param string $filename			the filename after removing characters
	 * @param string $filename_raw		original filename before any modifications
	 * @return string	
	 */
	public function handler_wp_sanitize_file_name($filename, $filename_raw)
	{
		$filename = strtolower ($filename);
		if(!empty(self::$chars_to_be_replaced))
		{
			$filename = str_replace(self::$chars_to_be_replaced, self::$chars_to_be_replaced_by, $filename);
		}
		
		//	Allows letters a-z, digits, %, space (\\040), hyphen (\\-),, + (\\+) underscore (\\_) and backslash (\\\\)
		$allowed = "/[^a-zA-Z0-9%\\040\\.\\-\\+\\_\\\\]/i";
		return preg_replace($allowed, '', $filename);
	}

	
	/**==================================================================================
	 * ==================================================================================
	 *          HELPER FUNCTIONS
	 * ==================================================================================
	 *===================================================================================*/
	
	/**
	 * Adds the array of filenames in $newfiles to $attachment. Checks, if the file added exists
	 * 
	 * @param string|array $attachment
	 * @param string $att_option	name of index in the optionarray
	 * @return string|array (see documentation of wp_mail) 
	 */
	protected function add_attachments($attachment, $att_option = "")
	{
		if(!is_string($att_option))
			return $attachment;
		
		if(empty($att_option))
			return $attachment;
				
		$newfiles = array();
		if(isset ($this->options[$att_option]))
			$newfiles = $this->options[$att_option];
		
		/**
		 * Fallback in case someone other changed the structure
		 */
		if(!is_array($newfiles))
		{
			is_string($newfiles) ? $newfiles = array($newfiles) : $newfiles = array();
		}
		
		if(empty ($newfiles))
			return $attachment;
		
		$addarray = true;
			
		/**
		 * Check for array or CRLF seperated string (see documentation for wp_mail)
		 */
		if(is_string($attachment))
		{
			if((false === strpos($attachment, "\r\n")) )
			{
				$attachment = trim($attachment);
				(empty($attachment)) ? $attachment = array() : $attachment = array($attachment);
			}
			else
			{
				$addarray = false;
			}
		}
		
		/**
		 * Add all filenames and check if exist
		 */
		foreach ($newfiles as $value) 
		{
			$addfile = $this->options[self::OPT_UPLOAD_FOLDER].$value;
			if(file_exists($addfile))
			{
				if($addarray)
				{
					$attachment[] = $addfile;
				}
				else 
				{
					if(!empty($attachment))
					{
						$attachment .= "\r\n";
					}
					$attachment .= $addfile;
				}
			}
		}
		
		/**
		 * Return "" in any case
		 */
		if(empty($attachment))
		{
			$attachment = "";
		}
		
		return $attachment;
	}
	
	
	/**
	 * Adds the CC and BCC from the option array to the headers
	 * 
	 * @param string|array $headers		string must be "\r\n" seperated and must end with PHP_EOL (see documentation of wp_mail)
	 * @param string $head_option		name of index in the optionarray
	 * @return string|array (see documentation of wp_mail) 
	 */
	protected function add_headers($headers, $head_option = "")
	{
		if(!is_string($head_option))
			return $headers;
		
		if(empty($head_option))
			return $headers;
				
		$adresses = array();
		if(isset ($this->options[$head_option]))
			$adresses = $this->options[$head_option];
		
		/**
		 * Fallback in case someone other changed the structure -> ignore
		 */
		if(!is_array($adresses))
		{
			return $headers;
		}
		
		if(empty ($adresses))
			return $headers;
		
		$addarray = true;
			
		/**
		 * Check for array or \r\n seperated string (see documentation for wp_mail)
		 */
		if(is_string($headers))
		{
			if(false === strpos($headers, "\r\n"))
			{
				$headers = trim($headers);
				(empty($headers)) ? $headers = array() : $headers = array($headers);
			}
			else
			{
				$addarray = false;
			}
		}
		
		/**
		 * Add the BC and CC adresses. The adresses are , seperated if exist
		 */
		foreach ($adresses as $key => $value) 
		{
			if(empty($value))
				continue;
			
			if($addarray)
			{
				$headers[] = $key.': '.$value;
			}
			else
			{
				if(!empty($value))
				{
					$headers .= $key.': '.$value."\r\n";
				}
			}
		}
		unset ($value);
		
		/**
		 * Return "" in any case
		 */
		if(empty($headers))
		{
			$headers = '';
		}
		
		return $headers;
	}



	/**==================================================================================
	 * ADMIN - SECTION
	 *===================================================================================*/
	
	/**
	 * Add all tables
	 */
	public function add_tab() 
	{
		foreach ( $this->settings_tabs as $name => $label )
		{
			$class = 'nav-tab';
			if( $this->current_tab == $name ) $class .= ' nav-tab-active';
			if ( version_compare( WC_VERSION, '2.2.0', '<' ) ) 
			{
				echo '<a href="' . admin_url( 'admin.php?page=woocommerce&tab=' . $name ) . '" class="' . $class . '">' . $label . '</a>';
			}
			else
			{
				echo '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $name ) . '" class="' . $class . '">' . $label . '</a>';
			}
		}
	}
	
	/**
	 * Actionhandler: 
	 *
	 * Called when viewing our custom settings tab(s). One function for all tabs.
	 */
	public function set_tabs() 
	{
		global $woocommerce_settings;

		// Determine the current tab in effect.
		$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_settings_tabs_' );

		// Hook onto this from another function to keep things clean.
		do_action( 'woocommerce_email_attachment_settings' );

		// Display settings for this tab (make sure to add the settings to the tab).
		woocommerce_admin_fields( $woocommerce_settings[$current_tab] );
	}		
	
	/**
	 * Woocommerce saves settings in a single field in the database for each option. 
	 * This does not apply for this plugin, we use our own structure and also handle
	 * initialising of form with stored values.
	 * 
	 * We ignore woocommere options handling
	 */
	public function save_settings() 
	{				
//		global $woocommerce_settings;

		// Make sure our settings fields are recognised.
//		$this->add_settings_fields();

//		$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_update_options_' );
//		woocommerce_update_options( $woocommerce_settings[$current_tab] );
		
		//	save all data to own option
		$this->save_all_options();
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
	 * Add settings fields for each tab.
	 */
	public function add_settings_fields() 
	{
		global $woocommerce_settings;

		// Load the prepared form fields.
		$this->init_form_fields();

		if ( is_array( $this->fields ) ) 
		{
			foreach ( $this->fields as $k => $v )
			{
				$woocommerce_settings[$k] = $v;
			}
		}
	}
	
	/**
	 * Prepare form fields to be used in the various tabs.
	 */
	protected function init_form_fields() 
	{
		$id = woocommerce_email_attachments::TABID;
		$idcount = 1;
		
		$attachmentfiles = $this->get_all_files();
		
		$checked_deactivate = '';
		if(isset($this->options[self::OPT_DEL_ON_DEACTIVATE]) && $this->options[self::OPT_DEL_ON_DEACTIVATE])
			$checked_deactivate = "checked";
		
		$checked_uninstall = '';
		if(isset($this->options[self::OPT_DEL_ON_UNINSTALL]) && $this->options[self::OPT_DEL_ON_UNINSTALL])
			$checked_uninstall = "checked";
		
		$tmp_mess = __( 'Specify the basic settings for this plugin, upload and delete the files and select the attachments. <br/><br/>When finished with the upload, click "Reinitialize Selection List" or "Save Changes" to refresh screen and make the new uploaded files available for selection and save all your input .<br/><br/>If you need more help, ','woocommerce_email_attachments');
		$tmp_mess .= '<a href="'.plugins_url('documentation/english/index.html', __FILE__).'" target="_blank">';
		$tmp_mess .= __('here you find the documentation.', 'woocommerce_email_attachments');
		$tmp_mess .= '</a><br/><br/>';
		
		$inputfields = array(
			array(	
				'name' 	=> __( 'Basic Settings', 'woocommerce_email_attachments' ), 
				'type' => 'title',
				'desc' => $tmp_mess, 
				'id' => $id.$idcount
				),
			array(
				'type'		=>	self::FORM_CHECKBOX,
				'id'		=>  self::OPT_DEL_ON_DEACTIVATE,
				'name' 		=>  __('Deactivation:', 'woocommerce_email_attachments'),
				'desc'		=>  __('Delete all files and settings on deactivation of plugin (Be aware, that Wordpress deactivates the plugin when upgrading to a new version).', 'woocommerce_email_attachments'),
				'css'		=>  '',
				'checked'	=>	$checked_deactivate
					),
			array(
				'type'		=>	self::FORM_CHECKBOX,
				'id'		=>  self::OPT_DEL_ON_UNINSTALL,
				'name' 		=>  __('Uninstallation:', 'woocommerce_email_attachments'),
				'desc'		=>  __('Delete all files and settings on uninstalling of plugin', 'woocommerce_email_attachments'),
				'css'		=>  '',
				'checked'	=>	$checked_uninstall
					),
			array(
				'type' 		=>	self::FORM_AJAXLOAD,
				'desc'		=>	__('<span class="statusinfo">Processing...</span>','woocommerce_email_attachments'),
					),
			array(  
				'type' 		=> 'text',
				'name' 		=> __( 'Upload folder:', 'woocommerce_email_attachments' ),
				'desc' 		=> __( '<br/>You can change the upload folder to a new one. You have to specify the complete serverpath.', 'woocommerce_email_attachments' ),
				'tip' 		=> '',
				'id' 		=> self::OPT_UPLOAD_FOLDER,
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options[self::OPT_UPLOAD_FOLDER],
				'std' 		=> $this->options[self::OPT_UPLOAD_FOLDER],
					),
			array(  
				'type' 		=> 'text',
				'name' 		=> __( 'Attachment notification headline:', 'woocommerce_email_attachments' ),
				'desc' 		=> __( '<br/>Insert the headline of your attachment notification. The attachment notification will be displayed in the email footer. Leave this field empty if you don\'t want to use this feature.', 'woocommerce_email_attachments' ),
				'tip' 		=> '',
				'id' 		=> self::OPT_EMAIL_NOTIFICATION_HEADLINE,
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options[self::OPT_EMAIL_NOTIFICATION_HEADLINE],
				'std' 		=> $this->options[self::OPT_EMAIL_NOTIFICATION_HEADLINE],
					),
			array(  
				'type' 		=> 'textarea',
				'name' 		=> __( 'Attachment notification text:', 'woocommerce_email_attachments' ),
				'desc' 		=> __( '<br/>Insert the text of your attachment notification. The attachment notification will be displayed in the email footer and tells the recipient that your email contains attached files. E.g. this notification is useful if you fear that attachments may be blocked because of the file size, filters, etc. and you want to make sure that the recipient is aware of the fact that he should have received attachments. Leave this field empty if you don\'t want to use this feature.', 'woocommerce_email_attachments' ),
				'tip' 		=> '',
				'id' 		=> self::OPT_EMAIL_NOTIFICATION_TEXT,
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options[self::OPT_EMAIL_NOTIFICATION_TEXT],
				'std' 		=> $this->options[self::OPT_EMAIL_NOTIFICATION_TEXT],
					),
			array( 'type' => 'sectionend', 
				   'id' => $id.$idcount
				 )
			);
		
		$inputfields[] =
			array(
				'type'		=>	self::FORM_UPLOAD,
				'id'		=> 'upload',
				'head' 		=> __('Upload the files you need for attachment', 'woocommerce_email_attachments' ), 
				'desc' 		=> '',
				'tip' 		=> '',
				'css' 		=> 'button',
				'default' 	=> '',
				'std'		=> '',
				'button'	=> __('Upload File', 'woocommerce_email_attachments' ),
				'refresh'	=> __('Reinitialize Selection List','woocommerce_email_attachments')
				);
		
		//	Add email sections
		for ($pos = 0; $pos < sizeof(self::$inputfields_param); $pos++)
		{
			$this->add_email_sections($pos, $inputfields, $idcount, $attachmentfiles);
		}
		
			//	allow to hook
		$this->fields[$id] = apply_filters('woocommerce_email_attachments_fields', $inputfields);
	}
	
	
	/**
	 * Adds a speific section for CC, BCC and fileselection given by $pos. Seleccts the values
	 * from an array set in the constructor.
	 * 
	 * @param int $pos				Selects the section
	 * @param array $inputfields	
	 */
	protected function add_email_sections ($pos, &$inputfields, &$idcount, &$attachmentfiles)
	{
		$id = woocommerce_email_attachments::TABID;
		
		$inputfields[] = 
			array(	
				'type'	=>	self::FORM_TOGGLESTART,
				'id'	=>	'',
				'class' =>	'',
				'css'	=>	'',
				);
		
		$idcount++;
		$inputfields[] = 
			array(	
				'type' => self::FORM_TOGGLETITLE,
				'tag' => 'h3',
				'id' => '',
				'class' => self::CLASS_TOGGLE,
				'css' => '', 
				'title' => self::$inputfields_param[$pos]['toggletitle']
				 );
		
		$inputfields[] = 
			array(	
				'type'	=>	self::FORM_TOGGLEAREA,
				'id'	=>	'',
				'class' =>	'',
				'css'	=>	'',
				);
		
		$idcount++;
		$inputfields[] = 
			array(	
				'name' 	=> '', 
				'type' => 'title',
				'desc' => '', 
				'id' => $id.$idcount
				);
		
		$inputfields[] = 
			array(  
				'type' 		=> self::FORM_EMAIL,
				'name' 		=> self::$inputfields_const['cc_text'],
				'desc' 		=> self::$inputfields_const['cc_desc'],
				'email'		=> 'check_multi',
				'emailname'	=> 'yes',
				'tip' 		=> '',
				'id' 		=> self::$inputfields_param[$pos]['cc_id'],
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options[self::$inputfields_param[$pos]['id']]['cc'],
				'std' 	=> $this->options[self::$inputfields_param[$pos]['id']]['cc'],
					);
		
		$inputfields[] = 
			array(  
				'type' 		=> self::FORM_EMAIL,
				'name' 		=> self::$inputfields_const['bcc_text'],
				'desc' 		=> self::$inputfields_const['cc_desc'],
				'email'		=> 'check_multi',
				'emailname'	=> 'yes',
				'tip' 		=> '',
				'id' 		=> self::$inputfields_param[$pos]['bcc_id'],
				'css' 		=> 'min-width:600px;',
				'default' 		=> $this->options[self::$inputfields_param[$pos]['id']]['bcc'],
				'std' 		=> $this->options[self::$inputfields_param[$pos]['id']]['bcc'],
					);
		
		$inputfields[] = 
			array( 'type' => 'sectionend', 
				   'id' => $id.$idcount
			);
		
		$idcount++;
		$inputfields[] = 
			array(	
				'name' 	=> '', 
				'type' => 'title',
				'desc' => '', 
				'id' => $id.$idcount
				);
		
		$this->add_filesection($inputfields, $attachmentfiles, self::$inputfields_param[$pos]['att_id']);
		$inputfields[] = 
			array( 'type' => 'sectionend', 
				   'id' => $id.$idcount
			);
		
		$inputfields[] = 
			array( 'type' => self::FORM_TOGGLEEND
			);
		
	}
	
	/**
	 * Shows all the attachment filenames with a checkbox and checks the already selected
	 * and adds to the $inputfields
	 * 
	 * @param array $inputfields		the checkboxes are added
	 * @param array $attachmentfiles	all uploaded files
	 * @param string $option_name		name of option to load
	 */
	protected function add_filesection(&$inputfields = array(), &$attachmentfiles = array(), $option_name = '')
	{
		$filestoadd = null;
		if(isset($this->options[$option_name]))
		{
			$filestoadd = $this->options[$option_name];
		}
		//	fallback
		if(!is_array($filestoadd))
		{
			$filestoadd = array();
		}
		
		foreach ($attachmentfiles as $name) 
		{
			$checked = '';
			if(in_array($name, $filestoadd))
				$checked = 'checked';
			
			$inputfields[] = array(
						'type'		=>	self::FORM_CHECKBOX,
						'id'		=>  $option_name."[$name]",
						'name' 		=>  '',
						'desc'		=>  $name,
						'css'		=>  'filedelete',
						'checked'	=>	$checked,
						'id_delete' =>	$name
					);
		}
	}
	
	/**
	 * Saves the options in own option entry
	 */
	protected function save_all_options()
	{
		$this->options[self::OPT_DEL_ON_DEACTIVATE] = isset($_REQUEST[self::OPT_DEL_ON_DEACTIVATE]);
		$this->options[self::OPT_DEL_ON_UNINSTALL] = isset($_REQUEST[self::OPT_DEL_ON_UNINSTALL]);
		
		$oldpath = $newpath = strtolower($this->options[self::OPT_UPLOAD_FOLDER]);
		if(isset($_REQUEST[self::OPT_UPLOAD_FOLDER]))
		{
			$newpath = trim(strtolower(stripslashes($_REQUEST[self::OPT_UPLOAD_FOLDER])));
			$newpath = str_replace('\\', '/', $newpath);
			if(strlen($newpath) == 0)
			{
				$newpath = $oldpath;
			}
			else
			{
				$newpath = trailingslashit($newpath);
			}
			if(strcasecmp ($oldpath, $newpath)!= 0)
			{
				$this->options[self::OPT_UPLOAD_FOLDER] = $this->move_uploadfolder($oldpath, $newpath);
			}
		}
		
		
		if(isset($_REQUEST[self::OPT_EMAIL_NOTIFICATION_HEADLINE]) && !empty($_REQUEST[self::OPT_EMAIL_NOTIFICATION_HEADLINE]))
		{
			$this->options[self::OPT_EMAIL_NOTIFICATION_HEADLINE] = trim($_REQUEST[self::OPT_EMAIL_NOTIFICATION_HEADLINE]);
		}
		else
			$this->options[self::OPT_EMAIL_NOTIFICATION_HEADLINE] = '';
		
		if(isset($_REQUEST[self::OPT_EMAIL_NOTIFICATION_TEXT]) && !empty($_REQUEST[self::OPT_EMAIL_NOTIFICATION_TEXT]))
		{
			$this->options[self::OPT_EMAIL_NOTIFICATION_TEXT] = trim($_REQUEST[self::OPT_EMAIL_NOTIFICATION_TEXT]);
		}
		else
			$this->options[self::OPT_EMAIL_NOTIFICATION_TEXT] = '';
		
		
		$this->options[self::OPT_ATT_NEW_ORDER] = $this->get_selected_files(self::OPT_ATT_NEW_ORDER);
		$this->options[self::OPT_ATT_PROCESSING] = $this->get_selected_files(self::OPT_ATT_PROCESSING);
		$this->options[self::OPT_ATT_COMPLETED] = $this->get_selected_files(self::OPT_ATT_COMPLETED);
		$this->options[self::OPT_ATT_INVOICE] = $this->get_selected_files(self::OPT_ATT_INVOICE);
		$this->options[self::OPT_ATT_NOTE] = $this->get_selected_files(self::OPT_ATT_NOTE);
		$this->options[self::OPT_ATT_LOW_STOCK] = $this->get_selected_files(self::OPT_ATT_LOW_STOCK);
		$this->options[self::OPT_ATT_NO_STOCK] = $this->get_selected_files(self::OPT_ATT_NO_STOCK);
		$this->options[self::OPT_ATT_BACKORDER] = $this->get_selected_files(self::OPT_ATT_BACKORDER);
		$this->options[self::OPT_ATT_NEW_ACCOUNT] = $this->get_selected_files(self::OPT_ATT_NEW_ACCOUNT);
		
		$this->options[self::OPT_HEAD_NEW_ORDER] = $this->get_emailaddress(self::OPT_HEAD_NEW_ORDER);
		$this->options[self::OPT_HEAD_PROCESSING] = $this->get_emailaddress(self::OPT_HEAD_PROCESSING);
		$this->options[self::OPT_HEAD_COMPLETED] = $this->get_emailaddress(self::OPT_HEAD_COMPLETED);
		$this->options[self::OPT_HEAD_INVOICE] = $this->get_emailaddress(self::OPT_HEAD_INVOICE);
		$this->options[self::OPT_HEAD_NOTE] = $this->get_emailaddress(self::OPT_HEAD_NOTE);
		$this->options[self::OPT_HEAD_LOW_STOCK] = $this->get_emailaddress(self::OPT_HEAD_LOW_STOCK);
		$this->options[self::OPT_HEAD_NO_STOCK] = $this->get_emailaddress(self::OPT_HEAD_NO_STOCK);
		$this->options[self::OPT_HEAD_BACKORDER] = $this->get_emailaddress(self::OPT_HEAD_BACKORDER);
		$this->options[self::OPT_HEAD_NEW_ACCOUNT] = $this->get_emailaddress(self::OPT_HEAD_NEW_ACCOUNT);
		
		
		update_option(self::OPTIONNAME, $this->options);
		
		self::$admin_message = 'Options saved';
	}
	
	/**
	 * Returns all selected files for attachment by the user on the admin page.
	 * key for options are the filenames selected for the option.
	 * 
	 * @param string $optionname	name of option to load
	 * @return array				filenames selected for option
	 */
	protected function get_selected_files($optionname)
	{
		$selected = array();
		
		if(!isset($_REQUEST[$optionname]))
			return $selected;
		
		$requ = $_REQUEST[$optionname];
			
		foreach ($requ as $key => $value) 
		{
			$selected[] = $key;
		}
		return $selected;
	}
	
	/**
	 * Returns all entered emails (cc and bcc) by the user on the admin page.
	 * 
	 * @param string $optionname	name of option to load
	 * @return array				valid emails entered for option
	 */
	protected function get_emailaddress($optionname)
	{
		$emails = array(
			'cc'	=> '',
			'bcc'	=> ''
		);
		
		if(isset($_REQUEST[$optionname]))
		{
			$arr = $_REQUEST[$optionname];
			if(isset($arr['cc']))
				$emails['cc'] = trim($arr['cc']);
			if(isset($arr['bcc']))
				$emails['bcc'] = trim($arr['bcc']);
		}
		
		return $emails;
	}

	/**
	 * Returns all existing files in the upload directory lowercase sorted by name
	 * 
	 * This function checks for lowercase and specioal characters to recognise manual upload.
	 * Filenames are changed if necessary.
	 * 
	 * @param bool $skipdefaults	if true, it skips the default files in the list
	 * @return array				files in the upload directory
	 */
	protected function get_all_files($skipdefaults = true)
	{
		$files = array();
		
		$path = trailingslashit($this->options[self::OPT_UPLOAD_FOLDER]);
		if(empty($path))
			return $files;
		
		if(!is_dir($path))
			return $files;
		
		$files = scandir($path);
		$retfiles = array();
		if(($files === false) || empty($files))
			return $retfiles;
		
		foreach ($files as $name)
		{
			if(($name == ".") || ($name == ".."))
				continue;
				//	skip any files you do not want to display
			if($skipdefaults && in_array($name, self::$skip_files))
				continue;
				//	skip folders
			if(is_dir($path.$name))
				continue;
			
			if(!is_file($path.$name))
				continue;
			
			//	file not to be displayed need not be sanatized
			if(in_array($name, self::$skip_files))
			{
				$retfiles[] = $name;
				continue;
			}
			
			//	Test for manual upload and change filename
			$filename = sanitize_file_name(strtolower ($name));
			if(strcmp($filename, $name) == 0)
			{
				$retfiles[] = $name;
				continue;
			}
			
			$renamed = false;
			$fn = $filename;
			$ext = '';
			$i = 1;
			while(!$renamed)
			{
				if(!file_exists($path.$fn))
				{
					if(rename($path.$name, $path.$fn))
					{
						$renamed = true;
						break;
					}
				}
				
				$parts = explode('.', $filename);
				$anz = count($parts);
				
				if($anz == 1)
				{
					$fn = $filename;
					$ext = '';
				}
				else
				{
					$ext = array_pop($parts);
					$fn = implode('.', $parts);
				}
				
				$fn .= '_'.strval($i);		//	add '_xx'
				if(!empty($ext))
				{
					$fn .= '.'.$ext;
				}
				$i++;
				//	leave loop to avoid endless loop and try to delete file
				if($i > 30)
				{
					unlink($path.$name);
					break;
				}
			}
			if($renamed)
			{
				$retfiles[] = $fn;
			}
		}
		return $retfiles;
	}
	
	/**
	 * Copies the file to new directory. Creates the new one and try to delete the old one.
	 * 
	 * @param string $oldpath
	 * @param string $newpath 
	 */
	protected function move_uploadfolder($oldpath, $newpath)
	{
		$created = woocommerce_email_attachments_activation::create_folder($newpath, false);
		if(!$created)
		{
			self::$admin_message .= __('Failed to create new directory:', 'woocommerce_email_attachments');
			self::$admin_message .= ' "'.$newpath.'". ';
			self::$admin_message .= __('Old Directory remains valid:', 'woocommerce_email_attachments');
			self::$admin_message .= ' "'.$oldpath.'". ';
			return $oldpath;
		}
		
		$files = $this->get_all_files(false);
		
		$newf = array();
		$oldf = array();
		$err = false;
		foreach ($files as $name)
		{
			$old = $oldpath.$name;
			$new = $newpath.$name;
			
			if(!copy($old, $new))
			{
				self::$admin_message .= __('Failed to copy file to new directory:', 'woocommerce_email_attachments');
				self::$admin_message .= ' "'.$old.'". ';
				self::$admin_message .= __('Old Directory and content remain valid:', 'woocommerce_email_attachments');
				self::$admin_message .= ' "'.$oldpath.'". ';
				$err = true;
				break;
			}
			$oldf[] = $old;
			$newf[] = $new;
		}
		
		//	delete copied files if error
		if($err)
		{
			foreach($newf as $name)
			{
				unlink($name);
			}
			rmdir($newpath);
			return $oldpath;
		}
		
		//	delete all old files
		foreach($oldf as $name)
		{
			unlink($name);
		}
		
		rmdir($oldpath);
		return $newpath;
	}

	/**
	 * Responds to implement a custom part of the "Settings" - FORM_CHECKBOX = "wc_ip_chackbox";
	 * 
	 * @param array|mixed $value Value to display in <tr>...<tr>
	 */
	public function set_checkbox_field($value = null)
	{
		?>
	<tr valign="top" class="<?php echo $value['css'] ?>">
		<th scope="row" class="titledesc"><?php 
			echo $value['name'];?>
		</th>
			<td class="forminp">
				<fieldset>
	            <legend class="screen-reader-text"><span><?php echo $value['name'] ?></span></legend>
					<label for="<?php echo $value['id'] ?>">
					<input name="<?php echo  $value['id'] ; ?>" id="<?php echo  $value['id']; ?>" type="checkbox" value="1" <?php echo $value['checked']; ?> />
					<?php echo $value['desc'] ?></label><br>
				</fieldset>
			</td>
			<td>
				<?php 
			if(isset($value['id_delete'])) {?>
				<a href="#" filedelete="<?php echo  $value['id_delete']; ?>" class="delete">x</a>
			<?php }?>
			</td>
	</tr>
				<?php
	}
	
	/**
	 * Responds to implement a custom part of the "Settings" - FORM_AJAXLOAD = "wc_ip_ajaxload";
	 * 
	 * @param array|mixed $value Value to display in <tr>...<tr>
	 */
	public function set_ajaxload_field( $value = null)
	{
		isset($value['desc']) ? $mess =  $value['desc']: $mess = __('Loading...','woocommerce_email_attachments') ;
		?>
		<tr valign="top">
			<th></th>
			<td>
				<div id="load" align="center">
					<img src="<?php echo plugins_url( 'images/loading.gif' , __FILE__ );?>" height="28" align="absmiddle"/> <?php echo $mess;?>
				</div>
			</td>
		</tr>
		<?php
	}
	
	/**
	 * Responds to implement a custom part of the "Settings" - FORM_UPLOAD = "wc_ip_fileupload";
	 * 
	 * @param array|mixed $value Value to display in <tr>...<tr>
	 */
	public function set_upload_file_field($value = null)
	{
		if(empty ($value))
			return;
		
		$up = new inoplugs_plupload(self::ID_PLUPLOAD_UNIQUE);
		$upload = $up->get_element_html();
		
		?>
		<div id="wc_plupload_container" ><span><?php echo $upload ?></span></div>
		<div id="<?php echo $value['id'].'refresh' ?>" class="button"><span><?php echo $value['refresh'] ?><span></div>
					<div style="clear: both;"><span id="status" ></span></div>
		<ul id="files" ></ul>
        <?php
	}
	
	/**
	 * Responds to implement a custom part of the "Settings"
	 * 
	 * 'id' string		value for id
	 * 'class' string	value for class
	 * 'css' string		complete string added to div without modificatioon
	 * 
	 * @param array|mixed $value Value to start a toggle div area
	 */
	public function set_toggle_start_field($value = null)
	{
		if(empty ($value))
			return;
		
		echo '<div';
		
		if(isset($value['id'])) 
		{
			echo ' id="'.$value['id'].'"';
		}
		
		if(isset($value['class']))
		{
			echo ' class="'.$value['class'].'"';
		}
		
		if(isset($value['css']))
		{
			echo ' '.$value['css'];
		}
		
		echo '>';
	}
	
	/**
	 * Responds to implement a custom part of the "Settings"
	 * 
	 * 'tag' string		Headline tag (h3, h2, ....)
	 * 'id' string		value for id
	 * 'class' string	value for class
	 * 'css' string		complete string added to tag without modificatioon
	 * 
	 * @param array|mixed $value Value to start a toggle div area
	 */
	public function set_toggle_title_field($value = null)
	{
		if(empty ($value))
			return;
		
		//	Fallback
		if(!isset ($value['tag']))
		{
			$value['tag'] = 'h3';
		}
		
		echo '<'.$value['tag'];
		
		if(isset($value['id'])) 
		{
			echo ' id="'.$value['id'].'"';
		}
		
		if(isset($value['class']))
		{
			echo ' class="'.$value['class'].' open"';
		}
		
		if(isset($value['css']))
		{
			echo ' '.$value['css'];
		}
		
		echo '>';
		
		if(isset($value['title']))
		{
			echo ' '.htmlentities($value['title'], ENT_QUOTES);
		}
		
		echo '</'.$value['tag'].'>';
	}
	
	
	/**
	 * Responds to implement a custom part of the "Settings".
	 * 
	 * 'id' string		value for id
	 * 'class' string	value for class
	 * 'css' string		complete string added to div without modificatioon
	 * 
	 * @param array|mixed $value Value to start a toggle div area
	 */
	public function set_toggle_area_field($value = null)
	{
		$this->set_toggle_start_field($value);
	}
	
	
	
	/**
	 * Closes the 2 open div's from toggle section
	 *  
	 * @param type $value 
	 */
	public function set_toggle_end_field($value = null)
	{
		if(empty ($value))
			return;
		
		echo '</div> </div>';
	}
	
	/**
	 * Is identical to woocommerce text (woocommerce-admin-settings-forms.php), 
	 * but adds email="check"
	 * 
	 * @param type $value 
	 */
	public function set_email_field($value = null)
	{
		if(empty ($value))
			return;
		
		isset($value['desc']) ? $description = '<span class="description">'.$value['desc'].'</span>' : $description = '';
		isset($value['email']) ? $email = ' email="'.$value['email'].'" ' : $email = '';
		isset($value['emailname']) ? $emailname = ' emailname="'.$value['emailname'].'" ' : $emailname = '';
		
		?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                    <td class="forminp"><input name="<?php echo esc_attr( $value['id'] ); ?>"<?php echo $email; echo $emailname;?>id="<?php echo esc_attr( $value['id'] ); ?>" type="<?php echo esc_attr( $value['type'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" value="<?php if ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) { echo esc_attr( stripslashes( get_option($value['id'] ) ) ); } else { echo esc_attr( $value['default'] ); } ?>" /> <?php echo $description; ?></td>
          </tr><?php
	}

	/**
	 * Copys uploaded file to destination folder - overrides if file exists
	 */
	public function handler_ajax_wc_upload_file() 
	{
		$nonce = $_POST[self::AJAX_NONCE];
		$testnonce = wp_verify_nonce( $nonce, self::AJAX_NONCE );
					
		// response output
		header( "Content-Type: application/json" );
		$response = array(self::AJAX_NONCE => wp_create_nonce( self::AJAX_NONCE ));
		
		if (!((current_user_can( 'manage_options' )) && $testnonce))
		{
			$response ['message'] = __('Sorry, you don\'t have permisson to upload files!', 'woocommerce_email_attachments');
			echo json_encode( $response );
			exit;
		}
		
		if(!isset($this->options[self::OPT_UPLOAD_FOLDER]))
		{
			$response ['message'] = __('No Uploadfolder set', 'woocommerce_email_attachments');
			echo json_encode( $response );
			exit;
		}		
				
		$uploaddir = $this->options[self::OPT_UPLOAD_FOLDER];
		
		//	lower case and eliminate special characters
		$fn = sanitize_file_name(strtolower(trim(basename($_FILES[inoplugs_plupload::VALUE_UPLOADED_FILE_NAME]['name'])))); 
		
		$file = $uploaddir.$fn; 

		//	fallback if folder not exists
		if(!is_dir($uploaddir))
		{
			$isdir = woocommerce_email_attachments_activation::create_folder($uploaddir);
			if(!$isdir)
			{
				$response ['message'] = __('File not uploaded - Uploadfolder cannot be created', 'woocommerce_email_attachments');
				echo json_encode( $response );
				exit;
			}
		}
		
		if(file_exists($file))
		{
			$response ['message'] = __('File already exists', 'woocommerce_email_attachments');
			echo json_encode( $response );
			exit;
		}
		
		$mess = "";
		if (move_uploaded_file($_FILES[inoplugs_plupload::VALUE_UPLOADED_FILE_NAME]['tmp_name'], $file)) 
		{ 
			$response ['message'] = 'success';
			$response ['newname'] = $fn;
		} 
		else 
		{
			$response ['message'] = __('File could not be saved', 'woocommerce_email_attachments');
		}
		
		echo json_encode( $response );
		exit;
	}
	
	/**
	 * Deletes requested file
	 */
	public function handler_ajax_delete_file() 
	{
		$nonce = $_POST[self::AJAX_NONCE];
		$testnonce = wp_verify_nonce( $nonce, self::AJAX_NONCE );
		
		// response output
		header( "Content-Type: application/json" );
		$response = array(self::AJAX_NONCE => wp_create_nonce( self::AJAX_NONCE ));
		
		if (!((current_user_can( 'manage_options' )) && $testnonce))
		{
			$response ['message'] = __('Sorry, you don\'t have permisson to delete files!', 'woocommerce_email_attachments');
			echo json_encode( $response );
			exit;
		}
		
		if(!isset($this->options[self::OPT_UPLOAD_FOLDER]))
		{
			$response ['message'] = __('No Uploadfolder set', 'woocommerce_email_attachments');
			echo json_encode( $response );
			exit;
		}		
				
		$uploaddir = $this->options[self::OPT_UPLOAD_FOLDER];
		$file = $uploaddir . basename($_POST['filename']); 

		$del = true;
		if(file_exists($file))
		{
			$del = unlink($file);
		}
		
		if($del)
			$response ['message'] = 'success';
		else
			$response ['message'] = __('File could not be deleted', 'woocommerce_email_attachments');
		echo json_encode( $response );
		exit;
	}
	
	/**
	 * Shows messages on the admin page fitting standard WP-design
	 */
	public static function show_admin_messages()
	{
		// Only show to admins
		if (current_user_can('manage_options')) 
		{
//			if(!empty(self::$admin_message))
//				self::show_message(self::$admin_message);
		}
		return;
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
	private static function show_message($message, $errormsg = true)
	{
		if ($errormsg) {
			echo '<div id="message" class="error">';
		}
		else {
			echo '<div id="message" class="updated fade">';
		}

		echo "<p><strong>$message</strong></p></div>";
	}    
	
	
	
	/**
	 * Add attachment notification text to email footer
	 */
	public function handler_email_attachment_footer() 
	{
		if(!$this->attachments_sent)
				return;
		
		$head = $this->options[self::OPT_EMAIL_NOTIFICATION_HEADLINE];
		$text = $this->options[self::OPT_EMAIL_NOTIFICATION_TEXT];
		
		if(empty($head) && empty($text))
			return;
		
		?>
		<div style="float:left; width: 100%;">
		<?php if(!empty($head)): ?>
			<h3 style="float:left; width: 100%;"><?php echo $head; ?></h3>
		<?php endif;
				if(!empty($text)):?>
			<p style="float:left; width: 100%;"><?php echo $text; ?></p>
		<?php endif; ?>
		</div>
		
		<?php
	}
	
	/**
	 * Plupload filter handler for messages
	 */
	public function handler_translate_messages($messages, $unique_id)
	{
		if($unique_id != self::ID_PLUPLOAD_UNIQUE)
			return;
		
		if(!is_array($messages))
			return $messages;
		
		$max_size = wp_max_upload_size();
		
		$translate = array(
				'delete_this_file'	=> __('Delete this file','woocommerce_email_attachments'),
				'delete'			=> __('Delete','woocommerce_email_attachments'),
				'edit'				=> __('Edit','woocommerce_email_attachments'),
				'files uploading'	=> __('Files Uploading','woocommerce_email_attachments'),
				'uploaded files'	=> __('Uploaded Files','woocommerce_email_attachments'),
				'upload files'		=> __('Upload Files for Attachment','woocommerce_email_attachments').' (max. '.$max_size.' byte):',
				'drop images here'	=> __('Drop Files Here','woocommerce_email_attachments'),
				'or'				=> __('or','woocommerce_email_attachments'),
				'title browse button' => __('Open your File Browser','woocommerce_email_attachments'),
				'text browse button' => __('Choose Your Files From Local Computer','woocommerce_email_attachments')
			);
		
		$new = array_merge($messages, $translate);
		return $new;
	}

	/**
	 * Plupload filter handler for hidden fields
	 */
	public function handler_set_hidden_field_data($data, $unique_id)
	{
		if($unique_id != self::ID_PLUPLOAD_UNIQUE)
			return;
		
		if(!is_array($data))
			return $data;
		
		$data[inoplugs_plupload::ID_MAX_FILE_UPLOAD] = 10;
		$data[inoplugs_plupload::ID_HIDE_ON_MAX_FILE] = 0;
		$data[inoplugs_plupload::ID_ACTION_CALLBACK] = woocommerce_email_attachments::VAL_PLUPLOAD_CALLBACK;
		$data[inoplugs_plupload::ID_SHOW_UPLOADED_IMAGES] = 0;
		$data[inoplugs_plupload::ID_JAVA_FILES_ADDED] = woocommerce_email_attachments::VAL_JAVA_FILES_ADDED;
		$data[inoplugs_plupload::ID_JAVA_ERROR] = woocommerce_email_attachments::VAL_JAVA_ERROR;
		$data[inoplugs_plupload::ID_JAVA_UPLOAD_PROGRESS] = '';
		$data[inoplugs_plupload::ID_JAVA_FILE_UPLOADED] = woocommerce_email_attachments::VAL_JAVA_FILES_UPLOADED;
		
		$data[self::AJAX_NONCE] = wp_create_nonce( self::AJAX_NONCE); 
		
		return $data;
	}
	
}
?>
