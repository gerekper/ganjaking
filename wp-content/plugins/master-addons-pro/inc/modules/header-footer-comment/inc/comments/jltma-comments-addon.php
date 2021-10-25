<?php 
	namespace MasterHeaderFooter\Inc\Comments\Addon;

	// Elementor Classes
	use \Elementor\Widget_Base;
	use \Elementor\Utils;
	use \Elementor\Icons_Manager;
	use \Elementor\Controls_Manager;
	use \Elementor\Repeater;
	use \Elementor\Switcher;
	use \Elementor\Group_Control_Border;
	use \Elementor\Group_Control_Typography;
	use \Elementor\Scheme_Typography;
	use \Elementor\Group_Control_Image_Size;
    use \Elementor\Group_Control_Background;    
	use \Elementor\Group_Control_Box_Shadow;
	use \Elementor\Group_Control_Text_Shadow;
	use \Elementor\Group_Control_Css_Filter;
	use \Elementor\Scheme_Color;

	
	use MasterHeaderFooter\Inc\Comments\JLTMA_Comments_Builder;
	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 22/06/2020
	 */

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) { exit; }

	/**
	 * Master Comments: Master Addons Element
	 */
    
    class Master_Addons_Comments extends Widget_Base {

		public function get_name() {
			return 'jltma-comments';
		}    

		public function get_title() {
			return esc_html__( 'MA Comments', JLTMA_TD );
		}

		public function get_categories() {
			return [ 'general', 'master-addons' ];
		}

		public function get_icon() {
			return 'ma-el-icon eicon-comments';
		}

		public function get_keywords() {
			return [ 
					'comments', 
					'comments', 
					'disquss', 
					'facebook', 
					'jetpack', 
					'discussion'
				];
		}


	    public function get_script_depends() {
	        return [ 
	        	'jltma-comments',
	        	'google-recaptcha' 
	        ];
	    }


	    public function get_style_depends() {
	        return [ 
	        	'font-awesome-5-all',
				'font-awesome-4-shim',
	        	// 'dashicons', 
	        	'jltma-comments'
	        ];
		}


        public function get_custom_help_url(){
            return 'https://master-addons.com/demos/comments-builder';
        }
		

		protected function _register_controls() {
	        
	        /*
			 * MA Comments Section
			 */
			$this->start_controls_section(
				'jltma_comment_section_start',
				[
					'label' => esc_html__( 'Comments', JLTMA_TD ),
				]
	        );


			$this->add_control(
				'jltma_comment_style_preset',
				[
					'label' 		=> esc_html__( 'Design Presets', JLTMA_TD ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'style_one',
					'options' 		=> [
						'style_one'          	=> esc_html__( 'Modern Style', JLTMA_TD ),
						'style_two'    			=> esc_html__( 'Lounge Style', JLTMA_TD ),
						'style_three'   		=> esc_html__( 'Facebook Style', JLTMA_TD )
					],
					'style_transfer' => true,
				]
			);


	        $this->add_control(
	            'jltma_comment_total_number',
	            [
					'label' 		    => esc_html__( 'Total Number of Comments', JLTMA_TD ),
					'type'              => Controls_Manager::SWITCHER,
	                'default'           => 'show',
	                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
	                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
	                'return_value'      => 'show',
	                'style_transfer'    => true,
	            ]
			);

	        $this->add_control(
	            'jltma_comment_no_comment_text',
	            [
					'label' 		    => esc_html__( 'No Comments Text', JLTMA_TD ),
	                'type'              => Controls_Manager::TEXT,
                    'default'           => esc_html__( 'No Comments', JLTMA_TD ),
	                'style_transfer'    => true,
                    'condition'             => [
                        'jltma_comment_total_number'       => 'show'
                    ],                    

	            ]
			);

	        $this->add_control(
	            'jltma_comment_single_comment_text',
	            [
					'label' 		    => esc_html__( 'One Comment Text', JLTMA_TD ),
	                'type'              => Controls_Manager::TEXT,
                    'default'           => esc_html__( 'One Comment', JLTMA_TD ),
	                'style_transfer'    => true,
                    'condition'             => [
                        'jltma_comment_total_number'       => 'show'
                    ],                    

	            ]
			);
	        $this->add_control(
	            'jltma_comment_plural_comment_text',
	            [
					'label' 		    => esc_html__( 'Multiple Comments Text', JLTMA_TD ),
	                'type'              => Controls_Manager::TEXT,
                    'default'           => esc_html__( 'Comments', JLTMA_TD ),
	                'style_transfer'    => true,
                    'condition'             => [
                        'jltma_comment_total_number'       => 'show'
                    ],                    

	            ]
			);

	        $this->add_control(
	            'jltma_comment_gravatar',
	            [
					'label' 		    => esc_html__( 'Show Gravatar', JLTMA_TD ),
					'type'              => Controls_Manager::SWITCHER,
	                'default'           => 'show',
	                'label_on'          => esc_html__( 'Hide', JLTMA_TD ),
	                'label_off'         => esc_html__( 'Show', JLTMA_TD ),
	                'return_value'      => 'show',
	                'style_transfer'    => true,
	            ]
			);


	        $this->add_control(
	            'jltma_comment_replies',
	            [
					'label' 		    => esc_html__( 'Show / Hide Replies', JLTMA_TD ),
					'type'              => Controls_Manager::SWITCHER,
	                'default'           => 'show',
	                'label_on'          => esc_html__( 'Hide', JLTMA_TD ),
	                'label_off'         => esc_html__( 'Show', JLTMA_TD ),
	                'return_value'      => 'show',
	                'style_transfer'    => true,
	            ]
			);


	        $this->add_control(
	            'jltma_comment_ratings',
	            [
					'label' 		    => esc_html__( 'Comment Rating', JLTMA_TD ),
					'type'              => Controls_Manager::SWITCHER,
	                'default'           => 'show',
	                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
	                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
	                'return_value'      => 'show',
	                'style_transfer'    => true,
	            ]
			);

	        $this->add_control(
	            'jltma_comment_pagination',
	            [
					'label' 		    => esc_html__( 'Enable Pagination', JLTMA_TD ),
					'type'              => Controls_Manager::SWITCHER,
	                'default'           => 'yes',
	                'label_on'          => esc_html__( 'Yes', JLTMA_TD ),
	                'label_off'         => esc_html__( 'No', JLTMA_TD ),
	                'return_value'      => 'yes',
	                'style_transfer'    => true,
	            ]
			);

	        $this->add_control(
	            'jltma_comment_spam_protection_enable',
	            [
					'label' 		    => esc_html__( 'Enable reCaptcha', JLTMA_TD ),
					'type'              => Controls_Manager::SWITCHER,
	                'default'           => 'no',
	                'label_on'          => esc_html__( 'Enable', JLTMA_TD ),
	                'label_off'         => esc_html__( 'Disable', JLTMA_TD ),
	                'return_value'      => 'yes',
	                'style_transfer'    => true,
	            ]
			);
	        $this->add_control(
	            'jltma_comment_extra_fields_enable',
	            [
					'label' 		    => esc_html__( 'Enable Extra Comment Fields', JLTMA_TD ),
					'type'              => Controls_Manager::SWITCHER,
	                'default'           => 'no',
	                'label_on'          => esc_html__( 'Enable', JLTMA_TD ),
	                'label_off'         => esc_html__( 'Disable', JLTMA_TD ),
	                'return_value'      => 'yes',
	                'style_transfer'    => true,
	            ]
			);

            $this->add_responsive_control(
                'jltma_comment_pagination_items',
                [
                    'label'                 => esc_html__('Number Of Comments Per Page', JLTMA_TD),
                    'type'                  => Controls_Manager::SLIDER,
                    'default'               => [ 'size' => 10 ],
                    'range'                 => [
                        'px' => [
                            'min'   => 1,
                            'max'   => 100,
                            'step'  => 1,
                        ],
                    ],
                    'size_units'            => '',
                    'condition'             => [
                        'jltma_comment_pagination'       => 'yes'
                    ],                    
                ]
            );
	        $this->end_controls_section();




	        /**
             * Content Tab: Display Settings
             */
            $this->start_controls_section(
                'jltma_comment_section_customization',
                [
                    'label'                 => esc_html__( 'Customization Settings', JLTMA_TD ),
                ]
            );


            $this->add_control(
                'jltma_comment_before_notes',
                [
                    'label'                 => esc_html__( 'Before Notes Text', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXTAREA,
                    'placeholder'           => esc_html__( 'Your email address will not be published.', JLTMA_TD ),
                ]
            );

            $this->add_control(
                'jltma_comment_after_notes',
                [
                    'label'                 => esc_html__( 'After Notes Text', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXTAREA,
                    'placeholder'           => esc_html__( 'Your email address will not be published.', JLTMA_TD )
                ]
            );

            $this->add_control(
                'jltma_comment_label',
                [
                    'label'                 => esc_html__( 'Comment Label', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXT,
                    'default'               => esc_html__( 'Leave A Comment', JLTMA_TD ),
                ]
            );

            $this->add_control(
                'jltma_comment_reply_label',
                [
                    'label'                 => esc_html__( 'Reply Button Label', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXT,
                    'default'               => esc_html__( 'Reply', JLTMA_TD ),
                    'condition'				=> [
						'jltma_comment_replies' => 'show',
					]
                    
                ]
            );

            $this->add_control(
                'jltma_comment_show_reply_label',
                [
                    'label'                 => esc_html__( 'Show Reply Label', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXT,
                    'placeholder'           => esc_html__( 'Show Replies', JLTMA_TD ),
                    'condition'				=> [
						'jltma_comment_replies' => 'show',
					]
                    
                ]
            );
            $this->add_control(
                'jltma_comment_hide_reply_label',
                [
                    'label'                 => esc_html__( 'Hide Reply Label', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXT,
                    'placeholder'           => esc_html__( 'Hide Replies', JLTMA_TD ),
                    'condition'				=> [
						'jltma_comment_replies' => 'show',
					]
                    
                ]
            );

            $this->add_control(
                'jltma_comment_cancel_reply_label',
                [
                    'label'                 => esc_html__( 'Cancel Reply Label', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXT,
                    'default'               => esc_html__( 'Cancel Reply', JLTMA_TD )
                ]
            );

            $this->add_control(
                'jltma_comment_form_submit_label',
                [
                    'label'                 => esc_html__( 'Comment Form Submit Label', JLTMA_TD ),
                    'type'                  => Controls_Manager::TEXT,
                    'default'               => esc_html__( 'Post Comment', JLTMA_TD )
                ]
            );


	        $this->end_controls_section();




	        /**
             * Content Tab: SPAM Protection
             */
            $this->start_controls_section(
                'jltma_comment_section_spam_protection',
                [
                    'label'                 => esc_html__( 'SPAM Protection', JLTMA_TD ),
                    'condition'				=> [
						'jltma_comment_spam_protection_enable' => 'yes',
					]
                ]
            );

			
		        $this->add_control(
		            'jltma_comment_spam_protection',
		            [
						'label' 		    => esc_html__( 'Enable SPAM Protection', JLTMA_TD ),
						'type'              => Controls_Manager::SWITCHER,
		                'default'           => 'yes',
		                'label_on'          => esc_html__( 'Enable', JLTMA_TD ),
		                'label_off'         => esc_html__( 'Disable', JLTMA_TD ),
		                'return_value'      => 'yes',
		                'style_transfer'    => true,
		            ]
				);

		        $this->add_control(
		            'jtlma_comment_spam_protection_help',
		            [
		                'type'              => Controls_Manager::RAW_HTML,
		                'raw'               => sprintf( __('First configure API Settings <a href="%1$s">click here</a>', MELA_TD), admin_url('admin.php?page=master-addons-settings#ma_api_keys')),
		                'content_classes'   => 'elementor-descriptor',
		            ]
		        );

		   


	        $this->end_controls_section();




	        /**
             * Content Tab: Fields Settins
             */
            $this->start_controls_section(
                'jltma_comment_section_fields',
                [
                    'label'                 => esc_html__( 'Fields Settings', JLTMA_TD ),
                ]
            );

			
				$this->start_controls_tabs( 'jltma_comment_section_fields_tab' );
				$this->start_controls_tab( 'jltma_comment_fields_name_tab', [ 
					'label' => esc_html__( 'Name', JLTMA_TD ) 
				] );


		        $this->add_control(
		            'jltma_comment_fields_name_label_display',
		            [
						'label' 		    => esc_html__( 'Display Label?', JLTMA_TD ),
						'type'              => Controls_Manager::SWITCHER,
		                'default'           => 'show',
		                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
		                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
		                'return_value'      => 'show',
		                'style_transfer'    => true,
		            ]
				);

	            $this->add_control(
	                'jltma_comment_fields_name_label',
	                [
	                    'label'                 => esc_html__( 'Name', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Name', JLTMA_TD ),
	                    'condition'				=> [
							'jltma_comment_fields_name_label_display' => 'show',
						]
	                ]
	            );

	            $this->add_control(
	                'jltma_comment_fields_name_label_placeholder',
	                [
	                    'label'                 => esc_html__( 'Placeholder', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Enter your Name', JLTMA_TD )
	                ]
	            );
				$this->end_controls_tab();


				//Email
				$this->start_controls_tab( 'jltma_comment_fields_email_tab', [ 
					'label' => esc_html__( 'Email', JLTMA_TD ) 
				] );

		        $this->add_control(
		            'jltma_comment_fields_email_label_display',
		            [
						'label' 		    => esc_html__( 'Display Label?', JLTMA_TD ),
						'type'              => Controls_Manager::SWITCHER,
		                'default'           => 'show',
		                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
		                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
		                'return_value'      => 'show',
		                'style_transfer'    => true,
		            ]
				);


	            $this->add_control(
	                'jltma_comment_fields_email_label',
	                [
	                    'label'                 => esc_html__( 'Email', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Email', JLTMA_TD ),
	                    'condition'				=> [
							'jltma_comment_fields_email_label_display' => 'show',
						]
	                ]
	            );


	            $this->add_control(
	                'jltma_comment_fields_email_label_placeholder',
	                [
	                    'label'                 => esc_html__( 'Placeholder', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Enter your Email', JLTMA_TD )
	                ]
	            );

				$this->end_controls_tab();



				//Website
				$this->start_controls_tab( 'jltma_comment_fields_url_tab', [ 
					'label' => esc_html__( 'Website', JLTMA_TD ) 
				] );



		        $this->add_control(
		            'jltma_comment_fields_url_display',
		            [
						'label' 		    => esc_html__( 'Display Field?', JLTMA_TD ),
						'type'              => Controls_Manager::SWITCHER,
		                'default'           => 'show',
		                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
		                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
		                'return_value'      => 'show',
		                'style_transfer'    => true,
		            ]
				);

		        $this->add_control(
		            'jltma_comment_fields_url_label_display',
		            [
						'label' 		    => esc_html__( 'Display Label?', JLTMA_TD ),
						'type'              => Controls_Manager::SWITCHER,
		                'default'           => 'show',
		                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
		                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
		                'return_value'      => 'show',
		                'style_transfer'    => true,
	                    'condition'				=> [
							'jltma_comment_fields_url_display' => 'show',
						]
		            ]
				);

	            $this->add_control(
	                'jltma_comment_fields_url_label',
	                [
	                    'label'                 => esc_html__( 'Website', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Website', JLTMA_TD ),
	                    'condition'				=> [
							'jltma_comment_fields_url_label_display' => 'show',
							'jltma_comment_fields_url_display' => 'show',
						]
	                ]
	            );


	            $this->add_control(
	                'jltma_comment_fields_url_label_placeholder',
	                [
	                    'label'                 => esc_html__( 'Placeholder', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Enter your Website', JLTMA_TD ),
	                    'condition'				=> [
							'jltma_comment_fields_url_display' => 'show',
						]
	                ]
	            );

				$this->end_controls_tab();


				//Comment Box
				$this->start_controls_tab( 'jltma_comment_fields_textarea_tab', [ 
					'label' => esc_html__( 'Comment', JLTMA_TD ) 
				] );

		        $this->add_control(
		            'jltma_comment_fields_textarea_label_display',
		            [
						'label' 		    => esc_html__( 'Display Label?', JLTMA_TD ),
						'type'              => Controls_Manager::SWITCHER,
		                'default'           => 'show',
		                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
		                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
		                'return_value'      => 'show',
		                'style_transfer'    => true,
		            ]
				);


	            $this->add_control(
	                'jltma_comment_fields_textarea_label',
	                [
	                    'label'                 => esc_html__( 'Comment Box', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Comments', JLTMA_TD ),
	                    'condition'				=> [
							'jltma_comment_fields_textarea_label_display' => 'show',
						]
	                ]
	            );

	            $this->add_control(
	                'jltma_comment_fields_textarea_label_placeholder',
	                [
	                    'label'                 => esc_html__( 'Placeholder', JLTMA_TD ),
	                    'type'                  => Controls_Manager::TEXT,
	                    'default'               => esc_html__( 'Enter your Comments', JLTMA_TD )
	                ]
	            );



		        $this->add_control(
		            'jltma_comment_fields_textarea_notice',
		            [
						'label' 		    => esc_html__( 'Display Notice?', JLTMA_TD ),
						'type'              => Controls_Manager::SWITCHER,
		                'default'           => 'hide',
		                'label_on'          => esc_html__( 'Show', JLTMA_TD ),
		                'label_off'         => esc_html__( 'Hide', JLTMA_TD ),
		                'return_value'      => 'show',
		                'style_transfer'    => true,
		            ]
				);


		        $this->add_control(
					'jltma_comment_fields_textarea_notice_content',
					[
						'label'                 => esc_html__( 'Content', JLTMA_TD ),
						'type'                  => Controls_Manager::WYSIWYG,
						'default'               => esc_html__( 'Comments are moderated and will only be made live if they add to the discussion in a constructive way. If you disagree with a point, be polite. This should be a conversation between professional people with the aim that we all learn.', JLTMA_TD ),
						'condition'             => [
							'jltma_comment_fields_textarea_notice'	=> 'show',
						],
					]
				);

				$this->add_control(
		            'jltma_comment_fields_textarea_notice_align',
		            [
		                'label' 		=> esc_html__( 'Alignment', JLTMA_TD ),
		                'type' 			=> Controls_Manager::CHOOSE,
		                'label_block' 	=> false,
		                'options' 		=> [
		                    'left' 	=> [
									'title' 	=> esc_html__( 'Left', JLTMA_TD ),
									'icon' 		=> 'eicon-h-align-left',
		                    ],
		                    'right' 		=> [
		                        'title' 		=> esc_html__( 'Right', JLTMA_TD ),
		                        'icon' 			=> 'eicon-h-align-right',
		                    ],
		                ],
		                'default' 		 => 'right',
		                'style_transfer' => true,
		                'condition'             => [
							'jltma_comment_fields_textarea_notice'	=> 'show',
						],
		            ]
		        );


				$this->end_controls_tab();

				$this->end_controls_tabs();

	        $this->end_controls_section();




	        /**
             * Extra Comments Fields: Dynamic Custom Fields Settings
             */
            $this->start_controls_section(
                'jltma_comment_extra_fields_sections',
                [
                    'label'                 => esc_html__( 'Extra Comment Fields', JLTMA_TD ),
                    'condition'				=> [
						'jltma_comment_extra_fields_enable' => 'yes',
					]
                ]
            );

            			

				$this->add_control(
					'jltma_comment_extra_fields_items',
					[
						'label'       => esc_html__( 'Comment Fields', JLTMA_TD ),
						'type'        => Controls_Manager::REPEATER,
						'seperator'         => 'before',
						'default'     => [

							[
								'label_name'                            => esc_html__( 'Cup of Tea', JLTMA_TD ),
								'field_type'   							=> 'text',
								'placeholder'                 			=> esc_html__( 'Living', JLTMA_TD ),
								'error_msg'            					=> esc_html__( '', JLTMA_TD ),
								'required'   							=> '',
								// 'multi_checkbox_options'   					=> '',
								'display_label'   						=> 'show'
							],
							[
								'label_name'                            => esc_html__( 'Enter Age', JLTMA_TD ),
								'field_type'   							=> 'text',
								'placeholder'                 			=> esc_html__( 'Enter Your Age', JLTMA_TD ),
								'error_msg'            					=> esc_html__( '', JLTMA_TD ),
								'required'   							=> '',
								// 'multi_checkbox_options'   					=> '',
								'display_label'   						=> 'show'
							]

						],
						'fields'          => [			
							[
								'name'           => 'display_label',
								'label'        	 => esc_html__( 'Display Label?', JLTMA_TD ),
								'type'         	 => Controls_Manager::SWITCHER,
								'default'      	 => 'show',
								'label_on'       => esc_html__( 'Show', JLTMA_TD ),
		                		'label_off'      => esc_html__( 'Hide', JLTMA_TD ),
								'return_value' 	 => 'show',
								'style_transfer' => true,
							],
							[
								'type'          => Controls_Manager::TEXT,
								'name'          => 'label_name',
								'label'         => esc_html__( 'Label Name', JLTMA_TD ),
								'label_block'   => true,
								'default'       => esc_html__( 'Age', JLTMA_TD ),
			                    'condition'     => [
			                        'display_label'       => 'show'
			                    ]
							],

							[
								'name'          => 'required',
								'label'        => esc_html__( 'Required Field?', JLTMA_TD ),
								'type'         => Controls_Manager::SWITCHER,
								'default'      => 'no',
								'return_value' => 'yes'
							],
							[
								'name'          => 'field_type',
								'label'         => esc_html__( 'Field Type', JLTMA_TD ),
								'type'          => Controls_Manager::SELECT,
								'options'   => [
									'text'           => esc_html__( 'Text', JLTMA_TD ),
									'textarea'       => esc_html__( 'Textarea', JLTMA_TD ),
									'checkbox'       => esc_html__( 'Checkbox', JLTMA_TD )
								],
								'default'   => 'text'
							],
							// [
							// 	'type'          => Controls_Manager::TEXTAREA,
							// 	'name'          => 'multi_checkbox_options',
							// 	'label'         => esc_html__( 'Multi Checkbox Field', JLTMA_TD ),
							// 	'label_block'   => true,
							// 	'description'   => esc_html__( 'One Options per line', MELA_TD ),
			    //                 'condition'     => [
			    //                     'field_type'       => 'checkbox'
			    //                 ]							
							// ],
							[
								'type'          => Controls_Manager::TEXT,
								'name'          => 'placeholder',
								'label'         => esc_html__( 'Placeholder Text', JLTMA_TD ),
								'label_block'   => true,
								'default'       => esc_html__( 'Enter Age', JLTMA_TD ),
			                    'condition'     => [
			                        'field_type!'       => 'checkbox'
			                    ]									
							],
							[
								'type'          => Controls_Manager::TEXT,
								'name'          => 'error_msg',
								'label'         => esc_html__( 'Error Message', JLTMA_TD ),
								'label_block'   => true,
								'default'       => esc_html__( 'Age Error', JLTMA_TD )
							],
						],
						'title_field' => '{{label_name}}'
					]
				);


			


			$this->end_controls_section();






	        /* Master Comment: Style Tab */
	        $this->start_controls_section('jltma_comments_style_start',
				[
					'label' 		=> esc_html__( 'Comment Box', JLTMA_TD ),
					'tab' 			=> Controls_Manager::TAB_STYLE
				]
			);

			$this->add_control('jltma_comments_bg_color',
				[
					'label' 		=> esc_html__( 'Background Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'default'       => '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comments-wrap' => 'background: {{VALUE}};'
					]
				]
			);


	        $this->add_control(
	            'jltma_comments_total',
	            [
	                'label' => esc_html__( 'Total Number Comments', JLTMA_TD ),
	                'type' => Controls_Manager::HEADING,
	                'separator' => 'before',
	            ]
	        );
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comments_total_typography',
					'selector' => '{{WRAPPER}} .jltma-comments-title',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1
				]
			);

			$this->add_control('jltma_comments_total_color',
				[
					'label' => esc_html__( 'Total Comments Color', JLTMA_TD ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_3
					],
					'default'   => '#000',
					'selectors' => [
						'{{WRAPPER}} .jltma-comments-title' => 'color: {{VALUE}};'
					],
				]
			);


			$this->add_responsive_control(
				'jltma_comments_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-comments-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comments-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_border',
					'selector'      => '{{WRAPPER}} .jltma-comments-wrap'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comments-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comments-wrap'
				]
			);

			$this->end_controls_section();



	        /* Master Comment Item: Style Tab */
	        $this->start_controls_section('jltma_comment_style_start',
				[
					'label' 		=> esc_html__( 'Comment Item', JLTMA_TD ),
					'tab' 			=> Controls_Manager::TAB_STYLE
				]
			);

			$this->add_control('jltma_comment_bg_color',
				[
					'label' 		=> esc_html__( 'Background Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'default'       => '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-listing-wrapper .jltma-comment-list' => 'background: {{VALUE}};'
					]
				]
			);


			// Comment Item Styles
			$this->start_controls_tabs( 'jltma_comment_author_section' );

			$this->start_controls_tab( 'jltma_comment_author_tab_style', [ 
				'label' => esc_html__( 'Author', JLTMA_TD ) 
			] );
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comment_author_name_typography',
					'label' => esc_html__( 'Author Name Typography', JLTMA_TD ),
					'selector' => '{{WRAPPER}} .jltma-comment-listing-wrapper .jltma-comment-list .jltma-author-name',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1
				]
			);

			$this->add_control('jltma_comment_author_name_color',
				[
					'label' => esc_html__( 'Author Name Color', JLTMA_TD ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_3
					],
					'default'   => '#46494c',
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-listing-wrapper .jltma-comment-list .jltma-author-name' => 'color: {{VALUE}};'
					],
				]
			);


			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comment_author_name_text_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comment-listing-wrapper .jltma-comment-list .jltma-author-name'
				]
			);


			$this->end_controls_tab();

			// Gravatar
			$this->start_controls_tab( 'jltma_comment_gravatar_tab_style', [ 
				'label' => esc_html__( 'Gravatar', JLTMA_TD ) 
			] );


            $this->add_responsive_control(
                'jltma_comment_gravatar_size',
                [
                    'label'                 => esc_html__('Size', JLTMA_TD),
                    'type'                  => Controls_Manager::SLIDER,
                    'default'               => [ 'size' => 100 ],
                    'range'                 => [
                        'px' => [
                            'min'   => 1,
                            'max'   => 350,
                            'step'  => 1,
                        ],
                    ],
                    'size_units'            => '',
                    'condition'             => [
                        'jltma_comment_gravatar'       => 'show'
                    ],
                    'selectors'          => [
                    	'{{WRAPPER}} .jltma-comment-list .jltma-comment-gravatar' => "width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};"
                    ]
                ]
            );


			$this->add_responsive_control(
				'jltma_comment_gravatar_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-list .jltma-comment-gravatar img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comment_gravatar_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-list .jltma-comment-gravatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comment_gravatar_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-list .jltma-comment-gravatar img'
				]
			);

			$this->add_responsive_control(
				'jltma_comment_gravatar_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-list .jltma-comment-gravatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comment_gravatar_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comment-list .jltma-comment-gravatar img'
				]
			);

			$this->end_controls_tab();


			// Comment Text
			$this->start_controls_tab( 'jltma_comment_author_commentstyle', [ 
				'label' => esc_html__( 'Comments', JLTMA_TD ) 
			] );

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comment_author_comment_typography',
					'selector' => '{{WRAPPER}} .jltma-comment-listing-wrapper .jltma-comment-list .jltma-comment',
					'scheme' => Scheme_Typography::TYPOGRAPHY_3
				]
			);

			$this->add_control('jltma_comment_author_comment_color',
				[
					'label' => esc_html__( 'Total Comments Color', JLTMA_TD ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_3
					],
					'default'   => '#5a5e63',
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-listing-wrapper .jltma-comment-list .jltma-comment' => 'color: {{VALUE}};'
					],
				]
			);


			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comment_author_comment_text_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comment-listing-wrapper .jltma-comment-list .jltma-comment'
				]
			);

			$this->end_controls_tab();
			
			$this->end_controls_tabs();


	        $this->add_control(
	            'jltma_comments_time_settings',
	            [
	                'label' => esc_html__( 'Comment Datetime Settings', JLTMA_TD ),
	                'type' => Controls_Manager::HEADING,
	                'separator' => 'before',
	            ]
	        );

            $this->add_control(
                'jltma_comments_time_type',
                array(
                    'label'       => esc_html__('Type of Datetime', JLTMA_TD),
                    'type'        => Controls_Manager::SELECT,
                    'default'     => 'custom',
                    'options'     => array(
                       'custom'    => esc_html__('Custom'   , JLTMA_TD ),
                       'mysql'     => esc_html__('MySql' , JLTMA_TD ),
                       'timestamp' => esc_html__('TimeStamp'    , JLTMA_TD )
                    )
                )
            );
    
            $this->add_control(
                'jltma_comments_time_format',
                array(
                    'label'        => esc_html__('Datetime Format String', JLTMA_TD ),
                    'type'         => Controls_Manager::TEXT,
                    'default'      => get_option( 'date_format' ),
                    'description' => '<span class="pro-feature"> <a href="' . esc_url_raw('https://wordpress.org/support/article/formatting-date-and-time/') . '" target="_blank">Date Time Format Examples </a> </span>',
                    'condition'    => array(
                        'jltma_comments_time_type' => array('custom'),
                    )
                )
            );


			$this->add_control('jltma_comments_time_color',
				[
					'label' => esc_html__( 'Datetime Color', JLTMA_TD ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_3
					],
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .jltma-title-date .jltma-date-time' => 'color: {{VALUE}};'
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comments_time_typography',
					'separator' => 'after',
					'selector' => '{{WRAPPER}} .jltma-title-date .jltma-date-time',
					'scheme' => Scheme_Typography::TYPOGRAPHY_3
				]
			);



	        $this->add_control(
	            'jltma_comments_like_dislike_settings',
	            [
	                'label' 		=> esc_html__( 'Like/Dislike Settings', JLTMA_TD ),
	                'type' 			=> Controls_Manager::HEADING,
	                'separator' 	=> 'before',
                    'condition'     => [
                        'jltma_comment_ratings'       => 'show'
                    ],	                
	            ]
	        );

			$this->add_control('jltma_comments_like_dislike_icon_color',
				[
					'label' 		=> esc_html__( 'Like/Dislike Icon Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '#393c3f',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-like-dislike-wrapper .fa' => 'color: {{VALUE}};'
					],
                    'condition'     => [
                        'jltma_comment_ratings'       => 'show'
                    ]					
				]
			);


			$this->add_control('jltma_comments_like_dislike_number_color',
				[
					'label' 		=> esc_html__( 'Like/Dislike Number Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-like-dislike-wrapper .jltma-like-count-wrap,
						{{WRAPPER}} .jltma-like-dislike-wrapper .jltma-dislike-count-wrap' => 'color: {{VALUE}};'
					],
                    'condition'     => [
                        'jltma_comment_ratings'       => 'show'
                    ]					
				]
			);




			$this->add_responsive_control(
				'jltma_comment_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-comments-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comment_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comments-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_comment_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-listing-wrapper > ul.jltma-comment-list'
				]
			);


			$this->add_responsive_control(
				'jltma_comment_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comments-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comment_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comments-wrap'
				]
			);

			$this->end_controls_section();






			// Reply Settings
			$this->start_controls_section(
				'jltma_comments_reply_section_start',
				[
					'label' => esc_html__( 'Reply Settings', JLTMA_TD ),
					'tab' 			=> Controls_Manager::TAB_STYLE
				]
	        );

	        $this->add_control(
	            'jltma_comments_child_reply_settings',
	            [
	                'label' 		=> esc_html__( 'Children Replies Settings', JLTMA_TD ),
	                'type' 			=> Controls_Manager::HEADING,
	                'separator' 	=> 'before' 
	            ]
	        );


			$this->add_control('jltma_comments_replies_bg_color',
				[
					'label' 		=> esc_html__( 'Replies Background', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-children.jltma-comment-list' => 'background: {{VALUE}} !important;'
					]					
				]
			);



			$this->add_responsive_control(
				'jltma_comments_replies_pading',
				[
					'label'         => esc_html__( 'Replies Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-children.jltma-comment-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_replies_margin',
				[
					'label'         => esc_html__( 'Replies Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'default' => [
						'top' => 0,
						'right' => 0,
						'bottom' => 40,
						'left' => 40,
						'isLinked' => true,
					],					
					'selectors'     => [
						'{{WRAPPER}} .jltma-children.jltma-comment-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_replies_border',
					'selector'      => '{{WRAPPER}} .jltma-children.jltma-comment-list'
				]
			);


			$this->add_responsive_control(
				'jltma_comments_replies_border_radius',
				[
					'label' => esc_html__( 'Repies Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-children.jltma-comment-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Repies Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_replies_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-children.jltma-comment-list'
				]
			);





	        $this->add_control(
	            'jltma_comments_reply_settings',
	            [
	                'label' 		=> esc_html__( 'Reply Button Settings', JLTMA_TD ),
	                'type' 			=> Controls_Manager::HEADING,
	                'separator' 	=> 'before' 
	            ]
	        );


			$this->start_controls_tabs( 'jltma_comments_reply_tabs' );
			
			$this->start_controls_tab( 'jltma_comments_reply_tab', [ 
				'label' => esc_html__( 'Normal', JLTMA_TD ) 
			] );

			$this->add_control('jltma_comments_reply_btn_bg_color',
				[
					'label' 		=> esc_html__( 'Reply BG Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-footer .jltma-reply-button,
						{{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger' => 'background: {{VALUE}};'
					]					
				]
			);

			$this->add_control('jltma_comments_reply_text_color',
				[
					'label' 		=> esc_html__( 'Reply Text Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '#78909c',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-footer .jltma-reply-button,
						{{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger' => 'color: {{VALUE}};'
					],				
				]
			);


			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comments_reply_typography',
					'label' => esc_html__( 'Reply Typography', JLTMA_TD ),
					'selector' => '{{WRAPPER}} .jltma-comment-footer .jltma-reply-button, {{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_reply_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-footer .jltma-reply-button, {{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_reply_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-footer .jltma-reply-button, 
						{{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_reply_shadow',
					'selector'      	=> '{{WRAPPER}} .jltma-comment-footer .jltma-reply-button, {{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger'
				]
			);

			$this->end_controls_tab();


			//Hover
			$this->start_controls_tab( 'jltma_comments_reply_hover_tabs', [ 
				'label' => esc_html__( 'Hover', JLTMA_TD ) 
			] );
			$this->add_control('jltma_comments_reply_hover_btn_bg_color',
				[
					'label' 		=> esc_html__( 'Reply BG Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-footer .jltma-reply-button:hover,
						{{WRAPPER}} .jltma-comment-style_one .jltma-comment-footer a.jltma-show-replies-trigger:hover' => 'background: {{VALUE}};'
					]					
				]
			);

			$this->add_control('jltma_comments_reply_hover_text_color',
				[
					'label' 		=> esc_html__( 'Reply Text Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '#fff',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-footer .jltma-reply-button:hover,
						{{WRAPPER}} .jltma-comment-style_one .jltma-comment-footer a.jltma-show-replies-trigger:hover' => 'color: {{VALUE}};'
					],				
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comments_reply_hover_typography',
					'label' => esc_html__( 'Reply Typography', JLTMA_TD ),
					'selector' => '{{WRAPPER}} .jltma-comment-footer .jltma-reply-button:hover, {{WRAPPER}} .jltma-comment-style_one .jltma-comment-footer a.jltma-show-replies-trigger:hover',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_reply_hover_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-footer .jltma-reply-button:hover, {{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger:hover'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_reply_hover_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-footer .jltma-reply-button:hover, 
						{{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_reply_hover_shadow',
					'selector'      => '{{WRAPPER}} .jltma-comment-footer .jltma-reply-button:hover, {{WRAPPER}} .jltma-comment-footer a.jltma-show-replies-trigger:hover'
				]
			);

			$this->end_controls_tab();
			$this->end_controls_tabs();


	        $this->add_control(
	            'jltma_comments_show_reply_heading',
	            [
	                'label' => esc_html__( 'Reply/Show Replies Button', JLTMA_TD ),
	                'type' => Controls_Manager::HEADING,
	                'separator' => 'before',
	            ]
	        );

			$this->add_responsive_control(
				'jltma_comments_reply_show_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-style_one .jltma-body .jltma-edit-comments-wrapper, 
						{{WRAPPER}} .jltma-comment-style_one .jltma-body .jltma-reply-button, 
						{{WRAPPER}} .jltma-comment-style_two .jltma-body .jltma-reply-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->end_controls_section();







			// Comment Form
			$this->start_controls_section(
				'jltma_comments_form_start',
				[
					'label' => esc_html__( 'Comment Form', JLTMA_TD ),
					'tab' 			=> Controls_Manager::TAB_STYLE
				]
	        );


			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'jltma_comments_form_background',
					'label'     => esc_html__( 'Background', JLTMA_TD ),
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .jltma-comments-wrap .comment-respond',
				]
			);


			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comments_form_typography',
					'selector' => '{{WRAPPER}} .jltma-comments-wrap .comment-respond',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1
				]
			);


			$this->add_control(
				'jltma_comments_form_width',
				[
					'label' => esc_html__( 'Form Width', JLTMA_TD ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', '%' ],
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 1500,
						],
						'em' => [
							'min' => 1,
							'max' => 80,
						],
					],
					'default'   => [
						'unit'  => '%',
						'size'  => '100'
					],
					'selectors' => [
						'{{WRAPPER}} .jltma-comments-wrap .comment-respond' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);








			$this->add_responsive_control(
				'jltma_comments_form_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-comments-wrap .comment-respond' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comments-wrap .comment-respond' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_form_border',
					'selector'      => '{{WRAPPER}} .jltma-comments-wrap .comment-respond'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comments-wrap .comment-respond' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_form_box_shadow',
					'separator' 		=> 'after',
					'selector'          => '{{WRAPPER}} .jltma-comments-wrap .comment-respond'
				]
			);



			$this->start_controls_tabs( 'jltma_comments_form_section_fields_tab' );
			$this->start_controls_tab( 'jltma_comments_form_fields_name_tab', [ 
				'label' => esc_html__( 'Name', JLTMA_TD ),
			] );

			$this->add_control(
				'jltma_comments_form_fields_name_width',
				[
					'label' => esc_html__( 'Field Width', JLTMA_TD ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', '%' ],
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 1500,
						],
						'em' => [
							'min' => 1,
							'max' => 80,
						],
					],
					'default'   => [
						'unit'  => '%',
						'size'  => '100'
					],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-name' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);


			$this->add_responsive_control(
				'jltma_comments_form_fields_name_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_name_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_form_fields_name_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-form .jltma-name #author'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_name_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-name #author' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_form_fields_name_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comment-form .jltma-name  #author'
				]
			);

			$this->end_controls_tab();


			//Email
			$this->start_controls_tab( 'jltma_comments_form_fields_email_tabs', [ 
				'label' => esc_html__( 'Email', JLTMA_TD ) 
			] );

			$this->add_control(
				'jltma_comments_form_fields_email_width',
				[
					'label' => esc_html__( 'Field Width', JLTMA_TD ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', '%' ],
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 1500,
						],
						'em' => [
							'min' => 1,
							'max' => 80,
						],
					],
					'default'   => [
						'unit'  => '%',
						'size'  => '100'
					],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-email' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);


			$this->add_responsive_control(
				'jltma_comments_form_fields_email_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-email' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_email_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-email' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_form_fields_email_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-form .jltma-email #email'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_email_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-email #email' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_form_fields_email_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comment-form .jltma-email  #email'
				]
			);

			$this->end_controls_tab();



			//Website
			$this->start_controls_tab( 'jltma_comments_form_fields_url_tab', [ 
				'label' => esc_html__( 'Website', JLTMA_TD ) 
			] );

			$this->add_control(
				'jltma_comments_form_fields_url_width',
				[
					'label' => esc_html__( 'Field Width', JLTMA_TD ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', '%' ],
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 1500,
						],
						'em' => [
							'min' => 1,
							'max' => 80,
						],
					],
					'default'   => [
						'unit'  => '%',
						'size'  => '100'
					],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-url' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);


			$this->add_responsive_control(
				'jltma_comments_form_fields_url_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-url' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_url_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-url' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_form_fields_url_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-form .jltma-url #url'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_url_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-url #url' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_form_fields_url_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comment-form .jltma-url  #url'
				]
			);

			$this->end_controls_tab();


			//Comment Box
			$this->start_controls_tab( 'jltma_comments_form_fields_textarea_tab', [ 
				'label' => esc_html__( 'Comment', JLTMA_TD ) 
			] );

			$this->add_control(
				'jltma_comments_form_fields_textarea_width',
				[
					'label' => esc_html__( 'Field Width', JLTMA_TD ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', '%' ],
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 1500,
						],
						'em' => [
							'min' => 1,
							'max' => 80,
						],
					],
					'default'   => [
						'unit'  => '%',
						'size'  => '100'
					],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-comment' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);


			$this->add_responsive_control(
				'jltma_comments_form_fields_textarea_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-comment' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_textarea_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-form .jltma-comment' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_form_fields_textarea_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-form .jltma-comment #comment'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_form_fields_textarea_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-form .jltma-comment #comment' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_form_fields_textarea_box_shadow',
					'selector'          => '{{WRAPPER}} .jltma-comment-form .jltma-comment  #comment'
				]
			);
			$this->end_controls_tab();

			$this->end_controls_tabs();


			$this->end_controls_section();





			// Comment Submit
			$this->start_controls_section(
				'jltma_comments_submit_start',
				[
					'label' => esc_html__( 'Comment Submit Button', JLTMA_TD ),
					'tab' 			=> Controls_Manager::TAB_STYLE
				]
	        );

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'jltma_comments_submit_background',
					'label'     => esc_html__( 'Background', JLTMA_TD ),
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .jltma-comment-style_one .jltma-form-submit input.jltma-comment-form-submit',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'jltma_comments_submit_typography',
					'selector' => '{{WRAPPER}} .jltma-comment-style_one .jltma-form-submit input.jltma-comment-form-submit',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1
				]
			);


			$this->add_responsive_control(
				'jltma_comments_submit_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-style_one .jltma-form-submit input.jltma-comment-form-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comments_submit_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-style_one .jltma-form-submit input.jltma-comment-form-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_submit_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-style_one .jltma-form-submit input.jltma-comment-form-submit'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_submit_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-style_one .jltma-form-submit input.jltma-comment-form-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_submit_box_shadow',
					'separator' 		=> 'after',
					'selector'          => '{{WRAPPER}} .jltma-comment-style_one .jltma-form-submit input.jltma-comment-form-submit'
				]
			);
			$this->end_controls_section();





	        /*
			 * MA Comments Pagination
			 */
			$this->start_controls_section(
				'jltma_comment_pagination_section_start',
				[
					'label' => esc_html__( 'Pagination', JLTMA_TD ),
					'tab' 			=> Controls_Manager::TAB_STYLE,
                    'condition'             => [
                        'jltma_comment_pagination'       => 'yes'
                    ],					
				]
	        );


			$this->add_responsive_control(
				'jltma_comment_pagination_pading',
				[
					'label'         => esc_html__( 'Padding', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'separator' => 'before',
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->add_responsive_control(
				'jltma_comment_pagination_margin',
				[
					'label'         => esc_html__( 'Margin', JLTMA_TD ),
					'type'          => Controls_Manager::DIMENSIONS,
					'size_units'    => [ 'px', 'em', '%' ],
					'selectors'     => [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);

			$this->start_controls_tabs( 'jltma_comments_pagination_tabs' );
			
			$this->start_controls_tab( 'jltma_comments_pagination_tab', [ 
				'label' => esc_html__( 'Normal', JLTMA_TD ) 
			] );

			$this->add_control('jltma_comments_pagination_btn_bg_color',
				[
					'label' 		=> esc_html__( 'BG Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a' => 'background: {{VALUE}};'
					]					
				]
			);

			$this->add_control('jltma_comments_pagination_text_color',
				[
					'label' 		=> esc_html__( 'Text Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a' => 'color: {{VALUE}};'
					],				
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_pagination_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_pagination_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_pagination_shadow',
					'selector'      	=> '{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a'
				]
			);

			$this->end_controls_tab();


			//Hover
			$this->start_controls_tab( 'jltma_comments_pagination_hover_tabs', [ 
				'label' => esc_html__( 'Hover', JLTMA_TD ) 
			] );


			$this->add_control('jltma_comments_pagination_hover_btn_bg_color',
				[
					'label' 		=> esc_html__( 'BG Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a:hover' => 'background: {{VALUE}};'
					]					
				]
			);

			$this->add_control('jltma_comments_pagination_hover_text_color',
				[
					'label' 		=> esc_html__( 'Text Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a:hover' => 'color: {{VALUE}};'
					],				
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_pagination_hover_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a:hover'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_pagination_hover_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_pagination_hover_shadow',
					'selector'      	=> '{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a:hover'
				]
			);

			$this->end_controls_tab();


			//Active
			$this->start_controls_tab( 'jltma_comments_pagination_active_tabs', [ 
				'label' => esc_html__( 'Active', JLTMA_TD ) 
			] );


			$this->add_control('jltma_comments_pagination_active_btn_bg_color',
				[
					'label' 		=> esc_html__( 'BG Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a.jltma-current-page' => 'background: {{VALUE}};'
					]					
				]
			);

			$this->add_control('jltma_comments_pagination_active_text_color',
				[
					'label' 		=> esc_html__( 'Text Color', JLTMA_TD ),
					'type' 			=> Controls_Manager::COLOR,
					'scheme' 		=> [
						'type' 		=> Scheme_Color::get_type(),
						'value' 	=> Scheme_Color::COLOR_3
					],
					'default'   	=> '',
					'selectors' 	=> [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a.jltma-current-page' => 'color: {{VALUE}};'
					],				
				]
			);


			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'          => 'jltma_comments_pagination_active_border',
					'selector'      => '{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a.jltma-current-page'
				]
			);

			$this->add_responsive_control(
				'jltma_comments_pagination_active_border_radius',
				[
					'label' => esc_html__( 'Border Radius', JLTMA_TD ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units'    => ['px', '%' ,'em'],
					'selectors' => [
						'{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a.jltma-current-page' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
					]
				]
			);


			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label'             => esc_html__('Box Shadow',JLTMA_TD),
					'name'              => 'jltma_comments_pagination_active_shadow',
					'selector'      	=> '{{WRAPPER}} .jltma-comment-pagination-wrapper.jltma-page-number li a.jltma-current-page'
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();




	    }




        public function jltma_comment_form($args = array(), $post_id = null) {

            if (null === $post_id)
                $post_id = get_the_ID();

            // Exit the function when comments for the post are closed.
            if (!comments_open($post_id)) {
                do_action('comment_form_comments_closed');
                return;
            }

            $commenter 		= wp_get_current_commenter();
            $user 			= wp_get_current_user();
            $user_identity 	= $user->exists() ? $user->display_name : '';

            $args 			= wp_parse_args($args);
            if (!isset($args['format']))
                $args['format'] = current_theme_supports('html5', 'comment-form') ? 'html5' : 'xhtml';

            $req 			= get_option('require_name_email');

            $aria_req 		= ( $req ? " aria-required='true'" : '' );
            $html_req 		= ( $req ? " required='required'" : '' );
            $html5 			= 'html5' === $args['format'];
            
            $fields = array(
                'author' => '<p class="jltma-comment-form-author">' . '<label for="author">' . esc_html__('Name') . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                	'<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245"' . $aria_req . $html_req . ' /></p>',

                'email' => '<p class="jltma-comment-form-email"><label for="email">' . esc_html__('Email') . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                	'<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>',

                'url' => '<p class="jltma-comment-form-url"><label for="url">' . esc_html__('Website') . '</label> ' .
                	'<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" /></p>',
            );

            $required_text 	= sprintf(' ' . esc_html__('Required fields are marked %s'), '<span class="required">*</span>');

            $fields 		= apply_filters('comment_form_default_fields', $fields);

            $defaults 	= array(
                'fields' 				=> $fields,
                'comment_field' 		=> '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun') . '</label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required"></textarea></p>',
                'must_log_in' 			=> '<p class="must-log-in">' . sprintf(
                    esc_html__('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url(apply_filters('the_permalink', get_permalink($post_id), $post_id))
                ) . '</p>',
                'logged_in_as' 			=> '<p class="logged-in-as">' . sprintf(
                    esc_html__('<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>'), get_edit_user_link(),
                    esc_attr(sprintf(__('Logged in as %s. Edit your profile.'), $user_identity)), $user_identity, wp_logout_url(apply_filters('the_permalink', get_permalink($post_id), $post_id))
                ) . '</p>',
                'comment_notes_before' 	=> '<p class="comment-notes"><span id="email-notes">' . esc_html__('Your email address will not be published.') . '</span>' . ( $req ? $required_text : '' ) . '</p>',
                'comment_notes_after' 	=> '',
                'action' 				=> site_url('/wp-comments-post.php'),
                'id_form' 				=> 'commentform',
                'id_submit' 			=> 'submit',
                'class_form' 			=> 'comment-form',
                'class_submit' 			=> 'submit',
                'name_submit' 			=> 'submit',
                'title_reply' 			=> esc_html__('Leave a Reply', JLTMA_TD),
                'title_reply_to' 		=> esc_html__('Leave a Reply to %s'),
                'title_reply_before' 	=> '<h3 id="reply-title" class="comment-reply-title clearfix">',
                'title_reply_after' 	=> '</h3>',
                'cancel_reply_before' 	=> ' <small>',
                'cancel_reply_after' 	=> '</small>',
                'cancel_reply_link' 	=> esc_html__('Cancel reply', JLTMA_TD),
                'label_submit' 			=> esc_html__('Post Comment', JLTMA_TD),
                'submit_button' 		=> '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
                'submit_field' 			=> '<p class="form-submit">%1$s %2$s</p>',
                'format' 				=> 'xhtml',
            );
            $args = wp_parse_args($args, apply_filters('comment_form_defaults', $defaults));

            $args = array_merge($defaults, $args);

            do_action('comment_form_before');
            ?>

            <div id="respond" class="comment-respond">
                <?php
                echo $args['title_reply_before'];

                comment_form_title($args['title_reply'], $args['title_reply_to']);

                echo $args['cancel_reply_before'];

                cancel_comment_reply_link($args['cancel_reply_link']);

                echo $args['cancel_reply_after'];

                echo $args['title_reply_after'];

                if (get_option('comment_registration') && !is_user_logged_in()) :
	                    echo $args['must_log_in'];
	                do_action('comment_form_must_log_in_after');
	            else :
                ?>
                <form action="<?php echo esc_url($args['action']); ?>" method="post" id="<?php echo esc_attr($args['id_form']); ?>" class="<?php echo esc_attr($args['class_form']); ?>"<?php echo $html5 ? ' novalidate' : ''; ?>>
                    <?php
                    do_action('comment_form_top');

                    if (is_user_logged_in()) :

                        echo apply_filters('comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity);
                        do_action('comment_form_logged_in_after', $commenter, $user_identity);

                    else :

                        echo $args['comment_notes_before'];

                    endif;

                    $comment_fields = array('comment' => $args['comment_field']) + (array) $args['fields'];

                    $comment_fields = apply_filters('comment_form_fields', $comment_fields);

                    $comment_field_keys = array_diff(array_keys($comment_fields), array('comment'));

                    $first_field = reset($comment_field_keys);
                    
                    $last_field = end($comment_field_keys);

                    foreach ($comment_fields as $name => $field) {

                        if (!is_user_logged_in()) {

                            if ($first_field === $name) {
                                do_action('comment_form_before_fields');
                            }

                            echo apply_filters("comment_form_field_{$name}", $field) . "\n";

                            if ($last_field === $name) {
                                do_action('comment_form_after_fields');
                            }

                        } elseif (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) {
            			
            				do_action('comment_form_before_fields');

                            echo apply_filters("comment_form_field_{$name}", $field) . "\n";
							
							if ($last_field === $name) {
                            	do_action('comment_form_after_fields');
                            }

                        } else {

                            if ($first_field === $name) {
                                do_action('comment_form_before_fields');
                            }
                            if ($name != 'name' && $name != 'email' && $name != 'url') {
                                echo apply_filters("comment_form_field_{$name}", $field) . "\n";
                            }

                            if ($last_field === $name) {
                                do_action('comment_form_after_fields');
                            }
                        }
                    }

                    echo $args['comment_notes_after'];

                    $submit_button = sprintf(
                        $args['submit_button'], esc_attr($args['name_submit']), esc_attr($args['id_submit']), esc_attr($args['class_submit']), esc_attr($args['label_submit'])
                    );
                    $submit_button = apply_filters('comment_form_submit_button', $submit_button, $args);

                    $submit_field = sprintf(
                        $args['submit_field'], $submit_button, get_comment_id_fields($post_id)
                    );
                    echo apply_filters('comment_form_submit_field', $submit_field, $args);
                    do_action('jltma_comment_form', $post_id);
                    ?>
                </form>
            <?php endif; ?>
        </div>


        <?php
    	}     



        public function jltma_frontend_form_build() {
        	$settings       = $this->get_settings_for_display();

            if (comments_open() ) {
                $commenter = wp_get_current_commenter();
                $user = wp_get_current_user();
                $user_identity = $user->exists() ? $user->display_name : '';
                
				$fields = '';

                
                // Name Field
			    $jltma_cf_name_required= esc_html__('(Required)',JLTMA_TD);

				$jltma_cf_name_label_container = "";
			    if ($settings['jltma_comment_fields_name_label_display'] == "show") {
			    	$jltma_cf_name_label = ($settings['jltma_comment_fields_name_label']!= '') ? esc_attr($settings['jltma_comment_fields_name_label']) :'';
			    	
			    	if($jltma_cf_name_label){
				    	$jltma_cf_name_label_container = '<div class="jltma-name-div">	
								<label>' . esc_attr( $jltma_cf_name_label ) . '</label>
							</div>';			    		
			    	}

				}

            	$jltma_cf_name_placeholder = ($settings['jltma_comment_fields_name_label_placeholder']!='') ? esc_attr($settings['jltma_comment_fields_name_label_placeholder']) : '';

            	$jltma_cf_name_style = "";

            	$jltma_cf_name_ft = (isset( $settings['jltma_field_type']) && $settings['jltma_field_type'] ) ? $settings['jltma_field_type'] : "text";

		        $fields .= '<div class="jltma-name-value-div jltma-name">
						' . $jltma_cf_name_label_container . '
						<div class="jltma-value-div">
							<input class="form-control" type="' . $jltma_cf_name_ft . '" name="author" id="author" value="" placeholder="' . $jltma_cf_name_placeholder . '" ' . $jltma_cf_name_style . '/>
						</div>
					</div>';


                // Email Field
			    $jltma_cf_email_required= esc_html__('(Required)',JLTMA_TD);
				
				$jltma_cf_email_label_container = "";
				if ($settings['jltma_comment_fields_email_label_display'] == "show") {

			    	$jltma_cf_email_label = ($settings['jltma_comment_fields_email_label']!= '') ? esc_attr($settings['jltma_comment_fields_email_label']) :'';

					if($jltma_cf_email_label){
						$jltma_cf_email_label_container = '<div class="jltma-email-div">	
							<label>' . esc_attr( $jltma_cf_email_label ) . '</label>
						</div>';
					}

				}


            	$jltma_cf_email_placeholder = ($settings['jltma_comment_fields_email_label_placeholder']!='') ? esc_attr($settings['jltma_comment_fields_email_label_placeholder']) : '';

            	$jltma_cf_email_style = "";

            	$jltma_cf_email_ft = ( isset( $settings['jltma_field_type']) && $settings['jltma_field_type']) ? $settings['jltma_field_type'] : "email";

		        $fields .= '<div class="jltma-email-value-div jltma-email">
		        	' . $jltma_cf_email_label_container . '
						<div class="jltma-value-div">
							<input class="form-control" type="' . $jltma_cf_email_ft . '" id="email" name="email" value="" placeholder="' . $jltma_cf_email_placeholder . '" ' . $jltma_cf_email_style . '/>
						</div>
					</div>';


                // URL Field
			    $jltma_cf_url_required = esc_html__('(Required)',JLTMA_TD);

			    if($settings['jltma_comment_fields_url_display'] == 'show'){
				    $jltma_cf_url_label_container = "";
					if ($settings['jltma_comment_fields_url_label_display'] == "show") {
				    	$jltma_cf_url_label = ($settings['jltma_comment_fields_url_label']!= '') ? esc_attr($settings['jltma_comment_fields_url_label']) :'';

				    	if($jltma_cf_url_label){
					    	$jltma_cf_url_label_container = '<div class="jltma-url-div">	
						        	<label>' . $jltma_cf_url_label . '</label>
						        </div>';			    		
				    	}

					}

	            	$jltma_cf_url_placeholder = ($settings['jltma_comment_fields_url_label_placeholder']!='') ? esc_attr($settings['jltma_comment_fields_url_label_placeholder']) : '';

	            	$jltma_cf_url_style = "";

	            	$jltma_cf_url_ft = ( isset($settings['jltma_field_type']) && $settings['jltma_field_type'] ) ? $settings['jltma_field_type'] : "text";

			        $fields .= '<div class="jltma-url-value-div jltma-url">
							' . $jltma_cf_url_label_container . '
							<div class="jltma-value-div">
								<input class="form-control" type="' . $jltma_cf_url_ft . '" id="url" name="url" value="" placeholder="' . $jltma_cf_url_placeholder . '" ' . $jltma_cf_url_style . '/>
							</div>
						</div>';
			    }



				// Textarea Field
			    $jltma_ta_required 		= esc_html__('(Required)',JLTMA_TD);

			    $jltma_ta_label_container = "";
			    if ( $settings['jltma_comment_fields_textarea_label_display'] == "show") {
			    	
			    	$jltma_ta_label 		= ($settings['jltma_comment_fields_textarea_label']!= '') ? esc_attr($settings['jltma_comment_fields_textarea_label']) :'';
					
					if($jltma_ta_label){
			    		$jltma_ta_label_container = '<div class="jltma-title-div">	
				        	<label>' . $jltma_ta_label . '</label>
				        </div>';
				    }
			    }

			    // Notice
			    $jltma_ta_notice_container = "";
			    if ( $settings['jltma_comment_fields_textarea_notice'] == "show") {
			    	
			    	$jltma_ta_label 		= ($settings['jltma_comment_fields_textarea_label']!= '') ? esc_attr($settings['jltma_comment_fields_textarea_label']) :'';
			    	$jltma_ta_notice_content = ($settings['jltma_comment_fields_textarea_notice_content'] !="") ? $settings['jltma_comment_fields_textarea_notice_content'] : '';
			    	$jltma_ta_notice_align = ($settings['jltma_comment_fields_textarea_notice_align'] !="") ? $settings['jltma_comment_fields_textarea_notice_align'] : 'right';

			    	$jltma_ta_notice_container = '<div class="field-description">
					        	<span class="jltma-comment-description_tooltip ' . $jltma_ta_notice_align . '">' . $jltma_ta_notice_content . '</span>
					        </div>';
			    }




            	$jltma_ta_placeholder 	= ($settings['jltma_comment_fields_textarea_label_placeholder']!='') ? esc_attr($settings['jltma_comment_fields_textarea_label_placeholder']) : '';

            	$jltma_ta_style 		= "";

            	$jltma_cta_ft 			= (isset($settings['jltma_field_type'])) ? $settings['jltma_field_type'] : "";

		        $comment_ta_field = '<div class="jltma-title-value-div jltma-comment">
				        ' . $jltma_ta_label_container . '
				        <div class="jltma-value-div">
					        <textarea class="form-control" name="comment" id="comment" cols="45" rows="8" class="textarea-comment" placeholder="' . $jltma_ta_placeholder . '" '. $jltma_cta_ft . '></textarea>
					        </div>' . $jltma_ta_notice_container . '</div>';
			


                $comment_notes_before =  ($settings['jltma_comment_before_notes'] != '') ? esc_attr($settings['jltma_comment_before_notes']) : "";

                $comment_notes_after =  (isset($settings['jltma_comment_after_notes']) && $settings['jltma_comment_after_notes'] != '') ? esc_attr($settings['jltma_comment_after_notes']) : "";

                $title_reply = (isset($settings['jltma_comment_label']) && $settings['jltma_comment_label'] != '') ? esc_attr($settings['jltma_comment_label']) : esc_html__('Leave a Comment', JLTMA_TD);

                $title_reply_to = (isset($settings['jltma_comment_reply_label']) && $settings['jltma_comment_reply_label'] != '') ? esc_attr($settings['jltma_comment_reply_label']) : esc_html__('Leave a Reply', JLTMA_TD);

                $cancel_reply_link = (isset($settings['jltma_comment_cancel_reply_label']) && $settings['jltma_comment_cancel_reply_label'] != '') ? esc_attr($settings['jltma_comment_cancel_reply_label']) : esc_html__('Cancel Reply', JLTMA_TD);

                $label_submit = (isset($settings['jltma_comment_form_submit_label']) && $settings['jltma_comment_form_submit_label'] != '') ? esc_attr($settings['jltma_comment_form_submit_label']) : esc_html__('Post Comment', JLTMA_TD);
    
    			// API Settings
    			$jltma_api_settings = get_option( 'jltma_api_save_settings' );
                $submit_field ='';
                if (!is_user_logged_in()) {
	                if(isset($settings['jltma_comment_spam_protection']) && $settings['jltma_comment_spam_protection'] =="yes"){
	                	$submit_field .= '<div class="g-recaptcha" data-sitekey="'. $jltma_api_settings['recaptcha_site_key'] .'" data-badge="inline" render="explicit" style="padding-top:30px;"></div>';	
	                }
	            }

                if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) {
                	echo esc_html__('Check Frontend Comment Form for reCaptcha', MELA_TD);
                }
	            
	            $submit_field .= '<div class="jltma-form-submit">%1$s %2$s</div>';


                $comments_args = array(
                    'fields' 					=> apply_filters( 'comment_form_default_fields', $fields ),
                    'comment_field' 			=> '',
                    'title_reply' 				=> $title_reply,
                    'title_reply_to' 			=> $title_reply_to,

                    'must_log_in' 				=> '<p class="must-log-in">' . sprintf(esc_html__('You must be %1$slogged in%2$s to post a comment.', JLTMA_TD), '<a href="' . wp_login_url(apply_filters('the_permalink', get_permalink())) . '">', '</a>') . '</p>',

                    'logged_in_as' 				=> '<p class="logged-in-as">' . sprintf(esc_html__('Logged in as %1$s. %2$sLog out &raquo;%3$s', JLTMA_TD), '<a href="' . esc_url( admin_url('profile.php') ) . '">' . $user_identity . '</a>', '<a href="' . wp_logout_url(apply_filters('the_permalink', get_permalink())) . '" title="' . esc_html__('Log out of this account', JLTMA_TD) . '">', '</a>') . '</p>',

                    'comment_notes_before' 		=> $comment_notes_before,
                    'comment_notes_after' 		=> $comment_notes_after,
                    'cancel_reply_link' 		=> $cancel_reply_link,
                    'id_submit' 				=> 'jltma-comment-submit',
                    'class_submit' 				=> 'jltma-comment-form-submit',
                    'label_submit' 				=> $label_submit,
                    'submit_button' 			=> '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
                    // 'submit_field' 				=> '<div class="jltma-form-submit">%1$s %2$s</div>',
                    'submit_field' 				=> $submit_field,
                    'id_form' 					=> 'jltma-commentform',
                    'id_submit' 				=> 'jltma-submit',
                    'class_form' 				=> 'jltma-comment-form clearfix',
                    'comment_field'				=> $comment_ta_field
                );


                $this->jltma_comment_form($comments_args);

            } 
        }
        

        public function jltma_comment_listing_inner_template(){
        	
        	global $wpdb, $post;

			$settings = $this->get_settings_for_display();
			
			$page_number = empty($page_number) ? 1 : $page_number;
			
			$template = ($settings['jltma_comment_style_preset'] != "") ? esc_attr($settings['jltma_comment_style_preset']) : 'style_one';

			$pagination = ( $settings['jltma_comment_pagination']) =="yes" ? esc_attr($settings['jltma_comment_pagination']): '';

			$items_per_page = isset($settings['jltma_comment_pagination_items']['size']) ? esc_attr($settings['jltma_comment_pagination_items']['size']) : '10';

			$pagination_type = 'page_number';
			$sort_type = 'default';
			$post_id = $post->ID;
			?>
			
			<input type="hidden" id="jltma-current-post-id" value=<?php echo $post_id; ?> >

			<div class="jltma-comment-list-inner">
			    <?php
				    $db_table_name = $wpdb->prefix . "comments";
				    $comment_listing = JLTMA_Comments_Builder::jltma_recursive_array_builder(
			    		$db_table_name = $wpdb->prefix . "comments", 
			    		$parent = 0, 
			    		$parent_child = true, 
			    		$post_id, 
			    		$sort_type, 
			    		$pagination, 
			    		$items_per_page, 
			    		$pagination_type, 
			    		$page_number
			    	);
			    ?>
			    <div class="jltma-comment-listing-wrapper">
			        <?php
				        $class = 'jltma-comment-list';
				        $css="";
				        $child = 0;
				        $jltma_list_comments = new JLTMA_Comments_Builder();
				        $jltma_list_comments->jltma_list_comments($comment_listing, $class, $css, $template, $settings);
			        ?>
			    </div>
			</div>


			    <?php 

			    	if ($settings['jltma_comment_pagination'] == 'yes') { 

				    	$this->jltma_comment_pagination_wrapper( $comment_listing, $post_id, $settings );

				    	// Load More Comments
				  		// $jc_page = get_query_var('cpage') ? get_query_var('cpage') : 1;
						// if( $jc_page > 1 ) {
						// 	echo '<div class="jltma_comment_loadmore">More comments</div>';
						// }
			    	}
				?>			

        <?php }


        public function jltma_comment_pagination_wrapper( $comment_listing, $post_id, $settings ){
			global $post;

			$page_number 	 = empty($page_number) ? 1 : $page_number;
			$items_per_page = $settings['jltma_comment_pagination_items']['size'];
			$all_comments_approved = JLTMA_Comments_Builder::parent_comment_counter($post_id);

			$total = ceil($all_comments_approved / $items_per_page );

			$pagination_type='page-number';

			$jltma_comment_settings = new JLTMA_Comments_Builder();
			$jltma_comment_settings->jltma_comment_pagination( $settings );

		?>

			<div class="jltma-comment-pagination-wrapper jltma-pagination-not-demo jltma-<?php echo $pagination_type ?> ">
			    <?php
			        ?>
			        <ul>
			            <?php
			            for ($i = 1; $i <= $total - 1; $i++) {
			                ?>
			                <li><a href="javascript:void(0);" data-total-page="<?php echo $total; ?>" id="jltma-page-number" data-page-number="<?php echo $i; ?>" data-pagination-type="page-number" class= "<?php echo ($i == 1) ? 'jltma-current-page' : ''; ?> jltma-page-link" ><?php echo $i; ?></a></li>
			                <?php
			            }
			            if ($total > 1) {
			                ?>
			                <li class="jltma-next-page-wrap"><a href="javascript:void(0);" data-total-page="<?php echo $total; ?>" data-page-number="2" data-pagination-type= "page-number" class="jltma-next-page" ><i class="fa fa-angle-right"></i></a></li>
			            <?php } ?>
			        </ul>
			        <img src="<?php echo JLTMA_PLUGIN_URL . 'assets/images/ajax-loader.gif'?>" class="jltma-page-number-loader" style="display:none;">
			        <?php
			    ?>
			</div>

		<?php }


        public function jltma_comment_list_template(){
        	$settings = $this->get_settings_for_display();

			$page_number 	 = empty($page_number) ? 1 : $page_number;
			$template 		 = ($settings['jltma_comment_style_preset'] != "") ? esc_attr($settings['jltma_comment_style_preset']) : 'style_one';
			$pagination 	 = ($settings['jltma_comment_pagination'] =="yes") ? esc_attr($settings['jltma_comment_pagination']): '';

			$items_per_page  = isset($settings['jltma_comment_pagination_items']['size']) ? esc_attr($settings['jltma_comment_pagination_items']['size']) : '10';
			$pagination_type = 'page_number';
		?>

			<div class="jltma-comment-listing-wrap" id="jltma-comment-listing-wrap">
			    
			    <?php
					// Demo Contents for Elementor Template Preivew
					if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) {
			    		echo '<h5 class="alert alert-info">These Comments Data\'s are only for Live Preview on Elementor Backend</h5><br>';
			    	}

					if (isset($settings['jltma_comment_total_number']) && $settings['jltma_comment_total_number'] == 'show') {
					
						// Demo Contents for Elementor Template Preivew
						if (is_user_logged_in() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode()) {

							if($settings['jltma_comment_plural_comment_text'] !=""){
								$backend_comment_text = $settings['jltma_comment_plural_comment_text'];
							} elseif($settings['jltma_comment_single_comment_text'] !=""){
								$backend_comment_text = $settings['jltma_comment_single_comment_text'];
							} elseif($settings['jltma_comment_plural_comment_text'] !=""){
								$backend_comment_text = $settings['jltma_comment_plural_comment_text'];
							} else{
								$backend_comment_text = $settings['jltma_comment_no_comment_text'];
							}
					        echo "<h3 class='jltma-comments-title'>";
					        echo '5 ' . $backend_comment_text . ' on ' . get_the_title();
					        echo "</h3>";	            	
			            
			            } else{

			            	$no_comment 	= $settings['jltma_comment_no_comment_text'];
			            	$one_comment 	= $settings['jltma_comment_single_comment_text'];
			            	$plural_comment = $settings['jltma_comment_plural_comment_text'];
			            	
					        echo "<h3 class='jltma-comments-title'>";

							$comments_number =comments_number( $no_comment , $one_comment, sprintf( '%1$s %2$s', '%',$plural_comment));

					        echo $comments_number . ' on ' . get_the_title();
					        echo "</h3>";
					    }		
		            }


				    $this->jltma_comment_listing_inner_template();
				?>
			</div>

        <?php }


        public function jltma_comment_template() {
            
            global $post, $comments;
            $settings       = $this->get_settings_for_display();
            $jltma_id       = 'jltma-comment-' . $this->get_id();

            $jltma_comment_style_preset = ($settings['jltma_comment_style_preset']) ? $settings['jltma_comment_style_preset'] : "style_one";



            $this->add_render_attribute([
				'jltma_comments_wrap' => [
					'id'    => esc_attr( $jltma_id ),
					'class' => implode(' ', [
						'jltma-comments-wrap',
                    	'jltma-comment-' . $jltma_comment_style_preset,
						$jltma_id
					]),
					'data-jltma-comment-settings' => [
						wp_json_encode(array_filter([
							"container_id"          =>  esc_attr( $this->get_id()),
							"template"          	=>  $jltma_comment_style_preset,
							"reCaptchaprotected"   =>  $settings['jltma_comment_spam_protection'] =="yes" ? $settings['jltma_comment_spam_protection'] : 'no',
						])) 
					]
            	]
        	]); 


            if(isset($settings['jltma_comment_spam_protection']) && $settings['jltma_comment_spam_protection'] =="yes"){
                
                $jltma_api_settings = get_option( 'jltma_api_save_settings' );

                $jltma_spam_protection = [
                    'sitekey'   => $jltma_api_settings['recaptcha_site_key'] ? $jltma_api_settings['recaptcha_site_key'] : '',
                    'theme'     => "light"
                ];

				$this->add_render_attribute([ 'jltma_comments_wrap' =>[ 
					'data-recaptcha' => wp_json_encode( $jltma_spam_protection ) 
				]]);
            } ?>

            <div <?php echo $this->get_render_attribute_string( 'jltma_comments_wrap' );?> >

                <?php if ( comments_open() && JLTMA_Comments_Builder::jltma_comment_elementor_preview_mode() ) { 
					
					$this->jltma_comment_list_template();

					$this->jltma_frontend_form_build();

                } else {

					$this->jltma_comment_list_template();

					$this->jltma_frontend_form_build();
				} ?>
            </div>

        <?php }


		protected function render() {

            $settings       = $this->get_settings_for_display();
            
            $jltma_settings = new JLTMA_Comments_Builder($settings);
            
            $this->jltma_comment_template();

        }

    }