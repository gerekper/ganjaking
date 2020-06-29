<?php
/**
 * Description of wc_panel_admin
 *
 * @author Guenter Schoenmann
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

class WC_Email_Att_Panel_Admin 
{
	const PREFIX_SECTION_LINKS = 'wc_eatt_links_';
	const PREFIX_JS_NAMES = 'wc_eatt_';
	const PREFIX_JS_NAMES_PROD = 'wc_eatt_prod_';
	
	/**
	 *
	 * @var array
	 */
	public $options;

	/**
	 * variable inputfield parameters filled io constructor
	 * 
	 * @var array 
	 */
	public $inputfields_param;
		
	/**
	 * fixed inputfield constants filled io constructor
	 * @var array 
	 */
	public $inputfields_const;
	
	/**
	 * Inputarray for settingspage
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Pointer to global object
	 *
	 * @var WC_Email_Att
	 */
	public $wc_email_att;

	/**
	 *
	 * @var WC_Addons_Email_Att or any derived class
	 */
	public $woo_addons;
	
	public function __construct( WC_Addons_Email_Att $woo_addons )
	{
		$this->wc_email_att = WC_Email_Att::instance();
		$this->options = $this->wc_email_att->options;
		$this->woo_addons = $woo_addons;
		$this->emailsubjects = $this->wc_email_att->emailsubjects;
				
		$this->fields = array();
		$this->inputfields_const = array();
		$this->inputfields_param = array();
		
		$this->init_inputfield_constants();
	}

	public function __destruct()
	{
		unset( $this->options );
		unset( $this->fields );
		unset( $this->wc_email_att );
		unset( $this->woo_addons );
		
		unset( $this->inputfields_const );
		unset( $this->inputfields_param );
		unset( $this->emailsubjects );
	}
	
	/**
	 * Initializes static fields for input
	 */
	public function init_inputfield_constants()
	{
		if( empty( $this->inputfields_const ) )
		{
			$this->inputfields_const = array(
				'cc_text'	=> __( 'CC:', WC_Email_Att::TEXT_DOMAIN ),
				'cc_desc'	=> __( 'Enter the email address(es) you want, seperate multiple addresses with ",", leave blank if not needed. Follow these rules for a single address: <br/>recipient_name@hisdomain.ext -- or -- hisfirstname hislastname &lt;recipient_name@hisdomain.ext&gt;', WC_Email_Att::TEXT_DOMAIN ),
				'bcc_text'	=> __( 'BCC:', WC_Email_Att::TEXT_DOMAIN ),
				'cc_placeholder' => __( 'recipient_name@domain.ext -- or -- firstname lastname &lt;recipient_name@hisdomain.ext&gt;', WC_Email_Att::TEXT_DOMAIN ),
				'cc_desc_prod'	=> __( 'Enter the email address(es) you want, seperate multiple addresses with ",", leave blank if not needed.', WC_Email_Att::TEXT_DOMAIN ),
				'note_head_text' => __( 'Attachment notification headline:', WC_Email_Att::TEXT_DOMAIN ),
				'note_head_desc' => __( 'Insert the headline of your attachment notification.<br/> The attachment notification will be displayed in the email footer. Leave this field empty if you do not want to use this feature.', WC_Email_Att::TEXT_DOMAIN ),
				'note_text_text' => __( 'Attachment notification text:', WC_Email_Att::TEXT_DOMAIN ),
				'note_text_desc' => __( 'Insert the text of your attachment notification.<br/> The attachment notification will be displayed in the email footer and tells the recipient that your email contains attached files. <br/>E.g. this notification is useful if you fear that attachments may be blocked because of the file size, filters, etc. and you want to make sure that the recipient is aware of the fact that he should have received attachments.<br/> Leave this field empty if you do not want to use this feature.', WC_Email_Att::TEXT_DOMAIN ),
			);
		}
		
		if( ! empty( $this->inputfields_param ) )
		{
			return;
		}
		
		foreach ( $this->emailsubjects as $email_key => $emailclass ) 
		{
			$this->inputfields_param[ $email_key ] =			
				array ( //	'toggletitle' => __( 'Select CC, BCC and Attachment Files for: ', wc_email_att::TEXT_DOMAIN ).$emailclass['title'],
						'id'			=>	'head_'.$email_key,
						'cc_id'			=>	'head_['.$email_key.'][cc]',
						'bcc_id'		=>	'head_['.$email_key.'][bcc]',
						'att_id'		=>	'att_'.$email_key,
						'att_name'		=>	'att_['.$email_key.'][]',
						'att_wpml_id'	=>	'att_wpml_'.$email_key,
						'att_wpml_name'	=>	'att_wpml_['.$email_key.'][]',
						'note_head_id'	=>	'notification_headline_'.$email_key,
						'note_head_name'	=>	'notification_headline_['.$email_key.']',
						'note_txt_id'	=>	'notification_text_'.$email_key,
						'note_txt_name'	=>	'notification_text_['.$email_key.']',
						'chk_note_id'	=>	'show_notes_always_'.$email_key,
						'chk_note_name'	=>	'show_notes_always_['.$email_key.']'
						);
		}
	}
	
	/**
	 * Returns the HTML output for the settings tab section
	 *
	 * @return string
	 */
	public function &get_form_fields_settings_page()
	{
			//	surrounding container
		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_OPEN,
			'tag' => 'div',
			'id' => 'wc_emai_att_settings_container',
			'class' => 'subsubsub_section'
			);

		$err_cnt = $this->woo_addons->count_errors();
		if( $err_cnt > 0 )
		{
			$msg = sprintf( __( '%1$d Error(s) have been found and were reset to original value. Please check your entries.', WC_Email_Att::TEXT_DOMAIN ), $err_cnt );
			$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_COMPLETE,
				'tag' => 'div',
				'class' => 'error',
				'innerhtml' => $msg
			);
		}
		
			//	add hidden field to identify the last tab selected and restore it after saving
		$this->fields[] = array(
				'type' => WC_Addons_Email_Att::HIDDEN_INPUT,
				'id' => 'wc_eatt_last_tab_active',
				'default' => $this->woo_addons->last_tab_active
			);
		$this->fields[] = array(
				'type' => WC_Addons_Email_Att::HIDDEN_INPUT,
				'id' => 'wc_eatt_last_subject_active',
				'default' => $this->woo_addons->last_subject_active 
			);
		

		$this->get_link_list_fields( false );
		
		$this->fields = apply_filters( 'wc_email_att_add_licence_fields', $this->fields, $this );
		
		$this->get_general_setting_fields();
		foreach( $this->wc_email_att->emailsubjects as $email_key => &$emailsubject )
		{
			$this->get_emailsubject_inputfields( $email_key, $emailsubject );
		}

		//	surrounding container end
		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_CLOSE,
			'tag' => 'div'
			);

		return $this->fields;
	}
	
	
	/**
	 * Adds the EMail subjects link list to $this->fields[]
	 *
	 * @param bool $standard_class
	 * @param bool $skip_general     suppresses output of general setting tab, if not needed
	 */
	protected function get_link_list_fields( $standard_class = true, $skip_general = false )
	{
		$nr_emailsubjects = count( $this->wc_email_att->emailsubjects );
		$seperator = '|';

		$standard_class ? $cl = 'subsubsub' : $cl = 'email_att_ul';
		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_OPEN,
			'tag' => 'ul',
			'class' => $cl
			);

		if( ! $skip_general )
		{
			$this->fields = apply_filters( 'wc_email_att_add_link', $this->fields, 'first', $this );
			
			$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_OPEN,
				'tag' => 'li',
				'class' => 'email_att_basic_link'
				);

			$s = '';
			if( $nr_emailsubjects > 0 )
			{
				$s = ' '.$seperator;
			}
		
			$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_COMPLETE,
				'tag' => 'a',
				'class' => 'current',
				'href' => '#'.self::PREFIX_SECTION_LINKS.'General_Settings',
				'innerhtml' => __( 'General Settings', WC_Email_Att::TEXT_DOMAIN ) . $s
				);
		
			$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_CLOSE,
				'tag' => 'li'
				);
		}
		
		if( $nr_emailsubjects > 0 )
		{
			$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_OPEN,
				'tag' => 'li',
				'class' => 'email_att_basic_link'
				);

			$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_COMPLETE,
				'tag' => 'a',
				'class' => 'current',
				'href' => '#'.self::PREFIX_SECTION_LINKS.'email_type_selection',
				'innerhtml' => __( 'E-Mail Settings', WC_Email_Att::TEXT_DOMAIN )
				);

			$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_CLOSE,
				'tag' => 'li'
				);
		}
		
		if( ! $skip_general )
		{
			$this->fields = apply_filters('wc_email_att_add_link', $this->fields, 'last', $this);
		}


		$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_CLOSE,
				'tag' => 'ul'
				);
		
		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_STANDALONE,
			'tag' => 'br',
			'class' => 'clear'
			);
		
		if( $nr_emailsubjects > 0 )
		{
			$fields = $this->get_email_select( 'select_email_type_sett' );
			$this->fields = array_merge( $this->fields, $fields );
		}
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function &get_email_select( $select_id, $select_class = '' )
	{
		$fields = array();
		$fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_OPEN,
			'tag' => 'div',
			'id' => self::PREFIX_SECTION_LINKS.'email_type_selection',
			'class' => 'section'
			);
		
		$options = array();
		$default = '';
		foreach($this->wc_email_att->emailsubjects as $k => &$emailsubject)
		{
			$options[ '#' . self::PREFIX_SECTION_LINKS . sanitize_title( str_replace( '%', '', $emailsubject['id'] ) ) ] = $emailsubject['title'];
			if(empty($default))
			{
				$default = '#' . self::PREFIX_SECTION_LINKS . sanitize_title(str_replace( '%', '', $emailsubject['id'] ) ); 
			}
		}
		
		$fields[] = array(
				'type' => 'select',
				'id' => self::PREFIX_JS_NAMES . $select_id,
				'title' => __( 'Select type of E-Mail for settings:', WC_Email_Att::TEXT_DOMAIN ),
				'default' => $default,
//				'desc' => __( 'Select desc', wc_email_att::TEXT_DOMAIN ),
				'desc_tip' => __( 'Select the type of E-Mail, where you want to set or change the atttachments from the dropdown list.', WC_Email_Att::TEXT_DOMAIN ),
				'options' => $options,
				'class' => $select_class
				); 
		
		$fields[] = array(
			'type' => 'sectionend'
			);
		
		$fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_CLOSE,
			'tag' => 'div'
			);
	
		return $fields;
	}
	
	/**
	 *
	 */
	protected function get_general_setting_fields()
	{
		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_OPEN,
			'tag' => 'div',
			'id' => self::PREFIX_SECTION_LINKS . 'General_Settings',
			'class' => 'section'
			);

		$this->fields[] = array(
			'type' => 'title',
			'id' => 'ips_general_settings',
			'title' => __( 'General settings for WooCommerce Email Attachment plugin', WC_Email_Att::TEXT_DOMAIN )
			);

		if( WC_Email_Att::$show_activation )
		{
			$checked = ($this->options['del_on_deactivate']) ? 'yes' : 'no';
			$this->fields[] = array(
				'type' => 'checkbox',
				'id' => self::PREFIX_JS_NAMES . 'del_on_deactivate',
				'default' => $checked,
				'desc' => __( 'Delete options on deactivate', WC_Email_Att::TEXT_DOMAIN ),
				'desc_tip' => __( 'Check to delete options on deactivate. The uploaded attachment files are not deleted. Keep in mind, that WP deactivates the plugin when updating.', WC_Email_Att::TEXT_DOMAIN )
					);
		}

		if( WC_Email_Att::$show_uninstall )
		{
			$checked = ( $this->options['del_on_uninstall'] ) ? 'yes' : 'no';
			$this->fields[] = array(
				'type' => 'checkbox',
				'id' => self::PREFIX_JS_NAMES . 'del_on_uninstall',
				'default' => $checked,
				'desc' => __( 'Delete options on uninstall', WC_Email_Att::TEXT_DOMAIN ),
				'desc_tip' => __( 'Check to delete options on uninstall. The uploaded attachment files are not deleted. You can force to delete the files on the plugin page.', WC_Email_Att::TEXT_DOMAIN )
					);
		}
		
		$this->fields[] = array(  
				'type' 		=> 'text',
				'name' 		=> __( 'Upload folder:', WC_Email_Att::TEXT_DOMAIN ),
				'desc' 		=> __( '<br/>You can change the upload folder to a new one. Enter a path, which will be located below the standard WP upload folder. Files in the previous folder are NOT moved and the old folder with its content is kept.', WC_Email_Att::TEXT_DOMAIN ),
				'tip' 		=> '',
				'id' 		=> self::PREFIX_JS_NAMES . 'upload_folder',
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options['upload_folder'],
				'std' 		=> $this->options['upload_folder'],
					);
		
		
		$this->fields[] = array(  
				'type' 		=> 'text',
				'name' 		=> $this->inputfields_const['note_head_text'],
				'desc' 		=> '<br/>' . $this->inputfields_const['note_head_desc'],
				'tip' 		=> '',
				'id' 		=> self::PREFIX_JS_NAMES . 'notification_headline',
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options['notification_headline'],
				'std' 		=> $this->options['notification_headline'],
					);
		
		$this->fields = apply_filters( 'wc_eatt_settings_notification_headline', $this->fields, $this->options );
		
		$this->fields[] = array(  
				'type' 		=> 'textarea',
				'name' 		=> $this->inputfields_const['note_text_text'],
				'desc' 		=> '<br/>' . $this->inputfields_const['note_text_desc'],
				'tip' 		=> '',
				'id' 		=> self::PREFIX_JS_NAMES . 'notification_text',
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options['notification_text'],
				'std' 		=> $this->options['notification_text'],
					);
		
		$this->fields = apply_filters( 'wc_eatt_settings_notification_text', $this->fields, $this->options );
		
		$checked = ( $this->options['show_notes_always'] ) ? 'yes' : 'no';
		$this->fields[] = array(
			'type' => 'checkbox',
			'id' => self::PREFIX_JS_NAMES . 'show_notes_always',
			'default' => $checked,
			'desc' => __( 'always show the attachment notification', WC_Email_Att::TEXT_DOMAIN ),
			'desc_tip' => __( 'Uncheck, if you want to show notification only, when attachment files are sent. If checked, the notifications are sent regrardless of attachment files.', WC_Email_Att::TEXT_DOMAIN )
				);
		
		$this->fields[] = array(
			'type' => 'sectionend'
			);

		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_CLOSE,
			'tag' => 'div'
			);
	}
	
	/**
	 * Initialise emailsubject inputfields to be able to load them from request
	 * Namefields are placed in arrays like:
	 *		prefex_head[$key][bcc]
	 * 
	 * 
	 */
	public function get_emailsubject_inputfields( $email_key, $emailsubject )
	{
		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_OPEN,
			'tag' => 'div',
			'id' => self::PREFIX_SECTION_LINKS . sanitize_title(str_replace( '%', '', $emailsubject['id'] ) ),
			'class' => 'section section_email'
			);


		$s = __( 'E-Mail Attachment Settings for subject:', WC_Email_Att::TEXT_DOMAIN );
		$s .= ' ' . $emailsubject['title'] . ' ['.$emailsubject['id'] . ']';
		
		$this->fields[] = array(
			'type' => 'title',
			'id' => 'ips_email_settings_' . $emailsubject['id'],
			'title' => $s
				);
		
		$s = ( ! empty($emailsubject['description'] ) ) ? $emailsubject['description'] : __( 'Sorry, there is no description available for this E-Mail. Check documentation of WooCommerce or the other plugins.' , WC_Email_Att::TEXT_DOMAIN );
		$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_COMPLETE,
				'tag' => 'div',
				'class' => 'ips_email_infotext',
				'innerhtml' => $s
			);
		
		$this->fields[] = array(  
				'type' 		=> 'text',
				'name' 		=> $this->inputfields_const['cc_text'],
				'desc' 		=> '<br/>' . $this->inputfields_const['cc_desc'],
				'tip' 		=> '',
				'id' 		=> self::PREFIX_JS_NAMES . $this->inputfields_param[ $email_key ]['cc_id'],
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options[ $this->inputfields_param[ $email_key ]['id'] ]['cc'],
				'std' 		=> $this->options[ $this->inputfields_param[ $email_key ]['id'] ]['cc'],
				'custom_attributes' => array(
									'email'		=> 'check_multi',
									'emailname'	=> 'yes',
							)
					);
		
		$this->fields[] = array(  
				'type' 		=> 'text',
				'name' 		=> $this->inputfields_const['bcc_text'],
				'desc' 		=> '<br/>' . $this->inputfields_const['cc_desc'],
				'tip' 		=> '',
				'id' 		=> self::PREFIX_JS_NAMES . $this->inputfields_param[ $email_key ]['bcc_id'],
				'css' 		=> 'min-width:600px;',
				'default' 	=> $this->options[ $this->inputfields_param[ $email_key ]['id'] ]['bcc'],
				'std' 		=> $this->options[ $this->inputfields_param[ $email_key ]['id'] ]['bcc'],
				'custom_attributes' => array(
									'email'		=> 'check_multi',
									'emailname'	=> 'yes',
							)
					);
		
		$this->fields[] = array(
			'type' => 'sectionend'
			);
		
		$this->fields[] = array(
			'type' => 'title',
			'id' => 'ips_email_settings_' . $emailsubject['id'],
			'title' => __( 'Attachment Files Management:', WC_Email_Att::TEXT_DOMAIN )
				);
		
		$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_COMPLETE,
				'tag' => 'div',
				'class' => 'ips_email_infotext',
				'innerhtml' => __( 'Selected files for attachment are listed below. Drag and drop the files to control their order of attachment.', WC_Email_Att::TEXT_DOMAIN )
			);
		
		$selected_files = $this->wc_email_att->get_attachment_files_info ( $this->options[ $this->inputfields_param[ $email_key ]['att_id'] ] );
		
		$columns = array(
				'thumb' => __( 'Thumb', WC_Email_Att::TEXT_DOMAIN ),
				'name' => __( 'Name', WC_Email_Att::TEXT_DOMAIN ),
			);
		$columns = apply_filters( 'wc_eatt_wpml_column', $columns );
		
		$columns['id'] = __( 'ID', WC_Email_Att::TEXT_DOMAIN );
		$columns['path'] = __( 'Location', WC_Email_Att::TEXT_DOMAIN );
		$columns['button_remove'] = '';
		
			//	This is a fallback, if a product had been switched from variation to simple - variations are kept in Database and WPML is inactive
		$wpml_langs = isset( $this->options[ $this->inputfields_param[ $email_key ]['att_wpml_id'] ] ) ? $this->options[ $this->inputfields_param[ $email_key ]['att_wpml_id'] ] : array();
		
		$this->fields[] = array(
				'type'				=> WC_Addons_Email_Att::FILE_MEDIA,
				'button_remove'		=> __( 'Remove', WC_Email_Att::TEXT_DOMAIN ),
				'button_remove_tip' => __( 'Removes this file from the list of attachments, but does not delete the file. You can reuse it later at any time.', WC_Email_Att::TEXT_DOMAIN ),
				'info'				=> __( 'Selected files:', WC_Email_Att::TEXT_DOMAIN ),
				'columns'			=> $columns,
				'js_prefix'			=> self::PREFIX_JS_NAMES,
				'id'				=> $this->inputfields_param[ $email_key ]['att_name'],
				'std'				=> $selected_files,
				'attachment_infos'	=> $this->wc_email_att->attachment_infos,
				'language_name'		=> $this->inputfields_param[ $email_key ]['att_wpml_name'],
				'language'			=> $wpml_langs,
				'subject'			=> $email_key,
				'rows_only'			=> false,
				'wc_settings_page'	=> true,
				'product_id'		=> 0
			);
		
		$this->fields[] = array(
			'type' => 'sectionend'
			);
		
		$this->fields[] = array(
				'type' => WC_Addons_Email_Att::TAG_COMPLETE,
				'tag' => 'div',
				'class' => 'button-secondary wc_eatt_select_att tips',
				'innerhtml' => __( 'Select files or upload new files', WC_Email_Att::TEXT_DOMAIN ),
				'attributes' => array(
					'data-choose' => __( 'Choose your attachment file(s)', WC_Email_Att::TEXT_DOMAIN ),
					'data-update' => __( 'Accept as attachment', WC_Email_Att::TEXT_DOMAIN ),
					'data-tip' => __( 'Select one or more files from the uploaded attachment files, the stamdard media gallery or upload new attachment files. Upload needed files only once and select them at the emails needed.', WC_Email_Att::TEXT_DOMAIN ))
			);
		
		$this->fields[] = array(
			'type' => 'sectionend'
			);

		$this->fields[] = array(
			'type' => WC_Addons_Email_Att::TAG_CLOSE,
			'tag' => 'div'
			);
	}
	
	
	/**
	 * Saves the options in own option entry
	 */
	public function save_settings_page_options()
	{
		$this->options['del_on_deactivate'] = (WC_Email_Att::$show_activation) ? isset( $_REQUEST[ self::PREFIX_JS_NAMES . 'del_on_deactivate' ] ) : false;
		$this->options['del_on_uninstall'] = (WC_Email_Att::$show_uninstall) ? isset( $_REQUEST[self::PREFIX_JS_NAMES . 'del_on_uninstall'] ) : true;
		
		$this->options['notification_headline'] = (isset( $_REQUEST[ self::PREFIX_JS_NAMES . 'notification_headline']) && ! empty( $_REQUEST[self::PREFIX_JS_NAMES . 'notification_headline' ] ) ) ? $_REQUEST[ self::PREFIX_JS_NAMES . 'notification_headline' ] : '';
		$this->options['notification_headline'] =  trim( stripslashes( $this->options['notification_headline'] ) );
		
		$this->options['notification_text'] = (isset( $_REQUEST[ self::PREFIX_JS_NAMES . 'notification_text']) && ! empty( $_REQUEST[self::PREFIX_JS_NAMES . 'notification_text'])) ? $_REQUEST[self::PREFIX_JS_NAMES . 'notification_text' ] : '';
		$this->options['notification_text'] = trim( stripslashes( $this->options['notification_text'] ) );
	
		$this->options['show_notes_always'] = isset( $_REQUEST[ self::PREFIX_JS_NAMES . 'show_notes_always' ] );
		
		$oldpath = $newpath = strtolower( $this->options['upload_folder'] );
		if( isset( $_REQUEST[ self::PREFIX_JS_NAMES . 'upload_folder' ] ) )
		{
			$newpath = strtolower( trim( strtolower( stripslashes( $_REQUEST[ self::PREFIX_JS_NAMES . 'upload_folder'] ) ) ) );
			$newpath = str_replace( '\\', '/', $newpath );
			$newpath = (strlen( $newpath ) == 0 ) ? $oldpath : untrailingslashit( $newpath );
			$newpath = '/' . ltrim( $newpath, '/' );
			
			if( $oldpath != $newpath )
			{
				if( WC_Email_Att_Func::create_folder( $newpath ) )
				{
					$this->options['upload_folder'] = $newpath;
				}
				WC_Email_Att_Func::remove_empty_folder( $oldpath, WC_Email_Att::$skip_files );
			}
		}
		foreach ( $this->emailsubjects as $email_key => $emailclass ) 
		{
			$opt_key = $this->inputfields_param[ $email_key ]['id'];
			$this->options[ $opt_key ] = $this->get_opt_emailaddress( self::PREFIX_JS_NAMES . 'head_', $email_key );
			
			$opt_key = $this->inputfields_param[ $email_key ]['att_id'];
			$this->options[ $opt_key ] = $this->get_opt_attachment_files( self::PREFIX_JS_NAMES . 'att_', $email_key );
		}
		
		$this->options = apply_filters( 'wc_eatt_save_settings_options', $this->options, $this->emailsubjects, $this->inputfields_param );
		
		update_option( WC_Email_Att::OPTIONNAME, $this->options );
		
		WC_Email_Att::instance()->options = $this->options;
		
		WC_Email_Att::$admin_message = 'Options saved';
		
		$this->woo_addons->last_tab_active = isset( $_REQUEST[ 'wc_eatt_last_tab_active' ] ) ? $_REQUEST[ 'wc_eatt_last_tab_active' ] : '';
		$this->woo_addons->last_subject_active = isset( $_REQUEST[ 'wc_eatt_last_subject_active' ] ) ? $_REQUEST[ 'wc_eatt_last_subject_active' ] : '';
	}
	
	/**
	 * Returns the table lines for the requested Attachment ID'S (ajax call)
	 * 
	 * @param array $file_ids
	 * @param string $email_subject
	 * @param int $product_id			needed to create unique name, if more products are displayed (e.g. product page)
	 * @param bool $new_attachments
	 * @return string			HTML Code to insert in table on settings page
	 */
	public function &get_attachment_files_info( array $file_ids, $email_subject, $product_id = 0, $new_attachments = false )
	{
		$html = '';
		
		if( empty( $file_ids ) )
			return $html;
		
		$selected_files = $this->wc_email_att->get_attachment_files_info ( $file_ids );
		
		$id = $this->inputfields_param[ $email_subject ]['att_name'];
		$wpml_lang = isset( $this->options[ $this->inputfields_param[ $email_subject ]['att_wpml_id'] ] ) ? $this->options[ $this->inputfields_param[ $email_subject ]['att_wpml_id'] ] : array();
		$lang = $new_attachments ? array() :  $wpml_lang;

		if( $product_id != 0 )
		{
			$id = str_replace( '[]', '[' . $product_id . '][]', $id );
			if( ! $new_attachments )
			{		//	load existing language settings
				$pm = get_post_meta( $product_id );
				$lang = isset( $pm[ $this->inputfields_param[ $email_subject ]['att_wpml_id'] ]) ? $this->inputfields_param[ $email_subject ]['att_wpml_id'] : array();
			}
		}
		
		$columns = array(
				'thumb' => __( 'Thumb', WC_Email_Att::TEXT_DOMAIN ),
				'name' => __( 'Name', WC_Email_Att::TEXT_DOMAIN ),
			);
		$columns = apply_filters( 'wc_eatt_wpml_column', $columns );
		
		$columns['id'] = __( 'ID', WC_Email_Att::TEXT_DOMAIN );
		$columns['path'] = __( 'Location', WC_Email_Att::TEXT_DOMAIN );
		$columns['button_remove'] = '';
		
		$element = array(
				'type'				=> WC_Addons_Email_Att::FILE_MEDIA,
				'button_remove'		=> __( 'Remove', WC_Email_Att::TEXT_DOMAIN ),
				'button_remove_tip' => __( 'Removes this file from the list of attachments for this email, but does not delete the file. You can reuse it later at any time.', WC_Email_Att::TEXT_DOMAIN ),
				'info'				=> __( 'Selected files:', WC_Email_Att::TEXT_DOMAIN ),
				'columns'			=> $columns,
				'js_prefix'			=> ($product_id == 0) ? self::PREFIX_JS_NAMES : self::PREFIX_JS_NAMES_PROD,
				'id'				=> $id,
				'std'				=> $selected_files,
				'attachment_infos'	=> $this->wc_email_att->attachment_infos,
				'language_name'		=> $this->inputfields_param[ $email_subject ]['att_wpml_name'],
				'language'			=> $lang,
				'subject'			=> $email_subject,
				'rows_only'			=> true,
				'wc_settings_page'	=> true,
				'product_id'		=> $product_id
			);
		
			ob_start();
			do_action( 'woocommerce_admin_field_' . WC_Addons_Email_Att::FILE_MEDIA, $element );
			$buffer = ob_get_contents();
			ob_clean();
			
			return $buffer;
	}
	
	/**
	 * Returns all selected files for attachment by the user on the admin page.
	 * 
	 * @param string $optionname	name of option to load
	 * @param string $email_key		name of the type of email = key to retrieve the array of files
	 * @param int $product_id		ID of product-post to filter, if several products have been displayed, e.g. on product page
	 * @return array				filenames selected for option
	 */
	protected function &get_opt_attachment_files( $optionname, $email_key, $product_id = 0 )
	{
		$selected = array();
		
		if( $product_id == 0 )
		{
			if( ! isset( $_REQUEST[ $optionname ][ $email_key ] ) )
			{
				return $selected;
			}
			
			$requ = $_REQUEST[ $optionname ][ $email_key ];
		}
		else
		{
			if( ! isset( $_REQUEST[ $optionname ][ $email_key ][ $product_id ] ) )
			{
				return $selected;
			}
			
			$requ = $_REQUEST[ $optionname ][ $email_key ][ $product_id ];
		}

		if(! is_array( $requ ) )
		{
			return $selected;
		}
		
		$selected = array_merge( $requ );
		return $selected;
	}
	

	
	/**
	 * Returns all entered emails (cc and bcc) by the user on the admin page and product page.
	 * 
	 * @param string $optionname	name of option to load
	 * @param string $email_key		
	 * @param int $product_id		ID of product-post to filter, if several products have been displayed, e.g. on product page
	 * @return array				valid emails entered for option
	 */
	protected function &get_opt_emailaddress( $optionname, $email_key, $product_id = 0 )
	{
		$emails = array(
				'cc'	=> '',
				'bcc'	=> ''
			);
		
		if( isset( $_REQUEST[ $optionname ][ $email_key ] ) )
		{
			$arr = $_REQUEST[ $optionname ][ $email_key ];
			if( $product_id == 0 )
			{
				if( isset( $arr['cc'] ) )
				{
					$emails['cc'] =  trim( stripslashes( $arr['cc'] ));
				}
				if( isset($arr['bcc'] ) )
				{
					$emails['bcc'] = trim( stripslashes( $arr['bcc'] ) );
				}
			}
			else 
			{
				if( isset( $arr['cc'][ $product_id ] ) )
				{
					$emails['cc'] =  trim( stripslashes( $arr['cc'][ $product_id ] ) );
				}
				if( isset( $arr['bcc'][$product_id]))
				{
					$emails['bcc'] = trim( stripslashes( $arr['bcc'][ $product_id ] ) );
				}
			}
		}
			
		return $emails;
	}

}

