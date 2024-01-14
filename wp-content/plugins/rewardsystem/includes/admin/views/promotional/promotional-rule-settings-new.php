<?php
/* Admin HTML Promotional Points Settings */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<div class="srp-rules-content-wrapper">
	<h3 class="srp-rule-name">
		<?php echo esc_html__( 'Untitled' , 'rewardsystem' ) ; ?>
		<span class="dashicons dashicons-arrow-down"></span>
		<span class="dashicons dashicons-trash srp-delete-rule" data-ruleid=""></span>
	</h3>
	<input type="hidden" id="srp_promotional_key" value="<?php echo esc_attr( $key ) ; ?>"/>
	<div class="srp-rule-fields">
		<p>
			<label><?php esc_html_e( 'Enable this Rule' , 'rewardsystem' ) ; ?></label>
			<input type="checkbox" name="srp_promotional_rules[new][<?php echo esc_attr( $key ) ; ?>][srp_enable]"/>
		</p>
		<p>
			<label><?php esc_html_e( 'Promotion Title' , 'rewardsystem' ) ; ?></label>
			<input type="text" name="srp_promotional_rules[new][<?php echo esc_attr( $key ) ; ?>][srp_name]"/>
		</p>
		<p class="srp-date-range">
			<label><?php esc_html_e( 'Date Range' , 'rewardsystem' ) ; ?></label>
			<span><?php esc_html_e( 'From' , 'rewardsystem' ) ; ?></span>
			<?php
			$args = array(
				'name'    => 'srp_promotional_rules[new][' . esc_attr( $key ) . '][srp_from_date]',
				'wp_zone' => false,
					) ;
			srp_get_datepicker_html( $args ) ;
			?>
			<span><?php esc_html_e( 'To' , 'rewardsystem' ) ; ?></span>
			<?php
			$args = array(
				'name'    => 'srp_promotional_rules[new][' . esc_attr( $key ) . '][srp_to_date]',
				'wp_zone' => false,
					) ;
			srp_get_datepicker_html( $args ) ;
			?>
		</p>
		<p>
			<label><?php esc_html_e( 'Enter the Multiplicator Value' , 'rewardsystem' ) ; ?></label>
			<input type="number" min="0" name="srp_promotional_rules[new][<?php echo esc_attr( $key ) ; ?>][srp_point]"/>
		</p>
	</div>
</div>
<?php
