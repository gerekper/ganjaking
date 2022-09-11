<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

wp_clear_scheduled_hook( 'ywsbs_renew_orders' );
wp_clear_scheduled_hook( 'ywsbs_check_subscription_payment' );
wp_clear_scheduled_hook( 'ywsbs_cancel_subscription_expired' );
wp_clear_scheduled_hook( 'ywsbs_trigger_email_renew_reminder' );
wp_clear_scheduled_hook( 'ywsbs_trigger_email_before_subscription_expired' );
wp_clear_scheduled_hook( 'ywsbs_resume_orders' );
wp_clear_scheduled_hook( 'ywsbs_check_overdue_subscriptions' );
wp_clear_scheduled_hook( 'ywsbs_check_suspended_subscriptions' );
wp_clear_scheduled_hook( 'ywsbs_trash_pending_subscriptions' );
wp_clear_scheduled_hook( 'ywsbs_trash_cancelled_subscriptions' );
wp_clear_scheduled_hook( 'ywsbs_pay_renew_subscription_orders' );
