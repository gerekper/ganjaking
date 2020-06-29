<strong><?php _e( 'Install Statistics from Order History', 'wc_recommender' ); ?></strong>

<p><?php _e( 'Statistics are collected from the time when the plugin is first activated.', 'wc_recommender'); ?></p>
<p><?php _e('If you would like to include statistics based on order history before the plugin was activated, click Install Statistics below' ); ?></p>

<form method="POST">

	<div id="wc-recommender-complete" style="display:none;">
		<p><?php _e( 'Installation of statistics from order history complete.', 'wc_recommender' ); ?></p>
	</div>
	
	<div id="wc-recommender-start" style="margin-top:150px;">
		<?php _e( 'Install statistics from order history now.', 'wc_recommender' ); ?>
		<br />
		<br />
		<input class='button primary' id="install-stats" type="button" value="<?php _e( 'Install Statistics' ); ?>" />
	</div>

	<div id="wc-recommender-status" style="display:none;">
		<p><?php _e( 'Installing Statistics:', 'wc_recommender' ); ?> ...</p>
	</div>

</form>
