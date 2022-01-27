<?php if (!defined('ABSPATH')) {
	die;
}
use Premmerce\SDK\V2\FileManager\FileManager;


/**
 * Used variables
 *
 * @var array $images Array of product images from gallery + feature image
 * @var array $dbPins Array of the ids pinned product images (pins created from product and fetched from database).
 * @var bool $dbCarousel Carousel is pinned.
 * @var array[] $boards Array of the Pinterest boards. Keys is boards ids, values is board data arrays
 * @var string[] $productBoards Array of Pinterest boards tied with current product
 * @var string[] $boardsFromProductCategories Array of boards retrieved from product categories settings
 * @var array $productPinterestTags Array of tags will be added to all pins created from this product
 * @var string $title Pinterest product title
 * @var string $description Pinterest product description
 * @var string $descriptionFieldTip
 * @var WP_Post $post
 * @var array $tagsBoxRenderingArgs
 * @var string $tagsSettingsUrl Plugin settings page tags section link
 * @var string $boardsSettingsUrl Plugin settings page boards section link
 * @var string $descriptionVariablesButtons Rendered description variables buttons template
 * @var FileManager $fileManager FileManager instance
 * @var array $pinDescriptionVariables
 *
 * @todo: find better names for tabs
 *
 * @todo: Use actions to render each tabs content. Put each tabs code to separate template. Create class with methods to call on actions to render
 */

