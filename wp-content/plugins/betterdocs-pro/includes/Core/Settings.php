<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Utils\Database;
use WPDeveloper\BetterDocs\Admin\Builder\Rules;
use WPDeveloper\BetterDocs\Admin\Builder\GlobalFields;
use WPDeveloper\BetterDocs\Core\Settings as FreeSettings;

class Settings extends FreeSettings {

    public function __construct( Database $database ) {
        parent::__construct( $database );

        add_filter( 'betterdocs_default_settings', [$this, '_default'], 11 );
        add_filter( 'betterdocs_settings_tab_sidebar_layout', [$this, 'tab_sidebar_layout'] );
        add_filter( 'betterdocs_shortcode_fields', [$this, 'shortcode_fields'] );
        add_filter( 'betterdocs_settings_args', [$this, '_args'], 11 );
    }

    public function get_raw_field( $key, $default = null ) {
        $_settings = $this->database->get( $this->base_key, [] );

        if( isset( $_settings[ $key ] ) ) {
            return $this->get_normalized_value( $key, $_settings[ $key ], $default );
        }

        return $default;
    }

    /**
     * A list of deprecated settings key.
     *
     * @since 2.5.0
     * @return array
     */
    public function deprecated_settings() {
        $_deprecated_settings = [
            'reporting_subject' => 'reporting_subject_updated'
        ];

        return array_merge( parent::deprecated_settings(), $_deprecated_settings );
    }

    /**
     * Migration for version 2.5.0
     *
     * @since 2.5.0
     * @return void
     */
    public function v250() {
        if( method_exists( parent::class, 'v250' ) ) {
            parent::v250();
        }

        $_reporting_subject = $this->get( 'reporting_subject_updated', '' );
        if ( ! empty( $_reporting_subject ) ) {
            $this->save( 'reporting_subject', $_reporting_subject );
        }

        $_ia_settings_migration = [
            'ia_heading_font_size',
            'ia_sub_heading_size',
            'iac_article_title_size',
            'iac_article_content_size',
            'ia_feedback_title_size',
            'ia_feedback_icon_size',
            'ia_response_icon_size',
            'ia_response_title_size',
            'iac_docs_title_font_size',
            'iac_article_content_h1',
            'iac_article_content_h2',
            'iac_article_content_h3',
            'iac_article_content_h4',
            'iac_article_content_h5',
            'iac_article_content_h6',
            'iac_article_content_p'
        ];

        $_defaults = $this->get_default();
        $_raw_settings = $this->database->get( $this->base_key, [] );
        foreach ($_ia_settings_migration as $key) {
            $data = isset( $_raw_settings[ $key ] ) ? $_raw_settings[ $key ] : null;
            if( $data !== null ) {
                if( empty( $data ) ) {
                    $this->save( $key, $_defaults[ $key ] );
                }
            }
        }
    }

    public function v253(){
        if( method_exists( parent::class, 'v253' ) ) {
            parent::v253();
        }

        $ia_options = [
            'display_ia_pages',
            'display_ia_archives',
            'display_ia_texonomy',
            'display_ia_single'
        ];

        foreach( $ia_options as $option ) {
            if( $this->get_raw_field( $option ) == false ) {
                $this->save( $option, [] );
            }
        }
    }

    public function enqueue( $hook ) {
        if ( $hook !== 'betterdocs_page_betterdocs-settings' ) {
            return;
        }
        wp_enqueue_media();

        parent::enqueue( $hook );

        betterdocs_pro()->assets->enqueue( 'betterdocs-pro-settings', 'admin/css/settings.css' );
        betterdocs_pro()->assets->enqueue( 'betterdocs-pro-settings', 'admin/js/settings.js' );
        betterdocs_pro()->assets->localize( 'betterdocs-pro-settings', 'betterdocsProAdminSettings', [
            'multiple_kb_url' => admin_url( 'edit-tags.php?taxonomy=knowledge_base&post_type=docs' )
        ] );
    }

