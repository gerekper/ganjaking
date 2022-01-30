<?php
/**
 * Implements the pluploader as a class.
 * 
 * @author Schoenmann Guenter
 * @version 1.0.0.1
 */
class inoplugs_plupload
{
	const DEFAULT_ID = 'inoplugs';
	const NONCE_CONTEXT = 'inoplugs_plupload';
	
	const ID_NONCE = 'inoplugs_plupload_nonce';
	const ID_UNIQUE_ID = 'inoplugs_plupload_unique_id';
	const ID_UPLOADED_FILE_KEY = 'inoplugs_uploaded_file_key';
	const ID_MAX_FILE_UPLOAD = 'inoplugs_plupload_max_file_upload';
	const ID_HIDE_ON_MAX_FILE = 'inoplugs_plupload_hide_on_max_file';
	const ID_ACTION_CALLBACK = 'inoplugs_plupload_action_callback';
	const ID_JAVA_INIT = 'inoplugs_plupload_java_init';
	const ID_JAVA_FILES_ADDED = 'inoplugs_plupload_java_files_added';
	const ID_JAVA_ERROR = 'inoplugs_plupload_java_error';
	const ID_JAVA_UPLOAD_PROGRESS = 'inoplugs_plupload_java_upload_progress';
	const ID_JAVA_FILE_UPLOADED = 'inoplugs_plupload_java_file_uploaded';
	const ID_SHOW_UPLOADED_IMAGES = 'inoplugs_plupload_show_uploaded_images';
	const ID_POST_ID = 'inoplugs_plupload_post_id';
	
	const ID_ATTR_PLUPLOAD = 'inoplugs_plupload';
	
	const VALUE_AJAX_ACTION = 'inoplugs_plupload_image_upload';
	const VALUE_UPLOADED_FILE_NAME = 'inoplugs_async_upload';
	
	const ADMINPAGE_ONLY = 1;
	const PAGES_ONLY = 2;		//	currently not supported
	const ALL_PAGES = 3;
	
	/**
	 * All created objects with key = UniqueID of objects. Can be retrieved at any time
	 * from outside by $unique_id.
	 * 
	 * @var array
	 */
	static protected $objects;
	
	/**
	 * Array containing parameters for configuring plupload
	 * 
	 * @var array
	 */
	static protected $plupload_params;
	
	/**
	 * Where to load the CSS and JS Files
	 * 
	 * @var integer
	 */
	static protected $show_on_pages;


	/**
	 * Unique ID to identify this object. Is used in HTML output to unify each uploadfield.
	 * Should be checked immedeatly after creation to ensure proper function.
	 * @var string
	 */
	protected $unique_id;
	
	/**
	 * Contains the key => value that are stored in hidden fields and can be retrieved on
	 * ajax request
	 * 
	 * @var array
	 */
	protected $data;

	/**
	 * array of messages to print on HTML output - can be localized
	 * 
	 * @var array
	 */
	public $messages;


