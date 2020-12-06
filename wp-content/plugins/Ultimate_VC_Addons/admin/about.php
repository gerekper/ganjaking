<?php
/**
 * About Page
 *
 *  @package About Page
 */

if ( isset( $_GET['author'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
	$author = true;
} else {
	$author = false;
}
	$author_extend = '';
if ( $author ) {
	$author_extend = '&author';
}
?>

<div class="wrap about-wrap bsf-page-wrapper ultimate-about bend">
<div class="wrap-container">
	<div class="bend-heading-section ultimate-header">
	<h1><?php esc_html_e( 'Ultimate Addons for WPBakery Page Builder', 'ultimate_vc' ); ?></h1>
	<h3><?php esc_html_e( 'Welcome! You are about to begin with the most powerful addon for WPBakery Page Builder that add in many advanced features developed with love at Brainstorm Force.', 'ultimate_vc' ); ?></h3>
	<div class="bend-head-logo">
		<div class="bend-product-ver">
			<?php
			esc_html_e( 'Version', 'ultimate_vc' );
			echo ' ' . ULTIMATE_VERSION; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>
	</div><!-- bend-heading section -->

	<div class="bend-content-wrap">
	<div class="smile-settings-wrapper">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo admin_url( 'admin.php?page=about-ultimate' . $author_extend ); ?>" data-tab="about-ultimate" class="nav-tab nav-tab-active"> <?php echo esc_attr__( 'About', 'ultimate_vc' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-dashboard' . $author_extend ); ?>" data-tab="ultimate-modules" class="nav-tab"> <?php echo esc_attr__( 'Elements', 'ultimate_vc' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-smoothscroll' . $author_extend ); ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_attr__( 'Smooth Scroll', 'ultimate_vc' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-scripts-and-styles' . $author_extend ); ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_attr__( 'Scripts and Styles', 'ultimate_vc' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?> </a>
			<?php if ( $author ) : ?>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-debug-settings' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-debug" class="nav-tab"> Debug </a>
			<?php endif; ?>
		</h2>
	</div><!-- smile-settings-wrapper -->

	</hr>

	<div class="container ultimate-content">
			<div class="row">

				<div class="container bsf-grid-row" style="margin-top: 50px;">
					<div class="col-sm-6 col-lg-6">

						<div class="bsf-wrap-content">
							<div class="bsf-wrap-left-icon">
								<i class="dashicons dashicons-info abt-icon-style"></i>
							</div><!--bsf-wrap-lefticon-->
							<div class="bsf-wrap-right-content">
								<h4 class="ult-addon-heading"><?php echo esc_attr__( 'What is the Ultimate Addons for WPBakery Page Builder?', 'ultimate_vc' ); ?></h4>
								<p class="ult-addon-discription"><?php echo esc_attr__( 'The Ultimate Addons for WPBakery Page Builder is developed to extend WPBakery Page Builder to save your time in building websites. This plugin adds a number of elements to the basic set of elements that come with the page builder. All these elements are added to your WPBakery Page Builder editor and can be used just like any other built-in features.', 'ultimate_vc' ); ?></p>
								<p class="ult-addon-discription"><?php echo esc_attr__( 'Info Box, Fancy Text, Interactive Banner, Flip Box, Info Circle are just some popular elements you can try now.', 'ultimate_vc' ); ?></p>
							</div><!--bsf-wrap-right-content-->
						</div><!--bsf wrap content-->						


						<div class="bsf-wrap-content">
							<div class="bsf-wrap-left-icon">
								<i class="dashicons dashicons-universal-access-alt abt-icon-style"></i>
							</div><!--bsf-wrap-lefticon-->
							<div class="bsf-wrap-right-content">
								<h4 class="ult-addon-heading"><?php echo esc_attr__( 'We stand by you!', 'ultimate_vc' ); ?></h4>
								<p class="ult-addon-discription"><?php echo esc_attr__( 'With', 'ultimate_vc' ); ?> <a target="_blank" href="https://www.youtube.com/playlist?list=PL1kzJGWGPrW9CDWwdAWrd_9YQsh1z7u6O"><?php echo esc_attr__( 'several video tutorials', 'ultimate_vc' ); ?></a> <?php echo esc_attr__( 'and a', 'ultimate_vc' ); ?> <a target="_blank" href="https://ultimate.brainstormforce.com/support/"><?php echo esc_attr__( 'dedicated support team', 'ultimate_vc' ); ?></a>,<?php echo esc_attr__( ' we assure complete help and support whenever you need us.', 'ultimate_vc' ); ?></p>
								<p class="ult-addon-discription"><?php echo esc_attr__( 'Go ahead and explore the Ultimate elements of the Ultimate Addons for WPBakery Page Builder!', 'ultimate_vc' ); ?></p>
							</div><!--bsf-wrap-right-content-->
						</div><!--bsf wrap content-->

					</div><!--vc_col-sm-6-->

					<div class="col-sm-6 col-lg-6">

						<div class="bsf-wrap-content">
							<div class="bsf-wrap-left-icon">
								<i class="dashicons dashicons-layout abt-icon-style"></i>
							</div><!--bsf-wrap-lefticon-->
							<div class="bsf-wrap-right-content">
								<h4 class="ult-addon-heading"><?php echo esc_attr__( 'Advanced Elements', 'ultimate_vc' ); ?></h4>
								<p class="ult-addon-discription"><?php echo esc_attr__( 'With the Ultimate Addons, you are free to use the basic elements of WPBakery Page Builder with', 'ultimate_vc' ); ?> <a target="_blank" href="https://cloudup.com/ce1eKqH_PDp"><?php echo esc_attr__( 'several advanced elements', 'ultimate_vc' ); ?></a> <?php echo esc_attr__( 'that it adds in the editor.', 'ultimate_vc' ); ?></p>
							</div><!--bsf-wrap-right-content-->
						</div><!--bsf wrap content-->

						<div class="bsf-wrap-content">
							<div class="bsf-wrap-left-icon">
								<i class="dashicons dashicons-playlist-video abt-icon-style"></i>
							</div><!--bsf-wrap-lefticon-->
							<div class="bsf-wrap-right-content">
								<h4 class="ult-addon-heading"><?php echo esc_attr__( 'Row Features (Parallax / Video Background)', 'ultimate_vc' ); ?></h4>
								<p class="ult-addon-discription"><?php echo esc_attr__( 'Tired of the plain old rows that the page builder offers? You can now add', 'ultimate_vc' ); ?> <a target="_blank" href="https://ultimate.brainstormforce.com/backgrounds/"><?php echo esc_attr__( 'amazing parallax effects', 'ultimate_vc' ); ?></a> <?php echo esc_attr__( 'on your website. Now', 'ultimate_vc' ); ?> <a target="_blank" href="https://cloudup.com/cwZLz6UYl9r"><?php echo esc_attr__( 'edit your row', 'ultimate_vc' ); ?></a> <?php echo esc_attr__( 'and see what you\'ve got! Almost all kinds of parallax effects, video background, row separator and much more…', 'ultimate_vc' ); ?></p>
							</div><!--bsf-wrap-right-content-->
						</div><!--bsf wrap content-->


						<div class="bsf-wrap-content">
							<div class="bsf-wrap-left-icon">
								<i class="dashicons dashicons-thumbs-up abt-icon-style"></i>
							</div><!--bsf-wrap-lefticon-->
							<div class="bsf-wrap-right-content">
								<h4 class="ult-addon-heading"><?php echo esc_attr__( 'Google Fonts / Icons Font', 'ultimate_vc' ); ?></h4>
								<p class="ult-addon-discription"><?php echo esc_attr__( 'We\'ve shipped about 360 useful icons that you can use in most of the Ultimate elements. That\'s not it! The Icon Font Manager within the plugin introduces several font icons that can be easily used with WPBakery Page Builder. We also have a built-in Google Fonts manager to help you shortlist the text fonts of your choice and use them with any of Ultimate Addon elements.', 'ultimate_vc' ); ?></p>
							</div><!--bsf-wrap-right-content-->
						</div><!--bsf wrap content-->

					</div><!--vc_col-sm-6-->

				</div><!--container end-->

			</div><!--col-md-12-->
		</div><!-- .ultimate-content -->
	</div><!-- bend-content-wrap -->
</div><!-- .wrap-container -->
</div><!-- .bend -->
