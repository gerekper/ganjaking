<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php

use Premmerce\WooCommercePinterest\Tags\PinterestTagsTaxonomy;
use Premmerce\WooCommercePinterest\Admin\Admin;

?>

<?php
/**
 * Used vars list
 *
 * @var WP_Term[] $allPinterestTags
 */
?>

<div class="form-field term-pinterest-tag-wrap">
	<label for="<?php echo esc_attr(Admin::CATEGORY_TAGS_FIELD_KEY); ?>"> <?php esc_html_e('Pinterest hastags', 'woocommerce-pinterest'); ?></label>
	<select id="<?php echo esc_attr(Admin::CATEGORY_TAGS_FIELD_KEY); ?>"
			name="<?php echo esc_attr(Admin::CATEGORY_TAGS_FIELD_KEY); ?>" class="postform" multiple>
		<?php foreach ($allPinterestTags as $pinterestTag) : ?>
			<option value="<?php echo esc_attr($pinterestTag->term_id); ?>"> <?php echo esc_attr($pinterestTag->name); ?></option>
		<?php endforeach; ?>
	</select>
	<?php $pinterestTagsTaxUrl = admin_url('edit-tags.php?taxonomy=' . PinterestTagsTaxonomy::PINTEREST_TAGS_TAXONOMY_SLUG); ?>
	<p class="description"> 
	<?php
	/* translators: '%s' is replaced with <a> html tag with 'here word' */
	echo sprintf(__(
			esc_html('You can add new hashtags %s or on any product page using Pinterest hashtags tab.')),
	sprintf('<a href="%s">here</a>', esc_url($pinterestTagsTaxUrl))
	);
	?>
			</p>
</div>
