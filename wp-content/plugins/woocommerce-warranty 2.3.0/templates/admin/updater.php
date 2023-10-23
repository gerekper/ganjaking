<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
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
		<?php esc_html_e( 'Data Update', 'wc_warranty' ); ?>
	</h2>

	<p id="total-items-label"><?php esc_html_e( 'Loading', 'wc_warranty' ); ?>...</p>
	<div id="progressbar"><div class="progress-label"><?php esc_html_e( 'Loading', 'wc_warranty' ); ?>...</div></div>

	<div id="log"></div>
</div>
