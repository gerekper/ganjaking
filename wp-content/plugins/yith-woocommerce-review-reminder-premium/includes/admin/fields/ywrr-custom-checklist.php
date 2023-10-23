<?php
/**
 * Custom Checklist template
 *
 * @package YITH\ReviewReminder
 * @var array $field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

extract( $field ); //phpcs:ignore WordPress.PHP.DontExtract.extract_extract

?>

	<div class="ywcc-checklist-div" style="vertical-align: top; margin-bottom: 3px;" id="<?php echo esc_attr( $id ); ?>">
		<input type="hidden" id="<?php echo esc_attr( $id ); ?>" class="ywcc-values" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<span class="ywcc-value-list select2 select2-container select2-container--default">
		<span class="selection">
			<span class="select2-selection select2-selection--multiple">
				<ul class="select2-selection__rendered">
				</ul>
			</span>
		</span>
		<div class="ywcc-checklist-ajax">
				<input type="text" id="ywcc-new-element-<?php echo esc_attr( $id ); ?>" class="ywcc-insert select2-input form-input-tip" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" />
		</div>
	</span>
	</div>

<?php
if ( isset( $field['desc-inline'] ) ) {
	echo '<span class="description inline">' . esc_attr( $field['desc-inline'] ) . '</span>';
}
