<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @var string $element_form_fields_template_path
 * @var string $ai_element_type
 * @var string $ai_element_id
 * @var Vc_Ai_Modal_Controller $ai_modal_controller
 */
?>

<form method="post" action="" class="vc_ui-panel-content-container vc_ui-hidden">
	<div class="vc_ui-panel-content vc_properties-list" data-vc-ui-element="panel-content">
		<div class="vc_row">
			<?php
			vc_include_template(
				$element_form_fields_template_path,
				[
					'ai_element_type' => $ai_element_type,
					'ai_element_id' => $ai_element_id,
					'ai_modal_controller' => $ai_modal_controller,
				]
			);
			?>
		</div>
	</div>
</form>
