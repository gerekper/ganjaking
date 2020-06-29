<?php
/**
 * @var int $current_page
 */

$prev_text = __( 'Back to step %s', 'yith-point-of-sale-for-woocommerce' );
$next_text = __( 'Proceed to step %s', 'yith-point-of-sale-for-woocommerce' );
?>
<div id="yith-pos-wizard-pagination" class="yith-pos-wizard__has-current-page-data" data-current-page="<?php echo $current_page ?>">
    <span id="yith-pos-wizard-pagination__prev" data-text="<?php echo $prev_text ?>"><?php printf( $prev_text, $current_page - 1 ) ?></span>
    <span id="yith-pos-wizard-pagination__next" data-text="<?php echo $next_text ?>"><?php printf( $next_text, $current_page + 1 ) ?></span>
    <span id="yith-pos-wizard-pagination__save"><?php _e( 'Save Store', 'yith-point-of-sale-for-woocommerce' ); ?></span>
</div>