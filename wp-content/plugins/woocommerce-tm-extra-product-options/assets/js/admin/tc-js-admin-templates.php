<?php
/**
 * The admin javascript-based template for displayed javascript generated html code
 *
 * NOTE that this file is not meant to be overriden
 *
 * @see           https://codex.wordpress.org/Javascript_Reference/wp.template
 * @author        themeComplete
 * @package       WooCommerce Extra Product Options/Templates
 * @version       4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<script class="tm-hidden" type="text/template" id="tmpl-tc-floatbox">
    <div class="header">
        <h3>{{{ data.title }}}</h3>
        <# if (data.uniqid){ #>
            <span data-uniqid="{{{ data.uniqid }}}" class="tm-element-uniqid">{{{ data.uniqidtext }}}{{{ data.uniqid }}}</span>
            <# } #>
    </div>
    <div id="{{{ data.id }}}" class="float_editbox">{{{ data.html }}}</div>
    <div class="footer">
        <div class="inner">
            <button type="button" class="tc tc-button floatbox-update">{{{ data.update }}}</button>&nbsp;
            <button type="button" class="tc tc-button floatbox-cancel">{{{ data.cancel }}}</button>
        </div>
    </div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-floatbox-edit">
    <div class="header">
        <h3>{{{ data.title }}}</h3>
    </div>
    <div id="{{{ data.id }}}" class="float_editbox">{{{ data.html }}}</div>
    <div class="footer">
        <div class="inner">
            <button type="button" class="tc tc-button floatbox-edit-update">{{{ data.update }}}</button>&nbsp;
            <button type="button" class="tc tc-button floatbox-edit-cancel">{{{ data.cancel }}}</button>
        </div>
    </div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-floatbox-import">
    <div class="header">
        <h3>{{{ data.title }}}</h3>
    </div>
    <div id="{{{ data.id }}}" class="float_editbox">{{{ data.html }}}</div>
    <div class="footer">
        <div class="inner">
            <button type="button" class="tc tc-button floatbox-cancel">{{{ data.cancel }}}</button>
        </div>
    </div>
</script>
