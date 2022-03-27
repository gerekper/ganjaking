<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
	name: 'Tooltip',
	props: ['content', 'options', 'buttonClass'],
	mounted: function () {
		/**
		 * @todo Are there general or performance concerns around re-running this for every tooltip?
		 */
		window.gform_initialize_tooltips();
		jQuery(document).trigger('gperks_tooltips_initialized');
	},
	computed: {
		isLegacy: function () {
			return document.body.classList.contains('gf-legacy-ui');
		},
		gpTooltipOptions: function () {
			if (!this.options) {
				return null;
			}

			return JSON.stringify(this.options);
		},
		buttonClasses: function() {
			let cssClass = 'gf_tooltip tooltip gp-tooltip';

			if (this.buttonClass) {
				cssClass += ' ' + this.buttonClass;
			}

			return cssClass;
		}
	}
});
</script>

<template>
	<a v-if="isLegacy"
	   href="#"
	   onclick="return false;"
	   onkeypress="return false;"
	   class="gf_tooltip tooltip tooltip_form_field_label"
	   :title="content">
		<i class="fa fa-question-circle"></i>
	</a>

	<button v-else
	        onclick="return false;"
	        onkeypress="return false;"
	        :class="buttonClasses"
	        :data-gp-tooltip-options="gpTooltipOptions"
	        tooltip_gwreadonly_readonly=""
	        :aria-label="content"
	>
		<slot>
			<i class="gform-icon gform-icon--question-mark" aria-hidden="true"></i>
		</slot>
	</button>
</template>