?>
<div class="woocommerce">
	<div class="panel-wrap">

		<ul class="wc-tabs">

			<li class="woocommerce-pinterest-product-tabs product_image">
				<a href="#woocommerce_pinterest_product_image">
					<span> <?php esc_html_e('Images', 'woocommerce-pinterest'); ?></span>
				</a>
			</li>

			<li class="woocommerce-pinterest-product-tabs product_boards">
				<a href="#woocommerce_pinterest_product_boards">
					<span> <?php esc_html_e('Boards', 'woocommerce-pinterest'); ?></span>
				</a>
			</li>

			<li class="woocommerce-pinterest-product-tabs product_tags">
				<a href="#woocommerce_pinterest_product_tags">
					<span> <?php esc_html_e('Hashtags', 'woocommerce-pinterest'); ?></span>
				</a>
			</li>

			<li class="woocommerce-pinterest-product-tabs product_description">
				<a href="#woocommerce_pinterest_product_description">
					<span> <?php esc_html_e('Summary', 'woocommerce-pinterest'); ?></span>
				</a>
			</li>

		</ul>

		<div id="woocommerce_pinterest_product_image"
			 class="panel woocommerce_options_panel woocommerce-pinterest-options-panel hidden">
			<div class="woo-pinterest-pin-this-wrapper options_group">
				<input type="hidden" name="woocommerce_pinterest_metabox" value="1">
				<label>
					<input type="checkbox"
						   name="woocommerce_pinterest_pinned"
						   value="1"
						   data-pinterest-pinned
						<?php checked(!empty($dbPins), true, true); ?>
					>
					<?php esc_html_e('Pin product image', 'woocommerce-pinterest'); ?>
				</label>
			</div>

			<div data-pinterest-pin-container>
				<?php foreach ($images as $image) : ?>
					<div class="woo-pinterest-image-wrapper 
					<?php 
					echo in_array($image,
						$dbPins, true)  ? 'checked' : ''
					?>
						">
						<button class="check" type="button" tabindex="-1">
							<span class="media-modal-icon"></span>
							<span class="screen-reader-text">Deselect</span>
						</button>
						<?php echo wp_get_attachment_image($image); ?>

						<input type="checkbox"
							   name="woocommerce_pinterest_images[]"
							   data-pinterest-image-checkbox="<?php echo esc_attr($image); ?>"
							   value="<?php echo esc_attr($image); ?>"
							<?php checked(true, in_array($image, $dbPins, true)); ?>>

					</div>
				<?php endforeach; ?>
				<div id="pinterest-control-buttons">
					<button type="button" class="button" data-pinterest-image-toggle-all="1">
						<?php esc_html_e('Select All', 'woocommerce-pinterest'); ?>
					</button>
					<button type="button" class="button" data-pinterest-image-toggle-all="0">
						<?php esc_html_e('Unselect All', 'woocommerce-pinterest'); ?>
					</button>
                    <label for="woocommerce_pinterest_images_carousel" style="float:none;margin:0;padding:5px 0 0 5px;display:inline-block;width:auto">
                        <input type="checkbox" name="woocommerce_pinterest_images_carousel" id="woocommerce_pinterest_images_carousel" value="1" <?php echo $dbCarousel ? 'checked' : ''; ?>>
                        <span><?php esc_html_e('Also make carousel with selected images', 'woocommerce-pinterest'); ?></span>
                    </label>
                    <button class="button right" data-pinterest-custom-image-upload><span
								class="dashicons dashicons-format-image woo-pinterest-custom-image-icon"></span>
						<?php esc_html_e('Upload additional images', 'woocommerce-pinterest'); ?>

					</button>
				</div>
			</div>
		</div>

		<div id="woocommerce_pinterest_product_boards"
			 class="panel woocommerce-options-panel woocommerce-pinterest-options-panel hidden">

			<div class="woo-pinterest-boards-from-product-categories">
				<?php if ($boardsFromProductCategories) : ?>
				<p class="woocommerce-pinterest-options-panel__sub-title">
					<?php
					/* translators: '%s' is replaced with <a> tag with 'settings' word */
					echo sprintf(__(
						esc_html('Boards from %s:'),
						'woocommerce-pinterest'), sprintf('<a href="%s">' . esc_html(__('settings', 'woocommerce-pinterest')) . '</a>', esc_url($boardsSettingsUrl)));
					?>
						</p>
				<div>
					<ul>
						<?php foreach ($boardsFromProductCategories as $boardId) : ?>
							<li><?php echo esc_html($boards[$boardId]['name']); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>

			<div class="woo-pinterest-select-board-wrapper">
				<?php if (!empty($boards)) : ?>
					<label> <span
								class="woocommerce-pinterest-options-panel__sub-title"> 
								<?php
								if ($boardsFromProductCategories) {
									esc_html_e('You can also select other boards: ', 'woocommerce-pinterest');
								} else {
									esc_html_e('Select boards', 'woocommerce-pinterest');
								}
								?>
								</span>
						<br>
						<select id="woocommerce-pinterest-product-boards-select" name="woocommerce_pinterest_product_board[]" multiple>
							<?php foreach ($boards as $board) : ?>
								<option value="<?php echo esc_attr($board['id']); ?>" 
														  <?php 
															selected(in_array($board['id'],
															$productBoards, true));
															?>
									 > <?php echo esc_html($board['name']); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				<?php else : ?>
					<?php 
					esc_html_e('You have no boards. Please, make sure you connected your Pinterest account.',
						'woocommerce-pinterest'); 
					?>
				<?php endif; ?>
			</div>


		</div>

		<div id="woocommerce_pinterest_product_tags"
			 class="panel woocommerce-options-panel woocommerce-pinterest-options-panel hidden">

			<div>
				<div class="woocommerce-pinterest-options-panel__title">
					<label for="woocommerce_pinterest_pin_description">
						<?php esc_html_e('Add pinterest hashtags:', 'woocommerce-pinterest'); ?>
					</label>
				</div>

				<?php post_tags_meta_box($post, $tagsBoxRenderingArgs); ?>


				<p>
				<?php 
				esc_html(__('Pinterest hashtags will be added (order will be preserved)',
						'woocommerce-pinterest'));
				?>
						</p>
				<ul>
					<?php foreach ($productPinterestTags as $productPinterestTag) : ?>
						<li><?php echo esc_html('#' . $productPinterestTag); ?></li>
					<?php endforeach; ?>
				</ul>

				<?php
				/* translators: '%s' is replaced with <a> html tag*/
				echo sprintf(__(esc_html('You can change the hashtags rules on the %s'),
					'woocommerce-pinterest'), sprintf('<a href="%s">' . esc_html(__('settings page')) . '</a>', esc_url($tagsSettingsUrl)));
				?>
			</div>
		</div>

		<div id="woocommerce_pinterest_product_description"
			 class="panel woocommerce-options-panel woocommerce-pinterest-options-panel hidden">

			<div style="margin-bottom: 15px;">
                <div class="woocommerce-pinterest-options-panel__title">
                    <label for="woocommerce_pinterest_pin_title">
                      <?php /* translators: '%s' is replaced with html tag */ ?>
                      <?php echo sprintf(__(esc_html('Pinterest pin title %s'), 'woocommerce-pinterest'), wc_help_tip($descriptionFieldTip)); ?>
                    </label>
                </div>
                <textarea
                        name="woocommerce_pinterest_pin_title_template"
                        id="woocommerce_pinterest_pin_title"
                        style="width: 100%" rows="3"
                ><?php echo esc_html($title); ?></textarea>
                <?php $fileManager->includeTemplate('admin/woocommerce/pin-title-variables.php', array(
                      'variables' => $pinDescriptionVariables,
                      'fieldName' => 'woocommerce_pinterest_pin_title'
                )); ?>
            </div>
            <div>
				<div class="woocommerce-pinterest-options-panel__title">
					<label for="woocommerce_pinterest_pin_description">
						<?php /* translators: '%s' is replaced with html tag */ ?>
						<?php echo sprintf(__(esc_html('Pinterest pin description %s'), 'woocommerce-pinterest'), wc_help_tip($descriptionFieldTip)); ?>
					</label>
				</div>
				<textarea
						name="woocommerce_pinterest_pin_description_template"
						id="woocommerce_pinterest_pin_description"
						style="width: 100%" rows="3"
				><?php echo esc_html($description); ?></textarea>
                <?php $fileManager->includeTemplate('admin/woocommerce/pin-description-variables.php', array(
                      'variables' => $pinDescriptionVariables,
                      'fieldName' => 'woocommerce_pinterest_pin_description'
                )); ?>
            </div>
		</div>
	</div>
</div>
