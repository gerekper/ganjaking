<?php
/*
 * Template for Metabox Restrict Access
 */

$plans = YITH_WCMBS_Manager()->get_plans();
?>

<?php if ( ! empty( $plans ) ) : ?>
	<input name="_yith_wcmbs_restrict_access_edit_post" type="hidden" value="1">
	<div class="">
		<label for="yith_wcmbs_restrict_access_plan"><?php esc_html_e( 'Include this item in a membership', 'yith-woocommerce-membership' ); ?>:</label>
		<?php $loop = 0; ?>
		<?php foreach ( $plans as $plan ) : ?>
			<p>
				<input id="yith-wcmbs-rap-<?php echo esc_attr( $loop ); ?>" type="checkbox" name="_yith_wcmbs_restrict_access_plan[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $plan->get_id() ); ?>"
					<?php checked( true, in_array( $plan->get_id(), (array) $restrict_access_plan ), true ) ?> >
				<label for="yith-wcmbs-rap-<?php echo esc_attr( $loop ); ?>"><?php echo esc_html( $plan->get_name() ); ?></label>
			</p>
			<?php $loop ++; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>