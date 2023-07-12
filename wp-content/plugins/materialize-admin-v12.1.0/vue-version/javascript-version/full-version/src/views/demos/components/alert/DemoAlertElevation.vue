<script setup>
const alertShadow = ref(5)
</script>

<template>
  <VSlider
    v-model="alertShadow"
    color="primary"
    :max="24"
    :min="0"
    :step="1"
    thumb-label
  />

  <VAlert
    color="primary"
    :elevation="alertShadow"
  >
    I'm an alert with box shadow.
  </VAlert>
</template>
