<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/** This is a controller that handles all of the DigLaps Stripe Payment
  * specific public static functions for the Affiliate Program.
  */
class WafpDigLabsStripePaymentsController {

  public static function load_hooks() {
    if(function_exists( 'stripe_register_payment_end_callback' ))
      stripe_register_payment_end_callback( 'WafpDigLabsStripePaymentsController::track_subscription' );

    add_action( 'stripe_payment_notification',
                'WafpDigLabsStripePaymentsController::process_stripe_event', 10, 1 );
  }

  /* Tracks when a transaction completes */
  public static function track_subscription($data)
  {
    if(isset($_COOKIE['wafp_click']))
      WafpSubscription::create($data['cust_id'], __('DigLabs Stripe Payment', 'affiliate-royale', 'easy-affiliate'), $_COOKIE['wafp_click'], __("Subscription", 'affiliate-royale', 'easy-affiliate'), $_SERVER['IP_ADDR']);
  }

  /* Tracks when a subscription transaction completes */
  public static function process_stripe_event($event) {
    if(isset($event->type) and $event->type == 'charge.succeeded') {
      WafpTransaction::track( WafpUtils::format_float($event->data->object->amount / 100.00),
                              $event->data->object->id,
                              $event->customer->description,
                              '', $event->customer->id,
                              WafpUtils::object_to_string($event), '',
                              'false', __('DigLabs Stripe Payment', 'affiliate-royale', 'easy-affiliate') );
    }
    else if(isset($event->type) and $event->type == 'charge.refunded') {
      if( $afro_txn = WafpTransaction::get_one_by_trans_num($event->data->object->id) )
        WafpTransaction::update_refund( $afro_txn->id, ($event->data->object->amount / 100.00) );
    }
  }
}
