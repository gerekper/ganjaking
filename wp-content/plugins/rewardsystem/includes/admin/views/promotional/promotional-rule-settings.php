<?php
/* Admin HTML Promotional Points Settings */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<div class="srp-rules-content-wrapper">
	<h3 class="srp-rule-name">
		<?php
		$expired_label = ( 'srp-inactive-rule' == $inactive ) ? ' - Rule Expired( Duration reached )' : '' ;
		echo ! empty( $rule->get_name() ) ? esc_html( $rule->get_name() . $expired_label ) : esc_html__( 'Untitled' . $expired_label , 'rewardsystem' ) ;
		?>
		<span class="dashicons dashicons-arrow-down"></span>
		<span class="dashicons dashicons-trash srp-delete-rule" data-ruleid="<?php echo esc_attr( $rule_id ) ; ?>"></span>
	</h3>
	<div class="srp-rule-fields <?php echo esc_attr( $inactive ) ; ?>">
		<p>
			<label><?php esc_html_e( 'Enable this Rule' , 'rewardsystem' ) ; ?></label>
			<input type="checkbox" name="srp_promotional_rules[<?php echo esc_attr( $rule_id ) ; ?>][srp_enable]" <?php echo checked( $rule->get_enable() , 'yes' , true ) ; ?>/>
		</p>
		<p>
			<label><?php esc_html_e( 'Promotional Title' , 'rewardsystem' ) ; ?></label>
			<input type="text" name="srp_promotional_rules[<?php echo esc_attr( $rule_id ) ; ?>][srp_name]" value="<?php echo esc_attr( $rule->get_name() ) ; ?>"/>
		</p>
		<p class="srp-date-range">
			<label><?php esc_html_e( 'Date Range' , 'rewardsystem' ) ; ?></label>
			<span><?php esc_html_e( 'From' , 'rewardsystem' ) ; ?></span>
			<?php
			$args      = array(
				'name'    => 'srp_promotional_rules[' . esc_attr( $rule_id ) . '][srp_from_date]',
				'value'   => $rule->get_from_date(),
				'wp_zone' => false,
					) ;
			srp_get_datepicker_html( $args ) ;
			?>
			<span><?php esc_html_e( 'To' , 'rewardsystem' ) ; ?></span>
			<?php
			$args      = array(
				'name'    => 'srp_promotional_rules[' . esc_attr( $rule_id ) . '][srp_to_date]',
				'value'   => $rule->get_to_date(),
				'wp_zone' => false,
					) ;
			srp_get_datepicker_html( $args ) ;
			?>
		</p>
		<p>
			<label><?php esc_html_e( 'Enter the Multiplicator Value' , 'rewardsystem' ) ; ?></label>
			<input type="number" min="0" name="srp_promotional_rules[<?php echo esc_attr( $rule_id ) ; ?>][srp_point]" value="<?php echo esc_attr( $rule->get_point() ) ; ?>"/>
		</p>
	</div>
</div>
<?php
