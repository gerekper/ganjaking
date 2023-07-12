<script setup lang="ts">
const sliderValues = ref([10, 60])
</script>

<template>
  <VRangeSlider v-model="sliderValues" />
</template>
