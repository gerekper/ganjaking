<?php

class woocommerce_group_pricing_rules_admin {

	public function __construct() {
		
	}

	public function on_init() {
		
	}

	public function basic_meta_box() {
		?>
		<div id="poststuff" class="woocommerce-roles-wrap">
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php settings_fields( '_s_group_pricing_rules' ); ?>
				<?php $pricing_rules = get_option( '_s_group_pricing_rules' ); ?>

				<table class="widefat">
					<thead>
					<th>Enabled</th>
					<th>
						Group
					</th>
					<th style="display:none;">Free Shipping?</th>
					<th>
						Type
					</th>
					<th>
						Amount
					</th>

					</thead>
					<tbody>
						<?php
						$results = wc_dynamic_pricing_groups_get_all_groups();
						if ( !empty( $results ) && !is_wp_error( $results ) ):
							?>
							<?php $default = array('type' => 'percent', 'direction' => '+', 'amount' => '', 'free_shipping' => 'no'); ?>
							<?php $set_index = 0; ?>
							<?php foreach ( $results as $group ) : ?>
								<?php
								$group_id = $group['group_id'];

								$set_index++;
								$name = 'set_' . $set_index;

								$condition_index = 0;
								$index = 0;

								$rule_set = $pricing_rules[$name];
								$rule = isset( $pricing_rules[$name] ) && isset( $pricing_rules[$name]['rules'][0] ) ? $pricing_rules[$name]['rules'][0] : array();
								$rule = array_merge( $default, $rule );
								?>
								<?php $checked = isset( $rule_set['conditions'][0]['args']['groups'] ) && in_array( $group_id, $rule_set['conditions'][0]['args']['groups'] ) ? 'checked="checked"' : ''; ?>
								<tr>
									<td>
										<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions_type]" value="all" />
										<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][type]" value="apply_to" />
										<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][applies_to]" value="groups" /> 
										<input type="hidden" name="pricing_rules[<?php echo $name; ?>][collector][type]" value="always" />  
										<input class="checkbox" <?php echo $checked; ?> type="checkbox" id="group_<?php echo $group_id; ?>" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][groups][]" value="<?php echo $group_id; ?>" />
									</td>
									<td>
										<strong><?php echo $group['name']; ?></strong>
									</td>
									<td style="display:none;">

										<input <?php checked( 'yes', $rule['free_shipping'] ); ?> type="checkbox" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][free_shipping]" value="yes" />

									</td>
									<td>
										<select id="pricing_rule_type_value_<?php echo $name . '_' . $index; ?>" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][type]">
											<option <?php $this->selected( 'true', empty( $checked ) ); ?>></option>
											<option <?php $this->selected( 'fixed_product', $rule['type'] ); ?> value="fixed_product">Price Discount</option>
											<option <?php $this->selected( 'percent_product', $rule['type'] ); ?> value="percent_product">Percentage Discount</option>
										</select>
									</td>
									<td>
										<input type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][amount]" value="<?php echo esc_attr( $rule['amount'] ); ?>" />
									</td>
								</tr>
							<?php endforeach; ?>   
						<?php endif; ?>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'woocommerce-dynamic-pricing' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	private function selected( $value, $compare, $arg = true ) {
		if ( !$arg ) {
			echo '';
		} else if ( (string) $value == (string) $compare ) {
			echo 'selected="selected"';
		}
	}

}
