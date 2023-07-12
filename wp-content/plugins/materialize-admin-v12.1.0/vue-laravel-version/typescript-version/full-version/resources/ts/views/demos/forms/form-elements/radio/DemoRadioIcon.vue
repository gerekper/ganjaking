<script lang="ts" setup>
const radioGroup = ref(1)
</script>

<template>
  <VRadioGroup
    v-model="radioGroup"
    false-icon="mdi-bell-off-outline"
    true-icon="mdi-bell-outline"
  >
    <VRadio
      v-for="n in 2"
      :key="n"
      :label="`Radio ${n}`"
      :value="n"
    />
  </VRadioGroup>
</template>
