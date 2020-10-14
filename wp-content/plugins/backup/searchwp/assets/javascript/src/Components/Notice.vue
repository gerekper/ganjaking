<template>
	<div :class="['searchwp-notice', type && type.length ? 'inline notice notice-' + type : '']">
		<p v-if="message && message.length" :class="[cssClass ? cssClass : '']">
			<span v-if="icon" :class="icon"></span>
			<Tooltip v-if="tooltip && tooltip.length" :content="tooltip">
				{{ message }}
			</Tooltip>
			<span v-else>{{ message }}</span>
			<a v-if="more && more.target && more.text" :href="more.target" target="_blank">
				{{ more.text }}
			</a>
		</p>
		<Tooltip v-else-if="tooltip && tooltip.length" :content="tooltip">
			{{ message }}
		</Tooltip>
		<slot></slot>
	</div>
</template>

<script>
import Tooltip from './Tooltip.vue';

export default {
	name: 'Notice',
	props: {
		type: String,
		icon: String,
		message: String,
		tooltip: String,
		more: {
			type: Object,
			default: function() {
				return {
					target: '',
					text: ''
				}
			}
		},
		cssClass: String
	},
	components: {
		Tooltip
	}
}
</script>

<style lang="scss">

</style>
