<?php
global $wpdb, $woocommerce_recommender;

$wp_table = new WC_Recommender_Table_Session_History( );
$wp_table->prepare_items();
?>

<style type="text/css">
	table #actions {
		width:125px;
		text-align: right;
	}

	table .rebuild-cell {
		text-align: right;
	}
</style>
<div class="wc_recommendations_table">
	<form id="form-group-list" action="" method="post">
		<input type="hidden" name="wc-recommender-admin-action" value="bulk-recommendation-action" />
		<?php $wp_table->display(); ?>
	</form>
</div>
