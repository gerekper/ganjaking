<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var WP_Term $term
 * @var bool $includeDataCategoryId
 * @var int $level Term nesting level
 */
?>

<?php $dataCategoryId = 'data-category-id="' . esc_attr($term->term_taxonomy_id) . '"'; ?>

<strong>
	<a class="row-title" href="<?php echo esc_url(get_edit_term_link($term->term_taxonomy_id)); ?>">
		<span <?php echo $includeDataCategoryId ? esc_html($dataCategoryId) : ''; ?>>
			<?php echo esc_html(str_repeat(' &#x2014; ', $level) . $term->name); ?>
		</span>
	</a>
</strong>
