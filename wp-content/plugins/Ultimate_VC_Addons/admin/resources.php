<?php
/**
 * Google Font Page
 *
 *  @package Google Font Page
 */

?>
<div class="wrap bsf-page-wrapper ultimate-about">
	<div class="wrap-container">
		<div class="heading-section">
			<div class="bsf-pr-header bsf-left-header" style="margin-bottom: 55px;">
				<h2><?php echo esc_attr__( 'Resources!', 'bsf' ); ?></h2>
				<div class="bsf-pr-decription"><?php esc_html_e( 'Resources used to improve your site with Google Fonts, Font Icons etc.', 'bsf' ); ?></div>
			</div>

			<div class="right-logo-section">
				<div class="bsf-company-logo">
				</div><!--company-logo-->
			</div><!--right-logo-section-->
		</div>	<!--heading section-->

		<div class="inside bsf-wrap">
			<div class="container">
				<?php
				if (
					( isset( $connects ) && ( true === $connects || 'true' == $connects ) ) ||
					( ! isset( $connects ) )
				) :
					?>
					<div class="col-sm-3 col-lg-3">
						<a class="resource-block-link" href="<?php echo admin_url( 'admin.php?page=contact-manager' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
							<div class="resource-block-icon">
								<span class="dashicons dashicons-share"></span>
							</div>
							<div class="resource-block-content">
													<?php echo esc_attr__( 'Connects', 'bsf' ); ?>
							</div>
						</a>
					</div><!--col-sm-3-->
				<?php endif; ?>

				<?php
				if (
					( isset( $icon_manager ) && ( true === $icon_manager || 'true' == $icon_manager ) ) ||
					( ! isset( $icon_manager ) )
				) :
					?>
					<div class="col-sm-3 col-lg-3">
						<a class="resource-block-link" href="<?php echo admin_url( 'admin.php?page=bsf-font-icon-manager' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
							<div class="resource-block-icon">
								<span class="dashicons dashicons-awards"></span>
							</div>
							<div class="resource-block-content">
								<?php echo esc_attr__( 'Font Icon Manager', 'bsf' ); ?>
							</div>
						</a>
					</div><!--col-sm-3-->
				<?php endif; ?>

				<?php
				if (
					( isset( $google_fonts ) && ( true === $google_fonts || 'true' == $google_fonts ) ) ||
					( ! isset( $google_fonts ) )
				) :
					?>
					<div class="col-sm-3 col-lg-3">
						<a class="resource-block-link" href="<?php echo admin_url( 'admin.php?page=bsf-google-font-manager' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
							<div class="resource-block-icon">
								<span class="dashicons dashicons-edit"></span>
							</div>
							<div class="resource-block-content">
								<?php echo esc_attr__( 'Google Fonts Manager', 'bsf' ); ?>
							</div>
						</a>
					</div><!--col-sm-3-->
				<?php endif; ?>
			</div><!--container-->

		</div><!--bsf-wrap-->
	</div><!--wrap-container-->
</div><!--wrap-->
