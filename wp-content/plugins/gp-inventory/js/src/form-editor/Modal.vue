<!--<script> is first to improve reliability of HMR not resetting state.
   See https://github.com/vuejs/vue-loader/issues/1682-->
<script lang="ts">
import Vue from 'vue';
import Tooltip from "./Tooltip.vue";
import { MountingPortal } from 'portal-vue';

export default Vue.extend({
	name: 'Modal',
	props: {
		show: {
			type: Boolean,
			required: true,
		},
		title: {
			type: String,
			required: true,
		},
		subtitle: {
			type: String,
			required: true,
		},
		submitText: {
			type: String,
			default: 'Save',
		},
		submitDisabled: {
			type: Boolean,
			default: false,
		}
	},
	components: {
		Tooltip,
		MountingPortal,
	},
	data() {
		return {};
	},
	watch: {
		show: function(show) {
			if (show) {
				document.body.classList.add('gp-inventory-modal-visible');
			} else {
				document.body.classList.remove('gp-inventory-modal-visible');
			}
		}
	},
	computed: {
	},
	methods: {}
});
</script>

<!-- append prop on MountingPortal is needed for v-if to work properly -->
<template>
	<MountingPortal mountTo="#gp-inventory-modal-portal" v-if="show" append>
		<div id="TB_overlay" class="TB_overlayBG"></div>
		<div id="TB_window" class="thickbox-loading gp-inventory-thickbox-window"
		     style="margin-left: -315px; width: 630px; margin-top: -250px; visibility: visible;">
			<div id="TB_title">
				<div id="TB_ajaxWindowTitle">
					<div class="tb-title">
						<div class="tb-title__logo"></div>
						<div class="tb-title__text">
							<div class="tb-title__main">{{ title }}</div>
							<div class="tb-title__sub">{{ subtitle }}</div>
						</div>
					</div>
				</div>
				<div id="TB_closeAjaxWindow">
					<button type="button" id="TB_closeWindowButton" @click="$emit('close')"><span class="screen-reader-text">{{ $i18n.close }}</span><span
						class="tb-close-icon"></span></button>
				</div>
			</div>
			<div id="TB_ajaxContent" style="width:600px;height:455px">
				<div class="gform_column_wrapper">
					<slot></slot>
				</div>
				<div class="modal_footer">
					<div class="panel-buttons" style="">
						<input type="button" class="button-primary" :disabled="submitDisabled" :value="submitText" @click="$emit('submit')">&nbsp;
						<input type="button" class="button" value="Cancel" @click="$emit('close')">
					</div>
					<div class="panel-right-buttons">
						<slot name="panel-right-buttons"></slot>
					</div>
				</div>
			</div>
		</div>
	</MountingPortal>
</template>

<style>
.gp-inventory-modal-visible .ui-tooltip.ui-widget-content {
	z-index: 10000;
}

#gp-inventory-modal-portal #TB_overlay {
	z-index: 9998;
}

#gp-inventory-modal-portal #TB_window {
	z-index: 9999;
}

.gp-inventory-thickbox-window .modal_footer {
	display: flex;
}

.gp-inventory-thickbox-window .panel-right-buttons {
	margin-left: auto;
	align-items: center;
	display: flex;
}

.gp-inventory-thickbox-window .button-link-delete {
	color: #a00;
	text-decoration: none;
}
</style>
