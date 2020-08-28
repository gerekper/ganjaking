<template>
	<span v-if="'data:' === icon.substring(0, 5)" class="searchwp-button-icon searchwp-button-icon-svg" :style="{
		backgroundSize: '85% 85%',
		backgroundRepeat: 'no-repeat',
		backgroundPosition: 'center center',
		backgroundImage: 'url(' + icon + ') !important'
	}"></span>
	<span v-else :class="[ 'searchwp-button-icon', icon ]"></span>
</template>

<script>
export default {
	name: 'Icon',
	props: {
		icon: {
			type: String,
			required: true
		}
	}
}
</script>

<style lang="scss">

</style>