    /**
     * A list of default settings.
     *
     * @param array $defaults
     * @return array
     */
    public function _default( $defaults ) {
        $_pro_defaults = [
            'multiple_kb'                         => false,
            'disable_root_slug_mkb'               => false,
            'advance_search'                      => false,
            'child_category_exclude'              => false,
            'popular_keyword_limit'               => 5,
            'search_button_text'                  => __( 'Search', 'betterdocs' ),
            'kb_based_search'                     => false,
            'article_roles'                       => ['administrator'],
            'settings_roles'                      => ['administrator'],
            'analytics_roles'                     => ['administrator'],
            'enable_content_restriction'          => false,
            'content_visibility'                  => ['all'],
            'restrict_template'                   => ['all'],
            'restrict_category'                   => ['all'],
            'restrict_kb'                   => ['all'],
            'restricted_redirect_url'             => '',
            'reporting_frequency'                 => 'betterdocs_weekly',
            'reporting_subject'                   => wp_sprintf(
                __( 'Your Documentation Performance of %s Website', 'betterdocs-pro' ),
                get_bloginfo( 'name' )
            ),
            'select_reporting_data'               => ['overview', 'top-docs', 'most-search'],
            'archive_nested_subcategory'          => true,

            'enable_disable'                      => true,
            'ia_enable_preview'                   => true,
            'content_type'                        => 'docs',
            'docs_list'                           => [],
            'doc_category_list'                   => [],
            'doc_category_limit'                  => 10,
            'display_ia_pages'                    => ['all'],
            'display_ia_archives'                 => ['all'],
            'display_ia_texonomy'                 => ['all'],
            'display_ia_single'                   => ['all'],
            'ask_subject'                         => '[ia_subject]',
            'ask_email'                           => get_bloginfo( 'admin_email' ),
            'ask_thanks_title'                    => __( 'Thanks', 'betterdocs-pro' ),
            'ask_thanks_text'                     => __( 'Your Message Has Been Sent Successfully', 'betterdocs-pro' ),
            'launcher_open_icon'                  => [],
            'launcher_close_icon'                 => [],
            'search_visibility_switch'            => false,
            'search_placeholder_text'             => __( 'Search...', 'betterdocs-pro' ),
            'answer_tab_visibility_switch'        => false,
            'answer_tab_icon'                     => [],
            'answer_tab_title'                    => __( 'Answer', 'betterdocs-pro' ),
            'answer_tab_subtitle'                 => __( 'Instant Answer', 'betterdocs-pro' ),
            'chat_tab_visibility_switch'          => false,
            'chat_tab_icon'                       => [],
            'chat_tab_title'                      => __( 'Ask', 'betterdocs-pro' ),
            'chat_subtitle_one'                   => __( 'Stuck with something? Send us a message.', 'betterdocs-pro' ),
            'chat_subtitle_two'                   => __( 'Generally, we reply within 24-48 hours.', 'betterdocs-pro' ),
            'disable_reaction'                    => false,
            'reaction_title'                      => __( 'How did you feel?', 'betterdocs-pro' ),
            'disable_response'                    => false,
            'response_title'                      => __( 'Thanks for the feedback', 'betterdocs-pro' ),
            'disable_response_icon'               => false,
            'disable_branding'                    => false,
            'chat_position'                       => 'right',
            'chat_zindex'                         => 9999,
            'search_not_found_1'                  => __( 'Oops...', 'betterdocs-pro' ),
            'search_not_found_2'                  => __( 'We couldn’t find any docs that match your search. Try searching for a new term.', 'betterdocs-pro' ),
            'ia_luncher_bg'                       => '#19ca9e',
            'ia_luncher_bg_hover'                 => '#19ca9e',
            'ia_color_title'                      => '',
            'ia_accent_color'                     => '#19ca9e',
            'ia_sub_accent_color'                 => '#16b38c',
            'ia_heading_font_size'                => 19,
            'ia_heading_color'                    => '#fff',
            'ia_sub_heading_size'                 => 12,
            'ia_sub_heading_color'                => '#fff', //No color was set by default in the previous version
            'ia_searchbox_bg'                     => '#fff',
            'ia_searchbox_icon_color'             => '#ccc',
            'ia_searchbox_text'                   => '#2c3338',
            'iac_article_bg'                      => '', //does not contain any default value on default selectors(in previous version as well)
            'iac_article_title_size'              => 16,
            'iac_article_title'                   => '#1d2327',
            'iac_article_content_size'            => 16,
            'iac_article_content'                 => '', //does not contain any default value on default selectors(in previous version as well)
            'ia_feedback_title_size'              => 14,
            'ia_feedback_title_color'             => '', //does not contain any default value on default selectors(in previous version as well)
            'ia_feedback_icon_size'               => 15,
            'ia_feedback_icon_color'              => '#fff',
            'ia_response_icon_size'               => 0, //the selector is unknown and does not work in the previous version as well, left it 0
            'ia_response_title_size'              => 13, //the selector is unknown and does not work in the previous version as well, left it 25
            'ia_response_title_color'             => '', //the selector is unknown and does not work in the previous version as well, left it empty
            'ia_response_icon_color'              => '', //the selector is unknown and does not work in the previous version as well(where the key was empty), left it empty
            'ia_ask_bg_color'                     => '#fff',
            'ia_ask_input_foreground'             => '#939eaa',
            'ia_ask_send_disable_button_bg'       => '#19ca9e', //does not work in previous version, problem might exist
            'ia_ask_send_disable_button_hover_bg' => '#19ca9e', //does not work in previous version, problem might exist
            'ia_ask_send_button_bg'               => '#19ca9e', //does not work in previous version, problem might exist
            'ia_ask_send_button_hover_bg'         => '#19ca9e',
            'content_heading_tag'                 => '',
            'iac_docs_title_font_size'            => 20,
            'iac_article_content_h1'              => 26,
            'iac_article_content_h2'              => 24,
            'iac_article_content_h3'              => 22,
            'iac_article_content_h4'              => 20,
            'iac_article_content_h5'              => 18,
            'iac_article_content_h6'              => 16,
            'iac_article_content_p'               => 14
        ];

        return array_merge( $defaults, $_pro_defaults );
    }

    /**
     * A list of default settings (ONLY PRO)
     * @return array
     */
    public function get_pro_defaults() {
        return $this->_default( [] );
    }

    public function tab_sidebar_layout( $args ) {
        $args['fields']['layout_documentation_page']['fields']['popular_docs'] = [
            'name'     => 'popular_docs',
            'type'     => 'section',
            'label'    => __( 'Popular Docs', 'betterdocs-pro' ),
            'priority' => 26,
            'fields'   => [
                'betterdocs_popular_docs_text'   => [
                    'name'     => 'betterdocs_popular_docs_text',
                    'type'     => 'text',
                    'label'    => __( 'Popular Docs Text', 'betterdocs-pro' ),
                    'default'  => __( 'Popular Docs', 'betterdocs-pro' ),
                    'priority' => 10,
                    "is_pro"   => true
                ],
                'betterdocs_popular_docs_number' => [
                    'name'     => 'betterdocs_popular_docs_number',
                    'type'     => 'number',
                    'label'    => __( 'Popular Docs Number', 'betterdocs-pro' ),
                    'default'  => 10,
                    'priority' => 11,
                    "is_pro"   => true
                ]
            ]
        ];

        return $args;
    }

