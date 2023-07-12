<script setup>
const selectedRadio = ref('primary')

const colorsRadio = [
  'Primary',
  'Secondary',
  'Success',
  'Info',
  'Warning',
  'Error',
]
</script>

<template>
  <VRadioGroup
    v-model="selectedRadio"
    inline
  >
    <VRadio
      v-for="radio in colorsRadio"
      :key="radio"
      :label="radio"
      :color="radio.toLocaleLowerCase()"
      :value="radio.toLocaleLowerCase()"
    />
  </VRadioGroup>
</template>
