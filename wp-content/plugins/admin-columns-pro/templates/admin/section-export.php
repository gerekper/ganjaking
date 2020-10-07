<?php

use AC\Type\Url\Documentation;
use ACP\Migrate\Export;
use ACP\Storage\ListScreen\SerializerTypes;

?>
<div class="ac-section -export">
	<div class="ac-section__header">
		<h2 class="ac-section__header__title"><?= __( 'Export', 'codepress-admin-columns' ) ?></h2>
	</div>
	<div class="ac-section__body">
		<p>
			<?= __( 'Select the columns settings you would like to export.', 'codepress-admin-columns' ); ?>
			<?= __( 'The result is a JSON file that can be imported in any WordPress install that uses Admin Columns Pro.', 'codepress-admin-columns' ); ?>
		</p>

		<form method="POST">
			<?php wp_nonce_field( Export\Request::ACTION, Export\Request::NONCE_NAME ); ?>
			<input type="hidden" name="action" value="<?= Export\Request::ACTION ?>">
			<input type="hidden" name="encoder" value="<?= SerializerTypes::JSON ?>">
			<?= $this->table->render() ?>
			<div style="display: flex;">
				<button class="button button-primary" data-export="<?= SerializerTypes::JSON ?>"><?php _e( 'Export To JSON', 'codepress-admin-columns' ); ?></button>
				<p class="php-export">
					<?= __( 'Looking for PHP Export?', 'codepress-admin-columns' ); ?>
					<a target="_blank" href="<?= esc_url( ( new Documentation( Documentation::ARTICLE_LOCAL_STORAGE ) )->get_url() ); ?>">
						<?= sprintf( __( 'Read about its successor: %s', 'codepress-admin-columns' ), __( 'Local Storage', 'codepress-admin-columns' ) ); ?>
					</a>
				</p>
			</div>
		</form>
	</div>
</div>