    public function shortcode_fields( $args ) {
        $args['category_box_l3_shortcode'] = [
            'name'      => 'category_box_l3_shortcode',
            'type'      => 'text',
            'label'     => __( 'Category Box- Layout 3', 'betterdocs-pro' ),
            'default'   => '[betterdocs_category_box_2]',
            'readOnly'  => true,
            'copyOnClick' => true,
            'priority'  => 8,
            'help'      => __( '<strong>You can use:</strong> [betterdocs_category_box_2 column="" nested_subcategory="" terms="" terms_orderby="" kb_slug="" multiple_knowledge_base="false" title_tag="h2"]', 'betterdocs-pro' )
        ];
        $args['category_grid_2_shortcode'] = [
            'name'      => 'category_grid_2_shortcode',
            'type'      => 'text',
            'label'     => __( 'Category Grid- Layout 4', 'betterdocs-pro' ),
            'default'   => '[betterdocs_category_grid_2]',
            'readOnly'  => true,
            'copyOnClick' => true,
            'priority'  => 9,
            'help'      => __( '<strong>You can use:</strong> [betterdocs_category_grid_2 orderby="" order="" masonry="" column="" posts="" nested_subcategory="" terms="" kb_slug="" terms_orderby="" terms_order="" multiple_knowledge_base="false" title_tag="h2"]', 'betterdocs-pro' )
        ];
        $args['multiple_kb_shortcode'] = [
            'name'      => 'multiple_kb_shortcode',
            'type'      => 'text',
            'label'     => __( 'Multiple KB- Layout 1', 'betterdocs-pro' ),
            'default'   => '[betterdocs_multiple_kb]',
            'readOnly'  => true,
            'copyOnClick' => true,
            'priority'  => 10,
            'help'      => __( '<strong>You can use:</strong> [betterdocs_multiple_kb column="" terms="" title_tag="h2"]', 'betterdocs-pro' )
        ];
        $args['multiple_kb_2_shortcode'] = [
            'name'      => 'multiple_kb_2_shortcode',
            'type'      => 'text',
            'label'     => __( 'Multiple KB- Layout 2', 'betterdocs-pro' ),
            'default'   => '[betterdocs_multiple_kb_2]',
            'readOnly'  => true,
            'copyOnClick' => true,
            'priority'  => 11,
            'help'      => __( '<strong>You can use:</strong> [betterdocs_multiple_kb_2 column="" terms="" title_tag="h2"]', 'betterdocs-pro' )
        ];
        $args['multiple_kb_3_shortcode'] = [
            'name'      => 'multiple_kb_3_shortcode',
            'type'      => 'text',
            'label'     => __( 'Multiple KB- Layout 3', 'betterdocs-pro' ),
            'default'   => '[betterdocs_multiple_kb_list]',
            'readOnly'  => true,
            'copyOnClick' => true,
            'priority'  => 12,
            'help'      => __( '<strong>You can use:</strong> [betterdocs_multiple_kb_list terms="" title_tag="h2"]', 'betterdocs-pro' )
        ];
        $args['multiple_kb_4_shortcode'] = [
            'name'      => 'multiple_kb_4_shortcode',
            'type'      => 'text',
            'label'     => __( 'Multiple KB- Layout 4', 'betterdocs-pro' ),
            'default'   => '[betterdocs_multiple_kb_tab_grid]',
            'readOnly'  => true,
            'copyOnClick' => true,
            'priority'  => 13,
            'help'      => __( '<strong>You can use:</strong> [betterdocs_multiple_kb_tab_grid terms="" terms_orderby="" terms_order="" orderby="" order="" posts_per_page="" title_tag="h2"]', 'betterdocs-pro' )
        ];
        $args['mkb_popular_docs'] = [
            'name'      => 'mkb_popular_docs',
            'type'      => 'text',
            'label'     => __( 'Popular Docs', 'betterdocs-pro' ),
            'default'   => '[betterdocs_popular_articles]',
            'readOnly'  => true,
            'copyOnClick' => true,
            'priority'  => 14,
            'help'      => __( '<strong>You can use:</strong> [betterdocs_popular_articles post_per_page="" title="Popular Docs" title_tag="h2" multiple_knowledge_base="false"]', 'betterdocs-pro' )
        ];

        return $args;
    }

