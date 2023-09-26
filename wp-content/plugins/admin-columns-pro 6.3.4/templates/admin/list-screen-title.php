<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="acp-layout-title">
	<input placeholder="<?php _e( 'Add title', 'codepress-admin-columns' ); ?>" name="title" value="<?= $this->title; ?>"/>
</div>
<style>
	.acp-layout-title input {
		width: 100%;
		font-size: 1.3em;
		line-height: 100%;
		padding: 3px 8px;
		margin-bottom: 20px;
		color: #494949;
		height: 40px;
	}
</style>