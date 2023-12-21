<?php

use Happy_Addons\Elementor\Theme_Builder;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$types = Theme_Builder::TEMPLATE_TYPE;
$selected = get_query_var('ha_library_type');

?>

<div class="modal micromodal-slide" id="modal-login" aria-hidden="false">
	<div class="modal__overlay" tabindex="-1">
		<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-login-title">
			<header class="modal__header">
				<h3 class="modal__title" id="modal-2-title">
					Edit <span id="edit-template-type"></span>Template
				</h3>
				<button class="modal__close" aria-label="Close modal" data-micromodal-close=""></button>
			</header>
			<form id="ha-template-edit-form">
				<div class="modal__content" id="modal-2-content">
					<div class="ha-template-form-input-group">
						<div class="ha-template-form-field__switch__wrapper">
							<h2 class="settings_title">
								<label for="ha-template-activate">Activate Template</label>
							</h2>
							<label for="ha-template-activate">
								<div class="ha-dashboard-widgets__item-toggle ha-toggle">
									<input id="ha-template-activate" type="checkbox" class="ha-toggle__check ha-feature" name="template_active" value="active">
									<b class="ha-toggle__switch"></b>
									<b class="ha-toggle__track"></b>
								</div>
							</label>
						</div>
					</div>
					<h2 class="settings_title">Display Conditions</h2>

					<div class="ha-template-form-input-group">
						<div class="ha-template-form-field__select__wrapper">
							<label class="elementor-form-field__label"> </label>
							<select id="template_display_type" name="template_display_type" required>
								<option value="general">Entire Website</option>
								<option value="singular">Sigular (Only Pro)</option>
								<option value="archive">Archive (Only Pro)</option>
							</select>
						</div>
					</div>

					<div class="ha-template-form-input-group">
						<div class="ha-template-form-field__select__wrapper">
							<label class="attr-input-label"></label>
							<select id="condition_singular" name="condition_singular">
								<option value="all">All Singulars (Only Pro)</option>
								<option value="front_page">Front Page (Only Pro)</option>
								<option value="posts">All Posts (Only Pro)</option>
								<option value="pages">All Pages (Only Pro)</option>
								<option value="selective">Selective Singular (Only Pro) </option>
								<option value="404page">404 Page (Only Pro)</option>
							</select>
						</div>
					</div>

					<div class="ha-template-form-input-group">
						<div class="ha-template-form-field__select__wrapper">
							<label class="attr-input-label"></label>
							<select id="ha-template-singular-select2" multiple name="condition_singular_id[]" class="ha-template-form-field__select__wrapper"></select>
						</div>
					</div>
				</div>
			</form>
			<footer class="modal__footer">
				<button id="ha-template-edit" class="modal__btn modal__btn-secondary">Edit Content</button>
				<button id="ha-template-save-data" class="modal__btn modal__btn-primary">Save Settings</button>
			</footer>
		</div>
	</div>
</div>