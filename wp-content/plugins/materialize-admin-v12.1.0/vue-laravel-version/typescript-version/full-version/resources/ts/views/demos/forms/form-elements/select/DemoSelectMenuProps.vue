<script lang="ts" setup>
const items = ['Foo', 'Bar', 'Fizz', 'Buzz']
</script>

<template>
  <VSelect
    :items="items"
    :menu-props="{ transition: 'scroll-y-transition' }"
    label="Label"
    placeholder="Select Item"
  />
</template>
