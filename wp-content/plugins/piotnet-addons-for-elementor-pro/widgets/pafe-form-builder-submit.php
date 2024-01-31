<?php

class PAFE_Form_Builder_Submit extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-form-builder-submit';
	}

	public function get_title() {
		return __( 'Submit', 'pafe' );
	}

	public function get_icon() {
		return 'icon-w-button';
	}

	public function get_categories() {
		return [ 'pafe-form-builder' ];
	}

	public function get_keywords() {
		return [ 'input', 'form', 'field', 'submit' ];
	}

	public function get_script_depends() {
		return [ 
			'pafe-form-builder',
			'pafe-paypal',
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-form-builder-style'
		];
	}

	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'elementor' ),
			'sm' => __( 'Small', 'elementor' ),
			'md' => __( 'Medium', 'elementor' ),
			'lg' => __( 'Large', 'elementor' ),
			'xl' => __( 'Extra Large', 'elementor' ),
		];
	}

	public function acf_get_field_key( $field_name, $post_id ) {
		global $wpdb;
		$acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s" , $field_name , 'acf-field' ) );
		// get all fields with that name.
		switch ( count( $acf_fields ) ) {
			case 0: // no such field
				return false;
			case 1: // just one result. 
				return $acf_fields[0]->post_name;
		}
		// result is ambiguous
		// get IDs of all field groups for this post
		$field_groups_ids = array();
		$field_groups = acf_get_field_groups( array(
			'post_id' => $post_id,
		) );
		foreach ( $field_groups as $field_group )
			$field_groups_ids[] = $field_group['ID'];
		
		// Check if field is part of one of the field groups
		// Return the first one.
		foreach ( $acf_fields as $acf_field ) {
            $acf_field_id = acf_get_field($acf_field->post_parent);
            if ( in_array($acf_field_id['parent'],$field_groups_ids) ) {
				return $acf_field->post_name;
            }
		}
		return false;
	}

    public function jetengine_repeater_get_field_object( $field_name, $meta_field_id ) {
        $meta_objects = get_option('jet_engine_meta_boxes');
        foreach ( $meta_objects as $meta_object ) {
            $meta_fields = $meta_object['meta_fields'];
            foreach ( $meta_fields as $meta_field ) {
                if ( ($meta_field['name'] == $meta_field_id) && ($meta_field['type'] == 'repeater') ) {
                    $meta_repeater_fields = $meta_field['repeater-fields'];
                    foreach ( $meta_repeater_fields as $meta_repeater_field ) {
                        if ( $meta_repeater_field['name'] == $field_name ) {
                            return $meta_repeater_field;
                        }
                    }
                }
            }
        }
    }

    public function metabox_group_get_field_object( $field_name, $meta_objects ) {
        foreach ( $meta_objects as $meta_object ) {
            $meta_fields = $meta_object['fields'];
            foreach ( $meta_fields as $meta_field ) {
                if ( ($meta_field['type'] == 'group') && ($meta_field['clone']) ) {
                    $meta_repeater_fields = $meta_field['fields'];
                    foreach ( $meta_repeater_fields as $meta_repeater_field ) {
                        if ( $meta_repeater_field['id'] == $field_name ) {
                            return $meta_repeater_field;
                        }
                    }
                }
            }
        }
        return false;
    }

	protected function _register_controls() {

		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'Button', 'elementor' ),
			]
		);

		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form ID* (Required)', 'pafe' ),
				'type' => $pafe_forms ? \Elementor\Controls_Manager::HIDDEN : \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter the same form id for all fields in a form, with latin character and no space. E.g order_form', 'pafe' ),
				'default' => $pafe_forms ? get_the_ID() : '',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Submit', 'elementor' ),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => self::get_button_sizes(),
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'elementor' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'label_block' => true,
				'default' => '',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => __( 'Icon Position', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __( 'Before', 'elementor' ),
					'right' => __( 'After', 'elementor' ),
				],
				'condition' => [
					'icon!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => __( 'Icon Spacing', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_other_options',
			[
				'label' => __( 'Other Options', 'elementor' ),
			]
		);

		$this->add_control(
			'remove_empty_form_input_fields',
			[
				'label' => __( 'Remove Empty Form Input Fields', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

        $this->add_control(
            'enter_submit_form',
            [
                'label' => __( 'Press Enter To Submit Form', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'hide_button_after_submitting',
            [
                'label' => __( 'Hide The Button After Submitting', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_integration',
			[
				'label' => __( 'Actions After Submit', 'elementor-pro' ),
			]
		);

		$actions = [
			[
				'name' => 'email',
				'label' => 'Email'
			],
			[
				'name' => 'email2',
				'label' => 'Email 2'
			],
			[
				'name' => 'booking',
				'label' => 'Booking'
			],
			[
				'name' => 'redirect',
				'label' => 'Redirect'
			],
			[
				'name' => 'register',
				'label' => 'Register'
			],
			[
				'name' => 'login',
				'label' => 'Login'
			],
			[
				'name' => 'update_user_profile',
				'label' => 'Update User Profile'
			],
			[
				'name' => 'webhook',
				'label' => 'Webhook'
			],
			[
				'name' => 'remote_request',
				'label' => 'Remote Request'
			],
			[
				'name' => 'popup',
				'label' => 'Popup'
			],
			[
				'name' => 'open_popup',
				'label' => 'Open Popup'
			],
			[
				'name' => 'close_popup',
				'label' => 'Close Popup'
			],
			[
				'name' => 'submit_post',
				'label' => 'Submit Post'
			],
			[
				'name' => 'woocommerce_add_to_cart',
				'label' => 'Woocommerce Add To Cart'
			],
			[
				'name' => 'mailchimp',
				'label' => 'MailChimp'
			],
			[
				'name' => 'mailchimp_v3',
				'label' => 'MailChimp V3 (Recommended)'
			],
			[
				'name' => 'mailerlite',
				'label' => 'MailerLite'
			],
			[
				'name' => 'mailerlite_v2',
				'label' => 'MailerLite V2 (Recommended)'
			],
			[
				'name' => 'mailchimp_v3',
				'label' => 'MailChimp V3 (Recommended)'
			],
			[
				'name' => 'activecampaign',
				'label' => 'ActiveCampaign'
			],
			[
				'name' => 'pdfgenerator',
				'label' => 'PDF Generator'
			],
			[
				'name' => 'getresponse',
				'label' => 'Getresponse'
			],
			[
				'name' => 'mailpoet',
				'label' => 'Mailpoet'
			],
			[
				'name' => 'zohocrm',
				'label' => 'Zoho CRM'
			],
            [
                'name'  => 'google_calendar',
                'label' => 'Google Calendar',
            ],
            [
                'name' => 'sendy',
                'label' => 'Sendy'
            ],
            [
                'name' => 'hubspot',
                'label' => 'Hubspot'
            ],
            [
                'name' => 'twilio_whatsapp',
                'label' => 'Twilio Whatsapp'
            ],
            [
                'name'  => 'twilio_sms',
                'label' => 'Twilio SMS',
            ],
            [
                'name'  => 'sendfox',
                'label' => 'SendFox',
            ],
			[
                'name'  => 'sendinblue',
                'label' => 'Sendinblue',
            ],
			[
                'name'  => 'constantcontact',
                'label' => 'Constant Contact',
            ],
			[
                'name'  => 'convertkit',
                'label' => 'Convertkit',
            ],
            [
                'name' => 'webhook_slack',
                'label' => 'Webhook Slack'
            ],
            [
                'name'  => 'twilio_sendgrid',
                'label' => 'Twilio SendGrid',
            ],
		];

		$actions_options = [];

		foreach ( $actions as $action ) {
			$actions_options[ $action['name'] ] = $action['label'];
		}

		$this->add_control(
			'submit_actions',
			[
				'label' => __( 'Add Action', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $actions_options,
				'label_block' => true,
				'default' => [
					'email',
				],
				'description' => __( 'Add actions that will be performed after a visitor submits the form (e.g. send an email notification). Choosing an action will add its setting below.', 'elementor-pro' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_email',
			[
				'label' => 'Email',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'submit_actions' => 'email',
				],
			]
		);

		$this->add_control(
			'email_to',
			[
				'label' => __( 'To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                'dynamic' => [
                    'active' => true,
                ],
			]
		);

		/* translators: %s: Site title. */
		$default_message = sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_option( 'blogname' ) );

		$this->add_control(
			'email_subject',
			[
				'label' => __( 'Subject', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => $default_message,
				'placeholder' => $default_message,
				'label_block' => true,
				'render_type' => 'none',
                'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'email_content',
			[
				'label' => __( 'Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields pafe-control-dynamic-tags--metadata',
				'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above. Enter this if you want to customize sent fields and remove line if field empty [field id="your_field_id"][remove_line_if_field_empty]', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		// $site_domain = Utils::get_site_domain();

		$site_domain = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );

		$this->add_control(
			'email_from',
			[
				'label' => __( 'From Email', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'email@' . $site_domain,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_from_name',
			[
				'label' => __( 'From Name', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_bloginfo( 'name' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_reply_to',
			[
				'label' => __( 'Reply-To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'options' => [
					'' => '',
				],
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_cc',
			[
				'label' => __( 'Cc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_bcc',
			[
				'label' => __( 'Bcc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

        $this->add_control(
            'disable_attachment_pdf_email',
            [
                'label' => esc_html__( 'Disable attachment PDF file', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'pafe' ),
                'label_off' => esc_html__( 'No', 'pafe' ),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'submit_actions' => 'pdfgenerator'
                ]
            ]
        );

		$this->add_control(
			'form_metadata',
			[
				'label' => __( 'Meta Data', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'default' => [
					'date',
					'time',
					'page_url',
					'user_agent',
					'remote_ip',
				],
				'options' => [
					'date' => __( 'Date', 'elementor-pro' ),
					'time' => __( 'Time', 'elementor-pro' ),
					'page_url' => __( 'Page URL', 'elementor-pro' ),
					'user_agent' => __( 'User Agent', 'elementor-pro' ),
					'remote_ip' => __( 'Remote IP', 'elementor-pro' ),
				],
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_content_type',
			[
				'label' => __( 'Send As', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'plain',
				'render_type' => 'none',
				'options' => [
					'html' => __( 'HTML', 'elementor-pro' ),
					'plain' => __( 'Plain', 'elementor-pro' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_email_2',
			[
				'label' => 'Email 2',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'submit_actions' => 'email2',
				],
			]
		);

		$this->add_control(
			'email_to_2',
			[
				'label' => __( 'To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                'dynamic' => [
                    'active' => true,
                ],
			]
		);

		/* translators: %s: Site title. */
		$default_message = sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_option( 'blogname' ) );

		$this->add_control(
			'email_subject_2',
			[
				'label' => __( 'Subject', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => $default_message,
				'placeholder' => $default_message,
				'label_block' => true,
				'render_type' => 'none',
                'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'email_content_2',
			[
				'label' => __( 'Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields pafe-control-dynamic-tags--metadata',
				'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above. Enter this if you want to customize sent fields and remove line if field empty [field id="your_field_id"][remove_line_if_field_empty]', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_from_2',
			[
				'label' => __( 'From Email', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'email@' . $site_domain,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_from_name_2',
			[
				'label' => __( 'From Name', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_bloginfo( 'name' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_reply_to_2',
			[
				'label' => __( 'Reply-To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'options' => [
					'' => '',
				],
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_cc_2',
			[
				'label' => __( 'Cc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_bcc_2',
			[
				'label' => __( 'Bcc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

        $this->add_control(
            'disable_attachment_pdf_email2',
            [
                'label' => esc_html__( 'Disable attachment PDF file', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'pafe' ),
                'label_off' => esc_html__( 'No', 'pafe' ),
                'return_value' => 'yes',
                'default' => '',
                'condition' => [
                    'submit_actions' => 'pdfgenerator'
                ]
            ]
        );

        $this->add_control(
			'form_metadata_2',
			[
				'label' => __( 'Meta Data', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'default' => [],
				'options' => [
					'date' => __( 'Date', 'elementor-pro' ),
					'time' => __( 'Time', 'elementor-pro' ),
					'page_url' => __( 'Page URL', 'elementor-pro' ),
					'user_agent' => __( 'User Agent', 'elementor-pro' ),
					'remote_ip' => __( 'Remote IP', 'elementor-pro' ),
				],
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_content_type_2',
			[
				'label' => __( 'Send As', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'plain',
				'render_type' => 'none',
				'options' => [
					'html' => __( 'HTML', 'elementor-pro' ),
					'plain' => __( 'Plain', 'elementor-pro' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_form_database',
			[
				'label' => __( 'Form Database', 'pafe' ),
			]
		);

		$this->add_control(
			'form_database_disable',
			[
				'label' => __( 'Disable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'form_database_hidden_field_option',
			[
				'label' => __( 'Hidden Field (Database)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => 'When selected, the fields will be saved as ******.',
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'form_database_hidden_field',
			[
				'label' => __( 'Field ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'form_database_list_hidden_field',
			[
				'label' => __( 'Field List', 'pafe' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'condition' => [
					'form_database_hidden_field_option' => 'yes'
				],
				'title_field' => '{{{ form_database_hidden_field }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_booking',
			[
				'label' => __( 'Booking', 'pafe' ),
				'condition' => [
					'submit_actions' => 'booking',
				],
			]
		);

		$this->add_control(
			'booking_shortcode',
			[
				'label' => __( 'Booking Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_register',
			[
				'label' => __( 'Register', 'pafe' ),
				'condition' => [
					'submit_actions' => 'register',
				],
			]
		);

		global $wp_roles;
		$roles = $wp_roles->roles;
		$roles_array = array();
		foreach ($roles as $key => $value) {
			$roles_array[$key] = $value['name'];
		}

		$this->add_control(
			'register_role',
			[
				'label' => __( 'Role', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $roles_array,
				'label_block' => true,
				'default' => 'subscriber',
			]
		);

		$this->add_control(
			'register_email',
			[
				'label' => __( 'Email Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_username',
			[
				'label' => __( 'Username Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="username"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_password',
			[
				'label' => __( 'Password Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="password"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_password_confirm',
			[
				'label' => __( 'Confirm Password Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="confirm_password"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_password_confirm_message',
			[
				'label' => __( 'Wrong Password Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Wrong Password', 'pafe' ),
			]
		);

		$this->add_control(
			'register_first_name',
			[
				'label' => __( 'First Name Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="first_name"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_last_name',
			[
				'label' => __( 'Last Name Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="last_name"]', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'register_user_meta',
            [
                'label' => __( 'User Meta', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'Choose', 'pafe' ),
                    'meta' => __( 'User Meta Key', 'pafe' ),
                    'acf' => __( 'ACF Field', 'pafe' ),
                    'metabox' => __( 'MetaBox Field', 'pafe' ),
                    'toolset' => __( 'Toolset Field', 'pafe' ),
                ],
                'description' => __( 'If you want to update user password, you have to create a password field and confirm password field', 'pafe' ),
            ]
        );

        $repeater->add_control(
            'register_user_meta_type',
            [
                'label' => __( 'User Meta Type', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'text' => __( 'Text,Textarea,Number,Email,Url,Password', 'pafe' ),
                    'image' => __( 'Image', 'pafe' ),
                    'gallery' => __( 'Gallery', 'pafe' ),
                    'select' => __( 'Select', 'pafe' ),
                    'radio' => __( 'Radio', 'pafe' ),
                    'checkbox' => __( 'Checkbox', 'pafe' ),
                    'true_false' => __( 'True / False', 'pafe' ),
                    'date' => __( 'Date', 'pafe' ),
                    'time' => __( 'Time', 'pafe' ),
                    // 'repeater' => __( 'ACF Repeater', 'pafe' ),
                    // 'google_map' => __( 'ACF Google Map', 'pafe' ),
                ],
                'default' => 'text',
                'condition' => [
                    'register_user_meta' => ['acf', 'metabox', 'toolset']
                ],
            ]
        );

		$repeater->add_control(
			'register_user_meta_key',
			[
				'label' => __( 'Meta Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g description',
			]
		);

		$repeater->add_control(
			'register_user_meta_field_id',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="description"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_user_meta_list',
			[
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ register_user_meta_key }}} - {{{ register_user_meta_field_id }}}',
				'label' => __( 'User Meta List', 'pafe' ),
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'pafe_google_calendar_section',
            [
                'label' => __('Google Calendar', 'pafe' ),
                'condition' => [
                    'submit_actions' => 'google_calendar',
                ],
            ]
        );

        $this->add_control(
            'google_calendar_enable',
            [
                'label'        => __('Enable', 'pafe' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'pafe' ),
                'label_off'    => __( 'No', 'pafe' ),
                'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

        $this->add_control(
            'google_calendar_summary',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Summary* (Required)', 'pafe' ),
                'label_block'  => true,
                'placeholder' => '[field id="summary"] or Event ABC',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'google_calendar_date_type',
            [
                'label' => __('Date Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,

				'options' => [
                    'date' => __( 'Date', 'pafe' ),
                    'date_time'   => __( 'Date Time', 'pafe' ),
				],
                'default' => 'date',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'google_calendar_date_start',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Date Start* (Required)', 'pafe' ),
                'label_block'  => true,
                'placeholder' => '[field id="date_start"]',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'google_calendar_date_end',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Date End* (Required)', 'pafe' ),
                'label_block'  => true,
                'placeholder' => '[field id="date_end"]',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

    	$this->add_control(
            'google_calendar_duration',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => 'Duration* (Required)',
                'label_block'  => true,
                'placeholder' => '',
                'description' => __('The unit is minute. Eg:30,60,90,...Use this option if you do not have the Date End', 'pafe' ),
                'condition' => [
                	'google_calendar_enable' => 'yes',
                    'google_calendar_date_type' => 'date_time',
                    'google_calendar_date_end' => ''
                ]
            ]
        );

        $this->add_control(
            'google_calendar_attendees_name',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Attendees Name* (Required)', 'pafe' ),
                'label_block'  => true,
                'placeholder' => '[field id="attendees_name"]',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'google_calendar_attendees_email',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Attendees Email* (Required)', 'pafe' ),
                'label_block'  => true,
                'placeholder' => '[field id="attendees_email"]',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'google_calendar_description',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Description', 'pafe' ),
                'label_block'  => true,
                'placeholder' => '[field id="description"]',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'google_calendar_location',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Location', 'pafe' ),
                'label_block'  => true,
                'placeholder' => '[field id="location"]',
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'google_calendar_remind_method',
            [
                'type' => \Elementor\Controls_Manager::SELECT,
                'label'        => __('Remind Method* (Required)', 'pafe' ),
                'label_block'  => true,
                'value'        => 'left',
                'options'      => [
                    'email'   => __( 'Email', 'pafe' ),
                    'popup' => __( 'Popup', 'pafe' ),
                ],
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'google_calendar_remind_time',
            [
                'type'         => \Elementor\Controls_Manager::TEXT,
                'label'        => __('Remind Time* (Required)', 'pafe' ),
                'label_block'  => true,
                'description' => __( 'The unit is minute. Eg:30,60,90,...', 'pafe' ),
                'condition' => [
                    'google_calendar_enable' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'section_login',
			[
				'label' => __( 'Login', 'pafe' ),
				'condition' => [
					'submit_actions' => 'login',
				],
			]
		);

		$this->add_control(
			'login_username',
			[
				'label' => __( 'Username or Email Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="username"]', 'pafe' ),
			]
		);

		$this->add_control(
			'login_password',
			[
				'label' => __( 'Password Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="password"]', 'pafe' ),
			]
		);

		$this->add_control(
			'login_remember',
			[
				'label' => __( 'Remember Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="remember"]', 'pafe' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_update_user_profile',
			[
				'label' => __( 'Update User Profile', 'pafe' ),
				'condition' => [
					'submit_actions' => 'update_user_profile',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'update_user_meta',
			[
				'label' => __( 'User Meta', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Choose', 'pafe' ),
					'display_name' => __( 'Display Name', 'pafe' ),
					'first_name' => __( 'First Name', 'pafe' ),
					'last_name' => __( 'Last Name', 'pafe' ),
					'description' => __( 'Bio', 'pafe' ),
					'email' => __( 'Email', 'pafe' ),
					'password' => __( 'Password', 'pafe' ),
					'url' => __( 'Website', 'pafe' ),
					'meta' => __( 'User Meta Key', 'pafe' ),
					'acf' => __( 'ACF Field', 'pafe' ),
					'metabox' => __( 'MetaBox Field', 'pafe' ),
					'toolset' => __( 'Toolset Field', 'pafe' ),
				],
				'description' => __( 'If you want to update user password, you have to create a password field and confirm password field', 'pafe' ),
			]
		);

		$repeater->add_control(
			'update_user_meta_type',
			[
				'label' => __( 'User Meta Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'text' => __( 'Text,Textarea,Number,Email,Url,Password', 'pafe' ),
					'image' => __( 'Image', 'pafe' ),
					'gallery' => __( 'Gallery', 'pafe' ),
					'select' => __( 'Select', 'pafe' ),
					'radio' => __( 'Radio', 'pafe' ),
					'checkbox' => __( 'Checkbox', 'pafe' ),
					'true_false' => __( 'True / False', 'pafe' ),
					'date' => __( 'Date', 'pafe' ),
					'time' => __( 'Time', 'pafe' ),
					// 'repeater' => __( 'ACF Repeater', 'pafe' ),
					// 'google_map' => __( 'ACF Google Map', 'pafe' ),
				],
				'default' => 'text',
				'condition' => [
					'update_user_meta' => ['acf', 'metabox', 'toolset']
				],
			]
		);

		$repeater->add_control(
			'update_user_meta_key',
			[
				'label' => __( 'User Meta Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g description',
				'condition' => [
					'update_user_meta' => ['meta', 'acf', 'metabox', 'toolset']
				],
			]
		);

		$repeater->add_control(
			'update_user_meta_field_shortcode',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="description"]', 'pafe' ),
			]
		);

		$repeater->add_control(
			'update_user_meta_field_shortcode_confirm_password',
			[
				'label' => __( 'Confirm Password Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="confirm_password"]', 'pafe' ),
				'condition' => [
					'update_user_meta' => 'password',
				],
			]
		);

		$repeater->add_control(
			'wrong_password_message',
			[
				'label' => __( 'Wrong Password Message', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Wrong Password', 'pafe' ),
				'condition' => [
					'update_user_meta' => 'password',
				],
			]
		);

		$this->add_control(
			'update_user_meta_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ update_user_meta }}} - {{{ update_user_meta_key }}} - {{{ update_user_meta_field_shortcode }}}',
				'label' => __( 'User Meta List', 'pafe' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_post',
			[
				'label' => __( 'Submit Post', 'pafe' ),
				'condition' => [
					'submit_actions' => 'submit_post',
				],
			]
		);

		$post_types = get_post_types( [], 'objects' );
		$post_types_array = array();
		$taxonomy = array();
		foreach ( $post_types as $post_type ) {
	        $post_types_array[$post_type->name] = $post_type->label;
	        $taxonomy_of_post_type = get_object_taxonomies( $post_type->name, 'names' );
	        $post_type_name = $post_type->name;
	        if (!empty($taxonomy_of_post_type) && $post_type_name != 'nav_menu_item' && $post_type_name != 'elementor_library' && $post_type_name != 'elementor_font' ) {
	        	if ($post_type_name == 'post') {
	        		$taxonomy_of_post_type = array_diff( $taxonomy_of_post_type, ["post_format"] );
	        	}
	        	$taxonomy[$post_type_name] = $taxonomy_of_post_type;
	        }
	    }

	    $taxonomy_array = array();
	    foreach ($taxonomy as $key => $value) {
	    	foreach ($value as $key_item => $value_item) {
	    		$taxonomy_array[$value_item . '|' . $key] = $value_item . ' - ' . $key;
	    	}
	    }

		$this->add_control(
			'submit_post_type',
			[
				'label' => __( 'Post Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $post_types_array,
				'default' => 'post',
			]
		);

		$this->add_control(
			'submit_post_taxonomy',
			[
				'label' => __( 'Taxonomy', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'category-post',
			]
		);

		$this->add_control(
			'submit_post_term_slug',
			[
				'label' => __( 'Term slug', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'description' => 'E.g news, [field id="term"]',
			]
		);

		$this->add_control(
			'submit_post_term',
			[
				'label' => __( 'Term Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'description' => __( 'E.g [field id="term"]', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'submit_post_taxonomy',
			[
				'label' => __( 'Taxonomy', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $taxonomy_array,
				'default' => 'category-post',
			]
		);

		$repeater->add_control(
			'submit_post_terms_slug',
			[
				'label' => __( 'Term slug', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g news',
			]
		);

		$repeater->add_control(
			'submit_post_terms_field_id',
			[
				'label' => __( 'Terms Select Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="term"]', 'pafe' ),
			]
		);

		$this->add_control(
			'submit_post_terms_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => 'term',
				'label' => __( 'Terms', 'pafe' ),
			)
		);

		$this->add_control(
			'submit_post_status',
			[
				'label' => __( 'Post Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'publish' => __( 'Publish', 'pafe' ),
					'pending' => __( 'Pending', 'pafe' ),
				],
				'default' => 'publish',
			]
		);

		$this->add_control(
			'submit_post_url_shortcode',
			[
				'label' => __( 'Post URL shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value="[post_url]" readonly />',
			]
		);

		$this->add_control(
			'submit_post_id_shortcode',
			[
				'label' => __( 'Post ID Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value="[post_id]" readonly />',
			]
		);

		$this->add_control(
			'submit_post_title',
			[
				'label' => __( 'Title Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="title"]', 'pafe' ),
			]
		);

		$this->add_control(
			'submit_post_content',
			[
				'label' => __( 'Content Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="content"]', 'pafe' ),
			]
		);

		$this->add_control(
			'submit_post_featured_image',
			[
				'label' => __( 'Featured Image Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="featured_image_upload"]', 'pafe' ),
			]
		);

		// $this->add_control(
		// 	'submit_post_url_edit',
		// 	[
		// 		'label' => __( 'Edit Post URL shortcode', 'pafe' ),
		// 		'label_block' => true,
		// 		'type' => \Elementor\Controls_Manager::RAW_HTML,
		// 		'classes' => 'forms-field-shortcode-edit-post',
		// 		'raw' => '<input class="elementor-form-field-shortcode" value="[edit_post edit_text='. "'Edit Post'" . ' sm=' . "'" . $this->get_id() . "'" . ' smpid=' . "'" . get_the_ID() . "'" .']' . get_the_permalink() . '[/edit_post]" readonly /></div><div class="elementor-control-field-description">' . __( 'Add this shortcode to your single template.', 'pafe' ) . ' The shortcode will be changed if you edit this form so you have to refresh Elementor Editor Page and then copy the shortcode. ' . __( 'Replace', 'pafe' ) . ' "' . get_the_permalink() . '" ' . __( 'by your Page URL contains your Submit Post Form.', 'pafe' ) . '</div>',
		// 	]
		// );

		$this->add_control(
			'submit_post_custom_field_source',
			[
				'label' => __( 'Custom Fields', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'post_custom_field' => __( 'Post Custom Field', 'pafe' ),
					'acf_field' => __( 'ACF Field', 'pafe' ),
					'toolset_field' => __( 'Toolset Field', 'pafe' ),
					'jet_engine_field' => __( 'JetEngine Field', 'pafe' ),
					'pods_field'  => __( 'Pods Field', 'pafe' ),
                    'metabox_field' => __( 'Metabox Field', 'pafe' ),
				],
				'default' => 'post_custom_field',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'submit_post_custom_field',
			[
				'label' => __( 'Custom Field Slug', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g custom_field_slug', 'pafe' ),
                'placeholder' => __( 'Avoid using common words like "image" "date"', 'pafe' ),
			]
		);

		$repeater->add_control(
			'submit_post_custom_field_id',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="addition"]', 'pafe' ),
			]
		);

		$repeater->add_control(
			'submit_post_custom_field_type',
			[
				'label' => __( 'Custom Field Type if you use ACF, Toolset, JetEngine, Pods or MetaBox', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'text' => __( 'Text,Textarea,Number,Email,Url,Password', 'pafe' ),
					'image' => __( 'Image', 'pafe' ),
					'gallery' => __( 'Gallery', 'pafe' ),
					'select' => __( 'Select', 'pafe' ),
					'radio' => __( 'Radio', 'pafe' ),
					'checkbox' => __( 'Checkbox', 'pafe' ),
					'true_false' => __( 'True / False', 'pafe' ),
					'date' => __( 'Date', 'pafe' ),
					'time' => __( 'Time', 'pafe' ),
					'repeater' => __( 'ACF Repeater', 'pafe' ),
					'google_map' => __( 'ACF Google Map', 'pafe' ),
					'acf_relationship' => __( 'ACF Relationship', 'pafe' ),
                    'jet_engine_repeater' => __( 'JetEngine Repeater', 'pafe' ),
                    'meta_box_group' => __( 'MetaBox Group', 'pafe' ),
                    'metabox_google_map' => __( 'MetaBox Google Map', 'pafe' ),

				],
				'default' => 'text',
			]
		);

        $repeater->add_control(
			'submit_post_save_timestamp',
			[
				'label' => __( 'Save as timestamp', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
                'condition' => [
                    'submit_post_custom_field_type' => 'date'
                ]
			]
		);

        $repeater->add_control(
            'submit_post_custom_field_group_id',
            [
                'label'       => __( 'Custom Field Group ID', 'pafe' ),
                'label_block' => true,
                'type'        => 'text',
                'description' => __( 'E.g custom_field_group', 'pafe' ),
                'condition'   => [
                    'submit_post_custom_field_type' => 'meta_box_group',
                ]
            ]
        );

		$this->add_control(
			'submit_post_custom_fields_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ submit_post_custom_field }}} - {{{ submit_post_custom_field_id }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stripe',
			[
				'label' => __( 'Stripe Payment', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_stripe_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pafe_stripe_currency',
			[
				'label' => __( 'Currency', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'USD' => 'USD',
					'AED' => 'AED',
					'AFN' => 'AFN',
					'ALL' => 'ALL',
					'AMD' => 'AMD',
					'ANG' => 'ANG',
					'AOA' => 'AOA',
					'ARS' => 'ARS',
					'AUD' => 'AUD',
					'AWG' => 'AWG',
					'AZN' => 'AZN',
					'BAM' => 'BAM',
					'BBD' => 'BBD',
					'BDT' => 'BDT',
					'BGN' => 'BGN',
					'BIF' => 'BIF',
					'BMD' => 'BMD',
					'BND' => 'BND',
					'BOB' => 'BOB',
					'BRL' => 'BRL',
					'BSD' => 'BSD',
					'BWP' => 'BWP',
					'BZD' => 'BZD',
					'CAD' => 'CAD',
					'CDF' => 'CDF',
					'CHF' => 'CHF',
					'CLP' => 'CLP',
					'CNY' => 'CNY',
					'COP' => 'COP',
					'CRC' => 'CRC',
					'CVE' => 'CVE',
					'CZK' => 'CZK',
					'DJF' => 'DJF',
					'DKK' => 'DKK',
					'DOP' => 'DOP',
					'DZD' => 'DZD',
					'EGP' => 'EGP',
					'ETB' => 'ETB',
					'EUR' => 'EUR',
					'FJD' => 'FJD',
					'FKP' => 'FKP',
					'GBP' => 'GBP',
					'GEL' => 'GEL',
					'GIP' => 'GIP',
					'GMD' => 'GMD',
					'GNF' => 'GNF',
					'GTQ' => 'GTQ',
					'GYD' => 'GYD',
					'HKD' => 'HKD',
					'HNL' => 'HNL',
					'HRK' => 'HRK',
					'HTG' => 'HTG',
					'HUF' => 'HUF',
					'IDR' => 'IDR',
					'ILS' => 'ILS',
					'INR' => 'INR',
					'ISK' => 'ISK',
					'JMD' => 'JMD',
					'JPY' => 'JPY',
					'KES' => 'KES',
					'KGS' => 'KGS',
					'KHR' => 'KHR',
					'KMF' => 'KMF',
					'KRW' => 'KRW',
					'KYD' => 'KYD',
					'KZT' => 'KZT',
					'LAK' => 'LAK',
					'LBP' => 'LBP',
					'LKR' => 'LKR',
					'LRD' => 'LRD',
					'LSL' => 'LSL',
					'MAD' => 'MAD',
					'MDL' => 'MDL',
					'MGA' => 'MGA',
					'MKD' => 'MKD',
					'MMK' => 'MMK',
					'MNT' => 'MNT',
					'MOP' => 'MOP',
					'MRO' => 'MRO',
					'MUR' => 'MUR',
					'MVR' => 'MVR',
					'MWK' => 'MWK',
					'MXN' => 'MXN',
					'MYR' => 'MYR',
					'MZN' => 'MZN',
					'NAD' => 'NAD',
					'NGN' => 'NGN',
					'NIO' => 'NIO',
					'NOK' => 'NOK',
					'NPR' => 'NPR',
					'NZD' => 'NZD',
					'PAB' => 'PAB',
					'PEN' => 'PEN',
					'PGK' => 'PGK',
					'PHP' => 'PHP',
					'PKR' => 'PKR',
					'PLN' => 'PLN',
					'PYG' => 'PYG',
					'QAR' => 'QAR',
					'RON' => 'RON',
					'RSD' => 'RSD',
					'RUB' => 'RUB',
					'RWF' => 'RWF',
					'SAR' => 'SAR',
					'SBD' => 'SBD',
					'SCR' => 'SCR',
					'SEK' => 'SEK',
					'SGD' => 'SGD',
					'SHP' => 'SHP',
					'SLL' => 'SLL',
					'SOS' => 'SOS',
					'SRD' => 'SRD',
					'STD' => 'STD',
					'SZL' => 'SZL',
					'THB' => 'THB',
					'TJS' => 'TJS',
					'TOP' => 'TOP',
					'TRY' => 'TRY',
					'TTD' => 'TTD',
					'TWD' => 'TWD',
					'TZS' => 'TZS',
					'UAH' => 'UAH',
					'UGX' => 'UGX',
					'UYU' => 'UYU',
					'UZS' => 'UZS',
					'VND' => 'VND',
					'VUV' => 'VUV',
					'WST' => 'WST',
					'XAF' => 'XAF',
					'XCD' => 'XCD',
					'XOF' => 'XOF',
					'XPF' => 'XPF',
					'YER' => 'YER',
					'ZAR' => 'ZAR',
					'ZMW' => 'ZMW',
				],
				'default' => 'USD',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions',
			[
				'label' => __( 'Subscriptions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'description' => __( 'E.g bills every day, 2 weeks, 3 months, 1 year', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'pafe_stripe_subscriptions_only_price_enable',
			[
				'label' => __( 'Subscriptions use only price?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'pafe_stripe_subscriptions_price_id',
			[
				'label' => __( 'Price ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions_only_price_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$this->add_control(
			'pafe_stripe_subscriptions_product_name',
			[
				'label' => __( 'Product Name* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Piotnet Addons For Elementor',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions_only_price_enable',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);
		$this->add_control(
			'pafe_stripe_subscriptions_product_id',
			[
				'label' => __( 'Product ID (Optional)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions_only_price_enable',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);
		$this->add_control(
			'pafe_stripe_tax_rate_enable',
			[
				'label' => __( 'Use Tax?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$this->add_control(
			'pafe_stripe_tax_rate',
			[
				'label' => __( 'Tax ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_tax_rate_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
					]
				]
			]
		);
		$this->add_control(
			'pafe_stripe_subscriptions_field_enable',
			[
				'label' => __( 'Subscriptions Plan Select Field', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions_only_price_enable',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions_field',
			[
				'label' => __( 'Subscriptions Plan Select Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="plan_select"]', 'pafe' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions_only_price_enable',
							'operator' => '==',
							'value' => ''
						],
						[
							'name' => 'pafe_stripe_subscriptions_field_enable',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_stripe_subscriptions_field_enable_repeater',
			[
				'label' => __( 'Subscriptions Plan Select Field', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_field_value',
			[
				'label' => __( 'Subscriptions Plan Field Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g Daily, Weekly, 3 Months, Yearly', 'pafe' ),
				'condition' => [
					'pafe_stripe_subscriptions_field_enable_repeater' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_interval',
			[
				'label' => __( 'Interval* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'day' => 'day',
					'week' => 'week',
					'month' => 'month',
					'year' => 'year',
				],
				'default' => 'year',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_interval_count',
			[
				'label' => __( 'Interval Count* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
				'description' => __( 'Interval "month", Interval Count "3" = Bills every 3 months', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_amount',
			[
				'label' => __( 'Amount', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'E.g 100, 1000', 'pafe' ),
				'condition' => [
					'pafe_stripe_subscriptions_amount_field_enable!' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_one_time_fee',
			[
				'label' => __( 'One-time Fee', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_amount_field_enable',
			[
				'label' => __( 'Amount Field Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_amount_field',
			[
				'label' => __( 'Amount Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="amount_yearly"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_subscriptions_amount_field_enable' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_cancel',
			[
				'label' => __( 'Canceling Subscriptions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_cancel_add',
			[
				'label' => __( '+', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'pafe_stripe_subscriptions_cancel' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_cancel_add_unit',
			[
				'label' => __( 'Unit', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'day' => 'day',
					'month' => 'month',
					'year' => 'year',
				],
				'default' => 'day',
				'condition' => [
					'pafe_stripe_subscriptions_cancel' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ pafe_stripe_subscriptions_interval_count }}} {{{ pafe_stripe_subscriptions_interval }}}',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pafe_stripe_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pafe_stripe_subscriptions_only_price_enable',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			)
		);

		$this->add_control(
			'pafe_stripe_amount',
			[
				'label' => __( 'Amount', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'E.g 100, 1000', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_amount_field_enable!' => 'yes',
					'pafe_stripe_subscriptions!' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_amount_field_enable',
			[
				'label' => __( 'Amount Field Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_subscriptions!' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_amount_field',
			[
				'label' => __( 'Amount Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="amount"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_amount_field_enable' => 'yes',
					'pafe_stripe_subscriptions!' => 'yes',
				],
			]
		);
		$this->add_control(
			'pafe_stripe_create_invoice',
			[
				'label' => __( 'Create Invoice?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				]
			]
		);
		$this->add_control(
			'pafe_stripe_tax_invoice',
			[
				'label' => __( 'Tax ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g: txr_1JJsT9Bi8bDi9Dwe8vDZZOVJ', 'pafe' ),
				'condition' => [
					'pafe_stripe_create_invoice' => 'yes'
				]
			]
		);
		$this->add_control(
			'pafe_stripe_customer_description',
			[
				'label' => __( 'Payment Description', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_name',
			[
				'label' => __( 'Customer Name Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_email',
			[
				'label' => __( 'Customer Email Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_info_field',
			[
				'label' => __( 'Customer Description Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_phone',
			[
				'label' => __( 'Customer Phone Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_line1',
			[
				'label' => __( 'Customer Address Line 1 Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_city',
			[
				'label' => __( 'Customer Address City Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_country',
			[
				'label' => __( 'Customer Address Country Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'description' => __( 'E.g [field id="country"]. You should create a select field, the country value is two-letter country code (https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_line2',
			[
				'label' => __( 'Customer Address Line 2 Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_postal_code',
			[
				'label' => __( 'Customer Address Postal Code Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_state',
			[
				'label' => __( 'Customer Address State Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_receipt_email',
			[
				'label' => __( 'Receipt Email', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_status_succeeded',
			[
				'label' => __( 'Succeeded Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'succeeded', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_status_pending',
			[
				'label' => __( 'Pending Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'pending', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_status_failed',
			[
				'label' => __( 'Failed Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'failed', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_message_succeeded',
			[
				'label' => __( 'Succeeded Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment success', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_message_pending',
			[
				'label' => __( 'Pending Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment pending', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_message_failed',
			[
				'label' => __( 'Failed Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment failed', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_paypal',
			[
				'label' => __( 'Paypal Payment', 'pafe' ),
			]
		);

		$this->add_control(
			'paypal_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'paypal_subscription_enable',
			[
				'label' => __( 'Enable Subscriptions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'paypal_enable' => 'yes'
				]
			]
		);
		$this->add_control(
			'paypal_subscription_sandbox',
			[
				'label' => __( 'Subscription Sandbox', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'no',
				'render_type' => 'none',
				'options' => [
					'no'  => __( 'No', 'pafe' ),
					'yes' => __( 'Yes', 'pafe' ),
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'paypal_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'paypal_subscription_enable',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'paypal_get_plans',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button class="pafe-admin-button-ajax elementor-button elementor-button-default" data-pafe-button-get-plans>Get Plans <i class="fas fa-spinner fa-spin"></i></button>', 'pafe' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'paypal_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'paypal_subscription_enable',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'paypal_result',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div class="pafe-paypal-plans-result"></div>', 'pafe' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'paypal_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'paypal_subscription_enable',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'paypal_plan',
			[
				'label' => __( 'Plan ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g: P-4X507011M1170704PMA3BWJA', 'pafe' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'paypal_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'paypal_subscription_enable',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'paypal_currency',
			[
				'label' => __( 'Currency', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'AUD' => 'AUD',
					'BRL' => 'BRL',
					'CAD' => 'CAD',
					'CZK' => 'CZK',
					'DKK' => 'DKK',
					'EUR' => 'EUR',
					'HKD' => 'HKD',
					'HUF' => 'HUF',
					'INR' => 'INR',
					'ILS' => 'ILS',
					'MYR' => 'MYR',
					'MXN' => 'MXN',
					'TWD' => 'TWD',
					'NZD' => 'NZD',
					'NOK' => 'NOK',
					'PHP' => 'PHP',
					'PLN' => 'PLN',
					'GBP' => 'GBP',
					'RUB' => 'RUB',
					'SGD' => 'SGD',
					'SEK' => 'SEK',
					'CHF' => 'CHF',
					'THB' => 'THB',
					'USD' => 'USD',
				],
				'default' => 'USD',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'paypal_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'paypal_subscription_enable',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);

		$this->add_control(
			'paypal_amount',
			[
				'label' => __( 'Amount', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g 100, 1000, [field id="amount"]', 'pafe' ),
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'paypal_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'paypal_subscription_enable',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);

		$this->add_control(
			'paypal_description',
			[
				'label' => __( 'Description', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g Piotnet Addons, [field id="description"]', 'pafe' ),
				'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'paypal_enable',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'paypal_subscription_enable',
							'operator' => '==',
							'value' => ''
						]
					]
				]
			]
		);

		$this->add_control(
			'paypal_locale',
			[
				'label'       => __( 'Locale', 'pafe' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g "fr_FR". By default PayPal smartly detects the correct locale for the buyer based on their geolocation and browser preferences. Go to this url to get your locale value <a href="https://developer.paypal.com/docs/checkout/reference/customize-sdk/#locale" target="_blank">https://developer.paypal.com/docs/checkout/reference/customize-sdk/#locale</a>', 'pafe' ),
					'conditions' => [
						'relation' => 'and',
						'terms' => [
							[
								'name' => 'paypal_enable',
								'operator' => '==',
								'value' => 'yes'
							],
							[
								'name' => 'paypal_subscription_enable',
								'operator' => '==',
								'value' => ''
							]
						]
					]
			]
		);

		$this->add_control(
			'pafe_paypal_message_succeeded',
			[
				'label' => __( 'Succeeded Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment success', 'pafe' ),
				'condition' => [
					'paypal_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_paypal_message_failed',
			[
				'label' => __( 'Failed Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment failed', 'pafe' ),
				'condition' => [
					'paypal_enable' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		//Mollie
		$this->start_controls_section(
			'section_mollie',
			[
				'label' => __( 'Mollie Payment', 'pafe' ),
			]
		);
		if(empty(get_option('piotnet-addons-for-elementor-pro-mollie-api-key'))){
			$this->add_control(
				'mollie_payment_note',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( 'Please enter mollie payment API Key at Dashboard->Piotnet Addons->Integration->Mollie Payment', 'pafe' ),
				]
			);
		}else{
			$this->add_control(
				'mollie_enable',
				[
					'label' => __( 'Enable', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);
            $this->add_control(
				'mollie_send_email',
				[
					'label' => __( 'Not sending to email when payment failed.', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
                    'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);
			$this->add_control(
				'mollie_currency',
				[
					'label' => __( 'Currency', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'options' => [
						'AUD' => 'AUD',
						'BRL' => 'BRL',
						'CAD' => 'CAD',
						'CZK' => 'CZK',
						'DKK' => 'DKK',
						'EUR' => 'EUR',
						'HKD' => 'HKD',
						'HUF' => 'HUF',
						'INR' => 'INR',
						'ILS' => 'ILS',
						'MYR' => 'MYR',
						'MXN' => 'MXN',
						'TWD' => 'TWD',
						'NZD' => 'NZD',
						'NOK' => 'NOK',
						'PHP' => 'PHP',
						'PLN' => 'PLN',
						'GBP' => 'GBP',
						'RUB' => 'RUB',
						'SGD' => 'SGD',
						'SEK' => 'SEK',
						'CHF' => 'CHF',
						'THB' => 'THB',
						'USD' => 'USD',
					],
					'default' => 'USD',
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'mollie_amount',
				[
					'label' => __( 'Amount', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'E.g 100, 1000, [field id="amount"]', 'pafe' ),
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'mollie_description',
				[
					'label' => __( 'Description', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'E.g Piotnet Addons, [field id="description"]', 'pafe' ),
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'mollie_locale',
				[
					'label' => __( 'Locale', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'en_US',
					'options' => [
						'en_US'  => __( 'en_US', 'pafe' ),
						'nl_NL'  => __( 'nl_NL', 'pafe' ),
						'nl_BE'  => __( 'nl_BE', 'pafe' ),
						'fr_FR'  => __( 'fr_FR', 'pafe' ),
						'fr_BE'  => __( 'fr_BE', 'pafe' ),
						'de_DE'  => __( 'de_DE', 'pafe' ),
						'de_AT'  => __( 'de_AT', 'pafe' ),
						'de_CH'  => __( 'de_CH', 'pafe' ),
						'es_ES'  => __( 'es_ES', 'pafe' ),
						'ca_ES'  => __( 'ca_ES', 'pafe' ),
						'pt_PT'  => __( 'pt_PT', 'pafe' ),
						'it_IT'  => __( 'it_IT', 'pafe' ),
						'nb_NO'  => __( 'nb_NO', 'pafe' ),
						'sv_SE'  => __( 'sv_SE', 'pafe' ),
						'fi_FI'  => __( 'fi_FI', 'pafe' ),
						'da_DK'  => __( 'da_DK', 'pafe' ),
						'is_IS'  => __( 'is_IS', 'pafe' ),
						'hu_HU'  => __( 'hu_HU', 'pafe' ),
						'pl_PL'  => __( 'pl_PL', 'pafe' ),
						'lv_LV'  => __( 'lv_LV', 'pafe' ),
						'lt_LT'  => __( 'lt_LT', 'pafe' ),
					],
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);
		$this->add_control(
			'mollie_custom_metadata',
			[
				'label' => esc_html__( 'Custom Metadata?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'pafe' ),
				'label_off' => esc_html__( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'mollie_enable' => 'yes',
				],
			]
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'mollie_metadata_label', [
				'label' => esc_html__( 'Label', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'mollie_metadata_value',
			[
				'label' => esc_html__( 'Value', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
			]
		);

		$this->add_control(
			'mollie_metadata_list',
			[
				'label' => esc_html__( 'Metadata List', 'pafe' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ mollie_metadata_value }}}',
				'condition' => [
					'mollie_enable' => 'yes',
					'mollie_custom_metadata' => 'yes'
				],
			]
		);
			$this->add_control(
				'pafe_mollie_message_succeeded',
				[
					'label' => __( 'Succeeded Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment success', 'pafe' ),
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'pafe_mollie_message_pending',
				[
					'label' => __( 'Pending Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment pending', 'pafe' ),
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'pafe_mollie_message_failed',
				[
					'label' => __( 'Failed Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment failed', 'pafe' ),
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);
			$this->add_control(
				'pafe_mollie_message_open',
				[
					'label' => __( 'Open Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment open', 'pafe' ),
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);
			$this->add_control(
				'pafe_mollie_message_canceled',
				[
					'label' => __( 'Canceled Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment canceled', 'pafe' ),
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);
			$this->add_control(
				'pafe_mollie_message_authorized',
				[
					'label' => __( 'Authorized Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment authorized', 'pafe' ),
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);
			$this->add_control(
				'pafe_mollie_message_expired',
				[
					'label' => __( 'Expired Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment expired', 'pafe' ),
					'condition' => [
						'mollie_enable' => 'yes',
					],
				]
			);
		}
		$this->end_controls_section();
        // Razorpay
        $this->start_controls_section(
			'section_razorpay',
			[
				'label' => __( 'Razorpay Payment', 'pafe' ),
			]
		);
        if(empty(get_option('piotnet-addons-for-elementor-pro-razorpay-api-key')) || empty(get_option('piotnet-addons-for-elementor-pro-razorpay-secret-key'))) {
			$this->add_control(
				'razorpay_payment_note',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( 'Please enter Razorpay API Key at Dashboard->Piotnet Addons->Integration->Razorpay Payment', 'pafe' ),
				]
			);
		}else{
			$this->add_control(
				'razorpay_enable',
				[
					'label' => __( 'Enable', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
                    'render_type' => 'none',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);
            $this->add_control(
				'razorpay_amount',
				[
					'label' => __( 'Amount', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'E.g 100, 1000, [field id="amount"]', 'pafe' ),
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'render_type' => 'none',
					'condition' => [
						'razorpay_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'razorpay_currency',
				[
					'label' => __( 'Currency', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
                    'render_type' => 'none',
					'options' => [
                        'AED' => 'AED','ALL' => 'ALL','AMD' => 'AMD','ARS' => 'ARS','AUD' => 'AUD','AWG' => 'AWG','BBD' => 'BBD','BDT' => 'BDT','BMD' => 'BMD',
                        'BND' => 'BND','BOB' => 'BOB','BSD' => 'BSD','BWP' => 'BWP','BZD' => 'BZD','CAD' => 'CAD','CHF' => 'CHF','CNY' => 'CNY','COP' => 'COP',
                        'CRC' => 'CRC','CUP' => 'CUP','CZK' => 'CZK','DKK' => 'DKK','DOP' => 'DOP','DZD' => 'DZD','EGP' => 'EGP','ETB' => 'ETB','EUR' => 'EUR',
                        'FJD' => 'FJD','GBP' => 'GBP','GHS' => 'GHS','GIP' => 'GIP','GMD' => 'GMD','GTQ' => 'GTQ','GYD' => 'GYD','HKD' => 'HKD','HNL' => 'HNL',
                        'HRK' => 'HRK','HTG' => 'HTG','HUF' => 'HUF','IDR' => 'IDR','ILS' => 'ILS','INR' => 'INR','JMD' => 'JMD','KES' => 'KES','KGS' => 'KGS',
                        'KHR' => 'KHR','KYD' => 'KYD','KZT' => 'KZT','LAK' => 'LAK','LKR' => 'LKR','LRD' => 'LRD','LSL' => 'LSL','MAD' => 'MAD','MDL' => 'MDL',
                        'MKD' => 'MKD','MMK' => 'MMK','MNT' => 'MNT','MOP' => 'MOP','MUR' => 'MUR','MVR' => 'MVR','MWK' => 'MWK','MXN' => 'MXN','MYR' => 'MYR',
                        'NAD' => 'NAD','NGN' => 'NGN','NIO' => 'NIO','NOK' => 'NOK','NPR' => 'NPR','NZD' => 'NZD','PEN' => 'PEN','PGK' => 'PGK','PHP' => 'PHP',
                        'PKR' => 'PKR','QAR' => 'QAR','RUB' => 'RUB','SAR' => 'SAR','SCR' => 'SCR','SEK' => 'SEK','SGD' => 'SGD','SLL' => 'SLL','SOS' => 'SOS','SSP' => 'SSP',
                        'SVC' => 'SVC','SZL' => 'SZL','THB' => 'THB','TTD' => 'TTD','TZS' => 'TZS','USD' => 'USD','UYU' => 'UYU','UZS' => 'UZS','YER' => 'YER','ZAR' => 'ZAR','TRY' => 'TRY',
					],
					'default' => 'INR',
					'condition' => [
						'razorpay_enable' => 'yes',
					],
				]
			);

            $this->add_control(
                'razorpay_partial_payment',
                [
                    'label' => esc_html__( 'Partial Payment?', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Yes', 'pafe' ),
                    'label_off' => esc_html__( 'No', 'pafe' ),
                    'return_value' => 'yes',
                    'render_type' => 'none',
                    'default' => '',
                    'condition' => [
                        'razorpay_enable' => 'yes',
                    ],
                ]
            );

            $this->add_control(
				'razorpay_first_payment_min_amount',
				[
					'label' => __( 'First Payment Min Amount', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'Minimum amount that must be paid by the customer as the first partial payment.', 'pafe' ),
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'render_type' => 'none',
					'condition' => [
						'razorpay_enable' => 'yes',
                        'razorpay_partial_payment' => 'yes'
					],
				]
			);

			$this->add_control(
				'razorpay_name',
				[
					'label' => __( 'Name', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
					'description' => __( 'The business name shown on the Checkout form. For example, Acme Corp.', 'pafe' ),
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
					'condition' => [
						'razorpay_enable' => 'yes',
					],
				]
			);
            $this->add_control(
				'razorpay_description',
				[
					'label' => __( 'Description', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
					'description' => __( 'E.g Piotnet Addons, [field id="description"]', 'pafe' ),
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
					'condition' => [
						'razorpay_enable' => 'yes',
					],
				]
			);
            $this->add_control(
				'razorpay_image',
				[
					'label' => __( 'Image', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
					'description' => __( 'Link to an image (usually your business logo) shown on the Checkout form.', 'pafe' ),
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
					'condition' => [
						'razorpay_enable' => 'yes',
					],
				]
			);
            $this->add_control(
                'razorpay_prefill',
                [
                    'label' => esc_html__( 'Prefill?', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Yes', 'pafe' ),
                    'label_off' => esc_html__( 'No', 'pafe' ),
                    'render_type' => 'none',
                    'return_value' => 'yes',
                    'default' => '',
                    'condition' => [
                        'razorpay_enable' => 'yes',
                    ],
                ]
            );
            $this->add_control(
				'razorpay_customer_name',
				[
					'label' => __( 'Customer Name', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'render_type' => 'none',
					'condition' => [
						'razorpay_enable' => 'yes',
                        'razorpay_prefill' => 'yes'
					],
				]
			);
            $this->add_control(
				'razorpay_customer_email',
				[
					'label' => __( 'Customer Email', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'render_type' => 'none',
					'condition' => [
						'razorpay_enable' => 'yes',
                        'razorpay_prefill' => 'yes'
					],
				]
			);
            $this->add_control(
				'razorpay_customer_contact',
				[
					'label' => __( 'Customer Phone', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
					'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
					'condition' => [
						'razorpay_enable' => 'yes',
                        'razorpay_prefill' => 'yes'
					],
				]
			);
            $this->add_control(
                'razorpay_notes',
                [
                    'label' => esc_html__( 'Notes?', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Yes', 'pafe' ),
                    'label_off' => esc_html__( 'No', 'pafe' ),
                    'return_value' => 'yes',
                    'render_type' => 'none',
                    'default' => '',
                    'condition' => [
                        'razorpay_enable' => 'yes',
                    ],
                ]
            );
            $repeater = new \Elementor\Repeater();

            $repeater->add_control(
                'razorpay_metadata_label', [
                    'label' => esc_html__( 'Label', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                ]
            );

            $repeater->add_control(
                'razorpay_metadata_value',
                [
                    'label' => esc_html__( 'Value', 'pafe' ),
                    'type' => \Elementor\PafeCustomControls\Select_Control::Select,
                ]
            );

            $this->add_control(
                'razorpay_note_list',
                [
                    'label' => esc_html__( 'Note List', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'title_field' => '{{{ razorpay_metadata_value }}}',
                    'render_type' => 'none',
                    'condition' => [
                        'razorpay_enable' => 'yes',
                        'razorpay_notes' => 'yes'
                    ],
                ]
            );
			$this->add_control(
				'pafe_razorpay_message_succeeded',
				[
					'label' => __( 'Succeeded Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment success', 'pafe' ),
                    'render_type' => 'none',
					'condition' => [
						'razorpay_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'pafe_razorpay_message_failed',
				[
					'label' => __( 'Failed Message', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'Payment failed', 'pafe' ),
                    'render_type' => 'none',
					'condition' => [
						'razorpay_enable' => 'yes',
					],
				]
			);
		}
		$this->end_controls_section();
        // Razorpay Subscriptions
        $this->start_controls_section(
			'section_razor_subcription',
			[
				'label' => __( 'Razorpay Subscriptions', 'pafe' ),
			]
		);
        if(empty(get_option('piotnet-addons-for-elementor-pro-razorpay-api-key')) || empty(get_option('piotnet-addons-for-elementor-pro-razorpay-secret-key'))) {
			$this->add_control(
				'razorpay_subcription_note',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( 'Please enter Razorpay API Key at Dashboard->Piotnet Addons->Integration->Razorpay Payment', 'pafe' ),
				]
			);
        }else{
            $this->add_control(
                'razorpay_sub_enable',
                [
                    'label' => __( 'Enable', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'default' => '',
                    'render_type' => 'none',
                    'label_on' => 'Yes',
                    'label_off' => 'No',
                    'return_value' => 'yes',
                ]
            );
            $this->add_control(
                'razor_subcription_get_plan',
                [
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'classes' => 'elementor-descriptor',
                    'raw' => __( '<div style="text-align:center;"><button class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button" data-pafe-sub-get-plan>Get Plan <i class="fas fa-spinner fa-spin"></i></button></div><div class="pafe-razor-get-plan-result" style="margin-top: 10px"></div>', 'pafe' ),
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razor_subcription_plan_id',
                [
                    'label' => __( 'Plan ID', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
                    'separator' => 'before',
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razor_subcription_total_count',
                [
                    'label' => __( 'Total Count', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'description' => __( 'The number of billing cycles for which the customer should be charged.', 'pafe'),
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'render_type' => 'none',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razor_subcription_quantity',
                [
                    'label' => __( 'Quantity', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
                    'description' => __( 'The number of times the customer should be charged the plan amount per invoice.', 'pafe'),
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'default' => '1',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razor_subcription_name',
                [
                    'label' => __( 'Name', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razor_subcription_desc',
                [
                    'label' => __( 'Description', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razor_subcription_image',
                [
                    'label' => __( 'Image', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razor_sub_prefill',
                [
                    'label' => __( 'Prefill?', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'default' => '',
                    'render_type' => 'none',
                    'label_on' => 'Yes',
                    'label_off' => 'No',
                    'return_value' => 'yes',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ]
                ]
            );
            $this->add_control(
                'razorpay_sub_customer_name',
                [
                    'label' => __( 'Customer Name', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'render_type' => 'none',
                    'condition' => [
                        'razor_sub_prefill' => 'yes',
                        'razorpay_sub_enable' => 'yes'
                    ],
                ]
            );
            $this->add_control(
                'razorpay_sub_customer_email',
                [
                    'label' => __( 'Customer Email', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'render_type' => 'none',
                    'condition' => [
                        'razor_sub_prefill' => 'yes',
                        'razorpay_sub_enable' => 'yes'
                    ],
                ]
            );
            $this->add_control(
                'razorpay_sub_customer_contact',
                [
                    'label' => __( 'Customer Phone', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'render_type' => 'none',
                    'classes' => 'pafe-control-dynamic-tags pafe-control-dynamic-tags--get-fields',
                    'condition' => [
                        'razor_sub_prefill' => 'yes',
                        'razorpay_sub_enable' => 'yes'
                    ],
                ]
            );
            $this->add_control(
                'razorpay_sub_notes',
                [
                    'label' => esc_html__( 'Notes?', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Yes', 'pafe' ),
                    'label_off' => esc_html__( 'No', 'pafe' ),
                    'return_value' => 'yes',
                    'render_type' => 'none',
                    'default' => '',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ],
                ]
            );
            $repeater = new \Elementor\Repeater();

            $repeater->add_control(
                'razorpay_sub_metadata_label', [
                    'label' => esc_html__( 'Label', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                ]
            );

            $repeater->add_control(
                'razorpay_sub_metadata_value',
                [
                    'label' => esc_html__( 'Value', 'pafe' ),
                    'type' => \Elementor\PafeCustomControls\Select_Control::Select,
                ]
            );

            $this->add_control(
                'razorpay_sub_note_list',
                [
                    'label' => esc_html__( 'Note List', 'pafe' ),
                    'type' => \Elementor\Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'title_field' => '{{{ razorpay_sub_metadata_label }}}',
                    'render_type' => 'none',
                    'condition' => [
                        'razorpay_sub_notes' => 'yes',
                        'razorpay_sub_enable' => 'yes'
                    ],
                ]
            );
            $this->add_control(
                'pafe_razorpay_sub_message_succeeded',
                [
                    'label' => __( 'Succeeded Message', 'pafe' ),
                    'label_block' => true,
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __( 'Subscription success', 'pafe' ),
                    'render_type' => 'none',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'pafe_razorpay_sub_message_failed',
                [
                    'label' => __( 'Failed Message', 'pafe' ),
                    'label_block' => true,
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __( 'Subscription failed', 'pafe' ),
                    'render_type' => 'none',
                    'condition' => [
                        'razorpay_sub_enable' => 'yes',
                    ],
                ]
            );
        }
        $this->end_controls_section();
		$this->start_controls_section(
			'section_recaptcha',
			[
				'label' => __( 'reCAPTCHA V3', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_recaptcha_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __('To use reCAPTCHA, you need to add the Site Key and Secret Key in Dashboard > Piotnet Addons > reCAPTCHA.'),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pafe_recaptcha_hide_badge',
			[
				'label' => __( 'Hide the reCaptcha v3 badge', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
                'condition' => [
                    'pafe_recaptcha_enable' => 'yes'
                ]
			]
		);

        $this->add_control(
			'pafe_recaptcha_score',
			[
				'label' => __( 'reCaptcha score?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
                'condition' => [
                    'pafe_recaptcha_enable' => 'yes',
                ]
			]
		);
        
        $this->add_control(
			'pafe_recaptcha_score_value',
			[
				'label' => __( 'Score', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 0.1,
                'max' => 1,
                'default' => 0.3,
                'condition' => [
                    'pafe_recaptcha_enable' => 'yes',
                    'pafe_recaptcha_score' => 'yes'
                ]
			]
		);
        $this->add_control(
			'pafe_recaptcha_msg_error',
			[
				'label' => __( 'Error messages', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'none',
                'default' => __( 'Cannot verify recaptcha identity.', 'pafe' ),
                'condition' => [
                    'pafe_recaptcha_enable' => 'yes',
                    'pafe_recaptcha_score' => 'yes'
                ]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_redirect',
			[
				'label' => __( 'Redirect', 'elementor-pro' ),
				'condition' => [
					'submit_actions' => 'redirect',
				],
			]
		);

		$this->add_control(
			'redirect_to',
			[
				'label' => __( 'Redirect To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-link.com', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->add_control(
			'redirect_open_new_tab',
			[
				'label' => __( 'Open In New Tab', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		if ( class_exists( 'WooCommerce' ) ) {  
			$this->start_controls_section(
				'section_woocommerce_add_to_cart',
				[
					'label' => __( 'WooCommerce Add To Cart', 'pafe' ),
					'condition' => [
						'submit_actions' => 'woocommerce_add_to_cart',
					],
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_product_id',
				[
					'label' => __( 'Product ID', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'submit_actions' => 'woocommerce_add_to_cart',
					],
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_price',
				[
					'label' => __( 'Price Field Shortcode', 'pafe' ),
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'label_block' => true,
					'condition' => [
						'submit_actions' => 'woocommerce_add_to_cart',
					],
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_custom_order_item_meta_enable',
				[
					'label' => __( 'Custom Order Item Meta', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'woocommerce_add_to_cart_custom_order_item_field_shortcode',
				[
					'label' => __( 'Field Shortcode, Repeater Shortcode', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
				]
			);

			$repeater->add_control(
				'woocommerce_add_to_cart_custom_order_item_remove_if_field_empty',
				[
					'label' => __( 'Remove If Field Empty', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$repeater->add_control(
				'woocommerce_add_to_cart_custom_order_item_remove_if_value_zero',
				[
					'label' => __( 'Remove If value is zero', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_custom_order_item_list',
				array(
					'type'    => Elementor\Controls_Manager::REPEATER,
					'fields'  => $repeater->get_controls(),
					'title_field' => '{{{ woocommerce_add_to_cart_custom_order_item_field_shortcode }}}',
					'condition' => [
						'woocommerce_add_to_cart_custom_order_item_meta_enable' => 'yes',
					],
				)
			);

			$this->end_controls_section();
    	}

		if ( defined('ELEMENTOR_PRO_VERSION') ) {
		    if ( version_compare( ELEMENTOR_PRO_VERSION, '2.4.0', '>=' ) ) {
		    	$this->start_controls_section(
					'section_popup',
					[
						'label' => __( 'Popup', 'elementor-pro' ),
						'condition' => [
							'submit_actions' => 'popup',
						],
					]
				);

				$this->add_control(
					'popup_action',
					[
						'label' => __( 'Action', 'elementor-pro' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'' => __( 'Choose', 'elementor-pro' ),
							'open' => __( 'Open Popup', 'elementor-pro' ),
							'close' => __( 'Close Popup', 'elementor-pro' ),
						],
					]
				);

				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.6.0', '<' ) ) {

					$this->add_control(
						'popup_action_popup_id',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'filter_type' => 'popup_templates',
							'condition' => [
								'popup_action' => ['open','close'],
							],
						]
					);

				} else {

					$this->add_control(
						'popup_action_popup_id',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'autocomplete' => [
								'object' => \ElementorPro\Modules\QueryControl\Module::QUERY_OBJECT_LIBRARY_TEMPLATE,
								'query' => [
									'posts_per_page' => 20,
									'meta_query' => [
										[
											'key' => Elementor\Core\Base\Document::TYPE_META_KEY,
											'value' => 'popup',
										],
									],
								],
							],
							'condition' => [
								'popup_action' => ['open','close'],
							],
						]
					);

				}

				$this->end_controls_section();

				$this->start_controls_section(
					'section_popup_open',
					[
						'label' => __( 'Open Popup', 'elementor-pro' ),
						'condition' => [
							'submit_actions' => 'open_popup',
						],
					]
				);

				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.6.0', '<' ) ) {

					$this->add_control(
						'popup_action_popup_id_open',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'filter_type' => 'popup_templates',
						]
					);

				} else {

					$this->add_control(
						'popup_action_popup_id_open',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'autocomplete' => [
								'object' => \ElementorPro\Modules\QueryControl\Module::QUERY_OBJECT_LIBRARY_TEMPLATE,
								'query' => [
									'posts_per_page' => 20,
									'meta_query' => [
										[
											'key' => Elementor\Core\Base\Document::TYPE_META_KEY,
											'value' => 'popup',
										],
									],
								],
							],
						]
					);

				}

				$this->end_controls_section();

				$this->start_controls_section(
					'section_popup_close',
					[
						'label' => __( 'Close Popup', 'elementor-pro' ),
						'condition' => [
							'submit_actions' => 'close_popup',
						],
					]
				);

				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.6.0', '<' ) ) {

					$this->add_control(
						'popup_action_popup_id_close',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'filter_type' => 'popup_templates',
						]
					);

				} else {

					$this->add_control(
						'popup_action_popup_id_close',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'autocomplete' => [
								'object' => \ElementorPro\Modules\QueryControl\Module::QUERY_OBJECT_LIBRARY_TEMPLATE,
								'query' => [
									'posts_per_page' => 20,
									'meta_query' => [
										[
											'key' => Elementor\Core\Base\Document::TYPE_META_KEY,
											'value' => 'popup',
										],
									],
								],
							],
						]
					);

				}

				$this->end_controls_section();
	    	}
    	}

    	$this->start_controls_section(
			'section_webhook',
			[
				'label' => __( 'Webhook', 'elementor-pro' ),
				'condition' => [
					'submit_actions' => 'webhook',
				],
			]
		);

		$this->add_control(
			'webhooks',
			[
				'label' => __( 'Webhook URL', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-webhook-url.com', 'elementor-pro' ),
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter the integration URL (like Zapier) that will receive the form\'s submitted data.', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'webhooks_advanced_data',
			[
				'label' => __( 'Advanced Data', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_remote_request',
			[
				'label' => __( 'Remote Request', 'pafe' ),
				'condition' => [
					'submit_actions' => 'remote_request',
				],
			]
		);

		$this->add_control(
			'remote_request_url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-endpoint-url.com', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'remote_request_arguments_parameter',
			[
				'label' => __( 'Parameter', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g method, timeout', 'pafe' ),
			]
		);

		$repeater->add_control(
			'remote_request_arguments_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g POST, 30', 'pafe' ),
			]
		);

		$this->add_control(
			'remote_request_arguments_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ remote_request_arguments_parameter }}} = {{{ remote_request_arguments_value }}}',
				'label' => __( 'Request arguments. E.g method = POST, method = GET, timeout = 30', 'pafe' ),
				'separator' => 'before',
			)
		);
		$this->add_control(
			'remote_request_header',
			[
				'label' => __( 'Header arguments', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'remote_request_header_parameter',
			[
				'label' => __( 'Parameter', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g content-type, x-powered-by', 'pafe' ),
			]
		);

		$repeater->add_control(
			'remote_request_header_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g application/php, PHP/5.3.3', 'pafe' ),
			]
		);

		$this->add_control(
			'remote_request_header_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ remote_request_header_parameter }}} = {{{ remote_request_header_value }}}',
				'label' => __( 'Header arguments. E.g content-type = application/php, x-powered-by = PHP/5.3.3', 'pafe' ),
				'separator' => 'before',
				'condition' => [
					'remote_request_header' => 'yes'
				]
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'remote_request_body_parameter',
			[
				'label' => __( 'Parameter', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email', 'pafe' ),
			]
		);

		$repeater->add_control(
			'remote_request_body_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'remote_request_body_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ remote_request_body_parameter }}} = {{{ remote_request_body_value }}}',
				'label' => __( 'Body arguments. E.g email = [field id="email"]', 'pafe' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mailchimp',
			[
				'label' => __( 'MailChimp', 'pafe' ),
				'condition' => [
					'submit_actions' => 'mailchimp',
				],
			]
		);

		$this->add_control(
			'mailchimp_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using MailChimp API Key set in WP Dashboard > Piotnet Addons > MailChimp Integration. You can also set a different MailChimp API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'mailchimp_api_key_source' => 'default',
				],
			]
		);

		$this->add_control(
			'mailchimp_api_key_source',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'mailchimp_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_audience_id',
			[
				'label' => __( 'Audience ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g 82e5ab8640', 'pafe' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'mailchimp_acceptance_field_shortcode',
			[
				'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="acceptance"]', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_groups_id',
			[
				'label' => __( 'Groups', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'render_type' => 'none',
				'condition' => [
					'mailchimp_list!' => '',
				],
			]
		);

		$this->add_control(
			'mailchimp_tags',
			[
				'label' => __( 'Tags', 'elementor-pro' ),
				'description' => __( 'Add comma separated tags', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'none',
				'condition' => [
					'mailchimp_list!' => '',
				],
			]
		);

		// $this->add_control(
		// 	'mailchimp_double_opt_in',
		// 	[
		// 		'label' => __( 'Double Opt-In', 'elementor-pro' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'default' => '',
		// 		'condition' => [
		// 			'mailchimp_list!' => '',
		// 		],
		// 	]
		// );

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'mailchimp_field_mapping_address',
			[
				'label' => __( 'Address Field', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name. E.g EMAIL, FNAME, LNAME, ADDRESS', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g EMAIL, FNAME, LNAME, ADDRESS', 'pafe' ),
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode E.g [field id="email"]', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
				'condition' => [
					'mailchimp_field_mapping_address' => '',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_address_1',
			[
				'label' => __( 'Address 1 Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_address_2',
			[
				'label' => __( 'Address 2 Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_city',
			[
				'label' => __( 'City Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_state',
			[
				'label' => __( 'State Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_zip',
			[
				'label' => __( 'Zip Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_country',
			[
				'label' => __( 'Country Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$this->add_control(
			'mailchimp_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ mailchimp_field_mapping_tag_name }}} = {{{ mailchimp_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->end_controls_section();
		//Mailerlite V2
		$this->start_controls_section(
			'section_mailerlite_v2',
			[
				'label' => __( 'Mailerlite V2', 'pafe' ),
				'condition' => [
					'submit_actions' => 'mailerlite_v2',
				],
			]
		);
		$this->add_control(
			'mailerlite_note_v2',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using Mailerlite API Key set in WP Dashboard > Piotnet Addons > Mailerlite Integration. You can also set a different MailChimp API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'mailerlite_api_key_source_v2' => 'default',
				],
			]
		);

		$this->add_control(
			'mailerlite_api_key_source_v2',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'mailerlite_api_key_v2',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailerlite_api_key_source_v2' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);
		$this->add_control(
			'mailerlite_api_acceptance_field',
			[
				'label' => __( 'Acceptance Field?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'mailerlite_api_acceptance_field_shortcode',
			[
				'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="acceptance"]', 'pafe' ),
				'condition' => [
					'mailerlite_api_acceptance_field' => 'yes'
				]
			]
		);
		$this->add_control(
			'mailerlite_api_get_groups',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-mailerlite_api_get_groups class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Groups <i class="fas fa-spinner fa-spin"></i></button><br><div class="pafe-mailerlite-group-result" data-pafe-mailerlite-api-get-groups-results></div>', 'pafe' ),
			]
		);
		$this->add_control(
			'mailerlite_api_group',
			[
				'label' => __( 'Group ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your group here', 'pafe' ),
			]
		);
		$this->add_control(
			'mailerlite_api_get_fields',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div class="pafe-mailerlite-fields-result" data-pafe-mailerlite-api-get-fields-results></div>', 'pafe' ),
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'mailerlite_api_field_mapping_tag_name_v2',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g EMAIL, FNAME, LNAME, ADDRESS', 'pafe' ),
			]
		);

		$repeater->add_control(
			'mailerlite_api_field_mapping_field_shortcode_v2',
			[
				'label' => __( 'Field Shortcode E.g [field id="email"]', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'mailerlite_api_field_mapping_list_v2',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ mailerlite_api_field_mapping_tag_name_v2 }}} = {{{ mailerlite_api_field_mapping_field_shortcode_v2 }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);
		$this->end_controls_section();
		//Mailchimp V3
		$this->start_controls_section(
			'section_mailchimp_v3',
			[
				'label' => __( 'MailChimp V3', 'pafe' ),
				'condition' => [
					'submit_actions' => 'mailchimp_v3',
				],
			]
		);

		$this->add_control(
			'mailchimp_note_v3',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using MailChimp API Key set in WP Dashboard > Piotnet Addons > MailChimp Integration. You can also set a different MailChimp API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'mailchimp_api_key_source_v3' => 'default',
				],
			]
		);

		$this->add_control(
			'mailchimp_api_key_source_v3',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'mailchimp_api_key_v3',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_api_key_source_v3' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);
		$this->add_control(
			'mailchimp_confirm_email_v3',
			[
				'label' => __( 'Send confirm email?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'mailchimp_acceptance_field_shortcode_v3',
			[
				'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="acceptance"]', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_get_data_list',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-mailchimp-get-data-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get List IDs&ensp;<i class="fas fa-spinner fa-spin"></i></button><br><div data-pafe-mailchimp-get-data-list-results></div>', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_list_id',
			[
				'label' => __( 'List ID (<i>required</i>)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g 82e5ab8640', 'pafe' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'mailchimp_get_group_and_fields',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-mailchimp-get-group-and-field class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Groups and Fields <i class="fas fa-spinner fa-spin"></i></button><br>', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_get_groups',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div data-pafe-mailchimp-get-groups></div>', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_group_id',
			[
				'label' => __( 'Group IDs', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g ade42df840', 'pafe' ),
				'description' => 'You can add multiple group ids separated by commas.',
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'mailchimp_get_merge_fields',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div data-pafe-mailchimp-get-data-merge-fields></div>', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'mailchimp_field_mapping_address_v3',
			[
				'label' => __( 'Address Field?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'mailchimp_checkbox_field_mapping' => ''
				]
			]
		);

		$repeater->add_control(
			'address_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Address 1, City, State and Zip are required fields.', 'pafe' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition' => [
					'mailchimp_field_mapping_address_v3' => 'yes'
				]
			]
		);

		$repeater->add_control(
			'mailchimp_checkbox_field_mapping',
			[
				'label' => __( 'Field is Checkbox?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_tag_name_v3',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g EMAIL, FNAME, LNAME, ADDRESS', 'pafe' ),
				'condition' => [
					'mailchimp_checkbox_field_mapping' => ''
				]
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_field_shortcode_v3',
			[
				'label' => __( 'Field Shortcode E.g [field id="email"]', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
				'condition' => [
					'mailchimp_field_mapping_address_v3' => '',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_v3_field_mapping_address_field_shortcode_address_1',
			[
				'label' => __( 'Address 1 Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address_v3' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_v3_field_mapping_address_field_shortcode_address_2',
			[
				'label' => __( 'Address 2 Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address_v3' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_v3_field_mapping_address_field_shortcode_city',
			[
				'label' => __( 'City Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address_v3' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_v3_field_mapping_address_field_shortcode_state',
			[
				'label' => __( 'State Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address_v3' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_v3_field_mapping_address_field_shortcode_zip',
			[
				'label' => __( 'Zip Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address_v3' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_v3_field_mapping_address_field_shortcode_country',
			[
				'label' => __( 'Country Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailchimp_field_mapping_address_v3' => 'yes',
				],
			]
		);

		$this->add_control(
			'mailchimp_field_mapping_list_v3',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ mailchimp_field_mapping_tag_name_v3 }}} = {{{ mailchimp_field_mapping_field_shortcode_v3 }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_mailerlite',
			[
				'label' => __( 'MailerLite', 'pafe' ),
				'condition' => [
					'submit_actions' => 'mailerlite',
				],
			]
		);

		$this->add_control(
			'mailerlite_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using MailerLite API Key set in WP Dashboard > Piotnet Addons > MailerLite Integration. You can also set a different MailerLite API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'mailerlite_api_key_source' => 'default',
				],
			]
		);

		$this->add_control(
			'mailerlite_api_key_source',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'mailerlite_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailerlite_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);

		$this->add_control(
			'mailerlite_group_id',
			[
				'label' => __( 'GroupID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g 87562190', 'pafe' ),
			]
		);

		$this->add_control(
			'mailerlite_email_field_shortcode',
			[
				'label' => __( 'Email Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'mailerlite_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
			]
		);

		$repeater->add_control(
			'mailerlite_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'mailerlite_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ mailerlite_field_mapping_tag_name }}} = {{{ mailerlite_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_getresponse',
			[
				'label' => __( 'Getresponse', 'pafe' ),
				'condition' => [
					'submit_actions' => 'getresponse',
				],
			]
		);

		$this->add_control(
			'getresponse_api_key_source',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'getresponse_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'getresponse_api_key_source' => 'custom',
				],
			]
		);

		$this->add_control(
			'getresponse_get_data_list',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-getresponse-get-data-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get List&ensp;<i class="fas fa-spinner fa-spin"></i></button><div id="pafe-getresponse-list"></div>', 'pafe' ),
			]
		);

		$this->add_control(
			'getresponse_campaign_id',
			[
				'label' => __( 'Campaign ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);
		$this->add_control(
			'getresponse_date_of_cycle',
			[
				'label' => __( 'Day Of Cycle', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
			]
		);
		$this->add_control(
			'getresponse_get_data_custom_fields',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-getresponse-get-data-custom-fields class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Custom Fields&ensp;<i class="fas fa-spinner fa-spin"></i></button><div id="pafe-getresponse-custom-fields"></div>', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'getresponse_field_mapping_multiple',
			[
				'label' => __( 'Multiple Field?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$repeater->add_control(
			'getresponse_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
			]
		);

		$repeater->add_control(
			'getresponse_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'getresponse_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ getresponse_field_mapping_tag_name }}} = {{{ getresponse_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->end_controls_section();

		//Mailpoet
		$this->start_controls_section(
			'section_mailpoet',
			[
				'label' => __( 'Mailpoet', 'pafe' ),
				'condition' => [
					'submit_actions' => 'mailpoet',
				],
			]
		);
		$this->add_control(
			'mailpoet_send_confirmation_email',
			[
				'label' => __( 'Send Confirmation Email?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'description' => __( 'Send confirmation email to customer, if not send subscriber to be added as unconfirmed.', 'pafe' ),
			]
		);
		$this->add_control(
			'mailpoet_send_welcome_email',
			[
				'label' => __( 'Send Welcome Email', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'mailpoet_skip_subscriber_notification',
			[
				'label' => __( 'Skip subscriber notification?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'mailpoet_acceptance_field',
			[
				'label' => __( 'Acceptance Field?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'mailpoet_acceptance_field_shortcode',
			[
				'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'mailpoet_acceptance_field' => 'yes'
				]
			]
		);
		$this->add_control(
			'mailpoet_select_list',
			[
				'label' => __( 'Select Lists', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->mailpoet_get_list(),
				'label_block' => true,
			]
		);
		$this->add_control(
			'mailpoet_get_custom_field',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button class="elementor-button elementor-button-default piotnet-button-mailpoet-get-fields" data-piotnet-mailpoet-get-custom-fields>GET CUSTOM FIELDS <i class="fa fa-spinner fa-spin"></i></button><div class="piotnet-custom-fiedls-result" data-piotnet-mailpoet-result-custom-field></div>', 'pafe' ),
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'mailpoet_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
			]
		);

		$repeater->add_control(
			'mailpoet_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
			]
		);
		$this->add_control(
			'mailpoet_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ mailpoet_field_mapping_tag_name }}} = {{{ mailpoet_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
				'default' => [
					[
						'mailpoet_field_mapping_tag_name' =>  __( 'email', 'pafe' ),
					]
				]
			)
		);

		$this->end_controls_section();

		//Activecampaign

		$this->start_controls_section(
			'section_activecampaign',
			[
				'label' => __( 'ActiveCampaign', 'pafe' ),
				'condition' => [
					'submit_actions' => 'activecampaign',
				],
			]
		);

		$this->add_control(
			'activecampaign_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using ActiveCampaign API Key set in WP Dashboard > Piotnet Addons > ActiveCampaign Integration. You can also set a different ActiveCampaign API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'activecampaign_api_key_source' => 'default',
				],
			]
		);

		$this->add_control(
			'activecampaign_api_key_source',
			[
				'label' => __( 'API Credentials', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'activecampaign_api_url',
			[
				'label' => __( 'Custom API URL', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'activecampaign_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API URL for the current form', 'pafe' ),
			]
		);

		$this->add_control(
			'activecampaign_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'activecampaign_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);
		
		$this->add_control(
			'activecampaign_edit_contact',
			[
				'label' => __( 'Edit Contact?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'activecampaign_get_data_list',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-campaign-get-data-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Click Here To Get List IDs&ensp;<i class="fas fa-spinner fa-spin"></i></button><br><br><div data-pafe-campaign-get-data-list-results></div>', 'pafe' ),
				'content_classes' => 'your-class',
			]
		);

		$this->add_control(
			'activecampaign_list',
			[
				'label' => __( 'List ID* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

		$this->add_control(
			'activecampaign_get_flelds',
			[	
				'label' => __( 'Tag Name List', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div>
				<br><br>
				<input type="text" value="email" readonly/>
				<br><br>
				<input type="text" value="first_name" readonly/>
				<br><br>
				<input type="text" value="last_name" readonly/>
				<br><br>
				<input type="text" value="phone" readonly/>
				<br><br>
				<input type="text" value="customer_acct_name" readonly/>
				<br><br>
				<input type="text" value="tags" readonly/>
				<br><br>
				</div>
				<div data-pafe-campaign-get-fields></div>
				', 'pafe' ),
				'content_classes' => 'your-class',
			]
		);


		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'activecampaign_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
			]
		);

		$repeater->add_control(
			'activecampaign_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
			]
		);

		$this->add_control(
			'activecampaign_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ activecampaign_field_mapping_tag_name }}} = {{{ activecampaign_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->end_controls_section();
		//Zoho CRM
		$zoho_token = get_option('zoho_access_token');
		$this->start_controls_section(
			'section_zohocrm',
			[
				'label' => __( 'Zoho CRM', 'pafe' ),
				'condition' => [
					'submit_actions' => 'zohocrm',
				],
			]
		);
		if(empty($zoho_token)){
			$this->add_control(
				'zohocrm_note',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( 'Please get the Zoho CRM token in admin page.', 'pafe' ),
				]
			);
		}else{
			$this->add_control(
				'zohocrm_module',
				[
					'label' => __( 'Zoho Module', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'Leads',
					'options' => [
						'Leads'  => __( 'Leads', 'pafe' ),
						'Accounts' => __( 'Accounts', 'pafe' ),
						'Contacts' => __( 'Contacts', 'pafe' ),
						'campaigns' => __( 'Campaigns', 'pafe' ),
						'deals' => __( 'Deals', 'pafe' ),
						'tasks' => __( 'Tasks', 'pafe' ),
						'cases' => __( 'Cases', 'pafe' ),
						'events' => __( 'Events', 'pafe' ),
						'calls' => __( 'Calls', 'pafe' ),
						'solutions' => __( 'Solutions', 'pafe'),
						'products' => __( 'Products', 'pafe'),
						'vendors' => __( 'Vendors', 'pafe'),
						'pricebooks' => __( 'Pricebooks', 'pafe'),
						'quotes' => __( 'Quotes', 'pafe'),
						'salesorders' => __( 'Salesorders', 'pafe' ),
						'purchaseorders' => __( 'Purchaseorders', 'pafe'),
						'invoices' => __( 'Invoices', 'pafe'),
						'custom' => __( 'Custom', 'pafe'),
						'notes' => __( 'Notes', 'pafe'),
 					],
				]
			);
			$this->add_control(
				'zohocrm_custom_module',
				[
					'label' => __( 'Module API Name', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'Enter your api module name here', 'pafe' ),
					'condition' => [
						'zohocrm_module' => 'custom'
					]
				]
			);
			$this->add_control(
				'zohocrm_get_field_mapping',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( '<button data-pafe-zohocrm-get-tag-name class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Tag Name&ensp;<i class="fas fa-spinner fa-spin"></i></button><div id="pafe-zohocrm-tag-name"></div>', 'pafe' ),
				]
			);
			$this->add_control(
				'zoho_acceptance_field',
				[
					'label' => __( 'Acceptance Field?', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'pafe' ),
					'label_off' => __( 'No', 'pafe' ),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			$this->add_control(
				'zoho_acceptance_field_shortcode',
				[
					'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'condition' => [
						'zoho_acceptance_field' => 'yes'
					]
				]
			);
	
			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'zohocrm_tagname', [
					'label' => __( 'Tag Name', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'zohocrm_shortcode', [
					'label' => __( 'Field Shortcode', 'pafe' ),
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'label_block' => true,
				]
			);

			$this->add_control(
				'zohocrm_fields_map',
				[
					'label' => __( 'Fields Mapping', 'pafe' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'default' => [
						[
							'list_title' => __( 'Title #1', 'pafe' ),
							'list_content' => __( 'Item content. Click the edit button to change this text.', 'pafe' ),
						],
						[
							'list_title' => __( 'Title #2', 'pafe' ),
							'list_content' => __( 'Item content. Click the edit button to change this text.', 'pafe' ),
						],
					],
					'title_field' => '{{{ zohocrm_tagname }}} --- {{{ zohocrm_shortcode }}}',
				]
			);
		}
		$this->end_controls_section();
		//Convertkit
		$this->start_controls_section(
			'section_convertkit',
			[
				'label' => __( 'Convertkit', 'pafe' ),
				'condition' => [
					'submit_actions' => 'convertkit',
				],
			]
		);
		$this->add_control(
			'convertkit_api_key_source',
			[
				'label' => __( 'API Credentials', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'render_type' => 'none'
			]
		);
		$this->add_control(
			'convertkit_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your api key here', 'pafe' ),
				'condition'   => [
					'convertkit_api_key_source' => 'custom',
				],
				'render_type' => 'none'
			]
		);
		$this->add_control(
			'convertkit_acceptance_field',
			[
				'label' => __( 'Acceptance Field?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'pafe' ),
				'label_off' => __( 'Hide', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'render_type' => 'none'
			]
		);
		$this->add_control(
			'convertkit_acceptance_field_shortcode',
			[
				'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition'   => [
					'convertkit_acceptance_field' => 'yes',
				],
				'render_type' => 'none'
			]
		);
		$this->add_control(
			'convertkit_form_id',
			[
				'label' => __( 'Form ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your form id here', 'pafe' ),
				'render_type' => 'none'
			]
		);
		$this->add_control(
			'convertkit_get_data_list',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-convertkit-get-data class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Click Here To Get Form IDs&ensp;<i class="fas fa-spinner fa-spin"></i></button><br><br><div data-pafe-convertkit-get-data-results></div>', 'pafe' ),
			]
		);
		$this->add_control(
			'convertkit_get_data_fields',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div data-pafe-convertkit-fields></div>', 'pafe' ),
			]
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'convertkit_tag_name', [
				'label' => __( 'Tag Name', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'render_type' => 'none',
				'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
			]
		);

		$repeater->add_control(
			'convertkit_shortcode', [
				'label' => __( 'Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'label_block' => true,
				'render_type' => 'none',
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);
		$this->add_control(
			'convertkit_field_mapping_list',
			[
				'label' => __( 'Mapping List', 'pafe' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'convertkit_tag_name' => __( 'email', 'pafe' ),
					],
				],
				'title_field' => '{{{ convertkit_tag_name }}}',
			]
		);
		$this->end_controls_section();
		//Sendinblue
		$this->start_controls_section(
			'section_sendinblue',
			[
				'label' => __( 'Sendinblue', 'pafe' ),
				'condition' => [
					'submit_actions' => 'sendinblue',
				],
			]
		);
		$this->add_control(
			'sendinblue_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using Sendinblue API Key set in WP Dashboard > Piotnet Addons > Sendinblue Integration. You can also set a different Sendinblue API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'sendinblue_api_key_source' => 'default',
				],
			]
		);
		$this->add_control(
			'sendinblue_api_key_source',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);
		$this->add_control(
			'sendinblue_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'sendinblue_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);
        $this->add_control(
			'sendinblue_api_update_contact',
			[
				'label' => __( 'Update Contact?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'sendinblue_api_acceptance_field',
			[
				'label' => __( 'Acceptance Field?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'sendinblue_api_acceptance_field_shortcode',
			[
				'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'condition' => [
					'sendinblue_api_acceptance_field' => 'yes'
				]
			]
		);
		$this->add_control(
			'sendinblue_list_ids',
			[
				'label' => __( 'List ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);
		$this->add_control(
			'sendinblue_api_get_list',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<button data-pafe-sendinblue-get-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Lists <i class="fas fa-spinner fa-spin"></i></button><br><div class="pafe-sendinblue-group-result" data-pafe-sendinblue-api-get-list-results></div>', 'pafe' ),
			]
		);
		$this->add_control(
			'sendinblue_api_get_attr',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<div class="pafe-sendinblue-attribute-result" data-pafe-sendinblue-api-get-attributes-result></div>', 'pafe' ),
			]
		);
		$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'sendinblue_tagname', [
					'label' => __( 'Tag Name', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
				]
			);

			$repeater->add_control(
				'sendinblue_shortcode', [
					'label' => __( 'Field Shortcode', 'pafe' ),
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'label_block' => true,
				]
			);

			$this->add_control(
				'sendinblue_fields_map',
				[
					'label' => __( 'Fields Mapping', 'pafe' ),
					'type' => \Elementor\Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'default' => [
						[
							'sendinblue_tagname' => __( 'email', 'pafe' ),
						],
					],
					'title_field' => '{{{ sendinblue_tagname }}} --- {{{ sendinblue_shortcode }}}',
				]
			);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_constant',
			[
				'label' => __( 'Constant Contact', 'pafe' ),
				'condition' => [
					'submit_actions' => 'constantcontact',
				],
			]
		);
		$constant_contact_token = get_option('piotnet-constant-contact-access-token');
		if(empty($constant_contact_token)){
			$this->add_control(
				'constant_contact_token_note',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( 'Please get the Constant Contact token in admin page.', 'pafe' ),
				]
			);
		}else{
			$this->add_control(
				'constant_contact_list_id',
				[
					'label' => __( 'List IDs', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'Enter your list id here', 'pafe' ),
				]
			);
			$this->add_control(
				'constant_contact_kind',
				[
					'label' => __( 'The type of address', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => 'home',
					'description' => 'The type of address. Available types are: home, work, mobile, fax, other',
					'placeholder' => __( 'Enter your kind here', 'pafe' ),
				]
			);
			$this->add_control(
				'constant_contact_get_list',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( '<button data-pafe-constant-contact-get-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get List&ensp;<i class="fas fa-spinner fa-spin"></i></button><div id="pafe-constant-contact-list"></div>', 'pafe' ),
				]
			);
			$this->add_control(
				'constant_contact_get_custom_fields',
				[
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw' => __( '<button data-pafe-constant-contact-get-tag-name class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Custom Fields&ensp;<i class="fas fa-spinner fa-spin"></i></button><div id="pafe-constant-contact-tag-name"></div>', 'pafe' ),
				]
			);
			$this->add_control(
				'constant_contact_acceptance_field',
				[
					'label' => __( 'Acceptance Field?', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Yes', 'pafe' ),
					'label_off' => __( 'No', 'pafe' ),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			$this->add_control(
				'constant_contact_acceptance_field_shortcode',
				[
					'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
					'type' => \Elementor\PafeCustomControls\Select_Control::Select,
					'get_fields' => true,
					'condition' => [
						'constant_contact_acceptance_field' => 'yes'
					]
				]
			);
			$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'constant_contact_tagname', [
						'label' => __( 'Tag Name', 'pafe' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'label_block' => true,
					]
				);

				$repeater->add_control(
					'constant_contact_shortcode', [
						'label' => __( 'Field Shortcode', 'pafe' ),
						'type' => \Elementor\PafeCustomControls\Select_Control::Select,
						'get_fields' => true,
						'label_block' => true,
					]
				);

				$this->add_control(
					'constant_contact_fields_map',
					[
						'label' => __( 'Fields Mapping', 'pafe' ),
						'type' => \Elementor\Controls_Manager::REPEATER,
						'fields' => $repeater->get_controls(),
						'default' => [
							[
								'constant_contact_tagname' => __( 'email_address', 'pafe' ),
							],
						],
						'title_field' => '{{{ constant_contact_tagname }}} --- {{{ constant_contact_shortcode }}}',
					]
				);
			}
		$this->end_controls_section();
        //Slack Webhook
        $this->start_controls_section(
            'section_webhook_slack',
            [
                'label' => __( 'Webhook Slack', 'pafe' ),
                'condition' => [
                    'submit_actions' => 'webhook_slack',
                ],
            ]
        );

        $this->add_control(
            'slack_webhook_url',
            [
                'label'       => __( 'Webhook URL', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'description' => __( "Enter the webhook URL that will receive the form's submitted data. <a href='https://slack.com/apps/A0F7XDUAZ-incoming-webhooks/' target='_blank'>Click here for instructions</a>" , 'pafe' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'slack_icon_url',
            [
                'label'       => __( 'Icon URL', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'slack_channel',
            [
                'label'       => __( 'Channel', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'description' => 'Enter the channel ID / channel name'
            ]
        );

        $this->add_control(
            'slack_username',
            [
                'label'       => __( 'Username', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'slack_pre_text',
            [
                'label'       => __( 'Pre Text', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'slack_title',
            [
                'label'       => __( 'Title', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'slack_message',
            [
                'label'       => __( 'Message', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'       => '[all-fields]',
                'placeholder' => '[all-fields]',
                'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above. Enter this if you want to customize sent fields and remove line if field empty [field id="your_field_id"][remove_line_if_field_empty]', 'pafe' ),
                'label_block' => true,
                'render_type' => 'none',
            ]
        );

        $this->add_control(
            'slack_color',
            [
                'label'       => __( 'Color', 'pafe' ),
                'type'        => \Elementor\Controls_Manager::COLOR,
                'value'       => '#2eb886',
            ]
        );

        $this->add_control(
            'slack_timestamp',
            [
                'label' => __( 'Timestamp', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();
        //End Slack Webhook

        //SendGrid
        $this->start_controls_section(
            'section_twilio_sendgrid',
            [
                'label' => __( 'Twilio SendGrid', 'pafe' ),
                'condition' => [
                    'submit_actions' => 'twilio_sendgrid',
                ],
            ]
        );

        $this->add_control(
            'twilio_sendgrid_api_key',
            [
                'label' => __( 'API Key', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'twilio_sendgrid_get_data_list',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __( '<button data-pafe-twilio-sendgrid-get-data-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get List IDs&ensp;<i class="fas fa-spinner fa-spin"></i></button><br><div data-pafe-twilio-sendgrid-get-data-list-results></div>', 'pafe' ),
            ]
        );

        $this->add_control(
            'twilio_sendgrid_list_ids',
            [
                'label' => __( 'List IDs', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'title' => __( 'Separate IDs with commas', 'pafe' ),
                'render_type' => 'none',
            ]
        );

        $this->add_control(
            'twilio_sendgrid_email_field_shortcode',
            [
                'label'       => __( 'Email Field Shortcode* (Required)', 'pafe' ),
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
                'label_block' => true,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'twilio_sendgrid_field_mapping_tag_name',
            [
                'label' => __( 'Tag Name', 'pafe' ),
                'label_block' => true,
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'E.g first_name, last_name, phone_number', 'pafe' ),
            ]
        );

        $repeater->add_control(
            'twilio_sendgrid_field_mapping_field_shortcode',
            [
                'label' => __( 'Field Shortcode', 'pafe' ),
                'label_block' => true,
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
                'placeholder' => __( 'E.g [field id="first_name"]', 'pafe' ),
            ]
        );

        $this->add_control(
            'twilio_sendgrid_field_mapping_list',
            array(
                'type'    => Elementor\Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'title_field' => '{{{ twilio_sendgrid_field_mapping_tag_name }}} = {{{ twilio_sendgrid_field_mapping_field_shortcode }}}',
                'label' => __( 'Reserved Field Mapping', 'pafe' ),
            )
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'twilio_sendgrid_field_mapping_custom_field_name',
            [
                'label' => __( 'Tag Name', 'pafe' ),
                'label_block' => true,
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'your_custom_field_name', 'pafe' ),
            ]
        );

        $repeater->add_control(
            'twilio_sendgrid_field_mapping_custom_field_shortcode',
            [
                'label' => __( 'Field Shortcode', 'pafe' ),
                'label_block' => true,
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( '[field id="custom_field"]', 'pafe' ),
            ]
        );

        $this->add_control(
            'twilio_sendgrid_field_mapping_custom_field_list',
            array(
                'type'    => Elementor\Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'title_field' => '{{{ twilio_sendgrid_field_mapping_custom_field_name }}} = {{{ twilio_sendgrid_field_mapping_custom_field_shortcode }}}',
                'label' => __( 'Custom Field Mapping', 'pafe' ),
            )
        );
        $this->end_controls_section();
        //End SendGrid

		//PDF Generator
		$this->start_controls_section(
			'section_pdfgenerator',
			[
				'label' => __( 'PDF Generator', 'pafe' ),
				'condition' => [
					'submit_actions' => 'pdfgenerator',
				],
			]
		);
		$this->add_control(
			'pdfgenerator_set_custom',
			[
				'label' => __( 'Custom Layout', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'pdfgenerator_import_template',
			[
				'label' => __( 'Import Template', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'description' => __( 'Only A4 size.', 'pafe' ),
				'default' => '',
				'condition' => [
					'pdfgenerator_set_custom' => 'yes',
					'pdfgenerator_size' => 'a4'
				]
			]
		);
		$this->add_control(
			'pdfgenerator_template_url',
			[
				'label' => __( 'PDF Template File URL', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Go to WP Dashboard > Media > Library > Upload PDF Template File > Get File URL', 'pafe' ),
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'pdfgenerator_set_custom',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'pdfgenerator_import_template',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				]
			]
		);
		$this->add_control(
			'pdfgenerator_size',
			[
				'label' => __( 'PDF Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'a4',
				'options' => [
					'a3'  => __( 'A3 (297*420)', 'pafe' ),
					'a4' => __( 'A4 (210*297)', 'pafe' ),
					'a5' => __( 'A5 (148*210)', 'pafe' ),
					'letter' => __( 'Letter (215.9*279.4)', 'pafe' ),
					'legal' => __( 'Legal (215.9*355.6)', 'pafe' ),
				],
			]
		);
		$this->add_control(
			'pdfgenerator_title',
			[
				'label' => __( 'Title', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title here', 'pafe' ),
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$this->add_control(
			'pdfgenerator_title_text_align',
			[
				'label' => __( 'Title Align', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __( 'Left', 'pafe' ),
					'center' => __( 'Center', 'pafe' ),
					'right' => __( 'Right', 'pafe' ),
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-pdf-generator-preview__title' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$this->add_control(
			'pdfgenerator_title_font_size',
			[
				'label' => __( 'Title Font Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-pdf-generator-preview__title' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$pdf_fonts = $this->pafe_get_pdf_fonts();
		$this->add_control(
			'pdfgenerator_font_family',
			[
				'label' => __( 'Font Family', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => $pdf_fonts,
			]
		);
		$this->add_control(
			'pdfgenerator_save_file',
			[
				'label' => __( 'Save file in core coding.', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'description' => 'This PDF file will be stored in: "wp-content\uploads\piotnet-addons-for-elementor"',
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'render_type' => 'none',
				'default' => '',
			]
		);
		$this->add_control(
			'pdfgenerator_custom_export_file',
			[
				'label' => __( 'Custom Export File Name ', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		$this->add_control(
			'pdfgenerator_export_file_name',
			[
				'label' => __( 'File Name', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Type your file here', 'pafe' ),
				'condition' => [
					'pdfgenerator_custom_export_file' => 'yes'
				]
			]
		);
		$this->add_control(
			'pdfgenerator_font_size',
			[
				'label' => __( 'Content Font Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-pdf-generator-preview__item' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pdfgenerator_set_custom' => 'yes'
				]
			]
		);

		$this->add_control(
			'pdfgenerator_color',
			[
				'label' => __( 'Title Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-pdf-generator-preview__item' => 'color: {{VALUE}}',
				],
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$this->add_control(
			'pdfgenerator_content_html',
			[
				'label' => __( 'Content HTML?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'pdfgenerator_background_image_enable',
			[
				'label' => __( 'Image Background', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'Hide', 'pafe' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'pdfgenerator_background_image',
			[
				'label' => __( 'Choose Image', 'pafe' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'description' => "Only access image fomat jpg.",
				'condition'=>[
					'pdfgenerator_background_image_enable'=>'yes'
				]
			]
		);
		$this->add_control(
			'pdfgenerator_heading_field_mapping',
			[
				'label' => __( 'Field Mapping', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$this->add_control(
			'pdfgenerator_heading_field_mapping_show_label',
			[
				'label' => __( 'Show Label', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$this->add_control(
			'pdfgenerator_heading_field_mapping_font_size',
			[
				'label' => __( 'Font Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-field-mapping__preview' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$this->add_control(
			'pdfgenerator_heading_field_mapping_color',
			[
				'label' => __( 'Text Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-field-mapping__preview' => 'color: {{VALUE}}',
				],
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);
		$this->add_control(
			'pdfgenerator_heading_field_mapping_text_align',
			[
				'label' => __( 'Text Align', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => __( 'Left', 'pafe' ),
					'center' => __( 'Center', 'pafe' ),
					'right' => __( 'Right', 'pafe' ),
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-field-mapping__preview' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'pdfgenerator_set_custom' => ''
				]
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pdfgenerator_field_shortcode',
			[
				'label' => __( 'Field shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pdfgenerator_field_type',
			[
				'label' => __( 'Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => __( 'Default', 'pafe' ),
					'image' => __( 'Image', 'pafe' ),
					'image-upload' => __( 'Image upload', 'pafe' ),
				],
			]
		);

		$repeater->add_control(
			'pdfgenerator_image_field',
			[
				'label' => __( 'Choose Image', 'pafe' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition'=>[
					'pdfgenerator_field_type' => ['image-upload']
				]
			]
		);

		// $this->add_control(
		// 	'pdfgenerator_image_field',
		// 	[
		// 		'label' => __( 'Image Upload', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'label_on' => __( 'Yes', 'pafe' ),
		// 		'label_off' => __( 'Hide', 'pafe' ),
		// 		'return_value' => 'yes',
		// 		'default' => 'yes',
		// 		'condition'=>[
		// 			'pdfgenerator_field_type' => ['image','image-upload']
		// 		]
		// 	]
		// );

		$repeater->add_control(
			'custom_font',
			[
				'label' => __( 'Custom Font Size?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'=>[
					'pdfgenerator_field_type' => ['default']
				],
			]
		);
		$repeater->add_control(
			'auto_position',
			[
				'label' => __( 'Auto Position?', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'pafe' ),
				'label_off' => __( 'No', 'pafe' ),
				'return_value' => 'yes',
				'default' => '',
				'condition'=>[
					'pdfgenerator_field_type' => ['default']
				],
			]
		);

		$repeater->add_control(
			'text_align',
			[
				'label' => esc_html__( 'Alignment', 'pafe' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'pafe' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'pafe' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'pafe' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};margin: 0px auto;width:95%;margin-left:20px',
				],
			]
		);

		$repeater->add_control(
			'font_size',
			[
				'label' => __( 'Font Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 14,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'=>[
					'custom_font'=>'yes'
				]
			]
		);
		$pdf_fonts_styles = $this->pafe_get_pdf_fonts_style();
		$repeater->add_control(
			'font_weight',
			[
				'label' => __( 'Font Style', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'N',
				'options' => $pdf_fonts_styles,
				'condition'=>[
					'custom_font'=>'yes'
				]
			]
		);
		$repeater->add_control(
			'color',
			[
				'label' => __( 'Text Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
                ],
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}}',
				],
				'condition'=>[
					'custom_font'=>'yes'
				]
			]
		);
		$repeater->add_control(
			'pdfgenerator_width',
			[
				'label' => __( 'Width', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => '%',
				'range' =>[
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}%;',
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'width: {{SIZE}}%;',
				],
				'condition'=>[
					'pdfgenerator_field_type' => ['default', 'image-upload', 'image']
				]
			]
		);
		$repeater->add_control(
			'pdfgenerator_height',
			[
				'label' => __( 'Height', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => '%',
				'range' =>[
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'height: {{SIZE}}%;',
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'height: {{SIZE}}%;',
				],
				'condition'=>[
					'pdfgenerator_field_type' => ['image']
				]
			]
		);

		$repeater->add_control(
			'pdfgenerator_set_x',
			[
				'label' => __( 'Set X (mm)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' =>[
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'description' => 'This feature only works while custom layout enabled.',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%;',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'auto_position',
							'operator' => '==',
							'value' => ''
						],
						[
							'name' => 'pdfgenerator_field_type',
							'operator' => '==',
							'value' => 'default'
						]
					],
				]
			]
		);

		$repeater->add_control(
			'pdfgenerator_set_y',
			[
				'label' => __( 'Set Y (mm)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' =>[
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'description' => 'This feature only works while custom layout enabled.',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%;',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'auto_position',
							'operator' => '==',
							'value' => ''
						],
						[
							'name' => 'pdfgenerator_field_type',
							'operator' => '==',
							'value' => 'default'
						]
					]
				]
			]
		);
		//Image
		$repeater->add_control(
			'pdfgenerator_image_set_x',
			[
				'label' => __( 'Set X (mm)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => '%',
				'range' =>[
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'description' => 'This feature only works while custom layout enabled.',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'left: {{SIZE}}%;',
				],
				'condition'=>[
					'pdfgenerator_field_type' => ['image', 'image-upload']
				],
			]
		);
		
		$repeater->add_control(
			'pdfgenerator_image_set_y',
			[
				'label' => __( 'Set Y (mm)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => '%',
				'range' =>[
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'description' => 'This feature only works while custom layout enabled.',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} img' => 'top: {{SIZE}}%;',
				],
				'condition'=>[
					'pdfgenerator_field_type' => ['image', 'image-upload']
				],
			]
		);

		$this->add_control(
			'pdfgenerator_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ pdfgenerator_field_shortcode }}} - {{{pdfgenerator_width["size"]}}} - {{{ pdfgenerator_set_x["size"] }}} - {{{ pdfgenerator_set_y["size"] }}} - {{{pdfgenerator_field_type}}}',
				'label' => __( 'Field Mapping', 'pafe' ),
				'condition' => [
					'pdfgenerator_set_custom' => 'yes'
				]
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_form_options',
			[
				'label' => __( 'Custom Messages', 'elementor-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'success_message',
			[
				'label' => __( 'Success Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'The form was sent successfully.', 'elementor-pro' ),
				'placeholder' => __( 'The form was sent successfully.', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'error_message',
			[
				'label' => __( 'Error Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'An error occured.', 'elementor-pro' ),
				'placeholder' => __( 'An error occured.', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'required_field_message',
			[
				'label' => __( 'Required Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'This field is required.', 'elementor-pro' ),
				'placeholder' => __( 'This field is required.', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'invalid_message',
			[
				'label' => __( 'Invalid Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( "There's something wrong. The form is invalid.", "elementor-pro" ),
				'placeholder' => __( "There's something wrong. The form is invalid.", "elementor-pro" ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_conditional_logic',
			[
				'label' => __( 'Conditional Logic', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_speed',
			[
				'label' => __( 'Speed', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g 100, 1000, slow, fast' ),
				'default' => 400,
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_easing',
			[
				'label' => __( 'Easing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g swing, linear' ),
				'default' => 'swing',
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_conditional_logic_form_if',
			[
				'label' => __( 'Show this submit If', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_comparison_operators',
			[
				'label' => __( 'Comparison Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'not-empty' => __( 'not empty', 'pafe' ),
					'empty' => __( 'empty', 'pafe' ),
					'=' => __( 'equals', 'pafe' ),
					'!=' => __( 'not equals', 'pafe' ),
					'>' => __( '>', 'pafe' ),
					'>=' => __( '>=', 'pafe' ),
					'<' => __( '<', 'pafe' ),
					'<=' => __( '<=', 'pafe' ),
					'checked' => __( 'checked', 'pafe' ),
					'unchecked' => __( 'unchecked', 'pafe' ),
					'contains' => __( 'contains', 'pafe' ),
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_type',
			[
				'label' => __( 'Type Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'string' => __( 'String', 'pafe' ),
					'number' => __( 'Number', 'pafe' ),
				],
				'default' => 'string',
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<=', 'contains'],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( '50', 'pafe' ),
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<=', 'contains'],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_and_or_operators',
			[
				'label' => __( 'OR, AND Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'or' => __( 'OR', 'pafe' ),
					'and' => __( 'AND', 'pafe' ),
				],
				'default' => 'or',
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'title_field' => '{{{ pafe_conditional_logic_form_if }}} {{{ pafe_conditional_logic_form_comparison_operators }}} {{{ pafe_conditional_logic_form_value }}}',
			)
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'section_limit_form_entries',
            [
                'label' => __( 'Limit The Form Entries', 'pafe' ),
            ]
        );
        $this->add_control(
            'pafe_limit_form_enable',
            [
                'label' => __( 'Enable', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => 'Yes',
                'label_off' => 'No',
                'return_value' => 'yes',
            ]
        );
        $this->add_control(
            'pafe_limit_entries_total_post',
            [
                'label' => __( 'Total Post', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( '', 'pafe' ),
                'label_block' => true,
                'condition' => [
                    'pafe_limit_form_enable' => 'yes'
                ],
            ]
        );
        $this->add_control(
            'pafe_limit_entries_custom_message',
            [
                'label' => __( 'Custom Message', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( '', 'pafe' ),
                'label_block' => true,
                'default' => __( 'Your contents have not been sent yet. The Form will be opened soon.', 'pafe' ),
                'condition' => [
                    'pafe_limit_form_enable' => 'yes'
                ],
            ]
        );

        $this->end_controls_section();

        //Hubspot
        $this->start_controls_section(
            'pafe_hubspot_section',
            [
                'label' => __( 'Hubspot', 'pafe' ),
                'condition' => [
                    'submit_actions' => 'hubspot',
                ],
            ]
        );

        $this->add_control(
            'pafe_hubspot_acceptance_field',
            [
                'label' => __( 'Acceptance Field?', 'pafe' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'pafe' ),
                'label_off' => __( 'No', 'pafe' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'pafe_hubspot_acceptance_field_shortcode',
            [
                'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
                'label_block' => true,
                'default' => __( '', 'pafe' ),
                'placeholder' => __( 'Enter your shortcode here', 'pafe' ),
                'condition' => [
                    'pafe_hubspot_acceptance_field' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'pafe_hubspot_get_group',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __( '<button data-pafe-hubspot-get-group-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Group List<i class="fas fa-spinner fa-spin"></i></button><div class="pafe-hubspot-group-list"></div>', 'pafe' ),
            ]
        );
        $this->add_control(
            'pafe_hubspot_group_key',
            [
                'label' => __( 'Group Key', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __( '', 'pafe' ),
                'placeholder' => __( 'Enter the group key here', 'pafe' ),
            ]
        );
        $this->add_control(
            'pafe_hubspot_get_property',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __( '<button data-pafe-hubspot-get-property-list class="pafe-admin-button-ajax elementor-button elementor-button-default" type="button">Get Property List<i class="fas fa-spinner fa-spin"></i></button><div class="pafe-hubspot-property-list"></div>', 'pafe' ),
            ]
        );


        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'pafe_hubspot_property_name',
            [
                'label' => __( 'Property Name', 'pafe' ),
                'label_block' => true,
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
            ]
        );

        $repeater->add_control(
            'pafe_hubspot_field_shortcode',
            [
                'label' => __( 'Field Shortcode', 'pafe' ),
                'label_block' => true,
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
            ]
        );

        $this->add_control(
            'pafe_hubspot_property_list',
            [
                'type'    => Elementor\Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'title_field' => '{{{pafe_hubspot_property_name}}}',
                'label' => __( 'Property', 'pafe' ),
            ]
        );

        $this->end_controls_section();

        // Add Sendy Integration
		$this->start_controls_section(
			'section_sendy',
			[
				'label' => __( 'Sendy', 'pafe' ),
				'condition' => [
					'submit_actions' => 'sendy',
				],
			]
		);

		$this->add_control(
            'sendy_url',
            [
                'label' => __( 'Sendy URL', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => 'http://your_sendy_installation/',
                'label_block' => true,
                'separator' => 'before',
                'description' => __( 'Enter the URL where you have Sendy installed, including a trailing /', 'pafe' ),
            ]
        );
		$this->add_control(
	        'sendy_api_key',
	        [
	            'label' => __( 'API key', 'pafe' ),
	            'type' => \Elementor\Controls_Manager::TEXT,
	            'description' => __( 'To find it go to Settings (top right corner) -> Your API Key.', 'pafe' ),
	        ]
        );
        $this->add_control(
            'sendy_list_id',
            [
                'label' => __( 'Sendy List ID', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'separator' => 'before',
                'description' => __( 'The list id you want to subscribe a user to.', 'pafe' ),
            ]
        );

        $this->add_control(
            'sendy_name_field_shortcode',
            [
                'label' => __( 'Name Field Shortcode', 'pafe' ),
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
            ]
        );

        $this->add_control(
            'sendy_email_field_shortcode',
            [
                'label' => __( 'Email Field Shortcode', 'pafe' ),
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
            ]
        );

        $this->add_control(
			'sendy_gdpr_shortcode',
			[
				'label' => __( 'GDPR/CCPA Compliant Shortcode', 'pafe' ),
				'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
			]
		);


        // $this->add_control(
        //     'sendy_gdpr',
        //     [
        //         'label' => __( 'GDPR/CCPA Compliant:', 'pafe' ),
        //         'type' => \Elementor\Controls_Manager::SWITCHER,
        //         'separator' => 'before',
        //         'return_value' => 'yes',
        //         'default' => 'no',
        //         'description' => __( 'Enable if your form is GDPR and CCPA compliant', 'pafe' ),
        //     ]
        // );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'custom_field_name', [
                'label' => __( 'Sendy Custom Field Name', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Place the Name of the Sendy Custom Field', 'pafe' ),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'custom_field_shortcode', [
                'label' => __( 'Custom Field Shortcode', 'pafe' ),
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'sendy_custom_fields',
            [
                'label' => __( 'Custom Fields', 'pafe' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ custom_field_name }}}',
                'separator' => 'before'
            ]
        );

		$this->end_controls_section();

		// End Sendy Integration

		// Add Twilio whatsapp
        $this->start_controls_section(
			'twilio_whatsapp_settings_section',
			[
				'label' => __( 'Twilio Whatsapp', 'pafe' ),
				'condition' => [
					'submit_actions' => 'twilio_whatsapp',
				],
			]
		);

		$this->add_control(
            'whatsapp_to',
            [
                'label' => __( 'Whatsapp To', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Phone with country code, like: +14155238886<br>You can send to multiple phone numbers, separated by commas.', 'pafe' ),
            ]
        );

        $this->add_control(
            'whatsapp_form',
            [
                'label' => __( 'Whatsapp Form', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Phone with country code, like: +14155238886', 'pafe' ),
            ]
        );

        $this->add_control(
            'whatsapp_message',
            [
				'label' => __( 'Message', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above. Enter this if you want to customize sent fields and remove line if field empty [field id="your_field_id"][remove_line_if_field_empty]', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
			]
        );

		$this->end_controls_section();
		// End Twilio whatsapp

        // Add Twilio SMS
        $this->start_controls_section(
            'add_twilio_sms_setting_controls',
            [
                'label' => __( 'Twilio SMS', 'pafe' ),
                'condition' => [
                    'submit_actions' => 'twilio_sms',
                ],
            ]
        );

        $this->add_control(
            'twilio_sms_to',
            [
                'label' => __( 'To', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __( 'Phone with country code, like: +14155238886', 'pafe' ),
            ]
        );

        $this->add_control(
            'twilio_sms_messaging_service_id',
            [
                'label' => __( 'Messaging ServiceS ID', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'twilio_sms_message',
            [
                'label' => __( 'Message', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '[all-fields]',
                'placeholder' => '[all-fields]',
                'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above. Enter this if you want to customize sent fields and remove line if field empty [field id="your_field_id"][remove_line_if_field_empty]', 'pafe' ),
                'label_block' => true,
                'render_type' => 'none',
            ]
        );

        $this->end_controls_section();

        // Add Sendfox
        $this->start_controls_section(
            'add_sendfox_setting_controls',
            [
                'label' => __( 'SendFox', 'pafe' ),
                'condition' => [
                    'submit_actions' => 'sendfox',
                ],
            ]
        );

        $this->add_control(
            'sendfox_list_id',
            [
                'label' => __( 'Sendfox List ID', 'pafe' ),
                'type' => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'sendfox_email_field_shortcode',
            [
                'label' => __( 'Email Field Shortcode', 'pafe' ),
               'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
            ]
        );

        $this->add_control(
            'sendfox_first_name_field_shortcode',
            [
                'label' => __( 'Frist Name Field Shortcode', 'pafe' ),
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
            ]
        );

        $this->add_control(
            'sendfox_last_name_field_shortcode',
            [
                'label' => __( 'Last Name Field Shortcode', 'pafe' ),
                'type' => \Elementor\PafeCustomControls\Select_Control::Select,
				'get_fields' => true,
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Button', 'elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
                ],
				'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
                ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .elementor-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => __( 'Padding', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_messages_style',
			[
				'label' => __( 'Messages', 'elementor-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
				'selector' => '{{WRAPPER}} .elementor-message',
			]
		);

		$this->add_control(
			'success_message_color',
			[
				'label' => __( 'Success Message Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-message-success' => 'color: {{COLOR}};',
				],
			]
		);

		$this->add_control(
			'error_message_color',
			[
				'label' => __( 'Error Message Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-message-danger' => 'color: {{COLOR}};',
				],
			]
		);

		$this->add_control(
			'inline_message_color',
			[
				'label' => __( 'Inline Message Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-help-inline' => 'color: {{COLOR}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$settings['form_id'] = !empty($settings['form_id']) ? $settings['form_id'] : '';
		$pafe_forms = get_post_type() == 'pafe-forms' ? true : false;
		$form_id = $pafe_forms ? get_the_ID() : $settings['form_id'];
        $form_id = !empty($form_id) ? $form_id : get_the_ID();
		$form_id = !empty($GLOBALS['pafe_form_id']) ? $GLOBALS['pafe_form_id'] : $form_id;

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'button', 'href', $settings['link']['url'] );
			$this->add_render_attribute( 'button', 'class', 'elementor-button-link' );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}
		}
		$submit_keyboard = !empty($settings['enter_submit_form']) ? 'true' : 'false';
		$submit_hide = !empty($settings['hide_button_after_submitting']) ? 'true' : 'false';
		$this->add_render_attribute( 'button', 'class', 'elementor-button' );
		$this->add_render_attribute( 'button', 'class', 'pafe-form-builder-button' );
		$this->add_render_attribute( 'button', 'data-pafe-submit-keyboard', $submit_keyboard );
		$this->add_render_attribute( 'button', 'data-pafe-submit-hide', $submit_hide );
		$this->add_render_attribute( 'button', 'role', 'button' );

		$this->add_render_attribute( 'button', 'data-pafe-form-builder-required-text', $settings['required_field_message'] );

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
		}

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
		}

		if (! empty( $settings['align']) ) {
			if ($settings['align'] == 'center' || $settings['align'] =='right') {
                $this->add_render_attribute( 'button', 'pafe-form-builder-submit', $settings['align'] );
			}
		}

		if ( $settings['hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		if ( $form_id ) {
            $razor_enable = !empty($settings['razorpay_enable']) ? 'yes' : 'no';
            $razor_sub_enable = !empty($settings['razorpay_sub_enable']) ? 'yes' : 'no';
			$this->add_render_attribute( 'button', 'data-pafe-form-builder-submit-form-id', $form_id );
            $this->add_render_attribute( 'button', 'data-pafe-razor-payment', $razor_enable );
            $this->add_render_attribute( 'button', 'data-pafe-razor-sub', $razor_sub_enable );
		}

		if ( !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key')) && !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key')) && !empty($settings['pafe_recaptcha_enable']) ) {
			$this->add_render_attribute( 'button', 'data-pafe-form-builder-submit-recaptcha', esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key') ) );
		}

		$list_conditional = $settings['pafe_conditional_logic_form_list'];	
		if( !empty($settings['pafe_conditional_logic_form_enable']) && !empty($list_conditional[0]['pafe_conditional_logic_form_if']) && !empty($list_conditional[0]['pafe_conditional_logic_form_comparison_operators']) ) {
			$this->add_render_attribute( 'button', [
				'data-pafe-form-builder-conditional-logic' => str_replace('\"]','', str_replace('[field id=\"','', json_encode($list_conditional))),
				'data-pafe-form-builder-conditional-logic-speed' => $settings['pafe_conditional_logic_form_speed'],
				'data-pafe-form-builder-conditional-logic-easing' => $settings['pafe_conditional_logic_form_easing'],
				'data-pafe-form-builder-conditional-logic-not-field' => '',
				'data-pafe-form-builder-conditional-logic-not-field-form-id' => $form_id,
			] );
		}

		if(in_array('update_user_profile', $settings['submit_actions'])) {
			if (is_user_logged_in()) {
				if (!empty($settings['update_user_meta_list'])) {
					$update_user_profile = array();
					$user_id = get_current_user_id();

					foreach ($settings['update_user_meta_list'] as $user_meta) {
						if (!empty($user_meta['update_user_meta']) && !empty($user_meta['update_user_meta_field_shortcode'])) {

							$user_meta_key = $user_meta['update_user_meta'];
							$user_meta_value = '';

							if ($user_meta['update_user_meta'] == 'meta' || $user_meta['update_user_meta'] == 'acf') {
								if (!empty($user_meta['update_user_meta_key'])) {
									$user_meta_key = $user_meta['update_user_meta_key'];

									if ($user_meta['update_user_meta'] == 'meta') {
										$user_meta_value = get_user_meta( $user_id, $user_meta_key, true );
									} else {
										$user_meta_value = get_field( $user_meta_key, 'user_' . $user_id );
									}
								}
							} elseif ($user_meta['update_user_meta'] == 'email') {
								$user_meta_value = get_the_author_meta( 'user_email', $user_id );
							} else {
								$user_meta_value = get_user_meta( $user_id, $user_meta_key, true );
							}

							if ( $user_meta['update_user_meta'] == 'acf' ) {
								$meta_type = $user_meta['update_user_meta_type'];

								if ($meta_type == 'image') {
									if (!empty($user_meta_value)) {
										$user_meta_value = $user_meta_value['url'];
									}
								}

								if ($meta_type == 'gallery') {
									if (is_array($user_meta_value)) {
										$images = '';
										foreach ($user_meta_value as $item) {
											if (is_array($item)) {
												if (isset($item['url'])) {
													$images .= $item['url'] . ',';
												}
											}
										}
										$user_meta_value = rtrim($images, ',');
									}
								}

                                if ( $meta_type == 'select' || $meta_type == 'checkbox' ) {
                                    if ( is_array( $user_meta_value ) ) {
                                        $value_string = '';
                                        foreach ( $user_meta_value as $item ) {
                                            $value_string .= $item . ',';
                                        }
                                        $user_meta_value = rtrim( $value_string, ',' );
                                    }

                                }
							}

							if ($user_meta_key != 'password') {
								$update_user_profile[] = array(
									'user_meta_key' => $user_meta_key,
									'user_meta_value' => $user_meta_value,
									'field_id' => $user_meta['update_user_meta_field_shortcode'],
								);
							}
						}
					}

					$this->add_render_attribute( 'button', [
						'data-pafe-form-builder-submit-update-user-profile' => str_replace('\"]','', str_replace('[field id=\"','', json_encode($update_user_profile))),
					] );
				}
			}
		}
		if(!empty($settings['mollie_enable'])){
			$this->add_render_attribute('button', [
				'data-pafe-form-builder-mollie-payment' => $form_id
			]);
			wp_enqueue_script( 'pafe-form-builder-mollie-script' );
		}
		if( !empty($settings['paypal_enable']) && isset($form_id)) {
			$this->add_render_attribute( 'button', [
				'data-pafe-form-builder-paypal-submit' => '',
				'data-pafe-form-builder-paypal-submit-enable' => '',
			] );
		}

		if( !empty($settings['pafe_stripe_enable']) ) {

			$this->add_render_attribute( 'button', [
				'data-pafe-form-builder-stripe-submit' => '',
			] );

			if( !empty($settings['pafe_stripe_amount']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-amount' => $settings['pafe_stripe_amount'],
				] );
			}

			if( !empty($settings['pafe_stripe_currency']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-currency' => $settings['pafe_stripe_currency'],
				] );
			}

			if( !empty($settings['pafe_stripe_amount_field_enable']) && !empty($settings['pafe_stripe_amount_field']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-amount-field' => $settings['pafe_stripe_amount_field'],
				] );
			}

			if( !empty($settings['pafe_stripe_customer_info_field']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-customer-info-field' => $settings['pafe_stripe_customer_info_field'],
				] );
			}
		}

		if( !empty($settings['woocommerce_add_to_cart_product_id']) ) {

			$this->add_render_attribute( 'button', [
				'data-pafe-form-builder-woocommerce-product-id' => $settings['woocommerce_add_to_cart_product_id'],
			] );
		}

		if( !empty($_GET['edit']) ) {
			$post_id = intval($_GET['edit']);
			if( is_user_logged_in() && get_post($post_id) != null ) {
				if (current_user_can( 'edit_others_posts' ) || get_current_user_id() == get_post($post_id)->post_author) {
					$sp_post_id = get_post_meta($post_id,'_submit_post_id',true);
					$sp_button_id = get_post_meta($post_id,'_submit_button_id',true);

					if (!empty($_GET['smpid'])) {
						$sp_post_id = sanitize_text_field($_GET['smpid']);
					}

					if (!empty($_GET['sm'])) {
						$sp_button_id = sanitize_text_field($_GET['sm']);
					}

					$elementor = \Elementor\Plugin::$instance;

					if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
						$meta = $elementor->documents->get( $sp_post_id )->get_elements_data();
					} else {
						$meta = $elementor->db->get_plain_editor( $sp_post_id );
					}

					$form = find_element_recursive( $meta, $sp_button_id );

					if ( !empty($form)) {
						$this->add_render_attribute( 'button', [
							'data-pafe-form-builder-submit-post-edit' => intval($post_id),
						] );

						$submit_post_id = $post_id;

						if (isset($form['settings']['submit_post_custom_fields_list'])) {

							$sp_custom_fields = $form['settings']['submit_post_custom_fields_list'];

							if (is_array($sp_custom_fields)) {
								foreach ($sp_custom_fields as $sp_custom_field) {
									if ( !empty( $sp_custom_field['submit_post_custom_field'] ) ) {
										$custom_field_value = '';
										$meta_type = !empty($sp_custom_field['submit_post_custom_field_type']) ? $sp_custom_field['submit_post_custom_field_type'] : 'text';

										if ($meta_type == 'repeater' && function_exists('update_field') && $form['settings']['submit_post_custom_field_source'] == 'acf_field') {
											$custom_field_value = get_field($sp_custom_field['submit_post_custom_field'], $submit_post_id);
											if (!empty($custom_field_value)) {
												array_walk($custom_field_value, function (& $item, $custom_field_value_key, $submit_post_id_value) {
													foreach ($item as $key => $value) {
														$field_object = get_field_object($this->acf_get_field_key( $key, $submit_post_id_value ));
														if (!empty($field_object)) {
															$field_type = $field_object['type'];
															$item_value = $value;

                                                            if ($field_type == 'repeater') {
                                                                foreach ($item_value as $item_value_key => $item_value_element) {
                                                                    foreach ($field_object['sub_fields'] as $item_sub_field) {
                                                                        foreach ($item_value_element as $item_value_element_key => $item_value_element_value) {
                                                                            if ($item_sub_field['name'] == $item_value_element_key) {
                                                                                if ($item_sub_field['type'] == 'image') {
                                                                                    if (!empty($item_value_element_value['url'])) {
                                                                                        $item_value[$item_value_key][$item_value_element_key] = $item_value_element_value['url'];
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }

															if ($field_type == 'image') {
																if (!empty($item_value['url'])) {
																	$item_value = $item_value['url'];
																}
															}

															if ($field_type == 'gallery') {
																if (is_array($item_value)) {
																	$images = '';
																	foreach ($item_value as $itemx) {
																		if (is_array($itemx)) {
																			$images .= $itemx['url'] . ',';
																		}
																	}
																	$item_value = rtrim($images, ',');
																}
															}

															if ($field_type == 'select' || $field_type == 'checkbox') {
																if (is_array($item_value)) {
																	$value_string = '';
																	foreach ($item_value as $itemx) {
																		$value_string .= $itemx . ',';
																	}
																	$item_value = rtrim($value_string, ',');
																}
															}

															if ($field_type == 'date_picker') {
																$time = strtotime( $item_value );
																$item_value = date(get_option( 'date_format' ),$time);
															}

															$item[$key] = $item_value;
														}
													}
												}, $_GET['edit']);

												?>
													<div data-pafe-form-builder-repeater-value data-pafe-form-builder-repeater-value-id="<?php echo $sp_custom_field['submit_post_custom_field']; ?>" data-pafe-form-builder-repeater-value-form-id="<?php echo $form_id; ?>" style="display: none;">
														<?php echo json_encode($custom_field_value); ?>
													</div>
												<?php
											}
										}

										if ($meta_type == 'jet_engine_repeater' && $form['settings']['submit_post_custom_field_source'] == 'jet_engine_field') {
                                            $custom_field_value = get_post_meta($submit_post_id, $sp_custom_field['submit_post_custom_field'], true);
                                            if (!empty($custom_field_value)) {
                                                foreach ($custom_field_value as $item_key => $custom_field_item) {
                                                    foreach ($custom_field_item as $key => $value) {
                                                        $field_object = $this->jetengine_repeater_get_field_object( $key, $sp_custom_field['submit_post_custom_field'] );
                                                        if (!empty($field_object)) {
                                                            $field_type = $field_object['type'];
                                                            $item_value = $value;

                                                            if ($field_type == 'media') {
                                                                $image = get_the_guid($value);
                                                                if (!empty($image)) {
                                                                    $item_value = $image;
                                                                }
                                                            }

                                                            if ($field_type == 'gallery') {
                                                                $images_array = explode(',', $item_value);
                                                                if (is_array($images_array)) {
                                                                    $images = '';
                                                                    foreach ($images_array as $images_item) {
                                                                        if (!empty($images_item)) {
                                                                            $images .= get_the_guid($images_item) . ',';
                                                                        }
                                                                    }
                                                                    if (!empty($images)) {
                                                                        $item_value = rtrim($images, ',');
                                                                    }
                                                                }
                                                            }

                                                            if ($field_type == 'checkbox') {
                                                                if (is_array($item_value)) {
                                                                    $value_string = '';
                                                                    foreach ($item_value as $itemx => $itemx_value) {
                                                                        if ($itemx_value == 'true') {
                                                                            $value_string .= $itemx . ',';
                                                                        }
                                                                    }
                                                                    $item_value = rtrim($value_string, ',');
                                                                }
                                                            }

                                                            if ($field_type == 'date') {
                                                                $time = strtotime( $item_value );
                                                                if (empty($item_value)) {
                                                                    $item_value = '';
                                                                } else {
                                                                    $item_value = date('Y-m-d',$time);
                                                                }
                                                            }

                                                            if ($field_type == 'time') {
                                                                $time = strtotime( $item_value );
                                                                $item_value = date('H:i',$time);
                                                            }

                                                            $custom_field_item[$key] = $item_value;
                                                        }
                                                    }

                                                    if ( is_string($item_key) ) {
                                                        unset($custom_field_value[$item_key]);
                                                        $custom_field_value[] = $custom_field_item;
                                                    } else { $custom_field_value[$item_key] = $custom_field_item; }
                                                }

                                                ?>
                                                <div data-pafe-form-builder-repeater-value data-pafe-form-builder-repeater-value-id="<?php echo $sp_custom_field['submit_post_custom_field']; ?>" data-pafe-form-builder-repeater-value-form-id="<?php echo $form_id; ?>" style="display: none;">
                                                    <?php echo json_encode($custom_field_value); ?>
                                                </div>
                                                <?php
                                            }
                                        }

                                        if ($meta_type == 'meta_box_group' && function_exists('rwmb_get_value') && $form['settings']['submit_post_custom_field_source'] == 'metabox_field') {
                                            $custom_field_value = rwmb_get_value($sp_custom_field['submit_post_custom_field'], array(), $submit_post_id );

                                            $custom_field_group_id = $sp_custom_field['submit_post_custom_field_group_id'];
                                            $agrs = array(
                                                'name' => $custom_field_group_id,
                                                'post_type' => 'meta-box',
                                            );

                                            $custom_field_post_id = get_posts($agrs)[0]->ID;
                                            $custom_field_objects = get_post_meta($custom_field_post_id, 'meta_box');

                                            if (!empty($custom_field_value)) {
                                                array_walk($custom_field_value, function (& $item, $custom_field_value_key, $custom_field_object_value) {
                                                    foreach ($item as $key => $value) {
                                                        $field_object = $this->metabox_group_get_field_object( $key, $custom_field_object_value );
                                                        if (!empty($field_object)) {
                                                            $field_type = $field_object['type'];
                                                            $item_value = $value;

                                                            if ( ($field_type == 'group') && ($field_object['clone']) ) {
                                                                foreach ($item_value as $item_value_key => $item_value_element ) {
                                                                    foreach ($field_object['fields'] as $fields_items) {
                                                                        foreach ($item_value_element as $item_value_element_key => $item_value_element_value) {
                                                                            if ( $fields_items['id'] == $item_value_element_key ) {
                                                                                if ($fields_items['type'] == 'single_image') {
                                                                                    $image = wp_get_attachment_url($item_value_element_value);
                                                                                    if ( !empty( $image ) ) {
                                                                                        $item_value[$item_value_key][$item_value_element_key] = $image;
                                                                                    }
                                                                                }

                                                                                if ( $fields_items['type'] == 'image' ) {
                                                                                    if ( is_array( $item_value_element_value ) ) {
                                                                                        $images = '';
                                                                                        foreach ( $item_value_element_value as $image_item ) {
                                                                                            $image = wp_get_attachment_url($image_item);
                                                                                            if ( !empty( $image ) ) {
                                                                                                $images .= $image . ',';
                                                                                            }
                                                                                        }
                                                                                        $item_value[$item_value_key][$item_value_element_key] = rtrim( $images, ',' );
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                            if ( $field_type == 'single_image' ) {
                                                                $image = wp_get_attachment_url($value);
                                                                if ( !empty( $image ) ) {
                                                                    $item_value = $image;
                                                                }
                                                            }

                                                            if ( $field_type == 'image' ) {
                                                                if ( is_array( $item_value ) ) {
                                                                    $images = '';
                                                                    foreach ( $item_value as $image_item ) {
                                                                        $image = wp_get_attachment_url($image_item);
                                                                        if ( !empty( $image ) ) {
                                                                            $images .= $image . ',';
                                                                        }
                                                                    }
                                                                    $item_value = rtrim( $images, ',' );
                                                                }
                                                            }

                                                            if ($field_type == 'select' || $field_type == 'checkbox') {
                                                                if (is_array($item_value)) {
                                                                    $value_string = '';
                                                                    foreach ($item_value as $itemx) {
                                                                        $value_string .= $itemx . ',';
                                                                    }
                                                                    $item_value = rtrim($value_string, ',');
                                                                }
                                                            }

                                                            if ($field_type == 'date') {
                                                                $time = strtotime( $item_value );
                                                                if (empty($item_value)) {
                                                                    $item_value = '';
                                                                } else {
                                                                    $item_value = date(get_option( 'date_format' ),$time);
                                                                }
                                                            }

                                                            if ($field_type == 'time') {
                                                                $time = strtotime( $item_value );
                                                                $item_value = date('H:i',$time);
                                                            }
                                                            $item[$key] = $item_value;
                                                        }
                                                    }
                                                }, $custom_field_objects);
                                                ?>
                                                <div data-pafe-form-builder-repeater-value data-pafe-form-builder-repeater-value-id="<?php echo $sp_custom_field['submit_post_custom_field']; ?>" data-pafe-form-builder-repeater-value-form-id="<?php echo $form_id; ?>" style="display: none;">
                                                    <?php echo json_encode($custom_field_value); ?>
                                                </div>
                                                <?php
                                            }
                                        }
									}
								}
							}
						}
					}
				}
			}
		}

		?>
		<input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $form_id ) {echo $form_id;} ?>"/>
		<input type="hidden" name="form_id" value="<?php echo $this->get_id(); ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $form_id ) {echo $form_id;} ?>"/>
		<input type="hidden" name="remote_ip" value="<?php echo $this->get_client_ip(); ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $form_id ) {echo $form_id;} ?>"/>

		<?php if(in_array('redirect', $settings['submit_actions'])) : ?>
			<input type="hidden" name="redirect" value="<?php echo $settings['redirect_to']; ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $form_id ) {echo $form_id;} ?>" data-pafe-form-builder-open-new-tab="<?php echo $settings['redirect_open_new_tab']; ?>"/>
		<?php endif; ?>

		<?php if(in_array('popup', $settings['submit_actions'])) : ?>
			<?php if(!empty( $settings['popup_action'] ) && !empty( $settings['popup_action_popup_id'] )) : ?>
				<a href="<?php echo $this->create_popup_url($settings['popup_action_popup_id'],$settings['popup_action']); ?>" data-pafe-form-builder-popup data-pafe-form-builder-hidden-form-id="<?php if ( $form_id ) {echo $form_id;} ?>" style="display: none;"></a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if(in_array('open_popup', $settings['submit_actions'])) : ?>
			<?php if(!empty( $settings['popup_action_popup_id_open'] )) : ?>
				<a href="<?php echo $this->create_popup_url($settings['popup_action_popup_id_open'],'open'); ?>" data-pafe-form-builder-popup-open data-pafe-form-builder-hidden-form-id="<?php if ( $form_id ) {echo $form_id;} ?>" style="display: none;"></a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if(in_array('close_popup', $settings['submit_actions'])) : ?>
			<?php if(!empty( $settings['popup_action_popup_id_close'] )) : ?>
				<a href="<?php echo $this->create_popup_url($settings['popup_action_popup_id_close'],'close'); ?>" data-pafe-form-builder-popup-close data-pafe-form-builder-hidden-form-id="<?php if ( $form_id ) {echo $form_id;} ?>" style="display: none;"></a>
			<?php endif; ?>
		<?php endif; ?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<button <?php echo $this->get_render_attribute_string( 'button' ); ?>>
				<?php $this->render_text(); ?>
			</button>
		</div>

		<?php if(in_array('submit_post', $settings['submit_actions'])) : ?>
			<?php if(\Elementor\Plugin::$instance->editor->is_edit_mode()) :
                echo '<div style="margin-top: 20px;">' . __('Edit Post URL Shortcode','pafe') . '</div><input class="elementor-form-field-shortcode" style="min-width: 300px; padding: 10px;" value="[edit_post edit_text='. "'Edit Post'" . ' sm=' . "'" . $this->get_id() . "'" . ' smpid=' . "'" . get_the_ID() . "'" .']' . get_the_permalink() . '[/edit_post]" readonly /><div class="elementor-control-field-description">' . __( 'Add this shortcode to your single template.', 'pafe' ) . ' The shortcode will be changed if you edit this form so you have to refresh Elementor Editor Page and then copy the shortcode. ' . __( 'Replace', 'pafe' ) . ' "' . get_the_permalink() . '" ' . __( 'by your Page URL contains your Submit Post Form.', 'pafe' ) . '</div>';
                echo '<div style="margin-top: 20px;">' . __('Delete Post URL Shortcode','pafe') . '</div><input class="elementor-form-field-shortcode" style="min-width: 300px; padding: 10px;" value="[delete_post force_delete='. "'0'". ' delete_text='. "'Delete Post'" . ' sm=' . "'" . $this->get_id() . "'" . ' smpid=' . "'" . get_the_ID() . "'" . ' redirect='."'http://YOUR-DOMAIN'".']'.'[/delete_post]" readonly /><div class="elementor-control-field-description">' . __( 'Add this shortcode to your single template.', 'pafe' ) . ' The shortcode will be changed if you edit this form so you have to refresh Elementor Editor Page and then copy the shortcode. ' . __( 'Replace', 'pafe' ) . ' "http://YOUR-DOMAIN" ' . __( 'by your Page URL', 'pafe' ) . '</div>';
            ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if( !empty($settings['paypal_enable']) && isset($form_id)) : ?>
			<?php
				$paypal_button_class = is_admin() ? ' pafe-paypal-admin' : '';
			?>
			<div class="pafe-form-builder-paypal">
				<!-- Set up a container element for the button -->
			    <div class="pafe-paypal-button<?php echo $paypal_button_class; ?>" id="pafe-paypal-button-container-<?php echo $this->get_id(); ?>"></div>
		    </div>

            <?php
                $paypal_sdk_src = "https://www.paypal.com/sdk/js?client-id=" . esc_attr( get_option("piotnet-addons-for-elementor-pro-paypal-client-id") );
                if(!empty($settings['paypal_currency'])){
					$paypal_sdk_src .= "&currency=" . $settings["paypal_currency"];
				}
				if(!empty($settings["paypal_locale"])) {
					$paypal_sdk_src .= "&locale=" . $settings["paypal_locale"];
                }
                if(!empty($settings['paypal_subscription_enable']) && !empty($settings['paypal_plan'])){
					$paypal_sdk_src .= '&vault=true';
				}
                $paypal_sdk_script_id = "script-pafe-paypal-" . $form_id;

                // Fix Conflict with WooCommerce PayPal Payments	
                wp_dequeue_script('ppcp-smart-button');
            ?>
		    <script id="<?php echo  $paypal_sdk_script_id ?>-js" src="<?php echo $paypal_sdk_src; ?>" data-namespace="paypal<?php echo str_replace(' ', '', $this->get_id() ); ?>"></script>
		    <script>
		    	function getFieldValue(string, replace) {
                    let ids = string.match(/\[field id="([^"]+)"\]/);
                    let id = ids[1] ? ids[1] : '';
		    		var fieldName = 'form_fields[' + id + ']',
		    			$field = jQuery(document).find('[name="' + fieldName + '"]'),
		    			fieldType = $field.attr('type'),
						formID = $field.attr('data-pafe-form-builder-form-id');

					if (fieldType == 'radio' || fieldType == 'checkbox') {
                        let fieldCheck = $field.closest('.elementor-element').find('input:checked');
                        let dataLabel = fieldCheck.attr('data-pafe-form-builder-send-data-by-label');
                        if(replace && typeof dataLabel !== 'undefined' && dataLabel !== false){
                            var fieldValue = dataLabel;
                        }else{
                            var fieldValue = fieldCheck.val();
                        }
			        } else {
			        	var fieldValue = $field.val();
			        }
                    fieldValue = string.replace('[field id="'+id+'"]', fieldValue);
			        if (fieldValue == '') {
			        	var fieldValue = 0;
			        }

			        return fieldValue;
		    	}

		    	function pafeValidateForm<?php echo $form_id; ?>() {
		    		var formID = '<?php echo $form_id; ?>',
		    			$ = jQuery,
			    		$fields = $(document).find('[data-pafe-form-builder-form-id='+ formID +']'),
			    		$submit = $(document).find('[data-pafe-form-builder-submit-form-id='+ formID +']'),
			    		requiredText = $submit.data('pafe-form-builder-required-text'),
			    		error = 0;

					var $parent = $submit.closest('.elementor-element');

					$fields.each(function(){
						if ( $(this).data('pafe-form-builder-stripe') == undefined && $(this).data('pafe-form-builder-html') == undefined ) {
							var $checkboxRequired = $(this).closest('.elementor-field-type-checkbox.elementor-field-required');
							var checked = 0;
							if ($checkboxRequired.length > 0) {
								checked = $checkboxRequired.find("input[type=checkbox]:checked").length;
							}

							if ($(this).attr('oninvalid') != undefined) {
								requiredText = $(this).attr('oninvalid').replace("this.setCustomValidity('","").replace("')","");
							}

                            var isValid = $(this)[0].checkValidity();
                            var next_ele = $($(this)[0]).next()[0];
                            if ($(next_ele).hasClass('flatpickr-mobile')) {
                                isValid = next_ele.checkValidity();
                            }

							if ( !isValid && $(this).closest('.elementor-widget').css('display') != 'none' && $(this).closest('[data-pafe-form-builder-conditional-logic]').css('display') != 'none' && $(this).data('pafe-form-builder-honeypot') == undefined &&  $(this).closest('[data-pafe-signature]').length == 0 || checked == 0 && $checkboxRequired.length > 0 && $(this).closest('.elementor-element').css('display') != 'none') {
								if ($(this).css('display') == 'none' || $(this).closest('div').css('display') == 'none' || $(this).data('pafe-form-builder-image-select') != undefined || $checkboxRequired.length > 0) {
									$(this).closest('.elementor-field-group').find('[data-pafe-form-builder-required]').html(requiredText);
								} else {
									if ($(this).data('pafe-form-builder-image-select') == undefined) {
										$(this)[0].reportValidity();
									}
								}

								error++;
							} else {

								$(this).closest('.elementor-field-group').find('[data-pafe-form-builder-required]').html('');

								if ($(this).closest('[data-pafe-signature]').length > 0) {
									var $pafeSingature = $(this).closest('[data-pafe-signature]'),
										$exportButton = $pafeSingature.find('[data-pafe-signature-export]');

									$exportButton.trigger('click');

									if ($(this).val() == '' && $(this).closest('.elementor-widget').css('display') != 'none' && $(this).attr('required') != undefined) {
										$(this).closest('.elementor-field-group').find('[data-pafe-form-builder-required]').html(requiredText);
										error++;
									}
								}
							}
						}
					});

					if (error == 0) {
						return true;
					} else {
						return false;
					}
		    	}

		        // Render the PayPal button into #paypal-button-container
                jQuery('#<?php echo $paypal_sdk_script_id ?>-js').ready(function () {
					var isFirefox = typeof InstallTrigger !== 'undefined';
                    setTimeout(function () {
                        var paypal_button_container = jQuery("#pafe-paypal-button-container-<?php echo $this->get_id(); ?>");
                        var paypalPlanID = '<?php echo $settings['paypal_plan'] ?>';
                        if (paypal_button_container.length > 0 && paypal_button_container.children().length === 0) {
                            paypal<?php echo str_replace(' ', '', $this->get_id() ); ?>.Buttons({
                                onClick :  function(data, actions){
                                    if(paypalPlanID.indexOf('[field id="') !== -1){
										paypalFieldName = paypalPlanID.replace('[field id="', '').replace('"]', '');
										paypalPlanID = jQuery('[name="form_fields['+paypalFieldName+']"][data-pafe-form-builder-form-id="<?php echo $settings['form_id'] ?>"]').val();
									}
                                    if(!pafeValidateForm<?php echo $form_id; ?>()){
										if(isFirefox){
											setTimeout(() => {
												pafeValidateForm<?php echo $form_id; ?>()
											}, 300)
										}
                                        return false;
                                    }else {
                                        return true;
                                    }
                                },

								<?php if(!empty($settings['paypal_subscription_enable']) && !empty($settings['paypal_plan'])){ ?>
									createSubscription: function(data, actions) {
										return actions.subscription.create({
										/* Creates the subscription */
										plan_id: paypalPlanID
										});
									},
									onApprove: function(data, actions) {
										var $submit = jQuery(document).find('[data-pafe-form-builder-submit-form-id="<?php echo $settings['form_id']; ?>"]'),
                                        	$parent = $submit.closest('.elementor-element');

                                        $submit.attr('data-pafe-form-builder-paypal-submit-transaction-id', data.subscriptionID);
                                        $submit.trigger('click');
                                        $parent.find('.elementor-message').removeClass('visible');
                                        $parent.find('.pafe-form-builder-alert--paypal .elementor-message-success').addClass('visible');
									},
									style: {
										label: 'subscribe'
									},
								<?php }else{ ?>
                                // Set up the transaction
                                createOrder: function(data, actions) {
                                    return actions.order.create({
                                        purchase_units: [{
                                            amount: {
                                                <?php if (strpos($settings['paypal_amount'], 'field id="') !== false) : ?>
                                                value: getFieldValue('<?php echo $settings['paypal_amount']; ?>', false),
                                                <?php else : ?>
                                                value: '<?php echo $settings['paypal_amount']; ?>',
                                                <?php endif; ?>
                                            },
                                            <?php if (strpos($settings['paypal_description'], '[field id="') !== false) : ?>
                                            description: getFieldValue('<?php echo $settings['paypal_description']; ?>', true),
                                            <?php else : ?>
                                            description: '<?php echo $settings['paypal_description']; ?>',
                                            <?php endif; ?>
                                        }]
                                    });
                                },
								// Finalize the transaction
								onApprove: function(data, actions) {
                                    return actions.order.capture().then(function(details) {
                                        // Show a success message to the buyer
                                        // alert('Transaction completed by ' + details.payer.name.given_name + '!');
                                        var paypalElement = jQuery('#pafe-paypal-button-container-<?php echo $this->get_id(); ?>');
                                        var $submit = paypalElement.closest('.elementor-widget-container').find('[data-pafe-form-builder-submit-form-id="<?php echo $form_id; ?>"]'),
                                        	$parent = $submit.closest('.elementor-element');

                                        $submit.attr('data-pafe-form-builder-paypal-submit-transaction-id', details.id);
                                        $submit.trigger('click');
                                        $parent.find('.elementor-message').removeClass('visible');
                                        $parent.find('.pafe-form-builder-alert--paypal .elementor-message-success').addClass('visible');
                                    });
                                },
                                <?php } ?>
					            onError: function (err) {
					            	var $submit = jQuery(document).find('[data-pafe-form-builder-submit-form-id="<?php echo $form_id; ?>"]'),
                            			$parent = $submit.closest('.elementor-element');

                                    $parent.find('.elementor-message').removeClass('visible');
                                    $parent.find('.pafe-form-builder-alert--paypal .elementor-message-danger').addClass('visible');
                                    $parent.find('[data-pafe-form-builder-trigger-failed]').trigger('click');
								}
                            }).render('#pafe-paypal-button-container-<?php echo $this->get_id(); ?>');
                        }
                    }, 10);
                });
		    </script>
	    <?php endif; ?>
        <?php  if(!empty($settings['razorpay_enable'])): ?>
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <div class="pafe-form-builder-alert pafe-form-builder-alert--razor">
				<div class="elementor-message elementor-message-success" role="alert"><?php echo $settings['pafe_razorpay_message_succeeded']; ?></div>
				<div class="elementor-message elementor-help-inline" role="alert"><?php echo $settings['pafe_razorpay_message_failed']; ?></div>
			</div>
        <?php endif; ?>
        <?php  if(!empty($settings['razorpay_sub_enable'])): ?>
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <div class="pafe-form-builder-alert pafe-form-builder-alert--razor">
				<div class="elementor-message elementor-message-success" role="alert"><?php echo $settings['pafe_razorpay_sub_message_succeeded']; ?></div>
				<div class="elementor-message elementor-help-inline" role="alert"><?php echo $settings['pafe_razorpay_sub_message_failed']; ?></div>
			</div>
        <?php endif; ?>
		<?php if(!empty($settings['mollie_enable'])): ?>
			<div class="pafe-form-builder-alert pafe-form-builder-alert--mollie">
				<div class="elementor-message elementor-message-success" role="alert"><?php echo $settings['pafe_mollie_message_succeeded']; ?></div>
				<div class="elementor-message elementor-message-danger" role="alert"><?php echo $settings['pafe_mollie_message_pending']; ?></div>
				<div class="elementor-message elementor-help-inline" role="alert"><?php echo $settings['pafe_mollie_message_failed']; ?></div>
			</div>
		<?php endif; ?>
		<?php if( !empty($settings['pafe_stripe_enable']) ) : ?>
			<div class="pafe-form-builder-alert pafe-form-builder-alert--stripe">
				<div class="elementor-message elementor-message-success" role="alert"><?php echo $settings['pafe_stripe_message_succeeded']; ?></div>
				<div class="elementor-message elementor-message-danger" role="alert"><?php echo $settings['pafe_stripe_message_failed']; ?></div>
				<div class="elementor-message elementor-help-inline" role="alert"><?php echo $settings['pafe_stripe_message_pending']; ?></div>
			</div>
		<?php endif; ?>
		<?php if(!empty($settings['paypal_enable'])): ?>
			<div class="pafe-form-builder-alert pafe-form-builder-alert--paypal">
				<div class="elementor-message elementor-message-success" role="alert"><?php echo !empty($settings['pafe_paypal_message_succeeded']) ? $settings['pafe_paypal_message_succeeded'] : 'Payment Success'; ?></div>
				<div class="elementor-message elementor-message-danger" role="alert"><?php echo !empty($settings['pafe_paypal_message_failed']) ? $settings['pafe_paypal_message_failed'] : 'Payment Failed'; ?></div>
			</div>
		<?php endif; ?>

        <?php if( !empty($settings['pafe_limit_form_enable']) ) : ?>
            <div class="pafe-form-builder-alert pafe-form-builder-alert--limit-entries">
                <div class="elementor-message elementor-message-success" role="alert"><?php echo $settings['pafe_limit_entries_custom_message']; ?></div>
            </div>
        <?php endif; ?>

        <?php if ( !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key')) && !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key')) && !empty($settings['pafe_recaptcha_enable']) ) : ?>
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr(get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key')); ?>"></script>
		<?php if (!empty($settings['pafe_recaptcha_hide_badge'])) : ?>
			<style type="text/css">
				.grecaptcha-badge {
					opacity:0 !important;
					visibility: collapse !important;
				}
			</style>
		<?php endif; ?>
		<?php endif; ?>
		<div id="pafe-form-builder-trigger-success-<?php if ( $form_id ) {echo $form_id;} ?>" data-pafe-form-builder-trigger-success="<?php if ( $form_id ) {echo $form_id;} ?>" style="display: none"></div>
		<div id="pafe-form-builder-trigger-failed-<?php if ( $form_id ) {echo $form_id;} ?>" data-pafe-form-builder-trigger-failed="<?php if ( $form_id ) {echo $form_id;} ?>" style="display: none"></div>
		<div class="pafe-form-builder-alert pafe-form-builder-alert--mail">
			<div class="elementor-message elementor-message-success" role="alert" data-pafe-form-builder-message="<?php echo $settings['success_message']; ?>"><?php echo $settings['success_message']; ?></div>
			<div class="elementor-message elementor-message-danger" role="alert" data-pafe-form-builder-message="<?php echo $settings['error_message']; ?>"><?php echo $settings['error_message']; ?></div>
			<!-- <div class="elementor-message elementor-help-inline" role="alert">Server error. Form not sent.</div> -->
		</div>
		<?php if (in_array("pdfgenerator", $settings['submit_actions'])): ?>
		<?php if($settings['pdfgenerator_background_image_enable'] == 'yes'){
			if(isset($settings['pdfgenerator_background_image']['url'])){
				$pdf_generator_image = $settings['pdfgenerator_background_image']['url'];
			}
		} ?>
		<?php if($settings['pdfgenerator_import_template'] == 'yes' && !empty($settings['pdfgenerator_template_url'])): ?>
		<?php if(is_admin()): ?>
		<div class="pafe-button-load-pdf-template" style="text-align:center">
			<button data-pafe-load-pdf-template="<?php echo $settings['pdfgenerator_template_url']; ?>">Load PDF Template</button>
		</div>
		<?php endif; ?>
		<?php endif; ?>
		<div id="pafe-pdf-preview" class="pafe-form-builder-pdf-generator-preview<?php if(empty($settings['pdfgenerator_set_custom'])) { echo ' pafe-form-builder-pdf-generator-preview--not-custom'; } ?> <?php echo $settings['pdfgenerator_size'] ?>" style="border: 1px solid #000; margin: 0 auto; position: relative; <?php if(isset($pdf_generator_image)) {echo "background-image:url('".$pdf_generator_image."'); background-size: contain; background-position: left top; background-repeat: no-repeat;"; } ?>">
		<?php if($settings['pdfgenerator_set_custom'] == 'yes' && $settings['pdfgenerator_import_template'] == 'yes' && !empty($settings['pdfgenerator_template_url'])): ?>
		<canvas id="pafe-pdf-preview-template"></canvas>
		<?php endif; ?>
		<?php if(!empty($settings['pdfgenerator_title'])): ?>
		<div class="pafe-form-builder-pdf-generator-preview__title" style="margin-top: 20px; margin-left: 20px;"><?php echo $settings['pdfgenerator_title'] ?></div>
		<?php endif; ?>
			<?php if($settings['pdfgenerator_set_custom'] == 'yes'){ ?>
			<?php if(is_admin() && $settings['pdfgenerator_import_template'] == 'yes' && !empty($settings['pdfgenerator_template_url'])){ ?>
				<script src="<?php echo plugin_dir_url( __FILE__ ).'../assets/js/minify/pdf.min.js' ?>"></script>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						var pdfTemplatePreview = $('[data-pafe-load-pdf-template]').attr('data-pafe-load-pdf-template');
						if(pdfTemplatePreview != ''){
							previewPDFTemplate(pdfTemplatePreview);
						} 
						$(document).on('click', '[data-pafe-load-pdf-template]', function(){
							var pdfTemplate = $(this).attr('data-pafe-load-pdf-template');
							if(pdfTemplate){
								previewPDFTemplate(pdfTemplate);
							}
						});
						function previewPDFTemplate(url){
							var pdfjsLib = window['pdfjs-dist/build/pdf'];

							pdfjsLib.GlobalWorkerOptions.workerSrc = '<?php echo plugin_dir_url( __FILE__ ).'../assets/js/minify/pdf.worker.min.js' ?>';
							var loadingTask = pdfjsLib.getDocument(url);
							loadingTask.promise.then(function(pdf) {
							
							var pageNumber = 1;
							pdf.getPage(pageNumber).then(function(page) {
								
								var scale = 1.32;
								var viewport = page.getViewport({scale: scale});

								var canvas = document.getElementById('pafe-pdf-preview-template');
								var context = canvas.getContext('2d');
								context.clearRect(0, 0, 1122, 793);
								canvas.height = 1122;//viewport.height;
								canvas.width = 793;//viewport.width;

								var renderContext = {
								canvasContext: context,
								viewport: viewport
								};
								var renderTask = page.render(renderContext);
								renderTask.promise.then(function () {
									console.log('Page rendered');
								});
							});
							}, function (reason) {
							// PDF loading error
								console.error(reason);
							});
						}
					});
				</script>
			<?php } ?>
			<?php foreach($settings['pdfgenerator_field_mapping_list'] as $item): ?>
				<?php if($item['pdfgenerator_field_type'] == 'default'){ ?>
				<?php
					$pdf_font_weight = !empty($item['font_weight'])	? $item['font_weight'] : '';
				?>
					<?php if($item['auto_position'] == 'yes'){ ?>
						<div class="pafe-form-builder-pdf-generator-preview__item <?php echo $pdf_font_weight; ?> elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>" style="background: #dedede;line-height: 1; margin-left:15px;margin-top:15px;">
							<?php echo $item['pdfgenerator_field_shortcode']; ?>
						</div>
					<?php }else{ ?>
						<div class="pafe-form-builder-pdf-generator-preview__item <?php echo $pdf_font_weight; ?> elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>" style="position: absolute; background: #dedede;line-height: 1;">
							<?php echo $item['pdfgenerator_field_shortcode']; ?>
						</div>
					<?php } ?>
				<?php }elseif($item['pdfgenerator_field_type'] == 'image'){ ?>
				<div class="pafe-form-builder-pdf-generator-preview__item-image  elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
					<img src="<?php echo plugins_url().'/piotnet-addons-for-elementor-pro/assets/images/signature.png'; ?>" style="position: absolute;">
					<?php //echo 'Type image in form'; ?>
				</div>
				<?php }else{ ?>
				<?php
					$pdf_image_preview_url = !empty($item['pdfgenerator_image_field']['url']) ? $item['pdfgenerator_image_field']['url'] : plugins_url().'/piotnet-addons-for-elementor-pro/assets/images/signature.png';
				?>
				<div class="pafe-form-builder-pdf-generator-preview__item-image  elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
					<img src="<?php echo $pdf_image_preview_url; ?>" style="position: absolute;">
				</div>
			<?php } endforeach; }else{ ?>
			<div class="pafe-form-builder-field-mapping__preview">
				<?php if($settings['pdfgenerator_heading_field_mapping_show_label'] == 'yes'){ 
					echo "Label: Your Field Value";
				}else{
					echo 'Your Field Value';
				} ?>
			</div>
			<?php } ?>
		</div>
		<?php endif; ?>
		<?php

	}

	public function create_popup_url($id,$action) {
    	if($action == 'open' || $action == 'toggle') {
    		if ( version_compare( ELEMENTOR_PRO_VERSION, '2.9.0', '<' ) ) {
				$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:open', [
					'id' => $id,
					'toggle' => 'toggle' === $action,
				] );
			} else {
				$link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash( 'popup:open', [
					'id' => $id,
					'toggle' => 'toggle' === $action,
				] );
			}
    	} else {
    		if ( version_compare( ELEMENTOR_PRO_VERSION, '2.9.0', '<' ) ) {
				$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:close' );
			} else {
				$link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash( 'popup:close' );
			}
    	}
    	
		return $link_action_url;
    }

	protected function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	/**
	 * Render button text.
	 *
	 * Render button widget text.
	 *
	 * @since 1.5.0
	 * @access protected
	 */
	protected function render_text() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'content-wrapper' => [
				'class' => 'elementor-button-content-wrapper',
			],
			'icon-align' => [
				'class' => [
					'elementor-button-icon',
					'elementor-align-icon-' . $settings['icon_align'],
				],
			],
			'text' => [
				'class' => 'elementor-button-text',
			],
		] );

		$this->add_inline_editing_attributes( 'text', 'none' );
		?>
		<span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
			<span class="elementor-button-text elementor-form-spinner"><i class="fa fa-spinner fa-spin"></i></span>
			<?php if ( ! empty( $settings['icon'] ) ) : ?>
			<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
				<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
			</span>
			<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo $settings['text']; ?></span>
		</span>
		<?php
	}

	public function mailpoet_get_list(){
		$data = [];
		if (class_exists(\MailPoet\API\API::class)) {
			$mailpoet_api = \MailPoet\API\API::MP('v1');
			$lists = $mailpoet_api->getLists();
			foreach($lists as $item){
				$data[$item['id']] = $item['name'];
			}
		}
		return $data;
	}

	protected function create_list_exist($repeater) {
		$settings = $this->get_settings_for_display();

		// $repeater_terms = $repeater->get_controls();

		// if (!empty($settings['submit_post_term_slug']) && empty($repeater_terms)) {
		// 	$repeater_terms[0] = $settings['submit_post_term_slug'];
		// 	$repeater_terms[1] = $settings['submit_post_term'];
		// }

		return $settings;
	}

	public function add_wpml_support() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
	}

	public function wpml_widgets_to_translate_filter( $widgets ) {
		$widgets[ $this->get_name() ] = [
			'conditions' => [ 'widgetType' => $this->get_name() ],
			'fields'     => [
				[
					'field'       => 'text',
					'type'        => __( 'Button Text', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_to',
					'type'        => __( 'Email To', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_subject',
					'type'        => __( 'Email Subject', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_content',
					'type'        => __( 'Email Content', 'pafe' ),
					'editor_type' => 'AREA'
				],
				[
					'field'       => 'email_from',
					'type'        => __( 'Email From', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_from_name',
					'type'        => __( 'Email From Name', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_reply_to',
					'type'        => __( 'Email Reply To', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_to_cc',
					'type'        => __( 'Cc', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_to_bcc',
					'type'        => __( 'Bcc', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_to_2',
					'type'        => __( 'Email To 2', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_subject_2',
					'type'        => __( 'Email Subject 2', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_content_2',
					'type'        => __( 'Email Content 2', 'pafe' ),
					'editor_type' => 'AREA'
				],
				[
					'field'       => 'email_from_2',
					'type'        => __( 'Email From 2', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_from_name_2',
					'type'        => __( 'Email From Name 2', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_reply_to_2',
					'type'        => __( 'Email Reply To 2', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_to_cc_2',
					'type'        => __( 'Cc 2', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'email_to_bcc_2',
					'type'        => __( 'Bcc 2', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'success_message',
					'type'        => __( 'Success Message', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'error_message',
					'type'        => __( 'Error Message', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'required_field_message',
					'type'        => __( 'Required Message', 'pafe' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'invalid_message',
					'type'        => __( 'Invalid Message', 'pafe' ),
					'editor_type' => 'LINE'
				],
			],
		];

		return $widgets;
	}
	public function pafe_get_pdf_fonts(){
		$pdf_fonts = [];
		$pdf_fonts['default'] = 'Default';
		$pdf_fonts['Courier'] = 'Courier';
		$pdf_fonts['Helvetica'] = 'Helvetica';
		$pdf_fonts['Times'] = 'Times';
		$fonts = get_posts(array( 
			'post_type' => 'pafe-fonts',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		));

		foreach($fonts as $key => $font){
			$font_key = get_post_meta($font->ID, '_pafe_pdf_font', true);
			$font_key = substr($font_key, strpos($font_key, 'uploads/')+8);
			$pdf_fonts[$font_key] = $font->post_title;
		}
		return $pdf_fonts;
	}
	public function pafe_get_pdf_fonts_style(){
		$pdf_fonts_style = [];
		$pdf_fonts_style['N'] = 'Normal';
		$pdf_fonts_style['I'] = 'Italic';
		$pdf_fonts_style['B'] = 'Bold';
		$pdf_fonts_style['BI'] = 'Bold Italic';
		$fonts = get_posts(array( 
			'post_type' => 'pafe-fonts',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		));
		foreach($fonts as $key => $font){
			$font_key = get_post_meta($font->ID, '_pafe_pdf_font', true);
			$font_key = substr($font_key, strpos($font_key, 'uploads/')+8);
			$pdf_fonts_style[$font_key] = $font->post_title;
		}
		return $pdf_fonts_style;
	}
}

?>