	public function __construct($unique_id = '', $create_unique_id = false) 
	{
		if(!isset(self::$objects))
		{
			self::$objects = array();
		}
		
		if(!isset(self::$plupload_params))
		{
			self::$plupload_params = array(
				'runtimes'				=> 'html5,silverlight,flash,html4',
				'file_data_name'		=> self::VALUE_UPLOADED_FILE_NAME,
				'multiple_queues'		=> true,
//				'max_file_size'			=> wp_max_upload_size() . 'b',      must be set later - function not exists now
				'url'					=> admin_url( 'admin-ajax.php' ),
				'flash_swf_url'			=> includes_url( 'js/plupload/plupload.flash.swf' ),
				'silverlight_xap_url'	=> includes_url( 'js/plupload/plupload.silverlight.xap' ),
				'filters'				=> array( array(
						'title'				=> 'Allowed all Files',
						'extensions'		=> '*'
						)),
				'multipart'				=> true,
				'multipart_params'		=> array(
						'action'			=> self::VALUE_AJAX_ACTION
							),
				'urlstream_upload'		=> true,
			);
		};
		
		if(!isset(self::$show_on_pages))
		{
			self::$show_on_pages = self::ADMINPAGE_ONLY;
		}
		
		$this->unique_id = $this->create_unique_id($unique_id, $create_unique_id);
		self::$objects[$this->unique_id] = &$this;
		
			//	initialise standard values
		$this->data = array(
						self::ID_NONCE => '',
						self::ID_UPLOADED_FILE_KEY => self::VALUE_UPLOADED_FILE_NAME,
						self::ID_UNIQUE_ID => $this->unique_id,
						self::ID_MAX_FILE_UPLOAD => 0,
						self::ID_HIDE_ON_MAX_FILE => 1,			//	1 = true, 0 = false
						self::ID_ACTION_CALLBACK => '',
						self::ID_JAVA_INIT => '',
						self::ID_JAVA_FILES_ADDED => '',
						self::ID_JAVA_ERROR => '',
						self::ID_JAVA_UPLOAD_PROGRESS => '',
						self::ID_JAVA_FILE_UPLOADED => '',
						self::ID_SHOW_UPLOADED_IMAGES => 1,			//	1 = true, 0 = false
						self::ID_POST_ID => 0
				);
	
		$this->messages = array(
				'delete_this_file' => 'Delete this file',
				'delete' => 'Delete',
				'edit' => 'Edit',
				'files uploading' => 'Files in uploading queue',
				'uploaded files' => 'Uploaded files',
				'upload files' => 'Upload files',
				'drop images here' => 'Drop images here',
				'or' => 'or',
				'title browse button' => 'Click to select files with browser',
				'text browse button' => 'Select Files'
			);	
	}

	public function __destruct() 
	{
		unset($this->data);
		unset($this->messages);
	}
	
	/**
	 * This function must be called immediatly after loading the class.
	 * It forces to attach the appropriate handlers to load the .css and .js
	 * files.
	 */
	static public function activate($needed = self::ADMINPAGE_ONLY)
	{
		$obj = null;
		isset($this) ? 	$obj = $this : $obj = new self();
		
		switch($needed)
		{
			case self::PAGES_ONLY:
			case self::ALL_PAGES:
				self::$show_on_pages = $needed;
				break;
			case self::ADMINPAGE_ONLY:
			default:
				self::$show_on_pages = self::ADMINPAGE_ONLY;
		}
		
			//	currently only this is supported
		self::$show_on_pages = self::ADMINPAGE_ONLY;
		
		add_action('admin_init', array(&$obj, 'handler_wp_admin_init' ));	
		
	}

		/**
	 * Checks, if the ID does not exist in the objects array and returns a new unique key.
	 * 
	 * @param string $id
	 * @param bool $create_unique_id
	 * @return string 
	 */
	protected function create_unique_id($id = '', $create_unique_id = false)
	{
		if(empty($id) || !is_string($id))
		{
			$id = self::DEFAULT_ID;
		}
		
		if(!is_bool($create_unique_id) || !$create_unique_id)
		{
			return $id;
		}
		
		$id_new = $id;
		$i = 0;
		while(array_key_exists($id_new, self::$objects))
		{
			$i++;
			$id_new = $id.'_'.$i;
		}
		
		return $id_new;
	}
	
	/**
	 * Returns the ID of this object.
	 * 
	 * @return string
	 */
	public function get_unique_id()
	{
		return $this->unique_id;
	}
	
	/**
	 * Returns the pointer to the inoplugs_plupload with $unique_id
	 * 
	 * @param string $unique_id
	 * @return inoplugs_plupload 
	 */
	static public function get_object($unique_id)
	{
		return self::$objects[$unique_id];
	}

