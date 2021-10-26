<?php

use AC\Form\Element;
use ACP\RequestHandler\ListScreenCreate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="item new">
	<form method="post">

		<input type="hidden" name="_ac_nonce" value="<?= esc_attr( $this->nonce ); ?>"/>
		<input type="hidden" name="<?= ListScreenCreate::PARAM_ACTION; ?>" value="<?= ListScreenCreate::PARAM_CREATE_LIST; ?>">
		<input type="hidden" name="list_key" value="<?= esc_attr( $this->list_screen->get_key() ); ?>">
		<input type="hidden" name="list_id" value="<?= esc_attr( $this->list_screen->get_layout_id() ); ?>">

		<div class="body">
			<div class="row info">
				<p><?php printf( __( "Create new sets to switch between different column views on the %s screen.", 'codepress-admin-columns' ), $this->list_screen->get_label() ); ?></p>
			</div>
			<div class="row name">
				<label for="new_listscreen_name">
					<?php _e( 'Name', 'codepress-admin-columns' ); ?>
				</label>
				<div class="input">
					<div class="ac-error-message">
						<p>
							<?php _e( 'Please enter a title.', 'codepress-admin-columns' ); ?>
						<p>
					</div>
					<input name="title" id="new_listscreen_name" class="name" data-value="" placeholder="<?= __( 'Enter name', 'codepress-admin-columns' ); ?>" value="" type="text">
				</div>
			</div>
			<div class="row template">
				<?php
				$toggle = new Element\Toggle( 'clone_current', __( 'Copy current settings', 'codepress-admin-columns' ), true, 1 );
				echo $toggle->render()
				?>
			</div>

			<div class="row actions">

				<a class="instructions ac-pointer" rel="layout-help" data-pos="left" data-width="305" data-noclick="1">
					<?php _e( 'Instructions', 'codepress-admin-columns' ); ?>
				</a>

				<input class="save button-primary" type="submit" value="<?php _e( 'Add', 'codepress-admin-columns' ); ?>">
			</div>
		</div>

	</form>
</div>