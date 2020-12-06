<?php if ( ! defined('ABSPATH')) {
	die;
}  ?>

<?php
/**
 * Used vars list
 *
 * @var array $categories
 * @var int $selectedCategoryId
 */
?>


<select class="woocommerce-pinterest-google-category-select">
	<option value="" ><?php esc_html_e('Not selected', 'woocommerce-pinterest'); ?></option>
	<?php foreach ($categories as $categoryData) : ?>
		<option value="<?php echo esc_attr($categoryData['id']); ?>" <?php selected($selectedCategoryId, $categoryData['id']); ?>>
			<?php echo esc_html($categoryData['name']); ?>
		</option>
	<?php endforeach; ?>
</select>
