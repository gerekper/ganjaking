<?php
/**
 * Template file for the email variable "{membership_plan_discounts}".
 *
 * To edit this template, copy this file over to your wp-content/[current_theme]/follow-up-emails/email-variables
 * then edit the new file. A single variable named $lists is passed along to this template.
 *
 * $rules = WC_Memberships_Membership_Plan_Rule[]
 */
?>
<ul>
	<?php
	foreach ( $rules as $rule ) {
		echo '<li>'. esc_html( FUE_Addon_WC_Memberships::discount_rule_string( $rule ) ) .'</li>';
	}
	?>
</ul>