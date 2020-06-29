<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

GFForms::include_addon_framework();

/**
 * Implements the YWRAQ_Gravity_Forms_Add_On class.
 *
 *
 * @class    YWRAQ_Gravity_Forms_Add_On
 * @package  YITH
 * @since    1.6.0
 * @author   YITH
 */

/**
 * Class YWRAQ_Gravity_Forms_Add_On
 */
class YWRAQ_Gravity_Forms_Add_On extends GFAddOn {

	/**
	 * @var string
	 */
	protected $_version = '1.0.0';
	/**
	 * @var string
	 */
	protected $_min_gravityforms_version = '2.0.6';
	/**
	 * @var string
	 */
	protected $_slug = 'yith-woocommerce-request-a-quote';
	/**
	 * @var string
	 */
	protected $_path = 'ywraq/ywraq.php';
	/**
	 * @var string
	 */
	protected $_full_path = __FILE__;
	/**
	 * @var string
	 */
	protected $_title = 'YITH WooCommerce Request a Quote';
	/**
	 * @var string
	 */
	protected $_short_title = 'YITH WooCommerce Request a Quote';
	/**
	 * @var string
	 */
	protected $_message = '';
	/**
	 * @var
	 */
	protected $lead;
	/**
	 * @var
	 */
	protected $quote;
	/**
	 * @var
	 */
	protected $form;
	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get an instance of YWRAQ_Gravity_Forms_Add_On
	 *
	 * @return YWRAQ_Gravity_Forms_Add_On
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new YWRAQ_Gravity_Forms_Add_On();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();

		if ( is_admin() ) {
			add_filter( 'gravity_forms_get_contact_forms', array( $this, 'get_forms' ) );
			add_filter( 'gform_custom_merge_tags', array( $this, 'custom_merge_tags' ) );
			add_filter( 'ywraq_form_type_list', array( $this, 'add_to_option_list' ) );
			add_filter( 'ywraq_additional_form_options', array( $this, 'add_option' ), 10, 3 );
		}

		add_filter( 'gform_entry_created', array( $this, 'ywraq_gform_notification' ), 9, 2 );
		add_filter( 'gform_pre_replace_merge_tags', array( $this, 'pre_replace_merge_tags' ), 10, 7 );

