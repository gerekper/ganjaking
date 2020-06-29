<?php
$default_args = array(
	'page_title'    => 'Data Update',
	'return_url'    => '',
	'ajax_endpoint' => '',
	'entity_label_singular' => 'item',
	'entity_label_plural'   => 'items',
	'action_label'          => 'updated'
);
if ( isset( $args ) ) {
	$args = wp_parse_args( $args, $default_args );
} else {
	$args = $default_args;
}
?>
<style>
	.wrap > div.updated {
		display: none;
	}
	.ui-progressbar {
		position: relative;
	}
	.ui-progressbar-value {
		border: 1px solid #fff;
		background: #ededed;
	}
	.progress-label {
		position: absolute;
		left: 10px;
		top: 4px;
		font-weight: bold;
		text-shadow: 1px 1px 0 #fff;
		color: #a9a9a9;
	}
	#log {
		max-height: 300px;
		overflow: auto;
	}
	#log p.success {
		color: green;
	}
	#log p.failure {
		color: #ff0000;
	}
</style>
<div class="wrap">
	<h2>
		<?php echo esc_html( $args['page_title'] ); ?>
	</h2>

	<p id="total-items-label"><?php esc_html_e('Loading', 'follow_up_emails'); ?>...</p>
	<div id="progressbar"><div class="progress-label"><?php esc_html_e('Loading', 'follow_up_emails'); ?>...</div></div>

	<div id="log"></div>
</div>
<script>
	var return_url              = '<?php echo esc_js( $args['return_url'] ); ?>';
	var ajax_endpoint           = '<?php echo esc_js( $args['ajax_endpoint'] ); ?>';
	var ajax_params             = {};
	var entity_label_singular   = '<?php echo esc_js( addslashes( $args['entity_label_singular'] ) ); ?>';
	var entity_label_plural     = '<?php echo esc_js( addslashes( $args['entity_label_plural'] ) ); ?>';
	var action_label            = '<?php echo esc_js( addslashes( $args['action_label'] ) ); ?>';
	<?php
	if ( !empty( $_GET['params'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$params = array_map( 'sanitize_text_field', wp_unslash( $_GET['params'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		foreach ( $params as $key => $value ) {
			echo "ajax_params." . esc_js( $key ) . " = '" . esc_js( $value ) . "'\n";
		}
	}
	?>
</script>