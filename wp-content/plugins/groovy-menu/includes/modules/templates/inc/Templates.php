<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class Templates
 */
class Templates {

	public function __construct() {
	}

	static function presetLibraryModal( $presets = array() ) {
		?>

		<div
				class="gm-modal gm-hidden"
				id="add-preset-from-library">
			<div class="gm-modal-header">
				<h4 class="modal-title"><?php esc_html_e( 'Groovy presets library', 'groovy-menu' ); ?></h4>
			</div>
			<div class="gm-modal-body">
				<div class="gm-modal-row">
					<?php
					if ( empty( $presets ) ) {
						echo '<div class="gm-modal-notice">'.
						     esc_html__( 'To show presets from the online library, please enable toggle beside "Allow fetching presets from online library" placed in "Global Settings" at the "Tools" tab', 'groovy-menu' ) .
						'</div>';
					}

					foreach ( $presets as $preset ) { ?>
						<div
								class="preset"
								data-id="<?php echo esc_attr( $preset['id'] ); ?>"
								data-name="<?php echo esc_attr( $preset['name'] ); ?>"
						>
							<div class="preset-inner">
								<div class="preset-placeholder">
									<img
											src="<?php echo esc_attr( $preset['img'] ); ?>"
											alt="">
								</div>
								<div class="preset-info">
									<div class="preset-title">
											<span
													class="preset-title__alpha"><?php echo esc_html( $preset['name'] ); ?></span>
									</div>
									<div class="preset-options">
										<i class="preset-preview fa fa-search"></i>
										<i
												data-href="?page=groovy_menu_settings&action=importFromLibrary&id=<?php echo esc_attr( $preset['id'] ); ?>"
												class="preset-import-from-library fa fa-plus"></i>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>

				</div>
			</div>
		</div>

		<?php
	}

	static function presetImportModal() {
		?>

		<div class="gm-modal gm-hidden" id="import-modal">
			<div class="gm-modal-content">
				<form
						method="post"
						action="?page=groovy_menu_settings&action=import"
						enctype="multipart/form-data">
					<?php echo wp_nonce_field(); ?>
					<div class="gm-modal-header">
						<h4 class="modal-title"><?php esc_html_e( 'Import', 'groovy-menu' ); ?></h4>
					</div>
					<div class="gm-modal-body">
						<input
								type="file"
								name="import"/>
					</div>
					<div class="gm-modal-footer">
						<div class="btn-group">
							<button
									type="submit"
									class="btn modal-btn"><?php esc_html_e( 'Import', 'groovy-menu' ); ?>
							</button>
							<button
									type="button"
									class="btn modal-btn gm-modal-close"><?php esc_html_e( 'Close', 'groovy-menu' ); ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="gm-modal gm-hidden" id="import-preset-modal">
			<div class="gm-modal-content">
				<form
					class="gm-import-preset-form"
					method="post"
					action="?page=groovy_menu_settings&action=importPreset"
					enctype="multipart/form-data">
					<?php echo wp_nonce_field(); ?>
					<div class="gm-modal-header">
						<h4 class="modal-title"><?php esc_html_e( 'Import', 'groovy-menu' ); ?> <?php esc_html_e( 'preset', 'groovy-menu' ); ?> <span class="gm-modal-title-preset-name"></span></h4>
					</div>
					<div class="gm-modal-body">
						<input
							type="file"
							name="import"/>
					</div>
					<div class="gm-modal-footer">
						<div class="btn-group">
							<button
								type="submit"
								class="btn modal-btn"><?php esc_html_e( 'Import', 'groovy-menu' ); ?>
							</button>
							<button
								type="button"
								class="btn modal-btn gm-modal-close"><?php esc_html_e( 'Close', 'groovy-menu' ); ?>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<?php
	}

	static function presetNewDashboard() {
		?>

		<div class="preset preset--create-new">
			<div class="preset-inner">
				<div class="preset-placeholder">
					<div class="preset-placeholder-inner">
						<span class="gm-gui-icon gm-icon-list"></span>
						<span
							class="preset-title__alpha"><?php esc_html_e( 'New preset', 'groovy-menu' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<?php
	}


	static function presetImportDashboard() {
		?>

		<div class="preset preset--import">
			<div class="preset-inner">
				<div
					class="preset-placeholder"
					data-target="#import-modal"
					data-toggle="modal">
					<div class="preset-placeholder-inner">
						<span class="gm-gui-icon gm-icon-download"></span>
						<span
							class="preset-title__alpha"><?php esc_html_e( 'Import preset', 'groovy-menu' ); ?></span>
					</div>
				</div>
			</div>
		</div>

		<?php
	}


	static function presetActionLiDublicate() {
		?>

		<li class="preset-opts__nav__item preset-duplicate">
			<i class="fa fa-clone"></i>
			<span
				class="preset-opts__nav__item__txt"><?php esc_html_e( 'Duplicate', 'groovy-menu' ); ?></span>
		</li>

		<?php
	}


	static function presetActionLiExport() {
		?>

		<li class="preset-opts__nav__item preset-export">
			<i class="fa fa-paper-plane"></i>
			<span
				class="preset-opts__nav__item__txt"><?php esc_html_e( 'Export', 'groovy-menu' ); ?></span>
		</li>

		<?php
	}


	static function presetActionLiImport() {
		?>

		<li class="preset-opts__nav__item preset-import">
			<i class="fa fa-long-arrow-up"></i>
			<span
				class="preset-opts__nav__item__txt"><?php esc_html_e( 'Import', 'groovy-menu' ); ?></span>
		</li>

		<?php
	}

}
