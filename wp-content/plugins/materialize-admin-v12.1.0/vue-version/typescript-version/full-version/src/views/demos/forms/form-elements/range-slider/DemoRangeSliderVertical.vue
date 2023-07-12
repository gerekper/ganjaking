<script lang="ts" setup>
const sliderValues = ref([20, 40])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    direction="vertical"
  />
</template>
