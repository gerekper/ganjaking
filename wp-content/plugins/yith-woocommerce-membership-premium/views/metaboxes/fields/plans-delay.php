<?php
/**
 * @var array $field The field.
 */
! defined( 'YITH_WCMBS' ) && exit();
global $post;

$value = isset( $field['value'] ) && is_array( $field['value'] ) ? $field['value'] : array();
$name  = $field['name'];
?>
<div class="yith-wcmbs-admin-plans-delay">
	<?php foreach ( $value as $plan_id => $delay ) : ?>

		<?php
		yith_wcmbs_get_view( 'metaboxes/fields/parts/single-plan-delay.php', array(
			'plan_id'   => $plan_id,
			'plan_name' => get_the_title( $plan_id ),
			'delay'     => $delay,
			'name'      => $name,
		) );
		?>
	<?php endforeach; ?>

	<script type="text/html" id="tmpl-yith-wcmbs-admin-single-plan-delay">
		<?php
		yith_wcmbs_get_view( 'metaboxes/fields/parts/single-plan-delay.php', array(
			'plan_id'   => '{{data.planID}}',
			'plan_name' => '{{data.planName}}',
			'delay'     => 0,
			'name'      => $name,
		) );
		?>
	</script>
</div>
