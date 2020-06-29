<td class="rule-type"><?php
    // allow custom location rules
    $types = apply_filters('wc_conditional_content_get_rule_types', array());

    // create field
    $args = array(
        'input' => 'select',
        'name' => 'wccc_rule[<%= groupId %>][<%= ruleId %>][rule_type]',
        'class' => 'rule_type',
        'choices' => $types,
    );

    WC_Conditional_Content_Input_Builder::create_input_field($args, 'product_select');
    ?>
</td>

<?php
WC_Conditional_Content_Admin_Controller::instance()->render_rule_choice_template(array(
    'group_id' => 0,
    'rule_id' => 0,
    'rule_type' => 'product_select',
    'condition' => false,
    'operator' => false
));
?>
<td class="loading" colspan="2" style="display:none;"><?php _e('Loading...', 'wc_conditional_content'); ?></td>
<td class="add"><a href="#" class="wccc-add-rule button"><?php _e("and", 'wc_conditional_content'); ?></a></td>
<td class="remove"><a href="#" class="wccc-remove-rule wccc-button-remove" <?php _e('Remove condition', 'wc_conditional_content'); ?>><?php _e('remove', 'wc_conditional_content'); ?></a></td>
