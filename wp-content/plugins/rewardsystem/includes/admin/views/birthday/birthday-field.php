<?php
/* Admin HTML Birthday Coupon Settings */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<table class="form-table">
	<tr>
		<th>
			<label><?php esc_html_e( 'Birthday', 'rewardsystem' ); ?></label>
		</th>
		<td>
			<?php $readonly = $show_add_date_button ? true: false; ?>
			<input type="date" name="srp_birthday_date" value="<?php echo esc_attr( $birthday_date ); ?>" 
																		  <?php 
																			if ($readonly) :
																				?>
  readonly="readonly" <?php endif; ?>/>
			<?php
			if ( $show_add_date_button ) :
				?>
				<a href="#" class="rs-add-birthday-date-action button button-primary"><?php esc_html_e( 'Add/Update Date', 'rewardsystem' ); ?></a>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php
