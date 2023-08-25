<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WPDeveloper\BetterDocs\Utils\Base;
use WPDeveloper\BetterDocs\Utils\CSSGenerator;

class InstantAnswer extends Base {
    private $settings;
    private $is_visible = true;

    public function __construct( Settings $settings ) {
        $this->settings = $settings;

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [$this, 'scripts'] );
            add_action( 'admin_footer', [$this, 'add_preview'] );
        }

        if ( ! $this->settings->get( 'enable_disable', false ) ) {
            return;
        }

        add_filter( 'rest_doc_category_query', [$this, 'order_ia_doc_taxonomies'], 10, 2 );

        if ( ! is_admin() ) {
            add_action( 'wp_enqueue_scripts', [$this, 'scripts'] );
            add_action( 'wp_footer', [$this, 'add_ia'] );
        }
    }

    public function add_preview() {
        static $printed = false;
        $screen         = get_current_screen();
        if ( $screen->id !== 'betterdocs_page_betterdocs-settings' || $printed ) {
            return;
        }

        $preview_visiable = $this->settings->get( 'ia_enable_preview', false );
        $this->add_ia( $preview_visiable );
        $printed = true;
    }

    private function is_page( $conditions = [] ){
        return is_page() && ! empty( $conditions )  && ( in_array( "all", $conditions ) || is_page( $conditions ) );
    }

    private function is_post_type_archive( $conditions = [] ){
        return is_archive() && ! is_tax() && ! is_category() && ! is_tag() && ! empty( $conditions ) && (
            in_array( "all", $conditions ) || is_post_type_archive( $conditions )
        );
    }
    private function is_other_archive( $conditions = [] ){
        return is_archive() && ! empty( $conditions ) && (
            in_array( "post", $conditions ) && is_date() || is_author() || is_day()
        );
    }

    private function is_taxonomy( $conditions = [], $queried_object = null ){
        return ( is_tax() || is_category() || is_tag() ) && ! empty( $conditions ) && (
            in_array( "all", $conditions ) || in_array( $queried_object->taxonomy, $conditions )
        );
    }

    private function is_home_archive( $conditions = [] ){
        return is_home() && ! empty( $conditions ) && (
            in_array( "all", $conditions ) || in_array( "post", $conditions )
        );
    }

    private function is_post_type_product_archive( $conditions = [], $queried_object = null ){
        return is_archive() && ! empty( $conditions ) && (
            in_array( "product", $conditions ) && get_taxonomy( $queried_object->taxonomy )->object_type[0] === 'product'
        );
    }

    private function is_singular( $conditions = [] ){
        return ! is_page() && is_singular() && ! empty( $conditions ) && (
            in_array( "all", $conditions ) || is_singular( $conditions )
        );
    }

    public function ia_conditions() {
        $display_ia_pages    = $this->settings->get_raw_field( 'display_ia_pages' );
        $display_ia_archives = $this->settings->get_raw_field( 'display_ia_archives' );
        $display_ia_texonomy = $this->settings->get_raw_field( 'display_ia_texonomy' );
        $display_ia_single   = $this->settings->get_raw_field( 'display_ia_single' );
        $queried_object      = get_queried_object();

        if( $this->is_page( $display_ia_pages ) ) {
            return true;
        } elseif( $this->is_taxonomy( $display_ia_texonomy, $queried_object ) ){
            return true;
        } elseif( $this->is_post_type_archive( $display_ia_archives ) ) {
            return true;
        } elseif( $this->is_home_archive( $display_ia_archives ) ) {
            return true;
        } elseif( $this->is_other_archive( $display_ia_archives ) ) {
            return true;
        } elseif( $this->is_post_type_product_archive( $display_ia_archives, $queried_object ) ){
            return true;
        } elseif( $this->is_singular( $display_ia_single ) ) {
            return true;
        }

        // $query_object        = get_queried_object();
        // if (
        //     is_page()
        //     && ( in_array( "all", $display_ia_pages ) || is_page( $display_ia_pages ) )
        // ) {
        //     return true;
        // } elseif (
        //     ( is_tax() || is_category() || is_tag() )
        //     && ( in_array( "all", $display_ia_texonomy ) || in_array( $query_object->taxonomy, $display_ia_texonomy ) )
        // ) {
        //     return true;
        // } elseif (
        //     is_archive()
        //     && ! is_tax()
        //     && ! is_category()
        //     && ! is_tag()
        //     && ( in_array( "all", $display_ia_archives ) || is_post_type_archive( $display_ia_archives ) )
        // ) {
        //     return true;
        // } elseif (
        //     is_home()
        //     && ( in_array( "all", $display_ia_archives ) || in_array( "post", $display_ia_archives ) )
        // ) {
        //     return true;
        // } elseif (
        //     is_archive()
        //     && ( in_array( "post", $display_ia_archives )
        //         && is_date()
        //         || is_author()
        //         || is_day() )
        // ) {
        //     return true;
        // } elseif (
        //     is_archive()
        //     && in_array( "product", $display_ia_archives )
        //     && get_taxonomy( $query_object->taxonomy )->object_type[0] === 'product'
        // ) {
        //     return true;
        // } elseif (
        //     ! is_page()
        //     && is_singular()
        //     && ( in_array( "all", $display_ia_single ) || is_singular( $display_ia_single ) )
        // ) {
        //     return true;
        // }

        return false;
    }

    public function add_ia( $preview_visiable = null ) {
        if( $preview_visiable === '' && ! $this->is_visible ) {
            return;
        }

        $style = '';
        if ( $preview_visiable === false ) {
            $style = 'style="display: none"';
        }

        echo wp_sprintf(
            '<div id="betterdocs-ia" class="betterdocs-ia %s" %s"></div>',
            'betterdocs-' . $this->settings->get( 'chat_position', 'right' ),
            $style
        );
    }

    public function order_ia_doc_taxonomies( $args, $request ) {
        if ( empty( $args['include'] ) && empty( $_GET ) && $args['taxonomy'] == 'doc_category' ) {
            $tax_limit          = $this->settings->get( 'doc_category_limit', 10 );
            $args['number']     = $tax_limit;
            $args['hide_empty'] = 1;
            $args['meta_key']   = 'doc_category_order';
            $args['orderby']    = 'meta_value_num';
            $args['order']      = 'ASC';
        }
        return $args;
    }

    public function scripts( $hook ) {
        if ( is_admin() && $hook !== 'betterdocs_page_betterdocs-settings' ) {
            return;
        }

        $this->is_visible = $this->ia_conditions();

        if( ! is_admin() && ! $this->is_visible ) {
            return;
        }

        betterdocs_pro()->assets->enqueue( 'betterdocs-instant-answer', 'public/css/instant-answer.css' );
        wp_add_inline_style( 'betterdocs-instant-answer', self::inline_style( $this->settings ) );

        betterdocs_pro()->assets->enqueue(
            'betterdocs-instant-answer',
            'public/js/instant-answer.js',
            ['wp-i18n', 'wp-element', 'wp-hooks', 'wp-util', 'wp-components']
        );

        betterdocs_pro()->assets->localize( 'betterdocs-instant-answer', 'betterdocs', $this->localize_settings() );
    }

    public static function inline_style( $settings ) {
        $_all_settings = $settings->get_all();
        $css           = new CSSGenerator( $_all_settings );

        $css->add_rule(
            '.betterdocs-widget-container',
            $css->properties( [
                'z-index' => 'chat_zindex'
            ] )
        );

        $css->add_rule(
            '.betterdocs-conversation-container,
            .betterdocs-footer-wrapper, .betterdocs-launcher, .betterdocs-ask-wrapper .betterdocs-ask-submit,
            .betterdocs-footer-wrapper .bd-ia-feedback-wrap, .betterdocs-footer-wrapper .bd-ia-feedback-response',
            $css->properties( [
                'background-color' => 'ia_accent_color'
            ] )
        );

        $css->add_rule(
            'betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-answer .toggle:first-of-type > p,
            .betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-ask .toggle:last-of-type > p',
            $css->properties( [
                'color' => 'ia_accent_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-answer .toggle:first-of-type svg,
            .betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-ask .toggle:last-of-type svg',
            $css->properties( [
                'fill' => 'ia_accent_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-header-wrapper .betterdocs-header .inner-container,
            .betterdocs-footer-wrapper .betterdocs-footer-emo > div',
            $css->properties( [
                'background-color' => 'ia_sub_accent_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-launcher[type=button], .betterdocs-launcher[type=button]:focus ',
            $css->properties( [
                'background-color' => 'ia_luncher_bg'
            ] )
        );

        $css->add_rule(
            '.betterdocs-widget-container .betterdocs-launcher[type=button]:hover ',
            $css->properties( [
                'background-color' => 'ia_luncher_bg_hover'
            ], ' !important' )
        );

        $css->add_rule(
            '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > h3,
            .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > h3',
            $css->properties( [
                'color' => 'ia_heading_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > h3.bd-ia-subtitle,
            .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > h3.bd-ia-subtitle ',
            $css->properties( [
                'font-size' => 'ia_heading_font_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > p,
            .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > p',
            $css->properties( [
                'color' => 'ia_sub_heading_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > p,
            .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > p',
            $css->properties( [
                'font-size' => 'ia_sub_heading_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-tab-content-wrapper .bdc-search-box,
            .betterdocs-tab-content-wrapper .bdc-search-box .search-button,
            .betterdocs-tab-content-wrapper .bdc-search-box input',
            $css->properties( [
                'background-color' => 'ia_searchbox_bg'
            ] )
        );

        $css->add_rule(
            '.betterdocs-tab-content-wrapper .bdc-search-box input',
            $css->properties( [
                'color' => 'ia_searchbox_text'
            ] )
        );

        $css->add_rule(
            '.betterdocs-tab-content-wrapper .bdc-search-box .search-button svg',
            $css->properties( [
                'fill' => 'ia_searchbox_icon_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-card-link',
            $css->properties( [
                'background-color' => 'iac_article_bg'
            ] )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-title-wrapper .betterdocs-card-title',
            $css->properties( [
                'color' => 'iac_article_title'
            ] )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-title-wrapper .betterdocs-card-title',
            $css->properties( [
                'font-size' => 'iac_article_title_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-body-wrapper .betterdocs-card-body',
            $css->properties( [
                'color' => 'iac_article_content'
            ] )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-body-wrapper .betterdocs-card-body',
            $css->properties( [
                'font-size' => 'iac_article_content_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-footer-wrapper .betterdocs-footer-label p',
            $css->properties( [
                'font-size' => 'ia_feedback_title_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-footer-wrapper .betterdocs-footer-label p',
            $css->properties( [
                'color' => 'ia_feedback_title_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-footer-wrapper .betterdocs-emo',
            $css->properties( [
                'fill' => 'ia_feedback_icon_color'
            ] )
        );

        //Units are modified here (might create issue, removed similarity with the previous version for i/a styles)
        if ( isset( $_all_settings['ia_feedback_icon_size'] ) && $_all_settings['ia_feedback_icon_size'] > 0 ) {
            $width  = $_all_settings['ia_feedback_icon_size'] * 2;
            $height = $_all_settings['ia_feedback_icon_size'] * 2;

            $css->add_rule(
                '.betterdocs-footer-wrapper .betterdocs-footer-emo > div',
                $css->properties( [
                    'width'  => $width,
                    'height' => $height
                ], 'px' )
            );
        }

        $css->add_rule(
            '.betterdocs-footer-wrapper .betterdocs-emo',
            $css->properties( [
                'width'  => 'ia_feedback_icon_size',
                'height' => 'ia_feedback_icon_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-icon',
            $css->properties( [
                'width' => 'ia_response_icon_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-icon',
            $css->properties( [
                'fill' => 'ia_response_icon_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-title',
            $css->properties( [
                'font-size' => 'ia_response_title_size'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-title',
            $css->properties( [
                'color' => 'ia_response_title_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-tab-ask .betterdocs-ask-wrapper input[type="text"],
            .betterdocs-tab-ask .betterdocs-ask-wrapper input[type="email"],
            .betterdocs-tab-ask .betterdocs-ask-wrapper textarea',
            $css->properties( [
                'background-color' => 'ia_ask_bg_color'
            ] )
        );

        $css->add_rule(
            '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit',
            $css->properties( [
                'background-color' => 'ia_ask_send_button_bg'
            ] )
        );

        $css->add_rule(
            '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit:hover',
            $css->properties( [
                'background-color' => 'ia_ask_send_button_hover_bg'
            ] )
        );

        $css->add_rule(
            '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit.betterdocs-disable-submit',
            $css->properties( [
                'background-color' => 'ia_ask_send_disable_button_bg'
            ] )
        );

        $css->add_rule(
            '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit.betterdocs-disable-submit:hover',
            $css->properties( [
                'background-color' => 'ia_ask_send_disable_button_hover_bg'
            ] )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h1',
            $css->properties( [
                'font-size' => 'iac_article_content_h1'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h2',
            $css->properties( [
                'font-size' => 'iac_article_content_h2'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h3',
            $css->properties( [
                'font-size' => 'iac_article_content_h3'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h4',
            $css->properties( [
                'font-size' => 'iac_article_content_h4'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h5',
            $css->properties( [
                'font-size' => 'iac_article_content_h5'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h6',
            $css->properties( [
                'font-size' => 'iac_article_content_h6'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content,
            .betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content p,
            .betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content strong',
            $css->properties( [
                'font-size' => 'iac_article_content_h6'
            ], 'px' )
        );

        $css->add_rule(
            '.betterdocs-ask-wrapper input:not([type="submit"]),
            .betterdocs-ask-wrapper textarea, .betterdocs-ask-wrapper .betterdocs-attach-button',
            $css->properties( [
                'color' => 'ia_ask_input_foreground'
            ] )
        );

        $css->add_rule( '.betterdocs-ask-wrapper .betterdocs-attach-button',
            $css->properties( [
                'fill' => 'ia_ask_input_foreground'
            ] )
        );

        $css->add_rule(
            '.betterdocs-ask-wrapper input:not([type="submit"])::placeholder,
            .betterdocs-ask-wrapper textarea::placeholder',
            $css->properties( [
                'color' => 'ia_ask_input_foreground'
            ] )
        );

        $css->add_rule(
            '.betterdocs-ask-wrapper input:not([type="submit"]), .betterdocs-ask-wrapper textarea',
            $css->properties( [
                'color' => 'ia_ask_input_foreground'
            ], ' !important' )
        );

        return $css->get_output( true );
    }

    public static function snippet() {
        ob_start();
        betterdocs_pro()->views->get( 'admin/ia-snippet', [
            'styles'  => self::inline_style( betterdocs()->settings ),
            'scripts' => self::get_instance( betterdocs()->settings )->localize_settings()
        ] );
        return ob_get_clean();
    }

    public function localize_settings() {
        $url = $this->get_rest_url();

        $search_settings = $answer_settings = $chat_settings = $launcher_settings = $thanks_settings = $branding_settings = $response_settings = [];

        /**
         * Chat Settings
         */
        if ( $this->settings->get( 'chat_tab_visibility_switch', false ) ) {
            $chat_settings['show'] = false;
        }

        $chat_tab_icon = $this->settings->get( 'chat_tab_icon', [] );
        if ( ! empty( $chat_tab_icon['url'] ) ) {
            $chat_settings['icon'] = esc_url( $chat_tab_icon['url'] );
        }

        $chat_settings['label']    = stripslashes( $this->settings->get( 'chat_tab_title', __( 'Ask', 'betterdocs-pro' ) ) );
        $chat_settings['subtitle'] = stripslashes( $this->settings->get(
            'chat_subtitle_one',
            __( 'Stuck with something? Send us a message.', 'betterdocs-pro' )
        ) );
        $chat_settings['subtitle_two'] = stripslashes( $this->settings->get(
            'chat_subtitle_two',
            __( 'Generally, we reply within 24-48 hours.', 'betterdocs-pro' )
        ) );

        /**
         * Answer Tab Settings
         */
        if ( $this->settings->get( 'answer_tab_visibility_switch', false ) ) {
            $answer_settings['show'] = false;
        }

        $answer_tab_icon = $this->settings->get( 'answer_tab_icon', [] );
        if ( ! empty( $answer_tab_icon['url'] ) ) {
            $answer_settings['icon'] = esc_url( $answer_tab_icon['url'] );
        }

        $answer_settings['label']    = stripslashes( $this->settings->get( 'answer_tab_title', __( 'Answer', 'betterdocs-pro' ) ) );
        $answer_settings['subtitle'] = stripslashes( $this->settings->get( 'answer_tab_subtitle', __( 'Instant Answer', 'betterdocs-pro' ) ) );

        /**
         * Search Settings
         */
        if ( $this->settings->get( 'search_visibility_switch', false ) ) {
            $search_settings['show'] = false;
        }

        $search_settings['SEARCH_URL']         = $this->get_rest_url( true );
        $search_settings['SEARCH_PLACEHOLDER'] = stripslashes( $this->settings->get( 'search_placeholder_text', __( 'Search...', 'betterdocs-pro' ) ) );
        $search_settings['OOPS']               = stripslashes( $this->settings->get( 'search_not_found_1', __( 'Oops...', 'betterdocs-pro' ) ) );
        $search_settings['NOT_FOUND']          = stripslashes( $this->settings->get(
            'search_not_found_2',
            __( "We couldn’t find any docs that match your search. Try searching for a new term.", 'betterdocs-pro' )
        ) );

        // dump( $search_settings['SEARCH_URL'] );

        /**
         * Response Settings
         */
        if ( $this->settings->get( 'disable_response', false ) ) {
            $response_settings['show'] = false;
        }
        $response_settings['title'] = stripslashes( $this->settings->get( 'response_title', __( 'Thanks for the feedback', 'betterdocs-pro' ) ) );

        if ( $this->settings->get( 'disable_response_icon', false ) ) {
            $response_settings['icon']['show'] = false;
        }

        $instant_answer = [
            'CHAT'     => $chat_settings,
            'ANSWER'   => $answer_settings,
            'URL'      => $url,
            'SEARCH'   => $search_settings,
            'FEEDBACK' => [
                'DISPLAY' => ! $this->settings->get( 'disable_reaction', false ),
                'SUCCESS' => __( 'Thanks for your feedback', 'betterdocs-pro' ),
                'TEXT'    => stripslashes( $this->settings->get( 'reaction_title', __( 'How did you feel?', 'betterdocs-pro' ) ) ),
                'URL'     => get_rest_url( null, '/betterdocs/v1/feedback' )
            ],
            'RESPONSE' => $response_settings,
            'ASKFORM'  => [
                'NAME'       => __( 'Name', 'betterdocs-pro' ),
                'EMAIL'      => __( 'Email Address', 'betterdocs-pro' ),
                'SUBJECT'    => __( 'Subject', 'betterdocs-pro' ),
                'TEXTAREA'   => __( 'How can we help?', 'betterdocs-pro' ),
                'ATTACHMENT' => __( 'Only supports .jpg, .png, .jpeg, .gif files', 'betterdocs-pro' ),
                'SENDING'    => __( 'Sending', 'betterdocs-pro' ),
                'SEND'       => __( 'Send', 'betterdocs-pro' )
            ],
            'ASK_URL'  => get_rest_url( null, '/betterdocs/v1/ask' )
        ];

        /**
         * Launcher Settings
         */
        $launcher_open_icon = $this->settings->get( 'launcher_open_icon', [] );
        if ( ! empty( $launcher_open_icon['url'] ) ) {
            $launcher_settings['open_icon'] = $launcher_open_icon['url'];
        }

        $launcher_close_icon = $this->settings->get( 'launcher_close_icon', [] );
        if ( ! empty( $launcher_close_icon['url'] ) ) {
            $launcher_settings['close_icon'] = $launcher_close_icon['url'];
        }

        if ( ! empty( $launcher_settings ) ) {
            $instant_answer = array_merge( $instant_answer, ['LAUNCHER' => $launcher_settings] );
        }

        /**
         * Branding Settings
         */
        if ( $this->settings->get( 'disable_branding', false ) ) {
            $branding_settings['show'] = false;
        }

        if ( ! empty( $branding_settings ) ) {
            $instant_answer = array_merge( $instant_answer, ['BRANDING' => $branding_settings] );
        }

        /**
         * Thanks Settings
         */
        $_thanks_title = $this->settings->get( 'ask_thanks_title', '' );
        if ( ! empty( $_thanks_title ) ) {
            $thanks_settings['title'] = stripslashes( $_thanks_title );
        }

        $_thanks_text = $this->settings->get( 'ask_thanks_text', '' );
        if ( ! empty( $_thanks_text ) ) {
            $thanks_settings['text'] = stripslashes( $_thanks_text );
        }

        if ( ! empty( $thanks_settings ) ) {
            $instant_answer = array_merge( $instant_answer, ['THANKS' => $thanks_settings] );
        }

        return $instant_answer;
    }

    public function get_rest_url( $is_search = false ) {
        $_query_strings_array = [];
        $_content_type        = $this->settings->get( 'content_type', 'docs' );
        $_base_url            = get_rest_url( null, 'wp/v2/docs' );

        if ( has_filter( 'wpml_current_language' ) ) { // get wpml language
            $lang = apply_filters( 'wpml_current_language', NULL );
            if ( $lang ) {
                $_query_strings_array[] = 'lang=' . $lang;
            }
        }

        switch ( $_content_type ) {
            case 'docs':
                $_content_list = $this->settings->get( 'docs_list', [] );
                if ( ! empty( $_content_list ) ) {
                    $_query_strings_array['include'] = $_content_list;
                }
                break;
            case 'docs_categories':
                if ( ! $is_search ) {
                    $_base_url = get_rest_url( null, 'wp/v2/doc_category' );
                }
                $_content_list = $this->settings->get( 'doc_category_list', [] );
                if ( ! empty( $_content_list ) ) {
                    $_cats_list = implode( ',', $_content_list );

                    $_query_strings_array[$is_search ? 'doc_category' : 'include'] = $_cats_list;
                }
                break;
            default:
                $_query_strings_array['per_page'] = 10;
                break;
        }

        // dump( $_query_strings_array );

        $_query_strings_array = apply_filters( 'betterdocs_ia_query_string_array', $_query_strings_array, $_content_type, $is_search, $_content_list );
        $_query_string        = preg_replace( ['/%5B[0-9]%5D/', '/%2C/'], ['[]', ','], http_build_query( $_query_strings_array, '', '&' ) );
        $_parsed_url          = parse_url( $_base_url );

        if ( ! empty( $_query_string ) ) {
            $_parsed_url['query'] = isset( $_parsed_url['query'] ) ? "{$_parsed_url['query']}&$_query_string" : $_query_string;
        }

        return $this->unparse_url( $_parsed_url );
    }

    /**
     * Unparse URL
     * @param array $parsed_url
     * @return string of url
     * @since 1.0.0
     */
    public static function unparse_url( $parsed_url ) {
        $scheme   = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
        $port     = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
        $user     = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
        $pass     = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';
        $pass     = ( $user || $pass ) ? "$pass@" : '';
        $path     = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
        $query    = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
        $fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}
