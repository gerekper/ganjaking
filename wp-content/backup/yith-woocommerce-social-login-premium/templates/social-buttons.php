<?php
/**
 * Section to show social login buttons
 *
 * @package YITH WooCommerce Social Login
 * @since   1.0.0
 * @author  YITH
 */
?>

<div class="wc-social-login">
    <style>
        a.ywsl-social{
            text-decoration: none;
            display: inline-block;
            margin-right: 2px;
        }
    </style>
    <?php if( !empty($label)):?>
        <p class="ywsl-label"><?php echo $label ?></p>
        <p class="socials-list">
    <?php
        endif;

    YITH_WC_Social_Login_Frontend()->social_buttons('social-icons', false, $args); ?>

    </p>
</div>