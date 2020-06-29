<?php

function wc_dynamic_pricing_groups_get_all_groups() {
	global $wpdb;
	$group_table = _groups_get_tablename( 'group' );
	$results = $wpdb->get_results( "SELECT * FROM $group_table ORDER BY name", ARRAY_A );

	return $results;
}

add_action( 'woocommerce_dynamic_pricing_applies_to_options', 'wc_dynamic_pricing_groups_applies_to_option', 10, 4 );

function wc_dynamic_pricing_groups_applies_to_option( $module_name, $condition, $name, $condition_index ) {
	?>
	<option <?php selected( 'groups', $condition['args']['applies_to'] ); ?> value="groups"><?php _e( 'Groups', 'wc_dynamic_pricing' ); ?></option>
	<?php
}

add_action( 'woocommerce_dynamic_pricing_applies_to_selectors', 'wc_dynamic_pricing_groups_applies_to_selector', 10, 4 );

function wc_dynamic_pricing_groups_applies_to_selector( $module_name, $condition, $name, $condition_index ) {

	$div_style = ($condition['args']['applies_to'] != 'groups') ? 'display:none;' : '';

	$all_groups = wc_dynamic_pricing_groups_get_all_groups();
	?>

	<div class="groups" style="<?php echo $div_style; ?>">
		<?php $chunks = array_chunk( $all_groups, ceil( count( $all_groups ) / 3 ), true ); ?>
		<?php foreach ( $chunks as $chunk ) : ?>
			<ul class="list-column">        
				<?php foreach ( $chunk as $group ) : ?>
					<?php $group_id = $group['group_id']; ?>
					<?php $group_checked = (isset( $condition['args']['groups'] ) && is_array( $condition['args']['groups'] ) && in_array( $group_id, $condition['args']['groups'] )) ? 'checked="checked"' : ''; ?>
					<li>
						<label for="<?php echo $name; ?>_group_<?php echo $group_id; ?>" class="selectit">
							<input <?php echo $group_checked; ?> type="checkbox" id="<?php echo $name; ?>_group_<?php echo $group_id; ?>" name="pricing_rules[<?php echo $name; ?>][conditions][<?php echo $condition_index; ?>][args][groups][]" value="<?php echo $group_id; ?>" /><?php echo $group['name']; ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</div>

	<?php
}

add_action( 'woocommerce_dynamic_pricing_metabox_js', 'woocommerce_dynamic_groups_pricing_metabox_js' );

function woocommerce_dynamic_groups_pricing_metabox_js( $module_name ) {
	?>
	$('#woocommerce-pricing-rules-wrap').delegate('.pricing_rule_apply_to', 'change', function(event) {  
	var value = $(this).val();
	if (value != 'groups' && $('.groups', $(this).parent()).is(':visible')) {
	$('.groups', $(this).parent() ).fadeOut();
	$('.groups input[type=checkbox]', $(this).closest('div')).removeAttr('checked');
	}

	if (value == 'groups') {
	$('.groups', $(this).parent() ).fadeIn();
	}

	});
	<?php
}

add_filter( 'woocommerce_dynamic_pricing_is_rule_set_valid_for_user', 'woocommerce_dynamic_pricing_groups_is_rule_set_valid_for_user', 10, 3 );

function woocommerce_dynamic_pricing_groups_is_rule_set_valid_for_user( $result, $condition, $rule_set ) {
	$groups_user = new Groups_User( get_current_user_id() );
	switch ( $condition['type'] ) {
		case 'apply_to':
			if ( is_array( $condition['args'] ) && isset( $condition['args']['applies_to'] ) ) {
				if ( $condition['args']['applies_to'] == 'groups' && isset( $condition['args']['groups'] ) && is_array( $condition['args']['groups'] ) ) {
					if ( is_user_logged_in() ) {
						foreach ( $condition['args']['groups'] as $group ) {
							$current_group = Groups_Group::read( $group );
							if ( $current_group ) {
								if ( Groups_User_Group::read( $groups_user->user->ID, $current_group->group_id ) ) {
									$result = 1;
									break;
								}
							}
						}
					}
				}
			}
			break;
		default:
			break;
	}

	return $result;
}
