<?php
/**
 * functions for extending WooCommerce Core
 *
 * @author Schoenmann Guenter
 * @version 1.0.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

final class WC_Addons_Email_Att
{
	const TAG_OPEN = 'wc_email_att_addons_tag_open_ea';
	const TAG_CLOSE = 'wc_email_att_addons_tag_close_ea';
	const TAG_STANDALONE = 'wc_email_att_addons_tag_standalone_ea';
	const TAG_COMPLETE = 'wc_email_att_addons_tag_complete_ea';
	const TEXT = 'wc_email_att_addons_text_ea';
	const FILE_MEDIA = 'wc_email_att_addons_file_media_ea';
	const HIDDEN_INPUT = 'wc_email_att_addons_hidden_input';

	const ERR_MSG_ERROR = 'error';
	const ERR_MSG_INFO = 'info';

	/**
	 * Error messages:
	 * 'field id' => array ('message' => '...',
	 *					    'status' =>   type of error (enumeration ERR_MSG_ERROR, ERR_MSG_INFO
	 *				)
	 * @var array
	 */
	protected $errors;

	/**
	 * Saves the ID of the active tab from a hidden field to be able to open it after save
	 * 
	 * @var string
	 */
	public $last_tab_active;
	
	/**
	 * Saves the ID of the selected subject selectbox from a hidden field to be able to open it after save
	 * 
	 * @var string 
	 */
	public $last_subject_active;
	/**
	 * 
	 */
	public function __construct()
	{
		$this->errors = array();
		$this->last_tab_active = '';
		$this->last_subject_active = '';
		
		$this->attach_fields();
	}

	/**
	 * 
	 */
	public function __destruct()
	{
		unset( $this->errors );
	}


	/**
	 * attach to WooCommerce hooks
	 */
	public function attach_fields()
	{
				// Attach hooks to special form elements
		add_action( 'woocommerce_admin_field_' . self::TAG_OPEN, array( $this, 'form_tag_open' ), 10, 1 );
		add_action( 'woocommerce_admin_field_' . self::TAG_CLOSE, array( $this, 'form_tag_close' ), 10, 1 );
		add_action( 'woocommerce_admin_field_' . self::TAG_STANDALONE, array( $this, 'form_tag_standalone' ), 10, 1 );
		add_action( 'woocommerce_admin_field_' . self::TAG_COMPLETE, array( $this, 'form_tag_complete' ), 10, 1 );
		add_action( 'woocommerce_admin_field_' . self::TEXT, array( $this, 'form_text' ), 10, 1 );
		add_action( 'woocommerce_admin_field_' . self::FILE_MEDIA, array( $this, 'form_media_management' ), 10, 1 );
		add_action( 'woocommerce_admin_field_' . self::HIDDEN_INPUT, array( $this, 'form_hidden_input_field' ), 10, 1 );
//		add_action( 'woocommerce_admin_field_' . self::BUTTON, array( $this, 'form_button' ), 10 );
	}

	/**
	 * Echos a form element
	 *
	 * @param array $element
	 */
	public function echo_html_string( array $element )
    {
		if( ! isset( $element['type'] ) ) 
		{
			return;
		}
		
		do_action( 'woocommerce_admin_field_' . $element['type'], $element );
		return;
	}

    /**
	 * Outputs a starting element tag
	 *
	 * @param array $element
	 */
	public function form_tag_open( array $element )
	{
		$e = $this->init_element( $element );
		if( empty( $e['tag'] ) )
		{
			return;
		}

		echo '<' . $e['tag'];
		echo $this->get_attribute_string( 'id', $e['id'] );
		echo $this->get_attribute_string( 'class', $e['class'] );
		echo $this->get_attribute_string( 'href', $e['href'] );
		echo $this->get_attribute_string( 'attributes', $e['attributes'], true );
		echo '>';
	}

	/**
	 * Outputs a ending element tag
	 *
	 * @param array $element
	 */
	public function form_tag_close( array $element )
	{
		$e = $this->init_element( $element );
		if( empty( $e['tag'] ) )
		{
			return;
		}

		echo '</' . $e['tag'] . '>';
	}

	/**
	 * Outputs a standalone element tag
	 * e.g. <br />
	 *
	 * @param array $element
	 */
	public function form_tag_standalone( array $element )
	{
		$e = $this->init_element( $element );
		if( empty( $e['tag'] ) )
		{
			return;
		}

		echo '<' . $e['tag'];
		echo $this->get_attribute_string( 'id', $e['id'] );
		echo $this->get_attribute_string( 'class', $e['class'] );
		echo $this->get_attribute_string( 'href', $e['href'] );
		echo $this->get_attribute_string( 'attributes', $e['attributes'], true );
		echo ' />';
	}

	/**
	 * Outputs a standalone element tag
	 *
	 * @param array $element
	 */
	public function form_text( array $element )
	{
		$e = $this->init_element( $element );
		if(strlen($e['innerhtml'])== 0)
		{
			return;
		}

		echo esc_html( $e['innerhtml'] );
	}

	/**
	 * Outputs a ending element tag
	 *
	 * @param array $element
	 */
	public function form_tag_complete( array $element )
	{
		$e = $this->init_element( $element );
		if( empty( $e['tag'] ) )
		{
			return;
		}

		echo '<' . $e['tag'];
		echo $this->get_attribute_string( 'id', $e['id'] );
		echo $this->get_attribute_string( 'class', $e['class'] );
		echo $this->get_attribute_string( 'href', $e['href'] );
		echo $this->get_attribute_string( 'attributes', $e['attributes'], true );
		echo '>';
		echo $e['is_html_code'] === 'no' ? esc_html( $e['innerhtml'] ) : $e['innerhtml'];
		echo '</' . $e['tag'] . '>';
	}
	
	/**
	 * Outputs a hidden field
	 * 
	 * @param array $element
	 */
	public function form_hidden_input_field( array $element )
	{
		$e = $this->init_element( $element );
		
		echo '<input type="hidden" ';
		echo $this->get_attribute_string( 'name', $e['id'] );
		echo $this->get_attribute_string( 'value', $e['default'] );
		echo '>';
	}

	/**
	 * Outputs a sortable list of attachment files with a delete button and a select file button
	 * 
	 * @param array $element
	 */
	public function form_media_management( array $element )
	{
		$e = $this->init_media_element( $element );
		$class = $e['wc_settings_page'] ? ' wc_eatt_att_table' : ' wc_eatt_prod_att_table';
		
		if( ! $e['rows_only'] && $e['wc_settings_page'] )
		{
			echo '<tr valign="top">';
			echo 	'<th scope="row" class="titledesc">' . $e['info'] . '</th>';
			echo		'<td class="forminp">';
		}
		else if( ! $e['rows_only'] && ! $e['wc_settings_page'] )
		{
			echo '<h4>' . $e['info'] . '</h4>';
		}
		if( ! $e['rows_only'] )
		{
			echo			'<table class="wc_gateways widefat' . $class . '" cellspacing="0">';

			echo				'<thead>';    
			echo					'<tr>';

			foreach ( $e['columns'] as $key => $column ) 
			{
				echo					'<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
			}

			echo					'</tr>';
			echo				'</thead>';

			echo			'<tbody wc_eatt_subject="' . $e['subject'] . '" wc_eatt_product_id="' . $e['product_id'] . '">';			
		}
		
		$tips_remove_class = ( ! empty( $e['button_remove_tip'] ) ) ? ' tips' : '';
		$tip_remove = ( ! empty( $e['button_remove_tip'] ) ) ?  ' clone-tip="'. esc_attr($e['button_remove_tip']) . '"' . ' data-tip="'. esc_attr( $e['button_remove_tip'] ) . '"' : '';
		
		$tbl_line = 0;
		foreach ( $e['std'] as $attachment_key => $attachment_id ) 
		{
			$error = $e['attachment_infos'][ $attachment_id ]['error'] ? ' wc_email_att_file_err' : '';
			echo			"<tr>";
			
			foreach ( $e['columns'] as $key => $column ) 
			{
				switch ( $key ) 
				{
					case 'thumb' :
							echo	'<td class="thumb wc_eatt_td_thumbnail wc_eatt_td_drag">';
							echo		( $e['attachment_infos'][ $attachment_id ]['thumb'] ) ? '<img src="' . $e['attachment_infos'][ $attachment_id ]['thumb'] . '" style="max-height: 80px;"/>' : '';
					        echo	'</td>';
							break;
					case 'name' :
							echo	'<td class="name wc_eatt_td_name'.$error.' wc_eatt_td_drag">';
							echo	esc_html( $e['attachment_infos'][ $attachment_id ]['name']);
					        echo	'</td>';
							break;
					case 'path' :
							echo	'<td class="name wc_eatt_td_path' . $error . ' wc_eatt_td_drag">';
							echo	esc_html( str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $e['attachment_infos'][ $attachment_id ]['source'] ) );
					        echo	'</td>';
							break;
					case 'id' :
							echo	'<td class="name wc_eatt_td_id'.$error.' wc_eatt_td_drag">';
							echo	esc_html($attachment_id);
					        echo	'</td>';
							break;
					case 'button_remove':
							echo	'<td class="settings wc_eatt_td_settings'.$error.'">';
					        echo		'<div class="button-secondary button_remove' . $tips_remove_class . '"'.$tip_remove.'> ' . $e['button_remove'] . '</div>';	
							echo		'<input type="hidden" name="' . $e['js_prefix'] . $e['id'] . '" value="' . $attachment_id . '" />';
							do_action( 'wc_eatt_settings_hidden', $attachment_key, $attachment_id, $element, $tbl_line );
							echo	'</td>';	
							break;
					default :
							do_action( 'wc_eatt_form_media_column_' . $key, $attachment_key, $attachment_id, $element, $tbl_line );
							break;
				}
			}
			echo			'</tr>';
			$tbl_line++;
		}
		if( ! $e['rows_only'] )
		{
			echo			'</tbody>';			
			echo		'</table>';
		}
		if( ! $e['rows_only'] && $e['wc_settings_page'] )
		{
			echo	'</td>';
			echo '</tr>';
		}
	}

	/**
	 * Initialises all default values
	 *
	 * @param array $element
	 * @return array
	 */
	protected function &init_element( array $element )
	{
		$default = array(
					'type'		=> '',
					'tag'		=> '',
					'id'		=> '',
					'class'		=> '',
					'href'		=> '',
					'innerhtml' => '',
					'default'	=> '',
					'is_html_code' => 'no',
					'attributes' => array()
			);

		$new = wp_parse_args( $element, $default );
		return $new;
	}
	
	/**
	 * Initialises all default values for a media element
	 *
	 * @param array $element
	 * @return array
	 */
	protected function &init_media_element( array $element )
	{
		$e = array();
		$e['button_remove'] = isset( $element['button_remove'] ) ? $element['button_remove'] : '';
		$e['button_remove_tip'] = isset( $element['button_remove_tip'] ) ? $element['button_remove_tip'] : '';
		$e['info'] = isset( $element['info'] ) ? $element['info'] : '';
		$e['columns'] = isset( $element['columns'] ) ? $element['columns'] : array();
			$e['columns']['id'] = isset( $element['columns']['id'] ) ? $element['columns']['id'] : '';
			$e['columns']['thumb'] = isset( $element['columns']['thumb'] ) ? $element['columns']['thumb'] : '';
			$e['columns']['name'] = isset( $element['columns']['name'] ) ? $element['columns']['name'] : '';
			$e['columns']['path'] = isset( $element['columns']['path'] ) ? $element['columns']['path'] : '';
			$e['columns']['button_remove'] = isset( $element['columns']['button_remove'] ) ? $element['columns']['button_remove'] : '';										
		$e['js_prefix'] = isset( $element['js_prefix'] ) ? $element['js_prefix'] : '';
		$e['id'] = isset( $element['id'] ) ? $element['id'] : '';
		$e['std'] = isset( $element['std'] ) ? $element['std'] : array();
		$e['attachment_infos'] = isset( $element['attachment_infos'] ) ? $element['attachment_infos'] : array();
		$e['language_name'] = isset( $element['language_name'] ) ? $element['language_name'] : '';
		$e['language'] = isset( $element['language'] ) ? $element['language'] : '';
		$e['subject']  = isset( $element['subject'] ) ? $element['subject'] : '';
		$e['wc_settings_page'] = isset( $element['wc_settings_page'] ) ? $element['wc_settings_page'] : true;
		$e['rows_only'] = isset( $element['rows_only'] ) ? $element['rows_only'] : false;
		$e['product_id'] = isset( $element['product_id'] ) ? $element['product_id'] : 0;
	
		$e['columns'] = apply_filters( 'wc_email_att_setting_file_columns', $e['columns'] );
		
		return $e;
	}

	/**
	 * Returns a string ' key="value"'
	 *
	 * @param string $key
	 * @param string|array $value
	 * @param bool $value_is_key_array		true, if $value is 'key' => 'value' pair, otherwise 'value' is a concatinated string
	 * &return string
	 */
	protected function &get_attribute_string( $key, $value, $value_is_key_array = false )
	{
		$ret = '';
		$k = trim( (string) $key );
		if( strlen( $k ) == 0 )
		{
			return $ret;
		}

			//	return ' key="value"'
		if( ! is_array( $value ) )
		{
			try
			{
				$v = trim( (string) $value );

				if( (strlen( $v ) == 0 ) )
				{
					return $ret;
				}
				$ret = ' ' . $k . '="' . $v . '"';
			}
			catch( Exception $e )
			{
			}
			return $ret;
		}
			//	concatinate values to string
		if( ! $value_is_key_array )
		{
			$v = implode( ' ', $value );
			if( ( strlen( $v ) == 0 ) )
			{
				return $ret;
			}
			$ret = ' ' . $k . '="' . $v . '"';
			return $ret;
		}

		foreach ( $value as $k => &$v )
		{
			$r = $this->get_attribute_string( $k, $v );
			$ret .= $r;
		}
		unset ( $v );
		return $ret;
	}

	/**
	 * Summarizes the error messages for input fields in an array
	 *
	 * @param string $id
	 * @param string $message
	 * @param string $status
	 */
	public function add_field_error_message( $id, $message, $status = self::ERR_MSG_ERROR )
	{
		switch ( $status )
		{
			case self::ERR_MSG_ERROR:
			case self::ERR_MSG_INFO:
				break;
			default:
				self::ERR_MSG_ERROR;
				break;
		}

		$this->errors [$id] = array(
					'message' => $message,
					'status' => $status
				);
	}

	/**
	 * Returns the error entry for a given field id
	 *
	 * @return array|null
	 */
	public function get_error_message( $id )
	{
		if( isset( $this->errors[ $id ] ) )
		{
			return $this->errors[ $id ];
		}

		return null;
	}

	/**
	 * Number of Error messages stored
	 *
	 * @return int
	 */
	public function count_errors()
	{
		return count( $this->errors );
	}
	
}
