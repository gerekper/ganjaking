<?php WC_Conditional_Content::nonce_field('admin'); ?>

<?php
global $post;

// vars
$groups = get_post_meta($post->ID, 'wccc_rule', true);


// at lease 1 location rule
if (empty($groups)) {
	$default_rule_id = 'rule' . uniqid();
	$groups = array(
	    'group0' => array(
		$default_rule_id => array(
		    'rule_type' => 'general_always',
		    'operator' => '==',
		    'condition' => '',
		)
	    )
	);
}
?>

<div class="wccc-rules-builder woocommerce_options_panel">

	<div class="label">
		<h4><?php _e("Rules", 'wc_conditional_content'); ?></h4>
		<p class="description"><?php _e("Create a set of rules to determine when the content defined above will be displayed.", 'wc_conditional_content'); ?></p>
	</div>

	<div id="wccc-rules-groups">
		<div class="wccc-rule-group-target">
			<?php if (is_array($groups)): ?>
				<?php
				$group_counter = 0;
				foreach ($groups as $group_id => $group):
					if (empty($group_id)) {
						$group_id = 'group' . $group_id;
					}
					?>

					<div class="wccc-rule-group-container" data-groupid="<?php echo $group_id; ?>">
						<div class="wccc-rule-group-header">
							<?php if ($group_counter == 0): ?>
								<h4><?php _e('Apply this content when these conditions are matched:', 'wc_conditional_content'); ?></h4>
							<?php else: ?>
								<h4><?php _e("or", 'wc_conditional_content'); ?></h4>
							<?php endif; ?>
							<a href="#" class="wccc-remove-rule-group button"><?php _e('Remove', 'wc_conditional_content'); ?></a>
						</div>
						<?php if (is_array($group)): ?>
							<table class="wccc-rules" data-groupid="<?php echo $group_id; ?>">
								<tbody>
									<?php
									foreach ($group as $rule_id => $rule) :
										if (empty($rule_id)) {
											$rule_id = 'rule' . $rule_id;
										}
										?>
										<tr data-ruleid="<?php echo $rule_id; ?>" class="wccc-rule">
											<td class="rule-type"><?php
												// allow custom location rules
												$types = apply_filters('wc_conditional_content_get_rule_types', array());

												// create field
												$args = array(
												    'input' => 'select',
												    'name' => 'wccc_rule[' . $group_id . '][' . $rule_id . '][rule_type]',
												    'class' => 'rule_type',
												    'choices' => $types,
												);

												WC_Conditional_Content_Input_Builder::create_input_field($args, $rule['rule_type']);
												?>
											</td>

											<?php
											WC_Conditional_Content_Admin_Controller::instance()->ajax_render_rule_choice(array(
											    'group_id' => $group_id,
											    'rule_id' => $rule_id,
											    'rule_type' => $rule['rule_type'],
											    'condition' => isset($rule['condition']) ? $rule['condition'] : false,
											    'operator' => $rule['operator']
											));
											?>
											<td class="loading" colspan="2" style="display:none;"><?php _e('Loading...', 'wc_conditional_content'); ?></td>
											<td class="add">
												<a href="#" class="wccc-add-rule button"><?php _e("and", 'wc_conditional_content'); ?></a>
											</td>
											<td class="remove">
												<a href="#" class="wccc-remove-rule wccc-button-remove" title="<?php _e('Remove condition', 'wc_conditional_content'); ?>"><?php _e('remove', 'wc_conditional_content'); ?></a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
					<?php $group_counter++; ?>
				<?php endforeach; ?>
			</div>
			
			<h4 class="or" style="<?php echo ($group_counter > 1 ? 'display:block;' : 'display:none'); ?>" ><?php _e('or when these conditions are matched', 'wc_conditional_content'); ?></h4>
			<button class="button button-primary wccc-add-rule-group" title="<?php _e('Add a set of conditions', 'wc_conditional_content'); ?>"><?php _e("Add Rule Set", 'wc_conditional_content'); ?></button>
		<?php endif; ?>
	</div>
</div>

<script type="text/template" id="wccc-rule-template">
	<?php include 'metabox-rules-rule-template.php'; ?>
</script>