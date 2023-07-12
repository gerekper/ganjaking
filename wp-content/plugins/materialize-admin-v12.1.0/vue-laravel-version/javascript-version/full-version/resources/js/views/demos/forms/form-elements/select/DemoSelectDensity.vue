<script setup>
const items = [
  'Foo',
  'Bar',
  'Fizz',
  'Buzz',
]
</script>

<template>
  <VSelect
    :items="items"
    label="Density"
    density="compact"
    placeholder="Select Item"
  />
</template>
