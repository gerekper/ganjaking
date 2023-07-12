<script setup>
const selectedItem = ref('Programming')

const items = [
  'Programming',
  'Design',
  'Vue',
  'Vuetify',
]
</script>

<template>
  <VCombobox
    v-model="selectedItem"
    :items="items"
  />
</template>
