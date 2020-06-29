<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
if (! class_exists('WC_Email')){
    require_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
}

// include css inliner
if ( ! class_exists( 'Emogrifier' ) && class_exists( 'DOMDocument' ) ) {
    include_once( WC()->plugin_path() . '/includes/libraries/class-emogrifier.php' );
}
*/

if ( !class_exists( 'YITH_WCMBS_Welcome_Mail' ) ) :

    /**
     * Membership Welcome Email
     *
     * @class       YITH_WCMBS_Welcome_Mail
     * @version     1.0.0
     * @package     YITH WooCommerce Membership Premium
     * @author      Yithemes
     * @extends     WC_Email
     */
    class YITH_WCMBS_Welcome_Mail extends WC_Email {

        public $custom_message;

        public $user_id;

        /**
         * Constructor
         */
        function __construct() {

            $this->id             = 'membership_welcome';
            $this->customer_email = true;
            $this->title          = __( 'Membership - Welcome', 'yith-woocommerce-membership' );
            $this->description    = __( 'The welcome email is sent when a customer becomes member.', 'yith-woocommerce-membership' );

            $this->template_base  = YITH_WCMBS_TEMPLATE_PATH . '/';
            $this->template_html  = 'emails/membership-welcome.php';
            $this->template_plain = 'emails/plain/membership-welcome.php';

            $this->subject = __( 'Welcome to membership {membership_name}', 'yith-woocommerce-membership' );
            $this->heading = __( 'Welcome to membership {membership_name}', 'yith-woocommerce-membership' );

            // Triggers
            add_action( 'yith_wcmbs_new_member_notification', array( $this, 'trigger' ) );

            // Other settings
            $this->custom_message = $this->get_option( 'custom_message', __( 'Dear {firstname} {lastname}, welcome to membership {membership_name}. Expiration Date: {membership_expire_date}', 'yith-woocommerce-membership' ) );

            // Call parent constructor
            parent::__construct();
        }


        /**
         * Trigger.
         *
         * @param array $args
         */
        function trigger( $args ) {

            if ( !$this->is_enabled() ) {
                return;
            }

            if ( $args ) {
                /**
                 * @var int                          $user_id
                 * @var YITH_WCMBS_Membership | bool $membership
                 */
                $default = array(
                    'user_id'    => 0,
                    'membership' => false
                );

                $args = wp_parse_args( $args, $default );
                extract( $args );

                if ( $membership instanceof YITH_WCMBS_Membership ) {
                    $this->object = $membership;
                    $user         = get_user_by( 'id', $user_id );
                    $order        = isset( $membership->order_id ) ? wc_get_order( $membership->order_id ) : false;

                    $plan_post = get_post( $membership->id );
                    if ( !$plan_post )
                        return;

                    $this->find[ 'firstname' ]              = '{firstname}';
                    $this->find[ 'lastname' ]               = '{lastname}';
                    $this->find[ 'membership-name' ]        = '{membership_name}';
                    $this->find[ 'membership-expire-date' ] = '{membership_expire_date}';

                    $this->replace[ 'firstname' ]              = !!$user ? $user->user_firstname : '';
                    $this->replace[ 'lastname' ]               = !!$user ? $user->user_lastname : '';
                    $this->replace[ 'membership-name' ]        = $membership->get_plan_title();
                    $this->replace[ 'membership-expire-date' ] = apply_filters( 'yith_wcmbs_email_membership_status_expiration_date', $membership->get_formatted_date( 'end_date' ), $membership, $this );

                    $user_email = !!$order ? $order->get_billing_email() : '';
                    if ( !$user_email ) {
                        $user_email = !!$user ? $user->user_email : '';
                    }
                    $this->recipient = $user_email;

                    $this->user_id = $user_id;

                    if ( $this->get_recipient() ) {
                        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                    }
                }

            }
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            ob_start();
            wc_get_template( $this->template_html, array(
                'email_heading'  => $this->get_heading(),
                'custom_message' => $this->format_string( $this->custom_message ),
                'email'          => $this,
                'user_id'        => $this->user_id
            ), '', $this->template_base );

            return ob_get_clean();
        }

        /**
         * Get content plain.
         *
         * @return string
         */
        function get_content_plain() {
            ob_start();
            wc_get_template( $this->template_plain, array(
                'email_heading'  => $this->get_heading(),
                'custom_message' => $this->format_string( $this->custom_message ),
                'email'          => $this,
                'user_id'        => $this->user_id
            ), '', $this->template_base );

            return ob_get_clean();
        }


        /**
         * Initialise Settings Form Fields - these are generic email options most will use.
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled'        => array(
                    'title'   => __( 'Enable/Disable', 'woocommerce' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable this email notification', 'woocommerce' ),
                    'default' => 'yes'
                ),
                'subject'        => array(
                    'title'       => __( 'Email Subject', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->subject ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'heading'        => array(
                    'title'       => __( 'Email Heading', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->heading ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'custom_message' => array(
                    'title'       => __( 'Custom Message', 'yith-woocommerce-membership' ),
                    'type'        => 'textarea',
                    'description' => sprintf( __( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->custom_message ),
                    'placeholder' => '',
                    'default'     => __( 'Dear {firstname} {lastname}, welcome to membership {membership_name}. Expiration Date: {membership_expire_date}', 'yith-woocommerce-membership' )
                ),
                'email_type'     => array(
                    'title'       => __( 'Email type', 'woocommerce' ),
                    'type'        => 'select',
                    'description' => __( 'Choose the format of the email.', 'woocommerce' ),
                    'default'     => 'html',
                    'class'       => 'email_type wc-enhanced-select',
                    'options'     => $this->get_email_type_options()
                )
            );
        }

    }

endif;

return new YITH_WCMBS_Welcome_Mail();