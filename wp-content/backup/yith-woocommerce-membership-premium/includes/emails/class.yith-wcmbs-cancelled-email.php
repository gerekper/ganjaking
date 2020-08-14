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

if ( !class_exists( 'YITH_WCMBS_Cancelled_Mail' ) ) :

    /**
     * Membership Cancelled Email
     *
     * @class       YITH_WCMBS_Cancelled_Mail
     * @version     1.0.0
     * @package     YITH WooCommerce Membership Premium
     * @author      Yithemes
     * @extends     WC_Email
     */
    class YITH_WCMBS_Cancelled_Mail extends WC_Email {

        public $custom_message;

        /**
         * Constructor
         */
        function __construct() {

            $this->id             = 'membership_cancelled';
            $this->customer_email = true;
            $this->title          = __( 'Cancelled Membership', 'yith-woocommerce-membership' );
            $this->description    = __( 'Cancelled Membership email is sent when a customer membership is cancelled.', 'yith-woocommerce-membership' );

            $this->template_base  = YITH_WCMBS_TEMPLATE_PATH . '/';
            $this->template_html  = 'emails/membership-cancelled.php';
            $this->template_plain = 'emails/plain/membership-cancelled.php';

            $this->subject = __( 'Membership {membership_name} cancelled', 'yith-woocommerce-membership' );
            $this->heading = __( 'Membership {membership_name} cancelled', 'yith-woocommerce-membership' );

            // Triggers
            add_action( 'yith_wcmbs_membership_cancelled_notification', array( $this, 'trigger' ) );

            // Other settings
            $this->custom_message = $this->get_option( 'custom_message', __( 'Dear Customer {firstname} {lastname}, your membership {membership_name} is cancelled.', 'yith-woocommerce-membership' ) );

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
                 * @var YITH_WCMBS_Membership $membership
                 * @var int                   $user_id
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

                    $this->find[ 'firstname' ]       = '{firstname}';
                    $this->find[ 'lastname' ]        = '{lastname}';
                    $this->find[ 'membership-name' ] = '{membership_name}';

                    $this->replace[ 'firstname' ]       = !!$user ? $user->user_firstname : '';
                    $this->replace[ 'lastname' ]        = !!$user ? $user->user_lastname : '';
                    $this->replace[ 'membership-name' ] = $membership->get_plan_title();


                    $user_email = !!$order ? $order->get_billing_email() : '';
                    if ( !$user_email ) {
                        $user_email = !!$user ? $user->user_email : '';
                    }
                    $this->recipient = $user_email;

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
                'email'          => $this
            ), '', $this->template_base );

            return ob_get_clean();
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            ob_start();
            wc_get_template( $this->template_plain, array(
                'email_heading'  => $this->get_heading(),
                'custom_message' => $this->format_string( $this->custom_message ),
                'email'          => $this

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
                    'default'     => __( 'Dear Customer {firstname} {lastname}, your membership {membership_name} is cancelled.', 'yith-woocommerce-membership' )
                ),
                'email_type'     => array(
                    'title'       => __( 'Email type', 'woocommerce' ),
                    'type'        => 'select',
                    'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
                    'default'     => 'html',
                    'class'       => 'email_type wc-enhanced-select',
                    'options'     => $this->get_email_type_options()
                )
            );
        }

    }

endif;

return new YITH_WCMBS_Cancelled_Mail();