<?php

use Happy_Addons\Elementor\Theme_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$types    = Theme_Builder::TEMPLATE_TYPE;
$selected = get_query_var( 'ha_library_type' );

?>
<script type="text/template" id="tmpl-elementor-new-template">
	<div id="elementor-new-template__description">
		<div id="elementor-new-template__description__title">
			<?php
			printf(
				esc_html__( '%1$s Happy Addon %2$s Theme Builder Helps You %3$sWork Efficiently%4$s', 'happy-elementor-addons' ),
				'<span>',
				'</span>',
				'<span>',
				'</span>'
			);
			?>
	    </div>
		<div id="elementor-new-template__description__content"><?php echo esc_html__( 'Create various bits and pieces (e.g: Header, Footer etc) of your site and then later reuse them when needed.', 'happy-elementor-addons' ); ?></div>
	</div>
	<form id="elementor-new-template__form" action="<?php esc_url( admin_url( '/edit.php' ) );?>">
		<input type="hidden" name="post_type" value="ha_library">
		<input type="hidden" name="action" value="ha_library_new_post">
		<?php // PHPCS - a nonce doesn't have to be escaped. ?>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ha_library_new_post_action' ); ?>">

		<div id="newViewGroup" x-data="newTemplateForm()" x-init="
			$watch('selectedSingular', value => {
				if(value == 'selective'){
					jQuery('#elementor-new-template__display_type_selected').select2({
						dropdownParent: jQuery('#elementor-new-template-modal')
					});
				}
			});
		">

			<div x-show="step == 1">
				<div>
					<div id="elementor-new-template__form__title"><?php echo esc_html__( 'Choose Template Type', 'happy-elementor-addons' ); ?></div>
					<div id="elementor-new-template__form__template-type__wrapper" class="elementor-form-field">
						<label for="elementor-new-template__form__template-type" class="elementor-form-field__label"><?php echo esc_html__( 'Select the type of template you want to work on', 'happy-elementor-addons' ); ?></label>
						<div class="elementor-form-field__select__wrapper">
							<select id="elementor-new-template__form__template-type" class="elementor-form-field__select" x-model="templateType" name="template_type" required>
								<option value=""><?php echo esc_html__( 'Select', 'happy-elementor-addons' ); ?>...</option>
								<?php
foreach ( $types as $value => $type_title ) {
	printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $selected, $value, false ), esc_html( $type_title ) );
}
?>
							</select>
						</div>
					</div>

					<div id="elementor-new-template__form__post-title__wrapper" class="elementor-form-field">
						<label for="elementor-new-template__form__post-title" class="elementor-form-field__label">
							<?php echo esc_html__( 'Name your template', 'happy-elementor-addons' ); ?>
						</label>
						<div class="elementor-form-field__text__wrapper">
							<input type="text" x-model="postTitle" placeholder="<?php echo esc_attr__( 'Enter template name (optional)', 'happy-elementor-addons' ); ?>" id="elementor-new-template__form__post-title" class="elementor-form-field__text" name="post_data[post_title]">
						</div>
					</div>

					<button @click.prevent="step = 2" x-bind:disabled="buttonDisabled()" id="elementor-new-template__form__submit" class="elementor-button ha-btn ha-btn-primary"><?php echo esc_html__( 'Next', 'happy-elementor-addons' ); ?></button>
				</div>
			</div>

			<div x-show="step == 2">
				<div>
					<div id="elementor-new-template__form__title"><?php echo esc_html__( 'Choose Display Condition', 'happy-elementor-addons' ); ?></div>
					<div id="elementor-new-template__form__post-title__wrapper" class="elementor-form-field">
						<div class="elementor-form-field__select__wrapper">
							<label class="elementor-form-field__label"> </label>
							<select id="elementor-new-template__display_type" x-model="selectedType" class="elementor-form-field__select" name="template_display_type" required>
								<template x-if="templateType != 'single'">
									<template x-for="[key,value] in Object.entries(conditionType)">
										<option
											x-bind:value="key"
											x-text="value"
											x-bind:selected="key === selectedType"
										></option>
									</template>
								</template>
								<template x-if="templateType == 'single'">
									<template x-for="[key,value] in Object.entries(singularData)">
										<option
											x-bind:value="key"
											x-text="value"
											x-bind:selected="key === selectedType"
										></option>
									</template>
								</template>
							</select>
						</div>
					</div>
					<div x-show="selectedType === 'singular'">
						<div id="elementor-new-template__form__post-title__wrapper" class="elementor-form-field">
							<div class="elementor-form-field__select__wrapper">
							<label class="elementor-form-field__label"> </label>
								<select x-model="selectedSingular" @change="getSelective()" id="elementor-new-template__display_type_singular" class="elementor-form-field__select" name="template_display_type_singular">
									<template x-for="[key,value] in Object.entries(singularData)">
										<option
											x-bind:value="key"
											x-text="value"
											x-bind:selected="key === selectedSingular"
										></option>
									</template>
								</select>
							</div>
						</div>
					</div>
					<div x-show="selectedSingular == 'selective'">
						<div id="elementor-new-template__form__post-title__wrapper" class="elementor-form-field">
							<div class="elementor-form-field__select__wrapper">
							<label class="elementor-form-field__label"> </label>
								<select id="elementor-new-template__display_type_selected" class="elementor-form-field__select" name="template_display_type_selected[]" multiple>
								<?php
									$pages = get_pages();
									foreach ( $pages as $page ) {
										$option = '<option value="' . $page->ID . '">';
										$option .= $page->post_title;
										$option .= '</option>';
										echo $option;
									}
								?>
								</select>
							</div>
						</div>
					</div>
					<button id="elementor-new-template__form__submit" class="elementor-button ha-btn ha-btn-primary"><?php echo esc_html__( 'Create Template', 'happy-elementor-addons' ); ?></button>
				</div>
			</div>
		</div>
	</form>
