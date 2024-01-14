<?php
/* Admin HTML Promotional Points Settings */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<div class="srp-promotional-settings">
	<div class="srp-rules-wrapper">
		<div class="srp-rules-content">
			<?php
			if ( srp_check_is_array( $rule_ids ) ) {
				foreach ( $rule_ids as $rule_id ) {
					$rule     = srp_get_rule( $rule_id ) ;
					$inactive = ( time() > strtotime( $rule->get_to_date() . ' 11:59:59' ) ) ? 'srp-inactive-rule' : 'srp-active-rule' ;
					include 'promotional-rule-settings.php'  ;
				}
			}
			?>
		</div>
		<div class="srp-add-rule-button-wrapper">
			<button class="srp-add-rule"><?php esc_html_e( 'Add Rule' , 'rewardsystem' ) ; ?></button>
		</div>
	</div>
</div>
<?php