    public function _args( $args ) {
        /**
         * Instant Answer Related Options
         */
        $args['tabs']['tab-instant-answer']['fields']['instant_answer_tab'] = [
            'id'              => 'instant_answer_tab',
            'name'            => 'instant_answer_tab',
            'classes'         => 'tab-layout',
            'type'            => 'tab',
            'label'           => __( 'Instant Answer Setting', 'betterdocs-pro' ),
            'active'          => "initial_content_type_settings",
            'completionTrack' => true,
            'sidebar'         => true,
            'submit'          => [
                'show' => false
            ],
            'step'            => [
                'show' => false
            ],
            'rules'           => Rules::is( 'enable_disable', true ),
            'priority'        => 1,
            'fields'          => [
                'initial_content_type_settings'    => [
                    'id'       => 'initial_content_type_settings',
                    'name'     => 'initial_content_type_settings',
                    'type'     => 'section',
                    'label'    => __( 'Initial Content Settings', 'betterdocs-pro' ),
                    'priority' => 1,
                    'fields'   => [
                        'ia_initial_title'    => [
                            'name'     => 'ia_initial_title',
                            'type'     => 'title',
                            'label'    => __( 'Initial Content Settings', 'betterdocs-pro' ),
                            'priority' => 0
                        ],
                        'content_type'        => [
                            'name'     => 'content_type',
                            'label'    => __( 'Content Type', 'betterdocs-pro' ),
                            'type'     => 'select',
                            'priority' => 1,
                            'default'  => 'docs',
                            'options'  => GlobalFields::normalize_fields( [
                                'docs'            => __( 'Docs', 'betterdocs-pro' ),
                                'docs_categories' => __( 'Docs Categories', 'betterdocs-pro' )
                            ] )
                        ],
                        'docs_list'           => [
                            'name'     => 'docs_list',
                            'label'    => __( 'Select Docs', 'betterdocs-pro' ),
                            'type'     => 'select',
                            'priority' => 2,
                            'multiple' => true,
                            'search'   => true,
                            'options'  => $this->docs(),
                            'rules'    => Rules::includes( 'content_type', 'docs' )
                        ],
                        'doc_category_list'   => [
                            'name'     => 'doc_category_list',
                            'label'    => __( 'Select Docs Categories', 'betterdocs-pro' ),
                            'type'     => 'select',
                            'priority' => 3,
                            'multiple' => true,
                            'options'  => $this->docs_categories(),
                            'rules'    => Rules::includes( 'content_type', 'docs_categories' )
                        ],
                        'doc_category_limit'  => [
                            'name'     => 'doc_category_limit',
                            'type'     => 'number',
                            'label'    => __( 'Number Of Categories', 'betterdocs-pro' ),
                            'default'  => 10,
                            'priority' => 4,
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'content_type', 'docs_categories' )
                            ] )
                        ],
                        'display_ia_pages'    => [
                            'name'     => 'display_ia_pages',
                            'type'     => 'select',
                            'label'    => __( 'Show on Pages', 'betterdocs-pro' ),
                            'priority' => 5,
                            'multiple' => true,
                            'search'   => true,
                            'disable'  => false,
                            'default'  => ['all'],
                            'options'  => $this->get_pages_for_ia()
                        ],
                        'display_ia_archives' => [
                            'name'     => 'display_ia_archives',
                            'type'     => 'select',
                            'label'    => __( 'Show on Archive Templates', 'betterdocs-pro' ),
                            'priority' => 6,
                            'multiple' => true,
                            'search'   => true,
                            'disable'  => false,
                            'default'  => ['all'],
                            'options'  => $this->get_all_post_types()
                        ],
                        'display_ia_texonomy' => [
                            'name'     => 'display_ia_texonomy',
                            'type'     => 'select',
                            'label'    => __( 'Show on Texonomy Templates', 'betterdocs-pro' ),
                            'priority' => 7,
                            'multiple' => true,
                            'search'   => true,
                            'disable'  => false,
                            'default'  => ['all'],
                            'options'  => $this->get_all_registered_texonomy()
                        ],
                        'display_ia_single'   => [
                            'name'     => 'display_ia_single',
                            'type'     => 'select',
                            'label'    => __( 'Show on Single Pages', 'betterdocs-pro' ),
                            'priority' => 8,
                            'multiple' => true,
                            'search'   => true,
                            'disable'  => false,
                            'default'  => ['all'],
                            'options'  => $this->get_all_post_types()
                        ]
                    ]
                ],
                'betterdocs_chat_settings'         => [
                    'id'       => 'betterdocs_chat_settings',
                    'name'     => 'betterdocs_chat_settings',
                    'type'     => 'section',
                    'label'    => __( 'Chat Settings', 'betterdocs-pro' ),
                    'priority' => 2,
                    'fields'   => [
                        'ia_chat_title'    => [
                            'name'     => 'ia_chat_title',
                            'type'     => 'title',
                            'label'    => __( 'Chat Settings', 'betterdocs-pro' ),
                            'priority' => 9
                        ],
                        'ask_subject'      => [
                            'name'     => 'ask_subject',
                            'type'     => 'text',
                            'label'    => __( 'Custom Subject', 'betterdocs-pro' ),
                            'priority' => 1,
                            'default'  => '[ia_subject]',
                            'help'     => __( 'You can use <mark>[ia_subject]</mark>, <mark>[ia_email]</mark>, <mark>[ia_name]</mark> as placeholder. <br><strong>i.e:</strong> An enquiry is placed By [ia_name] for [ia_subject].' )
                        ],
                        'ask_email'        => [
                            'name'     => 'ask_email',
                            'type'     => 'text',
                            'label'    => __( 'Email Address', 'betterdocs-pro' ),
                            'priority' => 10,
                            'default'  => get_bloginfo( 'admin_email' )
                        ],
                        'ask_thanks_title' => [
                            'name'     => 'ask_thanks_title',
                            'type'     => 'text',
                            'label'    => __( 'Success Message Title', 'betterdocs-pro' ),
                            'priority' => 3,
                            'default'  => __( 'Thanks', 'betterdocs-pro' )
                        ],
                        'ask_thanks_text'  => [
                            'name'     => 'ask_thanks_text',
                            'type'     => 'text',
                            'label'    => __( 'Success Message Text', 'betterdocs-pro' ),
                            'priority' => 11,
                            'default'  => __( 'Your Message Has Been Sent Successfully', 'betterdocs-pro' )
                        ]
                    ]
                ],
                'betterdocs_appearance_settings'   => [
                    'id'       => 'betterdocs_appearance_settings',
                    'name'     => 'betterdocs_appearance_settings',
                    'type'     => 'section',
                    'label'    => __( 'Appearance Settings', 'betterdocs-pro' ),
                    'priority' => 3,
                    'fields'   => [
                        'ia_appearance_title'          => [
                            'name'     => 'ia_appearance_title',
                            'type'     => 'title',
                            'label'    => __( 'Appearance Settings', 'betterdocs-pro' ),
                            'priority' => 0
                        ],
                        'launcher_open_icon'           => [
                            'name'     => 'launcher_open_icon',
                            'type'     => 'media',
                            'value'    => '',
                            'label'    => __( 'Instant Answer Open Icon', 'betterdocs-pro' ),
                            'priority' => 1
                        ],
                        'launcher_close_icon'          => [
                            'name'     => 'launcher_close_icon',
                            'type'     => 'media',
                            'value'    => '',
                            'label'    => __( 'Instant Answer Close Icon', 'betterdocs-pro' ),
                            'priority' => 2
                        ],
                        'search_visibility_switch'     => [
                            'name'     => 'search_visibility_switch',
                            'type'     => 'checkbox',
                            'label'    => __( 'Disable Search', 'betterdocs-pro' ),
                            'priority' => 3
                        ],
                        'search_placeholder_text'      => [
                            'name'     => 'search_placeholder_text',
                            'type'     => 'text',
                            'label'    => __( 'Search Placeholder', 'betterdocs-pro' ),
                            'priority' => 4,
                            'default'  => __( 'Search...', 'betterdocs-pro' ),
                            'rules'    => Rules::is( 'search_visibility_switch', false )
                        ],
                        'answer_tab_visibility_switch' => [
                            'name'     => 'answer_tab_visibility_switch',
                            'type'     => 'checkbox',
                            'label'    => __( 'Disable Answer Tab', 'betterdocs-pro' ),
                            'priority' => 5
                        ],
                        'answer_tab_icon'              => [
                            'name'     => 'answer_tab_icon',
                            'type'     => 'media',
                            'label'    => __( 'Instant Answer Tab Icon', 'betterdocs-pro' ),
                            'priority' => 6,
                            'rules'    => Rules::is( 'answer_tab_visibility_switch', false )
                        ],
                        'answer_tab_title'             => [
                            'name'     => 'answer_tab_title',
                            'type'     => 'text',
                            'label'    => __( 'Instant Answer Tab Title', 'betterdocs-pro' ),
                            'priority' => 7,
                            'default'  => __( 'Answer', 'betterdocs-pro' ),
                            'rules'    => Rules::is( 'answer_tab_visibility_switch', false )
                        ],
                        'answer_tab_subtitle'          => [
                            'name'     => 'answer_tab_subtitle',
                            'type'     => 'text',
                            'label'    => __( 'Instant Answer Tab Subtitle', 'betterdocs-pro' ),
                            'priority' => 8,
                            'default'  => __( 'Instant Answer', 'betterdocs-pro' ),
                            'rules'    => Rules::is( 'answer_tab_visibility_switch', false )
                        ],
                        'chat_tab_visibility_switch'   => [
                            'name'     => 'chat_tab_visibility_switch',
                            'type'     => 'checkbox',
                            'label'    => __( 'Disable Chat Tab', 'betterdocs-pro' ),
                            'priority' => 9
                        ],
                        'chat_tab_icon'                => [
                            'name'     => 'chat_tab_icon',
                            'type'     => 'media',
                            'value'    => '',
                            'label'    => __( 'Instant Chat Tab Icon', 'betterdocs-pro' ),
                            'priority' => 10,
                            'rules'    => Rules::is( 'chat_tab_visibility_switch', false )

                        ],
                        'chat_tab_title'               => [
                            'name'     => 'chat_tab_title',
                            'type'     => 'text',
                            'label'    => __( 'Instant Chat Tab Title', 'betterdocs-pro' ),
                            'priority' => 11,
                            'default'  => __( 'Ask', 'betterdocs-pro' ),
                            'rules'    => Rules::is( 'chat_tab_visibility_switch', false )
                        ],
                        'chat_subtitle_one'            => [
                            'name'     => 'chat_subtitle_one',
                            'type'     => 'text',
                            'label'    => __( 'Chat Tab Subtitle One', 'betterdocs-pro' ),
                            'priority' => 12,
                            'default'  => __( 'Stuck with something? Send us a message.', 'betterdocs-pro' ),
                            'rules'    => Rules::is( 'chat_tab_visibility_switch', false )
                        ],
                        'chat_subtitle_two'            => [
                            'name'     => 'chat_subtitle_two',
                            'type'     => 'text',
                            'label'    => __( 'Chat Tab Subtitle Two', 'betterdocs-pro' ),
                            'priority' => 13,
                            'default'  => __( 'Generally, we reply within 24-48 hours.', 'betterdocs-pro' ),
                            'rules'    => Rules::is( 'chat_tab_visibility_switch', false )
                        ],
                        'disable_reaction'             => [
                            'name'     => 'disable_reaction',
                            'type'     => 'checkbox',
                            'label'    => __( 'Disable Reaction', 'betterdocs-pro' ),
                            'priority' => 14
                        ],
                        'reaction_title'               => [
                            'name'     => 'reaction_title',
                            'type'     => 'text',
                            'label'    => __( 'Reaction Title', 'betterdocs-pro' ),
                            'priority' => 15,
                            'default'  => __( 'How did you feel?', 'betterdocs-pro' ),
                            'rules'    => Rules::is( 'disable_reaction', false )

                        ],
                        'disable_response'             => [
                            'name'     => 'disable_response',
                            'type'     => 'checkbox',
                            'label'    => __( 'Disable Response', 'betterdocs-pro' ),
                            'priority' => 16,
                            'rules'    => Rules::is( 'disable_reaction', false )
                        ],
                        'response_title'               => [
                            'name'     => 'response_title',
                            'type'     => 'text',
                            'label'    => __( 'Response Title', 'betterdocs-pro' ),
                            'priority' => 17,
                            'default'  => __( 'Thanks for the feedback', 'betterdocs-pro' ),
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'disable_reaction', false ),
                                Rules::is( 'disable_response', false )
                            ] )
                        ],
                        'disable_response_icon'        => [
                            'name'     => 'disable_response_icon',
                            'type'     => 'checkbox',
                            'label'    => __( 'Disable Response Icon', 'betterdocs-pro' ),
                            'priority' => 18,
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'disable_reaction', false ),
                                Rules::is( 'disable_response', false )
                            ] )
                        ],
                        'disable_branding'             => [
                            'name'     => 'disable_branding',
                            'type'     => 'checkbox',
                            'label'    => __( 'Disable Branding', 'betterdocs-pro' ),
                            'priority' => 19
                        ],
                        'chat_position'                => [
                            'name'     => 'chat_position',
                            'type'     => 'select',
                            'label'    => __( 'Position', 'betterdocs-pro' ),
                            'priority' => 20,
                            'default'  => 'right',
                            'options'  => $this->normalize_options( [
                                'left'  => __( 'Left', 'betterdocs-pro' ),
                                'right' => __( 'Right', 'betterdocs-pro' )
                            ] )
                        ],
                        'chat_zindex'                  => [
                            'name'     => 'chat_zindex',
                            'type'     => 'number',
                            'label'    => __( 'Z-index', 'betterdocs-pro' ),
                            'priority' => 21,
                            'default'  => 9999
                        ],
                        'search_not_found_1'           => [
                            'name'     => 'search_not_found_1',
                            'type'     => 'text',
                            'label'    => __( 'Docs not Found', 'betterdocs-pro' ),
                            'priority' => 22,
                            'default'  => __( 'Oops...', 'betterdocs-pro' )
                        ],
                        'search_not_found_2'           => [
                            'name'     => 'search_not_found_2',
                            'type'     => 'text',
                            'label'    => __( 'Docs not Found', 'betterdocs-pro' ),
                            'priority' => 23,
                            'default'  => __( 'We couldn’t find any docs that match your search. Try searching for a new term.', 'betterdocs-pro' )
                        ]
                    ]
                ],
                'betterdocs_color_settings'        => [
                    'id'       => 'betterdocs_color_settings',
                    'name'     => 'betterdocs_color_settings',
                    'type'     => 'section',
                    'label'    => __( 'Style Settings', 'betterdocs-pro' ),
                    'priority' => 4,
                    'fields'   => [
                        'ia_luncher_bg'                       => [
                            'name'     => 'ia_luncher_bg',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Launcher Background Color', 'betterdocs-pro' ),
                            'default'  => '#19ca9e',
                            'priority' => 1
                        ],
                        'ia_luncher_bg_hover'                 => [
                            'name'     => 'ia_luncher_bg_hover',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Launcher Hover Background Color', 'betterdocs-pro' ),
                            'default'  => '#19ca9e',
                            'priority' => 2
                        ],
                        'ia_color_title'                      => [
                            'name'     => 'ia_color_title',
                            'type'     => 'title',
                            'label'    => __( 'Color Settings', 'betterdocs-pro' ),
                            'priority' => 3
                        ],
                        'ia_accent_color'                     => [
                            'name'     => 'ia_accent_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Accent Color', 'betterdocs-pro' ),
                            'default'  => '#19ca9e',
                            'priority' => 4
                        ],
                        'ia_sub_accent_color'                 => [
                            'name'     => 'ia_sub_accent_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Sub Accent Color', 'betterdocs-pro' ),
                            'default'  => '#16b38c',
                            'priority' => 5
                        ],
                        'ia_heading_font_size'                => [
                            'name'     => 'ia_heading_font_size',
                            'type'     => 'number',
                            'label'    => __( 'Heading Font Size', 'betterdocs-pro' ),
                            'default'  => 19,
                            'priority' => 6
                        ],
                        'ia_heading_color'                    => [
                            'name'     => 'ia_heading_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Heading Color', 'betterdocs-pro' ),
                            'default'  => '#fff',
                            'priority' => 7
                        ],
                        'ia_sub_heading_size'                 => [
                            'name'     => 'ia_sub_heading_size',
                            'type'     => 'number',
                            'label'    => __( 'Sub Heading Size', 'betterdocs-pro' ),
                            'default'  => 12,
                            'priority' => 8
                        ],
                        'ia_sub_heading_color'                => [
                            'name'     => 'ia_sub_heading_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Sub Heading Color', 'betterdocs-pro' ),
                            'default'  => '#fff',
                            'priority' => 9
                        ],
                        'ia_searchbox_bg'                     => [
                            'name'     => 'ia_searchbox_bg',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Search Box Background Color', 'betterdocs-pro' ),
                            'default'  => '#fff',
                            'priority' => 10
                        ],
                        'ia_searchbox_icon_color'             => [
                            'name'     => 'ia_searchbox_icon_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Search Box Icon Color', 'betterdocs-pro' ),
                            'default'  => '#ccc',
                            'priority' => 11
                        ],
                        'ia_searchbox_text'                   => [
                            'name'     => 'ia_searchbox_text',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Search Box Foreground Color', 'betterdocs-pro' ),
                            'default'  => '#2c3338',
                            'priority' => 12
                        ],
                        'iac_article_bg'                      => [
                            'name'     => 'iac_article_bg',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Docs Card Background', 'betterdocs-pro' ),
                            'priority' => 13
                        ],
                        'iac_content_heading_tag'             => [
                            'name'     => 'iac_content_heading_tag',
                            'type'     => 'html',
                            'priority' => 14,
                            'html'     => wp_sprintf( '<h3>%s</h3>', __( 'Content Area Settings' ) )
                        ],
                        'iac_article_title_size'              => [
                            'name'     => 'iac_article_title_size',
                            'type'     => 'number',
                            'label'    => __( 'Docs Title Font Size', 'betterdocs-pro' ),
                            'default'  => 16,
                            'priority' => 15
                        ],
                        'iac_article_title'                   => [
                            'name'     => 'iac_article_title',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Docs Title Color', 'betterdocs-pro' ),
                            'default'  => '#1d2327',
                            'priority' => 16
                        ],
                        'iac_article_content_size'            => [
                            'name'     => 'iac_article_content_size',
                            'type'     => 'number',
                            'label'    => __( 'Docs Content Font Size', 'betterdocs-pro' ),
                            'default'  => 16,
                            'priority' => 17
                        ],
                        'iac_article_content'                 => [
                            'name'     => 'iac_article_content',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Docs Content Color', 'betterdocs-pro' ),
                            'priority' => 18
                        ],
                        'ia_feedback_title_size'              => [
                            'name'     => 'ia_feedback_title_size',
                            'type'     => 'number',
                            'label'    => __( 'Feedback Title Size', 'betterdocs-pro' ),
                            'default'  => 14,
                            'priority' => 19
                        ],
                        'ia_feedback_title_color'             => [
                            'name'     => 'ia_feedback_title_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Feedback Title Color', 'betterdocs-pro' ),
                            'priority' => 20
                        ],
                        'ia_feedback_icon_size'               => [
                            'name'     => 'ia_feedback_icon_size',
                            'type'     => 'number',
                            'label'    => __( 'Feedback Icon Size', 'betterdocs-pro' ),
                            'default'  => 15,
                            'priority' => 21
                        ],
                        'ia_feedback_icon_color'              => [
                            'name'     => 'ia_feedback_icon_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Feedback Icon Color', 'betterdocs-pro' ),
                            'default'  => '#fff',
                            'priority' => 22
                        ],
                        'ia_response_icon_size'               => [
                            'name'     => 'ia_response_icon_size',
                            'type'     => 'number',
                            'label'    => __( 'Response Icon Size', 'betterdocs-pro' ),
                            'default'  => 0, //ia_response_icon_size & the selector is unknown and does not work in the previous version as well, left it 0
                            'priority' => 23,
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'disable_reaction', false ),
                                Rules::is( 'disable_response', false )
                            ] )
                        ],
                        'ia_response_icon_color'              => [
                            'name'     => 'ia_response_icon_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Response Icon Color', 'betterdocs-pro' ), //the selector is unknown and does not work in the previous version as well, left it empty
                            'default'  => '',
                            'priority' => 24,
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'disable_reaction', false ),
                                Rules::is( 'disable_response', false )
                            ] )
                        ],
                        'ia_response_title_size'              => [
                            'name'     => 'ia_response_title_size',
                            'type'     => 'number',
                            'label'    => __( 'Response Title Size', 'betterdocs-pro' ),
                            'default'  => 13,
                            'priority' => 25,
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'disable_reaction', false ),
                                Rules::is( 'disable_response', false )
                            ] )
                        ],
                        'ia_response_title_color'             => [
                            'name'     => 'ia_response_title_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Response Title Color', 'betterdocs-pro' ),
                            'priority' => 26,
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'disable_reaction', false ),
                                Rules::is( 'disable_response', false )
                            ] )
                        ],
                        'ia_ask_bg_color'                     => [
                            'name'     => 'ia_ask_bg_color',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Ask Form Input BG Color', 'betterdocs-pro' ),
                            'default'  => '#fff',
                            'priority' => 27
                        ],
                        'ia_ask_input_foreground'             => [
                            'name'     => 'ia_ask_input_foreground',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Ask Form Input Text Color', 'betterdocs-pro' ),
                            'default'  => '#939eaa',
                            'priority' => 28
                        ],
                        'ia_ask_send_disable_button_bg'       => [
                            'name'     => 'ia_ask_send_disable_button_bg',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Ask Send Disabled Button Background', 'betterdocs-pro' ),
                            'default'  => '#19ca9e',
                            'priority' => 29
                        ],
                        'ia_ask_send_disable_button_hover_bg' => [
                            'name'     => 'ia_ask_send_disable_button_hover_bg',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Ask Send Disabled Button Hover Background', 'betterdocs-pro' ),
                            'default'  => '#19ca9e',
                            'priority' => 30
                        ],
                        'ia_ask_send_button_bg'               => [
                            'name'     => 'ia_ask_send_button_bg',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Ask Send Button Background', 'betterdocs-pro' ),
                            'default'  => '#19ca9e',
                            'priority' => 31
                        ],
                        'ia_ask_send_button_hover_bg'         => [
                            'name'     => 'ia_ask_send_button_hover_bg',
                            'type'     => 'colorpicker',
                            'label'    => __( 'Ask Send Button Hover Background', 'betterdocs-pro' ),
                            'default'  => '#19ca9e',
                            'priority' => 32
                        ],
                        'content_heading_tag'                 => [
                            'name'     => 'content_heading_tag',
                            'type'     => 'title',
                            'label'    => __( 'Content Area Settings', 'betterdocs-pro' ),
                            'priority' => 33
                        ],
                        'iac_docs_title_font_size'            => [
                            'name'     => 'iac_docs_title_font_size',
                            'type'     => 'number',
                            'label'    => __( 'Docs Title Font Size', 'betterdocs-pro' ),
                            'default'  => 20,
                            'priority' => 34
                        ],
                        'iac_article_content_h1'              => [
                            'name'     => 'iac_article_content_h1',
                            'type'     => 'number',
                            'label'    => __( 'H1 Font Size', 'betterdocs-pro' ),
                            'default'  => 26,
                            'priority' => 35
                        ],
                        'iac_article_content_h2'              => [
                            'name'     => 'iac_article_content_h2',
                            'type'     => 'number',
                            'label'    => __( 'H2 Font Size', 'betterdocs-pro' ),
                            'default'  => 24,
                            'priority' => 36
                        ],
                        'iac_article_content_h3'              => [
                            'name'     => 'iac_article_content_h3',
                            'type'     => 'number',
                            'label'    => __( 'H3 Font Size', 'betterdocs-pro' ),
                            'default'  => 22,
                            'priority' => 37
                        ],
                        'iac_article_content_h4'              => [
                            'name'     => 'iac_article_content_h4',
                            'type'     => 'number',
                            'label'    => __( 'H4 Font Size', 'betterdocs-pro' ),
                            'default'  => 20,
                            'priority' => 38
                        ],
                        'iac_article_content_h5'              => [
                            'name'     => 'iac_article_content_h5',
                            'type'     => 'number',
                            'label'    => __( 'H5 Font Size', 'betterdocs-pro' ),
                            'default'  => 18,
                            'priority' => 39
                        ],
                        'iac_article_content_h6'              => [
                            'name'     => 'iac_article_content_h6',
                            'type'     => 'number',
                            'label'    => __( 'H6 Font Size', 'betterdocs-pro' ),
                            'default'  => 16,
                            'priority' => 40
                        ],
                        'iac_article_content_p'               => [
                            'name'     => 'iac_article_content_p',
                            'type'     => 'number',
                            'label'    => __( 'Content Font Size', 'betterdocs-pro' ),
                            'default'  => 14,
                            'priority' => 41
                        ]
                    ]
                ],
                'betterdocs_cross_domain_settings' => [
                    'id'       => 'betterdocs_cross_domain_settings',
                    'name'     => 'betterdocs_cross_domain_settings',
                    'type'     => 'section',
                    'label'    => __( 'Cross Domain Settings', 'betterdocs-pro' ),
                    'priority' => 5,
                    'fields'   => [
                        'ia_cd_title'         => [
                            'name'     => 'ia_cd_title',
                            'type'     => 'title',
                            'label'    => __( 'Cross Domain Settings', 'betterdocs-pro' ),
                            'priority' => 0
                        ],
                        'ia_cd_title_content' => [
                            'name'        => 'ia_cd_title_content',
                            'type'        => 'codeviewer',
                            'readOnly'    => true,
                            'copyOnClick' => true,
                            'default'     => InstantAnswer::snippet(),
                            'label'       => __( 'Cross Domain Integration Snippet', 'betterdocs-pro' ),
                            'help'        => __( 'To display Instant Answer widget to an external website, insert this snippet before the closing body tag.' ),
                            'priority'    => 1
                            // 'view'     => [ $this, 'cross_domain' )
                        ]
                    ]
                ]
            ]
        ];

        /**
         * License Tab
         */
        $args['tabs']['tab-license'] = apply_filters( 'betterdocs_settings_tab_license', [
            'id'       => 'tab-license',
            'label'    => __( 'License', 'betterdocs-pro' ),
            'priority' => 7,
            'fields'   => [
                'betterdocs_licnese' => [
                    'name'    => 'betterdocs_licnese',
                    'type'    => 'action',
                    'action'  => 'betterdocs_settings_licnese',
                    'label'   => __( 'License', 'betterdocs' ),
                    'logourl' => BETTERDOCS_ABSURL . 'assets/admin/images/betterdocs-icon.svg'
                ]
            ]
        ] );

        $args['submit']['rules'] = Rules::logicalRule( [
            Rules::is( 'config.active', 'tab-design', true ),
            Rules::is( 'config.active', 'tab-shortcodes', true ),
            Rules::is( 'config.active', 'tab-license', true )
        ], 'and' );

        return $args;
    }

    /**
     * Get all docs
     */
    public function docs() {
        $docs = $this->database->get_cache( 'betterdocs::instant_answer::all_docs' );

        if ( $docs ) {
            return $docs;
        }

        $docs = [];

        $_docs = get_posts( [
            'post_type'      => 'docs',
            'numberposts'    => -1,
            'posts_per_page' => -1
        ] );

        if ( ! empty( $_docs ) ) {
            foreach ( $_docs as $doc ) {
                $docs[$doc->ID] = betterdocs()->template_helper->kses( $doc->post_title );
            }
            $docs = GlobalFields::normalize_fields( $docs );
            $this->database->set_cache( 'betterdocs::instant_answer::all_docs', $docs );
        }

        return $docs;
    }

    /**
     * Get All Categories for Docs Type
     * @return array
     */
    public function docs_categories() {
        $docs_terms = [];
        $terms      = get_terms( [
            'taxonomy' => 'doc_category'
        ] );
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $docs_terms[$term->term_id] = $term->name;
            }
        }

        $docs_terms = GlobalFields::normalize_fields( $docs_terms );

        return $docs_terms;
    }

    /**
     * Get All Pages
     * @return array
     */
    public function get_pages_for_ia() {
        $pages = $this->database->get_cache( 'betterdocs::instant_answer::all_pages' );

        if ( $pages ) {
            return $pages;
        }

        $allpages  = get_pages( ['post_status' => 'publish'] );
        $page_list = [];
        if ( $allpages ) {
            $page_list['all'] = 'All';
            foreach ( $allpages as $page ) {
                $page_list[$page->ID] = betterdocs()->template_helper->kses( $page->post_title );
            }

            $page_list = GlobalFields::normalize_fields( $page_list );
            if ( count( $page_list ) > 1 ) {
                $this->database->set_cache( 'betterdocs::instant_answer::all_pages', $page_list );
            }
        }
        return $page_list;
    }

    /**
     * Get All Post Type
     * @return array
     */
    public function get_all_post_types() {
        $types = $this->database->get_cache( 'betterdocs::instant_answer::types' );

        if ( $types ) {
            return $types;
        }

        $args = [
            'public'   => true,
            '_builtin' => false
        ];
        $types      = [];
        $post_types = get_post_types( $args, 'objects' );
        if ( $post_types ) {
            $types['all']  = 'All';
            $types['post'] = 'Post';
            foreach ( $post_types as $post_type ) {
                $types[$post_type->name] = $post_type->labels->name;
            }

            $types = GlobalFields::normalize_fields( $types );
            if ( count( $types ) > 2 ) {
                $this->database->set_cache( 'betterdocs::instant_answer::types', $types );
            }
        }
        return $types;
    }

    /**
     * Get All Registered Texonomy
     * @return array|void
     */
    public function get_all_registered_texonomy() {
        $taxonomies = $this->database->get_cache( 'betterdocs::instant_answer::taxonomies' );

        if ( $taxonomies ) {
            return $taxonomies;
        }

        $args = [
            'public'   => true,
            '_builtin' => false
        ];
        $_taxonomies = get_taxonomies( $args, 'objects' );
        $taxonomies  = [];
        if ( $_taxonomies ) {
            $taxonomies['all']      = 'All';
            $taxonomies['category'] = 'Post Categories';
            $taxonomies['post_tag'] = 'Post Tag';
            foreach ( $_taxonomies as $taxonomy ) {
                $taxonomies[$taxonomy->name] = $taxonomy->labels->name;
            }

            $taxonomies = GlobalFields::normalize_fields( $taxonomies );
            if ( count( $taxonomies ) > 3 ) {
                $this->database->set_cache( 'betterdocs::instant_answer::taxonomies', $taxonomies );
            }
        }
        return $taxonomies;
    }
}
