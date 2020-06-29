<?php if ( ! defined('ABSPATH')) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var array $categoryBoardRelation
 * @var array $boards
 * @var int $categoryId
 * @var int $fieldId
 */


$relationId = isset($categoryBoardRelation['id']) ? $categoryBoardRelation['id'] : '';
$nameBase = sprintf('woocommerce-pinterest-category-board-relations[%d][%d]', $categoryId, $fieldId);

?>

<div class="woocommerce-pinterest-board-select-container">
	<input
			type="hidden"
			name="<?php echo esc_attr($nameBase . '[relation_id]'); ?>"
			value="<?php echo esc_attr($relationId); ?>"
			data-relations-id
	>

	<select
		name="<?php echo esc_attr($nameBase . '[board_id]'); ?>"
		class="<?php echo 'woocommerce-pinterest-category-board-relations'; ?>"
	>
		<option value=""><?php esc_html_e('Default board'); ?></option>

		<?php foreach ($boards as $board) : ?>
			<?php $selected = isset($categoryBoardRelation['board_id']) && $categoryBoardRelation['board_id'] === $board['id']; ?>
			<option value="<?php echo esc_attr($board['id']); ?>" <?php selected($selected); ?>><?php echo esc_html($board['name']); ?></option>
		<?php endforeach; ?>
	</select>

	<a href="#" class="woocommerce-pinterest-remove-board-select"><?php esc_attr_e('Remove', 'woocommerce-pinterest'); ?></a>
</div>
