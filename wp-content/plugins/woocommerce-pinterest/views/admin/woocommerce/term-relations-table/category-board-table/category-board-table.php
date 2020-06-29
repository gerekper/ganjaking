<?php if ( ! defined('ABSPATH')) {
	die;
}

use \Premmerce\WooCommercePinterest\Admin\Table\TermRelationsTable\WcCategoryPinterestBoardRelationsTable;

/**
 * Used vars list
 *
 * @var $tableCategoryArray
 * @var WcCategoryPinterestBoardRelationsTable $table
 */

?>

<tr valign="top">
	<th scope="row" class="titledesc">

	</th>
	<td class="forminp">
		<div id="woocommerce-pinterest-category-board-table" class="woocommerce-pinterest-settings-list-table">
				<?php $table->display(); ?>
			<div class="woocommerce-pinterest-list-table-save">
				<?php submit_button(__('Save boards settings', 'woocommerce-pinterest'), 'primary', 'save-category-boards-table', false); ?>
			</div>
			<div class="woocommerce-pinterest-table-saved-message">
				<p><?php esc_html_e('Saved', 'woocommerce-pinterest'); ?></p>
			</div>
		</div>
	</td>
</tr>
