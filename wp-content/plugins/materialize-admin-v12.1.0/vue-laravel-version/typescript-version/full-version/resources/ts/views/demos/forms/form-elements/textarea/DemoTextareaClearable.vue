<script setup lang="ts">
const textareaValue = ref('This is clearable text.')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    clearable
    clear-icon="mdi-close-circle-outline"
    label="Text"
    placeholder="Placeholder Text"
  />
</template>
