<?php 
/**
 * BetterDocs_Pro_IA class
 */
class BetterDocs_Pro_IA {

    use BetterDocs_Content_Restrictions;

    const DEV_MODE = false;

    /**
     * BetterDocs_Pro_IA instance
     * @var BetterDocs_Pro_IA
     */
    private static $_instance = null;
    /**
     * REST API namespace
     * @var string
     */
    private $namespace = 'betterdocs';
    private $bdocs_settings = null;
    private $screen = null;
    /**
     * Singleton Instance of BetterDocs_Pro_IA
     * @return BetterDocs_Pro_IA
     */
    public static function instance() {
        return self::$_instance === null ? self::$_instance = new self() : self::$_instance;
    }

    public function __construct() {
        if( is_admin() ) {
            if( ! function_exists( 'get_current_screen' ) ) {
                require_once ABSPATH . 'wp-admin/includes/screen.php';
            }
            $this->screen = get_current_screen();
        }

        add_action('rest_api_init', array($this, 'register_api_endpoint'));
        add_action('wp_enqueue_scripts', array($this, 'scripts'));
        add_action('admin_enqueue_scripts', array($this, 'scripts'));
        add_filter('betterdocs_settings_tab', array($this, 'settings'));
        add_filter('rest_doc_category_query', array( $this, 'order_ia_doc_taxonomies' ), 10, 2 );
        $ia = BetterDocs_DB::get_settings('enable_disable');
        $ia_preview = BetterDocs_DB::get_settings('ia_enable_preview');
        if($ia == 1) {
            add_action('wp_footer', array($this, 'add_ia_icon'));
        }
        if($ia_preview == 1) {
            add_action('admin_footer', array($this, 'add_admin_ia_icon'));
        }
    }

    public function order_ia_doc_taxonomies( $args, $request ) {
        if( empty( $args['include'] ) && empty( $_GET ) && $args['taxonomy'] == 'doc_category' ) {
            $tax_limit          = empty( BetterDocs_DB::get_settings('doc_category_limit') ) ? 10 : BetterDocs_DB::get_settings('doc_category_limit');
            $args['number']     = $tax_limit;
            $args['hide_empty'] = 1;
            $args['meta_key']   = 'doc_category_order';
            $args['orderby']    = 'meta_value_num';
            $args['order']      = 'ASC';
        }
        return $args;
    }