</script>

<script type="text/template" id="tmpl-ha-templates-modal__header__logo">
	<span class="elementor-templates-modal__header__logo__icon-wrapper ha-logo-wrapper">
		<!-- <i class="eicon-elementor"></i> -->
		<svg version="1.1" x="0px" y="0px" viewBox="0 0 110 118" enable-background="new 0 0 110 118" xml:space="preserve">
			<g>
				<g>
					<path fill="#ffffff" d="M101.1,27.8c1,0,1.9-0.2,2.9-0.2c1.9-0.2,3.1-1.9,2.9-3.6c-0.2-1.9-1.9-3.2-3.5-2.9
c-12.8,1.5-24.9-6.3-28.8-18.7c-0.6-1.7-2.5-2.7-4.1-2.1c-1.6,0.6-2.7,2.5-2.1,4.2C72.9,18.7,86.5,28.4,101.1,27.8z" />
					<path fill="#ffffff" d="M105.9,40.6c-1-2.3-3.3-3.8-5.8-3.8c-3.3,0.2-6.8,0-10.3-0.8C75.4,33,64.5,22.7,59.5,9.7
c-0.8-2.3-3.3-4-5.8-3.8C27,6.5,3.7,26.9,0.4,55.5c-2.9,26.3,13,51.5,37.5,59.7c31.7,10.5,64.5-9.5,71.1-42.1
C111.2,61.8,109.8,50.5,105.9,40.6z M63.9,44.8c0.4-1.7,2.1-2.9,3.9-2.5l13.6,2.9c1.6,0.4,2.9,2.1,2.5,4c-0.4,1.7-2.1,2.9-3.9,2.5
l-13.6-2.9C64.7,48.2,63.4,46.5,63.9,44.8z M33.8,40.4c0.8-4.2,4.9-6.9,9.1-6.1c4.1,0.8,6.8,5,6,9.3c-0.8,4.2-4.9,6.9-9.1,6.1
C35.6,48.8,33,44.6,33.8,40.4z M86.5,79.3C79.7,95.7,61.6,105,43.9,99.1c-13.2-4.4-22.5-16.8-23.7-30.5C20,65,22.9,62,26.4,62.7
l56,9.3C85.7,72.6,87.8,76.1,86.5,79.3z" />
					<path fill="#ffffff" d="M58.9,83.9c-6.8-1.5-13.4,1.3-17.1,6.3c-0.8,1.1-0.4,2.7,0.8,3.2c2.1,1.1,4.5,1.9,7,2.5
c6.6,1.5,13.2,0.2,18.5-2.7c1.2-0.6,1.4-2.3,0.6-3.4C66.3,86.9,62.8,84.8,58.9,83.9z" />
				</g>
			</g>
		</svg>
	</span>
	<span class="elementor-templates-modal__header__logo__title">{{{ title }}}</span>
</script>


