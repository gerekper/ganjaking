<?php
class WC_Conditional_Content_Input_Cart_Category_Select {
	public function __construct() {
		// vars
		$this->type = 'Cart_Category_Select';

		$this->defaults = array(
		    'multiple' => 0,
		    'allow_null' => 0,
		    'choices' => array(),
		    'default_value' => array(),
		    'class' => ''
		);
	}

	public function render($field, $value = null) {

		$field = array_merge($this->defaults, $field);
		if (!isset($field['id'])) {
			$field['id'] = sanitize_title($field['id']);
		}

		$current = isset($value['categories']) ? $value['categories'] : array();
		$choices = $field['choices'];
		?>
		<table style="width:100%;">
			<tr>
				<td style="width:32px;"><?php _e('Quantity', 'wc_conditional_content'); ?></td>
				<td><?php _e('Categories', 'wc_conditional_content'); ?></td>
			</tr>
			<tr>
				<td style="width:32px; vertical-align:top;">
					<input type="text"  id="<?php echo $field['id']; ?>_qty" name="<?php echo $field['name']; ?>[qty]" value="<?php echo isset($value['qty']) ? $value['qty'] : 1; ?>"  />
				</td>
				<td>
					<select id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>[categories][]" class="chosen_select <?php echo esc_attr($field['class']); ?>" multiple="multiple" data-placeholder="<?php echo (isset($field['placeholder']) ? $field['placeholder'] : __('Search...', 'wc_conditional_content')); ?>">
						<?php
						foreach ($choices as $choice => $title) {
							$selected = in_array($choice, $current);
							echo '<option value="' . esc_attr($choice) . '" ' . selected($selected, true, false) . '">' . esc_html($title) . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
		</table>

		<?php
	}

}
?>