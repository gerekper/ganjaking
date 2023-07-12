<script lang="ts" setup>
const value = ref(10)
</script>

<template>
  <VSlider
    v-model="value"
    direction="vertical"
  />
</template>
