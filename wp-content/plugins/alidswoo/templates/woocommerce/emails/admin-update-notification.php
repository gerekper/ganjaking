<?php
/**
 * Created by PhpStorm.
 * User: Denis Zharov
 * Date: 08.08.2018
 * Time: 15:46
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php do_action( 'adsw_output_admin_update_notification_html', $adsw_products, $adsw_settings ); ?>

<?php do_action( 'woocommerce_email_footer', $email );
