<?php
/**
 * Banners modal template
 *
 * @since 3.0.0
 * @package YITH WooCommerce Customize My Account Page
 * @var array $banners
 */

defined( 'YITH_WCMAP' ) || exit;

?>
<div id="banners-modal" title="<?php echo esc_html_x( 'Add banners', 'Admin banners modal title', 'yith-woocommerce-customize-myaccount-page' ); ?>" style="display: none;">
	<p><?php echo esc_html_x( 'Choose one or more banner to add to endpoint content', 'Admin banners modal content', 'yith-woocommerce-customize-myaccount-page' ); ?></p>
	<select class="banners" multiple style="width:100%">
		<?php foreach ( $banners as $banner => $banner_name ) : ?>
		<option value="<?php echo esc_attr( $banner ); ?>">
			<?php echo esc_html( $banner_name ); ?>
		</option>
		<?php endforeach; ?>
	</select>
</div>
