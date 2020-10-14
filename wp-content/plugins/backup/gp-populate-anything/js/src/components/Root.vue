<template>
	<div id="gppa">
		<populate-dynamically ref="choices" :field="field" populate="choices"></populate-dynamically>
		<populate-dynamically ref="values" :field="field" populate="values"></populate-dynamically>
	</div>
</template>

<script lang="ts">
	import PopulateDynamically from './PopulateDynamically'

	export default {
		name: 'Root',
		props: [
			'field',
		],
		components: {
			PopulateDynamically
		},
		methods: {
			refresh: function () {
				this.$refs.choices.initialLoad();
				this.$refs.values.initialLoad();
			},
		}
	}
</script>
