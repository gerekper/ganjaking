<script setup lang="ts">
const items = ['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming']
</script>

<template>
  <VAutocomplete
    label="States"
    :items="items"
    placeholder="Select State"
  />
</template>
