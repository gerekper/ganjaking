<?php

class woocommerce_totals_pricing_rules_admin {

	public function __construct() {
	}


	public function advanced_metabox() {
		?>
		<div id="woocommerce-pricing-totals">
			<?php settings_errors(); ?>
			<h3><span><?php _e( 'Advanced Rules', 'woocommerce-dynamic-pricing' ); ?></span></h3>

			<form method="post" action="options.php">
				<?php settings_fields( '_a_totals_pricing_rules' ); ?>
				<?php $pricing_rule_sets = get_option( '_a_totals_pricing_rules', array() ); ?>
				<div id="woocommerce-pricing-rules-wrap" class="inside" data-setindex="<?php echo count( $pricing_rule_sets ); ?>">
					<?php $this->meta_box_javascript(); ?>
					<?php $this->meta_box_css(); ?>
					<?php if ( $pricing_rule_sets && is_array( $pricing_rule_sets ) && sizeof( $pricing_rule_sets ) > 0 ) : ?>
						<?php $this->create_rulesets( $pricing_rule_sets ); ?>
					<?php endif; ?>
				</div>
				<button id="woocommerce-pricing-add-ruleset" type="button" class="button button-secondary">Add Pricing Group</button>
				<p class="submit" style="float:right;">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
				</p>
			</form>
			<?php
		}

		public function create_rulesets( $pricing_rule_sets ) {


			foreach ( $pricing_rule_sets as $name => $pricing_rule_set ) {
				$pricing_rules = isset( $pricing_rule_set['rules'] ) ? $pricing_rule_set['rules'] : null;
				$pricing_conditions = isset( $pricing_rule_set['conditions'] ) ? $pricing_rule_set['conditions'] : null;
				$collector = isset( $pricing_rule_set['collector'] ) ? $pricing_rule_set['collector'] : array('type' => 'cart_total', 'args' => array('cats' => array()));
				$targets = isset( $pricing_rule_set['targets'] ) ? $pricing_rule_set['targets'] : null;

				$date_from = isset( $pricing_rule_set['date_from'] ) ? $pricing_rule_set['date_from'] : '';
				$date_to = isset( $pricing_rule_set['date_to'] ) ? $pricing_rule_set['date_to'] : '';

				$invalid = isset( $pricing_rule_set['invalid'] );
				$validation_class = $invalid ? 'invalid' : '';
				?>
				<div id="woocommerce-pricing-ruleset-<?php echo $name; ?>" class="woocommerce_pricing_ruleset <?php echo $validation_class; ?>">
					<h4 class="first"><?php echo (isset( $pricing_rule_set['admin_title'] ) && !empty( $pricing_rule_set['admin_title'] ) ? esc_attr( $pricing_rule_set['admin_title'] ) : __( 'Order Total Pricing', 'woocommerce-dynamic-pricing' )); ?><a href="#" data-name="<?php echo $name; ?>" class="delete_pricing_ruleset" ><img  src="<?php echo WC_Dynamic_Pricing::plugin_url(); ?>/assets/images/delete.png" title="delete this set" alt="delete this set" style="cursor:pointer; margin:0 3px;float:right;" /></a></h4>
					<div>
						<p>
							<label for="pricing_rule_admin_title_<?php echo $name; ?>"><?php _e( 'Admin Title', 'woocommerce-dynamic-pricing' ); ?>:</label>
							<input type="text" name="pricing_rules[<?php echo $name; ?>][admin_title]" value="<?php echo (isset( $pricing_rule_set['admin_title'] ) ? esc_attr( $pricing_rule_set['admin_title'] ) : ''); ?>" />
						</p>
					</div>

					<?php
					if ( is_array( $collector ) && count( $collector ) > 0 ) {
						$this->create_collector( $collector, $name );
					}
					?>

					<div id="woocommerce-pricing-targets-<?php echo $name; ?>" class="section" style="" >
						<?php
						if ( is_array( $targets ) && count( $targets ) > 0 ) {
							$this->create_target_selector( $targets, $name, $collector );
						} else {
							$this->create_target_selector( array(), $name, $collector );
						}
						?>
					</div>

					<div id="woocommerce-pricing-conditions-<?php echo $name; ?>" class="section">
						<?php
						$condition_index = 0;
						if ( is_array( $pricing_conditions ) && sizeof( $pricing_conditions ) > 0 ):
							?>
							<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions_type]" value="all" />
							<?php
							foreach ( $pricing_conditions as $condition ) :
								$condition_index++;
								$this->create_condition( $condition, $name, $condition_index );
							endforeach;
						else :
							?>
							<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions_type]" value="all" />
							<?php
							$this->create_condition( array('type' => 'apply_to', 'args' => array('applies_to' => 'everyone', 'roles' => array('customer'))), $name, 1 );
						endif;
						?>
					</div>



