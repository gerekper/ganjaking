<?php
/*
 * Template Name: YITH POS template Page
 */


/**
 * The header for our theme
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link       https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package    WordPress
 * @subpackage Twenty_Nineteen
 * @since      1.0.0
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>"/>
    <link rel="profile" href="https://gmpg.org/xfn/11"/>
	<?php yith_pos_head() ?>
</head>

<body <?php yith_pos_body_class(); ?>>


<?php if ( ! is_user_logged_in() ): ?>
	<?php wc_get_template( 'yith-pos-login.php', array(), '', YITH_POS_TEMPLATE_PATH ); ?>
<?php else: ?>

	<?php

	$register_id = yith_pos_register_logged_in();

	if ( yith_pos_can_view_register() ) : ?>
        <div id="yith-pos-root" data-no-support="<?php _e( "You are using an outdated browser; please update your browser or use a new generation web browser!", 'yith-point-of-sale-for-woocommerce' ) ?>"></div>
	<?php else: ?>
		<?php
		$register_id  = isset( $_REQUEST[ 'register' ] ) ? absint( $_REQUEST[ 'register' ] ) : $register_id;
		$user_editing = isset( $_REQUEST[ 'user-editing' ] ) ? absint( $_REQUEST[ 'user-editing' ] ) : yith_pos_check_register_lock( $register_id );
		wc_get_template( 'yith-pos-store-register.php', compact( 'register_id', 'user_editing' ), '', YITH_POS_TEMPLATE_PATH ); ?>
	<?php endif; ?>
<?php endif; ?>
<?php yith_pos_footer(); ?>

</body>
</html>
