<div class="ac-section -syncronisation -closable" data-section="ac-sync">
	<div class="ac-section__header">
		<h2 class="ac-section__header__title">
			<?php _e( 'Synchronisation', 'codepress-admin-columns' ); ?>
		</h2>
	</div>
	<div class="ac-section__body">
		<p>
			<?php _e( 'With synchronisation enabled, your column settings wil be stored in both the database and in PHP files.', 'codepress-admin-columns' ); ?>&nbsp;
			<?php _e( 'Making it easier to share column settings between environments (development, staging and production sites).', 'codepress-admin-columns' ); ?>
		</p>
		<p>
			<?= $this->message; ?>
		</p>
		<p>
			<?php _e( 'Once this folder exists, each time you save your columns a PHP file will be created (or updated) with the column settings.', 'codepress-admin-columns' ); ?>
		</p>
		<p>
			<?php printf( __( 'Read more about: %s', 'codepress-admin-columns' ), sprintf( '<a target="_blank" href="%s">%s</a>', ac_get_site_url( 'synchronisation' ), __( 'Synchronisation', 'codepress-admin-columns' ) ) ); ?>
		</p>
	</div>
</div>