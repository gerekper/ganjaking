<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<strong><?= _x( 'Style', 'table view display', 'codepress-admin-columns' ) ?></strong>
<p><?= sprintf( __( 'Select the style for the %s selector on the table screen.', 'codepress-admin-columns' ), sprintf( '"%s"', __( "Table View", 'codepress-admin-columns' ) ) ) ?></p>
<div class="ac-general-setting-row__layout">
	<input name="layout_style" data-component="acui-toggle-buttons" data-options="<?= esc_attr( $this->options ) ?>" value="<?= esc_attr( $this->value ) ?>">
</div>
