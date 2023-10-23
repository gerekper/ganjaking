<?php
/**
 * Frontend Manager content
 *
 * @package YITH\FrontendManager\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DO_ACTION: yith_wcfm_before_account_content
 *
 * Allows to render some content before the section content.
 */
do_action( 'yith_wcfm_before_account_content' );
?>

<div class="<?php echo esc_attr( $content_wrapper_classes ); ?>">
	<?php $obj->print_shortcode( $atts, $content, $endpoint_shortcode ); ?>
</div>

<?php
/**
 * DO_ACTION: yith_wcfm_after_account_content
 *
 * Allows to render some content after the section content.
 */
do_action( 'yith_wcfm_after_account_content' );
?>
