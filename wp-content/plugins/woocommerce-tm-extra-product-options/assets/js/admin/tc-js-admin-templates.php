<?php
/**
 * The admin javascript-based template for displayed javascript generated html code
 *
 * NOTE that this file is not meant to be overriden
 *
 * @see     https://codex.wordpress.org/Javascript_Reference/wp.template
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 4.0
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
	<div id="{{{ data.id }}}" class="float-editbox">{{{ data.html }}}</div>
	<div class="footer">
		<div class="inner">
			<button type="button" class="tc tc-button floatbox-update">{{{ data.update }}}</button>&nbsp;
			<button type="button" class="tc tc-button alt floatbox-cancel">{{{ data.cancel }}}</button>
		</div>
	</div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-floatbox-edit">
	<div class="header">
		<h3>{{{ data.title }}}</h3>
	</div>
	<div id="{{{ data.id }}}" class="float-editbox">{{{ data.html }}}</div>
	<div class="footer">
		<div class="inner">
			<button type="button" class="tc tc-button floatbox-edit-update">{{{ data.update }}}</button>&nbsp;
			<button type="button" class="tc tc-button alt floatbox-edit-cancel">{{{ data.cancel }}}</button>
		</div>
	</div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-floatbox-import">
	<div class="header">
		<h3>{{{ data.title }}}</h3>
	</div>
	<div id="{{{ data.id }}}" class="float-editbox">{{{ data.html }}}</div>
	<div class="footer">
		<div class="inner">
			<button type="button" class="tc tc-button alt floatbox-cancel">{{{ data.cancel }}}</button>
		</div>
	</div>
</script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-constant-template">
	<tr valign="top" class="constantrow">
		<th scope="row" class="titledesc">
			<div class="constant-label-wrap constant-name-text{{{ data.labelnameclass }}}">
				<label for="constant-name{{{ data.id }}}">{{{ data.labelname }}}</label>
				<input id="constant-name{{{ data.id }}}" type="text" value="{{{ data.constantname }}}" class="constant-name">
			</div>
		</th>
		<td class="forminp forminp-text">
			<div class="constant-value-wrap">
				<div class="constant-label-wrap constant-value-text{{{ data.labelvalueclass }}}">
					<label for="constant-value{{{ data.id }}}">{{{ data.labelvalue }}}</label>
					<input id="constant-value{{{ data.id }}}" type="text" value="{{{ data.constantvalue }}}" class="constant-value">
				</div>
				<div class="constant-value-delete">
					<div class="tc-constant-delete">
						<button type="button" class="tmicon tcfa tcfa-times delete"></button>
					</div>
				</div>
			</div>
		</td>
	</tr>
</script>
