<style>
	#log {
		width: 98%;
		padding: 10px;
		background: #cecece;
		color: #000;
		line-height: 18px;
	}
	#log table td, #log table th {
		padding: 5px
	}
</style>
<h3><?php esc_html_e('System Information', 'follow_up_emails'); ?></h3>

<div id="log"><?php print_r(fue_get_system_data('html')); ?></div>