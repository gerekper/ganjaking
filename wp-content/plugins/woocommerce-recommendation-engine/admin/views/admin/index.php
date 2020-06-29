<?php
global $wpdb, $woocommerce_recommender;

$wp_table = new WC_Recommender_Table_Recommendations( );
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
    <div id="blockMessage">

        <span></span><br />

        <button id="bulkActionCancel">Cancel</button>
    </div>
	<form id="form-group-list" action="" method="post">
		<?php $wp_table->search_box( 'search', 'search_id' ); ?>
		<input type="hidden" name="wc-recommender-admin-action" value="bulk-recommendation-action" />
		<?php $wp_table->display(); ?>
	</form>
</div>

