<?php if (!defined('ABSPATH')) {
	die;
} ?>

<label>
	<span class="title"><?php esc_html_e('Pinterest Board', 'woocommerce-pinterest'); ?></span>
	<span class="input-text-wrap">
				<select class="pinterest-board" name="pinterest_board[]" multiple>
					<option value=""><?php esc_html_e('— No change —', 'woocommerce-pinterest'); ?></option>
					<?php
					foreach ($options as $key => $value) {
						echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
					}
					?>
				</select>
			</span>
</label>
