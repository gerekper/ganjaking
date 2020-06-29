<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

/**
 * ================= *
 * Standard Step Mapping
 * before version 2.0.0
 *
 * 0 -> Login
 * 1 -> Billing
 * 2 -> Shipping
 * 3 -> Order Info
 * 4 -> Payment
 * ================= *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$enable_checkout_login_reminder = 'yes' == get_option( 'woocommerce_enable_checkout_login_reminder', 'yes' );
$show_back_to_cart_button       = 'yes' == get_option( 'yith_wcms_nav_enable_back_to_cart_button', 'yes' );
$is_user_logged_in              = is_user_logged_in();
$show_login_step                = ! $is_user_logged_in && $enable_checkout_login_reminder;

$button_class = apply_filters( 'yith_wcms_step_button_class', 'button alt yith-wcms-button' );

$labels = apply_filters( 'yith_wcms_timeline_labels', array(
        'next'          => __( 'Next', 'yith-woocommerce-multi-step-checkout' ),
        'skip_login'    => __( 'Next', 'yith-woocommerce-multi-step-checkout' ),
        'prev'          => __( 'Previous', 'yith-woocommerce-multi-step-checkout' ),
        'login'         => _x( 'Login', 'Checkout: user timeline', 'yith-woocommerce-multi-step-checkout' ),
        'billing'       => _x( 'Billing', 'Checkout: user timeline', 'yith-woocommerce-multi-step-checkout' ),
        'shipping'      => _x( 'Shipping', 'Checkout: user timeline', 'yith-woocommerce-multi-step-checkout' ),
        'order'         => _x( 'Order Info', 'Checkout: user timeline', 'yith-woocommerce-multi-step-checkout' ),
        'payment'       => _x( 'Payment Info', 'Checkout: user timeline', 'yith-woocommerce-multi-step-checkout' ),
        'back_to_cart'  => _x( 'Back to cart', 'Frontend: button label', 'yith-woocommerce-multi-step-checkout' )
    )
);

$display = apply_filters( 'yith_wcms_timeline_display', 'horizontal' );
$remove_shipping_step = 'yes' == get_option( 'yith_wcms_timeline_remove_shipping_step', 'no' );

$timeline_args = array(
    'labels'                => $labels,
    'is_user_logged_in'     => $is_user_logged_in,
    'display'               => $display,
    'style'                 => get_option( 'yith_wcms_timeline_template', 'text' ),
    'remove_shipping_step'  => $remove_shipping_step
);

$timeline_template = apply_filters( 'yith_wcms_timeline_template', 'checkout-timeline.php' );

$enable_nav_button = 'yes' == get_option( 'yith_wcms_nav_buttons_enabled', 'yes' );

$enable_checkout_login_reminder = 'yes' == get_option( 'woocommerce_enable_checkout_login_reminder', 'yes' );

$default_login_message = _x( 'If you have already registered, please, enter your details below. If you are a new customer, please, go to the Billing &amp; Shipping section.', '[Frontend] Default message for returning customer on checkout page', 'yith-woocommerce-multi-step-checkout' );
$login_message         = apply_filters( 'yith_wcms_form_checkout_login_message', get_option( 'yith_wcms_form_checkout_login_message', $default_login_message ) );

//load timeline template
function_exists( 'yith_wcms_checkout_timeline' ) && yith_wcms_checkout_timeline( $timeline_template, $timeline_args );

do_action( 'woocommerce_before_checkout_form', $checkout );
?>

    <div id="checkout-wrapper" class="timeline-<?php echo $display ?>">
        <div id="checkout_coupon" class="woocommerce_checkout_coupon">
            <?php do_action( 'yith_woocommerce_checkout_coupon', $checkout ); ?>
        </div>
        <?php if( $enable_checkout_login_reminder ) : ?>
            <div id="checkout_login" class="woocommerce_checkout_login">
                <?php do_action( 'yith_wcms_checkout_login_form', $login_message ) ?>
            </div>
        <?php endif; ?>

        <?php

        // If checkout registration is disabled and not logged in, the user cannot checkout
        if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
            echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in before proceeding to checkout.', 'yith-woocommerce-multi-step-checkout' ) );
            return;
        }

        // filter hook for include new pages inside the payment method
        $get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', yith_get_checkout_url() ); ?>

        <form name="checkout" method="post" class="checkout woocommerce-checkout processing" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

            <?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

                <div class="checkout_billing <?php echo $is_user_logged_in ? 'logged-in' : 'not-logged-in'; echo $enable_checkout_login_reminder ? ' show-login-reminder' : ' hide-login-reminder'; ?>" id="customer_billing_details">
                    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                </div>
                <?php if( ! $remove_shipping_step ) : ?>
                    <div class="checkout_shipping" id="customer_shipping_details">
                        <?php
                        do_action( 'woocommerce_before_checkout_shipping_form', $checkout );
                        do_action( 'woocommerce_checkout_shipping' );
                        do_action( 'woocommerce_checkout_after_customer_details' );
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php do_action( 'woocommerce_checkout_before_order_review',$checkout ); ?>

            <div id="order_review" class="woocommerce-checkout-review-order">
                <div id="order_info" class="woocommerce-checkout-review-order">
                    <h3 id="order_review_heading"><?php _e( 'Your order', 'yith-woocommerce-multi-step-checkout' ); ?></h3>
                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                    <?php do_action( 'yith_woocommerce_checkout_order_review' ); ?>
                </div>

                <div id="order_checkout_payment" class="yith-woocommerce-checkout-payment">
                    <h3 id="payment_heading">
                        <?php echo apply_filters( 'yith_wcms_payment_step_section_title', __( 'Payment', 'yith-woocommerce-multi-step-checkout' ) ); ?>
                    </h3>
                    <?php do_action( 'yith_woocommerce_checkout_payment' ); ?>
                </div>
            </div>

            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

        </form>

        <div id="form_actions" class="<?php echo $enable_nav_button ? 'enabled' : 'disabled'; ?>" data-step="<?php echo $show_login_step ? 'login' : 'billing'; ?>">
            <input type="button" class="<?php echo $button_class ?> prev" name="checkout_prev_step" value="<?php echo $labels['prev'] ?>" data-action="prev">
            <span></span>
            <input type="button" class="<?php echo $button_class ?> next" name="checkout_next_step" value="<?php echo $enable_checkout_login_reminder && ! is_user_logged_in() ? $labels['skip_login'] : $labels['next'] ?>" data-action="next">
            <?php if( $show_back_to_cart_button ) : ?>
                <a href="<?php echo wc_get_cart_url(); ?>" id="yith-wcms-back-to-cart-button" class="<?php echo $button_class ?>"><?php echo $labels['back_to_cart'] ?></a>
            <?php endif; ?>
        </div>
    </div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout );