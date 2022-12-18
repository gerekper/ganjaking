<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * @version     3.4.0
 */

defined( 'ABSPATH' ) || exit;

$load_posts_only = porto_is_ajax() && isset( $_REQUEST['load_posts_only'] );

if ( ! $load_posts_only ) {
	get_header( 'shop' );
}

?>

<?php wc_get_template_part( 'archive-product-content' ); ?>

<?php
if ( ! $load_posts_only ) {
	get_footer( 'shop' );
}
