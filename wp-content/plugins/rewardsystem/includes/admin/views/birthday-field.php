<?php
/* Admin HTML Birthday Coupon Settings */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="form-table">
	<tr>
		<th>
			<label><?php esc_html_e( 'Birthday' , 'rewardsystem' ) ; ?></label>
		</th>
		<td>
			<input type="text" name="srp_birthday_date" value="<?php echo esc_attr( $birthday_date ) ; ?>" readonly="readonly"/>
		</td>
	</tr>
</table>
<?php
