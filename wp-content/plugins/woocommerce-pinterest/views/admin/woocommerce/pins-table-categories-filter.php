<?php use Premmerce\WooCommercePinterest\Admin\Table\PinsTable;

if (!defined('ABSPATH')) {
	die;
}

/**
 * Available vars list
 *
 * @var array $boards
 * @var string $selectedBoard
 */
?>

<div class="alignleft actions">
	<select name="<?php echo esc_attr(PinsTable::FILTER_BY_BOARD_INPUT_NAME); ?>" class="select short">
		<option value="" <?php selected($selectedBoard, ''); ?>><?php esc_html_e('All boards', 'woocommerce-pinterest'); ?></option>
		<?php foreach ($boards as $boardId => $boardTitle) : ?>

		<option value="<?php echo esc_attr($boardId); ?>" <?php selected($selectedBoard, $boardId); ?>><?php echo esc_html($boardTitle); ?></option>

		<?php endforeach; ?>
	</select>

	<?php
	$buttonText = _x('Filter', 'Verb. Filter pins by boards in this case.', 'woocommerce-pinterest');

	submit_button($buttonText, 'secondary', 'woocommerce-pinterest-board-filter', false);
	?>
</div>