		if ( get_option( 'ywraq_inquiry_form_type', 'default' ) == 'gravity-forms' ) {
			add_filter( 'ywraq_ajax_create_order_args', array( $this, 'ywraq_ajax_create_order_args' ), 10, 2 );
			add_filter( 'yith_ywraq_frontend_localize', array( $this, 'frontend_localize' ) );
			add_action( 'gform_after_email', array( $this, 'reset_list' ), 10, 12 );
			add_action( 'ywraq_add_order_meta', array( $this, 'register_quote' ) );
			add_filter( 'ywraq_order_meta_list', array( $this, 'add_order_metas' ), 10, 3 );

			add_action( 'gform_pre_submission', array( $this, 'save_product_list' ) );
			if ( get_option( 'ywraq_activate_thank_you_page' ) == 'yes' ) {
                global $sitepress;
				if ( function_exists( 'icl_get_languages' ) && !is_null( $sitepress) ) {
					$current_language = $sitepress->get_current_language();
					$gravity_form  = get_option( 'ywraq_inquiry_gravity_forms_id_' . $current_language );
				} else {
					$gravity_form = get_option( 'ywraq_inquiry_gravity_forms_id' );
				}
				add_filter( 'gform_form_args', array( $this, 'no_ajax_on_all_forms' ), 10, 1 );
				add_action( 'gform_after_submission_' . $gravity_form, array(
					$this,
					'redirect_after_submission_mail_gravityform'
				), 10, 2 );
			}
		}

	}

	/**
	 * Add current Gravity Form to javascript frontend localization.
	 *
	 * @param array $localize
	 *
	 * @since  2.0
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function frontend_localize( $localize ) {
		global $sitepress;
		if ( function_exists( 'icl_get_languages' ) && !is_null( $sitepress) ) {

			$current_language = $sitepress->get_current_language();
			$gravity_form  = get_option( 'ywraq_inquiry_gravity_forms_id_' . $current_language );
		} else {
			$gravity_form = get_option( 'ywraq_inquiry_gravity_forms_id' );
		}
		$localize['gf_id'] = apply_filters( 'ywraq_inquiry_gf_id', $gravity_form );

		return $localize;
	}

	/**
	 * @param $args
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function no_ajax_on_all_forms( $args ) {
        global $sitepress;
		if ( function_exists( 'icl_get_languages' ) && !is_null( $sitepress) ) {
			$current_language = $sitepress->get_current_language();
			$gravity_form  = get_option( 'ywraq_inquiry_gravity_forms_id_' . $current_language );
		} else {
			$gravity_form = get_option( 'ywraq_inquiry_gravity_forms_id' );
		}
		if ( $gravity_form == $args['form_id'] ) {
			$args['ajax'] = false;
		}

		return $args;
	}

	/**
	 * Do the redirect after that email is sent with gravity form without ajax enabled
	 *
	 * @param $form
	 */
	function redirect_after_submission_mail_gravityform( $form ) {
        global $sitepress;
		if ( function_exists( 'icl_get_languages' ) && !is_null( $sitepress) ) {

			$current_language = $sitepress->get_current_language();
			$gravity_form  = get_option( 'ywraq_inquiry_gravity_forms_id_' . $current_language );
		} else {
			$gravity_form = get_option( 'ywraq_inquiry_gravity_forms_id' );
		}
		if ( $form['form_id'] == $gravity_form ) {
			wp_redirect( YITH_Request_Quote()->get_redirect_page_url() );
		}
	}

	/**
	 * @param $order_id
	 */
	function register_quote( $order_id ) {
		$this->quote = $order_id;
	}

	/**
	 * Add the shortcode {ywraq_quote_table} inside the list of gravity form shortcode
	 *
	 * @param $custom
	 *
	 * @return array
	 */
	public function custom_merge_tags( $custom ) {
		$custom[] = array(
			'tag'   => '{ywraq_quote_table}',
			'label' => esc_html__( 'YITH Quote List', 'yith-woocommerce-request-a-quote' )
		);

		return $custom;
	}

	/**
	 * Replace the table in the email gravity form
	 *
	 * @param $text
	 *
	 * @return mixed
	 */
	public function pre_replace_merge_tags( $text ) {

		$text = str_replace( '{ywraq_quote_table}', $this->_message, $text );

		return $text;
	}

	/**
	 * Filter the arguments after the submit of form
	 *
	 * @param $args
	 * @param $posted
	 *
	 * @return mixed
	 */
	public function ywraq_ajax_create_order_args( $args, $posted ) {

		if ( isset( $posted['gform_submit'] ) ) {
			$form_id = $posted['gform_submit'];

			if ( $form_id == $this->get_selected_form_id() ) {
				$other_email_content  = '';
				$form                 = GFAPI::get_form( $form_id );
				$raq_c                = $form['yith-woocommerce-request-a-quote'];
				$fields_to_exclude    = array();
				$args['user_name']    = '';
				$args['user_email']   = '';
				$args['user_message'] = '';

				//Name
				if ( $raq_c['ywraq_name'] != '' ) {
					$id                  = $raq_c['ywraq_name'];
					$fields_to_exclude[] = $id;
					$ywraq_field         = $this->get_field_by_id( $id, $form );
					if ( $ywraq_field->type == 'name' ) {
						$args['_billing_first_name'] = isset( $this->lead[ $id . '.3' ] ) ? $this->lead[ $id . '.3' ] : '';
						$args['_billing_last_name']  = isset( $this->lead[ $id . '.6' ] ) ? $this->lead[ $id . '.6' ] : '';
					}
					$args['user_name'] = $ywraq_field->get_value_entry_detail( $this->extract_from_lead( $raq_c['ywraq_name'] ) );
				}

				//Email
				if ( $raq_c['ywraq_email'] != '' ) {
					$fields_to_exclude[] = $raq_c['ywraq_email'];
					$ywraq_field         = $this->get_field_by_id( $raq_c['ywraq_email'], $form );
					if ( $ywraq_field->type == 'email' ) {
						$args['user_email'] = $this->lead[ $raq_c['ywraq_email'] ];
					} else {
						$args['user_email'] = $ywraq_field->get_value_entry_detail( $this->extract_from_lead( $raq_c['ywraq_email'] ) );
					}
				}

				//Message

				if ( $raq_c['ywraq_message'] != '' ) {
					$fields_to_exclude[]  = $raq_c['ywraq_message'];
					$ywraq_field          = $this->get_field_by_id( $raq_c['ywraq_message'], $form );
					$args['user_message'] = $ywraq_field->get_value_entry_detail( $this->extract_from_lead( $raq_c['ywraq_message'] ) );
				}

				//Address
				if ( $raq_c['ywraq_billing_address'] != '' ) {
					$id                  = $raq_c['ywraq_billing_address'];
					$fields_to_exclude[] = $id;
					$ywraq_field         = $this->get_field_by_id( $id, $form );
					if ( $ywraq_field->type == 'address' ) {
						$args['_billing_address_1'] = isset( $this->lead[ $id . '.1' ] ) ? $this->lead[ $id . '.1' ] : '';
						$args['_billing_address_2'] = isset( $this->lead[ $id . '.2' ] ) ? $this->lead[ $id . '.2' ] : '';
						$args['_billing_city']      = isset( $this->lead[ $id . '.3' ] ) ? $this->lead[ $id . '.3' ] : '';
						$args['_billing_state']     = isset( $this->lead[ $id . '.4' ] ) ? $this->lead[ $id . '.4' ] : '';
						$args['_billing_postcode']  = isset( $this->lead[ $id . '.5' ] ) ? $this->lead[ $id . '.5' ] : '';
						$args['_billing_country']   = isset( $this->lead[ $id . '.6' ] ) ? $this->lead[ $id . '.6' ] : '';
					}
					$args['billing-address'] = $ywraq_field->get_value_entry_detail( $this->extract_from_lead( $raq_c['ywraq_billing_address'] ) );
				}

				//Address
				if ( $raq_c['ywraq_shipping_address'] != '' ) {
					$id                  = $raq_c['ywraq_shipping_address'];
					$fields_to_exclude[] = $id;
					$ywraq_field         = $this->get_field_by_id( $id, $form );
					if ( $ywraq_field->type == 'address' ) {
						$args['_shipping_address_1'] = isset( $this->lead[ $id . '.1' ] ) ? $this->lead[ $id . '.1' ] : '';
						$args['_shipping_address_2'] = isset( $this->lead[ $id . '.2' ] ) ? $this->lead[ $id . '.2' ] : '';
						$args['_shipping_city']      = isset( $this->lead[ $id . '.3' ] ) ? $this->lead[ $id . '.3' ] : '';
						$args['_shipping_state']     = isset( $this->lead[ $id . '.4' ] ) ? $this->lead[ $id . '.4' ] : '';
						$args['_shipping_postcode']  = isset( $this->lead[ $id . '.5' ] ) ? $this->lead[ $id . '.5' ] : '';
						$args['_shipping_country']   = isset( $this->lead[ $id . '.6' ] ) ? $this->lead[ $id . '.6' ] : '';
					}
					$args['shipping-address'] = $ywraq_field->get_value_entry_detail( $this->extract_from_lead( $raq_c['ywraq_shipping_address'] ) );
				}

				if ( $raq_c['ywraq_billing_phone'] != '' ) {
					$fields_to_exclude[]   = $raq_c['ywraq_billing_phone'];
					$ywraq_field           = $this->get_field_by_id( $raq_c['ywraq_billing_phone'], $form );
					$args['billing-phone'] = $ywraq_field->get_value_entry_detail( $this->extract_from_lead( $raq_c['ywraq_billing_phone'] ) );
				}

				$other_fields          = $this->ywraq_get_other_field( $form_id, $posted );
				$other_fields_labelled = array();
				if ( ! empty( $other_fields ) ) {
					foreach ( $form['fields'] as $index => $field ) {
						if ( ! in_array( $field->id, $fields_to_exclude ) ) {
							$formatted_value                        = $field->get_value_entry_detail( $this->extract_from_lead( $field->id ) );
							if(!empty($formatted_value)){
								$other_email_content                    .= sprintf( '<strong>%s</strong>: %s<br>', $field['label'], $formatted_value );
								$other_fields_labelled[ $field->label ] = $formatted_value;
							}

						}
					}

					$args['other_email_content'] = $other_email_content;
					$args['other_email_fields']  = $other_fields_labelled;
				}
			}
		}

		return $args;

	}

	/**
	 * Add order meta from request.
	 *
	 * @param $attr
	 * @param $order_id
	 * @param $raq
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function add_order_metas( $attr, $order_id, $raq ) {
		//default fields
		$attr['ywraq_customer_name']    = $raq['user_name'];
		$attr['ywraq_customer_message'] = $raq['user_message'];
		$attr['ywraq_customer_email']   = isset($raq['user_email']) ? $raq['user_email']: '';
		$attr['_raq_request']           = $raq;

		if ( isset( $raq['billing-phone'] ) ) {
			$attr['ywraq_billing_phone'] = $raq['billing-phone'];
		}

		if ( isset( $raq['other_email_content'] ) ) {
			$attr['ywraq_other_email_content'] = $raq['other_email_content'];
		}

		if ( isset( $raq['other_email_fields'] ) ) {
			$attr['ywraq_other_email_fields'] = $raq['other_email_fields'];
		}

		$ov_field = apply_filters( 'ywraq_override_order_billing_fields', true );
		if ( $ov_field ) {
			$supported_fields       = ywraq_get_connect_fields();
			$attr['_billing_email'] = $raq['user_email'];
			foreach ( $supported_fields as $field ) {
				$name = '_' . $field;
				if ( isset( $raq[ $name ] ) && ! empty( $raq[ $name ] ) ) {
					$attr[ $name ] = $raq[ $name ];
				}
			}
		}

		return $attr;
	}

	/**
	 * @param $id_field
	 *
	 * @return mixed
	 */
	function extract_from_lead( $id_field ) {

		$lead = $this->lead;

		foreach ( $this->lead as $key => $item ) {
			if ( ! preg_match( "/\A" . $id_field . "\b/i", $key ) && ! preg_match( "/\b" . $id_field . ".\b/i", $key ) ) {
				unset( $lead[ $key ] );
			}
		}

		return count( $lead ) > 1 ? $lead : reset( $lead );
	}

	/**
	 * @param $id
	 * @param $form
	 *
	 * @return mixed
	 */
	function get_field_by_id( $id, $form ) {
		foreach ( $form['fields'] as $item ) {
			if ( $item->id == $id ) {
				return $item;
			}
		}

		return false;
	}

	/**
	 * Add the request a quote table inside the email content
	 *
	 * @param $lead
	 * @param $form
	 *
	 * @return mixed
	 * @internal param $args
	 * @internal param $posted
	 */
	public function ywraq_gform_notification( $lead, $form ) {

		$this->form = $form;
		$this->lead = $lead;

		if ( $form['id'] == $this->get_selected_form_id() && isset( $form['yith-woocommerce-request-a-quote'] ) ) {

			YITH_YWRAQ_Order_Request()->ajax_create_order( false );
			$this->_message = '<div style="max-width:600px">';
			$this->_message .= yith_ywraq_get_email_template( true );
			$this->_message .= '</div>';


		}

		return $lead;
	}

	/**
	 * Filter the fields that should be showed in the quote
	 *
	 * @param $form_id
	 * @param $posted
	 *
	 * @return mixed
	 * @internal param array $exclusion_list
	 *
	 * @internal param $form
	 *
	 * @internal param $args
	 * @internal param $posted
	 */
	public function ywraq_get_other_field( $form_id, $posted ) {

		$selected_form = GFAPI::get_form( $form_id );

		//remove from $posted the fields that are not input fields
		foreach ( $posted as $k => $v ) {
			if ( strpos( $k, 'input_' ) === false ) {
				unset( $posted[ $k ] );
			}
		}

		if ( isset( $selected_form['yith-woocommerce-request-a-quote'] ) ) {
			foreach ( $selected_form['yith-woocommerce-request-a-quote'] as $key => $value ) {
				$key_post = 'input_' . $value;
				if ( isset( $posted[ $key_post ] ) ) {
					unset( $posted[ $key_post ] );
				}
			}
		}

		return $posted;
	}

	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'YITH WooCommerce Request a Quote Settings', 'yith-woocommerce-request-a-quote' ),
				'fields' => array(
					array(
						'name'    => 'ywraq',
						'tooltip' => esc_html__( 'This is the tooltip', 'yith-woocommerce-request-a-quote' ),
						'label'   => esc_html__( 'This is the label', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'text',
						'class'   => 'small',
					)
				)
			)
		);
	}

	/**
	 * Return the list label/value of the fields in the form
	 *
	 * @return array
	 */
	public function get_fields_of_current_form() {

		$current_form = $this->get_current_form();
		$fields       = array();

		if ( isset( $current_form['fields'] ) ) {
			$fields[] = array(
				'label' => esc_html__( 'Choose the field', 'yith-woocommerce-request-a-quote' ),
				'value' => '',
			);
			foreach ( $current_form['fields'] as $index => $field ) {
				$fields[] = array(
					'label' => esc_html__( $field['label'], 'yith-woocommerce-request-a-quote' ),
					'value' => $field['id'],
				);
			}
		}

		return $fields;

	}

	/**
	 * Configures the settings which should be rendered on the Form Settings
	 *
	 * @param $form
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {

		$fields = $this->get_fields_of_current_form();

		$settings_fields = array(
			array(
				'title'  => esc_html__( 'YITH WooCommerce Request a Quote Settings', 'yith-woocommerce-request-a-quote' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Name of user *', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'select',
						'name'    => 'ywraq_name',
						'tooltip' => esc_html__( 'Choose what field should be used for the name', 'yith-woocommerce-request-a-quote' ),
						'choices' => $fields,
					),
					array(
						'label'   => esc_html__( 'Email of user *', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'select',
						'name'    => 'ywraq_email',
						'tooltip' => esc_html__( 'Choose what field should be used for the email', 'yith-woocommerce-request-a-quote' ),
						'choices' => $fields,
					),

					array(
						'label'   => esc_html__( 'Message', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'select',
						'name'    => 'ywraq_message',
						'tooltip' => esc_html__( 'Choose what field should be used for the email', 'yith-woocommerce-request-a-quote' ),
						'choices' => $fields,
					),

					array(
						'label'   => esc_html__( 'Phone', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'select',
						'name'    => 'ywraq_billing_phone',
						'tooltip' => esc_html__( 'Choose what field should be used for the Phone', 'yith-woocommerce-request-a-quote' ),
						'choices' => $fields,
					),

					array(
						'label'   => esc_html__( 'Billing Address', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'select',
						'name'    => 'ywraq_billing_address',
						'tooltip' => esc_html__( 'Choose what field should be used for the Address', 'yith-woocommerce-request-a-quote' ),
						'choices' => $fields,
					),

					array(
						'label'   => esc_html__( 'Shipping Address', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'select',
						'name'    => 'ywraq_shipping_address',
						'tooltip' => esc_html__( 'Choose what field should be used for the Shipping Address', 'yith-woocommerce-request-a-quote' ),
						'choices' => $fields,
					),
					array(
						'label'   => esc_html__( 'Product List', 'yith-woocommerce-request-a-quote' ),
						'type'    => 'select',
						'name'    => 'ywraq_product_list',
						'tooltip' => esc_html__( 'Choose what field should be used for the Shipping Address', 'yith-woocommerce-request-a-quote' ),
						'choices' => $fields,
					),
				),
			),
		);

		return apply_filters( 'ywraq_gravity_forms_addon_setting_fields', $settings_fields );
	}

	/**
	 * Return an array for show the list of forms in the RAQ Form Setting Page
	 *
	 * @return array
	 */
	public function get_forms() {
		if ( ! ywraq_gravity_form_installed() ) {
			return array( '' => __( 'Plugin Gravity Forms not activated or not installed', 'yith-woocommerce-request-a-quote' ) );
		}

		$posts = GFAPI::get_forms();
		$array = array();

		foreach ( $posts as $key => $post ) {
			if ( ! $post['is_trash'] ) {
				$array[ $post['id'] ] = $post['title'];
			}
		}

		if ( empty( $array ) ) {
			return array( '' => __( 'No contact form found', 'yith-woocommerce-request-a-quote' ) );
		}

		return $array;
	}

	/**
	 * Get the selected form id in the Form Settings
	 *
	 * @return integer
	 */
	public function get_selected_form_id() {
        global $sitepress;
		if ( function_exists( 'icl_get_languages' ) && !is_null( $sitepress) ) {

			$current_language = $sitepress->get_current_language();
			$gravity_form_id  = get_option( 'ywraq_inquiry_gravity_forms_id_' . $current_language );
		} else {
			$gravity_form_id = get_option( 'ywraq_inquiry_gravity_forms_id' );
		}

		return apply_filters( 'ywraq_inquiry_gravity_form_id', $gravity_form_id );
	}

	/**
	 * Clear the list of request a quote
	 *
	 */
	public function reset_list( $is_success, $to, $subject, $message, $headers, $attachments, $message_format, $from, $from_name, $bcc, $reply_to, $entry ) {
        global $sitepress;
		if ( function_exists( 'icl_get_languages' ) && !is_null( $sitepress) ) {

			$current_language = $sitepress->get_current_language();
			$gravity_form  = get_option( 'ywraq_inquiry_gravity_forms_id_' . $current_language );
		} else {
			$gravity_form = get_option( 'ywraq_inquiry_gravity_forms_id' );
		}

		if ( $is_success && $entry['form_id'] == $gravity_form ) {

			if ( apply_filters( 'ywraq_clear_list_after_send_quote', true ) ) {
				YITH_Request_Quote()->clear_raq_list();
			}

			yith_ywraq_add_notice( ywraq_get_message_after_request_quote_sending( $this->quote ), 'success' );

		}
	}

	/**
	 * @param $list
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function add_to_option_list( $list ) {
		$list['gravity-forms'] = __( 'Gravity Forms', 'yith-woocommerce-request-a-quote' );

		return $list;
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	public function add_option( $options ) {
		if ( function_exists( 'wpml_get_active_languages_filter' )  ) {
			$langs = wpml_get_active_languages_filter( '', 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );

			if ( is_array( $langs ) ) {
				foreach ( $langs as $key => $lang ) {
					$gravity_forms[ 'gravity_forms_' . $key ] = array(
						'name'      => sprintf( '%s:', $lang['native_name'] ),
						'type'      => 'yith-field',
						'yith-type' => 'select',
						'desc'      => __( 'Choose the form to display', 'yith-woocommerce-request-a-quote' ),
						'options'   => apply_filters( 'gravity_forms_get_contact_forms', array() ),
						'id'        => 'ywraq_inquiry_gravity_forms_id_' . $key,
						'deps'      => array(
							'id'    => 'ywraq_inquiry_form_type',
							'value' => 'gravity-forms',
							'type'  => 'hide'
						),
						'class'     => 'gravity-forms'
					);
				}
			}

		} else {
			$gravity_forms = array(
				'name'      => '',
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'desc'      => sprintf( '%s. <a href="%s" class="ywraq_gf_link">%s<a>', __( 'Choose the form to display', 'yith-woocommerce-request-a-quote' ), esc_url( add_query_arg( array( 'page' => 'gf_edit_forms' ), admin_url() ) ), __( 'Edit form', 'yith-woocommerce-request-a-quote' ) ),
				'options'   => apply_filters( 'gravity_forms_get_contact_forms', array() ),
				'id'        => 'ywraq_inquiry_gravity_forms_id',
				'deps'      => array(
					'id'    => 'ywraq_inquiry_form_type',
					'value' => 'gravity-forms',
					'type'  => 'hide'
				),
				'class'     => 'gravity-forms'
			);

		}

		if ( ! empty( $gravity_forms ) ) {
			foreach ( $gravity_forms as $k => $gf ) {
				if ( ! is_array( $gf ) ) {
					$options['gravity_forms'] = $gravity_forms;
					break;
				}
				$options[ $k ] = $gf;
			}
		}

		return $options;
	}

	/**
	 * @param $form
	 */
	public function save_product_list( $form ){

		$form_id = isset( $_POST['gform_submit'] ) ? $_POST['gform_submit'] : false;
		$raq_content = !empty( YITH_Request_Quote()->get_raq_return() )  ? YITH_Request_Quote()->get_raq_return() : false;
		if( $form_id && $form_id === $this->get_selected_form_id() && $raq_content ){
			$products_list = array();
			foreach( $raq_content as $item => $value ){

				$product_id = !empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];
				$qty = !empty( $value['quantity'] ) ? $value['quantity'] : 1;
				$product = wc_get_product( $product_id );
				$products_list [] = $product->get_formatted_name().'x'.$qty;

			}

			$products_list = count( $products_list ) > 0 ? implode(', ', $products_list ) : '';

			$form                 = GFAPI::get_form( $form_id );
			$raq_c                = isset( $form['yith-woocommerce-request-a-quote'] ) ? $form['yith-woocommerce-request-a-quote'] : array();
			if( !empty( $raq_c['ywraq_product_list'] ) && isset( $_POST['input_'. $raq_c['ywraq_product_list'] ] ) && !empty( $products_list ) ) {
				$_POST[ 'input_' . $raq_c['ywraq_product_list'] ] = $products_list;
			}
		}
	}

}

/**
 * Unique access to instance of YWRAQ_Gravity_Forms_Add_On class
 *
 * @return \YWRAQ_Gravity_Forms_Add_On
 */
function YWRAQ_Gravity_Forms_Add_On() {
	return YWRAQ_Gravity_Forms_Add_On::get_instance();
}
