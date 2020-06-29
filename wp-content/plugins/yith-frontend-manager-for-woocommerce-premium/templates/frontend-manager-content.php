<?php
/**
 * Frontend Manager content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'yith_wcfm_before_account_content' );
?>

<div class="<?php echo $content_wrapper_classes ?>">
    <?php $obj->print_shortcode( $atts, $content, $endpoint_shortcode  ); ?>
</div>

<?php do_action( 'yith_wcfm_after_account_content' ); ?>
