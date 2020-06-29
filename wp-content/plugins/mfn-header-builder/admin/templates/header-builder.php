<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}
?>

<?php if( defined( 'THEME_VERSION' ) && version_compare( THEME_VERSION, '20.9.6.3', '>=' ) ): ?>
	<noscript>
		You need to enable JavaScript to run this app.
	</noscript>
	<div id="mfnHeaderBuilder"></div>
<?php else: ?>
	<div class="mfn-message mfn-error">
		This plugin can not be activated because it requires at least <b>BeTheme version 20.9.6.3</b>.<br />Please <a href="admin.php?page=betheme">update</a> your theme.
	</div>
<?php endif; ?>
