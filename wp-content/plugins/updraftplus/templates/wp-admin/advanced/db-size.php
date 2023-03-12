<?php
if (!defined('UPDRAFTPLUS_DIR')) {
	die('No direct access allowed');
}

$search_placeholder = __('Search for table', 'updraftplus');
?>
<div class="advanced_tools db_size">
	<p>
		<strong><?php _e('Total Size', 'updraftplus'); ?>: <span class="total-size"></span></strong>
	</p>

	<p>
		<input type="text" class="db-search" placeholder="<?php echo $search_placeholder; ?>" title="<?php echo $search_placeholder; ?>" aria-label="<?php echo $search_placeholder; ?>"/>
		<a href="#" class="button db-search-clear"><?php _e('Clear', 'updraftplus'); ?></a>
		<a href="#" class="button-primary db-size-refresh"><?php _e('Refresh', 'updraftplus'); ?></a>
	</p>

	<table class="wp-list-table widefat striped">
		<thead>
			<tr>
				<th><strong><?php _e('Table name', 'updraftplus'); ?></strong></th>
				<th><strong><?php _e('Records', 'updraftplus'); ?></strong></th>
				<th><strong><?php _e('Data size', 'updraftplus'); ?></strong></th>
				<th><strong><?php _e('Index size', 'updraftplus'); ?></strong></th>
				<th><strong><?php _e('Type', 'updraftplus'); ?></strong></th>
			</tr>
		</thead>

		<tbody class="db-size-content"></tbody>
	</table>
</div>