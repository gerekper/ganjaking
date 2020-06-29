<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
?>

<?php if( WHITE_LABEL ): ?>
	<h1><?php esc_html_e( 'Welcome to WordPress', 'mfn-opts' ); ?></h1>
<?php else: ?>
	<h1><?php esc_html_e( 'Welcome to Betheme', 'mfn-opts' ); ?></h1>
<?php endif; ?>

<p class="about-text">
	<?php if( mfn_is_registered() ): ?>

		<?php esc_html_e( 'Your copy of theme is registered.', 'mfn-opts' ); ?><br />
		<?php echo sprintf( __( 'Discover <a href="%s">pre-built websites</a>, premium <a href="%s">bundled plugins</a>, auto updates and much more.', 'mfn-opts' ), 'admin.php?page=be-websites', 'admin.php?page=be-plugins' ); ?>

	<?php else: ?>

		Please register this version of theme to get access to pre-built websites,<br />bundled premium plugins and auto updates.

	<?php endif; ?>
</p>

<div class="logo">

	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="140px" height="97px" viewBox="0 0 140 97">
		<linearGradient id="be_gradient_1" gradientUnits="userSpaceOnUse" x1="-7.1818" y1="24.8414" x2="116.5682" y2="109.8414">
			<stop  offset="0" style="stop-color:#25A8EF"/>
			<stop  offset="0.15" style="stop-color:#1399ED"/>
			<stop  offset="0.3248" style="stop-color:#058DEC"/>
			<stop  offset="0.4751" style="stop-color:#0089EB"/>
			<stop  offset="0.6059" style="stop-color:#048CEC"/>
			<stop  offset="0.7462" style="stop-color:#1195ED"/>
			<stop  offset="0.8905" style="stop-color:#27A5EF"/>
			<stop  offset="1" style="stop-color:#3CB4F2"/>
		</linearGradient>
		<path fill="url(#be_gradient_1)" d="M0,95.6V0.4h35c6.6,0,12.2,0.6,16.8,1.8c4.6,1.2,8.4,2.9,11.3,5.2c2.9,2.2,5,4.9,6.4,8.1
			c1.3,3.2,2,6.7,2,10.7c0,2.1-0.3,4.2-0.9,6.2c-0.6,2-1.6,3.8-2.9,5.6c-1.3,1.7-3,3.3-5,4.7c-2,1.4-4.5,2.6-7.4,3.7
			c6.3,1.5,10.9,4,13.9,7.4c3,3.4,4.5,7.8,4.5,13.2c0,4.1-0.8,7.8-2.4,11.3c-1.6,3.5-3.9,6.5-6.9,9.1c-3,2.6-6.8,4.6-11.2,6.1
			c-4.4,1.5-9.5,2.2-15.2,2.2H0z M22.1,40.4h11.5c2.4,0,4.6-0.2,6.6-0.5c2-0.3,3.6-1,5-1.8c1.4-0.9,2.4-2.1,3.1-3.6
			c0.7-1.5,1.1-3.4,1.1-5.8c0-2.3-0.3-4.2-0.9-5.7c-0.6-1.5-1.4-2.7-2.6-3.6s-2.7-1.6-4.5-2c-1.8-0.4-4-0.6-6.4-0.6H22.1V40.4z
			 M22.1,55.2v23.7h15.6c2.9,0,5.3-0.4,7.2-1.1c1.8-0.7,3.3-1.7,4.3-2.9s1.8-2.5,2.2-4c0.4-1.5,0.6-3,0.6-4.5c0-1.7-0.2-3.3-0.7-4.7
			c-0.5-1.4-1.2-2.6-2.4-3.5s-2.6-1.7-4.4-2.2c-1.8-0.5-4.1-0.8-6.9-0.8H22.1z"/>
		<linearGradient id="be_gradient_2" gradientUnits="userSpaceOnUse" x1="13.0315" y1="-4.5869" x2="136.7815" y2="80.4131">
			<stop  offset="0" style="stop-color:#25A8EF"/>
			<stop  offset="0.15" style="stop-color:#1399ED"/>
			<stop  offset="0.3248" style="stop-color:#058DEC"/>
			<stop  offset="0.4751" style="stop-color:#0089EB"/>
			<stop  offset="0.6059" style="stop-color:#048CEC"/>
			<stop  offset="0.7462" style="stop-color:#1195ED"/>
			<stop  offset="0.8905" style="stop-color:#27A5EF"/>
			<stop  offset="1" style="stop-color:#3CB4F2"/>
		</linearGradient>
		<path fill="url(#be_gradient_2)" d="M109.5,26.8c4.5,0,8.6,0.7,12.3,2.1c3.7,1.4,6.9,3.4,9.6,6.1c2.7,2.7,4.8,5.9,6.3,9.8
			c1.5,3.9,2.3,8.2,2.3,13.1c0,1.5-0.1,2.8-0.2,3.7c-0.1,1-0.4,1.7-0.7,2.3s-0.8,1-1.4,1.2c-0.6,0.2-1.3,0.3-2.3,0.3H96.3
			c0.7,5.7,2.4,9.8,5.2,12.3c2.8,2.6,6.4,3.8,10.8,3.8c2.4,0,4.4-0.3,6.1-0.8c1.7-0.6,3.2-1.2,4.5-1.9c1.3-0.7,2.5-1.3,3.7-1.9
			c1.1-0.6,2.3-0.9,3.5-0.9c1.6,0,2.8,0.6,3.7,1.8l5.9,7.3c-2.1,2.4-4.3,4.3-6.8,5.8c-2.4,1.5-4.9,2.7-7.4,3.5
			c-2.5,0.9-5.1,1.4-7.6,1.8c-2.5,0.3-5,0.5-7.3,0.5c-4.7,0-9.2-0.8-13.4-2.3c-4.2-1.5-7.8-3.8-10.9-6.9c-3.1-3-5.6-6.8-7.4-11.4
			c-1.8-4.5-2.7-9.8-2.7-15.8c0-4.5,0.8-8.8,2.3-12.9c1.5-4.1,3.8-7.6,6.7-10.7c2.9-3,6.4-5.5,10.5-7.3
			C99.7,27.7,104.4,26.8,109.5,26.8z M109.9,40.8c-3.9,0-6.9,1.1-9.1,3.3c-2.2,2.2-3.6,5.4-4.3,9.5h25.3c0-1.6-0.2-3.2-0.6-4.7
			c-0.4-1.5-1.1-2.9-2-4.1c-0.9-1.2-2.2-2.2-3.7-2.9C114,41.1,112.1,40.8,109.9,40.8z"/>
	</svg>

	<span class="version"><?php echo MFN_THEME_VERSION; ?></span>

	<?php if( mfn_is_registered() && version_compare( $this->version, MFN_THEME_VERSION, '>' )): ?>

		<a href="update-core.php" class="button"><?php esc_html_e( 'Update to', 'mfn-opts' ); ?> <?php echo esc_html( $this->version ); ?></a>

	<?php endif; ?>

