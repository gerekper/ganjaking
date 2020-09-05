<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var \WP_Post $post
 */
$post       = $this->arguments[0];
$controller = Admin_Position_Controller::instance();

$ruleset = Rules_Model::get_ruleset( $post->ID );
?>

<p class="description"><?php _e( 'Create a set of rules to determine which pages will use these schema.org syntax.', 'rich-snippets-schema' ); ?></p>

<table class="widefat striped wpb-rs-ruleset <?php echo $ruleset->has_rulegroups() ? 'has-rules' : ''; ?>">
    <thead>
	<?php
	$controller->print_rule_row();
	$controller->print_group_break();
	?>
    </thead>
    <tbody>
	<?php

	foreach ( $ruleset->get_rulegroups() as $rule_group ) {
		$i = ( $i ?? 0 ) + 1;

		/**
		 * @var Position_Rule $rule
		 */
		foreach ( $rule_group->get_rules() as $rule ) {
			$controller->print_rule_row( $rule );
		}

		if ( $i != $ruleset->count() ) {
			$controller->print_group_break();
		}
	}

	?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5">
			<?php echo esc_html_x( 'or', 'logical OR when defining a ruleset', 'rich-snippets-schema' ); ?><br/>
            <a href="#" class="button wpb-rs-rulegroup-add"><?php _e( 'Add rulegroup', 'rich-snippets-schema' ) ?></a>
        </td>
    </tr>
    </tfoot>
</table>
