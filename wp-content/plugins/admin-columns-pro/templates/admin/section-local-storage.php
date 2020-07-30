<div class="ac-section -local_storage">
	<div class="ac-section__header">
		<h2 class="ac-section__header__title"><?= __( 'Local Storage', 'codepress-admin-columns' ) ?></h2>
	</div>
	<div class="ac-section__body">
		<p>
			Local storage allows you to store column settings in PHP files instead of the database. This allows you to ship and migrate column settings easily between environments and even store them in a version control system (VCS) to share with your fellow developers.
		</p>
		<p>
			By using the <em>acp/storage/file/directory</em> hook, you can set the storage directory to a folder on your file system.<br>
			Enable this functionality by placing this code snippet into your theme's `functions.php` file:
		</p>
		<pre class="code-snippet-local-storage">
add_filter( 'acp/storage/file/directory', function() {
	return get_stylesheet_directory() . '/acp-settings';
} );</pre>
		<p>
			Every time you save your column settings, a PHP file will be created in the provided folder.
		</p>
		<p style="border: 1px solid #fbed50; border-radius: 2px; padding: 8px 10px; background: #fcefa1;">
			Local Storage is available since Admin Columns Pro 5.1 and its the replacement for "PHP Export". You can migrate your old settings to local storage by following <a target="_blank" href="<?= ac_get_site_url( 'documentation/local-storage' ); ?>#migrate">our migration guide</a>.
		</p>
		Read more about <a target="_blank" href="<?= ac_get_site_url( 'documentation/local-storage' ); ?>">Local Storage on our website &raquo;</a>
	</div>
</div>