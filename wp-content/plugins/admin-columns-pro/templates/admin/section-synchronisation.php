<div class="ac-section -syncronisation -closable" data-section="ac-sync" data-selected="<?= filter_input( INPUT_GET, 'selected_id' ); ?>">
	<div class="ac-section__header">
		<h2 class="ac-section__header__title">
			<?php _e( 'Synchronisation', 'codepress-admin-columns' ); ?>
		</h2>
	</div>
	<div class="ac-section__body">
		<p>
			<?php _e( 'Synchronisation is enabled. Your column settings wil be stored in both the database and in PHP files.', 'codepress-admin-columns' ); ?>&nbsp;
			<?php _e( 'Making it easier to share column settings between environments (development, staging and production sites).', 'codepress-admin-columns' ); ?>
		</p>
		<p>
			<?php printf( 'Sync directory: %s', sprintf( '<code>%s</code>', $this->directory ) ); ?>
		</p>
		<ul class="subsubsub ac-sync-filter" data-table-filter>
			<li class="sync">
				<a class="current" data-filter="-outofsync"><?php _e( 'Sync Available', 'codepress-admin-columns' ); ?></a>
			</li>
			<li class="all">
				<a><?php _e( 'All', 'codepress-admin-columns' ); ?></a>
			</li>
		</ul>
		<form method="post">
			<input type="hidden" name="ac_action" value="ac_sync_import"/>
			<?php $this->table->render(); ?>
			<button class="button button-primary"><?= __( 'Sync Selected', 'codepress-admin-columns' ); ?></button>
		</form>
	</div>
</div>