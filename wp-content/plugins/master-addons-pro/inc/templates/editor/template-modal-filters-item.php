<?php
/**
 * Template Library Filter Item
 */
?>
<label class="ma-el-modal-template-filter-label">
	<input type="radio" value="{{ slug }}" <# if ( '' === slug ) { #> checked<# } #> name="ma-el-modal-template-filter">
	<span>{{ title.replace('&amp;', '&') }}</span>
</label>