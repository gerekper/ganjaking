<div class="notice notice-info is-dismissible">
	<?php if ( $migration_started ) : ?>
		<p>
			<?php
			esc_html_e(
				'Your vendor admin data is being migrated to the new storage method. This may take a while.',
				'woocommerce-product-vendors'
			);
			?>
		</p>
	<?php else : ?>
		<form method="post">
			<p>
				<?php
				esc_html_e(
					'There is a new way to store vendor admin information in the database. Please migrate your data to the new storage method.',
					'woocommerce-product-vendors'
				);
				?>
			</p>
			<p>
				<input type="hidden" name="wcpv_migrate_admin_storage_migrate" value="1" />
				<?php submit_button(
					__(
						'Start migration',
						'woocommerce-product-vendors'
					),
					'primary',
					'migration-submit',
					false
				); ?>
			</p>
		</form>
	<?php endif; ?>
</div>