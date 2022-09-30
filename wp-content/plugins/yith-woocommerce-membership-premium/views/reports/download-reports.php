<?php
/*
 * Template for Reports Page
 */
?>

<?php if ( YITH_WCMBS_Downloads_Report()->has_downloads() ) : ?>
	<div class="postbox yith-wcmbs-reports-metabox">
		<h2><span><?php esc_html_e( 'Downloads', 'yith-woocommerce-membership' ) ?></span></h2>

		<div class="yith-wcmbs-reports-content">
			<?php yith_wcmbs_get_view( '/reports/download-reports-graphics.php' ); ?>
		</div>
	</div>

	<div class="postbox yith-wcmbs-reports-metabox">
		<h2><span><?php esc_html_e( 'Membership download reports', 'yith-woocommerce-membership' ) ?></span></h2>

		<div class="yith-wcmbs-reports-content">
			<div class="yith-wcmbs-reports-downloads-menu-wrapper">
				<ul class="yith-wcmbs-reports-downloads-menu">
					<li><a href="#" class='active' data-type="downloads-by-product"><?php esc_html_e( 'Downloads by product', 'yith-woocommerce-membership' ) ?></a></li>
					<li><a href="#" data-type="downloads-by-user"><?php esc_html_e( 'Downloads by user', 'yith-woocommerce-membership' ) ?></a></li>
				</ul>
			</div>

			<div class="yith-wcmbs-reports-downloads-content-wrapper">
				<div id="yith-wcmbs-reports-downloads-content-downloads-by-product" class="yith-wcmbs-reports-downloads-content">
					<?php yith_wcmbs_get_view( '/reports/download-reports-downloads-by-product.php' ); ?>
				</div>

				<div id="yith-wcmbs-reports-downloads-content-downloads-by-user" class="yith-wcmbs-reports-downloads-content" style="display:none">
					<?php yith_wcmbs_get_view( '/reports/download-reports-downloads-by-user.php' ); ?>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>
	<div class="yith-wcmbs-reports-metabox__blank-state">
		<span class="yith-wcmbs-reports-metabox__blank-state__icon dashicons dashicons-download"></span>
		<div class="yith-wcmbs-reports-metabox__blank-state__message"><?php esc_html_e( 'No downloads yet', 'yith-woocommerce-membership' ); ?></div>
	</div>
<?php endif; ?>