</div>

<?php
	settings_errors('betheme_registration');
	if( ! empty($this->error) ){
		echo '<div id="setting-error-registration_error" class="error inline mfn-dashboard-error settings-error notice">';
			echo '<p><strong>'. esc_html($this->error) .'</strong></p>';
		echo '</div>';
	}
?>

<?php
	$current_screen = get_current_screen();
	$current_screen = $current_screen->base;
?>

<h2 class="nav-tab-wrapper wp-clearfix">
	<a href="admin.php?page=betheme" class="nav-tab<?php if( $current_screen == 'toplevel_page_betheme' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Welcome', 'mfn-opts' ); ?></a>
	<?php if( ! mfn_is_hosted() ): ?>
		<a href="admin.php?page=be-status" class="nav-tab<?php if( $current_screen == 'betheme_page_be-status' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'System status', 'mfn-opts' ); ?></a>
	<?php endif; ?>
	<a href="admin.php?page=be-support" class="nav-tab<?php if( $current_screen == 'betheme_page_be-support' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Manual & Support', 'mfn-opts' ); ?></a>
	<a href="admin.php?page=be-changelog" class="nav-tab<?php if( $current_screen == 'betheme_page_be-changelog' ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Changelog', 'mfn-opts' ); ?></a>
</h2>
