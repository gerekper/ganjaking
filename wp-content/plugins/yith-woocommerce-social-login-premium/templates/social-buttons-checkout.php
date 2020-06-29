<?php
/**
 * Section to show social login buttons
 *
 * @package YITH WooCommerce Social Login
 * @since   1.0.0
 * @author  YITH
 */
?>
<?php

if( !empty($label_checkout) ):
?>
    <p class="woocommerce-info"><?php printf( '%s <a href="#" class="show-ywsl-box">'.__('Click here to login', 'yith-woocommerce-social-login').'</a>', $label_checkout ) ?>
    <form class="login ywsl-box">
<?php
    endif;

    YITH_WC_Social_Login_Frontend()->social_buttons('social-icons'); ?>

</form>