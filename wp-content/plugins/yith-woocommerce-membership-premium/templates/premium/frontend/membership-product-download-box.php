<?php
/**
 * @var bool   $can_download_without_credits Can the user download the product without spending credits?
 * @var int    $credits_after                User credits after downloading the product.
 * @var int    $credits_before               User credits before downloading the product.
 * @var int    $credits                      Credits needed to download the product.
 * @var string $links_html                   The links.
 */
defined( 'ABSPATH' ) || exit;

$extra_class = $can_download_without_credits ? 'yith-wcmbs-product-download-box--can-download' : 'yith-wcmbs-product-download-box--needs-credits';

yith_wcmbs_late_enqueue_assets( 'membership' );

$labels = array(
	'free-download'    => __( 'Download this product FOR FREE!', 'yith-woocommerce-membership' ),
	// translators: %s i the number of credits.
	'credits-download' => sprintf( _n( 'Download this product for 1 credit', 'Download this product for %s credits', $credits, 'yith-woocommerce-membership' ), $credits ), // phpcs:ignore WordPress.WP.I18n.MismatchedPlaceholders, WordPress.WP.I18n.MissingSingularPlaceholder
	'credits-before'   => __( 'Your credits', 'yith-woocommerce-membership' ),
	'credits-after'    => __( 'Credits after this download', 'yith-woocommerce-membership' ),
	'no-credits'       => apply_filters( 'yith_wcmbs_membership_download_non_sufficient_credits_message', __( "You don't have enough credits to download this product!", 'yith-woocommerce-membership' ) ),
);

$labels = apply_filters( 'yith_wcmbs_membership_download_labels', $labels, compact( 'credits', 'credits_after', 'credits_before', 'can_download_without_credits' ) );

?>
<div class='yith-wcmbs-product-download-box <?php echo esc_attr( $extra_class ); ?>'>
	<div class='yith-wcmbs-product-download-box__heading'>
		<?php
		if ( $can_download_without_credits ) {
			echo wp_kses_post( $labels['free-download'] );
		} else {
			echo wp_kses_post( $labels['credits-download'] );
		}
		?>
	</div>

	<?php if ( ! $can_download_without_credits ) : ?>

		<div class='yith-wcmbs-product-download-box__credits-before'>
			<span class='yith-wcmbs-product-download-box__label'><?php echo wp_kses_post( $labels['credits-before'] ); ?></span>
			<span class='yith-wcmbs-product-download-box__value'><?php echo esc_html( $credits_before ); ?></span>
		</div>

		<?php if ( $credits_after >= 0 ) : ?>
			<div class='yith-wcmbs-product-download-box__credits-after'>
				<span class='yith-wcmbs-product-download-box__label'><?php echo wp_kses_post( $labels['credits-after'] ); ?></span>
				<span class='yith-wcmbs-product-download-box__value'><?php echo esc_html( $credits_after ); ?></span>
			</div>
		<?php else : ?>
			<div class='yith-wcmbs-product-download-box__non-sufficient-credits'><?php echo wp_kses_post( $labels['no-credits'] ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	<?php if ( $credits_after >= 0 && $links_html ) : ?>
		<div class='yith-wcmbs-product-download-box__downloads'><?php echo wp_kses_post( $links_html ); ?></div>
	<?php endif; ?>
</div>
