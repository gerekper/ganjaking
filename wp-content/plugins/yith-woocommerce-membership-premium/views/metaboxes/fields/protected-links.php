<?php
/**
 * @var array $field The field.
 */
! defined( 'YITH_WCMBS' ) && exit();
global $post;

$links = isset( $field['value'] ) && is_array( $field['value'] ) ? $field['value'] : array();
$name  = isset( $field['name'] ) ? $field['name'] : '';
if ( ! $links ) {
	$links = array(
		array(
			'name'       => '',
			'link'       => '',
			'membership' => array(),
		),
	);
}
$plan_ids = yith_wcmbs_get_plans( array( 'fields' => 'ids' ) );
$plans    = array_combine( $plan_ids, array_map( 'get_the_title', $plan_ids ) );
?>
<div class="yith-wcmbs-admin-protected-links__container">
	<div class="yith-wcmbs-admin-protected-links">
		<?php
		foreach ( $links as $index => $link ) {
			yith_wcmbs_get_view( 'metaboxes/fields/parts/protected-link.php', array(
				'index' => $index,
				'link'  => $link,
				'plans' => $plans,
				'name'  => $name,
			) );
		}
		?>
	</div>
	<div class="yith-wcmbs-admin-protected-links__actions">
		<span id="yith-wcmbs-admin-protected-links__add-link"><?php esc_html_e( 'Add file', 'yith-woocommerce-membership' ); ?></span>
	</div>

	<script type="text/html" id="tmpl-yith-wcmbs-admin-protected-links">
		<?php
		yith_wcmbs_get_view( 'metaboxes/fields/parts/protected-link.php', array(
			'index' => '{{data.index}}',
			'plans' => $plans,
			'name'  => $name,
		) );
		?>
	</script>
</div>