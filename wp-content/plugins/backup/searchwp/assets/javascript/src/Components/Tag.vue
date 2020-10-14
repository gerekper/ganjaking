<template>
	<span :class="['searchwp-tag', 'searchwp-tag-' + type]">
		<slot></slot>
	</span>
</template>

<script>
export default {
	name: 'Tag',
	props: {
		type: {
			type: String,
			default: 'searchwp-tag-success',
			required: false
		}
	}
}
</script>

<style lang="scss">
	.searchwp-tag {
		border-radius: 2px;
		display: inline-block;
		line-height: 1;
		padding: 0.4em 0.6em;
		color: #fff;
		font-weight: 400;
		font-size: 13px;
		background-color: #75A575;
	}

	.searchwp-tag-error {
		background-color: #c82b2b;
	}
</style>
