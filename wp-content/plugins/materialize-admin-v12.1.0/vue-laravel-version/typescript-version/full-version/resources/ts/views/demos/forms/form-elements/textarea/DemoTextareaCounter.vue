<script lang="ts" setup>
const textareaValue = ref('Hello!')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    counter
    label="Text"
    placeholder="Placeholder Text"
  />
</template>