					<div id="woocommerce-pricing-dates-<?php echo $name; ?>" class="section pricing-rule-date-fields">
						<label for="pricing_ruleset_dates_value_<?php echo $name . '_date_from'; ?>"><?php _e( 'Dates: (Inclusive)', 'woocommerce-dynamic-pricing' ); ?></label>
						<input value="<?php echo $date_from; ?>" type="text" class="short date_from" title="<?php _e( 'Leave both fields blank to not restrict this pricing group to a date range', 'woocommerce-dynamic-pricing' ); ?>" name="pricing_rules[<?php echo $name; ?>][date_from]" id="pricing_ruleset_dates_value_<?php echo $name . '_date_from'; ?>" value="" placeholder="<?php echo _x( 'From&hellip;', 'placeholder', 'woocommerce' ) ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
						<input value="<?php echo $date_to; ?>" type="text" class="short date_to" title="<?php _e( 'Leave both fields blank to not restrict this pricing group to a date range', 'woocommerce-dynamic-pricing' ); ?>" name="pricing_rules[<?php echo $name; ?>][date_to]" id="pricing_ruleset_dates_value_<?php echo $name . '_date_to'; ?>" value="" placeholder="<?php echo _x( 'To&hellip;', 'placeholder', 'woocommerce' ); ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
						<div class="clear"></div>
					</div>

					<script type="text/javascript">
						jQuery(document).ready(function ($) {
							// DATE PICKER FIELDS
							$(".pricing-rule-date-fields input:not(.hasDatepicker)").datepicker({
								defaultDate: "",
								dateFormat: "yy-mm-dd",
								numberOfMonths: 1,
								showButtonPanel: true,
								showOn: "button",
								buttonImage: woocommerce_pricing_admin.calendar_image,
								buttonImageOnly: true,
								onSelect: function (selectedDate) {
									var option = $(this).is('.date_from') ? "minDate" : "maxDate";

									var instance = $(this).data("datepicker"),
										date = $.datepicker.parseDate(
											instance.settings.dateFormat ||
											$.datepicker._defaults.dateFormat,
											selectedDate, instance.settings);

									var dates = $(this).parents('.pricing-rule-date-fields').find('input');
									dates.not(this).datepicker("option", option, date);
								}
							});
						})
					</script>

