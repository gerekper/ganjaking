<script setup>
const insetSwitch1 = ref(true)
const insetSwitch2 = ref(false)
</script>

<template>
  <div class="demo-space-x">
    <VSwitch
      v-model="insetSwitch1"
      inset
      :label="`Switch 1: ${insetSwitch1.toString()}`"
    />
    <VSwitch
      v-model="insetSwitch2"
      inset
      :label="`Switch 2: ${insetSwitch2.toString()}`"
    />
  </div>
</template>
