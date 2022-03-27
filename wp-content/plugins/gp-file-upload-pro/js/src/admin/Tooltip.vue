<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
	name: 'Tooltip',
	props: ['content'],
	mounted: function () {
		/**
		 * @todo Are there general or performance concerns around re-running this for every tooltip?
		 */
		window.gform_initialize_tooltips();
	},
	computed: {
		isLegacy: function () {
			return document.body.classList.contains('gf-legacy-ui');
		},
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
			class="gf_tooltip tooltip"
			tooltip_gwreadonly_readonly=""
			:aria-label="content"
	>
		<slot>
			<i class="gform-icon gform-icon--question-mark" aria-hidden="true"></i>
		</slot>
	</button>
</template>
