<?php

/**
 * Class handles all attachments - hooks 
 * 
 * Uploads the files to a selectable Folder or a given default (see WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH).
 * Uses the standard WP Media 3.5 uploader, but stores the file in the previous described folder.
 * Files are kept as attachment files to a single dummy post (custom post type).
 * WPML is supported to attach files only, if the selected language is active or 'all languages' are selected.
 * All files are bound to the same language as the custom post.
 * 
 * The attachment files have to be deleted on user request, otherwise they are kept.
 * 
 * 
 * Optionstructure: "woocom_email_att" - see function get_options_default for description (changed with 3.0.0
 *		
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

final class WC_Email_Att
{
	const VERSION = '3.0.11';
	const OPTIONNAME = "woocom_email_att";
	const OPTIONNAME_NOTICE = 'woocom_email_att_notice';
	const OPTIONNAME_UPDATE = "woocom_email_att_update";
	const TEXT_DOMAIN = 'woocommerce_email_attachments';
	
	const AJAX_NONCE = 'woocommerce_email_attachments_nonce';
	
	const POST_TYPE = 'wc_eatt_files';
	const TAXONOMY = 'wc_eatt_entry';
	
	/**
	 * @var WC_Email_Att The single instance of the class
	 * @since 2.1
	 */
	static public $_instance = null;
	
	/**
	 * If true, deactivation checkbox is shown
	 * 
	 * @var boolean
	 */
	static public $show_activation;
	
	/**
	 * If true, uninstall checkbox is shown
	 * 
	 * @var boolean
	 */
	static public $show_uninstall;

	/**
	 *
	 * @var string
	 */
	static public $plugin_path;
	
	/**
	 *
	 * @var string
	 */
	static public $plugin_url;

	/**
	 *
	 * @var string 
	 */
	static public $plugin_base_name;

	/**
	 * Messagestring to show on admin page
	 * 
	 * @var string
	 */
	static public $admin_message;
	
	/**
	 * Filenames to ignore in folder for attachment files (e.g. .htaccess,...)
	 * 
	 * @var array
	 */
	static public $skip_files;
	
	/**
	 * Holds the options for this plugin
	 * 
	 * @var array 
	 */
	public $options;
	
	/**
	 * Defined EMail subjects set via filter hook to allow other plugins to add more
	 * 
	 * @var array 
	 */
	public $emailsubjects;
	
	/**
	 * Set to true, when $emailsubjects was initialized by handler_wc_email_classes
	 * 
	 * @var boolean
	 */
	public $emailsubjects_init;
	
	/**
	 * Saves current EMail subject as footer action does not support email_subject as parameter
	 * 
	 * @var string
	 */
	public $current_email_subject;
	
	/**
	 *
	 * @var order|product|null
	 */
	public $current_email_object;
	
	/**
	 *
	 * @var WC_Addons_Email_Att
	 */
	public $woo_addons;
	
	/**
	 * The only post entry to hold all attachment files
	 * 
	 * @var WP_Post 
	 */
	public $email_post;
	
	/**
	 * Stores all attachment infos of used files
	 * Key is Attachment ID
	 * 
	 * @var array
	 */
	public $attachment_infos;
	
	/**
	 *
	 * @var WC_Email_Att_Admin 
	 */
	protected $email_attachments_admin;
	
	/**
	 *
	 * @var WC_Email_Att_WPML 
	 */
	public $wpml;
	
	/**
	 * key = email subject
	 * value = true, if attachments had beem sent for the email subject
	 * 
	 * @var array 
	 */
	public $attachments_sent;
	
	/**
	 * Main wc_email_att Instance
	 *
	 * Ensures only one instance of wc_email_att is loaded or can be loaded.
	 *
	 * @see WC_email_attachments()
	 * @return WC_Email_Att - Main instance
	 */
	public static function instance() 
	{
		if ( is_null( self::$_instance ) ) 
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() 
	{
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', WC_Email_Att::TEXT_DOMAIN ), '3.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() 
	{
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', WC_Email_Att::TEXT_DOMAIN ), '3.0' );
	}
	
	/**
	 * 
	 */
	public function __construct() 
	{
		global $wc_email_att_htaccess;
		
		spl_autoload_register( 'WC_Email_Att::autoload' );
		
		if( ! isset( self::$show_activation ) )
		{
			self::$show_activation = true;
		}

		if( ! isset( self::$show_uninstall ) )
		{
			self::$show_uninstall = true;
		}

		if( ! isset( self::$plugin_path ) )
		{
			self::$plugin_path = '';
		}
		
		if( ! isset( self::$plugin_url ) )
		{
			self::$plugin_url = '';
		}
		
		if( ! isset( self::$plugin_base_name ) )
		{
			self::$plugin_base_name = '';
		}
		
		if( ! isset( self::$skip_files ) )
		{
			self::$skip_files = array();
		}
		
		WC_Email_Att_Func::$htaccess = $wc_email_att_htaccess;
		
		$this->woo_addons = ( is_admin() ) ? new WC_Addons_Email_Att() : null;
		$this->email_attachments_admin = ( is_admin() ) ? new WC_Email_Att_Admin() : null;
		
		$this->emailsubjects = array(
							'new_order'					=> array(),
							'customer_processing_order' => array(),
							'customer_completed_order'	=> array(),
							'customer_invoice'			=> array(),
							'customer_note'				=> array(),
							'customer_new_account'		=> array(),
							'customer_reset_password'	=> array(),
							'low_stock'					=> array(),
							'no_stock'					=> array(),
							'backorder'					=> array()
		);
		
		foreach ( $this->emailsubjects as $email_key => &$value ) 
		{
			$this->emailsubjects[ $email_key ] = array(
											'id' => $email_key,
											'title' => $email_key,
											'heading' => $email_key,
											'description' => '',
											'wc_email' => null
									);
		}
		unset( $value );
		
		$this->current_email_subject = '';
		$this->current_email_object = null;
		$this->emailsubjects_init = false;
		$this->attachment_infos = array();
		$this->wpml = new WC_Email_Att_WPML();
		$this->email_post = null;
		$this->attachments_sent = array();
		
				//	reload after WPML and German Market is activated in handler_wp_init
		$this->options = self::get_options_default( $this->emailsubjects );
		

		add_action( 'init', array( $this, 'handler_wp_load_textdomains' ), 1 );	
		add_action( 'init', array( $this, 'handler_wp_init' ), 1 );
		add_action( 'init', array( $this, 'handler_wp_register_cpt_email_att' ), 10 );
		
		//	added in 3.0.4 -> load email classes moved from woocommerce_init hook for "Order Status Manager" plugin
		add_action( 'init', array( $this, 'handler_wc_init'), 1000 );
		add_action( 'init', array( $this, 'handler_wc_email_att_init'), 1001 );
		
		$this->attach_to_wc_emails();
	}
	
	public function __destruct() 
	{
		unset( $this->woo_addons );
		unset( $this->email_attachments_admin );
		unset( $this->emailsubjects );
		unset( $this->current_email_object );
		unset( $this->attachment_infos );
		unset( $this->options );
		unset( $this->email_post );
		unset( $this->wpml );
		unset( $this->attachments_sent );
	}
	
	/**
	 * This function is called by the parser when it finds a class, that is not loaded already.
	 * Needed, because WC Classes might be loaded after our plugin and to ensure, that depending plugins can load classes.
	 *
	 * @param string $class_name		classname to load rendered by php-parser
	 */
	static public function autoload( $class_name )
	{
		$class_name = strtolower( $class_name );
		$filename = str_replace( '_', '-', $class_name );
		
			//	insert all folders, where class files may be found.
			//	files must follow following naming convention, all lowercase: 
			//				'class-' . $filename . '.php'
			//
		$folders_php = array(
					WC_Email_Att::$plugin_path . 'classes/',
					WC_Email_Att::$plugin_path . 'classes/panels/'
			);
		
		foreach( $folders_php as $folder )
		{
			$file = $folder . 'class-' . $filename . '.php';
			if( file_exists( $file ) )
			{
				require_once $file;
				return;
			}
		}
	}
	
	/**
	 * Filter woocommerce_email_classes is called in init hook -> therefore we need this to allow other plugins to rely on
	 * an initialised class
	 */
	public function handler_wc_email_att_init()
	{
		//	allow plugins to load after our plugin and can therefore rely on initialised class, if loaded before
		do_action( 'wc_email_att_init' );
	}

	/**
	 * Gets the options for this plugin and returns an array filled with all needed values initialised
	 * Updates to the new option structure if necessary
	 *
	 * @return array
	 */
	static public function &get_options_default( array $emailsubjects = array() )
	{
		$default = array();
		
		$default['version'] = WC_Email_Att::VERSION;
		$default['del_on_deactivate'] = false;
		$default['del_on_uninstall'] = true;
		$default['upload_folder'] = WOOCOMMERCE_ATTACHMENTS_DEFAULT_UPLOAD_PATH;	//  must be:  /foldername
		$default['plugin_base_name'] = self::$plugin_base_name;
				
		if( empty( $emailsubjects ) && isset( self::$_instance ) )
		{
			$emailsubjects = self::$_instance->emailsubjects;
		}
		
		foreach ( $emailsubjects as $emailkey => &$emailclass )
		{
			$default[ 'head_' . $emailkey ] = array(
											'cc' => '', 
											'bcc' => ''
											);
			$default[ 'att_' . $emailkey ] = array();
		}
		unset( $emailclass );
		
		$default['notification_headline'] = '';
		$default['notification_text'] = '';	
		$default['show_notes_always'] = true;
		
		$default = apply_filters( 'wc_eatt_options_default', $default, $emailsubjects );
		
		$options = get_option( self::OPTIONNAME, array() );
		
		$new_options = wp_parse_args( $options, $default );
		
		/**
		 * Force setting of option value since up to 3.0.0 this value is empty due to a programming error
		 */
		if( empty( $new_options['plugin_base_name'] ) )
		{
			$new_options['plugin_base_name'] = self::$plugin_base_name;
		}
		
		$old_opt = serialize( $options );
		$new_opt = serialize( $new_options );
		
		if( version_compare( $new_options['version'], self::VERSION, '!=' ) || ( $old_opt != $new_opt ) )
		{
			$new_options['version'] = WC_Email_Att::VERSION;
			update_option( self::OPTIONNAME, $new_options );
			
			//	fire action for classes to update option
			do_action( 'wc_email_att_options_changed', $new_options );
		}

		return $new_options;
	}

	/**
	 * Attach to WooCommerce Hooks with E-Mails
	 * 
	 * As other plugins my overwrite our settings we hook with a low priority to ensure, that our settings do not get lost
	 */
	public function attach_to_wc_emails()
	{	
		//	removed in 3.0.4 -> load email classes moved to wp init hook for "Order Status Manager" plugin
//		add_action( 'woocommerce_init', array( $this, 'handler_wc_init'), 100 );
		
		//  Attach to update all email_classes, if added some by other plugins
		add_filter( 'woocommerce_email_classes', array( $this, 'handler_wc_email_classes' ), 1000, 1 );
		
		//	Arrach to E-Mail Handlers for CC, BCC, additional headers
		add_filter( 'woocommerce_email_headers', array( $this, 'handler_wc_email_headers' ), 900, 3 );
		
		//	Arrach to E-Mail Handlers for attachments
		add_filter( 'woocommerce_email_attachments', array( $this, 'handler_wc_email_attachments' ), 900, 3 );	
		
		// add attachment notification text to email content
		add_action( 'woocommerce_email_footer', array( $this, 'handler_wc_email_attachment_footer' ), 900, 1 );
	}

	/**
	 * Set plugin url with filters hooked by other plugins
	 */
	public function handler_wp_init()
	{
		self::$plugin_url = trailingslashit(plugins_url( '', plugin_basename( dirname( __FILE__ ) ) ) );
		
			//	init WPML and reload options to fill default values of WPML
		$this->wpml->init();
		
		$this->options = self::get_options_default( $this->emailsubjects );
	}

	/**
	 * Localisation
	 **/
	public function handler_wp_load_textdomains()
	{
		$pos = strrpos( self::$plugin_base_name, '/' );
		if( $pos === false )
		{
			$pos = strrpos( self::$plugin_base_name, '\\' );
		}
		
		$language_path = ( $pos === false ) ? 'languages' : trailingslashit ( substr( self::$plugin_base_name, 0, $pos + 1 ) ) . 'languages';		
		load_plugin_textdomain( self::TEXT_DOMAIN, false, $language_path );
	}
	
	/**
	 * Registers custom Post type 
	 */
	public function handler_wp_register_cpt_email_att()
	{
		$labels = array(
			'name' => __( 'E-Mail Attachments', self::TEXT_DOMAIN ),
			'singular_name' => __( 'E-Mail Attachment', self::TEXT_DOMAIN ),
			'add_new' => __( 'Add New', self::TEXT_DOMAIN ),
			'add_new_item' => __( 'Add new E-Mail Attachment', self::TEXT_DOMAIN ),
			'edit_item' => __( 'Edit E-Mail Attachment', self::TEXT_DOMAIN ),
			'new_item' => __( 'New E-Mail Attachment', self::TEXT_DOMAIN ),
			'all_items' => __( 'All E-Mail Attachments', self::TEXT_DOMAIN ),
			'view_item' => __( 'View E-Mail Attachment', self::TEXT_DOMAIN ),
			'search_items' => __( 'Search E-Mail Attachments', self::TEXT_DOMAIN ),
			'not_found' =>  __( 'No E-Mail Attachment found', self::TEXT_DOMAIN ),
			'not_found_in_trash' => __( 'No E-Mail Attachment(s) found in Trash', self::TEXT_DOMAIN ), 
			'parent_item_colon' => '',
			'menu_name' => __( 'E-Mail Attachments', self::TEXT_DOMAIN )
					);
		$args = array(
			'labels' => $labels,
			'exclude_from_search' => true,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true, 
			'show_in_menu' => false, 
			'query_var' => true,
			'rewrite' => array( 'slug' => _x( 'E_Mail_Attachment', 'URL slug', self::TEXT_DOMAIN ) ),
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => true,
			'menu_position' => 300,
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ,'custom-fields','page-attributes'),
//			'taxonomies' => array('category', 'post_tag')
		  ); 
		register_post_type( self::POST_TYPE, $args );
		
		register_taxonomy( self::TAXONOMY, 
				array(	self::POST_TYPE), 
				array(	"hierarchical" => true, 
						"label" => "Categories E-Mail Attachments", 
						"singular_label" => "Category E-Mail Attachment", 
						"rewrite" => true,
						"query_var" => true
					)
				); 
		
//		register_taxonomy_for_object_type('category', 'my_post_type');
		
		if( empty( $this->email_post ) )
		{
			$this->email_post = $this->get_the_only_post();
		}
		
				//	set default WC Emails with translation text
		foreach ( $this->emailsubjects as $email_key => $email ) 
		{
			switch ( $email_key )
				{
					case 'low_stock':
						$id = $email_key;
						$title = __( 'Low Stock', self::TEXT_DOMAIN );
						$heading = __( 'Low Stock', self::TEXT_DOMAIN );
						$description = __( 'WooCommerce internal E-Mail: sent when a product reaches low stock.', self::TEXT_DOMAIN );
						break;
					case 'no_stock':
						$id = $email_key;
						$title = __( 'No Stock', self::TEXT_DOMAIN );
						$heading = __( 'No Stock', self::TEXT_DOMAIN );
						$description = __( 'WooCommerce internal E-Mail: sent when a product reaches no stock.', self::TEXT_DOMAIN );
						break;
					case 'backorder':
						$id = $email_key;
						$title = __( 'Backorder', self::TEXT_DOMAIN );
						$heading = __( 'Backorder', self::TEXT_DOMAIN );
						$description = __( 'WooCommerce internal E-Mail: sent when a product is on backorder.', self::TEXT_DOMAIN );
						break;
					default:
						if( isset( $email['wc_email'] ) )
						{
							$id = '';
						}
						else 
						{
							$id = $email_key;
							$title = __( 'Unknown E-Mailtype:', self::TEXT_DOMAIN ) . '  ' . $email_key;
							$heading = __( 'Unknown E-Mailtype:', self::TEXT_DOMAIN ) . '  ' . $email_key;
							$description = __( 'Unknown E-Mailtype:', self::TEXT_DOMAIN ) . '  ' . $email_key.'.  '. __( 'There is no description available. Please check with supplier of plugin or in documentation.', self::TEXT_DOMAIN );
						}
						break;
					}
				
				if( empty( $id ) )  
				{  
					continue;  
				}
				
				$this->emailsubjects[ strtolower( $email_key ) ] = array(
											'id' => $id,
											'title' => $title,
											'heading' => $heading,
											'description' => $description,
											'wc_email' => null
									);
		}
		
	}

	/**
	 * Needed to load email classes in admin to adjust options and add additional hook
	 */
	public function handler_wc_init()
	{
		/**
		 * Added with 3.0.11 as WPML makes problems sending mails from backend - translation does not work.
		 * Moved to class-wc-email-att-admin.php and limited to settings page only
		 */
		if( version_compare( WC()->version, '3.9', '>=' ) )
		{
			return;
		}
		
		if( is_admin() ) 
		{
				//	fires wc_email_classes -> handler_wc_email_classes
			$wc_emails = WC_Emails::instance();
			unset( $wc_emails );
		}
	}

	/**
	 * Attach to all defined E-Mail classes
	 * 
	 * @param array $wc_emails			'email_key' => WC_Email
	 * @return array
	 */
	public function handler_wc_email_classes( array $wc_emails ) 
	{
		if( $this->emailsubjects_init )  
		{
			return $wc_emails;
		}
		
		foreach ( $wc_emails as $email_key => &$wc_email ) 
		{
			if( ! $wc_email instanceof WC_Email )
			{	
				$id = $email_key;
				$title = __( 'Unknown E-Mailtype:', self::TEXT_DOMAIN ) . '  ' . $email_key;
				$heading = __( 'Unknown E-Mailtype:', self::TEXT_DOMAIN ) . '  ' . $email_key;
				$description = __( 'Unknown E-Mailtype:', self::TEXT_DOMAIN ) . '  ' . $email_key.'.  '. __( 'There is no description available. Please check with supplier of plugin or in documentation.', self::TEXT_DOMAIN );
			
				$this->emailsubjects[ strtolower( $email_key ) ] = array(
											'id' => $id,
											'title' => $title,
											'heading' => $heading,
											'description' => $description,
											'wc_email' => null
									);
			}
			else 
			{
				$this->emailsubjects[ strtolower( $wc_email->id ) ] = array(
											'id' => $wc_email->id,
											'title' => $wc_email->title,
											'heading' => $wc_email->heading,
											'description' => $wc_email->description,
											'wc_email' => $wc_email
									);
			}
		}
		unset( $wc_email );
		
		
				//	needed to identify subject, as action 'woocommerce_email_footer' does not send any parameters
		foreach ( $this->emailsubjects as $email_id => $value ) 
		{
			add_filter( 'woocommerce_email_subject_' . $email_id, array( $this, 'handler_wc_email_subject'), 10, 2 );
		}
		
		$this->options = self::get_options_default();
		$this->emailsubjects_init = true;
		
		return $wc_emails;
	}
	
	/**
	 * 
	 * @param string $subject_text
	 * @param WC_Order|WP_User|WC_Product|null $object		object needed to identify closer information about the email content (e.g. order, product, user)
	 * @return string
	 */
	public function handler_wc_email_subject( $subject_text, $object )
	{
		$filter = current_filter();
		$this->current_email_subject = str_replace( 'woocommerce_email_subject_', '', current_filter() );
		$this->current_email_object = $object;
		
		$subject_text = apply_filters( 'wc_email_attachments_email_subject_set', $subject_text, $object, $this->current_email_subject );
		return $subject_text;
	}
	
	/**
	 * WooCommerce main handler for CC and BCC and other EMail headings. 
	 * 
	 * @param string $headers
	 * @param string $emailsubject 
	 * @param WC_Order|WP_User|WC_Product|null $object		object needed to identify closer information about the email content (e.g. order, product, user)
	 * @return string										(see documentation of wp_mail) 
	 */
	public function handler_wc_email_headers( $headers = '', $emailsubject = '', $object = null )
	{
		if( empty($emailsubject) || ! is_string( $emailsubject ) )
		{
			return $headers;
		}
		
		$emailsubject = strtolower( $emailsubject );
		$this->current_email_subject = $emailsubject;
		$this->current_email_object = $object;
		
		if( ! array_key_exists( $emailsubject, $this->emailsubjects ) )
		{
			return $headers;
		}
		
		$headers = $this->add_headers( $headers, 'head_' . $emailsubject, $emailsubject, $object );
		return $headers;
	}
	
	/**
	 * WooCommerce main handler for attachments. Routes to the approtiate handl3er 
	 * to get the attachments.
	 * 
	 * @param array $attachment
	 * @param string $emailsubject 
	 * @param WC_Order|WP_User|WC_Product|null $object		object needed to identify closer information about the email content (e.g. order, product, user)
	 * @return array										(see documentation of wp_mail) 
	 */
	public function handler_wc_email_attachments( $attachment = array(), $emailsubject = '', $object = null )
	{
		if( empty($emailsubject ) || ! is_string( $emailsubject ) )
		{
			return $attachment;
		}
		
		$emailsubject = strtolower( $emailsubject );
		if( empty( $this->current_email_subject ) ) 
		{
			$this->current_email_subject = $emailsubject;
		}
		
		if( ! array_key_exists( $emailsubject, $this->emailsubjects ) )
		{
			return $attachment;
		}
		
		$attachment = $this->add_attachments( $attachment, 'att_' . $emailsubject, $emailsubject, $object );
		return $attachment;
	}
		
	/**
	 * Add attachment notification text to email footer only, if attachments have been sent
	 * 
	 * @param WC_Email	$email
	 */
	public function handler_wc_email_attachment_footer( $email ) 
	{
		$footers = array();		
		$note = array();
		
		/**
		 * since 3.0 this handler is called before attachments are checked -> force reading of attachments
		 */
		$this->handler_wc_email_attachments( array(), $this->current_email_subject, $this->current_email_object );
		
		$attachments_sent = isset( $this->attachments_sent[ $this->current_email_subject ] ) ? $this->attachments_sent[ $this->current_email_subject ] : false;
		$attachments = ( $this->options['show_notes_always'] == 'yes' ) ? true :  $attachments_sent;
		if( $attachments )
		{
			$note['head'] = apply_filters( 'wc_eatt_wpml_notification_headline', $this->options['notification_headline'], $this->options, $this->current_email_subject, $this->current_email_object );
			$note['text'] = apply_filters( 'wc_eatt_wpml_notification_text', $this->options['notification_text'], $this->options, $this->current_email_subject, $this->current_email_object );
		}
		
		if( ! empty( $note ) )
		{
			$footers[] = &$note;
			unset( $note );
		}
					
		$footers = apply_filters( 'wc_email_attachments_email_footer', $footers, $this->current_email_subject, $this->current_email_object );
		
		foreach ( $footers as &$footer ) 
		{
			$head = isset( $footer['head'] ) ? $footer['head'] : '';
			$text = isset( $footer['text'] ) ? $footer['text'] : '';
		
			if( empty( $head ) && empty( $text ) )  
			{  
				continue;  
			}
		
			?>
			<div style="float:left; width: 100%;">
			<?php if( ! empty( $head ) ): ?>
				<h3 style="float:left; width: 100%;"><?php echo esc_html($head); ?></h3>
			<?php endif;
					if( ! empty( $text ) ):?>
				<p style="float:left; width: 100%;"><?php echo esc_html($text); ?></p>
			<?php endif; ?>
			</div>

			<?php
			
			unset( $head );
			unset( $text );
			unset( $footer );
		}
	}
	
	/**==================================================================================
	 * ==================================================================================
	 *          HELPER FUNCTIONS
	 * ==================================================================================
	 *===================================================================================*/
	
	/**
	 * Adds the CC and BCC from the option array to the headers
	 * 
	 * @param string $headers									string must be "\r\n" seperated and must end with PHP_EOL (see documentation of wp_mail)
	 * @param string $head_option								name of index in the optionarray
	 * @param string $emailsubject								subject of eMail
	 * @param WC_Order|WP_User|WC_Product|null $object			object needed to identify closer information about the email content (e.g. order, product, user)object of eMail
	 * @return string											(see documentation of wp_mail) 
	 */
	protected function add_headers( $headers = '', $head_option = '', $emailsubject = '', $object = null )
	{
		if( ! is_string( $head_option ) )
		{
			return $headers;
		}
		
		if( empty( $head_option ) )
		{
			return $headers;
		}
				
		$adresses = array();
		if( isset ( $this->options[ $head_option ] ) )
		{	
			$adresses = $this->options[ $head_option ];
		}
			//	Get more headers in array('cc' => ..., 'bcc'=> ...) to be merged with given $headers
		$adresses = apply_filters( 'wc_email_attachments_email_headers', $adresses, $emailsubject, $object );
		/**
		 * Fallback in case someone other changed the structure -> ignore
		 */
		if( ! is_array( $adresses ) )
		{
			return $headers;
		}
		
		if( empty ( $adresses ) )
		{
			return $headers;
		}
		
		$addarray = true;
			
		/**
		 * Check for array or \r\n seperated string (see documentation for wp_mail)
		 */
		if( is_string( $headers ) )
		{
			if( false === strpos( $headers, "\r\n" ) )
			{
				$headers = trim( $headers );
				$headers = ( empty( $headers ) ) ? array() : array( $headers );
			}
			else
			{
				$addarray = false;
			}
		}
		
		/**
		 * Add the BC and CC adresses. The adresses are , seperated if exist
		 */
		foreach ( $adresses as $key => $value ) 
		{
			if( empty( $value ) )
			{
				continue;
			}
			
			if( $addarray )
			{
				$headers[] = $key.': ' . $value;
			}
			else
			{
				if( ! empty( $value ) )
				{
					$headers .= $key.': ' . $value . "\r\n";
				}
			}
		}
		unset( $value );
		
		/**
		 * Return "" in any case
		 */
		if( empty( $headers ) )
		{
			$headers = '';
		}
		
		return $headers;
	}


	/**
	 * Adds the array of filenames in $newfiles to $attachment. Checks, if the file added exists
	 * 
	 * @param array $attachment
	 * @param string $att_option								name of index in the options array
	 * @param string $emailsubject								subject of eMail
	 * @param WC_Order|WP_User|WC_Product|null	$object			object needed to identify closer information about the email content (e.g. order, product, user)
	 * @return array											(see documentation of wp_mail) 
	 */
	protected function add_attachments( $attachment = array(), $att_option = '', $emailsubject = '', $object = null )
	{
		if( ! is_string( $att_option ) )
		{
			return $attachment;
		}
		
		if( empty( $att_option ) )
		{
			return $attachment;
		}
				
		$newfiles = array();
		$all_attachments = array();
		if( ( isset ($this->options[ $att_option ] ) ) && ( is_array( $this->options[ $att_option ] ) ) )
		{
			$all_attachments = $this->options[ $att_option ];
		}
		
		$lang_option_name = 'att_wpml_' . str_replace( 'att_', '', $att_option );
		$langs = isset( $this->options[ $lang_option_name ] ) ? $this->options[ $lang_option_name ] : array();
		$all_attachments = apply_filters( 'wc_eatt_wpml_filter_attachments', $all_attachments, $langs );
		
		$this->attachments_sent[ $emailsubject ] = ! empty( $all_attachments );
		
			//	merge new attachment id's - you need not take care of duplicates - will be removed here later
		$all_attachments = apply_filters( 'wc_email_attachments_email_attachments', $all_attachments, $att_option, $emailsubject, $object );
		if( is_array( $all_attachments ) )
		{
			foreach ( $all_attachments as $attachment_id ) 
			{
				if( ! in_array( $attachment_id, $newfiles ) )
				{
					$newfiles[] = $attachment_id;
				}
			}
		}
		
		
		if( empty( $newfiles ) )
		{
			return $attachment;
		}
		
		$addarray = true;
			
		/**
		 * Check for array or CRLF seperated string (see documentation for wp_mail)
		 * 
		 * with WC 3.0 only array is used
		 */
		if( is_string( $attachment ) )
		{
			if( ( false === strpos( $attachment, "\r\n") ) )
			{
					//	one file only => put in array
				$attachment = trim( $attachment );
				$attachment = ( empty( $attachment ) ) ? array() : array( $attachment );
			}
			else
			{
				$addarray = false;
			}
		}
		
		/**
		 * Add all filenames and check if exist
		 */
		$add_ids = $this->get_attachment_files_info( $newfiles );
		foreach ( $add_ids as $attachment_id ) 
		{
			if( $this->attachment_infos[ $attachment_id]['error'] )  
			{  
				continue;  
			}
			
			$addfile = $this->attachment_infos[ $attachment_id ]['source'];
			if( file_exists( $addfile ) )
			{
				if( $addarray )
				{
					$attachment[] = $addfile;
				}
				else 
				{
					$attachment = empty( $attachment ) ? $addfile : $attachment . "\r\n" . $addfile;
				}
			}
		}
		
		/**
		 * Return "" in any case
		 */
		if( empty( $attachment ) )
		{
			$attachment = array();
		}
		
		return $attachment;
	}

	/**
	 * Gets the first and only post for email attachments
	 * 
	 * WPML hooks into query -> our post is duplicated, because it is no longer recognized in the standard WP query
	 * 
	 * @param boolean $create_new 
	 * @return WP_Post
	 */
	protected function &get_the_only_post( $create_new = true )
	{
		global $wpdb;
		
		$the_post = null;
		
				//	see comment above
		$request = "SELECT * FROM {$wpdb->posts} WHERE post_type = '" . self::POST_TYPE . "' ORDER BY ID ASC";
		$posts = $wpdb->get_results( $request );
		
		if( ( count( $posts ) == 0 ) && $create_new )
		{
			$post_data = array(
				'post_content'   =>  '*** This post is a dummy post to hold all attachment files for the plugin WooCommerce Email Attachments.',
				'post_name'      =>  'WooCommerce_Email_Attachments',
				'post_title'     =>  'WooCommerce Email Attachments Files',
				'post_status'    =>  'publish',
				'post_type'		 =>	 self::POST_TYPE,
//				'tax_input'      =>  array (self::TAXONOMY)
				);
			
			$id = wp_insert_post( $post_data );
			$posts = $wpdb->get_results( $request );
		}
		
		$the_post = ( count( $posts ) > 0 ) ? $posts[0] : null;
		
		if( is_null( $the_post ) )   
		{  
			return $the_post;
		}
		
		$this->wpml->add_the_only_post( $the_post->ID, self::POST_TYPE );
		
		return $the_post;
	}
	
	/**
	 * Deletes the only post, but NOT the attachments
	 * 
	 * WPML hooks into query -> our post might no longer be recognized in the standard WP query
	 * 
	 */
	public function delete_the_only_post()
	{
		global $wpdb;
		
		if( empty( $this->email_post ) )
		{
			$this->email_post = $this->get_the_only_post( false );
		}
		
		if( ! empty( $this->email_post ) ) 
		{
			wp_delete_post( $this->email_post->ID, true );
		}
			//	fallback to clean up database
			//	see comment above
		$request = "SELECT * FROM {$wpdb->posts} WHERE post_type = '" . self::POST_TYPE . "' ORDER BY ID ASC";
		$posts = $wpdb->get_results( $request );
		
		foreach ( $posts as $post ) 
		{
			wp_delete_post( $post->ID, true );
		}
	}

	/**
	 * Returns an array of the found attachment posts in the array $attachment_ids.
	 * Fills a local array with the info to avoid loading info multiple times
	 * 
	 * @param array $attachment_ids
	 * @return array
	 */
	public function &get_attachment_files_info( array $attachment_ids  )
	{
		$atts = array();
		if( empty( $attachment_ids ) )
		{
			return $atts;
		}
		
		foreach ( $attachment_ids as $id ) 
		{
			if ( ! array_key_exists( $id, $this->attachment_infos ) ) 
			{
				$this->attachment_infos[ $id ] = array(
							'source'	=> get_attached_file( $id ),
							'thumb'		=> wp_get_attachment_thumb_url( $id ),
							'metadata'	=> wp_get_attachment_metadata( $id ),
							'error'		=> false
				);
						//	file deleted or no attachment ??
				if( ! empty( $this->attachment_infos[ $id ]['source'] ) )
				{
					$path_parts = pathinfo( $this->attachment_infos[ $id ]['source'] );
					$this->attachment_infos[ $id ]['name'] = $path_parts['basename'];
				}
				else 
				{
					$this->attachment_infos[ $id ]['source'] = __( 'Unknown File - probably deleted', self::TEXT_DOMAIN );
					$this->attachment_infos[ $id ]['name'] = '';
					$this->attachment_infos[ $id ]['error'] = true;
				}
			}
			$atts[] = $id;
		}
		
		return $atts;		
	}
	

}