	/**
	 * Register all stylesheets and js - Menus alredy exists
	 */
	public function handler_wp_admin_init()
	{
		$urlpath = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__));

		$dependent_css = array();
		$dependent_css = apply_filters('inoplugs_plupload_dependent_css', $dependent_css);
		wp_register_style( 'inoplugs_plupload_css', $urlpath . 'css/inoplugs_plupload.css', $dependent_css);

		$dependent_js = array( 'jquery-ui-sortable', 'wp-ajax-response', 'plupload-all' );
		$dependent_js = apply_filters('inoplugs_plupload_dependent_js', $dependent_js);
		wp_register_script( 'inoplugs_plupload_script', $urlpath . 'js/inoplugs_plupload.js', $dependent_js);
	
		$this->attach_register_print_styles();
		
			//	attach to ajax callbacks
		add_action( 'wp_ajax_'.self::VALUE_AJAX_ACTION, array(&$this , 'handler_image_upload' ) );
		add_action( 'wp_ajax_nopriv_'.self::VALUE_AJAX_ACTION, array(&$this , 'handler_image_upload' ) );
	}
	
	/**
	 * Called to attach styles to all pages or special pages only.
	 * Use registered $page handle to hook stylesheet loading (see Function Reference/wp enqueue style)
	 * 
	 * @param array|string $load_scripts_on_pages
	 */
	protected function attach_register_print_styles()
	{
		$load_scripts_on_pages = array();
		$load_scripts_on_pages = apply_filters('inoplugs_plupload_load_scripts_on_pages', $load_scripts_on_pages);
	
		if(empty($load_scripts_on_pages))
		{
			add_action('admin_print_styles', array(&$this, 'handler_wp_admin_print_styles'));
			return;
		}
		if(is_string($load_scripts_on_pages))
		{
			$load_scripts_on_pages = array($load_scripts_on_pages);
		}
		
		if(!is_array($load_scripts_on_pages))
			return;
		
		/* Using registered $page handle to hook stylesheet loading */
		foreach ($load_scripts_on_pages as $page) 
		{
			add_action( 'admin_print_styles-' . $page, 'handler_wp_admin_print_styles' );
		}
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function handler_wp_admin_print_styles()
	{
		wp_enqueue_style('inoplugs_plupload_css');
		wp_enqueue_script('inoplugs_plupload_script');

		$data = array('url_loader_img' => plugin_dir_url( __FILE__ ).'img/loader.gif');
		$data = apply_filters('inoplugs_plupload_general_data', $data);
		
		wp_localize_script('inoplugs_plupload_script', 'inoplugs_plupload_general_data', $data);
	
		self::$plupload_params['max_file_size'] = wp_max_upload_size() . 'b';
		$plupload_params = apply_filters('inoplugs_plupload_defaults', self::$plupload_params);
		wp_localize_script('inoplugs_plupload_script', 'inoplugs_plupload_defaults', $plupload_params);
	}

	/**
	 * Get image html
	 *
	 * @param int $img_id
	 *
	 * @return string
	 */
	protected function img_html( $img_id )
	{
		$i18n_del_file = _x( 'Delete this file', 'image upload', 'inoplugs' );
		$i18n_delete   = _x( 'Delete', 'image upload', 'inoplugs' );
		$i18n_edit     = _x( 'Edit', 'image upload', 'inoplugs' );

		$src = wp_get_attachment_image_src( $img_id, 'thumbnail' );
		$src = $src[0];
		$link = get_edit_post_link( $img_id );

		$html = <<<HTML
<li id='item_{$img_id}'>
<img src='{$src}' />
<div class='inoplugs_image_bar'>
	<a title='{$i18n_edit}' class='inoplugs_edit_file' href='{$link}' target='_blank'>{$i18n_edit}</a> |
	<a title='{$i18n_del_file}' class='inoplugs_delete_file' href='#' rel='{$img_id}'>{$i18n_delete}</a>
</div>
</li>
HTML;
		return $html;
	}
	
	/**
	 * Get field as a HTML string
	 *
	 * @return string
	 */
	public function &get_element_html()
	{
		$this->messages = apply_filters('inoplugs_plupload_translate_messages', $this->messages, $this->unique_id);
		$this->data = apply_filters('inoplugs_plupload_set_hidden_field_data', $this->data, $this->unique_id);
		
		
			//	Needed to retrieve Unique ID by JavaScript
		$surr_container = '<div id="inoplugs_plupload_main_container_'.$this->unique_id.'" class="inoplugs_plupload_main_container" ';
		$surr_container .= self::ID_ATTR_PLUPLOAD.'="'.$this->unique_id.'">';

		$clear = '<div class="inoplugs_plupload_clear_both"></div>';
		
		$surr_container .= $clear;
		
		$hidden = $this->get_hidden_section();
		$surr_container .= $hidden;
		
		$upload_container = $this->get_upload_container();
		$surr_container .= $upload_container;
		
		$surr_container .= $clear;
		
		$surr_container .= '</div>';
		return $surr_container;
		}

	/**
	 * Returns the hidden section with all information needed by JavaScript
	 * 
	 * @return string
	 */
	protected function &get_hidden_section()
	{
		$hidden_container = '<div id="inoplugs_plupload_hidden_'.$this->unique_id.'" class="hidden" ';
		$hidden_container .= 'inoplugs_plupload_configsection="'.$this->unique_id.'">';
		
			//	prepare for default behaviour
		$this->data[self::ID_NONCE] = wp_create_nonce(self::NONCE_CONTEXT);
		
		global $post;
		if(isset($post) && !empty ($post))
		{
			$this->data[self::ID_POST_ID] = $post->ID;
		}
		
			//	load all data in hidden fields - will be returned
		foreach ($this->data as $id => &$value) 
		{
			$hidden = '<input type="hidden" id="'.$id.'_'.$this->unique_id.'" ';
			$hidden .= 'name="'.$id.'" value="'.$value.'" '.self::ID_ATTR_PLUPLOAD.'_hidden="'.$this->unique_id.'">';
			$hidden_container .= $hidden;
		}
		unset($value);

		$hidden_container .= '</div>';
		return $hidden_container;
	}
	
	/**
	 * Returns the complete upload container
	 * 
	 * @return inoplugs_html_baseelement
	 */
	protected function &get_upload_container()
	{
		$at = array( 'id' => 'inoplugs_plupload_container_'.$this->unique_id,
					 'class' => 'inoplugs_plupload_container',
//					 'inoplugs_plupload' => $this->unique_id		
				);
		$upload_container = '<div id="inoplugs_plupload_container_'.$this->unique_id.'" class="inoplugs_plupload_container" ';
//		$upload_container .= 'inoplugs_plupload="'.$this->unique_id.'"';
		$upload_container .= '>';
		
		$upload = $this->get_upload_section();
		$upload_container .= $upload;
		
		$progress = $this->get_progress_section();
		$upload_container .= $progress;
		
		$uploaded = $this->get_uploaded_section();
		$upload_container .= $uploaded;
		
		$upload_container .= '</div>';
		return $upload_container;
	}
	
	/**
	 * Returns the section for displaying uploaded files
	 * 
	 * @return inoplugs_html_baseelement 
	 */
	protected function &get_uploaded_section()
	{	
		$at = 'id="inoplugs_plupload_uploaded_'.$this->unique_id.'"';
		$at .= ' class="inoplugs_plupload_uploaded_list inoplugs_plupload_hide_on_load"';
		$uploaded_container = "<div $at>";
		
		$uploaded_container .= '<h4>'.$this->messages['uploaded files'].'</h4>';
		
		$at = 'id="inoplugs_plupload_uploaded_files_'.$this->unique_id.'"';
		$at .= ' class="inoplugs_plupload_uploaded_images"';
		$uploaded_container .= "<ul $at></ul>";
		
		$uploaded_container .= '</div>';
		return $uploaded_container;
	}
	
	/**
	 * Returns the setion for implementing the upload
	 * 
	 * @return inoplugs_html_baseelement
	 */
	protected function &get_upload_section()
	{	
		$at = 'id="inoplugs_plupload_upload_'.$this->unique_id.'"';
		$at .= ' class="inoplugs_plupload_upload"';
		$upload_container = "<div $at>";
		
		$upload_container .= '<h4>'.$this->messages['upload files'].'</h4>';
		
				//	container for drag drop
		$at = 'id="inoplugs_plupload_dragdrop_'.$this->unique_id.'"';
		$at .= ' class="inoplugs_drag_drop hide_if_no_js"';
		$upload_subcont = "<div $at>";
		
		
		$at = 'id="inoplugs_drag_drop_inside_'.$this->unique_id.'"';
		$at .= ' class="inoplugs_drag_drop_inside"';
		$drag_cont = "<div $at>";
		
		$drag_cont .= '<p>'.$this->messages['drop images here'].'</p>';
		$drag_cont .= '<p>'.$this->messages['or'].'</p>';
		
			//	browse button
		$drag_cont .= '<p>';
		
		$at = 'class="button"';
		$at .= ' id="inoplugs_plupload_browse_button_'.$this->unique_id.'"';
		$at .= ' name="inoplugs_plupload_browse_button_'.$this->unique_id.'"';
		$at .= ' title="'.$this->messages['title browse button'].'"';
		$at .= ' value="'.$this->messages['text browse button'].'"';
		$drag_cont .= '<input type="button" '.$at.'>';
		
		$drag_cont .= '</p>';
		$drag_cont .= '</div>';
		
		$upload_subcont .= $drag_cont;
		$upload_subcont .= '</div>';
		$upload_container .= $upload_subcont;
		
		$upload_container .= '</div>';
		return $upload_container;
		
	}
	
	/**
	 * Returns the progress section
	 * 
	 * @return inoplugs_html_baseelement 
	 */
	protected function &get_progress_section()
	{	
		$at = 'id="inoplugs_plupload_progress_'.$this->unique_id.'"';
		$at .= ' class="inoplugs_plupload_progress inoplugs_plupload_hide_on_load"';
		$progress = "<div $at>";
		
		$progress .= '<h4>'.$this->messages['files uploading'].'</h4>';
		
		$at = 'id="inoplugs_plupload_progress_list_'.$this->unique_id.'"';
		$at .= ' class="inoplugs_plupload_progress_list"';
		$progress .= "<ul $at></ul>";
		
		$progress .= '</div>'; 
		return $progress;
	}

	/**
	 * Upload
	 * Ajax callback function
	 *
	 * @return error or (XML-)response
	 */
	public function handler_image_upload ()
	{
	//		does not work in IE !!!!
	//	check_admin_referer(-1, self::ID_NONCE);

		if(isset($_REQUEST[self::ID_ACTION_CALLBACK]))
		{
			$callback = $_REQUEST[self::ID_ACTION_CALLBACK];
			if(!empty($callback))
			{
				if(has_action($callback))
				{
					do_action($callback, $this);
					return;
				}
			}
		}
		
		
		
		$post_id = 0;
		if (is_numeric($_REQUEST[self::ID_POST_ID]))
			$post_id = (int) $_REQUEST[self::ID_POST_ID];

		// You can use WP's wp_handle_upload() function:
		$file       = $_FILES['async-upload'];
		$file_attr  = wp_handle_upload( $file, array(
			'test_form'=> true,
			'action'   => 'plupload_image_upload'
				));
		$attachment = array(
			'post_mime_type' => $file_attr['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file['name'])),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		// Adds file as attachment to WordPress
		$id = wp_insert_attachment( $attachment, $file_attr['file'], $post_id);
		if ( ! is_wp_error( $id ) )
		{
			$response = new WP_Ajax_Response();
			wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $file_attr['file']));
			if(isset($_REQUEST[self::ID_UNIQUE_ID]))
			{
				// Save file ID in meta field
				add_post_meta($post_id, $_REQUEST[self::ID_UNIQUE_ID], $id, false );
			}
			$response->add( array(
				'what' => 'inoplugs_image_response',
				'data' => self::img_html( $id )
			) );
			$response->send();
		}
		// Faster than die();
		exit;
	}
	

}
