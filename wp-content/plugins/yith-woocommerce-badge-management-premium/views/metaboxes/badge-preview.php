<?php
/**
 * Badge Preview Metabox
 *
 * @package YITH\BadgeManagement\Views
 */

global $pagenow;
$button = 'post-new.php' === $pagenow ? 'publish' : 'save';

?>
<div class="yith-wcbm-preview-metabox">
	<div class="yith-wcbm-preview-title">
		<?php esc_html_e( 'Preview', 'yith-woocommerce-badges-management' ); ?>
	</div>

	<?php if ( defined( 'YITH_WCBM_PREMIUM' ) ) : ?>
		<div class="yith-wcbm-preview-description">
			<?php esc_html_e( 'Use the drag and drop to move the badge above the product image.', 'yith-woocommerce-badges-management' ); ?>
		</div>
	<?php endif; ?>

	<div class="yith-wcbm-preview-wrapper">
		<div class="yith-wcbm-preview-container">
			<div class="yith-wcbm-badge"></div>
		</div>
	</div>
</div>

<input type="submit" name="<?php echo esc_html( $button ); ?>" id="<?php echo esc_html( $button ); ?>" class="button button-primary button-large yith-wcbm-save-badge-button yith-plugin-fw__button--xxl" value="<?php esc_html_e( 'Save badge', 'yith-woocommerce-badges-management' ); ?>">

<script type="text/html" id="tmpl-yith-wcbm-css-badge-html">
	<?php yith_wcbm_get_view( 'badges/css.php', array( 'is_template' => true ) ); ?>
</script>
<script type="text/html" id="tmpl-yith-wcbm-advanced-badge-html">
	<?php
	$args = array(
		'is_template' => true,
		'badge'       => yith_wcbm_get_badge_object( get_post() ),
	);
	yith_wcbm_get_view( 'badges/advanced.php', $args );
	?>
</script>