					<div class="section">
						<label><?php _e('Order Total Pricing', 'woocommerce-dynamic-pricing'); ?></label>
						<table id="woocommerce-pricing-rules-table-<?php echo $name; ?>" data-lastindex="<?php echo (is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0) ? count( $pricing_rules ) : '1'; ?>">
							<thead>
							<th>
								<?php _e( 'Minimum Order Total', 'woocommerce-dynamic-pricing' ); ?>
							</th>
							<th>
								<?php _e( 'Max Order Total', 'woocommerce-dynamic-pricing' ); ?>
							</th>
							<th>
								<?php _e( 'Type', 'woocommerce-dynamic-pricing' ); ?>
							</th>
							<th>
								<?php _e( 'Amount', 'woocommerce-dynamic-pricing' ); ?>
							</th>
							<th>&nbsp;</th>
							</thead>
							<tbody>
								<?php
								$index = 0;
								if ( is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0 ) {
									foreach ( $pricing_rules as $rule ) {
										$index++;
										$this->get_row( $rule, $name, $index );
									}
								} else {
									$this->get_row( array('to' => '', 'from' => '', 'amount' => '', 'type' => ''), $name, 1 );
								}
								?>
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
				</div><?php
			}
		}

		public function create_empty_ruleset( $set_index ) {
			$pricing_rule_sets = array();
			$pricing_rule_sets['set_' . $set_index] = array();
			$pricing_rule_sets['set_' . $set_index]['title'] = 'Rule Set ' . $set_index;
			$pricing_rule_sets['set_' . $set_index]['rules'] = array();
			$this->create_rulesets( $pricing_rule_sets );
		}

		private function create_condition( $condition, $name, $condition_index ) {
			global $wp_roles;
			switch ( $condition['type'] ) {
				case 'apply_to':
					$this->create_condition_apply_to( $condition, $name, $condition_index );
					break;
				default:
					break;
			}
		}

		private function create_condition_apply_to( $condition, $name, $condition_index ) {
			if ( !isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
			$all_roles = $wp_roles->roles;
			$div_style = ($condition['args']['applies_to'] != 'roles') ? 'display:none;' : '';
			?>

			<div>
				<label for="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>">Applies To:</label>
				<input type="hidden" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][type]" value="apply_to" />

				<select class="pricing_rule_apply_to" id="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][applies_to]">
					<option <?php selected( 'everyone', $condition['args']['applies_to'] ); ?> value="everyone"><?php _e('Everyone', 'woocommerce-dynamic-pricing'); ?></option>
					<option <?php selected( 'unauthenticated', $condition['args']['applies_to'] ); ?> value="unauthenticated"><?php _e( 'Guests', 'woocommerce-dynamic-pricing' ); ?></option>
					<option <?php selected( 'roles', $condition['args']['applies_to'] ); ?> value="roles"><?php _e('Specific Roles', 'woocommerce-dynamic-pricing'); ?></option>
					<?php do_action( 'woocommerce_dynamic_pricing_applies_to_options', 'advanced_totals', $condition, $name, $condition_index ); ?>

				</select>

				<div class="roles" style="<?php echo $div_style; ?>">

                <label for="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>_roles"><?php _e('Roles:', 'woocommerce-dynamic-pricing'); ?></label>


				 <select style="width: 80%;" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][roles][]" id="pricing_rule_apply_to_<?php echo $name . '_' . $condition_index; ?>_roles" class="multiselect wc-enhanced-select" multiple="multiple">
		            <?php foreach($all_roles as $role_id => $role): ?>
			           <?php $role_checked = (isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) && in_array( $role_id, $condition['args']['roles'] )) ? true : false; ?>

			            <option <?php selected($role_checked); ?> value="<?php esc_attr_e($role_id); ?>"><?php esc_html_e($role['name']); ?></option>
		            <?php endforeach; ?>
                </select>

				</div>

				<?php do_action( 'woocommerce_dynamic_pricing_applies_to_selectors', 'advanced_totals', $condition, $name, $condition_index ); ?>


				<div style="clear:both;"></div>
			</div>
			<?php
		}

		private function create_collector( $collector, $name ) {

			$terms = (array) get_terms( 'product_cat', array('get' => 'all') );
			?>
			<label for="pricing_rule_when_<?php echo $name; ?>"><?php _e( 'Quantities based on:', 'woocommerce-dynamic-pricing' ); ?></label>
			<select title="Choose how to calculate the quantity.  This tallied amount is used in determining the min and max quantities used below in the Quantity Pricing section." class="pricing_rule_when" id="pricing_rule_when_<?php echo $name; ?>" name="pricing_rules[<?php echo $name; ?>][collector][type]">
				<option title="Calculate total based on entire cart" <?php selected( 'cart_total', $collector['type'] ); ?> value="cart_total"><?php _e( 'Cart Total', 'woocommerce-dynamic-pricing' ); ?></option>
				<option title="Calculate total based on total sum of the categories in the cart" <?php selected( 'cat', $collector['type'] ); ?> value="cat"><?php _e( 'Category Total', 'woocommerce-dynamic-pricing' ); ?></option>
			</select>
			<div class="cats" style="<?php echo ($collector['type'] == 'cart_total' ? 'display:none;' : ''); ?>">
				<label style="margin-top:10px;"><?php _e( 'Required Categories', 'woocommerce-dynamic-pricing' ); ?>:</label>

                  <select style="width: 90%;" name="pricing_rules[<?php echo $name; ?>][collector][args][cats][]" class="multiselect wc-enhanced-select" multiple="multiple">
						<?php foreach($terms as $term): ?>
						    <?php $term_checked = (isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) && in_array( $term->term_id, $collector['args']['cats'] )) ? true : false; ?>

						    <option <?php selected($term_checked); ?> value="<?php esc_attr_e($term->term_id); ?>"><?php esc_html_e($term->name); ?></option>

						<?php endforeach; ?>
                </select>


			</div>

			<?php
		}

		private function create_target_selector( $targets, $name, $collector ) {
			$terms = (array) get_terms( 'product_cat', array('get' => 'all') );
			?>
			<br />
			<br />
			<div class="cats" style="<?php echo ($collector['type'] == 'cart_total' ? 'display:none;' : ''); ?>">

				<label> <?php _e( 'Categories to apply adjustment to:', 'woocommerce-dynamic-pricing' ); ?> </label>

                <select style="width: 90%;" name="pricing_rules[<?php echo $name; ?>][targets][]" class="multiselect wc-enhanced-select" multiple="multiple">
						<?php foreach($terms as $term): ?>
						   <?php $term_checked = (isset( $targets ) && is_array( $targets ) && in_array( $term->term_id, $targets )) ? true : false; ?>

						    <option <?php selected($term_checked); ?> value="<?php esc_attr_e($term->term_id); ?>"><?php esc_html_e($term->name); ?></option>

						<?php endforeach; ?>
                </select>


			</div>
			<?php
		}

		private function get_row( $rule, $name, $index ) {
			?>
			<tr id="pricing_rule_row_<?php echo $name . '_' . $index; ?>">
				<td>
					<input class="int_pricing_rule" id="pricing_rule_from_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index ?>][from]" value="<?php echo $rule['from']; ?>" />
				</td>
				<td>
					<input class="int_pricing_rule" id="pricing_rule_to_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index ?>][to]" value="<?php echo $rule['to']; ?>" />
				</td>
				<td>
					<select id="pricing_rule_type_value_<?php echo $name . '_' . $index; ?>" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][type]">
						<option <?php selected( 'percentage_discount', $rule['type'] ); ?> value="percentage_discount"><?php _e('Percentage Adjustment', 'woocommerce-dynamic-pricing'); ?></option>
					</select>
				</td>
				<td>
					<input class="float_rule_number" id="pricing_rule_amount_input_<?php echo $name . '_' . $index; ?>" type="text" name="pricing_rules[<?php echo $name; ?>][rules][<?php echo $index; ?>][amount]" value="<?php echo $rule['amount']; ?>" />
				</td>
				<td><a class="add_pricing_rule" data-index="<?php echo $index; ?>" data-name="<?php echo $name; ?>"><img
							src="<?php echo WC_Dynamic_Pricing::plugin_url() . '/assets/images/add.png'; ?>"
							title="add another rule" alt="add another rule"
							style="cursor:pointer; margin:0 3px;" /></a><a <?php echo ($index > 1) ? '' : 'style="display:none;"'; ?> class="delete_pricing_rule" data-index="<?php echo $index; ?>" data-name="<?php echo $name; ?>"><img
							src="<?php echo WC_Dynamic_Pricing::plugin_url() . '/assets/images/remove.png'; ?>"
							title="add another rule" alt="add another rule"
							style="cursor:pointer; margin:0 3px;" /></a>
				</td>
			</tr>
			<?php
		}

		private function meta_box_javascript() {
			?>
			<script type="text/javascript">

				jQuery(document).ready(function ($) {
		<?php do_action( 'woocommerce_dynamic_pricing_metabox_js', 'advanced_totals' ); ?>

					var set_index = 0;
					var rule_indexes = new Array();

					$('.woocommerce_pricing_ruleset').each(function () {
						var length = $('table tbody tr', $(this)).length;
						if (length == 1) {
							$('.delete_pricing_rule', $(this)).hide();
						}
					});


					$("#woocommerce-pricing-add-ruleset").click(function (event) {
						event.preventDefault();

						var set_index = $("#woocommerce-pricing-rules-wrap").data('setindex') + 1;
						$("#woocommerce-pricing-rules-wrap").data('setindex', set_index);

						var data = {
							set_index: set_index,
							post:<?php echo intval($_GET['post'] ?? 0); ?>,
							action: 'create_empty_totals_ruleset'
						}

						$.post(ajaxurl, data, function (response) {
							$('#woocommerce-pricing-rules-wrap').append(response);
							 $(document.body).trigger('wc-enhanced-select-init');
						});
					});

					$('#woocommerce-pricing-rules-wrap').delegate('.pricing_rule_apply_to', 'change', function (event) {
						var value = $(this).val();
						if (value != 'roles') {
							$('.roles', $(this).parent()).fadeOut();
							$('.roles input[type=checkbox]', $(this).closest('div')).removeAttr('checked');
						} else {
							$('.roles', $(this).parent()).fadeIn();
						}
					});

					$('#woocommerce-pricing-rules-wrap').delegate('.pricing_rule_when', 'change', function (event) {
						var value = $(this).val();
						if (value != 'cat') {
							$('.cats', $(this).closest('div')).fadeOut();
							$('.cats input[type=checkbox]', $(this).closest('div')).removeAttr('checked');
						} else {
							$('.cats', $(this).closest('div')).fadeIn();
						}
					});

					//Remove Pricing Set
					$('#woocommerce-pricing-rules-wrap').delegate('.delete_pricing_ruleset', 'click', function (event) {
						event.preventDefault();
						DeleteRuleSet($(this).data('name'));
					});

					//Add Button
					$('#woocommerce-pricing-rules-wrap').delegate('.add_pricing_rule', 'click', function (event) {
						event.preventDefault();
						InsertRule($(this).data('index'), $(this).data('name'));
					});



					//Remove Button
					$('#woocommerce-pricing-rules-wrap').delegate('.delete_pricing_rule', 'click', function (event) {
						event.preventDefault();
						DeleteRule($(this).data('index'), $(this).data('name'));
					});



					$('#woocommerce-pricing-rules-wrap').delegate('.float_pricing_rule', 'keydown', function (event) {
						// Allow only backspace, delete and tab
						if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 190) {
							// let it happen, don't do anything
						}
						else {
							// Ensure that it is a number and stop the keypress
							if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
								event.preventDefault();
							}
						}
					});

					$("#woocommerce-pricing-rules-wrap").sortable(
						{
							handle: 'h4.first',
							containment: 'parent',
							axis: 'y'
						});

					function InsertRule(previousRowIndex, name) {


						var $index = $("#woocommerce-pricing-rules-table-" + name).data('lastindex') + 1;
						$("#woocommerce-pricing-rules-table-" + name).data('lastindex', $index);

						var html = '';
						html += '<tr id="pricing_rule_row_' + name + '_' + $index + '">';
						html += '<td>';
						html += '<input class="int_pricing_rule" id="pricing_rule_from_input_' + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][from]" value="" /> ';
						html += '</td>';
						html += '<td>';
						html += '<input class="int_pricing_rule" id="pricing_rule_to_input_' + name + '_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][to]" value="" /> ';
						html += '</td>';
						html += '<td>';
						html += '<select id="pricing_rule_type_value_' + name + '_' + $index + '" name="pricing_rules[' + name + '][rules][' + $index + '][type]">';
						html += '<option value="percentage_discount"><?php _e('Percentage Adjustment', 'woocommerce-dynamic-pricing'); ?></option>';
						html += '</select>';
						html += '</td>';
						html += '<td>';
						html += '<input class="float_pricing_rule" id="pricing_rule_amount_input_' + $index + '" type="text" name="pricing_rules[' + name + '][rules][' + $index + '][amount]" value="" /> ';
						html += '</td>';
						html += '<td>';
						html += '<a data-index="' + $index + '" data-name="' + name + '" class="add_pricing_rule"><img  src="<?php echo WC_Dynamic_Pricing::plugin_url() . '/assets/images/add.png'; ?>" title="add another rule" alt="add another rule" style="cursor:pointer; margin:0 3px;" /></a>';
						html += '<a data-index="' + $index + '" data-name="' + name + '" class="delete_pricing_rule"><img data-index="' + $index + '" src="<?php echo WC_Dynamic_Pricing::plugin_url() . '/assets/images/remove.png'; ?>" title="remove rule" alt="remove rule" style="cursor:pointer; margin:0 3px;" /></a>';
						html += '</td>';
						html += '</tr>';

						$('#pricing_rule_row_' + name + '_' + previousRowIndex).after(html);
						$('.delete_pricing_rule', "#woocommerce-pricing-rules-table-" + name).show();

					}

					function DeleteRule(index, name) {
						if (confirm("<?php _e('Are you sure you would like to remove this price adjustment?', 'woocommerce-dynamic-pricing'); ?>")) {
							$('#pricing_rule_row_' + name + '_' + index).remove();

							var $index = $('tbody tr', "#woocommerce-pricing-rules-table-" + name).length;
							if ($index > 1) {
								$('.delete_pricing_rule', "#woocommerce-pricing-rules-table-" + name).show();
							} else {
								$('.delete_pricing_rule', "#woocommerce-pricing-rules-table-" + name).hide();
							}
						}
					}

					function DeleteRuleSet(name) {
						if (confirm("<?php _e('Are you sure you would like to remove this price set?', 'woocommerce-dynamic-pricing'); ?>")) {
							$('#woocommerce-pricing-ruleset-' + name).slideUp().remove();
						}
					}

				});

			</script>
			<?php
		}

		public function meta_box_css() {
			?>
			<style>
				#woocommerce-pricing-totals div.section {
					margin-bottom: 10px;
				}

				#woocommerce-pricing-totals label {
					display:block;
					font-weight: bold;
					margin-bottom:5px;
				}

				#woocommerce-pricing-totals .list-column {
					float:left;
					margin-right:25px;
					margin-top:0px;
					margin-bottom: 0px;
				}

				#woocommerce-pricing-totals .list-column label {
					margin-bottom:0px;
				}

				#woocommerce-pricing-rules-wrap {
					margin:10px;
				}

				#woocommerce-pricing-rules-wrap h4 {
					border-bottom: 1px solid #E5E5E5;
					padding-bottom: 6px;
					font-size: 1.2em;
					margin: 1em 0 1em;
					text-transform: uppercase;
				}

				#woocommerce-pricing-rules-wrap h4.first {
					margin-top:0px;
					cursor:move;
				}

				.woocommerce_pricing_ruleset {

					border-color:#dfdfdf;
					border-width:1px;
					border-style:solid;
					-moz-border-radius:3px;
					-khtml-border-radius:3px;
					-webkit-border-radius:3px;
					border-radius:3px;
					padding: 10px;
					border-style:solid;
					border-spacing:0;
					background-color:#F9F9F9;
					margin-bottom: 25px;
				}

				.woocommerce_pricing_ruleset.invalid {
					border-color:#EACBCC;
					background-color:#FFDFDF;
				}

			</style>
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
