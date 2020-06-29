<?php
/**
 * Connects this plugin to WPML
 *
 * @author Guenter Schönmann
 * @version 1.0.0
 * @since 3.0.0
 * 
 * @needs WPML 3.1.6
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class WC_Email_Att_WPML 
{
	const WPML_PLUGIN_NAME = 'sitepress-multilingual-cms/sitepress.php';
	const MIN_WPML_VERSION = '3.1.6';
	
	/**
	 * Needed to ensure, that initialisation is not run before all plugins are loaded
	 * 
	 * @var bool
	 */
	public $is_init;
	
	/**
	 * true, if Plugin WPML is acrtivated and version is OK
	 * 
	 * @var bool
	 */
	public $active;
	
	/**
	 *
	 * @var bool
	 */
	public $version_conflict;

	/**
	 * Language information array
	 * 
	 * @var array
	 */
	public $langs;

	/**
	 * Post ID to check if deleted in WPML table
	 * 
	 * @var int
	 */
	public $attachment_id_deleted;
	/**
	 * The only translatable post entry
	 * 
	 * @var array
	 */
	public $the_only_post;
	
	/**
	 * Possible languages to fill in select box extended with 'All Languages'
	 * 
	 * @var array
	 */
	public $select_langs;
	
	public function __construct() 
	{
		$this->is_init = false;
		$this->active = false;
		$this->version_conflict = true;
		
		$this->langs = array();
		$this->the_only_post = array();
		$this->attachment_id_deleted = 0;
		$this->select_langs = array();
			
				//	new attachment added / deleted
		add_action( 'init', array( $this, 'handler_wp_reinit_language'), 900 );
		add_action( 'add_attachment', array( $this, 'handler_wp_add_attachment'), 900, 1 );
		add_action( 'delete_attachment', array( $this, 'handler_wp_delete_attachment'), 900, 1 );
		add_action( 'deleted_post', array( $this, 'handler_wp_deleted_post'), 900, 1 );		
		
		add_filter( 'wc_eatt_options_default', array( $this, 'handler_wc_eatt_options_default'), 10, 2 );
		add_filter( 'wc_eatt_save_settings_options', array( $this, 'handler_wc_eatt_save_settings_options'), 10, 3 );
		
		add_filter( 'wc_eatt_wpml_column', array( $this, 'handler_wc_eatt_wpml_column'), 10, 1 );
		add_action( 'wc_eatt_form_media_column_language', array( $this, 'handler_wc_eatt_column_language'), 10, 4 );	
		add_action( 'wc_eatt_settings_hidden', array( $this, 'handler_wc_eatt_settings_hidden'), 10, 4 );
		
		add_filter( 'wc_eatt_settings_notification_headline', array( $this, 'handler_wc_eatt_settings_notification_headline'), 10, 2 );
		add_filter( 'wc_eatt_settings_notification_text', array( $this, 'handler_wc_eatt_settings_notification_text'), 10, 2 );
		
		add_filter( 'wc_eatt_wpml_filter_attachments', array ( $this, 'handler_wc_eatt_filter_attachments'), 10, 2 );
		add_filter( 'wc_eatt_wpml_notification_headline', array ( $this, 'handler_wc_eatt_notification_headline'), 10, 4 );
		add_filter( 'wc_eatt_wpml_notification_text', array ( $this, 'handler_wc_eatt_notification_text'), 10, 4 );

	}
	
	public function __destruct() 
	{
		unset( $this->langs );
		unset( $this->the_only_post );
		unset( $this->select_langs );
	}
	
	/**
	 * Check, if WPML is active and set state of this class
	 */
	public function init()
	{		
		if( $this->is_init ) 
		{
			return;
		}
				
			//	also checks for network activation
		if ( ! function_exists( 'is_plugin_active' ) )
		{
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		
		if ( ! is_plugin_active( self::WPML_PLUGIN_NAME ) ) 
		{
			return;
		}
		
		if( ! defined( 'ICL_SITEPRESS_VERSION' ) )
		{   
			return;  
		}
		
		if( ! version_compare( self::MIN_WPML_VERSION, ICL_SITEPRESS_VERSION, '<=' ) )
		{   
			return;  
		}
		
		$this->version_conflict = false;
		
		if( ! function_exists( 'wpml_add_translatable_content' ) )
		{
			if( ! file_exists( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' ) )
			{   
				return;  
			}
			
			include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
		}
		
			//	check, that all functions exist, otherwise keep object inactive
		if( ! function_exists( 'wpml_add_translatable_content' ) ) 
		{   
			return;  
		}
		
		if( ! function_exists( 'icl_object_id' ) )
		{   
			return;  
		}
		
		$this->active = true;
		$this->is_init = true;
		
				//	get all defined languages
		$this->langs = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
	}

	/**
	 * Adds the only post for EMail attachment files to WPML
	 * 
	 * @param int $post_id
	 * @param string $post_type
	 * @return boolean
	 */
	public function add_the_only_post( $post_id, $post_type )
	{
		$this->init();
		if( ! $this->active )  
		{  
			return false;  
		}
		
		$result = $this->add_translation_post( $post_id, $post_type );
		$this->the_only_post = empty( $result ) ? $result : $result[0];
	}

	/**
	 * Adds a basic translatable post type and set language to default language, if post not already exists
	 * 
	 * @param int $post_id
	 * @param string $post_type
	 * @param string $prefix		will be added as a prefix to $post_type (needed by WPML)
	 * @return array				the new inserted or found translation entry
	 */
	public function &add_translation_post( $post_id, $post_type, $prefix = 'post_' )
	{
		global $sitepress;
	
		$this->init();		
		if( ! $this->active )   
		{  
			return false;  
		}
		
		$results = $this->get_translations( $post_id, $post_type, $prefix );
		
		if( empty( $results ) )
		{
			$results = array();
			$lang = empty( $this->the_only_pos ) ? $sitepress->get_default_language() : $this->the_only_post['language_code'];
			
					//	insert post
			$old_lang = isset( $_POST['icl_post_language'] ) ? $_POST['icl_post_language'] : null;
			$_POST['icl_post_language'] = $lang;// change the language code
			$out = wpml_add_translatable_content( $prefix . $post_type, $post_id, $lang );
			if( isset( $old_lang ) )
			{
				($_POST['icl_post_language'] = $old_lang);
			}
			else
			{
				unset( $_POST['icl_post_language'] );
			}
			if( $out == WPML_API_SUCCESS )
			{
				$results = $this->get_translations( $post_id, $post_type, $prefix );
			}
			else
			{
				$this->active = false;
			}
		}	
			
		return $results;
	}
	
	/**
	 * Trys to delete the attachment from WPML table to clean up, if WPML does not.
	 * 
	 * @global wpdb $wpdb
	 * @param int $post_id
	 * @param string $post_type
	 * @param string $prefix
	 * @return bool     see WP Documentation
	 */
	public function delete_translation_post( $post_id, $post_type, $prefix = 'post_' )
	{
		global $wpdb;
		
		$deleted = $wpdb->query( 
				$wpdb->prepare( 
					"DELETE FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type = %s",
					array( $post_id, 'post_' . $post_type) )
				);
					
		return $deleted;
	}

	/**
	 * On the admin page check for current language and set E-Mail attachment post and all attachment to this
	 * language - Needed, that attachments can be seen in media uploader (if not done it maybe that they are not visible)
	 */
	public function handler_wp_reinit_language()
	{
		global $wpdb;
		
		if( ( ! is_admin()) || ( ! $this->active ) )  {  return;  }
		
		if( ( ! isset( $this->the_only_post ) ) || ( ! isset( $this->the_only_post['language_code'] ) ) )   {  return;  }
		if( icl_get_current_language() == $this->the_only_post['language_code'] )  {  return;  }
		
		$query = "SELECT * FROM {$wpdb->prefix}icl_translations AS T  ";
		$query .= "RIGHT JOIN {$wpdb->prefix}posts AS P ON T.element_id = P.ID ";
		$query .= "WHERE P.post_parent = %d AND P.post_type = 'attachment' AND T.element_id IS NULL ";

		$missing_atts = $wpdb->get_results( 
					$wpdb->prepare($query, $this->the_only_post['element_id']),
					ARRAY_A
						);
		
		foreach ( $missing_atts as $missing_att ) 
		{
			$this->add_translation_post( $missing_att['ID'], 'attachment' );
		}
		
		$wpdb->update(	
				$wpdb->prefix.'icl_translations',
					array ('language_code' => icl_get_current_language()),
					array ('element_id' => $this->the_only_post['element_id'],
						   'element_type' => $this->the_only_post['element_type']
						  ),
					array ( '%s'),
					array ( '%d', 
						    '%s'
						  )
				);
		$this->the_only_post['language_code'] = icl_get_current_language();
		
		$query =  "UPDATE {$wpdb->prefix}icl_translations AS T ";
		$query .= "INNER JOIN {$wpdb->prefix}posts AS P ON T.element_id = P.ID ";
		$query .= "SET T.language_code = %s ";
		$query .= "WHERE P.post_parent = %d AND P.post_type = 'attachment'	AND T.element_type = 'post_attachment' ";
		
		$wpdb->query(
			$wpdb->prepare(
					$query, icl_get_current_language(), $this->the_only_post['element_id']
							)
						);
	}
	
	/**
	 * Return the translation db entry for a post from WPML
	 * 
	 * @global wpdb $wpdb
	 * @param int $post_id
	 * @param string $post_type
	 * @param string $prefix
	 * @return array
	 */
	protected function &get_translations( $post_id, $post_type, $prefix = 'post_' )
	{
		global $wpdb;
		
		$results = $wpdb->get_results( 
				$wpdb->prepare( 
					"SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type = %s",
					array($post_id, 'post_'.$post_type) ), 
					ARRAY_A 
				);
					
		return $results;
	}
	
	/**
	 * Hooks into WP to insert new uploaded attachment in WPML table
	 * 
	 * do_action( 'add_attachment', $post_ID );
	 * @param int $attachment_id
	 */
	public function handler_wp_add_attachment( $attachment_id )
	{
		if( ! $this->active )  {  return;  }
		
			//	check for post parent = the_only_post
		$post = get_post( $attachment_id );
		
		if( ! isset( $post ) || empty( $post->post_parent ) )   {  return;  }
		
		if( $post->post_parent != $this->the_only_post['element_id'] ) {  return;  }
		
		$this->add_translation_post( $attachment_id, 'attachment' );
	}
		
	/**
	 * Hooks into WP to allow to delete attachment in WPML table, if WPML does not
	 * 
	 * do_action( 'delete_attachment', $post_ID );
	 * @param int $attachment_id
	 */
	public function handler_wp_delete_attachment( $attachment_id )
	{
		if( ! $this->active )  {  return;  }
		
		//	check for post parent = the_only_post
		$post = get_post( $attachment_id );
		
		if( ! isset( $post ) || empty( $post->post_parent ) ) {  return;  }
		if( $post->post_parent != $this->the_only_post['element_id'] ) {  return;  }
		
		$this->attachment_id_deleted = $attachment_id;
	}
	
	/**
	 * Clean up, in case WPML does not
	 * 
	 * do_action( 'deleted_post', $post_id );
	 * @param int $post_id
	 */
	public function handler_wp_deleted_post( $post_id )
	{
		if( ! $this->active )  {  return;  }
		
		if( $this->attachment_id_deleted != $post_id ) {  return;  }
		$this->delete_translation_post( $post_id, 'attachment' );
		$this->attachment_id_deleted = 0;
	}
	
	/**
	 * Add WPML specific default options
	 * 
	 * @param array $default_options
	 * @param array $emailsubjects 
	 * @return array 
	 */
	public function handler_wc_eatt_options_default (array $default_options, array $emailsubjects)
	{
		if( ! $this->active )  
		{  
			return $default_options;  
		}
		
		if( empty( $emailsubjects ) && isset( WC_Email_Att::$_instance ) )
		{
			$emailsubjects = WC_Email_Att::$_instance->emailsubjects;
		}
		
		foreach ( $emailsubjects as $emailkey => &$emailclass )
		{
			$default_options[ 'att_wpml_' . $emailkey ] = array();			//	array(array):  array of selected language codes in same sequence as att_ files, only needed for wpml array of array of languages
		}
		unset( $emailclass );
		
		
		$default_options['notification_headline_wpml'] = array();		//	key is language key of WPML
		$default_options['notification_text_wpml'] = array();			//	key is language key of WPML	
		
		return $default_options;
	}
	
	/**
	 * Stores the WPML specific entered options in the option array
	 * 
	 * @param array $options
	 * @param array $emailsubjects
	 * @param array $inputfields_param
	 * @return array
	 */
	public function handler_wc_eatt_save_settings_options(array $options, array $emailsubjects, array $inputfields_param)
	{
		if( ! $this->active )   
		{  
			return $options;  
		}
		
		$notes = ( isset( $_REQUEST[ WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_headline_wpml' ] ) && ! empty( $_REQUEST[ WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_headline_wpml' ] ) ) ? $_REQUEST[ WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_headline_wpml' ] : array();
		foreach ( $notes as $lang => $value ) 
		{
			$notes[ $lang ] = trim( stripslashes( $value ) );
		}
		
		$options['notification_headline_wpml'] = wp_parse_args( $notes, $options['notification_headline_wpml'] );
		
		$notes = ( isset( $_REQUEST[ WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_text_wpml' ] ) && ! empty( $_REQUEST[ WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_text_wpml' ] ) ) ? $_REQUEST[ WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_text_wpml' ] : array();	
		foreach ($notes as $lang => $value) 
		{
			$notes[ $lang ] = trim( stripslashes( $value ) );
		}
		
		$options['notification_text_wpml'] = wp_parse_args( $notes, $options['notification_text_wpml'] );
		
		foreach ( $emailsubjects as $email_key => $emailclass ) 
		{
			$opt_key = $inputfields_param[ $email_key ]['att_wpml_id'];
			$options[ $opt_key ] = $this->get_opt_attachment_languages( WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'att_wpml_', $email_key, $options[ $opt_key ] );
		}
		
		return $options;
	}
	
	/**
	 * Returns all selected WPML languages for attachment by the user on the admin page.
	 * 
	 * @param string $optionname	name of option to load
	 * @param string $email_key		name of the type of email = key to retrieve the array of files
	 * @param array $original		original array to return if WPML is disabled (avoid using settings if user disables WPML)
	 * @return array				filenames selected for option
	 */
	protected function &get_opt_attachment_languages( $optionname, $email_key, array $original )
	{
//				To keep correct language settings for attachment when WPML is deactivated, do NOT exit - stored in hidden fields
//		if(!wc_email_att::instance()->wpml->active) return $original;
		
		
		if( ! isset( $_REQUEST[ $optionname ][ $email_key ]))
		{
			return $original;
		}

		$requ = $_REQUEST[ $optionname ][ $email_key ];
		
		if(! is_array( $requ ) )
		{
			return $original;
		}
		
			//	numeric index, array_merge renumber them !!!!
		foreach ( $requ as $key => $value ) 
		{
			$original[ $key ] = $value;
		}
		return $original;
	}
		
	/**
	 * Add column information for settings page
	 * 
	 * @param array $columns
	 */
	public function handler_wc_eatt_wpml_column( array $columns )
	{
		if( ! $this->active ) 
		{
			return $columns;
		}
		
		$columns['language'] = __( 'Language', WC_Email_Att::TEXT_DOMAIN );
		return $columns;
	}
	
	/**
	 * Outputs the select box for all active languages and selects the language for the attachment
	 * 
	 * @param int $attachment_key
	 * @param int $attachment_id
	 * @param array $element
	 * @param int $tbl_line
	 */
	public function handler_wc_eatt_column_language ( $attachment_key, $attachment_id, array $element, $tbl_line )
	{
		if( ! $this->active )   {  return;  }
		
		echo	'<td class="settings wc_eatt_td_select">';
		
		$name = $element['language_name'];
		if(! empty( $element['product_id'] ) )
		{
			$name = str_replace( '[]', "[{$element['product_id']}][]", $name );
		}
		
		$att_id = isset( $element['std'][ $tbl_line ] ) ? $element['std'][ $tbl_line ] : 0;
		$name = $element['js_prefix'] . str_replace( '[]', "[{$att_id}][]", $name );
		
//				was a select box before - code kept only in case of fallback
//		echo		'<select class="select wc_eatt_td_language" name="'.$element['js_prefix'].$name.'" size="1" required="required">';
		echo '<div class="wc_eatt_td_chkbox_array">';
		
		$sel_langs = isset( $element['language'] ) && is_array( $element['language'] ) ? $element['language'] : array();
		$select_lang = ( isset( $sel_langs[ $att_id ] ) ) ? (array) $sel_langs[ $att_id ] : array('all'); 
		
			//	save to speed up
		if( empty( $this->select_langs ) )
		{
			$this->select_langs = array_merge( 
										array(
											array(
												'code' => 'all',
												'display_name' => __('All languages', WC_Email_Att::TEXT_DOMAIN),
												'english_name' => ''
												)), 
									wpml_get_active_languages());
		}
		
		$tip = ' data-tip="' . esc_attr(__('Check the languages where you want to have this attachment or all languages.', WC_Email_Att::TEXT_DOMAIN ) ) . '" ';
		foreach ( $this->select_langs as $value ) 
		{
			$selected = ( in_array( $value['code'], $select_lang)) ? ' checked="checked"' : '';
			$remark = ! empty( $value['english_name'] ) ? ' (' . $value['english_name'] . ')' : '';
//			echo		'<option value="'.$value['code'].'"'.$selected.'>'.$value['display_name'].$remark.'</option>';
			echo	'<label class="wc_eatt_td_chkbox" for="' . $name . '">';
			echo		'<input type="checkbox" name="' . $name . '" class="tips" value="' . $value['code'] . '"' . $selected.$tip.'>' . $value['display_name'] . $remark;
			echo	'</label>';
		}
		
//		echo		'</select>';
		echo		'</div>';
		echo	'</td>';
	}
	
	/**
	 * Outputs the selected language in a hidden field to save user selection for the attachment in case WPML had been deactivated
	 * 
	 * @param int $attachment_key
	 * @param int $attachment_id
	 * @param array $element
	 * @param int $tbl_line
	 */
	public function handler_wc_eatt_settings_hidden($attachment_key, $attachment_id, array $element, $tbl_line)
	{
		if( $this->active )   {  return;  }
		
		$name = $element['language_name'];
		if(! empty( $element['product_id'] ) )
		{
			$name = str_replace( '[]', "[{$element['product_id']}][]", $name );
		}
		$name = $element['js_prefix'] . str_replace( '[]', "[{$attachment_id}][]", $name );
		
		$sel_langs = isset( $element['language'] ) && is_array( $element['language'] ) ? $element['language'] : array();
		$select_lang = ( isset( $sel_langs[ $attachment_id ] ) ) ? (array) $sel_langs[ $attachment_id ] : array('all'); 
		
		foreach( $select_lang as $value )
		{
			echo '<input type="hidden" name="' . $name.'" value="' . $value . '" />';
		}
	}
	
	
	/**
	 * Returns the output fields for attachment notification headline in all active WPML languages
	 * 
	 * @param array $fields
	 * @param array $options
	 * @return array
	 */
	public function handler_wc_eatt_settings_notification_headline( array $fields, array $options )
	{
		if( !$this->active ) 
		{
			return $fields;
		}
		
		foreach ( wpml_get_active_languages() as $lang ) 
		{
			$value = isset( $options['notification_headline_wpml'][ $lang['code'] ] ) ? $options['notification_headline_wpml'][ $lang['code'] ] : '';
			$fields[] = array(  
					'type' 		=> 'text',
					'name' 		=> '', //__('in ', wc_email_att::TEXT_DOMAIN).$lang['display_name'].': ',
					'desc' 		=>  '<br/>' . sprintf( __( 'WPML translation in %1$s: Enter your translation of notification headline in %1$s here', WC_Email_Att::TEXT_DOMAIN), $lang['display_name'] ),
					'tip' 		=> '',
					'id' 		=> WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_headline_wpml[' . $lang['code'] . ']',
					'css' 		=> 'min-width:600px;',
					'default' 	=> $value,
					'std' 		=> $value
						);
		}
		return $fields;
	}
	
	/**
	 * Returns the output fields for attachment notification t iextn all active WPML languages
	 * 
	 * @param array $fields
	 * @param array $options
	 * @return array
	 */
	public function handler_wc_eatt_settings_notification_text( array $fields, array $options )
	{
		if( ! $this->active ) 
		{
			return $fields;
		}
		
		foreach ( wpml_get_active_languages() as $lang ) 
		{
			$value = isset( $options['notification_text_wpml'][ $lang['code'] ] ) ? $options['notification_text_wpml'][ $lang['code'] ] : '';
			$fields[] = array(  
					'type' 		=> 'textarea',
					'name' 		=> '', //__('in ', wc_email_att::TEXT_DOMAIN).$lang['display_name'].': ',
					'desc' 		=> sprintf( __('WPML translation in %1$s: Enter your translation of notification text in %1$s here:', WC_Email_Att::TEXT_DOMAIN ), $lang['display_name'] ),
					'tip' 		=> '',
					'id' 		=> WC_Email_Att_Panel_Admin::PREFIX_JS_NAMES . 'notification_text_wpml[' . $lang['code'] . ']',
					'css' 		=> 'min-width:600px;',
					'default' 	=> $value,
					'std' 		=> $value,
						);
		}
		
		return $fields;
	}
	
	
	
	/**
	 * Filters the attachment list for the current selected language
	 * If the attachment is not found in $langs, it is sent by default
	 * 
	 * @param array $attachments
	 * @param array $langs
	 */
	public function handler_wc_eatt_filter_attachments(array $attachments, array $langs)
	{
		if( ! $this->active) 
		{
			return $attachments;
		}
		
		$new_attachments = array();
		
		$curr_lang = icl_get_current_language();
		
		foreach ( $attachments as $line => $attachment_id )
		{
			$att_lang = ( isset( $langs[ $attachment_id ] ) ) ? (array) $langs[ $attachment_id ] : array ('all');
			if( in_array( $curr_lang, $att_lang ) || in_array( 'all', $att_lang ) )
			{
				$new_attachments [] = $attachment_id;
			}
		}
		
		return $new_attachments;
	}
	
	/**
	 * Returns the translation in the current language, otherwise default
	 * 
	 * @param string $headline
	 * @param array $options
	 * @param string $current_email_subject
	 * @param WC_Order|WP_User|WC_Product|null  $current_email_object
	 * @return string
	 */
	public function handler_wc_eatt_notification_headline( $headline, array $options, $current_email_subject, $current_email_object )
	{
		if( ! $this->active ) 
		{
			return $headline;
		}
		
		if( ! isset( $options['notification_headline_wpml'] ) ) 
		{
			return $headline;
		}
		
		return  isset( $options['notification_headline_wpml'][ icl_get_current_language() ] ) ? $options['notification_headline_wpml'][ icl_get_current_language() ] : $headline;
	}


	/**
	 * Returns the translation in the current language, otherwise default
	 * 
	 * @param string $text
	 * @param array $options
	 * @param string $current_email_subject
	 * @param WC_Order|WP_User|WC_Product|null  $current_email_object
	 * @return string
	 */
	public function handler_wc_eatt_notification_text( $text, array $options, $current_email_subject, $current_email_object )
	{
		if( ! $this->active) 
		{
			return $text;
		}
		
		if( ! isset( $options['notification_text_wpml'] ) ) 
		{
			return $text;
		}
		
		return  isset( $options['notification_text_wpml'][ icl_get_current_language() ] ) ? $options['notification_text_wpml'][ icl_get_current_language() ] : $text;
	}
	
}