    public function scripts( $hook ) {
        if( is_null( $this->bdocs_settings ) ) {
            $this->bdocs_settings = BetterDocs_DB::get_settings();
        }

        if( $hook !== 'betterdocs_page_betterdocs-settings' ) {
            if( $this->ia_conditions() === false ) {
                return;
            }
        }

        if( ( isset( $this->bdocs_settings['enable_disable'] ) && $this->bdocs_settings['enable_disable'] == 1 ) || ! isset( $this->bdocs_settings['enable_disable'] ) ) {
            if( is_admin() ) {
                if( $this->screen === null ) {
                    $this->screen = get_current_screen();
                }
                if( $this->screen->id !== 'betterdocs_page_betterdocs-settings' ) {
                    return;
                }
                if( ! ( isset( $this->bdocs_settings['ia_enable_preview'] ) && $this->bdocs_settings['ia_enable_preview'] == 1 ) || ! isset( $this->bdocs_settings['ia_enable_preview'] ) ) {
                    return;
                }
            }
            if( ! self::DEV_MODE ) {
                wp_enqueue_style(
                    'betterdocs-instant-answer', 
                    BETTERDOCS_PRO_PUBLIC_URL . 'modules/instant-answer.css', 
                    array(),  BETTERDOCS_PRO_VERSION, 'all'
                );
            }

            wp_add_inline_style( 'betterdocs-instant-answer', $this->inline_style() );

            wp_enqueue_script(
                'betterdocs-instant-answer', 
                self::DEV_MODE ? BETTERDOCS_PRO_PUBLIC_URL . 'instant-answer/lib/bundle.js' : BETTERDOCS_PRO_PUBLIC_URL . 'modules/instant-answer.js',
                array('wp-i18n', 'wp-element', 'wp-hooks', 'wp-util', 'wp-components'), BETTERDOCS_PRO_VERSION, true 
            );
            $enable_instant_answer = BetterDocs_DB::get_settings('enable_disable');
            if ( $enable_instant_answer == 1 ) {
                wp_localize_script('betterdocs-instant-answer', 'betterdocs', $this->jsObject( $this->bdocs_settings ));
            }
        }
    }
    /**
     * Inline Style From Settings
     */
    public function inline_style(){
        $settings = $this->bdocs_settings;

        $css = '';

        if( $this->setNempty( 'chat_zindex', $settings ) ) {
            $css .= '.betterdocs-widget-container{z-index:' . $settings['chat_zindex'] . '}';
        }

        if( $this->setNempty( 'ia_accent_color', $settings ) ) {
            $css .= '.betterdocs-conversation-container, .betterdocs-footer-wrapper, .betterdocs-launcher, .betterdocs-ask-wrapper .betterdocs-ask-submit{background-color:' . $settings['ia_accent_color'] . '}';
            $css .= '.betterdocs-footer-wrapper .bd-ia-feedback-wrap, .betterdocs-footer-wrapper .bd-ia-feedback-response{background-color:' . $settings['ia_accent_color'] . '}';
            $css .= '.betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-answer .toggle:first-of-type > p, .betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-ask .toggle:last-of-type > p{color:' . $settings['ia_accent_color'] . '}';
            $css .= '.betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-answer .toggle:first-of-type svg, .betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-ask .toggle:last-of-type svg{fill:' . $settings['ia_accent_color'] . '}';
        }

        if( $this->setNempty( 'ia_sub_accent_color', $settings ) ) {
            $css .= '.betterdocs-header-wrapper .betterdocs-header .inner-container, .betterdocs-footer-wrapper .betterdocs-footer-emo > div{background-color:' . $settings['ia_sub_accent_color'] . '}';
        }

        if( $this->setNempty( 'ia_luncher_bg', $settings ) ) {
            $css .= '.betterdocs-launcher[type=button], .betterdocs-launcher[type=button]:focus {background-color:' . $settings['ia_luncher_bg'] . '}';
        }
        
        if( $this->setNempty( 'ia_luncher_bg_hover', $settings ) ) {
            $css .= '.betterdocs-widget-container .betterdocs-launcher[type=button]:hover {background-color:' . $settings['ia_luncher_bg_hover'] . ' !important}';
        }

        if( $this->setNempty( 'ia_heading_color', $settings ) ) {
            $css .= '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > h3, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > h3{color:' . $settings['ia_heading_color'] . '}';
        }

        if( $this->setNempty( 'ia_heading_font_size', $settings ) ) {
            $css .= '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > h3.bd-ia-subtitle, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > h3.bd-ia-subtitle {font-size:' . $settings['ia_heading_font_size'] . 'px}';
        }

        if( $this->setNempty( 'ia_sub_heading_color', $settings ) ) {
            $css .= '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > p, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > p{color:' . $settings['ia_sub_heading_color'] . '}';
        }

        if( $this->setNempty( 'ia_sub_heading_size', $settings ) ) {
            $css .= '.betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > p, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > p{font-size:' . $settings['ia_sub_heading_size'] . 'px}';
        }

        if( $this->setNempty( 'ia_searchbox_bg', $settings ) ) {
            $css .= '.betterdocs-tab-content-wrapper .bdc-search-box,
            .betterdocs-tab-content-wrapper .bdc-search-box .search-button,
            .betterdocs-tab-content-wrapper .bdc-search-box input{background-color:' . $settings['ia_searchbox_bg'] . '}';
        }

        if( $this->setNempty( 'ia_searchbox_text', $settings ) ) {
            $css .= '.betterdocs-tab-content-wrapper .bdc-search-box input{color:' . $settings['ia_searchbox_text'] . '}';
        }

        if( $this->setNempty( 'ia_searchbox_icon_color', $settings ) ) {
            $css .= '.betterdocs-tab-content-wrapper .bdc-search-box .search-button svg {fill:' . $settings['ia_searchbox_icon_color'] . '}';
        }

        if( $this->setNempty( 'iac_article_bg', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-card-link { background-color:' . $settings['iac_article_bg'] . '}';
        }
        
        if( $this->setNempty( 'iac_article_title', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-title-wrapper .betterdocs-card-title { color:' . $settings['iac_article_title'] . '}';
        }
        
        if( $this->setNempty( 'iac_article_title_size', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-title-wrapper .betterdocs-card-title { font-size:' . $settings['iac_article_title_size'] . 'px}';
        }
        
        if( $this->setNempty( 'iac_article_content', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-body-wrapper .betterdocs-card-body { color:' . $settings['iac_article_content'] . '}';
        }
        
        if( $this->setNempty( 'iac_article_content_size', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-card-link .betterdocs-card-body-wrapper .betterdocs-card-body { font-size:' . $settings['iac_article_content_size'] . 'px}';
        }
        
        if( $this->setNempty( 'ia_feedback_title_size', $settings ) ) {
            $css .= '.betterdocs-footer-wrapper .betterdocs-footer-label p { font-size:' . $settings['ia_feedback_title_size'] . 'px}';
        }
        
        if( $this->setNempty( 'ia_feedback_title_color', $settings ) ) {
            $css .= '.betterdocs-footer-wrapper .betterdocs-footer-label p { color:' . $settings['ia_feedback_title_color'] . '}';
        }
        
        if( $this->setNempty( 'ia_feedback_icon_color', $settings ) ) {
            $css .= '.betterdocs-footer-wrapper .betterdocs-emo { fill:' . $settings['ia_feedback_icon_color'] . '}';
        }
        
        if( $this->setNempty( 'ia_feedback_icon_size', $settings ) ) {
            $css .= '.betterdocs-footer-wrapper .betterdocs-footer-emo > div { width:' . ( intval( $settings['ia_feedback_icon_size'] ) * 2 ) . 'px; height: '. ( intval( $settings['ia_feedback_icon_size'] ) * 2 ) .'px}';
            $css .= '.betterdocs-footer-wrapper .betterdocs-emo { width:' . $settings['ia_feedback_icon_size'] . 'px; height: '. $settings['ia_feedback_icon_size'] .'px}';
        }

        if( $this->setNempty( 'ia_response_icon_size', $settings ) ) {
            $css .= '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-icon {width: '.$settings['ia_response_icon_size'].'px}';
        }

        if( $this->setNempty('ia_response_icon_color', $settings) ) {
            $css .= '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-icon {fill: '.$settings['ia_response_icon_color'].'}';
        }

        if( $this->setNempty( 'ia_response_title_size', $settings ) ) {
            $css .= '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-title {font-size: '.$settings['ia_response_title_size'].'px}';
        }

        if( $this->setNempty('ia_response_title_color', $settings) ) {
            $css .= '.betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-title {color: '.$settings['ia_response_title_color'].'}';
        }
        
        if( $this->setNempty( 'ia_ask_bg_color', $settings ) ) {
            $css .= '.betterdocs-tab-ask .betterdocs-ask-wrapper input[type="text"], .betterdocs-tab-ask .betterdocs-ask-wrapper input[type="email"], .betterdocs-tab-ask .betterdocs-ask-wrapper textarea { background-color: '.  $settings['ia_ask_bg_color'] .'}';
        }     

        if( $this->setNempty( 'ia_ask_send_button_bg', $settings ) ) {
            $css .= '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit { background-color: '.  $settings['ia_ask_send_button_bg'] .'}';
        }   

        if( $this->setNempty( 'ia_ask_send_button_hover_bg', $settings ) ) {
            $css .= '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit:hover { background-color: '.  $settings['ia_ask_send_button_hover_bg'] .'}';
        }
        
        if( $this->setNempty( 'ia_ask_send_disable_button_bg', $settings ) ) {
            $css .= '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit.betterdocs-disable-submit { background-color: '.  $settings['ia_ask_send_disable_button_bg'] .'}';
        }
             
        if( $this->setNempty( 'ia_ask_send_disable_button_hover_bg', $settings ) ) {
            $css .= '.betterdocs-tab-ask .betterdocs-ask-wrapper .betterdocs-ask-submit.betterdocs-disable-submit:hover { background-color: '.  $settings['ia_ask_send_disable_button_hover_bg'] .'}';
        }

        if( $this->setNempty( 'iac_article_content_h1', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h1 { font-size: '.  $settings['iac_article_content_h1'] .'px}';
        }

        if( $this->setNempty( 'iac_article_content_h2', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h2 { font-size: '.  $settings['iac_article_content_h2'] .'px}';
        }

        if( $this->setNempty( 'iac_article_content_h3', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h3 { font-size: '.  $settings['iac_article_content_h3'] .'px}';
        }

        if( $this->setNempty( 'iac_article_content_h4', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h4 { font-size: '.  $settings['iac_article_content_h4'] .'px}';
        }

        if( $this->setNempty( 'iac_article_content_h5', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h5 { font-size: '.  $settings['iac_article_content_h5'] .'px}';
        }

        if( $this->setNempty( 'iac_article_content_h6', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h6 { font-size: '.  $settings['iac_article_content_h6'] .'px}';
        }

        if( $this->setNempty( 'iac_article_content_p', $settings ) ) {
            $css .= '.betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content,
            .betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content p,
            .betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content strong { font-size: '.  $settings['iac_article_content_h6'] .'px}';
        }

        if( $this->setNempty( 'ia_ask_input_foreground', $settings ) ) {
            $css .= '.betterdocs-ask-wrapper input:not([type="submit"]), .betterdocs-ask-wrapper textarea, .betterdocs-ask-wrapper .betterdocs-attach-button { color: '.  $settings['ia_ask_input_foreground'] .'}';
            $css .= '.betterdocs-ask-wrapper .betterdocs-attach-button { fill: '.  $settings['ia_ask_input_foreground'] .'}';

            $css .= '.betterdocs-ask-wrapper input:not([type="submit"])::placeholder, .betterdocs-ask-wrapper textarea::placeholder { color: '.  $settings['ia_ask_input_foreground'] .'}';
            $css .= '.betterdocs-ask-wrapper input:not([type="submit"]), .betterdocs-ask-wrapper textarea { color: '.  $settings['ia_ask_input_foreground'] .' !important;}';
        }


        return $css;
    }
    /**
     * JS Object
     *
     * @param array $settings
     * @return void
     */
    public function jsObject( $settings ) {
        $url = $this->make_url( $settings );

        $search_settings = $answer_settings = $chat_settings = $launcher_settings = $thanks_settings = $branding_settings = $response_settings = array();

        $answer_tab_title    = $this->setNempty( 'answer_tab_title', $settings ) ? $settings['answer_tab_title'] : __( 'Answer', 'betterdocs-pro' );
        $answer_tab_subtitle = $this->setNempty( 'answer_tab_subtitle', $settings ) ? $settings['answer_tab_subtitle'] : __( 'Instant Answer', 'betterdocs-pro' );
        $chat_tab_title      = $this->setNempty( 'chat_tab_title', $settings ) ? $settings['chat_tab_title'] : __( 'Ask', 'betterdocs-pro' );
        $chat_subtitle_one   = $this->setNempty( 'chat_subtitle_one', $settings ) ? $settings['chat_subtitle_one'] : __( 'Stuck with something? Send us a message.', 'betterdocs-pro' );
        $chat_subtitle_two   = $this->setNempty( 'chat_subtitle_two', $settings ) ? $settings['chat_subtitle_two'] : __( 'Generally, we reply within 24-48 hours.', 'betterdocs-pro' );

        $ask_thanks_title   = $this->setNempty( 'ask_thanks_title', $settings ) ? $settings['ask_thanks_title'] : '';
        $ask_thanks_text   = $this->setNempty( 'ask_thanks_text', $settings ) ? $settings['ask_thanks_text'] : '';

        if( ! empty( $ask_thanks_title ) ) {
            $thanks_settings['title'] = $ask_thanks_title;
        }
        if( ! empty( $ask_thanks_text ) ) {
            $thanks_settings['text'] = $ask_thanks_text;
        }

        $launcher_open_icon   = $this->setNempty( 'launcher_open_icon', $settings ) ? $settings['launcher_open_icon'] : [];
        if( ! empty( $launcher_open_icon ) && ! empty( $launcher_open_icon['url'] ) ) {
            $launcher_settings['open_icon'] = $launcher_open_icon['url'];
        }
        $launcher_close_icon   = $this->setNempty( 'launcher_close_icon', $settings ) ? $settings['launcher_close_icon'] : [];
        if( ! empty( $launcher_close_icon ) && ! empty( $launcher_close_icon['url'] ) ) {
            $launcher_settings['close_icon'] = $launcher_close_icon['url'];
        }

        $search_switch = $this->setNempty( 'search_visibility_switch', $settings ) ? $settings['search_visibility_switch'] : [];
        if( ! empty( $search_switch ) && $search_switch === '1' ) {
            $search_settings['show'] = false;
        }

        $search_url = $this->make_url( $settings, '', true );
        $search_settings['SEARCH_URL'] = $search_url;

        $search_placeholder = $this->setNempty( 'search_placeholder_text', $settings ) ? $settings['search_placeholder_text'] : __( 'Search...', 'betterdocs-pro' );
        $search_settings['SEARCH_PLACEHOLDER'] = $search_placeholder;

        $search_settings['OOPS'] = $this->setNempty( 'search_not_found_1', $settings ) ? $settings['search_not_found_1'] : __( 'Oops...', 'betterdocs-pro' );
        $search_settings['NOT_FOUND'] = $this->setNempty( 'search_not_found_2', $settings ) ? $settings['search_not_found_2'] : __( 'We couldn’t find any articles that match your search. Try searching for a new term.', 'betterdocs-pro' );

        $answer_tab_switch = $this->setNempty( 'answer_tab_visibility_switch', $settings ) ? $settings['answer_tab_visibility_switch'] : [];
        if( ! empty( $answer_tab_switch ) && $answer_tab_switch === '1' ) {
            $answer_settings['show'] = false;
        }

        $answer_tab_icon   = $this->setNempty( 'answer_tab_icon', $settings ) ? $settings['answer_tab_icon'] : [];
        if( ! empty( $answer_tab_icon ) && ! empty( $answer_tab_icon['url'] ) ) {
            $answer_settings['icon'] = $answer_tab_icon['url'];
        }

        $chat_tab_switch = $this->setNempty( 'chat_tab_visibility_switch', $settings ) ? $settings['chat_tab_visibility_switch'] : [];
        if( ! empty( $chat_tab_switch ) && $chat_tab_switch === '1' ) {
            $chat_settings['show'] = false;
        }

        $chat_tab_icon   = $this->setNempty( 'chat_tab_icon', $settings ) ? $settings['chat_tab_icon'] : [];
        if( ! empty( $chat_tab_icon ) && ! empty( $chat_tab_icon['url'] ) ) {
            $chat_settings['icon'] = $chat_tab_icon['url'];
        }

        $disable_branding   = $this->setNempty( 'disable_branding', $settings ) ? $settings['disable_branding'] : [];
        if( ! empty( $disable_branding ) && $disable_branding === '1' ) {
            $branding_settings['show'] = false;
        }

        $disable_response   = $this->setNempty( 'disable_response', $settings ) ? $settings['disable_response'] : [];
        if( ! empty( $disable_response ) && $disable_response === '1' ) {
            $response_settings['show'] = false;
        }

        $response_settings['title'] = $this->setNempty( 'response_title', $settings ) ? $settings['response_title'] : __( 'Thanks for the feedback', 'betterdocs-pro' );

        $disable_response_icon   = $this->setNempty( 'disable_response_icon', $settings ) ? $settings['disable_response_icon'] : [];
        if( ! empty( $disable_response_icon ) && $disable_response_icon === '1' ) {
            $response_settings['icon']['show'] = false;
        }


        $answer_settings['label'] = $answer_tab_title;
        $answer_settings['subtitle'] = $answer_tab_subtitle;

        $chat_settings['label'] = $chat_tab_title;
        $chat_settings['subtitle'] = $chat_subtitle_one;
        $chat_settings['subtitle_two'] = $chat_subtitle_two;

        $disable_reaction = $this->setNempty( 'disable_reaction', $settings ) ? $this->bdocs_settings['disable_reaction'] : '';
        $reaction = $disable_reaction == 1 ? false : true;
        $reaction_title = $this->setNempty( 'reaction_title', $settings ) ? $this->bdocs_settings['reaction_title'] : esc_html__('How did you feel?','betterdocs-pro');
        //remove the slash from the Instant Answer search text placeholder
        $search_settings['SEARCH_PLACEHOLDER'] = stripslashes($search_settings['SEARCH_PLACEHOLDER']);
        //remove slash from Instant Answer Tab Title
        $answer_settings['label'] = stripslashes($answer_settings['label']);
        //remove slash from Instant Answer Tab Title
        $answer_settings['subtitle'] = stripslashes($answer_settings['subtitle']);
        //remove slash from Chat Tab Subtitle One
        $chat_settings['subtitle'] = stripslashes($chat_settings['subtitle']);
        //remove slash from Chat Tab Subtitle Two
        $chat_settings['subtitle_two'] = stripslashes($chat_settings['subtitle_two']);
        //remove slash from Reaction Title
        $reaction_title = stripslashes($reaction_title);
        //remove slash from response title
        $response_settings['title'] = stripslashes($response_settings['title']);
        //remove slash from docs not found oops
        $search_settings['OOPS'] = stripslashes( $search_settings['OOPS'] );
        //remove slash from docs not found message
        $search_settings['NOT_FOUND'] = stripslashes($search_settings['NOT_FOUND']);
        $instant_answer = array(
            'CHAT' => $chat_settings,
            'ANSWER' => $answer_settings,
            'URL' => $url,
            'SEARCH'    => $search_settings,
            'FEEDBACK' => array(
                'DISPLAY' => $reaction,
                'SUCCESS' => esc_html__('Thanks for your feedback','betterdocs-pro'),
                'TEXT'    => $reaction_title,
                'URL'     => home_url() . '/?rest_route=/betterdocs/feedback',
            ),
            'RESPONSE'  => $response_settings,
            'ASKFORM' => array(
                'NAME'     => esc_html__('Name','betterdocs-pro'),
                'EMAIL'    => esc_html__('Email Address','betterdocs-pro'),
                'SUBJECT'  => esc_html__('Subject','betterdocs-pro'),
                'TEXTAREA' => esc_html__('How can we help?','betterdocs-pro'),
                'ATTACHMENT' => esc_html__('Only supports .jpg, .png, .jpeg, .gif files','betterdocs-pro'),
                'SENDING' => esc_html__('Sending','betterdocs-pro'),
                'SEND' => esc_html__('Send','betterdocs-pro'),
            ),
            'ASK_URL' => home_url() . '/?rest_route=/betterdocs/ask',
        );
        
        if( ! empty( $launcher_settings ) ) {
            $instant_answer = array_merge( $instant_answer, array( 'LAUNCHER' => $launcher_settings ) );
        }
        if( ! empty( $branding_settings ) ) {
            $instant_answer = array_merge( $instant_answer, array( 'BRANDING' => $branding_settings ) );
        }
        if( ! empty( $thanks_settings ) ) {
            $instant_answer = array_merge( $instant_answer, array( 'THANKS' => $thanks_settings ) );
        }

        return $instant_answer;
    }
    /**
     * Check if a key is set and and is not empty.
     * @param string $key
     * @param array $data
     * @return boolean
     */
    private static function setNempty( $key, $data ) {
        return isset( $data[ $key ] ) && ! empty( $data[ $key ] );
    }
	/**
	 * Unparse URL
	 * @param array $parsed_url
	 * @return string of url
	 * @since 1.0.0
	 */
	public static function unparse_url($parsed_url) {
		$scheme   = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
		$port     = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
		$user     = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
		$pass     = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass']  : '';
		$pass     = ( $user || $pass ) ? "$pass@" : '';
		$path     = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
		$query    = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
		$fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}

    public function make_url( $settings, $base = false, $search = false ) {
        $sub_string_arr_include = [];
        $query_string_as_array = [];
        $sub_string_url = '';
        $site_url = get_rest_url();
        $base_url = $site_url . 'wp/v2/docs';

        if( $base == true ) {
            return $base_url;
        }

        if( isset( $settings['content_type'] ) && ! empty( $settings['content_type'] ) && $settings['content_type'] === 'docs_categories' && $search == false ) {
            $base_url = $site_url . 'wp/v2/doc_category';
        }

        if ( has_filter('wpml_current_language') ) { // get wpml language
            $lang = apply_filters( 'wpml_current_language', NULL );
            if ($lang) {
                $sub_string_arr_include[] = 'lang=' . $lang;
            }
        }

        $url = $base_url . '&per_page=10';

        $parsed_url = parse_url( $base_url );
        $query_string_as_array = isset( $parsed_url['path'] ) ? explode( '&', $parsed_url['path'] ) : '';

        if( isset( $settings['content_type'] ) && ! empty( $settings['content_type'] ) ) {
            switch( $settings['content_type'] ) {
                case 'docs' :
                    $sub_string_url = is_array( $settings['docs_list'] ) ? implode( ',', $settings['docs_list'] ) : '';
                    if( ! empty( $sub_string_url ) ) {
                        $sub_string_arr = explode(",", $sub_string_url);
                        foreach ($sub_string_arr as $value) {
                            $sub_string_arr_include[] = 'include[]='.$value;
                        }
                        $query_string_as_array[] = implode("&", $sub_string_arr_include);
                    }
                    if ($this->content_restriction() == 1) {
                        if ($this->content_visibility_by_role() == false && !empty($this->get_restricted_category())) {
                            $query_string_as_array[] = 'doc_category_exclude=' . implode(',', $this->get_restricted_category());
                        }
                        if ($this->content_visibility_by_role() == false && !empty($this->get_restricted_kb())) {
                            $query_string_as_array[] = 'knowledge_base_exclude=' . implode( ',', $this->get_restricted_kb());
                        }
                    }
                    break;
                case 'docs_categories' :
                    $sub_string_url = is_array( $settings['doc_category_list'] ) ? implode( ',', $settings['doc_category_list'] ) : '';
                    if ( $search == true && ! empty( $sub_string_url )) {
                        $query_string_as_array[] = 'doc_category=' . $sub_string_url;
                    } else if( ! empty( $sub_string_url ) ) {
                        $query_string_as_array[] = 'include=' . $sub_string_url;
                    }

                    $doc_category_ids = get_terms([
                        'taxonomy' => 'doc_category',
                        'fields' => 'ids',
                    ]);

                    if( $search == true && empty( $sub_string_url ) && $this->content_restriction() == 1 && $this->content_visibility_by_role() == false && !empty($this->get_restricted_category())) {
                        $query_string_as_array[] = 'doc_category=' . implode(',', array_diff($doc_category_ids, $this->get_restricted_category()));
                    } else if ( empty( $sub_string_url ) && $this->content_restriction() == 1 && $this->content_visibility_by_role() == false && !empty($this->get_restricted_category())) {
                        $query_string_as_array[] = 'include=' . implode(',', array_diff($doc_category_ids, $this->get_restricted_category()));
                    }
                    break;
            }

            $firstKey = reset($query_string_as_array);
            $othersKey = array_slice($query_string_as_array, 1);
            if ( $othersKey ) {
                $query_string_as_array = $firstKey . '?' . implode( '&', $othersKey );
            } else {
                $query_string_as_array = $firstKey;
            }


            if( ! empty( $query_string_as_array ) ) {
                $parsed_url['path'] = $query_string_as_array;
            }
            $url = self::unparse_url( $parsed_url );
        }

        return $url;
    }

    public function settings( $settings ) {
        $settings['betterdocs_instant_answer'] = array(
            'title'       => __( 'Instant Answer', 'betterdocs-pro' ),
            'priority'    => 20,
            'button_text' => __( 'Save Settings' ),
            'sections'    => apply_filters( 'betterdocs_pro_instant_answer_sections', array(
                'enable_instant_answer' => array(
                    'title' => __('Enable/Disable Instant Answer', 'betterdocs-pro'),
                    'priority'    => 0,
                    'fields' => array(
                        'ia_title' => array(
                            'type'        => 'title',
                            'label'       => __('Enable/Disable Instant Answer' , 'betterdocs-pro'),
                            'priority'    => 0,
                        ),
                        'ia_description' => array(
                            'type' => 'html',
                            'priority' => 1,
                            'html' => __( 'Display a list of articles or categories in a chat-like widget to give your visitors a chance of self-learning about your website.', 'betterdocs-pro' )
                        ),
                        'enable_disable' => array(
                            'type' => 'checkbox',
                            'priority' => 2,
                            'label'    => __( 'Enable/Disable', 'betterdocs-pro' ),
                            'default' => 1,
                            'dependency' => array(
                                1 => array(
                                    'fields' => array( 'ia_enable_preview' ),
                                    'sections' => array( 'instant_answer_tab' )
                                )
                            ),
                        ),
                        'ia_enable_preview' => array(
                            'type'        => 'checkbox',
                            'label'       => __('Enable IA Live Preview' , 'betterdocs-pro'),
                            'priority'    => 1,
                        ),
                    )
                ),
                'instant_answer_tab' => array(
                    'title' => __('Instant Answer Settings', 'betterdocs-pro'),
                    'priority'    => 1,
                    'tabs' => array(
                        'initial_content_type_settings' => array(
                            'title'  => __( 'Initial Content Settings', 'betterdocs-pro' ),
                            'priority'    => 1,
                            'fields' => array(
                                'ia_initial_title' => array(
                                    'type'        => 'title',
                                    'label'       => __('Initial Content Settings' , 'betterdocs-pro'),
                                    'priority'    => 0,
                                ),
                                'content_type' => array(
                                    'label'    => __( 'Content Type', 'betterdocs-pro' ),
                                    'type'     => 'select',
                                    'priority' => 1,
                                    'default'  => 'docs',
                                    'options'  => array(
                                        'docs'       => __('Docs', 'betterdocs-pro'),
                                        'docs_categories' => __('Docs Categories', 'betterdocs-pro'),
                                    ),
                                    'dependency'  => array(
                                        'docs' => array(
                                            'fields' => [ 'docs_list' ]
                                        ),
                                        'docs_categories' => array(
                                            'fields' => [ 'doc_category_list' ]
                                        ),
                                    ),
                                    'hide'  => array(
                                        'docs_categories' => array(
                                            'fields' => [ 'docs_list' ]
                                        ),
                                        'docs' => array(
                                            'fields' => [ 'doc_category_list' ]
                                        ),
                                    )
                                ),
                                'docs_list' => array(
                                    'label' => __( 'Select Docs', 'betterdocs-pro' ),
                                    'type'     => 'select',
                                    'priority' => 2,
                                    'multiple' => true,
                                    'options'  => $this->docs(),
                                ),
                                'doc_category_list' => array(
                                    'label' => __( 'Select Docs Categories', 'betterdocs-pro' ),
                                    'type'     => 'select',
                                    'priority' => 3,
                                    'multiple' => true,
                                    'options'  => $this->docs_categories(),
                                    'dependency' => array(
                                        '' => array(
                                            'fields' => array( 'doc_category_limit' ),
                                        )
                                    ),
                                ),
                                'doc_category_limit' => array(
                                    'type'        => 'number',
                                    'label'       => __('Number Of Categories' , 'betterdocs-pro'),
                                    'default'     => 10,
                                    'priority'    => 4
                                ),
                                'display_ia_pages' => array(
                                    'type'        => 'select',
                                    'label'       => __('Show on Pages', 'betterdocs-pro'),
                                    'priority'    => 4,
                                    'multiple' => true,
                                    'disable' => false,
                                    'default' => array('all'),
                                    'options' => $this->get_pages()
                                ),
                                'display_ia_archives' => array(
                                    'type'        => 'select',
                                    'label'       => __('Show on Archive Templates', 'betterdocs-pro'),
                                    'priority'    => 4,
                                    'multiple' => true,
                                    'disable' => false,
                                    'default' => array('all'),
                                    'options' => $this->get_all_post_types()
                                ),
                                'display_ia_texonomy' => array(
                                    'type'        => 'select',
                                    'label'       => __('Show on Texonomy Templates', 'betterdocs-pro'),
                                    'priority'    => 4,
                                    'multiple' => true,
                                    'disable' => false,
                                    'default' => array('all'),
                                    'options' => $this->get_all_registered_texonomy()
                                ),
                                'display_ia_single' => array(
                                    'type'        => 'select',
                                    'label'       => __('Show on Single Pages', 'betterdocs-pro'),
                                    'priority'    => 4,
                                    'multiple' => true,
                                    'disable' => false,
                                    'default' => array('all'),
                                    'options' => $this->get_all_post_types()
                                ),
                            )
                        ),
                        'betterdocs_chat_settings' => array(
                            'title'    => __('Chat Settings' , 'betterdocs-pro'),
                            'priority' => 2,
                            'fields'   => array(
                                'ia_chat_title' => array(
                                    'type'        => 'title',
                                    'label'       => __('Chat Settings' , 'betterdocs-pro'),
                                    'priority'    => 0,
                                ),
                                'ask_subject' => array(
                                    'type'     => 'text',
                                    'label'    => __('Custom Subject' , 'betterdocs-pro'),
                                    'priority' => 1,
                                    'default'  => '[ia_subject]',
                                    'help'     => __( 'You can use <mark>[ia_subject]</mark>, <mark>[ia_email]</mark>, <mark>[ia_name]</mark> as placeholder. <br><strong>i.e:</strong> An enquiry is placed By [ia_name] for [ia_subject].' )
                                ),
                                'ask_email' => array(
                                    'type'     => 'text',
                                    'label'    => __('Email Address' , 'betterdocs-pro'),
                                    'priority' => 2,
                                    'default'  => get_bloginfo('admin_email')
                                ),
                                'ask_thanks_title' => array(
                                    'type'     => 'text',
                                    'label'    => __('Success Message Title' , 'betterdocs-pro'),
                                    'priority' => 3,
                                    'default'  => __( 'Thanks', 'betterdocs-pro' )
                                ),
                                'ask_thanks_text' => array(
                                    'type'     => 'text',
                                    'label'    => __('Success Message Text' , 'betterdocs-pro'),
                                    'priority' => 4,
                                    'default'  => __( 'Your Message Has Been Sent Successfully', 'betterdocs-pro' )
                                ),
                            )
                        ),
                        'betterdocs_appearance_settings' => array(
                            'title'    => __('Appearance Settings' , 'betterdocs-pro'),
                            'priority' => 3,
                            'fields'   => array(
                                'ia_appearance_title' => array(
                                    'type'        => 'title',
                                    'label'       => __('Appearance Settings' , 'betterdocs-pro'),
                                    'priority'    => 0,
                                ),
                                'launcher_open_icon' => array(
                                    'type'     => 'media',
                                    'label'    => __('Instant Answer Open Icon' , 'betterdocs-pro'),
                                    'priority' => 1,
                                ),
                                'launcher_close_icon' => array(
                                    'type'     => 'media',
                                    'label'    => __('Instant Answer Close Icon' , 'betterdocs-pro'),
                                    'priority' => 2,
                                ),
                                'search_visibility_switch'  => array(
                                    'type' => 'checkbox',
                                    'label' => __('Disable Search', 'betterdocs-pro'),
                                    'priority' => 2,
                                    'hide'    => [
                                        1 => [
                                            'fields'    => [
                                                'search_placeholder_text'
                                            ]
                                        ]
                                    ],
                                    'dependency'    => [
                                        0 => [
                                            'fields'    => [
                                                'search_placeholder_text'
                                            ]
                                        ]
                                    ]
                                ),
                                'search_placeholder_text' => array(
                                    'type'     => 'text',
                                    'label'    => __('Search Placeholder' , 'betterdocs-pro'),
                                    'priority' => 2,
                                    'default'  => __( 'Search...', 'betterdocs-pro' )
                                ),
                                'answer_tab_visibility_switch' => array(
                                    'type' => 'checkbox',
                                    'label' => __('Disable Answer Tab', 'betterdocs-pro'),
                                    'priority' => 3,
                                    'hide'    => [
                                        1 => [
                                            'fields'    => [
                                                'answer_tab_icon',
                                                'answer_tab_title',
                                                'answer_tab_subtitle'
                                            ]
                                        ]
                                    ],
                                    'dependency'    => [
                                        0 => [
                                            'fields'    => [
                                                'answer_tab_icon',
                                                'answer_tab_title',
                                                'answer_tab_subtitle'
                                            ]
                                        ]
                                    ]
                                ),
                                'answer_tab_icon' => array(
                                    'type'     => 'media',
                                    'label'    => __('Instant Answer Tab Icon' , 'betterdocs-pro'),
                                    'priority' => 3,
                                ),
                                'answer_tab_title' => array(
                                    'type'     => 'text',
                                    'label'    => __('Instant Answer Tab Title' , 'betterdocs-pro'),
                                    'priority' => 4,
                                    'default'  => __( 'Answer', 'betterdocs-pro' )
                                ),
                                'answer_tab_subtitle' => array(
                                    'type'     => 'text',
                                    'label'    => __('Instant Answer Tab Subtitle' , 'betterdocs-pro'),
                                    'priority' => 5,
                                    'default'  => __( 'Instant Answer', 'betterdocs-pro' )
                                ),
                                'chat_tab_visibility_switch' => array(
                                    'type' => 'checkbox',
                                    'label' => __('Disable Chat Tab', 'betterdocs-pro'),
                                    'priority' => 6,
                                    'hide'  => [
                                        1   => [
                                            'fields'    => [
                                                'chat_tab_icon',
                                                'chat_tab_title',
                                                'chat_subtitle_one',
                                                'chat_subtitle_two'
                                            ]
                                        ]
                                    ],
                                    'dependency'    => [
                                        0   => [
                                            'fields'    => [
                                                'chat_tab_icon',
                                                'chat_tab_title',
                                                'chat_subtitle_one',
                                                'chat_subtitle_two'
                                            ]
                                        ]
                                    ]
                                ),
                                'chat_tab_icon' => array(
                                    'type'     => 'media',
                                    'label'    => __('Instant Chat Tab Icon' , 'betterdocs-pro'),
                                    'priority' => 6,
                                ),
                                'chat_tab_title' => array(
                                    'type'     => 'text',
                                    'label'    => __('Instant Chat Tab Title' , 'betterdocs-pro'),
                                    'priority' => 7,
                                    'default'  => __( 'Ask', 'betterdocs-pro' )
                                ),
                                'chat_subtitle_one' => array(
                                    'type'     => 'text',
                                    'label'    => __('Chat Tab Subtitle One' , 'betterdocs-pro'),
                                    'priority' => 8,
                                    'default'  => __( 'Stuck with something? Send us a message.', 'betterdocs-pro' )
                                ),
                                'chat_subtitle_two' => array(
                                    'type'     => 'text',
                                    'label'    => __('Chat Tab Subtitle Two' , 'betterdocs-pro'),
                                    'priority' => 9,
                                    'default'  => __( 'Generally, we reply within 24-48 hours.', 'betterdocs-pro' )
                                ),
                                'disable_reaction' => array(
                                    'type'     => 'checkbox',
                                    'label'    => __('Disable Reaction' , 'betterdocs-pro'),
                                    'priority' => 10,
                                    'hide'      => [
                                        1   => [
                                            'fields'    => [
                                                'reaction_title',
                                                'disable_response',
                                                'response_title',
                                                'disable_response_icon',
                                                'ia_response_icon_size',
                                                'ia_response_icon_color',
                                                'ia_response_title_size',
                                                'ia_response_title_color'
                                            ]
                                        ]
                                    ],
                                    'dependency'    => [
                                        0 => [
                                            'fields'    => [
                                                'reaction_title',
                                                'disable_response',
                                                'response_title',
                                                'disable_response_icon',
                                                'ia_response_icon_size',
                                                'ia_response_icon_color',
                                                'ia_response_title_size',
                                                'ia_response_title_color'
                                            ]
                                        ]
                                    ]
                                ),
                                'reaction_title' => array(
                                    'type'     => 'text',
                                    'label'    => __('Reaction Title' , 'betterdocs-pro'),
                                    'priority' => 11,
                                    'default'  => __( 'How did you feel?', 'betterdocs-pro' )
                                ),
                                'disable_response' => array(
                                    'type'     => 'checkbox',
                                    'label'    => __('Disable Response' , 'betterdocs-pro'),
                                    'priority' => 10,
                                    'hide'  => [
                                        1   => [
                                            'fields'    => [
                                                'response_title',
                                                'disable_response_icon',
                                                'ia_response_icon_size',
                                                'ia_response_icon_color',
                                                'ia_response_title_size',
                                                'ia_response_title_color'
                                            ]
                                        ]
                                    ],
                                    'dependency'    => [
                                        0   => [
                                            'fields'    => [
                                                'response_title',
                                                'disable_response_icon',
                                                'ia_response_icon_size',
                                                'ia_response_icon_color',
                                                'ia_response_title_size',
                                                'ia_response_title_color'
                                            ]
                                        ]
                                    ]
                                ),
                                'response_title' => array(
                                    'type'     => 'text',
                                    'label'    => __('Response Title' , 'betterdocs-pro'),
                                    'priority' => 11,
                                    'default'  => __( 'Thanks for the feedback', 'betterdocs-pro' )
                                ),
                                'disable_response_icon' => array(
                                    'type'     => 'checkbox',
                                    'label'    => __('Disable Response Icon' , 'betterdocs-pro'),
                                    'priority' => 10,
                                ),
                                'disable_branding' => array(
                                    'type'     => 'checkbox',
                                    'label'    => __('Disable Branding' , 'betterdocs-pro'),
                                    'priority' => 12,
                                ),
                                'chat_position' => array(
                                    'type'     => 'select',
                                    'label'    => __('Position' , 'betterdocs-pro'),
                                    'priority' => 13,
                                    'default'  => 'right',
                                    'options' => [
                                        'left' => __( 'Left', 'betterdocs-pro' ),
                                        'right' => __( 'Right', 'betterdocs-pro' ),
                                    ]
                                ),
                                'chat_zindex' => array(
                                    'type'     => 'number',
                                    'label'    => __('Z-index' , 'betterdocs-pro'),
                                    'priority' => 14,
                                    'default'  => 9999
                                ),
                                'search_not_found_1' => array(
                                    'type'     => 'text',
                                    'label'    => __('Docs not Found' , 'betterdocs-pro'),
                                    'priority' => 11,
                                    'default'  => __( 'Oops...', 'betterdocs-pro' )
                                ),
                                'search_not_found_2' => array(
                                    'type'     => 'text',
                                    'label'    => __('Docs not Found' , 'betterdocs-pro'),
                                    'priority' => 11,
                                    'default'  => __( 'We couldn’t find any articles that match your search. Try searching for a new term.', 'betterdocs-pro' )
                                ),
                            )
                        ),
                        'betterdocs_color_settings' => array(
                            'title'    => __('Style Settings' , 'betterdocs-pro'),
                            'priority' => 4,
                            'fields'   => array(
                                'ia_luncher_bg' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Launcher Background Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_luncher_bg_hover' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Launcher Hover Background Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_color_title' => array(
                                    'type'        => 'title',
                                    'label'       => __('Color Settings' , 'betterdocs-pro'),
                                    'priority'    => 0,
                                ),
                                'ia_accent_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Accent Color' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_sub_accent_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Sub Accent Color' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_heading_font_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Heading Font Size' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_heading_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Heading Color' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_sub_heading_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Sub Heading Size' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_sub_heading_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Sub Heading Color' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_searchbox_bg' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Search Box Background Color' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_searchbox_icon_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Search Box Icon Color' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'ia_searchbox_text' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Search Box Foreground Color' , 'betterdocs-pro'),
                                    'priority'    => 2,
                                ),
                                'iac_article_bg' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Docs Card Background' , 'betterdocs-pro'),
                                    'priority'    => 3,
                                ),
                                'iac_article_title_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Docs Title Font Size' , 'betterdocs-pro'),
                                    'priority'    => 4,
                                ),
                                'iac_article_title' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Docs Title Color' , 'betterdocs-pro'),
                                    'priority'    => 4,
                                ),
                                'iac_article_content_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Docs Content Font Size' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'iac_article_content' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Docs Content Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_feedback_title_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Feedback Title Size' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_feedback_title_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Feedback Title Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_feedback_icon_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Feedback Icon Size' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_feedback_icon_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Feedback Icon Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_response_icon_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Response Icon Size' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_response_icon_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Response Icon Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_response_title_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Response Title Size' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_response_title_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Response Title Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_ask_bg_color' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Ask Form Input BG Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_ask_input_foreground' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Ask Form Input Text Color' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_ask_send_disable_button_bg' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Ask Send Disabled Button Background' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_ask_send_disable_button_hover_bg' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Ask Send Disabled Button Hover Background' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_ask_send_button_bg' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Ask Send Button Background' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'ia_ask_send_button_hover_bg' => array(
                                    'type'        => 'colorpicker',
                                    'label'       => __('Ask Send Button Hover Background' , 'betterdocs-pro'),
                                    'priority'    => 5
                                ),
                                'content_heading_tag' => array(
                                    'type'        => 'title',
                                    'label'       => __('Content Area Settings' , 'betterdocs-pro'),
                                    'priority'    => 6,
                                ),
                                'iac_docs_title_font_size' => array(
                                    'type'        => 'number',
                                    'label'       => __('Docs Title Font Size' , 'betterdocs-pro'),
                                    'default'     => 20,
                                    'priority'    => 6,
                                ),
                                'iac_article_content_h1' => array(
                                    'type'        => 'number',
                                    'label'       => __('H1 Font Size' , 'betterdocs-pro'),
                                    'default'     => 26,
                                    'priority'    => 6,
                                ),
                                'iac_article_content_h2' => array(
                                    'type'        => 'number',
                                    'label'       => __('H2 Font Size' , 'betterdocs-pro'),
                                    'default'     => 24,
                                    'priority'    => 6,
                                ),
                                'iac_article_content_h3' => array(
                                    'type'        => 'number',
                                    'label'       => __('H3 Font Size' , 'betterdocs-pro'),
                                    'default'     => 22,
                                    'priority'    => 6,
                                ),
                                'iac_article_content_h4' => array(
                                    'type'        => 'number',
                                    'label'       => __('H4 Font Size' , 'betterdocs-pro'),
                                    'default'     => 20,
                                    'priority'    => 6,
                                ),
                                'iac_article_content_h5' => array(
                                    'type'        => 'number',
                                    'label'       => __('H5 Font Size' , 'betterdocs-pro'),
                                    'default'     => 18,
                                    'priority'    => 6,
                                ),
                                'iac_article_content_h6' => array(
                                    'type'        => 'number',
                                    'label'       => __('H6 Font Size' , 'betterdocs-pro'),
                                    'default'     => 16,
                                    'priority'    => 6,
                                ),
                                'iac_article_content_p' => array(
                                    'type'        => 'number',
                                    'label'       => __('Content Font Size' , 'betterdocs-pro'),
                                    'default'     => 14,
                                    'priority'    => 6,
                                ),
                            )
                        ),
                        'betterdocs_cross_domain_settings' => array( 
                            'title'    => __('Cross Domain Settings' , 'betterdocs-pro'),
                            'priority' => 5,
                            'fields'   => array(
                                'ia_cd_title' => array(
                                    'type'        => 'title',
                                    'label'       => __('Cross Domain Settings' , 'betterdocs-pro'),
                                    'priority'    => 0,
                                ),
                                'ia_cd_title' => array(
                                    'type'     => 'func',
                                    'label'    => __('Cross Domain Integration Snippet' , 'betterdocs-pro'),
                                    'help'     => __( 'To display Instant Answer widget to an external website, insert this snippet before the closing body tag.' ),
                                    'priority' => 1,
                                    'view'     => array( $this, 'cross_domain' )
                                ),
                            )
                        ),
                    )
                ),
            ) ),
        );

        return $settings;
    }

    /**
     * Get all docs
     */
    public function docs() {
        $docs = [];
        $_docs = get_posts(array(
            'post_type' => 'docs',
            'numberposts' => -1,
            'posts_per_page' => -1
        ));

        if( ! empty( $_docs ) ) {
            foreach( $_docs as $doc ) {
                $docs[ $doc->ID ] = wp_kses($doc->post_title, BETTERDOCS_PRO_KSES_ALLOWED_HTML);
            }
        }

        return $docs;
    }
    /**
     * Get All Categories for Docs Type
     * @return void
     */
    public function docs_categories() {
        $docs_terms = [];
        $terms = get_terms(array(
            'taxonomy' => 'doc_category'
        ));
        if( ! is_wp_error( $terms ) ) {
            foreach( $terms as $term ) {
                $docs_terms[ $term->term_id ] = $term->name;
            }
        }
        return $docs_terms;
    }
    /**
     * Get All Pages
     * @return void
     */
    public function get_pages() {
        $allpages = get_pages(array('post_status'  => 'publish'));
        $page_list = [];
        if($allpages ) {
            $page_list[ 'all' ] = 'All';
            foreach( $allpages as $page ) {
                $page_list[ $page->ID ] = wp_kses($page->post_title, BETTERDOCS_PRO_KSES_ALLOWED_HTML);
            }
        }
        return $page_list;
    }
    
    /**
     * Get All Post Type
     * @return void
     */
    public function get_all_post_types() {
        $args = array(
            'public'   => true,
            '_builtin' => false
         );
        $post_types = get_post_types($args, 'objects');
        // remove unnecessary types
        // $remove_keys = array('product_variation' , 'shop_order', 'shop_order_refund', 'shop_coupon');
        // $post_types = array_diff_key($post_types, array_flip($remove_keys));
        $post_list = [];
        if($post_types ) {
            $post_list[ 'all' ] = 'All';
            $post_list[ 'post' ] = 'Post';
            foreach( $post_types as $post_type ) {
                $post_list[ $post_type->name ] = $post_type->labels->name;
            }
        }
        return $post_list;
    }
    
    /**
     * Get All Registered Texonomy
     * @return void
     */
    public function get_all_registered_texonomy() {
        $args = array(
            'public'   => true,
            '_builtin' => false     
        ); 
        $taxonomies = get_taxonomies( $args, 'objects' );
        $post_list = [];
        if($taxonomies ) {
            $post_list[ 'all' ] = 'All';
            $post_list[ 'category' ] = 'Post Categories';
            $post_list[ 'post_tag' ] = 'Post Tag';
            foreach( $taxonomies as $taxonomy ) {
                $post_list[ $taxonomy->name ] = $taxonomy->labels->name;
            }
        }
        return $post_list;
    }

    public function ia_conditions()
    {
        $display_ia_pages = BetterDocs_DB::get_settings('display_ia_pages');
        $display_ia_archives = BetterDocs_DB::get_settings('display_ia_archives');
        $display_ia_texonomy = BetterDocs_DB::get_settings('display_ia_texonomy');
        $display_ia_single = BetterDocs_DB::get_settings('display_ia_single');
        $query_object = get_queried_object();

        if (
            is_page()
            && $display_ia_pages != 'off'
            && (in_array("all", $display_ia_pages) || is_page($display_ia_pages))
        ) {
            return true;
        } elseif (
            (is_tax() || is_category() || is_tag())
            && $display_ia_texonomy != 'off'
            && in_array("all", $display_ia_texonomy)
        ) {
            return true;
        } elseif (
            (is_tax() || is_category() || is_tag())
            && $display_ia_texonomy != 'off'
            && in_array($query_object->taxonomy, $display_ia_texonomy)
        ) {
            return true;
        } elseif (
            is_archive()
            && !is_tax()
            && !is_category()
            && !is_tag()
            && $display_ia_archives != 'off'
            && in_array("all", $display_ia_archives)
        ) {
            return true;
        } elseif (
            is_archive()
            && !is_tax()
            && !is_category()
            && !is_tag()
            && $display_ia_archives != 'off'
            && in_array("all", $display_ia_archives)
            || $display_ia_archives != 'off'
            && is_post_type_archive($display_ia_archives)
        ) {
            return true;
        } elseif (
            is_home()
            && ($display_ia_archives != 'off'
                && in_array("all", $display_ia_archives)
                || $display_ia_archives != 'off'
                && in_array("post", $display_ia_archives))
        ) {
            return true;
        } elseif (
            is_archive()
            && ($display_ia_archives != 'off'
                && in_array("post", $display_ia_archives)
                && is_date()
                || is_author()
                || is_day())
        ) {
            return true;
        } elseif (
            is_archive()
            && $display_ia_archives != 'off'
            && in_array("product", $display_ia_archives)
            && get_taxonomy($query_object->taxonomy)->object_type[0] === 'product'
        ) {
            return true;
        } elseif (
            !is_page()
            && is_singular()
            && $display_ia_single != 'off'
            && (in_array("all", $display_ia_single) || is_singular($display_ia_single))
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function add_ia_icon() {
        if( is_null( $this->bdocs_settings ) ) {
            $this->bdocs_settings = BetterDocs_DB::get_settings();
        }

        if( $this->setNempty('chat_position', $this->bdocs_settings ) && $this->ia_conditions() == true ) {
            echo '<div id="betterdocs-ia" class="betterdocs-'. $this->bdocs_settings['chat_position'] .'"></div>';
        } elseif ( $this->ia_conditions() == true ) {
            echo '<div id="betterdocs-ia"></div>';
        }
    }
    
    public function add_admin_ia_icon() {
        if( is_admin() ) {
            $screen = get_current_screen();
            if( $screen->id !== 'betterdocs_page_betterdocs-settings' ) {
                return;
            }
            if( is_null( $this->bdocs_settings ) ) {
                $this->bdocs_settings = BetterDocs_DB::get_settings();
            }
            if($this->setNempty('chat_position', $this->bdocs_settings ) && $this->setNempty('ia_enable_preview', $this->bdocs_settings )) {
                echo '<div id="betterdocs-ia" class="betterdocs-'. $this->bdocs_settings['chat_position'] . '"></div>';
            } else if ($this->setNempty('ia_enable_preview', $this->bdocs_settings )){
                echo '<div id="betterdocs-ia"></div>';
            }
        }   
    }

    public function register_api_endpoint() {
        // FIXME: Incase if we need to remove default cors header from WP itself.
        // remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
        register_rest_route( $this->namespace, '/ask', array(
            'methods'   => 'POST',
            'callback'  => array( $this, 'send_asked_mail' ),
            'permission_callback' => '__return_true'
        ));
        register_rest_route( $this->namespace, '/feedback', array(
            'methods'   => [ 'PUT', 'POST' ],
            'callback'  => array( $this, 'save_global_response' ),
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * Save Global Feedback
     * @param WP_REST_Request $request
     * @return void
     */
    public function save_global_response( WP_REST_Request $request ){
        $feedback = get_option( '_betterdocs_feelings', array() );
        $feelings = isset( $request['feelings'] ) ? $request['feelings'] : 'happy';
        $feedback[ $feelings ] = ( isset( $feedback[ $feelings ] ) ? intval( $feedback[ $feelings ] ) : 0 ) + 1;
        if( update_option( '_betterdocs_feelings', $feedback ) ) {
            return true;
        }
        return false;
    }



    public function send_asked_mail( WP_REST_Request $request ){
        $sanitized_data = $this->sanitize( $_POST );
        if( empty( $sanitized_data ) || ! isset( $sanitized_data['email'] ) ) {
            return;
        }

        if( is_null( $this->bdocs_settings ) ) {
            $this->bdocs_settings = BetterDocs_DB::get_settings();
        }

        $ask_subject = $this->setNempty( 'ask_subject', $this->bdocs_settings ) ? $this->bdocs_settings['ask_subject'] : '[ia_subject]';
        $to = $this->setNempty( 'ask_email', $this->bdocs_settings ) ? $this->bdocs_settings['ask_email'] : get_bloginfo('admin_email');

        $subject = html_entity_decode( $this->ready_subject( $sanitized_data, $ask_subject ), ENT_QUOTES, 'UTF-8' );
        if( isset( $sanitized_data['subject'] ) ) {
            $sanitized_data['subject'] = html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' );
        }

        $files = $request->get_file_params();
        if( ! empty( $files ) ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            
            $upload_overrides = array( 'test_form' => false );
            $new_files = $files['file'];
            $movedFiles = array();
            foreach ($new_files['name'] as $key => $value) {
                if ( $new_files['name'][$key] ) {
                    $file = array(
                        'name'     => $new_files['name'][$key],
                        'type'     => $new_files['type'][$key],
                        'tmp_name' => $new_files['tmp_name'][$key],
                        'error'    => $new_files['error'][$key],
                        'size'     => $new_files['size'][$key]
                    );
                    $movedFile = wp_handle_upload( $file, $upload_overrides );
                    if( ! isset( $movedFile['error'] ) ) {
                        $movedFiles[] = $movedFile;
                    }
                }
            }
            if( ! empty( $movedFiles ) ) {
                $sanitized_data['files'] = $movedFiles;
            }
        }
        $body = $this->simple_mail_template( $sanitized_data );
        $name = $sanitized_data['name'];
        $from = $sanitized_data['email'];
        $headers = array( 'Content-Type: text/html; charset=UTF-8', "From: $name <$from>", 'Reply-To: ' . $from );
        if( wp_mail( $to, $subject, $body, $headers ) ) {
            return true;
        }
        return false;
    }

    protected function simple_mail_template( $all_data ) {
        if( empty( $all_data ) ) {
            return '';
        }
        $output = '';
        $output = '<table width="640" cellspacing="0" cellpadding="0" border="0" align="center" style="max-width:640px; width:100%;" bgcolor="#FFFFFF"><tr><td align="center" valign="top" style="padding:10px;"><table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="max-width:600px; width:100%;">';
        foreach( $all_data as $key => $data ) {
            if( is_array( $data ) && $key == 'files' ) {
                $output .= '<tr bgcolor="#ddd"><td align="left" valign="top" style="padding:10px;">'. $key .'</td></tr>';
                $output .= '<tr><td align="left" valign="top" style="padding:10px;">';
                foreach( $data as $file_key => $file ) {
                    $output .= '<a href="'. $file['url'] .'" style="width:300px; display: inline-block">';
                        $output .= '<img width="100%" src="'. $file['url'] .'"/>';
                    $output .= '</a>';
                }
                $output .= '</td></tr>';
            } else {
                $output .= '<tr bgcolor="#ddd"><td align="left" valign="top" style="padding:10px;">'. $key .'</td></tr>';
                $output .= '<tr><td align="left" valign="top" style="padding:10px;">'. $data .'</td></tr>';
            }
        }
        $output .= '</table></td></tr></table>';
        return $output;
    }

    public function sanitize( $post_data = array() ) {
        if( ! empty( $post_data ) ) {
            $sanitized_data = array();
            foreach( $post_data as $key => $data ) {
                if( $key === 'email' ) {
                    $sanitized_data[ $key ] = sanitize_email( $data );
                } else {
                    $sanitized_data[ $key ] = esc_html( stripslashes($data) );
                }
            }
            
            return $sanitized_data;
        }
        return array();
    }
    public function ready_subject( $sanitized_data, $ask_subject ) {
        $ask_subject = ! empty( $ask_subject ) ? $ask_subject : '[ia_subject]';
        $subject = isset( $sanitized_data[ 'subject' ] ) ? str_replace( '[ia_subject]', $sanitized_data[ 'subject' ], $ask_subject ) : str_replace( '[ia_subject]', '', $ask_subject );
        $subject = isset( $sanitized_data[ 'email' ] ) ? str_replace( '[ia_email]', $sanitized_data[ 'email' ], $subject ) : str_replace( '[ia_email]', '', $subject );
        $subject = isset( $sanitized_data[ 'name' ] ) ? str_replace( '[ia_name]', $sanitized_data[ 'name' ], $subject ) : str_replace( '[ia_name]', '', $subject );

        return $subject;
    }

    public function cross_domain(){
        $bdocs_settings = BetterDocs_DB::get_settings();
        $jsObject = $this->jsObject( $bdocs_settings );
        $script = "window.betterdocs = " . wp_json_encode( $jsObject ) . ';';
        ob_start();?>
        <div class="betterdocs-cross-domain-code">
            <a href="#" data-clipboard-text='<div id="betterdocs-ia"></div><link rel="stylesheet" href="<?php echo BETTERDOCS_PRO_PUBLIC_URL . 'modules/instant-answer.css'; ?>"><style type="text/css"><?php echo $this->inline_style(); ?></style><script><?php echo $script; ?></script><script src="<?php echo BETTERDOCS_PRO_PUBLIC_URL . 'modules/instant-answer.js';?>"></script>' class="betterdocs-copy-button betterdocs-button"><?php _e( 'Copy Snippet', 'betterdocs-pro' ); ?></a>
        <pre><xmp><div id="betterdocs-ia"></div></xmp><xmp><link rel="stylesheet" href="<?php echo BETTERDOCS_PRO_PUBLIC_URL . 'modules/instant-answer.css'; ?>"></xmp><?php if( ! empty( $this->inline_style() ) ) : ?><xmp><style type="text/css"><?php echo $this->inline_style(); ?></style></xmp><?php endif; ?><xmp><script><?php echo $script; ?></script></xmp><xmp><script src="<?php echo BETTERDOCS_PRO_PUBLIC_URL . 'modules/instant-answer.js';?>"></script></xmp></pre>
        </div>
        <?php 

        echo ob_get_clean();
    }
}

if( class_exists('BetterDocs') ) {
    BetterDocs_Pro_IA::instance();
}