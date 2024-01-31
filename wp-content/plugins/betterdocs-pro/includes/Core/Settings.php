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
        // add_filter( 'betterdocs_settings_tab_sidebar_layout', [$this, 'tab_sidebar_layout'] );
        add_filter( 'betterdocs_shortcode_fields', [$this, 'shortcode_fields'] );
        add_filter( 'betterdocs_instant_answer_fields', [$this, 'instant_answer_revamp_fields'] );
        // add_filter( 'betterdocs_instant_answer_fields', [$this, 'instant_answer_fields'] );
        add_filter( 'betterdocs_settings_args', [$this, '_args'], 11 );
    }

    public function get_raw_field( $key, $default = null ) {
        $_settings = $this->database->get( $this->base_key, [] );

        if ( isset( $_settings[$key] ) ) {
            return $this->get_normalized_value( $key, $_settings[$key], $default );
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
        if ( method_exists( parent::class, 'v250' ) ) {
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

        $_defaults     = $this->get_default();
        $_raw_settings = $this->database->get( $this->base_key, [] );
        foreach ( $_ia_settings_migration as $key ) {
            $data = isset( $_raw_settings[$key] ) ? $_raw_settings[$key] : null;
            if ( $data !== null ) {
                if ( empty( $data ) ) {
                    $this->save( $key, $_defaults[$key] );
                }
            }
        }
    }

    public function v253() {
        if ( method_exists( parent::class, 'v253' ) ) {
            parent::v253();
        }

        $ia_options = [
            'display_ia_pages',
            'display_ia_archives',
            'display_ia_texonomy',
            'display_ia_single'
        ];

        foreach ( $ia_options as $option ) {
            if ( $this->get_raw_field( $option ) == false ) {
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
            'multiple_kb'                               => false,
            'disable_root_slug_mkb'                     => false,
            'advance_search'                            => false,
            'child_category_exclude'                    => false,
            'betterdocs_popular_docs_text'              => __( 'Popular Docs', 'betterdocs' ),
            'betterdocs_popular_docs_number'            => 10,
            'popular_keyword_limit'                     => 5,
            'search_button_text'                        => __( 'Search', 'betterdocs' ),
            'kb_based_search'                           => false,
            'article_roles'                             => ['administrator'],
            'settings_roles'                            => ['administrator'],
            'analytics_roles'                           => ['administrator'],
            'enable_content_restriction'                => false,
            'content_visibility'                        => ['all'],
            'restrict_template'                         => ['all'],
            'restrict_category'                         => ['all'],
            'restrict_kb'                               => ['all'],
            'restricted_redirect_url'                   => '',
            'reporting_frequency'                       => 'betterdocs_weekly',
            'reporting_subject'                         => wp_sprintf(
                __( 'Your Documentation Performance of %s Website', 'betterdocs-pro' ),
                get_bloginfo( 'name' )
            ),
            'select_reporting_data'                     => ['overview', 'top-docs', 'most-search'],
            'archive_nested_subcategory'                => true,

            'enable_disable'                            => true,
            'ia_enable_preview'                         => true,
            'content_type'                              => 'docs',
            'docs_list'                                 => [],
            'doc_category_list'                         => [],
            'doc_category_limit'                        => 10,
            'display_ia_pages'                          => ['all'],
            'display_ia_archives'                       => ['all'],
            'display_ia_texonomy'                       => ['all'],
            'display_ia_single'                         => ['all'],
            'ask_subject'                               => '[ia_subject]',
            'ask_email'                                 => get_bloginfo( 'admin_email' ),
            'ask_thanks_title'                          => __( 'Thanks', 'betterdocs-pro' ),
            'ask_thanks_text'                           => __( 'Your Message Has Been Sent Successfully', 'betterdocs-pro' ),
            'launcher_open_icon'                        => [],
            'launcher_close_icon'                       => [],
            'search_visibility_switch'                  => false,
            'search_placeholder_text'                   => __( 'Search...', 'betterdocs-pro' ),
            'chat_tab_visibility_switch'                => true,
            'chat_tab_icon'                             => [],
            'chat_tab_title'                            => __( 'Ask', 'betterdocs-pro' ),
            'chat_subtitle_one'                         => __( 'Need a hand? Shoot us a message.', 'betterdocs-pro' ),
            'chat_subtitle_two'                         => __( 'We typically respond within 24-48 hours. Your solution is just a message away.', 'betterdocs-pro' ),
            'ia_reaction'                               => true,
            'reaction_title'                            => __( 'How did you feel?', 'betterdocs-pro' ),
            'response_title'                            => __( 'Thanks for the feedback', 'betterdocs-pro' ),
            'ia_branding'                               => true,
            'chat_position'                             => 'right',
            'chat_zindex'                               => 9999,
            'search_not_found_1'                        => __( 'Oops...', 'betterdocs-pro' ),
            'search_not_found_2'                        => __( 'We couldn’t find any docs that match your search. Try searching for a new term.', 'betterdocs-pro' ),
            'ia_luncher_bg'                             => '#00b682',
            'ia_luncher_bg_hover'                       => '#00b682;',
            'ia_color_title'                            => '',
            'ia_accent_color'                           => '#19ca9e',
            'ia_sub_accent_color'                       => '#16b38c',
            'ia_heading_font_size'                      => 19,
            'ia_heading_color'                          => '#FFFFFF',
            'ia_sub_heading_size'                       => 12,
            'ia_sub_heading_color'                      => '#FFFFFF', //No color was set by default in the previous version
            'ia_searchbox_bg'                           => '#FFFFFF',
            'ia_searc_icon_color'                       => '#FFFFFF',
            'ia_searchbox_text'                         => '#2c3338',
            'iac_article_bg'                            => '', //does not contain any default value on default selectors(in previous version as well)
            'iac_article_title_size'                    => 16,
            'iac_article_title'                         => '#1d2327',
            'iac_article_content_size'                  => 16,
            'iac_article_content'                       => '', //does not contain any default value on default selectors(in previous version as well)
            'ia_feedback_title_size'                    => 14,
            'ia_feedback_title_color'                   => '', //does not contain any default value on default selectors(in previous version as well)
            'ia_feedback_icon_size'                     => 15,
            'ia_feedback_icon_color'                    => '#FFFFFF',
            'ia_response_icon_size'                     => 24, //the selector is unknown and does not work in the previous version as well, left it 0
            'ia_response_title_size'                    => 13, //the selector is unknown and does not work in the previous version as well, left it 25
            'ia_response_title_color'                   => '', //the selector is unknown and does not work in the previous version as well, left it empty
            'ia_response_icon_color'                    => '', //the selector is unknown and does not work in the previous version as well(where the key was empty), left it empty
            'ia_ask_bg_color'                           => '#FFFFFF',
            'ia_ask_input_foreground'                   => '#939eaa',
            'ia_ask_send_disable_button_bg'             => '#19ca9e', //does not work in previous version, problem might exist
            'ia_ask_send_disable_button_hover_bg'       => '#19ca9e', //does not work in previous version, problem might exist
            'ia_ask_send_button_bg'                     => '#19ca9e', //does not work in previous version, problem might exist
            'ia_ask_send_button_hover_bg'               => '#19ca9e',
            'content_heading_tag'                       => '',
            'iac_docs_title_font_size'                  => 20,
            'iac_article_content_h1'                    => 26,
            'iac_article_content_h2'                    => 24,
            'iac_article_content_h3'                    => 22,
            'iac_article_content_h4'                    => 20,
            'iac_article_content_h5'                    => 18,
            'iac_article_content_h6'                    => 16,
            'iac_article_content_p'                     => 14,
            'ia_resources_doc_categories'               => [],
            // 'ia_resources_doc_categories_number'        => 5,
            'ia_resources_faq_group'                    => [],
            'ia_resources_faq_list'                     => [],
            'ia_resources_doc_categories_switch'        => true,
            'ia_resources_faq_switch'                   => true,
            'ia_resources_faq_content_type'             => 'faq-list',
            // 'ia_resources_faq_group_number'             => 5,
            // 'ia_resources_faq_list_number'              => 5,
            'ia_resources_doc_category_title_text'      => __( 'Doc Categories', 'betterdcocs' ),
            'ia_resources_faq_title'                    => __( 'FAQ', 'betterdcocs' ),
            'home_tab_title'                            => __( 'Home', 'betterdocs' ),
            'home_content_title'                        => __( 'Get Instant Help', 'betterdcocs' ),
            'home_content_subtitle'                     => __( 'Need any assistance? Get quick solutions to any problems you face.', 'betterdocs' ),
            'ia_resources_general_content_title'        => __( 'Resources', 'betterdocs' ),
            'ia_resource_general_tab_title'             => __( 'Resources', 'betterdocs' ),
            'ia_card_title_color'                       => '#111213',
            'ia_card_title_background_color'            => '#FFFFFF',
            'ia_card_title_list_color'                  => '#111213',
            'ia_card_list_description_color'            => '#6D7175',
            'ia_card_list_background_color'             => '#FFFFFF',
            // 'ia_card_list_arrow_color'                  => '#19ca9e',
            // 'ia_doc_title_color'                                   => '#111213',
            'ia_search_box_placeholder_text_color'      => '#1c1c1c',
            'ia_search_box_input_text_color'            => '#000000',
            'ia_message_tab_title_font_color'           => '#FFFFFF',
            'ia_message_tab_subtitle_font_color'        => '#FFFFFF',
            // 'ia_message_button_background_color'        => '#00B682',
            'ia_message_button_text_color'              => '#FFFFFF',
            'ia_message_input_label_text_color'         => '#202223',
            'ia_message_upload_button_background_color' => '#FFFFFF',
            'ia_message_upload_text_color'              => '#6d7175',
            'ia_launcher_tabs_background_color'         => '#FFFFFF',
            'ia_launcher_tabs_text_color'               => '#202223',
            'ia_launcher_active_tab_text_color'         => '#16CA9E',
            // 'header_background_color'                   => '#00b682',
            'ia_single_doc_title_font_color'            => '#111213',
            'ia_single_title_header_font_color'         => '#111213',
            'ia_single_doc_title_header_bg_color'       => '#F6F6F7',
            // 'ia_single_doc_back_icon_color'             => '#16CA9E',
            'ia_single_doc_back_icon_hover_color'       => '#d6ddd9',
            // 'ia_single_expand_icon_color'               => '#16CA9E',
            'ia_single_expand_icon_hover_color'         => '#16CA9E',
            'ia_single_icons_bg_color'                  => '#f6f6f7',
            'ia_reaction_primary_color'                 => '#00A375',
            'ia_reaction_secondary_color'               => '#FFFFFF',
            'ia_reaction_title_color'                   => '#FAFAFA',
            'header_background_image'                   => [],
            // 'upload_header_logo'                        => [],
            'upload_home_icon'                          => [],
            'upload_sendmessage_icon'                   => [],
            'upload_resource_icon'                      => [],
            'ia_terms_orderby'                          => 'name',
            'ia_terms_order'                            => 'asc',
            // 'ia_docs_order_by'                          => 'ID',
            // 'ia_docs_order'                             => 'asc',
            'faq_terms_orderby'                         => 'name',
            'faq_terms_order'                           => 'asc',
            'ia_faq_list_order_by'                      => 'id',
            'ia_faq_list_order'                         => 'asc',
            // 'ia_resources_doc_category_title_text_color'           => '#19ca9e',
            // 'ia_resources_doc_category_card_title_color'           => '#19ca9e',
            // 'ia_resources_doc_category_card_list_color'            => '#19ca9e',
            // 'ia_resources_doc_category_card_list_background_color' => '#19ca9e',
            // 'ia_resources_doc_category_card_arrow_color'           => '#19ca9e'
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

    public function shortcode_fields( $args ) {
        $args['category_box_l3_shortcode'] = [
            'name'                => 'category_box_l3_shortcode',
            'type'                => 'copy-to-clipboard',
            'label'               => __( 'Category Box- Layout 3', 'betterdocs-pro' ),
            'default'             => '[betterdocs_category_box_2]',
            'readOnly'            => true,
            'priority'            => 6,
            'description'         => __( '[betterdocs_category_box_2 column="" nested_subcategory="" terms="" terms_orderby="" kb_slug="" multiple_knowledge_base="false" title_tag="h2"]', 'betterdocs-pro' ),
            'descriptionLabel'    => __( 'Example with parameters:', 'betterdocs' ),
            'descriptionCopyable' => true
        ];
        $args['category_grid_2_shortcode'] = [
            'name'                => 'category_grid_2_shortcode',
            'type'                => 'copy-to-clipboard',
            'label'               => __( 'Category Grid- Layout 4', 'betterdocs-pro' ),
            'default'             => '[betterdocs_category_grid_2]',
            'readOnly'            => true,
            'priority'            => 7,
            'description'         => __( '[betterdocs_category_grid_2 orderby="" order="" masonry="" column="" posts="" nested_subcategory="" terms="" kb_slug="" terms_orderby="" terms_order="" multiple_knowledge_base="false" title_tag="h2"]', 'betterdocs-pro' ),
            'descriptionLabel'    => __( 'Example with parameters:', 'betterdocs' ),
            'descriptionCopyable' => true
        ];
        $args['multiple_kb_shortcode'] = [
            'name'                => 'multiple_kb_shortcode',
            'type'                => 'copy-to-clipboard',
            'label'               => __( 'Multiple KB- Layout 1', 'betterdocs-pro' ),
            'default'             => '[betterdocs_multiple_kb]',
            'readOnly'            => true,
            'priority'            => 8,
            'description'         => __( '[betterdocs_multiple_kb column="" terms="" title_tag="h2"]', 'betterdocs-pro' ),
            'descriptionLabel'    => __( 'Example with parameters:', 'betterdocs' ),
            'descriptionCopyable' => true
        ];
        $args['multiple_kb_2_shortcode'] = [
            'name'                => 'multiple_kb_2_shortcode',
            'type'                => 'copy-to-clipboard',
            'label'               => __( 'Multiple KB- Layout 2', 'betterdocs-pro' ),
            'default'             => '[betterdocs_multiple_kb_2]',
            'readOnly'            => true,
            'priority'            => 9,
            'description'         => __( '[betterdocs_multiple_kb_2 column="" terms="" title_tag="h2"]', 'betterdocs-pro' ),
            'descriptionLabel'    => __( 'Example with parameters:', 'betterdocs' ),
            'descriptionCopyable' => true
        ];
        $args['multiple_kb_3_shortcode'] = [
            'name'                => 'multiple_kb_3_shortcode',
            'type'                => 'copy-to-clipboard',
            'label'               => __( 'Multiple KB- Layout 3', 'betterdocs-pro' ),
            'default'             => '[betterdocs_multiple_kb_list]',
            'readOnly'            => true,
            'priority'            => 10,
            'description'         => __( '[betterdocs_multiple_kb_list terms="" title_tag="h2"]', 'betterdocs-pro' ),
            'descriptionLabel'    => __( 'Example with parameters:', 'betterdocs' ),
            'descriptionCopyable' => true
        ];
        $args['multiple_kb_4_shortcode'] = [
            'name'                => 'multiple_kb_4_shortcode',
            'type'                => 'copy-to-clipboard',
            'label'               => __( 'Multiple KB- Layout 4', 'betterdocs-pro' ),
            'default'             => '[betterdocs_multiple_kb_tab_grid]',
            'readOnly'            => true,
            'priority'            => 11,
            'description'         => __( '[betterdocs_multiple_kb_tab_grid terms="" terms_orderby="" terms_order="" orderby="" order="" posts_per_page="" title_tag="h2"]', 'betterdocs-pro' ),
            'descriptionLabel'    => __( 'Example with parameters:', 'betterdocs' ),
            'descriptionCopyable' => true
        ];
        $args['mkb_popular_docs'] = [
            'name'                => 'mkb_popular_docs',
            'type'                => 'copy-to-clipboard',
            'label'               => __( 'Popular Docs', 'betterdocs-pro' ),
            'default'             => '[betterdocs_popular_articles]',
            'readOnly'            => true,
            'priority'            => 12,
            'description'         => __( '[betterdocs_popular_articles post_per_page="" title="Popular Docs" title_tag="h2" multiple_knowledge_base="false"]', 'betterdocs-pro' ),
            'descriptionLabel'    => __( 'Example with parameters:', 'betterdocs' ),
            'descriptionCopyable' => true
        ];

        return $args;
    }

    public function instant_answer_revamp_fields( $args ) {

        /**
         * I/A Revamped Fields
         *
         * @param array $args
         * @return void
         */
        $args['instant_answer_tab'] = [
            'id'              => 'instant_answer_tab',
            'name'            => 'instant_answer_tab',
            'classes'         => 'tab-layout',
            'type'            => "tab",
            'label'           => __( 'Instant Answer', 'betterdocs-pro' ),
            'active'          => "initial_content_type_settings",
            'default'         => "initial_content_type_settings",
            'completionTrack' => true,
            'sidebar'         => false,
            'save'            => false,
            'priority'        => 20,
            'submit'          => [
                'show' => false
            ],
            'step'            => [
                'show' => false
            ],
            'rules'           => Rules::is( 'enable_disable', true ),
            'fields'          => [
                'initial_content_type_settings'    => [
                    'id'       => 'initial_content_type_settings',
                    'name'     => 'initial_content_type_settings',
                    'type'     => 'section',
                    'label'    => __( "Initial Settings", 'betterdocs-pro' ),
                    'priority' => 1,
                    'fields'   => [
                        'intial-content-tab' => [
                            'id'              => 'intial_content_tab',
                            'name'            => 'intial_content_tab',
                            'label'           => __( 'Content', 'betterdocs' ),
                            'classes'         => 'tab-nested-layout',
                            'type'            => "tab",
                            'active'          => "home_content",
                            'completionTrack' => true,
                            'sidebar'         => false,
                            'save'            => false,
                            'title'           => false,
                            'config'          => [
                                'active'  => 'home_content',
                                'sidebar' => false,
                                'title'   => false
                            ],
                            'submit'          => [
                                'show' => false
                            ],
                            'step'            => [
                                'show' => false
                            ],
                            'priority'        => 1,
                            'fields'          => [
                                'home-content'  => [
                                    'id'       => 'home_content',
                                    'name'     => 'home_content',
                                    'type'     => "section",
                                    'label'    => __( 'Content', 'betterdocs' ),
                                    'priority' => 1,
                                    'fields'   => [
                                        'ia_enable_preview'   => [
                                            'name'                       => 'ia_enable_preview',
                                            'type'                       => 'toggle',
                                            'priority'                   => 1,
                                            'label'                      => __( 'Live Preview', 'betterdocs-pro' ),
                                            'enable_disable_text_active' => true,
                                            'default'                    => true,
                                            'is_pro'                     => true
                                        ],
                                        'display_ia_pages'    => [
                                            'name'     => 'display_ia_pages',
                                            'type'     => 'checkbox-select',
                                            'label'    => __( 'Show on Page', 'betterdocs-pro' ),
                                            'priority' => 2,
                                            'multiple' => true,
                                            'default'  => ['all'],
                                            'options'  => $this->get_pages_for_ia()
                                        ],
                                        'display_ia_archives' => [
                                            'name'     => 'display_ia_archives',
                                            'type'     => 'checkbox-select',
                                            'label'    => __( 'Show on Archive Templates', 'betterdocs-pro' ),
                                            'priority' => 3,
                                            'multiple' => true,
                                            'default'  => ['all'],
                                            'options'  => $this->get_all_post_types()
                                        ],
                                        'display_ia_texonomy' => [
                                            'name'     => 'display_ia_texonomy',
                                            'type'     => 'checkbox-select',
                                            'label'    => __( 'Show on Taxonomy Templates', 'betterdocs-pro' ),
                                            'priority' => 4,
                                            'multiple' => true,
                                            'default'  => ['all'],
                                            'options'  => $this->get_all_registered_texonomy()
                                        ],
                                        'display_ia_single'   => [
                                            'name'     => 'display_ia_single',
                                            'type'     => 'checkbox-select',
                                            'label'    => __( 'Show on Single Pages', 'betterdocs-pro' ),
                                            'priority' => 5,
                                            'multiple' => true,
                                            'default'  => ['all'],
                                            'options'  => $this->get_all_post_types()
                                        ]
                                    ]
                                ],
                                'style-content' => [
                                    'id'       => 'style_content',
                                    'name'     => 'style_content',
                                    'type'     => "section",
                                    'label'    => __( 'Style', 'betterdocs' ),
                                    'priority' => 2,
                                    'fields'   => [
                                        'intial-content-style' => [
                                            'id'              => 'intial_content_style',
                                            'name'            => 'intial_content_style',
                                            'label'           => __( 'Style', 'betterdocs' ),
                                            'classes'         => 'tab-nested-layout',
                                            'type'            => "tab",
                                            'active'          => "launcher_style",
                                            'completionTrack' => true,
                                            'sidebar'         => false,
                                            'save'            => false,
                                            'title'           => false,
                                            'config'          => [
                                                'active'  => 'launcher_style',
                                                'sidebar' => false,
                                                'title'   => false
                                            ],
                                            'submit'          => [
                                                'show' => false
                                            ],
                                            'step'            => [
                                                'show' => false
                                            ],
                                            'priority'        => 1,
                                            'fields'          => [
                                                'launcher-style' => [
                                                    'id'       => 'launcher_style',
                                                    'name'     => 'launcher_style',
                                                    'type'     => "section",
                                                    'label'    => __( 'Common', 'betterdocs' ),
                                                    'priority' => 1,
                                                    'fields'   => [
                                                        'chat_position'       => [
                                                            'name'     => 'chat_position',
                                                            'label'    => __( "I/A Appearance Position", "betterdocs" ),
                                                            "type"     => 'select',
                                                            'priority' => 1,
                                                            'default'  => '',
                                                            'options'  => GlobalFields::normalize_fields( [
                                                                'left'  => __( 'Left', 'betterdocs-pro' ),
                                                                'right' => __( 'Right', 'betterdocs-pro' )
                                                            ] )
                                                        ],
                                                        'ia_luncher_bg'       => [
                                                            'name'       => 'ia_luncher_bg',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Primary Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#00b682',
                                                            'priority'   => 3
                                                        ],
                                                        'ia_luncher_bg_hover' => [
                                                            'name'     => 'ia_luncher_bg_hover',
                                                            'type'     => 'colorpicker',
                                                            'label'    => __( 'Launcher Hover Background Color', 'betterdocs-pro' ),
                                                            'default'  => '#00b682',
                                                            'priority' => 4
                                                        ],
                                                        'launcher_open_icon'  => [
                                                            'name'     => 'launcher_open_icon',
                                                            'type'     => 'media',
                                                            'value'    => '',
                                                            'label'    => __( 'Instant Answer Open Icon', 'betterdocs-pro' ),
                                                            'priority' => 5
                                                        ],
                                                        'launcher_close_icon' => [
                                                            'name'     => 'launcher_close_icon',
                                                            'type'     => 'media',
                                                            'value'    => '',
                                                            'label'    => __( 'Instant Answer Close Icon', 'betterdocs-pro' ),
                                                            'priority' => 6
                                                        ]
                                                    ]
                                                ],
                                                'header-style'   => [
                                                    'id'       => 'header_style',
                                                    'name'     => 'header_style',
                                                    'type'     => "section",
                                                    'label'    => __( 'Header', 'betterdocs' ),
                                                    'priority' => 2,
                                                    'fields'   => [
                                                        // 'header_background_color' => [
                                                        //     'name'       => 'header_background_color',
                                                        //     'type'       => 'colorpicker',
                                                        //     'label'      => __( 'Background Color', 'betterdocs-pro' ),
                                                        //     'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                        //     'default'    => '#00b682',
                                                        //     'priority'   => 1
                                                        // ],
                                                        'header_background_image' => [
                                                            'name'     => 'header_background_image',
                                                            'type'     => 'media',
                                                            'value'    => '',
                                                            'label'    => __( 'Upload Background Image', 'betterdocs-pro' ),
                                                            'priority' => 1
                                                        ],
                                                        // 'upload_header_logo'      => [
                                                        //     'name'     => 'upload_header_logo',
                                                        //     'type'     => 'media',
                                                        //     'value'    => '',
                                                        //     'label'    => __( 'Upload Logo', 'betterdocs-pro' ),
                                                        //     'priority' => 2
                                                        // ],
                                                        'ia_heading_color'        => [
                                                            'name'       => 'ia_heading_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Title Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#fff',
                                                            'priority'   => 3
                                                        ],
                                                        'ia_sub_heading_color'    => [
                                                            'name'       => 'ia_sub_heading_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Subtitle Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#fff',
                                                            'priority'   => 4
                                                        ]
                                                    ]
                                                ],
                                                'card-style'     => [
                                                    'id'       => 'card_style',
                                                    'name'     => 'card_style',
                                                    'type'     => "section",
                                                    'label'    => __( 'Card', 'betterdocs' ),
                                                    'priority' => 3,
                                                    'fields'   => [
                                                        'ia_card_title_color'            => [
                                                            'name'       => 'ia_card_title_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Title Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#111213',
                                                            'priority'   => 1
                                                        ],
                                                        'ia_card_title_background_color' => [
                                                            'name'       => 'ia_card_title_background_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Title Background Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#fff',
                                                            'priority'   => 2
                                                        ],
                                                        'ia_card_title_list_color'       => [
                                                            'name'       => 'ia_card_title_list_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'List Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#111213',
                                                            'priority'   => 3
                                                        ],
                                                        'ia_card_list_description_color' => [
                                                            'name'       => 'ia_card_list_description_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'List Description Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#6D7175',
                                                            'priority'   => 4
                                                        ],
                                                        'ia_card_list_background_color'  => [
                                                            'name'       => 'ia_card_list_background_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'List Background Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#fff',
                                                            'priority'   => 5
                                                        ]
                                                        // 'ia_card_list_arrow_color'       => [
                                                        //     'name'       => 'ia_card_list_arrow_color',
                                                        //     'type'       => 'colorpicker',
                                                        //     'label'      => __( 'Arrow Color', 'betterdocs-pro' ),
                                                        //     'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                        //     'default'    => '#19ca9e',
                                                        //     'priority'   => 6
                                                        // ]
                                                    ]
                                                ],
                                                'search-style'   => [
                                                    'id'       => 'search_style',
                                                    'name'     => 'search_style',
                                                    'type'     => "section",
                                                    'label'    => __( 'Search', 'betterdocs' ),
                                                    'priority' => 4,
                                                    'fields'   => [
                                                        'ia_searchbox_bg'                      => [
                                                            'name'       => 'ia_searchbox_bg',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Background Color', "betterdocs-pro" ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#fff',
                                                            'priority'   => 1
                                                        ],
                                                        'ia_search_box_placeholder_text_color' => [
                                                            'name'       => 'ia_search_box_placeholder_text_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Placeholder Text Color', "betterdocs-pro" ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#1c1c1c',
                                                            'priority'   => 2
                                                        ],
                                                        'ia_search_box_input_text_color'       => [
                                                            'name'       => 'ia_search_box_input_text_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Input Text Color', "betterdocs-pro" ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#000000',
                                                            'priority'   => 3
                                                        ],
                                                        'ia_searc_icon_color'                  => [
                                                            'name'       => 'ia_searc_icon_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Icon Color', "betterdocs-pro" ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#FFFFFF',
                                                            'priority'   => 4
                                                        ]
                                                    ]
                                                ],
                                                'tabs-style'     => [
                                                    'id'       => 'tabs_style',
                                                    'name'     => 'tabs_style',
                                                    'type'     => "section",
                                                    'label'    => __( 'Tabs', 'betterdocs' ),
                                                    'priority' => 5,
                                                    'fields'   => [
                                                        'ia_launcher_tabs_background_color' => [
                                                            'name'       => 'ia_launcher_tabs_background_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Background Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#FFF',
                                                            'priority'   => 1
                                                        ],
                                                        'ia_launcher_tabs_text_color'       => [
                                                            'name'       => 'ia_launcher_tabs_text_color',
                                                            'type'       => 'colorpicker',
                                                            'label'      => __( 'Text Color', 'betterdocs-pro' ),
                                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                                            'default'    => '#202223',
                                                            'priority'   => 2
                                                        ],
                                                        'upload_home_icon'                  => [
                                                            'name'     => 'upload_home_icon',
                                                            'type'     => 'media',
                                                            'value'    => '',
                                                            'label'    => __( 'Upload Home Icon', 'betterdocs-pro' ),
                                                            'priority' => 3
                                                        ],
                                                        'upload_sendmessage_icon'           => [
                                                            'name'     => 'upload_sendmessage_icon',
                                                            'type'     => 'media',
                                                            'value'    => '',
                                                            'label'    => __( 'Upload Send Message Icon', 'betterdocs-pro' ),
                                                            'priority' => 4
                                                        ],
                                                        'upload_resource_icon'              => [
                                                            'name'     => 'upload_resource_icon',
                                                            'type'     => 'media',
                                                            'value'    => '',
                                                            'label'    => __( 'Upload Resource Icon', 'betterdocs-pro' ),
                                                            'priority' => 5
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'ia_home_settings'                 => [
                    'id'       => 'ia_home_settings',
                    'name'     => 'ia_home_settings',
                    'type'     => 'section',
                    'label'    => __( "Home", "betterdocs-pro" ),
                    'priority' => 2,
                    'fields'   => [
                        'content_type'            => [
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
                        'docs_list'               => [
                            'name'     => 'docs_list',
                            'label'    => __( 'Select Docs', 'betterdocs-pro' ),
                            'type'     => 'checkbox-select',
                            'priority' => 2,
                            'multiple' => true,
                            'search'   => true,
                            'options'  => array_merge( [['value' => 'all', 'label' => 'All']], $this->docs() ),
                            'rules'    => Rules::includes( 'content_type', 'docs' )
                        ],
                        'doc_category_list'       => [
                            'name'     => 'doc_category_list',
                            'label'    => __( 'Select Docs Categories', 'betterdocs-pro' ),
                            'type'     => 'checkbox-select',
                            'priority' => 3,
                            'multiple' => true,
                            'options'  => array_merge( [['value' => 'all', 'label' => 'All']], $this->docs_categories() ),
                            'rules'    => Rules::includes( 'content_type', 'docs_categories' )
                        ],
                        'doc_category_limit'      => [
                            'name'     => 'doc_category_limit',
                            'type'     => 'number',
                            'label'    => __( 'Number Of Categories', 'betterdocs-pro' ),
                            'default'  => 10,
                            'priority' => 4,
                            'rules'    => Rules::logicalRule( [
                                Rules::is( 'content_type', 'docs_categories' )
                            ] )
                        ],
                        'home_tab_title'          => [
                            'name'     => 'home_tab_title',
                            'type'     => 'text',
                            'label'    => __( 'Tab Title', 'betterdocs-pro' ),
                            'priority' => 6,
                            'default'  => __( 'Home', 'betterdocs-pro' )
                        ],
                        'home_content_title'      => [
                            'name'     => 'home_content_title',
                            'type'     => 'text',
                            'label'    => __( 'Title', 'betterdocs-pro' ),
                            'priority' => 7,
                            'default'  => __( 'Get Instant Help', 'betterdocs-pro' )
                        ],
                        'home_content_subtitle'   => [
                            'name'     => 'home_content_subtitle',
                            'type'     => 'text',
                            'label'    => __( 'Subtitle', 'betterdocs-pro' ),
                            'priority' => 8,
                            'default'  => __( 'Need any assistance? Get quick solutions to any problems you face.', 'betterdocs-pro' )
                        ],
                        // 'home_message_title'      => [
                        //     'name'     => 'home_message_title',
                        //     'type'     => 'text',
                        //     'label'    => __( 'Message Title', 'betterdocs-pro' ),
                        //     'priority' => 7,
                        //     'default'  => __( 'Subtitle...', 'betterdocs-pro' )
                        // ],
                        // 'home_message_subtitle'   => [
                        //     'name'     => 'home_message_subtitle',
                        //     'type'     => 'text',
                        //     'label'    => __( 'Message Subtitle', 'betterdocs-pro' ),
                        //     'priority' => 8,
                        //     'default'  => __( 'Message Subtitle...', 'betterdocs-pro' )
                        // ],
                        // 'home_card_title'         => [
                        //     'name'     => 'home_card_title',
                        //     'type'     => 'text',
                        //     'label'    => __( 'Card Title', 'betterdocs-pro' ),
                        //     'priority' => 9,
                        //     'default'  => __( 'Card Title...', 'betterdocs-pro' )
                        // ],
                        // 'search_visibility_switch' => [
                        //     'name'        => 'search_visibility_switch',
                        //     'type'        => 'toggle',
                        //     'label'       => __( 'Search', 'betterdocs-pro' ),
                        //     'description' => __( 'Disable Search', 'betterdocs-pro' ),
                        //     'priority'    => 10
                        // ],
                        'search_placeholder_text' => [
                            'name'     => 'search_placeholder_text',
                            'type'     => 'text',
                            'label'    => __( 'Search Placeholder', 'betterdocs-pro' ),
                            'priority' => 9,
                            'default'  => __( 'Search...', 'betterdocs-pro' )
                        ],
                        'search_not_found_1'      => [
                            'name'     => 'search_not_found_1',
                            'type'     => 'text',
                            'label'    => __( 'Docs not Found Title', 'betterdocs-pro' ),
                            'priority' => 10,
                            'default'  => __( 'Oops...', 'betterdocs-pro' )
                        ],
                        'search_not_found_2'      => [
                            'name'     => 'search_not_found_2',
                            'type'     => 'textarea',
                            'label'    => __( 'Docs not Found Subtitle', 'betterdocs-pro' ),
                            'priority' => 11,
                            'default'  => __( 'We couldn’t find any docs that match your search. Try searching for a new term.', 'betterdocs-pro' )
                        ]
                    ]
                ],
                'ia_message_settings'              => [
                    'id'       => 'ia_message_settings',
                    'name'     => 'ia_message_settings',
                    'type'     => 'section',
                    'label'    => __( 'Message', 'betterdocs-pro' ),
                    'priority' => 3,
                    'fields'   => [
                        'ia-message-content' => [
                            'id'              => 'ia-message-content',
                            'name'            => 'ia-message-content',
                            'label'           => __( 'Content', 'betterdocs' ),
                            'classes'         => 'tab-nested-layout',
                            'type'            => "tab",
                            'active'          => "ia-message-tab-content",
                            'completionTrack' => true,
                            'sidebar'         => false,
                            'save'            => false,
                            'title'           => false,
                            'config'          => [
                                'active'  => 'ia-message-tab-content',
                                'sidebar' => false,
                                'title'   => false
                            ],
                            'submit'          => [
                                'show' => false
                            ],
                            'step'            => [
                                'show' => false
                            ],
                            'priority'        => 1,
                            'fields'          => [
                                'ia-message-tab-content'      => [
                                    'id'       => 'ia-message-tab-content',
                                    'name'     => 'ia-message-tab-content',
                                    'type'     => "section",
                                    'label'    => __( 'Content', 'betterdocs' ),
                                    'priority' => 1,
                                    'fields'   => [
                                        'chat_tab_visibility_switch' => [
                                            'name'                       => 'chat_tab_visibility_switch',
                                            'type'                       => 'toggle',
                                            'label'                      => __( 'Ask Tab', 'betterdocs-pro' ),
                                            'enable_disable_text_active' => true,
                                            'default'                    => true,
                                            'priority'                   => 1
                                        ],
                                        'chat_tab_title'             => [
                                            'name'     => 'chat_tab_title',
                                            'type'     => 'text',
                                            'label'    => __( 'Instant Ask Tab Title', 'betterdocs-pro' ),
                                            'priority' => 2,
                                            'default'  => __( 'Ask', 'betterdocs-pro' ),
                                            'rules'    => Rules::is( 'chat_tab_visibility_switch', true )
                                        ],
                                        'chat_subtitle_one'          => [
                                            'name'     => 'chat_subtitle_one',
                                            'type'     => 'text',
                                            'label'    => __( 'Ask Tab Subtitle One', 'betterdocs-pro' ),
                                            'priority' => 3,
                                            'default'  => __( 'Need a hand? Shoot us a message.', 'betterdocs-pro' ),
                                            'rules'    => Rules::is( 'chat_tab_visibility_switch', true )
                                        ],
                                        'chat_subtitle_two'          => [
                                            'name'     => 'chat_subtitle_two',
                                            'type'     => 'text',
                                            'label'    => __( 'Ask Tab Subtitle Two', 'betterdocs-pro' ),
                                            'priority' => 4,
                                            'default'  => __( 'We typically respond within 24-48 hours. Your solution is just a message away.', 'betterdocs-pro' ),
                                            'rules'    => Rules::is( 'chat_tab_visibility_switch', true )
                                        ]
                                    ]
                                ],
                                'ia-message-tab-style'        => [
                                    'id'       => 'ia-message-tab-style',
                                    'name'     => 'ia-message-tab-style',
                                    'type'     => 'section',
                                    'label'    => __( 'Style', "betterdocs-pro" ),
                                    'priotity' => 2,
                                    'fields'   => [
                                        'ia_message_tab_title_font_color'           => [
                                            'name'       => 'ia_message_tab_title_font_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Title Font Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#fff',
                                            'priority'   => 1
                                        ],
                                        'ia_message_tab_subtitle_font_color'        => [
                                            'name'       => 'ia_message_tab_subtitle_font_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Subtitle Font Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#fff',
                                            'priority'   => 2
                                        ],
                                        // 'ia_message_button_background_color'        => [
                                        //     'name'       => 'ia_message_button_background_color',
                                        //     'type'       => 'colorpicker',
                                        //     'label'      => __( 'Button Background Color', 'betterdocs-pro' ),
                                        //     'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                        //     'default'    => '#00B682',
                                        //     'priority'   => 4
                                        // ],
                                        'ia_message_button_text_color'              => [
                                            'name'       => 'ia_message_button_text_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Button Text Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#fff',
                                            'priority'   => 3
                                        ],
                                        'ia_ask_bg_color'                           => [
                                            'name'       => 'ia_ask_bg_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Input Background Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#fff',
                                            'priority'   => 4
                                        ],
                                        'ia_message_input_label_text_color'         => [
                                            'name'       => 'ia_message_input_label_text_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Input Label Text Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#202223',
                                            'priority'   => 5
                                        ],
                                        'ia_message_upload_button_background_color' => [
                                            'name'       => 'ia_message_upload_button_background_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Upload Button Background Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#fff',
                                            'priority'   => 6
                                        ],
                                        'ia_message_upload_text_color'              => [
                                            'name'       => 'ia_message_upload_text_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Upload Text Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#6d7175',
                                            'priority'   => 7
                                        ]
                                    ]
                                ],
                                'ia-message-mail-settings'    => [
                                    'id'       => 'ia-message-mail-settings',
                                    'name'     => 'ia-message-mail-settings',
                                    'type'     => 'section',
                                    'label'    => __( 'Mail Settings', "betterdocs-pro" ),
                                    'priority' => 3,
                                    'fields'   => [
                                        'ask_subject' => [
                                            'name'           => 'ask_subject',
                                            'type'           => 'text',
                                            'label'          => __( 'Subject', 'betterdocs-pro' ),
                                            'priority'       => 1,
                                            'default'        => __( '[ia_subject]', 'betterdocs-pro' ),
                                            'label_subtitle' => __( 'You can use [ia_subject], [ia_email], [ia_name] as placeholder. i.e: An enquiry is placed By [ia_name] for [ia_subject].' )
                                        ],
                                        'ask_email'   => [
                                            'name'     => 'ask_email',
                                            'type'     => 'text',
                                            'label'    => __( 'Email Address', 'betterdocs-pro' ),
                                            'priority' => 2,
                                            'default'  => get_bloginfo( 'admin_email' )
                                        ]
                                    ]
                                ],
                                'ia-message-success-settings' => [
                                    'id'       => 'ia-message-success-settings',
                                    'name'     => 'ia-message-success-settings',
                                    'type'     => 'section',
                                    'label'    => __( 'Success Screen', "betterdocs-pro" ),
                                    'priority' => 4,
                                    'fields'   => [
                                        'ask_thanks_title' => [
                                            'name'     => 'ask_thanks_title',
                                            'type'     => 'text',
                                            'label'    => __( 'Success Message Title', 'betterdocs-pro' ),
                                            'priority' => 1,
                                            'default'  => __( 'Thanks', 'betterdocs-pro' )
                                        ],
                                        'ask_thanks_text'  => [
                                            'name'     => 'ask_thanks_text',
                                            'type'     => 'textarea',
                                            'label'    => __( 'Success Message Text', 'betterdocs-pro' ),
                                            'priority' => 2,
                                            'default'  => __( 'Your Message Has Been Sent Successfully', 'betterdocs-pro' )
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'ia_resources_settings'            => [
                    'id'       => 'ia_resources_settings',
                    'name'     => 'ia_resources_settings',
                    'type'     => 'section',
                    'priority' => 4,
                    'label'    => __( 'Resources', "betterdocs-pro" ),
                    'fields'   => [
                        'ia-resources-general' => [
                            'id'              => 'ia-resources-general',
                            'name'            => 'ia-resources-general',
                            'label'           => __( 'General', 'betterdocs' ),
                            'classes'         => 'tab-nested-layout',
                            'type'            => "tab",
                            'active'          => "ia-resources-general-tab",
                            'completionTrack' => true,
                            'sidebar'         => false,
                            'save'            => false,
                            'title'           => false,
                            'config'          => [
                                'active'  => 'ia-resources-general-tab',
                                'sidebar' => false,
                                'title'   => false
                            ],
                            'submit'          => [
                                'show' => false
                            ],
                            'step'            => [
                                'show' => false
                            ],
                            'priority'        => 1,
                            'fields'          => [
                                'ia-resources-general-tab'      => [
                                    'id'       => 'ia-resources-general-tab',
                                    'name'     => 'ia-resources-general-tab',
                                    'type'     => 'section',
                                    'priority' => 1,
                                    'label'    => __( 'General', "betterdocs-pro" ),
                                    'fields'   => [
                                        'ia_resources_general_content_title' => [
                                            'name'     => 'ia_resources_general_content_title',
                                            'type'     => 'text',
                                            'label'    => __( 'Header Title', 'betterdocs-pro' ),
                                            'priority' => 1,
                                            'default'  => __( 'Resources', 'betterdocs-pro' )
                                        ],
                                        'ia_resource_general_tab_title'      => [
                                            'name'     => 'ia_resource_general_tab_title',
                                            'type'     => 'text',
                                            'label'    => __( 'Tab Title', 'betterdocs-pro' ),
                                            'priority' => 2,
                                            'default'  => __( 'Resources', 'betterdocs-pro' )
                                        ]
                                    ]
                                ],
                                'ia-resources-doc-category-tab' => [
                                    'id'       => 'ia-resources-doc-category-tab',
                                    'name'     => 'ia-resources-doc-category-tab',
                                    'priority' => 2,
                                    'type'     => 'section',
                                    'label'    => __( "Docs Category", "betterdocs" ),
                                    'fields'   => [
                                        'ia_resources_doc_categories_switch'   => [
                                            'name'                       => 'ia_resources_doc_categories_switch',
                                            'type'                       => 'toggle',
                                            'label'                      => __( 'Enable Category', 'betterdocs-pro' ),
                                            'enable_disable_text_active' => true,
                                            'priority'                   => 1,
                                            'default'                    => true
                                        ],
                                        'ia_resources_doc_categories'          => [
                                            'name'     => 'ia_resources_doc_categories',
                                            'label'    => __( 'Select Categories', 'betterdocs-pro' ),
                                            'type'     => 'checkbox-select',
                                            'priority' => 2,
                                            'default'  => [],
                                            'multiple' => true,
                                            'options'  => array_merge( [['value' => 'all', 'label' => 'All']], $this->docs_categories() ),
                                            'rules'    => Rules::is( 'ia_resources_doc_categories_switch', true )
                                        ],
                                        'ia_resources_doc_category_title_text' => [
                                            'name'     => 'ia_resources_doc_category_title_text',
                                            'type'     => 'text',
                                            'label'    => __( 'Category Title Text', 'betterdocs-pro' ),
                                            'priority' => 4,
                                            'default'  => __( 'Doc Categories', 'betterdocs-pro' ),
                                            'rules'    => Rules::is( 'ia_resources_doc_categories_switch', true )
                                        ],
                                        'ia_terms_orderby'                     => [
                                            'name'     => 'ia_terms_orderby',
                                            'type'     => 'select',
                                            'label'    => __( 'Terms Order By', 'betterdocs' ),
                                            'default'  => 'name',
                                            'options'  => $this->normalize_options(
                                                [
                                                    'none'               => __( 'No order', 'betterdocs' ),
                                                    'name'               => __( 'Name', 'betterdocs' ),
                                                    'slug'               => __( 'Slug', 'betterdocs' ),
                                                    'term_group'         => __( 'Term Group', 'betterdocs' ),
                                                    'term_id'            => __( 'Term ID', 'betterdocs' ),
                                                    'id'                 => __( 'ID', 'betterdocs' ),
                                                    'description'        => __( 'Description', 'betterdocs' ),
                                                    'parent'             => __( 'Parent', 'betterdocs' ),
                                                    'doc_category_order' => __( 'BetterDocs Order', 'betterdocs' )
                                                ]
                                            ),
                                            'priority' => 5,
                                            'rules'    => Rules::is( 'ia_resources_doc_categories_switch', true )
                                        ],
                                        'ia_terms_order'                       => [
                                            'name'     => 'ia_terms_order',
                                            'type'     => 'select',
                                            'label'    => __( 'Terms Order', 'betterdocs' ),
                                            'default'  => 'asc',
                                            'options'  => $this->normalize_options( [
                                                'asc'  => 'Ascending',
                                                'desc' => 'Descending'
                                            ] ),
                                            'priority' => 6,
                                            'rules'    => Rules::is( 'ia_resources_doc_categories_switch', true )
                                        ]
                                        // 'ia_docs_order_by' => [
                                        //     'name'     => 'ia_docs_order_by',
                                        //     'type'     => 'select',
                                        //     'label'    => __( 'Docs Order By', 'betterdocs' ),
                                        //     'default'  => 'ID',
                                        //     'options'  => $this->normalize_options( [
                                        //         'none'             => __( 'No order', 'betterdocs' ),
                                        //         'ID'               => __( 'Docs ID', 'betterdocs' ),
                                        //         'author'           => __( 'Docs Author', 'betterdocs' ),
                                        //         'title'            => __( 'Title', 'betterdocs' ),
                                        //         'date'             => __( 'Date', 'betterdocs' ),
                                        //         'modified'         => __( 'Last Modified Date', 'betterdocs' ),
                                        //         'parent'           => __( 'Parent Id', 'betterdocs' ),
                                        //         'rand'             => __( 'Random', 'betterdocs' ),
                                        //         'comment_count'    => __( 'Comment Count', 'betterdocs' ),
                                        //         'menu_order'       => __( 'Menu Order', 'betterdocs' ),
                                        //         'betterdocs_order' => __( 'BetterDocs Order', 'betterdocs' )
                                        //     ] ),
                                        //     'priority' => 7
                                        // ],
                                        // 'ia_docs_order'                => [
                                        //     'name'     => 'ia_docs_order',
                                        //     'type'     => 'select',
                                        //     'label'    => __( 'Docs Order', 'betterdocs' ),
                                        //     'default'  => 'asc',
                                        //     'options'  => $this->normalize_options( [
                                        //         'asc'  => 'Ascending',
                                        //         'desc' => 'Descending'
                                        //     ] ),
                                        //     'priority' => 8
                                        // ]
                                    ]
                                ],
                                'ia-resources-faq-tab'          => [
                                    'id'       => 'ia-resources-faq-tab',
                                    'name'     => 'ia-resources-faq-tab',
                                    'priority' => 3,
                                    'type'     => 'section',
                                    'label'    => __( "FAQ", "betterdocs" ),
                                    'fields'   => [
                                        'ia_resources_faq_switch'       => [
                                            'name'                       => 'ia_resources_faq_switch',
                                            'type'                       => 'toggle',
                                            'label'                      => __( 'Enable FAQ', 'betterdocs-pro' ),
                                            'enable_disable_text_active' => true,
                                            'priority'                   => 1,
                                            'default'                    => true
                                        ],
                                        'ia_resources_faq_content_type' => [
                                            'name'     => 'ia_resources_faq_content_type',
                                            'label'    => __( 'Content Type', 'betterdocs-pro' ),
                                            'type'     => 'select',
                                            'priority' => 2,
                                            'default'  => 'faq-list',
                                            'options'  => GlobalFields::normalize_fields( [
                                                'faq-list'  => __( 'FAQ List', 'betterdocs-pro' ),
                                                'faq-group' => __( 'FAQ Group', 'betterdocs-pro' )
                                            ] ),
                                            'rules'    => Rules::is( 'ia_resources_faq_switch', true )
                                        ],
                                        'ia_resources_faq_list'         => [
                                            'name'     => 'ia_resources_faq_list',
                                            'label'    => __( 'Select FAQ List', 'betterdocs-pro' ),
                                            'type'     => 'checkbox-select',
                                            'priority' => 3,
                                            'multiple' => true,
                                            'search'   => true,
                                            'default'  => [],
                                            'options'  => $this->faqs(),
                                            'rules'    => Rules::logicalRule( [
                                                Rules::is( 'ia_resources_faq_content_type', 'faq-list' ),
                                                Rules::is( 'ia_resources_faq_switch', true )
                                            ] )
                                        ],
                                        'ia_resources_faq_group'        => [
                                            'name'     => 'ia_resources_faq_group',
                                            'label'    => __( 'Select FAQ Group', 'betterdocs-pro' ),
                                            'type'     => 'checkbox-select',
                                            'priority' => 4,
                                            'multiple' => true,
                                            'search'   => true,
                                            'default'  => [],
                                            'options'  => $this->faqs_categories(),
                                            'rules'    => Rules::logicalRule( [
                                                Rules::is( 'ia_resources_faq_content_type', 'faq-group' ),
                                                Rules::is( 'ia_resources_faq_switch', true )
                                            ] )
                                        ],
                                        'faq_terms_orderby'             => [
                                            'name'     => 'faq_terms_orderby',
                                            'type'     => 'select',
                                            'label'    => __( 'FAQ Group Order By', 'betterdocs' ),
                                            'default'  => 'name',
                                            'options'  => $this->normalize_options(
                                                [
                                                    'none'        => __( 'No order', 'betterdocs' ),
                                                    'name'        => __( 'Name', 'betterdocs' ),
                                                    'slug'        => __( 'Slug', 'betterdocs' ),
                                                    'term_group'  => __( 'Term Group', 'betterdocs' ),
                                                    'id'          => __( 'ID', 'betterdocs' ),
                                                    'description' => __( 'Description', 'betterdocs' ),
                                                    'parent'      => __( 'Parent', 'betterdocs' )
                                                ]
                                            ),
                                            'priority' => 6,
                                            'rules'    => Rules::logicalRule( [
                                                Rules::is( 'ia_resources_faq_content_type', 'faq-group' ),
                                                Rules::is( 'ia_resources_faq_switch', true )
                                            ] )
                                        ],
                                        'faq_terms_order'               => [
                                            'name'     => 'faq_terms_order',
                                            'type'     => 'select',
                                            'label'    => __( 'FAQ Group Order', 'betterdocs' ),
                                            'default'  => 'asc',
                                            'options'  => $this->normalize_options( [
                                                'asc'  => 'Ascending',
                                                'desc' => 'Descending'
                                            ] ),
                                            'priority' => 7,
                                            'rules'    => Rules::logicalRule( [
                                                Rules::is( 'ia_resources_faq_content_type', 'faq-group' ),
                                                Rules::is( 'ia_resources_faq_switch', true )
                                            ] )
                                        ],
                                        'ia_faq_list_order_by'          => [
                                            'name'     => 'ia_faq_list_order_by',
                                            'type'     => 'select',
                                            'label'    => __( 'FAQ List Order By', 'betterdocs' ),
                                            'default'  => 'id',
                                            'options'  => $this->normalize_options( [
                                                'none'     => __( 'No order', 'betterdocs' ),
                                                'id'       => __( 'ID', 'betterdocs' ),
                                                'author'   => __( 'Author', 'betterdocs' ),
                                                'title'    => __( 'Title', 'betterdocs' ),
                                                'date'     => __( 'Date', 'betterdocs' ),
                                                'modified' => __( 'Last Modified Date', 'betterdocs' ),
                                                'parent'   => __( 'Parent Id', 'betterdocs' )
                                            ] ),
                                            'priority' => 8,
                                            'rules'    => Rules::logicalRule( [
                                                Rules::is( 'ia_resources_faq_content_type', 'faq-list' ),
                                                Rules::is( 'ia_resources_faq_switch', true )
                                            ] )
                                        ],
                                        'ia_faq_list_order'             => [
                                            'name'     => 'ia_faq_list_order',
                                            'type'     => 'select',
                                            'label'    => __( 'FAQ List Order', 'betterdocs' ),
                                            'default'  => 'asc',
                                            'options'  => $this->normalize_options( [
                                                'asc'  => 'Ascending',
                                                'desc' => 'Descending'
                                            ] ),
                                            'priority' => 9,
                                            'rules'    => Rules::logicalRule( [
                                                Rules::is( 'ia_resources_faq_content_type', 'faq-list' ),
                                                Rules::is( 'ia_resources_faq_switch', true )
                                            ] )
                                        ],
                                        // 'ia_resources_faq_list_number'  => [
                                        //     'name'     => 'ia_resources_faq_list_number',
                                        //     'type'     => 'number',
                                        //     'label'    => __( 'Number Of FAQ List', 'betterdocs-pro' ),
                                        //     'default'  => 5,
                                        //     'priority' => 5,
                                        //     'rules'    => Rules::logicalRule( [
                                        //         Rules::is( 'ia_resources_faq_content_type', 'faq-list' ),
                                        //         Rules::is( 'ia_resources_faq_switch', true )
                                        //     ] )
                                        // ],
                                        // 'ia_resources_faq_group_number' => [
                                        //     'name'     => 'ia_resources_faq_group_number',
                                        //     'type'     => 'number',
                                        //     'label'    => __( 'Number Of FAQ Group', 'betterdocs-pro' ),
                                        //     'default'  => 5,
                                        //     'priority' => 6,
                                        //     'rules'    => Rules::logicalRule( [
                                        //         Rules::is( 'ia_resources_faq_content_type', 'faq-group' ),
                                        //         Rules::is( 'ia_resources_faq_switch', true )
                                        //     ] )
                                        // ],
                                        'ia_resources_faq_title'        => [
                                            'name'     => 'ia_resources_faq_title',
                                            'type'     => 'text',
                                            'label'    => __( 'FAQ Title Text', 'betterdocs-pro' ),
                                            'priority' => 5,
                                            'default'  => __( 'FAQ', 'betterdocs-pro' ),
                                            'rules'    => Rules::is( 'ia_resources_faq_switch', true )
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'ia_single_doc'                    => [
                    'id'       => 'ia_single_doc',
                    'name'     => 'ia_single_doc',
                    'type'     => 'section',
                    'priority' => 5,
                    'label'    => __( "Single Doc", "betterdocs-pro" ),
                    'fields'   => [
                        'ia_single_doc' => [
                            'id'              => 'ia_single_doc',
                            'name'            => 'ia_single_doc',
                            'label'           => __( 'Single Doc', 'betterdocs' ),
                            'classes'         => 'tab-nested-layout',
                            'type'            => "tab",
                            'active'          => "ia-single-doc-content",
                            'completionTrack' => true,
                            'sidebar'         => false,
                            'save'            => false,
                            'title'           => false,
                            'config'          => [
                                'active'  => 'ia-single-doc-content',
                                'sidebar' => false,
                                'title'   => false
                            ],
                            'submit'          => [
                                'show' => false
                            ],
                            'step'            => [
                                'show' => false
                            ],
                            'priority'        => 1,
                            'fields'          => [
                                'ia-single-doc-content' => [
                                    'id'       => 'ia-single-doc-content',
                                    'name'     => 'ia-single-dia_brandingoc-content',
                                    'type'     => 'section',
                                    'priority' => 1,
                                    'label'    => __( 'Content', "betterdocs-pro" ),
                                    'fields'   => [
                                        'ia_branding'    => [
                                            'name'                       => 'ia_branding',
                                            'type'                       => 'toggle',
                                            'label'                      => __( 'Branding', 'betterdocs-pro' ),
                                            'enable_disable_text_active' => true,
                                            'default'                    => true,
                                            'priority'                   => 1
                                        ],
                                        'ia_reaction'    => [
                                            'name'                       => 'ia_reaction',
                                            'type'                       => 'toggle',
                                            'label'                      => __( 'Reaction', 'betterdocs-pro' ),
                                            'enable_disable_text_active' => true,
                                            'default'                    => true,
                                            'priority'                   => 2
                                        ],
                                        'reaction_title' => [
                                            'name'     => 'reaction_title',
                                            'type'     => 'text',
                                            'label'    => __( 'Reaction Text', 'betterdocs-pro' ),
                                            'priority' => 5,
                                            'default'  => __( 'How did you feel?', 'betterdocs-pro' ),
                                            'rules'    => Rules::is( 'ia_reaction', true )
                                        ],
                                        'response_title' => [
                                            'name'     => 'response_title',
                                            'type'     => 'text',
                                            'label'    => __( 'Response Text', 'betterdocs-pro' ),
                                            'priority' => 6,
                                            'default'  => __( 'Thanks for the feedback', 'betterdocs-pro' ),
                                            'rules'    => Rules::is( 'ia_reaction', true )
                                        ]
                                    ]
                                ],
                                'ia-single-doc-style'   => [
                                    'id'       => 'ia-single-doc-style',
                                    'name'     => 'ia-single-doc-style',
                                    'priority' => 2,
                                    'type'     => 'section',
                                    'label'    => __( "Style", "betterdocs" ),
                                    'fields'   => [
                                        'ia_single_doc_title_header_bg_color' => [
                                            'name'       => 'ia_single_doc_title_header_bg_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Header Background Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#F6F6F7',
                                            'priority'   => 1
                                        ],
                                        'ia_single_doc_title_font_color'      => [
                                            'name'       => 'ia_single_doc_title_font_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'Title Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#111213',
                                            'priority'   => 2
                                        ],
                                        'ia_single_title_header_font_color'   => [
                                            'name'       => 'ia_single_title_header_font_color',
                                            'type'       => 'colorpicker',
                                            'label'      => __( 'On Scroll Title Color', 'betterdocs-pro' ),
                                            'reset_text' => __( 'Reset', 'betterdocs-pro' ),
                                            'default'    => '#111213',
                                            'priority'   => 3
                                        ],
                                        'ia_single_icons_bg_color'            => [
                                            'name'     => 'ia_single_icons_bg_color',
                                            'type'     => 'colorpicker',
                                            'label'    => __( 'Icons Background Color', 'betterdocs-pro' ),
                                            'default'  => '#f6f6f7',
                                            'priority' => 4
                                        ],
                                        'ia_reaction_primary_color'           => [
                                            'name'     => 'ia_reaction_primary_color',
                                            'type'     => 'colorpicker',
                                            'label'    => __( 'Reactions Primary Color', 'betterdocs-pro' ),
                                            'default'  => '#00A375',
                                            'priority' => 5
                                        ],
                                        'ia_reaction_secondary_color'         => [
                                            'name'     => 'ia_reaction_secondary_color',
                                            'type'     => 'colorpicker',
                                            'label'    => __( 'Reactions Secondary Color', 'betterdocs-pro' ),
                                            'default'  => '#ffffff',
                                            'priority' => 6
                                        ],
                                        'ia_reaction_title_color'             => [
                                            'name'     => 'ia_reaction_title_color',
                                            'type'     => 'colorpicker',
                                            'label'    => __( 'Reaction Title Color', 'betterdocs-pro' ),
                                            'default'  => '#FAFAFA',
                                            'priority' => 7
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'betterdocs_cross_domain_settings' => [
                    'id'       => 'betterdocs_cross_domain_settings',
                    'name'     => 'betterdocs_cross_domain_settings',
                    'type'     => 'section',
                    'label'    => __( 'Cross Domain', 'betterdocs-pro' ),
                    'priority' => 6,
                    'fields'   => [
                        'ia_cd_title_content' => [
                            'name'        => 'ia_cd_title_content',
                            'type'        => 'codeviewer',
                            'readOnly'    => true,
                            'copyOnClick' => true,
                            'code'        => InstantAnswer::snippet(),
                            'label'       => __( 'Code snippet', 'betterdocs-pro' ),
                            'priority'    => 1
                        ]
                    ]
                ]

            ]
        ];
        return $args;
    }

    public function _args( $args ) {
        /**
         * License Tab
         */
        $args['tabs']['tab-license'] = apply_filters( 'betterdocs_settings_tab_license', [
            'id'       => 'tab-license',
            'label'    => __( 'License', 'betterdocs-pro' ),
            'priority' => 90,
            'fields'   => [
                'title-design' => [
                    'name'     => 'title-design-tab',
                    'type'     => 'section',
                    'label'    => __( 'License', 'betterdocs' ),
                    'priority' => 30,
                    'fields'   => [
                        'betterdocs_licnese' => [
                            'name'    => 'betterdocs_licnese',
                            'type'    => 'action',
                            'action'  => 'betterdocs_settings_licnese',
                            'label'   => __( 'License', 'betterdocs' ),
                            'logourl' => BETTERDOCS_ABSURL . 'assets/admin/images/betterdocs-icon.svg'
                        ]
                    ]
                ]
            ]
        ] );

        $args['submit']['rules'] = Rules::logicalRule( [
            Rules::is( 'config.active', 'tab-design', true ),
            Rules::is( 'config.active', 'tab-shortcodes', true ),
            Rules::is( 'config.active', 'tab-license', true ),
            Rules::is( 'config.active', 'tab-import-export', true ),
            Rules::is( 'config.active', 'tab-migration', true )
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
     * Get All FAQ
     */
    public function faqs() {
        $faqs = $this->database->get_cache( 'betterdocs::instant_answer::all_faq' );

        if ( $faqs ) {
            return $faqs;
        }

        $faqs = [
            'all' => [
                'value' => 'all',
                'label' => __( "All", "betterdocs-pro" )
            ]
        ];

        $_faqs = get_posts( [
            'post_type'      => 'betterdocs_faq',
            'numberposts'    => -1,
            'posts_per_page' => -1
        ] );

        if ( ! empty( $_faqs ) ) {
            foreach ( $_faqs as $faq ) {
                $faqs[$faq->ID] = betterdocs()->template_helper->kses( $faq->post_title );
            }
            $faqs = GlobalFields::normalize_fields( $faqs );
            $this->database->set_cache( 'betterdocs::instant_answer::all_faq', $faqs );
        }

        return $faqs;
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
     * Get all FAQ Categories
     *
     * @return void
     */
    public function faqs_categories() {
        $faq_terms = [
            'all' => [
                'value' => 'all',
                'label' => __( "All", "betterdocs-pro" )
            ]
        ];

        $terms = get_terms( [
            'taxonomy' => 'betterdocs_faq_category'
        ] );

        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $faq_terms[$term->term_id] = $term->name;
            }
        }

        $faq_terms = GlobalFields::normalize_fields( $faq_terms );
        return $faq_terms;
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
