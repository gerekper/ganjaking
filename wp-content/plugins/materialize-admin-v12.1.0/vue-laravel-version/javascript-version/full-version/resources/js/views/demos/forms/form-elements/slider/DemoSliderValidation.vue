<script setup>
const value = ref(30)
const rules = [v => v <= 40 || 'Only 40 in stock']
</script>

<template>
  <VSlider
    v-model="value"
    :error="value > 40"
    :rules="rules"
    :step="10"
    thumb-label="always"
    show-ticks
  />
</template>
