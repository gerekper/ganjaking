<script lang="ts" setup>
const slidersValues = ref([30, 60])
</script>

<template>
  <VRangeSlider
    v-model="slidersValues"
    disabled
    label="Disabled"
  />
</template>
