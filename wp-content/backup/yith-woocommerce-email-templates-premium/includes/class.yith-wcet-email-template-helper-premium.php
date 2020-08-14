<?php
/**
 * Email Template Helper PREMIUM
 *
 * @author  Yithemes
 * @package YITH WooCommerce Email Templates
 * @version 1.2.0
 */

defined( 'YITH_WCET' ) || exit; // Exit if accessed directly

if ( !class_exists( 'YITH_WCET_Email_Template_Helper_Premium' ) ) {
    /**
     * YITH_WCET_Email_Template_Helper_Premium class.
     * The class manage all the admin behaviors.
     *
     * @since    1.2.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCET_Email_Template_Helper_Premium extends YITH_WCET_Email_Template_Helper {

        private $_wp_email = false;

        /**
         * Constructor
         *
         * @access public
         * @since  1.2.0
         */
        public function __construct() {
            parent::__construct();

            add_filter( 'yith_wcet_premium_email_extra_settings', array( $this, 'premium_email_extra_settings' ), 10, 2 );

            add_action( 'yith_wcet_pre_header', array( $this, 'print_pre_header' ), 10, 2 );

            add_action( 'wp_ajax_yith_wcet_send_test_email', array( $this, 'send_test_email' ) );
            add_action( 'wp_ajax_nopriv_yith_wcet_send_test_email', array( $this, 'send_test_email' ) );

            /**
             * WordPress Emails
             */
            add_filter( 'wp_mail', array( $this, 'filter_wp_email_args' ) );
        }

        /**
         * get the YITH_WCET_WP_Email
         *
         * @return bool|YITH_WCET_WP_Email
         */
        public function get_wp_email() {
            if ( class_exists( 'WC_Email' ) && !$this->_wp_email ) {
                require_once( YITH_WCET_INCLUDES_PATH . '/class.yith-wcet-wp-email.php' );
                $this->_wp_email = new YITH_WCET_WP_Email();
            }
            return $this->_wp_email;
        }

        /**
         * return true if the message is a WC email
         *
         * @param string $message
         *
         * @return bool
         */
        public function is_wc_email( $message = '' ) {
            $is_wc_email                     = false;
            $check_woocommerce_email_strings = array(
                'yith-wcet-woocommerce-email-wrapper',
                'template_header_image',
            );
            foreach ( $check_woocommerce_email_strings as $string ) {
                if ( strpos( $message, $string ) ) {
                    $is_wc_email = true;
                    break;
                }
            }
            return apply_filters( 'yith_wcet_check_email_is_wc_email', $is_wc_email, $message );
        }

        /**
         * return true if the message is already in HTML format
         *
         * @param string $message
         *
         * @since 1.3.15
         * @return bool
         */
        public function is_already_html( $message = '' ) {
            $is_already_html          = false;
            $check_html_email_strings = array(
                '<html>',
                '<html ',
                '<head>',
                '<head ',
                '<body>',
                '<body ',
            );
            foreach ( $check_html_email_strings as $string ) {
                if ( strpos( $message, $string ) !== false ) {
                    $is_already_html = true;
                    break;
                }
            }
            return apply_filters( 'yith_wcet_check_email_is_already_html', $is_already_html, $message );
        }


        /**
         * add content type in headers and return headers
         *
         * @param array|string $headers
         * @param string       $content_type
         *
         * @return array|string
         */
        public function add_content_type_in_headers( $headers, $content_type ) {
            $headers = !!$headers ? $headers : '';

            if ( is_array( $headers ) ) {
                $headers[ 'Content-Type' ] = $content_type;
            } else {
                $content_type_text = 'Content-Type:' . $content_type . "\r\n";
                if ( strpos( strtolower( $headers ), 'content-type' ) ) {
                    $headers = preg_replace( '/content-type.+\\n/i', $content_type_text, $headers );
                } else {
                    $headers = $content_type_text . $headers;
                }
            }

            return $headers;
        }

        /**
         * is content type plain in headers?
         *
         * @param $headers
         *
         * @return bool
         */
        public function header_content_type_is_plain( $headers ) {
            if ( is_array( $headers ) && isset( $headers[ 'Content-Type' ] ) && 'text/plain' !== $headers[ 'Content-Type' ] ) {
                return false;
            }
            if ( is_string( $headers ) && $headers && !strpos( strtolower( $headers ), 'text/plain' ) ) {
                return false;
            }
            return true;
        }

        /**
         * @param array $args
         *
         * @return array
         */
        public function filter_wp_email_args( $args = array() ) {
            $template = get_option( 'yith-wcet-email-template-yith_wcet_wp_email', 'default' );
            if ( $template !== 'default' && isset( $args[ 'subject' ] ) && isset( $args[ 'message' ] ) && !$this->is_wc_email( $args[ 'message' ] ) && !$this->is_already_html( $args[ 'message' ] ) ) {
                WC()->mailer();

                $email_heading = apply_filters( 'yith_wcet_wp_email_subject', $args[ 'subject' ], $args );
                $email         = $this->get_wp_email();

                // set content type in headers
                $headers           = !empty( $args[ 'headers' ] ) ? $args[ 'headers' ] : '';
                $was_plain         = $this->header_content_type_is_plain( $headers );
                $args[ 'headers' ] = $this->add_content_type_in_headers( $headers, $email->get_content_type() );
                $message           = $was_plain ? nl2br( $args[ 'message' ] ) : $args[ 'message' ];
                $message           = apply_filters( 'yith_wcet_wordpress_email_message', $message, $args );

                ob_start();
                do_action( 'woocommerce_email_header', $email_heading, $email );
                echo $message;
                do_action( 'woocommerce_email_footer', $email );
                $args[ 'message' ] = $this->mail_content_styling( ob_get_clean() );

                $args[ 'yith_wcet_is_wc_email' ] = false;
            }
            return apply_filters( 'yith_wcet_filter_wp_email_args', $args );
        }

        public function send_test_email() {
            if ( !empty( $_REQUEST[ 'preview' ] ) && !empty( $_REQUEST[ 'template_id' ] ) && !empty( $_REQUEST[ 'send_to' ] ) ) {
                global $current_email;
                $current_email = 'preview';
                $template_id   = $_REQUEST[ 'template_id' ];
                $mailer        = WC()->mailer();

                $email_heading = __( 'HTML Email Template', 'woocommerce' );

                ob_start();
                do_action( 'woocommerce_email_header', $email_heading, $current_email );
                wc_get_template( '/views/html-email-template-preview.php', array( 'template_id' => $template_id ), YITH_WCET_TEMPLATE_PATH, YITH_WCET_TEMPLATE_PATH );
                do_action( 'woocommerce_email_footer', $current_email );
                $message = ob_get_clean();

                $to      = $_REQUEST[ 'send_to' ];
                $subject = __( 'Test Email', 'yith-woocommerce-email-templates' );

                if ( $mailer->send( $to, $subject, $message ) ) {
                    $result = array(
                        'message' => __( 'Email sent', 'yith-woocommerce-email-templates' )
                    );
                } else {
                    $result = array(
                        'error' => __( 'Error: Email not sent', 'yith-woocommerce-email-templates' )
                    );
                }
            } else {
                $result = array(
                    'error' => __( 'Error: Email not sent', 'yith-woocommerce-email-templates' )
                );
            }
            wp_send_json( $result );
        }

        protected function _init_templates() {
            $templates = array(
                'emails/email-footer.php',
                'emails/email-header.php',
                'emails/email-order-details.php',
                'emails/email-order-items.php',
                'emails/email-styles.php'
            );

            $this->templates = apply_filters( 'yith_wcet_templates', $templates );
        }

        /**
         * print the pre-header for Gmail, iOS, Mail app, Outlook, etc..
         *
         * @param WC_Email $email
         * @param          $template
         */
        public function print_pre_header( $email, $template ) {
            if ( apply_filters( 'yith_wcet_print_pre_header', true, $email, $template ) ) {
                $custom_pre_header = apply_filters( 'yith_wcet_custom_pre_header_text', null, $email, $template );

                if ( !is_null( $custom_pre_header ) ) {
                    $pre_header = $custom_pre_header;
                } else {
                    $pre_header = $this->generate_pre_header_from_email( $email, $template );
                }

                $pre_header = apply_filters( 'yith_wcet_pre_header_text', $pre_header, $email, $template );

                if ( $pre_header ) {
                    wc_get_template( 'emails/email-pre-header.php', array( 'pre_header' => $pre_header, 'email' => $email ), '', YITH_WCET_TEMPLATE_PATH . '/' );
                }

            }
        }

        /**
         * generate the pre-header from an email
         *
         * @param WC_Email|string $email
         * @param string          $template
         *
         * @return string
         */
        public function generate_pre_header_from_email( $email, $template ) {
            $pre_header = '';
            if ( $email instanceof WC_Email ) {
                global $wp_filter, $wp_actions;

                $wp_actions_temp = $wp_actions;

                // Remove every WordPress Filters and Actions
                $actions_to_remove = apply_filters( 'yith_wcet_actions_to_remove_before_print_pre_header', array(
                    'woocommerce_email_header',
                    'woocommerce_email_footer',
                    'woocommerce_email_order_details',
                    'woocommerce_email_order_meta',
                    'woocommerce_email_customer_details',
                ), $email, $template );

                $saved_filters = array();

                foreach ( $actions_to_remove as $action ) {
                    if ( isset( $wp_filter[ $action ] ) ) {
                        if ( is_object( $wp_filter[ $action ] ) ) {
                            $saved_filters[ $action ] = clone $wp_filter[ $action ];
                        } else {
                            $saved_filters[ $action ] = $wp_filter[ $action ];
                        }
                    }
                    remove_all_actions( $action );
                }

                $content = '';
                try {
                    $content = $email->get_content();
                } catch ( Exception $e ) {
                    error_log( $e->getMessage() );
                }

                $content = strip_tags( $content );
                $content = trim( preg_replace( '/\s\s+/', ' ', $content ) );

                // Restore WordPress Filters and Actions
                $wp_actions = $wp_actions_temp;
                foreach ( $saved_filters as $action => $hook ) {
                    $wp_filter[ $action ] = $hook;
                }

                $pre_header = $email->get_heading() . ' - ' . $content;
                $pre_header = mb_substr( $pre_header, 0, 100 );

            } elseif ( 'preview' === $email ) {
                $pre_header = sprintf( __( 'You have received an order from %s. The order is as follows:', 'woocommerce' ), __( 'User', 'woocommerce' ) );
            }

            return $pre_header;
        }

        /**
         * Custom Template
         *
         * Filters wc_get_template for custom templates
         *
         * @return string
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function custom_template( $located, $template_name, $args, $template_path, $default_path ) {
            if ( in_array( $template_name, $this->templates ) ) {

                if ( $template_name == 'emails/email-styles.php' ) {
                    global $current_email;
                    $template = yith_wcet_get_email_template( $current_email );
                    $meta     = yith_wcet_get_template_meta( $template );
                    if ( $meta ) {
                        $premium_style = isset( $meta[ 'premium_mail_style' ] ) && $meta[ 'premium_mail_style' ] > 0 ? $meta[ 'premium_mail_style' ] : '';
                        $template_name = "emails/email-styles{$premium_style}.php";
                    }
                }

                /**
                 * to override templates of Email Templates put them into YOUR_THEME_FOLDER/yith-woocommerce-email-templates/emails/
                 */
                $template = locate_template( 'yith-woocommerce-email-templates/' . $template_name );

                if ( !$template ) {
                    $template = $this->locate_template_in_plugin( $template_name );
                }

                return apply_filters( 'yith_wcet_get_template', $template, $template_name );
            }

            return $located;
        }

        public function premium_email_extra_settings( $settings, $templates_array ) {
            $email_templates_url = admin_url( 'edit.php?post_type=yith-wcet-etemplate' );

            $info_text = sprintf(
                __( 'Please note: you can manage your email templates through the %sEmail Templates%s menu.', 'yith-woocommerce-email-templates' ),
                "<a class='yith-wcet-info-btn' href='$email_templates_url'>",
                "</a>"
            );

            $settings[] = array(
                'title' => __( 'YITH WooCommerce Email Settings', 'yith-woocommerce-email-templates' ),
                'type'  => 'title',
                'desc'  => __( 'Select templates for emails.', 'yith-woocommerce-email-templates' ) . ' ' . $info_text,
                'id'    => 'yith-wcet-email-extra-settings'
            );

            $mailer = WC()->mailer();
            $emails = $mailer->get_emails();

            foreach ( $emails as $email ) {
                if ( apply_filters( 'yith_wcet_hide_email_in_settings', false, $email ) )
                    continue;

                $settings[] = array(
                    'id'       => 'yith-wcet-email-template-' . $email->id,
                    'name'     => $email->title,
                    'type'     => 'select',
                    'desc_tip' => sprintf( __( 'Select the email template that you want to use for the %s email', 'yith-woocommerce-email-templates' ), $email->title ),
                    'class'    => 'yith-wcet-select2',
                    'options'  => $templates_array,
                    'default'  => 'default'
                );
            }

            $settings[] = array(
                'id'       => 'yith-wcet-email-template-yith_wcet_wp_email',
                'name'     => __( 'WordPress Emails', 'yith-woocommerce-email-templates' ),
                'type'     => 'select',
                'desc_tip' => sprintf( __( 'Select the email template that you want to use for the %s email', 'yith-woocommerce-email-templates' ), __( 'WordPress Emails', 'yith-woocommerce-email-templates' ) ),
                'class'    => 'yith-wcet-select2',
                'options'  => $templates_array,
                'default'  => 'default'
            );

            $settings[] = array(
                'type' => 'sectionend',
                'id'   => 'yith_wcet_email_extra_settings'
            );

            return $settings;
        }


    }
}