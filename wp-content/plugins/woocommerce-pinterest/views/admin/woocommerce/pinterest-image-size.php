<?php if (!defined('ABSPATH')) {
	die;
} ?>

<?php
/**
 * Used vars list
 *
 * @var string $key
 * @var array $field
 * @var array $pinterestSize
 * @var string $fieldKey
 */

?>


<tr valign="top" id="<?php echo esc_attr($key); ?>-field">
	<th scope="row" class="titledesc">
		<label><?php echo wp_kses_post($field['title']); ?></label>
	</th>
	<td>
		<fieldset>
			<legend class="screen-reader-text"><label><?php echo wp_kses_post($field['title']); ?></label>
			</legend>
			<label for="<?php echo esc_attr($fieldKey); ?>[w]"
				   class="pinterest-image-size-labels"
			>
			<?php 
			esc_html_e('Width',
					'woocommerce-pinterest'); 
			?>
					</label>
			<input name="<?php echo esc_attr($fieldKey); ?>[w]"
				   type="number"
				   step="1" min="0"
				   id="<?php echo esc_attr($fieldKey); ?>[w]"
				   value="<?php echo esc_attr($pinterestSize['w']); ?>"
				   class="pinterest-image-size-inputs small-text">
			<br>
			<br>
			<label for="<?php echo esc_attr($fieldKey); ?>[h]"
				   class="pinterest-image-size-labels"
			>
			<?php 
			esc_html_e('Height',
					'woocommerce-pinterest'); 
			?>
					</label>
			<input name="<?php echo esc_attr($fieldKey); ?>[h]"
				   type="number"
				   step="1" min="0"
				   id="<?php echo esc_attr($fieldKey); ?>[h]"
				   value="<?php echo esc_attr($pinterestSize['h']); ?>"
				   class="pinterest-image-size-inputs small-text">
		</fieldset>
		<br>
		<input name="<?php echo esc_attr($fieldKey); ?>[crop]" type="checkbox"
			   id="<?php echo esc_attr($fieldKey); ?>[crop]" value="1"
			<?php checked($pinterestSize['crop'], true); ?>>
		<label for="<?php echo esc_attr($fieldKey); ?>[crop]">
								<?php 
								esc_html_e('Crop image to match the exact dimensions (normally images are
                    proportional)', 'woocommerce-pinterest');
								?>
		</label>

        <br><br>

        <button id="regenerate_thumbnails" class="button-secondary" type="button" data-prompt="<?php esc_html_e( 'Are you sure you want to run this tool?', 'woocommerce-pinterest' ); ?>">
            <?php esc_html_e( 'Regenerate', 'woocommerce-pinterest' ); ?>
        </button>
	</td>
</tr>