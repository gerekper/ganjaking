<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
?>

<div id="mfn-dashboard" class="wrap about-wrap">

	<?php include_once get_theme_file_path('/functions/admin/templates/parts/header.php'); ?>

	<div class="dashboard-tab changes">

		<div class="col col-fw">

			<h3 class="primary"><?php esc_html_e( 'Changelog', 'mfn-opts' ); ?></h3>

			<?php include get_theme_file_path('changelog.html'); ?>

			<a class="mfn-button mfn-button-primary mfn-button-fw" target="_blank" href="http://themes.muffingroup.com/betheme/documentation/changelog.html"><?php esc_html_e( 'See full changelog', 'mfn-opts' ); ?></a>

		</div>

	</div>

</div>
