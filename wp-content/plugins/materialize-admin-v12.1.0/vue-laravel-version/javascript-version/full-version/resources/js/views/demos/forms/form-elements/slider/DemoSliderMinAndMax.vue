<script setup>
const min = ref(-50)
const max = ref(90)
const slider = ref(40)
</script>

<template>
  <div class="d-flex justify-space-between">
    <VSlider
      v-model="slider"
      :max="max"
      :min="min"
      :step="1"
    />

    <VTextField
      v-model="slider"
      type="number"
      style="max-inline-size: 5rem;"
    />
  </div>
</template>