<script type="text/template" id="tmpl-modal-new-template">
    <div class="modal micromodal-slide modal-template-condition ha-template-element-modal" id="modal-new-template" aria-hidden="false">
        <div class="modal__overlay" tabindex="-1">
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-login-title">
                <header class="modal__header">
                    <h3 class="modal__title" id="modal-2-title">
						<svg version="1.1" x="0px" y="0px" width="26px" viewBox="0 0 110 118" enable-background="new 0 0 110 118" xml:space="preserve">
							<g>
								<g>
									<path fill="#E2498A" d="M101.1,27.8c1,0,1.9-0.2,2.9-0.2c1.9-0.2,3.1-1.9,2.9-3.6c-0.2-1.9-1.9-3.2-3.5-2.9
				c-12.8,1.5-24.9-6.3-28.8-18.7c-0.6-1.7-2.5-2.7-4.1-2.1c-1.6,0.6-2.7,2.5-2.1,4.2C72.9,18.7,86.5,28.4,101.1,27.8z" />
									<path fill="#E2498A" d="M105.9,40.6c-1-2.3-3.3-3.8-5.8-3.8c-3.3,0.2-6.8,0-10.3-0.8C75.4,33,64.5,22.7,59.5,9.7
				c-0.8-2.3-3.3-4-5.8-3.8C27,6.5,3.7,26.9,0.4,55.5c-2.9,26.3,13,51.5,37.5,59.7c31.7,10.5,64.5-9.5,71.1-42.1
				C111.2,61.8,109.8,50.5,105.9,40.6z M63.9,44.8c0.4-1.7,2.1-2.9,3.9-2.5l13.6,2.9c1.6,0.4,2.9,2.1,2.5,4c-0.4,1.7-2.1,2.9-3.9,2.5
				l-13.6-2.9C64.7,48.2,63.4,46.5,63.9,44.8z M33.8,40.4c0.8-4.2,4.9-6.9,9.1-6.1c4.1,0.8,6.8,5,6,9.3c-0.8,4.2-4.9,6.9-9.1,6.1
				C35.6,48.8,33,44.6,33.8,40.4z M86.5,79.3C79.7,95.7,61.6,105,43.9,99.1c-13.2-4.4-22.5-16.8-23.7-30.5C20,65,22.9,62,26.4,62.7
				l56,9.3C85.7,72.6,87.8,76.1,86.5,79.3z" />
									<path fill="#E2498A" d="M58.9,83.9c-6.8-1.5-13.4,1.3-17.1,6.3c-0.8,1.1-0.4,2.7,0.8,3.2c2.1,1.1,4.5,1.9,7,2.5
				c6.6,1.5,13.2,0.2,18.5-2.7c1.2-0.6,1.4-2.3,0.6-3.4C66.3,86.9,62.8,84.8,58.9,83.9z" />
								</g>
							</g>
						</svg>
                        <span>Template Elements Condition</span>
                    </h3>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close=""></button>
                </header>
                <div class="modal__content new-template" id="modal-2-content">
					<div class="modal__information">
						<div class="info-title">HappyAddons Theme Builder helps you work efficiently</div>
                        <div class="info-message">Create various bits and pieces (e.g: Header, Footer etc) of your site and then later reuse them when needed.</div>
					</div>
					<form id="ha-new-template-form" action="<?php esc_url( admin_url( '/edit.php' ) );?>">
						<input type="hidden" name="post_type" value="ha_library">
						<input type="hidden" name="action" value="ha_library_new_post">
						<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ha_library_new_post_action' ); ?>">
						<div id="ha-new-template-form__title"><?php echo esc_html__( 'Choose Template Type', 'happy-elementor-addons' ); ?></div>
							<div id="ha-new-template-form__template-type__wrapper" class="elementor-form-field">
								<div class="ha-new-template-form__select__wrapper">
									<select id="ha-new-template-form__template-type" class="elementor-form-field__select" name="template_type" required>
										<option value=""><?php echo esc_html__( 'Select', 'happy-elementor-addons' ); ?>...</option>
										<?php
											foreach ( $types as $value => $type_title ) {
												printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $selected, $value, false ), esc_html( $type_title ) );
											}
										?>
									</select>
								</div>
							</div>

							<div id="ha-new-template-form__post-title__wrapper" class="elementor-form-field">
								<div class="ha-new-template-form__text__wrapper">
									<input type="text" placeholder="<?php echo esc_attr__( 'Enter template name', 'happy-elementor-addons' ); ?>" id="ha-new-template-form__post-title" class="ha-new-template-form__field__text" name="post_data[post_title]" required>
								</div>
							</div>

							<button id="ha-new-template-form__submit" class="ha-btn ha-btn-primary" disabled><?php echo esc_html__( 'Create Template', 'happy-elementor-addons' ); ?></button>
						</div>
					</form>
                </div>
            </div>
        </div>
    </div>
</script>