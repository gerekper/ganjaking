<?php
    $post_status = ( isset( $_GET['view'] ) ) ? $_GET['view'] : '';
?>

<select class="dashboard-search-field dashboard-select-view" name="view" id="dashboard-select-view">
	<option value=""><?php _e( 'Views', 'betterdocs-pro' );?></option>
	<option value="most_viewed"<?php echo ( 'most_viewed' === $post_status ) ? ' selected' : '' ?>>
		<?php _e( 'Most Viewed', 'betterdocs-pro' );?>
	</option>
	<option value="least_viewed"<?php echo ( 'least_viewed' === $post_status ) ? ' selected' : '' ?>>
		<?php _e( 'Least Viewed', 'betterdocs-pro' );?>
	</option>
</select>