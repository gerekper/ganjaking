<script setup>
const items = [
  'California',
  'Colorado',
  'Florida',
  'Georgia',
  'Texas',
  'Wyoming',
]
</script>

<template>
  <VAutocomplete
    label="States"
    :items="items"
    placeholder="Select State"
    multiple
  />
</template>
