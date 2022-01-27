<?php if ( ! defined('ABSPATH')) {
	die;
} ?>

<?php

use Premmerce\WooCommercePinterest\Admin\Admin;
use \Premmerce\WooCommercePinterest\Tags\PinterestTagsTaxonomy;

/**
 * Vars used in template
 *
 * @var WP_Term[] $allPinterestTags
 * @var WP_Term[] $selectedTagsIds
 */
?>

<tr>
	<th scope="row">
		<label><?php esc_html_e(__('Pinterest hashtags', 'woocommerce-pinterest')); ?></label>
	</th>
	<td>
		<select id="<?php echo esc_attr(Admin::CATEGORY_TAGS_FIELD_KEY); ?>" name="<?php echo esc_attr(Admin::CATEGORY_TAGS_FIELD_KEY); ?>[]" multiple>
			<?php
			foreach ($allPinterestTags as $pinterestTag) {
				echo '<option value="' . esc_attr($pinterestTag->term_id) . '" ' . selected(in_array($pinterestTag->term_id, $selectedTagsIds, true)) . ' >' . esc_attr($pinterestTag->name) . '</option>';
			}
			?>
		</select>

		<?php $pinterestTagsTaxUrl = admin_url('edit-tags.php?taxonomy=' . PinterestTagsTaxonomy::PINTEREST_TAGS_TAXONOMY_SLUG); ?>
		<p class="description"> 
		<?php
		/* translators: '%s' is replaced with html tag a */
		echo sprintf( esc_html(__('You can add new hashtags %s or on any product page using Product hashtags tab of Pinterest data meta box.')),
			sprintf('<a href="%s">here</a>', esc_url($pinterestTagsTaxUrl)));
		?>
			</p>
	</td>
</tr>
