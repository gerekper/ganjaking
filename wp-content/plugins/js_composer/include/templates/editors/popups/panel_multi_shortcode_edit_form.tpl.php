<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div id="vc_multi-properties-panel" class="vc_panel">
	<div class="vc_panel-heading">
		<a title="<?php esc_attr_e( 'Close panel', 'js_composer' ); ?>" href="#" class="vc_close" data-dismiss="panel" aria-hidden="true"><i class="icon"></i></a>
		<a title="<?php esc_attr_e( 'Hide panel', 'js_composer' ); ?>" href="#" class="vc_transparent" data-transparent="panel" aria-hidden="true"><i class="icon"></i></a>
		<h3 class="vc_panel-title"><?php esc_html_e( 'Edit Elements', 'js_composer' ); ?></h3>
	</div>
	<div class="vc_panel-body vc_properties-list">
	</div>
	<div class="vc_panel-footer">
		<button type="button" class="vc_btn vc_panel-btn-close" data-dismiss="panel"><?php esc_html_e( 'Close', 'js_composer' ); ?></button>
		<button type="button" class="vc_btn vc_panel-btn-save vc_save" data-save="true"><?php esc_html_e( 'Save Changes', 'js_composer' ); ?></button>
	</div>
</div>
